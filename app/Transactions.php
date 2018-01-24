<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use Config;

class Transactions extends Model
{

    protected $table = 'pro_tn_transactions';
    protected $guarded = [];

    public function saveTransation($transactionDetail) {
        if (isset($transactionDetail['id']) && $transactionDetail['id'] > 0) {
            $response = $this->where('id', $transactionDetail['id'])->update($transactionDetail);
        } else {
            $response = $this->create($transactionDetail);
        }

        return $response;
    }

    public function getTransactionsDetail($id, $type, $slot = '') {
        if ($slot > 0) {
            $slot = $slot * Config::get('constant.RECORD_PER_PAGE');
        }
        $transaction = DB::table(config::get('databaseconstants.TBL_TEENAGER_TRANSACTION') . " AS trans")
                ->leftjoin(config::get('databaseconstants.TBL_TEENAGERS') . " AS teen", 'teen.id', '=', 'trans.tn_userid')
                ->selectRaw('trans.* , tn_email, t_name')
                ->where('trans.tn_userid', $id)
                ->where('trans.tn_user_type', $type)
                ->orderBy('trans.id','desc')
                ->skip($slot)
                ->take(Config::get('constant.RECORD_PER_PAGE'))
                ->get();
        return $transaction;
    }

    public function getTransactionsDetailForAdmin($type) {
        $transaction = DB::table(config::get('databaseconstants.TBL_TEENAGER_TRANSACTION') . " AS trans")
                ->leftjoin(config::get('databaseconstants.TBL_TEENAGERS') . " AS teen", 'teen.id', '=', 'trans.tn_userid')
                ->selectRaw('trans.* , tn_email, t_name')
                ->where('trans.tn_user_type',$type)
                ->get();

        return $transaction;
    }

}
