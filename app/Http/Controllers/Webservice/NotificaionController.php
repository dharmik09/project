<?php

namespace App\Http\Controllers\Webservice;

use App\Http\Controllers\Controller;
use App\Services\Teenagers\Contracts\TeenagersRepository;
use App\Notifications;
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
        $this->log = new Logger('api-level1-activity-controller');
        $this->log->pushHandler(new StreamHandler(storage_path().'/logs/monolog-'.date('m-d-Y').'.log'));    
    }

    public function getNotificationPageWise(Request $request) {
        $response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg')];
        $teenager = $this->teenagersRepository->getTeenagerById($request->userId);
        $this->log->info('Get teenager detail for userId'.$request->userId , array('api-name'=> 'getNotification'));
        if($request->userId != "" && $teenager) {
            
            $pageNo = 0;
            if($request->pageNo != '' && $request->pageNo > 1){
                $pageNo = ($request->pageNo-1) * 10;
            }

            $data = $this->objNotifications->getNotificationsByUserTypeAnsId(Config::get('constant.NOTIFICATION_TEENAGER'),$teenager->id,$pageNo);

            foreach($data as $key => $value){
                if(isset($value->senderTeenager) && $value->senderTeenager != '') {
                    $teenPhoto = Config::get('constant.TEEN_ORIGINAL_IMAGE_UPLOAD_PATH').$value->senderTeenager->t_photo;
                } else {
                    $teenPhoto = Config::get('constant.TEEN_THUMB_IMAGE_UPLOAD_PATH').'proteen-logo.png';
                }
                $data[$key]->n_sender_image = Storage::url($teenPhoto);
                if($value->n_record_id != 0){
                    $data[$key]->n_request_status = $value->community->tc_status;
                }
                else{
                    $data[$key]->n_request_status = NULL;
                }
                unset($data[$key]->senderTeenager);
                unset($data[$key]->community);
            }
            
            if($data){
                $response['data'] = $data;
            }
            else{
                $response['data'] = trans('appmessages.data_empty_msg');
            }

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

                $data = $this->objNotifications->deleteNotificationById($request->notificationId);
                
                if($data){
                    $response['data'] = [];
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

            $data = $this->objNotifications->getUnreadNotificationByUserId($teenager->id);
            
            if(isset($data)){
                $response['data']['notificationsCount'] = $data;
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

            $id = $request->notificationId;
            $data =  $this->objNotifications->ChangeNotificationsReadStatus($id,Config::get('constant.NOTIFICATION_STATUS_READ'));
            
            if(isset($data)){
                $response['data'] = [];
                $response['message'] = trans('appmessages.default_success_msg');
            }
            else{
                $response['data'] = [];
                $response['message'] = trans('appmessages.default_error_msg');
            }

            $response['status'] = 1;
            $response['login'] = 1;

            $this->log->info('Response for change Notifications status to read' , array('api-name'=> 'readNotification'));

        } else {
            $this->log->error('Parameter missing error' , array('api-name'=> 'readNotification'));
            $response['message'] = trans('appmessages.missing_data_msg');
        }
        return response()->json($response, 200);
    }
    
}
