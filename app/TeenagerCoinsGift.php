<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Config;
use DB;

class TeenagerCoinsGift extends Model {

    protected $table = 'pro_tcg_teenager_coins_gift';
    protected $fillable = [];

    public function saveTeenagetGiftCoinsDetail($coinDetail) {
        $data = DB::table(config::get('databaseconstants.TBL_TEENAGER_GIFT_COINS'))->where('tcg_sender_id', $coinDetail['tcg_sender_id'])->where('tcg_reciver_id', $coinDetail['tcg_reciver_id'])->where('deleted', '1')->first();
        $return = DB::table(config::get('databaseconstants.TBL_TEENAGER_GIFT_COINS'))->insert($coinDetail);
        /*if (count($data) > 0) {
            $return = DB::table(config::get('databaseconstants.TBL_TEENAGER_GIFT_COINS'))->where('tcg_sender_id', $coinDetail['tcg_sender_id'])->where('tcg_reciver_id', $coinDetail['tcg_reciver_id'])->update($coinDetail);
        } else {
            $return = DB::table(config::get('databaseconstants.TBL_TEENAGER_GIFT_COINS'))->insert($coinDetail);
        }*/
        return $return;
    }
    public function getTeenagerCoinsGiftDetail($id, $type) {
        $coinsDetail = DB::table(config::get('databaseconstants.TBL_TEENAGER_GIFT_COINS') . " AS g_coins")
                ->leftjoin(config::get('databaseconstants.TBL_TEENAGERS') . " AS teen", 'g_coins.tcg_reciver_id', '=', 'teen.id')
                ->selectRaw('g_coins.* , teen.t_name, teen.t_email')
                ->where('g_coins.tcg_sender_id',$id)
                ->where('g_coins.tcg_user_type',$type)
                ->orderBy('g_coins.id','desc')
                ->paginate(Config::get('constant.RECORD_PER_PAGE'));

        return $coinsDetail;
    }
    public function getTeenagerCoinsGiftDetailName($id, $type, $searchData) {
        $whereArray = [];
        foreach ($searchData AS $key => $value) {
            $whereArray[] = " teen.t_name LIKE '%" . $value . "%'";
        }
         if (!empty($whereArray)) {
            $whereStr = implode(" OR ", $whereArray);
        }
        $coinsDetail = DB::table(config::get('databaseconstants.TBL_TEENAGER_GIFT_COINS') . " AS g_coins")
                ->leftjoin(config::get('databaseconstants.TBL_TEENAGERS') . " AS teen", 'g_coins.tcg_reciver_id', '=', 'teen.id')
                ->selectRaw('g_coins.* , teen.t_name, teen.t_email')
                ->where('g_coins.tcg_sender_id',$id)
                ->where('g_coins.tcg_user_type',$type)
                //->where('teen.t_name','like', '%' . $searchKeyword . '%')
                ->where(function ($query) use ($whereStr) {
                    $query->whereRaw($whereStr);
                })
                ->orderBy('g_coins.id','desc')
                ->paginate(Config::get('constant.RECORD_PER_PAGE'));

        return $coinsDetail;
    }
    public function getTeenagerCoinsGiftDetailHistory($id,$type,$slot) {
        if ($slot > 0) {
            $slot = $slot * config::get('constant.RECORD_PER_PAGE');
        }
        $coinsDetail = DB::table(config::get('databaseconstants.TBL_TEENAGER_GIFT_COINS') . " AS g_coins")
                ->leftjoin(config::get('databaseconstants.TBL_TEENAGERS') . " AS teen", 'g_coins.tcg_reciver_id', '=', 'teen.id')
                ->selectRaw('g_coins.* , teen.t_name, teen.t_email')
                ->where('g_coins.tcg_sender_id',$id)
                ->where('g_coins.tcg_user_type',$type)
                ->orderBy('g_coins.id','desc')
                ->skip($slot)
                ->take(config::get('constant.RECORD_PER_PAGE'))
                ->get();

        return $coinsDetail;
    }

    public function searchTeenagerCoinsGiftDetailHistory($id,$type,$slot,$searchData) {
        if ($slot > 0) {
            $slot = $slot * config::get('constant.RECORD_PER_PAGE');
        }
        $whereArray = [];
        foreach ($searchData AS $key => $value) {
            $whereArray[] = " t_name LIKE '%" . $value . "%'";
            $whereArray[] = " t_email LIKE '%" . $value . "%'";
        }
         if (!empty($whereArray)) {
            $whereStr = implode(" OR ", $whereArray);
        }
        $coinsDetail = DB::table(config::get('databaseconstants.TBL_TEENAGER_GIFT_COINS') . " AS g_coins")
                ->leftjoin(config::get('databaseconstants.TBL_TEENAGERS') . " AS teen", 'g_coins.tcg_reciver_id', '=', 'teen.id')
                ->selectRaw('g_coins.* , teen.t_name, teen.t_email')
                ->where('g_coins.tcg_sender_id',$id)
                ->where('g_coins.tcg_user_type',$type)
                //->where('teen.t_name','like', '%' . $search . '%')
                ->where(function ($query) use ($whereStr) {
                    $query->whereRaw($whereStr);
                })
                ->orderBy('g_coins.id','desc')
                ->skip($slot)
                ->take(config::get('constant.RECORD_PER_PAGE'))
                ->get();

        return $coinsDetail;
    }

