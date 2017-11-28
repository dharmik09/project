<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Http\Requests\SchoolPasswordChangeRequest;
use Auth;
use Image;
use Config;
use Helpers;
use Input;
use Redirect;
use Response;
use Mail;
use App\Transactions;
use App\Schools;
use App\Templates;
use App\Country;
use App\Services\Template\Contracts\TemplatesRepository;
use App\Services\Schools\Contracts\SchoolsRepository;
class PasswordController extends Controller {

    public function __construct(SchoolsRepository $SchoolsRepository,TemplatesRepository $TemplatesRepository
    ) {
        $this->objSchools = new Schools();
        $this->SchoolsRepository = $SchoolsRepository;
        $this->objTemplates = new Templates();
        $this->TemplateRepository = $TemplatesRepository;
        $this->teenOriginalImageUploadPath = Config::get('constant.TEEN_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->teenThumbImageUploadPath = Config::get('constant.TEEN_THUMB_IMAGE_UPLOAD_PATH');
        $this->teenThumbImageHeight = Config::get('constant.TEEN_THUMB_IMAGE_HEIGHT');
        $this->teenThumbImageWidth = Config::get('constant.TEEN_THUMB_IMAGE_WIDTH');
    }

    public function changePassword() {
        if (Auth::school()->check()) {
            return view('school.ChangePassword');
        }
        return view('school.Login');
    }

    public function updatePassword(SchoolPasswordChangeRequest $request) {

        $old_password = e(Input::get('old_password'));
        $new_password = e(Input::get('new_password'));
        $confirm_password = e(Input::get('confirm_password'));

        if (Auth::school()->check()) {
            $user_id = Auth::school()->get()->id;
            $old_password = e(Input::get('old_password'));
            $new_password = e(Input::get('new_password'));
            $confirm_password = e(Input::get('confirm_password'));
            $user = Schools::find($user_id);

            if (Auth::school()->attempt(['sc_email' => $user->sc_email, 'password' => $old_password, 'deleted' => 1])) {
                $user->password = bcrypt(e($new_password));
                $user->save();
                return Redirect::to("school/dashboard")->with('success', 'Password Updated successfully.');
            } else {
                return Redirect::back()
                                ->withErrors('Old Password did not match.');

            }
        }
    }

    public function forgotPassword() {


        return view('school.forgotPassword');
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
                    return Redirect::to('/school/forgotPassword')->with('error', trans('appmessages.inprocess'));
                    exit;
                } else {
                    $response['message'] = trans('appmessages.invalid_email_msg');
                    return Redirect::to('/school/forgotPassword')->with('error', trans('appmessages.invalid_email_msg'));
                    exit;
                }
            } else {
                $data = array();
                $schoolDetail = $this->SchoolsRepository->getSchoolDetailByEmailId($body['email']);
                if (isset($schoolDetail) && !empty($schoolDetail)) {
                    if ($schoolDetail['sc_social_provider'] == 'Google' || $schoolDetail['sc_social_provider'] == 'Facebook') {
                        $response['message'] = 'This email is associate with social account. Please sign in with your social account.';
                        return Redirect::to('/school/forgotPassword')->with('error', trans('This email is associate with social account. Please sign in with your social account.'));
                        exit;
                    } else {
                        // generate otp for school reset password
                        $OTP = Helpers::generateOtp();
                        $resetRequest = [];
                        $resetRequest['trp_school'] = $schoolDetail['id'];
                        $resetRequest['trp_otp'] = $OTP;
                        $this->SchoolsRepository->saveSchoolPasswordResetRequest($resetRequest);

                        $replaceArray = array();
                        $replaceArray['TEEN_NAME'] = $schoolDetail['sc_first_name'];
                        $replaceArray['ONE_TIME_PASSWORD'] = $OTP;
                        $emailTemplateContent = $this->TemplateRepository->getEmailTemplateDataByName(Config::get('constant.SCHOOL_RESET_EMAIL_TEMPLATE_NAME'));                       
                        $content = $this->TemplateRepository->getEmailContent($emailTemplateContent->et_body, $replaceArray);
                        $data = array();
                        $data['subject'] = $emailTemplateContent->et_subject;
                        $data['toEmail'] = $schoolDetail['sc_email'];
                        $data['toName'] = $schoolDetail['sc_first_name'];
                        $data['content'] = $content;

                        Mail::send(['html' => 'emails.Template'], $data, function($message) use ($data) {
                                    $message->subject($data['subject']);
                                    $message->to($data['toEmail'], $data['toName']);
                                });


                        $response['status'] = 1;
                        $response['message'] = trans('appmessages.mail_success_msg');
                        $response['data'] = ["userid" => $schoolDetail['id']];
                        return view('school.forgotPasswordOTP', compact('response'));
                        exit;
                    }
                } else {
                    $response['message'] = trans('appmessages.usernotexistwithemail');
                    return Redirect::to('/school/forgotPassword')->with('error', trans('appmessages.usernotexistwithemail'));
                    exit;
                }
            }
        } else {
            $response['message'] = trans('appmessages.missing_data_msg');
            return Redirect::to('/school/forgotPassword')->with('error', trans('appmessages.missing_data_msg'));
            exit;
        }
        return Redirect::back()
                        ->withErrors('Something went wrong. Please, try again.');
    }

    public function forgotPasswordOTPVerify() {
        $response = [];
        $response['status'] = 0;
        $response['message'] = trans('appmessages.default_error_msg');
        $body = Input::all();
        if(isset($body['userid']) && $body['userid'] > 0 && isset($body['OTP']) && $body['OTP'] != '')
        {
            $bool = $this->SchoolsRepository->verifyOTPAgainstSchoolId($body['userid'], $body['OTP']);
            if($bool)
            {
                $response['status'] = 1;
                $response['message'] = trans('appmessages.default_success_msg');
                $response['data'] = ['userid' => $body['userid']];
                return view('school.setForgotPassword', compact('response'));
                exit;
            }
            else
            {
                $response['message'] = trans('appmessages.invalidOTP');
                return Redirect::to('/school/forgotPasswordOTP')->with('error', trans('appmessages.invalidOTP'));
                exit;
            }
        }
        else
        {
            $response['message'] = trans('appmessages.missing_data_msg');
            return Redirect::to('/school/forgotPassword')->with('error', trans('appmessages.missing_data_msg'));
            exit;
        }
        return Redirect::back()
                        ->withErrors('Something went wrong. Please, try again.');
    }

    public function saveForgotPassword(){
        $response = [];
        $response['status'] = 0;
        $response['message'] = trans('appmessages.default_error_msg');
        $body = Input::all();

        if(isset($body['userid']) && $body['userid'] > 0)
        {
            if(isset($body['newPassword']) && $body['newPassword'] != '')
            {
                $schoolDetail = [];
                $schoolDetail['id'] = $body['userid'];
                $schoolDetail['password'] = bcrypt($body['newPassword']);
                $this->SchoolsRepository->saveSchoolDetail($schoolDetail);

                $response['status'] = 1;
                $response['message'] = trans('appmessages.default_success_msg');
                $response['data'] = ['userid' => $body['userid']];
                return view('school.saveForgotPassword', compact('response'));
                exit;
            }
            else
            {
                $response['message'] = trans('appmessages.missing_data_msg');
                return Redirect::to('/school/setForgotPassword')->with('error', trans('appmessages.missing_data_msg'));
                exit;
            }
        }
        else
        {
            $response['message'] = trans('appmessages.missing_data_msg');
            return Redirect::to('/school/forgotPassword')->with('error', trans('appmessages.missing_data_msg'));
                exit;
        }
    }
}
