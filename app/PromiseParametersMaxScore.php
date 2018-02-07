<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Config;
use DB;

class PromiseParametersMaxScore extends Model {

    protected $table = 'pro_promise_parameters_max_score';
    protected $guarded = [];
    
    public function getPromiseParametersMaxScore() 
    {
        $allData = PromiseParametersMaxScore::all();       
        return $allData;
    }
}