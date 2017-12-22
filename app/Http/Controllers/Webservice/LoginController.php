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
use App\TeenagerLoginToken;
use App\DeviceToken;
use Storage;
use Symfony\Component\Debug\Exception\FatalThrowableError;

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

    /* Request Params : 
    *  email, password, device_id, device_type
    */
    public function login(Request $request)
    {
    	try {
    		$response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg') ;
	    	if($request->email != "" && $request->password != "" && $request->device_id != "" && $request->device_type != "") {
	    		if (!filter_var($request->email, FILTER_VALIDATE_EMAIL) && is_numeric($request->email) && $request->email > 0 && $request->email == round($request->email, 0)) {
	    			$teenager = $this->teenagersRepository->getTeenagerByMobile($request->email);
	    			if(!$teenager) {
	    				$response['message'] = trans('appmessages.invalid_mobile_user_msg');
	    				return response()->json($response, 200);
	    			}
	    		} else {
	    			$teenager = $this->teenagersRepository->getTeenagerDetailByEmailId($request->email);
	    			if(!$teenager) {
	    				$response['message'] = trans('appmessages.invalid_email_msg');
	    				return response()->json($response, 200);
	    			}
	    		}
	    		if($teenager) {
	    			if(isset($teenager->t_isverified) && $teenager->t_isverified == 1) {
	    				if (Auth::guard('teenager')->attempt(['t_email' => $teenager->t_email, 'password' => $request->password, 'deleted' => 1])) {
	    					//Get/Format Sponsor Detail
	    					if (count($teenager->teenagerSponsors) > 0) {
	    						foreach ($teenager->teenagerSponsors as $sponsor) {
	    							$sponsor->sp_logo_thumb = (isset($sponsor->sponsor->sp_photo) && $sponsor->sponsor->sp_photo != "") ? Storage::url($this->sponsorThumbImageUploadPath . $sponsor->sponsor->sp_photo) : Storage::url($this->sponsorThumbImageUploadPath . "proteen-logo.png");
	    							$sponsor->sp_logo = (isset($sponsor->sponsor->sp_photo) && $sponsor->sponsor->sp_photo != "") ? Storage::url($this->sponsorOriginalImageUploadPath . $sponsor->sponsor->sp_photo) : Storage::url($this->sponsorOriginalImageUploadPath . "proteen-logo.png");
	    							$sponsor->sponsor_id = (isset($sponsor->sponsor->id)) ? $sponsor->sponsor->id : 0;
	    							$sponsor->sp_email = (isset($sponsor->sponsor->sp_email)) ? $sponsor->sponsor->sp_email : "";
	    							$sponsor->sp_admin_name = (isset($sponsor->sponsor->sp_admin_name)) ? $sponsor->sponsor->sp_admin_name : "";
	    							$sponsor->sp_company_name = (isset($sponsor->sponsor->sp_company_name)) ? $sponsor->sponsor->sp_company_name : ""; 
	    						}
	    					}
	    					//Teenager Image
	    					$teenager->t_photo_thumb = "";
	    					if ($teenager->t_photo != '') {
	    						$teenager->t_photo_thumb = Storage::url($this->teenThumbImageUploadPath . $teenager->t_photo);
	    						$teenager->t_photo = Storage::url($this->teenOriginalImageUploadPath . $teenager->t_photo);
	    					}
	    					//Save Login Token Data
	    					$loginDetail['tlt_teenager_id'] = $teenager->id;
	                        $loginDetail['tlt_login_token'] = base64_encode($teenager->t_email.':'.$teenager->password);
	                        $loginDetail['tlt_device_id'] = $request->device_id;
	                        $userTokenDetails = $this->objTeenagerLoginToken->saveTeenagerLoginDetail($loginDetail);
	                        //Save Device Token Data
	                        $saveData['tdt_user_id'] = $teenager->id;
	                        $saveData['tdt_device_token'] = base64_encode($teenager->t_email.':'.$teenager->password);
	                        $saveData['tdt_device_type'] = $request->device_type;
	                        $saveData['tdt_device_id'] = $request->device_id;
	                        $userDeviceDetails = $this->objDeviceToken->saveDeviceToken($saveData);

	                        $teenager->payment_status = $teenager->t_payment_status;

	                        $response['loginToken'] = base64_encode($teenager->t_email.':'.$teenager->password);
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
    	} catch(FatalThrowableError $e) {
    		$response['message'] = "exception";
    		return response()->json($response, 200);
    	}
    	
    	return response()->json($response, 200);
    	exit;
    }
}