<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Auth;
use Illuminate\Http\Request;
use App\Video;
use App\CMS;
use App\Testimonial;
use App\Helptext;
use Input;
use View;

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
        $this->objHelptext = new Helptext;
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
        $videoDetail = $this->objVideo->getVideos(0);
        $nextSlotExist = $this->objVideo->getVideos(1);
        $videoCount = $this->objVideo->getAllVideoDetail()->count();
        $testimonials = $this->objTestimonial->getAllTestimonials();
        $quoteImage = 'img/quote-blue.png';
        return view('parent.index', compact('videoDetail', 'type', 'text', 'testimonials', 'quoteImage', 'videoCount', 'nextSlotExist'));
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
        $videoDetail = $this->objVideo->getVideos(0);
        $nextSlotExist = $this->objVideo->getVideos(1);
        $videoCount = $this->objVideo->getAllVideoDetail()->count();
        $testimonials = $this->objTestimonial->getAllTestimonials();
        $quoteImage = 'img/quote-mentor.png';
        return view('parent.index', compact('videoDetail', 'type', 'text', 'testimonials', 'quoteImage', 'videoCount', 'nextSlotExist'));
    }

    /**
     * Returns More video on Index page.
     *
     * @return \Illuminate\Http\Response
     */
    public function loadMoreVideo(Request $request)
    {
        $slot = Input::get('slot');
        $videoDetail = $this->objVideo->getVideos($slot);
        $nextSlotExist = $this->objVideo->getVideos($slot + 1);
        $view = view('teenager.loadMoreVideo', compact('videoDetail', 'nextSlotExist'));
        $response['view'] = $view->render();
        $response['nextSlotExist'] = count($nextSlotExist);
        return response()->json($response, 200);
        exit;
    }

    /*
     * Get helptext details by passed slug
     */
    public function getHelpTextBySlug()
    { 
        $helpSlug = Input::get('helpSlug');
        
        $helptext = $this->objHelptext->getHelptextBySlug($helpSlug);
        if(isset($helptext) && count($helptext) > 0){
            $help = mb_convert_encoding($helptext->h_description"UTF-8", "HTML-ENTITIES");
        }else{
            $help = 'Invalid slug passed';
        }
        return $help;            
    }    

}