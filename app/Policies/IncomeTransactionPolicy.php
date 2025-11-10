<?php

namespace App\Policies;

use App\Models\IncomeTransaction;
use App\Models\User;

class IncomeTransactionPolicy
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
    public function view(User $user, IncomeTransaction $incomeTransaction): bool
    {
        return $incomeTransaction->user_id === $user->id;
    }

    /**
     * Determine whether the user can update the model (mark as received/not received).
     */
    public function update(User $user, IncomeTransaction $incomeTransaction): bool
    {
        return $incomeTransaction->user_id === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     * Income transactions should not be deletable directly (only through income deletion)
     */
    public function delete(User $user, IncomeTransaction $incomeTransaction): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, IncomeTransaction $incomeTransaction): bool
    {
        return $incomeTransaction->user_id === $user->id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, IncomeTransaction $incomeTransaction): bool
    {
        return false; // Never allow force delete
    }
}
