<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Http\Requests\SchoolSignupRequest;
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
use App\Sponsors;
use App\Country;
use App\State;
use App\City;
use App\Services\Template\Contracts\TemplatesRepository;
use App\Services\Schools\Contracts\SchoolsRepository;
use App\CMS;

class SignupController extends Controller {

    public function __construct(SchoolsRepository $SchoolsRepository, TemplatesRepository $TemplatesRepository) {

        $this->objSchools = new Schools();
        $this->SchoolsRepository = $SchoolsRepository;
        $this->TemplatesRepository = $TemplatesRepository;
        $this->contactpersonOriginalImageUploadPath = Config::get('constant.CONTACT_PERSON_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->contactpersonThumbImageUploadPath = Config::get('constant.CONTACT_PERSON_THUMB_IMAGE_UPLOAD_PATH');
        $this->contactpersonThumbImageHeight = Config::get('constant.CONTACT_PERSON_THUMB_IMAGE_HEIGHT');
        $this->contactpersonThumbImageWidth = Config::get('constant.CONTACT_PERSON_THUMB_IMAGE_WIDTH');

        $this->schoolOriginalImageUploadPath = Config::get('constant.SCHOOL_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->schoolThumbImageUploadPath = Config::get('constant.SCHOOL_THUMB_IMAGE_UPLOAD_PATH');
        $this->schoolThumbImageHeight = Config::get('constant.SCHOOL_THUMB_IMAGE_HEIGHT');
        $this->schoolThumbImageWidth = Config::get('constant.SCHOOL_THUMB_IMAGE_WIDTH');
        $this->cmsObj = new CMS();
    }

    public function signup() {
        
        $newuser = array();
        if (Auth::school()->check()) {
            return Redirect::to("/school/dashboard");
        }
        $countries = Helpers::getCountries();
        //$cities = Helpers::getCities();
        //echo "<pre>"; print_r($cities); die;
        //$states = Helpers::getStates();
        $schoolDetail = $this->SchoolsRepository->getApprovedSchools();

        $infotext = '';
        $termInfo = $this->cmsObj->getCmsBySlug('term-and-condition');
        if (!empty($termInfo)) {
            $termText = $termInfo->toArray();
            $infotext = $termText['cms_body'];
        }

        $policytext = '';
        $policyInfo = $this->cmsObj->getCmsBySlug('privacy-policy');
        if (!empty($policyInfo)) {
            $privacyText = $policyInfo->toArray();
            $policytext = $privacyText['cms_body'];
        }

        return view('school.Signup', compact('newuser', 'sponsorDetail', 'countries', 'infotext', 'policytext'));
    }

    public function doSignup(SchoolSignupRequest $request) {

        $body = Input::all();        
        $response = [];
        $response['status'] = 0;
        $response['message'] = trans('appmessages.default_error_msg');

        $schoolDetail = [];
        $schoolDetail['sc_name'] = (isset($body['school_name']) && $body['school_name'] != '') ? e($body['school_name']) : '';
        $schoolDetail['sc_address1'] = (isset($body['address1']) && $body['address1'] != '') ? $body['address1'] : '';
        $schoolDetail['sc_address2'] = (isset($body['address2']) && $body['address2'] != '') ? $body['address2'] : '';
        $schoolDetail['sc_pincode'] = (isset($body['pincode']) && $body['pincode'] != '') ? $body['pincode'] : '';
        $schoolDetail['sc_city'] = (isset($body['city']) && $body['city'] != '') ? $body['city'] : '';
        $schoolDetail['sc_state'] = (isset($body['state']) && $body['state'] != '') ? $body['state'] : '';
        $schoolDetail['sc_country'] = (isset($body['country']) && $body['country'] != '') ? $body['country'] : '';
        $schoolDetail['sc_isapproved'] = (isset($body['isapproved']) && $body['isapproved'] != '') ? $body['isapproved'] : '';
        $schoolDetail['sc_logo'] = '';
        $schoolDetail['sc_photo'] = '';
        $schoolDetail['sc_first_name'] = (isset($body['first_name']) && $body['first_name'] != '') ? $body['first_name'] : '';
        $schoolDetail['sc_last_name'] = (isset($body['last_name']) && $body['last_name'] != '') ? $body['last_name'] : '';
        $schoolDetail['sc_title'] = (isset($body['title']) && $body['title'] != '') ? $body['title'] : '';
        $schoolDetail['sc_phone'] = (isset($body['phone']) && $body['phone'] != '') ? $body['phone'] : '';
        $schoolDetail['sc_email'] = (isset($body['email']) && $body['email'] != '') ? $body['email'] : '';
        $schoolDetail['password'] = (isset($body['password']) && $body['password'] != '') ? bcrypt($body['password']) : '';
        $schoolDetail['deleted'] = '1';
        $schoolDetail['sc_uniqueid'] = Helpers::getTeenagerUniqueId();
        
        if ($schoolDetail['sc_email'] != '') {
            $schoolEmailExist = $this->SchoolsRepository->checkActiveEmailExist($schoolDetail['sc_email']);
        }
        if ($schoolDetail['sc_phone'] != '') {
            $schoolMobileExist = $this->SchoolsRepository->checkActivePhoneExist($schoolDetail['sc_phone']);
        }
        if (isset($schoolEmailExist) && $schoolEmailExist) {
            $response['message'] = trans('appmessages.userwithsameemailaddress');
            return Redirect::to("school/signup")->with('error', trans('appmessages.userwithsameemailaddress'));
            exit;
        } else if (isset($schoolMobileExist) && $schoolMobileExist) {
            $response['message'] = trans('appmessages.userwithsamenumber');
            return Redirect::to("school/signup")->with('error', trans('appmessages.userwithsamenumber'));
            exit;
        } else {

            if (($schoolDetail['sc_email'] != '') && $schoolDetail['password'] != '') {
                if (!filter_var($body['email'], FILTER_VALIDATE_EMAIL)) {
                    $response['message'] = trans('appmessages.invalid_email_msg');
                    return Redirect::to("school/signup")->with('error', trans('appmessages.invalid_email_msg'));
                    exit;
                } else {
                    if (Input::file()) {
                        $photo = Input::file('photo');
                        $logo = Input::file('logo');
                        if (!empty($photo)) {
                            $fileName = 'school_' . time() . '.' . $photo->getClientOriginalExtension();
                            $pathOriginal = public_path($this->contactpersonOriginalImageUploadPath . $fileName);
                            $pathThumb = public_path($this->contactpersonThumbImageUploadPath . $fileName);
                            Image::make($photo->getRealPath())->save($pathOriginal);
                            Image::make($photo->getRealPath())->resize($this->contactpersonThumbImageWidth, $this->contactpersonThumbImageHeight)->save($pathThumb);
                            $schoolDetail['sc_photo'] = $fileName;
                        }else{
                            $schoolDetail['sc_photo'] = "proteen_logo.png";
                        }
                             
                            if (!empty($logo)) {
                                $fileName = 'school_' . time() . '.' . $logo->getClientOriginalExtension();
                                $pathOriginal = public_path($this->schoolOriginalImageUploadPath . $fileName);
                                $pathThumb = public_path($this->schoolThumbImageUploadPath . $fileName);
                                Image::make($logo->getRealPath())->save($pathOriginal);
                                Image::make($logo->getRealPath())->resize($this->schoolThumbImageWidth, $this->schoolThumbImageHeight)->save($pathThumb);
                                $schoolDetail['sc_logo'] = $fileName;
                                
                            }
                            else{
                                $schoolDetail['sc_logo'] = "proteen_logo.png";
                            }
                        }
                        $schoolDetailSaved = $this->SchoolsRepository->saveSchoolDetail($schoolDetail);
                        $schoolDetailSaved = $schoolDetailSaved->toArray();


                        /*Send notification mail to admin*/
                        $adminEmail = Helpers::getConfigValueByKey('ADMIN_EMAIL');
                        $adminName = Helpers::getConfigValueByKey('ADMIN_NAME');

                        $replaceArray = [];
                        $replaceArray['ADMIN_NAME'] = $adminName;
                        $replaceArray['TYPE'] = 'School';
                        $replaceArray['USER_NAME'] = $schoolDetail['sc_first_name'] ." ". $schoolDetail['sc_last_name'];
                        $replaceArray['EMAIL'] = $schoolDetail['sc_email'];

                        $emailTemplateContent = $this->TemplatesRepository->getEmailTemplateDataByName(Config::get('constant.VARIFY_USER_REQUEST'));
                        $content = $this->TemplatesRepository->getEmailContent($emailTemplateContent->et_body, $replaceArray);

                        $data = array();
                        $data['subject'] = $emailTemplateContent->et_subject;
                        $data['toEmail'] = $adminEmail;
                        $data['toName'] = $adminName;
                        $data['content'] = $content;
                        Mail::send(['html' => 'emails.Template'], $data, function($message) use ($data) {
                            $message->subject($data['subject']);
                            $message->to($data['toEmail'], $data['toName']);
                        });

                        /* save sponser by teenager id if sponsor id is not blank */
                        $schoolDetailbyId = $this->SchoolsRepository->getSchoolById($schoolDetailSaved['id']);
                        $responseMsg = 'Hi <strong>'.$schoolDetailbyId->sc_first_name.'</strong>,<br/> Once ProTeen Admin verify your account, you will be able to login.';                            
                        return view('school.SignupVerification', compact('responseMsg'));
                    }
                }

            }
            return Redirect::to("school/signup")->with('error', trans('appmessages.missing_data_msg'));
            exit;
        }
    }


