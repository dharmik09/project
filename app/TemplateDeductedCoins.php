<?php

namespace App;
use Illuminate\Database\Eloquent\Model;
use Config;
use DB;

class TemplateDeductedCoins extends Model {

    protected $table = 'pro_tdc_template_deducted_coins';
//    protected $fillable = ['id', 'tdc_user_id', 'tdc_profession_id', 'tdc_template_id', 'tdc_total_coins', 'tdc_start_date', 'tdc_end_date', 'created_at','updated_at','deleted'];
    protected $guarded = [];

    public function saveDeductedCoinsDetail($coinDetail) {
        $return = DB::table(config::get('databaseconstants.TBL_TEMPLATE_DEDUCTED_COINS'))->insert($coinDetail);
        return $return;
    }

    public function getDeductedCoinsDetailById($userid,$proId, $id, $type) {
        $deductedDetail = DB::table(config::get('databaseconstants.TBL_TEMPLATE_DEDUCTED_COINS'))
                    ->selectRaw('*')
                    ->where('tdc_user_id',$userid)
                    ->where('tdc_profession_id',$proId)
                    ->where('tdc_template_id',$id)
                    ->where('tdc_total_coins','!=',0)
                    ->where('tdc_user_type',$type)
                    ->orderBy('tdc_start_date','desc')
                    ->get();

        return $deductedDetail;
    }

    public function getDeductedCoinsDetail($userid,$type) {
        $deductedDetail = DB::table(config::get('databaseconstants.TBL_TEMPLATE_DEDUCTED_COINS'). " AS d_coins")
                    ->leftjoin(config::get('databaseconstants.TBL_GAMIFICATION_TEMPLATE') . " AS template", 'template.id', '=', 'd_coins.tdc_template_id')
                    ->leftjoin(config::get('databaseconstants.TBL_PROFESSIONS') . " AS pro", 'pro.id', '=', 'd_coins.tdc_profession_id')
                    ->selectRaw('d_coins.* , template.gt_template_title, pro.pf_name')
                    ->where('d_coins.tdc_user_id',$userid)
                    ->where('d_coins.tdc_user_type',$type)
                    ->where('d_coins.tdc_total_coins','!=',0)
                    ->orderBy('d_coins.id','desc')
                    ->paginate(Config::get('constant.RECORD_PER_PAGE'));

        return $deductedDetail;
    }

    public function getDeductedCoinsDetailByProfession($id,$profession, $type) {
        $deductedDetail = DB::table(config::get('databaseconstants.TBL_TEMPLATE_DEDUCTED_COINS'). " AS d_coins")
                    ->leftjoin(config::get('databaseconstants.TBL_GAMIFICATION_TEMPLATE') . " AS template", 'template.id', '=', 'd_coins.tdc_template_id')
                    ->leftjoin(config::get('databaseconstants.TBL_PROFESSIONS') . " AS pro", 'pro.id', '=', 'd_coins.tdc_profession_id')
                    ->selectRaw('d_coins.* , template.gt_template_title, pro.pf_name')
                    ->where('d_coins.tdc_user_id',$id)
                    ->where('d_coins.tdc_user_type',$type)
                    ->where('d_coins.tdc_total_coins','!=',0)
                    ->where('pro.pf_name','like', '%' . $profession . '%')
                    ->orderBy('d_coins.id','desc')
                    ->paginate(Config::get('constant.RECORD_PER_PAGE'));
        return $deductedDetail;
    }

    public function getDeductedCoinsDetailHistory($id,$userType,$slot, $type) {
        if ($slot > 0) {
            $slot = $slot * config::get('constant.RECORD_PER_PAGE');
        }
        $deductedDetail = DB::table(config::get('databaseconstants.TBL_TEMPLATE_DEDUCTED_COINS'). " AS d_coins")
                    ->leftjoin(config::get('databaseconstants.TBL_GAMIFICATION_TEMPLATE') . " AS template", 'template.id', '=', 'd_coins.tdc_template_id')
                    ->leftjoin(config::get('databaseconstants.TBL_PROFESSIONS') . " AS pro", 'pro.id', '=', 'd_coins.tdc_profession_id')
                    ->selectRaw('d_coins.* , template.gt_template_title, pro.pf_name')
                    ->where('d_coins.tdc_user_id',$id)
                    ->where('d_coins.tdc_user_type',$userType)
                    ->where('d_coins.tdc_total_coins','!=',0)
                    ->orderBy('d_coins.id','desc')
                    ->skip($slot)
                    ->take(config::get('constant.RECORD_PER_PAGE'))
                    ->get();

        return $deductedDetail;
    }

    public function getDeductedCoinsDetailHistorySearch($id,$userType,$slot,$searchData, $type) {
        if ($slot > 0) {
            $slot = $slot * config::get('constant.RECORD_PER_PAGE');
        }
        $deductedDetail = DB::table(config::get('databaseconstants.TBL_TEMPLATE_DEDUCTED_COINS'). " AS d_coins")
                    ->leftjoin(config::get('databaseconstants.TBL_GAMIFICATION_TEMPLATE') . " AS template", 'template.id', '=', 'd_coins.tdc_template_id')
                    ->leftjoin(config::get('databaseconstants.TBL_PROFESSIONS') . " AS pro", 'pro.id', '=', 'd_coins.tdc_profession_id')
                    ->selectRaw('d_coins.* , template.gt_template_title, pro.pf_name')
                    ->where('d_coins.tdc_user_id',$id)
                    ->where('d_coins.tdc_user_type',$userType)
                    ->where('d_coins.tdc_total_coins','!=',0)
                    ->where('pro.pf_name', 'like', '%' . $searchData . '%')
                    ->orderBy('d_coins.id','desc')
                    ->skip($slot)
                    ->take(config::get('constant.RECORD_PER_PAGE'))
                    ->get();

        return $deductedDetail;
    }
}
