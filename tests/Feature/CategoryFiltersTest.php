<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryFiltersTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_filter_categories_by_name(): void
    {
        $user = User::factory()->create();

        // Create test categories
        Category::create(['uuid' => \Str::uuid(), 'user_id' => $user->id, 'name' => 'Alimentação', 'is_default' => false, 'status' => true]);
        Category::create(['uuid' => \Str::uuid(), 'user_id' => $user->id, 'name' => 'Transporte', 'is_default' => false, 'status' => true]);
        Category::create(['uuid' => \Str::uuid(), 'user_id' => $user->id, 'name' => 'Lazer', 'is_default' => false, 'status' => true]);

        // Test partial name filter
        $response = $this->actingAs($user)->get(route('dashboard.categories.index', [
            'filter' => ['name' => 'Ali'],
        ]));

        $response->assertOk();

        // Get response data
        $data = $response->viewData('page')['props'];

        $this->assertCount(1, $data['categories']['data']);
        $this->assertEquals('Alimentação', $data['categories']['data'][0]['name']);
    }

    public function test_can_filter_categories_by_status(): void
    {
        $user = User::factory()->create();

        // Create active and inactive categories
        Category::create(['uuid' => \Str::uuid(), 'user_id' => $user->id, 'name' => 'Active Category', 'is_default' => false, 'status' => true]);
        Category::create(['uuid' => \Str::uuid(), 'user_id' => $user->id, 'name' => 'Inactive Category', 'is_default' => false, 'status' => false]);

        // Filter by status = true (active)
        $response = $this->actingAs($user)->get(route('dashboard.categories.index', [
            'filter' => ['status' => '1'],
        ]));

        $response->assertOk();

        $data = $response->viewData('page')['props'];
        $this->assertCount(1, $data['categories']['data']);
        $this->assertEquals('Active Category', $data['categories']['data'][0]['name']);
    }

    public function test_can_filter_categories_by_is_default(): void
    {
        $user = User::factory()->create();

        // Create user category and default category
        Category::create(['uuid' => \Str::uuid(), 'user_id' => $user->id, 'name' => 'User Category', 'is_default' => false, 'status' => true]);
        Category::create(['uuid' => \Str::uuid(), 'user_id' => null, 'name' => 'Default Category', 'is_default' => true, 'status' => true]);

        // Filter by is_default = false (user categories only)
        $response = $this->actingAs($user)->get(route('dashboard.categories.index', [
            'filter' => ['is_default' => '0'],
        ]));

        $response->assertOk();

        $data = $response->viewData('page')['props'];
        $this->assertCount(1, $data['categories']['data']);
        $this->assertEquals('User Category', $data['categories']['data'][0]['name']);
    }

    public function test_can_sort_categories_by_name(): void
    {
        $user = User::factory()->create();

        // Create categories in random order
        Category::create(['uuid' => \Str::uuid(), 'user_id' => $user->id, 'name' => 'Zebra', 'is_default' => false, 'status' => true]);
        Category::create(['uuid' => \Str::uuid(), 'user_id' => $user->id, 'name' => 'Alpha', 'is_default' => false, 'status' => true]);
        Category::create(['uuid' => \Str::uuid(), 'user_id' => $user->id, 'name' => 'Beta', 'is_default' => false, 'status' => true]);

        // Sort by name ascending
        $response = $this->actingAs($user)->get(route('dashboard.categories.index', [
            'sort' => 'name',
        ]));

        $response->assertOk();

        $data = $response->viewData('page')['props'];
        $this->assertEquals('Alpha', $data['categories']['data'][0]['name']);
        $this->assertEquals('Beta', $data['categories']['data'][1]['name']);
        $this->assertEquals('Zebra', $data['categories']['data'][2]['name']);
    }

    public function test_can_sort_categories_by_name_descending(): void
    {
        $user = User::factory()->create();

        // Create categories
        Category::create(['uuid' => \Str::uuid(), 'user_id' => $user->id, 'name' => 'Alpha', 'is_default' => false, 'status' => true]);
        Category::create(['uuid' => \Str::uuid(), 'user_id' => $user->id, 'name' => 'Beta', 'is_default' => false, 'status' => true]);
        Category::create(['uuid' => \Str::uuid(), 'user_id' => $user->id, 'name' => 'Zebra', 'is_default' => false, 'status' => true]);

        // Sort by name descending
        $response = $this->actingAs($user)->get(route('dashboard.categories.index', [
            'sort' => '-name',
        ]));

        $response->assertOk();

        $data = $response->viewData('page')['props'];
        $this->assertEquals('Zebra', $data['categories']['data'][0]['name']);
        $this->assertEquals('Beta', $data['categories']['data'][1]['name']);
        $this->assertEquals('Alpha', $data['categories']['data'][2]['name']);
    }

    public function test_default_sort_puts_default_categories_first(): void
    {
        $user = User::factory()->create();

        // Create user and default categories
        Category::create(['uuid' => \Str::uuid(), 'user_id' => $user->id, 'name' => 'User Category', 'is_default' => false, 'status' => true]);
        Category::create(['uuid' => \Str::uuid(), 'user_id' => null, 'name' => 'Default Category', 'is_default' => true, 'status' => true]);

        // No sort specified, should use default sort (default categories first)
        $response = $this->actingAs($user)->get(route('dashboard.categories.index'));

        $response->assertOk();

        $data = $response->viewData('page')['props'];
        $this->assertTrue($data['categories']['data'][0]['is_default']);
        $this->assertFalse($data['categories']['data'][1]['is_default']);
    }

    public function test_can_combine_multiple_filters(): void
    {
        $user = User::factory()->create();

        // Create various categories
        Category::create(['uuid' => \Str::uuid(), 'user_id' => $user->id, 'name' => 'Food Active', 'is_default' => false, 'status' => true]);
        Category::create(['uuid' => \Str::uuid(), 'user_id' => $user->id, 'name' => 'Food Inactive', 'is_default' => false, 'status' => false]);
        Category::create(['uuid' => \Str::uuid(), 'user_id' => $user->id, 'name' => 'Transport Active', 'is_default' => false, 'status' => true]);

        // Filter by name AND status
        $response = $this->actingAs($user)->get(route('dashboard.categories.index', [
            'filter' => [
                'name' => 'Food',
                'status' => '1',
            ],
        ]));

        $response->assertOk();

        $data = $response->viewData('page')['props'];
        $this->assertCount(1, $data['categories']['data']);
        $this->assertEquals('Food Active', $data['categories']['data'][0]['name']);
    }
}
