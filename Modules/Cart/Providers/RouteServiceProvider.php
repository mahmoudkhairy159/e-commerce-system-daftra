<?php

namespace Modules\Cart\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The module namespace to assume when generating URLs to actions.
     *
     * @var string
     */
    protected $moduleNamespace = 'Modules\Cart\Http\Controllers';

    /**
     * Called before routes are registered.
     *
     * Register any model bindings or pattern based filters.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapApiRoutes();
        $this->mapAdminApiRoutes();
        $this->mapWebRoutes();
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::middleware('web')
            ->namespace($this->moduleNamespace)
            ->group(module_path('Cart', 'Routes/web.php'));
    }

    protected function mapApiRoutes(): void
    {
        Route::prefix('api/user')
            ->middleware('api')
            ->namespace($this->moduleNamespace . '\Api')
            ->group(module_path('Cart', 'Routes/api.php'));
    }


    protected function mapAdminApiRoutes(): void
    {
        Route::prefix('api/admin')
            ->middleware('api')
            ->namespace($this->moduleNamespace . '\Admin')
            ->group(module_path('Cart', 'Routes/admin-api.php'));
    }

   
}