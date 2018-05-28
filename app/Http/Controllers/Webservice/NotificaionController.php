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
use App\Schools;
use App\Teenagers;
use App\Parents;

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
                $data[$key]->n_notification_text = strip_tags($value->n_notification_text);
                // if(isset($value->senderTeenager) && $value->senderTeenager != '') {
                //     $photoURL = Config::get('constant.TEEN_ORIGINAL_IMAGE_UPLOAD_PATH').$value->senderTeenager->t_photo;
                //     if(Storage::size(Config::get('constant.TEEN_ORIGINAL_IMAGE_UPLOAD_PATH').$value->senderTeenager->t_photo)>0){
                //         $teenPhoto = Config::get('constant.TEEN_ORIGINAL_IMAGE_UPLOAD_PATH').$value->senderTeenager->t_photo;
                //     }
                // }
                if ($value->n_sender_type == Config::get('constant.NOTIFICATION_TEENAGER')) {
                    $teenDetails = Teenagers::find($value->n_sender_id);
                    if (isset($teenDetails) && !empty($teenDetails) && $teenDetails->t_photo != "" && Storage::size(Config::get('constant.TEEN_THUMB_IMAGE_UPLOAD_PATH').$teenDetails->t_photo) > 0) {
                        $photoUrl = Storage::url(Config::get('constant.TEEN_THUMB_IMAGE_UPLOAD_PATH').$teenDetails->t_photo);
                    } else {
                        $photoUrl = Storage::url(Config::get('constant.TEEN_THUMB_IMAGE_UPLOAD_PATH').'proteen-logo.png');
                    }
                } else if ($value->n_sender_type == Config::get('constant.NOTIFICATION_ADMIN')) {
                    $photoUrl = Storage::url('img/proteen-logo.png');
                } else if ($value->n_sender_type == Config::get('constant.NOTIFICATION_PARENT')) {
                    $parentDetails = Parents::find($value->n_sender_id);
                    if (isset($parentDetails) && !empty($parentDetails) && $parentDetails->p_photo != "" && Storage::size(Config::get('constant.PARENT_THUMB_IMAGE_UPLOAD_PATH').$parentDetails->p_photo) > 0) {
                        $photoUrl = Storage::url(Config::get('constant.PARENT_THUMB_IMAGE_UPLOAD_PATH').$teenDetails->t_photo);
                    } else {
                        $photoUrl = Storage::url(Config::get('constant.PARENT_THUMB_IMAGE_UPLOAD_PATH').'proteen-logo.png');
                    }
                } else if ($value->n_sender_type == Config::get('constant.NOTIFICATION_SCHOOL')) {
                    $schoolDetails = Schools::find($value->n_sender_id);
                    if (isset($schoolDetails) && !empty($schoolDetails) && $schoolDetails->sc_logo != "" && Storage::size(Config::get('constant.SCHOOL_ORIGINAL_IMAGE_UPLOAD_PATH').$schoolDetails->sc_logo) > 0) {
                        $photoUrl = Storage::url(Config::get('constant.SCHOOL_ORIGINAL_IMAGE_UPLOAD_PATH').$schoolDetails->sc_logo);
                    } else {
                        $photoUrl = Storage::url(Config::get('constant.SCHOOL_ORIGINAL_IMAGE_UPLOAD_PATH').'proteen-logo.png');
                    }
                } else {
                    $photoUrl = Storage::url('img/proteen-logo.png');
                } 
                $data[$key]->n_sender_image = $photoUrl;
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

            $finalArr = array_unique(array_merge($deletedData, $readData));
            $noticationCount = $this->objNotifications->getNotificationsCountByUserTypeAndIdByDeleted(Config::get('constant.NOTIFICATION_TEENAGER'),$teenager->id,[]);
            $count = $noticationCount - count($finalArr);

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
                $notificationDetails = $this->objNotifications->find($notificationId);
              
                if (isset($notificationDetails) && !empty($notificationDetails)) {
                   
                    $teenNotificationManagementCheck = $this->objTeenNotificationManagement->getTeenNotificationManagementByTeenagerId($teenager->id);
                   
                    $deletedNotificationArr = (isset($teenNotificationManagementCheck->tnm_notification_delete) && !empty($teenNotificationManagementCheck->tnm_notification_delete)) ? explode(',', $teenNotificationManagementCheck->tnm_notification_delete) : [];
                    
                    if (!in_array($notificationId, $deletedNotificationArr)) {
                        
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
                        if ($data) {
                            $response['status'] = 1;
                            $response['message'] = 'Notification deleted.';
                        }
                        else{
                            $response['status'] = 0;
                            $response['message'] = trans('appmessages.default_error_msg');
                        }
                    } else {
                        $response['status'] = 0;
                        $response['message'] = 'Notification already deleted.';
                    }
                } else {
                    $response['status'] = 0;
                    $response['message'] = 'Notification not available.';
                }
                $notificationManagementData = $this->objTeenNotificationManagement->getTeenNotificationManagementByTeenagerId($teenager->id);

                $deletedData = [];
                $readData = [];

                if(count($notificationManagementData)>0) {
                    if (!empty($notificationManagementData->tnm_notification_delete) && isset($notificationManagementData->tnm_notification_delete)) {
                        $deletedData = explode(',', $notificationManagementData->tnm_notification_delete);
                    }
                    if (!empty($notificationManagementData->tnm_notification_read) && isset($notificationManagementData->tnm_notification_read)) {
                        $readData = explode(',', $notificationManagementData->tnm_notification_read);
                    }
                }
                $finalArr = array_unique(array_merge($deletedData, $readData));
                $notificationCount = $this->objNotifications->getNotificationsCountByUserTypeAndIdByDeleted(Config::get('constant.NOTIFICATION_TEENAGER'),$teenager->id,[]);
                $count = $notificationCount - count($finalArr);

                $response['data'] = [];
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
            $finalArr = array_unique(array_merge($deletedData, $readData));
            $data = $this->objNotifications->getNotificationsCountByUserTypeAndIdByDeleted(Config::get('constant.NOTIFICATION_TEENAGER'),$teenager->id,[]);
            $count = $data - count($finalArr);
            
            if(isset($data)){
                $response['data']['notificationsCount'] = (isset($count) && $count > 0) ? $count : 0;
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


            $notificationDetails = $this->objNotifications->find($notificationId);
            if (isset($notificationDetails) && !empty($notificationDetails)) {
                $teenNotificationManagementCheck = $this->objTeenNotificationManagement->getTeenNotificationManagementByTeenagerId($teenager->id);

                $readNotificationArr = ($teenNotificationManagementCheck->tnm_notification_read && !empty($teenNotificationManagementCheck->tnm_notification_read)) ? explode(',', $teenNotificationManagementCheck->tnm_notification_read) : [];
                
                if (!in_array($notificationId, $readNotificationArr)) {
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
                    if(isset($data)) {
                        $response['status'] = 1;
                        $response['message'] = trans('appmessages.default_success_msg');
                    }
                    else {
                        $response['status'] = 0;
                        $response['message'] = trans('appmessages.default_error_msg');
                    }
                } else {
                    $response['status'] = 0;
                    $response['message'] = 'Notification already read by you';
                }
            } else {
                $response['status'] = 0;
                $response['message'] = 'Notification not available.';
            }
            $notificationManagementData = $this->objTeenNotificationManagement->getTeenNotificationManagementByTeenagerId($teenager->id);

            $deletedData = [];
            $readData = [];

            if(count($notificationManagementData)>0){
                if (isset($notificationManagementData->tnm_notification_delete) && !empty($notificationManagementData->tnm_notification_delete)) {
                    $deletedData = explode(',', $notificationManagementData->tnm_notification_delete);
                }
                if (isset($notificationManagementData->tnm_notification_read) && !empty($notificationManagementData->tnm_notification_read)) {
                    $readData = explode(',', $notificationManagementData->tnm_notification_read);
                }
            }
            $finalReadCount = array_unique(array_merge($deletedData, $readData));
            $notificationCount = $this->objNotifications->getNotificationsCountByUserTypeAndIdByDeleted(Config::get('constant.NOTIFICATION_TEENAGER'),$teenager->id,[]);
            $count = $notificationCount - count($finalReadCount);

            $response['data'] = [];
            //$response['status'] = 1;
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
