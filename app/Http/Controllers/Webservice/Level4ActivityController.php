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
use App\Services\Parents\Contracts\ParentsRepository;
use App\Services\Template\Contracts\TemplatesRepository;
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

class Level4ActivityController extends Controller {

    public function __construct(TeenagersRepository $teenagersRepository, ProfessionsRepository $professionsRepository, ParentsRepository $parentsRepository, TemplatesRepository $templatesRepository) 
    {
        $this->teenagersRepository = $teenagersRepository;
        $this->professionsRepository = $professionsRepository;
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

}
