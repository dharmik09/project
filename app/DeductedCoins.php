<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Config;
use DB;

class DeductedCoins extends Model  {


    protected $table = 'pro_dc_deducted_coins';
//    protected $fillable = ['id', 'dc_user_id', 'dc_user_type', 'dc_component_name', 'dc_profession_id', 'dc_total_coins', 'dc_start_date', 'dc_end_date', 'dc_days', 'created_at','updated_at','deleted'];
    protected $guarded = [];
    
    public function saveDeductedCoinsDetail($coinDetail) {
        $return = DB::table(config::get('databaseconstants.TBL_DEDUCTED_COINS'))->insert($coinDetail);
        return $return;
    }

    public function getDeductedCoinsDetail($id,$type) {
        $deductedDetail = DB::table(config::get('databaseconstants.TBL_DEDUCTED_COINS') . " AS d_coins")
                ->leftjoin(config::get('databaseconstants.TBL_PAID_COMPONENTS') . " AS paid", 'paid.id', '=', 'd_coins.dc_component_name')
                ->leftjoin(config::get('databaseconstants.TBL_PROFESSIONS') . " AS pro", 'pro.id', '=', 'd_coins.dc_profession_id')
                ->selectRaw('d_coins.* , paid.pc_element_name, pro.pf_name')
                ->where('d_coins.dc_user_id',$id)
                ->where('d_coins.dc_total_coins','!=',0)
                ->where('d_coins.dc_user_type',$type)
                ->orderBy('d_coins.id','desc')
                ->paginate(Config::get('constant.RECORD_PER_PAGE'));

        return $deductedDetail;
    }

    public function getDeductedCoinsDetailById($teenId, $comType, $type, $proId) {
        $deductedDetail = DB::table(config::get('databaseconstants.TBL_DEDUCTED_COINS') . " AS d_coins")
                    ->selectRaw('d_coins.*')
                    ->where('d_coins.dc_user_id',$teenId)
                    ->where('d_coins.dc_user_type',$type)
                    ->where('d_coins.dc_profession_id',$proId)
                    ->where('d_coins.dc_component_name',$comType)
                    ->orderBy('d_coins.dc_start_date','desc')
                    ->get();

        return $deductedDetail;
    }

    public function getDeductedCoinsDetailByIdForLS($teenId, $comType, $type) {
        $deductedDetail = DB::table(config::get('databaseconstants.TBL_DEDUCTED_COINS') . " AS d_coins")
                    ->selectRaw('d_coins.*')
                    ->where('d_coins.dc_user_id',$teenId)
                    ->where('d_coins.dc_user_type',$type)
                    ->where('d_coins.dc_component_name',$comType)
                    ->orderBy('d_coins.dc_start_date','desc')
                    ->get();

        return $deductedDetail;
    }

    public function getDeductedCoinsDetailByProfession($id,$type,$profession) {
        $deductedDetail = DB::table(config::get('databaseconstants.TBL_DEDUCTED_COINS') . " AS d_coins")
                ->leftjoin(config::get('databaseconstants.TBL_PAID_COMPONENTS') . " AS paid", 'paid.id', '=', 'd_coins.dc_component_name')
                ->leftjoin(config::get('databaseconstants.TBL_PROFESSIONS') . " AS pro", 'pro.id', '=', 'd_coins.dc_profession_id')
                ->selectRaw('d_coins.* , paid.pc_element_name, pro.pf_name')
                ->where('d_coins.dc_user_id',$id)
                ->where('d_coins.dc_user_type',$type)
                ->where('pro.pf_name','like', '%' . $profession . '%')
                ->where('paid.pc_element_name',Config::get('constant.PROMISE_PLUS'))
                ->orderBy('d_coins.id','desc')
                ->paginate(Config::get('constant.RECORD_PER_PAGE'));

        return $deductedDetail;
    }

