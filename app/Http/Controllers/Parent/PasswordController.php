<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Http\Requests\ParentPasswordChangeRequest;
use Auth;
use Image;
use Config;
use Helpers;
use Input;
use Redirect;
use Response;
use Mail;
use App\Transactions;
use App\Parents;
use App\Templates;
use App\Sponsors;
use App\Country;
use App\Services\Template\Contracts\TemplatesRepository;
use App\Services\Parents\Contracts\ParentsRepository;
use App\Services\Sponsors\Contracts\SponsorsRepository;

class PasswordController extends Controller {

    public function __construct(ParentsRepository $parentsRepository, SponsorsRepository $sponsorsRepository, TemplatesRepository $templatesRepository
    ) {
        $this->objParents = new Parents();
        $this->parentsRepository = $parentsRepository;
        $this->objSponsors = new Sponsors();
        $this->templateRepository = $templatesRepository;
        $this->sponsorsRepository = $sponsorsRepository;
        $this->objTemplates = new Templates();
        $this->teenOriginalImageUploadPath = Config::get('constant.TEEN_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->teenThumbImageUploadPath = Config::get('constant.TEEN_THUMB_IMAGE_UPLOAD_PATH');
        $this->teenThumbImageHeight = Config::get('constant.TEEN_THUMB_IMAGE_HEIGHT');
        $this->teenThumbImageWidth = Config::get('constant.TEEN_THUMB_IMAGE_WIDTH');
        $this->loggedInUser = Auth::guard('parent');
    }


    public function changePassword() {
        return view('parent.changePassword');
    }

    public function updatePassword(ParentPasswordChangeRequest $request) {

        $old_password = e(Input::get('old_password'));
        $new_password = e(Input::get('new_password'));
        $confirm_password = e(Input::get('confirm_password'));

        if ($this->loggedInUser->check()) {
            $user_id = $this->loggedInUser->user()->id;
            $old_password = e(Input::get('old_password'));
            $new_password = e(Input::get('new_password'));
            $confirm_password = e(Input::get('confirm_password'));
            $user = Parents::find($user_id);

            if (Auth::guard('parent')->attempt(['p_email' => $user->p_email, 'password' => $old_password, 'deleted' => 1])) {
                $user->password = bcrypt(e($new_password));
                $user->save();
                return Redirect::to("parent/home")->with('success', 'Password Updated successfully.');
            } else {
                return Redirect::back()
                                ->withErrors('Old Password did not match.');

            }
        }
    }

    public function forgotPassword() {

        return view('parent.forgotPassword');
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
                    return Redirect::to('/parent/forgot-password')->with('error', trans('appmessages.inprocess'));
                    exit;
                } else {
                    $response['message'] = trans('appmessages.invalid_email_msg');
                    return Redirect::to('/parent/forgot-password')->with('error', trans('appmessages.invalid_email_msg'));
                    exit;
                }
            } else {
                $data = array();
                $parentDetail = $this->parentsRepository->getParentDetailByEmailId($body['email']);
                if (isset($parentDetail) && !empty($parentDetail)) {
                    if ($parentDetail['p_social_provider'] == 'Google' || $parentDetail['p_social_provider'] == 'Facebook') {
                        $response['message'] = 'This email is associate with social account. Please sign in with your social account.';
                        return Redirect::to('/parent/forgot-password')->with('error', trans('This email is associate with social account. Please sign in with your social account.'));
                        exit;
                    } else {
                        // generate otp for teenager reset password
                        $OTP = Helpers::generateOtp();
                        $resetRequest = [];
                        $resetRequest['trp_parent'] = $parentDetail['id'];
                        $resetRequest['trp_otp'] = $OTP;
                        $this->parentsRepository->saveParentPasswordResetRequest($resetRequest);

                        $replaceArray = array();
                        $replaceArray['TEEN_NAME'] = $parentDetail['p_first_name'];
                        $replaceArray['ONE_TIME_PASSWORD'] = $OTP;

                        $emailTemplateContent = $this->templateRepository->getEmailTemplateDataByName(Config::get('constant.PARENT_RESET_EMAIL_TEMPLATE_NAME'));
                        $content = $this->templateRepository->getEmailContent($emailTemplateContent->et_body, $replaceArray);
                        $data = array();
                        $data['subject'] = $emailTemplateContent->et_subject;
                        $data['toEmail'] = $parentDetail['p_email'];
                        $data['toName'] = $parentDetail['p_name'];
                        $data['content'] = $content;

                        Mail::send(['html' => 'emails.Template'], $data, function($message) use ($data) {
                                    $message->subject($data['subject']);
                                    $message->to($data['toEmail'], $data['toName']);
                                });
                        $response['status'] = 1;
                        $response['message'] = trans('appmessages.mail_success_msg');
                        $response['data'] = ["userid" => $parentDetail['id']];
                        return view('parent.forgotPasswordOTP', compact('response'));
                        exit;
                    }
                } else {
                    $response['message'] = trans('appmessages.usernotexistwithemail');
                    return Redirect::to('/parent/forgot-password')->with('error', trans('appmessages.usernotexistwithemail'));
                    exit;
                }
            }
        } else {
            $response['message'] = trans('appmessages.missing_data_msg');
            return Redirect::to('/parent/forgot-password')->with('error', trans('appmessages.missing_data_msg'));
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
            $bool = $this->parentsRepository->verifyOTPAgainstParentId($body['userid'], $body['OTP']);

            if($bool)
            {
                $response['status'] = 1;
                $response['message'] = trans('appmessages.default_success_msg');
                $response['data'] = ['userid' => $body['userid']];
                return view('parent.setForgotPassword', compact('response'));
                exit;
            }
            else
            {
                $response['message'] = trans('appmessages.invalidOTP');
                return Redirect::to('/parent/forgot-password-OTP')->with('error', trans('appmessages.invalidOTP'));
                exit;
            }
        }
        else
        {
            $response['message'] = trans('appmessages.missing_data_msg');
            return Redirect::to('/parent/forgot-password')->with('error', trans('appmessages.missing_data_msg'));
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
                $parentDetail = [];
                $parentDetail['id'] = $body['userid'];
                $parentDetail['password'] = bcrypt($body['newPassword']);
                $this->parentsRepository->saveParentDetail($parentDetail);

                $response['status'] = 1;
                $response['message'] = trans('appmessages.default_success_msg');
                $response['data'] = ['userid' => $body['userid']];
                return view('parent.saveForgotPassword', compact('response'));
                exit;
            }
            else
            {
                $response['message'] = trans('appmessages.missing_data_msg');
                return Redirect::to('/parent/set-forgot-password')->with('error', trans('appmessages.missing_data_msg'));
                exit;
            }
        }
        else
        {
            $response['message'] = trans('appmessages.missing_data_msg');
            return Redirect::to('/parent/forgot-password')->with('error', trans('appmessages.missing_data_msg'));
                exit;
        }
    }
}
