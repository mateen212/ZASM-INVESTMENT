<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * This is used by Laravel authentication to redirect users after login.
     *
     * @var string
     */
    public const HOME = '/user/dashboard';

    /**
     * The controller namespace for the application.
     *
     * When present, controller route declarations will automatically be prefixed with this namespace.
     *
     * @var string|null
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::prefix('api')
                ->middleware('api')
                ->namespace($this->namespace)
                ->group(base_path('routes/api.php'));

            // Web routes (including partner auth routes)
            dd('Partner routes loaded');

            // Admin routes
            Route::prefix("admin")
                ->middleware(["web", "admin", "check.permission"])
                ->namespace($this->namespace . "\Admin")
                ->name("admin.")
                ->group(base_path("routes/admin.php"));
                
            // Partner routes for admin functionality
            // This allows partners to access admin-like functionality while staying in the partner URL context
            // Route::prefix("partner")
            //     ->middleware(["web", "auth:admin", "role:partner"])
            //     ->namespace($this->namespace . "\Admin")
            //     ->name("admin.")
            //     ->group(base_path("routes/admin_partner.php"));

            // User routes
            Route::middleware("web")
                ->namespace($this->namespace)
                ->group(base_path("routes/user.php"));
                
            // Partner dashboard routes - using a different name prefix to avoid conflicts
            Route::prefix("partner")
                ->middleware(["web", "auth:admin", "role:partner"])
                ->namespace($this->namespace)
                ->name("partner.")
                ->group(base_path("routes/partner_dashboard.php"));

            Route::middleware("web")
                ->namespace($this->namespace)
                ->group(base_path("routes/web.php"));
        });
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by(optional($request->user())->id ?: $request->ip());
        });
    }
}
