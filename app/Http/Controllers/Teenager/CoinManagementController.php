<?php

namespace App\Http\Controllers\Teenager;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Helpers;
use Config;
use App\Services\Teenagers\Contracts\TeenagersRepository;
use App\TeenagerCoinsGift;
use Input;
use App\Transactions;
use App\DeductedCoins;
use App\TemplateDeductedCoins;
use App\Services\Coin\Contracts\CoinRepository;
use App\PurchasedCoins;
use Softon\Indipay\Facades\Indipay;
use App\Services\Parents\Contracts\ParentsRepository;
use App\TeenParentRequest;
use App\Services\Template\Contracts\TemplatesRepository;
use Mail;
use Redirect;
use App\Teenagers;
use App\Notifications;

class CoinManagementController extends Controller
{
    
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(TeenagersRepository $teenagersRepository, CoinRepository $coinRepository, ParentsRepository $parentsRepository, TemplatesRepository $templatesRepository)
    {
        $this->teenagersRepository = $teenagersRepository;
        $this->objTransactions = new Transactions;
        $this->objDeductedCoins = new DeductedCoins;
        $this->objTemplateDeductedCoins = new TemplateDeductedCoins;
        $this->objPurchasedCoins = new PurchasedCoins;
        $this->objTeenParentRequest = new TeenParentRequest;
        $this->templateRepository = $templatesRepository;
        $this->coinRepository = $coinRepository;
        $this->parentsRepository = $parentsRepository;
        $this->objTeenager = new Teenagers;
        $this->objTeenagerCoinsGift = new TeenagerCoinsGift;
        $this->objNotifications = new Notifications();
    }

    /**
     * Gift Coin Data
     *
     * @return void
     */
    public function getGiftCoins() 
    {
        $teenId = Auth::guard('teenager')->user()->id;
        $teenCoinsDetail = $this->objTeenagerCoinsGift->getTeenagerCoinsGiftDetail($teenId, 1);
        $coinDetail = $this->teenagersRepository->getUserDataForCoinsDetail($teenId);
        return view('teenager.proCoinsGift', compact('teenCoinsDetail', 'coinDetail'));
    }

    public function userSearchToGiftCoins() 
    {
        $searchKeyword = Input::get('search_keyword');
        $teenId = Input::get('teenagerId');
        $searchArray = explode(",", $searchKeyword);
        $coinDetail = $this->teenagersRepository->getUserDataForCoinsDetail($teenId);
        if ($searchKeyword != "") {
            $activeTeenagers = $this->objTeenager->getMultipleActiveTeenagersForGiftCoins($teenId, $searchArray);
        } else {
            $activeTeenagers = Helpers::getActiveTeenagersForDashboard($teenId);
        }
        return view('teenager.searchGiftedCoins', compact('activeTeenagers', 'coinDetail'));
    }

    /**
     * ProCoin History Data
     *
     * @return void
     */
    public function getProCoinsHistory(Request $request) 
    {
        $teenId = Auth::guard('teenager')->user()->id;
        $tab = Input::get('get');
        $transactionDetail = $this->objTransactions->getTransactionsDetail($teenId, 1);
        $deductedCoinsDetail = $this->objDeductedCoins->getDeductedCoinsDetailForPS($teenId, 1);
        $deductedCoinsDetailLS = $this->objDeductedCoins->getDeductedCoinsDetailForLS($teenId, 1);
        $deductedTemplateCoinsDetail = $this->objTemplateDeductedCoins->getDeductedCoinsDetail($teenId, 1);
        return view('teenager.proCoinsHistory', compact('transactionDetail', 'deductedCoinsDetail', 'deductedTemplateCoinsDetail', 'deductedCoinsDetailLS', 'tab'));
    }

    /**
     * Buy ProCoins
     *
     * @return void
     */
    public function displayProCoins()
    {
        $coinsDetail = $this->coinRepository->getAllCoinsDetail(1);
        $teenId = Auth::guard('teenager')->user()->id;
        $teenData = $this->teenagersRepository->getTeenagerByTeenagerId($teenId);
        $transactionsDetail = $this->objTransactions->getTransactionsDetail($teenId, 1);
        $day = '';
        if (isset($transactionsDetail) && !empty($transactionsDetail) && count($transactionsDetail) > 0) {
            $currentTime = strtotime(date('Y-m-d'));
            $coinsData = $this->coinRepository->getAllCoinsDetailByid($transactionsDetail[0]->tn_package_id);
            $endTime = strtotime($transactionsDetail[0]->tn_trans_date . "+".$coinsData[0]->c_valid_for." days");

            $finalDate = round(abs($endTime - $currentTime) / 86400, 2);
            if ($endTime > $currentTime) {
                $day = round($finalDate);
            }
        }
        return view('teenager.proCoinsBuy', compact('coinsDetail', 'teenData', 'day'));
    }

