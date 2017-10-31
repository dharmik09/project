<?php

namespace App\Http\Controllers\Developer;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    public $redirectTo = '/developer/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('developer.guest', ['except' => 'logout']);
    }

    /**
     * Show the application's login form.
     *
     * @return \Illuminate\Http\Response
     */
    public function login()
    {
        return view('developer.login');
    }

    public function loginCheck(Request $request)
    {
        $data = $request->all();
        if (Auth::guard('developer')->attempt(['email' => $data['email'], 'password' => $data['password']])) {
            return redirect()->to(route('developer.home'));
        }
        return view('developer.login');
    }

    public function logout(Request $request)
    {
        Auth::guard('developer')->logout();
        $request->session()->flush();
        $request->session()->regenerate();
        return redirect()->to(route('developer.login'));
    }
    
}
