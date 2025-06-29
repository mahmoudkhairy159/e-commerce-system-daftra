<?php

declare(strict_types=1);

namespace App\Traits;

use App\Services\Cache\CategoryCacheService;
use App\Services\Cache\ProductCacheService;
use App\Types\CacheKeysType;
use Illuminate\Support\Facades\Cache;

trait CacheTrait
{
    /**
     * Remember a value in cache with the specified TTL
     */
    protected function remember(string $key, callable $callback, ?int $ttl = null): mixed
    {
        $ttl = $ttl ?? CacheKeysType::CACHE_TTL_SECONDS;
        return Cache::remember($key, $ttl, $callback);
    }

    /**
     * Forget cache key(s)
     */
    protected function forget(string|array $keys): bool
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
     * Get categories cache service
     */
    protected function getCategoriesCache(): CategoryCacheService
    {
        return app('cache.categories');
    }

    /**
     * Get products cache service
     */
    protected function getProductsCache(): ProductCacheService
    {
        return app('cache.products');
    }

    /**
     * Invalidate category-related caches
     */
    protected function invalidateCategoryCache(): void
    {
        $this->getCategoriesCache()->invalidate();
    }

    /**
     * Invalidate product-related caches
     */
    protected function invalidateProductCache(?int $categoryId = null, ?int $type = null): void
    {
        $this->getProductsCache()->invalidateSpecific($categoryId, $type);
    }

    /**
     * Invalidate all product caches
     */
    protected function invalidateAllProductCaches(): void
    {
        $this->getProductsCache()->invalidateAll();
    }

    /**
     * Clear all caches
     */
    protected function clearAllCaches(): void
    {
        $this->invalidateCategoryCache();
        $this->invalidateAllProductCaches();
    }

    /**
     * Check if request has filter parameters
     */
    protected function hasFilterParameters(): bool
    {
        $filterParams = request()->except(['page', 'per_page', 'limit', '_config', 'token']);
        return !empty($filterParams);
    }

    /**
     * Get filter parameters as string for cache key
     */
    protected function getFilterCacheKey(): string
    {
        $filterParams = request()->except(['page', 'per_page', 'limit', '_config', 'token']);

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
    protected function getFilteredCacheKey(string $baseKey): string
    {
        return $baseKey . $this->getFilterCacheKey();
    }

    /**
     * Check if should use cache (no filters) or database (with filters)
     */
    protected function shouldUseCache(): bool
    {
        return !$this->hasFilterParameters();
    }

    /**
     * Generate cache key with prefix
     */
    protected function generateCacheKey(string $prefix, string ...$parts): string
    {
        $key = $prefix;
        foreach ($parts as $part) {
            $key .= '_' . $part;
        }
        return strtoupper($key);
    }


}
