<?php

namespace App\Services\Coupons\Repositories;

use DB;
use Config;
use App\Services\Coupons\Contracts\CouponsRepository;
use App\Services\Repositories\Eloquent\EloquentBaseRepository;

class EloquentCouponsRepository extends EloquentBaseRepository
    implements CouponsRepository
{
     /**
     * @return array of all the active Coupons
       Parameters
       @$searchParamArray : Array of Searching and Sorting parameters
     */

      public function getAllCoupons($searchParamArray = array())
      {
        $whereStr = '';
        $orderStr = '';

        $whereArray = [];
        $whereArray[] = 'coupon.deleted IN (1,2)';
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

        $coupons = DB::table(config::get('databaseconstants.TBL_COUPONS'). " AS coupon")
                              ->join(config::get('databaseconstants.TBL_SPONSORS') . " AS sponsor", 'coupon.cp_sponsor', '=', 'sponsor.id')
                              ->selectRaw('coupon.* , sponsor.sp_company_name')
                              ->whereRaw($whereStr . $orderStr)
                              ->paginate(Config::get('constant.ADMIN_RECORD_PER_PAGE'));
        return $coupons;
      }

    /**
     * @return Coupons details object
       Parameters
       @$couponDetail : Array of coupons detail from front
     */
    public function saveCouponDetail($couponDetail)
    {
        if($couponDetail['id'] != '' && $couponDetail['id'] > 0)
        {
            $return = $this->model->where('id', $couponDetail['id'])->update($couponDetail);
        }
        else
        {
            $return = $this->model->create($couponDetail);
        }

        return $return;
    }

     /**
     * @return Boolean True/False
       Parameters
       @$id : Coupon ID
     */
    public function deleteCoupon($id)
    {
        $flag              = true;
        $coupon          = $this->model->find($id);
        $coupon->deleted = config::get('constant.DELETED_FLAG');
        $response          = $coupon->save();
        if($response)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public function saveCouponBulkDetail($couponDetail)
    {
          $result = DB::select(DB::raw("SELECT
                                            *
                                            FROM ".config::get('databaseconstants.TBL_COUPONS')));
          $flag = true;
          if($flag)
          {
                $return = $this->model->create($couponDetail);
                return $return;
          }
          else
          {
                return false;
          }
    }
    
    public function getSponsorsCoupon()
    {
        $coupons = DB::table(config::get('databaseconstants.TBL_COUPONS'). " AS coupon")
                              ->join(config::get('databaseconstants.TBL_SPONSORS') . " AS sponsor", 'coupon.cp_sponsor', '=', 'sponsor.id')
                              ->selectRaw('coupon.* , sponsor.sp_company_name')
                              ->whereRaw('coupon.deleted = 1')
                              ->whereRaw('coupon.cp_validfrom <= "'.date('Y-m-d').'"')
                              ->whereRaw('coupon.cp_validto >= "'.date('Y-m-d').'"')
                              ->whereRaw('coupon.cp_limit != 0')
                              ->get();
        return $coupons; 
    }
    
    public function saveTeenagerConsumedCoupon($data)
    {
        $couponData = DB::table('pro_tcu_teenager_coupon_usage')->insert($data);
        
        DB::table('pro_cp_coupons')->whereId($data['tcu_coupon_id'])->increment('cp_used');
        DB::table('pro_cp_coupons')->whereId($data['tcu_coupon_id'])->decrement('cp_limit');
        
        return $couponData;
    }
    
    public function getCouponsBySponsorId($sponsorId)
    {
        $coupons = DB::table(config::get('databaseconstants.TBL_COUPONS'). " AS coupon")
                              ->join(config::get('databaseconstants.TBL_SPONSORS') . " AS sponsor", 'coupon.cp_sponsor', '=', 'sponsor.id')
                              ->selectRaw('coupon.* , sponsor.sp_company_name')
                              ->whereRaw('coupon.deleted != 3')
                              ->whereRaw('coupon.cp_sponsor = '.$sponsorId)
                              ->get();
        return $coupons; 
    }
    
    public function getCouponsById($couponId)
    {
        $coupon = DB::table(config::get('databaseconstants.TBL_COUPONS'). " AS coupon")
                              ->join(config::get('databaseconstants.TBL_SPONSORS') . " AS sponsor", 'coupon.cp_sponsor', '=', 'sponsor.id')                                
                              ->selectRaw('coupon.*,sponsor.sp_company_name')
                              ->whereRaw('coupon.deleted != 3')
                              ->whereRaw('coupon.id = '.$couponId)
                              ->first();
        return $coupon; 
    }
    
    public function checkConsumeCoupon($couponId,$teengaerId){
        $couponData = DB::table(config::get('databaseconstants.TBL_TEENAGER_COUPON_USAGE'). " AS couponusage")
                              ->selectRaw('couponusage.id')
                              ->whereRaw('couponusage.tcu_coupon_id = '.$couponId)
                              ->whereRaw('couponusage.tcu_teenager = '.$teengaerId)
                              ->first();
        return $couponData;
    }
    
    public function checkConsumeCouponByTeen($couponId){
        $couponData = DB::table(config::get('databaseconstants.TBL_TEENAGER_COUPON_USAGE'). " AS couponusage")
                              ->join(config::get('databaseconstants.TBL_TEENAGERS') . " AS teen", 'couponusage.tcu_teenager', '=', 'teen.id')
                              ->selectRaw('couponusage.* , teen.t_name, teen.t_photo')
                              ->whereRaw('couponusage.tcu_coupon_id = '.$couponId)
                              ->get();
        return $couponData;
    }
}
