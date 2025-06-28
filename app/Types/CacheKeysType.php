<?php

namespace App\Types;

class CacheKeysType
{
    // Cache TTL in seconds (5 days)
    const CACHE_TTL_SECONDS = 432000; // 5 * 24 * 60 * 60

    // Countries cache keys - locale-based
    const COUNTRIES_CACHE_PREFIX = "COUNTRIES_CACHE_";

    // States cache keys
    const STATES_CACHE = "STATES_CACHE";
    const STATES_CACHE_PREFIX = "STATES_CACHE_";
    const STATES_BY_COUNTRY_CACHE_PREFIX = "STATES_BY_COUNTRY_";

    // Cities cache keys
    const CITIES_CACHE = "CITIES_CACHE";
    const CITIES_CACHE_PREFIX = "CITIES_CACHE_";
    const CITIES_BY_COUNTRY_CACHE_PREFIX = "CITIES_BY_COUNTRY_";
    const CITIES_BY_STATE_CACHE_PREFIX = "CITIES_BY_STATE_";

    // Categories cache keys
    const CATEGORIES_CACHE = "CATEGORIES_CACHE";
    const CATEGORIES_TREE_CACHE = "CATEGORIES_TREE_CACHE";
    const CATEGORIES_CACHE_PREFIX = "CATEGORIES_CACHE_";
    const CATEGORIES_ACTIVE_CACHE = "CATEGORIES_ACTIVE_CACHE";
    const CATEGORIES_FEATURED_CACHE = "CATEGORIES_FEATURED_CACHE";
    const CATEGORIES_MAIN_CACHE = "CATEGORIES_MAIN_CACHE";
    const CATEGORIES_BY_PARENT_CACHE_PREFIX = "CATEGORIES_BY_PARENT_";

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
     * Get countries cache keys for all supported locales
     */
    public static function getCountriesCacheKeys(): array
    {
        $supportedLocales = self::getSupportedLocales();
        $cacheKeys = [];

        foreach ($supportedLocales as $locale) {
            $cacheKeys[$locale] = self::COUNTRIES_CACHE_PREFIX . strtoupper($locale);
        }

        return $cacheKeys;
    }

    /**
     * Get countries cache key for specific locale
     */
    public static function getCountriesCacheKey(string $locale): string
    {
        return self::COUNTRIES_CACHE_PREFIX . strtoupper($locale);
    }




    /**
     * Get states cache key for all states
     */
    public static function getStatesCacheKey(): string
    {
        return self::STATES_CACHE;
    }

    /**
     * Get states cache key by country ID
     */
    public static function getStatesByCountryCacheKey(int $countryId): string
    {
        return self::STATES_BY_COUNTRY_CACHE_PREFIX . $countryId;
    }

    /**
     * Get states cache key by country ID and locale
     */
    public static function getStatesByCountryLocaleCacheKey(int $countryId, string $locale): string
    {
        return self::STATES_BY_COUNTRY_CACHE_PREFIX . $countryId . '_' . strtoupper($locale);
    }

    /**
     * Get cities cache key for all cities
     */
    public static function getCitiesCacheKey(): string
    {
        return self::CITIES_CACHE;
    }

    /**
     * Get cities cache key by country ID
     */
    public static function getCitiesByCountryCacheKey(int $countryId): string
    {
        return self::CITIES_BY_COUNTRY_CACHE_PREFIX . $countryId;
    }

    /**
     * Get cities cache key by country ID and locale
     */
    public static function getCitiesByCountryLocaleCacheKey(int $countryId, string $locale): string
    {
        return self::CITIES_BY_COUNTRY_CACHE_PREFIX . $countryId . '_' . strtoupper($locale);
    }

    /**
     * Get cities cache key by state ID
     */
    public static function getCitiesByStateCacheKey(int $stateId): string
    {
        return self::CITIES_BY_STATE_CACHE_PREFIX . $stateId;
    }

    /**
     * Get cities cache key by state ID and locale
     */
    public static function getCitiesByStateLocaleCacheKey(int $stateId, string $locale): string
    {
        return self::CITIES_BY_STATE_CACHE_PREFIX . $stateId . '_' . strtoupper($locale);
    }


