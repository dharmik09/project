<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use Auth;
use Image;
use Input;
use Redirect;
use Config;
use Helpers;
use Illuminate\Http\Request;
use App\Services\Coin\Contracts\CoinRepository;
use App\Coins;
use App\PurchasedCoins;
use App\TeenagerCoinsGift;
use App\Services\Teenagers\Contracts\TeenagersRepository;
use App\Services\Parents\Contracts\ParentsRepository;
use App\Services\Template\Contracts\TemplatesRepository;
use App\Services\Level4Activity\Contracts\Level4ActivitiesRepository;
use App\DeductedCoins;
use Mail;
use App\Transactions;
use App\TeenParentRequest;
use App\PaidComponent;
use App\Configurations;
use App\TemplateDeductedCoins;
use Softon\Indipay\Facades\Indipay;

class CoinManagementController extends Controller {

    public function __construct(CoinRepository $coinRepository, TeenagersRepository $teenagersRepository, ParentsRepository $parentsRepository, TemplatesRepository $templatesRepository, Level4ActivitiesRepository $level4ActivitiesRepository) {
        $this->objCoins = new Coins();
        $this->coinRepository =  $coinRepository;
        $this->teenagersRepository = $teenagersRepository;
        $this->parentsRepository = $parentsRepository;
        $this->templateRepository = $templatesRepository;
        $this->level4ActivitiesRepository = $level4ActivitiesRepository;
        $this->loggedInUser = Auth::guard('parent');
    }

    public function display() {
        $coinsDetail = $this->coinRepository->getAllCoinsDetail(1);
        $parentId = $this->loggedInUser->user()->id;
        $parentData = $this->parentsRepository->getParentById($parentId);
        $objTransactions = new Transactions();
        $transactionsDetail = $objTransactions->getTransactionsDetail($parentId,2);
        $day = '';
        if (isset($transactionsDetail) && !empty($transactionsDetail) && count($transactionsDetail) > 0) {
            $Current_time = strtotime(date('Y-m-d'));

            $coinsData = $this->coinRepository->getAllCoinsDetailByid($transactionsDetail[0]->tn_package_id);
            $end_time = strtotime($transactionsDetail[0]->tn_trans_date . "+".$coinsData[0]->c_valid_for." days");

            $final_date = round(abs($end_time - $Current_time) / 86400, 2);
            if ($end_time > $Current_time) {
                $day = round($final_date);
            }
        }
        return view('parent.coinsPackage', compact('coinsDetail', 'parentData','day'));
        exit;
    }

    public function saveCoinPurchasedData($id) {
        $coinsDetail = $this->coinRepository->getAllCoinsDetailByid($id);
        if (!empty($coinsDetail)) {
            $parentId = $this->loggedInUser->user()->id;
            $amount = $coinsDetail[0]->c_price;
            $parameters = [
                  'tid' => $parentId.time(),
                  'order_id' => time(),
                  'amount' => $amount,
                  'merchant_param1' => '2',
                  'merchant_param2' => $id,
            ];

            $order = Indipay::prepare($parameters);
            return Indipay::process($order);
        }
    }

    public function getGiftCoins() {
        if (Auth::guard('parent')->check()) {
            $parentid = $this->loggedInUser->user()->id;
            $objTeenagerCoinsGift = new TeenagerCoinsGift();
            $parentCoinsDetail = $objTeenagerCoinsGift->getTeenagerCoinsGiftDetailForParent($parentid,2);

            return view('parent.showGiftedCoins', compact('parentCoinsDetail'));
        }
        return view('parent.login'); exit;
    }

    public function userSearchForShowGiftCoins() {
        $searchKeyword = Input::get('search_keyword');
        $parentId = Input::get('parentId');
        $searchArray = explode(",",$searchKeyword);

        $objTeenagerCoinsGift = new TeenagerCoinsGift();
        if ($searchKeyword != '') {
            $parentCoinsDetail = $objTeenagerCoinsGift->getTeenagerCoinsGiftDetailNameForParent($parentId,2,$searchArray);

            return view('parent.searchGiftedCoins', compact('parentCoinsDetail'));
            exit;
        } else {
            $parentCoinsDetail = $objTeenagerCoinsGift->getTeenagerCoinsGiftDetailForParent($parentId,2);

            return view('parent.searchGiftedCoins', compact('parentCoinsDetail'));
            exit;
        }
    }

