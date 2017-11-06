<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Config;
use DB;

class ProfileViewDeductedCoins extends Model {

    protected $table = 'pro_pdc_profileview_deducted_coins';
//    protected $fillable = ['id', 'pdc_user_id', 'pdc_other_user_id', 'pdc_profession_id', 'pdc_component_name', 'pdc_total_coins', 'pdc_deducted_date', 'created_at','updated_at','deleted'];
    protected $guarded = [];
    
    public function saveDeductedCoinsDetail($coinDetail) {
        $return = DB::table(config::get('databaseconstants.TBL_PROFILEVIEW_DEDUCTED_COINS'))->insert($coinDetail);
        return $return;
    }
}