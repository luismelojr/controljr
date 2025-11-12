<?php

namespace App\Domain\Reports\Services;

use App\Domain\Reports\DTO\ReportFiltersData;
use App\Domain\Reports\Queries\BaseReportQuery;
use App\Domain\Reports\Queries\CashflowQuery;
use App\Domain\Reports\Queries\ExpensesByCategoryQuery;
use App\Domain\Reports\Queries\ExpensesByWalletQuery;
use App\Domain\Reports\Queries\ExpensesEvolutionQuery;
use App\Domain\Reports\Queries\TopExpensesQuery;
use App\Enums\ReportTypeEnum;

class ReportBuilderService
{
    /**
     * Map report types to their query classes
     */
    private array $queryMap = [
        'expenses_by_category' => ExpensesByCategoryQuery::class,
        'expenses_by_wallet' => ExpensesByWalletQuery::class,
        'expenses_evolution' => ExpensesEvolutionQuery::class,
        'top_expenses' => TopExpensesQuery::class,
        'cashflow' => CashflowQuery::class,
        // Income reports can be added later
        // 'income_by_category' => IncomesByCategoryQuery::class,
        // 'income_by_wallet' => IncomesByWalletQuery::class,
        // 'income_evolution' => IncomesEvolutionQuery::class,
    ];

    /**
     * Execute query for a specific report type
     */
    public function executeQuery(
        ReportTypeEnum $reportType,
        string $userId,
        ReportFiltersData $filters
    ): array {
        $queryClass = $this->resolveQueryClass($reportType);

        if (!$queryClass) {
            throw new \InvalidArgumentException("Query not found for report type: {$reportType->value}");
        }

        /** @var BaseReportQuery $query */
        $query = new $queryClass();

        return $query->execute($userId, $filters);
    }

    /**
     * Resolve query class from report type
     */
    private function resolveQueryClass(ReportTypeEnum $reportType): ?string
    {
        $queryClass = $this->queryMap[$reportType->value] ?? null;

        if ($queryClass && class_exists($queryClass)) {
            return $queryClass;
        }

        return null;
    }

    /**
     * Check if report type is supported
     */
    public function isSupported(ReportTypeEnum $reportType): bool
    {
        return isset($this->queryMap[$reportType->value]);
    }

    /**
     * Get all supported report types
     */
    public function getSupportedReportTypes(): array
    {
        return array_keys($this->queryMap);
    }

    /**
     * Get available visualizations for a report type
     */
    public function getAvailableVisualizations(ReportTypeEnum $reportType): array
    {
        // Define which visualizations are suitable for each report type
        return match ($reportType) {
            ReportTypeEnum::EXPENSES_BY_CATEGORY,
            ReportTypeEnum::EXPENSES_BY_WALLET => [
                'table',
                'pie_chart',
                'bar_chart',
            ],
            ReportTypeEnum::EXPENSES_EVOLUTION,
            ReportTypeEnum::CASHFLOW => [
                'table',
                'line_chart',
                'bar_chart',
            ],
            ReportTypeEnum::TOP_EXPENSES => [
                'table',
                'bar_chart',
            ],
            default => ['table'],
        };
    }
}
