<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Nwidart\Modules\Laravel\Module;

class ModulesUppercaseServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Override the module's path resolving methods
        $this->app->extend('modules', function ($modules, $app) {
            // Extend the module manager
            $modules->macro('getRoutesPath', function (Module $module) {
                return $module->getPath() . '/Routes';
            });

            $modules->macro('getConfigPath', function (Module $module) {
                return $module->getPath() . '/Config';
            });

            return $modules;
        });
    }

    public function boot()
    {
        // After modules are booted, modify paths for existing modules
        $this->app->booted(function () {
            $modules = $this->app['modules']->all();
            
            foreach ($modules as $module) {
                $this->fixModuleServiceProviders($module);
            }
        });
    }

    protected function fixModuleServiceProviders($module)
    {
        // Find the module's route service provider
        $providerPath = $module->getPath() . '/Providers/RouteServiceProvider.php';
        
        if (file_exists($providerPath)) {
            $content = file_get_contents($providerPath);
            
            // Replace any reference to lowercase 'routes' with uppercase 'Routes'
            $content = str_replace("'/routes'", "'/Routes'", $content);
            $content = str_replace("'/routes/", "'/Routes/", $content);
            $content = str_replace('"/routes/', '"/Routes/', $content);
            
            file_put_contents($providerPath, $content);
        }
    }
}
