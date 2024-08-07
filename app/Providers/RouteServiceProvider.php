<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, etc.
     */
    public function boot(): void
    {
        parent::boot();
    }

    /**
     * Define the routes for the application.
     */
    public function map(Request $request): void
    {
        $this->mapApiRoutes();
        $this->mapSiteRoutes();
        $this->mapWebRoutes($request);

        // $this->mapApiV1Routes();
        // $this->mapApiV2Routes();
        // $this->mapApiAdminRoutes();
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     */
    protected function mapWebRoutes($request)
    {
        Route::middleware('web')
            ->group(base_path('routes/web.php'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     */
    protected function mapApiRoutes()
    {
        Route::prefix('api')
            ->middleware('api')
            ->namespace($this->namespace)
            ->group(base_path('routes/api.php'));
    }

    protected function mapSiteRoutes()
    {
        Route::prefix('api')
            ->middleware('api')
            ->namespace($this->namespace)
            ->group(base_path('routes/Site.php'));
    }

    /**
     * Define the "api/v1" routes for the application.
     */
    protected function mapApiV1Routes()
    {
        Route::prefix('api/v1')
            ->middleware('api')
            ->namespace($this->namespace)
            ->group(base_path('routes/api_v1.php'));
    }

    /**
     * Define the "api/v2" routes for the application.
     */
    protected function mapApiV2Routes()
    {
        Route::prefix('api/v2')
            ->middleware('api')
            ->namespace($this->namespace)
            ->group(base_path('routes/api_v2.php'));
    }

    /**
     * Define the "api/admin" routes for the application.
     */
    protected function mapApiAdminRoutes()
    {
        Route::prefix('api/admin')
            ->middleware('api')
            ->namespace($this->namespace)
            ->group(base_path('routes/api_admin.php'));
    }
}
