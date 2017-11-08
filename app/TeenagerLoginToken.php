<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Config;
use DB;

class TeenagerLoginToken extends Model {

    protected $table = 'pro_tlt_teenager_login_token';

//    protected $fillable = ['id','tlt_teenager_id','tlt_login_token','created_at','updated_at','deleted'];
    protected $guarded = [];

    public function saveTeenagerLoginDetail($loginTokenDetail) {

        $data = DB::table(config::get('databaseconstants.TBL_TEENAGER_LOGIN_TOKEN'))->where('tlt_teenager_id', $loginTokenDetail['tlt_teenager_id'])->where('tlt_device_id', $loginTokenDetail['tlt_device_id'])->first();
        if (count($data) > 0) {
            $loginTokenDetail['deleted'] = 1;
            $return = DB::table(config::get('databaseconstants.TBL_TEENAGER_LOGIN_TOKEN'))->where('tlt_teenager_id', $loginTokenDetail['tlt_teenager_id'])->where('tlt_device_id', $loginTokenDetail['tlt_device_id'])->update($loginTokenDetail);
        } else {
            $return = DB::table(config::get('databaseconstants.TBL_TEENAGER_LOGIN_TOKEN'))->insert($loginTokenDetail);
        }
        return $return;
    }

    public function validateAccessToken($teenId,$token) {
        $token = $this->where('deleted', '1')->where('tlt_teenager_id', $teenId)->where('tlt_login_token',$token)->get();
        if (count($token) > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function deletedTeenagerLoginDetail($teenId,$deviceId) {
        $token = DB::table(config::get('databaseconstants.TBL_TEENAGER_LOGIN_TOKEN'))->where('tlt_teenager_id', $teenId)->where('tlt_device_id', $deviceId)->update(['deleted'=> 3]);
        return $token;
    }

    public function updateTeenagerLoginDetail($teenId,$deviceId) {

        $data = DB::table(config::get('databaseconstants.TBL_TEENAGER_LOGIN_TOKEN'))->where('tlt_teenager_id', $teenId)->where('tlt_device_id', $deviceId)->first();
        if (count($data) > 0) {
            $token = DB::table(config::get('databaseconstants.TBL_TEENAGER_LOGIN_TOKEN'))->where('tlt_teenager_id', $teenId)->where('tlt_device_id', $deviceId)->update(['deleted'=> 1]);
            return true;
        } else {
            return false;
        }
    }
}