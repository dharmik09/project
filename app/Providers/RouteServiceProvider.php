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
        //

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

        $this->mapSchoolRoutes();

        $this->mapSponsorRoutes();

        $this->mapParentRoutes();

        $this->mapTeenagerRoutes();

        $this->mapDeveloperRoutes();

        $this->mapAdminRoutes();

        //
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
             ->namespace($this->namespace)
             ->group(base_path('routes/api.php'));
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
        Route::group([
            'middleware' => ['web', 'admin', 'auth:admin'],
            'prefix' => 'admin',
            'as' => 'admin.',
            'namespace' => $this->namespace,
        ], function ($router) {
            require base_path('routes/admin.php');
        });
    }

    /**
     * Define the "developer" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapDeveloperRoutes()
    {
        Route::group([
            'middleware' => ['web', 'developer', 'auth:developer'],
            'prefix' => 'developer',
            'as' => 'developer.',
            'namespace' => $this->namespace,
        ], function ($router) {
            require base_path('routes/developer.php');
        });
    }

    /**
     * Define the "teenager" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapTeenagerRoutes()
    {
        Route::group([
            'middleware' => ['web', 'teenager', 'auth:teenager'],
            'prefix' => 'teenager',
            'as' => 'teenager.',
            'namespace' => $this->namespace,
        ], function ($router) {
            require base_path('routes/teenager.php');
        });
    }

    /**
     * Define the "parent" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapParentRoutes()
    {
        Route::group([
            'middleware' => ['web', 'parent', 'auth:parent'],
            'prefix' => 'parent',
            'as' => 'parent.',
            'namespace' => $this->namespace,
        ], function ($router) {
            require base_path('routes/parent.php');
        });
    }

    /**
     * Define the "sponsor" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapSponsorRoutes()
    {
        Route::group([
            'middleware' => ['web', 'sponsor', 'auth:sponsor'],
            'prefix' => 'sponsor',
            'as' => 'sponsor.',
            'namespace' => $this->namespace,
        ], function ($router) {
            require base_path('routes/sponsor.php');
        });
    }

    /**
     * Define the "school" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapSchoolRoutes()
    {
        Route::group([
            'middleware' => ['web', 'school', 'auth:school'],
            'prefix' => 'school',
            'as' => 'school.',
            'namespace' => $this->namespace,
        ], function ($router) {
            require base_path('routes/school.php');
        });
    }
}
