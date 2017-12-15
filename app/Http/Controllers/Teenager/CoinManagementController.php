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

class CoinManagementController extends Controller
{
    
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(TeenagersRepository $teenagersRepository)
    {
        $this->teenagersRepository = $teenagersRepository;
        $this->objTransactions = new Transactions;
        $this->objDeductedCoins = new DeductedCoins;
        $this->objTemplateDeductedCoins = new TemplateDeductedCoins;
    }

    /**
     * Gift Coin Data
     *
     * @return void
     */
    public function getGiftCoins() {
        $teenId = Auth::guard('teenager')->user()->id;
        $objTeenagerCoinsGift = new TeenagerCoinsGift;
        $teenCoinsDetail = $objTeenagerCoinsGift->getTeenagerCoinsGiftDetail($teenId, 1);
        return view('teenager.proCoinsGift', compact('teenCoinsDetail'));
    }

    public function userSearchForGiftCoins() {
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
    public function getProCoinsHistory() {
        $teenId = Auth::guard('teenager')->user()->id;
        $transactionDetail = $this->objTransactions->getTransactionsDetail($teenId, 1);
        $deductedCoinsDetail = $this->objDeductedCoins->getDeductedCoinsDetailForPS($teenId, 1);
        $deductedCoinsDetailLS = $this->objDeductedCoins->getDeductedCoinsDetailForLS($teenId, 1);
        $deductedTemplateCoinsDetail = $this->objTemplateDeductedCoins->getDeductedCoinsDetail($teenId, 1);
        return view('teenager.proCoinsHistory', compact('transactionDetail', 'deductedCoinsDetail', 'deductedTemplateCoinsDetail', 'deductedCoinsDetailLS'));
    }
}
