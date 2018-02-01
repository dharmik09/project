<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Professions;
use Config;

class Baskets extends Model
{
    protected $table = 'pro_b_baskets';
    protected $guarded = [];

    public function getActiveBaskets()
    {
        $result = Baskets::select('*')
                        ->where('deleted' ,'1')
                        ->get();
        return $result;
    }

    public function getActiveBasketsOrderByName()
    {
        $result = Baskets::select('*')
                        ->orderBy('b_name')
                        ->where('deleted' ,'1')
                        ->get();
        return $result;
    }


    public function profession(){
        return $this->hasMany(Professions::class, 'pf_basket');
    }

    public function getBasketsAndProfessionWithAttemptedProfessionByProfessionId($professionId, $userId, $countryId){
        $this->professionId = $professionId;
        $this->userId = $userId;
        $this->countryId = $countryId;
        $return = $this->select('*')
                ->with(['profession' => function ($query) {
                    $query->with(['professionAttempted' => function ($query) {
                        $query->where('tpa_teenager', $this->userId);
                    }])
                    ->with(['professionHeaders' => function ($query) {
                        $query->where('country_id',$this->countryId);
                    }])
                    ->where('deleted' ,config::get('constant.ACTIVE_FLAG'))
                    ->where('id',$this->professionId);
                }])
                ->whereHas('profession', function ($query) {
                    $query->where('id',$this->professionId)
                    ->where('deleted' ,config::get('constant.ACTIVE_FLAG'));
                })
                ->where('deleted' ,'1')
                ->get();
        return $return;
    }

    public function getBasketsAndProfessionWithAttemptedProfessionByBasketId($basketId, $userId, $countryId){
        $this->basketId = $basketId;
        $this->userId = $userId;
        $this->countryId = $countryId;
        $return = $this->select('*')
                ->with(['profession' => function ($query) {
                    $query->with(['professionAttempted' => function ($query) {
                        $query->where('tpa_teenager', $this->userId);
                    }])
                    ->with(['professionHeaders' => function ($query) {
                        $query->where('country_id',$this->countryId);
                    }])
                    ->where('deleted' ,config::get('constant.ACTIVE_FLAG'));
                }])
                ->where('id',$this->basketId)
                ->where('deleted' ,'1')
                ->get();
        return $return;
    }

    public function getAllBasketsAndProfessionWithAttemptedProfession($userId, $countryId){
        $this->userId = $userId;
        $this->countryId = $countryId;
        $return = $this->select('*')
                ->with(['profession' => function ($query) {
                    $query->with(['professionAttempted' => function ($query) {
                        $query->where('tpa_teenager', $this->userId);
                    }])
                    ->with(['professionHeaders' => function ($query) {
                        $query->where('country_id',$this->countryId);
                    }])
                    ->where('deleted' ,config::get('constant.ACTIVE_FLAG'));
                }])
                ->where('deleted' ,'1')
                ->get();
        return $return;
    }

    public function getBasketsAndProfessionWithAttemptedProfessionByUserId($userId){
        $this->userId = $userId;
        $return = $this->select('*')
                ->with(['profession' => function ($query) {
                    $query->with(['professionAttempted' => function ($query) {
                        $query->where('tpa_teenager', $this->userId);
                    }])
                    ->with('starRatedProfession')
                    ->where('deleted' ,config::get('constant.ACTIVE_FLAG'));
                }])
                ->whereHas('profession', function ($query) {
                    $query->whereHas('starRatedProfession')
                    ->where('deleted' ,config::get('constant.ACTIVE_FLAG'));
                })
                ->where('deleted' ,'1')
                ->get();
        return $return;
    }

