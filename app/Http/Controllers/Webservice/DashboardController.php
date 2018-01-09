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
use App\Http\Requests\TeenagerProfileUpdateRequest;
use App\Teenagers;
use App\TeenagerLoginToken;
use App\DeviceToken;
use App\Country;
use Storage;
use Carbon\Carbon;
use Image;
use Input;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class DashboardController extends Controller
{
    public function __construct(TeenagersRepository $teenagersRepository, FileStorageRepository $fileStorageRepository)
    {
        $this->teenagersRepository = $teenagersRepository;
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
}