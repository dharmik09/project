<?php

namespace App\Http\Controllers\Teenager;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Helpers;
use Config;
use Mail;
use Session;
use Storage;
use App\Services\Teenagers\Contracts\TeenagersRepository;
use App\Services\Template\Contracts\TemplatesRepository;
use App\Services\Sponsors\Contracts\SponsorsRepository;
use Redirect;
use Response;
use App\Country;

class DashboardController extends Controller
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
    public function __construct(SponsorsRepository $sponsorsRepository, TeenagersRepository $teenagersRepository, TemplatesRepository $templatesRepository)
    {
        $this->teenagersRepository = $teenagersRepository;
        $this->sponsorsRepository = $sponsorsRepository;
        $this->middleware('teenager');
        $this->objCountry = new Country();
        $this->templateRepository = $templatesRepository;
        $this->teenOriginalImageUploadPath = Config::get('constant.TEEN_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->teenThumbImageUploadPath = Config::get('constant.TEEN_THUMB_IMAGE_UPLOAD_PATH');
        $this->teenProfileImageUploadPath = Config::get('constant.TEEN_PROFILE_IMAGE_UPLOAD_PATH');
    }

    //Dashboard data
    public function dashboard()
    {
        $data = [];
        $user = Auth::guard('teenager')->user();
        $data['user_profile'] = (Auth::guard('teenager')->user()->t_photo != "" && Storage::size(Auth::guard('teenager')->user()->t_photo) > 0) ? Storage::url($this->teenProfileImageUploadPath.Auth::guard('teenager')->user()->t_photo) : asset($this->teenProfileImageUploadPath.'proteen-logo.png');
        $data['user_profile_thumb'] = (Auth::guard('teenager')->user()->t_photo != "" && Storage::size(Auth::guard('teenager')->user()->t_photo) > 0) ? Storage::url($this->teenThumbImageUploadPath.Auth::guard('teenager')->user()->t_photo) : asset($this->teenThumbImageUploadPath.'proteen-logo.png');
        $teenagerAPIData = Helpers::getTeenAPIScore(Auth::guard('teenager')->user()->id);
        //echo "<pre/>"; print_r($teenagerAPIData); die();
        return view('teenager.home', compact('data', 'user'));
    }

    //My profile data
    public function profile()
    {
        $data = [];
        $user = Auth::guard('teenager')->user();
        $data['user_profile'] = (Auth::guard('teenager')->user()->t_photo != "" && Storage::size(Auth::guard('teenager')->user()->t_photo) > 0) ? Storage::url($this->teenProfileImageUploadPath.Auth::guard('teenager')->user()->t_photo) : asset($this->teenProfileImageUploadPath.'proteen-logo.png');
        $countries = $this->objCountry->getAllCounries();
        $sponsorDetail = $this->sponsorsRepository->getApprovedSponsors();
        
        return view('teenager.profile', compact('data', 'user', 'countries', 'sponsorDetail'));   
    }

    public function chat()
    {
        return view('teenager.chat');
    }
}
