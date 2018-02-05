<?php

namespace App\Http\Controllers\Webservice;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Auth;
use Illuminate\Http\Request;
use App\Services\Teenagers\Contracts\TeenagersRepository;
use App\Services\Coupons\Contracts\CouponsRepository;
use App\Services\Template\Contracts\TemplatesRepository;
use Config;
use Storage;
use Helpers;
use Mail;
use App\Notifications;
use App\Teenagers;

class CouponController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(TeenagersRepository $teenagersRepository, CouponsRepository $couponsRepository, TemplatesRepository $templateRepository)
    {
        //$this->middleware('admin.guest', ['except' => 'logout']);
        $this->couponsRepository = $couponsRepository;
        $this->teenagersRepository = $teenagersRepository;
        $this->templateRepository = $templateRepository;
        $this->teenagerThumbImageUploadPath = Config::get('constant.TEEN_THUMB_IMAGE_UPLOAD_PATH');
        $this->couponOriginalImageUploadPath = Config::get('constant.COUPON_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->couponThumbImageUploadPath = Config::get('constant.COUPON_THUMB_IMAGE_UPLOAD_PATH');
        $this->objNotifications = new Notifications();
        $this->objTeenagers = new Teenagers();
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
            $data['thumbText'] = "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean pretium pellentesque commodo.";
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

    /* Request Params : consumeCoupon
     *  loginToken, userId, couponId, consumedEmail, type
     *  Service after loggedIn user
     */
    public function consumeCoupon(Request $request)
    {
        $response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg') ] ;
        $teenager = $this->teenagersRepository->getTeenagerById($request->userId);
        if($request->userId != "" && $teenager) {
            //Teenager selected sponsor array
            $sponsorArr = array();
            $teenagerSponsor = $this->teenagersRepository->getTeenagerSelectedSponsor($request->userId);
            if (isset($teenagerSponsor) && !empty($teenagerSponsor)) {
                foreach ($teenagerSponsor as $key => $val) {
                    $sponsorArr[] = $val->ts_sponsor;
                }
            }
            //Check if this is valid coupon id
            $couponData = $this->couponsRepository->getCouponsById($request->couponId);
            if (isset($couponData) && !empty($couponData)) {
                if ($couponData->cp_limit == 0) {
                    $response['status'] = 0;
                    $response['message'] = 'Limit is reached of this coupon';
                } else {
                    //Check if user has already consumed or gifted this coupon
                    $consumeCoupon = $this->couponsRepository->checkConsumeCoupon($request->couponId, $request->userId);
                    if (isset($consumeCoupon) && !empty($consumeCoupon)) {
                        $response['status'] = 0;
                        $response['message'] = 'You have already consumed this coupon';
                    } elseif (!in_array($couponData->cp_sponsor, $sponsorArr)) {
                        $response['status'] = 0;
                        $response['message'] = 'Unauthorised coupon';
                    } else {
                        if (isset($couponData->cp_image) && $couponData->cp_image != '' && Storage::size($this->couponOriginalImageUploadPath . $couponData->cp_image) > 0) {
                                $coupon_image = Storage::url($this->couponOriginalImageUploadPath . $couponData->cp_image);
                            } else {
                                $coupon_image = Storage::url($this->couponOriginalImageUploadPath . 'proteen-logo.png');
                            }

                            //Send email to user for coupon
                            $replaceArray = array();
                            $replaceArray['TEEN_NAME'] = $request->consumedEmail;
                            $replaceArray['SPONSOR_NAME'] = $couponData->sp_company_name;
                            $replaceArray['COUPON_IMAGE_URL'] = '<img src="' . $coupon_image . '" alt="">';
                            $replaceArray['COUPON_CODE'] = $couponData->cp_code;

                            $emailTemplateContent = $this->templateRepository->getEmailTemplateDataByName(Config::get('constant.CONSUME_COUPON_TEMPLATE'));

                            $content = $this->templateRepository->getEmailContent($emailTemplateContent->et_body, $replaceArray);

                            $data = array();
                            $data['subject'] = $emailTemplateContent->et_subject;
                            $data['toEmail'] = (isset($request->consumedEmail) && !empty($request->consumedEmail) && $request->consumedEmail != '' && $request->type == 'gift') ? $request->consumedEmail : $teenager->t_email;
                            $data['toName'] = '';
                            $data['content'] = $content;
                            $data['teen_id'] = $request->userId;
                            $data['tcu_coupon_id'] = $request->couponId;
                            $data['tcu_allocated_email'] = $teenager->t_email;
                            $data['tcu_consumed_email'] = (isset($request->consumedEmail) && !empty($request->consumedEmail) && $request->consumedEmail != '' && $request->type == 'gift') ? $request->consumedEmail : $teenager->t_email;
                            $data['tcu_type'] = $request->type;
                            $data['couponData'] = $couponData;
                            $data['consumedEmail'] = $request->consumedEmail;
                            $data['teenager'] = $teenager;

                            Mail::send(['html' => 'emails.Template'], $data, function($message) use ($data) {
                                $message->subject($data['subject']);
                                $message->to($data['toEmail'], $data['toName']);

                                $teenagerConsumeCouponData['tcu_teenager'] = $data['teen_id'];
                                $teenagerConsumeCouponData['tcu_coupon_id'] = $data['tcu_coupon_id'];
                                $teenagerConsumeCouponData['tcu_allocated_email'] = $data['tcu_allocated_email'];
                                $teenagerConsumeCouponData['tcu_consumed_email'] = $data['tcu_consumed_email'];
                                $teenagerConsumeCouponData['tcu_type'] = $data['tcu_type'];
                                $coupanResponse = $this->couponsRepository->saveTeenagerConsumedCoupon($teenagerConsumeCouponData);
                                $teenager = $data['teenager'];
                                $giftedUserData = $this->objTeenagers->getTeenagersDataByEmailId($data['consumedEmail']);
                                if($teenager->id != $giftedUserData->id){
                                    if($coupanResponse){
                                        $notificationData['n_sender_id'] = $teenager->id;
                                        $notificationData['n_sender_type'] = Config::get('constant.NOTIFICATION_TEENAGER');
                                        $notificationData['n_receiver_id'] = $giftedUserData->id;
                                        $notificationData['n_receiver_type'] = Config::get('constant.NOTIFICATION_TEENAGER');
                                        $notificationData['n_notification_type'] = Config::get('constant.NOTIFICATION_TYPE_GIFT_COUPANS');
                                        $notificationData['n_notification_text'] = '<strong>'.ucfirst($teenager->t_name).' '.ucfirst($teenager->t_lastname).'</strong> gifted you '.$data['couponData']->cp_code.' coupan';
                                        $this->objNotifications->insertUpdate($notificationData);
                                    }
                                }
                                    });
                            $response['status'] = 1;
                            $response['message'] = trans('appmessages.default_success_msg');
                        }
                    }
                } else {
                    $response['status'] = 0;
                    $response['message'] = 'Invalid coupon';
                }
                $response['login'] = 1;
        } else {
            $response['message'] = trans('appmessages.invalid_userid_msg') . ' or ' . trans('appmessages.notvarified_user_msg');
        }
        return response()->json($response, 200);
        exit;
    }
}