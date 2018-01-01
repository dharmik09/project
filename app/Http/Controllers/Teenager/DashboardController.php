<?php

namespace App\Http\Controllers\Teenager;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Helpers;
use Config;
use Mail;
use Session;
use Storage;
use App\Services\Teenagers\Contracts\TeenagersRepository;
use App\Services\Template\Contracts\TemplatesRepository;
use App\Services\Sponsors\Contracts\SponsorsRepository;
use Redirect;
use Response;
use App\Country;
use App\Http\Requests\TeenagerProfileUpdateRequest;
use App\Teenagers;
use Carbon\Carbon;
use App\TeenParentRequest;
use App\Services\Parents\Contracts\ParentsRepository;
use Input;
use App\Services\FileStorage\Contracts\FileStorageRepository;
use App\Services\Level1Activity\Contracts\Level1ActivitiesRepository;
use Image;
use App\Http\Requests\TeenagerPairRequest;

class DashboardController extends Controller
{
    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    public $redirectTo = '/teenager/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Level1ActivitiesRepository $level1ActivitiesRepository, SponsorsRepository $sponsorsRepository, TeenagersRepository $teenagersRepository, TemplatesRepository $templatesRepository, ParentsRepository $parentsRepository, FileStorageRepository $fileStorageRepository)
    {
        $this->teenagersRepository = $teenagersRepository;
        $this->sponsorsRepository = $sponsorsRepository;
        $this->middleware('teenager');
        $this->objCountry = new Country();
        $this->objTeenParentRequest = new TeenParentRequest;
        $this->templateRepository = $templatesRepository;
        $this->level1ActivitiesRepository = $level1ActivitiesRepository;
        $this->teenOriginalImageUploadPath = Config::get('constant.TEEN_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->teenThumbImageUploadPath = Config::get('constant.TEEN_THUMB_IMAGE_UPLOAD_PATH');
        $this->teenProfileImageUploadPath = Config::get('constant.TEEN_PROFILE_IMAGE_UPLOAD_PATH');
        $this->parentsRepository = $parentsRepository;
        $this->fileStorageRepository = $fileStorageRepository;
        $this->teenThumbImageHeight = Config::get('constant.TEEN_THUMB_IMAGE_HEIGHT');
        $this->teenThumbImageWidth = Config::get('constant.TEEN_THUMB_IMAGE_WIDTH');
    }

    //Dashboard data
    public function dashboard()
    {
        $data = [];
        $user = Auth::guard('teenager')->user();
        $data['user_profile'] = (Auth::guard('teenager')->user()->t_photo != "" && Storage::size(Auth::guard('teenager')->user()->t_photo) > 0) ? Storage::url($this->teenProfileImageUploadPath.Auth::guard('teenager')->user()->t_photo) : asset($this->teenProfileImageUploadPath.'proteen-logo.png');
        $data['user_profile_thumb'] = (Auth::guard('teenager')->user()->t_photo != "" && Storage::size(Auth::guard('teenager')->user()->t_photo) > 0) ? Storage::url($this->teenThumbImageUploadPath.Auth::guard('teenager')->user()->t_photo) : asset($this->teenThumbImageUploadPath.'proteen-logo.png');
        $teenagerAPIData = Helpers::getTeenInterestAndStregnthDetails(Auth::guard('teenager')->user()->id);
        $teenagerInterest = isset($teenagerAPIData['APIscore']['interest']) ? $teenagerAPIData['APIscore']['interest'] : [];
        $teenagerMI = isset($teenagerAPIData['APIscale']['MI']) ? $teenagerAPIData['APIscale']['MI'] : [];
        foreach($teenagerMI as $miKey => $miVal) {
            $mitName = Helpers::getMIBySlug($miKey);
            $teenagerMI[$miKey] = (array('score' => $miVal, 'name' => $mitName, 'type' => Config::get('constant.MULTI_INTELLIGENCE_TYPE')));
        }

        $teenagerAptitude = isset($teenagerAPIData['APIscale']['aptitude']) ? $teenagerAPIData['APIscale']['aptitude'] : [];
        foreach($teenagerAptitude as $apptitudeKey => $apptitudeVal) {
            $aptName = Helpers::getApptitudeBySlug($apptitudeKey);
            $teenagerAptitude[$apptitudeKey] = (array('score' => $apptitudeVal, 'name' => $aptName, 'type' => Config::get('constant.APPTITUDE_TYPE')));
        }
        $teenagerPersonality = isset($teenagerAPIData['APIscale']['personality']) ? $teenagerAPIData['APIscale']['personality'] : [];
        foreach($teenagerPersonality as $personalityKey => $personalityVal) {
            $ptName = Helpers::getPersonalityBySlug($personalityKey);
            $teenagerPersonality[$personalityKey] = (array('score' => $personalityVal, 'name' => $ptName, 'type' => Config::get('constant.PERSONALITY_TYPE')));
        }
        $teenagerStrength = array_merge($teenagerAptitude, $teenagerPersonality, $teenagerMI);
        //echo "<pre/>"; print_r($teenagerAPIData); die();
        return view('teenager.home', compact('data', 'user', 'teenagerStrength', 'teenagerInterest'));
    }

    //My profile data
    public function profile()
    {
        $data = [];
        $teenSponsorIds = [];
        $user = Auth::guard('teenager')->user();
        $data['user_profile'] = (Auth::guard('teenager')->user()->t_photo != "") ? Storage::url($this->teenProfileImageUploadPath.Auth::guard('teenager')->user()->t_photo) : asset($this->teenProfileImageUploadPath.'proteen-logo.png');
        $countries = $this->objCountry->getAllCounries();
        $sponsorDetail = $this->sponsorsRepository->getApprovedSponsors();
        $teenagerSponsors = $this->teenagersRepository->getTeenagerSelectedSponsor($user->id);
        $teenagerParents = $this->teenagersRepository->getTeenParents($user->id);
        foreach($teenagerSponsors as $teenagerSponsor) {
            $teenSponsorIds[] = $teenagerSponsor->ts_sponsor;
        }
        $level1Activities = $this->level1ActivitiesRepository->getNotAttemptedActivities(Auth::guard('teenager')->user()->id);
        $teenagerMeta = Helpers::getTeenagerMetaData(Auth::guard('teenager')->user()->id);
        return view('teenager.profile', compact('level1Activities', 'data', 'user', 'countries', 'sponsorDetail', 'teenSponsorIds', 'teenagerParents', 'teenagerMeta'));   
    }

    public function chat()
    {
        return view('teenager.chat');
    }

    //Store my profile data
    public function saveProfile(TeenagerProfileUpdateRequest $request)
    {
        $body = $request->all();
        $user = Auth::guard('teenager')->user();
        $user = Teenagers::find($user->id);
        $teenagerDetail['id'] = $user->id;
        $teenagerDetail['t_name'] = (isset($body['name']) && $body['name'] != '') ? e($body['name']) : '';
        $teenagerDetail['t_lastname'] = (isset($body['lastname']) && $body['lastname'] != '') ? e($body['lastname']) : '';
        //Nickname is ProTeen Code
        $teenagerDetail['t_nickname'] = (isset($body['proteen_code']) && $body['proteen_code'] != '') ? e($body['proteen_code']) : '';
        $stringVariable = $body['year']."-".$body['month']."-".$body['day'];
        $birthDate = Carbon::createFromFormat("Y-m-d", $stringVariable);
        $todayDate = Carbon::now();
        if (Helpers::validateDate($stringVariable, "Y-m-d") && $todayDate->gt($birthDate) ) {
            $teenagerDetail['t_birthdate'] = $stringVariable;
        } else {
            return Redirect::to("teenager/my-profile")->withErrors("Date is invalid")->withInput();
            exit;
        }
        $teenagerDetail['t_gender'] = (isset($body['gender']) && $body['gender'] != '') ? $body['gender'] : '';
        $t_email = (isset($body['email']) && $body['email'] != '') ? $body['email'] : '';
        $teenagerDetail['password'] = (isset($body['password']) && $body['password'] != '') ? bcrypt($body['password']) : $user->password;
        $teenagerDetail['t_phone'] = (isset($body['mobile']) && $body['mobile'] != '') ? $body['mobile'] : '';
        //Added new phone name field
        $teenagerDetail['t_phone_new'] = (isset($body['phone']) && $body['phone'] != '') ? $body['phone'] : '';
        $teenagerDetail['t_country'] = (isset($body['country']) && $body['country'] != '') ? $body['country'] : '';
        $teenagerDetail['t_pincode'] = (isset($body['pincode']) && $body['pincode'] != '') ? $body['pincode'] : '';
        $teenagerDetail['is_search_on'] = (isset($body['public_profile']) && $body['public_profile'] != '') ? $body['public_profile'] : '0';
        $teenagerDetail['is_share_with_other_members'] = (isset($body['share_with_members']) && $body['share_with_members'] != '') ? $body['share_with_members'] : '0';
        $teenagerDetail['is_share_with_parents'] = (isset($body['share_with_parents']) && $body['share_with_parents'] != '') ? $body['share_with_parents'] : '0';
        $teenagerDetail['is_share_with_teachers'] = (isset($body['share_with_teachers']) && $body['share_with_teachers'] != '') ? $body['share_with_teachers'] : '0';
        $teenagerDetail['is_notify'] = (isset($body['notifications']) && $body['notifications'] != '') ? $body['notifications'] : '0';
        $teenagerDetail['t_view_information'] = (isset($body['t_view_information']) && $body['t_view_information'] != '') ? $body['t_view_information'] : '0';

        //Check all default field value -> If those are entered dummy by users
        if ($teenagerDetail['t_name'] == '' || $teenagerDetail['t_lastname'] == '' || $teenagerDetail['t_country'] == '' || $teenagerDetail['t_pincode'] == '' || $teenagerDetail['t_phone'] == '' || $t_email == '') {
            return Redirect::to("teenager/my-profile")->withErrors(trans('validation.someproblems'))->withInput();
            exit;
        }
        if (!isset($body['selected_sponsor']) || count($body['selected_sponsor']) < 1) {
            return Redirect::to("teenager/my-profile")->withErrors("Please select atleast one sponsor choice")->withInput();
            exit;
        }

        if (!in_array($teenagerDetail['t_gender'], array("1", "2"))) {
            return Redirect::to("teenager/my-profile")->withErrors(trans('validation.someproblems'))->withInput();
            exit;
        }
        $teenagerMobileExist = false;
        $teenagerEmailExist = false;
        
        if ($t_email != '' && $user->t_social_provider == 'Normal') {
            $teenagerEmailExist = $this->teenagersRepository->checkActiveEmailExist($t_email, $user->id);
        }
        if ($teenagerDetail['t_phone'] != '' && $user->t_social_provider == 'Normal') {
            $teenagerMobileExist = $this->teenagersRepository->checkActivePhoneExist($teenagerDetail['t_phone'], $user->id);
        }

        if ($teenagerEmailExist) {
            $response['message'] = trans('appmessages.userwithsameemailaddress');
            return Redirect::to("teenager/my-profile")->withErrors(trans('appmessages.userwithsameemailaddress'))->withInput();
            exit;
        } else if ($teenagerMobileExist) {
            $response['message'] = trans('appmessages.userwithsamenumber');
            return Redirect::to("teenager/my-profile")->withErrors(trans('appmessages.userwithsamenumber'))->withInput();
            exit;
        } else {
            /* save sponser by teenager id if sponsor id is not blank */
            if (isset($body['selected_sponsor']) && !empty($body['selected_sponsor'])) {
                $sponserDetail = $this->teenagersRepository->saveTeenagerSponserId($user->id, implode(',', $body['selected_sponsor']));
            }
            if (Input::file()) {
                $file = Input::file('pic');
                if (!empty($file)) {
                    if(isset($user->t_photo) && !empty($user->t_photo)) {
                        $originalImageDelete = $this->fileStorageRepository->deleteFileToStorage($user->t_photo, $this->teenOriginalImageUploadPath, "s3");
                        $thumbImageDelete = $this->fileStorageRepository->deleteFileToStorage($user->t_photo, $this->teenThumbImageUploadPath, "s3");
                        $profileImageDelete = $this->fileStorageRepository->deleteFileToStorage($user->t_photo, $this->teenProfileImageUploadPath, "s3");
                    }
                    $fileName = 'teenager_' . time() . '.' . $file->getClientOriginalExtension();
                    $pathOriginal = public_path($this->teenOriginalImageUploadPath . $fileName);
                    $pathThumb = public_path($this->teenThumbImageUploadPath . $fileName);
                    $pathProfile = public_path($this->teenProfileImageUploadPath . $fileName);
                    Image::make($file->getRealPath())->save($pathOriginal);
                    Image::make($file->getRealPath())->resize($this->teenThumbImageWidth, $this->teenThumbImageHeight)->save($pathThumb);
                    Image::make($file->getRealPath())->resize(200, 200)->save($pathProfile);
                    //Uploading on AWS
                    $originalImage = $this->fileStorageRepository->addFileToStorage($fileName, $this->teenOriginalImageUploadPath, $pathOriginal, "s3");
                    $thumbImage = $this->fileStorageRepository->addFileToStorage($fileName, $this->teenThumbImageUploadPath, $pathThumb, "s3");
                    $profileImage = $this->fileStorageRepository->addFileToStorage($fileName, $this->teenProfileImageUploadPath, $pathProfile, "s3");
                    //Deleting Local Files
                    \File::delete($this->teenOriginalImageUploadPath . $fileName);
                    \File::delete($this->teenThumbImageUploadPath . $fileName);
                    \File::delete($this->teenProfileImageUploadPath . $fileName);
                    $teenagerDetail['t_photo'] = $fileName;
                }
            }
            $teenUpdate = $this->teenagersRepository->saveTeenagerDetail($teenagerDetail);
            if (isset($teenUpdate) && !empty($teenUpdate)) {
                return Redirect::to("teenager/my-profile")->with('success', 'Profile updated successfully.');
            } else {
                return Redirect::to("teenager/my-profile")->withErrors(trans('validation.somethingwrong'));
            }
            exit;
        }
    }

    //Update meta information for teenager
    public function saveTeenagerAchievement(Request $request) {
        $data = [];
        $data['tmd_teenager'] = Auth::guard('teenager')->user()->id;
        $data['tmd_meta_value'] = $request->meta_value;
        $data['tmd_meta_id'] = 1; //"1" is default us for achievement meta data, "2" is default for education data
        
        $teenagerMeta = Helpers::getTeenagerMetaData($data['tmd_teenager'], $data['tmd_meta_id']);
        $data['id'] = (isset($teenagerMeta['achievement'][0]['meta_value_id'])) ? $teenagerMeta['achievement'][0]['meta_value_id'] : 0; 
        
        //Saving the record 
        $teenagerMeta = $this->teenagersRepository->saveTeenagerMetaData($data);
        
        return Redirect::to("teenager/my-profile")->with('success', 'Achievement updated successfully.');
    }

    //Update meta information for teenager
    public function saveTeenagerAcademic(Request $request) {
        $data = [];
        $data['tmd_teenager'] = Auth::guard('teenager')->user()->id;
        $data['tmd_meta_value'] = $request->meta_value;
        $data['tmd_meta_id'] = 2; //"1" is default us for achievement meta data, "2" is default for education data
        
        $teenagerMeta = Helpers::getTeenagerMetaData($data['tmd_teenager'], $data['tmd_meta_id']);
        $data['id'] = (isset($teenagerMeta['education'][0]['meta_value_id'])) ? $teenagerMeta['education'][0]['meta_value_id'] : 0; 
        
        //Saving the record 
        $teenagerMeta = $this->teenagersRepository->saveTeenagerMetaData($data);
        
        return Redirect::to("teenager/my-profile")->with('success', 'Academic record updated successfully.');
    }

    public function getPhoneCodeByCountry(Request $request) {
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

    //Save parent/mentor and teen pair data
    public function savepair(TeenagerPairRequest $request) {
        $teenager = Auth::guard('teenager')->user();
        $postData = Input::all();
        if (!empty($postData)) {
            $parentEmailExist = '';
            $parentDetail = [];
            //$parentDetail['id'] = (isset($postData['p_id']) && $postData['p_id'] != '') ? $postData['p_id'] : 0;
            $parentDetail['p_email'] = (isset($postData['parent_email']) && $postData['parent_email'] != '') ? $postData['parent_email'] : '';
            $parentDetail['p_user_type'] = (isset($postData['p_user_type']) && $postData['p_user_type'] != '') ? $postData['p_user_type'] : '';
            $parentDetail['deleted'] = '1';
            //$password = str_random(10);
            //$parentDetail['password'] = bcrypt($password);
            //Check if parent email exist
            $getParentDetailByEmailId = '';
            $checkPairAvailability = [];
            if ($parentDetail['p_email'] != '') {
                $parentEmailExist = $this->parentsRepository->checkActiveEmailExist($parentDetail['p_email']);
                if ($parentEmailExist) {
                    $getParentDetailByEmailId = $this->parentsRepository->getParentDetailByEmailId($parentDetail['p_email']);
                    $checkPairAvailability = $this->parentsRepository->checkPairAvailability($teenager->id, $getParentDetailByEmailId->id);
                }
            }

            //Check if teenager email exist
            if ($parentDetail['p_email'] != '') {
                $parentteenagerEmailExist = $this->teenagersRepository->checkActiveEmailExist($parentDetail['p_email']);
            }
            if (isset($parentteenagerEmailExist) && $parentteenagerEmailExist) {
                return Redirect::to("teenager/my-profile")->with('error', 'Same email already exist for teenager, Please use different one.')->withInput();
                exit;
            } else {
                if (isset($checkPairAvailability) && !empty($checkPairAvailability) && count($checkPairAvailability) > 0) {
                    if ($checkPairAvailability->ptp_is_verified == 0) {
                        if ($checkPairAvailability->ptp_sent_by == "parent") {
                            $response['message'] = trans('Invitation already sent by them. Verification link emailed to you. Please, complete verification process.');
                        } else {
                            $response['message'] = trans('Invitation already sent by you. Verification link emailed to them. Please, complete verification process.');
                        }
                        return Redirect::to("teenager/my-profile")->with('error', $response['message'])->withInput();
                        exit;
                    } else {
                        $response['message'] = trans('You already paired with this user');
                        return Redirect::to("teenager/my-profile")->with('error', $response['message'])->withInput();
                        exit;
                    }
                } else {
                    if (!$parentEmailExist) {
                        // Save data in database
                        $parentData = $this->parentsRepository->saveParentDetail($parentDetail);
                        if (!empty($parentData)) {
                            $parentData = $parentData->toArray();
                            $parentId = $parentData['id'];
                        }
                    } else {
                        $parentData = $this->parentsRepository->getParentDetailByEmailId($parentDetail['p_email']);
                        $parentData = $parentData->toArray();
                        $parentId = $parentData['id'];
                    }
                    // --------------------start sending mail -----------------------------//
                    $replaceArray = array();
                    $replaceArray['PARENT_NAME'] = (isset($parentData['p_first_name']) && !empty($parentData['p_first_name'])) ? $parentData['p_first_name'] : "";
                    if($parentDetail['p_user_type'] == 1){
                        $replaceArray['PARENT_SET_PROFILE_URL'] = url("parent/set-profile");
                    }else{
                        $replaceArray['PARENT_SET_PROFILE_URL'] = url("counselor/set-profile");
                    }
                    //$replaceArray['PARENT_EMAIL'] = $parentData['p_email'];
                    //$replaceArray['PARENT_PASSWORD'] = $password;
                    $replaceArray['PARENT_UNIQUEID'] = Helpers::getParentUniqueId();
                    $replaceArray['VERIFICATION_URL'] = url("parent/verify-parent-teen-pair-registration?token=" . $replaceArray['PARENT_UNIQUEID']);
                    $replaceArray['USERNAME'] = Auth::guard('teenager')->user()->t_name;
                    if (isset($parentEmailExist) && $parentEmailExist) {
                        $emailTemplateContent = $this->templateRepository->getEmailTemplateDataByName(Config::get('constant.PARENT_TEEN_SECOND_TIME'));
                        $content = $this->templateRepository->getEmailContent($emailTemplateContent->et_body, $replaceArray);
                    } else {
                        $emailTemplateContent = $this->templateRepository->getEmailTemplateDataByName(Config::get('constant.PARENT_TEENAGER_VAIRIFIED_EMAIL_TEMPLATE_NAME'));
                        $content = $this->templateRepository->getEmailContent($emailTemplateContent->et_body, $replaceArray);
                    }

                    $data = array();
                    $data['subject'] = $emailTemplateContent->et_subject;
                    $data['toEmail'] = $parentData['p_email'];
                    $data['toName'] = (isset($parentData['p_first_name']) && !empty($parentData['p_first_name'])) ? $parentData['p_first_name'] : "";
                    $data['content'] = $content;
                    $data['ptp_token'] = $replaceArray['PARENT_UNIQUEID'];
                    $data['parent_id'] = $parentData['id'];
                    $data['parent_token'] = $replaceArray['PARENT_UNIQUEID'];
                    $data['teen_id'] = Auth::guard('teenager')->user()->id;
                    //$data['teen_id'] = $teenagerDetailbyId->id;

                    Mail::send(['html' => 'emails.Template'], $data, function($message) use ($data) {
                                $message->subject($data['subject']);
                                $message->to($data['toEmail'], $data['toName']);

                                // Save parent-teen id in verification table
                                $parentTeenVerificationData['ptp_parent_id'] = $data['parent_id'];
                                $parentTeenVerificationData['ptp_teenager'] = $data['teen_id'];
                                $parentTeenVerificationData['ptp_is_verified'] = 0;
                                $parentTeenVerificationData['ptp_sent_by'] = 'teen';
                                $parentTeenVerificationData['ptp_token'] = $data['parent_token'];

                                $this->teenagersRepository->saveParentTeenVerification($parentTeenVerificationData);
                            });
                    // ------------------------end sending mail ----------------------------//
                    return Redirect::to("teenager/my-profile")->with('success', 'Your invitation has been sent successfully.');
                    exit;
                }
            }
        }
    }
   
}
