<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Config;

class Country extends Model {
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'pro_c_country';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public function getAllCounries()
    {
        $countries = $this->select('*')->orderBy('c_name', 'ASC')->get();
        return $countries;
    }
    
    public function getActiveCounry($id)
    {
        $countries = $this->select('*')->where('id', $id)->get();
        return $countries;
    }
}
