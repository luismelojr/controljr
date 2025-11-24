<?php

namespace App\Http\Requests\Dashboard\Transaction;

use Illuminate\Foundation\Http\FormRequest;

class StoreTransactionRequest extends FormRequest
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
            'amount' => ['required', 'numeric', 'min:0.01'],
            'due_date' => ['required', 'date'],
            'paid_at' => ['nullable', 'date'],
            'category_id' => ['required', 'exists:categories,id'],
            'wallet_id' => ['required', 'exists:wallets,id'],
            'status' => ['required', 'in:pending,paid,overdue'],
            'is_reconciled' => ['boolean'],
            'external_id' => ['nullable', 'string'],
            'account_id' => ['nullable', 'exists:accounts,id'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'amount' => 'valor',
            'due_date' => 'data de vencimento',
            'paid_at' => 'data de pagamento',
            'category_id' => 'categoria',
            'wallet_id' => 'carteira',
            'status' => 'status',
            'is_reconciled' => 'conciliado',
            'external_id' => 'ID externo',
            'account_id' => 'conta',
        ];
    }
}
