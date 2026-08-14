<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MemberMessageReplyResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'body' => $this->body,
            'author' => $this->when($this->relationLoaded('author') && $this->author, fn () => [
                'id' => $this->author->id,
                'name' => $this->author->display_name,
                'is_admin' => $this->author->hasRole('administrateur'),
            ]),
            'created_at' => $this->created_at,
        ];
    }
}
