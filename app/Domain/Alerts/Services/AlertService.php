<?php

namespace App\Domain\Alerts\Services;

use App\Domain\Alerts\DTO\CreateAlertData;
use App\Domain\Alerts\DTO\UpdateAlertData;
use App\Enums\AlertTypeEnum;
use App\Enums\NotificationTypeEnum;
use App\Models\Alert;
use App\Models\AlertNotification;
use App\Models\User;
use App\Notifications\AlertTriggeredNotification;
use Illuminate\Support\Collection;

class AlertService
{
    /**
     * Create a new alert.
     */
    public function create(CreateAlertData $data): Alert
    {
        return Alert::create([
            'user_id' => $data->userId,
            'type' => $data->type->value,
            'alertable_type' => $data->alertableType,
            'alertable_id' => $data->alertableId,
            'trigger_value' => $data->triggerValue,
            'trigger_days' => $data->triggerDays,
            'notification_channels' => $data->notificationChannels,
            'is_active' => $data->isActive,
        ]);
    }

    /**
     * Update an existing alert.
     */
    public function update(Alert $alert, UpdateAlertData $data): Alert
    {
        $updateData = array_filter([
            'trigger_value' => $data->triggerValue,
            'trigger_days' => $data->triggerDays,
            'notification_channels' => $data->notificationChannels,
            'is_active' => $data->isActive,
        ], fn($value) => $value !== null);

        $alert->update($updateData);

        return $alert->fresh();
    }

    /**
     * Delete an alert.
     */
    public function delete(Alert $alert): bool
    {
        return $alert->delete();
    }

