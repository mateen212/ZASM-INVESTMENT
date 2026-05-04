<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Exceptions\UnauthorizedException;

class PermissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $permission
     * @return mixed
     */
    public function handle($request, Closure $next, $permission)
    {
        // Check if user is logged in
        if (!Auth::guard('admin')->check()) {
            return to_route('admin.login');
        }

        // Get the authenticated user
        $user = Auth::guard('admin')->user();
        
        // Debug logging (only in development environment)
        if (app()->environment('local', 'development')) {
            Log::info('Permission check for: ' . $permission);
            Log::info('User: ' . $user->name . ' (ID: ' . $user->id . ')');
            Log::info('User roles: ' . implode(', ', $user->getRoleNames()->toArray()));
        }
        
        // Super Admin role should bypass all permission checks
        if ($user->hasRole('Super Admin', 'admin')) {
            if (app()->environment('local', 'development')) {
                Log::info('User has Super Admin role - bypassing permission check for: ' . $permission);
            }
            return $next($request);
        }
        
        // CEO role should have access to most things except system-level permissions
        if ($user->hasRole('CEO', 'admin') && !str_starts_with($permission, 'system.')) {
            if (app()->environment('local', 'development')) {
                Log::info('User has CEO role - bypassing permission check for: ' . $permission);
            }
            return $next($request);
        }
        
        // C-Suite executives have access to their departments
        if ($this->hasExecutiveAccess($user, $permission)) {
            if (app()->environment('local', 'development')) {
                Log::info('User has executive access to this permission');
            }
            return $next($request);
        }
        
        // Department managers have full access to their department
        if ($this->hasDepartmentManagerAccess($user, $permission)) {
            if (app()->environment('local', 'development')) {
                Log::info('User has department manager access to this permission');
            }
            return $next($request);
        }

        // Check if the user has the required permission
        $hasPermission = $user->hasPermissionTo($permission, 'admin');
        
        if (app()->environment('local', 'development')) {
            Log::info('User has permission ' . $permission . ': ' . ($hasPermission ? 'Yes' : 'No'));
        }
        
        if (!$hasPermission) {
            throw new UnauthorizedException(403, 'You do not have the required permission to access this resource.');
        }

        return $next($request);
    }
    
    /**
     * Check if user has executive-level access to the permission
     *
     * @param  \App\Models\Admin  $user
     * @param  string  $permission
     * @return bool
     */
    private function hasExecutiveAccess($user, $permission)
    {
        $executiveMap = [
            'CFO' => ['accounting.', 'investments.view', 'deals.view', 'assets.view'],
            'COO' => ['deals.', 'assets.', 'partnerships.', 'investor_relations.', 'marketing.', 'general_management.'],
            'CTO' => ['tech.', 'system.', 'settings.'],
            'CLO' => ['compliance_legal.']
        ];
        
        foreach ($executiveMap as $role => $permissionPrefixes) {
            if ($user->hasRole($role, 'admin')) {
                foreach ($permissionPrefixes as $prefix) {
                    if (str_starts_with($permission, $prefix)) {
                        return true;
                    }
                }
            }
        }
        
        return false;
    }

    /**
     * Check if user has department manager access to the permission
     *
     * @param  \App\Models\Admin  $user
     * @param  string  $permission
     * @return bool
     */
    private function hasDepartmentManagerAccess($user, $permission)
    {
        $managerMap = [
            'Accounting Manager' => 'accounting.',
            'Deals Manager' => 'deals.',
            'Asset Manager' => 'assets.',
            'Partnerships Manager' => 'partnerships.',
            'Investor Relations Manager' => 'investor_relations.',
            'Marketing Manager' => 'marketing.',
            'General Management Manager' => 'general_management.',
            'Technology Manager' => ['tech.', 'system.view', 'system.manage_logs', 'system.manage_cache'],
            'Compliance & Legal Manager' => 'compliance_legal.'
        ];
        
        foreach ($managerMap as $role => $permissionPrefix) {
            if ($user->hasRole($role, 'admin')) {
                if (is_array($permissionPrefix)) {
                    foreach ($permissionPrefix as $prefix) {
                        if (str_starts_with($permission, $prefix) || $permission === $prefix) {
                            return true;
                        }
                    }
                } else if (str_starts_with($permission, $permissionPrefix)) {
                    return true;
                }
            }
        }
        
        return false;
    }
}
