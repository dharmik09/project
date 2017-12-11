<?php

namespace App\Http\Controllers\Sponsor;

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
use App\Services\Sponsors\Contracts\SponsorsRepository;
use App\Services\Template\Contracts\TemplatesRepository;
use App\Services\Schools\Contracts\SchoolsRepository;
use App\DeductedCoins;
use App\PaidComponent;
use Mail;
use App\Transactions;
use App\Configurations;
use Softon\Indipay\Facades\Indipay;

class CoinManagementController extends Controller {

    public function __construct(CoinRepository $coinRepository, SponsorsRepository $sponsorsRepository,TemplatesRepository $templatesRepository,SchoolsRepository $schoolsRepository) {
        $this->objCoins = new Coins();
        $this->coinRepository =  $coinRepository;
        $this->sponsorsRepository = $sponsorsRepository;
        $this->templateRepository = $templatesRepository;
        $this->schoolsRepository = $schoolsRepository;
        $this->loggedInUser = Auth::guard('sponsor');
    }

    public function display() {
        $coinsDetail = $this->coinRepository->getAllCoinsDetail(2);
        $sponsorId = $this->loggedInUser->user()->id;
        $sponsorData = $this->sponsorsRepository->getSponsorBySponsorId($sponsorId);
        $objTransactions = new Transactions();
        $transactionsDetail = $objTransactions->getTransactionsDetail($sponsorId,4);
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
        return view('sponsor.coinsPackage', compact('coinsDetail','sponsorData','day'));
        exit;
    }

    public function saveCoinPurchasedData($id) {
        $coinsDetail = $this->coinRepository->getAllCoinsDetailByid($id);
        if (!empty($coinsDetail)) {
            $sponsorId = $this->loggedInUser->user()->id;
            $amount = $coinsDetail[0]->c_price;
            $parameters = [
                  'tid' => $sponsorId.time(),
                  'order_id' => time(),
                  'amount' => $amount,
                  'merchant_param1' => '4',
                  'merchant_param2' => $id,
            ];

            $order = Indipay::prepare($parameters);
            return Indipay::process($order);
        }
    }

    public function getGiftCoins() {
        if (Auth::guard('sponsor')->check()) {
            $sponsorid = $this->loggedInUser->user()->id;
            $objTeenagerCoinsGift = new TeenagerCoinsGift();
            $sponsorCoinsDetail = $objTeenagerCoinsGift->getTeenagerCoinsGiftDetail($sponsorid,4);

            return view('sponsor.showGiftedCoins', compact('sponsorCoinsDetail'));
        }
        return view('sponsor.login'); exit;
    }

    public function getConsumption() {
        if (Auth::guard('sponsor')->check()) {
            $sponsorid = $this->loggedInUser->user()->id;
            $objDeductedCoins = new DeductedCoins();

            $deductedCoinsDetail = $objDeductedCoins->getDeductedCoinsDetail($sponsorid,4);

            return view('sponsor.showConsumptionCoins', compact('deductedCoinsDetail'));
        }
        return view('sponsor.login'); exit;
    }

    public function getTransaction() {
        if (Auth::guard('sponsor')->check()) {
            $sponsorid = $this->loggedInUser->user()->id;
            $objTransactions = new Transactions();

            $transactionDetail = $objTransactions->getTransactionsDetail($sponsorid,4);

            return view('sponsor.showTransaction', compact('transactionDetail'));
        }
        return view('sponsor.login'); exit;
    }

    public function getAvailableCoins() {
        if (Auth::guard('sponsor')->check()) {
            $sponsorId = Input::get('sponsorId');
            $objPaidComponent = new PaidComponent();
            $objDeductedCoins = new DeductedCoins();
            $componentsData = $objPaidComponent->getPaidComponentsData('Enterprise Report');
            if(isset($componentsData) && !empty($componentsData)){
                $deductedCoinsDetail = $objDeductedCoins->getDeductedCoinsDetailByIdForLS($sponsorId,$componentsData[0]->id,4);
            }
            $days = 0;
            if (!empty($deductedCoinsDetail->toArray())) {
                $days = Helpers::calculateRemaningDays($deductedCoinsDetail[0]->dc_end_date);
            }
            return $days;
            exit;
        }
        return view('sponsor.login'); exit;
    }

     public function getAvailableCoinsForSponsor() {
        if (Auth::guard('sponsor')->check()) {
            $sponsorId = Input::get('sponsorId');
            $objPaidComponent = new PaidComponent();
            $objDeductedCoins = new DeductedCoins();
            $componentsData = $objPaidComponent->getPaidComponentsData('Enterprise Report');

            return $componentsData[0]->pc_required_coins;
            exit;
        }
        return view('sponsor.login'); exit;
    }

    function getCoinsForSponsor() {
        if (Auth::guard('sponsor')->check()) {
            $sponsorId = Input::get('sponsorId');
            $objPaidComponent = new PaidComponent();
            $componentsData = $objPaidComponent->getPaidComponentsData('Enterprise Report');
            $coins = $componentsData[0]->pc_required_coins;

            $sponsorData = $this->sponsorsRepository->getSponsorDataForCoinsDetail($sponsorId);
            if (!empty($sponsorData)) {
                if ($sponsorData['sp_credit'] < $coins) {
                    return "1";
                    exit;
                }
            }
            return $sponsorData['sp_credit'];
            exit;
        }
        return view('sponsor.login'); exit;
    }

