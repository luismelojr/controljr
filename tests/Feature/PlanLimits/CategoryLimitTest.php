<?php

namespace Tests\Feature\PlanLimits;

use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryLimitTest extends TestCase
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

    public function test_free_plan_can_create_limited_categories()
    {
        // Free plan allows 10 categories
        $plan = SubscriptionPlan::factory()->create([
            'slug' => 'free',
            'features' => ['max_categories' => 10],
        ]);

        $user = User::factory()->create();
        $this->subscribeUser($user, $plan);

        $this->actingAs($user);

        // Create 10 categories
        for ($i = 0; $i < 10; $i++) {
            $response = $this->post(route('dashboard.categories.store'), [
                'name' => "Category $i",
                'color' => '#FFFFFF',
                'type' => 'expense',
            ]);
            $response->assertRedirect(route('dashboard.categories.index'));
        }

        $this->assertDatabaseCount('categories', 10);

        // 11th category should fail
        $response = $this->post(route('dashboard.categories.store'), [
            'name' => 'Category 11',
            'color' => '#000000',
            'type' => 'expense',
        ]);

        $response->assertSessionHas('toasts');
        $this->assertDatabaseCount('categories', 10);
    }

    public function test_premium_plan_can_create_unlimited_categories()
    {
        // Premium plan allows unlimited categories (-1)
        $plan = SubscriptionPlan::factory()->create([
            'slug' => 'premium',
            'features' => ['max_categories' => -1],
        ]);

        $user = User::factory()->create();
        $this->subscribeUser($user, $plan);

        $this->actingAs($user);

        // Create 15 categories (more than free limit)
        for ($i = 0; $i < 15; $i++) {
            $response = $this->post(route('dashboard.categories.store'), [
                'name' => "Category $i",
                'color' => '#FFFFFF',
                'type' => 'expense',
            ]);
            $response->assertRedirect(route('dashboard.categories.index'));
        }

        $this->assertDatabaseCount('categories', 15);
    }
}
