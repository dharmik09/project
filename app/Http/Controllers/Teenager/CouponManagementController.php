<?php

namespace App\Http\Controllers\Teenager;

use App\Http\Controllers\Controller;
use App\Services\Coupons\Contracts\CouponsRepository;
use App\Services\Teenagers\Contracts\TeenagersRepository;
use Auth;
use Config;
use Storage;
use Input;
use App\Services\Template\Contracts\TemplatesRepository;
use App\Teenagers;
use App\Notifications;
use Mail;
use Helpers;

class CouponManagementController extends Controller {

    public function __construct(CouponsRepository $couponsRepository, TeenagersRepository $teenagersRepository, TemplatesRepository $templateRepository) 
    {
        $this->couponsRepository = $couponsRepository;
        $this->teenagersRepository = $teenagersRepository;
        $this->couponOriginalImageUploadPath = Config::get('constant.COUPON_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->couponThumbImageUploadPath = Config::get('constant.COUPON_THUMB_IMAGE_UPLOAD_PATH');
        $this->templateRepository = $templateRepository;
        $this->objTeenagers = new Teenagers();
        $this->objNotifications = new Notifications();
    }

    public function coupons() 
    {
        $sponsorArr = array();
        $teenagerId = Auth::guard('teenager')->user()->id;
        $coupons = $this->couponsRepository->getSponsorsCoupon();
        $teenagerSponsor = $this->teenagersRepository->getTeenagerSelectedSponsor($teenagerId);
        if(isset($teenagerSponsor) && !empty($teenagerSponsor)) {
            foreach($teenagerSponsor as $key=>$val) {
                $sponsorArr[] = $val->ts_sponsor;
            }
        }
        $couponsArr = array();
        // Check weather coupon is active or inactive for teenager
        if(isset($coupons) && !empty($coupons)) {
            foreach($coupons as $key=>$coupon) {
                $finalCoupons['id'] = $coupon->id;
                $finalCoupons['code'] = $coupon->cp_code;
                $finalCoupons['description'] = $coupon->cp_description;
                $finalCoupons['sponsor'] = $coupon->sp_company_name;
                if(in_array($coupon->cp_sponsor, $sponsorArr)){
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
                $consumeCoupon = $this->couponsRepository->checkConsumeCoupon($coupon->id,$teenagerId);
                $finalCoupons['is_consume'] = isset($consumeCoupon) && !empty($consumeCoupon)?1:0;
               $couponsArr[] = $finalCoupons;
            }
        }
        return view('teenager.coupons', compact('couponsArr'));
    }

    public function consumeCoupon()
    {
        $sponsorArr = array();
        $teenagerSponsor = $this->teenagersRepository->getTeenagerSelectedSponsor(Auth::guard('teenager')->user()->id);
        if(isset($teenagerSponsor) && !empty($teenagerSponsor)) {
            foreach($teenagerSponsor as $key=>$val) {
                $sponsorArr[] = $val->ts_sponsor;
            }
        }
        $coupon_id = Input::get('coupon_id');
        $user_email = Input::get('user_email');
        $usage_type = Input::get('usage_type');

        //Check if this is valid coupon id
        $couponData = $this->couponsRepository->getCouponsById($coupon_id);
        if(isset($couponData) && !empty($couponData)) {
            if($couponData->cp_limit == 0) {
                echo "limit"; exit; 
            }
            else {            
                //Check if user has already consumed or gifted this coupon
                $consumeCoupon = $this->couponsRepository->checkConsumeCoupon($coupon_id, Auth::guard('teenager')->user()->id);
                if(isset($consumeCoupon) && !empty($consumeCoupon)) {
                   echo "consumed"; exit; 
                } else if(!in_array($couponData->cp_sponsor, $sponsorArr)) {
                   echo "unauthorised"; exit; 
                }
                else {
                    if (isset($couponData->cp_image) && $couponData->cp_image != '' ) {
                        $coupon_image = Storage::url($this->couponOriginalImageUploadPath . $couponData->cp_image);
                    } else {
                        $coupon_image = Storage::url($this->couponOriginalImageUploadPath . 'proteen-logo.png');
                    }

                    //Send email to user for coupon
                    $replaceArray = array();
                    $replaceArray['TEEN_NAME'] = $user_email;
                    $replaceArray['SPONSOR_NAME'] = $couponData->sp_company_name;
                    $replaceArray['COUPON_IMAGE_URL'] = '<img src="'.$coupon_image.'" alt="">';
                    $replaceArray['COUPON_CODE'] = $couponData->cp_code;

                    $emailTemplateContent = $this->templateRepository->getEmailTemplateDataByName(Config::get('constant.CONSUME_COUPON_TEMPLATE'));

                    $content = $this->templateRepository->getEmailContent($emailTemplateContent->et_body, $replaceArray);

                    $data = array();
                    $data['subject'] = $emailTemplateContent->et_subject;
                    $data['toEmail'] = $user_email;
                    $data['toName'] = $user_email;
                    $data['content'] = $content;
                    $data['teen_id'] = Auth::guard('teenager')->user()->id;
                    $data['tcu_coupon_id'] = $coupon_id;
                    $data['tcu_allocated_email'] = Auth::guard('teenager')->user()->t_email;
                    $data['tcu_consumed_email'] = $user_email;
                    $data['tcu_type'] = $usage_type;
                    $data['couponData'] = $couponData;

                    Mail::send(['html' => 'emails.Template'], $data, function($message) use ($data) {
                        $message->subject($data['subject']);
                        $message->to($data['toEmail'], $data['toName']);

                        $teenagerConsumeCouponData['tcu_teenager'] = $data['teen_id'];
                        $teenagerConsumeCouponData['tcu_coupon_id'] = $data['tcu_coupon_id'];
                        $teenagerConsumeCouponData['tcu_allocated_email'] = $data['tcu_allocated_email'];
                        $teenagerConsumeCouponData['tcu_consumed_email'] = $data['tcu_consumed_email'];
                        $teenagerConsumeCouponData['tcu_type'] = $data['tcu_type'];
                        $response = $this->couponsRepository->saveTeenagerConsumedCoupon($teenagerConsumeCouponData);
                        $userData = Auth::guard('teenager')->user();
                        $giftedUserData = $this->objTeenagers->getTeenagersDataByEmailId($data['toEmail']);
                        if($userData->id != $giftedUserData->id){
                            if($response){
                                $notificationData['n_sender_id'] = $userData->id;
                                $notificationData['n_sender_type'] = Config::get('constant.NOTIFICATION_TEENAGER');
                                $notificationData['n_receiver_id'] = $giftedUserData->id;
                                $notificationData['n_receiver_type'] = Config::get('constant.NOTIFICATION_TEENAGER');
                                $notificationData['n_notification_type'] = Config::get('constant.NOTIFICATION_TYPE_GIFT_COUPANS');
                                $notificationData['n_notification_text'] = '<strong>'.ucfirst($userData->t_name).' '.ucfirst($userData->t_lastname).'</strong> gited you '.$data['couponData']->cp_code.' coupan';
                                $this->objNotifications->insertUpdate($notificationData);
                            }
                        }                      
                    });     
                    echo "success"; exit;
                } 
            }                                            
        } else {
            echo "invalid"; exit;
        }                
    }

    public function getUsers()
    {
        $teenId = Auth::guard('teenager')->user()->id;
        $coupon_id = Input::get('coupon_id');
        $searchKeyword = Input::get('search_keyword');
        $searchArray = explode(",", $searchKeyword);
        if ($searchKeyword != '') {
            $activeTeenagers = Helpers::getActiveTeenagersForGiftCoupon($teenId, $searchArray);
            $teenagerArr = array();
            return view('teenager.searchedUsersForGiftCoupons', compact('activeTeenagers', 'coupon_id'));
        }
    }
}
