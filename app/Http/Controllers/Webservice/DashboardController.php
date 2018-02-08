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
        $this->log = new Logger('api-restless-controller');
        $this->log->pushHandler(new StreamHandler(storage_path().'/logs/monolog-'.date('m-d-Y').'.log'));
        $this->objProfessionWiseSubject = new ProfessionWiseSubject;
        $this->objTeenagerPromiseScore = new TeenagerPromiseScore;
        $this->subjectOriginalImageUploadPath = Config::get("constant.PROFESSION_SUBJECT_ORIGINAL_IMAGE_UPLOAD_PATH");
        $this->objProfessionSubject = new ProfessionSubject; 
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
            $teenager->recent_progress = "You advanced 7% on your last visit. Well done you!";
            
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
            $teenagerAPIMaxScore = Helpers::getTeenInterestAndStregnthMaxScore();
            if (isset($request->teenagerId) && !empty($request->teenagerId)) {
                $userId = $request->teenagerId;
            } else {
                $userId = $request->userId;
            }
            $teenagerAPIData = Helpers::getTeenInterestAndStregnthDetails($userId);
            $teenagerInterestArr = isset($teenagerAPIData['APIscore']['interest']) ? $teenagerAPIData['APIscore']['interest'] : [];
            $teenagerInterest = [];
            foreach($teenagerInterestArr as $interestKey => $interestVal){
                if ($interestVal < 1) { 
                    continue; 
                } else {
                    $itName = Helpers::getInterestBySlug($interestKey);
                    $teenItScore = $this->getTeenScoreInPercentage($teenagerAPIMaxScore['interest'][$interestKey], $interestVal);
                    $teenagerInterest[] = (array('type' => 'interest', 'points' => $teenItScore, 'slug' => $interestKey, 'link' => url('teenager/interest/').'/'.$interestKey, 'name' => $itName));
                }
            }
            
            $response['login'] = 1;
            $response['status'] = 1;
            $response['message'] = trans('appmessages.default_success_msg');
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
            $teenagerStrength = [];
            
            $teenagerAPIMaxScore = Helpers::getTeenInterestAndStregnthMaxScore();
            if (isset($request->teenagerId) && !empty($request->teenagerId)) {
                $userId = $request->teenagerId;
            } else {
                $userId = $request->userId;
            }
            $teenagerAPIData = Helpers::getTeenInterestAndStregnthDetails($userId);
            //Apptitude Array
            $teenagerAptitude = isset($teenagerAPIData['APIscore']['aptitude']) ? $teenagerAPIData['APIscore']['aptitude'] : [];
            $finalTeenagerAptitude = [];
            foreach($teenagerAptitude as $apptitudeKey => $apptitudeVal) {
                $aptName = Helpers::getApptitudeBySlug($apptitudeKey);
                $teenAptScore = $this->getTeenScoreInPercentage($teenagerAPIMaxScore['aptitude'][$apptitudeKey], $apptitudeVal);
                $teenagerStrength[] = (array('slug' => $apptitudeKey, 'points' => $teenAptScore, 'score' => $apptitudeVal, 'name' => $aptName, 'type' => Config::get('constant.APPTITUDE_TYPE'), 'link_url' => url('/teenager/multi-intelligence/').'/'.Config::get('constant.APPTITUDE_TYPE').'/'.$apptitudeKey));
            }
            //Personality Array
            $teenagerPersonality = isset($teenagerAPIData['APIscore']['personality']) ? $teenagerAPIData['APIscore']['personality'] : [];
            $finalTeenagerPersonality = [];
            foreach($teenagerPersonality as $personalityKey => $personalityVal) {
                $ptName = Helpers::getPersonalityBySlug($personalityKey);
                $teenPtScore = $this->getTeenScoreInPercentage($teenagerAPIMaxScore['personality'][$personalityKey], $personalityVal);
                $teenagerStrength[] = (array('slug' => $personalityKey, 'points' => $teenPtScore, 'score' => $personalityVal, 'name' => $ptName, 'type' => Config::get('constant.PERSONALITY_TYPE'), 'link_url' => url('/teenager/multi-intelligence/').'/'.Config::get('constant.PERSONALITY_TYPE').'/'.$personalityKey));
            }
            //MI Array
            $teenagerMI = isset($teenagerAPIData['APIscore']['MI']) ? $teenagerAPIData['APIscore']['MI'] : [];
            foreach($teenagerMI as $miKey => $miVal) {
                $mitName = Helpers::getMIBySlug($miKey);
                $teenMIScore = $this->getTeenScoreInPercentage($teenagerAPIMaxScore['MI'][$miKey], $miVal);
                $teenagerStrength[] = (array('slug' => $miKey, 'points' => $teenMIScore, 'score' => $miVal, 'name' => $mitName, 'type' => Config::get('constant.MULTI_INTELLIGENCE_TYPE'), 'link_url' => url('/teenager/multi-intelligence/').'/'.Config::get('constant.MULTI_INTELLIGENCE_TYPE').'/'.$miKey));
            }
            
            $response['login'] = 1;
            $response['status'] = 1;
            $response['message'] = trans('appmessages.default_success_msg');
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
            $response['login'] = 1;
            $response['status'] = 1;
            $response['message'] = trans('appmessages.default_success_msg');
            $response['data'] = $networkArray;
        } else {
            $response['message'] = trans('appmessages.invalid_userid_msg') . ' or ' . trans('appmessages.notvarified_user_msg');
        }
        return response()->json($response, 200);
        exit;
    }

    /* Request Params : getTeenagerCareers
    *  loginToken, userId
    *  ["match", "nomatch", "moderate"]
    */
    public function getTeenagerCareers(Request $request) {
        $response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg') ] ;
        $teenager = $this->teenagersRepository->getTeenagerById($request->userId);
        if($teenager) {
            $getTeenagerHML = Helpers::getTeenagerMatchScale($request->userId);
            $getTeenagerAttemptedProfession = $this->professionsRepository->getMyCareers($request->userId);
            if($getTeenagerAttemptedProfession) {
                foreach($getTeenagerAttemptedProfession as $key => $profession) {
                    $getTeenagerAttemptedProfession[$key]->matched = isset($getTeenagerHML[$profession->id]) ? $getTeenagerHML[$profession->id] : '';
                    $getTeenagerAttemptedProfession[$key]->attempted = 1;
                    $getTeenagerAttemptedProfession[$key]->pf_logo = ($profession->pf_logo != "") ? Storage::url(Config::get('constant.PROFESSION_ORIGINAL_IMAGE_UPLOAD_PATH').$profession->pf_logo) : Storage::url(Config::get('constant.PROFESSION_ORIGINAL_IMAGE_UPLOAD_PATH')."proteen-logo.png");
                    $getTeenagerAttemptedProfession[$key]->pf_logo_thumb = ($profession->pf_logo != "") ? Storage::url(Config::get('constant.PROFESSION_THUMB_IMAGE_UPLOAD_PATH').$profession->pf_logo) : Storage::url(Config::get('constant.PROFESSION_THUMB_IMAGE_UPLOAD_PATH')."proteen-logo.png");
                    $getTeenagerAttemptedProfession[$key]->pf_slug = $profession->pf_slug;
                }
            }
            $response['login'] = 1;
            $response['status'] = 1;
            $response['message'] = trans('appmessages.default_success_msg');
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
            $getTeenagerHML = Helpers::getTeenagerMatchScale($request->userId);
            
            $teenagerCareers = $this->professionsRepository->getMyCareers($request->userId);
            $teenagerCareersIds = (isset($teenagerCareers[0]) && count($teenagerCareers[0]) > 0) ? Helpers::getTeenagerCareersIds($request->userId)->toArray() : [];

            $getAllActiveProfessions = Helpers::getActiveProfessions();
            
            $allProfessions = [];
            if($getAllActiveProfessions) {
                foreach($getAllActiveProfessions as $key => $profession) {
                    $array = [];
                    $array['id'] = $profession->id;
                    $array['pf_name'] = $profession->pf_name;
                    $array['pf_slug'] = $profession->pf_slug;
                    $array['pf_logo'] = ($profession->pf_logo != "") ? Storage::url(Config::get('constant.PROFESSION_ORIGINAL_IMAGE_UPLOAD_PATH').$profession->pf_logo) : Storage::url(Config::get('constant.PROFESSION_ORIGINAL_IMAGE_UPLOAD_PATH')."proteen-logo.png");
                    $array['pf_logo_thumb'] = ($profession->pf_logo != "") ? Storage::url(Config::get('constant.PROFESSION_THUMB_IMAGE_UPLOAD_PATH').$profession->pf_logo) : Storage::url(Config::get('constant.PROFESSION_THUMB_IMAGE_UPLOAD_PATH')."proteen-logo.png");
                    $array['matched'] = isset($getTeenagerHML[$profession->id]) ? $getTeenagerHML[$profession->id] : '';
                    $array['attempted'] = (in_array($profession->id, $teenagerCareersIds)) ? 1 : 0;
                    $allProfessions[] = $array;
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
            $matchArr = ['potential', 'strong', 'unlikely'];
            $careerData = [];
            foreach ($relatedCareers as $career) {
                $careersArr = [];
                $careersArr['id'] = $career->id;
                $careersArr['pf_name'] = $career->pf_name;
                $careersArr['matched'] = $matchArr[rand(0,2)];
                $careersArr['attempted'] = rand(0,1);
                $careerData[] = $careersArr;
            }
            $data['strong'] = 4;
            $data['potential'] = 3;
            $data['unlikely'] = 5;
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
            $matchArr = ['potential', 'strong', 'unlikely'];
            $careerData = [];
            foreach ($relatedCareers as $career) {
                $careersArr = [];
                $careersArr['id'] = $career->id;
                $careersArr['pf_name'] = $career->pf_name;
                $careersArr['matched'] = $matchArr[rand(0,2)];
                $careersArr['attempted'] = rand(0,1);
                $careerData[] = $careersArr;
            }
            $data['strong'] = 4;
            $data['potential'] = 3;
            $data['unlikely'] = 5;
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
     *  loginToken, userId, slug, slotNo
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
}