    public function getTeenagerCoinsGiftDetailForParent($id,$type) {
        $coinsDetail = DB::table(config::get('databaseconstants.TBL_PARENT_TEEN_PAIR') . " AS parent")
                ->leftjoin(config::get('databaseconstants.TBL_TEENAGERS') . " AS teen", 'parent.ptp_teenager', '=', 'teen.id')
                ->leftjoin(config::get('databaseconstants.TBL_TEENAGER_GIFT_COINS') . " AS g_coins", function($join) use ($id)
                {
                    $join->on('g_coins.tcg_reciver_id', '=', 'teen.id')
                         ->where('g_coins.tcg_user_type','=', 2)
                         ->where('g_coins.tcg_sender_id', '=', $id);
                })
                ->selectRaw('g_coins.* , teen.t_name, teen.t_email')
                //->where('g_coins.tcg_sender_id',$id)
                ->where('parent.ptp_parent_id',$id)
                ->orderBy('g_coins.id','desc')
                ->paginate(Config::get('constant.RECORD_PER_PAGE'));

        return $coinsDetail;
    }

    public function getTeenagerCoinsGiftDetailNameForParent($id,$type,$searchData) {
        $whereArray = [];
        foreach ($searchData AS $key => $value) {
            $whereArray[] = " teen.t_name LIKE '%" . $value . "%'";
        }
         if (!empty($whereArray)) {
            $whereStr = implode(" OR ", $whereArray);
        }
        $coinsDetail = DB::table(config::get('databaseconstants.TBL_PARENT_TEEN_PAIR') . " AS parent")
                ->leftjoin(config::get('databaseconstants.TBL_TEENAGERS') . " AS teen", 'parent.ptp_teenager', '=', 'teen.id')
                ->leftjoin(config::get('databaseconstants.TBL_TEENAGER_GIFT_COINS') . " AS g_coins", function($join) use ($id)
                {
                    $join->on('g_coins.tcg_reciver_id', '=', 'teen.id')
                         ->where('g_coins.tcg_user_type','=', 2)
                         ->where('g_coins.tcg_sender_id', '=', $id);
                })
                ->selectRaw('g_coins.* , teen.t_name, teen.t_email')
                //->where('g_coins.tcg_sender_id',$id)
                ->where('parent.ptp_parent_id',$id)
                ->where(function ($query) use ($whereStr) {
                    $query->whereRaw($whereStr);
                })
                ->orderBy('g_coins.id','desc')
                ->paginate(Config::get('constant.RECORD_PER_PAGE'));

        return $coinsDetail;
    }

    public function getAllTeenagerCoinsGiftDetail($id,$type) {
        $coinsDetail = DB::table(config::get('databaseconstants.TBL_TEENAGER_GIFT_COINS') . " AS g_coins")
                ->leftjoin(config::get('databaseconstants.TBL_TEENAGERS') . " AS teen", 'g_coins.tcg_reciver_id', '=', 'teen.id')
                ->selectRaw('g_coins.* , teen.t_name, teen.t_email')
                ->where('g_coins.tcg_sender_id',$id)
                ->where('g_coins.tcg_user_type',$type)
                ->orderBy('g_coins.id','desc')
                ->get();

        return $coinsDetail;
    }

    public function updateExpiredCoinsField($userid,$type) {
        $userDetail = DB::table(config::get('databaseconstants.TBL_TEENAGER_GIFT_COINS'))->where('tcg_sender_id', 0)->where('tcg_user_type', $type)->where('tcg_reciver_id', $userid)->update(['tcg_coins_expired' => 1]);
        return $userDetail;
    }

    public function getAllTeenagerCoinsGiftDetailByAdmin($userId,$id,$type)
    {
        $coinsDetail = DB::table(config::get('databaseconstants.TBL_TEENAGER_GIFT_COINS') . " AS g_coins")
                ->leftjoin(config::get('databaseconstants.TBL_TEENAGERS') . " AS teen", 'g_coins.tcg_reciver_id', '=', 'teen.id')
                ->selectRaw('g_coins.* , teen.t_name, teen.t_email')
                ->where('g_coins.tcg_reciver_id',$userId)
                ->where('g_coins.tcg_sender_id',$id)
                ->where('g_coins.tcg_user_type',$type)
                ->where('tcg_coins_expired',0)
                ->orderBy('g_coins.id','desc')
                ->first();

        return $coinsDetail;
    }

    public function getAllTeenagerCoinsGiftDetailByAdminGifted($id,$type) {
        $coinsDetail = DB::table(config::get('databaseconstants.TBL_TEENAGER_GIFT_COINS') . " AS g_coins")
                ->leftjoin(config::get('databaseconstants.TBL_TEENAGERS') . " AS teen", 'g_coins.tcg_reciver_id', '=', 'teen.id')
                ->selectRaw('g_coins.* , teen.t_name, teen.t_email')
                ->where('g_coins.tcg_sender_id',$id)
                ->where('g_coins.tcg_user_type',$type)
                ->where('tcg_coins_expired',0)
                ->orderBy('g_coins.id','desc')
                ->get();

        return $coinsDetail;
    }
}