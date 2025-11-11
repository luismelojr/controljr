<?php

namespace App\Http\Requests\Income;

use App\Enums\IncomeStatusEnum;
use App\Enums\WalletTypeEnum;
use App\Models\Wallet;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateIncomeRequest extends FormRequest
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
                'nullable',
                'string',
                'exists:wallets,uuid',
            ],
            'name' => [
                'nullable',
                'string',
                'max:255',
            ],
            'notes' => [
                'nullable',
                'string',
                'max:1000',
            ],
            'status' => [
                'nullable',
                Rule::enum(IncomeStatusEnum::class),
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'wallet_id.exists' => 'A carteira selecionada não existe.',
            'name.string' => 'O nome deve ser um texto.',
            'name.max' => 'O nome não pode ter mais de 255 caracteres.',
            'notes.string' => 'As observações devem ser um texto.',
            'notes.max' => 'As observações não podem ter mais de 1000 caracteres.',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            // Validate wallet ownership and type
            if ($this->input('wallet_id')) {
                $wallet = Wallet::where('uuid', $this->input('wallet_id'))->first();

                if ($wallet) {
                    // Check ownership
                    if ($wallet->user_id !== auth()->id()) {
                        $validator->errors()->add(
                            'wallet_id',
                            'A carteira selecionada não pertence a você.'
                        );
                    }

                    // Check wallet type (only bank_account or other allowed)
                    if (!in_array($wallet->type->value, [WalletTypeEnum::BANK_ACCOUNT->value, WalletTypeEnum::OTHER->value])) {
                        $validator->errors()->add(
                            'wallet_id',
                            'Apenas carteiras do tipo Conta Bancária ou Outro podem receber receitas.'
                        );
                    }
                }
            }
        });
    }
}
