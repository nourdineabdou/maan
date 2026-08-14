<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MembershipResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'registration_number' => $this->registration_number,
            'member_number' => $this->member_number,
            'status' => $this->status,
            'is_draft' => $this->isDraft(),
            'is_pending' => $this->isPending(),
            'is_approved' => $this->isApproved(),
            'is_rejected' => $this->isRejected(),
            'can_be_submitted' => $this->canBeSubmitted(),
            'member_message' => $this->member_message,
            'population_needs' => $this->population_needs,
            'submitted_at' => $this->submitted_at,
            'reviewed_at' => $this->reviewed_at,
            'approved_at' => $this->approved_at,
            'rejected_at' => $this->rejected_at,
            'rejection_reason' => $this->rejection_reason,
            'card_is_active' => $this->card_is_active,
            'card_generated_at' => $this->card_generated_at,
            'user' => UserResource::make($this->whenLoaded('user')),
            'reviewer' => UserResource::make($this->whenLoaded('reviewer')),
            'problematics' => ProblematicResource::collection($this->whenLoaded('problematics')),
            'documents' => MemberDocumentResource::collection($this->whenLoaded('documents')),
            'status_histories' => MembershipStatusHistoryResource::collection($this->whenLoaded('statusHistories')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
