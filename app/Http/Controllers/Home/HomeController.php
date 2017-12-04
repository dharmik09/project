<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Auth;
use Illuminate\Http\Request;

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
        //$this->middleware('admin.guest', ['except' => 'logout']);
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
        return view('home.team');
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