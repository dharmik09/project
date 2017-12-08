<?php

namespace App\Http\Controllers\Sponsor;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Auth;
use Illuminate\Http\Request;
use App\Video;
use App\CMS;

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
        $objVideo = new Video();
        $videoDetail =  $objVideo->getAllVideoDetail();
        $enterpriseText = '';
        $loginInfo = $this->cmsObj->getCmsBySlug('sponsorlogininfotext');
        if(!empty($loginInfo)){
            $loginText = $loginInfo->toArray();
            $enterpriseText = $loginText['cms_body'];
        }
        return view('sponsor.index', compact('videoDetail', 'enterpriseText'));
    }
   
}