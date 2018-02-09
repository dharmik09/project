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
}


