<?php

namespace App\Policies;

use App\Models\Transaction;
use App\Models\User;

class TransactionPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Transaction $transaction): bool
    {
        return $transaction->user_id === $user->id;
    }

    /**
     * Determine whether the user can update the model (mark as paid/unpaid).
     */
    public function update(User $user, Transaction $transaction): bool
    {
        return $transaction->user_id === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     * Transactions should not be deletable directly (only through account deletion)
     */
    public function delete(User $user, Transaction $transaction): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Transaction $transaction): bool
    {
        return $transaction->user_id === $user->id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Transaction $transaction): bool
    {
        return false; // Never allow force delete
    }
}
