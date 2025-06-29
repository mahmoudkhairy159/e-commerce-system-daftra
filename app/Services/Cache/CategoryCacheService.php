<?php

declare(strict_types=1);

namespace App\Services\Cache;

use App\Contracts\CacheServiceInterface;
use App\Types\CacheKeysType;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Modules\Category\Repositories\CategoryRepository;

class CategoryCacheService implements CacheServiceInterface
{
    public function __construct(
        private CategoryRepository $categoryRepository
    ) {
    }

    /**
     * Get all categories from cache
     */
    public function getAll(): Collection
    {
        return Cache::remember(
            CacheKeysType::getCategoriesCacheKey(),
            CacheKeysType::CACHE_TTL_SECONDS,
            fn() => $this->categoryRepository->getAll()->get()
        );
    }

    /**
     * Get all active categories from cache
     */
    public function getAllActive(?string $locale = null): Collection
    {
        $cacheKey = $locale
            ? CacheKeysType::getCategoriesLocaleCacheKey($locale, 'active')
            : CacheKeysType::getCategoriesActiveCacheKey();

        return Cache::remember(
            $cacheKey,
            CacheKeysType::CACHE_TTL_SECONDS,
            fn() => $this->categoryRepository->getAllActive()->get()
        );
    }

    /**
     * Invalidate all category-related caches
     */
    public function invalidate(): void
    {
        $keys = CacheKeysType::getCategoryRelatedCacheKeys();

        foreach ($keys as $key) {
            Cache::forget($key);
        }
    }
}