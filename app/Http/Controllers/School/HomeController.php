<?php

namespace App\Http\Controllers\School;

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
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    public $redirectTo = '/school';

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
        $this->objVideo = new Video;
        $this->objHelptext = new Helptext;
    }

    /**
     * Show the school's home page.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(Auth::guard('school')->check()) {
            return redirect()->to(route('school.home'));
        }
        $schoolText = '';
        $loginInfo = $this->cmsObj->getCmsBySlug('schoollogininfotext');
        if(!empty($loginInfo)){
            $loginText = $loginInfo->toArray();
            $schoolText = $loginText['cms_body'];
        }
        $objVideo = new Video();
        $videoDetail = $this->objVideo->getVideos(0);
        $nextSlotExist = $this->objVideo->getVideos(1);
        $videoCount = $this->objVideo->getAllVideoDetail()->count();
        $testimonials = $this->objTestimonial->getAllTestimonials();
        $quoteImage = 'img/quote-school.png';
        return view('school.index', compact('videoDetail', 'schoolText', 'testimonials', 'quoteImage', 'videoCount', 'nextSlotExist'));
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
            $help = mb_convert_encoding($helptext->h_description, "UTF-8", "HTML-ENTITIES");
        }else{
            $help = 'Invalid slug passed';
        }
        return $help;            
    }    
}