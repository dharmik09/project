<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Config;
use DB;

class Notifications extends Model implements AuthenticatableContract, AuthorizableContract, CanResetPasswordContract {

    use Authenticatable,
        Authorizable,
        CanResetPassword;

    protected $table = 'pro_n_notifications';
    //protected $fillable = ['id', 'n_user_id', 'n_notification_text', 'n_status', 'created_at', 'updated_at', 'deleted'];
    protected $guarded = [];


    public function saveTeenagerDetailForSendNotification($userId, $message) {
        foreach ($userId AS $key => $Id) {
            $saveData = [];
            $notificationData = '';
            $saveData['n_user_id'] = $Id;
            $saveData['n_notification_text'] = $message;
            $response = DB::table(config::get('databaseconstants.TBL_NOTIFICATIONS'))->insert($saveData);
        }
        if ($response) {
            return true;
        } else {
            return false;
        }
    }

    public function getTeenDetailForNotification() {
        $teenagers = DB::table(config::get('databaseconstants.TBL_NOTIFICATIONS') . " AS notification ")
                    ->leftjoin(config::get('databaseconstants.TBL_TEENAGER_DEVICE_TOKEN') . " AS token ", 'token.tdt_user_id', '=', 'notification.n_user_id')
                    ->leftjoin(config::get('databaseconstants.TBL_TEENAGERS') . " AS teen ", 'teen.id', '=', 'notification.n_user_id')
                    ->selectRaw('token.tdt_device_token, token.tdt_device_type, notification.n_notification_text,notification.n_user_id,notification.id,teen.is_notify')
                    ->where('notification.n_status' , '=', 0)
                    ->where('notification.deleted' , '=', 1)
                    ->take(1000)
                    ->get();
        return $teenagers;
    }

    public function updateNotificationStatusById($id) {
        $result = DB::table(config::get('databaseconstants.TBL_NOTIFICATIONS'))->where('id', $id)->update(['n_status'=>1]);
        return $result;
    }

    public function deleteNotificationData() {
        $result = DB::table(config::get('databaseconstants.TBL_NOTIFICATIONS'))->where('n_status', 1)->delete();
        return $result;
    }

    public function saveAllActiveTeenagerForSendNotifivation($saveData){
        $response = DB::table(config::get('databaseconstants.TBL_NOTIFICATIONS'))->insert($saveData);
        if ($response) {
            return true;
        } else {
            return false;
        }
     }
}
