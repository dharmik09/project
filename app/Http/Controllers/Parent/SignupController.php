<?php

namespace App\Http\Controllers\Parent;
use App\Http\Controllers\Controller;
use App\Http\Requests\ParentSignupRequest;
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
use App\Teenagers;
use App\Templates;
use App\Sponsors;
use App\Country;
use App\State;
use App\City;
use App\Services\Template\Contracts\TemplatesRepository;
use App\Services\Parents\Contracts\ParentsRepository;
use App\Services\Teenagers\Contracts\TeenagersRepository;
use App\Services\Sponsors\Contracts\SponsorsRepository;
use App\CMS;
use App\Services\FileStorage\Contracts\FileStorageRepository;

class SignupController extends Controller {

    public function __construct(TeenagersRepository $teenagersRepository, ParentsRepository $parentsRepository, SponsorsRepository $sponsorsRepository, TemplatesRepository $templatesRepository, FileStorageRepository $fileStorageRepository) 
    {
        $this->objParents = new Parents();
        $this->parentsRepository = $parentsRepository;
        $this->objSponsors = new Sponsors();
        $this->templateRepository = $templatesRepository;
        $this->objTeenagers = new Teenagers();
        $this->teenagersRepository = $teenagersRepository;
        $this->sponsorsRepository = $sponsorsRepository;
        $this->objTemplates = new Templates();
        $this->parentOriginalImageUploadPath = Config::get('constant.PARENT_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->parentThumbImageUploadPath = Config::get('constant.PARENT_THUMB_IMAGE_UPLOAD_PATH');
        $this->parentThumbImageHeight = Config::get('constant.PARENT_THUMB_IMAGE_HEIGHT');
        $this->parentThumbImageWidth = Config::get('constant.PARENT_THUMB_IMAGE_WIDTH');
        $this->cmsObj = new CMS();
        $this->fileStorageRepository = $fileStorageRepository;
    }

    public function signup() {

        $newuser = array();
        if (Auth::guard('parent')->check()) {
            return Redirect::to("/parent/home");
        }
        $countries = Helpers::getCountries();
        //$cities =  Helpers::getCities();
        //$states =  Helpers::getStates();
        $type = 'Parent';
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

        return view('parent.signup', compact('newuser','countries','type', 'infotext', 'policytext'));
    }

    public function doSignup(ParentSignupRequest $request) {

        $body = Input::all();

        $response = [];
        $response['status'] = 0;
        $response['message'] = trans('appmessages.default_error_msg');
        $parentInitialCoins = Helpers::getConfigValueByKey('PARENT_INITIAL_COINS');
        $parentDetail = [];
        $parentDetail['p_coins'] = (isset($parentInitialCoins) && !empty($parentInitialCoins)) ? $parentInitialCoins : 0;
        $parentDetail['p_first_name'] = (isset($body['first_name']) && $body['first_name'] != '') ? e($body['first_name']) : '';
        $parentDetail['p_last_name'] = (isset($body['last_name']) && $body['last_name'] != '') ? e($body['last_name']) : '';
        $parentDetail['p_address1'] = (isset($body['address1']) && $body['address1'] != '') ? e($body['address1']) : '';
        $parentDetail['p_address2'] = (isset($body['address2']) && $body['address2'] != '') ? $body['address2'] : '';
        $parentDetail['p_pincode'] = (isset($body['pincode']) && $body['pincode'] != '') ? $body['pincode'] : '';
        $parentDetail['p_city'] = (isset($body['city']) && $body['city'] != '') ? $body['city'] : '';
        $parentDetail['p_state'] = (isset($body['state']) && $body['state'] != '') ? $body['state'] : '';
        $parentDetail['p_country'] = (isset($body['country']) && $body['country'] != '') ? $body['country'] : '';
        $parentDetail['p_gender'] = (isset($body['gender']) && $body['gender'] != '') ? $body['gender'] : '';
        //$parentDetail['p_photo'] = '';
        $parentDetail['password'] = (isset($body['password']) && $body['password'] != '') ? bcrypt($body['password']) : '';
        $parentDetail['p_email'] = (isset($body['email']) && $body['email'] != '') ? $body['email'] : '';
        $parentDetail['deleted'] = '1';
        $parentDetail['p_user_type'] = $body['user_type'];
        $p_teenager_reference_id = (isset($body['p_teenager_reference_id']) && $body['p_teenager_reference_id'] != '') ? $body['p_teenager_reference_id'] : '';
        
        //Check if teenager unique id is present 
        $teenagerData = $this->teenagersRepository->getTeenagerByUniqueId($p_teenager_reference_id);
        
        if (in_array($parentDetail['p_gender'], array("1", "2"))) {
        }
        else
        {
           return Redirect::to("parent/signup")->with('error', trans('validation.someproblems'));
           exit;
        }
        if ($parentDetail['p_email'] != '' ) {
            $parentEmailExist = $this->parentsRepository->checkActiveEmailExist($parentDetail['p_email']);
        }

        if (isset($parentEmailExist) && $parentEmailExist) 
        {
            $response['message'] = trans('appmessages.userwithsameemailaddress');            
            //return Redirect::to("parent/signup")->with('error', trans('appmessages.userwithsameemailaddress'));
            return Redirect::to("parent/signup")->withErrors(trans('appmessages.userwithsameemailaddress'))->withInput();
            exit;
        }
        elseif(empty($teenagerData))
        {
            return Redirect::to("parent/signup")->withErrors('Invalid teenager id')->withInput();
            exit;
        }
        else 
        {
            if (!filter_var($body['email'], FILTER_VALIDATE_EMAIL)) {
                $response['message'] = trans('appmessages.invalid_email_msg');
                return Redirect::to("parent/signup")->with('error', trans('appmessages.invalid_email_msg'));
                exit;
            } 
            else 
            {
                    $file = Input::file('photo');
                    if (!empty($file)) {
                        $fileName = 'parent' . time() . '.' . $file->getClientOriginalExtension();
                        $pathOriginal = public_path($this->parentOriginalImageUploadPath . $fileName);
                        $pathThumb = public_path($this->parentThumbImageUploadPath . $fileName);
                        Image::make($file->getRealPath())->save($pathOriginal);
                        Image::make($file->getRealPath())->resize($this->parentThumbImageWidth, $this->parentThumbImageHeight)->save($pathThumb);

                        //Uploading on AWS
                        $originalImage = $this->fileStorageRepository->addFileToStorage($fileName, $this->parentOriginalImageUploadPath, $pathOriginal, "s3");
                        $thumbImage = $this->fileStorageRepository->addFileToStorage($fileName, $this->parentThumbImageUploadPath, $pathThumb, "s3");
                        
                        \File::delete($this->parentOriginalImageUploadPath . $fileName);
                        \File::delete($this->parentThumbImageUploadPath . $fileName);

                        $parentDetail['p_photo'] = $fileName;
                    }
                    else{
                      $parentDetail['p_photo']= 'proteen_logo.png';
                    }
                   $parentDetailSaved = $this->parentsRepository->saveParentDetail($parentDetail);
                   $parentDetailbyId = $this->parentsRepository->getParentById($parentDetailSaved['id']);



                    // --------------------start sending mail of parent verification-----------------------------//
                    $replaceArray = array();
                    $replaceArray['PARENT_NAME'] = $parentDetailbyId->p_first_name;
                    $replaceArray['PARENT_UNIQUEID'] = Helpers::getParentUniqueId();
                    $replaceArray['PARENT_URL'] = url("parent/verify-parent-registration?token=" . $replaceArray['PARENT_UNIQUEID']);
                    $emailTemplateContent = $this->templateRepository->getEmailTemplateDataByName(Config::get('constant.PARENT_VAIRIFIED_EMAIL_TEMPLATE_NAME'));
                    $content = $this->templateRepository->getEmailContent($emailTemplateContent->et_body, $replaceArray);
                    $data = array();
                    $data['subject'] = $emailTemplateContent->et_subject;
                    $data['toEmail'] = $parentDetailbyId->p_email;
                    $data['toName'] = $parentDetailbyId->p_first_name;
                    $data['content'] = $content;
                    $data['parent_token'] = $replaceArray['PARENT_UNIQUEID'];
                    $data['parenr_url'] = $replaceArray['PARENT_URL'];
                    $data['parent_id'] = $parentDetailbyId->id;
                    Mail::send(['html' => 'emails.Template'], $data, function($message) use ($data) {
                                $message->subject($data['subject']);
                                $message->to($data['toEmail'], $data['toName']);
                                $parentTokenDetail = [];
                                $parentTokenDetail['tev_token'] = $data['parent_token'];
                                $parentTokenDetail['tev_parent'] = $data['parent_id'];
                                $this->parentsRepository->addParentEmailVarifyToken($parentTokenDetail);
                            });
                    // ------------------------end sending mail ----------------------------//

                    // --------------------start sending mail to teen for pair-----------------------------//
                    $teenagerDetail = $this->teenagersRepository->getTeenagerByUniqueId($p_teenager_reference_id);

                    if(isset($teenagerDetail) && !empty($teenagerDetail)){        
                        $replaceArray = array();
                        $replaceArray['TEEN_NAME'] = $teenagerDetail->t_name;
                        $replaceArray['PARENT_NAME'] = $parentDetailbyId->p_first_name.' '.$parentDetailbyId->p_last_name;
                        $replaceArray['PARENT_EMAIL'] = $parentDetailbyId->p_email;
                        $replaceArray['USERTYPE'] = ($parentDetailbyId->p_user_type == 1)?'Parent':'Counsellor';
                        $replaceArray['PARENT_UNIQUEID'] = Helpers::getParentUniqueId();
                        $replaceArray['VERIFICATION_URL'] = url("parent/verify-parent-teen-pair?token=" . $replaceArray['PARENT_UNIQUEID']);

                        $emailTemplateContent = $this->templateRepository->getEmailTemplateDataByName(Config::get('constant.PARENT_TEEN_PAIR_FROM_PARENT_SECTION'));

                        $content = $this->templateRepository->getEmailContent($emailTemplateContent->et_body, $replaceArray);

                        $data = array();
                        $data['subject'] = ($parentDetailbyId->p_user_type == 1)?$emailTemplateContent->et_subject:'Counsellor Teen Pair From Parent';
                        $data['toEmail'] = $teenagerDetail->t_email;
                        $data['toName'] = $teenagerDetail->t_name;
                        $data['content'] = $content;
                        $data['ptp_token'] = $replaceArray['PARENT_UNIQUEID'];
                        $data['parent_id'] = $parentDetailbyId->id;
                        $data['parent_token'] = $replaceArray['PARENT_UNIQUEID'];
                        $data['teen_id'] = $teenagerDetail->id;

                        Mail::send(['html' => 'emails.Template'], $data, function($message) use ($data) {
                            $message->subject($data['subject']);
                            $message->to($data['toEmail'], $data['toName']);

                            // Save parent-teen id in verification table
                            $parentTeenVerificationData['ptp_parent_id'] = $data['parent_id'];
                            $parentTeenVerificationData['ptp_teenager'] = $data['teen_id'];
                            $parentTeenVerificationData['ptp_is_verified'] = 0;
                            $parentTeenVerificationData['ptp_sent_by'] = 'parent';
                            $parentTeenVerificationData['ptp_token'] = $data['parent_token'];

                            $this->teenagersRepository->saveParentTeenVerification($parentTeenVerificationData);
                        });        
                    } 
                    $userType = $parentDetailbyId->p_user_type;
                $responseMsg = 'Hi <strong>'.$parentDetailbyId->p_first_name.'</strong>,<br/> The instruction to activate your account has been sent to your registered eMailID <strong>'.$parentDetailbyId->p_email.'</strong><br/>Also you can view progress of your children once they verify pair with you.';                           
                return view('parent.signupVerification', compact('responseMsg','userType'));                
            }                                 
        }
    }
}
