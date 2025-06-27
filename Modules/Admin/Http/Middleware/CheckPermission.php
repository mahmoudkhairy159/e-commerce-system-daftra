<?php

namespace Modules\Admin\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Exceptions\HttpResponseException;

class CheckPermission
{
    /**
     * Handle an incoming request.
     */
    public function handle($request, Closure $next, ...$permissions)
    {
        $admin = Auth::guard('admin-api')->user();
        foreach ($permissions as $permission) {
            if (!$admin->hasPermission($permission)) {
                throw new HttpResponseException(response()->json([
                    'message' => 'Unauthorized',
                    'success' => false,
                    'statusCode' => 403
                ], 401));
            }
        }


        return $next($request);
    }
}
