<?php

namespace App\Services\Schools\Repositories;

use DB;
use Config;
use App\Services\Schools\Contracts\SchoolsRepository;
use App\Services\Repositories\Eloquent\EloquentBaseRepository;

class EloquentSchoolsRepository extends EloquentBaseRepository
    implements SchoolsRepository
{
    /**
     * @return array of all the active schools
       Parameters
       @$searchParamArray : Array of Searching and Sorting parameters
     */

      public function getAllSchools($searchParamArray = array(), $isExport=false)
      {
            $whereStr = '';
            $orderStr = '';

            $whereArray = [];
            $whereArray[] = 'school.deleted IN (1,2)';
            if (isset($searchParamArray) && !empty($searchParamArray))
            {
                if (isset($searchParamArray['searchBy']) && isset($searchParamArray['searchText']) && $searchParamArray['searchBy'] != '' && $searchParamArray['searchText'] != '') {
                    $whereArray[] = $searchParamArray['searchBy'] . " LIKE '%" . $searchParamArray['searchText'] . "%'";
                }
                if (isset($searchParamArray['searchBy']) && isset($searchParamArray['fromText']) && $searchParamArray['searchBy'] != '' && $searchParamArray['fromText'] != '' && $searchParamArray['toText'] != '') {
                    $whereArray[] = $searchParamArray['searchBy'] . " BETWEEN  '" . $searchParamArray['fromText'] . "'" . " AND  '" . $searchParamArray['toText'] ."'"  ;
                }
                if (isset($searchParamArray['orderBy']) && isset($searchParamArray['sortOrder']) && $searchParamArray['orderBy'] != '' && $searchParamArray['sortOrder'] != '') {
                    $orderStr = " ORDER BY " . $searchParamArray['orderBy'] . " " . $searchParamArray['sortOrder'];
                }
            }

            if(!empty($whereArray))
            {
                $whereStr = implode(" AND ", $whereArray);
            }
            if($isExport){
                $schools = DB::table(config::get('databaseconstants.TBL_SCHOOLS') . " AS school")
                              ->leftjoin(config::get('databaseconstants.TBL_TEENAGERS') . " AS teenager", 'teenager.t_school', '=', 'school.id')   
                              ->selectRaw('school.*,count(teenager.id) as studentcount')
                              ->groupBy('school.id')
                              ->whereRaw($whereStr . $orderStr)
                              ->get();
            }else{
                $schools = DB::table(config::get('databaseconstants.TBL_SCHOOLS') . " AS school")
                              ->leftjoin(config::get('databaseconstants.TBL_TEENAGERS') . " AS teenager", 'teenager.t_school', '=', 'school.id')   
                              ->selectRaw('school.*,count(teenager.id) as studentcount')
                              ->groupBy('school.id')
                              ->whereRaw($whereStr . $orderStr)
                              ->paginate(Config::get('constant.ADMIN_RECORD_PER_PAGE'));
            }
            return $schools;
      }

      public function getAllSchoolsDataObj()
      {
            $schools = DB::table(config::get('databaseconstants.TBL_SCHOOLS') . " AS school")
                  ->leftjoin(config::get('databaseconstants.TBL_TEENAGERS') . " AS teenager", 'teenager.t_school', '=', 'school.id')   
                  ->selectRaw('school.*,count(teenager.id) as studentcount')
                  ->groupBy('school.id')
                  ->whereIn('school.deleted', ['1','2'])
                  ->where('teenager.deleted','1');
            return $schools;
      }

      public function getAllSchoolsData()
      {
            $schools = DB::table(config::get('databaseconstants.TBL_SCHOOLS') . " AS school")
                          ->leftjoin(config::get('databaseconstants.TBL_TEENAGERS') . " AS teenager", 'teenager.t_school', '=', 'school.id')   
                          ->selectRaw('school.*,count(teenager.id) as studentcount')
                          ->groupBy('school.id')
                          ->whereIn('school.deleted', ['1','2'])
                          ->where('school.sc_isapproved', '1')
                          ->where('teenager.deleted', '1')
                          ->get();
            return $schools;
      }
     /**
     * @return School details object
       Parameters
       @$schoolDetail : Array of schools detail from front
     */

      /*
     return : array of School detail by email id
     */
    public  function getSchoolDetailByEmailId($email)
    {
        $schoolDetail = $this->model->where('deleted','1')->where('sc_email',$email)->first();
        return $schoolDetail;
    }

     /**
     * get entire detail related user
     */
    public function getSchoolById($id)
    {
        $SchoolDetails = DB::select( DB::raw("SELECT school.*,country.c_name,country.id as country_id,country.c_code,s_name,city.c_name as city
                                          FROM " . config::get('databaseconstants.TBL_SCHOOLS') . " AS school
                                            left join " . config::get('databaseconstants.TBL_COUNTRIES') ." AS country on country.id = school.sc_country
                                            left join " . config::get('databaseconstants.TBL_STATES') . " AS state on state.id = school.sc_state
                                            left join " . config::get('databaseconstants.TBL_CITIES') . " AS city on city.id = school.sc_city                                           
                                           where school.id = ".$id));
        return $SchoolDetails[0];
    }



    public function saveSchoolDetail($schoolDetail)
    {
        /*
        if($schoolDetail['id'] != '' && $schoolDetail['id'] > 0)
        {
            $return = $this->model->where('id', $schoolDetail['id'])->update($schoolDetail);
        }
        else
        {
            $return = $this->model->create($schoolDetail);
        }*/

         if (isset($schoolDetail['id']) && $schoolDetail['id'] != '' && $schoolDetail['id'] > 0) {
            $return = $this->model->where('id', $schoolDetail['id'])->update($schoolDetail);
        } else {
            $return = $this->model->create($schoolDetail);
        }

        return $return;
    }

     /*
     * Parameter $resetRequest : array of Details of password reset request
     * return : Boolean TRUE
     */
    public function saveSchoolPasswordResetRequest($resetRequest)
    {
        DB::table(config::get('databaseconstants.TBL_SCHOOL_RESET_PASSWORD'))->insert($resetRequest);

        return true;
    }

     /**
     * @return Boolean True/False
       Parameters
       @$id : School ID
     */
    public function deleteSchool($id)
    {
        $flag              = true;
        $school          = $this->model->find($id);
        $school->deleted = config::get('constant.DELETED_FLAG');
        $response          = $school->save();
        if($response)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Get School
     */
     public function getApprovedSchools()
     {
        $school = $this->model->where('deleted', '1')->where('sc_isapproved','1')->select('id as school_id','sc_email', 'sc_name','sc_address1','sc_address2','sc_pincode','sc_city','sc_state','sc_country','password','sc_logo','sc_photo','sc_first_name','sc_last_name','sc_title','sc_phone')->get();
        return $school;
     }

    /**
     * Edit school to Approve by $id
     */
     public function editToApprovedSchool($id)
     {
        $this->model->where('id', $id)->update(['sc_isapproved' => 1]);
        $return = $this->model->where('id', $id)->first();
        return $return;
     }

      /*
    * get country id by country name....
    */
    public function getCountryIdByName($country)
    {
        $country =  DB::select(DB::raw("select country.id from ". config::get('databaseconstants.TBL_COUNTRIES')  ." AS country
                                                      where country.c_name ='".$country."' or country.c_code='".$country."'"));
         if(!empty($country))
         {
             return $country[0];
         }
         else{
             return 0;
         }

    }

     /*
    * get state id by state name....
    */
    public function getStateIdByName($state)
    {
        $state =  DB::select(DB::raw("select state.id from ". config::get('databaseconstants.TBL_STATES')  ." AS state
                                                      where state.s_name ='".$state."' or state.s_code='".$state."'"));
         if(!empty($state))
         {
             return $state[0];
         }
         else{
             return 0;
         }

    }

      /*
    * get state id by city name....
    */
    public function getCityIdByName($city)
    {
        $city =  DB::select(DB::raw("select city.id from ". config::get('databaseconstants.TBL_CITES')  ." AS country
                                                      where city.c_name ='".$city."' or city.c_code='".$city."'"));
         if(!empty($city))
         {
             return $city[0];
         }
         else{
             return 0;
         }

    }

     /**
     * @return Boolean True/False
       Parameters
       @$email : School's email
     */
    public function checkActiveEmailExist($email,$id='')
    {
        if($id != '')
        {
            $user = $this->model->where('deleted', '1')->where('sc_email', $email)->where('id','!=',$id)->get();
        }
        else
        {
            $user = $this->model->where('deleted', '1')->where('sc_email', $email)->get();
        }
        if($user->count() > 0)
        {
            return true;
        }
        else
        {
            return false;
        }
    }


     /**
     * @return Boolean True/False
       Parameters
       @$phone : Sponsor's phone
     */
    public function checkActivePhoneExist($phone,$id='')
    {
        if($id != '')
        {
           $user = $this->model->where('deleted', '1')->where('sc_phone', $phone)->where('id','!=',$id)->get();
        }
        else
        {
            $user = $this->model->where('deleted', '1')->where('sc_phone', $phone)->get();
        }


        if($user->count() > 0)
        {
            return true;
        }
        else
        {
            return false;
        }
    }


     /**
     * Parameter $schoolId : School ID from provider
     * Parameter $OTP : One Time Password
     * return : Boolean TRUE / FALSE
    */
    public function verifyOTPAgainstSchoolId($schoolId, $OTP)
    {
        $result = DB::table(config::get('databaseconstants.TBL_SCHOOL_RESET_PASSWORD'))->where("trp_school", $schoolId)->where("trp_otp", $OTP)->where("trp_status", 1)->first();

        if(isset($result) && !empty($result))
        {
            $currentDatetime = time(); // or your date as well
            $requestDatetime = strtotime($result->created_at);
            $datediff = $currentDatetime - $requestDatetime;
            $daysDifference =  floor($datediff/(60*60*24));
            if($daysDifference > config::get('constant.PASSWORD_RESET_REQUEST_VALIDITY_DAYS'))
            {
                return false;
            }
            else
            {
                $row = DB::table(config::get('databaseconstants.TBL_SCHOOL_RESET_PASSWORD'))->find($result->id);
                DB::table(config::get('databaseconstants.TBL_SCHOOL_RESET_PASSWORD'))->where('id', $result->id)->update(['trp_status' => 0]);
                return true;
            }
        }
        else
        {
            return false;
        }
    }

    public function checkCurrentPasswordAgainstSchool($schoolId, $currentPassword)
    {
        $result = $this->model->select('sc_email')->where('id', $schoolId)->where('deleted', '1')->first();

        if(isset($result) && !empty($result))
        {
            $result = $result->toArray();
            if ($user = Auth::school()->attempt(['sc_email' => $result['sc_email'], 'password' => $currentPassword, 'deleted' => 1,'sc_isapproved'=>'1']))
            {
                return true;
            }
            else
            {
                return false;
            }
        }
        else
        {
            return false;
        }
    }

    public function saveSchoolBulkDetail($schoolDetail)
    {
        //$result = DB::table(config::get('databaseconstants.TBL_SCHOOLS'))->where('sc_name', $schoolDetail['t_school'])->first();
        //$sc_id = $result->id;
        //$schoolDetail['t_school'] = $sc_id;

        $checkTeenagerData = array();
        if(!empty($schoolDetail) && isset($schoolDetail['t_email'])){
            $checkTeenagerData = DB::table(config::get('databaseconstants.TBL_TEENAGERS'))->where(['t_email' => $schoolDetail['t_email'], 'deleted' => 1])->first();
        }
        if(!empty($checkTeenagerData)){
            $dataId = $checkTeenagerData->id;
            $schoolBulk = DB::table(config::get('databaseconstants.TBL_TEENAGERS'))->where('id',$dataId)->update($schoolDetail);
        }else{
            $schoolDetail['t_uniqueid'] = uniqid("", TRUE);
            $schoolDetail['t_isverified'] = 0;
            //$schoolDetail['t_school_status'] = 1;
            $schoolDetail['t_social_provider'] = 'Normal';
            $schoolDetail['t_sponsor_choice'] = 2;
            $schoolBulk = DB::table(config::get('databaseconstants.TBL_TEENAGERS'))->insert($schoolDetail);
        }
        return $schoolBulk;
    }
    
    public function inactiveRecord($id,$status)
    {
        //$inactiveDetail = DB::table(config::get('databaseconstants.TBL_TEENAGERS'))->where('id', $id)->update(['deleted' => $status]);
        $status = ($status != '' && in_array($status,[0,1]))? $status : 0;
        $inactiveDetail = DB::table(config::get('databaseconstants.TBL_TEENAGERS'))->where('id', $id)->update(['t_school_status' => $status]);
        return $inactiveDetail;
    }
    
    public function getStudentDetailAsPerSchool($id, $searchParamArray)
    {
        $whereStr = '';
        $orderStr = '';

        $whereArray[] = "t_school = ".$id;
        $whereArray[] = "deleted = 1";
        if (isset($searchParamArray) && !empty($searchParamArray)) {
            if (isset($searchParamArray['searchBy']) && isset($searchParamArray['searchText']) && $searchParamArray['searchBy'] != '' && $searchParamArray['searchText'] != '') {
                $whereArray[] = $searchParamArray['searchBy'] . " LIKE '%" . $searchParamArray['searchText'] . "%'";
            }

            if (isset($searchParamArray['searchBy']) && isset($searchParamArray['fromText']) && $searchParamArray['searchBy'] != '' && $searchParamArray['fromText'] != '' && $searchParamArray['toText'] != '') {
                $whereArray[] = $searchParamArray['searchBy'] . " BETWEEN  '" . $searchParamArray['fromText'] . "'" . " AND  '" . $searchParamArray['toText'] ."'"  ;
            }

            if (isset($searchParamArray['orderBy']) && isset($searchParamArray['sortOrder']) && $searchParamArray['orderBy'] != '' && $searchParamArray['sortOrder'] != '') {
                $orderStr = " ORDER BY " . $searchParamArray['orderBy'] . " " . $searchParamArray['sortOrder'];
            }
        }

        if (!empty($whereArray)) {
            $whereStr = implode(" AND ", $whereArray);
        }

        $result = DB::table(config::get('databaseconstants.TBL_TEENAGERS'))
                  ->whereRaw( $whereStr . $orderStr )
                  ->paginate(Config::get('constant.ADMIN_RECORD_PER_PAGE'));
        return $result;
    }
    
    public function getClassDetail($id)
    {
        $return = DB::table(config::get('databaseconstants.TBL_TEENAGERS'))
                  ->where('t_school', $id)
                  ->where('t_class','!=',0)
                  ->where('deleted', 1)
                  ->select('t_class')
                  ->distinct('t_class')
                  ->orderBy('t_class', 'ASC')
                  ->get();
                  
        return $return;
    }

    public function getClassStudentList($school_id, $class_id)
    {
        $return = DB::table(config::get('databaseconstants.TBL_TEENAGERS'));
        $return->where('t_school', $school_id);
        if($class_id != "" && $class_id != "all")
        {
          $return->where('t_class', $class_id);
        }
        $return->where('deleted', 1);
        $return->orderBy('id', 'ASC');
        $return = $return->get();

        return $return;
    }
    
    public function getStudentForLevel1($schoolid, $cid)
    {
        $return = DB::table(config::get('databaseconstants.TBL_TEENAGERS') ." AS teenager")
                  ->join(config::get('databaseconstants.TBL_LEVEL1_ANSWERS') ." AS level1answer", 'teenager.id', '=', 'level1answer.l1ans_teenager')
                  ->selectRaw('teenager.*, level1answer.l1ans_teenager')
                  ->where('teenager.t_school', $schoolid)
                  ->where('teenager.t_class', $cid)
                  ->where('teenager.deleted', 1)
                  ->distinct('level1answer.l1ans_teenager')
                  ->count('level1answer.l1ans_teenager');
        
        return $return;
    }
    
    public function getStudentForLevel2($schoolid, $cid)
    {
        $return = DB::table(config::get('databaseconstants.TBL_TEENAGERS') ." AS teenager")
                  ->join(config::get('databaseconstants.TBL_LEVEL2_ANSWERS') ." AS level2answer", 'teenager.id', '=', 'level2answer.l2ans_teenager')
                  ->selectRaw('teenager.*, level2answer.l2ans_teenager')
                  ->where('teenager.t_school', $schoolid)
                  ->where('teenager.t_class', $cid)
                  ->where('teenager.deleted', 1)
                  ->distinct('level2answer.l2ans_teenager')
                  ->count('level2answer.l2ans_teenager');
        return $return;
    }
    
    public function getStudentForLevel3($schoolid, $cid)
    {
        $return = DB::table(config::get('databaseconstants.TBL_TEENAGERS') ." AS teenager")
                  ->join(config::get('databaseconstants.TBL_TEENAGER_PROFESSION_ATTEMPTED') ." AS attempted", 'teenager.id', '=', 'attempted.tpa_teenager')
                  ->selectRaw('teenager.*, attempted.tpa_teenager')
                  ->where('teenager.t_school', $schoolid)
                  ->where('teenager.t_class', $cid)
                  ->where('teenager.deleted', 1)
                  ->distinct('attempted.tpa_teenager')
                  ->count('attempted.tpa_teenager');
        return $return;
    }
    
    public function getStudentForLevel4($schoolid, $cid)
    {
        $return = DB::table(config::get('databaseconstants.TBL_TEENAGERS') ." AS teenager")
                  ->join(config::get('databaseconstants.TBL_LEVEL4_ANSWERS') ." AS level4answer", 'teenager.id', '=', 'level4answer.teenager_id')
                  ->selectRaw('teenager.*, level4answer.teenager_id')
                  ->where('teenager.t_school', $schoolid)
                  ->where('teenager.t_class', $cid)
                  ->where('teenager.deleted', 1)
                  ->distinct('level4answer.teenager_id')
                  ->count('level4answer.teenager_id');
        return $return;
    }
    
    public function getAttemptedProfession($schoolid, $cid)
    {
        $return['profession'] = DB::table(config::get('databaseconstants.TBL_TEENAGER_PROFESSION_ATTEMPTED') ." AS attempted")
                  ->join(config::get('databaseconstants.TBL_PROFESSIONS') ." AS profession", 'profession.id', '=', 'attempted.tpa_peofession_id')
                  ->join(config::get('databaseconstants.TBL_TEENAGERS') ." AS teenager", 'teenager.id', '=', 'attempted.tpa_teenager')
                  ->selectRaw('profession.*, attempted.tpa_peofession_id, teenager.t_class, teenager.t_school')
                  ->where('teenager.t_school', $schoolid)
                  ->where('teenager.t_class', $cid)
                  ->where('teenager.deleted', 1)
                  ->distinct('profession.id')
                  ->get();
        return $return;
    }
    
    public function getFirstClassDetail($id)
    {
        $return = DB::table(config::get('databaseconstants.TBL_TEENAGERS'))
                  ->where('t_school', $id)
                  ->where('t_class','!=',0)
                  ->where('deleted', 1)
                  ->select('t_class')
                  ->distinct('t_class')
                  ->orderBy('t_class', 'ASC')
                  ->get();
        if(isset($return) && count($return) > 0){   
            return $return[0];
        } else {
            return [];
        }
    }

    public function getSchoolDataForCoinsDetail($id) {
        $schoolData = $this->model->where('id', '=', $id)->where('deleted', 1)->get()->toArray();
        $data = [];
        if (isset($schoolData)) {
            $data['sc_name'] = $schoolData[0]['sc_name'];
            $data['sc_coins'] = $schoolData[0]['sc_coins'];
        }
        return $data;
    }

    public function updateSchoolCoinsDetail($id, $Coins) {
        $schoolDetail = $this->model->where('id', $id)->update(['sc_coins' => $Coins]);
        return $schoolDetail;
    }

    public function getSchoolBySchoolId($id) {
        $schoolData = $this->model->where('id', '=', $id)->where('deleted', 1)->get()->toArray();
        return $schoolData;
    }

    public function getSchoolDataForCoinsDetailByUniqueid($id) {
        $schoolData = $this->model->where('sc_uniqueid', '=', $id)->where('deleted', 1)->get()->toArray();
        $data = [];
        if (isset($schoolData)) {
            $data['sc_coins'] = $schoolData[0]['sc_coins'];
        }
        return $data;
    }

    public function updateSchoolCoinsDetailByUniqueid($id, $Coins) {
        $schoolDetail = $this->model->where('sc_uniqueid', $id)->update(['sc_coins' => $Coins]);

        return $schoolDetail;
    }

    public function checkActiveSchoolExist($id){
        $user = $this->model->where('deleted', '1')->where('sc_uniqueid', $id)->get();

        if ($user->count() > 0) {
            return false;
        } else {
            return true;
        }
    }

    public function getSchoolBySchoolUniqueid($id) {
        $schoolData = $this->model->where('sc_uniqueid', '=', $id)->where('deleted', 1)->first();
        return $schoolData;
    }

    public function getTotalAddedL2QuestionsBySchool($schoolId)
    {
        $l2Activities = DB::table(Config::get('databaseconstants.TBL_LEVEL2_ACTIVITY') . " AS activity")
            ->leftjoin(config::get('databaseconstants.TBL_LEVEL2_OPTIONS') . " AS options", 'activity.id', '=', 'options.l2op_activity')
            ->leftjoin(config::get('databaseconstants.TBL_LEVEL2_APPTITUDE') . " AS apptitude", 'apptitude.id', '=', 'activity.l2ac_apptitude_type')
            ->leftjoin(config::get('databaseconstants.TBL_LEVEL2_MI') . " AS mi", 'mi.id', '=', 'activity.l2ac_mi_type')
            ->leftjoin(config::get('databaseconstants.TBL_LEVEL2_INTEREST') . " AS interest", 'interest.id', '=', 'activity.l2ac_interest')
            ->leftjoin(config::get('databaseconstants.TBL_LEVEL2_PERSONALITY') . " AS personality", 'personality.id', '=', 'activity.l2ac_personality_type')
            ->selectRaw('activity.* , GROUP_CONCAT(options.l2op_option) AS l2op_option, GROUP_CONCAT(options.l2op_fraction) AS l2op_fraction , mi.mit_name , interest.it_name , personality.pt_name, apptitude.apt_name')
            ->groupBy('activity.id')
            ->where('activity.l2ac_school_id', $schoolId)
            ->where('activity.deleted', Config::get('constant.ACTIVE_FLAG'))
            ->get();
        return $l2Activities;
    }
}

