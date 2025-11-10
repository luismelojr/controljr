<?php

namespace App\Domain\Accounts\DTO;

use App\Enums\RecurrenceTypeEnum;
use Illuminate\Http\Request;

class CreateAccountData
{
    public function __construct(
        public readonly int $wallet_id,
        public readonly int $category_id,
        public readonly string $name,
        public readonly ?string $description,
        public readonly float $total_amount,
        public readonly RecurrenceTypeEnum $recurrence_type,
        public readonly ?int $installments,
        public readonly string $start_date,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            wallet_id: $request->integer('wallet_id'),
            category_id: $request->integer('category_id'),
            name: $request->input('name'),
            description: $request->input('description'),
            total_amount: (float) $request->input('total_amount'),
            recurrence_type: RecurrenceTypeEnum::from($request->input('recurrence_type')),
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
            'description' => $this->description,
            'total_amount' => $this->total_amount,
            'recurrence_type' => $this->recurrence_type,
            'installments' => $this->installments,
            'start_date' => $this->start_date,
        ];
    }
}
