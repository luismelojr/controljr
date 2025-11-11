<?php

namespace App\Domain\Incomes\DTO;

use App\Enums\IncomeRecurrenceTypeEnum;
use App\Models\Category;
use App\Models\Wallet;
use Illuminate\Http\Request;

class CreateIncomeData
{
    public function __construct(
        public readonly ?int $wallet_id,
        public readonly int $category_id,
        public readonly string $name,
        public readonly ?string $notes,
        public readonly float $total_amount,
        public readonly IncomeRecurrenceTypeEnum $recurrence_type,
        public readonly ?int $installments,
        public readonly string $start_date,
    ) {}

    public static function fromRequest(Request $request): self
    {
        // Convert UUIDs to IDs
        $category = Category::where('uuid', $request->input('category_id'))->firstOrFail();
        $wallet = $request->input('wallet_id')
            ? Wallet::where('uuid', $request->input('wallet_id'))->firstOrFail()
            : null;

        return new self(
            wallet_id: $wallet?->id,
            category_id: $category->id,
            name: $request->input('name'),
            notes: $request->input('notes'),
            total_amount: (float) $request->input('total_amount'),
            recurrence_type: IncomeRecurrenceTypeEnum::from($request->input('recurrence_type')),
            installments: $request->integer('installments') ?: null,
            start_date: $request->input('start_date'),
        );
    }

    public function toArray(): array
    {
        return [
            'wallet_id' => $this->wallet_id,
            'category_id' => $this->category_id,
            'name' => $this->name,
            'notes' => $this->notes,
            'total_amount' => $this->total_amount,
            'recurrence_type' => $this->recurrence_type,
            'installments' => $this->installments,
            'start_date' => $this->start_date,
        ];
    }
}
