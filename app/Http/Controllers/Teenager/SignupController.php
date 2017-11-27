<?php

namespace App\Http\Controllers\Teenager;

use App\Http\Controllers\Controller;
use App\Http\Requests\TeenagerSignupRequest;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Image;
use Config;
use Helpers;
use Carbon\Carbon;
use Input;
use App\Country;
use Mail;
use App\Services\Teenagers\Contracts\TeenagersRepository;
use App\Services\Sponsors\Contracts\SponsorsRepository;
use App\Services\FileStorage\Contracts\FileStorageRepository;
use App\Services\Template\Contracts\TemplatesRepository;
use Redirect;
use Response;

class SignupController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(TemplatesRepository $templatesRepository, FileStorageRepository $fileStorageRepository, TeenagersRepository $teenagersRepository, SponsorsRepository $sponsorsRepository)
    {
        $this->teenagersRepository = $teenagersRepository;
        $this->sponsorsRepository = $sponsorsRepository;
        $this->fileStorageRepository = $fileStorageRepository;
        $this->templateRepository = $templatesRepository;
        $this->middleware('teenager.guest', ['except' => 'logout']);
        $this->teenOriginalImageUploadPath = Config::get('constant.TEEN_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->teenThumbImageUploadPath = Config::get('constant.TEEN_THUMB_IMAGE_UPLOAD_PATH');
        $this->teenThumbImageHeight = Config::get('constant.TEEN_THUMB_IMAGE_HEIGHT');
        $this->teenThumbImageWidth = Config::get('constant.TEEN_THUMB_IMAGE_WIDTH');
        $this->objCountry = new Country();
    }
    /**
     * Show the application's signup form.
     *
     * @return \Illuminate\Http\Response
     */
    public function signup()
    {
        $countries = $this->objCountry->getAllCounries();
        $sponsorDetail = $this->sponsorsRepository->getApprovedSponsors();
        return view('teenager.signup', compact('sponsorDetail', 'countries'));
    }

    public function doSignup(TeenagerSignupRequest $request) {
        dd(env('MAIL_HOST'));
        $body = $request->all();
        $response = [];
        $response['status'] = 0;
        $response['message'] = trans('appmessages.default_error_msg');

        $teenagerDetail = [];
        $teenagerDetail['t_uniqueid'] = Helpers::getTeenagerUniqueId();
        $teenagerDetail['t_name'] = (isset($body['name']) && $body['name'] != '') ? e($body['name']) : '';
        $teenagerDetail['t_nickname'] = (isset($body['nickname']) && $body['nickname'] != '') ? e($body['nickname']) : '';
        
        $stringVariable = $body['year']."-".$body['month']."-".$body['day'];
        $birthDate = Carbon::createFromFormat("Y-m-d", $stringVariable);
        $todayDate = Carbon::now();
        if (Helpers::validateDate($stringVariable, "Y-m-d") && $todayDate->gt($birthDate) ) {
            $teenagerDetail['t_birthdate'] = $stringVariable;
        } else {
            return Redirect::to("teenager/signup")->withErrors("Date is invalid")->withInput();
            exit;
        }
        
        $teenagerDetail['t_gender'] = (isset($body['gender']) && $body['gender'] != '') ? $body['gender'] : '';
        $teenagerDetail['t_email'] = (isset($body['email']) && isset($body['email_confirmation']) && $body['email'] != '' && $body['email'] === $body['email_confirmation']) ? $body['email'] : '';
        $teenagerDetail['password'] = (isset($body['password']) && isset($body['password_confirmation']) && $body['password'] != '' && $body['password_confirmation'] === $body['password']) ? bcrypt($body['password']) : '';
        $teenagerDetail['t_phone'] = (isset($body['mobile']) && $body['mobile'] != '') ? $body['mobile'] : '';
        //Added new phone name field
        //$teenagerDetail['t_phone_new'] = (isset($body['phone']) && $body['phone'] != '') ? $body['phone'] : '';

        $teenagerDetail['t_country'] = (isset($body['country']) && $body['country'] != '') ? $body['country'] : '';
        $teenagerDetail['t_social_provider'] = (isset($body['social_provider']) && $body['social_provider'] != '') ? $body['social_provider'] : '';
        $teenagerDetail['t_social_identifier'] = (isset($body['social_id']) && $body['social_id'] != '') ? $body['social_id'] : '';
        $teenagerDetail['t_social_accesstoken'] = (isset($body['social_accesstoken']) && $body['social_accesstoken'] != '') ? $body['social_accesstoken'] : '';
        $teenagerDetail['t_sponsor_choice'] = 2;
        $teenagerDetail['t_pincode'] = (isset($body['pincode']) && $body['pincode'] != '') ? $body['pincode'] : '';
        //$teenagerDetail['fromLogin'] = (isset($body['fromLogin']) && $body['fromLogin'] != '') ? $body['fromLogin'] : '';
        $teenagerDetail['t_photo'] = '';
        $teenagerDetail['deleted'] = '1';

        //Check all default field value -> If those are entered dummy by users
        if ($teenagerDetail['t_name'] == '' || $teenagerDetail['t_country'] == '' || $teenagerDetail['t_pincode'] == '' || $teenagerDetail['password'] == '' || $teenagerDetail['t_email'] == '' || $teenagerDetail['t_gender'] == '' || $teenagerDetail['t_birthdate'] == '') {
            return Redirect::to("teenager/signup")->withErrors(trans('validation.someproblems'))->withInput();
            exit;
        }
        if (!isset($body['selected_sponsor']) || count($body['selected_sponsor']) < 1) {
            return Redirect::to("teenager/signup")->withErrors("Please select atleast one sponsor choice")->withInput();
            exit;
        }
        
        if (!in_array($teenagerDetail['t_gender'], array("1", "2"))) {
            return Redirect::to("teenager/signup")->withErrors(trans('validation.someproblems'))->withInput();
            exit;
        }

        if ($teenagerDetail['t_email'] != '' && $teenagerDetail['t_social_provider'] == 'Normal') {
            $teenagerEmailExist = $this->teenagersRepository->checkActiveEmailExist($teenagerDetail['t_email']);
        }
        if ($teenagerDetail['t_phone'] != '' && $teenagerDetail['t_social_provider'] == 'Normal') {
            $teenagerMobileExist = $this->teenagersRepository->checkActivePhoneExist($teenagerDetail['t_phone']);
        }
        if ($teenagerEmailExist) {
            $response['message'] = trans('appmessages.userwithsameemailaddress');
            return Redirect::to("teenager/signup")->withErrors(trans('appmessages.userwithsameemailaddress'))->withInput();
            exit;
        } else if ($teenagerMobileExist) {
            $response['message'] = trans('appmessages.userwithsamenumber');
            return Redirect::to("teenager/signup")->withErrors(trans('appmessages.userwithsamenumber'))->withInput();
            exit;
        } else {
            if (isset($teenagerDetail['t_social_provider']) && $teenagerDetail['t_social_provider'] != '' && $teenagerDetail['t_social_provider'] == 'Normal') {
                if (($teenagerDetail['t_email'] != '') && $teenagerDetail['password'] != '' && $teenagerDetail['t_name'] != '') {
                    if (!filter_var($body['email'], FILTER_VALIDATE_EMAIL)) {
                        $response['message'] = trans('appmessages.invalid_email_msg');
                        return Redirect::to("teenager/signup")->with('error', trans('appmessages.invalid_email_msg'));
                        exit;
                    } else {
                        if (Input::file()) {
                            $file = Input::file('photo');
                            if (!empty($file)) {
                                $fileName = 'teenager_' . time() . '.' . $file->getClientOriginalExtension();
                                $pathOriginal = public_path($this->teenOriginalImageUploadPath . $fileName);
                                $pathThumb = public_path($this->teenThumbImageUploadPath . $fileName);
                                Image::make($file->getRealPath())->save($pathOriginal);
                                Image::make($file->getRealPath())->resize($this->teenThumbImageWidth, $this->teenThumbImageHeight)->save($pathThumb);
                                
                                //Uploading on AWS
                                $originalImage = $this->fileStorageRepository->addFileToStorage($fileName, $this->teenOriginalImageUploadPath, $pathOriginal, "s3");
                                $thumbImage = $this->fileStorageRepository->addFileToStorage($fileName, $this->teenThumbImageUploadPath, $pathThumb, "s3");
                                //Deleting Local Files
                                \File::delete($this->teenOriginalImageUploadPath . $fileName);
                                \File::delete($this->teenThumbImageUploadPath . $fileName);
                                $teenagerDetail['t_photo'] = $fileName;
                            }
                        }
                        //echo "<pre/>"; print_r($teenagerDetail); die();
                        $teenagerDetailSaved = $this->teenagersRepository->saveTeenagerDetail($teenagerDetail);
                        $teenagerDetailSaved = $teenagerDetailSaved->toArray();

                        /* save sponser by teenager id if sponsor id is not blank */
                        if (isset($body['selected_sponsor']) && !empty($body['selected_sponsor'])) {
                            $sponserDetail = $this->teenagersRepository->saveTeenagerSponserId($teenagerDetailSaved['id'], implode(',', $body['selected_sponsor']));
                        }

                        $teenagerDetailbyId = $this->teenagersRepository->getTeenagerById($teenagerDetailSaved['id']);
                        // --------------------start sending mail -----------------------------//
                        $replaceArray = array();
                        $replaceArray['TEEN_NAME'] = $teenagerDetailbyId->t_name;

                        // if($teenagerDetailbyId->t_sponsor_choice == 1)
                        // {
                        //     //If user has selected Payment option
                        //     $emailTemplateContent = $this->templateRepository->getEmailTemplateDataByName(Config::get('constant.PAYMENT_REVIEW_TEMPLATE'));
                        //     $content = $this->templateRepository->getEmailContent($emailTemplateContent->et_body, $replaceArray);
                        //     $data = array();
                        //     $data['subject'] = $emailTemplateContent->et_subject;
                        //     $data['toEmail'] = $teenagerDetailbyId->t_email;
                        //     $data['toName'] = $teenagerDetailbyId->t_name;
                        //     $data['content'] = $content;
                        //     $data['teen_id'] = $teenagerDetailbyId->id;
                        //     Mail::send(['html' => 'emails.Template'], $data, function($message) use ($data) {
                        //         $message->subject($data['subject']);
                        //         $message->to($data['toEmail'], $data['toName']);
                        //         $teenagerPaymentDetail = [];
                        //         $userid = $data['teen_id'];
                        //         $teenagerPaymentDetail['t_payment_status'] = 1;
                        //         $this->teenagersRepository->updatePaymentStatus($userid,$teenagerPaymentDetail);
                        //     });
                        //     $replaceArray['TEEN_EMAIL'] = $teenagerDetailbyId->t_email;
                        //     //Send notification mail to admin
                        //     $emailTemplateContent = $this->templateRepository->getEmailTemplateDataByName(Config::get('constant.ADMIN_PAYMENT_NOTIFICATION_TEMPLATE'));
                        //     $content = $this->templateRepository->getEmailContent($emailTemplateContent->et_body, $replaceArray);
                        //     $data = array();
                        //     $data['subject'] = $emailTemplateContent->et_subject;
                        //     $data['toEmail'] = Helpers::getConfigValueByKey('ADMIN_EMAIL');
                        //     $data['toName'] = Helpers::getConfigValueByKey('ADMIN_NAME');
                        //     $data['content'] = $content;
                        //     $data['teen_id'] = $teenagerDetailbyId->id;
                        //     Mail::send(['html' => 'emails.Template'], $data, function($message) use ($data) {
                        //         $message->subject($data['subject']);
                        //         $message->to($data['toEmail'], $data['toName']);
                        //     });
                        // }
                        // else
                        // {
                            //If user has selected My choice or none option
                            $replaceArray['TEEN_UNIQUEID'] = Helpers::getTeenagerUniqueId();
                            $replaceArray['TEEN_URL'] = "<a href=" . url("verifyTeen?token=" . $replaceArray['TEEN_UNIQUEID']) . ">" . url("verifyTeen?token=" . $replaceArray['TEEN_UNIQUEID']) . "</a>";

                            $emailTemplateContent = $this->templateRepository->getEmailTemplateDataByName(Config::get('constant.TEENAGER_VAIRIFIED_EMAIL_TEMPLATE_NAME'));
                            $content = $this->templateRepository->getEmailContent($emailTemplateContent->et_body, $replaceArray);
                            $data = array();
                            $data['subject'] = $emailTemplateContent->et_subject;
                            $data['toEmail'] = $teenagerDetailbyId->t_email;
                            $data['toName'] = $teenagerDetailbyId->t_name;
                            $data['content'] = $content;
                            $data['teen_token'] = $replaceArray['TEEN_UNIQUEID'];
                            $data['teen_url'] = $replaceArray['TEEN_URL'];
                            $data['teen_id'] = $teenagerDetailbyId->id;
                            Mail::send(['html' => 'emails.Template'], $data, function($message) use ($data) {
                                $message->subject($data['subject']);
                                $message->to($data['toEmail'], $data['toName']);
                                $teenagerTokenDetail = [];
                                $teenagerTokenDetail['tev_token'] = $data['teen_token'];
                                $teenagerTokenDetail['tev_teenager'] = $data['teen_id'];
                                $this->teenagersRepository->addTeenagerEmailVarifyToken($teenagerTokenDetail);
                            });
                        //}
                        // ------------------------end sending mail ----------------------------//
                        //$teenagerDetailbyId['payment_status'] = 0;
                        // if($teenagerDetailbyId->t_sponsor_choice == 1){
                        //     $responseMsg = 'Hi <strong>'.$teenagerDetailbyId->t_name.'</strong>, The instruction to activate your account has been sent to your registered eMailID <strong>'.$teenagerDetailbyId->t_email.'</strong>';
                        // }else{
                            $responseMsg = 'Hi <strong>'.$teenagerDetailbyId->t_name.'</strong>, <br/> The access link to activate your account has been sent to your registered eMailID <strong>'.$teenagerDetailbyId->t_email.'</strong>';
                        //}
                        return view('teenager.signupVerification', compact('responseMsg'));
                    }
                } else {
                    $response['message'] = trans('appmessages.missing_data_msg');
                    return Redirect::to("teenager/signup")->with('error', trans('appmessages.missing_data_msg'));
                    exit;
                }
            } else {
                $response['message'] = trans('appmessages.missing_data_msg');
                return Redirect::to("teenager/signup")->withErrors(trans('appmessages.missing_data_msg'))->withInput();
                exit;
            }
            return Redirect::to("teenager/signup")->withErrors(trans('appmessages.missing_data_msg'))->withInput();
            exit;
        }

        return Redirect::to("teenager/signup")->withErrors(trans('appmessages.missing_data_msg'))->withInput();
        exit;
    }

    public function getPhoneCodeByCountry(Request $request)
    {
        $countryId = $request->country_id;
        $countryPhoneCode = '';
        if($countryId != ''){
            $countryData = $this->teenagersRepository->getCountryPhoneCode($countryId);
            if(isset($countryData->c_phone_code) && $countryData->c_phone_code != ''){
                $countryPhoneCode = $countryData->c_phone_code;
            }
        }
        echo $countryPhoneCode; 
        exit; 
    }
    
}
