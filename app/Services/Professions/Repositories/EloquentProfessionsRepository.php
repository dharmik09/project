<?php

namespace App\Services\Professions\Repositories;

use DB;
use Config;
use App\Baskets;
use App\ProfessionHeaders;
use App\Services\Professions\Contracts\ProfessionsRepository;
use App\Services\Repositories\Eloquent\EloquentBaseRepository;

class EloquentProfessionsRepository extends EloquentBaseRepository implements ProfessionsRepository {

    /**
     * @return array of all the active Professions
      Parameters
      @$searchParamArray : Array of Searching and Sorting parameters
     */

    public function getAllProfessions() {
        $professions = DB::table(config::get('databaseconstants.TBL_PROFESSIONS') . " AS profession")
                ->join(config::get('databaseconstants.TBL_BASKETS') . " AS basket", 'profession.pf_basket', '=', 'basket.id')
                ->selectRaw('profession.* , basket.b_name')
                ->where('profession.deleted', '<>', Config::get('constant.DELETED_FLAG'))
                ->get();
        return $professions;
    }

    /**
     * @return array of all the active Professions Count
      Parameters
      @$searchParamArray : Array of Searching and Sorting parameters
     */

    public function getAllProfessionsCount() {
        $professions = DB::table(config::get('databaseconstants.TBL_PROFESSIONS') . " AS profession")
                ->where('profession.deleted', Config::get('constant.ACTIVE_FLAG'))
                ->count();
        return $professions;
    }

    /**
     * @return Profession details object
      Parameters
      @$professionDetail : Array of professions detail from front
     */
    public function saveProfessionDetail($professionDetail) {
        if ($professionDetail['id'] != '' && $professionDetail['id'] > 0) {
            $return = $this->model->where('id', $professionDetail['id'])->update($professionDetail);
        } else {
            $return = $this->model->create($professionDetail);
        }
        return $return;
    }