    public function getConsumption() {
        if (Auth::guard('parent')->check()) {
            $parentid = $this->loggedInUser->user()->id;
            $objDeductedCoins = new DeductedCoins();

            $deductedCoinsDetail = $objDeductedCoins->getDeductedCoinsDetailForPS($parentid,2);

            $deductedCoinsDetailLS = $objDeductedCoins->getDeductedCoinsDetailForLS($parentid,2);
            $objTemplateDeductedCoins = new TemplateDeductedCoins();

            $deductedTemplateCoinsDetail = $objTemplateDeductedCoins->getDeductedCoinsDetail($parentid,2);

            return view('parent.showConsumptionCoins', compact('deductedCoinsDetail','deductedTemplateCoinsDetail','deductedCoinsDetailLS'));
        }
        return view('parent.login'); exit;
    }

    public function getTransaction() {
        if (Auth::guard('parent')->check()) {
            $parentid = $this->loggedInUser->user()->id;
            $objTransactions = new Transactions();

            $transactionDetail = $objTransactions->getTransactionsDetail($parentid,2);

            return view('parent.showTransaction', compact('transactionDetail'));
        }
        return view('parent.login'); exit;
    }

    public function giftcoinstoTeenager() {
       if (Auth::guard('parent')->check()) {
            $parentid = $this->loggedInUser->user()->id;
            $teenagerId = Input::get('teen_id');
            $userDetail = $this->teenagersRepository->getTeenagerByTeenagerId($teenagerId);

            return view('parent.giftCoinsToTeenager', compact('userDetail'));
            exit;
        }
        return view('parent.login'); exit;
    }

    public function saveGiftedCoinsDetail() {
        if (Auth::guard('parent')->check()) {
            $id = e(Input::get('id'));
            $giftcoins = e(Input::get('t_coins'));
            $parentId = $this->loggedInUser->user()->id;
            $objGiftUser = new TeenagerCoinsGift();
            $r_coins = 0;
            $parentDetail = $this->parentsRepository->getParentDataForCoinsDetail($parentId);
            if (!empty($parentDetail)) {
                $r_coins = $parentDetail['p_coins']-$giftcoins;
            }
            if ($giftcoins > $r_coins) {
                return Redirect::to("parent/home")->with('error', trans('labels.validcoinsparent'));
            } else {
                $saveData = [];
                $saveData['tcg_sender_id'] = $parentId;
                $saveData['tcg_reciver_id'] = $id;
                $saveData['tcg_total_coins'] = $giftcoins;
                $saveData['tcg_gift_date'] = date('Y-m-d');
                $saveData['tcg_user_type'] = 2;
                $return = $objGiftUser->saveTeenagetGiftCoinsDetail($saveData);

                //add coins to teenager
                $coins = 0;
                $userData = $this->teenagersRepository->getUserDataForCoinsDetail($id);
                if (!empty($userData)) {
                    $coins = $userData['t_coins']+$giftcoins;
                }
                $result = $this->teenagersRepository->updateTeenagerCoinsDetail($id, $coins);

                //deduct coins from parent account
                $parentDetail = $this->parentsRepository->getParentDataForCoinsDetail($parentId);
                if (!empty($parentDetail)) {
                    $giftcoins = $parentDetail['p_coins']-$giftcoins;
                }
                $result = $this->parentsRepository->updateParentCoinsDetail($parentId, $giftcoins);

                //Mail to both users
                //mail to parent

                $parentData = $this->parentsRepository->getParentById($parentId);
                $teenagerDetail = $this->teenagersRepository->getTeenagerByTeenagerId($id);

                $replaceArray = array();
                $replaceArray['TEEN_NAME'] = $parentData->p_first_name ." " .$parentData->p_last_name;
                $replaceArray['COINS'] = e(Input::get('t_coins'));
                $replaceArray['TO_USER'] = $teenagerDetail['t_name'];
                $emailTemplateContent = $this->templateRepository->getEmailTemplateDataByName(Config::get('constant.GIFTED_COINS_TEMPLATE'));
                $content = $this->templateRepository->getEmailContent($emailTemplateContent->et_body, $replaceArray);

                $data = array();
                $data['subject'] = $emailTemplateContent->et_subject;
                $data['toEmail'] = $parentData->p_email;
                $data['toName'] = $parentData->p_first_name;
                $data['content'] = $content;

                Mail::send(['html' => 'emails.Template'], $data , function ($m) use ($data) {
                    $m->from(Config::get('constant.FROM_MAIL_ID'), 'Gift ProCoins');
                    $m->subject($data['subject']);
                    $m->to($data['toEmail'], $data['toName']);
                });

                //mail to teenager

                $replaceArray = array();
                $replaceArray['TEEN_NAME'] = $teenagerDetail['t_name'];
                $replaceArray['COINS'] = e(Input::get('t_coins'));
                $replaceArray['FROM_USER'] = $parentData->p_first_name ." " .$parentData->p_last_name;
                $emailTemplateContent = $this->templateRepository->getEmailTemplateDataByName(Config::get('constant.COINS_RECEIBED_TEMPLATE'));
                $content = $this->templateRepository->getEmailContent($emailTemplateContent->et_body, $replaceArray);
                $data = array();
                $data['subject'] = $emailTemplateContent->et_subject;
                $data['toEmail'] = $teenagerDetail['t_email'];
                $data['toName'] = $teenagerDetail['t_name'];
                $data['content'] = $content;

                Mail::send(['html' => 'emails.Template'], $data , function ($m) use ($data) {
                    $m->from(Config::get('constant.FROM_MAIL_ID'), 'Gift Coins');
                    $m->subject($data['subject']);
                    $m->to($data['toEmail'], $data['toName']);
                });
                return Redirect::to("parent/home")->with('success', trans('labels.coinsgiftsuccess'));
            }
        }
        return view('parent.login'); exit;
     }

