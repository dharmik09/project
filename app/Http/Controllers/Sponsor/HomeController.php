<?php

namespace App\Http\Controllers\Sponsor;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Auth;
use Illuminate\Http\Request;
use App\Video;
use App\CMS;
use App\Testimonial;
use View;
use Input;

class HomeController extends Controller
{
    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    public $redirectTo = '/sponsor';

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
        $this->objVideo = new Video();
    }

    /**
     * Show the parent's home page.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(Auth::guard('sponsor')->check()) {
            return redirect()->to(route('sponsor.home'));
        }
        $videoCount = $this->objVideo->getAllVideoDetail()->count();
        $videoDetail = $this->objVideo->getVideos(0);
        $nextSlotExist = $this->objVideo->getVideos(1);
        $enterpriseText = '';
        $loginInfo = $this->cmsObj->getCmsBySlug('sponsorlogininfotext');
        if(!empty($loginInfo)){
            $loginText = $loginInfo->toArray();
            $enterpriseText = $loginText['cms_body'];
        }
        $testimonials = $this->objTestimonial->getAllTestimonials();
        $quoteImage = 'img/quote-enterprise.png';
        return view('sponsor.index', compact('videoDetail', 'enterpriseText', 'testimonials', 'quoteImage', 'videoCount', 'nextSlotExist'));
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
   
}