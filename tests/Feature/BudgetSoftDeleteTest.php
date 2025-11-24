<?php

namespace Tests\Feature;

use App\Models\Budget;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BudgetSoftDeleteTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_restore_soft_deleted_budget_on_create(): void
    {
        $user = User::factory()->create();
        $category = Category::create([
            'uuid' => \Str::uuid(),
            'user_id' => $user->id,
            'name' => 'Test Category',
            'is_default' => false,
            'status' => true
        ]);

        // 1. Create initial budget
        $response = $this->actingAs($user)->post(route('dashboard.budgets.store'), [
            'category_id' => $category->id,
            'amount' => 1000,
            'period' => now()->startOfMonth()->format('Y-m-d'),
            'recurrence' => 'monthly',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('budgets', [
            'user_id' => $user->id,
            'category_id' => $category->id,
            'amount' => 1000,
            'deleted_at' => null,
        ]);

        $budget = Budget::first();

        // 2. Delete the budget
        $response = $this->actingAs($user)->delete(route('dashboard.budgets.destroy', $budget));
        $response->assertRedirect();
        
        $this->assertSoftDeleted('budgets', [
            'id' => $budget->id,
        ]);

        // 3. Try to create the same budget again (should restore)
        $response = $this->actingAs($user)->post(route('dashboard.budgets.store'), [
            'category_id' => $category->id,
            'amount' => 2000, // Changed amount to verify update
            'period' => now()->startOfMonth()->format('Y-m-d'),
            'recurrence' => 'monthly',
        ]);

        $response->assertRedirect();
        
        // 4. Verify it was restored and updated
        $this->assertDatabaseHas('budgets', [
            'id' => $budget->id,
            'user_id' => $user->id,
            'category_id' => $category->id,
            'amount' => 2000,
            'deleted_at' => null,
        ]);
        
        $this->assertEquals(1, Budget::count());
    }
}
