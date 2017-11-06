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

use App\Services\Teenagers\Contracts\TeenagersRepository;
use App\Teenagers;
use App\Services\Teenagers\Repositories\EloquentTeenagersRepository;

use App\Services\Professions\Contracts\ProfessionsRepository;
use App\Professions;
use App\Services\Professions\Repositories\EloquentProfessionsRepository;

use App\Services\Sponsors\Contracts\SponsorsRepository;
use App\Sponsors;
use App\Services\Sponsors\Repositories\EloquentSponsorsRepository;

use App\Services\Template\Contracts\TemplatesRepository;
use App\Templates;
use App\Services\Template\Repositories\EloquentTemplatesRepository;

use App\Services\FileStorage\Contracts\FileStorageRepository;
use App\Services\FileStorage\Entities\FileStorage;
use App\Services\FileStorage\Repositories\EloquentFileStorageRepository;

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

        $this->app->bind(TeenagersRepository::class, function () {
            return new EloquentTeenagersRepository(new Teenagers());
        });

        $this->app->bind(ProfessionsRepository::class, function () {
            return new EloquentProfessionsRepository(new Professions());
        });

        $this->app->bind(SponsorsRepository::class, function () {
            return new EloquentSponsorsRepository(new Sponsors());
        });

        $this->app->bind(TemplatesRepository::class, function () {
            return new EloquentTemplatesRepository(new Templates());
        });

        $this->app->bind(FileStorageRepository::class, function () {
            return new EloquentFileStorageRepository(new FileStorage());
        });
    }
}
