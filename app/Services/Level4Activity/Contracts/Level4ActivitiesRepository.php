<?php

namespace App\Services\Level4Activity\Contracts;
use App\Services\Repositories\BaseRepository;
use App\Services\Level4Activity\Entities\Level4Activity;

interface Level4ActivitiesRepository extends BaseRepository
{    
    public function getAllLevel4AdvanceActivity();
}
