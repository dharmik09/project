<?php

namespace App\Services\Level4Activity\Repositories;

use DB;
use Helpers;
use Config;
use App\Level4Answers;
use App\Level4Options;
use App\Services\Level4Activity\Contracts\Level4ActivitiesRepository;
use App\Services\Repositories\Eloquent\EloquentBaseRepository;
use App\Level4ParentAnswers;

class EloquentLevel4ActivitiesRepository extends EloquentBaseRepository implements Level4ActivitiesRepository {

    /**
     * @return level4activity details object
      Parameters
      @$hintDetail : Array of level4 advance activity detail from front
     */
    public function getAllLevel4AdvanceActivity() {

        $result = DB::table(config::get('databaseconstants.TBL_LEVEL4_ADVANCE_ACTIVITY'))
                ->select('*')
                ->whereIn('deleted', ['1' , '2'])
                ->get();

        return $result;
    }
}
