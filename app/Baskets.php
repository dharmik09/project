<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Professions;

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
                    ->where('id',$this->professionId);
                }])
                ->whereHas('profession', function ($query) {
                    $query->where('id',$this->professionId);
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
                    }]);
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
                    }]);
                }])
                ->where('deleted' ,'1')
                ->get();
        return $return;
    }
}
