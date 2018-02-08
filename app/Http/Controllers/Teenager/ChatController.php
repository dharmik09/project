<?php

namespace App\Http\Controllers\Teenager;

use App\Http\Controllers\Controller;
use Auth;
use Config;
use Storage;
use Input;
use App\Services\Community\Contracts\CommunityRepository;
use Mail;
use Helpers;
use App\Services\Teenagers\Contracts\TeenagersRepository;
use Redirect;
use Request;
use App\Services\Schools\Contracts\SchoolsRepository;  
use App\Notifications;
use App\ForumQuestion;
use Carbon\Carbon;  

class ChatController extends Controller {

    public function __construct(CommunityRepository $communityRepository, TeenagersRepository $teenagersRepository, SchoolsRepository $schoolsRepository) 
    {
        $this->communityRepository = $communityRepository;
        $this->teenagersRepository = $teenagersRepository;
        $this->schoolsRepository = $schoolsRepository;
        $this->objNotifications = new Notifications();
        $this->objForumQuestion = new ForumQuestion();
    }

    /*
     * Chat page 
     */
    public function index()
    {
        $loggedInTeen = Auth::guard('teenager')->user()->id;  
        $user_profile_thumb_image = (Auth::guard('teenager')->user()->t_photo != "" && Storage::size('uploads/teenager/thumb/'.Auth::guard('teenager')->user()->t_photo) > 0) ? Storage::url('uploads/teenager/thumb/'.Auth::guard('teenager')->user()->t_photo) : Storage::url('uploads/teenager/thumb/proteen-logo.png');
        $record = 0;
        $notificationData = $this->objNotifications->getNotificationsByUserTypeAnsId(Config::get('constant.NOTIFICATION_TEENAGER'),$loggedInTeen,$record);
        $limit = 3;
        $skip = 0;
        $forumQuestionData = $this->objForumQuestion->getAllForumQuestionAndAnswersWithTeenagerData($limit,$skip);
        return view('teenager.chat',compact('user_profile_thumb_image','notificationData','forumQuestionData'));        
    }

    public function getPageWiseNotification()
    {
        $loggedInTeen = Auth::guard('teenager')->user()->id;
        $pageNo = Input::get('page_no');
        $record = $pageNo * 10;
        $notificationData = $this->objNotifications->getNotificationsByUserTypeAnsId(Config::get('constant.NOTIFICATION_TEENAGER'),$loggedInTeen,$record);
        $view = view('teenager.basic.notifications',compact('notificationData'));
        $response['notificationCount'] = count($notificationData);
        $response['notifications'] = $view->render();
        $response['pageNo'] = $pageNo+1;
        return $response;        
    }

    public function deleteNotification()
    {
        $id = Input::get('id');
        $response = $this->objNotifications->deleteNotificationById($id);
        return $response;        
    }

    public function getUnreadNotificationCount()
    {
        $loggedInTeen = Auth::guard('teenager')->user()->id;
        $response = $this->objNotifications->getUnreadNotificationByUserId($loggedInTeen);
        return $response;        
    }

    public function changeNotificationStatus()
    {
        $id = Input::get('notification_id');
        return $this->objNotifications->ChangeNotificationsReadStatus($id,Config::get('constant.NOTIFICATION_STATUS_READ'));
    }

    /*
     * Get chat users and pass json data
     */
    public function getChatUsers()
    { 
        $loggedInTeen = Auth::guard('teenager')->user()->id; 
        $myConnections = $this->communityRepository->getMyConnections($loggedInTeen); 
        if(isset($myConnections) && !empty($myConnections))
        {
            foreach($myConnections as $key=>$myConnection)
            {
                if(isset($myConnection->t_photo) && $myConnection->t_photo != '' && Storage::size(Config::get('constant.TEEN_THUMB_IMAGE_UPLOAD_PATH').$myConnection->t_photo)) {
                    $teenImage = Config::get('constant.TEEN_THUMB_IMAGE_UPLOAD_PATH').$myConnection->t_photo;
                } else {
                    $teenImage = Config::get('constant.TEEN_THUMB_IMAGE_UPLOAD_PATH').'proteen-logo.png';
                }
                $teenagerArr[] = array('userId'=>$myConnection->t_uniqueid,'displayName'=>$myConnection->t_name,'imageLink'=>Storage::url($teenImage),"imageData"=>"Base64 encoded image data");
            }
        }
        
        $jsonTeenagerArr = json_encode($teenagerArr);
        echo $jsonTeenagerArr;
        exit;
    }
    
    /*
     * Register user in applozic using curl request for chat
     */
    public function registerUserInAppLozic() 
    {        
        if(Auth::guard('teenager')->user()->is_chat_initialized == 0){
            $teenUniqueId = Auth::guard('teenager')->user()->t_uniqueid;
            $user_profile_thumb_image = (Auth::guard('teenager')->user()->t_photo != "" && Storage::size('uploads/teenager/thumb/'.Auth::guard('teenager')->user()->t_photo) > 0) ? Storage::url('uploads/teenager/thumb/'.Auth::guard('teenager')->user()->t_photo) : Storage::url('uploads/teenager/thumb/proteen-logo.png');            
            //check if user is there on applozic or not
            $postData = array('userId' => $teenUniqueId,'displayName' => Auth::guard('teenager')->user()->t_name, 'imageLink'=>$user_profile_thumb_image);
            $jsonData = json_encode($postData);

            $curlObj = curl_init();

            curl_setopt($curlObj, CURLOPT_URL, 'https://apps.applozic.com/rest/ws/user/v2/create');
            curl_setopt($curlObj, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curlObj, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($curlObj, CURLOPT_HEADER, 0);
            curl_setopt($curlObj, CURLOPT_HTTPHEADER, array('Content-type:application/json','Apz-AppId:'.Config::get('constant.APP_LOGIC_CHAT_API_KEY'),'Apz-Token:BASIC cHJvdGVlbmxpZmVAZ21haWwuY29tOiFQcm9UZWVubGlmZSE='));
            curl_setopt($curlObj, CURLOPT_POST, 1);
            curl_setopt($curlObj, CURLOPT_POSTFIELDS, $jsonData);

            $result = curl_exec($curlObj);
            $json = json_decode($result);
            //Update user info 
            
            $teenagerDetail['is_chat_initialized'] = 1; 
            $update = $this->teenagersRepository->updatePaymentStatus(Auth::guard('teenager')->user()->id, $teenagerDetail);            
            exit;            
        }
        else{            
            exit;
        }
    }
}
