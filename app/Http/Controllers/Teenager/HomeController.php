<?php

namespace App\Http\Controllers\Teenager;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Auth;
use Illuminate\Http\Request;
use App\Video;
use App\CMS;
use App\Testimonial;
use App\FAQ;
use Config;

class HomeController extends Controller
{
    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    public $redirectTo = '/teenager';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('admin.guest', ['except' => 'logout']);
        $this->cmsObj = new CMS;
        $this->objTestimonial = new Testimonial;
        $this->objFAQ = new FAQ;
        $this->faqThumbImageUploadPath = Config::get('constant.FAQ_THUMB_IMAGE_UPLOAD_PATH');
    }

    /**
     * Show the teenager's home page.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(Auth::guard('teenager')->check()) {
            return redirect()->to(route('teenager.home'));
        }
        $objVideo = new Video();
        $videoCount =  $objVideo->getAllVideoDetail()->count();
        $videoDetail = $objVideo->getVideos();
        $teenText = '';
        $loginInfo = $this->cmsObj->getCmsBySlug('teenagerlogininfotext');
        if(!empty($loginInfo)){
            $loginText = $loginInfo->toArray();
            $teenText = $loginText['cms_body'];
        }
        $testimonials = $this->objTestimonial->getAllTestimonials();
        $quoteImage = 'img/quote.png';
        return view('teenager.index', compact('videoDetail', 'teenText', 'testimonials', 'quoteImage', 'videoCount'));
    }

    /**
     * Show FAQ page.
     *
     * @return \Illuminate\Http\Response
     */
    public function help()
    {
        $helps = $this->objFAQ->getAllFAQ();
        $faqThumbImageUploadPath = $this->faqThumbImageUploadPath;
        return view('teenager.help', compact('helps', 'faqThumbImageUploadPath'));
    }

    /**
     * Returns More video on Index page.
     *
     * @return \Illuminate\Http\Response
     */
    public function loadMoreVideo(Request $request)
    {
        $id = $request->id;
        $videoDetail = $objVideo->getMoreVideos();
        return view('teenager.loadMoreVideo', compact('videoDetail'));
    }
   
}