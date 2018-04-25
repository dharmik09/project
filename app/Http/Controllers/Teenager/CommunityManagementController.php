<?php

namespace App\Http\Controllers\Teenager;

use App\Http\Controllers\Controller;
use Auth;
use Config;
use Storage;
use Input;
use App\Services\Community\Contracts\CommunityRepository;
use App\Services\Template\Contracts\TemplatesRepository;
use Mail;
use Helpers;
use App\Services\Teenagers\Contracts\TeenagersRepository;
use App\Services\Level1Activity\Contracts\Level1ActivitiesRepository;
use Redirect;
use Request;
use App\Services\Schools\Contracts\SchoolsRepository;  
use Carbon\Carbon;
use App\Events\SendMail;
use App\Notifications;
use Event;
use App\TeenagerPromiseScore;
use App\PromiseParametersMaxScore;
use App\MultipleIntelligentScale;
use App\ApptitudeTypeScale;
use App\PersonalityScale;
use App\DeviceToken;


class CommunityManagementController extends Controller {

    public function __construct(Level1ActivitiesRepository $level1ActivitiesRepository, TemplatesRepository $templateRepository, CommunityRepository $communityRepository, TeenagersRepository $teenagersRepository, SchoolsRepository $schoolsRepository) 
    {
        $this->templateRepository = $templateRepository;
        $this->communityRepository = $communityRepository;
        $this->teenagersRepository = $teenagersRepository;
        $this->schoolsRepository = $schoolsRepository;
        $this->level1ActivitiesRepository = $level1ActivitiesRepository;
        $this->objNotifications = new Notifications();
        $this->objTeenagerPromiseScore = new TeenagerPromiseScore();
        $this->objPromiseParametersMaxScore = new PromiseParametersMaxScore();
        $this->objMIScale = new MultipleIntelligentScale();
        $this->objApptitudeScale = new ApptitudeTypeScale();
        $this->objPersonalityScale = new PersonalityScale();
        $this->objDeviceToken = new DeviceToken();
    }

    public function index()
    {
        $loggedInTeen = Auth::guard('teenager')->user()->id;
        $searchConnections = Input::get('searchConnections');
        $filterBy = Input::get('filter_by');
        $filterOption = Input::get('filter_option');
        if ((isset($searchConnections) && !empty($searchConnections)) || (isset($filterOption) && !empty($filterOption) && isset($filterBy) && !empty($filterBy))) {
            if (isset($filterBy) && !empty($filterBy) && $filterBy == 't_age') {
                $filterBy = 't_birthdate';
                if (strpos($filterOption, '-') !== false) {
                    $ageArr = explode("-", $filterOption);
                    $toDate = Carbon::now()->subYears($ageArr[0]);
                    $fromDate = Carbon::now()->subYears($ageArr[1]);
                    $filterOptionArr['fromDate'] = $fromDate->format('Y-m-d');
                    $filterOptionArr['toDate'] = $toDate->format('Y-m-d');
                    $filterOption = $filterOptionArr;
                } 
            }
            $newConnections = $this->communityRepository->getNewConnections($loggedInTeen, $searchConnections, '', $filterBy, $filterOption);
            $newConnectionsCount = $this->communityRepository->getNewConnectionsCount($loggedInTeen, $searchConnections, '', $filterBy, $filterOption);
            $myConnections = $this->communityRepository->getMyConnections($loggedInTeen, $searchConnections, '', $filterBy, $filterOption);
            $myConnectionsCount = $this->communityRepository->getMyConnectionsCount($loggedInTeen, $searchConnections, '', $filterBy, $filterOption);
            return view('teenager.searchedConnections', compact('newConnections', 'myConnections', 'newConnectionsCount', 'myConnectionsCount'));

        } else {
            $newConnections = $this->communityRepository->getNewConnections($loggedInTeen, array(), '', $filterBy, $filterOption);
            $newConnectionsCount = $this->communityRepository->getNewConnectionsCount($loggedInTeen, array(), '', $filterBy, $filterOption);
            $myConnections = $this->communityRepository->getMyConnections($loggedInTeen, array(), '', $filterBy, $filterOption);
            $myConnectionsCount = $this->communityRepository->getMyConnectionsCount($loggedInTeen, array(), '', $filterBy, $filterOption);
            return view('teenager.community', compact('newConnections', 'myConnections', 'newConnectionsCount', 'myConnectionsCount'));
        }
    }

