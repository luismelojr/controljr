<?php

namespace App\Domain\Reports\Services;

use App\Domain\Reports\DTO\GenerateReportData;
use Illuminate\Support\Facades\Cache;

class ReportCacheService
{
    /**
     * Cache duration in seconds (10 minutes)
     */
    private const CACHE_TTL = 600;

    /**
     * Cache key prefix
     */
    private const CACHE_PREFIX = 'report_';

    /**
     * Get cache key for a report
     */
    public function getCacheKey(GenerateReportData $reportData, string $userId): string
    {
        $data = [
            'user_id' => $userId,
            'report_type' => $reportData->reportType->value,
            'filters' => $reportData->filters->toArray(),
            'visualization' => $reportData->visualizationType->value,
        ];

        // Create a unique hash based on report configuration
        $hash = md5(json_encode($data));

        return self::CACHE_PREFIX . $userId . '_' . $hash;
    }

    /**
     * Get cached report data
     */
    public function get(string $cacheKey): ?array
    {
        return Cache::get($cacheKey);
    }

    /**
     * Store report data in cache
     */
    public function put(string $cacheKey, array $data): void
    {
        Cache::put($cacheKey, $data, self::CACHE_TTL);
    }

    /**
     * Check if cache exists for this report
     */
    public function has(string $cacheKey): bool
    {
        return Cache::has($cacheKey);
    }

    /**
     * Clear cache for specific report
     */
    public function forget(string $cacheKey): void
    {
        Cache::forget($cacheKey);
    }

    /**
     * Clear all report caches for a specific user
     */
    public function clearUserCache(string $userId): void
    {
        $pattern = self::CACHE_PREFIX . $userId . '_*';

        // Get all cache keys matching pattern
        $keys = Cache::getStore()->getMemcached()?->getAllKeys() ?? [];

        foreach ($keys as $key) {
            if (str_starts_with($key, self::CACHE_PREFIX . $userId . '_')) {
                Cache::forget($key);
            }
        }
    }

    /**
     * Clear all report caches (all users)
     */
    public function clearAllReportsCache(): void
    {
        $keys = Cache::getStore()->getMemcached()?->getAllKeys() ?? [];

        foreach ($keys as $key) {
            if (str_starts_with($key, self::CACHE_PREFIX)) {
                Cache::forget($key);
            }
        }
    }

    /**
     * Get cache TTL in seconds
     */
    public function getCacheTtl(): int
    {
        return self::CACHE_TTL;
    }
}
