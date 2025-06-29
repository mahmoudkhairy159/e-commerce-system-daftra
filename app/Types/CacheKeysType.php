<?php

namespace App\Types;

class CacheKeysType
{
    // Cache TTL in seconds (5 days)
    const CACHE_TTL_SECONDS = 432000; // 5 * 24 * 60 * 60




    // Categories cache keys
    const CATEGORIES_CACHE = "CATEGORIES_CACHE";
    const CATEGORIES_CACHE_PREFIX = "CATEGORIES_CACHE_";
    const CATEGORIES_ACTIVE_CACHE = "CATEGORIES_ACTIVE_CACHE";

    // Products cache keys
    const PRODUCTS_CACHE = "PRODUCTS_CACHE";
    const PRODUCTS_ACTIVE_CACHE = "PRODUCTS_ACTIVE_CACHE";
    const PRODUCTS_CACHE_PREFIX = "PRODUCTS_CACHE_";
    const PRODUCTS_BY_TYPE_CACHE_PREFIX = "PRODUCTS_BY_TYPE_";
    const PRODUCTS_BY_CATEGORY_CACHE_PREFIX = "PRODUCTS_BY_CATEGORY_";
    const PRODUCTS_FEATURED_CACHE = "PRODUCTS_FEATURED_CACHE";
    const PRODUCTS_NEW_ARRIVAL_CACHE = "PRODUCTS_NEW_ARRIVAL_CACHE";
    const PRODUCTS_BEST_SELLER_CACHE = "PRODUCTS_BEST_SELLER_CACHE";
    const PRODUCTS_TOP_CACHE = "PRODUCTS_TOP_CACHE";

    /**
     * Get supported locales
     */
    private static function getSupportedLocales(): array
    {
        return ['en', 'ar'];
    }






    /**
     * Get categories cache key for all categories
     */
    public static function getCategoriesCacheKey(): string
    {
        return self::CATEGORIES_CACHE;
    }

    /**
     * Get categories cache key for active categories
     */
    public static function getCategoriesActiveCacheKey(): string
    {
        return self::CATEGORIES_ACTIVE_CACHE;
    }


    /**
     * Get categories cache key for specific locale
     */
    public static function getCategoriesLocaleCacheKey(string $locale, string $type = 'active'): string
    {
        return self::CATEGORIES_CACHE_PREFIX . strtoupper($type) . '_' . strtoupper($locale);
    }

    /**
     * Get all cache keys that should be invalidated when categories change
     */
    public static function getCategoryRelatedCacheKeys(): array
    {
        $keys = [
            self::getCategoriesCacheKey(),
            self::getCategoriesActiveCacheKey(),
        ];

        $locales = self::getSupportedLocales();
        foreach ($locales as $locale) {
            $keys[] = self::getCategoriesLocaleCacheKey($locale, 'active');
            $keys[] = self::getCategoriesLocaleCacheKey($locale, 'all');

        }


        return $keys;
    }

    /**
     * Get products cache key for all products
     */
    public static function getProductsCacheKey(): string
    {
        return self::PRODUCTS_CACHE;
    }

    /**
     * Get products cache key for active products
     */
    public static function getProductsActiveCacheKey(): string
    {
        return self::PRODUCTS_ACTIVE_CACHE;
    }

    /**
     * Get products cache key by type
     */
    public static function getProductsByTypeCacheKey(int $type): string
    {
        return self::PRODUCTS_BY_TYPE_CACHE_PREFIX . $type;
    }

    /**
     * Get products cache key by type and locale
     */
    public static function getProductsByTypeLocaleCacheKey(int $type, string $locale): string
    {
        return self::PRODUCTS_BY_TYPE_CACHE_PREFIX . $type . '_' . strtoupper($locale);
    }

    /**
     * Get products cache key by category
     */
    public static function getProductsByCategoryCacheKey(int $categoryId): string
    {
        return self::PRODUCTS_BY_CATEGORY_CACHE_PREFIX . $categoryId;
    }

    /**
     * Get products cache key by category and locale
     */
    public static function getProductsByCategoryLocaleCacheKey(int $categoryId, string $locale): string
    {
        return self::PRODUCTS_BY_CATEGORY_CACHE_PREFIX . $categoryId . '_' . strtoupper($locale);
    }

    /**
     * Get featured products cache key
     */
    public static function getProductsFeaturedCacheKey(): string
    {
        return self::PRODUCTS_FEATURED_CACHE;
    }

    /**
     * Get new arrival products cache key
     */
    public static function getProductsNewArrivalCacheKey(): string
    {
        return self::PRODUCTS_NEW_ARRIVAL_CACHE;
    }

    /**
     * Get best seller products cache key
     */
    public static function getProductsBestSellerCacheKey(): string
    {
        return self::PRODUCTS_BEST_SELLER_CACHE;
    }

    /**
     * Get top products cache key
     */
    public static function getProductsTopCacheKey(): string
    {
        return self::PRODUCTS_TOP_CACHE;
    }

    /**
     * Get products cache key for specific locale
     */
    public static function getProductsLocaleCacheKey(string $locale, string $type = 'active'): string
    {
        return self::PRODUCTS_CACHE_PREFIX . strtoupper($type) . '_' . strtoupper($locale);
    }

    /**
     * Get all cache keys that should be invalidated when products change
     */
    public static function getProductRelatedCacheKeys(int $categoryId = null, int $type = null): array
    {
        $keys = [
            self::getProductsCacheKey(),
            self::getProductsActiveCacheKey(),
            self::getProductsFeaturedCacheKey(),
            self::getProductsNewArrivalCacheKey(),
            self::getProductsBestSellerCacheKey(),
            self::getProductsTopCacheKey()
        ];

        $locales = self::getSupportedLocales();
        foreach ($locales as $locale) {
            $keys[] = self::getProductsLocaleCacheKey($locale, 'active');
            $keys[] = self::getProductsLocaleCacheKey($locale, 'all');
            $keys[] = self::getProductsLocaleCacheKey($locale, 'featured');
            $keys[] = self::getProductsLocaleCacheKey($locale, 'new_arrival');
            $keys[] = self::getProductsLocaleCacheKey($locale, 'best_seller');
            $keys[] = self::getProductsLocaleCacheKey($locale, 'top');
        }

        if ($categoryId) {
            $keys[] = self::getProductsByCategoryCacheKey($categoryId);
            foreach ($locales as $locale) {
                $keys[] = self::getProductsByCategoryLocaleCacheKey($categoryId, $locale);
            }
        }

        if ($type !== null) {
            $keys[] = self::getProductsByTypeCacheKey($type);
            foreach ($locales as $locale) {
                $keys[] = self::getProductsByTypeLocaleCacheKey($type, $locale);
            }
        }

        return $keys;
    }




}