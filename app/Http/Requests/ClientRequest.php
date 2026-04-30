<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ClientRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [

            'full_name' => [
                'required',
                'string',
                'max:255'
            ],

            'document_type' => [
                'required'
            ],

            'document_number' => [
                'required',
                'string',
                'max:20'
            ],

            'mobile' => [
                'nullable',
                'max:20'
            ],

            'phone' => [
                'nullable',
                'max:20'
            ],

            'email' => [
                'nullable',
                'email'
            ],

            'ubigeo_code' => [
                'nullable'
            ],

            'address' => [
                'nullable'
            ],

        ];
    }
}
