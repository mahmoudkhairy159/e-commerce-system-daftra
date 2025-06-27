<?php

namespace App\Types;

class CacheKeysType
{
    //countries in all languages

    public static function getCountriesCacheKeys(): array
    {
        $supportedLocales = ['en', 'ar'];
        $cacheKeys = [];

        foreach ($supportedLocales as $locale) {
            $cacheKeys[$locale] = "COUNTRIES_CACHE_{$locale}";
        }

        return $cacheKeys;
    }
    //countries in all languages


    const CITIES_CACHE = "CITIES_CACHE";
    const STATES_CACHE = "CITIES_CACHE";
    const CITIES_CACHE_PREFIX = "CITIES_CACHE_";
    const STATES_CACHE_PREFIX = "STATES_CACHE_";

    const CATEGORIES_CACHE = " CATEGORIES_CACHE";
    const CATEGORIES_TREE_CACHE = "CATEGORIES_TREE_CACHE";



    //topics in all languages
    // Dynamic topics cache keys
    public static function getTopicsCacheKeys(): array
    {
        $supportedLocales = ['en', 'ar', 'de'];
        $cacheKeys = [];

        foreach ($supportedLocales as $locale) {
            $cacheKeys[$locale] = "TOPICS_CACHE_{$locale}";
        }

        return $cacheKeys;
    }

    //topics in all languages
    const FAQS_CACHE = "FAQS_CACHE";
    const FAQS_CACHE_PREFIX = "FAQS_CACHE_";




    /**
     * Get the cache key for states by country ID.
     *
     * @param int $countryId
     * @return string
     */
    public static function citiesCacheKey(int $countryId): string
    {
        return self::CITIES_CACHE_PREFIX . $countryId;
    }
    public static function statesCacheKey(int $countryId): string
    {
        return self::STATES_CACHE_PREFIX . $countryId;
    }
    public static function faqsCacheKey(int $topicId): string
    {
        return self::FAQS_CACHE_PREFIX . $topicId;
    }

}