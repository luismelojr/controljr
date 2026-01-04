<?php

namespace Tests\Feature;

use App\Models\SavingsGoal;
use App\Models\User;
use App\Models\SubscriptionPlan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Inertia\Testing\AssertableInertia as Assert;

class SavingsGoalsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Seed plans if needed, or create them in test. 
        // Assuming plans exist or we mock limits.
        // We will mock limits via plan subscription.
    }

    private function subscribeUser(User $user, string $planSlug, array $features)
    {
        $plan = SubscriptionPlan::factory()->create([
            'slug' => $planSlug,
            'features' => $features,
        ]);

        $subscription = \App\Models\Subscription::factory()->create([
            'user_id' => $user->id,
            'subscription_plan_id' => $plan->id,
            'status' => \App\Enums\SubscriptionStatusEnum::ACTIVE->value,
        ]);

        $user->update(['current_subscription_id' => $subscription->id]);
    }

    public function test_premium_user_can_create_savings_goal()
    {
        $user = User::factory()->create();
        $this->subscribeUser($user, 'premium', ['max_savings_goals' => 20]);

        $response = $this->actingAs($user)->post(route('dashboard.savings-goals.store'), [
            'name' => 'New Car',
            'target_amount' => 50000.00,
            'target_date' => now()->addYear()->format('Y-m-d'),
            'icon' => '🚗',
            'color' => '#FF0000',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('savings_goals', [
            'user_id' => $user->id,
            'name' => 'New Car',
            'target_amount_cents' => 5000000,
        ]);
    }

    public function test_free_user_cannot_create_savings_goal_if_limit_reached()
    {
        $user = User::factory()->create();
        // Free plan with 0 goals allowed (as per implementation plan note)
        $this->subscribeUser($user, 'free', ['max_savings_goals' => 0]);

        $response = $this->actingAs($user)->post(route('dashboard.savings-goals.store'), [
            'name' => 'Dream Vacation',
            'target_amount' => 10000.00,
        ]);

        $response->assertRedirect(); // Should redirect back with error
        $this->assertDatabaseMissing('savings_goals', [
            'name' => 'Dream Vacation',
        ]);
        
        // Assert error toast (session has toasts)
         $response->assertSessionHas('toasts');
    }
    
    public function test_user_can_view_savings_goals()
    {
        $user = User::factory()->create();
        $this->subscribeUser($user, 'premium', ['max_savings_goals' => 20]);
        
        SavingsGoal::create([
            'user_id' => $user->id,
            'name' => 'Emergency Fund',
            'target_amount_cents' => 1000000,
            'uuid' => (string) \Illuminate\Support\Str::uuid(),
        ]);

        $response = $this->actingAs($user)->get(route('dashboard.savings-goals.index'));

        $response->assertStatus(200)
            ->assertInertia(fn (Assert $page) => $page
                ->component('dashboard/savings-goals/index')
                ->has('goals', 1)
                ->where('goals.0.name', 'Emergency Fund')
            );
    }

    public function test_user_can_update_savings_goal()
    {
        $user = User::factory()->create();
        $this->subscribeUser($user, 'premium', ['max_savings_goals' => 20]);
        
        $goal = SavingsGoal::create([
            'user_id' => $user->id,
            'name' => 'Old Name',
            'target_amount_cents' => 1000000,
            'uuid' => (string) \Illuminate\Support\Str::uuid(),
        ]);

        $response = $this->actingAs($user)->patch(route('dashboard.savings-goals.update', $goal), [
            'name' => 'New Name',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('savings_goals', [
            'id' => $goal->id,
            'name' => 'New Name',
        ]);
    }

    public function test_user_can_add_contribution()
    {
        $user = User::factory()->create();
        $this->subscribeUser($user, 'premium', ['max_savings_goals' => 20]);
        
        $goal = SavingsGoal::create([
            'user_id' => $user->id,
            'name' => 'House',
            'target_amount_cents' => 1000000,
            'current_amount_cents' => 0,
            'uuid' => (string) \Illuminate\Support\Str::uuid(),
        ]);

        $response = $this->actingAs($user)->post(route('dashboard.savings-goals.contribute', $goal), [
            'amount' => 500.00,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('savings_goals', [
            'id' => $goal->id,
            'current_amount_cents' => 50000, // 500 * 100
        ]);
        
        $goal->refresh();
        $this->assertEquals(50000, $goal->current_amount_cents);
    }
    
    public function test_user_cannot_update_others_goal()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $this->subscribeUser($user, 'premium', []); // Empty features array ok for this test
        
        $goal = SavingsGoal::create([
            'user_id' => $otherUser->id,
            'name' => 'Others Goal',
            'target_amount_cents' => 1000,
            'uuid' => (string) \Illuminate\Support\Str::uuid(),
        ]);

        $response = $this->actingAs($user)->patch(route('dashboard.savings-goals.update', $goal), [
            'name' => 'Hacked Name',
        ]);

        $response->assertForbidden();
        $this->assertDatabaseHas('savings_goals', [
            'id' => $goal->id,
            'name' => 'Others Goal',
        ]);
    }
}
