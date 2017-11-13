<?php

namespace App\Services\Reports\Repositories;

use DB;
use Auth;
use Config;
use App\Services\Reports\Contracts\ReportsRepository;
use App\Services\Repositories\Eloquent\EloquentBaseRepository;
use Helpers;

class EloquentReportsRepository extends EloquentBaseRepository implements ReportsRepository {

    /**
     * @return array of all the active teenagers
      Parameters
      @$searchParamArray : Array of Searching and Sorting parameters
     */
    public function getAlllevel1data() {
        
        $level1activities = DB::table(config::get('databaseconstants.TBL_LEVEL1_ANSWERS'). " AS answer ")
                              ->join(config::get('databaseconstants.TBL_LEVEL1_ACTIVITY'). " AS activity", 'answer.l1ans_activity', '=', 'activity.id')
                              ->join(config::get('databaseconstants.TBL_LEVEL1_OPTIONS') . " AS options ", 'answer.l1ans_answer', '=', 'options.id')
                               ->join(config::get('databaseconstants.TBL_TEENAGERS'). " AS teenager", 'answer.l1ans_teenager', '=', 'teenager.id')
                              ->selectRaw('activity.* , answer.* , options.*,teenager.t_gender')                               
                              ->get();
       //echo "<pre>"; print_r($level1activities); die;
        return $level1activities;
       
}
   
}