    /**
     * Get all cache keys that should be invalidated when countries change
     */
    public static function getCountryRelatedCacheKeys(): array
    {
        $keys = [];
        $locales = self::getSupportedLocales();

        // Add countries cache keys
        foreach ($locales as $locale) {
            $keys[] = self::getCountriesCacheKey($locale);
        }

        return $keys;
    }

    /**
     * Get all cache keys that should be invalidated when states change
     */
    public static function getStateRelatedCacheKeys(int $countryId): array
    {
        $keys = [
            self::getStatesCacheKey(),
            self::getStatesByCountryCacheKey($countryId)
        ];

        $locales = self::getSupportedLocales();
        foreach ($locales as $locale) {
            $keys[] = self::getStatesByCountryLocaleCacheKey($countryId, $locale);
        }

        return $keys;
    }

    /**
     * Get all cache keys that should be invalidated when cities change
     */
    public static function getCityRelatedCacheKeys(int $countryId, int $stateId): array
    {
        $keys = [
            self::getCitiesCacheKey(),
            self::getCitiesByCountryCacheKey($countryId),
            self::getCitiesByStateCacheKey($stateId)
        ];

        $locales = self::getSupportedLocales();
        foreach ($locales as $locale) {
            $keys[] = self::getCitiesByCountryLocaleCacheKey($countryId, $locale);
            $keys[] = self::getCitiesByStateLocaleCacheKey($stateId, $locale);
        }

        return $keys;
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
     * Get categories cache key for featured categories
     */
    public static function getCategoriesFeaturedCacheKey(): string
    {
        return self::CATEGORIES_FEATURED_CACHE;
    }

    /**
     * Get categories cache key for main categories
     */
    public static function getCategoriesMainCacheKey(): string
    {
        return self::CATEGORIES_MAIN_CACHE;
    }

    /**
     * Get categories cache key for tree structure
     */
    public static function getCategoriesTreeCacheKey(): string
    {
        return self::CATEGORIES_TREE_CACHE;
    }

    /**
     * Get categories cache key by parent ID
     */
    public static function getCategoriesByParentCacheKey(int $parentId): string
    {
        return self::CATEGORIES_BY_PARENT_CACHE_PREFIX . $parentId;
    }

    /**
     * Get categories cache key by parent ID and locale
     */
    public static function getCategoriesByParentLocaleCacheKey(int $parentId, string $locale): string
    {
        return self::CATEGORIES_BY_PARENT_CACHE_PREFIX . $parentId . '_' . strtoupper($locale);
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
    public static function getCategoryRelatedCacheKeys(int $parentId = null): array
    {
        $keys = [
            self::getCategoriesCacheKey(),
            self::getCategoriesActiveCacheKey(),
            self::getCategoriesFeaturedCacheKey(),
            self::getCategoriesMainCacheKey(),
            self::getCategoriesTreeCacheKey()
        ];

        $locales = self::getSupportedLocales();
        foreach ($locales as $locale) {
            $keys[] = self::getCategoriesLocaleCacheKey($locale, 'active');
            $keys[] = self::getCategoriesLocaleCacheKey($locale, 'all');
            $keys[] = self::getCategoriesLocaleCacheKey($locale, 'featured');
            $keys[] = self::getCategoriesLocaleCacheKey($locale, 'main');
            $keys[] = self::getCategoriesLocaleCacheKey($locale, 'tree');
        }

        if ($parentId) {
            $keys[] = self::getCategoriesByParentCacheKey($parentId);
            foreach ($locales as $locale) {
                $keys[] = self::getCategoriesByParentLocaleCacheKey($parentId, $locale);
            }
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

    // Legacy methods for backward compatibility (deprecated)
    public static function statesCacheKey(int $countryId): string
    {
        return self::getStatesByCountryCacheKey($countryId);
    }

    public static function citiesCacheKey(int $countryId): string
    {
        return self::getCitiesByCountryCacheKey($countryId);
    }


}