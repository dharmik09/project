<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use Config;

class Level4ProfessionProgress extends Model
{
    protected $table = 'pro_l4aapa_level4_profession_progress';
    protected $guarded = [];
    
    public function saveTeenagerProfessionProgress($array) 
    {
        $findData = [];
        if( isset($array['teenager_id']) && isset($array['profession_id']) ) {
            $findData = Level4ProfessionProgress::where(['teenager_id' => $array['teenager_id'], 'profession_id' => $array['profession_id']])->first();
            if($findData) {
                $findData->update($array);
                $findData = Level4ProfessionProgress::where(['teenager_id' => $array['teenager_id'], 'profession_id' => $array['profession_id']])->first(); 
            } else {
                $findData = Level4ProfessionProgress::insert($array);
            }
        }
        return $findData;
    }

    //Returns completed profession count by Teenager id
    public function getCompletedProfessionCountByTeenId($teenId) 
    {
        $careerCount = $this->where('teenager_id', $teenId)->where('level4_total', 100)->distinct('profession_id')->count('profession_id');
        return $careerCount;
    }
}
