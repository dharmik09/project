<?php

namespace App\Services\Baskets\Repositories;

use DB;
use Config;
use App\Level1Options;
use App\Services\Baskets\Contracts\BasketsRepository;
use App\Services\Repositories\Eloquent\EloquentBaseRepository;

class EloquentBasketsRepository extends EloquentBaseRepository implements BasketsRepository {

    /**
    * @return array of all the active baskets
    Parameters
    @$searchParamArray : Array of Searching and Sorting parameters
    */
    public function getAllBaskets() {
        $baskets = DB::table(config::get('databaseconstants.TBL_BASKETS'))
                    ->selectRaw('*')
                    ->where('deleted', '<>', Config::get('constant.DELETED_FLAG'))
                    ->get();
        return $baskets;
    }

    /**
    * @return Basket List with profession detail
    */
    public function getBasketsList()
    {
        $basketData = $this->model->where('deleted',1)->get();
        return $basketData;
    }

    /**
    * @return Basket details object
    Parameters
    @$basketDetail : Array of basket detail from front
    */
    public function saveBasketDetail($basketDetail) {
        if ($basketDetail['id'] != '' && $basketDetail['id'] > 0) {
            $return = $this->model->where('id', $basketDetail['id'])->update($basketDetail);
        } else {
            $return = $this->model->create($basketDetail);
        }
        return $return;
    }

    /**
    * @return Boolean True/False
    Parameters
    @$id : Basket ID
    */
    public function deleteBasket($id) {
        $basket = $this->model->find($id);
        $basket->deleted = config::get('constant.DELETED_FLAG');
        $response = $basket->save();
        if ($response) {
            return true;
        } else {
            return false;
        }
    }

    /*
    * Get Basket Data From Basket Name
    */

    public function getBasketData($basketName) {
        $basketData = DB::select(DB::raw("SELECT * FROM " . config::get('databaseconstants.TBL_BASKETS') . " WHERE b_name='" . $basketName . "'"));
        return $basketData;
    }

    /**
    * Parameter : $teenagerId and $basketid
    * return : no return but add record
    */
    public function addTeenagerBasketAttempted($userid, $basketid)
    {
        $data = DB::table(config::get('databaseconstants.TBL_TEENAGER_BASKET_ATTEMPTED'))->insert(['tba_teenager' => $userid, 'tba_basket_id' => $basketid]);
        $dataAttempted = DB::table(config::get('databaseconstants.TBL_TEENAGER_BASKET_ATTEMPTED'))->where(['tba_teenager' => $userid, 'tba_basket_id' => $basketid])->first();
        return $dataAttempted;
    }

    /**
    * Parameter : $userid and $basketid
    * return : array od basket attempt of teenager
    */
    public function getTeenagerBasketAttempted($userid,$basketid)
    {
        $basketattempt = DB::table(config::get('databaseconstants.TBL_TEENAGER_BASKET_ATTEMPTED'))->where('tba_teenager',$userid)->where('tba_basket_id',$basketid)->first();
        return $basketattempt;
    }

    /*
    * Get Basket Detail by Id
    */
    public function getBasketDetailById($basketId) {
        $basketDetail = DB::select(DB::raw("SELECT * FROM " . config::get('databaseconstants.TBL_BASKETS') . " WHERE id='" . $basketId . "'"));
        return $basketDetail;
    }
}
