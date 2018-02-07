<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Config;
use DB;

class TeenagerPromiseScore extends Model {

    protected $table = 'pro_teenager_promise_score';
    protected $guarded = [];
    
    public function saveTeenagerPromiseScore($array, $teenagerId) 
    {
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

    public function getTeenagersWithHighestPromiseScore($slug, $slot = '')
    {
        if ($slot > 0) {
            $slot = $slot * Config::get('constant.RECORD_PER_PAGE');
        }
        if(\Schema::hasColumn('pro_teenager_promise_score', $slug)) {
            $teenDetails = $this->join("pro_t_teenagers AS teenagers", 'pro_teenager_promise_score.teenager_id', '=', 'teenagers.id')
                            ->where('teenagers.deleted', Config::get('constant.ACTIVE_FLAG'))
                            ->where('pro_teenager_promise_score.'.$slug, '!=', "")
                            ->orderBy('pro_teenager_promise_score.'.$slug, 'DESC')
                            ->skip($slot)
                            ->take(Config::get('constant.RECORD_PER_PAGE'))
                            ->get();
        } else {
            $teenDetails = new \stdClass();
        }
        return $teenDetails;
    }
    
    public function getTeenagerPromiseScore($teenagerId) 
    {
        $findData = TeenagerPromiseScore::where('teenager_id', $teenagerId)->first();       
        return $findData;
    }
}