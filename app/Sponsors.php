<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use DB;

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

    public function teenagerSponsorCollection() {
        return $this->hasMany(TeenagerSponsor::class, 'ts_sponsor');
    }
}