<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class RejectAmbassadorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('ambassadors.reject');
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'reason' => ['required', 'string', 'min:5', 'max:2000'],
        ];
    }
}
