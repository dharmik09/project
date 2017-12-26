<?php

namespace App\Http\Controllers\Webservice;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Helpers;
use Config;
use Storage;
use App\Services\Teenagers\Contracts\TeenagersRepository;

class PasswordController extends Controller
{
    public function __construct(TeenagersRepository $teenagersRepository)
    {
        $this->teenagersRepository = $teenagersRepository;
    }

    /* Request Params : setPassword
    *  userId, loginToken, deviceId, deviceType, newPassword
    */
    public function setPassword(Request $request)
    {
		$response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg') ] ;
    	if($request->newPassword != "" && $request->deviceId != "" && $request->deviceType != "") {
    		$teenagerDetail['id'] = $request->userId;
            $teenagerDetail['password'] = bcrypt($request->newPassword);
            $this->teenagersRepository->saveTeenagerDetail($teenagerDetail);
            $response['status'] = 1;
            $response['login'] = 1;
            $response['message'] = trans('appmessages.default_success_msg');
    	} else {
            $response['login'] = 1;
            $response['message'] = trans('appmessages.missing_data_msg');
    	}
    	
    	return response()->json($response, 200);
    	exit;
    }

    /* Request Params : changePassword
    *  userId, loginToken, deviceId, deviceType, newPassword, oldPassword
    */
    public function changePassword(Request $request)
    {
        $response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg') ] ;
        if($request->newPassword != "" && $request->oldPassword != "" && $request->deviceId != "" && $request->deviceType != "") {
            $bool = $this->teenagersRepository->checkCurrentPasswordAgainstTeenager($request->userId, $request->oldPassword);
            
            $response['status'] = 1;
            $response['login'] = 1;
            $response['message'] = trans('appmessages.default_success_msg');
        } else {
            $response['login'] = 1;
            $response['message'] = trans('appmessages.missing_data_msg');
        }
        
        return response()->json($response, 200);
        exit;
    }
}