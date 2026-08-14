<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MemberDocumentResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'membership_id' => $this->membership_id,
            'document_type' => $this->document_type,
            'title' => $this->title,
            'file_url' => $this->file_url,
            'original_name' => $this->original_name,
            'mime_type' => $this->mime_type,
            'file_size' => $this->file_size,
            'is_required' => $this->is_required,
            'is_verified' => $this->is_verified,
            'verified_at' => $this->verified_at,
            'verification_note' => $this->verification_note,
            'created_at' => $this->created_at,
        ];
    }
}
