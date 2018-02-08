<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Config;
use DB;

class SponsorsActivity extends Model 
{

    protected $table = 'pro_sp_sponsor';

    protected $guarded = [];

    //Get activity details by activity type
    public function getActivityByTypeAndSponsor($sponsorArr, $activityType) {
	    $ads = DB::table('pro_sa_sponsor_activity')
	              ->join(Config::get('databaseconstants.TBL_SPONSORS') . " AS sponsor", 'pro_sa_sponsor_activity.sa_sponsor_id', '=', 'sponsor.id')
	              ->selectRaw('pro_sa_sponsor_activity.*, sponsor.sp_company_name')
	              ->where('pro_sa_sponsor_activity.deleted', 1)
	              ->where('pro_sa_sponsor_activity.sa_start_date', '<=', date('Y-m-d'))
	              ->where('pro_sa_sponsor_activity.sa_end_date', '>=', date('Y-m-d'))
	              ->where('pro_sa_sponsor_activity.sa_type', $activityType)
	              ->whereIn('pro_sa_sponsor_activity.sa_sponsor_id', $sponsorArr)
	              ->get();
        return $ads;  
    }
}


