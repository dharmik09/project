<?php

namespace App\Services\Level1Activity\Repositories;

use DB;
use Config;
use App\Level1Answers;
use App\Level1Options;
use App\Services\Level1Activity\Contracts\Level1ActivitiesRepository;
use App\Services\Repositories\Eloquent\EloquentBaseRepository;

class EloquentLevel1ActivitiesRepository extends EloquentBaseRepository implements Level1ActivitiesRepository {

    public function getLevel1AllActiveQuestion() {
        $result = DB::table(config::get('databaseconstants.TBL_LEVEL1_ACTIVITY'))->where("deleted", 1)->get();
        return $result;
    }

}
