<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ConsultationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'service_type' => ['required', 'string', Rule::in(array_keys(config('options.service_types')))],
            'legal_specialty_id' => 'required|exists:legal_specialties,id',
            'legal_subject_id' => 'required|exists:legal_subjects,id',
            'client_id' => 'required|exists:clients,id',
            'lawyer_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            // 'total_amount' => 'required|numeric|min:0',

            'installments' => 'nullable|array',
            'installments.*.amount' => 'required|numeric|min:0',
            'installments.*.due_date' => 'nullable|date',
        ];
    }
}