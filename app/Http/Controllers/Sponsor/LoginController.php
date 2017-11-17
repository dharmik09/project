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
            return Redirect::to("/sponsor/view-dashboard");
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
            flash('Welcome to the sponsor panel')->success();
            return redirect()->to(route('sponsor.home'));
        }
        flash('Invalid Credential')->error()->important();
        return redirect()->to(route('sponsor.login'));
    }

    public function logout(Request $request)
    {
        Auth::guard('sponsor')->logout();
        //$request->session()->flush();
        //$request->session()->regenerate();
        flash('Logout successfully!')->success();
        return redirect()->to(route('sponsor.login'));
    }
    
}
