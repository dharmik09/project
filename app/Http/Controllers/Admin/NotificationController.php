<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Services\Teenagers\Contracts\TeenagersRepository;
use App\Services\Sponsors\Contracts\SponsorsRepository;
use App\Services\Parents\Contracts\ParentsRepository;
use Input;
use App\Notifications;
use Redirect;
use Helpers;
use App\DeviceToken;
use Config;
use App\Jobs\SendPushNotificationToAllTeenagers;

class NotificationController extends Controller
{
    public function __construct(TeenagersRepository $teenagersRepository)
    {
        //$this->middleware('auth.admin');
        $this->teenagersRepository = $teenagersRepository;
        $this->userCerfificatePath = Config::get('constant.CERTIFICATE_PATH');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //$teenagers = $this->teenagersRepository->getAllActiveTeenagersForNotification();
        $teenagersName = $this->teenagersRepository->getAllActiveTeenagersForNotificationObj()->get();
        return view('admin.Notification', compact('teenagersName'));
    }

    public function getIndex(){
        $teenagers = $this->teenagersRepository->getAllActiveTeenagersForNotificationObj()->get()->count();
        $records = array();
        $columns = array(
            0 => 'id',
            1 => 't_name',
            2 => 't_email',
            3 => 't_gender',
            4 => 't_sponsor_choice',
            5 => 'c_name',
            6 => 't_social_provider',
            7 => 'deleted'
        );
        $order = Input::get('order');
        $search = Input::get('search');
        $records["data"] = array();
        $iTotalRecords = $teenagers;
        $iTotalFiltered = $iTotalRecords;
        $iDisplayLength = intval(Input::get('length')) <= 0 ? $iTotalRecords : intval(Input::get('length'));
        $iDisplayStart = intval(Input::get('start'));
        $sEcho = intval(Input::get('draw'));

        $records["data"] = $this->teenagersRepository->getAllActiveTeenagersForNotificationObj();
        if (!empty($searchName)) {
            $records["data"]->where('teenager.t_name', "Like", $searchName);

            // No of record after filtering
            $iTotalFiltered = $records["data"]->where('teenager.t_name', "Like", $searchName)->count();
        }
        if (!empty($search['value'])) {
            $val = $search['value'];
            $records["data"]->where(function($query) use ($val) {
                $query->where('teenager.t_name', "Like", "%$val%");
                $query->orWhere('teenager.t_email', "Like", "%$val%");
                $query->orWhere('country.c_name', "Like", "%$val%");
                $query->orWhere('teenager.t_social_provider', "Like", "%$val%");
            });

            // No of record after filtering
            $iTotalFiltered = $records["data"]->where(function($query) use ($val) {
                    $query->where('teenager.t_name', "Like", "%$val%");
                    $query->orWhere('teenager.t_email', "Like", "%$val%");
                    $query->orWhere('country.c_name', "Like", "%$val%");
                    $query->orWhere('teenager.t_social_provider', "Like", "%$val%");
                })->count();
        }
        
        //order by
        foreach ($order as $o) {
            $records["data"] = $records["data"]->orderBy($columns[$o['column']], $o['dir']);
        }

        //limit
        $records["data"] = $records["data"]->take($iDisplayLength)->offset($iDisplayStart)->get();
        // this $sid use for school edit teenager and admin edit teenager
        $sid = 0;
        if (!empty($records["data"])) {
            foreach ($records["data"] as $key => $_records) {
                $records["data"][$key]->t_name = trim($_records->t_name);
                $records["data"][$key]->t_gender = ($_records->t_gender == 1)? 'Male' : 'Female';
                switch ($_records->t_sponsor_choice) {
                    case "1":
                        $records["data"][$key]->t_sponsor_choice = trans('labels.formblself');
                        break;
                    case "2":
                        $records["data"][$key]->t_sponsor_choice = trans('labels.formblsponsor');
                        break;
                    default:
                        $records["data"][$key]->t_sponsor_choice = trans('labels.formblnone');
                    };
                $records["data"][$key]->deleted = ($_records->deleted == 1) ? "<i class='s_active fa fa-square'></i>" : "<i class='s_inactive fa fa-square'></i>";
                
            }
        }

        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalFiltered;

        return \Response::json($records);
        exit;
    }

