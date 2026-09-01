<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ClientRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Normalizar el celular antes de validar:
     * se eliminan espacios, guiones y paréntesis, y el prefijo
     * internacional 00 se convierte a +.
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

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $id = $this->route('client');

        return [

            'document_type' => [
                'required',
                Rule::in(array_keys(config('options.client_document_types')))
            ],

            'document_number' => [
                Rule::requiredIf(fn () => $this->input('document_type') !== 'T'),
                'nullable',
                'string',
                'max:20',
                Rule::unique('clients')
                    ->where(function ($query) {

                        return $query
                            ->where('document_type', $this->input('document_type'));

                    })
                    ->ignore($id)
            ],

            'full_name' => [
                'required',
                'string',
                'max:255'
            ],

            'address' => [
                'nullable',
                'string'
            ],

            // 'ubigeo_code' => [
            //     'string'
            // ],

            'email' => [
                'nullable',
                'email'
            ],

            'mobile' => [
                'required',
                'string',
                'regex:'.config('options.mobile_regex')
            ],

            'phone' => [
                'nullable'
            ],

        ];
    }

    public function messages()
    {
        return [

            'mobile.regex' =>
                'El celular debe tener 9 dígitos (Perú) o incluir el código de país (ej. +34 612 345 678).',

        ];
    }

}
