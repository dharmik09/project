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
use Carbon\Carbon;

class ProfileController extends Controller
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

    /* Request Params : getTeenagerProfileData
    *  loginToken, userId
    *  Service after loggedIn user
    */
    public function getTeenagerProfileData(Request $request)
    {
		$response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg') ] ;
    	$teenager = $this->teenagersRepository->getTeenagerById($request->userId);
        if($request->userId != "" && $teenager) {
    		$teenager->year = ($teenager->t_birthdate != "") ? Carbon::createFromFormat('Y-m-d', $teenager->t_birthdate)->year : "";
            $teenager->day = ($teenager->t_birthdate != "") ? Carbon::createFromFormat('Y-m-d', $teenager->t_birthdate)->day : "";
            $teenager->month = ($teenager->t_birthdate != "") ? Carbon::createFromFormat('Y-m-d', $teenager->t_birthdate)->month : "";
            if (count($teenager->t_sponsors) > 0) {
                foreach ($teenager->t_sponsors as $sponsor) {
                    $sponsor->sp_logo_thumb = (isset($sponsor->sp_photo) && $sponsor->sp_photo != "") ? Storage::url($this->sponsorThumbImageUploadPath . $sponsor->sp_photo) : Storage::url($this->sponsorThumbImageUploadPath . "proteen-logo.png");
                    $sponsor->sp_logo = (isset($sponsor->sp_photo) && $sponsor->sp_photo != "") ? Storage::url($this->sponsorOriginalImageUploadPath . $sponsor->sp_photo) : Storage::url($this->sponsorOriginalImageUploadPath . "proteen-logo.png");
                    $sponsor->sponsor_id = (isset($sponsor->sponsor_id)) ? $sponsor->sponsor_id : 0;
                    $sponsor->sp_email = (isset($sponsor->sp_email)) ? $sponsor->sp_email : "";
                    $sponsor->sp_admin_name = (isset($sponsor->sp_admin_name)) ? $sponsor->sp_admin_name : "";
                    $sponsor->sp_company_name = (isset($sponsor->sp_company_name)) ? $sponsor->sp_company_name : ""; 
                }
            }
            //Teenager Image
            $teenager->t_photo_thumb = "";
            if ($teenager->t_photo != '') {
                $teenager->t_photo_thumb = Storage::url($this->teenThumbImageUploadPath . $teenager->t_photo);
                $teenager->t_photo = Storage::url($this->teenOriginalImageUploadPath . $teenager->t_photo);
            }

            $ads = Helpers::getAds($request->userId);
            $response['status'] = 1;
            $response['login'] = 1;
            $response['ads'] = $ads;
            $response['message'] = trans('appmessages.default_success_msg');
            $response['data'] = $teenager;
        } else {
    		$response['message'] = trans('appmessages.missing_data_msg');
    	}
    	
    	return response()->json($response, 200);
    	exit;
    }

    /* Request Params : deleteTeenagerData
    *  loginToken, userId
    *  Service after loggedIn user
    */
    public function deleteTeenagerData(Request $request)
    {
        $response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg') ] ;
        $teenager = $this->teenagersRepository->getTeenagerDetailById($request->userId);
        if($request->userId != "" && $teenager) {
            $this->teenagersRepository->deleteTeenagerData($request->userId);
            $response['status'] = 1;
            $response['message'] = 'Successfully deleted!';
            $response['data'] = [];
        } else {
            $response['message'] = trans('appmessages.missing_data_msg');
        }
        return response()->json($response, 200);
        exit;
    }
}