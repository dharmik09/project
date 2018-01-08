<?php

namespace App\Http\Controllers\Webservice;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Services\Teenagers\Contracts\TeenagersRepository;
use Auth;
use Illuminate\Http\Request;
use App\Video;
use App\CMS;
use App\Testimonial;
use App\FAQ;
use Config;
use Input;
use Storage;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(TeenagersRepository $teenagersRepository)
    {
        //$this->middleware('admin.guest', ['except' => 'logout']);
        $this->cmsObj = new CMS;
        $this->objTestimonial = new Testimonial;
        $this->objFAQ = new FAQ;
        $this->faqThumbImageUploadPath = Config::get('constant.FAQ_THUMB_IMAGE_UPLOAD_PATH');
        $this->objVideo = new Video();
        $this->teenagersRepository = $teenagersRepository;
    }

    /* Request Params : help
    *  loginToken, userId
    *  Service after loggedIn user
    */
    public function help(Request $request)
    {
        $response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg') ] ;
        $teenager = $this->teenagersRepository->getTeenagerById($request->userId);
        if($request->userId != "" && $teenager) {
            $helps = $this->objFAQ->getAllFAQ();
            $data = [];
            if(isset($helps[0]->id) && !empty($helps[0])) {
                foreach($helps as $help) {
                    $help->f_photo  = ($help->f_photo != "") ? Storage::url($this->faqThumbImageUploadPath.$help->f_photo) : Storage::url($this->faqThumbImageUploadPath."proteen-logo.png");
                    $data[] = $help;
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

    /* Request Params : helpSearch
    *  loginToken, userId, searchText
    *  Service after loggedIn user
    */
    public function helpSearch(Request $request)
    {
        $response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg') ] ;
        $teenager = $this->teenagersRepository->getTeenagerById($request->userId);
        if($request->userId != "" && $teenager) {
            if($request->searchText != "") {
                $helps = $this->objFAQ->getSearchedFAQ(trim($request->searchText));
            } else {
                $helps = $this->objFAQ->getAllFAQ();
            }

            $data = [];
            if(isset($helps[0]->id) && !empty($helps[0])) {
                foreach($helps as $help) {
                    $help->f_photo  = ($help->f_photo != "") ? Storage::url($this->faqThumbImageUploadPath.$help->f_photo) : Storage::url($this->faqThumbImageUploadPath."proteen-logo.png");
                    $data[] = $help;
                }
            }
            $response['searchText'] = $request->searchText;
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