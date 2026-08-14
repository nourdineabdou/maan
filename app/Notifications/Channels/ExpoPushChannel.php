<?php

namespace App\Notifications\Channels;

use App\Models\ExpoPushToken;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Canal de notification pour l'app mobile (Expo push), en complément du
 * canal WebPush existant (navigateurs). Best-effort comme `sendPush()` dans
 * NotificationService : une erreur ici ne doit jamais remonter à l'appelant.
 */
class ExpoPushChannel
{
    public function send(object $notifiable, Notification $notification): void
    {
        if (! method_exists($notification, 'toExpoPush')) {
            return;
        }

        $tokens = $notifiable->routeNotificationFor('ExpoPush', $notification);

        if (empty($tokens)) {
            return;
        }

        $payload = $notification->toExpoPush($notifiable);
        $messages = collect($tokens)
            ->values()
            ->map(fn (string $token) => array_merge($payload, ['to' => $token]))
            ->all();

        try {
            $response = Http::post('https://exp.host/--/api/v2/push/send', $messages);
            $this->pruneInvalidTokens(array_values($tokens), $response->json('data') ?? []);
        } catch (\Throwable $e) {
            Log::warning('Échec de l\'envoi de la notification Expo push.', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Expo signale les jetons qui ne correspondent plus à une installation
     * (app désinstallée, jeton régénéré...) : on les supprime pour éviter de
     * les retenter indéfiniment.
     *
     * @param  array<int, string>  $tokens
     * @param  array<int, array<string, mixed>>  $results
     */
    private function pruneInvalidTokens(array $tokens, array $results): void
    {
        foreach ($results as $index => $result) {
            $isDeviceNotRegistered = ($result['status'] ?? null) === 'error'
                && ($result['details']['error'] ?? null) === 'DeviceNotRegistered';

            if ($isDeviceNotRegistered && isset($tokens[$index])) {
                ExpoPushToken::where('token', $tokens[$index])->delete();
            }
        }
    }
}
