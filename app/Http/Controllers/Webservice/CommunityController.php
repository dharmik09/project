<?php

namespace App\Http\Controllers\Webservice;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Auth;
use Illuminate\Http\Request;
use App\Services\Teenagers\Contracts\TeenagersRepository;
use App\Services\Community\Contracts\CommunityRepository;
use App\Services\Template\Contracts\TemplatesRepository;
use Config;
use Storage;
use Helpers;
use Mail;
use Carbon\Carbon;
use App\Events\SendMail;
use Event;
use App\Notifications;

class CommunityController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(TemplatesRepository $templateRepository, TeenagersRepository $teenagersRepository, CommunityRepository $communityRepository)
    {
        //$this->middleware('admin.guest', ['except' => 'logout']);
        $this->communityRepository = $communityRepository;
        $this->templateRepository = $templateRepository;
        $this->teenagersRepository = $teenagersRepository;
        $this->teenagerThumbImageUploadPath = Config::get('constant.TEEN_THUMB_IMAGE_UPLOAD_PATH');
        $this->objNotifications = new Notifications();
    }

    /* Request Params : communityNewConnections
     *  loginToken, userId, lastTeenId, sortBy, sortOption, searchText
     *  Service after loggedIn user
     */
    public function communityNewConnections(Request $request)
    {
        $response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg') ] ;
        $teenager = $this->teenagersRepository->getTeenagerById($request->userId);
        if($request->userId != "" && $teenager) {
            if (isset($request->sortBy) && $request->sortBy != '' && isset($request->sortOption) && $request->sortOption != '') {
                $sortBy = Helpers::getSortByColumn($request->sortBy);
                if ($sortBy == 't_birthdate') {
                    $ageVal = Helpers::age($request->sortOption);
                    if (strpos($ageVal, '-') !== false) {
                        $sortOption = Helpers::getDateRangeByAge($ageVal);
                    } else {
                        $sortOption = $ageVal;
                    }
                } else {
                    $sortOption = $request->sortOption;
                }
            } else {
                $sortBy = '';
                $sortOption = '';
            }
            if (isset($request->searchText) && $request->searchText != '') {
                $searchText = $request->searchText;
            } else {
                $searchText = '';
            }
            if (isset($request->lastTeenId) && $request->lastTeenId != '') {
                $lastTeenId = $request->lastTeenId;
            } else {
                $lastTeenId = '';
            }
            $newConnections = $this->communityRepository->getNewConnections($request->userId, $searchText, $lastTeenId, $sortBy, $sortOption);
            $newConnectionsCount = $this->communityRepository->getNewConnectionsCount($request->userId, $searchText, $lastTeenId, $sortBy, $sortOption);
            $data = [];
            $data['sortBy'] = Helpers::getCommunitySortByArray();
            $data['newConnections'] = [];
            if(isset($newConnections) && !empty($newConnections) && count($newConnections) > 0) {
                foreach($newConnections as $newConnection) {
                    $newConnection->t_photo  = ($newConnection->t_photo != "") ? Storage::url($this->teenagerThumbImageUploadPath.$newConnection->t_photo) : Storage::url($this->teenagerThumbImageUploadPath."proteen-logo.png");
                    $data['newConnections'][] = $newConnection;
                }
            }
            if (isset($newConnectionsCount) && $newConnectionsCount > 10) {
                $data['loadMoreFlag'] = 1;
            } else {
                $data['loadMoreFlag'] = 0;
            }
            $data['connectionsCount'] = $this->communityRepository->getMyConnectionsCount($request->userId);
            $response['login'] = 1;
            $response['status'] = 1;
            $response['message'] = trans('appmessages.default_success_msg');
            $response['data'] = $data;

        } else {
            $response['message'] = trans('appmessages.invalid_userid_msg') . ' or ' . trans('appmessages.notvarified_user_msg');
        }
        return response()->json($response, 200);
        exit;
    }

    /* Request Params : communityMyConnections
     *  loginToken, userId, lastTeenId, sortBy, sortOption, searchText, getAllRecords
     *  Service after loggedIn user
     */
    public function communityMyConnections(Request $request)
    {
        $response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg') ] ;
        $teenager = $this->teenagersRepository->getTeenagerById($request->userId);
        if($request->userId != "" && $teenager) {
            if (isset($request->sortBy) && $request->sortBy != '' && isset($request->sortOption) && $request->sortOption != '') {
                $sortBy = Helpers::getSortByColumn($request->sortBy);
                if ($sortBy == 't_birthdate') {
                    $ageVal = Helpers::age($request->sortOption);
                    if (strpos($ageVal, '-') !== false) {
                        $sortOption = Helpers::getDateRangeByAge($ageVal);
                    } else {
                        $sortOption = $ageVal;
                    }
                } else {
                    $sortOption = $request->sortOption;
                }
            } else {
                $sortBy = '';
                $sortOption = '';
            }
            if (isset($request->searchText) && $request->searchText != '') {
                $searchText = $request->searchText;
            } else {
                $searchText = '';
            }
            if (isset($request->lastTeenId) && $request->lastTeenId != '') {
                $lastTeenId = $request->lastTeenId;
            } else {
                $lastTeenId = '';
            }
            if (isset($request->getAllRecords) && $request->getAllRecords == 1) {
                $getAllRecords = 1;
            } else {
                $getAllRecords = '';
            }
            $myConnections = $this->communityRepository->getMyConnections($request->userId, $searchText, $lastTeenId, $sortBy, $sortOption, $getAllRecords);
            $myConnectionsCount = $this->communityRepository->getMyConnectionsCount($request->userId, $searchText, $lastTeenId, $sortBy, $sortOption);
            $data = [];
            $data['sortBy'] = Helpers::getCommunitySortByArray();
            $data['myConnections'] = [];
            if(isset($myConnections) && !empty($myConnections) && count($myConnections) > 0) {
                foreach($myConnections as $myConnection) {
                    $myConnection->t_photo  = ($myConnection->t_photo != "") ? Storage::url($this->teenagerThumbImageUploadPath.$myConnection->t_photo) : Storage::url($this->teenagerThumbImageUploadPath."proteen-logo.png");
                    $data['myConnections'][] = $myConnection;
                }
            }
            if (isset($myConnectionsCount) && $myConnectionsCount > 10) {
                $data['loadMoreFlag'] = 1;
            } else {
                $data['loadMoreFlag'] = 0;
            }
            $data['connectionsCount'] = $this->communityRepository->getMyConnectionsCount($request->userId);
            $response['login'] = 1;
            $response['status'] = 1;
            $response['message'] = trans('appmessages.default_success_msg');
            $response['data'] = $data;

        } else {
            $response['message'] = trans('appmessages.invalid_userid_msg') . ' or ' . trans('appmessages.notvarified_user_msg');
        }
        return response()->json($response, 200);
        exit;
    }

    /* Request Params : sendConnectionRequest
     *  loginToken, userId, senderId, receiverId
     *  senderId is same as userId
     *  Service after loggedIn user
    */
    public function sendConnectionRequest(Request $request) {
        $response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg') ] ;
        $teenager = $this->teenagersRepository->getTeenagerById($request->userId);
        if($request->userId != "" && $teenager) {
            if($request->senderId == $request->receiverId) {
                $response['login'] = 1;
                $response['status'] = 1;
                $response['message'] = "You are already in your connection!"; 
                return response()->json($response, 200);
                exit;
            }
            $receiverTeenDetails = $this->teenagersRepository->getTeenagerById($request->receiverId);
            $connectedTeen = $this->communityRepository->checkTeenConnectionStatus($request->receiverId, $request->senderId);
            if ($connectedTeen == 2) {
                $data = array();
                $replaceArray = array();
                
                $emailTemplateContent = $this->templateRepository->getEmailTemplateDataByName('send-connection-request-to-teen');
                $connectionUniqueId = uniqid("", TRUE);
                
                $replaceArray['RECEIVER_TEEN_NAME'] = $receiverTeenDetails->t_name;
                $replaceArray['SENDER_TEEN_NAME'] = $teenager->t_name;
                $content = $this->templateRepository->getEmailContent($emailTemplateContent->et_body, $replaceArray);
                
                if (isset($emailTemplateContent) && !empty($emailTemplateContent)) {
                    $data['subject'] = $emailTemplateContent->et_subject;
                    $data['toEmail'] = $receiverTeenDetails->t_email;
                    $data['toName'] = $receiverTeenDetails->t_name." ".$receiverTeenDetails->t_lastname;
                    $data['content'] = $content;
                    $data['tc_unique_id'] = $connectionUniqueId;
                    $data['tc_sender_id'] = $teenager->id;
                    $data['tc_receiver_id'] = $receiverTeenDetails->id;
                    
                    Event::fire(new SendMail("emails.Template", $data));

                    $connectionRequestData['tc_unique_id'] = $data['tc_unique_id'];
                    $connectionRequestData['tc_sender_id'] = $data['tc_sender_id'];
                    $connectionRequestData['tc_receiver_id'] = $data['tc_receiver_id'];
                    $connectionRequestData['tc_status'] = Config::get('constant.CONNECTION_PENDING_STATUS');

                    $connectionResponse = $this->communityRepository->saveConnectionRequest($connectionRequestData, '');
                    
                    $notificationData['n_sender_id'] = $teenager->id;
                    $notificationData['n_sender_type'] = Config::get('constant.NOTIFICATION_TEENAGER');
                    $notificationData['n_receiver_id'] = $data['tc_receiver_id'];
                    $notificationData['n_receiver_type'] = Config::get('constant.NOTIFICATION_TEENAGER');
                    $notificationData['n_record_id'] = $connectionResponse->id;
                    $notificationData['n_notification_type'] = Config::get('constant.NOTIFICATION_TYPE_CONNECTION_REQUEST');
                    $notificationData['n_notification_text'] = '<strong>'.ucfirst($teenager->t_name).' '.ucfirst($teenager->t_lastname).'</strong> has sent you a connection request';
                    $this->objNotifications->insertUpdate($notificationData);

                    $response['message'] = "Connection request sent successfully!";
                } else {
                    $response['message'] = trans('validation.somethingwrong');
                }
                $response['login'] = 1;
                $response['status'] = 1;
            } else {
                $response['login'] = 1;
                $response['status'] = 1;
                $response['message'] = ($connectedTeen == 1) ? "Already in your connection!" : ($connectedTeen == 0) ? "Request already sent!" : "Something went wrong!"; 
            }
        } else {
            $response['message'] = trans('appmessages.invalid_userid_msg') . ' or ' . trans('appmessages.notvarified_user_msg');
        }
        return response()->json($response, 200);
        exit;
    }

    /* Request Params : acceptConnectionRequest
     *  loginToken, userId, record_id
     *  Service after loggedIn user
    */
    public function acceptRequest(Request $request)
    {
        $response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg') ] ;
        $teenager = $this->teenagersRepository->getTeenagerById($request->userId);
        if($request->userId != "" && $teenager) {
            if($request->recordId != ""){ 

                $recordId = $request->recordId;       
                
                $checkConnectionResponse = $this->communityRepository->checkTeenConnectionStatusById($recordId);
                
                if($checkConnectionResponse == 1)
                {
                    $response['status'] = 0;
                    $response['message'] = 'You have already accepted request';
                }
                elseif($checkConnectionResponse == 2)
                {
                    $response['status'] = 0;
                    $response['message'] = 'You have already Declined request';
                }
                elseif($checkConnectionResponse == 0)
                {
                    $updateResponse = $this->communityRepository->changeTeenConnectionStatusById($recordId,Config::get('constant.CONNECTION_ACCEPT_STATUS'));
                    if($updateResponse){
                        $response['status'] = 1;
                        $response['message'] = trans('appmessages.default_success_msg');
                    }
                    else{
                        $response['status'] = 0;
                        $response['message'] = trans('appmessages.default_error_msg');
                    }
                }
                
                $response['login'] = 1;
            }
            else {
                $response['message'] = trans('appmessages.missing_data_msg');
            }

        } else {
            $response['message'] = trans('appmessages.invalid_userid_msg') . ' or ' . trans('appmessages.notvarified_user_msg');
        }
        return response()->json($response, 200);
    }

    /* Request Params : declineConnectionRequest
     *  loginToken, userId, record_id
     *  Service after loggedIn user
    */
    public function declineRequest(Request $request)
    {
        $response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg') ] ;
        $teenager = $this->teenagersRepository->getTeenagerById($request->userId);
        if($request->userId != "" && $teenager) {
            if($request->recordId != ""){ 

                $recordId = $request->recordId;       
                
                $checkConnectionResponse = $this->communityRepository->checkTeenConnectionStatusById($recordId);
                
                if($checkConnectionResponse == 1)
                {
                    $response['status'] = 0;
                    $response['message'] = 'You have already accepted request';
                }
                elseif($checkConnectionResponse == 2)
                {
                    $response['status'] = 0;
                    $response['message'] = 'You have already Declined request';
                }
                elseif($checkConnectionResponse == 0)
                {
                    $updateResponse = $this->communityRepository->changeTeenConnectionStatusById($recordId,Config::get('constant.CONNECTION_REJECT_STATUS'));
                    if($updateResponse){
                        $response['status'] = 1;
                        $response['message'] = trans('appmessages.default_success_msg');
                    }
                    else{
                        $response['status'] = 0;
                        $response['message'] = trans('appmessages.default_error_msg');
                    }
                }
                
                $response['login'] = 1;
            }
            else {
                $response['message'] = trans('appmessages.missing_data_msg');
            }

        } else {
            $response['message'] = trans('appmessages.invalid_userid_msg') . ' or ' . trans('appmessages.notvarified_user_msg');
        }
        return response()->json($response, 200);
    }
}