<?php

namespace App\Http\Controllers\Webservice;

use App\Http\Controllers\Controller;
use App\Services\Teenagers\Contracts\TeenagersRepository;
use App\Services\Professions\Contracts\ProfessionsRepository;
use Config;
use Storage;
use Helpers;  
use Auth;
use Input;
use Redirect;
use Illuminate\Http\Request;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use App\SponsorsActivity;
use App\TeenagerScholarshipProgram;
use App\TeenParentChallenge;
use App\ProfessionLearningStyle;
use App\UserLearningStyle;
use App\Services\Parents\Contracts\ParentsRepository;
use App\Services\Template\Contracts\TemplatesRepository;
use App\Services\Level4Activity\Contracts\Level4ActivitiesRepository;
use Mail;
use App\PromiseParametersMaxScore;
use App\TeenagerPromiseScore;
use App\MultipleIntelligentScale;
use App\ApptitudeTypeScale;
use App\PersonalityScale;
use App\MultipleIntelligent;
use App\Apptitude;
use App\Personality;
use App\Professions;
use App\TemplateDeductedCoins;
use App\Jobs\CalculateProfessionCompletePercentage;
use App\PromisePlus;
use App\PaidComponent;
use App\Level4ProfessionProgress;
use App\Level4Answers;
use App\LearningStyle;

class Level4ActivityController extends Controller {