    //Process purchased coins to Indipay
    public function saveCoinPurchasedData($id) {
        $coinsDetail = $this->coinRepository->getAllCoinsDetailByid($id);
        if (!empty($coinsDetail)) {
            $teenId = Auth::guard('teenager')->user()->id;
            $amount = $coinsDetail[0]->c_price;
            $parameters = [
                  'tid' => $teenId.time(),
                  'order_id' => time(),
                  'amount' => $amount,
                  'merchant_param1' => '1',
                  'merchant_param2' => $id,
            ];

            $order = Indipay::prepare($parameters);
            
            return Indipay::process($order);
        }
    }
    
    public function orderResponse(Request $request)
    {
        // For default Gateway
        $response = Indipay::response($request);
        dd($response);
    }


    //Mail to Parent for purchase coins
    public function requestParentForPurchasedCoins() {
        $email = Input::get('email');
        $teenId = Auth::guard('teenager')->user()->id;
        $parent = $this->parentsRepository->getParentDetailByEmailId($email);
        if (!empty($parent)) {
            $checkPairAvailability = $this->parentsRepository->checkPairAvailability($teenId, $parent['id']);
            if (!empty($checkPairAvailability)) {
                $saveData = [];
                $saveData['tpr_teen_id'] = $teenId;
                $saveData['tpr_parent_id'] = $parent['id'];
                $saveData['tpr_status'] = 1;
                $result = $this->objTeenParentRequest->saveTeenParentRequestDetail($saveData);

                $userDetail = $this->teenagersRepository->getTeenagerByTeenagerId($teenId);
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
                    $m->from(Config::get('constant.FROM_MAIL_ID'), 'ProCoins Request By Teenager');
                    $m->subject($data['subject']);
                    $m->to($data['toEmail'], $data['toName']);
                });

              return Redirect::to('/teenager/buy-procoins/')->with('success', trans('appmessages.parentrequestsuccess'));
              exit;
            } else {
                return Redirect::to('/teenager/buy-procoins/')->with('error', trans('appmessages.parentteenvarify'));
                exit;
            }
        } else {
            return Redirect::to('/teenager/buy-procoins/')->with('error', trans('appmessages.parent_email_invalid'));
            exit;
        }
    }
            
    public function saveGiftedCoinsData()
    {
        $teenId = Auth::guard('teenager')->user()->id;
        $giftTo = Input::get('teenId');
        $giftcoins = Input::get('gift_coins');

        $saveData = [];
        $saveData['tcg_sender_id'] = $teenId;
        $saveData['tcg_reciver_id'] = $giftTo;
        $saveData['tcg_total_coins'] = $giftcoins;
        $saveData['tcg_gift_date'] = date('Y-m-d');
        $saveData['tcg_user_type'] = 1;

        $return = $this->objTeenagerCoinsGift->saveTeenagetGiftCoinsDetail($saveData);
        $deductCoins = 0;
        //deduct coin from user
        $userDetail = $this->teenagersRepository->getUserDataForCoinsDetail($teenId);
        if (!empty($userDetail)) {
            $deductCoins = $userDetail['t_coins'] - $giftcoins;
        }
        $response = $this->teenagersRepository->updateTeenagerCoinsDetail($teenId, $deductCoins);

        //Add icons to other user
        $coins = $giftcoins;
        $userData = $this->teenagersRepository->getUserDataForCoinsDetail($giftTo);
        if (!empty($userData)) {
            $coins += $userData['t_coins'];
        }
        $result = $this->teenagersRepository->updateTeenagerCoinsDetail($giftTo, $coins);

        $userData = Auth::guard('teenager')->user();
        $notificationData['n_sender_id'] = $userData->id;
        $notificationData['n_sender_type'] = Config::get('constant.NOTIFICATION_TEENAGER');
        $notificationData['n_receiver_id'] = $giftTo;
        $notificationData['n_receiver_type'] = Config::get('constant.NOTIFICATION_TEENAGER');
        $notificationData['n_notification_type'] = Config::get('constant.NOTIFICATION_TYPE_CONNECTION_REQUEST');
        $notificationData['n_notification_text'] = '<strong>'.ucfirst($userData->t_name).' '.ucfirst($userData->t_lastname).'</strong> gited you '.$giftcoins.' coins';
        $this->objNotifications->insertUpdate($notificationData);

        //Mail to both users
        //Login user mail
        $userArray = $this->teenagersRepository->getTeenagerByTeenagerId($teenId);
        $otherUserArray = $this->teenagersRepository->getTeenagerByTeenagerId($giftTo);
        $replaceArray = array();
        $replaceArray['TEEN_NAME'] = $userArray['t_name'];
        $replaceArray['COINS'] = $giftcoins;
        $replaceArray['TO_USER'] = $otherUserArray['t_name'];
        $emailTemplateContent = $this->templateRepository->getEmailTemplateDataByName(Config::get('constant.GIFTED_COINS_TEMPLATE'));
        $content = $this->templateRepository->getEmailContent($emailTemplateContent->et_body, $replaceArray);
        $data = array();
        $data['subject'] = $emailTemplateContent->et_subject;
        $data['toEmail'] = $userArray['t_email'];
        $data['toName'] = $userArray['t_name'];
        $data['content'] = $content;

        Mail::send(['html' => 'emails.Template'], $data , function ($m) use ($data) {
            $m->from(Config::get('constant.FROM_MAIL_ID'), 'Gift ProCoins');
            $m->subject($data['subject']);
            $m->to($data['toEmail'], $data['toName']);
        });

        //Other user mail
        $replaceArray = array();
        $replaceArray['TEEN_NAME'] = $otherUserArray['t_name'];
        $replaceArray['COINS'] = $giftcoins;
        $replaceArray['FROM_USER'] = $userArray['t_name'];
        $emailTemplateContent = $this->templateRepository->getEmailTemplateDataByName(Config::get('constant.COINS_RECEIBED_TEMPLATE'));
        $content = $this->templateRepository->getEmailContent($emailTemplateContent->et_body, $replaceArray);
        $data = array();
        $data['subject'] = $emailTemplateContent->et_subject;
        $data['toEmail'] = $otherUserArray['t_email'];
        $data['toName'] = $otherUserArray['t_name'];
        $data['content'] = $content;

        Mail::send(['html' => 'emails.Template'], $data , function ($m) use ($data) {
            $m->from(Config::get('constant.FROM_MAIL_ID'), 'Gift ProCoins');
            $m->subject($data['subject']);
            $m->to($data['toEmail'], $data['toName']);
        });
        $response = 0;
        $userDetail = $this->teenagersRepository->getUserDataForCoinsDetail($teenId);
        if (!empty($userDetail)) {
            $response = $userDetail['t_coins'];
        }

        return number_format($response);
    }

    public function getAvailableCoins() 
    {
        $userId = Input::get('teenId');
        $userDetail = $this->teenagersRepository->getUserDataForCoinsDetail($userId);
        if (!empty($userDetail)) {
            return $userDetail['t_coins'];
            exit;
        } else {
            return false;
        }
    }

    public function getConsumptionHistoryMoreData()
    {
        $teenId = Auth::guard('teenager')->user()->id;
        $searchText = Input::get('searchText');
        $page = Input::get('page');
        $tab = Input::get('tab');
        if ($tab == 'promise_plus') {
            $deductedCoinsDetail = $this->objDeductedCoins->getDeductedCoinsDetailForPS($teenId, 1, $searchText);
            return view('teenager.searchedPromisePlus', compact('deductedCoinsDetail'));
        } else if ($tab == 'l4_concept_template') {
            $deductedTemplateCoinsDetail = $this->objTemplateDeductedCoins->getDeductedCoinsDetail($teenId, 1, $searchText);
            return view('teenager.searchedL4ConceptTemplate', compact('deductedTemplateCoinsDetail'));
        } else {
            $deductedCoinsDetailLS = $this->objDeductedCoins->getDeductedCoinsDetailForLS($teenId, 1);
            return view('teenager.learningGuidanceData', compact('deductedCoinsDetailLS'));
        }
    }
}