    public function sendNotification() {
        $getId = $_REQUEST['id'];
        $data = [];
        $data['message'] = input::get('notification_message');
        $andyToken = [];
                       
        if(count($getId) <= 5) 
        {
            $objDeviceToken = new DeviceToken();
            foreach ($getId AS $key => $value) {
                $teenData = $this->teenagersRepository->getTeenagerByTeenagerId($value);
                if ($teenData['is_notify'] == 1) {
                    $result = $objDeviceToken->getDeviceTokenDetail($value);
                    if (!empty($result)) {
                        foreach ($result AS $k => $tData) {                            
                            if ($tData->tdt_device_type == 1) 
                            {
                               $singleToken = $tData->tdt_device_token;
                               $certificatePath = public_path($this->userCerfificatePath);                            
                               $return = Helpers::pushNotificationForiPhone($singleToken,$data,$certificatePath);
                            } elseif ($tData->tdt_device_type == 2) {
                               $tokenArr[] = $tData->tdt_device_token;                               
                            }
                        }
                    }
                }
            }
            if(isset($tokenArr) && count($tokenArr) > 0)
            {
               $return = Helpers::pushNotificationForAndroid($tokenArr,$data); 
            }           
            return Redirect::to("admin/notification")->with('success', trans('labels.notificationsendsuccess'));
        } 
        else 
        {
            $message = input::get('notification_message');
            $objNotifications = new Notifications();
            $return = $objNotifications->saveTeenagerDetailForSendNotification($getId, $message);
            if ($return) {
                return Redirect::to("admin/notification")->with('success', trans('labels.notificationsendaftersuccess'));
            } else {
                return Redirect::to("admin/notification")->with('error', trans('labels.commonerrormessage'));
            }
        }
    }

    public function sendNotificationToTeen() {
        $data = [];
        $data['message'] = input::get('notification_message');
        $objNotifications = new Notifications();
        $sendToAll = Input::get('sendtoall');
        if (isset($sendToAll) && !empty($sendToAll)) {
            $notificationData['n_sender_id'] = '0';
            $notificationData['n_sender_type'] = Config::get('constant.NOTIFICATION_ADMIN');
            $notificationData['n_receiver_id'] = 0;
            $notificationData['n_receiver_type'] = Config::get('constant.NOTIFICATION_TEENAGER');
            $notificationData['n_notification_type'] = Config::get('constant.NOTIFICATION_TYPE_ADD_PROFESSION');
            $notificationData['n_notification_text'] = $data['message'];
            $return = $objNotifications->insertUpdate($notificationData);
            $pushNotificationData = [];
            $pushNotificationData['notificationType'] = Config::get('constant.COMMON_NOTIFICATION_TYPE');
            $pushNotificationData['message'] = (isset($notificationData['n_notification_text']) && !empty($notificationData['n_notification_text'])) ? strip_tags($notificationData['n_notification_text']) : '';
            dispatch( new SendPushNotificationToAllTeenagers($pushNotificationData) )->onQueue('processing');
            if ($return) {
                return Redirect::to("admin/notification")->with('success', trans('labels.notificationsendaftersuccess'));
            } else {
                return Redirect::to("admin/notification")->with('error', trans('labels.commonerrormessage'));
            }
        } else {
            $getId = Input::get('teenName');
            $andyToken = [];
            if(count($getId) <= 10) 
            {
                $objDeviceToken = new DeviceToken();
                foreach ($getId AS $key => $value) {
                    $teenData = $this->teenagersRepository->getTeenagerByTeenagerId($value);
                    if ($teenData['is_notify'] == 1) {
                        $result = $objDeviceToken->getDeviceTokenDetail($value);
                        if (!empty($result)) {
                            foreach ($result AS $k => $tData) {                            
                                if ($tData->tdt_device_type == 1) 
                                {
                                   $singleToken = $tData->tdt_device_token;
                                   $certificatePath = public_path($this->userCerfificatePath);                            
                                   //$return = Helpers::pushNotificationForiPhone($singleToken,$data,$certificatePath);
                                } elseif ($tData->tdt_device_type == 2) {
                                   $tokenArr[] = $tData->tdt_device_token;                               
                                }
                            }
                        }
                    }
                }
                if(isset($tokenArr) && count($tokenArr) > 0)
                {
                   $return = Helpers::pushNotificationForAndroid($tokenArr,$data); 
                }           
                return Redirect::to("admin/notification")->with('success', trans('labels.notificationsendsuccess'));
            } 
            else 
            {
                $notificationData['n_sender_id'] = '0';
                $notificationData['n_sender_type'] = Config::get('constant.NOTIFICATION_ADMIN');
                $notificationData['n_receiver_id'] = 0;
                $notificationData['n_receiver_type'] = Config::get('constant.NOTIFICATION_TEENAGER');
                $notificationData['n_notification_type'] = Config::get('constant.NOTIFICATION_TYPE_ADD_PROFESSION');
                $notificationData['n_notification_text'] = $data['message'];
                $return = $objNotifications->insertUpdate($notificationData);
                if ($return) {
                    return Redirect::to("admin/notification")->with('success', trans('labels.notificationsendaftersuccess'));
                } else {
                    return Redirect::to("admin/notification")->with('error', trans('labels.commonerrormessage'));
                }
            }
        }
    }
}