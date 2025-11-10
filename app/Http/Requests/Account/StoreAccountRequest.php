<?php

namespace App\Http\Requests\Account;

use App\Enums\RecurrenceTypeEnum;
use App\Enums\WalletTypeEnum;
use App\Models\Wallet;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreAccountRequest extends FormRequest
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
            'wallet_id' => [
                'required',
                'integer',
                'exists:wallets,id',
            ],
            'category_id' => [
                'required',
                'integer',
                'exists:categories,id',
            ],
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            'description' => [
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
                Rule::enum(RecurrenceTypeEnum::class),
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
            'wallet_id.required' => 'A carteira é obrigatória.',
            'wallet_id.exists' => 'A carteira selecionada não existe.',
            'category_id.required' => 'A categoria é obrigatória.',
            'category_id.exists' => 'A categoria selecionada não existe.',
            'name.required' => 'O nome da conta é obrigatório.',
            'name.max' => 'O nome não pode ter mais de 255 caracteres.',
            'description.max' => 'A descrição não pode ter mais de 1000 caracteres.',
            'total_amount.required' => 'O valor total é obrigatório.',
            'total_amount.numeric' => 'O valor total deve ser um número.',
            'total_amount.min' => 'O valor total deve ser maior que zero.',
            'recurrence_type.required' => 'O tipo de recorrência é obrigatório.',
            'installments.required_if' => 'O número de parcelas é obrigatório para compras parceladas.',
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
            // Validate wallet ownership
            if ($this->input('wallet_id')) {
                $wallet = Wallet::find($this->input('wallet_id'));

                if ($wallet && $wallet->user_id !== auth()->id()) {
                    $validator->errors()->add(
                        'wallet_id',
                        'A carteira selecionada não pertence a você.'
                    );
                    return;
                }

                // Validate credit card limit if wallet is a credit card
                if ($wallet && $wallet->type === WalletTypeEnum::CARD_CREDIT && $this->input('total_amount')) {
                    $totalAmount = (float) $this->input('total_amount');

                    // Calculate available limit (accessors already convert to reais)
                    $availableLimit = $wallet->card_limit - $wallet->card_limit_used;

                    // Check if there's enough limit
                    if ($totalAmount > $availableLimit) {
                        $availableLimitFormatted = number_format($availableLimit, 2, ',', '.');
                        $totalAmountFormatted = number_format($totalAmount, 2, ',', '.');

                        $validator->errors()->add(
                            'total_amount',
                            "O valor de R$ {$totalAmountFormatted} excede o limite disponível do cartão de R$ {$availableLimitFormatted}."
                        );
                    }
                }
            }
        });
    }
}
