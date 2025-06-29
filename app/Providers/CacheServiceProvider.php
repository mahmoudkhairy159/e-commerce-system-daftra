<?php

declare(strict_types=1);

namespace App\Providers;

use App\Services\Cache\CategoryCacheService;
use App\Services\Cache\ProductCacheService;
use Illuminate\Support\ServiceProvider;

class CacheServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->registerCacheServices();
    }

    /**
     * Register cache services
     */
    private function registerCacheServices(): void
    {
        // Register categories cache service
        $this->app->singleton('cache.categories', CategoryCacheService::class);

        // Register products cache service
        $this->app->singleton('cache.products', ProductCacheService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Warm up all caches
     */
    public function warmCache(): void
    {
        // Warm categories cache
        app('cache.categories')->getAllActive();

        // Warm products cache
        app('cache.products')->getAllActive();
    }
}
