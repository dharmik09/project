<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use DB;
use Config;

class Sponsors extends Authenticatable {


    /**
    * The database table used by the model.
    *
    * @var string
    */
    protected $table = 'pro_sp_sponsor';
    /**
    * The attributes that are mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
    /**
    * The get active sponsors.
    *
    * @var array
    */
    protected $hidden = ['password', 'remember_token'];
    
    public function getActiveSponsors()
    {
        $result = $this->select('*')
        ->where('deleted', '1')
        ->get();
        return $result;
    }

    public function setSponsorCredit($arr)
    {
        $id = $arr['id'];
        $availableCredit = $arr['sp_credit'];
        $credit = DB::table('pro_sp_sponsor')->where('id', $id)->update(['sp_credit' => $availableCredit]);
        return $credit;
    }

    public function getSponsorsAds($sponsorArr)
    {
        $ads = DB::table('pro_sa_sponsor_activity')
              ->join(Config::get('databaseconstants.TBL_SPONSORS') . " AS sponsor", 'pro_sa_sponsor_activity.sa_sponsor_id', '=', 'sponsor.id')
              ->selectRaw('pro_sa_sponsor_activity.*')
              ->where('pro_sa_sponsor_activity.deleted', 1)
              ->where('pro_sa_sponsor_activity.sa_start_date', '<=', date('Y-m-d'))
              ->where('pro_sa_sponsor_activity.sa_end_date', '>=', date('Y-m-d'))
              ->whereIn('pro_sa_sponsor_activity.sa_sponsor_id', $sponsorArr)
              ->get();
        return $ads; 
    }
    
    public function teenagerSponsorCollection() {
        return $this->hasMany(TeenagerSponsor::class, 'ts_sponsor');
    }
}