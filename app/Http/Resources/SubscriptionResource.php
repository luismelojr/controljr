<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionResource extends JsonResource
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
            'user_id' => $this->user_id,
            'plan' => new SubscriptionPlanResource($this->whenLoaded('plan')),
            'started_at' => $this->started_at?->toISOString(),
            'ends_at' => $this->ends_at?->toISOString(),
            'cancelled_at' => $this->cancelled_at?->toISOString(),
            'status' => $this->status,
            'status_label' => $this->status_label,
            'status_color' => $this->statusEnum?->color(),
            'payment_gateway' => $this->payment_gateway,
            'external_subscription_id' => $this->external_subscription_id,
            'is_active' => $this->isActive(),
            'is_cancelled' => $this->isCancelled(),
            'is_expired' => $this->isExpired(),
            'is_pending' => $this->isPending(),
            'on_grace_period' => $this->onGracePeriod(),
            'days_remaining' => $this->daysRemaining(),
            'can_resume' => $this->onGracePeriod(),
            'can_cancel' => $this->isActive() && !$this->isCancelled(),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
