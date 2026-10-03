<?php

namespace App\Http\Requests\Auth;

use App\Rules\MauritanianPhoneNumber;
use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Retire les espaces, tirets, et un éventuel indicatif +222/222 avant
     * validation, pour accepter les formats de saisie courants tout en
     * imposant en base un numéro local à 8 chiffres.
     */
    protected function prepareForValidation(): void
    {
        $phone = preg_replace('/[\s-]/', '', (string) $this->input('phone'));
        $phone = preg_replace('/^\+?222/', '', $phone);

        $this->merge(['phone' => $phone]);
    }

    /**
     * Mot de passe libre à partir de 4 caractères (accepte aussi bien un
     * code PIN numérique qu'un mot de passe classique).
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', new MauritanianPhoneNumber, 'unique:users,phone'],
            'email' => ['nullable', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', 'string', 'min:4'],
            'preferred_locale' => ['required', 'in:fr,ar'],
            'terms' => ['accepted'],

            // Volontairement facultatifs (cf. MemberRegistrationService) :
            // Apple refuse qu'une app impose ces informations sensibles dès
            // l'inscription (guideline 5.1.1) hors d'un cadre d'organisation
            // enregistrée. Un membre peut donc créer un compte léger, puis
            // compléter et soumettre son adhésion plus tard depuis l'app.
            'gender' => ['nullable', 'in:male,female'],
            'nni' => ['nullable', 'digits:10', 'unique:member_profiles,nni'],
            'region_id' => ['nullable', 'exists:regions,id'],
            'moughataa_id' => ['nullable', 'exists:moughataas,id'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:4096'],
            'identity_card_front' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'identity_card_back' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ];
    }
}
