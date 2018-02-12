<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Config;
use DB;

class TeenagerScholarshipProgram extends Model 
{

    protected $table = 'pro_tsp_teenager_scholarship_program';

    protected $guarded = [];

    //Store details for scholarship programs
    public function StoreDetailsForScholarshipProgram($activityDetails) 
    {
	    $scholarship = $this->create($activityDetails);
	    return $scholarship;  
    }

    //Get scholarship program details by activity id
    public function getScholarshipProgramDetailsByActivity($activityDetails) 
    {
    	$scholarshipDetails = $this->where('tsp_activity_id', $activityDetails['tsp_activity_id'])->where('tsp_teenager_id', $activityDetails['tsp_teenager_id'])->where('deleted', Config::get('constant.ACTIVE_FLAG'))->first();
    	return $scholarshipDetails;
    }

    //Get all scholarship programs by teenager id
    public function getAllScholarshipProgramsByTeenId($teenId)
    {
    	$scholarshipDetails = $this->where('tsp_teenager_id', $teenId)->where('deleted', Config::get('constant.ACTIVE_FLAG'))->get();
    	return $scholarshipDetails;
    }

    //Get all teenagers whose applied for scholarship programs
    public function getAllTeensByScholarshipId($activityId)
    {
        $teenDetails = $this->join('pro_t_teenagers AS teenager', 'pro_tsp_teenager_scholarship_program.tsp_teenager_id', '=', 'teenager.id')
                                ->selectRaw('teenager.*, pro_tsp_teenager_scholarship_program.tsp_activity_id, pro_tsp_teenager_scholarship_program.tsp_teenager_id')
                                ->where('pro_tsp_teenager_scholarship_program.tsp_activity_id', $activityId)
                                ->where('pro_tsp_teenager_scholarship_program.deleted', Config::get('constant.ACTIVE_FLAG'))
                                ->where('teenager.deleted', Config::get('constant.ACTIVE_FLAG'))
                                ->get();
        return $teenDetails;
    }
}


