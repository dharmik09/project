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

use App\Services\Schools\Contracts\SchoolsRepository;
use App\Schools;
use App\Services\Schools\Repositories\EloquentSchoolsRepository;

use App\Services\Teenagers\Contracts\TeenagersRepository;
use App\Teenagers;
use App\Services\Teenagers\Repositories\EloquentTeenagersRepository;

use App\Services\Professions\Contracts\ProfessionsRepository;
use App\Professions;
use App\Services\Professions\Repositories\EloquentProfessionsRepository;

use App\Services\Sponsors\Contracts\SponsorsRepository;
use App\Sponsors;
use App\Services\Sponsors\Repositories\EloquentSponsorsRepository;

use App\Services\Parents\Contracts\ParentsRepository;
use App\Parents;
use App\Services\Parents\Repositories\EloquentParentsRepository;

use App\Services\Template\Contracts\TemplatesRepository;
use App\Templates;
use App\Services\Template\Repositories\EloquentTemplatesRepository;

use App\Services\FileStorage\Contracts\FileStorageRepository;
use App\Services\FileStorage\Entities\FileStorage;
use App\Services\FileStorage\Repositories\EloquentFileStorageRepository;

use App\Services\CMS\Contracts\CMSRepository;
use App\CMS;
use App\Services\CMS\Repositories\EloquentCMSRepository;

use App\Services\FeedbackQuestions\Contracts\FeedbackQuestionsRepository;
use App\FeedbackQuestions;
use App\Services\FeedbackQuestions\Repositories\EloquentFeedbackQuestionsRepository;

use App\Services\Configurations\Contracts\ConfigurationsRepository;
use App\Configurations;
use App\Services\Configurations\Repositories\EloquentConfigurationsRepository;

use App\Services\Genericads\Contracts\GenericadsRepository;
use App\Genericads;
use App\Services\Genericads\Repositories\EloquentGenericadsRepository;

use App\Services\Coin\Contracts\CoinRepository;
use App\Coins;
use App\Services\Coin\Repositories\EloquentCoinRepository;

use App\Services\Level1CartoonIcon\Contracts\Level1CartoonIconRepository;
use App\Level1CartoonIcon;
use App\Services\Level1CartoonIcon\Repositories\EloquentLevel1CartoonIconRepository;

use App\Services\Level1HumanIcon\Contracts\Level1HumanIconRepository;
use App\Level1HumanIcon;
use App\Services\Level1HumanIcon\Repositories\EloquentLevel1HumanIconRepository;

use App\Services\Baskets\Contracts\BasketsRepository;
use App\Baskets;
use App\Services\Baskets\Repositories\EloquentBasketsRepository;

use App\Services\ProfessionHeaders\Contracts\ProfessionHeadersRepository;
use App\ProfessionHeaders;
use App\Services\ProfessionHeaders\Repositories\EloquentProfessionHeadersRepository;

use App\Services\CareerMapping\Contracts\CareerMappingRepository;
use App\CareerMapping;
use App\Services\CareerMapping\Repositories\EloquentCareerMappingRepository;

use App\Services\Coupons\Contracts\CouponsRepository;
use App\Coupons;
use App\Services\Coupons\Repositories\EloquentCouponsRepository;

use App\Services\LearningStyle\Contracts\LearningStyleRepository;
use App\LearningStyle;
use App\Services\LearningStyle\Repositories\EloquentLearningStyleRepository;

use App\Services\Reports\Contracts\ReportsRepository;
use App\Reports;
use App\Services\Reports\Repositories\EloquentReportsRepository;

use App\Services\Community\Contracts\CommunityRepository;
use App\Community;
use App\Services\Community\Repositories\EloquentCommunityRepository;

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

        $this->app->bind(SchoolsRepository::class, function () {
            return new EloquentSchoolsRepository(new Schools());
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

        $this->app->bind(ParentsRepository::class, function () {
            return new EloquentParentsRepository(new Parents());
        });
        
        $this->app->bind(TemplatesRepository::class, function () {
            return new EloquentTemplatesRepository(new Templates());
        });

        $this->app->bind(FileStorageRepository::class, function () {
            return new EloquentFileStorageRepository(new FileStorage());
        });
        
        $this->app->bind(CMSRepository::class, function () {
            return new EloquentCMSRepository(new CMS());
        });

        $this->app->bind(FeedbackQuestionsRepository::class, function () {
            return new EloquentFeedbackQuestionsRepository(new FeedbackQuestions());
        });

        $this->app->bind(ConfigurationsRepository::class, function () {
            return new EloquentConfigurationsRepository(new Configurations());
        });

        $this->app->bind(GenericadsRepository::class, function () {
            return new EloquentGenericadsRepository(new Genericads());
        });

        $this->app->bind(CoinRepository::class, function () {
            return new EloquentCoinRepository(new Coins());
        });

        $this->app->bind(Level1CartoonIconRepository::class, function () {
            return new EloquentLevel1CartoonIconRepository(new Level1CartoonIcon());
        });

        $this->app->bind(Level1HumanIconRepository::class, function () {
            return new EloquentLevel1HumanIconRepository(new Level1HumanIcon());
        });

        $this->app->bind(BasketsRepository::class, function () {
            return new EloquentBasketsRepository(new Baskets());
        });

        $this->app->bind(ProfessionHeadersRepository::class, function () {
            return new EloquentProfessionHeadersRepository(new ProfessionHeaders());
        });

        $this->app->bind(CareerMappingRepository::class, function () {
            return new EloquentCareerMappingRepository(new CareerMapping());
        });

        $this->app->bind(CouponsRepository::class, function () {
            return new EloquentCouponsRepository(new Coupons());
        });

        $this->app->bind(LearningStyleRepository::class, function () {
            return new EloquentLearningStyleRepository(new LearningStyle());
        });

        $this->app->bind(ReportsRepository::class, function () {
            return new EloquentReportsRepository(new Reports());
        });

        $this->app->bind(CommunityRepository::class, function () {
            return new EloquentCommunityRepository(new Community());
        });
    }
}
