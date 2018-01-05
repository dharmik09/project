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
            $response['message'] = trans('appmessages.missing_data_msg');
            $response['data'] = trans('appmessages.missing_data_msg');
            print_r($helps->toArray()); die();
        } else {
            $response['message'] = trans('appmessages.missing_data_msg');
        }
        
        return response()->json($response, 200);
        exit;

        //$faqThumbImageUploadPath = $this->faqThumbImageUploadPath;
    }

    /**
     * Returns More video on Index page.
     *
     * @return \Illuminate\Http\Response
     */
    public function loadMoreVideo(Request $request)
    {
        $id = $request->id;
        $videoDetail = $this->objVideo->getMoreVideos($id);
        $videoCount = $this->objVideo->loadMoreVideoCount($id);
        return view('teenager.loadMoreVideo', compact('videoDetail', 'videoCount'));
    }
}