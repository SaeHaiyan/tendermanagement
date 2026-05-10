<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($this->user()->id)],
            'company_name' => ['nullable', 'string', 'max:255'],
            'company_address' => ['nullable', 'string'],
            'pic_name' => ['nullable', 'string', 'max:255'],
            'office_phone' => ['nullable', 'string', 'max:20'],
            'pic_phone' => ['nullable', 'string', 'max:20'],
            'company_email' => ['nullable', 'email', 'max:255'],
            'cidb_reg_number' => ['nullable', 'string', 'max:100'],
            'ssm_number' => ['nullable', 'string', 'max:100'],
            'company_level' => ['nullable', 'string', 'max:100'],
            'services_provided' => ['nullable', 'string'],
            'year_established' => ['nullable', 'integer'],
            'cidb_grades' => ['nullable', 'array'], // Validate as an array
        ];
    }
}
