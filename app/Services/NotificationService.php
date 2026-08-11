<?php

namespace App\Services;

use App\Models\MessageCampaign;
use App\Models\Notification;
use App\Models\NotificationRecipient;
use App\Models\User;
use App\Notifications\PushNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Envoie une notification interne à un ensemble d'utilisateurs et trace
     * l'envoi dans une campagne de messages.
     *
     * @param  iterable<User>  $recipients
     */
    public function send(
        iterable $recipients,
        array $title,
        array $message,
        User $sender,
        ?string $actionUrl = null,
        ?MessageCampaign $campaign = null,
    ): Notification {
        $recipients = collect($recipients);

        $notification = DB::transaction(function () use ($recipients, $title, $message, $sender, $actionUrl, $campaign) {
            $notification = Notification::create([
                'type' => $campaign ? 'campaign_message' : 'system',
                'title' => $title,
                'message' => $message,
                'action_url' => $actionUrl,
                'message_campaign_id' => $campaign?->id,
                'created_by' => $sender->id,
            ]);

            foreach ($recipients as $recipient) {
                NotificationRecipient::create([
                    'notification_id' => $notification->id,
                    'user_id' => $recipient->id,
                    'channel' => 'internal',
                    'status' => 'sent',
                    'delivered_at' => now(),
                ]);
            }

            return $notification;
        });

        $this->sendPush($recipients, $title, $message, $actionUrl);

        return $notification;
    }

    /**
     * Envoie une notification push (best-effort) à chaque destinataire, dans
     * sa langue préférée. N'échoue jamais bruyamment : un destinataire sans
     * abonnement push, ou une erreur réseau, ne doit pas casser l'envoi de
     * la notification interne.
     *
     * @param  \Illuminate\Support\Collection<int, User>  $recipients
     */
    private function sendPush(\Illuminate\Support\Collection $recipients, array $title, array $message, ?string $actionUrl): void
    {
        foreach ($recipients as $recipient) {
            $locale = $recipient->preferred_locale ?? 'fr';

            try {
                $recipient->notify(new PushNotification(
                    title: $title[$locale] ?? reset($title),
                    body: $message[$locale] ?? reset($message),
                    url: $actionUrl,
                ));
            } catch (\Throwable $e) {
                Log::warning('Échec de l\'envoi de la notification push.', [
                    'user_id' => $recipient->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * Résout la liste des membres à cibler selon le type de ciblage.
     *
     * @return \Illuminate\Support\Collection<int, User>
     */
    public function resolveTargets(string $targetType, ?string $targetValue): \Illuminate\Support\Collection
    {
        $query = User::query()->role('membre');

        match ($targetType) {
            'region' => $query->whereHas('profile', fn ($q) => $q->where('region_id', $targetValue)),
            'employment_status' => $query->whereHas('profile', fn ($q) => $q->where('employment_status', $targetValue)),
            'membership_status' => $query->whereHas('latestMembership', fn ($q) => $q->where('status', $targetValue)),
            default => null,
        };

        return $query->get();
    }
}
