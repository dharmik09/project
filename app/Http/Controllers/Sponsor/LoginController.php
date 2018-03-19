<?php

namespace App\Http\Controllers\Sponsor;

use App\Http\Controllers\Controller;
//use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\SponsorLoginRequest;
use Illuminate\Http\Request;
use App\CMS;

class LoginController extends Controller
{
    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    public $redirectTo = '/sponsor/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('sponsor.guest', ['except' => 'logout']);
        $this->cmsObj = new CMS();
    }

    /**
     * Show the application's login form.
     *
     * @return \Illuminate\Http\Response
     */
    public function login()
    {
        if (Auth::guard('sponsor')->check()) {
            return Redirect::to("/sponsor/home");
        }
        $text = '';
        $loginInfo = $this->cmsObj->getCmsBySlug('sponsorlogininfotext');
        if(!empty($loginInfo)){
            $loginText = $loginInfo->toArray();
            $text = $loginText['cms_body'];
        }
        return view('sponsor.login', compact('text'));
    }

    public function loginCheck(SponsorLoginRequest $request)
    {
        $data = $request->all();
        if (Auth::guard('sponsor')->attempt(['sp_email' => $data['email'], 'password' => $data['password']])) {
            return redirect()->to(route('sponsor.home'))->with('success', 'Welcome to the sponsor panel!');
        }
        //flash('Invalid Credential')->error()->important();
        return redirect()->to(route('sponsor.login'))->with('error', trans('appmessages.invalid_user_pwd_msg'))->withInput($request->only('email'));
    }

    public function logout(Request $request)
    {
        Auth::guard('sponsor')->logout();
        return redirect()->to(route('sponsor.login'))->with('success', 'Logout successfully!');
    }
    
}
