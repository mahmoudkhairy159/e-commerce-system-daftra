<?php

declare(strict_types=1);

namespace App\Types;

class CacheKeysType
{
    // Cache TTL in seconds (5 days)
    public const CACHE_TTL_SECONDS = 432000; // 5 * 24 * 60 * 60

    // Categories cache keys
    public const CATEGORIES_CACHE = 'CATEGORIES_CACHE';
    public const CATEGORIES_ACTIVE_CACHE = 'CATEGORIES_ACTIVE_CACHE';
    public const CATEGORIES_CACHE_PREFIX = 'CATEGORIES_CACHE_';

    // Products cache keys
    public const PRODUCTS_CACHE = 'PRODUCTS_CACHE';
    public const PRODUCTS_ACTIVE_CACHE = 'PRODUCTS_ACTIVE_CACHE';
    public const PRODUCTS_CACHE_PREFIX = 'PRODUCTS_CACHE_';
    public const PRODUCTS_BY_TYPE_CACHE_PREFIX = 'PRODUCTS_BY_TYPE_';
    public const PRODUCTS_BY_CATEGORY_CACHE_PREFIX = 'PRODUCTS_BY_CATEGORY_';

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

        foreach (self::getSupportedLocales() as $locale) {
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
     * Get products cache key for specific locale
     */
    public static function getProductsLocaleCacheKey(string $locale, string $type = 'active'): string
    {
        return self::PRODUCTS_CACHE_PREFIX . strtoupper($type) . '_' . strtoupper($locale);
    }

    /**
     * Get all cache keys that should be invalidated when products change
     */
    public static function getProductRelatedCacheKeys(?int $categoryId = null, ?int $type = null): array
    {
        $keys = [
            self::getProductsCacheKey(),
            self::getProductsActiveCacheKey(),
        ];

        // Add locale-specific keys
        foreach (self::getSupportedLocales() as $locale) {
            $keys[] = self::getProductsLocaleCacheKey($locale, 'active');
            $keys[] = self::getProductsLocaleCacheKey($locale, 'all');
        }

        // Add type-specific keys
        foreach ([0, 1, 2, 3] as $productType) {
            $keys[] = self::getProductsByTypeCacheKey($productType);

            foreach (self::getSupportedLocales() as $locale) {
                $keys[] = self::getProductsByTypeLocaleCacheKey($productType, $locale);
                $keys[] = self::getProductsLocaleCacheKey($locale, self::getProductTypeString($productType));
            }
        }

        // Add category-specific keys if provided
        if ($categoryId !== null) {
            $keys[] = self::getProductsByCategoryCacheKey($categoryId);

            foreach (self::getSupportedLocales() as $locale) {
                $keys[] = self::getProductsByCategoryLocaleCacheKey($categoryId, $locale);
            }
        }

        // Add specific type keys if provided
        if ($type !== null) {
            $keys[] = self::getProductsByTypeCacheKey($type);

            foreach (self::getSupportedLocales() as $locale) {
                $keys[] = self::getProductsByTypeLocaleCacheKey($type, $locale);
            }
        }

        return array_unique($keys);
    }

    /**
     * Get product type string for cache key
     */
    private static function getProductTypeString(int $type): string
    {
        return match ($type) {
            0 => 'new_arrival',
            1 => 'featured',
            2 => 'top',
            3 => 'best_seller',
            default => 'unknown'
        };
    }
}
