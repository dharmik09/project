<?php

namespace App\Http\Controllers\Webservice;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Services\Teenagers\Contracts\TeenagersRepository;
use App\Services\Level1Activity\Contracts\Level1ActivitiesRepository;
use App\Services\Community\Contracts\CommunityRepository;
use Illuminate\Support\Facades\Auth;
use App\Teenagers;
use Helpers;
use App\Country;
use Config;
use Storage;
use Input;
use Image;
use Carbon\Carbon;

class TeenagerController extends Controller
{
    public function __construct(CommunityRepository $communityRepository, TeenagersRepository $teenagersRepository, Level1ActivitiesRepository $level1ActivitiesRepository)
    {
        $this->teenagersRepository = $teenagersRepository;
        $this->communityRepository = $communityRepository;
        $this->level1ActivitiesRepository = $level1ActivitiesRepository;
        $this->teenOriginalImageUploadPath = Config::get('constant.TEEN_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->teenThumbImageUploadPath = Config::get('constant.TEEN_THUMB_IMAGE_UPLOAD_PATH');
        $this->sponsorOriginalImageUploadPath = Config::get('constant.SPONSOR_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->sponsorThumbImageUploadPath = Config::get('constant.SPONSOR_THUMB_IMAGE_UPLOAD_PATH');
        $this->teenProfileImageUploadPath = Config::get('constant.TEEN_PROFILE_IMAGE_UPLOAD_PATH');
        $this->teenThumbImageHeight = Config::get('constant.TEEN_THUMB_IMAGE_HEIGHT');
        $this->teenThumbImageWidth = Config::get('constant.TEEN_THUMB_IMAGE_WIDTH');
        $this->objCountry = new Country();
        
    }

    /* Request Params : getActiveTeenagers
    *  loginToken, userId, pageNo
    */
    public function getActiveTeenagers(Request $request)
    {
		$response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg') ] ;
    	$teenager = $this->teenagersRepository->getTeenagerDetailById($request->userId);
        if($request->userId != "" && $teenager) {
            $pageNo = ($request->pageNo != "" && $request->pageNo > 0) ? $request->pageNo : 0;
            $activeTeenagers = Helpers::getActiveTeenagersForCoupon($request->userId, $pageNo);
            $teenagerArray = [];
            if (!empty($activeTeenagers)) {
                $teenagersArr = $activeTeenagers->toArray();
                foreach ($teenagersArr as $key => $data) {
                    if ($data['id'] != $request->userId) {
                        $teenagers['name'] = $data['t_name'];
                        $teenagers['email'] = $data['t_email'];
                        $teenagers['lastname'] = $data['t_lastname'];
                        $teenagers['nickname'] = $data['t_nickname'];
                        $teenagers['uniqueid'] = $data['t_uniqueid'];
                        $teenagers['id'] = $data['id'];
                        if ($data['t_photo'] != '' && Storage::size($this->teenOriginalImageUploadPath . $data['t_photo']) > 0) {
                            $teenagers['photo'] = Storage::url($this->teenOriginalImageUploadPath . $data['t_photo']);
                        } else {
                            $teenagers['photo'] = Storage::url($this->teenOriginalImageUploadPath . 'proteen-logo.png');
                        }
                        $teenagerArray[] = $teenagers;
                    }
                }
            }
            $nextPageExist = Helpers::getActiveTeenagersForCoupon($request->userId, $pageNo + 1);
            if (isset($nextPageExist) && count($nextPageExist) > 0) {
                $response['pageNo'] = $pageNo;
            } else {
                $response['pageNo'] = '-1';
            }
            $response['status'] = 1;
            $response['login'] = 1;
            //$response['pageNo'] = $pageNo;
            $response['message'] = trans('appmessages.default_success_msg');
            $response['data']['users'] = $teenagerArray;
        } else {
            $response['message'] = trans('appmessages.missing_data_msg');
        }
        return response()->json($response, 200);
        exit;
    }

    /* Request Params : getActiveTeenagersBySearch
    *  loginToken, userId, searchText, pageNo, 
    */
    public function getActiveTeenagersBySearch(Request $request)
    {
        $response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg') ] ;
        $teenager = $this->teenagersRepository->getTeenagerDetailById($request->userId);
        if($request->userId != "" && $teenager) {
            $page = ($request->pageNo != "" && $request->pageNo > 0) ? $request->pageNo : 0;
            $searchText = $request->searchText;
            $searchArray = explode(",", $searchText);
            $objTeenager = new Teenagers();
            $activeTeenagers = $objTeenager->getActiveTeenagersForCouponSearch($request->userId, $page, $searchArray);
            $teenagerArray = [];
            if (!empty($activeTeenagers)) {
                $teenagerArr = $activeTeenagers->toArray();
                foreach ($teenagerArr as $key => $data) {
                    if ($data['id'] != $request->userId) {
                        $teenagers['name'] = $data['t_name'];
                        $teenagers['email'] = $data['t_email'];
                        $teenagers['nickname'] = $data['t_nickname'];
                        $teenagers['lastname'] = $data['t_lastname'];
                        $teenagers['uniqueid'] = $data['t_uniqueid'];
                        $teenagers['id'] = $data['id'];
                        if ($data['t_photo'] != '' && Storage::size($this->teenOriginalImageUploadPath . $data['t_photo']) > 0) {
                            $teenagers['photo'] = Storage::url($this->teenOriginalImageUploadPath . $data['t_photo']);
                        } else {
                            $teenagers['photo'] = Storage::url($this->teenOriginalImageUploadPath . 'proteen-logo.png');
                        }
                        $teenagerArray[] = $teenagers;
                    }
                }
            }
            $nextPageExist = $objTeenager->getActiveTeenagersForCouponSearch($request->userId, $page + 1, $searchArray);
            if (isset($nextPageExist) && count($nextPageExist) > 0) {
                $response['pageNo'] = $page;
            } else {
                $response['pageNo'] = '-1';
            }
            $response['status'] = 1;
            $response['login'] = 1;
            //$response['pageNo'] = $page;
            $response['message'] = trans('appmessages.default_success_msg');
            $response['data']['users'] = $teenagerArray;
        } else {
            $response['message'] = trans('appmessages.missing_data_msg');
        }
        return response()->json($response, 200);
        exit;
    }

    /* Request Params : getTeenagerMemberDetail
    *  loginToken, userId, teenagerId
    *  teenagerId is the id for another teen. For network member detail page
    */
    public function getTeenagerMemberDetail(Request $request)
    {
        $response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg') ] ;
        $teenager = $this->teenagersRepository->getTeenagerDetailById($request->userId);
        if($request->userId != "" && $teenager) {
            $networkTeenager = $this->teenagersRepository->getTeenagerDetailById($request->teenagerId);
            if($networkTeenager) {
                $networkTeenager->t_birthdate = (isset($networkTeenager->t_birthdate) && $networkTeenager->t_birthdate != '0000-00-00') ? Carbon::parse($networkTeenager->t_birthdate)->format('d/m/Y') : '';
                //Teenager Image
                $networkTeenager->t_photo_thumb = "";
                if ($networkTeenager->t_photo != '') {
                    $networkTeenager->t_photo_thumb = Storage::url($this->teenThumbImageUploadPath . $networkTeenager->t_photo);
                    $networkTeenager->t_photo = Storage::url($this->teenOriginalImageUploadPath . $networkTeenager->t_photo);
                }
                //Country info
                $networkTeenager->c_code = ( isset(Country::getCountryDetail($networkTeenager->t_country)->c_code) ) ? Country::getCountryDetail($networkTeenager->t_country)->c_code : "";
                $networkTeenager->c_name = ( isset(Country::getCountryDetail($networkTeenager->t_country)->c_name) ) ? Country::getCountryDetail($networkTeenager->t_country)->c_name : "";
                $networkTeenager->country_id = $networkTeenager->t_country;
                
                //Get Location Area
                if ($networkTeenager->t_location != "") {
                    $getCityArea = $networkTeenager->t_location;
                } else if($networkTeenager->t_pincode != "") {
                    $getLocation = file_get_contents('http://maps.googleapis.com/maps/api/geocode/json?address='.$networkTeenager->t_pincode.'&sensor=true');
                    $getCityArea = ( isset(json_decode($getLocation)->results[0]->address_components[1]->long_name) && json_decode($getLocation)->results[0]->address_components[1]->long_name != "" ) ? json_decode($getLocation)->results[0]->address_components[1]->long_name : "Default";
                } else {
                    $getCityArea = ( $networkTeenager->c_name != "" ) ? $networkTeenager->c_name : "Default";
                }
                $networkTeenager->t_about_info = (isset($networkTeenager->t_about_info) && !empty($networkTeenager->t_about_info)) ? $networkTeenager->t_about_info : "";
                $response['teenagerLocationArea'] = $getCityArea. " Area";
                $response['profileComplete'] = "Profile 62% complete";
                $response['facebookUrl'] = "https://facebook.com";
                $response['googleUrl'] = "https://google.com";
                $response['connectionsCount'] = $this->communityRepository->getMyConnectionsCount($request->teenagerId);
                
                //Connection Status 0,1,2 :: 0 -> pendding, 1 -> connected, 2->rejected
                //checkTeenConnectionStatus($receiverId, $senderId) :: Here teenagerId => receiverId and userId => senderId
                $response['connectionStatus'] = $this->communityRepository->checkTeenConnectionStatus($request->teenagerId, $request->userId);
                $teenagerTrait = $traitAllQuestion = $this->level1ActivitiesRepository->getTeenagerTraitAnswerCount($request->teenagerId);
                $arrayData = [];
                if(isset($teenagerTrait[0]) && $teenagerTrait) {
                    foreach($teenagerTrait as $traitValue) {
                        $array = [];
                        $array['options_text'] = $traitValue->options_text;
                        $array['options_count'] = $traitValue->options_count; 
                        $arrayData[] = $array;
                    }
                }
                $response['traitData'] = $arrayData;
                $response['status'] = 1;
                $response['login'] = 1;
                $response['message'] = trans('appmessages.default_success_msg');
                $response['data'] = $networkTeenager;

            } else {
                $response['login'] = 1;
                $response['status'] = 1;
                $response['message'] = "Network Teenager not found or not verified!";
            }
        } else {
            $response['login'] = 1;
            $response['status'] = 1;
            $response['message'] = trans('appmessages.missing_data_msg');
        }
        return response()->json($response, 200);
        exit;
    }

    /* Request Params : getMemberConnections
    *  loginToken, userId, teenagerId
    */
    public function getMemberConnections(Request $request) {
        $response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg') ] ;
        $teenager = $this->teenagersRepository->getTeenagerById($request->userId);
        if($teenager) {
            $connections = [];
            if (isset($request->lastTeenId) && $request->lastTeenId != '') {
                $lastTeenId = $request->lastTeenId;
            } else {
                $lastTeenId = '';
            }
            $memberConnections = $this->communityRepository->getMyConnections($request->teenagerId, array(), $lastTeenId);
            foreach ($memberConnections as $connection) {
                //Teenager thumb Image
                $teenagerThumbImage = '';
                if ($connection->t_photo != '' && Storage::size($this->teenThumbImageUploadPath . $connection->t_photo) > 0) {
                    $teenagerThumbImage = Storage::url($this->teenThumbImageUploadPath . $connection->t_photo);
                } else {
                    $teenagerThumbImage = Storage::url($this->teenThumbImageUploadPath . 'proteen-logo.png');
                }
                //Teenager original image
                $teenagerOriginalImage = '';
                if ($connection->t_photo != '' && Storage::size($this->teenOriginalImageUploadPath . $connection->t_photo) > 0) {
                    $teenagerOriginalImage = Storage::url($this->teenOriginalImageUploadPath . $connection->t_photo);
                } else {
                    $teenagerOriginalImage = Storage::url($this->teenOriginalImageUploadPath . 'proteen-logo.png');
                }
                
                $connections[] = array('id' => $connection->id, 'uniqueId' => $connection->t_uniqueid, 'name' => $connection->t_name, 'thumbImage' => $teenagerThumbImage, 'originalImage' => $teenagerOriginalImage, 'coins' => $connection->t_coins); 
            }
            $response['login'] = 1;
            $response['status'] = 1;
            $response['message'] = trans('appmessages.default_success_msg');
            $response['data'] = $connections;
        } else {
            $response['message'] = trans('appmessages.invalid_userid_msg') . ' or ' . trans('appmessages.notvarified_user_msg');
        }
        return response()->json($response, 200);
        exit;
    }
}