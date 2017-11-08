<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Config;

class City extends Model {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'pro_city';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['c_name' , 'c_state'];

    public function getAllCities($state_id)
    {
        if($state_id > 0 )
        {
            $cities = City::select('*')->where('c_state',$state_id)->orderBy('c_name', 'ASC')->get();

        }
        else
        {
           // $cities = array();
           $cities = City::select('*')->orderBy('c_name', 'ASC')->get();


        }
        return $cities;

}
    public function getCityByStateId($id)
    {
        $cities = City::select('*')->where('c_state',$id)->orderBy('c_name', 'ASC')->get();
        return $cities;
    }
}
