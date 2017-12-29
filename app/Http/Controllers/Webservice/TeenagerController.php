<?php

namespace App\Http\Controllers\Webservice;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Services\Teenagers\Contracts\TeenagersRepository;
use Illuminate\Support\Facades\Auth;
use App\Teenagers;
use Helpers;
use Config;
use Storage;

class TeenagerController extends Controller
{
    public function __construct(TeenagersRepository $teenagersRepository)
    {
        $this->teenagersRepository = $teenagersRepository;
        $this->teenOriginalImageUploadPath = Config::get('constant.TEEN_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->teenThumbImageUploadPath = Config::get('constant.TEEN_THUMB_IMAGE_UPLOAD_PATH');
        
    }

    /* Request Params : getActiveTeenages
    *  loginToken, userId, pageNo
    */
    public function getActiveTeenages(Request $request)
    {
		$response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg') ] ;
    	$teenager = $this->teenagersRepository->getTeenagerDetailById($request->userId);
        if($request->userId != "" && $teenager) {
            $activeTeenagers = Helpers::getActiveTeenagersForCoupon($request->userId, $request->pageNo);
            $teenagerArray = [];
            if (!empty($activeTeenagers)) {
                $teenagersArr = $activeTeenagers->toArray();
                foreach ($teenagersArr as $key => $data) {
                    if ($data['id'] != $request->userId) {
                        $teenagers['name'] = $data['t_name'];
                        $teenagers['email'] = $data['t_email'];
                        $teenagers['lastname'] = $data['t_lastname'];
                        $teenagers['nickname'] = $data['t_nickname'];
                        if ($data['t_photo'] != '') {
                            $teenagers['photo'] = Storage::url($this->teenOriginalImageUploadPath . $data['t_photo']);
                        } else {
                            $teenagers['photo'] = Storage::url($this->teenOriginalImageUploadPath . 'proteen-logo.png');
                        }
                        $teenagerArray[] = $teenagers;
                    }
                }
            }
            $response['status'] = 1;
            $response['login'] = 1;
            $response['pageNo'] = $request->pageNo;
            $response['message'] = trans('appmessages.default_success_msg');
            $response['data']['users'] = $teenagerArray;
        } else {
            $response['message'] = trans('appmessages.missing_data_msg');
        }
        return response()->json($response, 200);
        exit;
    }

    /* Request Params : getActiveTeenagesBySearch
    *  loginToken, userId, searchText, pageNo, 
    */
    public function getActiveTeenagesBySearch(Request $request)
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
                        if ($data['t_photo'] != '') {
                            $teenagers['photo'] = Storage::url($this->teenOriginalImageUploadPath . $data['t_photo']);
                        } else {
                            $teenagers['photo'] = Storage::url($this->teenOriginalImageUploadPath . 'proteen-logo.png');
                        }
                        $teenagerArray[] = $teenagers;
                    }
                }
            }
            $response['status'] = 1;
            $response['login'] = 1;
            $response['pageNo'] = $page;
            $response['message'] = trans('appmessages.default_success_msg');
            $response['data']['users'] = $teenagerArray;
        } else {
            $response['message'] = trans('appmessages.missing_data_msg');
        }
        return response()->json($response, 200);
        exit;
    }
}