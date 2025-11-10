<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AccountResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'name' => $this->name,
            'description' => $this->description,
            'total_amount' => $this->total_amount,
            'recurrence_type' => $this->recurrence_type->value,
            'recurrence_type_label' => $this->getRecurrenceTypeLabel(),
            'installments' => $this->installments,
            'start_date' => $this->start_date?->format('Y-m-d'),
            'status' => $this->status->value,
            'status_label' => $this->getStatusLabel(),
            'wallet' => new WalletResource($this->whenLoaded('wallet')),
            'category' => new CategoryResource($this->whenLoaded('category')),
            'transactions' => TransactionResource::collection($this->whenLoaded('transactions')),
            'transactions_count' => $this->when(
                $this->relationLoaded('transactions'),
                fn() => $this->transactions->count()
            ),
            'paid_transactions_count' => $this->when(
                $this->relationLoaded('transactions'),
                fn() => $this->transactions->where('status', 'paid')->count()
            ),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }

    /**
     * Get recurrence type label
     */
    protected function getRecurrenceTypeLabel(): string
    {
        return match ($this->recurrence_type->value) {
            'one_time' => 'Única',
            'installments' => 'Parcelada',
            'recurring' => 'Recorrente',
            default => $this->recurrence_type->value,
        };
    }

    /**
     * Get status label
     */
    protected function getStatusLabel(): string
    {
        return match ($this->status->value) {
            'active' => 'Ativa',
            'completed' => 'Completa',
            'cancelled' => 'Cancelada',
            default => $this->status->value,
        };
    }
}
