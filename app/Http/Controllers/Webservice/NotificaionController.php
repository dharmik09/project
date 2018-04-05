<?php

namespace App\Http\Controllers\Webservice;

use App\Http\Controllers\Controller;
use App\Services\Teenagers\Contracts\TeenagersRepository;
use App\Notifications;
use App\TeenNotificationManagement;
use Config;
use Storage;
use Helpers;  
use Auth;
use Input;
use Redirect;
use Illuminate\Http\Request;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class NotificaionController extends Controller {

    public function __construct(TeenagersRepository $teenagersRepository) {
        $this->teenagersRepository = $teenagersRepository;
        $this->objNotifications = new Notifications();
        $this->objTeenNotificationManagement = new TeenNotificationManagement();
        $this->log = new Logger('api-level1-activity-controller');
        $this->log->pushHandler(new StreamHandler(storage_path().'/logs/monolog-'.date('m-d-Y').'.log'));    
    }

    public function getNotificationPageWise(Request $request) {
        $response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg')];
        $teenager = $this->teenagersRepository->getTeenagerById($request->userId);
        $this->log->info('Get teenager detail for userId'.$request->userId , array('api-name'=> 'getNotification'));
        if($request->userId != "" && $teenager) {
            $data = [];
            $pageNo = 0;
            if($request->pageNo != '' && $request->pageNo > 1){
                $pageNo = ($request->pageNo-1) * 20;
            }
            $notificationManagementData = $this->objTeenNotificationManagement->getTeenNotificationManagementByTeenagerId($teenager->id);

            $deletedData = [];
            $readData = [];

            if(count($notificationManagementData)>0){
                $deletedData = explode(',', $notificationManagementData->tnm_notification_delete);
                $readData = explode(',', $notificationManagementData->tnm_notification_read);
            }

            $data = $this->objNotifications->getNotificationsByUserTypeAndIdByDeleted(Config::get('constant.NOTIFICATION_TEENAGER'),$teenager->id,$pageNo,$deletedData);
            foreach($data as $key => $value){
                $teenPhoto = Config::get('constant.TEEN_THUMB_IMAGE_UPLOAD_PATH').'proteen-logo.png';
                if(isset($value->senderTeenager) && $value->senderTeenager != '') {
                    $photoURL = Config::get('constant.TEEN_ORIGINAL_IMAGE_UPLOAD_PATH').$value->senderTeenager->t_photo;
                    if(Storage::size(Config::get('constant.TEEN_ORIGINAL_IMAGE_UPLOAD_PATH').$value->senderTeenager->t_photo)>0){
                        $teenPhoto = Config::get('constant.TEEN_ORIGINAL_IMAGE_UPLOAD_PATH').$value->senderTeenager->t_photo;
                    }
                }
                $data[$key]->n_sender_image = Storage::url($teenPhoto);
                if($value->n_record_id != 0){
                    $data[$key]->n_request_status = $value->community->tc_status;
                }
                else{
                    $data[$key]->n_request_status = NULL;
                }
                $createdDate = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s',$value->created_at)->diffForHumans();
                $data[$key]->notification_time = $createdDate;
                $data[$key]->n_read_status = (in_array($value->id, $readData)) ? '1' : '0';
                // $data[$key]->notification_time = "";
                unset($data[$key]->senderTeenager);
                unset($data[$key]->community);
            }
            
            $noticationCount = $this->objNotifications->getNotificationsCountByUserTypeAndIdByDeleted(Config::get('constant.NOTIFICATION_TEENAGER'),$teenager->id,$deletedData);
            $count = $noticationCount - count($readData);

            $response['data'] = $data;
            $response['notificationUnreadCount'] = (isset($count) && $count > 0) ? $count : 0;
            $response['status'] = 1;
            $response['login'] = 1;
            $response['message'] = trans('appmessages.default_success_msg');

            $this->log->info('Response for fetch Notifications page wise' , array('api-name'=> 'getNotification'));
        } else {
            $this->log->error('Parameter missing error' , array('api-name'=> 'getNotification'));
            $response['message'] = trans('appmessages.missing_data_msg');
        }
        return response()->json($response, 200);
    }

    public function deleteNotification(Request $request) {
        $response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg')];
        $teenager = $this->teenagersRepository->getTeenagerById($request->userId);
        $this->log->info('Get teenager detail for userId'.$request->userId , array('api-name'=> 'getNotification'));
        if($request->userId != "" && $teenager) {
            if($request->notificationId != "") {
                $notificationId = $request->notificationId;
                $teenNotificationManagementCheck = $this->objTeenNotificationManagement->getTeenNotificationManagementByTeenagerId($teenager->id);
                $notificationData['tnm_notification_delete'] = $notificationId;
                
                if(count($teenNotificationManagementCheck)>0)
                {
                    $notificationData['id'] = $teenNotificationManagementCheck->id;
                    if($teenNotificationManagementCheck->tnm_notification_delete != "")
                    {
                        $notificationData['tnm_notification_delete'] = $teenNotificationManagementCheck->tnm_notification_delete.','.$notificationId;
                    }
                }
                $notificationData['tnm_teenager'] = $teenager->id;
                $data = $this->objTeenNotificationManagement->insertUpdate($notificationData);
                if($data){
                    $response['message'] = trans('appmessages.default_success_msg');
                }
                else{
                    $response['message'] = trans('appmessages.default_error_msg');
                }
                $notificationManagementData = $this->objTeenNotificationManagement->getTeenNotificationManagementByTeenagerId($teenager->id);

                $deletedData = [];
                $readData = [];

                if(count($notificationManagementData)>0){
                    $deletedData = explode(',', $notificationManagementData->tnm_notification_delete);
                    $readData = explode(',', $notificationManagementData->tnm_notification_read);
                }
                $notificationCount = $this->objNotifications->getNotificationsCountByUserTypeAndIdByDeleted(Config::get('constant.NOTIFICATION_TEENAGER'),$teenager->id,$deletedData);
                $count = $notificationCount - count($readData);

                $response['data'] = [];
                $response['status'] = 1;
                $response['login'] = 1;
                $response['notificationUnreadCount'] = (isset($count) && $count > 0) ? $count : 0;
                $this->log->info('Response for fetch Notifications page wise' , array('api-name'=> 'getNotification'));
            } else {
                $this->log->error('Parameter missing error' , array('api-name'=> 'getNotification'));
                $response['message'] = trans('appmessages.missing_data_msg');
            }
        } else {
            $this->log->error('Parameter missing error' , array('api-name'=> 'getNotification'));
            $response['message'] = trans('appmessages.missing_data_msg');
        }
        return response()->json($response, 200);
    }

    public function getUnreadNotificationCount(Request $request) {
        $response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg')];
        $teenager = $this->teenagersRepository->getTeenagerById($request->userId);
        $this->log->info('Get teenager detail for userId'.$request->userId , array('api-name'=> 'getNotification'));
        if($request->userId != "" && $teenager) {
            $notificationManagementData = $this->objTeenNotificationManagement->getTeenNotificationManagementByTeenagerId($teenager->id);

            $deletedData = [];
            $readData = [];

            if(count($notificationManagementData)>0){
                $deletedData = explode(',', $notificationManagementData->tnm_notification_delete);
                $readData = explode(',', $notificationManagementData->tnm_notification_read);
            }
            $data = $this->objNotifications->getNotificationsCountByUserTypeAndIdByDeleted(Config::get('constant.NOTIFICATION_TEENAGER'),$teenager->id,$deletedData);
            $count = $data - count($readData);
            
            if(isset($data)){
                $response['data']['notificationsCount'] = $count;
                $response['message'] = trans('appmessages.default_success_msg');
            }
            else{
                $response['data'] = [];
                $response['message'] = trans('appmessages.default_error_msg');
            }

            $response['status'] = 1;
            $response['login'] = 1;

            $this->log->info('Response for fetch Notifications page wise' , array('api-name'=> 'getNotification'));

        } else {
            $this->log->error('Parameter missing error' , array('api-name'=> 'getNotification'));
            $response['message'] = trans('appmessages.missing_data_msg');
        }
        return response()->json($response, 200);
    }

    public function changeNotificationStatus(Request $request) {
        $response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg')];
        $teenager = $this->teenagersRepository->getTeenagerById($request->userId);
        $this->log->info('Get teenager detail for userId'.$request->userId , array('api-name'=> 'readNotification'));
        if($request->userId != "" && $teenager) {

            $notificationId = $request->notificationId;
            $teenNotificationManagementCheck = $this->objTeenNotificationManagement->getTeenNotificationManagementByTeenagerId($teenager->id);
            $notificationData['tnm_notification_read'] = $notificationId;

            if(count($teenNotificationManagementCheck)>0)
            {
                $notificationData['id'] = $teenNotificationManagementCheck->id;
                if($teenNotificationManagementCheck->tnm_notification_read != "")
                {
                    $notificationData['tnm_notification_read'] = $teenNotificationManagementCheck->tnm_notification_read.','.$notificationId;
                }
            }
            
            $notificationData['tnm_teenager'] = $teenager->id;
            $data = $this->objTeenNotificationManagement->insertUpdate($notificationData);
            if(isset($data)){
                $response['message'] = trans('appmessages.default_success_msg');
            }
            else{
                $response['message'] = trans('appmessages.default_error_msg');
            }
            $notificationManagementData = $this->objTeenNotificationManagement->getTeenNotificationManagementByTeenagerId($teenager->id);

            $deletedData = [];
            $readData = [];

            if(count($notificationManagementData)>0){
                $deletedData = explode(',', $notificationManagementData->tnm_notification_delete);
                $readData = explode(',', $notificationManagementData->tnm_notification_read);
            }
            $notificationCount = $this->objNotifications->getNotificationsCountByUserTypeAndIdByDeleted(Config::get('constant.NOTIFICATION_TEENAGER'),$teenager->id,$deletedData);
            $count = $notificationCount - count($readData);

            $response['data'] = [];
            $response['status'] = 1;
            $response['login'] = 1;
            $response['notificationUnreadCount'] = (isset($count) && $count > 0) ? $count : 0;
            $this->log->info('Response for change Notifications status to read' , array('api-name'=> 'readNotification'));

        } else {
            $this->log->error('Parameter missing error' , array('api-name'=> 'readNotification'));
            $response['message'] = trans('appmessages.missing_data_msg');
        }
        return response()->json($response, 200);
    }
    
}
