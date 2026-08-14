<?php

namespace App\Notifications;

use App\Notifications\Channels\ExpoPushChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

class PushNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly string $title,
        private readonly string $body,
        private readonly ?string $url = null,
        private readonly ?int $notificationRecipientId = null,
    ) {
    }

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return [WebPushChannel::class, ExpoPushChannel::class];
    }

    public function toWebPush(object $notifiable, self $notification): WebPushMessage
    {
        return (new WebPushMessage)
            ->title($this->title)
            ->icon('/logo_fr.png')
            ->body($this->body)
            ->data(['url' => $this->url ?? '/'])
            ->options(['TTL' => 300]);
    }

    /**
     * @return array<string, mixed>
     */
    public function toExpoPush(object $notifiable): array
    {
        return [
            'title' => $this->title,
            'body' => $this->body,
            'sound' => 'default',
            'priority' => 'high',
            'data' => [
                'url' => $this->url,
                'notification_id' => $this->notificationRecipientId,
            ],
        ];
    }
}
