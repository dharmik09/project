<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Config;
use DB;

class ProfessionMatchScale extends Model {

    protected $table = 'pro_upms_user_profession_match_scale';
    protected $guarded = [];

    public function saveTeenagerProfessionScale($array) {
        $data['teenager_id'] = isset($array['teenager_id']) ? $array['teenager_id'] : 0;
        $data['match_scale'] = isset($array['match_scale']) ? $array['match_scale'] : null;
        $findData = [];
        if($data['teenager_id'] > 0) {
        	$findData = ProfessionMatchScale::where('teenager_id', $data['teenager_id'])->first();
	        if($findData) {
	        	$findData->match_scale = $data['match_scale'];
	        	$findData->save();
	        } else {
	        	$findData = ProfessionMatchScale::insert($data);
	        }
        }
        return $findData;
    }

}