    public function getDeductedCoinsHistory($id,$type,$slot) {
        if ($slot > 0) {
            $slot = $slot * config::get('constant.RECORD_PER_PAGE');
        }
        $deductedDetail = DB::table(config::get('databaseconstants.TBL_DEDUCTED_COINS') . " AS d_coins")
                ->leftjoin(config::get('databaseconstants.TBL_PAID_COMPONENTS') . " AS paid", 'paid.id', '=', 'd_coins.dc_component_name')
                ->leftjoin(config::get('databaseconstants.TBL_PROFESSIONS') . " AS pro", 'pro.id', '=', 'd_coins.dc_profession_id')
                ->selectRaw('d_coins.* , paid.pc_element_name, pro.pf_name')
                ->where('d_coins.dc_user_id',$id)
                ->where('d_coins.dc_user_type',$type)
                ->where('d_coins.dc_total_coins','!=',0)
                ->where('paid.pc_element_name',Config::get('constant.PROMISE_PLUS'))
                ->orderBy('d_coins.id','desc')
                ->skip($slot)
                ->take(config::get('constant.RECORD_PER_PAGE'))
                ->get();

        return $deductedDetail;
    }

    public function getDeductedCoinsDetailForPSHistory($id, $type, $slot = '') {
        if ($slot > 0) {
            $slot = $slot * Config::get('constant.RECORD_PER_PAGE');
        }
        $deductedDetail = DB::table(config::get('databaseconstants.TBL_DEDUCTED_COINS') . " AS d_coins")
                ->leftjoin(config::get('databaseconstants.TBL_PAID_COMPONENTS') . " AS paid", 'paid.id', '=', 'd_coins.dc_component_name')
                ->leftjoin(config::get('databaseconstants.TBL_PROFESSIONS') . " AS pro", 'pro.id', '=', 'd_coins.dc_profession_id')
                ->selectRaw('d_coins.* , paid.pc_element_name, pro.pf_name')
                ->where('d_coins.dc_user_id',$id)
                ->where('d_coins.dc_total_coins','!=',0)
                ->where('d_coins.dc_user_type',$type)
                ->where('paid.pc_element_name',Config::get('constant.PROMISE_PLUS'))
                ->orderBy('d_coins.id','desc')
                ->skip($slot)
                ->take(Config::get('constant.RECORD_PER_PAGE'))
                ->get();

        return $deductedDetail;
    }

    public function getDeductedCoinsDetailForPS($id, $type, $searchText = '') {
        $deductedDetail = DB::table(config::get('databaseconstants.TBL_DEDUCTED_COINS') . " AS d_coins")
                ->leftjoin(config::get('databaseconstants.TBL_PAID_COMPONENTS') . " AS paid", 'paid.id', '=', 'd_coins.dc_component_name')
                ->leftjoin(config::get('databaseconstants.TBL_PROFESSIONS') . " AS pro", 'pro.id', '=', 'd_coins.dc_profession_id')
                ->selectRaw('d_coins.* , paid.pc_element_name, pro.pf_name')
                ->where('d_coins.dc_user_id', $id)
                ->where('d_coins.dc_total_coins', '!=', 0)
                ->where('d_coins.dc_user_type', $type)
                ->where('paid.pc_element_name', Config::get('constant.PROMISE_PLUS'))
                ->where('pro.pf_name', 'like', '%' . $searchText . '%') 
                ->orderBy('d_coins.id','desc')
                ->paginate(10);

        return $deductedDetail;
    }

