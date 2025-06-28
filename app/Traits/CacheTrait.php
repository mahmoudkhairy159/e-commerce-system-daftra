<?php

namespace App\Traits;

use App\Types\CacheKeysType;
use Illuminate\Support\Facades\Cache;

trait CacheTrait
{
    /**
     * Remember a value in cache with the specified TTL
     */
    protected function remember(string $key, callable $callback, int $ttl = null): mixed
    {
        $ttl = $ttl ?? CacheKeysType::CACHE_TTL_SECONDS;
        return Cache::remember($key, $ttl, $callback);
    }

    /**
     * Forget cache key(s)
     */
    protected function forget($keys): bool
    {
        if (is_array($keys)) {
            foreach ($keys as $key) {
                Cache::forget($key);
            }
            return true;
        }

        return Cache::forget($keys);
    }

    /**
     * Get countries cache service
     */
    protected function getCountriesCache(): object
    {
        return app('cache.countries');
    }

    /**
     * Get states cache service
     */
    protected function getStatesCache(): object
    {
        return app('cache.states');
    }

    /**
     * Get cities cache service
     */
    protected function getCitiesCache(): object
    {
        return app('cache.cities');
    }

    /**
     * Get categories cache service
     */
    protected function getCategoriesCache(): object
    {
        return app('cache.categories');
    }

    /**
     * Invalidate country-related caches
     */
    protected function invalidateCountryCache(): void
    {
        $this->getCountriesCache()->invalidate();
    }

    /**
     * Invalidate state-related caches
     */
    protected function invalidateStateCache(int $countryId = null): void
    {
        $this->getStatesCache()->invalidate($countryId);
    }

    /**
     * Invalidate city-related caches
     */
    protected function invalidateCityCache(int $countryId = null, int $stateId = null): void
    {
        $this->getCitiesCache()->invalidate($countryId, $stateId);
    }

    /**
     * Invalidate category-related caches
     */
    protected function invalidateCategoryCache(): void
    {
        $this->getCategoriesCache()->invalidate();
    }

    /**
     * Clear all area-related caches
     */
    protected function clearAllAreaCaches(): void
    {
        $this->invalidateCountryCache();
        $this->invalidateStateCache();
        $this->invalidateCityCache();
        $this->invalidateCategoryCache();
    }

    /**
     * Warm up area caches
     */
    protected function warmAreaCaches(): void
    {
        $this->getCountriesCache()->getAll();
        $this->getStatesCache()->getAll();
        $this->getCitiesCache()->getAll();
        $this->getCategoriesCache()->getTree();
    }

    /**
     * Get cache statistics for debugging
     */
    protected function getCacheStats(): array
    {
        $stats = [];

        try {
            $startTime = microtime(true);
            $countries = $this->getCountriesCache()->getAll();
            $stats['countries'] = [
                'cached' => !empty($countries),
                'count' => is_array($countries) ? count($countries) : 0,
                'time_ms' => round((microtime(true) - $startTime) * 1000, 2)
            ];
        } catch (\Exception $e) {
            $stats['countries'] = ['error' => $e->getMessage()];
        }

        try {
            $startTime = microtime(true);
            $states = $this->getStatesCache()->getAll();
            $stats['states'] = [
                'cached' => !empty($states),
                'count' => $states ? $states->count() : 0,
                'time_ms' => round((microtime(true) - $startTime) * 1000, 2)
            ];
        } catch (\Exception $e) {
            $stats['states'] = ['error' => $e->getMessage()];
        }

        try {
            $startTime = microtime(true);
            $cities = $this->getCitiesCache()->getAll();
            $stats['cities'] = [
                'cached' => !empty($cities),
                'count' => $cities ? $cities->count() : 0,
                'time_ms' => round((microtime(true) - $startTime) * 1000, 2)
            ];
        } catch (\Exception $e) {
            $stats['cities'] = ['error' => $e->getMessage()];
        }

        return $stats;
    }

    /**
     * Tags for cache invalidation (if using cache tags)
     */
    protected function getCacheTags(): array
    {
        return [
            'countries' => ['countries', 'area'],
            'states' => ['states', 'area'],
            'cities' => ['cities', 'area'],
            'categories' => ['categories']
        ];
    }

    /**
     * Generate cache key with prefix
     */
    protected function generateCacheKey(string $prefix, ...$parts): string
    {
        $key = $prefix;
        foreach ($parts as $part) {
            $key .= '_' . $part;
        }
        return strtoupper($key);
    }

    public function getCache(string $key)
    {
        return Cache::get($key);
    }

    public function setCache(string $key, $value, $timeout)
    {
        return Cache::remember($key, $timeout, function () use ($value) {
            return $value;
        });
    }
    public function deleteCache(string $key)
    {
        return Cache::forget($key);
    }
    public function deleteCaches(array $keys)
    {
        foreach ($keys as $key) {
            $this->deleteCache($key);
        }
    }

    public function clearCache()
    {
        return Cache::flush();
    }

    public function getCacheKeys()
    {
        return Cache::keys();
    }

    /**
     * Check if request has filter parameters
     */
    public function hasFilterParameters(): bool
    {
        $request = request();
        $filterParams = $request->except(['page', 'per_page', 'limit', '_config', 'token']);

        return !empty($filterParams);
    }

    /**
     * Get filter parameters as string for cache key
     */
    public function getFilterCacheKey(): string
    {
        $request = request();
        $filterParams = $request->except(['page', 'per_page', 'limit', '_config', 'token']);

        if (empty($filterParams)) {
            return '';
        }

        // Sort parameters for consistent cache keys
        ksort($filterParams);

        return '_FILTERED_' . md5(serialize($filterParams));
    }

    /**
     * Get filtered cache key with base key
     */
    public function getFilteredCacheKey(string $baseKey): string
    {
        $filterKey = $this->getFilterCacheKey();
        return $baseKey . $filterKey;
    }

    /**
     * Check if should use cache (no filters) or database (with filters)
     */
    public function shouldUseCache(): bool
    {
        return !$this->hasFilterParameters();
    }
}