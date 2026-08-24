<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AmbassadorRequestResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status,
            'is_pending' => $this->isPending(),
            'is_approved' => $this->isApproved(),
            'is_rejected' => $this->isRejected(),
            'can_be_submitted' => $this->canBeSubmitted(),
            'submitted_at' => $this->submitted_at,
            'reviewed_at' => $this->reviewed_at,
            'approved_at' => $this->approved_at,
            'rejected_at' => $this->rejected_at,
            'rejection_reason' => $this->rejection_reason,
            'user' => UserResource::make($this->whenLoaded('user')),
            'reviewer' => UserResource::make($this->whenLoaded('reviewer')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