    public function getDeductedCoinsDetailForLS($id, $type, $searchText = '') {
        $deductedDetail = DB::table(config::get('databaseconstants.TBL_DEDUCTED_COINS') . " AS d_coins")
                ->leftjoin(config::get('databaseconstants.TBL_PAID_COMPONENTS') . " AS paid", 'paid.id', '=', 'd_coins.dc_component_name')
                ->leftjoin(config::get('databaseconstants.TBL_PROFESSIONS') . " AS pro", 'pro.id', '=', 'd_coins.dc_profession_id')
                ->selectRaw('d_coins.* , paid.pc_element_name, pro.pf_name')
                ->where('d_coins.dc_user_id', $id)
                ->where('d_coins.dc_total_coins', '!=', 0)
                ->where('d_coins.dc_user_type', $type)
                ->where('paid.pc_element_name', Config::get('constant.LEARNING_STYLE'))
                ->orderBy('d_coins.id','desc')
                ->paginate(10);

        return $deductedDetail;
    }

    public function getDeductedCoinsHistorySearch($id,$type,$slot,$searchData) {
        if ($slot > 0) {
            $slot = $slot * config::get('constant.RECORD_PER_PAGE');
        }
        $deductedDetail = DB::table(Config::get('databaseconstants.TBL_DEDUCTED_COINS') . " AS d_coins")
                ->leftjoin(config::get('databaseconstants.TBL_PAID_COMPONENTS') . " AS paid", 'paid.id', '=', 'd_coins.dc_component_name')
                ->leftjoin(config::get('databaseconstants.TBL_PROFESSIONS') . " AS pro", 'pro.id', '=', 'd_coins.dc_profession_id')
                ->selectRaw('d_coins.* , paid.pc_element_name, pro.pf_name')
                ->where('d_coins.dc_user_id',$id)
                ->where('d_coins.dc_user_type',$type)
                ->where('d_coins.dc_total_coins','!=',0)
                ->where('pro.pf_name', 'like', '%' . $searchData . '%')
                ->where('paid.pc_element_name',Config::get('constant.PROMISE_PLUS'))
                ->orderBy('d_coins.id','desc')
                ->skip($slot)
                ->take(config::get('constant.RECORD_PER_PAGE'))
                ->get();

        return $deductedDetail;
    }

    public function getDeductedCoinsDetailForLSHistory($id, $type, $slot = '') {
        if ($slot > 0) {
            $slot = $slot * Config::get('constant.RECORD_PER_PAGE');
        }
        $deductedDetail = DB::table(config::get('databaseconstants.TBL_DEDUCTED_COINS') . " AS d_coins")
                ->leftjoin(config::get('databaseconstants.TBL_PAID_COMPONENTS') . " AS paid", 'paid.id', '=', 'd_coins.dc_component_name')
                ->leftjoin(config::get('databaseconstants.TBL_PROFESSIONS') . " AS pro", 'pro.id', '=', 'd_coins.dc_profession_id')
                ->selectRaw('d_coins.* , paid.pc_element_name, pro.pf_name')
                ->where('d_coins.dc_user_id',$id)
                ->where('d_coins.dc_total_coins','!=',0)
                ->where('d_coins.dc_user_type',$type)
                ->where('paid.pc_element_name',Config::get('constant.LEARNING_STYLE'))
                ->orderBy('d_coins.id','desc')
                ->skip($slot)
                ->take(Config::get('constant.RECORD_PER_PAGE'))
                ->get();

        return $deductedDetail;
    }

    public function getAllDeductedCoinsDetail($id,$type) {
        $deductedDetail = DB::table(config::get('databaseconstants.TBL_DEDUCTED_COINS') . " AS d_coins")
                ->leftjoin(config::get('databaseconstants.TBL_PAID_COMPONENTS') . " AS paid", 'paid.id', '=', 'd_coins.dc_component_name')
                ->leftjoin(config::get('databaseconstants.TBL_PROFESSIONS') . " AS pro", 'pro.id', '=', 'd_coins.dc_profession_id')
                ->selectRaw('d_coins.* , paid.pc_element_name, pro.pf_name')
                ->where('d_coins.dc_user_id',$id)
                ->where('d_coins.dc_total_coins','!=',0)
                ->where('d_coins.dc_user_type',$type)
                ->get();

        return $deductedDetail;
    }
}
