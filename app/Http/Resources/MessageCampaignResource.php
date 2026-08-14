<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageCampaignResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->getTranslation('title'),
            'message' => $this->getTranslation('message'),
            'target_type' => $this->target_type,
            'target_filters' => $this->target_filters,
            'channel' => $this->channel,
            'status' => $this->status,
            'sent_at' => $this->sent_at,
            'creator' => $this->when($this->relationLoaded('creator') && $this->creator, fn () => [
                'id' => $this->creator->id,
                'name' => $this->creator->display_name,
            ]),
            'recipients_count' => $this->when(
                $this->relationLoaded('notifications'),
                fn () => $this->notifications->sum(fn ($n) => $n->recipients->count()),
            ),
            'created_at' => $this->created_at,
        ];
    }
}
