<?php

namespace App\Http\Controllers\School;
use App\Http\Controllers\Controller;
use App\Http\Requests\SchoolLoginRequest;
use Auth;
use Image;
use Config;
use Helpers;
use Input;
use Response;
use Mail;
use Redirect;
use Illuminate\Http\Request;
use App\Transactions;
use App\Templates;
use App\Schools;
use App\Country;
use App\Services\Schools\Contracts\SchoolsRepository;
use App\Services\Baskets\Contracts\BasketsRepository;
use App\Services\Professions\Contracts\ProfessionsRepository;
use App\CMS;

class LoginController extends Controller {

    public function __construct(SchoolsRepository $SchoolsRepository)
    {
        $this->objSchools = new Schools();
        $this->SchoolsRepository = $SchoolsRepository;
        $this->schoolOriginalImageUploadPath = Config::get('constant.SCHOOL_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->schoolThumbImageUploadPath = Config::get('constant.SCHOOL_THUMB_IMAGE_UPLOAD_PATH');
        $this->schoolThumbImageHeight = Config::get('constant.SCHOOL_THUMB_IMAGE_HEIGHT');
        $this->schoolThumbImageWidth = Config::get('constant.SCHOOL_THUMB_IMAGE_WIDTH');
        $this->cmsObj = new CMS();     
    }

    public function login() {
        if (Auth::school()->check()) {
            return Redirect::to("/school/dashboard");
        }
        $text = '';
        $loginInfo = $this->cmsObj->getCmsBySlug('schoollogininfotext');
        if(!empty($loginInfo)){
            $loginText = $loginInfo->toArray();
            $text = $loginText['cms_body'];
        }
        return view('school.Login',compact('text'));
    }

    public function loginCheck(SchoolLoginRequest $request) {

        $email = e(Input::get('email'));
        $password = e(Input::get('password'));

        $response = [];
        $response['status'] = 0;
        $response['message'] = trans('appmessages.default_error_msg');

        if (isset($email) && $email != '' && isset($password) && $password != '') {
 
                if ($user = Auth::school()->attempt(['sc_email' => $email, 'password' => $password, 'deleted' => 1])) {
                $school = $this->SchoolsRepository->getSchoolDetailByEmailId($email);
                    if (!empty($school) && $school['sc_isapproved'] == '1') {
                        return Redirect::to("school/dashboard");
                        exit;
                    } else {

                        Auth::school()->logout();
                        return Redirect::to('/school')->with('error', trans('appmessages.notvarified_user_msg'));
                    }

                } else {
                    $response['message'] = trans('appmessages.invalid_user_pwd_msg');
                    return Redirect::to('/school')->with('error', trans('appmessages.invalid_user_pwd_msg'));
                }

        } else {
            $response['message'] = trans('appmessages.missing_data_msg');
            return Redirect::to('/school')->with('error', trans('appmessages.missing_data_msg'));
        }

        return Redirect::back()
                        ->withInput()
                        ->withErrors(trans('validation.invalidcombo'));
    }

    public function getLogout() {
        Auth::school()->logout();
        return Redirect::to('/school');
        exit;
    }

}
