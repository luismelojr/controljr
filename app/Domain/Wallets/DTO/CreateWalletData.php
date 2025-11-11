<?php

namespace App\Domain\Wallets\DTO;

use App\Enums\WalletTypeEnum;
use App\Http\Requests\Wallet\StoreWalletRequest;

class CreateWalletData
{
    public function __construct(
        public string $name,
        public WalletTypeEnum $type,
        public ?int $day_close = null,
        public ?int $best_shopping_day = null,
        public ?float $card_limit = null,
        public ?float $initial_balance = null,
    ) {}

    public static function fromRequest(StoreWalletRequest $request): self
    {
        return new self(
            name: $request->input('name'),
            type: WalletTypeEnum::from($request->input('type')),
            day_close: $request->input('day_close'),
            best_shopping_day: $request->input('best_shopping_day'),
            card_limit: $request->input('card_limit'),
            initial_balance: $request->input('initial_balance'),
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'type' => $this->type,
            'day_close' => $this->day_close,
            'best_shopping_day' => $this->best_shopping_day,
            'card_limit' => $this->card_limit,
            'initial_balance' => $this->initial_balance,
            'status' => true,
        ];
    }
}
