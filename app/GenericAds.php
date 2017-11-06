<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Config;
use DB;

class GenericAds extends Model 
{

    protected $table = 'pro_ga_generic_ads';
    protected $fillable = ['id','ga_name', 'ga_apply_level','ga_image','ga_start_date','ga_end_date','deleted'];

    public function getGenericAds()
    {
        $ads = DB::table('pro_ga_generic_ads')
                              ->selectRaw('pro_ga_generic_ads.id,pro_ga_generic_ads.ga_image')
                              ->whereRaw('pro_ga_generic_ads.deleted = 1')
                              ->whereRaw('pro_ga_generic_ads.ga_start_date <= "'.date('Y-m-d').'"')
                              ->whereRaw('pro_ga_generic_ads.ga_end_date >= "'.date('Y-m-d').'"')
                              ->get();
        return $ads; 
       
    }
    
    
}


