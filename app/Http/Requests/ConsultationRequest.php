<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ConsultationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
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