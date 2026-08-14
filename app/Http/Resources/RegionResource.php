<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RegionResource extends JsonResource
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
            'name_translations' => $this->name,
            'is_active' => $this->is_active,
            'display_order' => $this->display_order,
            'moughataas_count' => $this->whenCounted('moughataas'),
        ];
    }
}
