<?php

namespace Database\Factories;

use App\Models\Payment;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payment>
 */
class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'subscription_id' => Subscription::factory(),
            'amount_cents' => $this->faker->numberBetween(1990, 9990), // R$ 19,90 to R$ 99,90
            'status' => 'pending',
            'payment_method' => $this->faker->randomElement(['pix', 'boleto', 'credit_card']),
            'payment_gateway' => 'asaas',
            'external_payment_id' => $this->faker->uuid,
            'invoice_url' => $this->faker->url,
            'due_date' => now()->addDays(3),
        ];
    }

    /**
     * Indicate that the payment is for PIX.
     */
    public function pix(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_method' => 'pix',
            'pix_qr_code' => $this->faker->text(500),
            'pix_copy_paste' => $this->faker->text(200),
        ]);
    }

    /**
     * Indicate that the payment is for Boleto.
     */
    public function boleto(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_method' => 'boleto',
            'boleto_barcode' => $this->faker->numerify('####################'),
        ]);
    }

    /**
     * Indicate that the payment is confirmed.
     */
    public function confirmed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'confirmed',
            'confirmed_at' => now(),
        ]);
    }

    /**
     * Indicate that the payment is received.
     */
    public function received(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'received',
            'confirmed_at' => now(),
            'paid_at' => now(),
        ]);
    }

    /**
     * Indicate that the payment is overdue.
     */
    public function overdue(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'overdue',
            'due_date' => now()->subDays(3),
        ]);
    }
}
