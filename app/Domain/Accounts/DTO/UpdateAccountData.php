<?php

namespace App\Domain\Accounts\DTO;

use App\Enums\AccountStatusEnum;
use Illuminate\Http\Request;

class UpdateAccountData
{
    public function __construct(
        public readonly ?string $name,
        public readonly ?string $description,
        public readonly ?AccountStatusEnum $status,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            name: $request->input('name'),
            description: $request->input('description'),
            status: $request->has('status') ? AccountStatusEnum::from($request->input('status')) : null,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'name' => $this->name,
            'description' => $this->description,
            'status' => $this->status,
        ], fn($value) => $value !== null);
    }
}
