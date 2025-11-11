<?php

namespace App\Domain\Incomes\DTO;

use App\Enums\IncomeStatusEnum;
use App\Models\Wallet;
use Illuminate\Http\Request;

class UpdateIncomeData
{
    public function __construct(
        public readonly ?int $wallet_id,
        public readonly ?string $name,
        public readonly ?string $notes,
        public readonly ?IncomeStatusEnum $status,
    ) {}

    public static function fromRequest(Request $request): self
    {
        $wallet = $request->has('wallet_id') && $request->input('wallet_id')
            ? Wallet::where('uuid', $request->input('wallet_id'))->firstOrFail()
            : null;

        return new self(
            wallet_id: $wallet?->id,
            name: $request->input('name'),
            notes: $request->input('notes'),
            status: $request->has('status') ? IncomeStatusEnum::from($request->input('status')) : null,
        );
    }

    public function toArray(): array
    {
        $data = [
            'name' => $this->name,
            'notes' => $this->notes,
            'status' => $this->status,
        ];

        // Only include wallet_id if it was explicitly set (even if null to clear it)
        if (isset($this->wallet_id)) {
            $data['wallet_id'] = $this->wallet_id;
        }

        return array_filter($data, fn($value) => $value !== null);
    }
}
