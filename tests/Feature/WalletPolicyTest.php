<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WalletPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_their_own_wallets(): void
    {
        $user = User::factory()->create();
        $wallet = Wallet::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get(route('dashboard.wallets.edit', $wallet));

        $response->assertOk();
    }

    public function test_user_cannot_view_other_users_wallet(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $wallet = Wallet::factory()->create(['user_id' => $user2->id]);

        $response = $this->actingAs($user1)->get(route('dashboard.wallets.edit', $wallet));

        $response->assertForbidden();
    }

    public function test_user_can_update_their_own_wallet(): void
    {
        $user = User::factory()->create();
        $wallet = Wallet::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->put(route('dashboard.wallets.update', $wallet), [
            'name' => 'Updated Wallet',
            'type' => 'bank_account',
        ]);

        $response->assertRedirect(route('dashboard.wallets.index'));
    }

    public function test_user_cannot_update_other_users_wallet(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $wallet = Wallet::factory()->create(['user_id' => $user2->id]);

        $response = $this->actingAs($user1)->put(route('dashboard.wallets.update', $wallet), [
            'name' => 'Hacked Wallet',
            'type' => 'bank_account',
        ]);

        $response->assertForbidden();

        // Verify wallet was not updated
        $this->assertDatabaseMissing('wallets', [
            'id' => $wallet->id,
            'name' => 'Hacked Wallet',
        ]);
    }

    public function test_user_can_delete_their_own_wallet(): void
    {
        $user = User::factory()->create();
        $wallet = Wallet::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->delete(route('dashboard.wallets.destroy', $wallet));

        $response->assertRedirect(route('dashboard.wallets.index'));
        $this->assertDatabaseMissing('wallets', ['id' => $wallet->id]);
    }

    public function test_user_cannot_delete_other_users_wallet(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $wallet = Wallet::factory()->create(['user_id' => $user2->id]);

        $response = $this->actingAs($user1)->delete(route('dashboard.wallets.destroy', $wallet));

        $response->assertForbidden();

        // Verify wallet still exists
        $this->assertDatabaseHas('wallets', ['id' => $wallet->id]);
    }

    public function test_guest_cannot_access_wallets(): void
    {
        $wallet = Wallet::factory()->create();

        $response = $this->get(route('dashboard.wallets.edit', $wallet));
        $response->assertRedirect(route('login'));

        $response = $this->put(route('dashboard.wallets.update', $wallet), []);
        $response->assertRedirect(route('login'));

        $response = $this->delete(route('dashboard.wallets.destroy', $wallet));
        $response->assertRedirect(route('login'));
    }
}
