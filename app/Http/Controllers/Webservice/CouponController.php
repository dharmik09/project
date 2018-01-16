<?php

namespace App\Http\Controllers\Webservice;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Auth;
use Illuminate\Http\Request;
use App\Services\Teenagers\Contracts\TeenagersRepository;
use App\Services\Coupons\Contracts\CouponsRepository;
use Config;
use Storage;
use Helpers;

class CouponController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(TeenagersRepository $teenagersRepository, CouponsRepository $couponsRepository)
    {
        //$this->middleware('admin.guest', ['except' => 'logout']);
        $this->couponsRepository = $couponsRepository;
        $this->teenagersRepository = $teenagersRepository;
        $this->teenagerThumbImageUploadPath = Config::get('constant.TEEN_THUMB_IMAGE_UPLOAD_PATH');
        $this->couponOriginalImageUploadPath = Config::get('constant.COUPON_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->couponThumbImageUploadPath = Config::get('constant.COUPON_THUMB_IMAGE_UPLOAD_PATH');
    }

    /* Request Params : getCoupons
     *  loginToken, userId
     *  Service after loggedIn user
     */
    public function getCoupons(Request $request)
    {
        $response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg') ] ;
        $teenager = $this->teenagersRepository->getTeenagerById($request->userId);
        if($request->userId != "" && $teenager) {
            $data = [];
            $data['couponInfo'] = "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean pretium pellentesque commodo.";
            //Get Coupon
            $sponsorArr = array();
            $coupons = $this->couponsRepository->getSponsorsCoupon();
            $teenagerSponsor = $this->teenagersRepository->getTeenagerSelectedSponsor($request->userId);
            if (isset($teenagerSponsor) && !empty($teenagerSponsor)) {
                foreach ($teenagerSponsor as $key => $val) {
                    $sponsorArr[] = $val->ts_sponsor;
                }
            }
            $couponsArr = array();
            // Check weather coupon is active or inactive for teenager
            if (isset($coupons) && !empty($coupons)) {
                foreach ($coupons as $key => $coupon) {
                    $finalCoupons['code'] = $coupon->cp_code;
                    $finalCoupons['coupon_id'] = $coupon->id;
                    $finalCoupons['coupon_description'] = $coupon->cp_description;
                    $finalCoupons['sponsor'] = $coupon->sp_company_name;
                    if (in_array($coupon->cp_sponsor, $sponsorArr)) {
                        $finalCoupons['type'] = 'active';
                    } else {
                        $finalCoupons['type'] = 'inactive';
                    }
                    if (isset($coupon->cp_image) && $coupon->cp_image != '' && Storage::size($this->couponOriginalImageUploadPath . $coupon->cp_image) > 0) {
                        $finalCoupons['coupon_logo'] = Storage::url($this->couponOriginalImageUploadPath . $coupon->cp_image);
                    } else {
                        $finalCoupons['coupon_logo'] = Storage::url($this->couponOriginalImageUploadPath . 'proteen-logo.png');
                    }
                    // Check if coupon is already consumed or not
                    $consumeCoupon = $this->couponsRepository->checkConsumeCoupon($coupon->id, $request->userId);
                    $finalCoupons['is_consume'] = isset($consumeCoupon) && !empty($consumeCoupon) ? 1 : 0;
                    $couponsArr[] = $finalCoupons;
                }
            } 
            $data['coupons'] = $couponsArr;
            $response['login'] = 1;
            $response['status'] = 1;
            $response['message'] = trans('appmessages.default_success_msg');
            $response['data'] = $data;
        } else {
            $response['message'] = trans('appmessages.invalid_userid_msg') . ' or ' . trans('appmessages.notvarified_user_msg');
        }
        return response()->json($response, 200);
        exit;
    }
}