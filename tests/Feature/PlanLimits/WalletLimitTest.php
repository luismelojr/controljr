<?php

namespace Tests\Feature\PlanLimits;

use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WalletLimitTest extends TestCase
{
    use RefreshDatabase;


    private function subscribeUser(User $user, SubscriptionPlan $plan)
    {
        $subscription = \App\Models\Subscription::factory()->create([
            'user_id' => $user->id,
            'subscription_plan_id' => $plan->id,
            'status' => \App\Enums\SubscriptionStatusEnum::ACTIVE->value,
        ]);

        $user->update(['current_subscription_id' => $subscription->id]);
    }

    public function test_free_plan_can_create_limited_wallets()
    {
        // Free plan allows 1 wallet
        $plan = SubscriptionPlan::factory()->create([
            'slug' => 'free',
            'features' => ['max_wallets' => 1],
        ]);

        $user = User::factory()->create();
        $this->subscribeUser($user, $plan);

        $this->actingAs($user);

        // First wallet should succeed
        $response = $this->post(route('dashboard.wallets.store'), [
            'name' => 'Wallet 1',
            'color' => '#FFFFFF',
            'type' => 'bank_account',
            'initial_balance' => 1000.00,
            'card_limit' => 0.0,
        ]);

        $response->assertRedirect(route('dashboard.wallets.index'));
        $this->assertDatabaseCount('wallets', 1);

        // Second wallet should fail
        $response = $this->post(route('dashboard.wallets.store'), [
            'name' => 'Wallet 2',
            'color' => '#000000',
            'type' => 'bank_account',
            'initial_balance' => 0,
            'card_limit' => 0.0,
        ]);

        $response->assertSessionHas('toasts'); // Assuming Toast uses session
        $this->assertDatabaseCount('wallets', 1);
    }

    public function test_premium_plan_can_create_unlimited_wallets()
    {
        // Premium plan allows unlimited wallets (-1)
        $plan = SubscriptionPlan::factory()->create([
            'slug' => 'premium',
            'features' => ['max_wallets' => -1],
        ]);

        $user = User::factory()->create();
        $this->subscribeUser($user, $plan);

        $this->actingAs($user);

        // Create 5 wallets
        for ($i = 0; $i < 5; $i++) {
            $response = $this->post(route('dashboard.wallets.store'), [
                'name' => "Wallet $i",
                'color' => '#FFFFFF',
                'type' => 'bank_account',
                'initial_balance' => 1000.00,
                'card_limit' => 0.0,
            ]);
            $response->assertRedirect(route('dashboard.wallets.index'));
        }

        $this->assertDatabaseCount('wallets', 5);
    }
}
