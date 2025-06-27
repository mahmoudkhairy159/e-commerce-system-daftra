<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->header('Accept-Language')) {
            // Set the application Accept-Language to the value in the header

            $supportedLocales = core()->getSupportedLanguagesKeys();
            $isSupported = in_array($request->header('Accept-Language'), $supportedLocales);
            if ($isSupported) {

                app()->setLocale($request->header('Accept-Language'));
            }
        }

        return $next($request);
    }
}