    /**
     * @return Boolean True/False
      Parameters
      @$id : Profession ID
     */
    public function deleteProfession($id) {
        $flag = true;
        $profession = $this->model->find($id);
        $profession->deleted = config::get('constant.DELETED_FLAG');
        $response = $profession->save();
        if ($response) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return array
      Parameters
      @$id : Basket ID
     */
    public function getProfessionsByBasketId($basketid) {
        $professions = DB::table(config::get('databaseconstants.TBL_PROFESSIONS') . " AS profession")
                ->join(config::get('databaseconstants.TBL_BASKETS') . " AS basket", 'profession.pf_basket', '=', 'basket.id')
                ->selectRaw('profession.*, basket.b_name')
                ->whereRaw('profession.deleted = 1')
                ->whereRaw('profession.pf_basket =' . $basketid)
                // ->orWhereRaw('FIND_IN_SET(' . $basketid . ',pf_related_basket)')
                //->orWhere('profession.pf_related_basket', $basketid)                
                ->get();
        return $professions;
    }

    /**
     * @return array
      Parameters
      @$id : Profession ID
     */
    public function getProfessionsByProfessionId($professionid) {
        //DB::statement("SET GLOBAL group_concat_max_len = 10000000");
        $professionDetail = DB::select(DB::raw("select profession.pf_video_type , profession.id as 'professionid' , profession.pf_basket , profession.pf_name , profession.pf_video, profession.pf_logo from " . config::get('databaseconstants.TBL_PROFESSIONS') . " AS profession
                                                      where profession.id=" . $professionid . " LIMIT 0,1"));

        $professionsHeader = DB::select(DB::raw("select header.id , profession.pf_video_type , profession.id as 'professionid' , profession.pf_name , profession.pf_video , header.pfic_title , header.pfic_content from " . config::get('databaseconstants.TBL_PROFESSIONS') . " AS profession
                                                      left join " . config::get('databaseconstants.TBL_PROFESSION_HEADER') . " AS header on profession.id=header.pfic_profession
                                                      where header.pfic_profession=" . $professionid . " LIMIT 0,1"));

        if (isset($professionDetail[0]) && !empty($professionDetail[0])) {
            if (isset($professionsHeader[0]) && !empty($professionsHeader[0])) {
                $professionDetail[0]->pfic_profession = (isset($professionsHeader[0]->pfic_profession) && $professionsHeader[0]->pfic_profession != '') ? $professionsHeader[0]->pfic_profession : '';
                $professionDetail[0]->pfic_title = (isset($professionsHeader[0]->pfic_title) && $professionsHeader[0]->pfic_title != '') ? $professionsHeader[0]->pfic_title : '';
                $professionDetail[0]->pfic_content = (isset($professionsHeader[0]->pfic_content) && $professionsHeader[0]->pfic_content != '') ? $professionsHeader[0]->pfic_content : '';
            } else {
                $professionDetail[0]->pfic_profession = '';
                $professionDetail[0]->pfic_title = '';
                $professionDetail[0]->pfic_content = '';
            }
        }
        return $professionDetail;
    }

    public function getProfessionsHeaderByProfessionId($professionid) {
        //DB::statement("SET GLOBAL group_concat_max_len = 10000000");
        $professions = DB::select(DB::raw("select header.id , profession.pf_video_type , profession.id as 'professionid' , profession.pf_name , profession.pf_video , header.pfic_title , header.pfic_content from " . config::get('databaseconstants.TBL_PROFESSIONS') . " AS profession
                                                      left join " . config::get('databaseconstants.TBL_PROFESSION_HEADER') . " AS header on profession.id=header.pfic_profession
                                                      where header.pfic_profession=" . $professionid . ""));

        return $professions;
    }

    /*
     * Get Basket Data From basket Name
     */

    public function getProfessionsData($professionName) {

        $professionData = DB::select(DB::raw("SELECT
                                            *
                                            FROM " . config::get('databaseconstants.TBL_PROFESSIONS') . " WHERE pf_name='" . $professionName . "' AND deleted = 1"));

        return $professionData;
    }

    public function getProfessionsDataFromId($professionId) {

        $professionData = DB::select(DB::raw("SELECT
                                            *
                                            FROM " . config::get('databaseconstants.TBL_PROFESSIONS') . " WHERE id='" . $professionId . "' AND deleted = 1"));

        return $professionData;
    }

    public function saveProfessionBulkDetail($professionDetail, $basketDetail, $headerTitle, $headerDetail, $countryId) {

        $result = DB::select(DB::raw("SELECT * FROM " . config::get('databaseconstants.TBL_PROFESSIONS')." WHERE deleted = 1"));
        $objBasket = New Baskets();

        $basketData = DB::select(DB::raw("SELECT * FROM " . config::get('databaseconstants.TBL_BASKETS')." WHERE deleted = 1"));

        $objHeader = New ProfessionHeaders();

        $headerlength = 0;
        foreach ($headerDetail as $data) {
            if ($data != '') {
                $headerlength++;
            }
        }
        $basketFlag = true;
        foreach ($basketData as $value) {
            foreach ($basketDetail as $data) {
                if ($data == $value->b_name) {
                    $basketFlag = false;
                    $professionDetail['pf_basket'] = $value->id;
                    $result1 = $objBasket->where('id', $value->id)->update($basketDetail);
                }
            }
        }
        $return = '';
        if ($basketFlag) {
            $return = $objBasket->create($basketDetail);
        }

        if ($return) {
            $professionDetail['pf_basket'] = $return->id;
        }
        $flag = true;
        foreach ($result as $value) {
            foreach ($professionDetail as $data) {
                if ($data == $value->pf_name) {
                    $flag = false;
                    $headerDetail['pfic_profession'] = $value->id;
                    $result1 = $this->model->where('id', $value->id)->update($professionDetail);
                }
            }
        }
        if ($flag) {
            $return = $this->model->create($professionDetail);
            $headerDetail['pfic_profession'] = $return->id;
        }

        $j = 0;
        $headerData = $objHeader->where('pfic_profession', $headerDetail['pfic_profession'])->where('country_id', $countryId)->get();
        for ($i = 0; $i < count($headerTitle); ++$i) {
            $headerDataNew = [];
            $headerDataNew['pfic_profession'] = $headerDetail['pfic_profession'];
            $headerDataNew['pfic_title'] = $headerTitle[$i];
            $headerDataNew['pfic_content'] = $headerDetail[$i];
            $headerDataNew['country_id'] = $countryId;
            if ($j < count($headerData)) {
                $return = $objHeader->where('id', $headerData[$i]['id'])->update($headerDataNew);
            } else {
                $return = $objHeader->create($headerDataNew);
            }
            $j++;
        }
        return;
    }

    /**
     * @return array
      Parameters
      @$id : Searchtext
     */
    public function getsearchByText($serachtext) {
        $professions = DB::select(DB::raw("select  profession.id,profession.pf_name,profession.pf_profession_alias, profession.pf_logo,GROUP_CONCAT('',basket.b_name) as b_name from " . config::get('databaseconstants.TBL_PROFESSIONS') . " AS profession
                                                      join " . config::get('databaseconstants.TBL_BASKETS') . " AS basket on (profession.pf_basket=basket.id OR FIND_IN_SET(basket.id, profession.pf_related_basket))
                                                      where profession.deleted = 1 AND ( profession.pf_name like '%" . $serachtext . "%' OR profession.pf_profession_alias like '%" . $serachtext . "%') group by profession.id"));

        return $professions;
    }

    /**
     * Parameter : $teenagerId and $basketid
     * return : no return but add record
     */
    public function addTeenagerProfessionAttempted($userid, $professionid, $type, $operation) {
        if ($operation == 'add') {
            DB::table(config::get('databaseconstants.TBL_TEENAGER_PROFESSION_ATTEMPTED'))->insert(['tpa_teenager' => $userid, 'tpa_peofession_id' => $professionid, 'tpa_type' => $type]);
        } else {
            DB::table(config::get('databaseconstants.TBL_TEENAGER_PROFESSION_ATTEMPTED'))
                    ->where('tpa_teenager', $userid)->where('tpa_peofession_id', $professionid)
                    ->update(['tpa_type' => $type]);
        }
    }

    /**
     * Parameter : $userid and $basketid
     * return : array of basket attempt of teenager
     */
    public function getTeenagerProfessionAttempted($userid, $professionid, $type) {
        $professionattempt = DB::table(config::get('databaseconstants.TBL_TEENAGER_PROFESSION_ATTEMPTED') . " AS profession")
                ->selectRaw('*')
                ->whereRaw('tpa_teenager =' . $userid)
                ->whereRaw('tpa_peofession_id =' . $professionid)
                //->whereRaw('FIND_IN_SET('.$type.',tpa_type)')
                ->get();
        return $professionattempt;
    }

    /**
     * Parameter: $teenagerId and $professionId
     * return: array of teenager level3 boosters points by profession ID
     */
    public function addTeenagerLevel3BoosterByProfessionid($userid, $points, $professionid = 0) {
        if ($professionid > 0) {
            $teenagerLevelPoints = DB::table(config::get('databaseconstants.TBL_TEENAGER_LEVEL_BOOSTERS'))->where("tlb_teenager", $userid)->where('tlb_profession', $professionid)->where("tlb_level", config::get('constant.LEVEL3_ID'))->first();
        }
        $teenagerLevel3PointsRow = [];
        if (empty($teenagerLevelPoints)) {
            $teenagerLevel3PointsRow['tlb_points'] = $points;
            $teenagerLevel3PointsRow['tlb_profession'] = $professionid;
            $teenagerLevel3PointsRow['tlb_teenager'] = $userid;
            $teenagerLevel3PointsRow['tlb_level'] = config::get('constant.LEVEL3_ID');

            DB::table(config::get('databaseconstants.TBL_TEENAGER_LEVEL_BOOSTERS'))->insert($teenagerLevel3PointsRow);
        }
    }

    /**
     * Parameter : $userid
     * return : array of attempted profession of teenager and level wise booster score
     */
    public function getTeenagerAttemptedProfession($userid) {
        $mainArray = [];
        $professionattempt = DB::select(DB::raw("select profession.pf_name,profession.pf_logo,profession.id from " . config::get('databaseconstants.TBL_TEENAGER_PROFESSION_ATTEMPTED') . " AS attempted
                                                      join " . config::get('databaseconstants.TBL_PROFESSIONS') . " AS profession on attempted.tpa_peofession_id = profession.id
                                                      where attempted.tpa_teenager=" . $userid ." AND profession.deleted=" . Config::get('constant.ACTIVE_FLAG') . " order BY attempted.id DESC"));
        
        return $professionattempt;
    }

    /**
     * Parameter : $userid
     * return : array of attempted profession of teenager and level wise booster score
     */
    public function getTeenagerAttemptedProfessionsSlotWise($userId, $lastAttemptedId = '') {
        $professionAttempt = $this->model->selectRaw('pro_pf_profession.pf_name, pro_pf_profession.pf_logo, pro_pf_profession.id, attempted.id as attemptedId')
                                ->join(config::get('databaseconstants.TBL_TEENAGER_PROFESSION_ATTEMPTED'). " AS attempted", 'attempted.tpa_peofession_id', '=', 'pro_pf_profession.id')
                                ->where('attempted.tpa_teenager', $userId)
                                ->where(function($query) use ($lastAttemptedId)  {
                                    if(isset($lastAttemptedId) && !empty($lastAttemptedId)) {
                                        $query->where('attempted.id', '<', $lastAttemptedId);
                                    }
                                 })
                                ->where('pro_pf_profession.deleted', Config::get('constant.ACTIVE_FLAG'))
                                ->orderBy('attempted.id', 'DESC')
                                ->limit(10)
                                ->get();

        return $professionAttempt;
    }

    public function getTeenagerAttemptedProfessionCount($userId, $lastAttemptedId = '') {
        $professionAttemptCount = $this->model->selectRaw('pro_pf_profession.pf_name, pro_pf_profession.pf_logo, pro_pf_profession.id, attempted.id as attemptedId')
                                ->join(config::get('databaseconstants.TBL_TEENAGER_PROFESSION_ATTEMPTED'). " AS attempted", 'attempted.tpa_peofession_id', '=', 'pro_pf_profession.id')
                                ->where('attempted.tpa_teenager', $userId)
                                ->where(function($query) use ($lastAttemptedId)  {
                                    if(isset($lastAttemptedId) && !empty($lastAttemptedId)) {
                                        $query->where('attempted.id', '<', $lastAttemptedId);
                                    }
                                 })
                                ->where('pro_pf_profession.deleted', Config::get('constant.ACTIVE_FLAG'))
                                ->orderBy('attempted.id', 'DESC')
                                ->limit(10)
                                ->count();

        return $professionAttemptCount;
    }

    /**
     * Parameter : $profession name
     * return : get profession id by profession name
     */
    public function getProfessionIdByName($professionname) {
        $professionid = $this->model->select('*')->where('pf_name', $professionname)->where("deleted", 1)->get();
        if (count($professionid) != 0) {
            return $professionid[0]->id;
        } else {
            return '0';
        }
    }

    public function getLevel3ActivityWithAnswer($id) {
        $level3activities = DB::table("pro_l4aapa_level4_profession_progress AS attempt")
                ->join(config::get('databaseconstants.TBL_PROFESSIONS') . " AS profession", 'attempt.profession_id', '=', 'profession.id')
                ->select('pf_name','profession.id','pf_logo')
                ->where('attempt.teenager_id', '=', $id)
                ->where('attempt.level4_total', '>', 0)
                ->get();       
        return $level3activities;
    }

    public function getAllActiveProfession() {
        $result = DB::table(config::get('databaseconstants.TBL_PROFESSIONS'))->where("deleted", 1)->get();
        return $result;
    }

    public function getExportProfession() {
        
        $professionData = array();
        $return = DB::table(config::get('databaseconstants.TBL_PROFESSIONS') . " AS profession")
                ->join(config::get('databaseconstants.TBL_BASKETS') . " AS basket", 'profession.pf_basket', '=', 'basket.id')
                ->selectRaw('profession.id,profession.pf_name, profession.pf_video, basket.b_name, basket.b_video')
                ->where('profession.deleted',1)
                ->get();
        if (isset($return) && !empty($return)) {
            $finalData = array();
            
          
            foreach ($return as $key => $val) {  
                $professionData = $professionHeaders = array();
                
                $headers = DB::table(config::get('databaseconstants.TBL_PROFESSION_HEADER') . " AS header")->select('pfic_profession', 'pfic_title', 'pfic_content')->where('pfic_profession', $val->id)->where('pfic_title','!=', 'profession_video')->where('pfic_title','!=', 'basket_video')->get();
                if (isset($headers) && !empty($headers)) {
                    foreach ($headers as $hkey => $hval) {
                        $professionHeaders[$hval->pfic_title] = $hval->pfic_content;
                    }
                }
                $professionData = array('basket_name' => $val->b_name, 'basket_video' => $val->b_video, 'profession_name' => $val->pf_name, 'profession_video' => $val->pf_video);
                $finalData[] = array_merge($professionData, $professionHeaders);
            }
        }
        return $finalData;
    }

    public function checkForBasket($id) {
        $return = DB::table(config::get('databaseconstants.TBL_PROFESSIONS'))
                ->where('pf_basket', $id)
                ->where('deleted', 1)
                ->get()
                ->toArray();

        return $return;
    }

    public function getProfessionAttemptedCount($professionIds,$sort,$gender) {

        $sortBy = (isset($sort) && ($sort == 'top' || $sort == 'all'))?'desc':'asc';
        $limit = (isset($sort) && ($sort == 'top' || $sort == 'bottom'))?10:count($professionIds);
        $gen = [];
        if ($gender == '') {
            $gen[] = 1;
            $gen[] = 2;
        }else {
            $gen[] = $gender;
        }
        $professionattemptCount = DB::table(config::get('databaseconstants.TBL_TEENAGER_PROFESSION_ATTEMPTED') . " AS pa")
                ->join(config::get('databaseconstants.TBL_PROFESSIONS') . " AS p", 'pa.tpa_peofession_id', '=', 'p.id')
                ->join(config::get('databaseconstants.TBL_TEENAGERS') . " AS teen", 'teen.id', '=', 'pa.tpa_teenager')
                ->selectRaw('count(pa.tpa_peofession_id) as professionCount,p.id,p.pf_name')
                ->whereIn('tpa_peofession_id',$professionIds)
                ->whereIn('teen.t_gender', $gen)
                ->groupBy('tpa_peofession_id')
                ->orderBy('professionCount',$sortBy)
                ->paginate($limit);

        return $professionattemptCount;
    }

    public function getNotAttemptedProfession($ids) {
        $return = DB::table(config::get('databaseconstants.TBL_PROFESSIONS'))
                ->select('*')
                ->whereIn('id', $ids)
                ->where('deleted', 1)
                ->get();

        return $return;
    }

    /**
     * Parameter : $professionArr, $userid
     * return : array of teenagers who attempted same profession
     */
    public function getTeenagerByAttemptedProfessions($attemptedProfeArr,$loggedUser,$schoolId)
    {
//        $query = DB::table(config::get('databaseconstants.TBL_TEENAGERS') . " AS teenager");
//        $query->leftjoin(config::get('databaseconstants.TBL_TEENAGER_PROFESSION_ATTEMPTED') . " AS attempted ", 'attempted.tpa_teenager', '=', 'teenager.id');
//        $query->selectRaw('teenager.id as teenagerid,teenager.t_uniqueid,teenager.t_name,teenager.t_photo,teenager.t_email,teenager.t_phone');
//        $query->groupBy('attempted.tpa_teenager');
//        if(isset($schoolId) && $schoolId != '' && $schoolId != 0){
//          $query->where('teenager.t_school', $schoolId);
//        }
//        $query->OrwhereIn('attempted.tpa_peofession_id', $attemptedProfeArr);
//        $query->where('teenager.id','!=', $loggedUser);
//        $professionattempt = $query->get();
//
        $attemptedProfeArrStr = '';
        $whereArrStr = [];
        $whereStr = '';
        if(isset($attemptedProfeArr) && !empty($attemptedProfeArr)){
            $attemptedProfeArrStr = implode(',', $attemptedProfeArr);
            $whereArrStr[] = 'attempted.tpa_peofession_id IN ('.$attemptedProfeArrStr.')';
        }

        if(isset($schoolId) && $schoolId != '' && $schoolId != 0){
          $whereArrStr[] = " teenager.t_school = ".$schoolId;
        }else{
          $schoolWhere = "";
        }

        if(isset($whereArrStr) && !empty($whereArrStr)) {
            $whereStr = '('.implode(" OR",$whereArrStr).')';
        }
        
        if ($whereStr == '') {
            $professionattempt = [];
        }else {
            $professionattempt = DB::select(DB::raw("select teenager.id as teenagerid,teenager.t_uniqueid,teenager.t_name,teenager.t_photo,teenager.t_email,teenager.t_phone from " . config::get('databaseconstants.TBL_TEENAGERS') . " AS teenager
                            left join " . config::get('databaseconstants.TBL_TEENAGER_PROFESSION_ATTEMPTED') . " AS attempted on attempted.tpa_teenager=teenager.id
                            where ( " . $whereStr ."  ) AND teenager.id != ".$loggedUser." AND teenager.is_search_on = 1 group by attempted.tpa_teenager"));

        }
        return $professionattempt;
    }

    public function getProfessionNameById($professionId) {
        $professionid = $this->model->select('*')->where('id', $professionId)->where("deleted", 1)->get();
        if (count($professionid) != 0) {
            return $professionid[0]->pf_name;
        } else {
            return '0';
        }
    }

    public function getProfessionsById($professionid) {
        $professionDetail = DB::select(DB::raw("select profession.pf_video_type , profession.id as 'professionid' , profession.pf_name , profession.pf_video, profession.pf_logo from " . config::get('databaseconstants.TBL_PROFESSIONS') . " AS profession
                                                      where profession.id=" . $professionid . " LIMIT 0,1"));
        return $professionDetail;
    }
    
    /**
    * Parameter : $userid
    * return : array of attempted profession of teenager and level wise booster score
    */
    public function getTeenagerAttemptedProfessionForDashboard($userid) {
        $mainArray = [];
        
        $query = DB::table(config::get('databaseconstants.TBL_TEENAGER_PROFESSION_ATTEMPTED') . " AS attempted");
        $query->join(config::get('databaseconstants.TBL_PROFESSIONS') . " AS profession", 'attempted.tpa_peofession_id', '=', 'profession.id');
        $query->select('profession.id','profession.pf_name');
        $query->where('attempted.tpa_teenager', '=', $userid);
        $query->orderBy('attempted.id', 'DESC');
        $mainArray =  $query->get();
        return $mainArray;
    }

    public function getTeenagerAttemptedProfessionForReport($userid,$professionid) {
        $mainArray = [];
        $professionattempt = DB::select(DB::raw("select teenager.t_gender , profession.pf_name,profession.pf_logo,profession.id from " . config::get('databaseconstants.TBL_TEENAGER_PROFESSION_ATTEMPTED') . " AS attempted
                                                      join " . config::get('databaseconstants.TBL_PROFESSIONS') . " AS profession on attempted.tpa_peofession_id = profession.id
                                                      join " . config::get('databaseconstants.TBL_TEENAGERS') . " AS teenager on teenager.id = attempted.tpa_teenager
                                                      where profession.id = " . $professionid ." group by attempted.tpa_teenager order BY attempted.id DESC"));

        return $professionattempt;
    }

    public function checkActiveProfession($id) {
        $profession = $this->model->where('deleted', '1')->where('id', $id)->get();
        if ($profession->count() > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
    * Parameter : $userid
    * return : array od basket attempt of teenager
    */
    public function getTeenagerTotalProfessionAttempted($userid)
    {
        $profession = DB::table(config::get('databaseconstants.TBL_PROFESSIONS') . " AS p")
                ->join(config::get('databaseconstants.TBL_TEENAGER_PROFESSION_ATTEMPTED') . " AS pa", 'pa.tpa_peofession_id', '=', 'p.id')
                ->selectRaw('count(pa.tpa_peofession_id) as professionAttemptCount')
                ->where('pa.tpa_teenager', '=', $userid)
                ->first();

        // $profession = DB::table(config::get('databaseconstants.TBL_TEENAGER_PROFESSION_ATTEMPTED') . " AS pa")
                // ->join(config::get('databaseconstants.TBL_PROFESSIONS') . " AS p", 'pa.tpa_peofession_id', '=', 'p.id')
        return $profession;
    }

    public function getMyCareers($teenId)
    {
        $careers = $this->model
                    ->join('pro_srp_star_rated_professions AS ratedCareer', 'pro_pf_profession.id', '=', 'ratedCareer.srp_profession_id')
                    ->selectRaw('pro_pf_profession.id, pro_pf_profession.pf_name, pro_pf_profession.pf_logo, pro_pf_profession.pf_slug')
                    ->where('ratedCareer.srp_teenager_id', $teenId)
                    ->where('pro_pf_profession.deleted', 1)
                    ->orderBy('ratedCareer.id', 'DESC')
                    ->get();
        return $careers;
    }
    
    public function getMyCareersCount($teenId, $careerId = '')
    {
        $careersCount = $this->model
                    ->join('pro_srp_star_rated_professions AS ratedCareer', 'pro_pf_profession.id', '=', 'ratedCareer.srp_profession_id')
                    ->selectRaw('pro_pf_profession.id, pro_pf_profession.pf_name, pro_pf_profession.pf_logo, ratedCareer.id as careerId, pro_pf_profession.pf_slug')
                    ->where(function($query) use ($careerId)  {
                        if(isset($careerId) && !empty($careerId)) {
                            $query->where('ratedCareer.id', '<', $careerId);
                        }
                     })
                    ->where('srp_teenager_id', $teenId)
                    ->orderBy('ratedCareer.id', 'DESC')
                    ->count();
        return $careersCount;
    }

    public function getMyCareersSlotWise($teenId, $careerId = '')
    {
        $careers = $this->model
                    ->join('pro_srp_star_rated_professions AS ratedCareer', 'pro_pf_profession.id', '=', 'ratedCareer.srp_profession_id')
                    ->selectRaw('pro_pf_profession.id, pro_pf_profession.pf_name, pro_pf_profession.pf_logo, ratedCareer.id as careerId, pro_pf_profession.pf_slug')
                    ->where(function($query) use ($careerId)  {
                        if(isset($careerId) && !empty($careerId)) {
                            $query->where('ratedCareer.id', '<', $careerId);
                        }
                     })
                    ->where('srp_teenager_id', $teenId)
                    ->orderBy('ratedCareer.id', 'DESC')
                    ->limit(10)
                    ->get();
        return $careers;
    }

    public function getAllProfessionsData()
    {
        $professions = DB::table(config::get('databaseconstants.TBL_PROFESSIONS') . " AS profession")
                ->join(config::get('databaseconstants.TBL_BASKETS') . " AS basket", 'profession.pf_basket', '=', 'basket.id')
                ->selectRaw('profession.* , basket.b_name')
                ->where('profession.deleted', '<>', Config::get('constant.DELETED_FLAG'));
        return $professions;
    }
}
