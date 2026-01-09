<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionPlanResource extends JsonResource
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
            'slug' => $this->slug,
            'price' => $this->price,
            'price_formatted' => $this->price_formatted,
            'price_cents' => $this->price_cents,
            'billing_period' => $this->billing_period,
            'features' => $this->features,
            'description' => $this->description,
            'max_users' => $this->max_users,
            'is_active' => $this->is_active,
            'is_free' => $this->isFree(),
            'is_premium' => $this->isPremium(),
            'plan_type' => $this->planType?->label(),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
