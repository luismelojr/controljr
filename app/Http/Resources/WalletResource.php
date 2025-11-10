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
