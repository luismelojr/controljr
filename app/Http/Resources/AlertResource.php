<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AlertResource extends JsonResource
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
            'type' => $this->type,
            'type_label' => $this->getTypeLabel(),
            'trigger_value' => $this->trigger_value,
            'trigger_days' => $this->trigger_days,
            'notification_channels' => $this->notification_channels,
            'is_active' => $this->is_active,
            'last_triggered_at' => $this->last_triggered_at?->toISOString(),
            'alertable' => $this->when($this->alertable, function () {
                return [
                    'type' => $this->alertable_type,
                    'id' => $this->alertable_id,
                    'name' => $this->alertable->name ?? 'Sem nome',
                ];
            }),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }

    /**
     * Get alert type label
     */
    protected function getTypeLabel(): string
    {
        return match ($this->type) {
            'credit_card_usage' => 'Uso de Cartão de Crédito',
            'bill_due_date' => 'Vencimento de Contas',
            'account_balance' => 'Saldo da Conta',
            'budget_exceeded' => 'Orçamento Excedido',
            default => $this->type,
        };
    }
}
