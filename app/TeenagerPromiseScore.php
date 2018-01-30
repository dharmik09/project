<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Config;
use DB;

class TeenagerPromiseScore extends Model {

    protected $table = 'pro_teenager_promise_score';
    protected $guarded = [];
    
    public function saveTeenagerPromiseScore($array, $teenagerId) {
    	$findData = [];
        if($teenagerId > 0 && count($array) > 0) {
        	$findData = TeenagerPromiseScore::where('teenager_id', $teenagerId)->first();
	        if($findData) {
	        	$findData->update($array);
	        } else {
	        	$array['teenager_id'] = $teenagerId;
	        	$findData = TeenagerPromiseScore::insert($array);
	        }
        }
        return $findData;
    }
}