<?php

namespace App\Http\Controllers\Webservice;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Auth;
use Illuminate\Http\Request;
use App\Services\Teenagers\Contracts\TeenagersRepository;
use App\Services\Template\Contracts\TemplatesRepository;
use Config;
use Storage;
use Helpers;
use Mail;
use App\Services\Coin\Contracts\CoinRepository;

class CoinController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(TeenagersRepository $teenagersRepository, TemplatesRepository $templateRepository, CoinRepository $coinRepository)
    {
        $this->teenagersRepository = $teenagersRepository;
        $this->templateRepository = $templateRepository;
        $this->teenagerThumbImageUploadPath = Config::get('constant.TEEN_THUMB_IMAGE_UPLOAD_PATH');
        $this->coinRepository = $coinRepository;
        $this->coinsOriginalImageUploadPath = Config::get('constant.COINS_ORIGINAL_IMAGE_UPLOAD_PATH');
    }

    /* Request Params : getProCoinsPackages
     *  loginToken, userId
     *  Service after loggedIn user
     */
    public function getProCoinsPackages(Request $request)
    {
        $response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg') ] ;
        $teenager = $this->teenagersRepository->getTeenagerById($request->userId);
        if($request->userId != "" && $teenager) {
            $data = [];
            $coinsDetail = $this->coinRepository->getAllCoinsPackageDetail(Config::get('constant.COIN_PACKAGE_TEENAGER_TYPE'));
            $teenData = $this->teenagersRepository->getTeenagerByTeenagerId($request->userId);
            foreach ($coinsDetail AS $key => $value) {
                if ($value->currency == 2) {
                    $value->currency = Storage::url('img/dollar-symbol.png');
                } else if ($value->currency == 1) {
                    $value->currency = Storage::url('img/rupee-symbol.png');
                }
                $url = '';
                $value->price = intval($value->price);
                if ($value->c_image != '' && Storage::size($this->coinsOriginalImageUploadPath . $value->c_image) > 0) {
                    $url = Storage::url($this->coinsOriginalImageUploadPath . $value->c_image);
                } else {
                    $url = Storage::url($this->coinsOriginalImageUploadPath . "proteen-logo.png");
                }
                $value->c_image = $url;
            }
            $data['coinsPackage'] = $coinsDetail;
            $data['availableCoins'] = (isset($teenData) && !empty($teenData)) ? $teenData['t_coins'] : 0;
            $response['login'] = 1;
            $response['status'] = 1;
            $response['message'] = trans('appmessages.default_success_msg');
            $response['data'] = $data;
        } else {
            $response['message'] = trans('appmessages.invalid_userid_msg') . ' or ' . trans('appmessages.notvarified_user_msg');
        }
        return response()->json($response, 200);
        exit;
    }
}