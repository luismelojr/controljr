<?php

namespace App\Domain\SavingsGoals\Services;

use App\Models\SavingsGoal;
use App\Models\User;

class SavingsGoalService
{
    public function getUserGoals(User $user)
    {
        return $user->savingsGoals()
            ->with(['category'])
            ->orderBy('is_active', 'desc')
            ->orderBy('target_date', 'asc')
            ->get();
    }

    public function create(User $user, array $data): SavingsGoal
    {
        return $user->savingsGoals()->create($data);
    }

    public function update(SavingsGoal $goal, array $data): SavingsGoal
    {
        $goal->update($data);
        return $goal->fresh();
    }

    public function delete(SavingsGoal $goal): bool
    {
        return $goal->delete();
    }

    public function addContribution(SavingsGoal $goal, int $amountCents): SavingsGoal
    {
        return $goal->addProgress($amountCents);
    }
}
