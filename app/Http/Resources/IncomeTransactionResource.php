<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class IncomeTransactionResource extends JsonResource
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
            'amount' => $this->amount,
            'month_reference' => $this->month_reference,
            'expected_date' => $this->expected_date?->format('Y-m-d'),
            'received_at' => $this->received_at?->format('Y-m-d'),
            'installment_number' => $this->installment_number,
            'total_installments' => $this->total_installments,
            'installment_label' => $this->getInstallmentLabel(),
            'status' => $this->status->value,
            'status_label' => $this->getStatusLabel(),
            'is_received' => $this->is_received,
            'is_overdue' => $this->status->value === 'overdue',
            'income' => new IncomeResource($this->whenLoaded('income')),
            'category' => new CategoryResource($this->whenLoaded('category')),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }

    /**
     * Get installment label (e.g., "1/10", "2/10")
     */
    protected function getInstallmentLabel(): ?string
    {
        if ($this->installment_number && $this->total_installments) {
            return "{$this->installment_number}/{$this->total_installments}";
        }

        return null;
    }

    /**
     * Get status label
     */
    protected function getStatusLabel(): string
    {
        return match ($this->status->value) {
            'pending' => 'Pendente',
            'received' => 'Recebida',
            'overdue' => 'Atrasada',
            default => $this->status->value,
        };
    }
}
