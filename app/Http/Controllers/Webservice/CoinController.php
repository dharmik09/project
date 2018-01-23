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
use App\TeenParentRequest;
use App\Services\Parents\Contracts\ParentsRepository;
use App\TeenagerCoinsGift;

class CoinController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(TeenagersRepository $teenagersRepository, TemplatesRepository $templateRepository, CoinRepository $coinRepository, ParentsRepository $parentsRepository)
    {
        $this->teenagersRepository = $teenagersRepository;
        $this->templateRepository = $templateRepository;
        $this->teenagerThumbImageUploadPath = Config::get('constant.TEEN_THUMB_IMAGE_UPLOAD_PATH');
        $this->coinRepository = $coinRepository;
        $this->coinsOriginalImageUploadPath = Config::get('constant.COINS_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->objTeenParentRequest = new TeenParentRequest;
        $this->parentsRepository = $parentsRepository;
        $this->objTeenagerCoinsGift = new TeenagerCoinsGift;
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
            $data['coinInfo'] = "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse suscipit eget massa ac consectetur.";
            $data['availableCoins'] = (isset($teenData) && !empty($teenData)) ? $teenData['t_coins'] : 0;
            $data['coinsPackage'] = $coinsDetail;
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

    /* Request Params : requestToParent
     *  loginToken, userId, parentEmail
     *  Service after loggedIn user
     */
    public function requestToParent(Request $request)
    {
        $response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg') ] ;
        $teenager = $this->teenagersRepository->getTeenagerById($request->userId);
        if($request->userId != "" && $teenager) {
            $parent = $this->parentsRepository->getParentDetailByEmailId($request->parentEmail);
            if (!empty($parent)) {
                $checkPairAvailability = $this->parentsRepository->checkPairAvailability($request->userId, $parent['id']);
                if (!empty($checkPairAvailability)) {
                    $requestData = [];
                    $requestData['tpr_teen_id'] = $request->userId;
                    $requestData['tpr_parent_id'] = $parent['id'];
                    $requestData['tpr_status'] = 1;
                    $result = $this->objTeenParentRequest->saveTeenParentRequestDetail($requestData);
                    $userDetail = $this->teenagersRepository->getTeenagerByTeenagerId($request->userId);
                    $replaceArray = array();
                    $replaceArray['USER_NAME'] = $parent['p_first_name'];
                    $emailTemplateContent = $this->templateRepository->getEmailTemplateDataByName(Config::get('constant.PARENT_COINS_REQUEST_TEMPLATE'));
                    $content = $this->templateRepository->getEmailContent($emailTemplateContent->et_body, $replaceArray);
                    $data = array();
                    $data['subject'] = $emailTemplateContent->et_subject;
                    $data['toEmail'] = $parent['p_email'];
                    $data['toName'] = $parent['p_first_name'] ." ". $parent['p_last_name'];
                    $data['content'] = $content;
                    Mail::send(['html' => 'emails.Template'], $data , function ($m) use ($data) {
                        $m->from(Config::get('constant.FROM_MAIL_ID'), 'ProCoins Request By Teenger');
                        $m->subject($data['subject']);
                        $m->to($data['toEmail'], $data['toName']);
                    });

                    $response['status'] = 1;
                    $response['message'] = trans('appmessages.default_success_msg');
                } else {
                    $response['status'] = 0;
                    $response['message'] = trans('appmessages.parentteenvarify');
                }
            } else {
                $response['status'] = 0;
                $response['message'] = trans('appmessages.parent_email_invalid');
            }
            $response['login'] = 1;
            
        } else {
            $response['message'] = trans('appmessages.invalid_userid_msg') . ' or ' . trans('appmessages.notvarified_user_msg');
        }
        return response()->json($response, 200);
        exit;
    }

    /* Request Params : getGiftedCoinsHistory
     *  loginToken, userId, userType, pageNo
     *  Service after loggedIn user
     */
    public function getGiftedCoinsHistory(Request $request)
    {
        $response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg') ] ;
        $teenager = $this->teenagersRepository->getTeenagerById($request->userId);
        if($request->userId != "" && $teenager) {
            $data = [];
            $page = $request->pageNo;
            $teenData = $this->teenagersRepository->getTeenagerByTeenagerId($request->userId);
            $response['availableCoins'] = (isset($teenData) && !empty($teenData)) ? $teenData['t_coins'] : 0;
            $teenCoinsDetail = $this->objTeenagerCoinsGift->getTeenagerCoinsGiftDetailHistory($request->userId, $request->userType, $page);
            $finalData = [];
            foreach ($teenCoinsDetail AS $key => $value) {
                $finalData['name'] = $value->t_name;
                $finalData['email'] = $value->t_email;
                $finalData['proCoins'] = $value->tcg_total_coins;
                $finalData['giftDate'] = date('d M Y', strtotime($value->tcg_gift_date));
                $data[] = $finalData;
            }
            $nextPageExist = $this->objTeenagerCoinsGift->getTeenagerCoinsGiftDetailHistory($request->userId, $request->userType, $page + 1);
            if (isset($nextPageExist) && count($nextPageExist) > 0) {
                $response['pageNo'] = $page;
            } else {
                $response['pageNo'] = '-1';
            }
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