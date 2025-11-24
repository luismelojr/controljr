<?php

namespace App\Domain\Budgets\Services;

use App\Domain\Budgets\DTO\CreateBudgetData;
use App\Domain\Budgets\DTO\UpdateBudgetData;
use App\Models\Budget;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class BudgetService
{
    public function create(User $user, CreateBudgetData $data): Budget
    {
        $period = Carbon::parse($data->period)->startOfMonth();

        $query = Budget::withTrashed()
            ->where('user_id', $user->id)
            ->where('category_id', $data->category_id)
            ->whereDate('period', $period);
            
        $budget = $query->first();

        if ($budget) {
            if ($budget->trashed()) {
                $budget->restore();
            }

            $budget->update([
                'amount' => $data->amount,
                'recurrence' => $data->recurrence,
                'status' => true, // Ensure it's active when recreated
            ]);

            return $budget;
        }

        return Budget::create([
            'user_id' => $user->id,
            'category_id' => $data->category_id,
            'amount' => $data->amount,
            'period' => $period,
            'recurrence' => $data->recurrence,
        ]);
    }

    public function update(Budget $budget, UpdateBudgetData $data): Budget
    {
        $budget->update(array_filter([
            'amount' => $data->amount,
            'recurrence' => $data->recurrence,
            'status' => $data->status,
        ], fn($value) => !is_null($value)));

        return $budget;
    }

    public function delete(Budget $budget): void
    {
        $budget->delete();
    }

    public function getBudgetsStatus(User $user, Carbon $month): Collection
    {
        $startOfMonth = $month->copy()->startOfMonth();
        $endOfMonth = $month->copy()->endOfMonth();

        // Get all active budgets for this month
        // Logic: 
        // 1. Budgets specifically for this month (period = startOfMonth)
        // 2. Recurring budgets created on or before this month
        // For now, let's stick to the simple implementation: Budget period must match the month
        // Or if we want "recurring", we need to find the budget definition.
        // Let's simplify: A budget entry exists for a specific month. 
        // If 'recurrence' is monthly, we might need to auto-copy it? 
        // OR we just query for "period <= this month AND recurrence = monthly" ?
        // Let's stick to: Budget row must exist for this specific month.
        // If we want recurrence, we should have a job that copies budgets to next month.
        // For this MVP, let's assume the user creates budgets for the month.
        
        $budgets = Budget::with('category')
            ->where('user_id', $user->id)
            ->where('period', $startOfMonth->format('Y-m-d'))
            ->get();

        // Calculate spent amount for each budget
        return $budgets->map(function (Budget $budget) use ($startOfMonth, $endOfMonth, $user) {
            $spentInCents = Transaction::where('user_id', $user->id)
                ->where('category_id', $budget->category_id)
                ->whereBetween('paid_at', [$startOfMonth, $endOfMonth])
                // We might want to include pending transactions too? Usually budgets track what you *will* spend too.
                // Let's include both paid and pending for now, or maybe just paid?
                // Usually budgets are "Cashflow" based, so Paid. 
                // But if I have a bill due tomorrow, it should count against my budget.
                // Let's count everything that has a due_date (if pending) or paid_at (if paid) in this month.
                ->where(function ($query) use ($startOfMonth, $endOfMonth) {
                     $query->whereBetween('paid_at', [$startOfMonth, $endOfMonth])
                           ->orWhere(function($q) use ($startOfMonth, $endOfMonth) {
                               $q->whereNull('paid_at')
                                 ->whereBetween('due_date', [$startOfMonth, $endOfMonth]);
                           });
                })
                ->sum('amount');

            $spent = $spentInCents / 100;
            $percentage = $budget->amount > 0 ? ($spent / $budget->amount) * 100 : 0;

            return [
                'id' => $budget->uuid,
                'category' => $budget->category->name,
                'category_id' => $budget->category->id, // Needed for frontend icons/colors
                'amount' => (float) $budget->amount,
                'spent' => $spent,
                'remaining' => $budget->amount - $spent,
                'percentage' => round($percentage, 1),
                'status' => $this->getStatusColor($percentage),
            ];
        });
    }

    private function getStatusColor(float $percentage): string
    {
        if ($percentage >= 100) return 'red';
        if ($percentage >= 85) return 'yellow';
        return 'green';
    }
}
