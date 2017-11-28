<?php

namespace App\Http\Controllers\Teenager;

use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Contracts\Auth\Authenticatable;
use Config;
use Helpers;
use Input;
use Redirect;
use Response;
use Illuminate\Http\Request;
use App\Teenagers;
use App\Templates;
use App\Sponsors;
use App\Services\Teenagers\Contracts\TeenagersRepository;
use App\Services\Sponsors\Contracts\SponsorsRepository;
use App\Services\Baskets\Contracts\BasketsRepository;
use App\Services\Professions\Contracts\ProfessionsRepository;
use Socialite;

class SocialLoginController extends Controller {

    public function __construct(TeenagersRepository $teenagersRepository, SponsorsRepository $sponsorsRepository, BasketsRepository $basketsRepository, ProfessionsRepository $professionsRepository) {
        $this->objTeenagers = new Teenagers();
        $this->teenagersRepository = $teenagersRepository;
        $this->objSponsors = new Sponsors();
        $this->sponsorsRepository = $sponsorsRepository;
    }

    //Redirect to social provider page
    public function redirectToProviderFacebook()
    {   
        Auth::guard('teenager')->logout();
        return Socialite::driver('facebook')->fields([
                    'first_name', 'last_name', 'email', 'gender'
                ])->scopes(['public_profile'])->redirect();
    }

    public function redirectToProviderGooglePlus()
    {   
        Auth::guard('teenager')->logout();
        return Socialite::driver('google')->redirect();
    }

    //Facebook redirect handle
    public function handleProviderCallbackFacebook(Request $request)
    {
        if (!$request->has('code') || $request->has('denied')) {
            return redirect('teenager/login');
        }
        //Need to set update field for each cases. Handle profile pic and phone unique. Handle deleted operation and username unique operation
        try
        {
            $user = Socialite::driver('facebook')->fields([
                    'first_name', 'last_name', 'email', 'gender'
                ])->user();
            
            $loginUrl = "teenager/login";
            $homeUrl = "teenager/home";
            $first_name = (isset($user->user['first_name'])) ? ucfirst($user->user['first_name']) : "" ;
            $last_name = (isset($user->user['last_name'])) ? ucfirst($user->user['last_name']) : "" ;
            $email = (isset($user->user['email'])) ? $user->user['email'] : "" ;
            $gender = (isset($user->user['gender'])) ? ($user->user['gender'] == "male") ? 1 : 2 : "";
            $nickname = (isset($user->nickname)) ? $user->nickname : ""; 

            //We assumed, email is compulsory for system
            if ($email == '' || $email == null)
            {
                return Redirect::to($loginUrl)->with('error', trans('labels.emailpermistionmustberequired'));
            }

            $teenagerDetail = [];
            $teenagerDetail['t_uniqueid'] = Helpers::getTeenagerUniqueId();
            $teenagerDetail['t_name'] = $first_name." ".$last_name;
            $teenagerDetail['t_email'] = $email;
            //$teenagerDetail['password'] = (isset($user->user['id'])) ? $user->user['id'] : "" ;
            $teenagerDetail['t_fb_social_identifier'] = (isset($user->user['id'])) ? $user->user['id'] : "" ;
            $teenagerDetail['t_social_provider'] = "Facebook";
            $teenagerDetail['t_fb_social_accesstoken'] = (isset($user->token)) ? $user->token : "" ;
            $teenagerDetail['deleted'] = '1';
            $teenagerDetail['t_isverified'] = '1';
            $teenagerDetail['gender'] = $gender;
            $teenagerDetail['nickname'] = $nickname;
            
            $teenagerWithSocialId = $this->teenagersRepository->getTeenagerBySocialId($teenagerDetail['t_fb_social_identifier'], $teenagerDetail['t_social_provider']);
            $teenagerWithEmailId = $this->teenagersRepository->getTeenagerDetailByEmailId($teenagerDetail['t_email']);

            if( isset($teenagerWithSocialId->id) && isset($teenagerWithEmailId->id) )
            {
                if($teenagerWithSocialId->id == $teenagerWithEmailId->id)
                {
                    //If user deleted then not login into system 
                    if($teenagerWithSocialId->deleted == Config::get('constant.DELETED_FLAG'))
                    {
                        return Redirect::to($loginUrl)->with('error', trans('labels.userisnolongeractive'));
                    }
                    //Login using id
                    if(Auth::guard('teenager')->loginUsingId($teenagerWithSocialId->id))
                    {
                        return Redirect::to($homeUrl)->with('success', trans('labels.welcome_profile_message'));
                    }
                } else {
                    //If user deleted then not login into system 
                    if($teenagerWithSocialId->deleted == Config::get('constant.DELETED_FLAG'))
                    {
                        return Redirect::to($loginUrl)->with('error', trans('labels.userisnolongeractive'));
                    }
                    //Update email address to set null. Because email exist on other id and we give priority to social ID 
                    $data = [];
                    $data['id'] = $teenagerWithSocialId->id;
                    $data['email'] = "";
                    $saveTeenagerDetail = $this->teenagersRepository->saveTeenagerDetail($data);
                    //Login using id
                    if(Auth::guard('teenager')->loginUsingId($teenagerWithSocialId->id))
                    {
                        return Redirect::to($homeUrl)->with('success', trans('labels.welcome_profile_message'));
                    }
                }
            } else if( isset($teenagerWithSocialId->id) ) {
                //If user deleted then not login into system 
                if($teenagerWithSocialId->deleted == Config::get('constant.DELETED_FLAG'))
                {
                    return Redirect::to($loginUrl)->with('error', trans('labels.userisnolongeractive'));
                }
                //Update Teenager details
                $data = [];
                $data['id'] = $teenagerWithSocialId->id;
                $data['deleted'] = $teenagerDetail['deleted'];
                $data['t_isverified'] = $teenagerDetail['t_isverified'];
                if($teenagerWithSocialId->t_email == "")
                {
                    $data['t_email'] = $teenagerDetail['t_email'];
                }
                $saveTeenagerDetail = $this->teenagersRepository->saveTeenagerDetail($data);
                
                //Login using id
                if(Auth::guard('teenager')->loginUsingId($teenagerWithSocialId->id))
                {
                    return Redirect::to($homeUrl)->with('success', trans('labels.welcome_profile_message'));
                }
            } else if ( isset($teenagerWithEmailId->id) ) {
                //If user deleted then not login into system 
                if($teenagerWithEmailId->deleted == Config::get('constant.DELETED_FLAG'))
                {
                    return Redirect::to($loginUrl)->with('error', trans('labels.userisnolongeractive'));
                }
                //Update Teenager details
                $data = [];
                $data['id'] = $teenagerWithEmailId->id;
                $data['t_fb_social_identifier'] = $teenagerDetail['t_fb_social_identifier'];
                $data['t_social_provider'] = $teenagerDetail['t_social_provider'];
                $data['t_fb_social_accesstoken'] = $teenagerDetail['t_fb_social_accesstoken'];
                $data['deleted'] = $teenagerDetail['deleted'];
                $data['t_isverified'] = $teenagerDetail['t_isverified'];
                $saveTeenagerDetail = $this->teenagersRepository->saveTeenagerDetail($data);
                //Login using id
                if(Auth::guard('teenager')->loginUsingId($teenagerWithEmailId->id))
                {
                    return Redirect::to($homeUrl)->with('success', trans('labels.welcome_profile_message'));
                }
            } else {
                $saveTeenagerDetail = $this->teenagersRepository->saveTeenagerDetail($teenagerDetail);
                //Login using id
                if(isset($saveTeenagerDetail->id) && Auth::guard('teenager')->loginUsingId($saveTeenagerDetail->id))
                {
                    return Redirect::to($homeUrl)->with('success', trans('labels.welcome_profile_message'));
                }
            }
            return Redirect::to($loginUrl)->with('error',trans('labels.somethingwronginsocialsignin'));
        } catch(Exception $e) {
            return \Redirect::to($loginUrl)->with('error', trans('labels.somethingwronginsocialsignin'));
        }
    }