     public function getrequestbyteen() {
        if (Auth::guard('parent')->check()) {
            $parentid = $this->loggedInUser->user()->id;
            $objTeenParentRequest = new TeenParentRequest();

            $userDetail = $objTeenParentRequest->getTeenParentRequestDetail($parentid);

            return view('parent.showTeenParentRequest', compact('userDetail'));
            exit;
        }
        return view('parent.login'); exit;
     }

     public function acceptTeenRequest() {
        if (Auth::guard('parent')->check()) {
            $parentid = $this->loggedInUser->user()->id;
            $teenagerId = Input::get('teen_id');
            $objTeenParentRequest = new TeenParentRequest();

            $result = $objTeenParentRequest->updateTeenParentRequestDetail($parentid,$teenagerId);
            return "1";
            exit;
        }
        return view('parent.login'); exit;
    }

    public function getAvailableCoins() {
        if (Auth::guard('parent')->check()) {
            $parentId = Input::get('parentId');
            $objPaidComponent = new PaidComponent();
            $componentsData = $objPaidComponent->getPaidComponentsData('Parent Report');

            return $componentsData->pc_required_coins;
            exit;
        }
        return view('parent.login'); exit;
    }

    public function getAvailableCoinsForParent() {
        if (Auth::guard('parent')->check()) {
            $parentId = Input::get('parentId');
            $parentData = $this->parentsRepository->getParentById($parentId);

            return $parentData->p_coins;
            exit;
        }
        return view('parent.login'); exit;
    }

