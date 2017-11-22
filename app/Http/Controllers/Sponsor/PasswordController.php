<?php

namespace App\Http\Controllers\Sponsor;

use App\Http\Controllers\Controller;
use App\Http\Requests\SponsorPasswordChangeRequest;
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
        $this->objTemplates = new Templates();
        $this->templateRepository = $templatesRepository;
        $this->sponsorsRepository = $sponsorsRepository;

        $this->teenOriginalImageUploadPath = Config::get('constant.TEEN_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->teenThumbImageUploadPath = Config::get('constant.TEEN_THUMB_IMAGE_UPLOAD_PATH');
        $this->teenThumbImageHeight = Config::get('constant.TEEN_THUMB_IMAGE_HEIGHT');
        $this->teenThumbImageWidth = Config::get('constant.TEEN_THUMB_IMAGE_WIDTH');
    }


    public function changePassword() {
        if (Auth::guard('sponsor')->check()) {
            return view('sponsor.changePassword');
        }
        return view('sponsor.login');
    }

    public function updatePassword(SponsorPasswordChangeRequest $request) {

        $old_password = e(Input::get('old_password'));
        $new_password = e(Input::get('new_password'));
        $confirm_password = e(Input::get('confirm_password'));

        if (Auth::guard('sponsor')->check()) {
            $user_id = Auth::guard('sponsor')->user()->id;
            $old_password = e(Input::get('old_password'));
            $new_password = e(Input::get('new_password'));
            $confirm_password = e(Input::get('confirm_password'));
            $user = Sponsors::find($user_id);
            //die ($old_password);

            if (Auth::guard('sponsor')->attempt(['sp_email' => $user->sp_email, 'password' => $old_password, 'deleted' => 1])) {
                $user->password = bcrypt(e($new_password));
                $user->save();
                return Redirect::to("sponsor/home")->with('success', 'Password Updated successfully.');
            } else {
                return Redirect::back()
                                ->withErrors('Old Password did not match.');

            }
        }
    }


    public function forgotPassword() {

        return view('sponsor.forgotPassword');
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
                    return Redirect::to('/sponsor/forgot-password')->with('error', trans('appmessages.inprocess'));
                    exit;
                } else {
                    $response['message'] = trans('appmessages.invalid_email_msg');
                    return Redirect::to('/sponsor/forgot-password')->with('error', trans('appmessages.invalid_email_msg'));
                    exit;
                }
            } else {
                $data = array();
                $sponsorDetail = $this->sponsorsRepository->getSponsorDetailByEmailId($body['email']);
                if (isset($sponsorDetail) && !empty($sponsorDetail)) {
                    if ($sponsorDetail['p_social_provider'] == 'Google' || $sponsorDetail['p_social_provider'] == 'Facebook') {
                        $response['message'] = 'This email is associate with social account. Please sign in with your social account.';
                        return Redirect::to('/sponsor/forgot-password')->with('error', trans('This email is associate with social account. Please sign in with your social account.'));
                        exit;
                    } else {
                        // generate otp for teenager reset password
                        $OTP = Helpers::generateOtp();
                        $resetRequest = [];
                        $resetRequest['trp_sponsor'] = $sponsorDetail['id'];
                        $resetRequest['trp_otp'] = $OTP;
                        $this->sponsorsRepository->saveSponsorPasswordResetRequest($resetRequest);

                        $replaceArray = array();
                        $replaceArray['TEEN_NAME'] = $sponsorDetail['sp_first_name'];
                        $replaceArray['ONE_TIME_PASSWORD'] = $OTP;

                        $emailTemplateContent = $this->templateRepository->getEmailTemplateDataByName(Config::get('constant.SPONSOR_RESET_EMAIL_TEMPLATE_NAME'));
                        //die($emailTemplateContent);
                        $content = $this->templateRepository->getEmailContent($emailTemplateContent->et_body, $replaceArray);
                        $data = array();
                        $data['subject'] = $emailTemplateContent->et_subject;
                        $data['toEmail'] = $sponsorDetail['sp_email'];
                        $data['toName'] = $sponsorDetail['sp_first_name'];
                        $data['content'] = $content;

                        Mail::send(['html' => 'emails.Template'], $data, function($message) use ($data) {
                                    $message->subject($data['subject']);
                                    $message->to($data['toEmail'], $data['toName']);
                                });


                        $response['status'] = 1;
                        $response['message'] = trans('appmessages.mail_success_msg');
                        $response['data'] = ["userid" => $sponsorDetail['id']];
                        return view('sponsor.forgotPasswordOTP', compact('response'));
                        exit;
                    }
                } else {
                    $response['message'] = trans('appmessages.usernotexistwithemail');
                    return Redirect::to('/sponsor/forgot-password')->with('error', trans('appmessages.usernotexistwithemail'));
                    exit;
                }
            }
        } else {
            $response['message'] = trans('appmessages.missing_data_msg');
            return Redirect::to('/sponsor/forgot-password')->with('error', trans('appmessages.missing_data_msg'));
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
            $bool = $this->sponsorsRepository->verifyOTPAgainstSponsorId($body['userid'], $body['OTP']);
            if($bool)
            {
                $response['status'] = 1;
                $response['message'] = trans('appmessages.default_success_msg');
                $response['data'] = ['userid' => $body['userid']];
                return view('sponsor.setForgotPassword', compact('response'));
                exit;
            }
            else
            {
                $response['message'] = trans('appmessages.invalidOTP');
                return Redirect::to('/sponsor/forgot-password-OTP')->with('error', trans('appmessages.invalidOTP'));
                exit;
            }
        }
        else
        {
            $response['message'] = trans('appmessages.missing_data_msg');
            return Redirect::to('/sponsor/forgot-password')->with('error', trans('appmessages.missing_data_msg'));
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
                $sponsorDetail = [];
                $sponsorDetail['id'] = $body['userid'];
                $sponsorDetail['password'] = bcrypt($body['newPassword']);
                $this->sponsorsRepository->saveSponsorDetail($sponsorDetail);

                $response['status'] = 1;
                $response['message'] = trans('appmessages.default_success_msg');
                $response['data'] = ['userid' => $body['userid']];
                return view('sponsor.saveForgotPassword', compact('response'));
                exit;
            }
            else
            {
                $response['message'] = trans('appmessages.missing_data_msg');
                return Redirect::to('/sponsor/set-forgot-password')->with('error', trans('appmessages.missing_data_msg'));
                exit;
            }
        }
        else
        {
            $response['message'] = trans('appmessages.missing_data_msg');
            return Redirect::to('/sponsor/forgot-password')->with('error', trans('appmessages.missing_data_msg'));
                exit;
        }
    }
}
