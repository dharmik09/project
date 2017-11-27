<?php

namespace App\Http\Controllers\Teenager;

use App\Http\Controllers\Controller;
use App\Http\Requests\TeenagerPasswordChangeRequest;
use Auth;
use Image;
use Config;
use Helpers;
use Input;
use Redirect;
use Response;
use Mail;
use App\Teenagers;
use App\Templates;
use App\Sponsors;
use App\Country;
use App\Services\Template\Contracts\TemplatesRepository;
use App\Services\Teenagers\Contracts\TeenagersRepository;
use App\Services\Sponsors\Contracts\SponsorsRepository;

class PasswordController extends Controller {

    public function __construct(TeenagersRepository $teenagersRepository, SponsorsRepository $sponsorsRepository, TemplatesRepository $templatesRepository
    ) {
        $this->objTeenagers = new Teenagers();
        $this->teenagersRepository = $teenagersRepository;
        $this->objSponsors = new Sponsors();
        $this->templateRepository = $templatesRepository;
        $this->sponsorsRepository = $sponsorsRepository;
        $this->objTemplates = new Templates();
        $this->teenOriginalImageUploadPath = Config::get('constant.TEEN_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->teenThumbImageUploadPath = Config::get('constant.TEEN_THUMB_IMAGE_UPLOAD_PATH');
        $this->teenThumbImageHeight = Config::get('constant.TEEN_THUMB_IMAGE_HEIGHT');
        $this->teenThumbImageWidth = Config::get('constant.TEEN_THUMB_IMAGE_WIDTH');
    }

    public function changePassword() {
        if (Auth::teenager()->check()) {
            $user_id = Auth::teenager()->get()->id;
            $user = Teenagers::find($user_id);
            $currentPassword = $user->password;
            return view('teenager.ChangePassword', compact('currentPassword'));
        }
        Auth::teenager()->logout();
        return Redirect::to('/teenager')->with('error', trans('appmessages.missing_data_msg'));
        exit;
    }

    public function updatePassword(TeenagerPasswordChangeRequest $request) {
        if (Auth::teenager()->check()) {
            $user_id = Auth::teenager()->get()->id;
            $old_password = e(Input::get('old_password'));
            $new_password = e(Input::get('new_password'));
            $confirm_password = e(Input::get('confirm_password'));
            $user = Teenagers::find($user_id);
            $currentPassword = $user->password;

            if ($new_password != $confirm_password) {
                return Redirect::back()
                                ->withErrors('New password and confirm password did not match.');
            }
            if ($new_password == '' || $confirm_password == '') {
                return Redirect::back()
                                ->withErrors('Password not to be null.');
            }

            if ($currentPassword != '') {
                if (Auth::teenager()->attempt(['t_email' => $user->t_email, 'password' => $old_password, 'deleted' => 1])) {
                    $user->password = bcrypt(e($new_password));
                    $user->save();
                    return Redirect::to("/teenager/updateprofile")->with('success', 'Your password has been changed successfully');
                } else {
                    return Redirect::back()
                                    ->withErrors('Old Password did not match.');
                }
            } else {
                $user->password = bcrypt(e($new_password));
                $user->save();
                return Redirect::to("/teenager/updateprofile")->with('success', 'Your password has been changed successfully');
            }
        } else {
            Auth::teenager()->logout();
            return Redirect::to('/teenager')->with('error', trans('appmessages.missing_data_msg'));
            exit;
        }
    }

    public function forgotPassword() {
        if (Auth::guard('teenager')->check()) {
            return Redirect::to("/teenager/home");
        } else {
            return view('teenager.forgotPassword');
        }
    }

    public function forgotPasswordOTP() {
        $body = Input::all();
        $response = [];
        $response['status'] = 0;
        $response['message'] = trans('appmessages.default_error_msg');

        if (isset($body['email']) && $body['email'] != '') {
            if (!filter_var($body['email'], FILTER_VALIDATE_EMAIL)) {
                if (is_numeric($body['email']) && $body['email'] > 0 && $body['email'] == round($body['email'], 0)) {
                    $response['message'] = trans('appmessages.inprocess');
                    return Redirect::to('/teenager/forgot-password')->with('error', trans('appmessages.inprocess'));
                    exit;
                } else {
                    $response['message'] = trans('appmessages.invalid_email_msg');
                    return Redirect::to('/teenager/forgot-password')->with('error', trans('appmessages.invalid_email_msg'));
                    exit;
                }
            } else {
                $data = array();
                $teenagerDetail = $this->teenagersRepository->getTeenagerDetailByEmailId($body['email']);
                if ($teenagerDetail) {
                    if($teenagerDetail->t_isverified != 1){
                        return Redirect::to('/teenager/forgot-password')->with('error', trans('Your account is not verified.'));
                        exit;
                    }
                    if ( ($teenagerDetail->t_social_provider == 'Google' || $teenagerDetail->t_social_provider == 'Facebook' ) && $teenagerDetail->password == '') {
                        $response['message'] = 'This email is associate with social account. Please sign in with your social account and set new password.';
                        return Redirect::to('/teenager/forgot-password')->with('error', trans('This email is associate with social account. Please sign in with your social account and set new password.'));
                        exit;
                    } else {
                        // generate otp for teenager reset password
                        $OTP = Helpers::generateOtp();
                        $resetRequest = [];
                        $resetRequest['trp_teenager'] = $teenagerDetail->id;
                        $resetRequest['trp_otp'] = $OTP;
                        $resetRequest['trp_uniqueid'] = $teenagerDetail->t_uniqueid;
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
                        $response['data'] = ["userid" => $teenagerDetail->id, "email" => $teenagerDetail->t_email, "OTP" => $OTP, 'u_token' => $teenagerDetail->t_uniqueid];
                        return view('teenager.forgotPasswordOTP', compact('response'));
                        exit;
                    }
                } else {
                    $response['message'] = trans('appmessages.usernotexistwithemail');
                    return Redirect::to('/teenager/forgot-password')->with('error', trans('appmessages.usernotexistwithemail'));
                    exit;
                }
            }
        } else {
            $response['message'] = trans('appmessages.missing_data_msg');
            return Redirect::to('/teenager/forgot-password')->with('error', trans('appmessages.missing_data_msg'));
            exit;
        }
        return Redirect::back()
                        ->withErrors('Something went wrong. Please, try again.');
    }

    public function resendOTP() {
        $body = Input::all();
        if ($body['email'] != '' && filter_var($body['email'], FILTER_VALIDATE_EMAIL)) {
            $teenagerDetail = $this->teenagersRepository->getTeenagerDetailByEmailId($body['email']);
            $OTP = Helpers::generateOtp();
            $resetRequest = [];
            $resetRequest['trp_teenager'] = $teenagerDetail->id;
            $resetRequest['trp_otp'] = $OTP;
            $resetRequest['trp_uniqueid'] = $teenagerDetail->t_uniqueid;
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
            echo "OTP sent successfully!";
            exit;
        }
        echo "Something went wrong!";
        exit;
    }

    public function forgotPasswordOTPVerify() {
        $response = [];
        $response['status'] = 0;
        $response['message'] = trans('appmessages.default_error_msg');
        $body = Input::all();

        if (isset($body['userid']) && $body['userid'] > 0 && isset($body['OTP']) && $body['OTP'] != '') {
            $bool = $this->teenagersRepository->verifyOTPAgainstTeenagerId($body['userid'], $body['OTP']);

            if ($bool) {
                $response['status'] = 1;
                $response['message'] = trans('appmessages.default_success_msg');
                $response['data'] = ['userid' => $body['userid'], 'otp' => $body['OTP']];
                return view('teenager.setForgotPassword', compact('response'));
                exit;
            } else {
                $response['message'] = trans('appmessages.invalidOTP');
                return Redirect::to('/teenager/forgot-password-OTP')->with('error', trans('appmessages.invalidOTP'));
                exit;
            }
        } else {
            $response['message'] = trans('appmessages.missing_data_msg');
            return Redirect::to('/teenager/forgot-password')->with('error', trans('appmessages.missing_data_msg'));
            exit;
        }
        return Redirect::back()
                        ->withErrors('Something went wrong. Please, try again.');
    }

    public function saveForgotPassword() {
        $response = [];
        $response['status'] = 0;
        $response['message'] = trans('appmessages.default_error_msg');
        $body = Input::all();

        if (isset($body['userid']) && $body['userid'] > 0 && isset($body['OTP']) && $body['OTP'] != '') {
            $bool = $this->teenagersRepository->isUserPasswordOTPMatch($body['userid'], $body['OTP']);
            if (isset($body['newPassword']) && $body['newPassword'] != '' && $bool) {
                $teenagerDetail = [];
                $teenagerDetail['id'] = $body['userid'];
                $teenagerDetail['password'] = bcrypt($body['newPassword']);
                $this->teenagersRepository->saveTeenagerDetail($teenagerDetail);

                $response['status'] = 1;
                $response['message'] = trans('appmessages.default_success_msg');
                $response['data'] = ['userid' => $body['userid']];
                return view('teenager.saveForgotPassword', compact('response'));
                exit;
            } else {
                $response['message'] = trans('appmessages.missing_data_msg');
                return Redirect::to('/teenager/set-forgot-password')->with('error', trans('appmessages.missing_data_msg'));
                exit;
            }
        } else {
            $response['message'] = trans('appmessages.missing_data_msg');
            return Redirect::to('/teenager/forgot-password')->with('error', trans('appmessages.missing_data_msg'));
            exit;
        }
    }

}
