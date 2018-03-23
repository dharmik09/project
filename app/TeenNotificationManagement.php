<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use Config;

class TeenNotificationManagement extends Model
{
    protected $table = 'pro_tnm_teen_notification_management';

    protected $fillable = ['tnm_teenager','tnm_notification_delete','tnm_notification_read','deleted'];

    /**
     * Insert and Update Profession Wise Tag
     */
    public function insertUpdate($data)
    {
        if (isset($data['id']) && $data['id'] != '' && $data['id'] > 0) {
            return TeenNotificationManagement::where('id', $data['id'])->update($data);
        } else {
            return TeenNotificationManagement::create($data);
        }
    }

    /**
     * get all Teen Notification Management
     */
    public function getAllTeenNotificationManagement() {
        $return = TeenNotificationManagement::where('deleted',Config::get('constant.ACTIVE_FLAG'))->get();
        return $return;
    }

    /**
     * get all Teen Notification Management by Teenager Id
     */
    public function getTeenNotificationManagementByTeenagerId($teenagerId) {
        $return = TeenNotificationManagement::where('tnm_teenager',$teenagerId)->where('deleted',Config::get('constant.ACTIVE_FLAG'))->first();
        return $return;
    }
}
