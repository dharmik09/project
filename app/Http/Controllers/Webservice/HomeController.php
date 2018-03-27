<?php

namespace App\Http\Controllers\Webservice;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Services\Teenagers\Contracts\TeenagersRepository;
use Auth;
use Illuminate\Http\Request;
use App\Video;
use App\CMS;
use App\Testimonial;
use App\FAQ;
use Config;
use Input;
use Storage;
use Helpers;
use App\PaidComponent;
use App\DeductedCoins;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(TeenagersRepository $teenagersRepository)
    {
        //$this->middleware('admin.guest', ['except' => 'logout']);
        $this->cmsObj = new CMS;
        $this->objTestimonial = new Testimonial;
        $this->objFAQ = new FAQ;
        $this->faqThumbImageUploadPath = Config::get('constant.FAQ_THUMB_IMAGE_UPLOAD_PATH');
        $this->faqOriginalImageUploadPath = Config::get('constant.FAQ_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->objVideo = new Video();
        $this->teenagersRepository = $teenagersRepository;
        $this->objPaidComponent = new PaidComponent;
        $this->objDeductedCoins = new DeductedCoins;
        $this->log = new Logger('api-home-controller');
        $this->log->pushHandler(new StreamHandler(storage_path().'/logs/monolog-'.date('m-d-Y').'.log'));    
    }

    /* Request Params : help
    *  loginToken, userId
    *  Service after loggedIn user
    */
    public function help(Request $request)
    {
        $response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg') ] ;
        $teenager = $this->teenagersRepository->getTeenagerById($request->userId);
        if($request->userId != "" && $teenager) {
            $helps = $this->objFAQ->getAllFAQ();
            $data = [];
            if(isset($helps[0]->id) && !empty($helps[0])) {
                foreach($helps as $help) {
                    $help->f_photo  = ($help->f_photo != "") ? Storage::url($this->faqOriginalImageUploadPath.$help->f_photo) : Storage::url($this->faqOriginalImageUploadPath."proteen-logo.png");
                    $data[] = $help;
                }
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

    /* Request Params : helpSearch
    *  loginToken, userId, searchText
    *  Service after loggedIn user
    */
    public function helpSearch(Request $request)
    {
        $response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg') ] ;
        $teenager = $this->teenagersRepository->getTeenagerById($request->userId);
        if($request->userId != "" && $teenager) {
            if($request->searchText != "") {
                $helps = $this->objFAQ->getSearchedFAQ(trim($request->searchText));
            } else {
                $helps = $this->objFAQ->getAllFAQ();
            }

            $data = [];
            if(isset($helps[0]->id) && !empty($helps[0])) {
                foreach($helps as $help) {
                    $help->f_photo  = ($help->f_photo != "") ? Storage::url($this->faqOriginalImageUploadPath.$help->f_photo) : Storage::url($this->faqOriginalImageUploadPath."proteen-logo.png");
                    $data[] = $help;
                }
            }
            $response['searchText'] = $request->searchText;
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

    /* Request Params : saveConsumedCoinsDetails
     *  loginToken, userId, componentName, careerId
     *  Service after loggedIn user
     */
    public function saveConsumedCoinsDetails(Request $request)
    {
        $response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg') ] ;
        $teenager = $this->teenagersRepository->getTeenagerById($request->userId);
        if($request->userId != "" && $teenager) {
            if ($request->componentName != "") {
                $teenId = $request->userId;
                $componentName = $request->componentName;
                $professionId = (isset($request->careerId) && $request->careerId > 0) ? $request->careerId : 0;
                $componentsData = $this->objPaidComponent->getPaidComponentsData($componentName);
                $consumedCoins = $componentsData->pc_required_coins;
                if (isset($professionId) && $professionId != "" && $professionId > 0) {
                    $deductedCoinsDetail = $this->objDeductedCoins->getDeductedCoinsDetailById($teenId, $componentsData->id, 1, $professionId);
                } else {
                    $deductedCoinsDetail = $this->objDeductedCoins->getDeductedCoinsDetailByIdForLS($teenId, $componentsData->id, 1);
                }
                if (isset($deductedCoinsDetail) && count($deductedCoinsDetail) > 0) {
                    $days = Helpers::calculateRemainingDays($deductedCoinsDetail[0]->dc_end_date);
                    $response['status'] = 0;
                    $response['message'] = "Coins already consumed for this activity";
                    $response['data']['remainingDays'] = $days;
                    $updatedCoins = $this->teenagersRepository->getTeenagerById($teenId);
                    $response['data']['availableCoins'] = (isset($updatedCoins) && !empty($updatedCoins)) ? $updatedCoins->t_coins : 0;
                } else {
                    $remainingDays = 0;
                    $deductCoins = 0;
                    //deduct coin from user
                    $userDetail = $this->teenagersRepository->getUserDataForCoinsDetail($teenId);
                    if (!empty($userDetail)) {
                        $deductCoins = $userDetail['t_coins'] - $consumedCoins;
                    }
                    $returnData = $this->teenagersRepository->updateTeenagerCoinsDetail($teenId, $deductCoins);
                    $return = Helpers::saveDeductedCoinsData($teenId, 1, $consumedCoins, $componentName, $professionId);
                    if ($return) {
                        if (isset($professionId) && $professionId != "" && $professionId > 0) {
                            $updatedDeductedCoinsDetail = $this->objDeductedCoins->getDeductedCoinsDetailById($teenId, $componentsData->id, 1, $professionId);
                        } else {
                            $updatedDeductedCoinsDetail = $this->objDeductedCoins->getDeductedCoinsDetailByIdForLS($teenId, $componentsData->id, 1);
                        }
                        if (!empty($updatedDeductedCoinsDetail)) {
                            $remainingDays = Helpers::calculateRemainingDays($updatedDeductedCoinsDetail[0]->dc_end_date);
                            $response['status'] = 1; 
                            $response['message'] = trans('appmessages.default_success_msg');
                            $response['data']['remainingDays'] = $remainingDays;
                            $updatedCoins = $this->teenagersRepository->getTeenagerById($teenId);
                            $response['data']['availableCoins'] = (isset($updatedCoins) && !empty($updatedCoins)) ? $updatedCoins->t_coins : 0;
                        } else {
                            $response['status'] = 0; 
                            $response['message'] = "Something went wrong.";
                        }
                    } else {
                        $response['status'] = 0; 
                        $response['message'] = "Something went wrong.";
                    }

                    //Store log in System
                    if ($componentName == Config::get('constant.ADVANCE_ACTIVITY')) {
                        $coinsConsumedFor = "Advance activity";
                    } else if ($componentName == Config::get('constant.LEARNING_STYLE')) {
                        $coinsConsumedFor = "Learning guidance";
                    } else {
                        $coinsConsumedFor = "";
                    }
                    $this->log->info('User coins consumed for' . $coinsConsumedFor, array('userId' => $teenId));
                }
                $response['login'] = 1;
            } else {
                $response['login'] = 1;
                $response['status'] = 0;
                $response['message'] = trans('appmessages.missing_data_msg');
            }
        } else {
            $response['message'] = trans('appmessages.invalid_userid_msg') . ' or ' . trans('appmessages.notvarified_user_msg');
        }
        return response()->json($response, 200);
        exit;
    }
}