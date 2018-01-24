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
use App\Services\Teenagers\Contracts\TeenagersRepository;
use App\Services\FileStorage\Contracts\FileStorageRepository;

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
    public function __construct(ParentsRepository $parentsRepository, SponsorsRepository $sponsorsRepository, BasketsRepository $basketsRepository, ProfessionsRepository $professionsRepository, TeenagersRepository $teenagersRepository, FileStorageRepository $fileStorageRepository)
    {
        $this->middleware('parent.guest', ['except' => 'logout']);
        $this->objParents = new Parents();
        $this->parentsRepository = $parentsRepository;
        $this->objSponsors = new Sponsors();
        $this->sponsorsRepository = $sponsorsRepository;
        $this->basketsRepository = $basketsRepository;
        $this->professionsRepository = $professionsRepository;
        $this->teenOriginalImageUploadPath = Config::get('constant.TEEN_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->teenThumbImageUploadPath = Config::get('constant.TEEN_THUMB_IMAGE_UPLOAD_PATH');
        $this->teenThumbImageHeight = Config::get('constant.TEEN_THUMB_IMAGE_HEIGHT');
        $this->teenThumbImageWidth = Config::get('constant.TEEN_THUMB_IMAGE_WIDTH');
        $this->cmsObj = new CMS();
        $this->teenagersRepository = $teenagersRepository;
        $this->parentOriginalImageUploadPath = Config::get('constant.PARENT_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->parentThumbImageUploadPath = Config::get('constant.PARENT_THUMB_IMAGE_UPLOAD_PATH');
        $this->parentThumbImageHeight = Config::get('constant.PARENT_THUMB_IMAGE_HEIGHT');
        $this->parentThumbImageWidth = Config::get('constant.PARENT_THUMB_IMAGE_WIDTH');
        $this->fileStorageRepository = $fileStorageRepository;
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
        $userType = Auth::guard('parent')->user()->p_user_type;
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
            $parentTokenVarify = $this->parentsRepository->updateParentTeenStatusByToken($token);
            if ($parentTokenVarify) {
                $parent = $this->parentsRepository->updateParentVerifyStatusById($parentTokenVarify[0]->ptp_parent_id);
                $parentData = $this->parentsRepository->getParentById($parentTokenVarify[0]->ptp_parent_id);
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

    public function verifyParentRegistration() 
    {
        $token = input::get('token');
        $userType = '';

        if ($token) {
            $parentTokenVarify = $this->parentsRepository->updateParentTokenStatusByToken($token);
            if ($parentTokenVarify) {
                $parent = $this->parentsRepository->updateParentVerifyStatusById($parentTokenVarify[0]->tev_parent);
                $parentData = $this->parentsRepository->getParentById($parentTokenVarify[0]->tev_parent);
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

    public function verifyParentTeenRegistration() 
    {
        $token = input::get('token');
        $userType = '';
        if ($token) {
            $countries = Helpers::getCountries();
            $parentData = $this->parentsRepository->getParentDetailsByPtpToken($token);
            $userType = $parentData->p_user_type;
            if ($parentData->p_isverified == 1 || $parentData->ptp_is_verified == 1) {
                if ($parentData->p_isverified != 1) {
                    $verifyParent = $this->parentsRepository->updateParentVerifyStatusById($parentData->id);
                    if (isset($verifyParent) && !empty($verifyParent)) {
                        $varifymessage = "You are verified";
                    } else {
                        $varifymessage = "Something went wrong. Please try again.";
                    }
                }
                if($parentData->ptp_is_verified != 1) {
                    $verifyPair = $this->parentsRepository->updateParentTeenStatusByToken($token);
                    if (isset($verifyPair) && !empty($verifyPair)) {
                        $varifymessage = "You are verified";
                    } else {
                        $varifymessage = "Something went wrong. Please try again.";
                    }
                }
                if ($parentData->p_isverified == 1 && $parentData->ptp_is_verified == 1) {
                    $varifymessage = "You are already verified";
                }
                return view('parent.verifyParent', compact('varifymessage', 'userType'));
            } else {
                $teenDatails = $this->teenagersRepository->getTeenagerById($parentData->ptp_teenager);
                $teenReferenceId = $teenDatails->t_uniqueid;
                if ($parentData) {
                    $verifymessage = 'Please set up your profile';
                } else {
                    $verifymessage = trans('appmessages.default_error_msg');
                }
                return view('parent.setUpProfile', compact('verifymessage', 'parentData', 'countries', 'teenReferenceId'));
            }
        }
    }

    public function setProfile()
    {
        $parentDetail['id'] = Input::get('id');
        $parentDetail['p_first_name'] = (Input::get('first_name') != '') ? e(Input::get('first_name')) : '';
        $parentDetail['p_last_name'] = (Input::get('last_name') != '') ? e(Input::get('last_name')) : '';
        $parentDetail['p_address1'] = (Input::get('address1') != '') ? e(Input::get('address1')) : '';
        $parentDetail['p_address2'] = (Input::get('address2') != '') ? e(Input::get('address2')) : '';
        $parentDetail['p_pincode'] = (Input::get('pincode') != '') ? e(Input::get('pincode')) : '';
        $parentDetail['p_city'] = (Input::get('city') != '') ? e(Input::get('city')) : '';
        $parentDetail['p_state'] = (Input::get('state') != '') ? e(Input::get('state')) : '';
        $parentDetail['p_country'] = (Input::get('country') != '') ? e(Input::get('country')) : '';
        $parentDetail['p_gender'] = (Input::get('gender') != '') ? e(Input::get('gender')) : '';
        $p_email = (Input::get('email') != '') ? e(Input::get('email')) : '';
        $parentDetail['password'] = (Input::get('password') != '') ? bcrypt(e(Input::get('password'))) : '';
        //Image upload
        if (Input::file())
        {
            $file = Input::file('photo');
            if (!empty($file)) {
                $fileName = 'parent_' . time() . '.' . $file->getClientOriginalExtension();
                $pathOriginal = public_path($this->parentOriginalImageUploadPath . $fileName);
                $pathThumb = public_path($this->parentThumbImageUploadPath . $fileName);
                Image::make($file->getRealPath())->save($pathOriginal);
                Image::make($file->getRealPath())->resize(300,300)->save($pathThumb);

                //Uploading on AWS
                $originalImage = $this->fileStorageRepository->addFileToStorage($fileName, $this->parentOriginalImageUploadPath, $pathOriginal, "s3");
                $thumbImage = $this->fileStorageRepository->addFileToStorage($fileName, $this->parentThumbImageUploadPath, $pathThumb, "s3");
                
                \File::delete($this->parentOriginalImageUploadPath . $fileName);
                \File::delete($this->parentThumbImageUploadPath . $fileName);
                $parentDetail['p_photo'] = $fileName;
            }
            else {
                $parentDetail['p_photo'] = 'proteen_logo.png';
            }
        }
        $parentDetailSaved = $this->parentsRepository->saveParentDetail($parentDetail);
        if (isset($parentDetailSaved) && !empty($parentDetailSaved)) {
            $parentVerify = $this->parentsRepository->updateParentVerifyStatusById($parentDetail['id']);
            $parentTokenVarify = $this->parentsRepository->updateParentTeenStatusByParentId($parentDetail['id']);
            if (isset($parentVerify) && isset($parentTokenVarify)) {
                return Redirect::to("parent/login");
                flash('Profile Set successfully.')->success();
            } else {
                return Redirect::to("parent/login");
                flash('Profile not verified')->error();
            }
        } else {
            return Redirect::to("parent/login");
            flash(trans('appmessages.default_error_msg'))->error();
        }
    }
}
