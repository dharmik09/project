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
        $user_type = $request->user_type;
        if ($user_type == 1) {
            $parCoun = 1;
            $loginRoute = url('parent/login');
        } else {
            $parCoun = 2;
            $loginRoute = url('counselor/login');
        }
        if (Auth::guard('parent')->attempt(['p_email' => $data['email'], 'password' => $data['password'], 'deleted' => 1, 'p_user_type' => $parCoun])) {
            flash('Welcome to the parent panel')->success();
            return redirect()->to(route('parent.home'));
        }
        return Redirect::to($loginRoute)->with('error', trans('appmessages.invalid_user_pwd_msg'))->withInput($request->only('email'));
    }

    public function logout(Request $request)
    {
        $userType = Auth::guard('parent')->get()->p_user_type;
        if ($userType == 1) {
            $loginRoute = url('parent/login');
        } else {
            $loginRoute = url('counselor/login');
        }
        Auth::guard('parent')->logout();
        flash('Logout successfully!')->success();
        return Redirect::to($loginRoute);
    }
    
    public function verifyParent() {
        $token = input::get('token');
        $userType = '';
        if ($token) {
            $parentTokenVarify = $this->ParentsRepository->updateParentTeenStatusByToken($token);
            if ($parentTokenVarify) {
                $parent = $this->ParentsRepository->updateParentVerifyStatusById($parentTokenVarify[0]->ptp_parent_id);
                $parentData = $this->ParentsRepository->getParentById($parentTokenVarify[0]->ptp_parent_id);
                $userType = $parentData->p_user_type;
                if ($parent) {
                    $varifymessage = 'Your pair has been verified';
                } else {
                    $varifymessage = trans('appmessages.default_error_msg');
                }
            } else {
                $varifymessage = trans('appmessages.already_email_verify_msg');
            }
        }
        return view('parent.verifyParent', compact('varifymessage', 'userType'));
    }

    public function verifyParentRegistration() {
        $token = input::get('token');
        $userType = '';

        if ($token) {
            $parentTokenVarify = $this->ParentsRepository->updateParentTokenStatusByToken($token);
            if ($parentTokenVarify) {
                $parent = $this->ParentsRepository->updateParentVerifyStatusById($parentTokenVarify[0]->tev_parent);
                $parentData = $this->ParentsRepository->getParentById($parentTokenVarify[0]->tev_parent);
                $userType = $parentData->p_user_type;
                if ($parent) {
                    $varifymessage = trans('appmessages.email_verify_msg');
                } else {
                    $varifymessage = trans('appmessages.default_error_msg');
                }
            } else {
                $varifymessage = trans('appmessages.already_email_verify_msg');
            }
        }
        return view('parent.verifyParent', compact('varifymessage', 'userType'));
    }
}
