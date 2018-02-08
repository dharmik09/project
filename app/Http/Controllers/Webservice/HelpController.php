<?php

namespace App\Http\Controllers\Webservice;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Auth;
use Config;
use Storage;
use Input;
use Mail;
use Helpers;
use App\Services\Teenagers\Contracts\TeenagersRepository;
use Redirect;
use Carbon\Carbon;  
use App\Helptext;
use Illuminate\Http\Request;

class HelpController extends Controller {

    public function __construct(TeenagersRepository $teenagersRepository) 
    {
        $this->teenagersRepository = $teenagersRepository;
        $this->objHelptext = new Helptext;
    }
    
    /* Request Params : getHelpTextBySlug
     *  loginToken, userId, helpSlug
     *  Service after loggedIn user
     */
    public function getHelpTextBySlug(Request $request)
    { 
        $response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg') ] ;
        $teenager = $this->teenagersRepository->getTeenagerById($request->userId);
        if($request->userId != "" && $teenager) {
            $helptext = $this->objHelptext->getHelptextBySlug($request->helpSlug);
            $response['login'] = 1;
            $response['status'] = 1;
            $response['message'] = trans('appmessages.default_success_msg');
            $response['data'] = $helptext;
        } else {
            $response['message'] = trans('appmessages.invalid_userid_msg') . ' or ' . trans('appmessages.notvarified_user_msg');
        }
        return response()->json($response, 200);
        exit;   
    }        
}
