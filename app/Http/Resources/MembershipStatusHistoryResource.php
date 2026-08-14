<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MembershipStatusHistoryResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'old_status' => $this->old_status,
            'new_status' => $this->new_status,
            'comment' => $this->comment,
            'changed_by' => $this->when(
                $this->relationLoaded('changedBy') && $this->changedBy,
                fn () => [
                    'id' => $this->changedBy->id,
                    'name' => $this->changedBy->display_name,
                ],
            ),
            'created_at' => $this->created_at,
        ];
    }
}
