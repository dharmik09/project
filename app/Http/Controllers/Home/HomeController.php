<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Auth;
use Illuminate\Http\Request;
use App\CMS;
use App\Testimonial;

class HomeController extends Controller
{
    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    public $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->cmsObj = new CMS;
        $this->testimonialObj = new Testimonial;
    }

    /**
     * Show the application's home page.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $mentorInfo = $this->cmsObj->getCmsBySlug('home-page-mentor-info');
        $parentInfo = $this->cmsObj->getCmsBySlug('home-page-parent-info');
        $schoolInfo = $this->cmsObj->getCmsBySlug('home-page-school-info');
        $sponsorInfo = $this->cmsObj->getCmsBySlug('home-page-sponsor-info');
        $teenInfo = $this->cmsObj->getCmsBySlug('home-page-teen-info');
        if(!empty($mentorInfo)){
            $mentorDetail = $mentorInfo->toArray();
            $mentorText = $mentorDetail['cms_body'];
        } else {
            $mentorText = '';
        }
        if(!empty($parentInfo)){
            $parentDetail = $parentInfo->toArray();
            $parentText = $parentDetail['cms_body'];
        } else {
            $parentText = '';
        }
        if(!empty($schoolInfo)){
            $schoolDetail = $schoolInfo->toArray();
            $schoolText = $schoolDetail['cms_body'];
        } else {
            $schoolText = '';
        }
        if(!empty($sponsorInfo)){
            $sponsorDetail = $sponsorInfo->toArray();
            $sponsorText = $sponsorDetail['cms_body'];
        } else {
            $sponsorText = '';
        }
        if(!empty($teenInfo)){
            $teenDetail = $teenInfo->toArray();
            $teenText = $teenDetail['cms_body'];
        } else {
            $teenText = '';
        }
        return view('home.index', compact('teenText', 'sponsorText', 'schoolText', 'parentText', 'mentorText'));
    }
    /**
     * Show the application's faq page.
     *
     * @return \Illuminate\Http\Response
     */
    public function faq()
    {
        return view('home.faq');
    }
    
    public function team()
    {
        $data = $this->testimonialObj->where(['t_type' => "management", 'deleted' => 1])->get();
        $advisoryData = $this->testimonialObj->where(['t_type' => "advisory", 'deleted' => 1])->get();
        return view('home.team', compact('data', 'advisoryData'));
    }

    public function contactUs()
    {
        return view('home.contactUs');
    }

    public function privacyPolicy()
    {
        $info = $this->cmsObj->getCmsBySlug('privacy-policy');
        return view('home.privacyPolicy', compact('info'));
    }

    public function termsCondition()
    {
        $termInfo = $this->cmsObj->getCmsBySlug('term-and-condition');
        return view('home.termsCondition', compact('termInfo'));
    }

    public function aboutUs()
    {
        $info = $this->cmsObj->getCmsBySlug('about-us');
        return view('home.aboutUs', compact('info'));
    }
}
