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
                'required',
                'string',
                Rule::unique('clients')
                    ->where(function ($query) {

                        return $query
                            ->where('document_type', request('document_type'));

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
                'regex:/^9\d{8}$/'
            ],

            'phone' => [
                'nullable'
            ],

        ];
    }

    public function messages()
    {
        return [

            'phone.regex' =>
                'El celular debe tener 9 dígitos y comenzar con 9.',

        ];
    }

}
