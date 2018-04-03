<?php

namespace App\Services\Sponsors\Repositories;

use DB;
use Config;
use App\Services\Sponsors\Contracts\SponsorsRepository;
use App\Services\Repositories\Eloquent\EloquentBaseRepository;

class EloquentSponsorsRepository extends EloquentBaseRepository
    implements SponsorsRepository
{
    /**
     * @return array of all the active sponsors
       Parameters
       @$searchParamArray : Array of Searching and Sorting parameters
     */

      public function getAllSponsors($searchParamArray = array())
      {
            $whereStr = '';
            $orderStr = '';

            $whereArray = [];
            $whereArray[] = 'deleted IN (1,2)';
            if (isset($searchParamArray) && !empty($searchParamArray))
            {
                if (isset($searchParamArray['searchBy']) && isset($searchParamArray['searchText']) && $searchParamArray['searchBy'] != '' && $searchParamArray['searchText'] != '') {
                    $whereArray[] = $searchParamArray['searchBy'] . " LIKE '%" . $searchParamArray['searchText'] . "%'";
                }

                if (isset($searchParamArray['orderBy']) && isset($searchParamArray['sortOrder']) && $searchParamArray['orderBy'] != '' && $searchParamArray['sortOrder'] != '') {
                    $orderStr = " ORDER BY " . $searchParamArray['orderBy'] . " " . $searchParamArray['sortOrder'];
                }
            }

            if(!empty($whereArray))
            {
                $whereStr = implode(" AND ", $whereArray);
            }

            $sponsors = DB::table(config::get('databaseconstants.TBL_SPONSORS'))
                              ->selectRaw('*')
                              ->whereRaw($whereStr . $orderStr)
                              ->paginate(Config::get('constant.ADMIN_RECORD_PER_PAGE'));

            return $sponsors;
      }
      /**
    * @return array
       Parameters
       @$id : Searchtext
     */
    public function getsearchByText($serachtext)
    {
        $sponsors =  DB::select(DB::raw("select sp_company_name,sp_logo from ". config::get('databaseconstants.TBL_SPONSORS'). "
                                                     where sp_company_name like '%".$serachtext."%'"));

        return $sponsors;
    }
     /**
     * @return Sponsor details object
       Parameters
       @$sponsorDetail : Array of sponsors detail from front
     */
    public function saveSponsorDetail($sponsorDetail)
    {
       if (isset($sponsorDetail['id']) && $sponsorDetail['id'] != '' && $sponsorDetail['id'] > 0) {
            $return = $this->model->where('id', $sponsorDetail['id'])->update($sponsorDetail);
        } else {
            $return = $this->model->create($sponsorDetail);
        }

        return $return;
    }

    /**
     * @return Boolean True/False
       Parameters
       @$id : Sponsor ID
     */
    public function deleteSponsor($id)
    {
        $flag              = true;
        $sponsor          = $this->model->find($id);
        $sponsor->deleted = config::get('constant.DELETED_FLAG');
        $response          = $sponsor->save();
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
     * Get Sponsor
     */
    public function getApprovedSponsors()
    {
        $sponsor = $this->model->where('deleted', '1')->where('sp_isapproved','1')->select('id as sponsor_id','sp_email', 'sp_company_name','sp_admin_name','sp_address1','sp_address2','sp_pincode','sp_city','sp_state','sp_country','password','sp_logo','sp_photo','sp_first_name','sp_last_name','sp_title','sp_phone')->get();
        return $sponsor;
    }

     /*
     return : array of Sponsor detail by email id
     */
    public  function getSponsorDetailByEmailId($email)
    {
        $sponsorDetail = $this->model->where('deleted','1')->where('sp_email',$email)->first();
        return $sponsorDetail;
    }

     /**
     * get entire detail related user
     */
    public function getSponsorById($id)
    {
        $SponsorDetails = DB::select( DB::raw("SELECT sponsor.*,country.c_name,country.id as country_id,country.c_code,s_name,city.c_name as city
                                          FROM " . config::get('databaseconstants.TBL_SPONSORS') . " AS sponsor
                                            left join " . config::get('databaseconstants.TBL_COUNTRIES') ." AS country on country.id = sponsor.sp_country
                                            left join " . config::get('databaseconstants.TBL_STATES') . " AS state on state.id = sponsor.sp_state
                                            left join " . config::get('databaseconstants.TBL_CITIES') . " AS city on city.id = sponsor.sp_city                                                                                          
                                           where sponsor.id = ".$id));






        return $SponsorDetails[0];
    }

     /**
     * Edit sponsor to Approve by $id
     */
     public function editToApprovedSponser($id)
     {
        $return = $this->model->where('id', $id)->update(['sp_isapproved' => 1]);
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
       @$email : Sponsor's email
     */
    public function checkActiveEmailExist($email,$id='')
    {
        if($id != '')
        {
            $user = $this->model->where('deleted', '1')->where('sp_email', $email)->where('id','!=',$id)->get();
        }
        else
        {
            $user = $this->model->where('deleted', '1')->where('sp_email', $email)->get();
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
           $user = $this->model->where('deleted', '1')->where('sp_phone', $phone)->where('id','!=',$id)->get();
        }
        else
        {
            $user = $this->model->where('deleted', '1')->where('sp_phone', $phone)->get();
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

     /*
     * Parameter $resetRequest : array of Details of password reset request
     * return : Boolean TRUE
     */
    public function saveSponsorPasswordResetRequest($resetRequest)
    {
        DB::table(config::get('databaseconstants.TBL_SPONSOR_RESET_PASSWORD'))->insert($resetRequest);

        return true;
    }

    /*
     return : array of Sponsor detail by email id

    public  function getSponsorDetailByEmailId($email)
    {
        $sponsorDetail = $this->model->where('deleted','1')->where('sp_email',$email)->first();
        return $sponsorDetail;
    }
     */
     /**
     * Parameter $sponsorId : Sponsor ID from provider
     * Parameter $OTP : One Time Password
     * return : Boolean TRUE / FALSE
    */
    public function verifyOTPAgainstSponsorId($sponsorId, $OTP)
    {
        $result = DB::table(config::get('databaseconstants.TBL_SPONSOR_RESET_PASSWORD'))->where("trp_sponsor", $sponsorId)->where("trp_otp", $OTP)->where("trp_status", 1)->first();

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
                $row = DB::table(config::get('databaseconstants.TBL_SPONSOR_RESET_PASSWORD'))->find($result->id);
                DB::table(config::get('databaseconstants.TBL_SPONSOR_RESET_PASSWORD'))->where('id', $result->id)->update(['trp_status' => 0]);
                return true;
            }
        }
        else
        {
            return false;
        }
    }
    
    public function getActiveSponsorActivityDetail($sponsorId)
    {
        $activityDetail = DB::table(config::get('databaseconstants.TBL_SPONSOR_ACTIVITY'). " AS activity")
                                  ->leftjoin(config::get('databaseconstants.TBL_SPONSORS') . " AS sponsor", 'activity.sa_sponsor_id', '=', 'sponsor.id')
                                  ->leftjoin(config::get('databaseconstants.TBL_SYSTEM_LEVELS') . " AS level", 'activity.sa_apply_level', '=', 'level.id')
                                  ->leftjoin(config::get('databaseconstants.TBL_CONFIGURATION') . " AS type", 'activity.sa_type', '=', 'type.id')
                                  ->selectRaw('activity.*,level.sl_name,type.cfg_key')
                                  ->whereRaw('activity.deleted != 3')                                  
                                  ->whereRaw('activity.sa_sponsor_id = '.$sponsorId)
                                  ->paginate(Config::get('constant.ADMIN_RECORD_PER_PAGE'), ['*'], 'activity');
        return $activityDetail;
    }
    
    public function saveSponsorActivityDetail($activityDetail)
    {
        if (isset($activityDetail['id']) && $activityDetail['id'] != '' && $activityDetail['id'] > 0) {
            $return = DB::table(config::get('databaseconstants.TBL_SPONSOR_ACTIVITY'))->where('id', $activityDetail['id'])->update($activityDetail);
        } else {
            $return = DB::table(config::get('databaseconstants.TBL_SPONSOR_ACTIVITY'))->insert($activityDetail);
        }
        return $return;
    }
    
    public function getActivityById($id)
    {
        $activityDetail = DB::table(config::get('databaseconstants.TBL_SPONSOR_ACTIVITY'))->where('id',$id)->first();
        return $activityDetail;
    }
    
    public function inactiveRecord($id)
    {
        $inactiveDetail = DB::table(config::get('databaseconstants.TBL_SPONSOR_ACTIVITY'))->where('id', $id)->update(['deleted' => 2]);
        return $inactiveDetail;
    }
    
    public function checkForSponsorToTeen($id)
    {
        
        $checkSponsorAllocatedToTeen = DB::table(config::get('databaseconstants.TBL_TEENAGERS_SPONSERS'))->where('ts_sponsor', $id)->get();
        return $checkSponsorAllocatedToTeen;
    }
    
    public function checkForSponsorToCoupon($id)
    {
        $checkSponsorAllocatedToCoupon = DB::table(config::get('databaseconstants.TBL_COUPONS'))->where('cp_sponsor', $id)->get();
        return $checkSponsorAllocatedToCoupon;
    }
    
    public function checkForSponsorToSponsorActivity($id)
    {
        $checkSponsorAllocatedToSponsorActivity = DB::table(config::get('databaseconstants.TBL_SPONSOR_ACTIVITY'))->where('sa_sponsor_id', $id)->get();
        return $checkSponsorAllocatedToSponsorActivity;
    }
    
    public function getSponsorTotalUsedCredit($id)
    {
        $sponsorActivityUsedCredit = DB::select( DB::raw("SELECT SUM(sa_credit_used) as totalactivitycredit  FROM " . config::get('databaseconstants.TBL_SPONSOR_ACTIVITY') . " where sa_sponsor_id = ".$id));
        $sponsorCouponUsedCredit = DB::select( DB::raw("SELECT SUM(cp_credit_used) as totalcouponcredit  FROM " . config::get('databaseconstants.TBL_COUPONS') . " where cp_sponsor = ".$id));
        $sponsorConsumeProCoins = DB::select( DB::raw("SELECT SUM(dc_total_coins) as totalprocoins FROM " . config::get('databaseconstants.TBL_DEDUCTED_COINS') . " where dc_user_id = ".$id." and dc_user_type = 4"));
        $totalCreditUsed = ($sponsorActivityUsedCredit[0]->totalactivitycredit)+($sponsorCouponUsedCredit[0]->totalcouponcredit)+($sponsorConsumeProCoins[0]->totalprocoins);
        return $totalCreditUsed;
    }

    public function getSponsorDataForCoinsDetail($id) {
        $sponsorData = $this->model->where('id', '=', $id)->where('deleted', 1)->get()->toArray();
        $data = [];
        if (isset($sponsorData)) {
            $data['sp_credit'] = $sponsorData[0]['sp_credit'];
        }
        return $data;
    }

    public function updateSponsorCoinsDetail($id, $Coins) {
        $sponsorDetail = $this->model->where('id', $id)->update(['sp_credit' => $Coins]);
        return $sponsorDetail;
    }
    public function getSponsorBySponsorId($id) {
        $sponsorData = $this->model->where('id', '=', $id)->where('deleted', 1)->get()->toArray();
        return $sponsorData;
    }

    public  function getSponsorDetailByUnqiueId($uniqueid)
    {
        $sponsorDetail = $this->model->where('deleted','1')->where('sp_uniqueid',$uniqueid)->first();
        return $sponsorDetail;
    }

     public function checkActiveSponsorExist($id){
        $user = $this->model->where('deleted', '1')->where('id', $id)->get();

        if ($user->count() > 0) {
            return false;
        } else {
            return true;
        }
    }
}