    public function getMemberDetails($uniqueId)
    {
        $teenDetails = $this->teenagersRepository->getTeenagerByUniqueId($uniqueId);
        if (isset($teenDetails) && !empty($teenDetails)) {
            if ($teenDetails->id == Auth::guard('teenager')->user()->id){
                return Redirect::to("teenager/my-profile");
            }
            if ($teenDetails->is_search_on != Config('constant.TEENAGER_PUBLIC_PROFILE_ON')) {
                return Redirect::to("teenager/home")->with('error', 'Private profile');
                exit;
            }
            
            //register user in applozic if already not available
//            if($teenDetails->is_chat_initialized == 0)
//            {
//                $teenUniqueId = $teenDetails->t_uniqueid;
//                $user_profile_thumb_image = ($teenDetails->t_photo != "" && Storage::size('uploads/teenager/thumb/'.$teenDetails->t_photo) > 0) ? Storage::url('uploads/teenager/thumb/'.$teenDetails->t_photo) : Storage::url('uploads/teenager/thumb/proteen-logo.png');            
//                //check if user is there on applozic or not
//                $postData = array('userId' => $teenUniqueId,'displayName' => $teenDetails->t_name, 'imageLink'=>$user_profile_thumb_image);
//                $jsonData = json_encode($postData);
//
//                $curlObj = curl_init();
//
//                curl_setopt($curlObj, CURLOPT_URL, 'https://apps.applozic.com/rest/ws/user/v2/create');
//                curl_setopt($curlObj, CURLOPT_RETURNTRANSFER, 1);
//                curl_setopt($curlObj, CURLOPT_SSL_VERIFYPEER, 0);
//                curl_setopt($curlObj, CURLOPT_HEADER, 0);
//                curl_setopt($curlObj, CURLOPT_HTTPHEADER, array('Content-type:application/json','Apz-AppId:'.Config::get('constant.APP_LOGIC_CHAT_API_KEY'),'Apz-Token:BASIC cHJvdGVlbmxpZmVAZ21haWwuY29tOiFQcm9UZWVubGlmZSE='));
//                curl_setopt($curlObj, CURLOPT_POST, 1);
//                curl_setopt($curlObj, CURLOPT_POSTFIELDS, $jsonData);
//
//                $result = curl_exec($curlObj);
//                $json = json_decode($result);
//                //Update user info 
//
//                $teenagerDetail['is_chat_initialized'] = 1; 
//                $update = $this->teenagersRepository->updatePaymentStatus($teenDetails->id, $teenagerDetail);            
//                exit; 
//            }
            
            $teenagerTrait = $traitAllQuestion = $this->level1ActivitiesRepository->getTeenagerTraitAnswerCount($teenDetails->id);
            $connectionStatus = $this->communityRepository->checkTeenConnectionStatusForNetworkMemberPage($teenDetails->id, Auth::guard('teenager')->user()->id);
            $myConnections = $this->communityRepository->getMyConnections($teenDetails->id);
            
            $teenagerInterest = $arraypromiseParametersMaxScoreBySlug = $teenagerStrength = $sortedMIHData = $sortedMIMData = $sortedMILData = [];                        
            
            //Get Max score for MI parameters
            $promiseParametersMaxScore = $this->objPromiseParametersMaxScore->getPromiseParametersMaxScore();
            $arraypromiseParametersMaxScore = $promiseParametersMaxScore->toArray();
            foreach($arraypromiseParametersMaxScore as $maxkey=>$maxVal){
                $arraypromiseParametersMaxScoreBySlug[$maxVal['parameter_slug']] = $maxVal;
            }            
            //Get teenager promise score 
            $teenPromiseScore = $this->objTeenagerPromiseScore->getTeenagerPromiseScore($teenDetails->id);            
            
            if(isset($teenPromiseScore) && count($teenPromiseScore) > 0)
            {
                $teenPromiseScore = $teenPromiseScore->toArray();                                
                foreach($teenPromiseScore as $paramkey=>$paramvalue)
                {                 
                    $arr = explode("_", $paramkey);
                    $first = $arr[0];
                    if ($first == 'it')
                    {
                        if($paramvalue < 1)
                        {
                            continue;
                        }
                        $teenAptScore = $this->getTeenScoreInPercentage($arraypromiseParametersMaxScoreBySlug[$paramkey]['parameter_max_score'], $paramvalue);
                        $teenagerInterest[$paramkey] = (array('score' => $teenAptScore, 'name' => $arraypromiseParametersMaxScoreBySlug[$paramkey]['parameter_name']));
                      //  $teenagerInterest[$paramkey] = (array('type' => 'interest', 'score' => $teenAptScore, 'slug' => $paramkey, 'link' => url('teenager/interest/').'/'.$paramkey, 'name' => $arraypromiseParametersMaxScoreBySlug[$paramkey]['parameter_name']));
                    }
                }
                
                foreach($teenPromiseScore as $paramkey=>$paramvalue)
                {                    
                    if (strpos($paramkey, 'apt_') !== false) { 
                        $scaleapt = $this->objApptitudeScale->calculateApptitudeHML($arraypromiseParametersMaxScoreBySlug[$paramkey]['parameter_name'], $paramvalue);
                        $teenAptScore = $this->getTeenScoreInPercentage($arraypromiseParametersMaxScoreBySlug[$paramkey]['parameter_max_score'], $paramvalue);
                        $teenagerStrength[] = (array('scale'=>$scaleapt,'slug' => $paramkey, 'score' => $teenAptScore, 'points' => $paramvalue, 'name' => $arraypromiseParametersMaxScoreBySlug[$paramkey]['parameter_name'], 'type' => Config::get('constant.APPTITUDE_TYPE'), 'link_url' => url('/teenager/multi-intelligence/').'/'.Config::get('constant.APPTITUDE_TYPE').'/'.$paramkey));
                    }elseif(strpos($paramkey, 'pt_') !== false){
                        $scalept = $this->objPersonalityScale->calculatePersonalityHML($arraypromiseParametersMaxScoreBySlug[$paramkey]['parameter_name'], $paramvalue);
                        $teenAptScore = $this->getTeenScoreInPercentage($arraypromiseParametersMaxScoreBySlug[$paramkey]['parameter_max_score'], $paramvalue);
                        $teenagerStrength[] = (array('scale'=>$scalept,'slug' => $paramkey, 'score' => $teenAptScore, 'points' => $paramvalue, 'name' => $arraypromiseParametersMaxScoreBySlug[$paramkey]['parameter_name'], 'type' => Config::get('constant.PERSONALITY_TYPE'), 'link_url' => url('/teenager/multi-intelligence/').'/'.Config::get('constant.PERSONALITY_TYPE').'/'.$paramkey));
                    }elseif(strpos($paramkey, 'mit_') !== false){
                        $scalemi = $this->objMIScale->calculateMIHML($arraypromiseParametersMaxScoreBySlug[$paramkey]['parameter_name'], $paramvalue);
                        $teenAptScore = $this->getTeenScoreInPercentage($arraypromiseParametersMaxScoreBySlug[$paramkey]['parameter_max_score'], $paramvalue);
                        $teenagerStrength[] = (array('scale'=>$scalemi,'slug' => $paramkey, 'score' => $teenAptScore, 'points' => $paramvalue, 'name' => $arraypromiseParametersMaxScoreBySlug[$paramkey]['parameter_name'], 'type' => Config::get('constant.MULTI_INTELLIGENCE_TYPE'), 'link_url' => url('/teenager/multi-intelligence/').'/'.Config::get('constant.MULTI_INTELLIGENCE_TYPE').'/'.$paramkey));
                    }
                }
                
                $finalSortedData = [];
                if (isset($teenagerStrength) && !empty($teenagerStrength)) {
                    foreach ($teenagerStrength as $key => $data) {
                        if ($data['scale'] == 'H') {
                            $sortedMIHData[] = $data;
                        }
                        if ($data['scale'] == 'M') {
                            $sortedMIMData[] = $data;
                        }
                        if ($data['scale'] == 'L') {
                            $sortedMILData[] = $data;
                        }
                    }
                    $teenagerStrength = array_merge($sortedMIHData, $sortedMIMData, $sortedMILData);
                }
            }
            
           
            //$teenagerStrength = array_merge($teenagerAptitude, $teenagerPersonality, $teenagerMI);
            $myConnectionsCount = $this->communityRepository->getMyConnectionsCount($teenDetails->id);
            $userData = Auth::guard('teenager')->user();
            $notificationData['n_sender_id'] = $userData->id;
            $notificationData['n_sender_type'] = Config::get('constant.NOTIFICATION_TEENAGER');
            $notificationData['n_receiver_id'] = $teenDetails->id;
            $notificationData['n_receiver_type'] = Config::get('constant.NOTIFICATION_TEENAGER');
            $notificationData['n_notification_type'] = Config::get('constant.NOTIFICATION_TYPE_PROFILE_VIEW');
            $notificationData['n_notification_text'] = '<strong>'.ucfirst($userData->t_name).' '.ucfirst($userData->t_lastname).'</strong> has viewed your profile';
            $recordExist = $this->objNotifications->checkIfNotificationAlreadyExist($notificationData);
            if ($recordExist && count($recordExist) > 0) {
                //$notificationData = [];
                $notificationData['created_at'] = Carbon::now();
                $notificationData['id'] = $recordExist->id;
            }
            $this->objNotifications->insertUpdate($notificationData);
            
            $androidToken = [];
            $pushNotificationData = [];
            $pushNotificationData['message'] =  isset($notificationData['n_notification_text'])?strip_tags($notificationData['n_notification_text']):'';
            $certificatePath = public_path(Config::get('constant.CERTIFICATE_PATH'));
            $userDeviceToken = $this->objDeviceToken->getDeviceTokenDetail($teenDetails->id);

            if(count($userDeviceToken)>0){
                foreach ($userDeviceToken as $key => $value) {
                    if($value->tdt_device_type == 2){
                        $androidToken[] = $value->tdt_device_token;
                    }
                    if($value->tdt_device_type == 1){
                        Helpers::pushNotificationForiPhone($value->tdt_device_token,$pushNotificationData,$certificatePath);
                    }
                }

                if(isset($androidToken) && count($androidToken) > 0)
                {
                    Helpers::pushNotificationForAndroid($androidToken,$pushNotificationData);
                }            
            }
            
            return view('teenager.networkMember', compact('teenagerTrait', 'teenDetails', 'myConnections', 'teenagerStrength', 'teenagerInterest', 'connectionStatus', 'myConnectionsCount'));
        } else {
            return Redirect::back()->with('error', 'Member not found');
        }
    }

