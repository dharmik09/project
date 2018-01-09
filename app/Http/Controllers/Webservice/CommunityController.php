<?php

namespace App\Http\Controllers\Webservice;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Auth;
use Illuminate\Http\Request;
use App\Services\Teenagers\Contracts\TeenagersRepository;
use App\Services\Community\Contracts\CommunityRepository;
use Config;
use Storage;
use Helpers;

class CommunityController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(TeenagersRepository $teenagersRepository, CommunityRepository $communityRepository)
    {
        //$this->middleware('admin.guest', ['except' => 'logout']);
        $this->communityRepository = $communityRepository;
        $this->teenagersRepository = $teenagersRepository;
        $this->teenagerThumbImageUploadPath = Config::get('constant.TEEN_THUMB_IMAGE_UPLOAD_PATH');
    }

    /* Request Params : communityNewConnections
     *  loginToken, userId
     *  Service after loggedIn user
     */
    public function communityNewConnections(Request $request)
    {
        $response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg') ] ;
        $teenager = $this->teenagersRepository->getTeenagerById($request->userId);
        if($request->userId != "" && $teenager) {
            if (isset($request->sortBy) && $request->sortBy != '' && isset($request->sortOption) && $request->sortOption != '') {
                $sortBy = Helpers::getSortByColumn($request->sortBy);
                if ($sortBy == 't_birthdate') {
                    $ageVal = Helpers::age($request->sortOption);
                    if (strpos($ageVal, '-') !== false) {
                        $sortOption = Helpers::getDateRangeByAge($ageVal);
                    } else {
                        $sortOption = $ageVal;
                    }
                } else {
                    $sortOption = $request->sortOption;
                }
            } else {
                $sortBy = '';
                $sortOption = '';
            }
            if (isset($request->searchText) && $request->searchText != '') {
                $searchText = $request->searchText;
            } else {
                $searchText = '';
            }
            if (isset($request->lastTeenId) && $request->lastTeenId != '') {
                $lastTeenId = $request->lastTeenId;
            } else {
                $lastTeenId = '';
            }
            $newConnections = $this->communityRepository->getNewConnections($request->userId, $searchText, $lastTeenId, $sortBy, $sortOption);
            $newConnectionsCount = $this->communityRepository->getNewConnectionsCount($request->userId, $searchText, $lastTeenId, $sortBy, $sortOption);
            $data = [];
            $data['sortBy'] = Helpers::getCommunitySortByArray();
            $data['newConnections'] = [];
            if(isset($newConnections) && !empty($newConnections) && count($newConnections) > 0) {
                foreach($newConnections as $newConnection) {
                    $newConnection->t_photo  = ($newConnection->t_photo != "") ? Storage::url($this->teenagerThumbImageUploadPath.$newConnection->t_photo) : Storage::url($this->teenagerThumbImageUploadPath."proteen-logo.png");
                    $data['newConnections'][] = $newConnection;
                }
            }
            if (isset($newConnectionsCount) && $newConnectionsCount > 10) {
                $data['loadMoreFlag'] = 1;
            } else {
                $data['loadMoreFlag'] = 0;
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

    /* Request Params : communityMyConnections
     *  loginToken, userId
     *  Service after loggedIn user
     */
    public function communityMyConnections(Request $request)
    {
        $response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg') ] ;
        $teenager = $this->teenagersRepository->getTeenagerById($request->userId);
        if($request->userId != "" && $teenager) {
            if (isset($request->sortBy) && $request->sortBy != '' && isset($request->sortOption) && $request->sortOption != '') {
                $sortBy = Helpers::getSortByColumn($request->sortBy);
                if ($sortBy == 't_birthdate') {
                    $ageVal = Helpers::age($request->sortOption);
                    if (strpos($ageVal, '-') !== false) {
                        $sortOption = Helpers::getDateRangeByAge($ageVal);
                    } else {
                        $sortOption = $ageVal;
                    }
                } else {
                    $sortOption = $request->sortOption;
                }
            } else {
                $sortBy = '';
                $sortOption = '';
            }
            if (isset($request->searchText) && $request->searchText != '') {
                $searchText = $request->searchText;
            } else {
                $searchText = '';
            }
            if (isset($request->lastTeenId) && $request->lastTeenId != '') {
                $lastTeenId = $request->lastTeenId;
            } else {
                $lastTeenId = '';
            }
            $myConnections = $this->communityRepository->getMyConnections($request->userId, $searchText, $lastTeenId, $sortBy, $sortOption);
            $myConnectionsCount = $this->communityRepository->getMyConnectionsCount($request->userId, $searchText, $lastTeenId, $sortBy, $sortOption);
            $data = [];
            $data['sortBy'] = Helpers::getCommunitySortByArray();
            $data['myConnections'] = [];
            if(isset($myConnections) && !empty($myConnections) && count($myConnections) > 0) {
                foreach($myConnections as $myConnection) {
                    $myConnection->t_photo  = ($myConnection->t_photo != "") ? Storage::url($this->teenagerThumbImageUploadPath.$myConnection->t_photo) : Storage::url($this->teenagerThumbImageUploadPath."proteen-logo.png");
                    $data['myConnections'][] = $myConnection;
                }
            }
            if (isset($myConnectionsCount) && $myConnectionsCount > 10) {
                $data['loadMoreFlag'] = 1;
            } else {
                $data['loadMoreFlag'] = 0;
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

    /* Request Params : searchCommunityMyConnections
     *  loginToken, userId
     *  Service after loggedIn user
     */
    public function searchCommunityMyConnections(Request $request)
    {
        $response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg') ] ;
        $teenager = $this->teenagersRepository->getTeenagerById($request->userId);
        if($request->userId != "" && $teenager) {
            if($request->searchText != "") {
                $myConnections = $this->communityRepository->getMyConnections($request->userId, $request->searchText);
            } else {
                $myConnections = $this->communityRepository->getMyConnections($request->userId);
            }
            $data = [];
            $data['sortBy'] = Helpers::getCommunitySortByArray();
            $data['myConnections'] = [];
            if(isset($myConnections) && !empty($myConnections) && count($myConnections) > 0) {
                foreach($myConnections as $myConnection) {
                    $myConnection->t_photo  = ($myConnection->t_photo != "") ? Storage::url($this->teenagerThumbImageUploadPath.$myConnection->t_photo) : Storage::url($this->teenagerThumbImageUploadPath."proteen-logo.png");
                    $data['myConnections'][] = $myConnection;
                }
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

    /* Request Params : searchCommunityNewConnections
     *  loginToken, userId
     *  Service after loggedIn user
     */
    public function searchCommunityNewConnections(Request $request)
    {
        $response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg') ] ;
        $teenager = $this->teenagersRepository->getTeenagerById($request->userId);
        if($request->userId != "" && $teenager) {
            if($request->searchText != "") {
                $newConnections = $this->communityRepository->getNewConnections($request->userId, $request->searchText);
            } else {
                $newConnections = $this->communityRepository->getNewConnections($request->userId, $request->searchText);
            }
            $data = [];
            $data['sortBy'] = Helpers::getCommunitySortByArray();
            $data['newConnections'] = [];
            if(isset($newConnections) && !empty($newConnections) && count($newConnections) > 0) {
                foreach($newConnections as $newConnection) {
                    $newConnection->t_photo  = ($newConnection->t_photo != "") ? Storage::url($this->teenagerThumbImageUploadPath.$newConnection->t_photo) : Storage::url($this->teenagerThumbImageUploadPath."proteen-logo.png");
                    $data['newConnections'][] = $newConnection;
                }
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