    public function getStarredBasketsAndProfessionByUserId($userId){
        $this->userId = $userId;
        $return = $this->select('*')
                ->with(['profession' => function ($query) {
                    $query
                    ->whereHas('starRatedProfession')
                    ->where('deleted' ,config::get('constant.ACTIVE_FLAG'));
                }])
                ->whereHas('profession', function ($query) {
                    $query->whereHas('starRatedProfession')
                    ->where('deleted' ,config::get('constant.ACTIVE_FLAG'));
                })
                ->where('deleted' ,'1')
                ->get();
        return $return;
    }

    public function getBasketsAndProfessionWithAttemptedProfessionByUserIdAndSearchValue($userId, $searchText){
        $this->userId = $userId;
        $this->searchText = $searchText;
        $return = $this->select('*')
                ->with(['profession' => function ($query) {
                    $query->with(['professionAttempted' => function ($query) {
                        $query->where('tpa_teenager', $this->userId);
                    }])
                    ->where('pf_name', 'like', '%'.$this->searchText.'%')
                    ->with('starRatedProfession')
                    ->where('deleted' ,config::get('constant.ACTIVE_FLAG'));
                }])
                ->whereHas('profession', function ($query) {
                    $query->where('pf_name', 'like', '%'.$this->searchText.'%')
                    ->whereHas('starRatedProfession')
                    ->where('deleted' ,config::get('constant.ACTIVE_FLAG'));
                })
                ->where('deleted' ,'1')
                ->get();
        return $return;
    }

    public function getBasketsAndProfessionBySearchValue($searchText){
        $this->value = $searchText;
        $return = $this->with(['profession' => function ($query) {
                        $query->where('pf_name', 'like', '%'.$this->value.'%')
                        ->where('deleted' ,config::get('constant.ACTIVE_FLAG'));
                    }])
                    ->whereHas('profession', function ($query) {
                        $query->where('pf_name', 'like', '%'.$this->value.'%')
                        ->where('deleted' ,config::get('constant.ACTIVE_FLAG'));
                    })
                    ->where('deleted' ,config::get('constant.ACTIVE_FLAG'))
                    ->get();
        return $return;
    }

    public function getBasketsAndProfessionByBaketIdAndCountryId($basketId,$countryId){
        $this->countryId = $countryId;
        $return = $this->with(['profession' => function ($query) {
                            $query->with(['professionHeaders' => function ($query) {
                                $query->where('country_id',$this->countryId);
                            }])->where('deleted' ,config::get('constant.ACTIVE_FLAG'));
                        }])
                        ->where('deleted' ,config::get('constant.ACTIVE_FLAG'))
                        ->find($basketId);
        return $return;
    }

    public function getBasketsAndProfessionByProfessionId($professionId, $userId, $countryId){
        $this->professionId = $professionId;
        $this->userId = $userId;
        $this->countryId = $countryId;
        $return = $this->select('*')
                ->with(['profession' => function ($query) {
                    $query->where('deleted' ,config::get('constant.ACTIVE_FLAG'))
                    ->where('id',$this->professionId);
                }])
                ->whereHas('profession', function ($query) {
                    $query->where('id',$this->professionId)
                    ->where('deleted' ,config::get('constant.ACTIVE_FLAG'));
                })
                ->where('deleted' ,'1')
                ->get();
        return $return;
    }

    public function getBasketsAndStarRatedProfessionByUserIdAndSearchValue($userId, $searchText){
        $this->userId = $userId;
        $this->searchText = $searchText;
        $return = $this->select('*')
                ->with(['profession' => function ($query) {
                    $query->where('pf_name', 'like', '%'.$this->searchText.'%')
                    ->whereHas('starRatedProfession')
                    ->where('deleted' ,config::get('constant.ACTIVE_FLAG'));
                }])
                ->whereHas('profession', function ($query) {
                    $query->where('pf_name', 'like', '%'.$this->searchText.'%')
                    ->whereHas('starRatedProfession')
                    ->where('deleted' ,config::get('constant.ACTIVE_FLAG'));
                })
                ->where('deleted' ,'1')
                ->get();
        return $return;
    }
}
