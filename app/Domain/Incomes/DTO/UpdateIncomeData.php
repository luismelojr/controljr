<?php

namespace App\Domain\Incomes\DTO;

use App\Enums\IncomeStatusEnum;
use Illuminate\Http\Request;

class UpdateIncomeData
{
    public function __construct(
        public readonly ?string $name,
        public readonly ?string $notes,
        public readonly ?IncomeStatusEnum $status,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            name: $request->input('name'),
            notes: $request->input('notes'),
            status: $request->has('status') ? IncomeStatusEnum::from($request->input('status')) : null,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'name' => $this->name,
            'notes' => $this->notes,
            'status' => $this->status,
        ], fn($value) => $value !== null);
    }
}
