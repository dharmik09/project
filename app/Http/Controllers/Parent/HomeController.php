<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Auth;
use Illuminate\Http\Request;
use App\Video;
use App\CMS;
use App\Testimonial;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->cmsObj = new CMS();
        $this->objTestimonial = new Testimonial;
        $this->objVideo = new Video;
        //$this->middleware('admin.guest', ['except' => 'logout']);
    }

    /**
     * Show the parent's home page.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(Auth::guard('parent')->check()) {
            return redirect()->to(route('parent.home'));
        }
        $text = '';
        $loginInfo = $this->cmsObj->getCmsBySlug('parentlogininfotext');
        if (!empty($loginInfo)) {
            $loginText = $loginInfo->toArray();
            $text = $loginText['cms_body'];
        }
        $type = 'Parent';
        $objVideo = new Video();
        $videoDetail =  $this->objVideo->getVideos();
        $videoCount = $this->objVideo->getAllVideoDetail()->count();
        $testimonials = $this->objTestimonial->getAllTestimonials();
        $quoteImage = 'img/quote-blue.png';
        return view('parent.index', compact('videoDetail', 'type', 'text', 'testimonials', 'quoteImage', 'videoCount'));
    }

    public function loginCounselor()
    {
        if(Auth::guard('parent')->check()) {
            return redirect()->to(route('parent.home'));
        }
        $text = '';
        $loginInfo = $this->cmsObj->getCmsBySlug('counselorlogininfotext');
        if (!empty($loginInfo)) {
            $loginText = $loginInfo->toArray();
            $text = $loginText['cms_body'];
        }
        $type = 'Mentor';
        $objVideo = new Video();
        $videoDetail =  $this->objVideo->getVideos();
        $videoCount = $this->objVideo->getAllVideoDetail()->count();
        $testimonials = $this->objTestimonial->getAllTestimonials();
        $quoteImage = 'img/quote-mentor.png';
        return view('parent.index', compact('videoDetail', 'type', 'text', 'testimonials', 'quoteImage', 'videoCount'));
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