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
    protected $namespace = 'App\Http\Controllers';

    /**
     * The controller namespace for the application.
     *
     * When present, controller route declarations will automatically be prefixed with this namespace.
     *
     * @var string|null
     */
    // protected $namespace = 'App\\Http\\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureAppRateLimiting();
        $this->mapAdminRoutes();
        $this->mapChatRoutes();
        $this->mapFrontRoutes(); // front routes should always be in bottom
        $this->mapApiRoutes();
    }

    /**
     * Limits for the SBK app's forms (routes/app.php), each counted on its own
     * per phone address, so signing in never uses up the contact form's turns.
     *
     * @return void
     */
    protected function configureAppRateLimiting()
    {
        $per = fn (string $what, int $perMinute) => RateLimiter::for('app-' . $what,
            fn (Request $request) => Limit::perMinute($perMinute)->by($what . '|' . $request->ip()));

        $per('auth', 10);
        $per('forgot', 5);
        $per('checkout', 10);
        $per('contact', 10);
        $per('chat-start', 30);
    }

    /**
     * The live chat (routes/chat.php). "web": the session says who is here.
     *
     * @return void
     */
    protected function mapChatRoutes()
    {
        Route::middleware('web')
            ->group(base_path('routes/chat.php'));
    }

    /**
     * Define the "admin" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapAdminRoutes()
    {
        Route::prefix('admin')
            ->middleware('web')
            ->namespace($this->namespace . '\Admin')
            ->group(base_path('routes/admin.php'));
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapFrontRoutes()
    {
        Route::middleware('web')
            ->namespace($this->namespace . '\Front')
            ->group(base_path('routes/front.php'));
    }

    /**
    * Define the "api" routes for the application.
    *
    * @return void
    */
    protected function mapApiRoutes()
    {
        Route::prefix('api')
             ->middleware('api')
            ->namespace($this->namespace . '\Api')
            ->group(base_path('routes/api.php'));
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
