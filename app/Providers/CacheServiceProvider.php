<?php

namespace App\Providers;

use App\Types\CacheKeysType;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\ServiceProvider;
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







                public function invalidate()
                {
                    $keys = CacheKeysType::getCategoryRelatedCacheKeys();
                    foreach ($keys as $key) {
                        Cache::forget($key);
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


    }



    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {

    }

    /**
     * Warm up all caches
     */
    public function warmCache()
    {


        // Warm categories cache
        app('cache.categories')->getAllActive();
    }


}