    /**
     * Returns More community connections
     *
     * @return \Illuminate\Http\Response
     */
    public function loadMoreNewConnections()
    {
        $lastTeenId = Input::get('lastTeenId');
        $loggedInTeen = Auth::guard('teenager')->user()->id;
        $searchConnections = Input::get('searchConnections');
        $filterBy = Input::get('filter_by');
        $filterOption = Input::get('filter_option');
        if (isset($filterBy) && !empty($filterBy) && $filterBy == 't_age') {
            $filterBy = 't_birthdate';
            if (strpos($filterOption, '-') !== false) {
                $ageArr = explode("-", $filterOption);
                $toDate = Carbon::now()->subYears($ageArr[0]);
                $fromDate = Carbon::now()->subYears($ageArr[1]);
                $filterOptionArr['fromDate'] = $fromDate->format('Y-m-d');
                $filterOptionArr['toDate'] = $toDate->format('Y-m-d');
                $filterOption = $filterOptionArr;
            } 
        }
        $newConnections = $this->communityRepository->getNewConnections($loggedInTeen, $searchConnections, $lastTeenId, $filterBy, $filterOption);
        $newConnectionsCount = $this->communityRepository->getNewConnectionsCount($loggedInTeen, $searchConnections, $lastTeenId, $filterBy, $filterOption);
        return view('teenager.loadMoreNewConnections', compact('newConnections', 'newConnectionsCount'));
    }

