<?php

namespace App\Services\Coin\Repositories;

use DB;
use Config;
use App\Services\Coin\Contracts\CoinRepository;
use App\Services\Repositories\Eloquent\EloquentBaseRepository;

class EloquentCoinRepository extends EloquentBaseRepository
implements CoinRepository
{
    /**
    * @return array of all the active Coins
    Parameters
    @$searchParamArray : Array of Searching and Sorting parameters
    */

    public function getAllCoins() 
    {
        $coins = $this->model->where('deleted', '<>', Config::get('constant.DELETED_FLAG'))->get();
        return $coins;
    }

    /**
    * @return Coins details object
    Parameters
    @$cmsDetail : Array of Coins detail from front
    */
    public function saveCoinDetail($coinDetail) 
    {
        if ($coinDetail['id'] != '' && $coinDetail['id'] > 0) {
            $return = $this->model->where('id', $coinDetail['id'])->update($coinDetail);
        } else {
            $return = $this->model->create($coinDetail);
        }
        return $return;
    }

    /**
    * @return Boolean True/False
    Parameters
    @$id : Coins ID
    */
    public function deleteCoins($id) 
    {
        $flag = true;
        $coins = $this->model->find($id);
        $coins->deleted = config::get('constant.DELETED_FLAG');
        $response = $coins->save();
        if ($response) {
            return true;
        } else {
            return false;
        }
    }

    public function getAllCoinsDetail($type) 
    {
        $coins = DB::table(config::get('databaseconstants.TBL_COINS_PACKAGE'))
                    ->selectRaw('*')
                    ->whereRaw('deleted IN (1)')
                    ->where('c_user_type', $type)
                    ->get();
        return $coins;
    }

    public function getAllCoinsDetailByid($id) 
    {
        $coins = DB::table(config::get('databaseconstants.TBL_COINS_PACKAGE'))
                    ->selectRaw('*')
                    ->whereRaw('deleted IN (1)')
                    ->where('id',$id)
                    ->get();
        return $coins;
    }

    public function getAllCoinsPackageDetail($type) 
    {
        $coins = DB::table(config::get('databaseconstants.TBL_COINS_PACKAGE'))
        ->selectRaw('id, c_coins AS coins, c_price AS price ,c_currency AS currency, c_package_name AS packageName, c_valid_for AS validFor, c_description AS Description, c_user_type AS userType, c_image')
        ->whereRaw('deleted IN (1)')
        ->where('c_user_type',$type)
        ->get();
        return $coins;
    }
}
