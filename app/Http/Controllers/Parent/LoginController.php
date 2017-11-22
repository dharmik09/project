<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Auth;
use App\Http\Requests\ParentLoginRequest;
use Illuminate\Http\Request;
use Image;
use Config;
use Helpers;
use Input;
use Response;
use Mail;
use Redirect;
use App\Transactions;
use App\Parents;
use App\Templates;
use App\Sponsors;
use App\Country;
use App\Services\Parents\Contracts\ParentsRepository;
use App\Services\Sponsors\Contracts\SponsorsRepository;
use App\Services\Baskets\Contracts\BasketsRepository;
use App\Services\Professions\Contracts\ProfessionsRepository;
use App\CMS;

class LoginController extends Controller
{
    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    public $redirectTo = '/parent/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(ParentsRepository $ParentsRepository, SponsorsRepository $SponsorsRepository, BasketsRepository $BasketsRepository, ProfessionsRepository $ProfessionsRepository)
    {
        $this->middleware('parent.guest', ['except' => 'logout']);
        $this->objParents = new Parents();
        $this->ParentsRepository = $ParentsRepository;
        $this->objSponsors = new Sponsors();
        $this->SponsorsRepository = $SponsorsRepository;
        $this->BasketsRepository = $BasketsRepository;
        $this->ProfessionsRepository = $ProfessionsRepository;
        $this->teenOriginalImageUploadPath = Config::get('constant.TEEN_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->teenThumbImageUploadPath = Config::get('constant.TEEN_THUMB_IMAGE_UPLOAD_PATH');
        $this->teenThumbImageHeight = Config::get('constant.TEEN_THUMB_IMAGE_HEIGHT');
        $this->teenThumbImageWidth = Config::get('constant.TEEN_THUMB_IMAGE_WIDTH');
        $this->cmsObj = new CMS();
    }

    /**
     * Show the application's login form.
     *
     * @return \Illuminate\Http\Response
     */
    public function login()
    {
        if (Auth::guard('parent')->check()) {
            return Redirect::to("/parent/home");
        }
        $text = '';
        $loginInfo = $this->cmsObj->getCmsBySlug('parentlogininfotext');
        if (!empty($loginInfo)) {
            $loginText = $loginInfo->toArray();
            $text = $loginText['cms_body'];
        }
        $type = 'Parent';
        return view('parent.login', compact('type', 'text'));
    }

    public function loginCheck(ParentLoginRequest $request)
    {
        $data = $request->all();
        if (Auth::guard('parent')->attempt(['p_email' => $data['email'], 'password' => $data['password'] ])) {
            flash('Welcome to the parent panel')->success();
            return redirect()->to(route('parent.home'));
        }
        flash('Invalid Credential')->error()->important();
        return redirect()->back()->withInput($request->only('email'));
    }

    public function logout(Request $request)
    {
        Auth::guard('parent')->logout();
        flash('Logout successfully!')->success();
        return redirect()->to(route('parent.login'));
    }
    
}
