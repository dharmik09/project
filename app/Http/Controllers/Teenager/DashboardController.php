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
use Event;
use App\Events\SendMail;
use App\Services\Level2Activity\Contracts\Level2ActivitiesRepository;
use App\Services\Community\Contracts\CommunityRepository;
use App\Services\Professions\Contracts\ProfessionsRepository;
use App\Jobs\SetProfessionMatchScale;

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
    public function __construct(Level1ActivitiesRepository $level1ActivitiesRepository, SponsorsRepository $sponsorsRepository, TeenagersRepository $teenagersRepository, TemplatesRepository $templatesRepository, ParentsRepository $parentsRepository, FileStorageRepository $fileStorageRepository, CommunityRepository $communityRepository, Level2ActivitiesRepository $Level2ActivitiesRepository, ProfessionsRepository $professionsRepository)
    {
        $this->teenagersRepository = $teenagersRepository;
        $this->sponsorsRepository = $sponsorsRepository;
        $this->middleware('teenager');
        $this->objCountry = new Country();
        $this->objTeenParentRequest = new TeenParentRequest;
        $this->templateRepository = $templatesRepository;
        $this->level1ActivitiesRepository = $level1ActivitiesRepository;
        $this->Level2ActivitiesRepository = $Level2ActivitiesRepository;
        $this->teenOriginalImageUploadPath = Config::get('constant.TEEN_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->teenThumbImageUploadPath = Config::get('constant.TEEN_THUMB_IMAGE_UPLOAD_PATH');
        $this->teenProfileImageUploadPath = Config::get('constant.TEEN_PROFILE_IMAGE_UPLOAD_PATH');
        $this->parentsRepository = $parentsRepository;
        $this->fileStorageRepository = $fileStorageRepository;
        $this->teenThumbImageHeight = Config::get('constant.TEEN_THUMB_IMAGE_HEIGHT');
        $this->teenThumbImageWidth = Config::get('constant.TEEN_THUMB_IMAGE_WIDTH');
        $this->cartoonThumbImageUploadPath = Config::get('constant.CARTOON_THUMB_IMAGE_UPLOAD_PATH');
        $this->humanThumbImageUploadPath = Config::get('constant.HUMAN_THUMB_IMAGE_UPLOAD_PATH');
        $this->relationIconThumbImageUploadPath = Config::get('constant.RELATION_ICON_THUMB_IMAGE_UPLOAD_PATH');
        $this->communityRepository = $communityRepository;
        $this->professionsRepository = $professionsRepository;
    }

    //Dashboard data
    public function dashboard()
    {
        $data = [];
        $user = Auth::guard('teenager')->user();
        $data['user_profile'] = (Auth::guard('teenager')->user()->t_photo != "" && Storage::size($this->teenOriginalImageUploadPath.Auth::guard('teenager')->user()->t_photo) > 0) ? Storage::url($this->teenOriginalImageUploadPath.Auth::guard('teenager')->user()->t_photo) : asset($this->teenOriginalImageUploadPath.'proteen-logo.png');
        $data['user_profile_thumb'] = (Auth::guard('teenager')->user()->t_photo != "" && Storage::size($this->teenThumbImageUploadPath.Auth::guard('teenager')->user()->t_photo) > 0) ? Storage::url($this->teenThumbImageUploadPath.Auth::guard('teenager')->user()->t_photo) : asset($this->teenThumbImageUploadPath.'proteen-logo.png');
        $teenagerAPIData = Helpers::getTeenInterestAndStregnthDetails(Auth::guard('teenager')->user()->id);
        $teenagerAPIMaxScore = Helpers::getTeenInterestAndStregnthMaxScore();
        $teenagerInterestArr = isset($teenagerAPIData['APIscore']['interest']) ? $teenagerAPIData['APIscore']['interest'] : [];
        $teenagerInterest = [];
        foreach($teenagerInterestArr as $interestKey => $interestVal){
            if ($interestVal < 1) { continue; } else {
            $itName = Helpers::getInterestBySlug($interestKey);
            $teenItScore = $this->getTeenScoreInPercentage($teenagerAPIMaxScore['interest'][$interestKey], $interestVal);
            $teenagerInterest[$interestKey] = (array('score' => $teenItScore, 'name' => $itName));
            }
        }
        $teenagerMI = isset($teenagerAPIData['APIscore']['MI']) ? $teenagerAPIData['APIscore']['MI'] : [];

        foreach($teenagerMI as $miKey => $miVal) {
            $mitName = Helpers::getMIBySlug($miKey);
            $teenMIScore = $this->getTeenScoreInPercentage($teenagerAPIMaxScore['MI'][$miKey], $miVal);
                $teenagerMI[$miKey] = (array('score' => $teenMIScore, 'name' => $mitName, 'type' => Config::get('constant.MULTI_INTELLIGENCE_TYPE')));
        }

        $teenagerAptitude = isset($teenagerAPIData['APIscore']['aptitude']) ? $teenagerAPIData['APIscore']['aptitude'] : [];
        foreach($teenagerAptitude as $apptitudeKey => $apptitudeVal) {
            $aptName = Helpers::getApptitudeBySlug($apptitudeKey);
            $teenAptScore = $this->getTeenScoreInPercentage($teenagerAPIMaxScore['aptitude'][$apptitudeKey], $apptitudeVal);
            $teenagerAptitude[$apptitudeKey] = (array('score' => $teenAptScore, 'name' => $aptName, 'type' => Config::get('constant.APPTITUDE_TYPE')));
        }
        $teenagerPersonality = isset($teenagerAPIData['APIscore']['personality']) ? $teenagerAPIData['APIscore']['personality'] : [];
        foreach($teenagerPersonality as $personalityKey => $personalityVal) {
            $ptName = Helpers::getPersonalityBySlug($personalityKey);
            $teenPtScore = $this->getTeenScoreInPercentage($teenagerAPIMaxScore['personality'][$personalityKey], $personalityVal);
            $teenagerPersonality[$personalityKey] = (array('score' => $teenPtScore, 'name' => $ptName, 'type' => Config::get('constant.PERSONALITY_TYPE')));
        }
        $teenagerStrength = array_merge($teenagerAptitude, $teenagerPersonality, $teenagerMI);

        $section1Collection = $this->Level2ActivitiesRepository->getNoOfTotalQuestionsAttemptedQuestionBySection($user->id,1);
        $section2Collection = $this->Level2ActivitiesRepository->getNoOfTotalQuestionsAttemptedQuestionBySection($user->id,2);
        $section3Collection = $this->Level2ActivitiesRepository->getNoOfTotalQuestionsAttemptedQuestionBySection($user->id,3);

        // $getLevel2AssessmentResult = Helpers::getTeenAPIScore($user->id);
        // dispatch( new SetProfessionMatchScale($user->id) );
        // echo "<pre/>"; die("die");

        $section1Percentage = 0;
        $section2Percentage = 0;
        $section3Percentage = 0;
        
        if($section1Collection[0]->NoOfTotalQuestions != 0){
            $section1Percentage = ($section1Collection[0]->NoOfAttemptedQuestions >= $section1Collection[0]->NoOfTotalQuestions) ? 100 : ($section1Collection[0]->NoOfAttemptedQuestions*100)/$section1Collection[0]->NoOfTotalQuestions;
        }
        if($section2Collection[0]->NoOfTotalQuestions != 0){
            $section2Percentage = ($section2Collection[0]->NoOfAttemptedQuestions >= $section2Collection[0]->NoOfTotalQuestions) ? 100 : ($section2Collection[0]->NoOfAttemptedQuestions*100)/$section2Collection[0]->NoOfTotalQuestions;
        }
        if($section3Collection[0]->NoOfTotalQuestions != 0){
            $section3Percentage = ($section3Collection[0]->NoOfAttemptedQuestions >= $section3Collection[0]->NoOfTotalQuestions) ? 100 : ($section3Collection[0]->NoOfAttemptedQuestions*100)/$section3Collection[0]->NoOfTotalQuestions;
        }

        $secComplete1 = $secComplete2 = $secComplete3 = 0; 
        if($section1Percentage == 0){
            $section1 = 'Begin now';
        }
        else{
            $section1 = number_format((float)$section1Percentage, 0, '.', '').'% Complete';
            $secComplete1 = (number_format((float)$section1Percentage, 0, '.', '') >= 100) ? 1 : 0;
        }

        if($section2Percentage == 0){
            $section2 = 'Begin now';
        }
        else{
            $section2 = number_format((float)$section2Percentage, 0, '.', '').'% Complete';
            $secComplete2 = (number_format((float)$section2Percentage, 0, '.', '') >= 100) ? 1 : 0;
        }

        if($section3Percentage == 0){
            $section3 = 'Begin now';
        }
        else{
            $section3 = number_format((float)$section3Percentage, 0, '.', '').'% Complete';
            $secComplete3 = (number_format((float)$section3Percentage, 0, '.', '') >= 100) ? 1 : 0;
        }

        $teenagerNetwork = $this->communityRepository->getMyConnections($user->id, array(), '', '', '', 1);
        $teenThumbImageUploadPath = $this->teenThumbImageUploadPath;
        $teenagerCareers = $this->professionsRepository->getMyCareers($user->id);
        $getTeenagerHML = Helpers::getTeenagerMatchScale($user->id);

        return view('teenager.home', compact('getTeenagerHML' ,'secComplete3', 'secComplete2', 'secComplete1', 'data', 'user', 'teenagerStrength', 'teenagerInterest','section1','section2','section3', 'teenagerNetwork', 'teenThumbImageUploadPath', 'teenagerCareers'));
    }

    //My profile data
    public function profile()
    {
        $data = [];
        $teenSponsorIds = [];
        $user = Auth::guard('teenager')->user();
        $data['user_profile'] = (Auth::guard('teenager')->user()->t_photo != "" && Storage::size($this->teenThumbImageUploadPath.Auth::guard('teenager')->user()->t_photo) > 0) ? Storage::url($this->teenThumbImageUploadPath.Auth::guard('teenager')->user()->t_photo) : asset($this->teenThumbImageUploadPath.'proteen-logo.png');
        $countries = $this->objCountry->getAllCounries();
        $sponsorDetail = $this->sponsorsRepository->getApprovedSponsors();
        $teenagerSponsors = $this->teenagersRepository->getTeenagerSelectedSponsor($user->id);
        $teenagerParents = $this->teenagersRepository->getTeenParents($user->id);
        foreach($teenagerSponsors as $teenagerSponsor) {
            $teenSponsorIds[] = $teenagerSponsor->ts_sponsor;
        }
        $level1Activities = $this->level1ActivitiesRepository->getNotAttemptedActivities(Auth::guard('teenager')->user()->id);
        $teenagerMeta = Helpers::getTeenagerMetaData(Auth::guard('teenager')->user()->id);
        $teenagerMyIcons = array();
        //Get teenager choosen Icon
        $teenagerIcons = $this->teenagersRepository->getTeenagerSelectedIcon(Auth::guard('teenager')->user()->id);
        $relationIcon = array();
        $fictionIcon = array();
        $nonFiction = array();
        if (isset($teenagerIcons) && !empty($teenagerIcons)) {
            foreach ($teenagerIcons as $key => $icon) {
                if ($icon->ti_icon_type == 1) {
                    if ($icon->fiction_image != '' && Storage::size($this->cartoonThumbImageUploadPath . $icon->fiction_image) > 0)  {
                        $fictionIcon[] = Storage::url($this->cartoonThumbImageUploadPath . $icon->fiction_image);
                    } else {
                        $fictionIcon[] = Storage::url($this->cartoonThumbImageUploadPath . 'proteen-logo.png');
                    }
                } else if ($icon->ti_icon_type == 2) {
                    if ($icon->nonfiction_image != '' && Storage::size($this->humanThumbImageUploadPath . $icon->nonfiction_image) > 0) {
                        $nonFiction[] = Storage::url($this->humanThumbImageUploadPath . $icon->nonfiction_image);
                    } else {
                        $nonFiction[] = Storage::url($this->humanThumbImageUploadPath . 'proteen-logo.png');
                    }
                } else {
                    if ($icon->ti_icon_image != '' && Storage::size($this->relationIconThumbImageUploadPath . $icon->ti_icon_image) > 0) {
                        $relationIcon[] = Storage::url($this->relationIconThumbImageUploadPath . $icon->ti_icon_image);
                    }
                }
            }
            $teenagerMyIcons = array_merge($fictionIcon, $nonFiction, $relationIcon);
        } else {
            $teenagerMyIcons = array();
        }
        $learningGuidance = Helpers::getCmsBySlug('learning-guidance-info');
        $myConnectionsCount = $this->communityRepository->getMyConnectionsCount($user->id);
        $myConnections = $this->communityRepository->getMyConnections($user->id);
        $myCareers = $this->professionsRepository->getMyCareersSlotWise($user->id);
        $myCareersCount = $this->professionsRepository->getMyCareersCount($user->id);
        return view('teenager.profile', compact('level1Activities', 'data', 'user', 'countries', 'sponsorDetail', 'teenSponsorIds', 'teenagerParents', 'teenagerMeta', 'teenagerMyIcons', 'learningGuidance', 'myConnectionsCount', 'myConnections', 'myCareers', 'myCareersCount'));   
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
            return Redirect::to("teenager/my-profile#profile-info")->withErrors("Date is invalid")->withInput();
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
        if($teenagerDetail['t_pincode'] != "") {
            $getLocation = json_decode(file_get_contents('http://maps.googleapis.com/maps/api/geocode/json?address='.$teenagerDetail['t_pincode'].'&sensor=true'));
            $getCityArea = ( isset($getLocation->results[0]->address_components[1]->long_name) && $getLocation->results[0]->address_components[1]->long_name != "" ) ? $getLocation->results[0]->address_components[1]->long_name : "Default";
        } else if ($teenagerDetail['t_country'] != "") {
            $country = $this->objCountry->find($teenagerDetail['t_country']);
            $getCityArea = $country->c_name;
        } else {
            $getCityArea = "Default";
        }
        $teenagerDetail['t_location'] = $getCityArea;
        $teenagerDetail['is_search_on'] = (isset($body['public_profile']) && $body['public_profile'] != '') ? $body['public_profile'] : '0';
        $teenagerDetail['is_share_with_other_members'] = (isset($body['share_with_members']) && $body['share_with_members'] != '') ? $body['share_with_members'] : '0';
        $teenagerDetail['is_share_with_parents'] = (isset($body['share_with_parents']) && $body['share_with_parents'] != '') ? $body['share_with_parents'] : '0';
        $teenagerDetail['is_share_with_teachers'] = (isset($body['share_with_teachers']) && $body['share_with_teachers'] != '') ? $body['share_with_teachers'] : '0';
        $teenagerDetail['is_notify'] = (isset($body['notifications']) && $body['notifications'] != '') ? $body['notifications'] : '0';
        $teenagerDetail['t_view_information'] = (isset($body['t_view_information']) && $body['t_view_information'] != '') ? $body['t_view_information'] : '0';
        $teenagerDetail['t_about_info'] = (isset($body['t_about_info']) && $body['t_about_info'] != '') ? $body['t_about_info'] : '';

        //Check all default field value -> If those are entered dummy by users
        if ($teenagerDetail['t_name'] == '' || $teenagerDetail['t_lastname'] == '' || $teenagerDetail['t_country'] == '' || $teenagerDetail['t_pincode'] == '' || $teenagerDetail['t_phone'] == '' || $t_email == '') {
            return Redirect::to("teenager/my-profile#profile-info")->withErrors(trans('validation.someproblems'))->withInput();
            exit;
        }
        if (!isset($body['selected_sponsor']) || count($body['selected_sponsor']) < 1) {
            return Redirect::to("teenager/my-profile#profile-info")->withErrors("Please select atleast one sponsor choice")->withInput();
            exit;
        }

        if (!in_array($teenagerDetail['t_gender'], array("1", "2"))) {
            return Redirect::to("teenager/my-profile#profile-info")->withErrors(trans('validation.someproblems'))->withInput();
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
            return Redirect::to("teenager/my-profile#profile-info")->withErrors(trans('appmessages.userwithsameemailaddress'))->withInput();
            exit;
        } else if ($teenagerMobileExist) {
            $response['message'] = trans('appmessages.userwithsamenumber');
            return Redirect::to("teenager/my-profile#profile-info")->withErrors(trans('appmessages.userwithsamenumber'))->withInput();
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
                return Redirect::to("teenager/my-profile#profile-info")->with('success', 'Profile updated successfully.');
            } else {
                return Redirect::to("teenager/my-profile#profile-info")->withErrors(trans('validation.somethingwrong'));
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
    public function savePair(TeenagerPairRequest $request) {
        $teenager = Auth::guard('teenager')->user();
        
        $parentDetail = [];
        $parentDetail['p_email'] = $request->parent_email;
        $parentDetail['p_user_type'] = $request->p_user_type;
        $parentDetail['deleted'] = '1';
        
        $parentTeenagerEmailExist = $this->teenagersRepository->checkActiveEmailExist($request->parent_email);
        if ($parentTeenagerEmailExist) {
            return Redirect::to("teenager/my-profile#sec-parents")->with('error', 'Same email already exist for teenager, Please use different one.')->withInput();
            exit;
        } else {
            $parentEmailExist = $this->parentsRepository->checkActiveEmailExist($request->parent_email);
            $checkPairAvailability = $getParentDetailByEmailId = [];
            
            if ($parentEmailExist) {
                $getParentDetailByEmailId = $this->parentsRepository->getParentDetailByEmailId($request->parent_email);
                $checkPairAvailability = $this->parentsRepository->checkPairAvailability($teenager->id, $getParentDetailByEmailId->id);
            }
            
            if ($checkPairAvailability && count($checkPairAvailability) > 0) {
                if ($checkPairAvailability->ptp_is_verified == 0) {
                    if ($checkPairAvailability->ptp_sent_by == "parent") {
                        $response['message'] = trans('Invitation already sent by them. Verification link emailed to you. Please, complete verification process.');
                    } else {
                        $response['message'] = trans('Invitation already sent by you. Verification link emailed to them. Please, complete verification process.');
                    }
                    return Redirect::to("teenager/my-profile#sec-parents")->with('error', $response['message'])->withInput();
                    exit;
                } else {
                    $response['message'] = trans('You already paired with this user');
                    return Redirect::to("teenager/my-profile#sec-parents")->with('error', $response['message'])->withInput();
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
                    $replaceArray['PARENT_LOGIN_URL'] = url("parent/login");
                }else{
                    $replaceArray['PARENT_SET_PROFILE_URL'] = url("counselor/set-profile");
                    $replaceArray['PARENT_LOGIN_URL'] = url("counselor/login");
                }
                $replaceArray['PARENT_EMAIL'] = $parentData['p_email'];
                $replaceArray['PARENT_PASSWORD'] = "********"; //bcrypt(str_random(10));
                $replaceArray['PARENT_UNIQUEID'] = Helpers::getParentUniqueId();
                $replaceArray['VERIFICATION_URL'] = url("parent/verify-parent-teen-pair-registration?token=" . $replaceArray['PARENT_UNIQUEID']);
                $replaceArray['USERNAME'] = ucwords(Auth::guard('teenager')->user()->t_name." ".Auth::guard('teenager')->user()->t_lastname);
                
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
                
                Event::fire(new SendMail("emails.Template", $data));
                
                $parentTeenVerificationData['ptp_parent_id'] = $data['parent_id'];
                $parentTeenVerificationData['ptp_teenager'] = $data['teen_id'];
                $parentTeenVerificationData['ptp_is_verified'] = 0;
                $parentTeenVerificationData['ptp_sent_by'] = 'teen';
                $parentTeenVerificationData['ptp_token'] = $data['parent_token'];

                $this->teenagersRepository->saveParentTeenVerification($parentTeenVerificationData);

                // Mail::send(['html' => 'emails.Template'], $data, function($message) use ($data) {
                //     $message->subject($data['subject']);
                //     $message->to($data['toEmail'], $data['toName']);

                //     // Save parent-teen id in verification table
                    
                // });
                // ------------------------end sending mail ----------------------------//
                return Redirect::to("teenager/my-profile#sec-parents")->with('success', 'Your invitation has been sent successfully.');
                exit; 
            }
        }
    }

    //Set profile form
    public function setProfile()
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
        return view('teenager.setUpProfile', compact('level1Activities', 'data', 'user', 'countries', 'sponsorDetail', 'teenSponsorIds', 'teenagerParents', 'teenagerMeta'));   
    }

    //My careers data
    public function loadMoreMyCareers() 
    {
        $lastAttemptedId = Input::get('lastAttemptedId');
        $loggedInTeen = Auth::guard('teenager')->user()->id;
        $myCareers = $this->professionsRepository->getMyCareersSlotWise($loggedInTeen, $lastAttemptedId);
        $myCareersCount = $this->professionsRepository->getMyCareersCount($loggedInTeen, $lastAttemptedId);
        return view('teenager.loadMoreCareers', compact('myCareers', 'myCareersCount'));
        
    }

    //Calculate teenager strength and interest score percentage
    public function getTeenScoreInPercentage($maxScore, $teenScore) 
    {
        if ($teenScore > $maxScore) {
            $teenScore = $maxScore;
        }
        $mul = 100*$teenScore;
        $percentage = $mul/$maxScore;
        return round($percentage);
    }

    //My Network Data
    public function getMyNetworkDetails()
    {
        $loggedInTeen = Auth::guard('teenager')->user()->id;
        $searchConnections = Input::get('searchConnections');
        $filterBy = Input::get('filter_by');
        $filterOption = Input::get('filter_option');
        $connectionsCount = $this->communityRepository->getMyConnectionsCount($loggedInTeen);
        if ((isset($searchConnections) && !empty($searchConnections)) || (isset($filterOption) && !empty($filterOption) && isset($filterBy) && !empty($filterBy))) {
            if (isset($filterBy) && !empty($filterBy) && $filterBy == 't_age') {
                $filterBy = 't_birthdate';
                if (strpos($filterOption, '-') !== false) {
                    $ageArr = explode("-", $filterOption);
                    $toDate = Carbon::now()->subYears($ageArr[0]);
                    $fromDate = Carbon::now()->subYears($ageArr[1]);
                    $filterOptionArr['fromDate'] = $fromDate->format('Y-m-d');
                    $filterOptionArr['toDate'] = $toDate->format('Y-m-d');
                    $filterOption = $filterOptionArr;
                } 
            }
            $memberDetails = $this->communityRepository->getMyConnections($loggedInTeen, $searchConnections, '', $filterBy, $filterOption, 1);
            return view('teenager.searchedNetwork', compact('memberDetails', 'connectionsCount'));
        } else {
            $memberDetails = $this->communityRepository->getMyConnections($loggedInTeen, $searchConnections, '', '', '', 1);
            return view('teenager.network', compact('memberDetails', 'connectionsCount'));
        }
    }
}
