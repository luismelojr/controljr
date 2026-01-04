<?php

namespace Tests\Feature\Tags;

use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

use App\Models\SubscriptionPlan;

class TagCrudTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    private function subscribeUser(User $user, string $planSlug = 'premium')
    {
         $plan = SubscriptionPlan::factory()->create([
            'slug' => $planSlug,
            'features' => ['max_tags' => -1], // Unlimited for premium
        ]);

        $subscription = \App\Models\Subscription::factory()->create([
            'user_id' => $user->id,
            'subscription_plan_id' => $plan->id,
            'status' => \App\Enums\SubscriptionStatusEnum::ACTIVE->value,
        ]);

        $user->update(['current_subscription_id' => $subscription->id]);
    }

    private function createPremiumUser()
    {
        $user = User::factory()->create();
        $this->subscribeUser($user, 'premium');
        return $user;
    }

    public function test_user_can_view_tags()
    {
        $user = $this->createPremiumUser();
        $this->actingAs($user);

        // Create some tags
        Tag::factory()->count(3)->create(['user_id' => $user->id]);

        $response = $this->get(route('dashboard.tags.index'));

        $response->assertStatus(200)
            ->assertInertia(fn ($page) => $page
                ->component('dashboard/tags/index')
                ->has('tags', 3)
            );
    }

    public function test_user_can_create_tag()
    {
        $user = $this->createPremiumUser();
        $this->actingAs($user);

        $tagData = [
            'name' => 'Viagem',
            'color' => '#FF5733',
        ];

        $response = $this->post(route('dashboard.tags.store'), $tagData);

        $response->assertRedirect();
        $this->assertDatabaseHas('tags', [
            'user_id' => $user->id,
            'name' => 'Viagem',
            'color' => '#FF5733',
        ]);
    }

    public function test_user_cannot_create_duplicate_tag_name()
    {
        $user = $this->createPremiumUser();
        $this->actingAs($user);

        Tag::factory()->create(['user_id' => $user->id, 'name' => 'Existing']);

        $response = $this->post(route('dashboard.tags.store'), [
            'name' => 'Existing',
            'color' => '#000000',
        ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_user_can_update_tag()
    {
        $user = $this->createPremiumUser();
        $this->actingAs($user);

        $tag = Tag::factory()->create(['user_id' => $user->id]);

        $response = $this->put(route('dashboard.tags.update', $tag), [
            'name' => 'Updated Name',
            'color' => '#123456',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('tags', [
            'id' => $tag->id,
            'name' => 'Updated Name',
            'color' => '#123456',
        ]);
    }

    public function test_user_can_delete_tag()
    {
        $user = $this->createPremiumUser();
        $this->actingAs($user);

        $tag = Tag::factory()->create(['user_id' => $user->id]);

        $response = $this->delete(route('dashboard.tags.destroy', $tag));

        $response->assertRedirect();
        $this->assertDatabaseMissing('tags', ['id' => $tag->id]);
    }

    public function test_user_cannot_manage_others_tags()
    {
        $user = $this->createPremiumUser();
        $otherUser = User::factory()->create();
        $otherTag = Tag::factory()->create(['user_id' => $otherUser->id]);

        $this->actingAs($user);

        $response = $this->put(route('dashboard.tags.update', $otherTag), [
            'name' => 'Hacked',
            'color' => '#000000',
        ]);

        $response->assertStatus(403);

        $response = $this->delete(route('dashboard.tags.destroy', $otherTag));
        $response->assertStatus(403);
    }
}
