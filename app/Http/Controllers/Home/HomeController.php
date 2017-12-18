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
        return view('home.index');
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
        //echo "<pre/>"; print_r($data->t_title); die();
        return view('home.team', compact('data'));
    }

    public function contactUs()
    {
        return view('home.contactUs');
    }

    public function privacyPolicy()
    {
        return view('home.privacyPolicy');
    }

    public function termsCondition()
    {
        return view('home.termsCondition');
    }
}
