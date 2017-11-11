<?php

namespace App\Services\Coupons\Contracts;
use App\Services\Repositories\BaseRepository;
use App\Coupons;

interface CouponsRepository extends BaseRepository
{
    /**
     * @return array of all active coupons in the application
     */
    public function getAllCoupons($searchParamArray = array());

    /**
     * Save Parent detail passed in $couponDetail array
     */
    public function saveCouponDetail($couponDetail);

    public function saveCouponBulkDetail($couponDetail);

    /**
     * Delete Coupon by $id
     */
    public function deleteCoupon($id);

}
