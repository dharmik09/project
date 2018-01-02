<?php

namespace App\Http\Controllers\Webservice;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Services\Sponsors\Contracts\SponsorsRepository;
use Illuminate\Support\Facades\Auth;
use Helpers;
use Config;
use Storage;
use App\Country;
use App\VersionsList;
use App\State;
use App\City;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class RestLessController extends Controller
{
    protected $sponsorsRepository;

    public function __construct(SponsorsRepository $sponsorsRepository)
    {
        $this->objCountry = new Country();
        $this->objState = new State();
        $this->objCity = new City();
        $this->objVersionsList = new VersionsList();
        $this->sponsorsRepository = $sponsorsRepository;
        $this->sponsorOriginalImageUploadPath = Config::get('constant.SPONSOR_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->sponsorThumbImageUploadPath = Config::get('constant.SPONSOR_THUMB_IMAGE_UPLOAD_PATH');
        $this->log = new Logger('api-restless-controller');
        $this->log->pushHandler(new StreamHandler(storage_path().'/logs/monolog-'.date('m-d-Y').'.log'));
    }

    /* Request Params : apiVersion
    *  loginToken //If service call from update profile
    *  Maintain force-update and upddate availables using response params
    */
    public function apiVersion(Request $request)
    {
        $response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg')];
        $this->log->info('Get Versions list from table', array('api-name'=> 'apiVersion'));
        $getVersionsList = $this->objVersionsList->first(['force_update', 'ios_version', 'android_version', 'web_version']);
        
        if($getVersionsList) {
            $response['data'] = $getVersionsList->toArray();
            $response['status'] = 1;
            $response['message'] = trans('appmessages.default_success_msg');
        } else {
            $response['message'] = "Versions list not found!";
        }

        if(isset($request->loginToken) && $request->loginToken != "") {
            $this->log->info('Get Versions list with login - apiVersion', array('api-name'=> 'apiVersion'));
            $response['login'] = 1;
        }
        $this->log->info('Api - response', array('api-name'=> 'apiVersion'));
        return response()->json($response, 200);
        exit;
    }

    /* Request Params : getCountryList
    *  loginToken //If service call from update profile
    */
    public function getCountryList(Request $request)
    {
		$response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg') ] ;
    	$countryList = $this->objCountry->getAllCounries();
        if($countryList->count() > 0) {
            $response['status'] = 1;
            $response['message'] = trans('appmessages.default_success_msg');
            $response['data'] = $countryList->toArray();
        } else {
            $response['message'] = trans('appmessages.data_empty_msg');
        }
        if(isset($request->loginToken) && $request->loginToken != "") {
            $response['login'] = 1;
        }
    	return response()->json($response, 200);
    	exit;
    }
    /* Request Params : getSponsors
    *  loginToken //If service call from update profile
    */
    public function getSponsors(Request $request)
    {
        $response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg') ] ;
        $sponsorDetail = $this->sponsorsRepository->getApprovedSponsors();
        if($sponsorDetail->count() > 0) {
            foreach ($sponsorDetail as $sponsor) {
                $sponsor->sp_logo_thumb = (isset($sponsor->sp_photo) && $sponsor->sp_photo != "") ? Storage::url($this->sponsorThumbImageUploadPath . $sponsor->sp_photo) : Storage::url($this->sponsorThumbImageUploadPath . "proteen-logo.png");
                $sponsor->sp_logo = (isset($sponsor->sp_photo) && $sponsor->sp_photo != "") ? Storage::url($this->sponsorOriginalImageUploadPath . $sponsor->sp_photo) : Storage::url($this->sponsorOriginalImageUploadPath . "proteen-logo.png");
                $sponsor->sponsor_id = (isset($sponsor->id)) ? $sponsor->id : 0;
                $sponsor->sp_email = (isset($sponsor->sp_email)) ? $sponsor->sp_email : "";
                $sponsor->sp_admin_name = (isset($sponsor->sponsor->sp_admin_name)) ? $sponsor->sp_admin_name : "";
                $sponsor->sp_company_name = (isset($sponsor->sp_company_name)) ? $sponsor->sp_company_name : ""; 
            }
            $response['status'] = 1;
            $response['message'] = trans('appmessages.default_success_msg');
            $response['data'] = $sponsorDetail->toArray();
        } else {
            $response['message'] = trans('appmessages.data_empty_msg');
        }
        if(isset($request->loginToken) && $request->loginToken != "") {
            $response['login'] = 1;
        }
        return response()->json($response, 200);
        exit;
    }
}