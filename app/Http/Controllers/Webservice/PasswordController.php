<?php

namespace App\Http\Controllers\Webservice;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Helpers;
use Config;
use Storage;
use Mail;
use App\Services\Teenagers\Contracts\TeenagersRepository;
use App\Services\Template\Contracts\TemplatesRepository;

class PasswordController extends Controller
{
    public function __construct(TeenagersRepository $teenagersRepository, TemplatesRepository $templatesRepository)
    {
        $this->teenagersRepository = $teenagersRepository;
        $this->templateRepository = $templatesRepository;
    }

    /* Request Params : setPassword
    *  userId, loginToken, newPassword
    */
    public function setPassword(Request $request)
    {
		$response = [ 'status' => 0, 'login' => 1, 'message' => trans('appmessages.default_error_msg') ];
    	if($request->newPassword != "" && $request->userId != "") {
    		$teenagerDetail['id'] = $request->userId;
            $teenagerDetail['password'] = bcrypt($request->newPassword);
            $this->teenagersRepository->saveTeenagerDetail($teenagerDetail);
            $response['status'] = 1;
            $response['login'] = 1;
            $response['message'] = trans('appmessages.default_success_msg');
    	} else {
            $response['login'] = 1;
            $response['message'] = trans('appmessages.missing_data_msg');
    	}
    	
    	return response()->json($response, 200);
    	exit;
    }

    /* Request Params : changePassword
    *  userId, loginToken, newPassword, oldPassword
    *  
    */
    public function changePassword(Request $request)
    {
        $response = [ 'status' => 0, 'login' => 1, 'message' => trans('appmessages.default_error_msg') ] ;
        if($request->newPassword != "" && strlen($request->newPassword) < 6) {
            $response['message'] = "Password length should be 6 digit long!";
            $response['status'] = 0;
            $response['login'] = 1;
            return response()->json($response, 200);
            exit;
        }
        
        if($request->newPassword != "" && $request->oldPassword != "" && $request->userId != "") {
            $bool = $this->teenagersRepository->checkCurrentPasswordAgainstTeenager($request->userId, $request->oldPassword);
            if($bool) {
                $teenagerDetail['id'] = $request->userId;
                $teenagerDetail['password'] = bcrypt($request->newPassword);
                $this->teenagersRepository->saveTeenagerDetail($teenagerDetail);
                $response['status'] = 1;
                $response['login'] = 1;
                $response['message'] = trans('Password changed successfully!');
            } else {
                $response['login'] = 1;
                $response['message'] = trans('appmessages.invalidoldpassword');
            }
        } else {
            $response['login'] = 1;
            $response['message'] = trans('appmessages.missing_data_msg');
        }
        
        return response()->json($response, 200);
        exit;
    }

    /* Request Params : resetPassword
    *  userId, newPassword, otp
    *  No loginToken required because it's call without loggedin user
    */
    public function resetPassword(Request $request)
    {
        $response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg') ] ;
        if($request->newPassword != "" && $request->userId != "" && $request->otp != "") {
            $bool = $this->teenagersRepository->verifyOTPAgainstTeenagerId($request->userId, $request->otp);
            if($bool) {
                $teenagerDetail['id'] = $request->userId;
                $teenagerDetail['password'] = bcrypt($request->newPassword);
                $this->teenagersRepository->saveTeenagerDetail($teenagerDetail);
                $response['status'] = 1;
                $response['message'] = trans('Password set successfully!');
            } else {
                $response['message'] = "OTP session is invalid or expired!";
            }
        } else {
            $response['message'] = trans('appmessages.missing_data_msg');
        }
        
        return response()->json($response, 200);
        exit;
    }

    /* Request Params : verifyOTP
    *  userId, otp
    *  No loginToken required because it's call without loggedin user
    */
    public function verifyOTP(Request $request)
    {
        $response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg') ] ;
        if($request->userId != "" && $request->otp != "") {
            $bool = $this->teenagersRepository->verifyOTPAgainstTeenagerId($request->userId, $request->otp);
            if($bool) {
                $response['status'] = 1;
                $response['message'] = trans('OTP verified successfully!');
                $response['data'] = ['userId' => $request->userId ];
            } else {
                $response['message'] = trans('appmessages.invalidOTP');
            }
        } else {
            $response['message'] = trans('appmessages.missing_data_msg');
        }
        
        return response()->json($response, 200);
        exit;
    }

    /* Request Params : forgotPassword
    *  email, 
    *  No loginToken required because it's call without loggedin user
    */
    public function forgotPassword(Request $request)
    {
        $response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg') ] ;
        if($request->email != "") {
            if (!filter_var($request->email, FILTER_VALIDATE_EMAIL)) {
                if (is_numeric($request->email) && $request->email > 0 && $request->email == round($request->email, 0)) {
                    $response['message'] = trans('appmessages.inprocess');
                } else {
                    $response['message'] = trans('appmessages.invalid_email_msg');
                }
            } else {
                $teenagerDetail = $this->teenagersRepository->getTeenagerDetailByEmailId($request->email);
                if ($teenagerDetail && !empty($teenagerDetail)) { 
                    if (($teenagerDetail->t_social_provider == 'Google' || $teenagerDetail->t_social_provider == 'Facebook' ) && $teenagerDetail->password == '') {
                        $response['message'] = 'This email is associate with social account. Please sign in with your social account and set new password.';
                    } else {
                        // generate otp for teenager reset password
                        $OTP = Helpers::generateOtp();
                        $resetRequest = [];
                        $resetRequest['trp_teenager'] = $teenagerDetail->id;
                        $resetRequest['trp_otp'] = $OTP;
                        $resetRequest['trp_uniqueid'] = $teenagerDetail->t_uniqueid;
                        $resetRequest['updated_at'] = date('Y-m-d H:i:s');
                        
                        $this->teenagersRepository->saveTeenagerPasswordResetRequest($resetRequest);
                        $replaceArray = array();
                        $replaceArray['TEEN_NAME'] = $teenagerDetail->t_name;
                        $replaceArray['ONE_TIME_PASSWORD'] = $OTP;

                        $emailTemplateContent = $this->templateRepository->getEmailTemplateDataByName(Config::get('constant.TEENAGER_RESET_EMAIL_TEMPLATE_NAME'));
                        $content = $this->templateRepository->getEmailContent($emailTemplateContent->et_body, $replaceArray);
                        $data = array();
                        $data['subject'] = $emailTemplateContent->et_subject;
                        $data['toEmail'] = $teenagerDetail->t_email;
                        $data['toName'] = $teenagerDetail->t_name;
                        $data['content'] = $content;

                        Mail::send(['html' => 'emails.Template'], $data, function($message) use ($data) {
                                    $message->subject($data['subject']);
                                    $message->to($data['toEmail'], $data['toName']);
                                });

                        $response['status'] = 1;
                        $response['message'] = trans('appmessages.mail_success_msg');
                        $response['data'] = ["userId" => $teenagerDetail->id];
                    }
                } else {
                    $response['message'] = trans('appmessages.usernotexistwithemail');
                }
            }
        } else {
            $response['message'] = trans('appmessages.missing_data_msg');
        }
        
        return response()->json($response, 200);
        exit;
    }

}