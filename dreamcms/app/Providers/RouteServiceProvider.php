<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        $this->pattern('id', '[0-9]+');
        $this->pattern('item_id', '[0-9]+');
        $this->pattern('onlytext', '[a-zA-Z]+');
        $this->pattern('string', '[a-zA-Z0-9_-]+');
        $this->pattern('login', '[a-zA-Z0-9_]+');
    
    
        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapApiRoutes();

        $this->mapWebRoutes();
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::middleware('web')
             ->namespace($this->namespace)
             ->group(base_path('routes/web.php'));

        Route::middleware('web')
            ->namespace($this->namespace)
            ->group(base_path('routes/admin.php'));
    
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::prefix('api')
             ->middleware('api')
             ->namespace($this->namespace . '\\API')
             ->group(base_path('routes/api_admin.php'));

        Route::prefix('api')
            ->middleware('api')
            ->namespace($this->namespace . '\\API')
            ->group(base_path('routes/api.php'));

    }
}
