<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Auth;
use Illuminate\Http\Request;
use App\Video;
use App\CMS;

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
        $videoDetail =  $objVideo->getAllVideoDetail();
        return view('parent.index', compact('videoDetail', 'type', 'text'));
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
        $videoDetail =  $objVideo->getAllVideoDetail();
        return view('parent.index', compact('videoDetail', 'type', 'text'));
    }
   
}