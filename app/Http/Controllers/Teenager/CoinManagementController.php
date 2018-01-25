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
    }

    /**
     * Gift Coin Data
     *
     * @return void
     */
    public function getGiftCoins() 
    {
        $teenId = Auth::guard('teenager')->user()->id;
        $objTeenagerCoinsGift = new TeenagerCoinsGift;
        $teenCoinsDetail = $objTeenagerCoinsGift->getTeenagerCoinsGiftDetail($teenId, 1);
        $coinDetail = $this->teenagersRepository->getUserDataForCoinsDetail($teenId);
        return view('teenager.proCoinsGift', compact('teenCoinsDetail', 'coinDetail'));
    }

    public function userSearchToGiftCoins() 
    {
        $searchKeyword = Input::get('search_keyword');
        $teenId = Input::get('teenagerId');
        $searchArray = explode(",", $searchKeyword);
        if ($searchKeyword != "") {
            $activeTeenagers = $this->objTeenager->getMultipleActiveTeenagersForGiftCoins($teenId, $searchArray);
        } else {
            $activeTeenagers = Helpers::getActiveTeenagersForDashboard($teenId);
        }
        return view('teenager.searchGiftedCoins', compact('activeTeenagers'));
    }

    /**
     * ProCoin History Data
     *
     * @return void
     */
    public function getProCoinsHistory() 
    {
        $teenId = Auth::guard('teenager')->user()->id;
        $transactionDetail = $this->objTransactions->getTransactionsDetail($teenId, 1);
        $deductedCoinsDetail = $this->objDeductedCoins->getDeductedCoinsDetailForPS($teenId, 1);
        $deductedCoinsDetailLS = $this->objDeductedCoins->getDeductedCoinsDetailForLS($teenId, 1);
        $deductedTemplateCoinsDetail = $this->objTemplateDeductedCoins->getDeductedCoinsDetail($teenId, 1);
        return view('teenager.proCoinsHistory', compact('transactionDetail', 'deductedCoinsDetail', 'deductedTemplateCoinsDetail', 'deductedCoinsDetailLS'));
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
            echo "<pre>";
            print_r($order);
            exit;
            return Indipay::process($order);
        }
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
    
    public function orderResponse(Request $request)
    {
        // For default Gateway
        $response = Indipay::response($request);
       // dd($response);
    }    
}
