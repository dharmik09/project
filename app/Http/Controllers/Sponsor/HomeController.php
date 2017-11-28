<?php

namespace App\Http\Controllers\Sponsor;

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
    public $redirectTo = '/sponsor';

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
        return view('sponsor.index', compact('videoDetail'));
    }
   
}