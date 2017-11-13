<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Config;
use DB;

class DeviceToken extends Model {

    protected $table = 'pro_tdt_teenager_device_token';
//    protected $fillable = ['id', 'tdt_user_id', 'tdt_device_token', 'tdt_device_type', 'tdt_device_id', 'created_at', 'updated_at', 'deleted'];
    protected $guarded = [];
    
    public function saveDeviceToken($tokenDetail) {

        $deviceToken = $this->where('tdt_device_token', $tokenDetail['tdt_device_token'])->where('deleted', '1')->first();
        if (count($deviceToken) > 0) {
            $data = $this->where('tdt_device_token', $tokenDetail['tdt_device_token'])->update($tokenDetail);
        } else {
            $data = $this->insert($tokenDetail);
        }
        return $data;
    }

    public function getDeviceTokenDetail($userId) {
      $result = DB::table(config::get('databaseconstants.TBL_TEENAGER_DEVICE_TOKEN'))
                        ->selectRaw('*')
                        ->where('deleted', '1')
                        ->where('tdt_user_id', $userId)
                        ->get();
        return $result;
    }

     /*
     * Delete device token
    */
    public function deleteDeviceToken($userId,$token) {
        $data = DB::table(config::get('databaseconstants.TBL_TEENAGER_DEVICE_TOKEN'))->where('tdt_user_id', $userId)->where('tdt_device_token', $token)->delete();
        //$data = DB::table(config::get('databaseconstants.TBL_TEENAGER_DEVICE_TOKEN'))->where('tdt_user_id', $userId)->delete();
        return $data;
    }
}
