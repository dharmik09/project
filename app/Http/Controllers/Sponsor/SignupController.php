<?php

namespace App\Http\Controllers\Sponsor;

use App\Http\Controllers\Controller;
use App\Http\Requests\SponsorSignupRequest;
use Auth;
use Image;
use Config;
use Helpers;
use Input;
use Redirect;
use Response;
use Mail;
use App\Transactions;
use App\Teenagers;
use App\Templates;
use App\Sponsors;
use App\Country;
use App\State;
use App\City;
use App\Services\Template\Contracts\TemplatesRepository;
use App\Services\Sponsors\Contracts\SponsorsRepository;
use App\Services\Schools\Contracts\SchoolsRepository;
use App\Services\Coin\Contracts\CoinRepository;
use App\CMS;
use Softon\Indipay\Facades\Indipay;
use App\Services\FileStorage\Contracts\FileStorageRepository;

class SignupController extends Controller {

    public function __construct(FileStorageRepository $fileStorageRepository, SponsorsRepository $sponsorsRepository,SchoolsRepository $schoolsRepository, TemplatesRepository $templatesRepository, CoinRepository $coinRepository) {

        $this->objSponsors = new Sponsors();
        $this->sponsorsRepository = $sponsorsRepository;
        $this->schoolsRepository = $schoolsRepository;
        $this->templatesRepository = $templatesRepository;
        $this->coinRepository =  $coinRepository;
        $this->fileStorageRepository = $fileStorageRepository;
        $this->contactphotoOriginalImageUploadPath = Config::get('constant.CONTACT_PHOTO_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->contactphotoThumbImageUploadPath = Config::get('constant.CONTACT_PHOTO_THUMB_IMAGE_UPLOAD_PATH');
        $this->contactphotoThumbImageHeight = Config::get('constant.CONTACT_PHOTO_THUMB_IMAGE_HEIGHT');
        $this->contactphotoThumbImageWidth = Config::get('constant.CONTACT_PHOTO_THUMB_IMAGE_WIDTH');

        $this->sponsorOriginalImageUploadPath = Config::get('constant.SPONSOR_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->sponsorThumbImageUploadPath = Config::get('constant.SPONSOR_THUMB_IMAGE_UPLOAD_PATH');
        $this->sponsorThumbImageHeight = Config::get('constant.SPONSOR_THUMB_IMAGE_HEIGHT');
        $this->sponsorThumbImageWidth = Config::get('constant.SPONSOR_THUMB_IMAGE_WIDTH');
        $this->cmsObj = new CMS();
    }

    public function signup() {
        $newuser = array();
        if (Auth::guard('sponsor')->check()) {
            return Redirect::to("/sponsor/home");
        }
        $countries = Helpers::getCountries();
        //$cities = Helpers::getCities();

        //$states = Helpers::getStates();
        $sponsorDetail = $this->sponsorsRepository->getApprovedSponsors();

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

        return view('sponsor.signup', compact('newuser', 'sponsorDetail', 'countries', 'infotext', 'policytext'));
    }

    public function doSignup(SponsorSignupRequest $request) {

        $body = Input::all();
        $response = [];
        $response['status'] = 0;
        $response['message'] = trans('appmessages.default_error_msg');
        $sponsorInitialCredit = Helpers::getConfigValueByKey('SPONSOR_INITIAL_CREDIT');

        $sponsorDetail = [];
        $sponsorDetail['sp_company_name'] = (isset($body['company_name']) && $body['company_name'] != '') ? e($body['company_name']) : '';
        $sponsorDetail['sp_admin_name'] = (isset($body['admin_name']) && $body['admin_name'] != '') ? e($body['admin_name']) : '';
        $sponsorDetail['sp_address1'] = (isset($body['address1']) && $body['address1'] != '') ? $body['address1'] : '';
        $sponsorDetail['sp_address2'] = (isset($body['address2']) && $body['address2'] != '') ? $body['address2'] : '';
        $sponsorDetail['sp_pincode'] = (isset($body['pincode']) && $body['pincode'] != '') ? $body['pincode'] : '';
        $sponsorDetail['sp_city'] = (isset($body['city']) && $body['city'] != '') ? $body['city'] : '';
        $sponsorDetail['sp_state'] = (isset($body['state']) && $body['state'] != '') ? $body['state'] : '';
        $sponsorDetail['sp_country'] = (isset($body['country']) && $body['country'] != '') ? $body['country'] : '';
        $sponsorDetail['sp_isapproved'] = (isset($body['isapproved']) && $body['isapproved'] != '') ? $body['isapproved'] : '';
        $sponsorDetail['sp_logo'] = '';
        $sponsorDetail['sp_photo'] = '';
        $sponsorDetail['sp_first_name'] = (isset($body['first_name']) && $body['first_name'] != '') ? $body['first_name'] : '';
        $sponsorDetail['sp_last_name'] = (isset($body['last_name']) && $body['last_name'] != '') ? $body['last_name'] : '';
        $sponsorDetail['sp_title'] = (isset($body['title']) && $body['title'] != '') ? $body['title'] : '';
        $sponsorDetail['sp_phone'] = (isset($body['phone']) && $body['phone'] != '') ? $body['phone'] : '';
        $sponsorDetail['sp_email'] = (isset($body['email']) && $body['email'] != '') ? $body['email'] : '';
        $sponsorDetail['password'] = (isset($body['password']) && $body['password'] != '') ? bcrypt($body['password']) : '';
        $sponsorDetail['sp_sc_uniqueid'] = (isset($body['sp_sc_uniqueid']) && $body['sp_sc_uniqueid'] != '') ? $body['sp_sc_uniqueid'] : '';
        $sponsorDetail['sp_credit'] = $sponsorInitialCredit;
        $sponsorDetail['deleted'] = '1';
        $sponsorDetail['sp_uniqueid'] = Helpers::getTeenagerUniqueId();
        $schoolCoins = $sponsorInitialCredit;
        if ($sponsorDetail['sp_sc_uniqueid'] != '') {
            $schoolExist = $this->schoolsRepository->checkActiveSchoolExist($sponsorDetail['sp_sc_uniqueid']);
        }

        if ($sponsorDetail['sp_email'] != '') {
            $sponsorEmailExist = $this->sponsorsRepository->checkActiveEmailExist($sponsorDetail['sp_email']);
        }

        if (isset($schoolExist) && $schoolExist) {
            $response['message'] = trans('appmessages.schoolnotexist');
            return Redirect::to("sponsor/signup")->withErrors(trans('appmessages.schoolnotexist'))->withInput();
            exit;
        } else {
            if (isset($sponsorEmailExist) && $sponsorEmailExist) {
                $response['message'] = trans('appmessages.userwithsameemailaddress');
                return Redirect::to("sponsor/signup")->with('error', trans('appmessages.userwithsameemailaddress'));
                exit;
            }else {
                if (($sponsorDetail['sp_email'] != '') && $sponsorDetail['password'] != '') {
                    if (!filter_var($body['email'], FILTER_VALIDATE_EMAIL)) {
                        $response['message'] = trans('appmessages.invalid_email_msg');
                        return Redirect::to("sponsor/signup")->with('error', trans('appmessages.invalid_email_msg'));
                        exit;
                    } else {
                        if (Input::file()) {
                            $file = Input::file('photo');
                            if (!empty($file)) {
                                $fileName = 'sponsor_' . time() . '.' . $file->getClientOriginalExtension();
                                $pathOriginal = public_path($this->contactphotoOriginalImageUploadPath . $fileName);
                                $pathThumb = public_path($this->contactphotoThumbImageUploadPath . $fileName);
                                Image::make($file->getRealPath())->save($pathOriginal);
                                Image::make($file->getRealPath())->resize($this->contactphotoThumbImageWidth, $this->contactphotoThumbImageHeight)->save($pathThumb);

                                //Uploading on AWS
                                $originalImage = $this->fileStorageRepository->addFileToStorage($fileName, $this->contactphotoOriginalImageUploadPath, $pathOriginal, "s3");
                                $thumbImage = $this->fileStorageRepository->addFileToStorage($fileName, $this->contactphotoThumbImageUploadPath, $pathThumb, "s3");
                                
                                \File::delete($this->contactphotoOriginalImageUploadPath . $fileName);
                                \File::delete($this->contactphotoThumbImageUploadPath . $fileName);
                                $sponsorDetail['sp_photo'] = $fileName;
                            } else {
                                $sponsorDetail['sp_photo'] = "proteen_logo.png";
                            }

                            if (Input::file()) {
                                $file = Input::file('logo');
                                if (!empty($file)) {
                                    $fileName = 'sponsor_' . time() . '.' . $file->getClientOriginalExtension();
                                    $pathOriginal = public_path($this->sponsorOriginalImageUploadPath . $fileName);
                                    $pathThumb = public_path($this->sponsorThumbImageUploadPath . $fileName);
                                    Image::make($file->getRealPath())->save($pathOriginal);
                                    Image::make($file->getRealPath())->resize($this->sponsorThumbImageWidth, $this->sponsorThumbImageHeight)->save($pathThumb);

                                    //Uploading on AWS
                                    $originalImage = $this->fileStorageRepository->addFileToStorage($fileName, $this->sponsorOriginalImageUploadPath, $pathOriginal, "s3");
                                    $thumbImage = $this->fileStorageRepository->addFileToStorage($fileName, $this->sponsorThumbImageUploadPath, $pathThumb, "s3");
                                    
                                    \File::delete($this->sponsorOriginalImageUploadPath . $fileName);
                                    \File::delete($this->sponsorThumbImageUploadPath . $fileName);
                                    $sponsorDetail['sp_logo'] = $fileName;
                                } else {
                                    return Redirect::to("sponsor/signup")->with('error', trans('labels.sponsorlogo'));
                                }
                            }
                            $sponsorDetailSaved = $this->sponsorsRepository->saveSponsorDetail($sponsorDetail);
                            $sponsorDetailSaved = $sponsorDetailSaved->toArray();
                            $sponsorDetailbyId = $this->sponsorsRepository->getSponsorById($sponsorDetailSaved['id']);
                            if ($sponsorDetail['sp_sc_uniqueid'] != '') {
                                $schoolData = $this->schoolsRepository->getSchoolDataForCoinsDetailByUniqueid($sponsorDetail['sp_sc_uniqueid']);

                                if (!empty($schoolData)) {
                                    $schoolCoins += $schoolData['sc_coins'];
                                }
                                $result = $this->schoolsRepository->updateSchoolCoinsDetailByUniqueid($sponsorDetail['sp_sc_uniqueid'], $schoolCoins);
                                $schoolExist = $this->schoolsRepository->checkActiveSchoolExist($sponsorDetail['sp_sc_uniqueid']);
                            }

                            /*Send notification mail to admin*/
                            $adminEmail = Helpers::getConfigValueByKey('ADMIN_EMAIL');
                            $adminName = Helpers::getConfigValueByKey('ADMIN_NAME');

                            $replaceArray = [];
                            $replaceArray['ADMIN_NAME'] = $adminName;
                            $replaceArray['TYPE'] = 'Enterprise(Sponsor)';
                            $replaceArray['USER_NAME'] = $sponsorDetail['sp_first_name'] ." ". $sponsorDetail['sp_last_name'];
                            $replaceArray['EMAIL'] = $sponsorDetail['sp_email'];

                            $emailTemplateContent = $this->templatesRepository->getEmailTemplateDataByName(Config::get('constant.VARIFY_USER_REQUEST'));
                            $content = $this->templatesRepository->getEmailContent($emailTemplateContent->et_body, $replaceArray);

                            $data = array();
                            $data['subject'] = $emailTemplateContent->et_subject;
                            $data['toEmail'] = $adminEmail;
                            $data['toName'] = $adminName;
                            $data['content'] = $content;
                            Mail::send(['html' => 'emails.Template'], $data, function($message) use ($data) {
                                $message->subject($data['subject']);
                                $message->to($data['toEmail'], $data['toName']);
                            });

                            $replaceArray = [];
                            $replaceArray['USER_NAME'] = $sponsorDetail['sp_first_name'] ." ". $sponsorDetail['sp_last_name'];
                            $replaceArray['URL'] = "<a href=" . url("sponsor/enterprise-request?token=" . $sponsorDetail['sp_uniqueid']) . ">" . url("sponsor/enterprise-request?token=" . $sponsorDetail['sp_uniqueid']) . "</a>";

                            $emailTemplateContent = $this->templatesRepository->getEmailTemplateDataByName(Config::get('constant.ENTERPRISE_SIGNUP_REQUEST'));
                            $content = $this->templatesRepository->getEmailContent($emailTemplateContent->et_body, $replaceArray);

                            $data = array();
                            $data['subject'] = $emailTemplateContent->et_subject;
                            $data['toEmail'] = $sponsorDetail['sp_email'];
                            $data['toName'] = $sponsorDetail['sp_first_name'] ." ". $sponsorDetail['sp_last_name'];;
                            $data['content'] = $content;
                            Mail::send(['html' => 'emails.Template'], $data, function($message) use ($data) {
                                $message->subject($data['subject']);
                                $message->to($data['toEmail'], $data['toName']);
                            });

                            $responseMsg = 'Hi <strong>'.$sponsorDetailbyId->sp_admin_name.'</strong>,<br/> An email has be sent to your registered email id for further action to get into ProTeen.';
                            return view('sponsor.signupVerification', compact('responseMsg'));
                        }
                    }
                    {
                        return Redirect::to("sponsor/signup")->with('error', trans('appmessages.missing_data_msg'));
                        exit;
                    }
                }
                return Redirect::to("sponsor/signup")->with('error', trans('appmessages.missing_data_msg'));
                exit;
            }
        }
    }

    public function preLoginPackagePurchase(){
        $token=input::get('token');
        $coinsDetail = $this->coinRepository->getAllCoinsDetail(2);
        $sponsorData = $this->sponsorsRepository->getSponsorDetailByUnqiueId($token);
        if($token && !empty($sponsorData) && count($sponsorData) > 0)
        {
            return view('sponsor.coinsPackagePurchase', compact('coinsDetail','sponsorData'));
        }
        return Redirect::to("sponsor/signup")->with('error', trans('labels.sponsor_not_exist'));
    }

     public function saveCoinPurchasedData($p_id) {
        $coinsDetail = $this->coinRepository->getAllCoinsDetailByid($p_id);
        $sponsorId=input::get('id');
        if (!empty($coinsDetail)) {
            $amount = $coinsDetail[0]->c_price;
            $parameters = [
                  'tid' => $sponsorId.time(),
                  'order_id' => time(),
                  'amount' => $amount,
                  'merchant_param1' => '4',
                  'merchant_param2' => $p_id,
                  'merchant_param3' => $sponsorId,

            ];
            $order = Indipay::prepare($parameters);
            return Indipay::process($order);
        }
   }

}
