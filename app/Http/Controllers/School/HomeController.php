<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Auth;
use Illuminate\Http\Request;
use App\Video;

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
        $objVideo = new Video();
        $videoDetail =  $objVideo->getAllVideoDetail();
        return view('school.index', compact('videoDetail'));
    }
}