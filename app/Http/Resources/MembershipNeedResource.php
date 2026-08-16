<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MembershipNeedResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'description' => $this->description,
            'status' => $this->status,
            'documents' => MemberDocumentResource::collection($this->whenLoaded('documents')),
            'created_at' => $this->created_at,
            'membership' => $this->whenLoaded('membership', fn () => [
                'id' => $this->membership->id,
                'registration_number' => $this->membership->registration_number,
                'member' => UserResource::make($this->membership->user),
            ]),
        ];
    }
}
