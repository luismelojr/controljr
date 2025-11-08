<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Gate;

class TestPoliciesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:policies';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test if policies are working correctly for Wallets and Categories';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🔐 Testing Policy System...');
        $this->newLine();

        // Create test users
        $user1 = User::factory()->create(['name' => 'Test User 1']);
        $user2 = User::factory()->create(['name' => 'Test User 2']);

        $this->info("✅ Created test users:");
        $this->line("   - {$user1->name} (ID: {$user1->id})");
        $this->line("   - {$user2->name} (ID: {$user2->id})");
        $this->newLine();

        // Test Wallet Policies
        $this->testWalletPolicies($user1, $user2);
        $this->newLine();

        // Test Category Policies
        $this->testCategoryPolicies($user1, $user2);
        $this->newLine();

        // Cleanup
        $user1->delete();
        $user2->delete();

        $this->info('✅ All tests completed successfully!');

        return Command::SUCCESS;
    }

    private function testWalletPolicies(User $user1, User $user2): void
    {
        $this->info('📁 Testing Wallet Policies:');

        // Create wallet for user1
        $wallet = Wallet::factory()->create(['user_id' => $user1->id]);

        // Test viewAny
        $canViewAny = Gate::forUser($user1)->allows('viewAny', Wallet::class);
        $this->assertPolicy('viewAny (Wallet)', $canViewAny, true);

        // Test view - own wallet
        $canView = Gate::forUser($user1)->allows('view', $wallet);
        $this->assertPolicy('view own wallet', $canView, true);

        // Test view - other user's wallet
        $cannotView = Gate::forUser($user2)->allows('view', $wallet);
        $this->assertPolicy('view other user wallet', $cannotView, false);

        // Test create
        $canCreate = Gate::forUser($user1)->allows('create', Wallet::class);
        $this->assertPolicy('create wallet', $canCreate, true);

        // Test update - own wallet
        $canUpdate = Gate::forUser($user1)->allows('update', $wallet);
        $this->assertPolicy('update own wallet', $canUpdate, true);

        // Test update - other user's wallet
        $cannotUpdate = Gate::forUser($user2)->allows('update', $wallet);
        $this->assertPolicy('update other user wallet', $cannotUpdate, false);

        // Test delete - own wallet
        $canDelete = Gate::forUser($user1)->allows('delete', $wallet);
        $this->assertPolicy('delete own wallet', $canDelete, true);

        // Test delete - other user's wallet
        $cannotDelete = Gate::forUser($user2)->allows('delete', $wallet);
        $this->assertPolicy('delete other user wallet', $cannotDelete, false);

        // Test forceDelete - should be blocked for everyone
        $cannotForceDelete = Gate::forUser($user1)->allows('forceDelete', $wallet);
        $this->assertPolicy('forceDelete (blocked)', $cannotForceDelete, false);

        $wallet->delete();
    }

    private function testCategoryPolicies(User $user1, User $user2): void
    {
        $this->info('📁 Testing Category Policies:');

        // Create user category for user1
        $userCategory = Category::create([
            'uuid' => \Str::uuid(),
            'user_id' => $user1->id,
            'name' => 'Test Category',
            'is_default' => false,
            'status' => true,
        ]);

        // Create default category
        $defaultCategory = Category::create([
            'uuid' => \Str::uuid(),
            'user_id' => null,
            'name' => 'Default Category Test',
            'is_default' => true,
            'status' => true,
        ]);

        // Test viewAny
        $canViewAny = Gate::forUser($user1)->allows('viewAny', Category::class);
        $this->assertPolicy('viewAny (Category)', $canViewAny, true);

        // Test view - own category
        $canView = Gate::forUser($user1)->allows('view', $userCategory);
        $this->assertPolicy('view own category', $canView, true);

        // Test view - default category
        $canViewDefault = Gate::forUser($user1)->allows('view', $defaultCategory);
        $this->assertPolicy('view default category', $canViewDefault, true);

        // Test view - other user's category
        $cannotView = Gate::forUser($user2)->allows('view', $userCategory);
        $this->assertPolicy('view other user category', $cannotView, false);

        // Test create
        $canCreate = Gate::forUser($user1)->allows('create', Category::class);
        $this->assertPolicy('create category', $canCreate, true);

        // Test update - own category
        $canUpdate = Gate::forUser($user1)->allows('update', $userCategory);
        $this->assertPolicy('update own category', $canUpdate, true);

        // Test update - default category (should be blocked)
        $cannotUpdateDefault = Gate::forUser($user1)->allows('update', $defaultCategory);
        $this->assertPolicy('update default category (blocked)', $cannotUpdateDefault, false);

        // Test update - other user's category
        $cannotUpdate = Gate::forUser($user2)->allows('update', $userCategory);
        $this->assertPolicy('update other user category', $cannotUpdate, false);

        // Test delete - own category
        $canDelete = Gate::forUser($user1)->allows('delete', $userCategory);
        $this->assertPolicy('delete own category', $canDelete, true);

        // Test delete - default category (should be blocked)
        $cannotDeleteDefault = Gate::forUser($user1)->allows('delete', $defaultCategory);
        $this->assertPolicy('delete default category (blocked)', $cannotDeleteDefault, false);

        // Test delete - other user's category
        $cannotDelete = Gate::forUser($user2)->allows('delete', $userCategory);
        $this->assertPolicy('delete other user category', $cannotDelete, false);

        // Test forceDelete - should be blocked for everyone
        $cannotForceDelete = Gate::forUser($user1)->allows('forceDelete', $userCategory);
        $this->assertPolicy('forceDelete (blocked)', $cannotForceDelete, false);

        $userCategory->delete();
        $defaultCategory->delete();
    }

    private function assertPolicy(string $test, bool $actual, bool $expected): void
    {
        if ($actual === $expected) {
            $this->line("   ✅ {$test}: " . ($actual ? 'allowed' : 'denied'));
        } else {
            $this->error("   ❌ {$test}: expected " . ($expected ? 'allowed' : 'denied') . ", got " . ($actual ? 'allowed' : 'denied'));
        }
    }
}