    public function getRemainigDays() {
        if (Auth::guard('parent')->check()) {
            $parentId = Input::get('parentId');
            $professionId = Input::get('profession');
            $objPaidComponent = new PaidComponent();
            $objDeductedCoins = new DeductedCoins();

            if ($professionId != 0) {
                $componentsData = $objPaidComponent->getPaidComponentsData(Config::get('constant.PROMISE_PLUS'));
                $deductedCoinsDetail = $objDeductedCoins->getDeductedCoinsDetailById($parentId,$professionId,2,$componentsData->id);
                $days = 0;
                if (!empty($deductedCoinsDetail)) {
                    $days = Helpers::calculateRemainingDays($deductedCoinsDetail[0]->dc_end_date);
                }
                /*$data = '<div class="promisebtn timer_btn">
                            <a href="javascript:void(0);" class="promise" title="" onclick="getPromisePlus('.$professionId.', '.$parentId.','.$days.');" data-ref="#'.$professionId.'">
                                <span class="promiseplus">PROMISE Plus</span>
                                <span class="coinouter">
                                    <span class="coinsnum">'.$days . ' Days Left</span>
                                </span>
                            </a>
                        </div>';*/
                return view('parent.getRemainingDays',compact('professionId' , 'parentId' , 'days'));
                //return $data;
                exit;
            } else {
                $componentsData = $objPaidComponent->getPaidComponentsData(Config::get('constant.LEARNING_STYLE'));
                $deductedCoinsDetail = $objDeductedCoins->getDeductedCoinsDetailByIdForLS($parentId,$componentsData->id,2);
                $days = 0;
                if (!empty($deductedCoinsDetail)) {
                    $days = Helpers::calculateRemainingDays($deductedCoinsDetail[0]->dc_end_date);
                }
                return view('parent.getRemainingDays',compact('days'));
                /*$data = '<span class="coinsnum">'.$days.' Days Left</span>';
                return $data;*/
                exit;
            }
        }
        return view('parent.login'); exit;
    }

     public function getCoinsForParent() {
        if (Auth::guard('parent')->check()) {
            $parentId = Input::get('parentId');
            $objPaidComponent = new PaidComponent();
            $componentsData = $objPaidComponent->getPaidComponentsData('Parent Report');
            $coins = $componentsData->pc_required_coins;
            $parentData = $this->parentsRepository->getParentById($parentId);

            if (!empty($parentData)) {
                if ($parentData->p_coins < $coins) {
                    return "1";
                    exit;
                }
            }
            return $parentData->p_coins;
            exit;
        }
        return view('parent.login'); exit;
    }

    public function getremainigdaysForReport() {
        if (Auth::guard('parent')->check()) {
            $parentId = Input::get('parentId');
            $objPaidComponent = new PaidComponent();
            $objDeductedCoins = new DeductedCoins();

            $componentsData = $objPaidComponent->getPaidComponentsData('Parent Report');
            $deductedCoinsDetail = $objDeductedCoins->getDeductedCoinsDetailByIdForLS($parentId,$componentsData->id,2);
            $days = 0;
            if (!empty($deductedCoinsDetail)) {
                $days = Helpers::calculateRemainingDays($deductedCoinsDetail[0]->dc_end_date);
            }
            return view('parent.getRemainingDaysForReport',compact('days'));
            /*$data = $days.' Days Left';
            return $data;*/
            exit;
        }
        return view('parent.login'); exit;
    }

    public function userSearchForCoins() {
        // $searchKeyword = Input::get('search_keyword');
        // $parentId = Input::get('parentId');

        $parentId = Auth::guard('parent')->user()->id;
        $searchKeyword = Input::get('searchText');
        $page = Input::get('page');
        $tab = Input::get('tab');
        $objDeductedCoins = new DeductedCoins();
        $objTemplateDeductedCoins = new TemplateDeductedCoins();
        //$deductedCoinsDetailLS = $objDeductedCoins->getDeductedCoinsDetailForLS($parentId,2);
        if ($searchKeyword != '') {
            if ($tab == 'promise_plus') {
                $deductedCoinsDetail = $objDeductedCoins->getDeductedCoinsDetailForPS($parentId,2,$searchKeyword);
                return view('parent.searchedPromisePlus', compact('deductedCoinsDetail')); 
            } else if ($tab == 'l4_concept_template') {
                $deductedTemplateCoinsDetail = $objTemplateDeductedCoins->getDeductedCoinsDetail($parentId, 2, $searchKeyword);
                return view('parent.searchedL4ConceptTemplate', compact('deductedTemplateCoinsDetail'));
            } else {
                $deductedCoinsDetailLS = $objDeductedCoins->getDeductedCoinsDetailForLS($parentId, 2);
                return view('parent.learningGuidanceData', compact('deductedCoinsDetailLS'));
            }
            //return view('parent.searchConsumedCoins', compact('deductedCoinsDetail', 'deductedTemplateCoinsDetail', 'searchKeyword','deductedCoinsDetailLS'));
            //exit;
        } else {
            if ($tab == 'promise_plus') {
                $deductedCoinsDetail = $objDeductedCoins->getDeductedCoinsDetailForPS($parentId,2);
                return view('parent.searchedPromisePlus', compact('deductedCoinsDetail')); 
            } else if ($tab == 'l4_concept_template') {
               $deductedTemplateCoinsDetail = $objTemplateDeductedCoins->getDeductedCoinsDetail($parentId, 2);
                return view('parent.searchedL4ConceptTemplate', compact('deductedTemplateCoinsDetail'));
            } else {
                $deductedCoinsDetailLS = $objDeductedCoins->getDeductedCoinsDetailForLS($parentId, 2);
                return view('parent.learningGuidanceData', compact('deductedCoinsDetailLS'));
            }
            // return view('parent.searchConsumedCoins', compact('deductedCoinsDetail','deductedTemplateCoinsDetail', 'searchKeyword','deductedCoinsDetailLS'));
            //exit;
        }
        exit;
    }

