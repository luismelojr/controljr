<?php

namespace App\Http\Requests\Wallet;

use App\Enums\WalletTypeEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateWalletRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::enum(WalletTypeEnum::class)],
            'day_close' => ['nullable', 'required_if:type,card_credit', 'integer', 'min:1', 'max:31'],
            'best_shopping_day' => ['nullable', 'required_if:type,card_credit', 'integer', 'min:1', 'max:31'],
            'card_limit' => ['nullable', 'required_if:type,card_credit', 'numeric', 'min:0'],
            'initial_balance' => ['nullable', 'numeric', 'min:0'],
            'status' => ['sometimes', 'boolean'],
        ];
    }

    /**
     * Get custom attribute names for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'nome',
            'type' => 'tipo',
            'day_close' => 'dia de fechamento',
            'best_shopping_day' => 'melhor dia de compra',
            'card_limit' => 'limite do cartão',
            'initial_balance' => 'saldo inicial',
            'status' => 'status',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'day_close.required_if' => 'O dia de fechamento é obrigatório para cartões de crédito.',
            'best_shopping_day.required_if' => 'O melhor dia de compra é obrigatório para cartões de crédito.',
            'card_limit.required_if' => 'O limite do cartão é obrigatório para cartões de crédito.',
        ];
    }
}
