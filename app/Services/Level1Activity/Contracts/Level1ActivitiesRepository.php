<?php

namespace App\Services\Level1Activity\Contracts;
use App\Services\Repositories\BaseRepository;
use App\Services\Level1Activity\Entities\Level1Activity;
use App\Level1Answers;
use App\Level1Options;

interface Level1ActivitiesRepository extends BaseRepository
{    
    public function getLevel1AllActiveQuestion();
}
