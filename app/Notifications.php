<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use App\Teenagers;
use App\Community;
use Config;
use DB;

class Notifications extends Model implements AuthenticatableContract, AuthorizableContract, CanResetPasswordContract {

    use Authenticatable,
        Authorizable,
        CanResetPassword;

    protected $table = 'pro_n_notifications';
    //protected $fillable = ['id', 'n_user_id', 'n_notification_text', 'n_status', 'created_at', 'updated_at', 'deleted'];
    protected $guarded = [];

    /**
     * Insert and Update Notifications
     */
    public function insertUpdate($data)
    {
        return Notifications::create($data);
    }


    public function senderTeenager()
    {
        return $this->belongsTo(Teenagers::class, 'n_sender_id');
    }

    public function community()
    {
        return $this->belongsTo(Community::class, 'n_record_id');
    }

    /**
     * Delete Notification by Id
     */
    public function deleteNotificationById($id)
    {
        return Notifications::where('id',$id)->update(['deleted' => config::get('constant.DELETED_FLAG')]);
    }
    
    /**
     * get Unread Notification count
     */
    public function getUnreadNotificationByUserId($userId)
    {
        return Notifications::where(function($query) use ($userId) {
                                $query->where('n_receiver_id', '=', $userId)
                                    ->orWhere('n_receiver_id', '=', 0);
                            })
                            ->where('n_read_status',Config::get('constant.NOTIFICATION_STATUS_UNREAD'))
                            ->where('deleted',config::get('constant.ACTIVE_FLAG'))
                            ->count();
    }

    /**
     * Get user Notifications by userid
     */
    public function getNotificationsByUserTypeAnsId($type,$userId,$record)
    {
        return Notifications::orderBy('created_at','DESC')
                            ->with('senderTeenager')
                            ->with('community')
                            ->where(function($query) use ($userId) {
                                $query->where('n_receiver_id', '=', $userId)
                                    ->orWhere('n_receiver_id', '=', 0);
                            })
                            ->where('n_receiver_type',$type)
                            ->where('deleted',config::get('constant.ACTIVE_FLAG'))
                            ->skip($record)
                            ->take(20)
                            ->get();
    }

    /**
     * Change Notifications read Status
     */
    public function ChangeNotificationsReadStatus($id,$status)
    {
        
        $response =  Notifications::where('id', $id)->update(['n_read_status' => $status]);
        return $response;
    }

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
        $teenagers = DB::table(config::get('databaseconstants.TBL_NOTIFICATIONS') . " AS notification")
                    ->leftjoin(config::get('databaseconstants.TBL_TEENAGER_DEVICE_TOKEN') . " AS token", 'token.tdt_user_id', '=', 'notification.n_user_id')
                    ->leftjoin(config::get('databaseconstants.TBL_TEENAGERS') . " AS teen", 'teen.id', '=', 'notification.n_user_id')
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

    /**
     * get Unread Notification count By User Id
     */
    public function getUnreadNotificationCountForUser($userId)
    {
        return Notifications::where('n_receiver_id', '=', $userId)
                            ->where('n_read_status',Config::get('constant.NOTIFICATION_STATUS_UNREAD'))
                            ->where('deleted',config::get('constant.ACTIVE_FLAG'))
                            ->count();
    }

    /**
     * get Unread Notification count By User Id
     */
    public function getNotificationDetailsByRecordId($recordId, $userId)
    {
        return Notifications::where('n_receiver_id', $userId)
                            ->where('n_record_id', $recordId)
                            ->where('n_read_status',Config::get('constant.NOTIFICATION_STATUS_UNREAD'))
                            ->where('deleted',config::get('constant.ACTIVE_FLAG'))
                            ->first();
    }
}
