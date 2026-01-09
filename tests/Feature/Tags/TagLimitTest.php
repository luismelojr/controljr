<?php

namespace Tests\Feature\Tags;

use App\Models\User;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Laravel\Cashier\Subscription;

use App\Models\SubscriptionPlan;

class TagLimitTest extends TestCase
{
    use RefreshDatabase;

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

    public function test_free_user_cannot_create_tags_if_limit_is_zero()
    {
        $user = User::factory()->create();
        // Free plan with 0 tags allowed
        $this->subscribeUser($user, 'free', ['max_tags' => 0]);

        // Acting as free user
        $this->actingAs($user);

        // Attempt to create a tag
        $response = $this->post(route('dashboard.tags.store'), [
            'name' => 'Tag 1',
            'color' => '#000000',
        ]);

        // Should redirect back with error toast
        $response->assertRedirect();
        $response->assertSessionHas('toasts'); 
        // Assert database is empty
        $this->assertDatabaseCount('tags', 0);
    }

    public function test_premium_user_can_create_tags_beyond_free_limit()
    {
        $user = User::factory()->create();
        // Premium plan with unlimited tags
        $this->subscribeUser($user, 'premium', ['max_tags' => -1]);

        $this->actingAs($user);

        // Create reasonable amount
        for ($i = 0; $i < 5; $i++) {
            $this->post(route('dashboard.tags.store'), [
                'name' => "Tag $i",
                'color' => '#000000',
            ]);
        }

        $this->assertDatabaseCount('tags', 5);
    }
}
