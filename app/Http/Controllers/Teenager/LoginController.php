<?php

namespace App\Http\Controllers\Teenager;

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
    public $redirectTo = '/teenager/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('teenager.guest', ['except' => 'logout']);
    }

    /**
     * Show the application's login form.
     *
     * @return \Illuminate\Http\Response
     */
    public function login()
    {
        return view('teenager.login');
    }

    public function loginCheck(Request $request)
    {
        $data = $request->all();
        if (Auth::guard('teenager')->attempt(['t_email' => $data['email'], 'password' => $data['password'], 'deleted' => 1])) {
            return redirect()->to(route('teenager.home'));
        }
        return view('teenager.login');
    }

    public function logout(Request $request)
    {
        Auth::guard('teenager')->logout();
        return redirect()->to(route('login'));
    }
    
}
