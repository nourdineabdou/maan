<?php

namespace App\Http\Requests\Member;

use Illuminate\Foundation\Http\FormRequest;

class ProfessionalInfoRequest extends FormRequest
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
        return [
            'employment_status' => ['nullable', 'in:employed,unemployed,student,retired,other'],
            'employment_sector' => ['nullable', 'in:public,private,informal,self_employed,ngo,other'],
            'function' => ['nullable', 'string', 'max:255'],
            'position' => ['nullable', 'string', 'max:255'],
            'employer' => ['nullable', 'string', 'max:255'],
            'other_employment_details' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
