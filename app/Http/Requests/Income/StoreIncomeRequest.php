<?php

namespace App\Http\Requests\Income;

use App\Enums\IncomeRecurrenceTypeEnum;
use App\Models\Category;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreIncomeRequest extends FormRequest
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
            'category_id' => [
                'required',
                'string',
                'exists:categories,uuid',
            ],
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            'notes' => [
                'nullable',
                'string',
                'max:1000',
            ],
            'total_amount' => [
                'required',
                'numeric',
                'min:0.01',
            ],
            'recurrence_type' => [
                'required',
                Rule::enum(IncomeRecurrenceTypeEnum::class),
            ],
            'installments' => [
                'nullable',
                'required_if:recurrence_type,installments',
                'integer',
                'min:2',
                'max:120',
            ],
            'start_date' => [
                'required',
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
            'category_id.required' => 'A categoria é obrigatória.',
            'category_id.exists' => 'A categoria selecionada não existe.',
            'name.required' => 'O nome da receita é obrigatório.',
            'name.max' => 'O nome não pode ter mais de 255 caracteres.',
            'notes.max' => 'As observações não podem ter mais de 1000 caracteres.',
            'total_amount.required' => 'O valor total é obrigatório.',
            'total_amount.numeric' => 'O valor total deve ser um número.',
            'total_amount.min' => 'O valor total deve ser maior que zero.',
            'recurrence_type.required' => 'O tipo de recorrência é obrigatório.',
            'installments.required_if' => 'O número de parcelas é obrigatório para receitas parceladas.',
            'installments.integer' => 'O número de parcelas deve ser um número inteiro.',
            'installments.min' => 'O número de parcelas deve ser no mínimo 2.',
            'installments.max' => 'O número de parcelas não pode ser maior que 120.',
            'start_date.required' => 'A data de início é obrigatória.',
            'start_date.date' => 'A data de início deve ser uma data válida.',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            // Validate category ownership
            if ($this->input('category_id')) {
                $category = Category::where('uuid', $this->input('category_id'))->first();

                if ($category && $category->user_id !== null && $category->user_id !== auth()->id()) {
                    $validator->errors()->add(
                        'category_id',
                        'A categoria selecionada não pertence a você.'
                    );
                }
            }
        });
    }
}
