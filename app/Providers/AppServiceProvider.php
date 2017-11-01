<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

use App\Level1Activity;
use App\Services\Level1Activity\Contracts\Level1ActivitiesRepository;
use App\Services\Level1Activity\Repositories\EloquentLevel1ActivitiesRepository;

use App\Level2Activity;
use App\Services\Level2Activity\Contracts\Level2ActivitiesRepository;
use App\Services\Level2Activity\Repositories\EloquentLevel2ActivitiesRepository;

use App\Level4Activity;
use App\Services\Level4Activity\Contracts\Level4ActivitiesRepository;
use App\Services\Level4Activity\Repositories\EloquentLevel4ActivitiesRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(Level1ActivitiesRepository::class, function () {
            return new EloquentLevel1ActivitiesRepository(new Level1Activity());
        });

        $this->app->bind(Level2ActivitiesRepository::class, function () {
            return new EloquentLevel2ActivitiesRepository(new Level2Activity());
        });

        $this->app->bind(Level4ActivitiesRepository::class, function () {
            return new EloquentLevel4ActivitiesRepository(new Level4Activity());
        });

        
    }
}