    public function loadMoreMyConnections()
    {
        $lastTeenId = Input::get('lastTeenId');
        $loggedInTeen = Auth::guard('teenager')->user()->id;
        $searchConnections = Input::get('searchConnections');
        $filterBy = Input::get('filter_by');
        $filterOption = Input::get('filter_option');
        if (isset($filterBy) && !empty($filterBy) && $filterBy == 't_age') {
            $filterBy = 't_birthdate';
            if (strpos($filterOption, '-') !== false) {
                $ageArr = explode("-", $filterOption);
                $toDate = Carbon::now()->subYears($ageArr[0]);
                $fromDate = Carbon::now()->subYears($ageArr[1]);
                $filterOptionArr['fromDate'] = $fromDate->format('Y-m-d');
                $filterOptionArr['toDate'] = $toDate->format('Y-m-d');
                $filterOption = $filterOptionArr;
            } 
        }
        $myConnections = $this->communityRepository->getMyConnections($loggedInTeen, $searchConnections, $lastTeenId, $filterBy, $filterOption);
        $myConnectionsCount = $this->communityRepository->getMyConnectionsCount($loggedInTeen, $searchConnections, $lastTeenId, $filterBy, $filterOption);
        return view('teenager.loadMoreMyConnections', compact('myConnections', 'myConnectionsCount'));
    }

