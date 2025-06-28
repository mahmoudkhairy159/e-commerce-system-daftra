<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Providers\CacheServiceProvider;

class CacheAreaCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:area {action : The action to perform (warm|clear|status)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manage area-related caches (countries, cities, states, categories, products)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $action = $this->argument('action');

        switch ($action) {
            case 'warm':
                return $this->warmCache();
            case 'clear':
                return $this->clearCache();
            case 'status':
                return $this->showCacheStatus();
            default:
                $this->error("Invalid action. Available actions: warm, clear, status");
                return 1;
        }
    }

    /**
     * Warm up all area-related caches
     */
    private function warmCache()
    {
        $this->info('Warming up area caches...');

        try {
            $startTime = microtime(true);

            // Warm countries cache
            $this->line('Warming countries cache...');
            $countriesCache = app('cache.countries');
            $countriesData = $countriesCache->getAll();
            $this->info('✓ Countries cache warmed for ' . count($countriesData) . ' locales');

            // Warm states cache
            $this->line('Warming states cache...');
            $statesCache = app('cache.states');
            $statesData = $statesCache->getAll();
            $this->info('✓ States cache warmed (' . count($statesData) . ' states)');

            // Warm cities cache
            $this->line('Warming cities cache...');
            $citiesCache = app('cache.cities');
            $citiesData = $citiesCache->getAll();
            $this->info('✓ Cities cache warmed (' . count($citiesData) . ' cities)');

            // Warm categories cache
            $this->line('Warming categories cache...');
            $categoriesCache = app('cache.categories');

            // Warm all category cache types
            $allCategories = $categoriesCache->getAll();
            $activeCategories = $categoriesCache->getAllActive();
            $featuredCategories = $categoriesCache->getFeatured();
            $mainCategories = $categoriesCache->getMainCategories();
            $treeCategories = $categoriesCache->getTree();

            $this->info('✓ Categories cache warmed (All: ' . count($allCategories) . ', Active: ' . count($activeCategories) . ', Featured: ' . count($featuredCategories) . ', Main: ' . count($mainCategories) . ', Tree: ' . count($treeCategories) . ')');

            // Warm products cache
            $this->line('Warming products cache...');
            $productsCache = app('cache.products');

            // Warm all product cache types
            $allProducts = $productsCache->getAll();
            $activeProducts = $productsCache->getAllActive();
            $featuredProducts = $productsCache->getFeatured();
            $newArrivals = $productsCache->getNewArrivals();
            $bestSellers = $productsCache->getBestSellers();
            $topProducts = $productsCache->getTopProducts();

            $this->info('✓ Products cache warmed (All: ' . count($allProducts) . ', Active: ' . count($activeProducts) . ', Featured: ' . count($featuredProducts) . ', New Arrivals: ' . count($newArrivals) . ', Best Sellers: ' . count($bestSellers) . ', Top: ' . count($topProducts) . ')');

            $endTime = microtime(true);
            $executionTime = round(($endTime - $startTime) * 1000, 2);

            $this->info("Cache warming completed in {$executionTime}ms");
            return 0;

        } catch (\Exception $e) {
            $this->error('Error warming cache: ' . $e->getMessage());
            return 1;
        }
    }

    /**
     * Clear all area-related caches
     */
    private function clearCache()
    {
        $this->info('Clearing area caches...');

        try {
            // Clear countries cache
            app('cache.countries')->invalidate();
            $this->info('✓ Countries cache cleared');

            // Clear states cache
            app('cache.states')->invalidate();
            $this->info('✓ States cache cleared');

            // Clear cities cache
            app('cache.cities')->invalidate();
            $this->info('✓ Cities cache cleared');

            // Clear categories cache
            app('cache.categories')->invalidate();
            $this->info('✓ Categories cache cleared');

            // Clear products cache
            app('cache.products')->invalidateAll();
            $this->info('✓ Products cache cleared');

            $this->info('All area caches cleared successfully');
            return 0;

        } catch (\Exception $e) {
            $this->error('Error clearing cache: ' . $e->getMessage());
            return 1;
        }
    }

    /**
     * Show cache status
     */
    private function showCacheStatus()
    {
        $this->info('Area Cache Status:');
        $this->line('==================');

        try {
            // Check countries cache
            $this->checkCacheStatus('Countries', function () {
                return app('cache.countries')->getAll();
            });

            // Check states cache
            $this->checkCacheStatus('States', function () {
                return app('cache.states')->getAll();
            });

            // Check cities cache
            $this->checkCacheStatus('Cities', function () {
                return app('cache.cities')->getAll();
            });

            // Check categories cache - multiple types
            $this->line('Categories Cache:');
            $this->checkCacheStatus('  - All Categories', function () {
                return app('cache.categories')->getAll();
            });
            $this->checkCacheStatus('  - Active Categories', function () {
                return app('cache.categories')->getAllActive();
            });
            $this->checkCacheStatus('  - Featured Categories', function () {
                return app('cache.categories')->getFeatured();
            });
            $this->checkCacheStatus('  - Main Categories', function () {
                return app('cache.categories')->getMainCategories();
            });
            $this->checkCacheStatus('  - Tree Structure', function () {
                return app('cache.categories')->getTree();
            });

            // Check products cache - multiple types
            $this->line('Products Cache:');
            $this->checkCacheStatus('  - All Products', function () {
                return app('cache.products')->getAll();
            });
            $this->checkCacheStatus('  - Active Products', function () {
                return app('cache.products')->getAllActive();
            });
            $this->checkCacheStatus('  - Featured Products', function () {
                return app('cache.products')->getFeatured();
            });
            $this->checkCacheStatus('  - New Arrivals', function () {
                return app('cache.products')->getNewArrivals();
            });
            $this->checkCacheStatus('  - Best Sellers', function () {
                return app('cache.products')->getBestSellers();
            });
            $this->checkCacheStatus('  - Top Products', function () {
                return app('cache.products')->getTopProducts();
            });

            return 0;

        } catch (\Exception $e) {
            $this->error('Error checking cache status: ' . $e->getMessage());
            return 1;
        }
    }

    /**
     * Check cache status for a specific type
     */
    private function checkCacheStatus(string $type, callable $checker)
    {
        try {
            $startTime = microtime(true);
            $data = $checker();
            $endTime = microtime(true);
            $responseTime = round(($endTime - $startTime) * 1000, 2);

            if ($data && (is_array($data) ? count($data) > 0 : $data->count() > 0)) {
                $count = is_array($data) ? count($data) : $data->count();
                $this->info("✓ {$type}: Cached ({$count} items, {$responseTime}ms)");
            } else {
                $this->warn("⚠ {$type}: Empty or not cached ({$responseTime}ms)");
            }
        } catch (\Exception $e) {
            $this->error("✗ {$type}: Error - " . $e->getMessage());
        }
    }
}
