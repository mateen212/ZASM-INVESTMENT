<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Spatie\Permission\Exceptions\UnauthorizedException;

class RolePermissionMiddleware
{
    public function handle(Request $request, Closure $next, $permission = null, $guard = null)
    {
        $authGuard = app('auth')->guard($guard);

        if ($authGuard->guest()) {
            throw UnauthorizedException::notLoggedIn();
        }

        if (! empty($permission)) {
            $permissions = is_array($permission)
                ? $permission
                : explode('|', $permission);

            foreach ($permissions as $permission) {
                if ($authGuard->user()->can($permission)) {
                    return $next($request);
                }
            }

            throw UnauthorizedException::forPermissions($permissions);
        }

        return $next($request);
    }
}
