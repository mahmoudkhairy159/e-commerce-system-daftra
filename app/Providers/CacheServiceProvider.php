<?php

namespace App\Providers;

use App\Types\CacheKeysType;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\ServiceProvider;
use Modules\Area\Repositories\CityRepository;
use Modules\Area\Repositories\StateRepository;
use Modules\Area\Repositories\CountryRepository;
use Modules\Category\Repositories\CategoryRepository;

class CacheServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerCacheKeys();
    }

    /**
     * Bind cache keys and their closures to the container.
     *
     * @return void
     */
    private function registerCacheKeys()
    {
        $cacheKeys = $this->getCacheKeys();

        foreach ($cacheKeys as $key => $closure) {
            $this->app->singleton($key, $closure);
        }
    }

    /**
     * Get an array of cache keys and their closures.
     *
     * @return array
     */
    private function getCacheKeys()
    {
        $cacheData = [];


        $countriesCacheKeys = CacheKeysType::getCountriesCacheKeys();



        // Register countries cache
        foreach ($countriesCacheKeys as $countriesCacheKey) {
            $cacheData[$countriesCacheKey] = function () use ($countriesCacheKey) {
                return Cache::remember($countriesCacheKey, now()->addDays(5), function () {
                    $locale = core()->getCurrentLocale();

                    return app(CountryRepository::class)->getAllActive($locale);
                });
            };
        }


        // Static cache definitions for cities, states, and event categories
        $cacheData = array_merge($cacheData, [
                // Cities Cache
            CacheKeysType::CITIES_CACHE => function () {
                return Cache::remember(CacheKeysType::CITIES_CACHE, now()->addDays(5), function () {
                    return app(CityRepository::class)->getAll()->get();
                });
            },

                // States Cache
            CacheKeysType::STATES_CACHE => function () {
                return Cache::remember(CacheKeysType::STATES_CACHE, now()->addDays(5), function () {
                    return app(StateRepository::class)->getAll()->get();
                });
            },

             //  Categories Cache
            // Categories Tree Structure Cache
            CacheKeysType::CATEGORIES_TREE_CACHE => function () {
                return Cache::remember(CacheKeysType::CATEGORIES_TREE_CACHE, now()->addDays(5), function () {
                    return app(CategoryRepository::class)->getActiveTreeStructure();
                });
            },




        ]);




        return $cacheData;
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Additional bootstrapping if needed
    }
}