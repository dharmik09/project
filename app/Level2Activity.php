<?php

namespace App;
use DB;
use Config;
use Illuminate\Database\Eloquent\Model;

class Level2Activity extends Model
{
    protected $table = 'pro_l2ac_level2_activities';
    protected $guarded = [];
    
    public function getActiveLevel2Activity($id)
    {
        $level2activities = DB::select( DB::raw("SELECT
                                              activity.*,apptitude.apt_name,mi.mit_name,interest.it_name,personality.pt_name ,GROUP_CONCAT(options.l2op_option) AS l2op_option,GROUP_CONCAT(options.l2op_fraction) AS l2op_fraction 
                                          FROM " . config::get('databaseconstants.TBL_LEVEL2_ACTIVITY') . " AS activity join " . config::get('databaseconstants.TBL_LEVEL2_OPTIONS') ." AS options on activity.id = options.l2op_activity 
                                            left  join ".config::get('databaseconstants.TBL_LEVEL2_APPTITUDE')." As apptitude on apptitude.id = activity.l2ac_apptitude_type
                                            left  join ".config::get('databaseconstants.TBL_LEVEL2_MI')." As mi on mi.id = activity.l2ac_mi_type
                                            left  join ".config::get('databaseconstants.TBL_LEVEL2_INTEREST')." As interest on interest.id = activity.l2ac_interest
                                            left  join ".config::get('databaseconstants.TBL_LEVEL2_PERSONALITY')." As personality on personality.id = activity.l2ac_personality_type
                                                where activity.id = ".$id." group by activity.id"));

        return $level2activities;
    }

}
