<?php

namespace App\Domain\Reports\Services;

use App\Domain\Reports\DTO\GenerateReportData;
use App\Domain\Reports\DTO\SaveReportConfigData;
use App\Enums\ExportFormatEnum;
use App\Models\SavedReport;
use Illuminate\Support\Collection;

class ReportService
{
    public function __construct(
        private ReportCacheService $cacheService,
        private ReportBuilderService $builderService,
        private ReportExportService $exportService,
    ) {}

    /**
     * Generate a report with caching
     */
    public function generateReport(GenerateReportData $reportData): array
    {
        $cacheKey = $this->cacheService->getCacheKey($reportData, $reportData->userId);

        // Check cache first
        if ($this->cacheService->has($cacheKey)) {
            $cachedData = $this->cacheService->get($cacheKey);
            if ($cachedData) {
                return array_merge($cachedData, ['from_cache' => true]);
            }
        }

        // Generate fresh report
        $result = $this->builderService->executeQuery(
            $reportData->reportType,
            $reportData->userId,
            $reportData->filters
        );

        // Add metadata
        $result['metadata'] = [
            'report_type' => $reportData->reportType->value,
            'report_label' => $reportData->reportType->label(),
            'visualization_type' => $reportData->visualizationType->value,
            'generated_at' => now()->toISOString(),
            'cache_ttl' => $this->cacheService->getCacheTtl(),
        ];

        // Cache the result
        $this->cacheService->put($cacheKey, $result);

        return array_merge($result, ['from_cache' => false]);
    }

    /**
     * Save report configuration
     */
    public function saveReportConfig(SaveReportConfigData $configData): SavedReport
    {
        $savedReport = SavedReport::create($configData->toArray());

        return $savedReport;
    }

    /**
     * Get user's saved reports
     */
    public function getUserReports(string $userId): Collection
    {
        return SavedReport::where('user_id', $userId)
            ->userReports()
            ->orderByDesc('created_at')
            ->get();
    }

    /**
     * Get user's favorite reports
     */
    public function getUserFavorites(string $userId): Collection
    {
        return SavedReport::where('user_id', $userId)
            ->favorites()
            ->orderByDesc('last_run_at')
            ->get();
    }

    /**
     * Get template reports
     */
    public function getTemplates(): Collection
    {
        return SavedReport::templates()
            ->orderBy('name')
            ->get();
    }

    /**
     * Run saved report
     */
    public function runSavedReport(SavedReport $savedReport): array
    {
        $reportData = GenerateReportData::fromSavedReport($savedReport);
        $result = $this->generateReport($reportData);

        // Update run statistics
        $savedReport->incrementRunCount();

        return $result;
    }

    /**
     * Export report to file
     */
    public function exportReport(
        array $reportData,
        GenerateReportData $config,
        ExportFormatEnum $format
    ): string {
        $path = $this->exportService->export(
            $reportData,
            $config->reportType,
            $format,
            $config->userId
        );

        return $this->exportService->getDownloadUrl($path);
    }

    /**
     * Update saved report
     */
    public function updateSavedReport(SavedReport $savedReport, array $data): SavedReport
    {
        $savedReport->update($data);

        // Clear cache for this user since report config changed
        $this->cacheService->clearUserCache($savedReport->user_id);

        return $savedReport->fresh();
    }

    /**
     * Delete saved report
     */
    public function deleteSavedReport(SavedReport $savedReport): bool
    {
        return $savedReport->delete();
    }

    /**
     * Toggle favorite status
     */
    public function toggleFavorite(SavedReport $savedReport): SavedReport
    {
        $savedReport->update([
            'is_favorite' => !$savedReport->is_favorite,
        ]);

        return $savedReport->fresh();
    }

    /**
     * Clear user's report cache
     */
    public function clearUserCache(string $userId): void
    {
        $this->cacheService->clearUserCache($userId);
    }

    /**
     * Check if report type is supported
     */
    public function isReportTypeSupported(string $reportType): bool
    {
        try {
            $enum = \App\Enums\ReportTypeEnum::from($reportType);
            return $this->builderService->isSupported($enum);
        } catch (\ValueError $e) {
            return false;
        }
    }

    /**
     * Get available visualizations for report type
     */
    public function getAvailableVisualizations(string $reportType): array
    {
        $enum = \App\Enums\ReportTypeEnum::from($reportType);
        return $this->builderService->getAvailableVisualizations($enum);
    }
}
