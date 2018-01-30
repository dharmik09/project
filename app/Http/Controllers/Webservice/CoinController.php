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
use App\Transactions;
use App\DeductedCoins;
use App\TemplateDeductedCoins;
use App\Teenagers;

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
        $this->objTransactions = new Transactions;
        $this->objDeductedCoins = new DeductedCoins;
        $this->objTemplateDeductedCoins = new TemplateDeductedCoins;
        $this->objTeenager = new Teenagers;
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

    /* Request Params : requestToParentForProCoins
     *  loginToken, userId, parentEmail
     *  Service after loggedIn user
     */
    public function requestToParentForProCoins(Request $request)
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
                    $response['message'] = "Request has been sent successfully";
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

    /* Request Params : getProCoinsTransactionsHistory
     *  loginToken, userId, userType, pageNo
     *  Service after loggedIn user
     */
    public function getProCoinsTransactionsHistory(Request $request)
    {
        $response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg') ] ;
        $teenager = $this->teenagersRepository->getTeenagerById($request->userId);
        if($request->userId != "" && $teenager) {
            $data = [];
            $pageNo = $request->pageNo;
            $response['transactionInfo'] = "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut sed risus consequat, volutpat dui id, vestibulum turpis.";
            $transactionDetail = $this->objTransactions->getTransactionsDetail($request->userId, $request->userType, $pageNo);
            foreach ($transactionDetail AS $key => $value) {
                $transactionsData = [];
                $transactionsData['name'] = $value->tn_billing_name;
                $transactionsData['email'] = $value->tn_email;
                $transactionsData['proCoins'] = $value->tn_coins;
                $transactionsData['transactionId'] = $value->tn_transaction_id;
                $transactionsData['paidAmount'] = $value->tn_amount;
                if ($value->tn_currency == 'USD') {
                    $transactionsData['currency'] =  Storage::url('img/dollar-symbol.png');
                } else if ($value->tn_currency == 'INR') {
                    $transactionsData['currency'] =  Storage::url('img/rupee-symbol.png');
                }
                $transactionsData['transactionDate'] = date('d M Y', strtotime($value->tn_trans_date));
                $data[] = $transactionsData;
            }
            $nextPageExist = $this->objTransactions->getTransactionsDetail($request->userId, $request->userType, $pageNo + 1);
            if (isset($nextPageExist) && count($nextPageExist) > 0) {
                $response['pageNo'] = $pageNo;
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

    /* Request Params : getProCoinsPromisePlusData
     *  loginToken, userId, userType, pageNo, searchText
     *  Service after loggedIn user
     */
    public function getProCoinsPromisePlusData(Request $request)
    {
        $response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg') ] ;
        $teenager = $this->teenagersRepository->getTeenagerById($request->userId);
        if($request->userId != "" && $teenager) {
            $data = [];
            $pageNo = $request->pageNo;
            $searchText = (isset($request->searchText) && !empty($request->searchText)) ? $request->searchText : "";
            $promisePlusDetails = $this->objDeductedCoins->getDeductedCoinsHistorySearch($request->userId, $request->userType, $pageNo, $searchText);
            foreach ($promisePlusDetails AS $key => $value) {
                $promisePlusData = [];
                $promisePlusData['componentName'] = $value->pc_element_name;
                $promisePlusData['professionName'] = (isset($value->pf_name) && !empty($value->pf_name)) ? $value->pf_name : "";
                $promisePlusData['consumedCoins'] = $value->dc_total_coins;
                $promisePlusData['startDate'] = date('d M Y', strtotime($value->dc_start_date));
                $promisePlusData['endDate'] = date('d M Y', strtotime($value->dc_end_date));
                $data[] = $promisePlusData;
            }
            $nextPageExist = $this->objDeductedCoins->getDeductedCoinsHistorySearch($request->userId, $request->userType, $pageNo + 1, $searchText);
            if (isset($nextPageExist) && count($nextPageExist) > 0) {
                $response['pageNo'] = $pageNo;
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

    /* Request Params : getProCoinsLearningGuidanceData
     *  loginToken, userId, userType, pageNo
     *  Service after loggedIn user
     */
    public function getProCoinsLearningGuidanceData(Request $request)
    {
        $response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg') ] ;
        $teenager = $this->teenagersRepository->getTeenagerById($request->userId);
        if($request->userId != "" && $teenager) {
            $data = [];
            $pageNo = $request->pageNo;
            $learningGuidanceDetails = $this->objDeductedCoins->getDeductedCoinsDetailForLSHistory($request->userId, $request->userType, $pageNo);
            foreach ($learningGuidanceDetails AS $key => $value) {
                $learningGuidanceData = [];
                $learningGuidanceData['componentName'] = $value->pc_element_name;
                $learningGuidanceData['consumedCoins'] = $value->dc_total_coins;
                $learningGuidanceData['startDate'] = date('d M Y', strtotime($value->dc_start_date));
                $learningGuidanceData['endDate'] = date('d M Y', strtotime($value->dc_end_date));
                $data[] = $learningGuidanceData;
            }
            $nextPageExist = $this->objDeductedCoins->getDeductedCoinsDetailForLSHistory($request->userId, $request->userType, $pageNo + 1);
            if (isset($nextPageExist) && count($nextPageExist) > 0) {
                $response['pageNo'] = $pageNo;
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

    /* Request Params : getProCoinsL4ConceptTemplateData
     *  loginToken, userId, userType, pageNo, searchText
     *  Service after loggedIn user
     */
    public function getProCoinsL4ConceptData(Request $request)
    {
        $response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg') ] ;
        $teenager = $this->teenagersRepository->getTeenagerById($request->userId);
        if($request->userId != "" && $teenager) {
            $data = [];
            $pageNo = $request->pageNo;
            $searchText = (isset($request->searchText) && !empty($request->searchText)) ? $request->searchText : "";
            $l4ConceptDetails = $this->objTemplateDeductedCoins->getDeductedCoinsDetailHistorySearch($request->userId, $request->userType, $pageNo, $searchText);
            foreach ($l4ConceptDetails AS $key => $value) {
                $l4ConceptData = [];
                $l4ConceptData['componentName'] = $value->gt_template_title;
                $l4ConceptData['professionName'] = (isset($value->pf_name) && !empty($value->pf_name)) ? $value->pf_name : "";
                $l4ConceptData['consumedCoins'] = $value->tdc_total_coins;
                $l4ConceptData['startDate'] = date('d M Y', strtotime($value->tdc_start_date));
                $l4ConceptData['endDate'] = date('d M Y', strtotime($value->tdc_end_date));
                $data[] = $l4ConceptData;
            }
            $nextPageExist = $this->objTemplateDeductedCoins->getDeductedCoinsDetailHistorySearch($request->userId, $request->userType, $pageNo + 1, $searchText);
            if (isset($nextPageExist) && count($nextPageExist) > 0) {
                $response['pageNo'] = $pageNo;
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

    /* Request Params : searchTeenagerToGiftCoins
     *  loginToken, userId, pageNo, searchText
     *  Service after loggedIn user
     */
    public function searchTeenagerToGiftCoins(Request $request)
    {
        $response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg') ] ;
        $teenager = $this->teenagersRepository->getTeenagerById($request->userId);
        if($request->userId != "" && $teenager) {
            $data = [];
            $pageNo = $request->pageNo;
            $searchText = (isset($request->searchText) && !empty($request->searchText)) ? $request->searchText : "";
            $searchArray = explode(",",$searchText);
            $activeTeenagers = $this->objTeenager->getSearchActiveTeenagersForGift($request->userId, $searchArray, $pageNo);
            foreach ($activeTeenagers AS $key => $value) {
                $teenData = [];
                $teenData['id'] = $value->id;
                $teenData['uniqueId'] = $value->t_uniqueid;
                $teenData['name'] = $value->t_name;
                $teenData['email'] = $value->t_email;
                $teenData['coins'] = $value->t_coins;
                if ($value->t_photo != '' && Storage::size($this->teenagerThumbImageUploadPath . $value->t_photo) > 0) {
                    $teenData['photo'] = Storage::url($this->teenagerThumbImageUploadPath . $value->t_photo);
                } else {
                    $teenData['photo'] = Storage::url($this->teenagerThumbImageUploadPath . 'proteen-logo.png');
                }
                $data[] = $teenData;
            }
            $coinDetail = $this->teenagersRepository->getUserDataForCoinsDetail($request->userId);
            $response['availableCoins'] = (isset($coinDetail) && !empty($coinDetail)) ? $coinDetail['t_coins'] : 0;
            $nextPageExist = $this->objTeenager->getSearchActiveTeenagersForGift($request->userId, $searchArray, $pageNo + 1);
            if (isset($nextPageExist) && count($nextPageExist) > 0) {
                $response['pageNo'] = $pageNo;
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

    /* Request Params : saveGiftedCoins
     *  loginToken, userId, giftedUserId, giftedCoins
     *  Service after loggedIn user
     */
    public function saveGiftedCoins(Request $request)
    {
        $response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg') ] ;
        $teenager = $this->teenagersRepository->getTeenagerById($request->userId);
        if($request->userId != "" && $teenager) {
            $giftedCoins = $request->giftedCoins;
            $saveData = [];
            $saveData['tcg_sender_id'] = $request->userId;
            $saveData['tcg_reciver_id'] = $request->giftedUserId;
            $saveData['tcg_total_coins'] = $giftedCoins;
            $saveData['tcg_gift_date'] = date('Y-m-d');
            $saveData['tcg_user_type'] = 1;
            $return = $this->objTeenagerCoinsGift->saveTeenagetGiftCoinsDetail($saveData);
            $deductCoins = 0;
            //deduct coin from user
            $userDetail = $this->teenagersRepository->getUserDataForCoinsDetail($request->userId);
            if (!empty($userDetail)) {
                $deductCoins = $userDetail['t_coins'] - $giftedCoins;
            }
            $result = $this->teenagersRepository->updateTeenagerCoinsDetail($request->userId, $deductCoins);
            //Add icons to otehr user
            $userData = $this->teenagersRepository->getUserDataForCoinsDetail($request->giftedUserId);
            if (!empty($userData)) {
                $giftedCoins += $userData['t_coins'];
            }
            $result = $this->teenagersRepository->updateTeenagerCoinsDetail($request->giftedUserId, $giftedCoins);

            //Mail to both users
            //Login user mail
            $userArray = $this->teenagersRepository->getTeenagerByTeenagerId($request->userId);
            $otherUserArray = $this->teenagersRepository->getTeenagerByTeenagerId($request->giftedUserId);
            $replaceArray = array();
            $replaceArray['TEEN_NAME'] = $userArray['t_name'];
            $replaceArray['COINS'] = $giftedCoins;
            $replaceArray['TO_USER'] = $otherUserArray['t_name'];
            $emailTemplateContent = $this->templateRepository->getEmailTemplateDataByName(Config::get('constant.GIFTED_COINS_TEMPLATE'));
            $content = $this->templateRepository->getEmailContent($emailTemplateContent->et_body, $replaceArray);
            $data = array();
            $data['subject'] = $emailTemplateContent->et_subject;
            $data['toEmail'] = $userArray['t_email'];
            $data['toName'] = $userArray['t_name'];
            $data['content'] = $content;

            Mail::send(['html' => 'emails.Template'], $data, function ($m) use ($data) {
                $m->from(Config::get('constant.FROM_MAIL_ID'), 'Gift ProCoins');
                $m->subject($data['subject']);
                $m->to($data['toEmail'], $data['toName']);
            });

            //Other user mail
            $replaceArray = array();
            $replaceArray['TEEN_NAME'] = $otherUserArray['t_name'];
            $replaceArray['COINS'] = $giftedCoins;
            $replaceArray['FROM_USER'] = $userArray['t_name'];
            $emailTemplateContent = $this->templateRepository->getEmailTemplateDataByName(Config::get('constant.COINS_RECEIBED_TEMPLATE'));
            $content = $this->templateRepository->getEmailContent($emailTemplateContent->et_body, $replaceArray);
            $data = array();
            $data['subject'] = $emailTemplateContent->et_subject;
            $data['toEmail'] = $otherUserArray['t_email'];
            $data['toName'] = $otherUserArray['t_name'];
            $data['content'] = $content;
            Mail::send(['html' => 'emails.Template'], $data, function ($m) use ($data) {
                $m->from(Config::get('constant.FROM_MAIL_ID'), 'Gift ProCoins');
                $m->subject($data['subject']);
                $m->to($data['toEmail'], $data['toName']);
            });
            $responseData = [];
            $userDetail = $this->teenagersRepository->getUserDataForCoinsDetail($request->userId);
            if (!empty($userDetail)) {
                $responseData['availableCoins'] = $userDetail['t_coins'];
            }
            $response['login'] = 1;
            $response['status'] = 1;
            $response['message'] = trans('appmessages.default_success_msg');
            $response['data'] = $responseData;
        } else {
            $response['message'] = trans('appmessages.invalid_userid_msg') . ' or ' . trans('appmessages.notvarified_user_msg');
        }
        return response()->json($response, 200);
        exit;
    }
}