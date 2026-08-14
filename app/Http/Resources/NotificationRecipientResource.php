<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationRecipientResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'channel' => $this->channel,
            'status' => $this->status,
            'delivered_at' => $this->delivered_at,
            'read_at' => $this->read_at,
            'is_read' => $this->isRead(),
            'notification' => $this->when($this->relationLoaded('notification'), fn () => [
                'id' => $this->notification->id,
                'type' => $this->notification->type,
                'title' => $this->notification->getTranslation('title'),
                'message' => $this->notification->getTranslation('message'),
                'action_url' => $this->notification->action_url,
                'created_at' => $this->notification->created_at,
            ]),
            'created_at' => $this->created_at,
        ];
    }
}
