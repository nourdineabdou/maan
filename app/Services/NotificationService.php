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
        ?array $data = null,
    ): Notification {
        $recipients = collect($recipients);

        [$notification, $recipientIds] = DB::transaction(function () use ($recipients, $title, $message, $sender, $actionUrl, $campaign, $data) {
            $notification = Notification::create([
                'type' => $campaign ? 'campaign_message' : 'system',
                'title' => $title,
                'message' => $message,
                'action_url' => $actionUrl,
                'data' => $data,
                'message_campaign_id' => $campaign?->id,
                'created_by' => $sender->id,
            ]);

            $recipientIds = [];

            foreach ($recipients as $recipient) {
                $recipientIds[$recipient->id] = NotificationRecipient::create([
                    'notification_id' => $notification->id,
                    'user_id' => $recipient->id,
                    'channel' => 'internal',
                    'status' => 'sent',
                    'delivered_at' => now(),
                ])->id;
            }

            return [$notification, $recipientIds];
        });

        $this->sendPush($recipients, $title, $message, $actionUrl, $recipientIds);

        return $notification;
    }

    /**
     * Envoie une notification push (best-effort) à chaque destinataire, dans
     * sa langue préférée. N'échoue jamais bruyamment : un destinataire sans
     * abonnement push, ou une erreur réseau, ne doit pas casser l'envoi de
     * la notification interne.
     *
     * La notification interne (cloche dans l'app) reste, elle, toujours
     * créée quel que soit ce réglage — seul l'envoi push (fonctionnalité
     * activable via PUSH_NOTIFICATIONS_ENABLED dans .env, pas depuis le
     * dashboard admin) est court-circuité ici.
     *
     * @param  \Illuminate\Support\Collection<int, User>  $recipients
     * @param  array<int, int>  $recipientIds  NotificationRecipient::id indexé par user_id
     */
    private function sendPush(\Illuminate\Support\Collection $recipients, array $title, array $message, ?string $actionUrl, array $recipientIds = []): void
    {
        if (! config('push.enabled')) {
            return;
        }

        foreach ($recipients as $recipient) {
            $locale = $recipient->preferred_locale ?? 'fr';

            try {
                $recipient->notify(new PushNotification(
                    title: $title[$locale] ?? reset($title),
                    body: $message[$locale] ?? reset($message),
                    url: $actionUrl,
                    notificationRecipientId: $recipientIds[$recipient->id] ?? null,
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
