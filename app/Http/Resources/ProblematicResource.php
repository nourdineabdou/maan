<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProblematicResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'name' => $this->getTranslation('name'),
            'description' => $this->getTranslation('description'),
            'icon' => $this->icon,
            'requires_justification' => $this->requires_justification,
            'is_active' => $this->is_active,
            'display_order' => $this->display_order,
            'pivot' => $this->when($this->pivot !== null, fn () => [
                'id' => $this->pivot->id,
                'description' => $this->pivot->description,
                'requested_solution' => $this->pivot->requested_solution,
                'locality' => $this->pivot->locality,
                'priority' => $this->pivot->priority,
                'status' => $this->pivot->status,
                'documents' => $this->when(
                    $this->pivot->relationLoaded('documents'),
                    fn () => MemberDocumentResource::collection($this->pivot->documents),
                ),
            ]),
        ];
    }
}
