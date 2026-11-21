<?php

namespace App\Http\Requests\Member;

use Illuminate\Foundation\Http\FormRequest;

class PersonalInfoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $profileId = $this->user()->profile?->id;

        return [
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'gender' => ['required', 'in:male,female'],
            // "sometimes" : l'app iOS n'envoie pas le NNI (version sans données
            // d'identité, App Store 5.1.1(ix)) ; absent, il n'est ni validé ni
            // écrasé. Le formulaire Web l'envoie toujours, il y reste exigé.
            // La soumission d'adhésion l'exige de toute façon (MembershipDraftService).
            'nni' => ['sometimes', 'required', 'digits:10', 'unique:member_profiles,nni,'.$profileId],
            'region_id' => ['required', 'exists:regions,id'],
            'moughataa_id' => ['required', 'exists:moughataas,id'],
            'birth_date' => ['nullable', 'date', 'before:today'],
            'commune_id' => ['nullable', 'exists:communes,id'],
            'locality' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:500'],
        ];
    }
}
