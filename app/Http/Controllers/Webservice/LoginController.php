<?php

namespace App\Http\Controllers\Webservice;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Helpers;
use Config;
use App\Services\Teenagers\Contracts\TeenagersRepository;
use App\Teenagers;
use App\Country;
use App\TeenagerLoginToken;
use App\DeviceToken;
use Storage;
use Carbon\Carbon;

class LoginController extends Controller
{
    public function __construct(TeenagersRepository $teenagersRepository)
    {
        $this->teenagersRepository = $teenagersRepository;
        $this->objTeenagerLoginToken = new TeenagerLoginToken();
        $this->objDeviceToken = new DeviceToken();
        $this->teenOriginalImageUploadPath = Config::get('constant.TEEN_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->teenThumbImageUploadPath = Config::get('constant.TEEN_THUMB_IMAGE_UPLOAD_PATH');
        $this->sponsorOriginalImageUploadPath = Config::get('constant.SPONSOR_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->sponsorThumbImageUploadPath = Config::get('constant.SPONSOR_THUMB_IMAGE_UPLOAD_PATH');
    }

    /* Request Params : login
    *  email, password, deviceId, deviceType, pushToken //pushToken is optional
    *  No loginToken required because it's call without loggedin user
    */
    public function login(Request $request)
    {
		$response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg') ] ;
    	if($request->email != "" && $request->password != "" && $request->deviceId != "" && $request->deviceType != "") {
    		if (!filter_var($request->email, FILTER_VALIDATE_EMAIL) && is_numeric($request->email) && $request->email > 0 && $request->email == round($request->email, 0)) {
    			$teenager = $this->teenagersRepository->getTeenagerByMobile($request->email);
    			if(!$teenager) {
    				$response['message'] = trans('appmessages.invalid_mobile_user_msg');
    				return response()->json($response, 200);
    			}
    		} else {
    			$teenager = $this->teenagersRepository->getTeenagerDetailByEmailId($request->email);
    			if(!$teenager) {
    				$response['message'] = trans('appmessages.usernotexistwithemail');
    				return response()->json($response, 200);
    			}
    		}
            if($teenager) {
    			if(isset($teenager->t_isverified) && $teenager->t_isverified == 1) {
    				if (Auth::guard('teenager')->attempt(['t_email' => $teenager->t_email, 'password' => $request->password, 'deleted' => 1])) {
    					//Get/Format Sponsor Detail
                        $teenager->t_sponsors = $this->teenagersRepository->getSelfSponserListData($teenager->id);
                        if (isset($teenager->t_sponsors)) {
                            foreach ($teenager->t_sponsors as $sponsor) {
                                $sponsorPhoto = ($sponsor->sp_logo != "") ? $sponsor->sp_logo : "proteen-logo.png";
                                $sponsor->sp_logo = Storage::url($this->sponsorOriginalImageUploadPath . $sponsorPhoto);
                                $sponsor->sp_logo_thumb = Storage::url($this->sponsorThumbImageUploadPath . $sponsorPhoto);
                            }
                        }
                        
    					//Teenager Image
    					$teenager->t_photo_thumb = "";
    					if ($teenager->t_photo != '') {
    						$teenager->t_photo_thumb = Storage::url($this->teenThumbImageUploadPath . $teenager->t_photo);
    						$teenager->t_photo = Storage::url($this->teenOriginalImageUploadPath . $teenager->t_photo);
    					}
                        //Country related info
                        $teenager->c_code = ( isset(Country::getCountryDetail($teenager->t_country)->c_code) ) ? Country::getCountryDetail($teenager->t_country)->c_code : "";
                        $teenager->c_name = ( isset(Country::getCountryDetail($teenager->t_country)->c_name) ) ? Country::getCountryDetail($teenager->t_country)->c_name : "";
                        $teenager->country_id = $teenager->t_country;
                        $teenager->t_birthdate = (isset($teenager->t_birthdate) && $teenager->t_birthdate != '0000-00-00') ? Carbon::parse($teenager->t_birthdate)->format('d/m/Y') : '';
            
                        //Save Login Token Data
    					$loginDetail['tlt_teenager_id'] = $teenager->id;
                        $loginDetail['tlt_login_token'] = base64_encode($teenager->t_email.':'.$teenager->t_uniqueid);
                        $loginDetail['tlt_device_id'] = $request->deviceId;
                        $userTokenDetails = $this->objTeenagerLoginToken->saveTeenagerLoginDetail($loginDetail);
                        //Save Device Token Data
                        $saveData['tdt_user_id'] = $teenager->id;
                        $saveData['tdt_device_token'] = ($request->pushToken != "") ? $request->pushToken : base64_encode($teenager->t_email.':'.$teenager->t_uniqueid);
                        $saveData['tdt_device_type'] = $request->deviceType;
                        $saveData['tdt_device_id'] = $request->deviceId;
                        $userDeviceDetails = $this->objDeviceToken->saveDeviceToken($saveData);

                        $response['loginToken'] = base64_encode($teenager->t_email.':'.$teenager->t_uniqueid);
                        $response['message'] = trans('appmessages.default_success_msg');
    					$response['status'] = 1;
    					$response['login'] = 1;
    					$response['data'] = $teenager;
    				} else {
    					$response['message'] = trans('appmessages.invalid_user_pwd_msg');
    				}
    			} else {
    				$response['message'] = trans('appmessages.not_verified_login');
    			}
    		} else {
    			$response['message'] = trans('appmessages.invalid_user_pwd_msg');
    		}
    	} else {
    		$response['message'] = trans('appmessages.missing_data_msg');
    	}
    	
    	return response()->json($response, 200);
    	exit;
    }

    /* Request Params : userLogout
    *  userId, token, deviceId
    *  No loginToken required because it's call without loggedin user
    */
    public function userLogout(Request $request) {
        $response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg') ] ;
        if($request->userId != "" && $request->deviceId != "" && $request->token != "") {
            $checkuserexist = $this->teenagersRepository->checkActiveTeenager($request->userId);
            if ($checkuserexist) {
                $userId = $request->userId;
                $token = $request->token;
                $result = $this->objDeviceToken->deleteDeviceToken($request->userId, $token);
                $return = $this->objTeenagerLoginToken->deletedTeenagerLoginDetail($request->userId, $request->deviceId);
                $response['status'] = 1;
                $response['message'] = trans('appmessages.default_success_msg');
            } else {
                $response['message'] = trans('appmessages.invalid_userid_msg') . ' or ' . trans('appmessages.notvarified_user_msg');
            }
        } else {
            $response['message'] = trans('appmessages.missing_data_msg');
        }

        return response()->json($response, 200);
        exit;
    }
    
    /* Request Params : saveUpdatedDeviceToken
    *  userId, pushToken, deviceId, deviceType
    *  No loginToken required because it's call without loggedin user
    */
    public function saveUpdatedDeviceToken(Request $request) {
        $response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg') ] ;
        if($request->userId != "" && $request->deviceId != "" && $request->pushToken != "" && $request->deviceType != "") {
            $checkuserexist = $this->teenagersRepository->checkActiveTeenager($request->userId);
            if ($checkuserexist) {
                $saveData['tdt_user_id'] = ($request->userId != '') ? $request->userId : '0';
                $saveData['tdt_device_token'] = $request->pushToken;
                $saveData['tdt_device_type'] = $request->deviceType;
                $saveData['tdt_device_id'] = $request->deviceId;
                $result = $this->objDeviceToken->saveDeviceToken($saveData);
                
                $teenagerDetail = $this->teenagersRepository->getTeenagerById($request->userId);
                $loginDetail = [];
                $loginDetail['tlt_teenager_id'] = $teenagerDetail->id;
                $loginDetail['tlt_login_token'] = base64_encode($teenagerDetail->t_email.':'.$teenagerDetail->t_uniqueid);
                $loginDetail['tlt_device_id'] = $request->deviceId;
                $userLoginDetails = $this->objTeenagerLoginToken->saveTeenagerLoginDetail($loginDetail);
                $response['loginToken'] = base64_encode($teenagerDetail->t_email.':'.$teenagerDetail->t_uniqueid);

                $response['status'] = 1;
                $response['login'] = 1;
                $response['message'] = trans('appmessages.default_success_msg');
            } else {
                $response['message'] = trans('appmessages.invalid_userid_msg') . ' or ' . trans('appmessages.notvarified_user_msg');
            }
        } else {
            $response['message'] = trans('appmessages.missing_data_msg');
        }
        return response()->json($response, 200);
        exit;
    }

    /* Request Params : updateTeenagerLoginToken
    *  userId, deviceId, pushToken, deviceType
    *  No loginToken required because it's call without loggedin user
    */
    public function updateTeenagerLoginToken(Request $request) {
        $response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg') ] ;
        if($request->userId != "" && $request->deviceId != "") {
            $checkuserexist = $this->teenagersRepository->checkActiveTeenager($request->userId);
            if ($checkuserexist) {
                $userLoginDetails = $this->objTeenagerLoginToken->updateTeenagerLoginDetail($request->userId, $request->deviceId);
                if($userLoginDetails) {
                    //Updating pushToken meanwhile
                    $saveData['tdt_user_id'] = ($request->userId != '') ? $request->userId : '0';
                    $saveData['tdt_device_token'] = $request->pushToken;
                    $saveData['tdt_device_type'] = $request->deviceType;
                    $saveData['tdt_device_id'] = $request->deviceId;
                    $result = $this->objDeviceToken->saveDeviceToken($saveData);
                    
                    $response['status'] = 1;
                    $response['login'] = 1;
                    $response['message'] = trans('appmessages.default_success_msg');
                } else {
                    $response['message'] = trans('appmessages.default_error_msg');
                }
            } else {
                $response['message'] = trans('appmessages.invalid_userid_msg') . ' or ' . trans('appmessages.notvarified_user_msg');
            }
        } else {
            $response['message'] = trans('appmessages.missing_data_msg');
        }
        return response()->json($response, 200);
        exit;
    }

}