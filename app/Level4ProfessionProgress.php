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
    
    //Returns completed profession count by Teenager id
    public function getTeenAttemptProfessionWithTotal($teenId) 
    {
       
        $careerData = $this->select('profession_id as id')->where('teenager_id', $teenId)->where('level4_total','>', 0)->get();
       
        return $careerData;
    }

    //Returns attempted profession list by Teenager id
    public function getTeenAttemptProfessions($teenId) 
    {
       
        $careerData = $this->join(Config::get('databaseconstants.TBL_PROFESSIONS') . " AS profession", 'pro_l4aapa_level4_profession_progress.profession_id', '=', 'profession.id')->selectRaw('pro_l4aapa_level4_profession_progress.*, profession.pf_name')->where('teenager_id', $teenId)->where('level4_total','>', 0)->get();
       return $careerData;
    }

    //Returns teenager list for attempted profession by profession id
    public function getTotalCompetingOfProfession($professionId) {
        $getTotalCompetingFromLevel3 = $this->join("pro_t_teenagers AS teenager", 'teenager.id', '=', 'pro_l4aapa_level4_profession_progress.teenager_id')
                ->select(DB::raw('DISTINCT(pro_l4aapa_level4_profession_progress.teenager_id) as teenager_id, teenager.t_photo, teenager.t_name, teenager.t_uniqueid,teenager.is_search_on, teenager.t_phone, teenager.t_email', 'pro_l4aapa_level4_profession_progress.*'))
                ->where('pro_l4aapa_level4_profession_progress.profession_id', $professionId)
                ->where('teenager.deleted', 1)
                ->get();
        return $getTotalCompetingFromLevel3;
    }
}
