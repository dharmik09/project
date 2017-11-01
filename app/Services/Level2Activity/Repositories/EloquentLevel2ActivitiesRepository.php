<?php

namespace App\Services\Level2Activity\Repositories;

use DB;
use Config;
use App\Level2Answers;
use App\Level2Options;
use App\Services\Level2Activity\Contracts\Level2ActivitiesRepository;
use App\Services\Repositories\Eloquent\EloquentBaseRepository;

class EloquentLevel2ActivitiesRepository extends EloquentBaseRepository implements Level2ActivitiesRepository {

    public function getLevel2AllActiveQuestion() {
        $result = DB::table(config::get('databaseconstants.TBL_LEVEL2_ACTIVITY'))->where("deleted", 1)->get();
        return $result;
    }

}