     public function getAvailableCoinsForTemplate() {
        if (Auth::guard('parent')->check()) {
            $professionId = Input::get('professionId');
            $template_id = Input::get('template_id');
            $getQuestionTemplateForProfession = $this->level4ActivitiesRepository->getConceptDataForCoinsDetail($professionId,$template_id);

            return $getQuestionTemplateForProfession[0]->gt_coins;
            exit;
        }
        return view('parent.login'); exit;
    }

    public function getCoinsForTemplate() {
        if (Auth::guard('parent')->check()) {
            $parentId = $this->loggedInUser->user()->id;
            $professionId = Input::get('professionId');
            $template_id = Input::get('template_id');
            $userData = $this->level4ActivitiesRepository->getTemplateDataForCoinsDetail($template_id);
            $coins = $userData['gt_coins'];
            $parentDetail = $this->parentsRepository->getParentDataForCoinsDetail($parentId);

            if (!empty($parentDetail)) {
                if ($parentDetail['p_coins'] < $coins) {
                    return $parentDetail['p_coins'];
                    exit;
                }
            }
            return "1";
            exit;
        }
        return view('parent.login'); exit;
    }

    public function saveConceptCoinsDetail() {
        if (Auth::guard('parent')->check()) {
            $parentId = $this->loggedInUser->user()->id;
            $professionId = Input::get('professionId');
            $template_id = Input::get('template_id');
            $attempted = Input::get('attempted');
            $objPaidComponent = new PaidComponent();
            $objTemplateDeductedCoins = new TemplateDeductedCoins();

            $deductedCoinsDetail = $objTemplateDeductedCoins->getDeductedCoinsDetailById($parentId,$professionId,$template_id,2);
            $days = 0;
            if (!empty($deductedCoinsDetail->toArray())) {
                $days = Helpers::calculateRemainingDays($deductedCoinsDetail[0]->tdc_end_date);
            }
            $userData = $this->level4ActivitiesRepository->getTemplateDataForCoinsDetail($template_id);
            $coins = $userData['gt_coins'];
            if ($days == 0 && $coins != 0 && $attempted == 'no') {
                $deductedCoins = $coins;
                $parentDetail = $this->parentsRepository->getParentDataForCoinsDetail($parentId);
                if (!empty($parentDetail)) {
                    $coins = $parentDetail['p_coins']-$coins;

                    $response = $this->parentsRepository->updateParentCoinsDetail($parentId, $coins);
                    $saveData = [];
                    $saveData['id'] = 0;
                    $saveData['tdc_user_id'] = $this->loggedInUser->user()->id;
                    $saveData['tdc_user_type'] = 2;
                    $saveData['tdc_profession_id'] = $professionId;
                    $saveData['tdc_template_id'] = $template_id;
                    $saveData['tdc_total_coins'] = $deductedCoins;
                    $saveData['tdc_start_date'] = date('y-m-d');;
                    $saveData['tdc_end_date'] = date('Y-m-d', strtotime("+". $userData['gt_valid_upto'] ." days"));

                    $response = $objTemplateDeductedCoins->saveDeductedCoinsDetail($saveData);
                }
            }
            return "1";
            exit;
        }
        return view('parent.login'); exit;
    }
}
