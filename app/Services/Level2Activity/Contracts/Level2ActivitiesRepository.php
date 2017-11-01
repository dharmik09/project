<?php

namespace App\Services\Level2Activity\Contracts;
use App\Services\Repositories\BaseRepository;
use App\Services\Level2Activity\Entities\Level2Activity;

interface Level2ActivitiesRepository extends BaseRepository
{    
    public function getLevel2AllActiveQuestion();
}
