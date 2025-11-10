<?php

namespace App\Http\Requests\Account;

use App\Enums\AccountStatusEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAccountRequest extends FormRequest
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
            'name' => [
                'nullable',
                'string',
                'max:255',
            ],
            'description' => [
                'nullable',
                'string',
                'max:1000',
            ],
            'status' => [
                'nullable',
                Rule::enum(AccountStatusEnum::class),
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.string' => 'O nome deve ser um texto.',
            'name.max' => 'O nome não pode ter mais de 255 caracteres.',
            'description.string' => 'A descrição deve ser um texto.',
            'description.max' => 'A descrição não pode ter mais de 1000 caracteres.',
        ];
    }
}
