<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $permission
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $permission = null)
    {
        // Check if user is authenticated
        if (!Auth::guard('admin')->check()) {
            return redirect()->route('admin.login');
        }

        $user = Auth::guard('admin')->user();
        
        // Super Admin bypasses all permission checks
        if ($user->hasRole('Super Admin', 'admin')) {
            return $next($request);
        }
        
        // If no specific permission is provided, try to determine it from the route
        if (empty($permission)) {
            $permission = $this->getPermissionFromRoute($request);
            
            // If still no permission required, just check authentication
            if (empty($permission)) {
                return $next($request);
            }
        }
        
        // Check if user has the required permission
        if ($this->checkUserPermission($user, $permission)) {
            return $next($request);
        }
        
        // If user doesn't have the required permission, redirect to dashboard with error
        return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to access this resource.');
    }
    
    /**
     * Get the permission required for a route from the permission map
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function getPermissionFromRoute(Request $request)
    {
        $routeName = Route::currentRouteName();
        if (!$routeName) {
            return null;
        }
        
        $permissionMap = config('permission_map');
        
        // Check for exact route match
        if (isset($permissionMap[$routeName])) {
            return $permissionMap[$routeName];
        }
        
        // Check for wildcard matches
        foreach ($permissionMap as $route => $permission) {
            if (str_ends_with($route, '*')) {
                $baseRoute = substr($route, 0, -1);
                if (str_starts_with($routeName, $baseRoute)) {
                    return $permission;
                }
            }
        }
        
        return null;
    }
    
    /**
     * Check if a user has a specific permission
     *
     * @param  \App\Models\Admin  $user
     * @param  string  $permission
     * @return bool
     */
    protected function checkUserPermission($user, $permission)
    {
        // Check for exact permission match
        if ($user->hasPermissionTo($permission, 'admin')) {
            return true;
        }
        
        // Check for wildcard permission
        $permissionPrefix = explode('.', $permission)[0];
        $wildcardPermission = $permissionPrefix . '.*';
        if ($user->hasPermissionTo($wildcardPermission, 'admin')) {
            return true;
        }
        
        // Check for any permission that starts with this prefix
        $userPermissions = $user->getAllPermissions()->pluck('name')->toArray();
        foreach ($userPermissions as $userPerm) {
            if (strpos($userPerm, $permissionPrefix . '.') === 0) {
                return true;
            }
        }
        
        return false;
    }
}