    public function sendRequest($uniqueId)
    {
        $receiverTeenDetails = $this->teenagersRepository->getTeenagerByUniqueId($uniqueId);
        $senderTeenDetails = $this->teenagersRepository->getTeenagerDetailById(Auth::guard('teenager')->user()->id);
        $connectedTeen = $this->communityRepository->checkTeenConnectionStatus($receiverTeenDetails->id, Auth::guard('teenager')->user()->id);
        if ($connectedTeen == 2) {
            $data = array();
            
            $connectionUniqueId = uniqid("", TRUE);

            $data['tc_unique_id'] = $connectionUniqueId;
            $data['tc_sender_id'] = Auth::guard('teenager')->user()->id;
            $data['tc_receiver_id'] = $receiverTeenDetails->id;

            $connectionRequestData['tc_unique_id'] = $data['tc_unique_id'];
            $connectionRequestData['tc_sender_id'] = $data['tc_sender_id'];
            $connectionRequestData['tc_receiver_id'] = $data['tc_receiver_id'];
            $connectionRequestData['tc_status'] = Config::get('constant.CONNECTION_PENDING_STATUS');

            $response = $this->communityRepository->saveConnectionRequest($connectionRequestData, '');

            $userData = Auth::guard('teenager')->user();
            
            $notificationData['n_sender_id'] = $userData->id;
            $notificationData['n_sender_type'] = Config::get('constant.NOTIFICATION_TEENAGER');
            $notificationData['n_receiver_id'] = $data['tc_receiver_id'];
            $notificationData['n_receiver_type'] = Config::get('constant.NOTIFICATION_TEENAGER');
            $notificationData['n_record_id'] = $response->id;
            $notificationData['n_notification_type'] = Config::get('constant.NOTIFICATION_TYPE_CONNECTION_REQUEST');
            $notificationData['n_notification_text'] = '<strong>'.ucfirst($userData->t_name).' '.ucfirst($userData->t_lastname).'</strong> has sent you a connection request';
            $notificationExist = $this->objNotifications->checkIfConnectionNotitficationExist($notificationData);
            if ($notificationExist && !empty($notificationExist)) { 
                $notificationData['id'] = $notificationExist->id;
                $notificationData['created_at'] = Carbon::now();
            }
            $this->objNotifications->insertUpdate($notificationData);

            $androidToken = [];
            $pushNotificationData = [];
            $pushNotificationData['message'] = (isset($notificationData['n_notification_text']) && !empty($notificationData['n_notification_text'])) ? strip_tags($notificationData['n_notification_text']) : '';
            $certificatePath = public_path(Config::get('constant.CERTIFICATE_PATH'));
            $userDeviceToken = $this->objDeviceToken->getDeviceTokenDetail($data['tc_receiver_id']);

            if(count($userDeviceToken)>0){
                foreach ($userDeviceToken as $key => $value) {
                    if($value->tdt_device_type == 2){
                        $androidToken[] = $value->tdt_device_token;
                    }
                    if($value->tdt_device_type == 1){
                        Helpers::pushNotificationForiPhone($value->tdt_device_token,$pushNotificationData,$certificatePath);
                    }
                }

                if(isset($androidToken) && count($androidToken) > 0)
                {
                    Helpers::pushNotificationForAndroid($androidToken,$pushNotificationData);
                }
    
            }


            return Redirect::back()->with('success', 'Connection request sent successfully');

        } else {
            return Redirect::back()->with('error', 'Request already sent');
        }
    }

