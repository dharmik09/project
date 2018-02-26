<?php

namespace App\Http\Controllers\Teenager;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Http\Requests\TeenagerLoginRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Helpers;
use Config;
use Mail;
use Session;
use App\Services\Teenagers\Contracts\TeenagersRepository;
use App\Services\Template\Contracts\TemplatesRepository;
use Redirect;
use Response;

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
    public function __construct(TeenagersRepository $teenagersRepository, TemplatesRepository $templatesRepository)
    {
        $this->teenagersRepository = $teenagersRepository;
        $this->middleware('teenager.guest', ['except' => 'logout']);
        $this->templateRepository = $templatesRepository;
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

    public function loginCheck(TeenagerLoginRequest $request)
    {
        $emailPhone = $request->email;
        $password = $request->password;
        $rememberMe = $request->has('remember_me') ? true : false;

        if ($emailPhone != '' && $password != '') {
            if (!filter_var($emailPhone, FILTER_VALIDATE_EMAIL)) {
                $teenager = $this->teenagersRepository->getTeenagerByMobile($emailPhone);
                if(is_numeric($emailPhone) && $emailPhone > 0 && $emailPhone == round($emailPhone, 0) && isset($teenager['id'])) {
                    if ($teenager['t_isverified'] == '1') {
                        if (Auth::guard('teenager')->attempt(['t_email' => $teenager['t_email'], 'password' => $password, 'deleted' => 1], $rememberMe)) {
                            return redirect()->to(route('teenager.home'));
                        } else {
                            return Redirect::to('/teenager/login')->with('error', trans('appmessages.invalid_user_pwd_msg'))->with('id', $teenager['id']);
                        }
                    } else {
                        return Redirect::to('/teenager/login')->with('error', trans('appmessages.notvarified_user_msg'))->with('t_uniqueid', $teenager['t_uniqueid']);
                    }
                } else {
                    return Redirect::to('/teenager/login')->with('error', "Phone number is invalid!");
                }
            } else {
                if (Auth::guard('teenager')->attempt(['t_email' => $emailPhone, 'password' => $password, 'deleted' => 1], $rememberMe)) {
                    $teenager = $this->teenagersRepository->getTeenagerDetailByEmailId($emailPhone);
                    if ($teenager->t_isverified == '1') {
                        return redirect()->to(route('teenager.home'));
                    } else {
                        Auth::guard('teenager')->logout();
                        return Redirect::to('/teenager/login')->with('error', trans('appmessages.notvarified_user_msg'))->with('t_uniqueid', $teenager->t_uniqueid);
                    }
                } else {
                    return Redirect::to('/teenager/login')->with('error', trans('appmessages.invalid_user_pwd_msg'));
                }
            }
        } else {
            return Redirect::to('/teenager/login')->with('error', trans('appmessages.missing_data_msg'));
        }
        return Redirect::back()
                ->withInput()
                ->withErrors(trans('validation.invalidcombo'));
    }

    public function logout(Request $request)
    {
        $user = Auth::guard('teenager')->user();
        $currentProgress = Helpers::calculateProfileComplete($user->id);
        $increasedProgress = $currentProgress - $user->t_progress_calculations;
        echo $currentProgress."<br/>";
        echo $user->t_progress_calculations."<br/>";
        echo $increasedProgress."<br/>"; exit;
        $teenDetails = $this->teenagersRepository->updateTeenagerProgressCalculationsById($user->id, $increasedProgress);
        Auth::guard('teenager')->logout();
        return redirect()->to(route('login'));
    }
        
}
