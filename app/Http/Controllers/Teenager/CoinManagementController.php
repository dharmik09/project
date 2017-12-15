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

class CoinManagementController extends Controller
{
    
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(TeenagersRepository $teenagersRepository, CoinRepository $coinRepository)
    {
        $this->teenagersRepository = $teenagersRepository;
        $this->objTransactions = new Transactions;
        $this->objDeductedCoins = new DeductedCoins;
        $this->objTemplateDeductedCoins = new TemplateDeductedCoins;
        $this->objPurchasedCoins = new PurchasedCoins;
        $this->coinRepository = $coinRepository;
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
        return view('teenager.proCoinsGift', compact('teenCoinsDetail'));
    }

    public function userSearchForGiftCoins() 
    {
        $searchKeyword = Input::get('search_keyword');
        $teenagerId = Input::get('teenagerId');
        $searchArray = explode(",", $searchKeyword);
        $objTeenagerCoinsGift = new TeenagerCoinsGift;
        if ($searchKeyword != '') {
            $teenCoinsDetail = $objTeenagerCoinsGift->getTeenagerCoinsGiftDetailName($teenagerId, 1, $searchArray);

            return view('teenager.searchGiftedCoins', compact('teenCoinsDetail'));
            exit;
        } else {
            $teenCoinsDetail = $objTeenagerCoinsGift->getTeenagerCoinsGiftDetail($teenagerId, 1);

            return view('teenager.searchGiftedCoins', compact('teenCoinsDetail'));
            exit;
        }
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
}
