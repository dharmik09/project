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

class NotificationController extends Controller
{
    public function __construct(TeenagersRepository $TeenagersRepository)
    {
        //$this->middleware('auth.admin');
        $this->TeenagersRepository = $TeenagersRepository;
        $this->userCerfificatePath = Config::get('constant.CERTIFICATE_PATH');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $teenagers = $this->TeenagersRepository->getAllActiveTeenagersForNotification();
        return view('admin/Notification', compact('teenagers'));
    }

    public function getIndex(){
        $teenagers = $this->TeenagersRepository->getAllActiveTeenagersForNotificationObj()->get()->count();
        echo "<pre/>"; print_r($teenagers); die();
        $records = array();
        $columns = array(
            0 => 'id',
            1 => 't_name',
            2 => 't_email',
            3 => 't_coins',
            4 => 't_phone',
            6 => 'deleted',
            8 => 'created_at',
        );
        $order = Input::get('order');
        $search = Input::get('search');
        $records["data"] = array();
        $iTotalRecords = $teenagers;
        $iTotalFiltered = $iTotalRecords;
        $iDisplayLength = intval(Input::get('length')) <= 0 ? $iTotalRecords : intval(Input::get('length'));
        $iDisplayStart = intval(Input::get('start'));
        $sEcho = intval(Input::get('draw'));

        $records["data"] = $this->teenagersRepository->getAllTeenagersData();
        if (!empty($search['value'])) {
            $val = $search['value'];
            $records["data"]->where(function($query) use ($val) {
                $query->where('teenager.t_name', "Like", "%$val%");
                $query->orWhere('teenager.created_at', "Like", "%$val%");
                $query->orWhere('teenager.t_nickname', "Like", "%$val%");
                $query->orWhere('teenager.t_email', "Like", "%$val%");
            });

            // No of record after filtering
            $iTotalFiltered = $records["data"]->where(function($query) use ($val) {
                    $query->where('teenager.t_name', "Like", "%$val%");
                    $query->orWhere('teenager.created_at', "Like", "%$val%");
                    $query->orWhere('teenager.t_nickname', "Like", "%$val%");
                    $query->orWhere('teenager.t_email', "Like", "%$val%");
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
                $records["data"][$key]->t_name = "<a target='_blank' href='".url('/admin/view-teenager')."/".$_records->id."'>".$_records->t_name."</a>";
                $records["data"][$key]->action = '<a href="'.url('/admin/edit-teenager').'/'.$_records->id.'/'.$sid.'"><i class="fa fa-edit"></i> &nbsp;&nbsp;</a>
                                                    <a onClick="return confirm(\'Are you sure want to delete?\')" href="'.url('/admin/delete-teenager').'/'.$_records->id.'"><i class="i_delete fa fa-trash"></i> &nbsp;&nbsp;</a>
                                                    <a href="#" onClick="add_details(\''.$_records->id.'\');" data-toggle="modal" id="#userCoinsData" data-target="#userCoinsData"><i class="fa fa-database" aria-hidden="true"></i></a>';
                $records["data"][$key]->deleted = ($_records->deleted == 1) ? "<i class='s_active fa fa-square'></i>" : "<i class='s_inactive fa fa-square'></i>";
                $records["data"][$key]->importData = "<a href='".url('/admin/export-l4-data')."/".$_records->id."'><i class='fa fa-file-excel-o' aria-hidden='true'></i></a>";
                $records["data"][$key]->t_name = trim($_records->t_name);
                $records["data"][$key]->t_birthdate = date('d/m/Y',strtotime($_records->t_birthdate));
                $records["data"][$key]->created_at = date('d/m/Y',strtotime($_records->created_at));
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
                $teenData = $this->TeenagersRepository->getTeenagerByTeenagerId($value);
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
}