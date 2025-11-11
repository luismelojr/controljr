<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WalletResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $isCreditCard = $this->type->value === 'card_credit';
        $isBankAccountOrOther = in_array($this->type->value, ['bank_account', 'other']);

        // Calculate total expenses (all transactions) for this wallet
        $totalExpenses = $this->transactions()
            ->sum('amount');

        // Calculate total incomes received for this wallet
        // Get all income_transactions from incomes linked to this wallet
        $totalIncomesReceived = \DB::table('income_transactions')
            ->join('incomes', 'income_transactions.income_id', '=', 'incomes.id')
            ->where('incomes.wallet_id', $this->id)
            ->where('income_transactions.is_received', true)
            ->sum('income_transactions.amount');

        // Convert from cents to decimal
        $totalExpensesDecimal = $totalExpenses / 100;
        $totalIncomesReceivedDecimal = $totalIncomesReceived / 100;

        // Calculate final balance: initial_balance + incomes - expenses
        $finalBalance = $this->initial_balance + $totalIncomesReceivedDecimal - $totalExpensesDecimal;

        return [
            'uuid' => $this->uuid,
            'name' => $this->name,
            'type' => $this->type->value,
            'type_label' => $this->getTypeLabel(),
            'is_credit_card' => $isCreditCard,
            'day_close' => $this->day_close,
            'best_shopping_day' => $this->best_shopping_day,
            'card_limit' => $this->card_limit,
            'card_limit_used' => $this->card_limit_used,
            'card_limit_available' => $isCreditCard ? $this->card_limit - $this->card_limit_used : null,
            'card_limit_percentage_used' => $isCreditCard && $this->card_limit > 0
                ? round(($this->card_limit_used / $this->card_limit) * 100, 2)
                : null,
            'initial_balance' => $this->initial_balance,
            'balance_incomes' => $isBankAccountOrOther ? $totalIncomesReceivedDecimal : null,
            'balance_expenses' => $isBankAccountOrOther ? $totalExpensesDecimal : null,
            'balance_available' => $isBankAccountOrOther ? $finalBalance : null,
            'balance_percentage_used' => $isBankAccountOrOther && ($this->initial_balance + $totalIncomesReceivedDecimal) > 0
                ? round(($totalExpensesDecimal / ($this->initial_balance + $totalIncomesReceivedDecimal)) * 100, 2)
                : null,
            'status' => $this->status,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }

    /**
     * Get wallet type label
     */
    protected function getTypeLabel(): string
    {
        return match ($this->type->value) {
            'card_credit' => 'Cartão de Crédito',
            'bank_account' => 'Conta Bancária',
            'other' => 'Outro',
            default => $this->type->value,
        };
    }
}
