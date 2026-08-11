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

            'gender' => ['required', 'in:male,female'],
            'nni' => ['required', 'digits:10', 'unique:member_profiles,nni'],
            'region_id' => ['required', 'exists:regions,id'],
            'moughataa_id' => ['required', 'exists:moughataas,id'],
            'photo' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:4096'],
            'identity_card_front' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'identity_card_back' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ];
    }
}
