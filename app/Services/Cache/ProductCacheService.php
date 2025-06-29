<?php

declare(strict_types=1);

namespace App\Services\Cache;

use App\Contracts\CacheServiceInterface;
use App\Types\CacheKeysType;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Modules\Product\Repositories\ProductRepository;

class ProductCacheService implements CacheServiceInterface
{
    public function __construct(
        private ProductRepository $productRepository
    ) {
    }

    /**
     * Get all products from cache
     */
    public function getAll(): Collection
    {
        return Cache::remember(
            CacheKeysType::getProductsCacheKey(),
            CacheKeysType::CACHE_TTL_SECONDS,
            fn() => $this->productRepository->getAll()->get()
        );
    }

    /**
     * Get all active products from cache
     */
    public function getAllActive(?string $locale = null): Collection
    {
        $cacheKey = $locale
            ? CacheKeysType::getProductsLocaleCacheKey($locale, 'active')
            : CacheKeysType::getProductsActiveCacheKey();

        return Cache::remember(
            $cacheKey,
            CacheKeysType::CACHE_TTL_SECONDS,
            fn() => $this->productRepository->getAllActive()->get()
        );
    }

    /**
     * Get products by type from cache
     */
    public function getByType(int $type, ?string $locale = null): Collection
    {
        $cacheKey = $locale
            ? CacheKeysType::getProductsByTypeLocaleCacheKey($type, $locale)
            : CacheKeysType::getProductsByTypeCacheKey($type);

        return Cache::remember(
            $cacheKey,
            CacheKeysType::CACHE_TTL_SECONDS,
            fn() => $this->productRepository->getProductByType($type)->get()
        );
    }

    /**
     * Get featured products from cache
     */
    public function getFeatured(?string $locale = null): Collection
    {
        return $this->getByType(1, $locale); // 1 = FEATURED
    }

    /**
     * Get new arrival products from cache
     */
    public function getNewArrivals(?string $locale = null): Collection
    {
        return $this->getByType(0, $locale); // 0 = NEW_ARRIVAL
    }

    /**
     * Get best seller products from cache
     */
    public function getBestSellers(?string $locale = null): Collection
    {
        return $this->getByType(3, $locale); // 3 = BEST_SELLER
    }

    /**
     * Get top products from cache
     */
    public function getTopProducts(?string $locale = null): Collection
    {
        return $this->getByType(2, $locale); // 2 = TOP_PRODUCT
    }

    /**
     * Get products by category from cache
     */
    public function getByCategory(int $categoryId, ?string $locale = null): Collection
    {
        $cacheKey = $locale
            ? CacheKeysType::getProductsByCategoryLocaleCacheKey($categoryId, $locale)
            : CacheKeysType::getProductsByCategoryCacheKey($categoryId);

        return Cache::remember(
            $cacheKey,
            CacheKeysType::CACHE_TTL_SECONDS,
            fn() => $this->productRepository
                ->getAllActive()
                ->whereHas('categories', fn($query) => $query->where('categories.id', $categoryId))
                ->get()
        );
    }

    /**
     * Invalidate cache (interface implementation)
     */
    public function invalidate(): void
    {
        $this->invalidateSpecific();
    }

    /**
     * Invalidate specific product caches
     */
    public function invalidateSpecific(?int $categoryId = null, ?int $type = null): void
    {
        $keys = CacheKeysType::getProductRelatedCacheKeys($categoryId, $type);

        foreach ($keys as $key) {
            Cache::forget($key);
        }
    }

    /**
     * Invalidate all product-related caches
     */
    public function invalidateAll(): void
    {
        $this->invalidateSpecific();

        // Invalidate specific type caches
        for ($type = 0; $type <= 3; $type++) {
            $this->invalidateSpecific(null, $type);
        }
    }
}
