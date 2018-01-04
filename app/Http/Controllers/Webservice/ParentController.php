<?php

namespace App\Http\Controllers\Webservice;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Helpers;
use Config;
use App\Services\Teenagers\Contracts\TeenagersRepository;
use Storage;
use App\TeenagerLoginToken;

class ParentController extends Controller
{
    public function __construct(TeenagersRepository $teenagersRepository)
    {
        $this->teenagersRepository = $teenagersRepository;
        $this->objTeenagerLoginToken = new TeenagerLoginToken();
        $this->teenOriginalImageUploadPath = Config::get('constant.TEEN_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->teenThumbImageUploadPath = Config::get('constant.TEEN_THUMB_IMAGE_UPLOAD_PATH');
        $this->parentOriginalImageUploadPath = Config::get('constant.PARENT_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->parentThumbImageUploadPath = Config::get('constant.PARENT_THUMB_IMAGE_UPLOAD_PATH');
    }

    /* Request Params : getParentList
    *  userId, loginToken, userType
    *  
    */
    public function getParentList(Request $request)
    {
		$response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg') ] ;
    	if($request->userId != "") {
            $teenager = $this->teenagersRepository->getTeenagerDetailById($request->userId);
            if($request->userId != "" && $teenager) {
                $type = ($request->userType != "") ? $request->userType : '0';
                $parentDetail = $this->teenagersRepository->getParentListByTeenagerId($request->userId, $type);
                $data = [];
                foreach ($parentDetail AS $key => $value) {
                    $parentData = [];
                    $parentData['parent_id'] = $value->ptp_parent_id;
                    $parentData['teenager_id'] = $value->ptp_teenager;
                    $parentData['parent_name'] = $value->p_first_name." ".$value->p_last_name;
                    $parentPhoto = $value->p_photo;
                    if ($parentPhoto != '') {
                        $parentData['p_photo'] = Storage::url($this->parentOriginalImageUploadPath . $parentPhoto);
                    } else {
                        $parentData['p_photo'] = Storage::url($this->parentOriginalImageUploadPath . 'proteen-logo.png');
                    }
                    $data[] = $parentData;
                }
                $response['status'] = 1;
                $response['login'] = 1;
                $response['message'] = trans('appmessages.default_success_msg');
                $response['data'] = $data;
            } else {
                $response['message'] = trans('appmessages.missing_data_msg');
            } 
    	} else {
    		$response['message'] = trans('appmessages.missing_data_msg');
    	}
    	
    	return response()->json($response, 200);
    	exit;
    }
    /* Request Params : getParentList
    *  userId, loginToken, userType
    *  
    */
    public function getParentList(Request $request)
    {
        
    }
}