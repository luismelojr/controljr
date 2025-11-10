<?php

namespace App\Http\Requests\IncomeTransaction;

use Illuminate\Foundation\Http\FormRequest;

class MarkAsReceivedRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'received_at' => [
                'nullable',
                'date',
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'received_at.date' => 'A data de recebimento deve ser uma data válida.',
        ];
    }
}