    /**
     * Get all alerts for a user.
     */
    public function getUserAlerts(string $userId): Collection
    {
        return Alert::where('user_id', $userId)
            ->with('alertable')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Toggle alert active status.
     */
    public function toggleStatus(Alert $alert): Alert
    {
        $alert->update(['is_active' => !$alert->is_active]);

        return $alert->fresh();
    }

    /**
     * Check credit card usage alerts.
     */
    public function checkCreditCardUsageAlerts(): void
    {
        // Get all active credit card usage alerts
        $alerts = Alert::active()
            ->ofType(AlertTypeEnum::CREDIT_CARD_USAGE->value)
            ->with('user')
            ->get();

        foreach ($alerts as $alert) {
            // If alertable is specified (specific card), check only that one
            if ($alert->alertable_type && $alert->alertable_id) {
                $alert->load('alertable');
                $this->processCreditCardAlert($alert, $alert->alertable);
            } else {
                // Check all credit cards for this user
                $creditCards = \App\Models\Wallet::where('user_id', $alert->user_id)
                    ->where('type', \App\Enums\WalletTypeEnum::CARD_CREDIT)
                    ->get();

                foreach ($creditCards as $creditCard) {
                    $this->processCreditCardAlert($alert, $creditCard);
                }
            }
        }
    }

    /**
     * Process a single credit card alert.
     */
    protected function processCreditCardAlert(Alert $alert, $creditCard): void
    {
        if (!$creditCard) {
            return;
        }

        // Calculate usage percentage
        if (!isset($creditCard->card_limit) || !isset($creditCard->card_limit_used) || $creditCard->card_limit <= 0) {
            return;
        }

        $usagePercent = ($creditCard->card_limit_used / $creditCard->card_limit) * 100;

        if ($usagePercent >= $alert->trigger_value) {
            // Check if already notified today for this specific card
            $alreadyNotified = AlertNotification::where('alert_id', $alert->id)
                ->whereDate('created_at', now())
                ->where(function($query) use ($creditCard) {
                    $query->whereJsonContains('data->wallet_id', $creditCard->id)
                        ->orWhereJsonContains('data->card_id', $creditCard->id);
                })
                ->exists();

            if ($alreadyNotified) {
                return;
            }

            $this->createNotification($alert, [
                'title' => 'Limite do Cartão Atingido',
                'message' => sprintf(
                    'Seu cartão %s atingiu %.2f%% do limite (R$ %.2f de R$ %.2f).',
                    $creditCard->name ?? 'sem nome',
                    $usagePercent,
                    $creditCard->card_limit_used,
                    $creditCard->card_limit
                ),
                'type' => $usagePercent >= 90 ? NotificationTypeEnum::DANGER->value : NotificationTypeEnum::WARNING->value,
                'usage_percent' => round($usagePercent, 2),
                'card_limit_used' => $creditCard->card_limit_used,
                'card_limit' => $creditCard->card_limit,
                'wallet_id' => $creditCard->id,
                'card_id' => $creditCard->id,
            ]);

            $alert->update(['last_triggered_at' => now()]);
        }
    }

    /**
     * Check bill due date alerts.
     * Checks pending transactions that are approaching their due date.
     */
    public function checkBillDueDateAlerts(): void
    {
        Alert::active()
            ->ofType(AlertTypeEnum::BILL_DUE_DATE->value)
            ->with(['user', 'alertable'])
            ->chunk(100, function ($alerts) {
                foreach ($alerts as $alert) {
                    $this->processBillAlert($alert);
                }
            });
    }

    /**
     * Process a single bill alert.
     * Checks transactions that are pending and approaching their due date.
     */
    protected function processBillAlert(Alert $alert): void
    {
        // Get transactions to check
        if ($alert->alertable_type === \App\Models\Account::class && $alert->alertable_id) {
            // Alert for a specific Account - check its transactions
            $transactions = \App\Models\Transaction::where('account_id', $alert->alertable_id)
                ->where('status', \App\Enums\TransactionStatusEnum::PENDING)
                ->get();
        } else {
            // Alert for all user's transactions
            $transactions = \App\Models\Transaction::where('user_id', $alert->user_id)
                ->where('status', \App\Enums\TransactionStatusEnum::PENDING)
                ->get();
        }

        // Check each transaction against the alert trigger days
        foreach ($transactions as $transaction) {
            if (!$transaction->due_date) {
                continue;
            }

            foreach ($alert->trigger_days as $days) {
                $targetDate = now()->addDays($days)->startOfDay();
                $dueDate = $transaction->due_date->startOfDay();

                if ($targetDate->isSameDay($dueDate)) {
                    // Check if already notified for this specific transaction and day threshold
                    $alreadyNotified = AlertNotification::where('alert_id', $alert->id)
                        ->whereJsonContains('data->transaction_id', $transaction->id)
                        ->whereJsonContains('data->days_before', $days)
                        ->whereDate('created_at', now())
                        ->exists();

                    if (!$alreadyNotified) {
                        $accountName = $transaction->account->name ?? 'Sem nome';
                        $categoryName = $transaction->category->name ?? 'Sem categoria';

                        $this->createNotification($alert, [
                            'title' => 'Conta Próxima do Vencimento',
                            'message' => sprintf(
                                'A conta "%s" (%s) vence em %d dia(s) no valor de R$ %.2f.',
                                $accountName,
                                $categoryName,
                                $days,
                                $transaction->amount
                            ),
                            'type' => $days <= 3 ? NotificationTypeEnum::DANGER->value : NotificationTypeEnum::WARNING->value,
                            'days_before' => $days,
                            'due_date' => $transaction->due_date->toDateString(),
                            'amount' => $transaction->amount,
                            'transaction_id' => $transaction->id,
                            'account_name' => $accountName,
                            'category_name' => $categoryName,
                        ]);
                    }
                }
            }
        }
    }

    /**
     * Check account balance alerts.
     * NOTE: Implement this method according to your Account model structure.
     */
    public function checkAccountBalanceAlerts(): void
    {
        Alert::active()
            ->ofType(AlertTypeEnum::ACCOUNT_BALANCE->value)
            ->with(['user', 'alertable'])
            ->chunk(100, function ($alerts) {
                foreach ($alerts as $alert) {
                    $this->processAccountBalanceAlert($alert);
                }
            });
    }

    /**
     * Process a single account balance alert.
     */
    protected function processAccountBalanceAlert(Alert $alert): void
    {
        $account = $alert->alertable;

        if (!$account || !isset($account->balance)) {
            return;
        }

        // Check if balance is below trigger value
        if ($account->balance <= $alert->trigger_value) {
            // Avoid notifying multiple times on the same day
            if ($alert->last_triggered_at?->isToday()) {
                return;
            }

            $this->createNotification($alert, [
                'title' => 'Saldo da Conta Baixo',
                'message' => sprintf(
                    'O saldo da conta %s está em R$ %.2f.',
                    $account->name ?? 'sem nome',
                    $account->balance
                ),
                'type' => NotificationTypeEnum::WARNING->value,
                'balance' => $account->balance,
                'trigger_value' => $alert->trigger_value,
            ]);

            $alert->update(['last_triggered_at' => now()]);
        }
    }

    /**
     * Create a notification for an alert.
     */
    protected function createNotification(Alert $alert, array $data): void
    {
        // Create in-app notification
        $notification = AlertNotification::create([
            'alert_id' => $alert->id,
            'user_id' => $alert->user_id,
            'title' => $data['title'],
            'message' => $data['message'],
            'type' => $data['type'],
            'data' => $data,
        ]);

        // Send email notification if configured
        if (in_array('mail', $alert->notification_channels)) {
            $alert->user->notify(new AlertTriggeredNotification($notification));
        }
    }

    /**
     * Get unread notifications for a user.
     */
    public function getUnreadNotifications(string $userId): Collection
    {
        return AlertNotification::where('user_id', $userId)
            ->unread()
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get unread notifications count for a user.
     */
    public function getUnreadNotificationsCount(string $userId): int
    {
        return AlertNotification::where('user_id', $userId)
            ->unread()
            ->count();
    }

    /**
     * Mark a notification as read.
     */
    public function markNotificationAsRead(AlertNotification $notification): void
    {
        $notification->markAsRead();
    }

    /**
     * Mark all notifications as read for a user.
     */
    public function markAllAsRead(string $userId): void
    {
        AlertNotification::where('user_id', $userId)
            ->unread()
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
    }

    /**
     * Delete a notification.
     */
    public function deleteNotification(AlertNotification $notification): bool
    {
        return $notification->delete();
    }

    /**
     * Delete all read notifications for a user.
     */
    public function deleteAllReadNotifications(string $userId): int
    {
        return AlertNotification::where('user_id', $userId)
            ->where('is_read', true)
            ->delete();
    }

    /**
     * Check budget threshold alerts.
     * Checks if budgets are approaching or exceeding their limits.
     */
    public function checkBudgetAlerts(): void
    {
        Alert::active()
            ->ofType(AlertTypeEnum::BUDGET_EXCEEDED->value)
            ->with(['user', 'alertable'])
            ->chunk(100, function ($alerts) {
                foreach ($alerts as $alert) {
                    $this->processBudgetAlert($alert);
                }
            });
    }

    /**
     * Process a single budget alert.
     * Calculates spending vs budget limit and triggers notification if threshold is reached.
     */
    protected function processBudgetAlert(Alert $alert): void
    {
        // Get budgets to check
        if ($alert->alertable_type === \App\Models\Budget::class && $alert->alertable_id) {
            // Alert for a specific Budget
            $budgets = [\App\Models\Budget::find($alert->alertable_id)];
        } else {
            // Alert for all user's budgets
            $budgets = \App\Models\Budget::where('user_id', $alert->user_id)
                ->where('status', true)
                ->get();
        }

        foreach ($budgets as $budget) {
            if (!$budget) {
                continue;
            }

            // Calculate spending for this budget's category in the current period
            $periodStart = \Carbon\Carbon::parse($budget->period)->startOfMonth();
            $periodEnd = $periodStart->copy()->endOfMonth();

            $totalSpentInCents = \App\Models\Transaction::where('user_id', $budget->user_id)
                ->where('category_id', $budget->category_id)
                ->where('status', \App\Enums\TransactionStatusEnum::PAID)
                ->whereBetween('paid_at', [$periodStart, $periodEnd])
                ->sum('amount');

            // Convert cents to reais (sum returns raw database value in cents)
            $totalSpent = round($totalSpentInCents / 100, 2);

            // Calculate usage percentage
            if ($budget->amount <= 0) {
                continue;
            }

            $usagePercent = ($totalSpent / $budget->amount) * 100;

            // Check if threshold is reached
            if ($usagePercent >= $alert->trigger_value) {
                // Check if already notified today for this specific budget
                $alreadyNotified = AlertNotification::where('alert_id', $alert->id)
                    ->whereDate('created_at', now())
                    ->whereJsonContains('data->budget_id', $budget->id)
                    ->exists();

                if ($alreadyNotified) {
                    continue;
                }

                $categoryName = $budget->category->name ?? 'Sem categoria';
                $isExceeded = $usagePercent >= 100;

                $this->createNotification($alert, [
                    'title' => $isExceeded ? 'Orçamento Excedido' : 'Orçamento Próximo do Limite',
                    'message' => sprintf(
                        '%s: você %s %.2f%% do orçamento de %s (R$ %.2f de R$ %.2f).',
                        $categoryName,
                        $isExceeded ? 'excedeu' : 'atingiu',
                        $usagePercent,
                        $periodStart->locale('pt_BR')->monthName,
                        $totalSpent,
                        $budget->amount
                    ),
                    'type' => $usagePercent >= 100 ? NotificationTypeEnum::DANGER->value : NotificationTypeEnum::WARNING->value,
                    'usage_percent' => round($usagePercent, 2),
                    'total_spent' => $totalSpent,
                    'budget_amount' => $budget->amount,
                    'budget_id' => $budget->id,
                    'category_id' => $budget->category_id,
                    'category_name' => $categoryName,
                    'period' => $budget->period->toDateString(),
                ]);

                $alert->update(['last_triggered_at' => now()]);
            }
        }
    }
}
