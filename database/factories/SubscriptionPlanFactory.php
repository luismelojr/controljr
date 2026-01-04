<?php

namespace Database\Factories;

use App\Models\SubscriptionPlan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SubscriptionPlan>
 */
class SubscriptionPlanFactory extends Factory
{
    protected $model = SubscriptionPlan::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->randomElement(['Gratuito', 'Premium', 'Família']),
            'slug' => $this->faker->unique()->slug,
            'price_cents' => $this->faker->randomElement([0, 1990, 2990]),
            'billing_period' => 'monthly',
            'features' => [
                'max_wallets' => $this->faker->numberBetween(2, 10),
                'max_categories' => $this->faker->numberBetween(10, 50),
                'financial_reports' => $this->faker->boolean,
            ],
            'description' => $this->faker->sentence,
            'is_active' => true,
            'max_users' => 1,
        ];
    }
}