    public function getremainigdaysForSponsor() {
        if (Auth::guard('sponsor')->check()) {
            $sponsorId = Input::get('sponsorId');
            $objPaidComponent = new PaidComponent();
            $objDeductedCoins = new DeductedCoins();

            $componentsData = $objPaidComponent->getPaidComponentsData('Enterprise Report');
            $deductedCoinsDetail = $objDeductedCoins->getDeductedCoinsDetailByIdForLS($sponsorId,$componentsData[0]->id,4);
            $days = 0;
            if (!empty($deductedCoinsDetail)) {
                $days = Helpers::calculateRemaningDays($deductedCoinsDetail[0]->dc_end_date);
            }
            return view('sponsor.gerRemaningDays',compact('days'));
            /*$data = $days.' Days Left';
            return $data;*/
            exit;
        }
        return view('sponsor.login'); exit;
    }

     public function giftcoinstoSchool() {
       if (Auth::guard('sponsor')->check()) {
            $sponsorid = $this->loggedInUser->user()->id;
            $userDetail = $this->sponsorsRepository->getSponsorById($sponsorid);

            return view('sponsor.giftCoinsToSchool', compact('userDetail'));
            exit;
        }
        return view('sponsor.login'); exit;
    }

     public function saveGiftedCoinsDetail() {
        if (Auth::guard('sponsor')->check()) {
            $school_uniqueid = e(Input::get('school_id'));
            $giftcoins = e(Input::get('t_coins'));
            $sponsorId = $this->loggedInUser->user()->id;
            $objGiftUser = new TeenagerCoinsGift();
            $r_coins = 0;
            $sponsorData = $this->sponsorsRepository->getSponsorDataForCoinsDetail($sponsorId);
            if (!empty($sponsorData)) {
                $r_coins = $sponsorData['sp_credit'];
            }
            if ($giftcoins > $r_coins) {
                return Redirect::to("sponsor/home")->with('error', trans('labels.validcoinsparent'));
            } else {
                $schoolExist = $this->schoolsRepository->checkActiveSchoolExist($school_uniqueid);
                 if (isset($schoolExist) && $schoolExist) {
                    return Redirect::to("sponsor/home")->with('error', trans('appmessages.schoolnotexist'));
                 }
                $saveData = [];
                $saveData['tcg_sender_id'] = $sponsorId;
                $saveData['tcg_reciver_id'] = $school_uniqueid;
                $saveData['tcg_total_coins'] = $giftcoins;
                $saveData['tcg_gift_date'] = date('Y-m-d');
                $saveData['tcg_user_type'] = 4;
                $return = $objGiftUser->saveTeenagetGiftCoinsDetail($saveData);

                //add coins to school
                $coins = 0;
                $schoolCoins = $giftcoins;
                $schoolData = $this->schoolsRepository->getSchoolDataForCoinsDetailByUniqueid($school_uniqueid);
                if (!empty($schoolData)) {
                    $schoolCoins += $schoolData['sc_coins'];
                }
                $result = $this->schoolsRepository->updateSchoolCoinsDetailByUniqueid($school_uniqueid, $schoolCoins);

                //deduct coins from parent account
                $gift_coins = $r_coins-$giftcoins;

                $result = $this->sponsorsRepository->updateSponsorCoinsDetail($sponsorId, $gift_coins);

                $sponsorDetail = $this->sponsorsRepository->getSponsorById($sponsorId);
                $schoolDetail = $this->schoolsRepository->getSchoolBySchoolUniqueid($school_uniqueid);
                //Mail to both users
                //mail to sponsor

                $replaceArray = array();
                $replaceArray['TEEN_NAME'] = $sponsorDetail->sp_first_name;
                $replaceArray['COINS'] = e(Input::get('t_coins'));
                $replaceArray['TO_USER'] = $schoolDetail['sc_name'];
                $emailTemplateContent = $this->templateRepository->getEmailTemplateDataByName(Config::get('constant.GIFTED_COINS_TEMPLATE'));
                $content = $this->templateRepository->getEmailContent($emailTemplateContent->et_body, $replaceArray);

                $data = array();
                $data['subject'] = $emailTemplateContent->et_subject;
                $data['toEmail'] = $sponsorDetail->sp_email;
                $data['toName'] = $sponsorDetail->sp_first_name;
                $data['content'] = $content;

                Mail::send(['html' => 'emails.Template'], $data , function ($m) use ($data) {
                    $m->from(Config::get('constant.FROM_MAIL_ID'), 'Gift ProCoins');
                    $m->subject($data['subject']);
                    $m->to($data['toEmail'], $data['toName']);
                });

                //mail to school

                $replaceArray = array();
                $replaceArray['TEEN_NAME'] = $schoolDetail['sc_name'];
                $replaceArray['COINS'] = e(Input::get('t_coins'));
                $replaceArray['FROM_USER'] = $sponsorDetail->sp_first_name;
                $emailTemplateContent = $this->templateRepository->getEmailTemplateDataByName(Config::get('constant.COINS_RECEIBED_TEMPLATE'));
                $content = $this->templateRepository->getEmailContent($emailTemplateContent->et_body, $replaceArray);
                $data = array();
                $data['subject'] = $emailTemplateContent->et_subject;
                $data['toEmail'] = $schoolDetail['sc_email'];
                $data['toName'] = $schoolDetail['sc_name'];
                $data['content'] = $content;

                Mail::send(['html' => 'emails.Template'], $data , function ($m) use ($data) {
                    $m->from(Config::get('constant.FROM_MAIL_ID'), 'Gift Coins');
                    $m->subject($data['subject']);
                    $m->to($data['toEmail'], $data['toName']);
                });
                return Redirect::to("sponsor/home")->with('success', trans('labels.coinsgiftsuccess'));
            }
        }
        return view('sponsor.login'); exit;
     }
}
