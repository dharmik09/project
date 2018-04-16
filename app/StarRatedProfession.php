<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use Config;

class StarRatedProfession extends Model
{
    protected $table = 'pro_srp_star_rated_professions';
    protected $guarded = [];
    
    /**
     * Insert star rated profession
     */
    public function addStarToCareer($careerDetails)
    {
        $return = [];
        $careerExist = $this->checkStarGivenToCareer($careerDetails);
        if (isset($careerExist) && !empty($careerExist) && count($careerExist) > 0) {
            return $return;
        } else {
            $return = $this->create($careerDetails);
            return $return;
        }
    }

    public function checkStarGivenToCareer($careerDetails)
    {
        $return = $this->where('srp_teenager_id', $careerDetails['srp_teenager_id'])->where('srp_profession_id', $careerDetails['srp_profession_id'])->first();
        return $return;
    }

    //Delete record from table
    public function deleteRecord($careerDetails)
    {
        $return = $this->where('srp_teenager_id', $careerDetails['srp_teenager_id'])->where('srp_profession_id', $careerDetails['srp_profession_id'])->delete();
        return $return;
    }
    
}
