<?php

namespace App\Services;

use App\Facades\Toast;
use App\Models\User;

class PlanLimitService
{
    /**
     * Check if user can create more of a specific resource
     */
    public static function canCreate(User $user, string $feature, int $currentCount): bool
    {
        $limits = $user->getPlanLimits();

        if (! isset($limits[$feature])) {
            return true; // No limit defined, allow creation
        }

        $limit = $limits[$feature];

        // -1 means unlimited
        if ($limit === -1) {
            return true;
        }

        // 0 or false means feature is disabled
        if ($limit === 0 || $limit === false) {
            return false;
        }

        // Check if current count is below limit
        return $currentCount < $limit;
    }

    /**
     * Check and flash error toast if limit is reached
     */
    public static function checkLimitWithToast(User $user, string $feature, int $currentCount, string $resourceName): bool
    {
        if (! self::canCreate($user, $feature, $currentCount)) {
            $limits = $user->getPlanLimits();
            $limit = $limits[$feature] ?? 0;

            if ($limit === 0 || $limit === false) {
                Toast::error("Este recurso não está disponível no seu plano atual")
                    ->title('Limite de Plano')
                    ->description("Faça upgrade para acessar {$resourceName}")
                    ->persistent();
            } else {
                Toast::error("Você atingiu o limite de {$limit} {$resourceName}")
                    ->title('Limite Atingido')
                    ->description('Faça upgrade do seu plano para criar mais')
                    ->persistent();
            }

            return false;
        }

        return true;
    }

    /**
     * Get limit value for a feature
     */
    public static function getLimit(User $user, string $feature): int|bool
    {
        $limits = $user->getPlanLimits();

        return $limits[$feature] ?? -1; // Default to unlimited if not defined
    }

    /**
     * Check if feature is enabled for user's plan
     */
    public static function hasFeature(User $user, string $feature): bool
    {
        $limits = $user->getPlanLimits();

        if (! isset($limits[$feature])) {
            return true; // Feature not defined, assume enabled
        }

        $value = $limits[$feature];

        // false or 0 means disabled
        if ($value === false || $value === 0) {
            return false;
        }

        return true;
    }

    /**
     * Get usage percentage for a countable feature
     */
    public static function getUsagePercentage(User $user, string $feature, int $currentCount): int
    {
        $limit = self::getLimit($user, $feature);

        // Unlimited or feature disabled
        if ($limit === -1 || $limit === false || $limit === 0) {
            return 0;
        }

        if ($limit === 0) {
            return 100;
        }

        $percentage = ($currentCount / $limit) * 100;

        return min(100, (int) round($percentage));
    }

    /**
     * Get remaining count for a feature
     */
    public static function getRemainingCount(User $user, string $feature, int $currentCount): int|string
    {
        $limit = self::getLimit($user, $feature);

        if ($limit === -1) {
            return 'Ilimitado';
        }

        if ($limit === false || $limit === 0) {
            return 0;
        }

        return max(0, $limit - $currentCount);
    }

    /**
     * Get feature display name
     */
    public static function getFeatureDisplayName(string $feature): string
    {
        return match ($feature) {
            'max_wallets' => 'carteiras',
            'max_categories' => 'categorias',
            'max_accounts' => 'contas recorrentes',
            'max_transactions_per_month' => 'transações por mês',
            'max_budgets' => 'orçamentos',
            'max_alerts' => 'alertas',
            'financial_reports' => 'relatórios financeiros',
            'data_export' => 'exportação de dados',
            'bank_reconciliation' => 'conciliação bancária',
            'multi_currency' => 'múltiplas moedas',
            'api_access' => 'acesso à API',
            'priority_support' => 'suporte prioritário',
            'max_team_members' => 'membros da equipe',
            default => $feature,
        };
    }
}