    public function __construct(Level4ActivitiesRepository $level4ActivitiesRepository, TeenagersRepository $teenagersRepository, ProfessionsRepository $professionsRepository, ParentsRepository $parentsRepository, TemplatesRepository $templatesRepository) 
    {
        $this->teenagersRepository = $teenagersRepository;
        $this->professionsRepository = $professionsRepository;
        $this->level4ActivitiesRepository = $level4ActivitiesRepository;
        $this->objSponsorsActivity = new SponsorsActivity; 
        $this->objTeenagerScholarshipProgram = new TeenagerScholarshipProgram;
        $this->objTeenParentChallenge = new TeenParentChallenge;
        $this->parentsRepository = $parentsRepository;
        $this->templatesRepository = $templatesRepository;
        $this->objPromiseParametersMaxScore = new PromiseParametersMaxScore;
        $this->objTeenagerPromiseScore = new TeenagerPromiseScore;
        $this->objMultipleIntelligent = new MultipleIntelligent;
        $this->objApptitude = new Apptitude;
        $this->objPersonality = new Personality;
        $this->objMIScale = new MultipleIntelligentScale();
        $this->objApptitudeScale = new ApptitudeTypeScale();
        $this->objPersonalityScale = new PersonalityScale();
        $this->professions = new Professions();
        $this->log = new Logger('api-level4-activity-controller');
        $this->log->pushHandler(new StreamHandler(storage_path().'/logs/monolog-'.date('m-d-Y').'.log'));    
        $this->extraQuestionDescriptionTime = Config::get('constant.EXTRA_QUESTION_DESCRIPTION');
        $this->questionDescriptionORIGINALImage = Config::get('constant.LEVEL4_INTERMEDIATE_QUESTION_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->questionDescriptionTHUMBImage = Config::get('constant.LEVEL4_INTERMEDIATE_QUESTION_THUMB_IMAGE_UPLOAD_PATH');
        $this->optionORIGINALImage = Config::get('constant.LEVEL4_INTERMEDIATE_ANSWER_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->optionTHUMBImage = Config::get('constant.LEVEL4_INTERMEDIATE_ANSWER_THUMB_IMAGE_UPLOAD_PATH');
        $this->answerResponseImageOriginal = Config::get('constant.LEVEL4_INTERMEDIATE_RESPONSE_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->objPromisePlus = new PromisePlus();
        $this->objLevel4ProfessionProgress = new Level4ProfessionProgress;
        $this->learningStyleThumbImageUploadPath = Config::get('constant.LEARNING_STYLE_THUMB_IMAGE_UPLOAD_PATH');
    }

    /* Request Params : getScholarshipProgramsDetails
     *  loginToken, userId
     */
    public function getScholarshipProgramsDetails(Request $request) 
    {
        $response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg') ] ;
        $teenager = $this->teenagersRepository->getTeenagerById($request->userId);
        if ($teenager) {
            //Get tenager sponsor's list
            $teenagerSponsors = $this->teenagersRepository->getTeenagerSelectedSponsor($request->userId);
            $sponsorArr = [];
            if (isset($teenagerSponsors) && count($teenagerSponsors) > 0) {
                foreach ($teenagerSponsors as $key => $val) {
                    $sponsorArr[] = $val->ts_sponsor;
                }
            }
            //Get scholarship programs list
            $scholarshipPrograms = [];
            if (!empty($sponsorArr)) {
                if (!empty($sponsorArr)) {
                    $scholarshipPrograms = $this->objSponsorsActivity->getActivityByTypeAndSponsor($sponsorArr, 3);
                }
            }
            //Get list for applied scholarship programs
            $appliedScholarshipDetails = $this->objTeenagerScholarshipProgram->getAllScholarshipProgramsByTeenId($request->userId);
            $scholarshipProgramIds = [];
            if (isset($appliedScholarshipDetails) && count($appliedScholarshipDetails) > 0) {
                foreach ($appliedScholarshipDetails as $appliedScholarshipDetail) {
                    $scholarshipProgramIds[] = $appliedScholarshipDetail->tsp_activity_id;
                }
            }
            //Get list for expired scholarship programs
            $expiredScholarshipPrograms = $this->objSponsorsActivity->getExpiredActivityByTypeAndSponsor($sponsorArr, 3);
            $expiredActivityIds = [];
            if (isset($expiredScholarshipPrograms) && count($expiredScholarshipPrograms) > 0) {
                foreach ($expiredScholarshipPrograms as $expiredScholarshipProgram) {
                    $expiredActivityIds[] = $expiredScholarshipProgram->id;
                }
            }
            $exceptScholarshipIds = array_unique(array_merge($scholarshipProgramIds, $expiredActivityIds));
            $scholarshipDetailsArr = [];
            foreach ($scholarshipPrograms as $scholarshipProgram) {
                $scholarshipData = [];
                $scholarshipData['id'] = $scholarshipProgram->id;
                if ($scholarshipProgram->sa_image != "" && Storage::size(Config::get('constant.SA_ORIGINAL_IMAGE_UPLOAD_PATH') . $scholarshipProgram->sa_image) > 0) {
                    $scholarshipData['image'] = Storage::url(Config::get('constant.SA_THUMB_IMAGE_UPLOAD_PATH') . $scholarshipProgram->sa_image);
                } else {
                    $scholarshipData['image'] = Storage::url(Config::get('constant.SA_THUMB_IMAGE_UPLOAD_PATH') . 'proteen-logo.png');
                }
                $scholarshipData['companyName'] = $scholarshipProgram->sp_company_name;
                $scholarshipData['title'] = $scholarshipProgram->sa_name;
                $scholarshipData['details'] = $scholarshipProgram->sa_description;
                $scholarshipData['learnMoreLink'] = url('teenager/learnMoreL4');
                $scholarshipData['is_expired'] = (!empty($expiredActivityIds) && in_array($scholarshipProgram->id, $expiredActivityIds)) ? 1 : 0;
                $scholarshipData['is_applied'] = (!empty($scholarshipProgramIds) && in_array($scholarshipProgram->id, $scholarshipProgramIds)) ? 1 : 0;
                if (!empty($expiredActivityIds) && in_array($scholarshipProgram->id, $expiredActivityIds)) {
                    $scholarshipData['activityStatus'] = "expired";
                } else {
                    if (!empty($appliedScholarshipDetails) && in_array($scholarshipProgram->id, $scholarshipProgramIds)) {
                        $scholarshipData['activityStatus'] = "applied";
                    } else {
                        $scholarshipData['activityStatus'] = "apply";
                    }
                }
                $scholarshipDetailsArr[] = $scholarshipData;
            }
            //Store log in System
            $this->log->info('Retrieve scholarship programs details', array('userid'=>$request->userId));
            $data['information'] = "Instructions: Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur congue velit vel nisi vulputate, eu faucibus eros porttitor. Nam nec placerat nunc. Suspendisse scelerisque luctus libero, ut tincidunt mi. Fusce quis tincidunt justo, at bibendum lorem.";
            $data['is_active'] = rand(0, 1);
            $data['scholarshipPrograms'] = $scholarshipDetailsArr;
            $response['login'] = 1;
            $response['status'] = 1;
            $response['message'] = trans('appmessages.default_success_msg');
            $response['data'] = $data;
        } else {
            $response['message'] = trans('appmessages.invalid_userid_msg') . ' or ' . trans('appmessages.notvarified_user_msg');
        }
        return response()->json($response, 200);
        exit;
    }

    /* Request Params : applyForScholarshipProgram
     *  loginToken, userId, activityId
     */
    public function applyForScholarshipProgram(Request $request) 
    {
        $response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg') ] ;
        $teenager = $this->teenagersRepository->getTeenagerById($request->userId);
        if ($teenager) {
            if (isset($request->activityId) && !empty($request->activityId)) {
                $activityDetails = [];
                $activityDetails['tsp_activity_id'] = $request->activityId;
                $activityDetails['tsp_teenager_id'] = $request->userId;
                //Check if user already applied for this scholarship program
                $checkIfAlreadyApplied = $this->objTeenagerScholarshipProgram->getScholarshipProgramDetailsByActivity($activityDetails);
                if (isset($checkIfAlreadyApplied) && !empty($checkIfAlreadyApplied)) {
                    $response['message'] = "You have already applied for this scholarship program";
                    //Store log in System
                    $this->log->info('User already registered for scholarship program.', array('userId'=>$request->userId, 'scholarshipId' => $request->activityId));
                } else {
                    //Store scholarship application details
                    $appliedForScholarship = $this->objTeenagerScholarshipProgram->StoreDetailsForScholarshipProgram($activityDetails);
                    if (isset($appliedForScholarship) && !empty($appliedForScholarship)) {
                        $response['message'] = "You have successfully applied for this scholarship program";
                        //Store log in System
                        $this->log->info('User successfully registered for scholarship program', array('userId' => $request->userId, 'scholarshipId' => $request->activityId));
                    } else {
                        $response['message'] = "Failed to applied for this scholarship program, Please try again later";
                        //Store log in System
                        $this->log->info('User failed to registered for scholarship program', array('userId' => $request->userId, 'scholarshipId' => $request->activityId));
                    } 
                }
                $response['status'] = 1;
            } else {
               $response['message'] = trans('appmessages.missing_data_msg'); 
               $response['status'] = 0;
            }
            $response['login'] = 1;
        } else {
            $response['message'] = trans('appmessages.invalid_userid_msg') . ' or ' . trans('appmessages.notvarified_user_msg');
        }
        return response()->json($response, 200);
        exit;
    }

    /* Request Params : getParentAndMentorListForChallengePlay
     *  loginToken, userId, professionId
     */
    public function getParentAndMentorListForChallengePlay(Request $request) 
    {
        $response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg') ] ;
        $teenager = $this->teenagersRepository->getTeenagerById($request->userId);
        if ($teenager) {
            if (isset($request->professionId) && !empty($request->professionId)) {
                $teenagerParents = $this->teenagersRepository->getTeenParents($request->userId);
                $parentArr = [];
                foreach ($teenagerParents as $teenagerParent) {
                    $parentData = [];
                    $parentData['id'] = $teenagerParent->id;
                    $parentData['firstname'] = $teenagerParent->p_first_name;
                    $parentData['lastname'] = $teenagerParent->p_last_name;
                    $parentData['email'] = $teenagerParent->p_email;
                    $parentArr[] = $parentData;
                }
                $challengedParents = $this->objTeenParentChallenge->getChallengedParentAndMentorList($request->professionId, $request->userId);
                $challengedParentsArr = [];
                foreach ($challengedParents as $challengedParent) {
                    $challengedParentsList = [];
                    $challengedParentsList['id'] = $challengedParent->parentId;
                    $challengedParentsList['firstname'] = $challengedParent->p_first_name;
                    $challengedParentsList['lastname'] = $challengedParent->p_last_name;
                    if (isset($challengedParent->p_photo) && $challengedParent->p_photo != '' && Storage::size(Config::get('constant.PARENT_THUMB_IMAGE_UPLOAD_PATH') . $challengedParent->p_photo) > 0) {
                        $challengedParentsList['photo'] = Storage::url(Config::get('constant.PARENT_THUMB_IMAGE_UPLOAD_PATH') . $challengedParent->p_photo);
                    } else {
                        $challengedParentsList['photo'] = Storage::url(Config::get('constant.PARENT_THUMB_IMAGE_UPLOAD_PATH') . "proteen-logo.png");
                    }
                    $challengedParentsArr[] = $challengedParentsList;
                }
                //Store log in System
                $this->log->info('Retrieve challenge play section data', array('userId'=>$request->userId));
                $data['information'] = "Instructions: Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur congue velit vel nisi vulputate, eu faucibus eros porttitor. Nam nec placerat nunc. Suspendisse scelerisque luctus libero, ut tincidunt mi. Fusce quis tincidunt justo, at bibendum lorem.";
                $data['parentList'] = $parentArr;
                $data['challengedParentList'] = $challengedParentsArr;
                $response['status'] = 1;
                $response['message'] = trans('appmessages.default_success_msg');
                $response['data'] = $data;
            } else {
                $response['status'] = 0;
                $response['message'] = trans('appmessages.missing_data_msg'); 
            }
            $response['login'] = 1;
        } else {
            $response['message'] = trans('appmessages.invalid_userid_msg') . ' or ' . trans('appmessages.notvarified_user_msg');
        }
        return response()->json($response, 200);
        exit;
    }

    /* Request Params : challengeToParentOrMentorForProfession
     *  loginToken, userId, parentId, professionId
     */
    public function challengeToParentOrMentorForProfession(Request $request) 
    {
        $response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg') ] ;
        $teenager = $this->teenagersRepository->getTeenagerById($request->userId);
        if ($teenager) {
            if (isset($request->professionId) && !empty($request->professionId) && isset($request->parentId) && !empty($request->parentId)) {
                

                $saveData = [];
                $saveData['tpc_teenager_id'] = $request->userId;
                $saveData['tpc_parent_id'] = $request->parentId;
                $saveData['tpc_profession_id'] = $request->professionId;
                $result = $this->objTeenParentChallenge->getTeenParentRequestDetail($saveData);
                if (isset($result) && $result) {
                   $response['status'] = 0;
                    $response['message'] = trans('labels.parentchallengeexist');
                } else {
                    $this->objTeenParentChallenge->saveTeenParentRequestDetail($saveData);
                    $teenDetail = $this->teenagersRepository->getTeenagerByTeenagerId($request->userId);
                    $parentDetail = $this->parentsRepository->getParentDetailByParentId($request->parentId);
                    $professionName =  $this->professionsRepository->getProfessionNameById($request->professionId);
                    //send mail
                    $replaceArray = array();
                    $replaceArray['USER_NAME'] = $parentDetail['p_first_name'];
                    $replaceArray['TEEN_NAME'] = $teenDetail['t_name'];
                    $replaceArray['PROFESSION_NAME'] = $professionName;
                    $emailTemplateContent = $this->templatesRepository->getEmailTemplateDataByName(Config::get('constant.PARENT_TEEN_CHALLEGE_REQUEST_TEMPLATE'));
                    $content = $this->templatesRepository->getEmailContent($emailTemplateContent->et_body, $replaceArray);
                    $mailData = array();
                    $mailData['subject'] = $emailTemplateContent->et_subject;
                    $mailData['toEmail'] = $parentDetail['p_email'];
                    $mailData['toName'] = $parentDetail['p_first_name'];
                    $mailData['content'] = $content;
                    Mail::send(['html' => 'emails.Template'], $mailData , function ($m) use ($mailData) {
                        $m->from(Config::get('constant.FROM_MAIL_ID'), 'Teen Challenge ');
                        $m->subject($mailData['subject']);
                        $m->to($mailData['toEmail'], $mailData['toName']);
                    });
                    $challengedParents = $this->objTeenParentChallenge->getChallengedParentAndMentorList($request->professionId, $request->userId);
                    $challengedParentsArr = [];
                    foreach ($challengedParents as $challengedParent) {
                        $challengedParentsList = [];
                        $challengedParentsList['id'] = $challengedParent->parentId;
                        $challengedParentsList['firstname'] = $challengedParent->p_first_name;
                        $challengedParentsList['lastname'] = $challengedParent->p_last_name;
                        if (isset($challengedParent->p_photo) && $challengedParent->p_photo != '' && Storage::size(Config::get('constant.PARENT_THUMB_IMAGE_UPLOAD_PATH') . $challengedParent->p_photo) > 0) {
                            $challengedParentsList['photo'] = Storage::url(Config::get('constant.PARENT_THUMB_IMAGE_UPLOAD_PATH') . $challengedParent->p_photo);
                        } else {
                            $challengedParentsList['photo'] = Storage::url(Config::get('constant.PARENT_THUMB_IMAGE_UPLOAD_PATH') . "proteen-logo.png");
                        }
                        $challengedParentsArr[] = $challengedParentsList;
                    }
                    //Store log in System
                    $this->log->info('Teenager challenged to parent/mentor for profession', array('teenId' => $request->userId, 'parentId' => $request->parentId, 'professionId' => $request->professionId));
                    $data['challengedParentList'] = $challengedParentsArr;
                    $response['status'] = 1;
                    $response['message'] = trans('labels.parentchallengesuccess');
                    $response['data'] = $data;
                }
            } else {
                $response['status'] = 0;
                $response['message'] = trans('appmessages.missing_data_msg'); 
            }
            $response['login'] = 1;
        } else {
            $response['message'] = trans('appmessages.invalid_userid_msg') . ' or ' . trans('appmessages.notvarified_user_msg');
        }
        return response()->json($response, 200);
        exit;
    }

    /* Request Params : getCareerPageAdvanceViewDetails
     *  loginToken, userId
     */
    public function getCareerPageAdvanceViewDetails(Request $request) 
    {
        $response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg') ] ;
        $teenager = $this->teenagersRepository->getTeenagerById($request->userId);
        if ($teenager) {
            $teenagerStrength = $arraypromiseParametersMaxScoreBySlug = [];
            //Get Max score for MI parameters
            $promiseParametersMaxScore = $this->objPromiseParametersMaxScore->getPromiseParametersMaxScore();
            $countryId = ($teenager->t_view_information == 1) ? 2 : 1;
            $professionsData = $this->professions->getProfessionBySlugWithHeadersAndCertificatesAndTags($request->careerSlug, $countryId, $request->userId);
            
            $professionPromiseParameters = Helpers::getCareerMapColumnName();
            
            $arraypromiseParametersMaxScore = $promiseParametersMaxScore->toArray();
            foreach($arraypromiseParametersMaxScore as $maxkey => $maxVal){
                $arraypromiseParametersMaxScoreBySlug[$maxVal['parameter_slug']] = $maxVal;
            }
            //Get teenager promise score 
            $teenPromiseScore = $this->objTeenagerPromiseScore->getTeenagerPromiseScore($request->userId);
            if(isset($teenPromiseScore) && count($teenPromiseScore) > 0) {
                $teenPromiseScore = $teenPromiseScore->toArray();
                foreach($teenPromiseScore as $paramkey=>$paramvalue)
                {                    
                    if (strpos($paramkey, 'apt_') !== false) { 
                        $careerMappingKey = $professionPromiseParameters[$paramkey];
                        $careerMappingHML = $professionsData->careerMapping->$careerMappingKey;
                        //get aptitude detail 
                        $aptitudeDetail =  $this->objApptitude->getApptitudeDetailBySlug($paramkey); 
                        $aptituteScale = $this->objApptitudeScale->getApptitudeScaleById($aptitudeDetail->id);
                        if($careerMappingHML == 'H'){
                            if($aptituteScale['ats_high_min_score'] == $aptituteScale['ats_high_max_score']){
                                $blueBand = $aptituteScale['ats_moderate_max_score'];
                            }else{
                                $blueBand = $aptituteScale['ats_high_max_score']; 
                            }  
                        }elseif($careerMappingHML == 'M')
                        {
                            $blueBand = $aptituteScale['ats_moderate_max_score'];
                        }else{
                            $blueBand = $aptituteScale['ats_low_max_score'];
                        }
                       
                        $teenAptScore = $this->getTeenScoreInPercentage($arraypromiseParametersMaxScoreBySlug[$paramkey]['parameter_max_score'], $paramvalue);
                        $teenagerStrength[] = (array('earnedScore' => $teenAptScore, 'name' => $arraypromiseParametersMaxScoreBySlug[$paramkey]['parameter_name'], 'type' => Config::get('constant.APPTITUDE_TYPE'), 'lowscoreOfH' => ((100*$blueBand)/$arraypromiseParametersMaxScoreBySlug[$paramkey]['parameter_max_score'])));
                    }elseif(strpos($paramkey, 'pt_') !== false){
                        $careerMappingKey = $professionPromiseParameters[$paramkey];
                        $careerMappingHML = $professionsData->careerMapping->$careerMappingKey;
                        //get personality detail 
                        $personalityDetail =  $this->objPersonality->getPersonalityDetailBySlug($paramkey); 
                        $personalityScale = $this->objPersonalityScale->getPersonalityScaleById($personalityDetail->id);
                        if($careerMappingHML == 'H'){
                            if($personalityScale['pts_high_min_score'] == $personalityScale['pts_high_max_score']){
                                $blueBand = $personalityScale['pts_moderate_max_score'];
                            }else{
                                $blueBand = $personalityScale['pts_high_max_score']; 
                            }  
                        }elseif($careerMappingHML == 'M')
                        {
                            $blueBand = $personalityScale['pts_moderate_max_score'];
                        }else{
                            $blueBand = $personalityScale['pts_low_max_score'];
                        }
                        $teenAptScore = $this->getTeenScoreInPercentage($arraypromiseParametersMaxScoreBySlug[$paramkey]['parameter_max_score'], $paramvalue);
                        $teenagerStrength[] = (array('earnedScore' => $teenAptScore, 'name' => $arraypromiseParametersMaxScoreBySlug[$paramkey]['parameter_name'], 'type' => Config::get('constant.PERSONALITY_TYPE'), 'lowscoreOfH' => ((100*$blueBand)/$arraypromiseParametersMaxScoreBySlug[$paramkey]['parameter_max_score'])));               
                    }elseif(strpos($paramkey, 'mit_') !== false){
                        $careerMappingKey = $professionPromiseParameters[$paramkey];
                        $careerMappingHML = $professionsData->careerMapping->$careerMappingKey;
                        //get MI detail 
                        $miDetail =  $this->objMultipleIntelligent->getMultipleIntelligenceDetailBySlug($paramkey); 
                        $miScale = $this->objMIScale->getMIScaleById($miDetail->id);
                        if($careerMappingHML == 'H'){
                            if($miScale['mts_high_min_score'] == $miScale['mts_high_max_score']){
                                $blueBand = $miScale['mts_moderate_max_score'];
                            }else{
                                $blueBand = $miScale['mts_high_max_score']; 
                            }  
                        }elseif($careerMappingHML == 'M')
                        {
                            $blueBand = $miScale['mts_moderate_max_score'];
                        }else{
                            $blueBand = $miScale['mts_low_max_score'];
                        }
                        $teenAptScore = $this->getTeenScoreInPercentage($arraypromiseParametersMaxScoreBySlug[$paramkey]['parameter_max_score'], $paramvalue);
                        $teenagerStrength[] = (array('earnedScore' => $teenAptScore, 'name' => $arraypromiseParametersMaxScoreBySlug[$paramkey]['parameter_name'], 'type' => Config::get('constant.MULTI_INTELLIGENCE_TYPE'), 'lowscoreOfH' => ((100*$blueBand)/$arraypromiseParametersMaxScoreBySlug[$paramkey]['parameter_max_score'])));
                    }
                }
            }
            //Store log in System
            $this->log->info('User retrieve career advance view details', array('userid' => $request->userId));
            $response['login'] = 1;
            $response['status'] = 1;
            $response['message'] = trans('appmessages.default_success_msg');
            $response['data'] = $teenagerStrength;
        } else {
            $response['message'] = trans('appmessages.invalid_userid_msg') . ' or ' . trans('appmessages.notvarified_user_msg');
        }
        return response()->json($response, 200);
        exit;
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

    /* Request Params : getTeenParentChallengeScoreDetails
     *  loginToken, userId, parentId, careerId
     */
    public function getTeenParentChallengeScoreDetails(Request $request) 
    {
        $response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg') ] ;
        $teenager = $this->teenagersRepository->getTeenagerById($request->userId);
        if ($teenager) {
            if (isset($request->careerId) && !empty($request->careerId) && isset($request->parentId) && !empty($request->parentId)) {
                $professionId = $request->careerId;
                $parentId = $request->parentId;
                $teenId = $request->userId;
                $getProfessionNameFromProfessionId = $this->professionsRepository->getProfessionsByProfessionId($professionId);
                $data['careerId'] = $professionId; 
                $data['careerName'] = (isset($getProfessionNameFromProfessionId[0]) && !empty($getProfessionNameFromProfessionId[0])) ? $getProfessionNameFromProfessionId[0]->pf_name : '';  
                $level4Booster = Helpers::level4Booster($professionId, $teenId);
                $level4ParentBooster = Helpers::level4ParentBooster($professionId, $parentId);
                $teenDetail = $this->teenagersRepository->getTeenagerByTeenagerId($teenId);
                $parentDetail = $this->parentsRepository->getParentDetailByParentId($parentId);

                $level4ParentBooster['yourRank'] = 0;
                foreach($level4Booster['allData'] AS $key => $value) {
                    if ($level4ParentBooster['yourScore'] != 0) {
                        if ($level4ParentBooster['yourScore'] == $value) {
                            $level4ParentBooster['yourRank'] = $key+1;
                        } 
                    }   
                }
                $rank = 0;
                foreach($level4Booster['allData'] AS $key => $value) {
                    if ($level4Booster['yourScore'] != 0) {
                        if ($level4Booster['yourScore'] == $value) {
                            $rank = $key + 1;
                        } 
                    } 
                }

                //Parent details
                $parentDetailsArr = [];
                $parentDetailsArr['id'] = $parentDetail->id;
                $parentDetailsArr['name'] = $parentDetail->p_first_name;
                if (isset($parentDetail->p_photo) && $parentDetail->p_photo != '' && Storage::size(Config::get('constant.PARENT_THUMB_IMAGE_UPLOAD_PATH') . $parentDetail->p_photo) > 0) {
                    $parentDetailsArr['parentPhoto'] = Storage::url(Config::get('constant.PARENT_THUMB_IMAGE_UPLOAD_PATH') . $parentDetail->p_photo);
                } else {
                    $parentDetailsArr['parentPhoto'] = Storage::url(Config::get('constant.PARENT_THUMB_IMAGE_UPLOAD_PATH') . "proteen-logo.png");
                }
                $parentDetailsArr['score'] = $level4ParentBooster['yourScore'];
                $parentDetailsArr['rank'] = $level4ParentBooster['yourRank'];
                $parentDetailsArr['parentPoints'] = $level4ParentBooster['yourScore'];
                $parentDetailsArr['parentTotalPoints'] = $level4ParentBooster['totalPobScore'];
                $data['parentDetails'] = $parentDetailsArr;

                //Teenager details
                $teenDetailsArr = [];
                $teenDetailsArr['id'] = $teenDetail['id'];
                $teenDetailsArr['name'] = $teenDetail['t_name'];
                if (isset($teenDetail['t_photo']) && $teenDetail['t_photo'] != '' && Storage::size(Config::get('constant.TEEN_THUMB_IMAGE_UPLOAD_PATH') . $teenDetail['t_photo']) > 0) {
                    $teenDetailsArr['teenPhoto'] = Storage::url(Config::get('constant.TEEN_THUMB_IMAGE_UPLOAD_PATH') . $teenDetail['t_photo']);
                } else {
                    $teenDetailsArr['teenPhoto'] = Storage::url(Config::get('constant.TEEN_THUMB_IMAGE_UPLOAD_PATH') . "proteen-logo.png");
                }
                $teenDetailsArr['teenScore'] = $level4Booster['yourScore'];
                $teenDetailsArr['teenRank'] = $rank;
                $teenDetailsArr['teenPoints'] = $level4Booster['yourScore'];
                $teenDetailsArr['teenTotalPoints'] = $level4Booster['totalPobScore'];
                $data['teenagerDetails'] = $teenDetailsArr;

                //Store log in System
                $this->log->info('Teenager retrieve challenge score details', array('teenId' => $request->userId, 'parentId' => $request->parentId, 'professionId' => $request->professionId));
                $response['status'] = 1;
                $response['message'] = trans('labels.parentchallengesuccess');
                $response['data'] = $data;
            } else {
                $response['status'] = 0;
                $response['message'] = trans('appmessages.missing_data_msg'); 
            }
            $response['login'] = 1;
        } else {
            $response['message'] = trans('appmessages.invalid_userid_msg') . ' or ' . trans('appmessages.notvarified_user_msg');
        }
        return response()->json($response, 200);
        exit;
    }

    /* Request Params : getLevel4BasicQuestions
     *  loginToken, userId, professionId
     */
    public function getLevel4BasicQuestions(Request $request) 
    {
        $response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg') ] ;
        $teenager = $this->teenagersRepository->getTeenagerById($request->userId);
        if ($teenager) {
            if (isset($request->professionId) && $request->professionId != "") {
                $activities = $this->level4ActivitiesRepository->getNotAttemptedActivities($teenager->id, $request->professionId);
                if (isset($activities[0]) && !empty($activities[0])) {
                    $activity = $activities;
                } else {
                    $activity = [];
                }
                $totalQuestion = $this->level4ActivitiesRepository->getNoOfTotalQuestionsAttemptedQuestion($teenager->id, $request->professionId);
                if(isset($totalQuestion[0]->NoOfTotalQuestions) && $totalQuestion[0]->NoOfTotalQuestions > 0 && ($totalQuestion[0]->NoOfTotalQuestions == $totalQuestion[0]->NoOfAttemptedQuestions) ) {
                    dispatch( new CalculateProfessionCompletePercentage($request->userId, $request->professionId) );
                }
                $response['status'] = 1;
                $response['message'] = trans('appmessages.default_success_msg');
                $response['NoOfTotalQuestions'] = $totalQuestion[0]->NoOfTotalQuestions;
                $response['NoOfAttemptedQuestions'] = $totalQuestion[0]->NoOfAttemptedQuestions;
                $response['data'] = $activity;
            } else {
                $response['status'] = 0;
                $response['message'] = trans('appmessages.missing_data_msg'); 
            }
            $response['login'] = 1;
        } else {
            $response['message'] = trans('appmessages.invalid_userid_msg') . ' or ' . trans('appmessages.notvarified_user_msg');
        }
        return response()->json($response, 200);
        exit;     
    }

    /* Request Params : saveLevel4BasicQuestions
     *  loginToken, userId, questionId, timer, answerId
     *  For multi select options answer. It's in commaseprate value in request params answerId.
     */
    public function saveLevel4BasicQuestions(Request $request) 
    {
        $response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg') ] ;
        $teenager = $this->teenagersRepository->getTeenagerById($request->userId);
        if ($teenager) {
            if (isset($request->questionId) && $request->questionId != "") {
                $timer = ($request->timer != "" && $request->timer > 0) ? $request->timer : 0;
                $answerID = ($request->answerId != "") ? $request->answerId : 0;
                //$answerID = (count($answerArray) > 0) ? implode(',', $answerArray) : $answerArray;
                $questionId = ($request->questionId != "" && $request->questionId > 0) ? $request->questionId : '';
                
                $getAllQuestionRelatedDataFromQuestionId = $this->level4ActivitiesRepository->getAllQuestionRelatedDataFromQuestionId($questionId);
                $array = [];

                if ($getAllQuestionRelatedDataFromQuestionId && !empty($getAllQuestionRelatedDataFromQuestionId)) {
                    $points = $getAllQuestionRelatedDataFromQuestionId->points;
                    $type = $getAllQuestionRelatedDataFromQuestionId->type;
                    $professionId = $getAllQuestionRelatedDataFromQuestionId->profession_id;
                    
                    $ansCorrect = $this->level4ActivitiesRepository->checkQuestionRightOrWrong($questionId, $answerID);
                    
                    if ($timer != 0 && $getAllQuestionRelatedDataFromQuestionId->timer < $timer) {
                        $array['points'] = 0;
                        $array['timer'] = 0;
                        $array['answerID'] = 0;
                        $answerID = 0;
                        $timer = 0;
                    }

                    if ($answerID == 0 && $timer == 0) {
                        $array['points'] = 0;
                        $array['timer'] = 0;
                        $array['answerID'] = 0;
                    } else {
                        $array['points'] = (isset($ansCorrect) && $ansCorrect == 1) ? $points : 0;
                        $array['answerID'] = $answerID;
                        $array['timer'] = $timer;
                    }

                    $array['questionID'] = $questionId;
                    $array['earned_points'] = $array['points'];
                    $array['profession_id'] = $professionId;

                    $data['answers'][] = $array;

                    //Save user response data for basic question
                    $questionsArray = $this->level4ActivitiesRepository->saveTeenagerActivityResponse($request->userId, $data['answers']);

                    $templateId = "L4B";
                    $objProfessionLearningStyle = new ProfessionLearningStyle();
                    $learningId = $objProfessionLearningStyle->getIdByProfessionIdForAdvance($professionId, $templateId);
                    if ($learningId != '') {
                        $objUserLearningStyle = new UserLearningStyle();
                        $learningData = $objUserLearningStyle->getUserLearningStyle($learningId);
                        if (!empty($learningData)) {
                            $array['points'] += $learningData->uls_earned_points;
                        }
                        $userData = [];
                        $userData['uls_learning_style_id'] = $learningId;
                        $userData['uls_profession_id'] = $professionId;
                        $userData['uls_teenager_id'] = $request->userId;
                        $userData['uls_earned_points'] = $array['points'];
                        $result = $objUserLearningStyle->saveUserLearningStyle($userData);
                    }

                    $totalQuestion = $this->level4ActivitiesRepository->getNoOfTotalQuestionsAttemptedQuestion($request->userId, $professionId);
                    if(isset($totalQuestion[0]->NoOfTotalQuestions) && $totalQuestion[0]->NoOfTotalQuestions > 0 && ($totalQuestion[0]->NoOfTotalQuestions == $totalQuestion[0]->NoOfAttemptedQuestions) ) {
                        dispatch( new CalculateProfessionCompletePercentage($request->userId, $professionId) );
                    }

                    $response['status'] = 1;
                    $response['professionId'] = $professionId;
                    $response['message'] = trans('appmessages.default_success_msg');
                    $response['data'] = $questionsArray;
                } else {
                    $response['status'] = 0;
                    $response['message'] = "Wrong Question Submitted!";
                }
            } else {
                $response['status'] = 0;
                $response['message'] = trans('appmessages.missing_data_msg'); 
            }
            $response['login'] = 1;
        } else {
            $response['message'] = trans('appmessages.invalid_userid_msg') . ' or ' . trans('appmessages.notvarified_user_msg');
        }
        return response()->json($response, 200);
        exit;
    }

    /* Request Params : getLevel4IntermediateQuestions
     *  loginToken, userId, professionId, templateId
     */
    public function getLevel4IntermediateQuestions(Request $request) 
    {
        $response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg') ] ;
        $teenager = $this->teenagersRepository->getTeenagerById($request->userId);
        if ($teenager) {
            if (isset($request->professionId) && $request->professionId != "" && isset($request->templateId) && $request->templateId != "") {
                $professionId = (int)$request->professionId;
                $template_id = (int)$request->templateId;
                $userId = (int)$request->userId;

                $objTemplateDeductedCoins = new TemplateDeductedCoins();
                $deductedCoinsDetail = $objTemplateDeductedCoins->getDeductedCoinsDetailById($userId, $professionId, $template_id, 1);
                $days = 0;
                            
                $intermediateActivities = $this->level4ActivitiesRepository->getNotAttemptedIntermediateActivities($userId, $professionId, $template_id);
                $totalIntermediateQuestion = $this->level4ActivitiesRepository->getNoOfTotalIntermediateQuestionsAttemptedQuestion($userId, $professionId, $template_id);
                
                $intermediateCompleted = 0;
                if(isset($totalIntermediateQuestion[0]->NoOfTotalQuestions) && $totalIntermediateQuestion[0]->NoOfTotalQuestions > 0 && ($totalIntermediateQuestion[0]->NoOfAttemptedQuestions >= $totalIntermediateQuestion[0]->NoOfTotalQuestions) ) {
                    $intermediateCompleted = 1;
                    dispatch( new CalculateProfessionCompletePercentage($userId, $professionId) );
                }
                
                if (isset($intermediateActivities[0]) && !empty($intermediateActivities[0])) {
                    $intermediateActivitiesData = $intermediateActivities[0];
                    $intermediateActivitiesData->gt_temlpate_answer_type = Helpers::getAnsTypeFromGamificationTemplateId($intermediateActivitiesData->l4ia_question_template);
                    $intermediateActivitiesData->l4ia_extra_question_time = $this->extraQuestionDescriptionTime;
                    $timer = $intermediateActivitiesData->l4ia_question_time;
                    $response['timer'] = $intermediateActivitiesData->l4ia_question_time;
                    
                    //Question audio
                    if (isset($intermediateActivitiesData->l4ia_question_audio) && $intermediateActivitiesData->l4ia_question_audio != '') {
                        $intermediateActivitiesData->l4ia_question_audio = Storage::url($this->questionDescriptionORIGINALImage . $intermediateActivitiesData->l4ia_question_audio);
                    } else {
                        $intermediateActivitiesData->l4ia_question_audio = '';
                    }

                    //Question image
                    $getQuestionImage = $this->level4ActivitiesRepository->getQuestionMultipleImages($intermediateActivitiesData->activityID);
                    if (isset($getQuestionImage) && !empty($getQuestionImage)) {
                        foreach ($getQuestionImage as $key => $image) {
                            $intermediateActivitiesData->question_images[$key]['l4ia_question_image'] = ( $image['image'] != "" && Storage::size($this->questionDescriptionTHUMBImage . $image['image']) > 0 ) ? Storage::url($this->questionDescriptionTHUMBImage . $image['image']) : Storage::url($this->questionDescriptionTHUMBImage . 'proteen-logo.png');
                            $intermediateActivitiesData->question_images[$key]['l4ia_question_imageDescription'] = $image['imageDescription'];
                        }
                    } else {
                    	$intermediateActivitiesData->question_images = [];
                        //$intermediateActivitiesData->l4ia_question_image = $intermediateActivitiesData->l4ia_question_imageDescription = '';
                    }

                    //Set question youtube video
                    $getQuestionVideo = $this->level4ActivitiesRepository->getQuestionVideo($intermediateActivitiesData->activityID);
                    if (isset($getQuestionVideo['video']) && !empty($getQuestionVideo['video'])) {
                        $videoCode = Helpers::youtube_id_from_url($getQuestionVideo['video']);
                        $intermediateActivitiesData->l4ia_question_video = $videoCode;
                    } else {
                        $intermediateActivitiesData->l4ia_question_video = '';
                    }

                    //Popup image
                    $intermediateActivitiesData->l4ia_question_popup_image = ($intermediateActivitiesData->l4ia_question_popup_image != "") ? (Storage::size($this->questionDescriptionTHUMBImage . $intermediateActivitiesData->l4ia_question_popup_image) > 0) ? Storage::url($this->questionDescriptionTHUMBImage . $intermediateActivitiesData->l4ia_question_popup_image) :  Storage::url($this->questionDescriptionTHUMBImage . 'proteen-logo.png') : "";
                    //Popup description
                    $intermediateActivitiesData->l4ia_question_popup_description = ($intermediateActivitiesData->l4ia_question_popup_description != "") ? $intermediateActivitiesData->l4ia_question_popup_description : '';
                    
                    //Image reorder extra options
                    if ($intermediateActivitiesData->gt_temlpate_answer_type == "image_reorder") {
                        if (isset($intermediateActivitiesData->l4ia_options_metrix) && $intermediateActivitiesData->l4ia_options_metrix != '') {
                            $columns = unserialize($intermediateActivitiesData->l4ia_options_metrix);
                            $intermediateActivitiesData->no_of_column = $columns['column'];
                        } else {
                            $intermediateActivitiesData->no_of_column = 4;
                        }
                    }

                    if ($intermediateActivitiesData->gt_temlpate_answer_type == "select_from_dropdown_option" || $intermediateActivitiesData->gt_temlpate_answer_type == "option_reorder" || $intermediateActivitiesData->gt_temlpate_answer_type == "option_choice" || $intermediateActivitiesData->gt_temlpate_answer_type == "true_false" || $intermediateActivitiesData->gt_temlpate_answer_type == "single_line_answer") {
                        if ($intermediateActivitiesData->gt_temlpate_answer_type == "select_from_dropdown_option") {
                            $response['optionOrder'] = (isset($intermediateActivitiesData->correctOrder)) ? explode(",", $intermediateActivitiesData->correctOrder) : '';
                        }
                        //$intermediateActivitiesData->questionAnswerText = ($intermediateActivitiesData->l4ia_question_answer_description != '') ? $intermediateActivitiesData->l4ia_question_answer_description : '';
                    }
                } else {
                    $intermediateActivitiesData = new \stdClass();
                }

                $getTemplateNo = Helpers::getTemplateNo($userId, $professionId);
                $response['congratulation'] = $getTemplateNo;
                $getTeenagerBoosterPoints = $this->teenagersRepository->getTeenagerBoosterPoints($userId);
                $response['NoOfTotalQuestions'] = $totalIntermediateQuestion[0]->NoOfTotalQuestions;
                $response['NoOfAttemptedQuestions'] = $totalIntermediateQuestion[0]->NoOfAttemptedQuestions;
                $level4Booster = Helpers::level4Booster($professionId, $userId);
                $level4Booster['total'] = ( isset($getTeenagerBoosterPoints['total']) ) ? $getTeenagerBoosterPoints['total'] : "";
                $response['level4Booster'] = $level4Booster;
                $response['professionId'] = $professionId;

                $response['boosterScale'] = 50;
                $response['data'] = $intermediateActivitiesData;

                $response['status'] = 1;
                $response['message'] = trans('appmessages.default_success_msg');
            } else {
                $response['status'] = 0;
                $response['message'] = trans('appmessages.missing_data_msg'); 
            }
            $response['login'] = 1;
        } else {
            $response['message'] = trans('appmessages.invalid_userid_msg') . ' or ' . trans('appmessages.notvarified_user_msg');
        }
        return response()->json($response, 200);
        exit;     
    }

    /* Request Params : saveLevel4IntermediateQuestions
     *  loginToken, userId, questionId, answer, answer_order, timer 
     */
    public function saveLevel4IntermediateQuestions(Request $request) 
    {
        $response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg') ] ;
        $teenager = $this->teenagersRepository->getTeenagerById($request->userId);
        if ($teenager) {
            if (isset($request->questionId) && $request->questionId != "") {
                $questionId = (isset($request->questionId) && $request->questionId > 0) ? $request->questionId : '';
                $getAllQuestionRelatedDataFromQuestionId = $this->level4ActivitiesRepository->getAllIntermediateQuestionRelatedDataFromQuestionId($questionId);
                $body = $request->all();
                $body['timer'] = (isset($request->timer) && $request->timer != 0) ? $request->timer : 0;
                
                if( isset($getAllQuestionRelatedDataFromQuestionId->id) && !empty($getAllQuestionRelatedDataFromQuestionId) ) {
                    $professionId = $getAllQuestionRelatedDataFromQuestionId->l4ia_profession_id;
                    $body['answer'] = explode(',', $request->answer);

                    if ($getAllQuestionRelatedDataFromQuestionId->gt_temlpate_answer_type == "single_line_answer" && isset($body['answer'][0])) {
                        $userAnswer = strtolower(str_replace(' ', '', trim($body['answer'][0])));
                        $systemCorrectAnswer = strtolower(str_replace(' ', '', trim($getAllQuestionRelatedDataFromQuestionId->correct_option)));
                        if ($userAnswer === $systemCorrectAnswer) {
                            $response['answerRightWrongMsg'] = $getAllQuestionRelatedDataFromQuestionId->l4ia_question_right_message;
                            $response['systemCorrectAnswer'] = 1;
                            $earnedPoints = $getAllQuestionRelatedDataFromQuestionId->l4ia_question_point;
                        } else {
                            $response['answerRightWrongMsg'] = $getAllQuestionRelatedDataFromQuestionId->l4ia_question_wrong_message;
                            $response['systemCorrectAnswer'] = 0;
                            $earnedPoints = 0;
                        }
                        $response['systemCorrectAnswerText'] = $getAllQuestionRelatedDataFromQuestionId->correct_option;
                        $data = [];
                        $data['l4iaua_teenager'] = $body['userId'];
                        $data['l4iaua_activity_id'] = $body['questionId'];
                        $data['l4iaua_profession_id'] = $professionId;
                        $data['l4iaua_template_id'] = $getAllQuestionRelatedDataFromQuestionId->l4ia_question_template;
                        $data['l4iaua_answer'] = (isset($body['timer']) && $body['timer'] != 0) ? $body['answer'][0] : 0;
                        $data['l4iaua_order'] = 0;
                        $data['l4iaua_earned_point'] = $earnedPoints;
                        $data['l4iaua_time'] = $body['timer'];
                        $templateId = $getAllQuestionRelatedDataFromQuestionId->l4ia_question_template;
                        $objProfessionLearningStyle = new ProfessionLearningStyle();
                        $learningId = $objProfessionLearningStyle->getIdByProfessionId($professionId, $templateId);
                        if ($learningId != '') {
                            $objUserLearningStyle = new UserLearningStyle();
                            $learningData = $objUserLearningStyle->getUserLearningStyle($learningId);
                            if (!empty($learningData)) {
                                $earnedPoints += $learningData->uls_earned_points;
                            }
                            $userData = [];
                            $userData['uls_learning_style_id'] = $learningId;
                            $userData['uls_profession_id'] = $professionId;
                            $userData['uls_teenager_id'] = $body['userId'];
                            $userData['uls_earned_points'] = $earnedPoints;
                            $result = $objUserLearningStyle->saveUserLearningStyle($userData);
                        }
                        $saveLevel4IntemediateActivityAnswer = $this->level4ActivitiesRepository->saveTeenagerIntermediateActivitySingleLineAnswer($body['userId'], $data);
                        $response['message'] = trans('appmessages.default_success_msg');
                        $response['status'] = 1;
                    } else if ($getAllQuestionRelatedDataFromQuestionId->gt_temlpate_answer_type == "select_from_dropdown_option" && isset($body['answer'][0])) {
                        $userAnswer = $body['answer'][0];
                        $userAnswerOrder = (isset($body['answer_order'][0])) ? $body['answer_order'][0] : 0;

                        $systemCorrectOptionArray = explode(',', $getAllQuestionRelatedDataFromQuestionId->correct_option);
                        $systemCorrectOptionOrderArray = explode(',', $getAllQuestionRelatedDataFromQuestionId->option_order);
                        $systemCorrectOptionIdArray = explode(',', $getAllQuestionRelatedDataFromQuestionId->options_id);

                        $searchSystemCorrectOption = array_search(1, $systemCorrectOptionArray);
                        $searchSystemCorrectOptionOrder = (isset($systemCorrectOptionOrderArray[$searchSystemCorrectOption])) ? $systemCorrectOptionOrderArray[$searchSystemCorrectOption] : 0;
                        $searchSystemCorrectOptionId = (isset($systemCorrectOptionIdArray[$searchSystemCorrectOption])) ? $systemCorrectOptionIdArray[$searchSystemCorrectOption] : 0;

                        if (($userAnswer == $searchSystemCorrectOptionId) && ($userAnswerOrder == $searchSystemCorrectOptionOrder)) {
                            $response['answerRightWrongMsg'] = $getAllQuestionRelatedDataFromQuestionId->l4ia_question_right_message;
                            $response['systemCorrectAnswer'] = 1;
                            $earnedPoints = $getAllQuestionRelatedDataFromQuestionId->l4ia_question_point;
                        } else {
                            $response['answerRightWrongMsg'] = $getAllQuestionRelatedDataFromQuestionId->l4ia_question_wrong_message;
                            $response['systemCorrectAnswer'] = 0;
                            $earnedPoints = 0;
                        }
                        $response['systemCorrectOptionOrder'] = $searchSystemCorrectOptionOrder;
                        $response['systemCorrectOptionId'] = $searchSystemCorrectOptionId;

                        $data = [];
                        $data['l4iaua_teenager'] = $body['userId'];
                        $data['l4iaua_activity_id'] = $body['questionId'];
                        $data['l4iaua_profession_id'] = $professionId;
                        $data['l4iaua_template_id'] = $getAllQuestionRelatedDataFromQuestionId->l4ia_question_template;
                        $data['l4iaua_answer'] = (isset($body['timer']) && $body['timer'] != 0) ? $body['answer'][0] : 0;
                        $data['l4iaua_order'] = (isset($body['answer_order'][0])) ? $body['answer_order'][0] : 0;
                        $data['l4iaua_earned_point'] = $earnedPoints;
                        $data['l4iaua_time'] = $body['timer'];
                        $templateId = $getAllQuestionRelatedDataFromQuestionId->l4ia_question_template;
                        $objProfessionLearningStyle = new ProfessionLearningStyle();
                        $learningId = $objProfessionLearningStyle->getIdByProfessionId($professionId,$templateId);
                        if ($learningId != '') {
                            $objUserLearningStyle = new UserLearningStyle();
                            $learningData = $objUserLearningStyle->getUserLearningStyle($learningId);
                            if (!empty($learningData)) {
                                $earnedPoints += $learningData->uls_earned_points;
                            }
                            $userData = [];
                            $userData['uls_learning_style_id'] = $learningId;
                            $userData['uls_profession_id'] = $professionId;
                            $userData['uls_teenager_id'] = $body['userId'];
                            $userData['uls_earned_points'] = $earnedPoints;
                            $result = $objUserLearningStyle->saveUserLearningStyle($userData);
                        }
                        $saveLevel4IntemediateActivityAnswer = $this->level4ActivitiesRepository->saveTeenagerIntermediateActivityDropDownAnswer($body['userId'], $data);
                        $response['message'] = trans('appmessages.default_success_msg');
                        $response['status'] = 1;
                    } else if ($getAllQuestionRelatedDataFromQuestionId->gt_temlpate_answer_type == "image_reorder" || $getAllQuestionRelatedDataFromQuestionId->gt_temlpate_answer_type == "option_reorder") {
                        $orderArray = explode(',', $getAllQuestionRelatedDataFromQuestionId->option_order);
                        $optionsIdArray = explode(',', $getAllQuestionRelatedDataFromQuestionId->options_id);
                        $userAnswerIdArray = $body['answer'];

                        foreach ($optionsIdArray as $k => $opId) {
                            $newAssoArray[$orderArray[$k]] = $opId;
                            $userAnswerIdArray2[$k + 1] = isset($userAnswerIdArray[$k]) ? $userAnswerIdArray[$k] : 0;
                        }
                        ksort($newAssoArray);
                        $orderArray = implode('', $newAssoArray);
                        $answerString = trim(implode('', $body['answer']));
                        if ($orderArray === $answerString) {
                            $response['answerRightWrongMsg'] = $getAllQuestionRelatedDataFromQuestionId->l4ia_question_right_message;
                            $response['systemCorrectAnswer'] = 1;
                            $earnedPoints = $getAllQuestionRelatedDataFromQuestionId->l4ia_question_point;
                        } else {
                            $response['answerRightWrongMsg'] = $getAllQuestionRelatedDataFromQuestionId->l4ia_question_wrong_message;
                            $response['systemCorrectAnswer'] = 0;
                            $earnedPoints = 0;
                        }
                        $data = [];
                        $data['l4iaua_teenager'] = $body['userId'];
                        $data['l4iaua_activity_id'] = $body['questionId'];
                        $data['l4iaua_profession_id'] = $professionId;
                        $data['l4iaua_template_id'] = $getAllQuestionRelatedDataFromQuestionId->l4ia_question_template;
                        $data['l4iaua_order'] = 0;
                        $data['l4iaua_earned_point'] = $earnedPoints;
                        $data['l4iaua_time'] = $body['timer'];
                        $templateId = $getAllQuestionRelatedDataFromQuestionId->l4ia_question_template;
                        $objProfessionLearningStyle = new ProfessionLearningStyle();
                        $learningId = $objProfessionLearningStyle->getIdByProfessionId($professionId,$templateId);
                        if ($learningId != '') {
                            $objUserLearningStyle = new UserLearningStyle();
                            $learningData = $objUserLearningStyle->getUserLearningStyle($learningId);
                            if (!empty($learningData)) {
                                $earnedPoints += $learningData->uls_earned_points;
                            }
                            $userData = [];
                            $userData['uls_learning_style_id'] = $learningId;
                            $userData['uls_profession_id'] = $professionId;
                            $userData['uls_teenager_id'] = $body['userId'];
                            $userData['uls_earned_points'] = $earnedPoints;
                            $result = $objUserLearningStyle->saveUserLearningStyle($userData);
                        }
                        $saveLevel4IntemediateActivityAnswer = $this->level4ActivitiesRepository->saveTeenagerIntermediateActivityImageReorderAnswer($body['userId'], $data, $userAnswerIdArray2);
                        $response['message'] = trans('appmessages.default_success_msg');
                        $response['status'] = 1;
                    } else if (($getAllQuestionRelatedDataFromQuestionId->gt_temlpate_answer_type == "filling_blank" || $getAllQuestionRelatedDataFromQuestionId->gt_temlpate_answer_type == "option_choice" || $getAllQuestionRelatedDataFromQuestionId->gt_temlpate_answer_type == "true_false" || $getAllQuestionRelatedDataFromQuestionId->gt_temlpate_answer_type == "option_choice" || $getAllQuestionRelatedDataFromQuestionId->gt_temlpate_answer_type == "option_choice_with_response") && isset($body['answer'])) {
                        $checkAnswerFromOption = '';
                        $yourResult = 0;
                        $optionsIdArray = explode(',', $getAllQuestionRelatedDataFromQuestionId->options_id);
                        $correctOptionsArray = explode(',', $getAllQuestionRelatedDataFromQuestionId->correct_option);
                        if (isset($body['answer']) && $body['answer'][0] != 0) {
                            $yourResult = $this->level4ActivitiesRepository->checkIntermediateQuestionRightOrWrong($body['questionId'], implode(',', $body['answer']));
                        } else {
                            $yourResult = 0;
                        }
                        if ($yourResult == 1) {
                            $response['answerRightWrongMsg'] = $getAllQuestionRelatedDataFromQuestionId->l4ia_question_right_message;
                            $response['systemCorrectAnswer'] = 1;
                            $earnedPoints = $getAllQuestionRelatedDataFromQuestionId->l4ia_question_point;
                        } else {
                            $response['answerRightWrongMsg'] = $getAllQuestionRelatedDataFromQuestionId->l4ia_question_wrong_message;
                            $response['systemCorrectAnswer'] = 0;
                            $earnedPoints = 0;
                        }

                        if ($getAllQuestionRelatedDataFromQuestionId->gt_temlpate_answer_type == "option_choice") {
                            $response['questionAnswerText'] = ($getAllQuestionRelatedDataFromQuestionId->l4ia_question_answer_description != '') ? $getAllQuestionRelatedDataFromQuestionId->l4ia_question_answer_description : '';
                        }

                        $data = [];
                        $data['l4iaua_teenager'] = $body['userId'];
                        $data['l4iaua_activity_id'] = $body['questionId'];
                        $data['l4iaua_profession_id'] = $professionId;
                        $data['l4iaua_template_id'] = $getAllQuestionRelatedDataFromQuestionId->l4ia_question_template;
                        $data['l4iaua_answer'] = (isset($body['timer']) && $body['timer'] != 0) ? $body['answer'] : $body['answer'];
                        $data['l4iaua_order'] = 0;
                        $data['l4iaua_earned_point'] = $earnedPoints;
                        $data['l4iaua_time'] = $body['timer'];
                        $templateId = $getAllQuestionRelatedDataFromQuestionId->l4ia_question_template;
                        $objProfessionLearningStyle = new ProfessionLearningStyle();
                        $learningId = $objProfessionLearningStyle->getIdByProfessionId($professionId, $templateId);
                        if ($learningId != '') {
                            $objUserLearningStyle = new UserLearningStyle();
                            $learningData = $objUserLearningStyle->getUserLearningStyle($learningId);
                            if (!empty($learningData)) {
                                $earnedPoints += $learningData->uls_earned_points;
                            }
                            $userData = [];
                            $userData['uls_learning_style_id'] = $learningId;
                            $userData['uls_profession_id'] = $professionId;
                            $userData['uls_teenager_id'] = $body['userId'];
                            $userData['uls_earned_points'] = $earnedPoints;
                            $result = $objUserLearningStyle->saveUserLearningStyle($userData);
                        }
                        $saveLevel4IntemediateActivityAnswer = $this->level4ActivitiesRepository->saveTeenagerIntermediateActivityFillInBlanksAnswer($body['userId'], $data, $body['answer']);

                        $response['message'] = trans('appmessages.default_success_msg');
                        $response['status'] = 1;
                    } else {
                        $response['status'] = 0;
                        $response['message'] = "Invalid Answer Type";
                    }
                    $getTeenagerBoosterPoints2 = $this->teenagersRepository->getTeenagerBoosterPoints($body['userId']);
                    $message = '';
                    if (!empty($getTeenagerBoosterPoints2)) {
                        $message = Helpers::sendMilestoneNotification($getTeenagerBoosterPoints2['total']);
                    }

                    $totalIntermediateQuestion = $this->level4ActivitiesRepository->getNoOfTotalIntermediateQuestionsAttemptedQuestion($body['userId'], $professionId, $getAllQuestionRelatedDataFromQuestionId->l4ia_question_template);
                    if(isset($totalIntermediateQuestion[0]->NoOfTotalQuestions) && $totalIntermediateQuestion[0]->NoOfTotalQuestions > 0 && ($totalIntermediateQuestion[0]->NoOfAttemptedQuestions >= $totalIntermediateQuestion[0]->NoOfTotalQuestions) ) {
                        $intermediateCompleted = 1;
                        dispatch( new CalculateProfessionCompletePercentage($body['userId'], $professionId) );
                    }

                    $response['displayMsg'] = $message;
                    $level4Booster = Helpers::level4Booster($professionId, $body['userId']);
                    $level4Booster['total'] = $getTeenagerBoosterPoints2['total'];
                    $response['level4Booster'] = $level4Booster;
                    //$response['booster_points'] = '';
                    $response['templateId'] = $getAllQuestionRelatedDataFromQuestionId->l4ia_question_template;
                    $response['answerType'] = $getAllQuestionRelatedDataFromQuestionId->gt_temlpate_answer_type;
                } else {
                    $response['status'] = 0;
                    $response['message'] = "Wrong question related data found!";
                }
            } else {
                $response['status'] = 0;
                $response['message'] = trans('appmessages.missing_data_msg'); 
            }
            $response['login'] = 1;
        } else {
            $response['message'] = trans('appmessages.invalid_userid_msg') . ' or ' . trans('appmessages.notvarified_user_msg');
        }
        return response()->json($response, 200);
        exit;     
    }

    /* Request Params : getLevel4IntermediateTemplate
     *  loginToken, userId, professionId
     */
    public function getLevel4IntermediateTemplate(Request $request) 
    {
        $response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg') ] ;
        $teenager = $this->teenagersRepository->getTeenagerById($request->userId);
        if ($teenager) {
            if (isset($request->professionId) && $request->professionId != "") {
                $getQuestionTemplateForProfession = $this->level4ActivitiesRepository->getQuestionTemplateForProfession($request->professionId);
                $objTemplateDeductedCoins = new TemplateDeductedCoins();
                if(!empty($getQuestionTemplateForProfession) && isset($getQuestionTemplateForProfession[0])) {
                    foreach ($getQuestionTemplateForProfession As $key => $value) {
                        $deductedCoinsDetail = $objTemplateDeductedCoins->getDeductedCoinsDetailById($request->userId, $request->professionId, $value->gt_template_id, 1);
                        
                        $days = 0;
                        if (!empty($deductedCoinsDetail) && isset($deductedCoinsDetail[0])) {
                            $days = Helpers::calculateRemainingDays($deductedCoinsDetail[0]->tdc_end_date);
                        }

                        $getQuestionTemplateForProfession[$key]->remainingDays = $days;
                        $intermediateActivities = [];
                        $intermediateActivities = $this->level4ActivitiesRepository->getNotAttemptedIntermediateActivities($request->userId, $request->professionId, $value->gt_template_id);
                        $totalIntermediateQuestion = $this->level4ActivitiesRepository->getNoOfTotalIntermediateQuestionsAttemptedQuestion($request->userId, $request->professionId, $value->gt_template_id);
                        if (empty($intermediateActivities) || ($totalIntermediateQuestion[0]->NoOfTotalQuestions == $totalIntermediateQuestion[0]->NoOfAttemptedQuestions) || ($totalIntermediateQuestion[0]->NoOfTotalQuestions < $totalIntermediateQuestion[0]->NoOfAttemptedQuestions)) {
                           $getQuestionTemplateForProfession[$key]->played = 1;
                        } else {
                            $getQuestionTemplateForProfession[$key]->played = 0;
                        }
                    }
                }

                if(!empty($getQuestionTemplateForProfession) && isset($getQuestionTemplateForProfession[0])) {
                    $response['questionTemplate'] = $getQuestionTemplateForProfession;
                } else {
                    $response['questionTemplate'] = [];
                }
                $getTeenagerBoosterPoints = $this->teenagersRepository->getTeenagerBoosterPoints($request->userId);
                $response['message'] = trans('appmessages.default_success_msg');
                $response['status'] = 1;
                $userDetail = $this->teenagersRepository->getUserDataForCoinsDetail($request->userId);
                $response['availableCoins'] = $userDetail['t_coins'];
                $level4Booster = Helpers::level4Booster($request->professionId, $request->userId);
                $l4BoosterDetails = [];
                $l4BoosterDetails['competing'] = $level4Booster['competing'];
                $l4BoosterDetails['yourScore'] = $level4Booster['yourScore'];
                $l4BoosterDetails['highestScore'] = $level4Booster['highestScore'];
                $l4BoosterDetails['yourRank'] = $level4Booster['yourRank'];
                $l4BoosterDetails['totalPobScore'] = $level4Booster['totalPobScore'];
                $level4Booster['total'] = $getTeenagerBoosterPoints['total'];
                $response['level4Booster'] = $l4BoosterDetails;
                //$response['boosterScale'] = 50;
            } else {
                $response['status'] = 0;
                $response['message'] = trans('appmessages.missing_data_msg'); 
            }
            $response['login'] = 1;
        } else {
            $response['message'] = trans('appmessages.invalid_userid_msg') . ' or ' . trans('appmessages.notvarified_user_msg');
        }
        return response()->json($response, 200);
        exit;     
    }

    /* Request Params : getLevel4PromisePlusDetails
     *  loginToken, userId, careerId
     */
    public function getLevel4PromisePlusDetails(Request $request) 
    {
        $response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg') ] ;
        $teenager = $this->teenagersRepository->getTeenagerById($request->userId);
        if ($teenager) {
            if (isset($request->careerId) && !empty($request->careerId)) {
                $data = [];
                $professionId = $request->careerId;
                $userId = $request->userId;
                $level4Booster = Helpers::level4Booster($professionId, $userId);
                $getTeenagerAllTypeBadges = $this->teenagersRepository->getTeenagerAllTypeBadges($userId, $professionId);
                $totalPoints = 0;
                if (!empty($getTeenagerAllTypeBadges)) {
                    if ($getTeenagerAllTypeBadges['level4Basic']['noOfAttemptedQuestion'] != 0) {
                        $totalPoints += $getTeenagerAllTypeBadges['level4Basic']['basicAttemptedTotalPoints'];
                    }
                    if ($getTeenagerAllTypeBadges['level4Intermediate']['noOfAttemptedQuestion'] != 0) {
                        foreach ($getTeenagerAllTypeBadges['level4Intermediate']['templateWiseEarnedPoint'] AS $k => $val) {
                            $totalPoints += $getTeenagerAllTypeBadges['level4Intermediate']['templateWiseTotalAttemptedPoint'][$k];
                        }
                    }
                    if ($getTeenagerAllTypeBadges['level4Advance']['earnedPoints'] != 0) {
                        $totalPoints += $getTeenagerAllTypeBadges['level4Advance']['advanceTotalPoints'];
                    }
                }
                $level2Data = '';
                $level4PromisePlus = '';
                $flag = false;
                if ($totalPoints != 0) {
                    $level4PromisePlus = Helpers::calculateLevel4PromisePlus($level4Booster['yourScore'], $totalPoints);
                    $flag = true;
                }

                $promisePlus = 0;
                if ($flag) {
                    if ($level4PromisePlus >= Config::get('constant.NOMATCH_MIN_RANGE') && $level4PromisePlus <= Config::get('constant.NOMATCH_MAX_RANGE') ) {
                        $promisePlus = "nomatch";
                    } else if ($level4PromisePlus >= Config::get('constant.MODERATE_MIN_RANGE') && $level4PromisePlus <= Config::get('constant.MODERATE_MAX_RANGE') ) {
                    $promisePlus = "moderate";
                    } else if ($level4PromisePlus >= Config::get('constant.MATCH_MIN_RANGE') && $level4PromisePlus <= Config::get('constant.MATCH_MAX_RANGE') ) {
                    $promisePlus = "match";
                    } else {
                        $promisePlus = "";
                    }
                } else {
                    $promisePlus = "";
                }

                //get L2 HML 
                $level2Promise = '';
                $getTeenagerHML = Helpers::getTeenagerMatchScale($userId);
                $level2Promise = isset($getTeenagerHML[$professionId])?$getTeenagerHML[$professionId]:'nomatch';

                if ($level2Promise == 'nomatch') {
                    $level2Data = 'TOUGH & CHALLENGING';
                } else if ($level2Promise == 'moderate') {
                    $level2Data = 'MODERATELY SUITED';
                } else if ($level2Promise == 'match') {
                    $level2Data = 'LIKELY FIT FOR YOU';
                } else {
                    $level2Promise = "";
                    $level2Data = '';
                }

                $promisePlusData = $this->objPromisePlus->getAllPromisePlus();

                $l4PromisePlus = [];
                $colorCode = '';
                $l4PP = '';
                $professionFeedback = '';
                if ($level2Promise == 'nomatch' && $promisePlus == 'nomatch' ) {
                    $professionFeedback = 0;
                    $colorCode = 1;
                    $l4PP = 1;
                } else if ($level2Promise == 'nomatch' && $promisePlus == 'moderate' ) {
                    $professionFeedback = 3;
                } else if ($level2Promise == 'nomatch' && $promisePlus == 'match' ) {
                    $professionFeedback = 6;
                } else if ($level2Promise == 'moderate' && $promisePlus == 'nomatch' ) {
                    $professionFeedback = 1;
                } else if ($level2Promise == 'moderate' && $promisePlus == 'moderate' ) {
                    $professionFeedback = 4;
                    $colorCode = 2;
                    $l4PP = 1;
                } else if ($level2Promise == 'moderate' && $promisePlus == 'match' ) {
                    $professionFeedback = 7;
                } else if ($level2Promise == 'match' && $promisePlus == 'nomatch' ) {
                    $professionFeedback = 2;
                } else if ($level2Promise == 'match' && $promisePlus == 'moderate' ) {
                    $professionFeedback = 5;
                } else if ($level2Promise == 'match' && $promisePlus == 'match' ) {
                    $professionFeedback = 8;
                    $colorCode = 3;
                    $l4PP = 1;
                }
                if (!empty($promisePlusData)) {
                    if ($promisePlus != '') {
                        $l4PromisePlus[] = $promisePlusData[$professionFeedback];
                    }
                } else {
                    $l4PromisePlus[] = '';
                }
                if(isset($l4PromisePlus) && count($l4PromisePlus) > 0) {
                    if ($level2Promise == 'match') {
                        if ($promisePlus == 'match') {
                            $data['image'] = Storage::url('img/Original-image/s-icon-1.png');
                        } else if ($promisePlus == 'moderate') {
                            $data['image'] = Storage::url('img/Original-image/m-icon-2.png');
                        } else if ($promisePlus == 'nomatch') {
                            $data['image'] = Storage::url('img/Original-image/icon-3.png');
                        }                   
                    }


                    if ($level2Promise == 'moderate') {
                        if ($promisePlus == 'match') {
                            $data['image'] = Storage::url('img/Original-image/s-icon-4.png');  
                        } else if ($promisePlus == 'moderate') {
                            $data['image'] = Storage::url('img/Original-image/m-icon-5.png');
                        } else if ($promisePlus == 'nomatch') {
                            $data['image'] = Storage::url('img/Original-image/icon-6.png');
                        }                   
                    }


                    if ($level2Promise == 'nomatch') {
                        if ($promisePlus == 'match') {
                            $data['image'] = Storage::url('img/Original-image/s-icon-7.png'); 
                        } else if ($promisePlus == 'moderate') {
                            $data['image'] = Storage::url('img/Original-image/m-icon-8.png'); 
                        } else if ($promisePlus == 'nomatch') {
                            $data['image'] = Storage::url('img/Original-image/icon-9.png');
                        }                   
                    } 

                    $data['details'] = $l4PromisePlus[0]->ps_description;
                    $response['status'] = 1;
                    $response['message'] = trans('appmessages.default_success_msg');
                    $response['data'] = $data;
                    //Store log in System
                    $this->log->info('Teenager retrieve promise plus details for profession', array('userId' => $request->userId, 'professionId' => $request->careerId));
                } else {
                    $response['status'] = 0;
                    $response['message'] = "Please attempt profession first to see Promise Plus";
                    $response['data'] = [];
                }
            } else {
                $response['status'] = 0;
                $response['message'] = trans('appmessages.missing_data_msg'); 
            }
            $response['login'] = 1;
        } else {
            $response['message'] = trans('appmessages.invalid_userid_msg') . ' or ' . trans('appmessages.notvarified_user_msg');
        }
        return response()->json($response, 200);
        exit;
    }

    /* Request Params : getLevel4PromisePlusDetails
     *  loginToken, userId, careerId, templateId, templatePlayed
     */
    public function saveTemplateConsumedCoinsDetail(Request $request) 
    {
        $response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg') ] ;
        $teenager = $this->teenagersRepository->getTeenagerById($request->userId);
        if ($teenager) {
            if (isset($request->careerId) && $request->careerId != '' && isset($request->templateId) && $request->templateId != '' && isset($request->templatePlayed) && $request->templatePlayed != '') {
                    $data = [];
                    $professionId = $request->careerId;
                    $templateId = $request->templateId;
                    $userId = $request->userId;
                    $attempted = $request->templatePlayed;

                    $objPaidComponent = new PaidComponent();
                    $objTemplateDeductedCoins = new TemplateDeductedCoins();

                    $deductedCoinsDetail = $objTemplateDeductedCoins->getDeductedCoinsDetailById($userId, $professionId, $templateId, 1);
                        
                    $days = 0;
                    if (!empty($deductedCoinsDetail) && isset($deductedCoinsDetail[0]->tdc_end_date)) {
                        $days = Helpers::calculateRemainingDays($deductedCoinsDetail[0]->tdc_end_date);
                    }
                        
                    $userData = $this->level4ActivitiesRepository->getTemplateDataForCoinsDetail($templateId);
                    $coins = isset($userData['gt_coins']) ? $userData['gt_coins'] : 0;
                    if ($days == 0 && $coins > 0 && $attempted == 0) {
                        $deductedCoins = $coins;
                        $userDetail = $this->teenagersRepository->getUserDataForCoinsDetail($userId);
                        $coins = $userDetail['t_coins'] - $coins;
                        $responsep = $this->teenagersRepository->updateTeenagerCoinsDetail($userId, $coins);
                        $saveData = [];
                        $saveData['id'] = 0;
                        $saveData['tdc_user_id'] = $userId;
                        $saveData['tdc_user_type'] = 1;
                        $saveData['tdc_profession_id'] = $professionId;
                        $saveData['tdc_template_id'] = $templateId;
                        $saveData['tdc_total_coins'] = $deductedCoins;
                        $saveData['tdc_start_date'] = date('y-m-d');
                        $saveData['tdc_end_date'] = date('Y-m-d', strtotime("+". $userData['gt_valid_upto'] ." days"));

                        $responsep = $objTemplateDeductedCoins->saveDeductedCoinsDetail($saveData);

                        $deductedCoinsDetail = $objTemplateDeductedCoins->getDeductedCoinsDetailById($userId, $professionId, $templateId, 1);
                        $remDays = 0;
                        if ($deductedCoinsDetail && isset($deductedCoinsDetail[0])) {
                            $remDays = Helpers::calculateRemainingDays($deductedCoinsDetail[0]->tdc_end_date);
                        }
                        $response['remainingDays'] = $remDays;  
                    }
                    $response['status'] = 1;
                    $response['message'] = trans('appmessages.default_success_msg');
                    //Store log in System
                    $this->log->info('Teenager consumed coins for level 4 intermediate activity', array('userId' => $request->userId, 'professionId' => $request->careerId, 'templateId' => $request->templateId));
            } else {
                $response['status'] = 0;
                $response['message'] = trans('appmessages.missing_data_msg'); 
            }
            $response['login'] = 1;
        } else {
            $response['message'] = trans('appmessages.invalid_userid_msg') . ' or ' . trans('appmessages.notvarified_user_msg');
        }
        return response()->json($response, 200);
        exit;
    }

    /* Request Params : learningGuidance
     *  loginToken, userId
     *  Service after loggedIn user
     */
    public function learningGuidance(Request $request)
    {
        $response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg') ] ;
        $teenager = $this->teenagersRepository->getTeenagerById($request->userId);
        if($request->userId != "" && $teenager) {
            $learningGuidance = [];
            $userId = $request->userId;
            //Insert all user learning style data
            $professionArray = $this->objLevel4ProfessionProgress->getTeenAttemptProfessionWithTotal($userId);
            
            $objLevel4Answers = new Level4Answers();
            $objProfessionLearningStyle = new ProfessionLearningStyle();
            $objUserLearningStyle = new UserLearningStyle();
            if (isset($professionArray) && !empty($professionArray)) {
                foreach ($professionArray as $key => $proValue) {
                    $professionId = $proValue->id;
                    $level4BasicData = $objLevel4Answers->getLevel4BasicDetailById($userId,$professionId);
                    if (isset($level4BasicData) && !empty($level4BasicData)) {
                        $templateId = "L4B";
                        $learningId = $objProfessionLearningStyle->getIdByProfessionIdForAdvance($professionId,$templateId);
                        if ($learningId != '') {
                            $userData = [];
                            $userData['uls_learning_style_id'] = $learningId;
                            $userData['uls_profession_id'] = $professionId;
                            $userData['uls_teenager_id'] = $userId;
                            $userData['uls_earned_points'] = $level4BasicData[0]->earned_points;
                            $result = $objUserLearningStyle->saveUserLearningStyle($userData);
                        }
                    }
                    $media = array(1,2,3);
                    for ($i = 0; $i < count($media); $i++) {
                        $level4AdvanceData = $this->level4ActivitiesRepository->getLevel4AdvanceDetailById($userId,$professionId,$media[$i]);
                        $templateId = '';
                        if ($media[$i] == 3) {
                            $templateId = "L4AP";
                        } else if ($media[$i] == 2) {
                            $templateId = "L4AD";
                        } else if ($media[$i] == 1) {
                            $templateId = "L4AV";
                        }
                        $learningId = $objProfessionLearningStyle->getIdByProfessionIdForAdvance($professionId,$templateId);
                        if (isset($level4AdvanceData) && !empty($level4AdvanceData)) {
                            if ($learningId != '') {
                                $userData = [];
                                $userData['uls_learning_style_id'] = $learningId;
                                $userData['uls_profession_id'] = $professionId;
                                $userData['uls_teenager_id'] = $userId;
                                $userData['uls_earned_points'] = $level4AdvanceData[0]->earned_points;
                                $result = $objUserLearningStyle->saveUserLearningStyle($userData);
                            }
                        }
                    }
                    $level4IntermediateData = $this->level4ActivitiesRepository->getLevel4IntermediateDetailById($userId,$professionId);
                    if (isset($level4IntermediateData) && !empty($level4IntermediateData)) {
                        $dataArr = [];
                        $uniqueArr =[];
                        foreach ($level4IntermediateData AS $key => $value) {
                            if(!in_array($value->l4iaua_template_id, $uniqueArr))
                            {
                                $uniqueArr[] = $value->l4iaua_template_id;
                                $data = [];
                                $data['l4iaua_template_id'] = $value->l4iaua_template_id;
                                $data['l4iaua_earned_point'] = 0;
                                $dataArr[] = $data;
                            }
                        }
                        foreach ($level4IntermediateData AS $key => $value) {
                            foreach ($dataArr As $k => $val) {
                                if ($value->l4iaua_template_id == $val['l4iaua_template_id']){
                                    $dataArr[$k]['l4iaua_earned_point'] += $value->l4iaua_earned_point;
                                }
                            }
                        }
                        for ($j = 0; $j < count($dataArr); $j++) {
                            $templateId = $dataArr[$j]['l4iaua_template_id'];
                            $learningId = $objProfessionLearningStyle->getIdByProfessionId($professionId,$templateId);
                            if ($learningId != '') {
                                $userData = [];
                                $userData['uls_learning_style_id'] = $learningId;
                                $userData['uls_profession_id'] = $professionId;
                                $userData['uls_teenager_id'] = $userId;
                                $userData['uls_earned_points'] = $dataArr[$j]['l4iaua_earned_point'];
                                $result = $objUserLearningStyle->saveUserLearningStyle($userData);
                            }
                        }
                    }
                }
            }

            $finalProfessionArray = [];
            $objLearningStyle = new LearningStyle();

            $userLearningData = $objLearningStyle->getLearningStyleDetails();
            $objProfession =  new Professions();
            $AllProData = $objProfession->getActiveProfessions();

            $TotalAttemptedP = 0;
            $allp = count($AllProData);
            $attemptedp = count($professionArray);
            $TotalAttemptedP = ($attemptedp * 100) / $allp;
            if (!empty($userLearningData)) {
                foreach ($userLearningData as $k => $value ) {
                    $userLearningData[$k]->earned_points = 0;
                    $userLearningData[$k]->total_points = 0;
                    $userLearningData[$k]->percentage = '';
                    $userLearningData[$k]->interpretationrange = '';
                    $userLearningData[$k]->totalAttemptedP = round($TotalAttemptedP);
                    $photo = $value->ls_image;
                    if ($photo != '' && file_exists($this->learningStyleThumbImageUploadPath . $photo)) {
                        $value->ls_image = asset($this->learningStyleThumbImageUploadPath . $photo);
                    } else {
                        $value->ls_image = asset("/frontend/images/proteen-logo.png");
                    }
                }

                if (isset($professionArray) && !empty($professionArray)) {
                    foreach ($professionArray as $key => $val) {
                        $professionId = $val->id;
                        $getTeenagerAllTypeBadges = $this->teenagersRepository->getTeenagerAllTypeBadges($userId, $professionId);
                        $level4Booster = Helpers::level4Booster($professionId, $userId);
                        $l4BTotal = (isset($getTeenagerAllTypeBadges['level4Basic']) && !empty($getTeenagerAllTypeBadges['level4Basic'])) ? $getTeenagerAllTypeBadges['level4Basic']['basicAttemptedTotalPoints'] : '';
                        $l4ATotal = (isset($getTeenagerAllTypeBadges['level4Advance']) && !empty($getTeenagerAllTypeBadges['level4Advance'])) ? $getTeenagerAllTypeBadges['level4Advance']['earnedPoints'] : '';
                        $UserLerningStyle = [];
                    
                        foreach ($userLearningData as $k => $value ) {
                            $userLData = $objLearningStyle->getLearningStyleDetailsByProfessionId($professionId,$value->parameterId,$userId);
                            if (!isset($userLData) && count($userLData) > 0) {
                                $points = '';
                                $LAPoints = '';
                                $points = $userLData[0]->uls_earned_points;
                                $userLearningData[$k]->earned_points += $userLData[0]->uls_earned_points;
                                $activityName = $userLData[0]->activity_name;
                                if (strpos($activityName, ',') !== false) {
                                    $Activities = explode(",",$activityName);
                                    foreach ($Activities As $Akey => $acty) {
                                        if ($acty == 'L4B') {
                                                $userLearningData[$k]->total_points += $l4BTotal;
                                        } else if ($acty == 'L4AV') {
                                            if ($l4ATotal != 0) {
                                                $userLearningData[$k]->total_points += Config::get('constant.USER_L4_VIDEO_POINTS');
                                            }
                                        }else if ($acty == 'L4AP') {
                                            if ($l4ATotal != 0) {
                                                $userLearningData[$k]->total_points += Config::get('constant.USER_L4_PHOTO_POINTS');
                                            }
                                        }else if ($acty == 'L4AD') {
                                            if ($l4ATotal != 0) {
                                            $userLearningData[$k]->total_points += Config::get('constant.USER_L4_DOCUMENT_POINTS');
                                            }
                                        } else if ($acty == 'N/A') {
                                            if ($points != 0) {
                                                $userLearningData[$k]->total_points += '';
                                            }
                                        } else {
                                            if ($acty != '' && intval($acty) > 0) {
                                                $TotalPoints = $getTeenagerAllTypeBadges['level4Intermediate']['templateWiseTotalAttemptedPoint'][$acty];
                                                $userLearningData[$k]->total_points += $TotalPoints;
                                            }

                                            }
                                    }
                              } else {
                                  if ($activityName == 'L4B') {
                                        $userLearningData[$k]->total_points += $l4BTotal;
                                  } else if ($activityName == 'L4AV') {
                                      if ($l4ATotal != 0) {
                                          $userLearningData[$k]->total_points += Config::get('constant.USER_L4_VIDEO_POINTS');
                                      }
                                  }else if ($activityName == 'L4AP') {
                                      if ($l4ATotal != 0) {
                                          $userLearningData[$k]->total_points += Config::get('constant.USER_L4_PHOTO_POINTS');
                                      }
                                  }else if ($activityName == 'L4AD') {
                                      if ($l4ATotal != 0) {
                                          $userLearningData[$k]->total_points += Config::get('constant.USER_L4_DOCUMENT_POINTS');
                                      }
                                  } else if ($activityName == 'N/A') {
                                      if ($points != 0) {
                                          $userLearningData[$k]->total_points += '';
                                      }
                                  } else {
                                      if (intval($activityName) > 0) {
                                          $TotalPoints = $getTeenagerAllTypeBadges['level4Intermediate']['templateWiseTotalAttemptedPoint'][$activityName];
                                            $userLearningData[$k]->total_points += $TotalPoints;
                                      }
                                  }
                            }
                            if ($userLearningData[$k]->total_points != 0) {
                                $LAPoints = ($value->earned_points * 100) / $userLearningData[$k]->total_points;
                            }
                            $range = '';
                            $LAPoints = round($LAPoints);
                            if ($LAPoints >= Config::get('constant.LS_LOW_MIN_RANGE') && $LAPoints <= Config::get('constant.LS_LOW_MAX_RANGE') ) {
                                $range = "Low";
                            } else if ($LAPoints >= Config::get('constant.LS_MEDIUM_MIN_RANGE') && $LAPoints <= Config::get('constant.LS_MEDIUM_MAX_RANGE') ) {
                                $range = "Medium";
                            } else if ($LAPoints >= Config::get('constant.LS_HIGH_MIN_RANGE') && $LAPoints <= Config::get('constant.LS_HIGH_MAX_RANGE') ) {
                                $range = "High";
                            }
                            $userLearningData[$k]->interpretationrange = $range;
                            $userLearningData[$k]->percentage = $LAPoints;
                            }
                        }
                    }
                }
            }
           
            if(isset($userLearningData) && !empty($userLearningData))
            {            
                foreach($userLearningData as $key=>$lg){                  
                    if (strpos($lg->ls_name, 'factual_') !== false)
                    {
                        $subPanelDataFactual[] = array('id'=>$lg->parameterId,'title'=>$lg->ls_name,'titleColor'=>'#f1c246','titleType'=>$lg->interpretationrange,'subPanelDescription'=>$lg->ls_description); 
                        $learningGuidance[0] = array('id'=>$lg->parameterId,'name'=>'Factual','slug'=>$lg->ls_name,'panelColor'=>'#ff5f44','image'=>'https://s3proteen.s3.ap-south-1.amazonaws.com/img/brain-img.png','subPanelData'=>$subPanelDataFactual);                                     
                    }
                    elseif (strpos($lg->ls_name, 'conceptual_') !== false)
                    {
                        $subPanelDataConcept[] = array('id'=>$lg->parameterId,'title'=>$lg->ls_name,'titleColor'=>'#f1c246','titleType'=>$lg->interpretationrange,'subPanelDescription'=>$lg->ls_description); 
                        $learningGuidance[1] = array('id'=>$lg->parameterId,'name'=>'Conceptual','slug'=>$lg->ls_name,'panelColor'=>'#27a6b5','image'=>'https://s3proteen.s3.ap-south-1.amazonaws.com/img/bulb-img.png','subPanelData'=>$subPanelDataConcept);                                       
                    }
                    elseif (strpos($lg->ls_name, 'procedural_') !== false)
                    {
                        $subPanelDataProcedural[] = array('id'=>$lg->parameterId,'title'=>$lg->ls_name,'titleColor'=>'#f1c246','titleType'=>$lg->interpretationrange,'subPanelDescription'=>$lg->ls_description); 
                        $learningGuidance[2] = array('id'=>$lg->parameterId,'name'=>'Procedural','slug'=>$lg->ls_name,'panelColor'=>'#65c6e6','image'=>'https://s3proteen.s3.ap-south-1.amazonaws.com/img/puzzle-img.png','subPanelData'=>$subPanelDataProcedural);                                       
                    }
                    elseif (strpos($lg->ls_name, 'meta_cognitive_') !== false)
                    {
                        $subPanelDataMeta[] = array('id'=>$lg->parameterId,'title'=>$lg->ls_name,'titleColor'=>'#f1c246','titleType'=>$lg->interpretationrange,'subPanelDescription'=>$lg->ls_description); 
                        $learningGuidance[3] = array('id'=>$lg->parameterId,'name'=>'Meta-Cognitive','slug'=>$lg->ls_name,'panelColor'=>'#73376d','image'=>'https://s3proteen.s3.ap-south-1.amazonaws.com/img/star-img.png','subPanelData'=>$subPanelDataMeta);                                       
                    }                   
                }
            }
            $responseData['learningGuidanceInfo'] = "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer lobortis vestibulum ipsum id commodo. Curabitur non turpis eget turpis laoreet mattisac sit amet turpismolestie lacus non, elementum velit.";
            $responseData['panelData'] = $learningGuidance;
            $response['login'] = 1;
            $response['status'] = 1;
            $response['message'] = trans('appmessages.default_success_msg');
            $response['data'] = $responseData;
        } else {
            $response['message'] = trans('appmessages.invalid_userid_msg') . ' or ' . trans('appmessages.notvarified_user_msg');
        }
        
        return response()->json($response, 200);
        exit;

    }

}
