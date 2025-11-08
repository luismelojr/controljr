<?php

namespace App\Domain\Wallets\Services;

use App\Domain\Wallets\DTO\CreateWalletData;
use App\Domain\Wallets\DTO\UpdateWalletData;
use App\Models\User;
use App\Models\Wallet;

class WalletService
{
    /**
     * Create a new wallet.
     */
    public function create(CreateWalletData $data, User $user): Wallet
    {
        return $user->wallets()->create($data->toArray());
    }

    /**
     * Update an existing wallet.
     */
    public function update(Wallet $wallet, UpdateWalletData $data): Wallet
    {
        $wallet->update($data->toArray());

        return $wallet->fresh();
    }

    /**
     * Delete a wallet.
     */
    public function delete(Wallet $wallet): bool
    {
        return $wallet->delete();
    }

    /**
     * Toggle wallet status.
     */
    public function toggleStatus(Wallet $wallet): Wallet
    {
        $wallet->update(['status' => !$wallet->status]);

        return $wallet->fresh();
    }
}
