<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Config;
use DB;

class PurchasedCoins extends Model {

    protected $table = 'pro_pc_purchased_coins';
//    protected $fillable = ['id', 'pc_user_id', 'pc_purchased_date','pc_total_coins', 'pc_total_price','pc_user_type','created_at','updated_at','deleted'];
    protected $guarded = [];
    
    public function savePurchasedCoinsDetail($coinDetail) {
        $return = DB::table(config::get('databaseconstants.TBL_PURCHASED_COINS'))->insert($coinDetail);
        return $return;
    }

    public function getPurchasedCoinsDetailByUser($teenId, $userType) {
        $result = DB::table(config::get('databaseconstants.TBL_PURCHASED_COINS'))
                    ->selectRaw('*')
                    ->where('pc_user_id',$teenId)
                    ->where('pc_user_type', $userType)
                    ->where('deleted','1')
                    ->get();
        return $result;
    }
}
