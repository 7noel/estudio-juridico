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
                'regex:/^9\d{8}$/'
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
}