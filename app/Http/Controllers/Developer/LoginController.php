<?php

namespace App\Http\Controllers\Developer;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\DeveloperLoginRequest;
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

    public function loginCheck(DeveloperLoginRequest $request)
    {
        $data = $request->all();
        if (Auth::guard('developer')->attempt(['email' => $data['email'], 'password' => $data['password']])) {
            flash('Welcome to the developer panel')->success();
            return redirect()->to(route('developer.home'));
        }
        flash('Invalid Credential')->error()->important();
        return view('developer.login');
    }

    public function logout(Request $request)
    {
        Auth::guard('developer')->logout();
        //$request->session()->flush();
        //$request->session()->regenerate();
        flash('Developer Logout successfully!')->success();
        return redirect()->to(route('developer.login'));
    }
    
}
