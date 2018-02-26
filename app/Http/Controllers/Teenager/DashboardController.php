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
use DB;

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
        $profileMessage = "Welcome to ProTeen";
        //Profile completion calculation
        // if ($user->t_progress_calculations > 0) {
        //     $profileMessage = "You advanced " . $user->t_progress_calculations . "% on your last visit";
        // } else {
        //     $profileMessage = "Welcome to the ProTeen";
        // }
        $data['user_profile'] = (Auth::guard('teenager')->user()->t_photo != "" && Storage::size($this->teenOriginalImageUploadPath.Auth::guard('teenager')->user()->t_photo) > 0) ? Storage::url($this->teenOriginalImageUploadPath.Auth::guard('teenager')->user()->t_photo) : Storage::url($this->teenOriginalImageUploadPath.'proteen-logo.png');
        $data['user_profile_thumb'] = (Auth::guard('teenager')->user()->t_photo != "" && Storage::size($this->teenThumbImageUploadPath.Auth::guard('teenager')->user()->t_photo) > 0) ? Storage::url($this->teenThumbImageUploadPath.Auth::guard('teenager')->user()->t_photo) : Storage::url($this->teenThumbImageUploadPath.'proteen-logo.png');
        
        $basicBoosterPoint = $this->teenagersRepository->getTeenagerBasicBooster($user->id);
        
        $section1Collection = $this->Level2ActivitiesRepository->getNoOfTotalQuestionsAttemptedQuestionBySection($user->id,1);
        $section2Collection = $this->Level2ActivitiesRepository->getNoOfTotalQuestionsAttemptedQuestionBySection($user->id,2);
        $section3Collection = $this->Level2ActivitiesRepository->getNoOfTotalQuestionsAttemptedQuestionBySection($user->id,3);
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

        $getAllActiveProfessions = Helpers::getActiveProfessions();
        $teenagerCareersIds = (isset($teenagerCareers[0]) && count($teenagerCareers[0]) > 0) ? Helpers::getTeenagerCareersIds($user->id)->toArray() : [];

        $careerConsideration = [];
        $match = $nomatch = $moderate = [];
        // if($getAllActiveProfessions) {
        //     foreach($getAllActiveProfessions as $profession) {
        //         $array = [];
        //         $array['id'] = $profession->id;
        //         $array['match_scale'] = isset($getTeenagerHML[$profession->id]) ? $getTeenagerHML[$profession->id] : '';
        //         $array['added_my_career'] = (in_array($profession->id, $teenagerCareersIds)) ? 1 : 0;
        //         $array['pf_name'] = $profession->pf_name;
        //         $array['pf_slug'] = $profession->pf_slug;
        //         if($array['match_scale'] == "match") {
        //             $match[] = $array;
        //         } else if($array['match_scale'] == "nomatch") {
        //             $nomatch[] = $array;
        //         } else if($array['match_scale'] == "moderate") {
        //             $moderate[] = $array;
        //         } else {
        //             $notSetArray[] = $array;
        //         }
        //     }
        //     if(count($match) < 1 && count($moderate) < 1) {
        //         $careerConsideration = $nomatch;
        //     } else if(count($match) > 0 || count($moderate) > 0) {
        //         $careerConsideration = array_merge($match, $moderate);
        //     } else {
        //         $careerConsideration = $notSetArray;
        //     }
        // }

        $adsDetails = Helpers::getAds($user->id);
        $advertisements = [];
        foreach ($adsDetails as $ad) {
            if ($ad['sizeType'] == 4) {
                if ($ad['image'] != '') {
                    $ad['image'] = Storage::url(Config::get('constant.SA_ORIGINAL_IMAGE_UPLOAD_PATH') . $ad['image']);
                } else {
                    $ad['image'] = Storage::url(Config::get('constant.SA_ORIGINAL_IMAGE_UPLOAD_PATH') . 'proteen-logo.png');
                }
                $advertisements[] = $ad;
            } 
        }

        return view('teenager.home', compact('basicBoosterPoint', 'careerConsideration', 'getTeenagerHML' ,'secComplete3', 'secComplete2', 'secComplete1', 'data', 'user', 'section1','section2','section3', 'teenagerNetwork', 'teenThumbImageUploadPath', 'teenagerCareers', 'advertisements', 'profileMessage'));
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
