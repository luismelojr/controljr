<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Wallet>
 */
class WalletFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $cardLimit = fake()->randomFloat(2, 1000, 10000);
        $cardLimitUsed = fake()->randomFloat(2, 0, $cardLimit * 0.8); // Até 80% do limite

        return [
            'name' => fake()->randomElement(['Nubank', 'Inter', 'Itaú', 'Bradesco', 'Santander', 'C6 Bank', 'Carteira Principal']),
            'type' => fake()->randomElement(['card_credit', 'bank_account', 'other']),
            'day_close' => fake()->numberBetween(1, 28),
            'best_shopping_day' => fake()->numberBetween(1, 28),
            'card_limit' => $cardLimit,
            'card_limit_used' => $cardLimitUsed,
            'status' => fake()->boolean(90), // 90% ativas
        ];
    }
}
