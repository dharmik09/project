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
        $this->log = new Logger('api-restless-controller');
        $this->log->pushHandler(new StreamHandler(storage_path().'/logs/monolog-'.date('m-d-Y').'.log'));
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

            $teenager->progress = 23;
            $teenager->total_points = 10000;
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
            //Dummy Records
            $array = array ( 'people' => "People", 'nature' => "Nature", 'technical' => "Technical", 'creative-fine-arts' => "Creative Fine Arts", 'numerical' => "Numerical", 'computers' => "Computers", 'research' => "Research", 'performing-arts' => "Performing Arts", 'social' => "Social", 'sports' => "Sports", 'language' => "Language", 'artistic' => "Artistic", 'musical' => "Musical");
            
            $teenagerAPIData = Helpers::getTeenInterestAndStregnthDetails($request->userId);
            $teenagerInterest = isset($teenagerAPIData['APIscore']['interest']) ? $teenagerAPIData['APIscore']['interest'] : $array;
            foreach($teenagerInterest as $tiNameKey => $tiPoint) {
                $dataArray[] = (array('type' => 'interest', 'points' => rand(0,100), 'slug' => $tiNameKey, 'link' => url('teenager/interest/').'/'.$tiNameKey, 'name' => $array[$tiNameKey]) );
            }
            
            $response['login'] = 1;
            $response['status'] = 1;
            $response['message'] = trans('appmessages.default_success_msg');
            $response['data'] = $dataArray;
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
            //Dummy Records
            $array = array ( 'people' => 0, 'nature' => 0, 'technical' => 0, 'creative-fine-arts' => 0, 'numerical' => 0, 'computers' => 0, 'research' => 0, 'performing-arts' => 0, 'social' => 0, 'sports' => 0, 'language' => 0, 'artistic' => 0, 'musical' => 0);
            
            $teenagerAPIData = Helpers::getTeenInterestAndStregnthDetails($request->userId);
            $teenagerMI = isset($teenagerAPIData['APIscale']['MI']) ? $teenagerAPIData['APIscale']['MI'] : [];
            foreach($teenagerMI as $miKey => $miVal) {
                $mitName = Helpers::getMIBySlug($miKey);
                $teenagerMI[$miKey] = (array('slug' => $miKey, 'points' => 0, 'score' => $miVal, 'name' => $mitName, 'type' => Config::get('constant.MULTI_INTELLIGENCE_TYPE'), 'link_url' => url('/teenager/multi-intelligence/').'/'.Config::get('constant.MULTI_INTELLIGENCE_TYPE').'/'.$miKey));
            }

            $teenagerAptitude = isset($teenagerAPIData['APIscale']['aptitude']) ? $teenagerAPIData['APIscale']['aptitude'] : [];
            foreach($teenagerAptitude as $apptitudeKey => $apptitudeVal) {
                $aptName = Helpers::getApptitudeBySlug($apptitudeKey);
                $teenagerAptitude[$apptitudeKey] = (array('points' => 0, 'score' => $apptitudeVal, 'name' => $aptName, 'type' => Config::get('constant.APPTITUDE_TYPE'), 'link_url' => url('/teenager/multi-intelligence/').'/'.Config::get('constant.APPTITUDE_TYPE').'/'.$apptitudeKey));
            }
            $teenagerPersonality = isset($teenagerAPIData['APIscale']['personality']) ? $teenagerAPIData['APIscale']['personality'] : [];
            foreach($teenagerPersonality as $personalityKey => $personalityVal) {
                $ptName = Helpers::getPersonalityBySlug($personalityKey);
                $teenagerPersonality[$personalityKey] = (array('points' => 0, 'score' => $personalityVal, 'name' => $ptName, 'type' => Config::get('constant.PERSONALITY_TYPE'), 'link_url' => url('/teenager/multi-intelligence/').'/'.Config::get('constant.PERSONALITY_TYPE').'/'.$personalityKey));
            }

            $teenagerStrength = array_merge($teenagerAptitude, $teenagerPersonality, $teenagerMI);
            //Dummy array
            $array = array (array ('slug' => 'scientific-reasoning', 'points' => 0,'score' => '','name' => 'Scientific Reasoning','type' => 'apptitude','link_url' => 'http://local.inexture.com/teenager/multi-intelligence/apptitude/scientific-reasoning',
                        ), array ('slug' => 'verbal-reasoning', 'points' => 25,'score' => '','name' => 'Verbal Reasoning','type' => 'apptitude','link_url' => 'http://local.inexture.com/teenager/multi-intelligence/apptitude/verbal-reasoning',
                        ), array ('slug' => 'numerical-ability', 'points' => 25,'score' => '','name' => 'Numerical Ability','type' => 'apptitude','link_url' => 'http://local.inexture.com/teenager/multi-intelligence/apptitude/numerical-ability',
                        ), array ('slug' => 'logical-reasoning', 'points' => 25,'score' => '','name' => 'Logical Reasoning','type' => 'apptitude','link_url' => 'http://local.inexture.com/teenager/multi-intelligence/apptitude/logical-reasoning',
                        ), array ('slug' => 'social-ability', 'points' => 15,'score' => '','name' => 'Social Ability','type' => 'apptitude','link_url' => 'http://local.inexture.com/teenager/multi-intelligence/apptitude/social-ability',
                        ), array ('slug' => 'artistic', 'points' => 90,'score' => 'H','name' => 'Artistic','type' => 'personality','link_url' => 'http://local.inexture.com/teenager/multi-intelligence/personality/artistic',
                        ), array ('slug' => 'mechanical', 'points' => 90,'score' => 'H','name' => 'Mechanical','type' => 'personality','link_url' => 'http://local.inexture.com/teenager/multi-intelligence/personality/mechanical',
                        ), array('slug' => 'mechanical', 'points' => 90,'score' => '','name' => 'Interpersonal','type' => 'mi','link_url' => 'http://local.inexture.com/teenager/multi-intelligence/mi/interpersonal',
                        ), array ('slug' => 'mechanical', 'points' => 75,'score' => '','name' => 'Logical','type' => 'mi','link_url' => 'http://local.inexture.com/teenager/multi-intelligence/mi/logical',
                    ));
            
            $response['login'] = 1;
            $response['status'] = 1;
            $response['message'] = trans('appmessages.default_success_msg');
            $response['data'] = $array;
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
                $interestThumbImageUploadPath = $this->interestThumbImageUploadPath;
                $interest = $this->objInterest->getInterestDetailBySlug($request->interestSlug);
                if($interest) {
                    $interest->it_video = ($interest->it_video != "") ? Helpers::youtube_id_from_url($interest->it_video) : "";
                    $interest->it_logo = ($interest->it_logo != "") ? Storage::url($this->interestThumbImageUploadPath . $interest->it_logo) : Storage::url($this->interestThumbImageUploadPath . "proteen-logo.png");
                }
                $data = $interest;
                $relatedCareers = [     ['id' => 1, 'pf_name' => "Library Technicians", 'attempted' => 1, 'matched' => 'strong'], 
                                        ['id' => 2, 'pf_name' => "Mechanical Engineers", 'attempted' => 0, 'matched' => 'potential'], 
                                        ['id' => 3, 'pf_name' => "Fine Artists - Including Painters - Sculptors - and Illustrators", 'attempted' => 1, 'matched' => 'strong'],
                                        ['id' => 4, 'pf_name' => "Producers and Directors", 'attempted' => 1, 'matched' => 'unlikely'],
                                        ['id' => 5, 'pf_name' => "Dancers and Choreographers", 'attempted' => 0, 'matched' => 'potential'],
                                        ['id' => 6, 'pf_name' => "Landscape Architects", 'attempted' => 1, 'matched' => 'strong'],
                                        ['id' => 7, 'pf_name' => "Computer Software Engineers", 'attempted' => 0, 'matched' => 'potential'],
                                        ['id' => 8, 'pf_name' => "Chefs and Head Cooks", 'attempted' => 0, 'matched' => 'unlikely'],
                                        ['id' => 9, 'pf_name' => "Civil Engineers", 'attempted' => 1, 'matched' => 'unlikely'],
                                        ['id' => 10, 'pf_name' => "Electrical Engineers", 'attempted' => 0, 'matched' => 'potential'],
                                    ];
                $careersGurus = [
                                    ['id' => 1, 
                                        't_name' => "Bhavdip B Pambhar", 
                                        'points' => 20, 
                                        't_photo' => Storage::url('uploads/teenager/thumb/teenager_1514397198.jpg'), 
                                        't_uniqueid' => '593e6952632df1.14591461'
                                    ], 
                                    ['id' => 2, 
                                        't_name' => "Ronak Luhar", 
                                        'points' => 200, 
                                        't_photo' => Storage::url('uploads/teenager/thumb/teenager_1514397198.jpg'), 
                                        't_uniqueid' => '593e6952632df1.14591461'
                                    ],
                                    ['id' => 3, 
                                        't_name' => "Apurv Prajapati", 
                                        'points' => 20000, 
                                        't_photo' => Storage::url('uploads/teenager/thumb/teenager_1514397198.jpg'), 
                                        't_uniqueid' => '593e6952632df1.14591461'
                                    ]
                                ];
                $data['strong'] = 4;
                $data['potential'] = 3;
                $data['unlikely'] = 5;
                $data['related_career'] = $relatedCareers;
                $data['career_guru'] = $careersGurus;
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
    *  loginToken, userId, type, slug
    */
    public function getStrengthDetailPage(Request $request) {
        $response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg') ] ;
        $teenager = $this->teenagersRepository->getTeenagerById($request->userId);
        if($teenager) {
            $data = [];
            if($request->strengthType != "" && $request->strengthSlug != "") {
                $getStrengthTypeRelatedInfo = Helpers::getStrengthTypeRelatedInfo($request->strengthType, $request->strengthSlug);
                $data = $getStrengthTypeRelatedInfo;
                $relatedCareers = [     ['id' => 1, 'pf_name' => "Library Technicians", 'attempted' => 1, 'matched' => 'strong'], 
                                        ['id' => 2, 'pf_name' => "Mechanical Engineers", 'attempted' => 0, 'matched' => 'potential'], 
                                        ['id' => 3, 'pf_name' => "Fine Artists - Including Painters - Sculptors - and Illustrators", 'attempted' => 1, 'matched' => 'strong'],
                                        ['id' => 4, 'pf_name' => "Producers and Directors", 'attempted' => 1, 'matched' => 'unlikely'],
                                        ['id' => 5, 'pf_name' => "Dancers and Choreographers", 'attempted' => 0, 'matched' => 'potential'],
                                        ['id' => 6, 'pf_name' => "Landscape Architects", 'attempted' => 1, 'matched' => 'strong'],
                                        ['id' => 7, 'pf_name' => "Computer Software Engineers", 'attempted' => 0, 'matched' => 'potential'],
                                        ['id' => 8, 'pf_name' => "Chefs and Head Cooks", 'attempted' => 0, 'matched' => 'unlikely'],
                                        ['id' => 9, 'pf_name' => "Civil Engineers", 'attempted' => 1, 'matched' => 'unlikely'],
                                        ['id' => 10, 'pf_name' => "Electrical Engineers", 'attempted' => 0, 'matched' => 'potential'],
                                    ];
                $careersGurus = [
                                    ['id' => 1, 
                                        't_name' => "Bhavdip B Pambhar", 
                                        'points' => 20, 
                                        't_photo' => Storage::url('uploads/teenager/thumb/teenager_1514397198.jpg'), 
                                        't_uniqueid' => '593e6952632df1.14591461'
                                    ], 
                                    ['id' => 2, 
                                        't_name' => "Ronak Luhar", 
                                        'points' => 200, 
                                        't_photo' => Storage::url('uploads/teenager/thumb/teenager_1514397198.jpg'), 
                                        't_uniqueid' => '593e6952632df1.14591461'
                                    ],
                                    ['id' => 3, 
                                        't_name' => "Apurv Prajapati", 
                                        'points' => 20000, 
                                        't_photo' => Storage::url('uploads/teenager/thumb/teenager_1514397198.jpg'), 
                                        't_uniqueid' => '593e6952632df1.14591461'
                                    ]
                                ];
                $data['strong'] = 4;
                $data['potential'] = 3;
                $data['unlikely'] = 5;
                $data['related_career'] = $relatedCareers;
                $data['career_guru'] = $careersGurus;
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
            $teenagerNetwork = $this->communityRepository->getMyConnections($request->userId);
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
                
                $networkArray[] = array('id' => $network->id, 'uniqueId' => $network->t_uniqueid, 'name' => $network->t_name, 'thumbImage' => $teenagerThumbImage, 'originalImage' => $teenagerOriginalImage); 
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
}