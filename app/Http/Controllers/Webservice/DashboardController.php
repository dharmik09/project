<?php

namespace App\Http\Controllers\Webservice;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Helpers;
use Config;
use App\Services\Teenagers\Contracts\TeenagersRepository;
use App\Services\FileStorage\Contracts\FileStorageRepository;
use App\Services\Professions\Contracts\ProfessionsRepository;
use App\Http\Requests\TeenagerProfileUpdateRequest;
use App\Teenagers;
use App\TeenagerLoginToken;
use App\DeviceToken;
use App\Country;
use Storage;
use Carbon\Carbon;
use Image;
use Input;
use App\Interest;
use App\MultipleIntelligent;
use App\Apptitude;
use App\Personality;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use App\Services\Community\Contracts\CommunityRepository;
use App\CareerMapping;
use App\ProfessionWiseSubject;
use App\TeenagerPromiseScore;
use App\ProfessionSubject;
use App\PromiseParametersMaxScore;
use App\MultipleIntelligentScale;
use App\ApptitudeTypeScale;
use App\PersonalityScale;
use App\PaidComponent;
use App\DeductedCoins;

class DashboardController extends Controller
{
    public function __construct(ProfessionsRepository $professionsRepository, TeenagersRepository $teenagersRepository, FileStorageRepository $fileStorageRepository, CommunityRepository $communityRepository)
    {
        $this->teenagersRepository = $teenagersRepository;
        $this->professionsRepository = $professionsRepository;
        $this->fileStorageRepository = $fileStorageRepository;
        $this->objTeenagerLoginToken = new TeenagerLoginToken();
        $this->objDeviceToken = new DeviceToken();
        $this->objCountry = new Country();
        $this->teenOriginalImageUploadPath = Config::get('constant.TEEN_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->teenThumbImageUploadPath = Config::get('constant.TEEN_THUMB_IMAGE_UPLOAD_PATH');
        $this->sponsorOriginalImageUploadPath = Config::get('constant.SPONSOR_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->sponsorThumbImageUploadPath = Config::get('constant.SPONSOR_THUMB_IMAGE_UPLOAD_PATH');
        $this->teenProfileImageUploadPath = Config::get('constant.TEEN_PROFILE_IMAGE_UPLOAD_PATH');
        $this->teenThumbImageHeight = Config::get('constant.TEEN_THUMB_IMAGE_HEIGHT');
        $this->teenThumbImageWidth = Config::get('constant.TEEN_THUMB_IMAGE_WIDTH');
        //Interest Management
        $this->objInterest = new Interest;
        $this->interestOriginalImageUploadPath = Config::get('constant.INTEREST_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->interestThumbImageUploadPath = Config::get('constant.INTEREST_THUMB_IMAGE_UPLOAD_PATH');
        //MI management
        $this->objMultipleIntelligent = new MultipleIntelligent();
        $this->objApptitude = new Apptitude();
        $this->objPersonality = new Personality();
        $this->miThumbImageUploadPath = Config::get('constant.MI_THUMB_IMAGE_UPLOAD_PATH');
        $this->apptitudeThumbImageUploadPath = Config::get('constant.APPTITUDE_THUMB_IMAGE_UPLOAD_PATH');
        $this->personalityThumbImageUploadPath = Config::get('constant.PERSONALITY_THUMB_IMAGE_UPLOAD_PATH');
        $this->communityRepository = $communityRepository;
        $this->objCareerMapping = new CareerMapping;
        $this->log = new Logger('api-dashboard-controller');
        $this->log->pushHandler(new StreamHandler(storage_path().'/logs/monolog-'.date('m-d-Y').'.log'));
        $this->objProfessionWiseSubject = new ProfessionWiseSubject;
        $this->objTeenagerPromiseScore = new TeenagerPromiseScore;
        $this->subjectOriginalImageUploadPath = Config::get("constant.PROFESSION_SUBJECT_ORIGINAL_IMAGE_UPLOAD_PATH");
        $this->objProfessionSubject = new ProfessionSubject;
        $this->objPromiseParametersMaxScore = new PromiseParametersMaxScore();
        $this->objMIScale = new MultipleIntelligentScale();
        $this->objApptitudeScale = new ApptitudeTypeScale();
        $this->objPersonalityScale = new PersonalityScale();
        $this->objPaidComponent = new PaidComponent;
        $this->objDeductedCoins = new DeductedCoins;
    }

    /* Request Params : getDashboardDetail
    *  loginToken, userId
    */
    public function getDashboardDetail(Request $request) {
        $response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg') ] ;
        $teenager = $this->teenagersRepository->getTeenagerById($request->userId);
        if($teenager) {
            $teenager->t_birthdate = (isset($teenager->t_birthdate) && $teenager->t_birthdate != '0000-00-00') ? Carbon::parse($teenager->t_birthdate)->format('d/m/Y') : '';
            //Teenager Image
            $teenager->t_photo_thumb = "";
            if ($teenager->t_photo != '') {
                $teenager->t_photo_thumb = Storage::url($this->teenThumbImageUploadPath . $teenager->t_photo);
                $teenager->t_photo = Storage::url($this->teenOriginalImageUploadPath . $teenager->t_photo);
            }
            
            $teenager->c_code = ( isset(Country::getCountryDetail($teenager->t_country)->c_code) ) ? Country::getCountryDetail($teenager->t_country)->c_code : "";
            $teenager->c_name = ( isset(Country::getCountryDetail($teenager->t_country)->c_name) ) ? Country::getCountryDetail($teenager->t_country)->c_name : "";
            $teenager->country_id = $teenager->t_country;

            $basicBoosterPoint = $this->teenagersRepository->getTeenagerBasicBooster($teenager->id);
            $profileComplete = Helpers::calculateProfileComplete($teenager->id);
            $teenager->progress = $profileComplete;
            $teenager->total_points = ( isset($basicBoosterPoint['Total']) && $basicBoosterPoint['Total'] > 0) ? $basicBoosterPoint['Total'] : 0;

            $profileComplete =  Helpers::calculateProfileComplete($teenager->id);
            if($teenager->t_logout_progress == 0 && $teenager->t_progress_calculations == 0){
                $teenager->recent_progress = 'Welcome! Now complete your profile';
            }
            else{
                $teenager->recent_progress = "You advanced " . $teenager->t_progress_calculations . "% on your last visit";
            }
            //$teenager->recent_progress = "You advanced 7% on your last visit. Well done you!";
            
            $response['login'] = 1;
            $response['status'] = 1;
            $response['message'] = trans('appmessages.default_success_msg');
            $response['data'] = $teenager;
        } else {
            $response['message'] = trans('appmessages.invalid_userid_msg') . ' or ' . trans('appmessages.notvarified_user_msg');
        }
        return response()->json($response, 200);
        exit;
    }

    /* Request Params : getInterestDetail
    *  loginToken, userId
    */
    public function getInterestDetail(Request $request) {
        $response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg') ] ;
        $teenager = $this->teenagersRepository->getTeenagerById($request->userId);
        if($teenager) {
            if (isset($request->teenagerId) && !empty($request->teenagerId)) {
                $userId = $request->teenagerId;
            } else {
                $userId = $request->userId;
            }
            
            $teenagerInterest = $arraypromiseParametersMaxScoreBySlug = [];                        
            //Get Max score for MI parameters
            $promiseParametersMaxScore = $this->objPromiseParametersMaxScore->getPromiseParametersMaxScore();
            $arraypromiseParametersMaxScore = $promiseParametersMaxScore->toArray();
            foreach($arraypromiseParametersMaxScore as $maxkey=>$maxVal){
                $arraypromiseParametersMaxScoreBySlug[$maxVal['parameter_slug']] = $maxVal;
            }            
            //Get teenager promise score 
            $teenPromiseScore = $this->objTeenagerPromiseScore->getTeenagerPromiseScore($userId);
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
                        $teenagerInterest[] = (array('type' => 'interest', 'points' => $teenAptScore, 'slug' => $paramkey, 'link' => url('teenager/interest/').'/'.$paramkey, 'name' => $arraypromiseParametersMaxScoreBySlug[$paramkey]['parameter_name']));
                    }
                }
            }            
            $response['login'] = 1;
            $response['status'] = 1;
            if(isset($teenagerInterest) && !empty($teenagerInterest)){
                $response['message'] = trans('appmessages.default_success_msg');
            }else{
                $response['message'] = trans('appmessages.nointerestfoundmsg');
            }            
            $response['data'] = $teenagerInterest;
        } else {
            $response['message'] = trans('appmessages.invalid_userid_msg') . ' or ' . trans('appmessages.notvarified_user_msg');
        }
        return response()->json($response, 200);
        exit;
    }

