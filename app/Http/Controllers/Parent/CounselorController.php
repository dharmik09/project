<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Http\Requests\ParentLoginRequest;
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
use App\CMS;

class CounselorController extends Controller {


    public function __construct()
    {
        $this->cmsObj = new CMS();
    }

    public function login() {

        if(Auth::guard('parent')->check()) {
            return Redirect::to("/parent/home");
        }
        $text = '';
        $loginInfo = $this->cmsObj->getCmsBySlug('counselorlogininfotext');
        if(!empty($loginInfo)){
            $loginText = $loginInfo->toArray();
            $text = $loginText['cms_body'];
        }
        $type = 'Mentor';
        return view('parent.login',compact('type','text'));
    }
    
    public function signup() {

        $newuser = array();
        if(Auth::guard('parent')->check()) {
            return Redirect::to("/parent/home");
        }
        $countries = Helpers::getCountries();
        $type = 'Mentor';

        $infotext = '';
        $termInfo = $this->cmsObj->getCmsBySlug('term-and-condition');
        if (!empty($termInfo)) {
            $termText = $termInfo->toArray();
            $infotext = $termText['cms_body'];
        }

        $policytext = '';
        $policyInfo = $this->cmsObj->getCmsBySlug('privacy-policy');
        if (!empty($policyInfo)) {
            $privacyText = $policyInfo->toArray();
            $policytext = $privacyText['cms_body'];
        }

        return view('parent.signup', compact('newuser','countries','type','infotext','policytext'));
    }
}
