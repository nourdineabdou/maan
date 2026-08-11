<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Numéro de téléphone mobile mauritanien : 8 chiffres, sans indicatif
 * international (+222), commençant par 2 (Mauritel), 3 (Mattel) ou
 * 4 (Chinguitel).
 */
class MauritanianPhoneNumber implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! preg_match('/^[234]\d{7}$/', (string) $value)) {
            $fail(__('validation.mauritanian_phone'));
        }
    }
}
