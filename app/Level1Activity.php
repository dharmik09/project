<?php

namespace App;

use DB;
use Config;
use Illuminate\Database\Eloquent\Model;

class Level1Activity extends Model
{
    protected $table = 'pro_l1ac_level1_activities';
    protected $guarded = [];

    public function getActiveLevel1Activity($id)
    {
        $level1activities = DB::select( DB::raw("SELECT
                                activity.* , GROUP_CONCAT(options.l1op_option) AS l1op_option , GROUP_CONCAT(options.l1op_fraction) AS l1op_fraction
                                FROM " . config::get('databaseconstants.TBL_LEVEL1_ACTIVITY') . " AS activity join " . config::get('databaseconstants.TBL_LEVEL1_OPTIONS') ." AS options on activity.id = options.l1op_activity
                                where activity.id = ".$id." group by  activity.id "
                            ));
        return $level1activities;
    }

    public function options() {
        return $this->hasMany(Level1Options::class, 'l1op_activity');
    }

    public function questionOptions($questionId) {
        return $this->where('id', $questionId)->with('options')->get();
    }
    
    public function getNoOfTotalQuestionsAttemptedQuestion($teenagerId) {
        $result = DB::select(DB::raw("select (SELECT count(*) FROM " . config::get('databaseconstants.TBL_LEVEL1_ACTIVITY') . " where deleted=1) as 'NoOfTotalQuestions', (select count(*) from " . config::get('databaseconstants.TBL_LEVEL1_ANSWERS') . " where l1ans_teenager=" . $teenagerId . ") as 'NoOfAttemptedQuestions' "), array());
        return $result;
    }
    
    public function getTeenAttemptedQualityType($teenagerId) {
        $result = DB::table(config::get('databaseconstants.TBL_TEENAGER_ICON'))->where(['ti_teenager' => $teenagerId , 'deleted' => 1])->groupBy('ti_icon_type')->pluck('ti_icon_type');
        return $result;
    }
}