    //GooglePlus redirect handle
    public function handleProviderCallbackGooglePlus(Request $request)
    {
        if (!$request->has('code') || $request->has('denied')) {
            return redirect('teenager/login');
        }
        //Need to set update field for each cases. Handle profile pic and phone unique. Handle deleted operation and username unique operation
        try
        {
            $user = Socialite::driver('google')->user();
            
            $loginUrl = "teenager/login";
            $homeUrl = "teenager/home";
            $name = (isset($user->name)) ? ucfirst($user->name) : "" ;
            $email = (isset($user->email)) ? $user->email : "" ;
            $gender = (isset($user->user['gender'])) ? ($user->user['gender'] == "male") ? 1 : 2 : "";
            $nickname = (isset($user->nickname)) ? $user->nickname : ""; 

            //We assumed, email is compulsory for system
            if ($email == '' || $email == null)
            {
                return Redirect::to($loginUrl)->with('error', trans('labels.emailpermistionmustberequired'));
            }

            $teenagerDetail = [];
            $teenagerDetail['t_uniqueid'] = Helpers::getTeenagerUniqueId();
            $teenagerDetail['t_name'] = $name;
            $teenagerDetail['t_email'] = $email;
            //$teenagerDetail['password'] = (isset($user->id)) ? $user->id : "" ;
            $teenagerDetail['t_social_identifier'] = (isset($user->id)) ? $user->id : "" ;
            $teenagerDetail['t_social_provider'] = "Google";
            $teenagerDetail['t_social_accesstoken'] = (isset($user->token)) ? $user->token : "" ;
            $teenagerDetail['deleted'] = '1';
            $teenagerDetail['gender'] = $gender;
            $teenagerDetail['nickname'] = $nickname;
            $teenagerDetail['t_isverified'] = '1';
            
            $teenagerWithSocialId = $this->teenagersRepository->getTeenagerBySocialId($teenagerDetail['t_social_identifier'], $teenagerDetail['t_social_provider']);
            $teenagerWithEmailId = $this->teenagersRepository->getTeenagerDetailByEmailId($teenagerDetail['t_email']);

            if( isset($teenagerWithSocialId->id) && isset($teenagerWithEmailId->id) )
            {
                if($teenagerWithSocialId->id == $teenagerWithEmailId->id)
                {
                    //If user deleted then not login into system 
                    if($teenagerWithSocialId->deleted == Config::get('constant.DELETED_FLAG'))
                    {
                        return Redirect::to($loginUrl)->with('error', trans('labels.userisnolongeractive'));
                    }
                    //Login using id
                    if(Auth::guard('teenager')->loginUsingId($teenagerWithSocialId->id))
                    {
                        return Redirect::to($homeUrl)->with('success', trans('labels.welcome_profile_message'));
                    }
                } else {
                    //If user deleted then not login into system 
                    if($teenagerWithSocialId->deleted == Config::get('constant.DELETED_FLAG'))
                    {
                        return Redirect::to($loginUrl)->with('error', trans('labels.userisnolongeractive'));
                    }
                    //Update email address to set null. Because email exist on other id and we give priority to social ID 
                    $data = [];
                    $data['id'] = $teenagerWithSocialId->id;
                    $data['email'] = "";
                    $saveTeenagerDetail = $this->teenagersRepository->saveTeenagerDetail($data);
                    //Login using id
                    if(Auth::guard('teenager')->loginUsingId($teenagerWithSocialId->id))
                    {
                        return Redirect::to($homeUrl)->with('success', trans('labels.welcome_profile_message'));
                    }
                }
            } else if( isset($teenagerWithSocialId->id) ) {
                //If user deleted then not login into system 
                if($teenagerWithSocialId->deleted == Config::get('constant.DELETED_FLAG'))
                {
                    return Redirect::to($loginUrl)->with('error', trans('labels.userisnolongeractive'));
                }
                //Update Teenager details
                $data = [];
                $data['id'] = $teenagerWithSocialId->id;
                $data['deleted'] = $teenagerDetail['deleted'];
                $data['t_isverified'] = $teenagerDetail['t_isverified'];
                if($teenagerWithSocialId->t_email == "")
                {
                    $data['t_email'] = $teenagerDetail['t_email'];
                }
                $saveTeenagerDetail = $this->teenagersRepository->saveTeenagerDetail($data);
                
                //Login using id
                if(Auth::guard('teenager')->loginUsingId($teenagerWithSocialId->id))
                {
                    return Redirect::to($homeUrl)->with('success', trans('labels.welcome_profile_message'));
                }
            } else if ( isset($teenagerWithEmailId->id) ) {
                //If user deleted then not login into system 
                if($teenagerWithEmailId->deleted == Config::get('constant.DELETED_FLAG'))
                {
                    return Redirect::to($loginUrl)->with('error', trans('labels.userisnolongeractive'));
                }
                //Update Teenager details
                $data = [];
                $data['id'] = $teenagerWithEmailId->id;
                $data['t_social_identifier'] = $teenagerDetail['t_social_identifier'];
                $data['t_social_provider'] = $teenagerDetail['t_social_provider'];
                $data['t_social_accesstoken'] = $teenagerDetail['t_social_accesstoken'];
                $data['deleted'] = $teenagerDetail['deleted'];
                $data['t_isverified'] = $teenagerDetail['t_isverified'];
                $saveTeenagerDetail = $this->teenagersRepository->saveTeenagerDetail($data);
                //Login using id
                if(Auth::guard('teenager')->loginUsingId($teenagerWithEmailId->id))
                {
                    return Redirect::to($homeUrl)->with('success', trans('labels.welcome_profile_message'));
                }
            } else {
                $saveTeenagerDetail = $this->teenagersRepository->saveTeenagerDetail($teenagerDetail);
                //Login using id
                if(isset($saveTeenagerDetail->id) && Auth::guard('teenager')->loginUsingId($saveTeenagerDetail->id))
                {
                    return Redirect::to($homeUrl)->with('success', trans('labels.welcome_profile_message'));
                }
            }
            return Redirect::to($loginUrl)->with('error',trans('labels.somethingwronginsocialsignin'));
        } catch(Exception $e) {
            return \Redirect::to($loginUrl)->with('error', trans('labels.somethingwronginsocialsignin'));
        }
    }
}