    public function getSubFilter()
    {
        $filterData = array();
        $filterOption = Input::get('filter_option');
        if ($filterOption == 't_school') {
            $filterData = $this->schoolsRepository->getApprovedSchools();
        } else if ($filterOption == 't_gender') {
            $filterData = Helpers::gender();
        } else if ($filterOption == 't_age') {
            $filterData = Helpers::age();
        } else {
            $filterData = array();
        }
        return view('teenager.communitySubFilter', compact('filterData', 'filterOption'));
    }

    //Calculate teenager strength and interest score percentage
    public function getTeenScoreInPercentage($maxScore, $teenScore) 
    {
        if ($teenScore > $maxScore) {
            $teenScore = $maxScore;
        }
        $mul = 100*$teenScore;
        $percentage = $mul/$maxScore;
        return round($percentage);
    }

    public function acceptRequest($id)
    {
        $response = $this->communityRepository->checkTeenConnectionStatusById($id); /*echo "<pre>"; print_r($response); exit;*/
        if($response == 1){
            return Redirect::back()->with('error', 'You have already accepted request');
        }
        elseif($response == 2){
            return Redirect::back()->with('error', 'You have already Declined request');
        }elseif($response == 0){
            $this->communityRepository->changeTeenConnectionStatusById($id,Config::get('constant.CONNECTION_ACCEPT_STATUS'));
            return Redirect::back()->with('success', 'Request accepted successfully');
        }
    }

    public function declineRequest($id)
    {
        $response = $this->communityRepository->checkTeenConnectionStatusById($id); /*echo "<pre>"; print_r($response); exit;*/
        if($response == 1){
            return Redirect::back()->with('error', 'You have already accepted request');
        }
        elseif($response == 2){
            return Redirect::back()->with('error', 'You have already Declined request');
        }elseif($response == 0){
            $this->communityRepository->changeTeenConnectionStatusById($id,Config::get('constant.CONNECTION_REJECT_STATUS'));
            return Redirect::back()->with('success', 'Request declined successfully');
        }
    }

    public function loadMoreMemberConnections()
    {
        $lastTeenId = Input::get('lastTeenId');
        $loggedInTeen = Input::get('teenId');
        $myConnections = $this->communityRepository->getMyConnections($loggedInTeen, array(), $lastTeenId);
        $myConnectionsCount = $this->communityRepository->getMyConnectionsCount($loggedInTeen, array(), $lastTeenId);
        return view('teenager.loadMoreMyConnections', compact('myConnections', 'myConnectionsCount'));   
    }
}