    /* Request Params : getStrengthDetail
    *  loginToken, userId
    */
    public function getStrengthDetail(Request $request) {
        $response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg') ] ;
        $teenager = $this->teenagersRepository->getTeenagerById($request->userId);
        if($teenager) {
            $teenagerStrength = $arraypromiseParametersMaxScoreBySlug = $sortedMIHData = $sortedMIMData = $sortedMILData = [];
            
            //Get Max score for MI parameters
            $promiseParametersMaxScore = $this->objPromiseParametersMaxScore->getPromiseParametersMaxScore();
            $arraypromiseParametersMaxScore = $promiseParametersMaxScore->toArray();
            foreach($arraypromiseParametersMaxScore as $maxkey=>$maxVal){
                $arraypromiseParametersMaxScoreBySlug[$maxVal['parameter_slug']] = $maxVal;
            }
            
            if (isset($request->teenagerId) && !empty($request->teenagerId)) {
                $userId = $request->teenagerId;
            } else {
                $userId = $request->userId;
            }
            
            //Get teenager promise score 
            $teenPromiseScore = $this->objTeenagerPromiseScore->getTeenagerPromiseScore($userId);
            if(isset($teenPromiseScore) && count($teenPromiseScore) > 0)
            {
                $teenPromiseScore = $teenPromiseScore->toArray();                
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
            $response['login'] = 1;
            $response['status'] = 1;
            if(isset($teenagerStrength) && !empty($teenagerStrength)){
                $response['message'] = trans('appmessages.default_success_msg');
            }else{
                $response['message'] = trans('appmessages.nostrengthfoundmsg');
            }        
            
            $response['data'] = $teenagerStrength;
        } else {
            $response['message'] = trans('appmessages.invalid_userid_msg') . ' or ' . trans('appmessages.notvarified_user_msg');
        }
        return response()->json($response, 200);
        exit;
    }

    /* Request Params : getInterestDetailPage
    *  loginToken, userId, interestType, interestSlug
    */
    public function getInterestDetailPage(Request $request) {
        $response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg') ] ;
        $teenager = $this->teenagersRepository->getTeenagerById($request->userId);
        if($teenager) {
            $data = [];
            if($request->interestType != "" && $request->interestSlug != "") {
                if(substr($request->interestSlug, 0, 3) === "it_") {
                    $interest = $this->objInterest->getInterestDetailBySlug($request->interestSlug);
                    if ($interest) {
                        $interestThumbImageUploadPath = $this->interestThumbImageUploadPath;
                        $interest->it_video = ($interest->it_video != "") ? Helpers::youtube_id_from_url($interest->it_video) : "WoelVRjFO4A";
                        $data['id'] = $interest->id;
                        $data['title'] = $interest->it_name;
                        $data['slug'] = $interest->it_slug;
                        if ($interest->it_logo != "" && Storage::size($this->interestThumbImageUploadPath . $interest->it_logo) > 0 ) {
                            $data['logo'] = Storage::url($this->interestThumbImageUploadPath . $interest->it_logo);
                        } else {
                            $data['logo'] = Storage::url($this->interestThumbImageUploadPath . 'proteen-logo.png');
                        }
                        $data['video'] = $interest->it_video;
                        $data['details'] = $interest->it_description;
                    }    
                } else {
                    $subjectDetails = $this->objProfessionSubject->getSubjectDetailsBySlug($request->interestSlug);
                    if ($subjectDetails) {
                        $data['id'] = $subjectDetails->id;
                        $data['title'] = $subjectDetails->ps_name;
                        $data['slug'] = $subjectDetails->ps_slug;
                        if ($subjectDetails->ps_image != "" && Storage::size($this->subjectOriginalImageUploadPath . $subjectDetails->ps_image) > 0 ) {
                            $data['logo'] = Storage::url($this->subjectOriginalImageUploadPath . $subjectDetails->ps_image);
                        } else {
                            $data['logo'] = Storage::url($this->subjectOriginalImageUploadPath . 'proteen-logo.png');
                        }
                        $data['video'] = "";
                        $data['details'] = "";
                    }
                }
                $response['message'] = trans('appmessages.default_success_msg');
                $response['login'] = 1;
            } else {
                $response['message'] = trans('appmessages.missing_data_msg');
            }
            $response['login'] = 1;
            $response['status'] = 1;
            $response['data'] = $data;
        } else {
            $response['message'] = trans('appmessages.invalid_userid_msg') . ' or ' . trans('appmessages.notvarified_user_msg');
        }
        return response()->json($response, 200);
        exit;
    }

    /* Request Params : getStrengthDetailPage
    *  loginToken, userId, strengthType, strengthSlug
    */
    public function getStrengthDetailPage(Request $request) {
        $response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg') ] ;
        $teenager = $this->teenagersRepository->getTeenagerById($request->userId);
        if($teenager) {
            $data = [];
            if($request->strengthType != "" && $request->strengthSlug != "") {
                $getStrengthTypeRelatedInfo = Helpers::getStrengthTypeRelatedInfo($request->strengthType, $request->strengthSlug);
                
                if($getStrengthTypeRelatedInfo) {
                    $getStrengthTypeRelatedInfo['details'] = ( isset($getStrengthTypeRelatedInfo['description']) ) ? $getStrengthTypeRelatedInfo['description'] : "";
                    $getStrengthTypeRelatedInfo['video'] = ( isset($getStrengthTypeRelatedInfo['video']) && $getStrengthTypeRelatedInfo['video'] != "" ) ? $getStrengthTypeRelatedInfo['video'] : "WoelVRjFO4A";
                }
                unset($getStrengthTypeRelatedInfo['description']);
                $data = $getStrengthTypeRelatedInfo;
                $response['message'] = trans('appmessages.default_success_msg');
                $response['login'] = 1;
            } else {
                $response['message'] = trans('appmessages.missing_data_msg');
            }
            $response['login'] = 1;
            $response['status'] = 1;
            $response['data'] = $data;
        } else {
            $response['message'] = trans('appmessages.invalid_userid_msg') . ' or ' . trans('appmessages.notvarified_user_msg');
        }
        return response()->json($response, 200);
        exit;
    }

    /* Request Params : getNetworkDetails
    *  loginToken, userId
    */
    public function getNetworkDetails(Request $request) {
        $response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg') ] ;
        $teenager = $this->teenagersRepository->getTeenagerById($request->userId);
        if($teenager) {
            $networkArray = [];
            $teenagerNetwork = $this->communityRepository->getMyConnections($request->userId, array(), '', '', '', 1);
            foreach ($teenagerNetwork as $network) {
                //Teenager thumb Image
                $teenagerThumbImage = '';
                if ($network->t_photo != '' && Storage::size($this->teenThumbImageUploadPath . $network->t_photo) > 0) {
                    $teenagerThumbImage = Storage::url($this->teenThumbImageUploadPath . $network->t_photo);
                } else {
                    $teenagerThumbImage = Storage::url($this->teenThumbImageUploadPath . 'proteen-logo.png');
                }
                //Teenager original image
                $teenagerOriginalImage = '';
                if ($network->t_photo != '' && Storage::size($this->teenOriginalImageUploadPath . $network->t_photo) > 0) {
                    $teenagerOriginalImage = Storage::url($this->teenOriginalImageUploadPath . $network->t_photo);
                } else {
                    $teenagerOriginalImage = Storage::url($this->teenOriginalImageUploadPath . 'proteen-logo.png');
                }
                
                $networkArray[] = array('id' => $network->id, 'uniqueId' => $network->t_uniqueid, 'name' => $network->t_name, 'lastname' => $network->t_lastname, 'thumbImage' => $teenagerThumbImage, 'originalImage' => $teenagerOriginalImage); 
            }
            if(isset($networkArray) && count($networkArray) > 0)
            {
                $response['message'] = trans('appmessages.default_success_msg');
            }else{
                $response['message'] = trans('appmessages.nonetworkfoundmsg');
            }
            $response['login'] = 1;
            $response['status'] = 1;            
            $response['data'] = $networkArray;
        } else {
            $response['message'] = trans('appmessages.invalid_userid_msg') . ' or ' . trans('appmessages.notvarified_user_msg');
        }
        return response()->json($response, 200);
        exit;
    }

    /* Request Params : getTeenagerCareers
    *  loginToken, userId, lastCareerId, getAllRecords
    *  ["match", "nomatch", "moderate"]
    */
    public function getTeenagerCareers(Request $request) {
        $response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg') ];
        $teenager = $this->teenagersRepository->getTeenagerById($request->userId);
        if($teenager) {
            if (isset($request->lastCareerId) && !empty($request->lastCareerId)) {
                $lastCareerId = $request->lastCareerId;
            } else {
                $lastCareerId = "";
            }
            if (isset($request->getAllRecords) && $request->getAllRecords == 1) {
                $getTeenagerAttemptedProfession = $this->professionsRepository->getMyCareers($request->userId, $lastCareerId);
            } else {
                $getTeenagerAttemptedProfession = $this->professionsRepository->getMyCareersSlotWise($request->userId, $lastCareerId);
            }
            $getTeenagerHML = Helpers::getTeenagerMatchScale($request->userId);
            $myCareersCount = $this->professionsRepository->getMyCareersCount($request->userId, $lastCareerId);
            if (isset($myCareersCount) && $myCareersCount > 10) {
                $response['loadMoreFlag'] = 1;
            } else {
                $response['loadMoreFlag'] = 0;
            }
            $careersCount = $this->professionsRepository->getMyCareersCount($request->userId);
            if($getTeenagerAttemptedProfession) {
                foreach($getTeenagerAttemptedProfession as $key => $profession) {
                    $getTeenagerAttemptedProfession[$key]->matched = isset($getTeenagerHML[$profession->id]) ? $getTeenagerHML[$profession->id] : '';
                    $getTeenagerAttemptedProfession[$key]->attempted = 1;
                    $getTeenagerAttemptedProfession[$key]->pf_logo = ($profession->pf_logo != "") ? Storage::url(Config::get('constant.PROFESSION_ORIGINAL_IMAGE_UPLOAD_PATH').$profession->pf_logo) : Storage::url(Config::get('constant.PROFESSION_ORIGINAL_IMAGE_UPLOAD_PATH')."proteen-logo.png");
                    $getTeenagerAttemptedProfession[$key]->pf_logo_thumb = ($profession->pf_logo != "") ? Storage::url(Config::get('constant.PROFESSION_THUMB_IMAGE_UPLOAD_PATH').$profession->pf_logo) : Storage::url(Config::get('constant.PROFESSION_THUMB_IMAGE_UPLOAD_PATH')."proteen-logo.png");
                    $getTeenagerAttemptedProfession[$key]->pf_slug = $profession->pf_slug;
                }
            }
            if(isset($getTeenagerAttemptedProfession) && count($getTeenagerAttemptedProfession) > 0)
            {
                $response['message'] = trans('appmessages.default_success_msg');
            }else{
                $response['message'] = trans('appmessages.nomycareerfoundmsg');
            }
            $response['login'] = 1;
            $response['status'] = 1;
            
            $response['careersCount'] = (isset($careersCount)) ? $careersCount : 0;
            $response['data'] = $getTeenagerAttemptedProfession;
        } else {
            $response['message'] = trans('appmessages.invalid_userid_msg') . ' or ' . trans('appmessages.notvarified_user_msg');
        }
        return response()->json($response, 200);
        exit;
    }

    /* Request Params : getTeenagerCareersConsider
    *  loginToken, userId
    *  ["match", "nomatch", "moderate"]
    */
    public function getTeenagerCareersConsider(Request $request) {
        $response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg') ] ;
        $teenager = $this->teenagersRepository->getTeenagerById($request->userId);
        if($teenager) {
            $response['availableCoins'] = $teenager->t_coins;
            //Career consider section coins consumption details
            $componentsCareerConsider = $this->objPaidComponent->getPaidComponentsData(Config::get('constant.CAREER_TO_CONSIDER'));
            $deductedCoinsCareerConsider = $this->objDeductedCoins->getDeductedCoinsDetailByIdForLS($request->userId, $componentsCareerConsider->id, 1);
            $remainingDaysForCareerConsider = 0;
            if (!empty($deductedCoinsCareerConsider[0])) {
                $remainingDaysForCareerConsider = Helpers::calculateRemainingDays($deductedCoinsCareerConsider[0]->dc_end_date);
            }
            $careerConsiderCoinsDetails = [];
            $careerConsiderCoinsDetails['componentId'] = $componentsCareerConsider->id;
            $careerConsiderCoinsDetails['componentName'] = Config::get('constant.CAREER_TO_CONSIDER');
            $careerConsiderCoinsDetails['componentCoins'] = $componentsCareerConsider->pc_required_coins;
            $careerConsiderCoinsDetails['remainingDays'] = $remainingDaysForCareerConsider;
            $response['careerConsiderCoinsDetails'] = $careerConsiderCoinsDetails;

            if($remainingDaysForCareerConsider == 0) {
                $response['login'] = 1;
                $response['status'] = 1;
                $response['data'] = [];
                $response['message'] = "Please consume your procoins to view your career suggestions!";
                return response()->json($response, 200);
                exit;
            }

            $getTeenagerHML = Helpers::getTeenagerMatchScale($request->userId);
            if(!$getTeenagerHML) {
                $response['login'] = 1;
                $response['status'] = 1;
                $response['data'] = [];
                $response['message'] = "Build your profile to know careers to consider!";
                return response()->json($response, 200);
                exit;
            }

            $teenagerCareers = $this->professionsRepository->getMyCareers($request->userId);
            $teenagerCareersIds = (isset($teenagerCareers[0]) && count($teenagerCareers[0]) > 0) ? Helpers::getTeenagerCareersIds($request->userId)->toArray() : [];
            $getAllActiveProfessions = Helpers::getActiveProfessions();
            
            $allProfessions = [];
            $match = $nomatch = $moderate = $matchHigh = $matchLow = $moderateHigh = $moderateLow = $nomatchHign = $nomatchLow = $matchSecondHigh = $matchSecondLow = $moderateSecondHigh = $moderateSecondLow = [];

            if($getAllActiveProfessions) {
                foreach($getAllActiveProfessions as $key => $profession) {
                    $array = [];
                    $array['id'] = $profession->id;
                    $array['pf_name'] = $profession->pf_name;
                    $array['pf_slug'] = $profession->pf_slug;
                    $array['pf_logo'] = ($profession->pf_logo != "") ? Storage::url(Config::get('constant.PROFESSION_ORIGINAL_IMAGE_UPLOAD_PATH').$profession->pf_logo) : Storage::url(Config::get('constant.PROFESSION_ORIGINAL_IMAGE_UPLOAD_PATH')."proteen-logo.png");
                    $array['pf_logo_thumb'] = ($profession->pf_logo != "") ? Storage::url(Config::get('constant.PROFESSION_THUMB_IMAGE_UPLOAD_PATH').$profession->pf_logo) : Storage::url(Config::get('constant.PROFESSION_THUMB_IMAGE_UPLOAD_PATH')."proteen-logo.png");
                    $array['matched'] = isset($getTeenagerHML[$profession->id]) ? $getTeenagerHML[$profession->id] : '';
                    $array['attempted'] = rand(0,1);
                    $array['star_career'] = (in_array($profession->id, $teenagerCareersIds)) ? 1 : 0;
                    //$allProfessions[] = $array;
                    
                    // if($array['matched'] == "match") {
                    //     $match[] = $array;
                    // } else if($array['matched'] == "nomatch") {
                    //     $nomatch[] = $array;
                    // } else if($array['matched'] == "moderate") {
                    //     $moderate[] = $array;
                    // } else {
                    //     $notSetArray[] = $array;
                    // }

                    if($array['matched'] == "match") {
                        $getCareerMappingFromSystem = Helpers::getCareerMappingFromSystemByProfession($profession->id);
                        if($getCareerMappingFromSystem) {
                            $mappingArray = [];
                            unset($getCareerMappingFromSystem->created_at);
                            unset($getCareerMappingFromSystem->updated_at);
                            unset($getCareerMappingFromSystem->deleted);
                            
                            $mappingArray = array_count_values((array)$getCareerMappingFromSystem);
                            $match[$profession->id] = $array;
                            
                            if(isset($mappingArray['H']) && isset($mappingArray['M']) && $mappingArray['M'] > 0 && $mappingArray['H'] > 0) { 
                                if($mappingArray['H'] > 0) {
                                    $matchHigh[$profession->id] = $mappingArray['H'];
                                } else {
                                    $matchLow[$profession->id] = $mappingArray['M'];
                                }       
                            } else {
                                if(isset($mappingArray['H']) && $mappingArray['H'] > 0) {
                                    $matchSecondHigh[$profession->id] = $mappingArray['H'];
                                } else {
                                    $matchSecondLow[$profession->id] = $mappingArray['L'];
                                }
                            }
                        }
                    } else if($array['matched'] == "nomatch") {
                        $nomatch[$profession->id] = $array;
                    } else if($array['matched'] == "moderate") {
                        $getCareerMappingFromSystem = Helpers::getCareerMappingFromSystemByProfession($profession->id);
                        if($getCareerMappingFromSystem) {
                            $mappingArray = [];
                            unset($getCareerMappingFromSystem->created_at);
                            unset($getCareerMappingFromSystem->updated_at);
                            unset($getCareerMappingFromSystem->deleted);
                            
                            $mappingArray = array_count_values((array)$getCareerMappingFromSystem);
                            $moderate[$profession->id] = $array;
                            if(isset($mappingArray['H']) && isset($mappingArray['M']) && $mappingArray['M'] > 0 && $mappingArray['H'] > 0) {
                                if($mappingArray['H'] > 0) {
                                    $moderateHigh[$profession->id] = $mappingArray['H'];
                                } else {
                                    $moderateLow[$profession->id] = $mappingArray['M'];
                                }
                            } else {
                                if(isset($mappingArray['H']) && $mappingArray['H'] > 0) {
                                    $moderateSecondHigh[$profession->id] = $mappingArray['H'];
                                } else {
                                    $moderateSecondLow[$profession->id] = $mappingArray['L'];
                                }
                            }
                        }
                    } else {
                        $notSetArray[$profession->id] = $array;
                    }
                }

                // if(count($match) < 1 && count($moderate) < 1 && count($nomatch) > 0) {
                //     $allProfessions = $nomatch;
                // } else if(count($match) > 0 || count($moderate) > 0) {
                //     $allProfessions = array_merge($match, $moderate);
                // } else {
                //     $allProfessions = $notSetArray;
                // }

                if(count($match) < 1 && count($moderate) < 1 && count($nomatch) > 0) {
                    $finalArray = [];
                    $allProfessions = $finalArray;
                } else if(count($match) > 0 || count($moderate) > 0) {
                    if( count($matchHigh) > 0 || count($moderateHigh) > 0 || count($moderateLow) > 0 || count($matchLow) > 0 ) {
                        arsort($matchHigh);
                        arsort($matchLow);
                        arsort($moderateHigh);
                        arsort($moderateLow);
                         
                        $allProfessionsTemp = $match + $moderate;
                        $finalArray1 = $finalArray2 = [];
                        $mergeMatchSortArray = $matchHigh + $matchLow;
                        
                        foreach($mergeMatchSortArray as $keyH => $sortArray) {
                            if(isset($allProfessionsTemp[$keyH])) {
                                $finalArray1[] =  $allProfessionsTemp[$keyH]; 
                            }
                        }
                      
                        $mergeModerateSortArray = $moderateHigh + $moderateLow;
                        foreach($mergeModerateSortArray as $keyM => $sortArray) {
                            if(isset($allProfessionsTemp[$keyM])) {
                                $finalArray2[] =  $allProfessionsTemp[$keyM]; 
                            }
                        }
                       
                        $finalArray = array_merge($finalArray1,$finalArray2);
                        
                    } else {
                        arsort($matchSecondHigh);
                        arsort($matchSecondLow);
                        arsort($moderateSecondHigh);
                        arsort($moderateSecondLow);
                       
                        $mergeAllSortArray = $matchSecondHigh + $matchSecondLow + $moderateSecondHigh + $moderateSecondLow;
                        $allProfessionsTemp = $match + $moderate;
                        foreach($mergeAllSortArray as $key => $sortArray) {
                            if(isset($allProfessionsTemp[$key])) {
                                $finalArray[] =  $allProfessionsTemp[$key]; 
                            }
                        }
                    }
                    $allProfessions = $finalArray;
                } else {
                    $allProfessions = $notSetArray;
                }
            }

            $response['login'] = 1;
            $response['status'] = 1;
            $response['message'] = trans('appmessages.default_success_msg');
            $response['data'] = $allProfessions;
        } else {
            $response['message'] = trans('appmessages.invalid_userid_msg') . ' or ' . trans('appmessages.notvarified_user_msg');
        }
        return response()->json($response, 200);
        exit;
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

    /* Request Params : getStrengthPageRelatedCareers
     *  loginToken, userId, strengthSlug, lastCareerId
     */
    public function getStrengthPageRelatedCareers(Request $request) {
        $response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg') ] ;
        $teenager = $this->teenagersRepository->getTeenagerById($request->userId);
        if($teenager) {
            $data = [];
            $careersDetails = Helpers::getCareerMapColumnName();
            if (isset($request->lastCareerId) && !empty($request->lastCareerId)) {
                $lastCareerId = $request->lastCareerId;
            } else {
                $lastCareerId = '';
            }
            $relatedCareers = $this->objCareerMapping->getRelatedCareers($careersDetails[$request->strengthSlug], $lastCareerId);
            $relatedCareersCount = $this->objCareerMapping->getRelatedCareersCount($careersDetails[$request->strengthSlug], $lastCareerId);
            if (isset($relatedCareersCount) && $relatedCareersCount > Config::get('constant.RECORD_PER_PAGE')) {
                $response["seeMoreFlag"] = 1;
            } else {
                $response["seeMoreFlag"] = 0;
            }
            
            $getTeenagerHML = Helpers::getTeenagerMatchScale($request->userId);

            $careerData = [];
            $match = $nomatch = $moderate = [];

            if($relatedCareers) {
                foreach ($relatedCareers as $career) {
                    $careersArr = [];
                    $careersArr['id'] = $career->id;
                    $careersArr['pf_name'] = $career->pf_name;
                    $careersArr['pf_slug'] = $career->pf_slug;
                    $careersArr['matched'] = isset($getTeenagerHML[$career->id]) ? $getTeenagerHML[$career->id] : '';
                    $professionComplete = Helpers::getProfessionCompletePercentage($request->userId, $career->id);
                    if (isset($professionComplete) && $professionComplete == 100) {
                        $careersArr['attempted'] = Config::get('constant.PROFESSION_ATTEMPTED_FLAG');
                    } else {
                        $careersArr['attempted'] = Config::get('constant.PROFESSION_NOT_ATTEMPTED_FLAG');
                    }
                    $careerData[] = $careersArr;
                    //Counting Data
                    if($careersArr['matched'] == "match") {
                        $match[] = $careersArr;
                    } else if($careersArr['matched'] == "nomatch") {
                        $nomatch[] = $careersArr;
                    } else if($careersArr['matched'] == "moderate") {
                        $moderate[] = $careersArr;
                    } else {
                        $notSetcareersArr[] = $careersArr;
                    }
                }
            }
            
            $data['strong'] = count($match);
            $data['potential'] = count($moderate);
            $data['unlikely'] = count($nomatch);
            $data['related_career'] = $careerData;
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

    /* Request Params : getInterestPageRelatedCareers
     *  loginToken, userId, interestSlug, lastCareerId
     */
    public function getInterestPageRelatedCareers(Request $request) {
        $response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg') ] ;
        $teenager = $this->teenagersRepository->getTeenagerById($request->userId);
        if($teenager) {
            $data = [];
            if(substr($request->interestSlug, 0, 3) === "it_") {
                $subSlug = explode('it_', $request->interestSlug);
                $slug = $subSlug[1];
            } else {
                $slug = $request->interestSlug;
            }
            if (isset($request->lastCareerId) && !empty($request->lastCareerId)) {
                $lastCareerId = $request->lastCareerId;
            } else {
                $lastCareerId = '';
            }
            $relatedCareers = $this->objProfessionWiseSubject->getProfessionsBySubjectSlug($slug, $lastCareerId);
            $relatedCareersCount = $this->objProfessionWiseSubject->getProfessionsCountBySubjectSlug($slug, $lastCareerId);
            if (isset($relatedCareersCount) && $relatedCareersCount > Config::get('constant.RECORD_PER_PAGE')) {
                $response["seeMoreFlag"] = 1;
            } else {
                $response["seeMoreFlag"] = 0;
            }

            $getTeenagerHML = Helpers::getTeenagerMatchScale($request->userId);
            $careerData = [];
            $match = $nomatch = $moderate = [];

            foreach ($relatedCareers as $career) {
                $careersArr = [];
                $careersArr['id'] = $career->id;
                $careersArr['pf_name'] = $career->pf_name;
                $careersArr['pf_slug'] = $career->pf_slug;
                $careersArr['matched'] = isset($getTeenagerHML[$career->id]) ? $getTeenagerHML[$career->id] : '';
                $professionComplete = Helpers::getProfessionCompletePercentage($request->userId, $career->id);
                if (isset($professionComplete) && $professionComplete == 100) {
                    $careersArr['attempted'] = Config::get('constant.PROFESSION_ATTEMPTED_FLAG');
                } else {
                    $careersArr['attempted'] = Config::get('constant.PROFESSION_NOT_ATTEMPTED_FLAG');;
                }
                $careerData[] = $careersArr;
                //Counting Data
                if($careersArr['matched'] == "match") {
                    $match[] = $careersArr['id'];
                } else if($careersArr['matched'] == "nomatch") {
                    $nomatch[] = $careersArr['id'];
                } else if($careersArr['matched'] == "moderate") {
                    $moderate[] = $careersArr['id'];
                } else {
                    $notSetcareersArr[] = $careersArr['id'];
                }
            }
            $data['strong'] = count($match);
            $data['potential'] = count($moderate);
            $data['unlikely'] = count($nomatch);
            $data['related_career'] = $careerData;
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

    /* Request Params : getMiAndInterestPageGurusDetails
     *  loginToken, userId, slug, slot
     */
    public function getMiAndInterestPageGurusDetails(Request $request) {
        $response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg') ] ;
        $teenager = $this->teenagersRepository->getTeenagerById($request->userId);
        if($teenager) {
            $data = [];
            $reasoningGurus = $this->objTeenagerPromiseScore->getTeenagersWithHighestPromiseScore($request->slug, $request->slot);
            $nextReasoningGurus = $this->objTeenagerPromiseScore->getTeenagersWithHighestPromiseScore($request->slug, $request->slot + 1);
            if (isset($nextReasoningGurus) && count($nextReasoningGurus) > 0) {
                $response['seeMoreFlag'] = $request->slot;
            } else {
                $response['seeMoreFlag'] = -1;
            }
            foreach ($reasoningGurus as $guru) {
                $teenArr = [];
                $teenArr['id'] = $guru->id;
                $teenArr['t_name'] = $guru->t_name;
                //Teenager thumb Image
                $teenagerThumbImage = '';
                if ($guru->t_photo != '' && Storage::size($this->teenThumbImageUploadPath . $guru->t_photo) > 0) {
                    $teenagerThumbImage = Storage::url($this->teenThumbImageUploadPath . $guru->t_photo);
                } else {
                    $teenagerThumbImage = Storage::url($this->teenThumbImageUploadPath . 'proteen-logo.png');
                }
                $teenArr['t_photo'] = $teenagerThumbImage;
                $teenArr['t_uniqueid'] = $guru->t_uniqueid;
                $teenArr['t_coins'] = $guru->t_coins;
                $basicBoosterPoint = Helpers::getTeenagerBasicBooster($guru->id);
                $teenArr['points'] = (isset($basicBoosterPoint['total']) && $basicBoosterPoint['total'] > 0) ? number_format($basicBoosterPoint['total']) : 0;
                $data[] = $teenArr;
            }
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
    
    /* Request Params : getDashboardAdvertisements
     *  loginToken, userId
     */
    public function getDashboardAdvertisements(Request $request) {
        $response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg') ] ;
        $teenager = $this->teenagersRepository->getTeenagerById($request->userId);
        if($teenager) {
            $data = [];
            $adsDetails = Helpers::getAds($request->userId);
            $advertisements = [];
            foreach ($adsDetails as $ad) {
                if ($ad['sizeType'] == 4) {
                    if ($ad['image'] != '') {
                        $ad['image'] = Storage::url(Config::get('constant.SA_ORIGINAL_IMAGE_UPLOAD_PATH') . $ad['image']);
                    } else {
                        $ad['image'] = Storage::url(Config::get('constant.SA_ORIGINAL_IMAGE_UPLOAD_PATH') . 'proteen-logo.png');
                    }
                    $advertisements[] = $ad;
                }
            }
            $response['login'] = 1;
            $response['status'] = 1;
            $response['message'] = trans('appmessages.default_success_msg');
            $response['data'] = $advertisements;
            //Store log in System
            $this->log->info('Retrieve dashboard page advertisements', array('userId' => $request->userId));
        } else {
            $response['message'] = trans('appmessages.invalid_userid_msg') . ' or ' . trans('appmessages.notvarified_user_msg');
        }
        return response()->json($response, 200);
        exit;
    }
    
}