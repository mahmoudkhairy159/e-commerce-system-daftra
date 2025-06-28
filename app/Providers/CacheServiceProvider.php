<?php

namespace App\Providers;

use App\Types\CacheKeysType;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\ServiceProvider;
use Modules\Area\Repositories\CityRepository;
use Modules\Area\Repositories\StateRepository;
use Modules\Area\Repositories\CountryRepository;
use Modules\Category\Repositories\CategoryRepository;
use Modules\Product\Repositories\ProductRepository;

class CacheServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerCacheServices();
    }

    /**
     * Register cache services
     *
     * @return void
     */
    private function registerCacheServices()
    {
        // Register countries cache service
        $this->app->singleton('cache.countries', function () {
            return new class {
                public function get(string $locale)
                {
                    $cacheKey = CacheKeysType::getCountriesCacheKey($locale);
                    return Cache::remember($cacheKey, CacheKeysType::CACHE_TTL_SECONDS, function () use ($locale) {
                        return app(CountryRepository::class)->getAllActive($locale);
                    });
                }

                public function getAll()
                {
                    $result = [];
                    foreach (['en', 'ar'] as $locale) {
                        $result[$locale] = $this->get($locale);
                    }
                    return $result;
                }

                public function invalidate(string $locale = null)
                {
                    if ($locale) {
                        Cache::forget(CacheKeysType::getCountriesCacheKey($locale));
                    } else {
                        $keys = CacheKeysType::getCountryRelatedCacheKeys();
                        foreach ($keys as $key) {
                            Cache::forget($key);
                        }
                    }
                }
            };
        });

        // Register states cache service
        $this->app->singleton('cache.states', function () {
            return new class {
                public function getAll()
                {
                    $cacheKey = CacheKeysType::getStatesCacheKey();
                    return Cache::remember($cacheKey, CacheKeysType::CACHE_TTL_SECONDS, function () {
                        return app(StateRepository::class)->getAll()->get();
                    });
                }

                public function getByCountry(int $countryId, string $locale = null)
                {
                    $cacheKey = $locale
                        ? CacheKeysType::getStatesByCountryLocaleCacheKey($countryId, $locale)
                        : CacheKeysType::getStatesByCountryCacheKey($countryId);

                    return Cache::remember($cacheKey, CacheKeysType::CACHE_TTL_SECONDS, function () use ($countryId) {
                        return app(StateRepository::class)->getActiveStatesByCountryId($countryId)->get();
                    });
                }

                public function invalidate(int $countryId = null)
                {
                    if ($countryId) {
                        $keys = CacheKeysType::getStateRelatedCacheKeys($countryId);
                        foreach ($keys as $key) {
                            Cache::forget($key);
                        }
                    } else {
                        Cache::forget(CacheKeysType::getStatesCacheKey());
                    }
                }
            };
        });

        // Register cities cache service
        $this->app->singleton('cache.cities', function () {
            return new class {
                public function getAll()
                {
                    $cacheKey = CacheKeysType::getCitiesCacheKey();
                    return Cache::remember($cacheKey, CacheKeysType::CACHE_TTL_SECONDS, function () {
                        return app(CityRepository::class)->getAll()->get();
                    });
                }

                public function getByCountry(int $countryId, string $locale = null)
                {
                    $cacheKey = $locale
                        ? CacheKeysType::getCitiesByCountryLocaleCacheKey($countryId, $locale)
                        : CacheKeysType::getCitiesByCountryCacheKey($countryId);

                    return Cache::remember($cacheKey, CacheKeysType::CACHE_TTL_SECONDS, function () use ($countryId) {
                        return app(CityRepository::class)->getActiveCitiesByCountryId($countryId)->get();
                    });
                }

                public function getByState(int $stateId, string $locale = null)
                {
                    $cacheKey = $locale
                        ? CacheKeysType::getCitiesByStateLocaleCacheKey($stateId, $locale)
                        : CacheKeysType::getCitiesByStateCacheKey($stateId);

                    return Cache::remember($cacheKey, CacheKeysType::CACHE_TTL_SECONDS, function () use ($stateId) {
                        return app(CityRepository::class)->getActiveCitiesByStateId($stateId)->get();
                    });
                }

                public function invalidate(int $countryId = null, int $stateId = null)
                {
                    if ($countryId && $stateId) {
                        $keys = CacheKeysType::getCityRelatedCacheKeys($countryId, $stateId);
                        foreach ($keys as $key) {
                            Cache::forget($key);
                        }
                    } else {
                        Cache::forget(CacheKeysType::getCitiesCacheKey());
                    }
                }
            };
        });

        // Register categories cache service
        $this->app->singleton('cache.categories', function () {
            return new class {
                public function getAll()
                {
                    return Cache::remember(CacheKeysType::getCategoriesCacheKey(), CacheKeysType::CACHE_TTL_SECONDS, function () {
                        return app(CategoryRepository::class)->getAll()->get();
                    });
                }

                public function getAllActive(string $locale = null)
                {
                    $cacheKey = $locale
                        ? CacheKeysType::getCategoriesLocaleCacheKey($locale, 'active')
                        : CacheKeysType::getCategoriesActiveCacheKey();

                    return Cache::remember($cacheKey, CacheKeysType::CACHE_TTL_SECONDS, function () {
                        return app(CategoryRepository::class)->getAllActive()->get();
                    });
                }

                public function getFeatured(string $locale = null)
                {
                    $cacheKey = $locale
                        ? CacheKeysType::getCategoriesLocaleCacheKey($locale, 'featured')
                        : CacheKeysType::getCategoriesFeaturedCacheKey();

                    return Cache::remember($cacheKey, CacheKeysType::CACHE_TTL_SECONDS, function () {
                        return app(CategoryRepository::class)->getFeaturedCategories()->get();
                    });
                }

                public function getMainCategories(string $locale = null)
                {
                    $cacheKey = $locale
                        ? CacheKeysType::getCategoriesLocaleCacheKey($locale, 'main')
                        : CacheKeysType::getCategoriesMainCacheKey();

                    return Cache::remember($cacheKey, CacheKeysType::CACHE_TTL_SECONDS, function () {
                        return app(CategoryRepository::class)->getActiveMainCategories()->get();
                    });
                }

                public function getTree(string $locale = null)
                {
                    $cacheKey = $locale
                        ? CacheKeysType::getCategoriesLocaleCacheKey($locale, 'tree')
                        : CacheKeysType::getCategoriesTreeCacheKey();

                    return Cache::remember($cacheKey, CacheKeysType::CACHE_TTL_SECONDS, function () {
                        return app(CategoryRepository::class)->getActiveTreeStructure();
                    });
                }

                public function getByParent(int $parentId, string $locale = null)
                {
                    $cacheKey = $locale
                        ? CacheKeysType::getCategoriesByParentLocaleCacheKey($parentId, $locale)
                        : CacheKeysType::getCategoriesByParentCacheKey($parentId);

                    return Cache::remember($cacheKey, CacheKeysType::CACHE_TTL_SECONDS, function () use ($parentId) {
                        return app(CategoryRepository::class)->getActiveByParentId($parentId)->get();
                    });
                }

                public function invalidate(int $parentId = null)
                {
                    $keys = CacheKeysType::getCategoryRelatedCacheKeys($parentId);
                    foreach ($keys as $key) {
                        Cache::forget($key);
                    }
                }

                public function invalidateAll()
                {
                    $this->invalidate();

                    // Also invalidate any parent-specific caches we might have
                    $categories = app(CategoryRepository::class)->getAll()->pluck('parent_id')->filter()->unique();
                    foreach ($categories as $parentId) {
                        $this->invalidate($parentId);
                    }
                }
            };
        });

        // Register products cache service
        $this->app->singleton('cache.products', function () {
            return new class {
                public function getAll()
                {
                    return Cache::remember(CacheKeysType::getProductsCacheKey(), CacheKeysType::CACHE_TTL_SECONDS, function () {
                        return app(ProductRepository::class)->getAll()->get();
                    });
                }

                public function getAllActive(string $locale = null)
                {
                    $cacheKey = $locale
                        ? CacheKeysType::getProductsLocaleCacheKey($locale, 'active')
                        : CacheKeysType::getProductsActiveCacheKey();

                    return Cache::remember($cacheKey, CacheKeysType::CACHE_TTL_SECONDS, function () {
                        return app(ProductRepository::class)->getAllActive()->get();
                    });
                }

                public function getByType(int $type, string $locale = null)
                {
                    $cacheKey = $locale
                        ? CacheKeysType::getProductsByTypeLocaleCacheKey($type, $locale)
                        : CacheKeysType::getProductsByTypeCacheKey($type);

                    return Cache::remember($cacheKey, CacheKeysType::CACHE_TTL_SECONDS, function () use ($type) {
                        return app(ProductRepository::class)->getProductByType($type)->get();
                    });
                }

                public function getFeatured(string $locale = null)
                {
                    $cacheKey = $locale
                        ? CacheKeysType::getProductsLocaleCacheKey($locale, 'featured')
                        : CacheKeysType::getProductsFeaturedCacheKey();

                    return Cache::remember($cacheKey, CacheKeysType::CACHE_TTL_SECONDS, function () {
                        return app(ProductRepository::class)->getProductByType(1)->get(); // 1 = FEATURED
                    });
                }

                public function getNewArrivals(string $locale = null)
                {
                    $cacheKey = $locale
                        ? CacheKeysType::getProductsLocaleCacheKey($locale, 'new_arrival')
                        : CacheKeysType::getProductsNewArrivalCacheKey();

                    return Cache::remember($cacheKey, CacheKeysType::CACHE_TTL_SECONDS, function () {
                        return app(ProductRepository::class)->getProductByType(0)->get(); // 0 = NEW_ARRIVAL
                    });
                }

                public function getBestSellers(string $locale = null)
                {
                    $cacheKey = $locale
                        ? CacheKeysType::getProductsLocaleCacheKey($locale, 'best_seller')
                        : CacheKeysType::getProductsBestSellerCacheKey();

                    return Cache::remember($cacheKey, CacheKeysType::CACHE_TTL_SECONDS, function () {
                        return app(ProductRepository::class)->getProductByType(3)->get(); // 3 = BEST_SELLER
                    });
                }

                public function getTopProducts(string $locale = null)
                {
                    $cacheKey = $locale
                        ? CacheKeysType::getProductsLocaleCacheKey($locale, 'top')
                        : CacheKeysType::getProductsTopCacheKey();

                    return Cache::remember($cacheKey, CacheKeysType::CACHE_TTL_SECONDS, function () {
                        return app(ProductRepository::class)->getProductByType(2)->get(); // 2 = TOP_PRODUCT
                    });
                }

                public function getByCategory(int $categoryId, string $locale = null)
                {
                    $cacheKey = $locale
                        ? CacheKeysType::getProductsByCategoryLocaleCacheKey($categoryId, $locale)
                        : CacheKeysType::getProductsByCategoryCacheKey($categoryId);

                    return Cache::remember($cacheKey, CacheKeysType::CACHE_TTL_SECONDS, function () use ($categoryId) {
                        return app(ProductRepository::class)
                            ->getAllActive()
                            ->whereHas('categories', function ($query) use ($categoryId) {
                                $query->where('categories.id', $categoryId);
                            })
                            ->get();
                    });
                }

                public function invalidate(int $categoryId = null, int $type = null)
                {
                    $keys = CacheKeysType::getProductRelatedCacheKeys($categoryId, $type);
                    foreach ($keys as $key) {
                        Cache::forget($key);
                    }
                }

                public function invalidateAll()
                {
                    $this->invalidate();

                    // Also invalidate specific type and category caches
                    for ($type = 0; $type <= 3; $type++) {
                        $this->invalidate(null, $type);
                    }
                }
            };
        });

        // Legacy cache key bindings for backward compatibility
        $this->registerLegacyCacheBindings();
    }

    /**
     * Register legacy cache bindings for backward compatibility
     */
    private function registerLegacyCacheBindings()
    {
        $countriesCacheKeys = CacheKeysType::getCountriesCacheKeys();

        // Register countries cache
        foreach ($countriesCacheKeys as $locale => $cacheKey) {
            $this->app->singleton($cacheKey, function () use ($locale) {
                return app('cache.countries')->get($locale);
            });
        }

        // Register static cache definitions
        $this->app->singleton(CacheKeysType::CITIES_CACHE, function () {
            return app('cache.cities')->getAll();
        });

        $this->app->singleton(CacheKeysType::STATES_CACHE, function () {
            return app('cache.states')->getAll();
        });

        $this->app->singleton(CacheKeysType::CATEGORIES_TREE_CACHE, function () {
            return app('cache.categories')->getTree();
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Register cache warming commands if needed
        if ($this->app->runningInConsole()) {
            $this->commands([
                // Add cache warming commands here if needed
            ]);
        }
    }

    /**
     * Warm up all caches
     */
    public function warmCache()
    {
        // Warm countries cache
        $countriesCache = app('cache.countries');
        $countriesCache->getAll();

        // Warm states cache
        $statesCache = app('cache.states');
        $statesCache->getAll();

        // Warm cities cache
        $citiesCache = app('cache.cities');
        $citiesCache->getAll();

        // Warm categories cache
        $categoriesCache = app('cache.categories');
        $categoriesCache->getTree();
    }

    /**
     * Clear all area-related caches
     */
    public function clearAreaCaches()
    {
        app('cache.countries')->invalidate();
        app('cache.states')->invalidate();
        app('cache.cities')->invalidate();
    }
}
