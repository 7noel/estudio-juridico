<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ConsultationFollowUpRequest extends FormRequest
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
        return [

            'consultation_id' => [
                'required',
                'exists:consultations,id',
            ],

            'contact_date' => [
                'required',
                'date',
            ],

            'communication_type' => [
                'required',
                Rule::in(array_keys(config('options.communication_types'))),
            ],

            'result' => [
                'required',
                Rule::in(array_keys(config('options.follow_up_results'))),
            ],

            'next_contact_date' => [
                'nullable',
                'date',
                'after_or_equal:contact_date',
            ],

            'notes' => [
                'nullable',
                'string',
                'max:5000',
            ],
            'generate_case' => [
                'nullable',
                'boolean',
            ],
            'reject_consultation' => [
                'nullable',
                'boolean',
            ],

        ];
    }

    public function attributes(): array
    {
        return [

            'consultation_id' => 'consulta',

            'contact_date' => 'fecha del contacto',

            'communication_type' => 'tipo de comunicación',

            'result' => 'resultado',

            'next_contact_date' => 'próxima fecha de contacto',

            'notes' => 'observaciones',

        ];
    }
}
