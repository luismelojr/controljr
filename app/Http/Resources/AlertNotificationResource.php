<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AlertNotificationResource extends JsonResource
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
            'title' => $this->title,
            'message' => $this->message,
            'type' => $this->type,
            'type_label' => $this->getTypeLabel(),
            'is_read' => $this->is_read,
            'read_at' => $this->read_at?->toISOString(),
            'data' => $this->data,
            'alert' => $this->when($this->relationLoaded('alert'), function () {
                return new AlertResource($this->alert);
            }),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
            'created_at_human' => $this->created_at?->diffForHumans(),
        ];
    }

    /**
     * Get notification type label
     */
    protected function getTypeLabel(): string
    {
        return match ($this->type) {
            'info' => 'Informação',
            'warning' => 'Aviso',
            'danger' => 'Perigo',
            'success' => 'Sucesso',
            default => $this->type,
        };
    }
}
