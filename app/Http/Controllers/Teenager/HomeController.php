<?php

namespace App\Http\Controllers\Teenager;

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
    public $redirectTo = '/teenager';

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
     * Show the teenager's home page.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(Auth::guard('teenager')->check()) {
            return redirect()->to(route('teenager.home'));
        }
        return view('teenager.index');
    }
   
}