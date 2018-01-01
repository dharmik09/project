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

    public function questionA() {
        return $this->hasMany(Level1Options::class, 'l1op_activity');
    }
}
