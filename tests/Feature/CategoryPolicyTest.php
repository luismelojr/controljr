<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_their_own_categories(): void
    {
        $user = User::factory()->create();
        $category = Category::create([
            'uuid' => \Str::uuid(),
            'user_id' => $user->id,
            'name' => 'Test Category',
            'is_default' => false,
            'status' => true,
        ]);

        $response = $this->actingAs($user)->get(route('dashboard.categories.edit', $category));

        $response->assertOk();
    }

    public function test_user_can_view_default_categories_in_list(): void
    {
        $user = User::factory()->create();
        $defaultCategory = Category::create([
            'uuid' => \Str::uuid(),
            'user_id' => null,
            'name' => 'Default Category',
            'is_default' => true,
            'status' => true,
        ]);

        // User can view default categories in the index
        $response = $this->actingAs($user)->get(route('dashboard.categories.index'));

        $response->assertOk();
    }

    public function test_user_cannot_edit_default_categories(): void
    {
        $user = User::factory()->create();
        $defaultCategory = Category::create([
            'uuid' => \Str::uuid(),
            'user_id' => null,
            'name' => 'Default Category',
            'is_default' => true,
            'status' => true,
        ]);

        // User cannot access edit form for default categories
        $response = $this->actingAs($user)->get(route('dashboard.categories.edit', $defaultCategory));

        $response->assertForbidden();
    }

    public function test_user_cannot_view_other_users_category(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $category = Category::create([
            'uuid' => \Str::uuid(),
            'user_id' => $user2->id,
            'name' => 'User 2 Category',
            'is_default' => false,
            'status' => true,
        ]);

        $response = $this->actingAs($user1)->get(route('dashboard.categories.edit', $category));

        $response->assertForbidden();
    }

    public function test_user_can_update_their_own_category(): void
    {
        $user = User::factory()->create();
        $category = Category::create([
            'uuid' => \Str::uuid(),
            'user_id' => $user->id,
            'name' => 'Original Name',
            'is_default' => false,
            'status' => true,
        ]);

        $response = $this->actingAs($user)->put(route('dashboard.categories.update', $category), [
            'name' => 'Updated Name',
            'status' => true,
        ]);

        $response->assertRedirect(route('dashboard.categories.index'));
        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Updated Name',
        ]);
    }

    public function test_user_cannot_update_default_category(): void
    {
        $user = User::factory()->create();
        $defaultCategory = Category::create([
            'uuid' => \Str::uuid(),
            'user_id' => null,
            'name' => 'Default Category',
            'is_default' => true,
            'status' => true,
        ]);

        $response = $this->actingAs($user)->put(route('dashboard.categories.update', $defaultCategory), [
            'name' => 'Hacked Name',
            'status' => true,
        ]);

        $response->assertForbidden();

        // Verify category was not updated
        $this->assertDatabaseHas('categories', [
            'id' => $defaultCategory->id,
            'name' => 'Default Category',
        ]);
    }

    public function test_user_cannot_update_other_users_category(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $category = Category::create([
            'uuid' => \Str::uuid(),
            'user_id' => $user2->id,
            'name' => 'User 2 Category',
            'is_default' => false,
            'status' => true,
        ]);

        $response = $this->actingAs($user1)->put(route('dashboard.categories.update', $category), [
            'name' => 'Hacked Name',
            'status' => true,
        ]);

        $response->assertForbidden();

        // Verify category was not updated
        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'User 2 Category',
        ]);
    }

    public function test_user_can_delete_their_own_category(): void
    {
        $user = User::factory()->create();
        $category = Category::create([
            'uuid' => \Str::uuid(),
            'user_id' => $user->id,
            'name' => 'To Delete',
            'is_default' => false,
            'status' => true,
        ]);

        $response = $this->actingAs($user)->delete(route('dashboard.categories.destroy', $category));

        $response->assertRedirect(route('dashboard.categories.index'));
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    public function test_user_cannot_delete_default_category(): void
    {
        $user = User::factory()->create();
        $defaultCategory = Category::create([
            'uuid' => \Str::uuid(),
            'user_id' => null,
            'name' => 'Default Category',
            'is_default' => true,
            'status' => true,
        ]);

        $response = $this->actingAs($user)->delete(route('dashboard.categories.destroy', $defaultCategory));

        $response->assertForbidden();

        // Verify category still exists
        $this->assertDatabaseHas('categories', ['id' => $defaultCategory->id]);
    }

    public function test_user_cannot_delete_other_users_category(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $category = Category::create([
            'uuid' => \Str::uuid(),
            'user_id' => $user2->id,
            'name' => 'User 2 Category',
            'is_default' => false,
            'status' => true,
        ]);

        $response = $this->actingAs($user1)->delete(route('dashboard.categories.destroy', $category));

        $response->assertForbidden();

        // Verify category still exists
        $this->assertDatabaseHas('categories', ['id' => $category->id]);
    }

    public function test_guest_cannot_access_categories(): void
    {
        $category = Category::create([
            'uuid' => \Str::uuid(),
            'user_id' => null,
            'name' => 'Test Category',
            'is_default' => true,
            'status' => true,
        ]);

        $response = $this->get(route('dashboard.categories.edit', $category));
        $response->assertRedirect(route('login'));

        $response = $this->put(route('dashboard.categories.update', $category), []);
        $response->assertRedirect(route('login'));

        $response = $this->delete(route('dashboard.categories.destroy', $category));
        $response->assertRedirect(route('login'));
    }
}
