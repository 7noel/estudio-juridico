<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Normalizar el celular antes de validar.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('mobile') && $this->get('mobile') !== null) {
            $this->merge([
                'mobile' => $this->normalizeMobile($this->get('mobile')),
            ]);
        }
    }

    protected function normalizeMobile(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = preg_replace('/[^\d+]/', '', $value);

        if (str_starts_with($value, '00')) {
            $value = '+'.substr($value, 2);
        }

        return $value;
    }

    public function rules(): array
    {
        $userId = $this->route('user');

        return [

            'name' => [
                'required',
                'string',
                'max:100'
            ],

            'email' => [
                'required',
                'email',
                Rule::unique('users','email')
                    ->ignore($userId)
            ],

            'mobile' => [
                'nullable',
                'string',
                'regex:'.config('options.mobile_regex')
            ],
            
            'role' => [
                'required',
                'string'
            ],

            'password' => [
                $userId ? 'nullable' : 'required',
                'string',
                'min:6'
            ],

        ];
    }

    public function messages(): array
    {
        return [
            'mobile.regex' =>
                'El celular debe tener 9 dígitos (Perú) o incluir el código de país (ej. +34 612 345 678).',
        ];
    }
}