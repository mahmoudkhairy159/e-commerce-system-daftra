<?php

namespace App\Helpers;

use App\Types\CacheKeysType;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;




class Core
{

    public function getACL()
    {
        return config('acl');
    }

    public function getSupportedLocales()
    {dd(LaravelLocalization::getSupportedLocales());
        return LaravelLocalization::getSupportedLocales();
    }

    public function getSupportedLanguagesKeys()
    {
        return LaravelLocalization::getSupportedLanguagesKeys();
    }


    public function getCurrentLocaleName()
    {
        return LaravelLocalization::getCurrentLocaleName();
    }


    public function getCurrentLocale()
    {
        return LaravelLocalization::getCurrentLocale();
    }

    public function getLocalesOrder()
    {
        return LaravelLocalization::getLocalesOrder();
    }

    public function getCurrentLocaleDirection()
    {
        return LaravelLocalization::getCurrentLocaleDirection();
    }




    public function getAppSettings()
    {
        // return app(CacheKeysType::APP_SETTINGS_CACHE);
        return  0;
    }


}
