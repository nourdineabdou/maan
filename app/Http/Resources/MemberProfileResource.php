<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MemberProfileResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'full_name' => $this->full_name,
            'nni' => $this->nni,
            'gender' => $this->gender,
            'birth_date' => $this->birth_date?->toDateString(),
            'region_id' => $this->region_id,
            'moughataa_id' => $this->moughataa_id,
            'commune_id' => $this->commune_id,
            'locality' => $this->locality,
            'address' => $this->address,
            'photo_url' => $this->photo_url,
            'employment_status' => $this->employment_status,
            'employment_sector' => $this->employment_sector,
            'function' => $this->function,
            'position' => $this->position,
            'employer' => $this->employer,
            'other_employment_details' => $this->other_employment_details,
            'profile_completed' => $this->profile_completed,
            'region' => RegionResource::make($this->whenLoaded('region')),
            'moughataa' => MoughataaResource::make($this->whenLoaded('moughataaRef')),
            'commune' => CommuneResource::make($this->whenLoaded('communeRef')),
        ];
    }
}
