<?php

namespace App\Http\Controllers\Teenager;

use App\Http\Controllers\Controller;
use App\Services\Professions\Contracts\ProfessionsRepository;
use App\Services\Baskets\Contracts\BasketsRepository;
use App\Baskets;
use App\Professions;
use App\ProfessionHeaders;
use App\MultipleIntelligent;
use App\Apptitude;
use App\Personality;
use App\Teenagers;
use Auth;
use Config;
use Storage;
use Input;
use Mail;
use Helpers;
use Redirect;
use Request; 
use PDF;  
use App\StarRatedProfession; 
use App\TeenagerPromiseScore;
use App\PromiseParametersMaxScore;
use App\Services\Teenagers\Contracts\TeenagersRepository;
use App\Services\Level4Activity\Contracts\Level4ActivitiesRepository;
use App\SponsorsActivity;
use App\TeenagerScholarshipProgram;
use App\PaidComponent;
use App\DeductedCoins;
use App\TeenParentChallenge;
use App\Services\Parents\Contracts\ParentsRepository;
use App\Services\Template\Contracts\TemplatesRepository;
use App\Interest;
use App\ProfessionSubject;
use App\ProfessionTag;

class ProfessionController extends Controller {

    public function __construct(Level4ActivitiesRepository $level4ActivitiesRepository, ProfessionsRepository $professionsRepository, BasketsRepository $basketsRepository, TeenagersRepository $teenagersRepository, ParentsRepository $parentsRepository, TemplatesRepository $templatesRepository) 
    {
        $this->professionsRepository = $professionsRepository;
        $this->level4ActivitiesRepository = $level4ActivitiesRepository;
        $this->baskets = new Baskets();
        $this->professions = new Professions();
        $this->professionHeaders = new ProfessionHeaders();
        $this->objStarRatedProfession = new StarRatedProfession;
        $this->objMultipleIntelligent = new MultipleIntelligent;
        $this->objApptitude = new Apptitude;
        $this->objPersonality = new Personality;
        $this->objTeenagers = new Teenagers;
        $this->aptitudeThumb = Config::get('constant.APPTITUDE_THUMB_IMAGE_UPLOAD_PATH');
        $this->miThumb = Config::get('constant.MI_THUMB_IMAGE_UPLOAD_PATH');
        $this->personalityThumb = Config::get('constant.PERSONALITY_THUMB_IMAGE_UPLOAD_PATH');
        $this->objTeenagerPromiseScore = new TeenagerPromiseScore();
        $this->careerDetailsPdfUploadedPath = Config::get('constant.CAREER_DETAILS_PDF_UPLOAD_PATH');
        $this->objPromiseParametersMaxScore = new PromiseParametersMaxScore();
        $this->teenagersRepository = $teenagersRepository;
        $this->objSponsorsActivity = new SponsorsActivity;
        $this->objTeenagerScholarshipProgram = new TeenagerScholarshipProgram;
        $this->objPaidComponent = new PaidComponent;
        $this->objDeductedCoins = new DeductedCoins;
        $this->objTeenParentChallenge = new TeenParentChallenge;
        $this->parentsRepository = $parentsRepository;
        $this->templatesRepository = $templatesRepository;
        $this->objInterest = new Interest; 
        $this->objSubject = new ProfessionSubject;
        $this->objTag = new ProfessionTag;
    }

    public function listIndex(){
        $userid = Auth::guard('teenager')->user()->id;
        $totalProfessionCount = $this->professionsRepository->getAllProfessionsCount($userid);
        $teenagerTotalProfessionAttemptedCount = $this->professionsRepository->getTeenagerTotalProfessionAttempted($userid);
        $basketsData = $this->baskets->with('profession')->where('deleted',config::get('constant.ACTIVE_FLAG'))->get();
        return view('teenager.careersListing', compact('basketsData','totalProfessionCount','teenagerTotalProfessionAttemptedCount'));
    }

    public function gridIndex(){
        $userid = Auth::guard('teenager')->user()->id;
        $totalProfessionCount = $this->professionsRepository->getAllProfessionsCount($userid);
        $teenagerTotalProfessionAttemptedCount = $this->professionsRepository->getTeenagerTotalProfessionAttempted($userid);
        $basketsData = $this->baskets->with('profession')->where('deleted',config::get('constant.ACTIVE_FLAG'))->get();
        return view('teenager.careerGrid', compact('basketsData','totalProfessionCount','teenagerTotalProfessionAttemptedCount'));
    }

    public function listGetIndex(){
        $userid = Auth::guard('teenager')->user()->id;
        $basketsData = $this->baskets->with(['profession' => function ($query) {
                            $query->where('deleted' ,config::get('constant.ACTIVE_FLAG'));
                        }])
        				->where('deleted' ,config::get('constant.ACTIVE_FLAG'))
        				->find(Input::get('basket_id'));

        $getTeenagerHML = Helpers::getTeenagerMatchScale($userid);
        
        $professionAttemptedCount = 0;
        $matchScaleCount = [];
        foreach ($basketsData->profession as $k => $v) {
            $professionAttempted = $this->professionsRepository->getTeenagerProfessionAttempted($userid, $v->id, null);
            if(count($professionAttempted) > 0){
                $basketsData['profession'][$k]['attempted'] = 'yes';
                $professionAttemptedCount++;
            }
            $matchScale = isset($getTeenagerHML[$v->id]) ? $getTeenagerHML[$v->id] : '';
            if($matchScale == "match") {
                $basketsData['profession'][$k]['match_scale'] = "match-strong";
                $matchScaleCount['match'][] = $v->id;
            } else if($matchScale == "nomatch") {
                $basketsData['profession'][$k]['match_scale'] = "match-unlikely";
                $matchScaleCount['nomatch'][] = $v->id;
            } else if($matchScale == "moderate") {
                $basketsData['profession'][$k]['match_scale'] = "match-potential";
                $matchScaleCount['moderate'][] = $v->id;
            } else {
                $basketsData['profession'][$k]['match_scale'] = "career-data-nomatch";
            }
        }
        return view('teenager.basic.basketProfessionList', compact('basketsData', 'professionAttemptedCount', 'matchScaleCount'));
    }

    public function gridGetIndex(){
        $user = Auth::guard('teenager')->user();
        $userid = $user->id;

        if($user->t_view_information == 1){
            $countryId = 2; // United States
        }else{
            $countryId = 1; // India
        }

        $basketsData = $this->baskets
                        ->with(['profession' => function ($query) use($countryId) {
                            $query->with(['professionHeaders' => function ($query) use($countryId) {
                                $query->where('country_id',$countryId);
                            }])->where('deleted' ,config::get('constant.ACTIVE_FLAG'));
                        }])
                        ->where('deleted' ,config::get('constant.ACTIVE_FLAG'))
                        ->find(Input::get('basket_id'));
        
        $getTeenagerHML = Helpers::getTeenagerMatchScale($userid);
        $professionAttemptedCount = 0;
        $matchScaleCount = [];
        
        foreach ($basketsData->profession as $k => $v) {
            $professionAttempted = $this->professionsRepository->getTeenagerProfessionAttempted($userid, $v->id,null);
            if(count($professionAttempted)>0){
                $basketsData['profession'][$k]['attempted'] = 'yes';
                $professionAttemptedCount++;
            }
            $matchScale = isset($getTeenagerHML[$v->id]) ? $getTeenagerHML[$v->id] : '';
            if($matchScale == "match") {
                $basketsData['profession'][$k]['match_scale'] = "match-strong";
                $matchScaleCount['match'][] = $v->id;
            } else if($matchScale == "nomatch") {
                $basketsData['profession'][$k]['match_scale'] = "match-unlikely";
                $matchScaleCount['nomatch'][] = $v->id;
            } else if($matchScale == "moderate") {
                $basketsData['profession'][$k]['match_scale'] = "match-potential";
                $matchScaleCount['moderate'][] = $v->id;
            } else {
                $basketsData['profession'][$k]['match_scale'] = "career-data-nomatch";
            }
        }
        
        return view('teenager.basic.basketProfessionGrid', compact('countryId', 'basketsData', 'professionAttemptedCount', 'matchScaleCount'));
    }

    public function gridGetSearch() {
        $user = Auth::guard('teenager')->user();
        $userid = $user->id;
        $searchValue = Input::get('search_text');
        
        if($user->t_view_information == 1) {
            $countryId = 2; // United States
        } else {
            $countryId = 1; // India
        }

        $basketsData = $this->baskets->with(['profession' => function ($query) use($searchValue, $countryId) {
                            $query->with(['professionHeaders' => function ($query) use($searchValue, $countryId) {
                                $query->where('country_id',$countryId);
                            }])->where('pf_name', 'like', '%'.$searchValue.'%')
                            ->where('deleted' ,config::get('constant.ACTIVE_FLAG'));
                        }])
                        ->whereHas('profession', function ($query) use($searchValue, $countryId) {
                            $query->where('pf_name', 'like', '%'.$searchValue.'%')
                            ->where('deleted' ,config::get('constant.ACTIVE_FLAG'));
                        })
                        ->where('deleted' ,config::get('constant.ACTIVE_FLAG'))
                        ->get();
        
        $getTeenagerHML = Helpers::getTeenagerMatchScale($userid);
        $professionAttemptedCount = 0;
        $matchScaleCount = [];
        
        if(count($basketsData) > 0)
        {
            foreach ($basketsData as $key => $value) {
                $professionAttemptedCount = 0;
                foreach($value->profession as $k => $v) {
                    $professionAttempted = $this->professionsRepository->getTeenagerProfessionAttempted($userid, $v->id,null);
                    if(count($professionAttempted)>0){
                        $basketsData[$key]['profession'][$k]['attempted'] = 'yes';
                        $professionAttemptedCount++;
                    }
                    $matchScale = isset($getTeenagerHML[$v->id]) ? $getTeenagerHML[$v->id] : '';
                    if($matchScale == "match") {
                        $basketsData[$key]['profession'][$k]['match_scale'] = "match-strong";
                        $matchScaleCount[$key]['match'][] = $v->id;
                    } else if($matchScale == "nomatch") {
                        $basketsData[$key]['profession'][$k]['match_scale'] = "match-unlikely";
                        $matchScaleCount[$key]['nomatch'][] = $v->id;
                    } else if($matchScale == "moderate") {
                        $basketsData[$key]['profession'][$k]['match_scale'] = "match-potential";
                        $matchScaleCount[$key]['moderate'][] = $v->id;
                    } else {
                        $basketsData[$key]['profession'][$k]['match_scale'] = "career-data-nomatch";
                    }
                }
            }
        }
        
        return view('teenager.basic.basketProfessionGridSearch', compact('searchValue', 'countryId', 'basketsData', 'professionAttemptedCount', 'matchScaleCount'));
    }

    public function listGetSearch() {
        $userid = Auth::guard('teenager')->user()->id;
        $searchValue = Input::get('search_text');
        $basketsData = $this->baskets
                        ->with(['profession' => function ($query) use($searchValue) {
                            $query->where('pf_name', 'like', '%'.$searchValue.'%')
                            ->where('deleted' ,config::get('constant.ACTIVE_FLAG'));
                        }])
                        ->whereHas('profession', function ($query) use($searchValue) {
                            $query->where('pf_name', 'like', '%'.$searchValue.'%')
                            ->where('deleted' ,config::get('constant.ACTIVE_FLAG'));
                        })
                        ->where('deleted' ,config::get('constant.ACTIVE_FLAG'))
                        ->get();
        
        $getTeenagerHML = Helpers::getTeenagerMatchScale($userid);
        $professionAttemptedCount = 0;
        $matchScaleCount = [];
        
        //print_r($basketsData->toArray()); die();
        
        if(count($basketsData) > 0)
        {
            foreach ($basketsData as $key => $value) {
                $professionAttemptedCount = 0;
                foreach($value->profession as $k => $v){
                    $professionAttempted = $this->professionsRepository->getTeenagerProfessionAttempted($userid, $v->id,null);
                    if(count($professionAttempted)>0){
                        $basketsData[$key]['profession'][$k]['attempted'] = 'yes';
                        $professionAttemptedCount++;
                    }
                    $matchScale = isset($getTeenagerHML[$v->id]) ? $getTeenagerHML[$v->id] : '';
                    if($matchScale == "match") {
                        $basketsData[$key]['profession'][$k]['match_scale'] = "match-strong";
                        $matchScaleCount[$key]['match'][] = $v->id;
                    } else if($matchScale == "nomatch") {
                        $basketsData[$key]['profession'][$k]['match_scale'] = "match-unlikely";
                        $matchScaleCount[$key]['nomatch'][] = $v->id;
                    } else if($matchScale == "moderate") {
                        $basketsData[$key]['profession'][$k]['match_scale'] = "match-potential";
                        $matchScaleCount[$key]['moderate'][] = $v->id;
                    } else {
                        $basketsData[$key]['profession'][$k]['match_scale'] = "career-data-nomatch";
                    }
                }
            }
        }
        
        return view('teenager.basic.basketProfessionListSearch', compact('searchValue', 'basketsData', 'professionAttemptedCount', 'matchScaleCount'));
    }

    public function careerDetails($slug)
    {
        $user = Auth::guard('teenager')->user();
        $getTeenagerHML = Helpers::getTeenagerMatchScale($user->id);
        //1=India, 2=US
        $countryId = ($user->t_view_information == 1) ? 2 : 1;

        $professionsData = $this->professions->getProfessionBySlugWithHeadersAndCertificatesAndTags($slug, $countryId, $user->id);
        $professionsData = ($professionsData) ? $professionsData : [];
        if(!$professionsData) {
            return Redirect::to("teenager/list-career")->withErrors("Invalid professions data");
        }

        $getQuestionTemplateForProfession = $this->level4ActivitiesRepository->getQuestionTemplateForProfession($professionsData->id);
        if( isset($getQuestionTemplateForProfession[0]) ) {
            foreach($getQuestionTemplateForProfession as $key => $professionTemplate) {
                $intermediateActivities = $this->level4ActivitiesRepository->getNotAttemptedIntermediateActivities($user->id, $professionsData->id, $professionTemplate->gt_template_id);
                $totalIntermediateQuestion = $this->level4ActivitiesRepository->getNoOfTotalIntermediateQuestionsAttemptedQuestion($user->id, $professionsData->id, $professionTemplate->gt_template_id);
                $response['NoOfTotalQuestions'] = $totalIntermediateQuestion[0]->NoOfTotalQuestions;
                $response['NoOfAttemptedQuestions'] = $totalIntermediateQuestion[0]->NoOfAttemptedQuestions;
                if (empty($intermediateActivities) || ($response['NoOfTotalQuestions'] == $response['NoOfAttemptedQuestions']) || ($response['NoOfTotalQuestions'] < $response['NoOfAttemptedQuestions'])) {
                   $getQuestionTemplateForProfession[$key]->attempted = 'yes';
                } else {
                    $getQuestionTemplateForProfession[$key]->attempted = 'no';
                }
            }
        }
        //echo "<pre/>"; print_r($getQuestionTemplateForProfession->toArray()); die();
        $careerMapHelperArray = Helpers::getCareerMapColumnName();
        $careerMappingdata = [];
        
        foreach ($careerMapHelperArray as $key => $value) {
            $data = [];
            if(isset($professionsData->careerMapping[$value]) && $professionsData->careerMapping[$value] != 'L'){
                $arr = explode("_", $key);
                if($arr[0] == 'apt'){
                    $apptitudeData = $this->objApptitude->getApptitudeDetailBySlug($key);
                    $data['cm_name'] = $apptitudeData->apt_name;   
                    $data['cm_image_url'] = Storage::url($this->aptitudeThumb . $apptitudeData->apt_logo);
                    $data['cm_slug_url'] = url('/teenager/multi-intelligence/'.Config::get('constant.APPTITUDE_TYPE').'/'.$apptitudeData->apt_slug); 
                    $careerMappingdata[] = $data;  
                }
            }
        }
        
        unset($professionsData->careerMapping);
        $professionsData->ability = $careerMappingdata;

        $teenagerStrength = $arraypromiseParametersMaxScoreBySlug = [];
            
        //Get Max score for MI parameters
        $promiseParametersMaxScore = $this->objPromiseParametersMaxScore->getPromiseParametersMaxScore();
        $arraypromiseParametersMaxScore = $promiseParametersMaxScore->toArray();
        foreach($arraypromiseParametersMaxScore as $maxkey=>$maxVal){
            $arraypromiseParametersMaxScoreBySlug[$maxVal['parameter_slug']] = $maxVal;
        }
        
        //Get teenager promise score 
        $teenPromiseScore = $this->objTeenagerPromiseScore->getTeenagerPromiseScore(Auth::guard('teenager')->user()->id);
        if(isset($teenPromiseScore) && count($teenPromiseScore) > 0)
        {
            $teenPromiseScore = $teenPromiseScore->toArray();                
            foreach($teenPromiseScore as $paramkey=>$paramvalue)
            {                    
                if (strpos($paramkey, 'apt_') !== false) {                       
                    $teenAptScore = $this->getTeenScoreInPercentage($arraypromiseParametersMaxScoreBySlug[$paramkey]['parameter_max_score'], $paramvalue);
                    $teenagerStrength[] = (array('score' => $teenAptScore, 'name' => $arraypromiseParametersMaxScoreBySlug[$paramkey]['parameter_name'], 'type' => Config::get('constant.APPTITUDE_TYPE'), 'lowscoreH' => ((100*$arraypromiseParametersMaxScoreBySlug[$paramkey]['parameter_low_score_for_H'])/$arraypromiseParametersMaxScoreBySlug[$paramkey]['parameter_max_score'])));
                }elseif(strpos($paramkey, 'pt_') !== false){
                    $teenAptScore = $this->getTeenScoreInPercentage($arraypromiseParametersMaxScoreBySlug[$paramkey]['parameter_max_score'], $paramvalue);
                    $teenagerStrength[] = (array('score' => $teenAptScore, 'name' => $arraypromiseParametersMaxScoreBySlug[$paramkey]['parameter_name'], 'type' => Config::get('constant.PERSONALITY_TYPE'), 'lowscoreH' => ((100*$arraypromiseParametersMaxScoreBySlug[$paramkey]['parameter_low_score_for_H'])/$arraypromiseParametersMaxScoreBySlug[$paramkey]['parameter_max_score'])));               
                }elseif(strpos($paramkey, 'mit_') !== false){
                    $teenAptScore = $this->getTeenScoreInPercentage($arraypromiseParametersMaxScoreBySlug[$paramkey]['parameter_max_score'], $paramvalue);
                    $teenagerStrength[] = (array('score' => $teenAptScore, 'name' => $arraypromiseParametersMaxScoreBySlug[$paramkey]['parameter_name'], 'type' => Config::get('constant.MULTI_INTELLIGENCE_TYPE'), 'lowscoreH' => ((100*$arraypromiseParametersMaxScoreBySlug[$paramkey]['parameter_low_score_for_H'])/$arraypromiseParametersMaxScoreBySlug[$paramkey]['parameter_max_score'])));
           
                }
            }
        }
        
        $professionCertificationImagePath = Config('constant.PROFESSION_CERTIFICATION_ORIGINAL_IMAGE_UPLOAD_PATH');
        $professionSubjectImagePath = Config('constant.PROFESSION_SUBJECT_ORIGINAL_IMAGE_UPLOAD_PATH');
        $adsDetails = Helpers::getAds($user->id);
        $mediumAdImages = [];
        $largeAdImages = [];
        $bannerAdImages = [];
        if (isset($adsDetails) && !empty($adsDetails)) {
            foreach ($adsDetails as $ad) {
                if ($ad['image'] != '') {
                    $ad['image'] = Storage::url(Config::get('constant.SA_ORIGINAL_IMAGE_UPLOAD_PATH') . $ad['image']);
                } else {
                    $ad['image'] = Storage::url(Config::get('constant.SA_ORIGINAL_IMAGE_UPLOAD_PATH') . 'proteen-logo.png');
                }
                switch ($ad['sizeType']) {
                    case '1':
                        $mediumAdImages[] = $ad;
                        break;

                    case '2':
                        $largeAdImages[] = $ad;
                        break; 

                    case '3':
                        $bannerAdImages[] = $ad;
                        break;

                    default:
                        break;
                };
            }
        }
        $teenagerSponsors = $this->teenagersRepository->getTeenagerSelectedSponsor($user->id);
        $sponsorArr = [];
        if (isset($teenagerSponsors) && count($teenagerSponsors) > 0) {
            foreach ($teenagerSponsors as $key => $val) {
                $sponsorArr[] = $val->ts_sponsor;
            }
        }
        $scholarshipPrograms = [];
        if (!empty($sponsorArr)) {
            $scholarshipPrograms = $this->objSponsorsActivity->getActivityByTypeAndSponsor($sponsorArr, 3);
        }
        $appliedScholarshipDetails = $this->objTeenagerScholarshipProgram->getAllScholarshipProgramsByTeenId($user->id);
        $scholarshipProgramIds = [];
        if (isset($appliedScholarshipDetails) && count($appliedScholarshipDetails) > 0) {
            foreach ($appliedScholarshipDetails as $appliedScholarshipDetail) {
                $scholarshipProgramIds[] = $appliedScholarshipDetail->tsp_activity_id;
            }
        }
        $expiredScholarshipPrograms = $this->objSponsorsActivity->getExpiredActivityByTypeAndSponsor($sponsorArr, 3);
        $expiredActivityIds = [];
        if (isset($expiredScholarshipPrograms) && count($expiredScholarshipPrograms) > 0) {
            foreach ($expiredScholarshipPrograms as $expiredScholarshipProgram) {
                $expiredActivityIds[] = $expiredScholarshipProgram->id;
            }
        }
        $exceptScholarshipIds = array_unique(array_merge($scholarshipProgramIds, $expiredActivityIds));
        $componentsData = $this->objPaidComponent->getPaidComponentsData(Config::get('constant.ADVANCE_ACTIVITY'));
        $deductedCoinsDetail = (isset($componentsData->id)) ? $this->objDeductedCoins->getDeductedCoinsDetailByIdForLS($user->id, $componentsData->id, 1) : [];
        $remainingDaysForActivity = 0;
        if (!empty($deductedCoinsDetail[0])) {
            $remainingDaysForActivity = Helpers::calculateRemainingDays($deductedCoinsDetail[0]->dc_end_date);
        }
        $teenagerParents = $this->teenagersRepository->getTeenParents($user->id);
        $challengedAcceptedParents = $this->objTeenParentChallenge->getChallengedParentAndMentorList($professionsData->id, $user->id);
        return view('teenager.careerDetail', compact('getQuestionTemplateForProfession', 'getTeenagerHML', 'professionsData', 'countryId', 'professionCertificationImagePath', 'professionSubjectImagePath', 'teenagerStrength', 'mediumAdImages', 'largeAdImages', 'bannerAdImages', 'scholarshipPrograms', 'exceptScholarshipIds', 'scholarshipProgramIds', 'expiredActivityIds', 'remainingDaysForActivity', 'componentsData', 'teenagerParents', 'challengedAcceptedParents'));
    }

    public function getTeenagerWhoStarRatedCareer()
    {
        $user = Auth::guard('teenager')->user();
        $pageNo = Input::get('page_no');
        $record = $pageNo * 10;
        $professionId = Input::get('professionId');
        $teenagerData = $this->objTeenagers->getAllTeenWhoStarRatedCareer($record, $professionId, $user->id);        
        $view = view('teenager.basic.level3TeenagerFansForCareer',compact('teenagerData'));
        $response['teenagersCount'] = count($teenagerData);
        $response['teenagers'] = $view->render();
        $response['pageNo'] = $pageNo+1;
        return $response;
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

    public function addStarToCareer(Request $request) 
    {
        $careerId = Input::get('careerId');
        $careerDetails['srp_teenager_id'] = Auth::guard('teenager')->user()->id;
        $careerDetails['srp_profession_id'] = $careerId;
        $return = $this->objStarRatedProfession->addStarToCareer($careerDetails);
        
        $response['status'] = 1;
        $response['message'] = "Added";
        return response()->json($response, 200);
        exit;
    }

    public function getSearchDropdown(Request $request) 
    {
        $queId = Input::get('queId');
        $return = '';
        if($queId == 1) // Industry
        {
            $data = $this->baskets->getActiveBasketsOrderByName();
            $return .= '<div class="form-group custom-select bg-blue"><select tabindex="8" class="form-control" id="answerId" onchange="fetchDropdownResult();">
                <option value="0">All Industry</option>';
            foreach ($data as $key => $value) {
                    $return .= '<option value="'.$value->id.'">'.$value->b_name.'</option>';
            }
            $return .= '</select></div>';
        }
        else if ($queId == 2) // Careers
        {
            $data = $this->professions->getActiveProfessionsOrderByName();
            $return .= '<div class="form-group custom-select bg-blue" id="answerDropdown"><select tabindex="8" class="form-control" id="answerId" onchange="fetchDropdownResult();">
                <option value="0">All Careers</option>';
            foreach ($data as $key => $value) {
                    $return .= '<option value="'.$value->id.'">'.$value->pf_name.'</option>';
            }
            $return .= '</select></div>';
        } 
        else if ($queId == 3) // Interest
        {
            $data = $this->objInterest->getActiveInterest();
            $return .= '<div class="form-group custom-select bg-blue" id="answerDropdown"><select tabindex="8" class="form-control" id="answerId" onchange="fetchDropdownResult();">
                <option value="0">All Interests</option>';
            foreach ($data as $key => $value) {
                    $return .= '<option value="'.$value->id.'">'.$value->it_name.'</option>';
            }
            $return .= '</select></div>';
        } 
        else if ($queId == 4) // Strength
        {
            $personality = $this->objPersonality->getActivepersonality();
            $ptData = [];
            if (!empty($personality)) {
                foreach ($personality as $ptKey => $ptVal) {
                    $ptArr = [];
                    $ptArr['id'] = $ptVal->id;
                    $ptArr['name'] = $ptVal->pt_name;
                    $ptArr['slug'] = $ptVal->pt_slug;
                    $ptArr['type'] = Config::get('constant.PERSONALITY_TYPE');
                    $ptData[] = $ptArr;
                }
            }

            $apptitude = $this->objApptitude->getActiveApptitude();
            $aptData = [];
            if (!empty($apptitude)) {
                foreach ($apptitude as $aptKey => $aptVal) {
                    $aptArr = [];
                    $aptArr['id'] = $aptVal->id;
                    $aptArr['name'] = $aptVal->apt_name;
                    $aptArr['slug'] = $aptVal->apt_slug;
                    $aptArr['type'] = Config::get('constant.APPTITUDE_TYPE');
                    $aptData[] = $aptArr;
                }
            }

            $mi = $this->objMultipleIntelligent->getActiveMultipleIntelligent();
            $miData = [];
            if (!empty($mi)) {
                foreach ($mi as $key => $val) {
                    $miArr = [];
                    $miArr['id'] = $val->id;
                    $miArr['name'] = $val->mit_name;
                    $miArr['slug'] = $val->mi_slug;
                    $miArr['type'] = Config::get('constant.MULTI_INTELLIGENCE_TYPE');
                    $miData[] = $miArr;
                }
            }
            $data = array_merge($aptData, $ptData, $miData);
            $return .= '<div class="form-group custom-select bg-blue" id="answerDropdown"><select tabindex="8" class="form-control" id="answerId" onchange="fetchDropdownResult();">
                <option value="0">All Strengths</option>';
            foreach ($data as $key => $value) {
                    $return .= '<option value="'.$value['id'].'">'.$value['name'].'</option>';
            }
            $return .= '</select></div>';
        } 
        else if ($queId == 5) // Subjects
        {
            $data = $this->objSubject->getAllProfessionSubjects();
            $return .= '<div class="form-group custom-select bg-blue" id="answerDropdown"><select tabindex="8" class="form-control" id="answerId" onchange="fetchDropdownResult();">
                <option value="0">All Subjects</option>';
            foreach ($data as $key => $value) {
                    $return .= '<option value="'.$value->id.'">'.$value->ps_name.'</option>';
            }
            $return .= '</select></div>';
        } 
        else if ($queId == 6) // Tags
        {
            $data = $this->objTag->getAllProfessionTags();
            $return .= '<div class="form-group custom-select bg-blue" id="answerDropdown"><select tabindex="8" class="form-control" id="answerId" onchange="fetchDropdownResult();">
                <option value="0">All Tags</option>';
            foreach ($data as $key => $value) {
                    $return .= '<option value="'.$value->id.'">'.$value->pt_name.'</option>';
            }
            $return .= '</select></div>';
        } 
        return $return;
    }

    public function getDropdownSearchResult(Request $request){
        $queId = Input::get('queId');
        $ansId = Input::get('ansId');
        $view = Input::get('view');
        $user = Auth::guard('teenager')->user();
        $userid = $user->id;

        if($user->t_view_information == 1){
            $countryId = 2; // United States
        }else{
            $countryId = 1; // India
        }

        if($ansId != 0){
            if($queId == 1) // Industry
            {
                $basketsData = $this->baskets->getBasketsAndProfessionWithAttemptedProfessionByBasketId($ansId, $userid, $countryId);
                if($view == 'GRID'){
                    return view('teenager.basic.level3CareerGridView', compact('basketsData','view','countryId'));
                }elseif($view == 'LIST'){
                    return view('teenager.basic.level3CareerListView', compact('basketsData','view'));
                }
            }
            elseif ($queId == 2) // Careers
            {
                $basketsData = $this->baskets->getBasketsAndProfessionWithAttemptedProfessionByProfessionId($ansId, $userid, $countryId);
                $totalProfessionCount = $this->professions->getActiveProfessionsCountByBaketId($basketsData[0]->id);
                if($view == 'GRID'){
                    return view('teenager.basic.level3CareerGridView', compact('basketsData','totalProfessionCount','countryId'));
                }elseif($view == 'LIST'){
                    return view('teenager.basic.level3CareerListView', compact('basketsData','totalProfessionCount'));
                }
            }
        }
        else // All Industry with Careers
        {         
            $basketsData = $this->baskets->getAllBasketsAndProfessionWithAttemptedProfession($userid, $countryId);
            if($view == 'GRID'){
                return view('teenager.basic.level3CareerGridView', compact('basketsData','view','countryId'));
            }elseif($view == 'LIST'){
                return view('teenager.basic.level3CareerListView', compact('basketsData','view'));
            }
        }
    }

    public function getTeenagerCareers()
    {
        $user = Auth::guard('teenager')->user();
        $basketsData = $this->baskets->getBasketsAndProfessionWithAttemptedProfessionByUserId($user->id);
        $teenagerTotalProfessionAttemptedCount = $this->professions->getTeenagerTotalProfessionAttemptedOutOfStarRated($user->id);
        $teenagerTotalProfessionStarRatedCount = $this->professions->getteenagerTotalProfessionStarRatedCount($user->id);
        $professionImagePath = Config('constant.PROFESSION_ORIGINAL_IMAGE_UPLOAD_PATH');
        $filterData = Helpers::getMyCareerPageFilter();
        return view('teenager.myCareers', compact('basketsData', 'professionImagePath','teenagerTotalProfessionAttemptedCount','teenagerTotalProfessionStarRatedCount', 'filterData'));
    }

    public function getTeenagerCareersSearch()
    {
        $user = Auth::guard('teenager')->user();
        $search_text = Input::get('search_text');
        $basketsData = $this->baskets->getBasketsAndProfessionWithAttemptedProfessionByUserIdAndSearchValue($user->id, $search_text);
        $professionImagePath = Config('constant.PROFESSION_ORIGINAL_IMAGE_UPLOAD_PATH');
        return view('teenager.basic.level3MyCareerSearch', compact('basketsData', 'professionImagePath','teenagerTotalProfessionAttemptedCount','teenagerTotalProfessionStarRatedCount'));
    }

    public function getCareerConsideration() {
        $user = Auth::guard('teenager')->user();

        if($user->id > 0) {
            $teenagerCareers = $this->professionsRepository->getMyCareers($user->id);
            $getAllActiveProfessions = Helpers::getActiveProfessions();
            $getTeenagerHML = Helpers::getTeenagerMatchScale($user->id);
            
            if(!$getTeenagerHML) {
                $response['status'] = 0;
                $response['message'] = "Please attempt at least one section of Profile Builder to view your suggested careers!";
                return response()->json($response, 200);
                exit;
            }

            $teenagerCareersIds = (isset($teenagerCareers[0]) && count($teenagerCareers[0]) > 0) ? Helpers::getTeenagerCareersIds($user->id)->toArray() : [];

            $match = $nomatch = $moderate = [];

            if($getAllActiveProfessions) {
                foreach($getAllActiveProfessions as $profession) {
                    $array = [];
                    $array['id'] = $profession->id;
                    $array['match_scale'] = isset($getTeenagerHML[$profession->id]) ? $getTeenagerHML[$profession->id] : '';
                    $array['added_my_career'] = (in_array($profession->id, $teenagerCareersIds)) ? 1 : 0;
                    $array['is_completed'] = 0;
                    $array['pf_name'] = $profession->pf_name;
                    $array['pf_slug'] = $profession->pf_slug;
                    if($array['match_scale'] == "match") {
                        $match[] = $array;
                    } else if($array['match_scale'] == "nomatch") {
                        $nomatch[] = $array;
                    } else if($array['match_scale'] == "moderate") {
                        $moderate[] = $array;
                    } else {
                        $notSetArray[] = $array;
                    }
                }
                if(count($match) < 1 && count($moderate) < 1 && count($nomatch) > 0) {
                    $careerConsideration = $nomatch;
                } else if(count($match) > 0 || count($moderate) > 0) {
                    $careerConsideration = array_merge($match, $moderate);
                } else {
                    $careerConsideration = $notSetArray;
                }

                

                return view('teenager.basic.careerConsideration', compact('careerConsideration', 'getTeenagerHML'));
            } else {
                $response['status'] = 0;
                $response['message'] = "No professions found!";
                return response()->json($response, 200);
                exit;
            }
        } 
        $response['status'] = 0;
        $response['message'] = "No professions found!";
        return response()->json($response, 200);
        exit;
    }

    public function getCareerPdf($slug)
    {
        $user = Auth::guard('teenager')->user();
        $getTeenagerHML = Helpers::getTeenagerMatchScale($user->id);
        //1=India, 2=US
        $countryId = ($user->t_view_information == 1) ? 2 : 1;

        $professionsData = $this->professions->getProfessionBySlugWithHeadersAndCertificatesAndTags($slug, $countryId, $user->id);
        $professionsData = ($professionsData) ? $professionsData : [];
        if(!$professionsData) {
            return Redirect::to("teenager/list-career")->withErrors("Invalid professions data");
        }
        $careerMapHelperArray = Helpers::getCareerMapColumnName();
        $careerMappingdata = [];
        
        foreach ($careerMapHelperArray as $key => $value) {
            $data = [];
            if(isset($professionsData->careerMapping[$value]) && $professionsData->careerMapping[$value] != 'L'){
                $arr = explode("_", $key);
                if($arr[0] == 'apt'){
                    $apptitudeData = $this->objApptitude->getApptitudeDetailBySlug($key);
                    $data['cm_name'] = $apptitudeData->apt_name;   
                    $data['cm_image_url'] = Storage::url($this->aptitudeThumb . $apptitudeData->apt_logo);
                    $data['cm_slug_url'] = url('/teenager/multi-intelligence/'.Config::get('constant.APPTITUDE_TYPE').'/'.$apptitudeData->apt_slug); 
                    $careerMappingdata[] = $data;  
                }
            }
        }
        
        unset($professionsData->careerMapping);
        $professionsData->ability = $careerMappingdata;

        $teenagerStrength = $arraypromiseParametersMaxScoreBySlug = [];
            
        //Get Max score for MI parameters
        $promiseParametersMaxScore = $this->objPromiseParametersMaxScore->getPromiseParametersMaxScore();
        $arraypromiseParametersMaxScore = $promiseParametersMaxScore->toArray();
        foreach($arraypromiseParametersMaxScore as $maxkey=>$maxVal){
            $arraypromiseParametersMaxScoreBySlug[$maxVal['parameter_slug']] = $maxVal;
        }

        //Get teenager promise score 
        $teenPromiseScore = $this->objTeenagerPromiseScore->getTeenagerPromiseScore(Auth::guard('teenager')->user()->id);
        if(isset($teenPromiseScore) && count($teenPromiseScore) > 0)
        {
            $teenPromiseScore = $teenPromiseScore->toArray();                
            foreach($teenPromiseScore as $paramkey=>$paramvalue)
            {                    
                if (strpos($paramkey, 'apt_') !== false) {                       
                    $teenAptScore = $this->getTeenScoreInPercentage($arraypromiseParametersMaxScoreBySlug[$paramkey]['parameter_max_score'], $paramvalue);
                    $teenagerStrength[] = (array('score' => $teenAptScore, 'name' => $arraypromiseParametersMaxScoreBySlug[$paramkey]['parameter_name'], 'type' => Config::get('constant.APPTITUDE_TYPE'), 'lowscoreH' => ((100*$arraypromiseParametersMaxScoreBySlug[$paramkey]['parameter_low_score_for_H'])/$arraypromiseParametersMaxScoreBySlug[$paramkey]['parameter_max_score'])));
                }elseif(strpos($paramkey, 'pt_') !== false){
                    $teenAptScore = $this->getTeenScoreInPercentage($arraypromiseParametersMaxScoreBySlug[$paramkey]['parameter_max_score'], $paramvalue);
                    $teenagerStrength[] = (array('score' => $teenAptScore, 'name' => $arraypromiseParametersMaxScoreBySlug[$paramkey]['parameter_name'], 'type' => Config::get('constant.PERSONALITY_TYPE'), 'lowscoreH' => ((100*$arraypromiseParametersMaxScoreBySlug[$paramkey]['parameter_low_score_for_H'])/$arraypromiseParametersMaxScoreBySlug[$paramkey]['parameter_max_score'])));               
                }elseif(strpos($paramkey, 'mit_') !== false){
                    $teenAptScore = $this->getTeenScoreInPercentage($arraypromiseParametersMaxScoreBySlug[$paramkey]['parameter_max_score'], $paramvalue);
                    $teenagerStrength[] = (array('score' => $teenAptScore, 'name' => $arraypromiseParametersMaxScoreBySlug[$paramkey]['parameter_name'], 'type' => Config::get('constant.MULTI_INTELLIGENCE_TYPE'), 'lowscoreH' => ((100*$arraypromiseParametersMaxScoreBySlug[$paramkey]['parameter_low_score_for_H'])/$arraypromiseParametersMaxScoreBySlug[$paramkey]['parameter_max_score'])));

                }
            }
        }
        $professionCertificationImagePath = Config('constant.PROFESSION_CERTIFICATION_ORIGINAL_IMAGE_UPLOAD_PATH');
        $professionSubjectImagePath = Config('constant.PROFESSION_SUBJECT_ORIGINAL_IMAGE_UPLOAD_PATH');
        
        $fileName = $professionsData->pf_slug."-".time().'.pdf';
        $checkPDF = PDF::loadView('teenager.careerDetailPdf',compact('getTeenagerHML', 'professionsData', 'countryId', 'professionCertificationImagePath', 'professionSubjectImagePath', 'teenagerStrength', 'mediumAdImages', 'largeAdImages', 'bannerAdImages','chartHtml'))->save($this->careerDetailsPdfUploadedPath.$fileName);
        
        if(isset($checkPDF))
        {
            $pdfPath = public_path($this->careerDetailsPdfUploadedPath.$fileName);           
            header('Content-type: application/pdf');
            header('Content-Disposition: inline; filename='.$fileName);
            header('Content-Transfer-Encoding: binary');
            header('Content-Length: ' . filesize($pdfPath));
            @readfile($pdfPath);
            exit;                      
        }
    }

    //Store records of teenager scholarship program
    public function applyForScholarshipProgram()
    {
        $activityDetails = [];
        $activityDetails['tsp_activity_id'] = Input::get('activityId');
        $activityDetails['tsp_teenager_id'] = Auth::guard('teenager')->user()->id;
        $checkIfAlreadyApplied = $this->objTeenagerScholarshipProgram->getScholarshipProgramDetailsByActivity($activityDetails);
        if (isset($checkIfAlreadyApplied) && !empty($checkIfAlreadyApplied)) {
            $message = "applied";
        } else {
            $appliedForScholarship = $this->objTeenagerScholarshipProgram->StoreDetailsForScholarshipProgram($activityDetails);
            $message = "success"; 
        }
        return $message;
    }

    //Store records for parent/mentor challenge request
    public function challengeToParentAndMentor()
    {
        $teenagerId = Auth::guard('teenager')->user()->id;
        $professionId = Input::get('professionId');
        $parentId =Input::get('parentId');

        $saveData = [];
        $saveData['tpc_teenager_id'] = $teenagerId;
        $saveData['tpc_parent_id'] = $parentId;
        $saveData['tpc_profession_id'] = $professionId;

        $result = $this->objTeenParentChallenge->getTeenParentRequestDetail($saveData);

        if (isset($result) && $result) {
            $response['status'] = 0;
            $response['message'] = trans('labels.parentchallengeexist');
            return response()->json($response, 200);
            exit;
        } else {
            $this->objTeenParentChallenge->saveTeenParentRequestDetail($saveData);
            $teenDetail = $this->teenagersRepository->getTeenagerByTeenagerId($teenagerId);
            $parentDetail = $this->parentsRepository->getParentDetailByParentId($parentId);
            $professionName =  $this->professionsRepository->getProfessionNameById($professionId);
            //send mail
            $replaceArray = array();
            $replaceArray['USER_NAME'] = $parentDetail['p_first_name'];
            $replaceArray['TEEN_NAME'] = $teenDetail['t_name'];
            $replaceArray['PROFESSION_NAME'] = $professionName;
            $emailTemplateContent = $this->templatesRepository->getEmailTemplateDataByName(Config::get('constant.PARENT_TEEN_CHALLEGE_REQUEST_TEMPLATE'));
            $content = $this->templatesRepository->getEmailContent($emailTemplateContent->et_body, $replaceArray);
            $data = array();
            $data['subject'] = $emailTemplateContent->et_subject;
            $data['toEmail'] = $parentDetail['p_email'];
            $data['toName'] = $parentDetail['p_first_name'];
            $data['content'] = $content;

            Mail::send(['html' => 'emails.Template'], $data , function ($m) use ($data) {
                $m->from(Config::get('constant.FROM_MAIL_ID'), 'Teen Challenge ');
                $m->subject($data['subject']);
                $m->to($data['toEmail'], $data['toName']);
            });
            $response['status'] = 1;
            $response['message'] = trans('labels.parentchallengesuccess');
            return response()->json($response, 200);
            exit;
        }
    }

    //Returns view for challenge play section
    public function getChallengedParentAndMentorList()
    {
        $teenId = Auth::guard('teenager')->user()->id;
        $professionId = Input::get('professionId');
        $teenagerParents = $this->teenagersRepository->getTeenParents($teenId);
        $challengedAcceptedParents = $this->objTeenParentChallenge->getChallengedParentAndMentorList($professionId, $teenId); 
        return view('teenager.basic.careerChallengePlaySection', compact('teenagerParents', 'challengedAcceptedParents'));
        exit;
    }

    //Returns view for teen/parent challenge score box
    public function getChallengeScoreDetails()
    {
        $teenId = Auth::guard('teenager')->user()->id;
        $professionId = Input::get('professionId');
        $parentId = Input::get('parentId');
        $professionName = '';
        $getProfessionNameFromProfessionId = $this->professionsRepository->getProfessionsByProfessionId($professionId);
        if (isset($getProfessionNameFromProfessionId[0]) && !empty($getProfessionNameFromProfessionId[0])) {
            $professionName = $getProfessionNameFromProfessionId[0]->pf_name;
        }
        $level4Booster = Helpers::level4Booster($professionId, $teenId);
        $level4ParentBooster = Helpers::level4ParentBooster($professionId, $parentId);
        $teenDetail = $this->teenagersRepository->getTeenagerByTeenagerId($teenId);
        $parentDetail = $this->parentsRepository->getParentDetailByParentId($parentId);
        $rank = 0;
        foreach($level4Booster['allData'] AS $key => $value) {
            if ($level4ParentBooster['yourScore'] != 0) {
              if ($level4ParentBooster['yourScore'] == $value) {
                $level4ParentBooster['yourRank'] = $key+1;
              }
            } else {
                $level4ParentBooster['yourRank'] = 0;
            }
        }
        foreach($level4Booster['allData'] AS $key => $value) {
            if ($level4Booster['yourScore'] != 0) {
              if ($level4Booster['yourScore'] == $value) {
                $rank = $key + 1;
              }
            } else {
                $rank = 0;
            }
        }
        return view('teenager.basic.careerChallengeScoreBox', compact('level4Booster','level4ParentBooster','professionName','teenDetail','parentDetail','rank'));
        exit;
    }

    public function getMyCareerDropdownSearchResult()
    {
        $queId = Input::get('queId');
        $ansId = Input::get('ansId');
        $user = Auth::guard('teenager')->user();
        $userId = $user->id;
        if($user->t_view_information == 1){
            $countryId = 2; // United States
        }else{
            $countryId = 1; // India
        }

        if($ansId != 0){
            if($queId == 1) // Industry
            {
                $basketsData = $this->baskets->getBasketsAndProfessionWithAttemptedProfessionByBasketIdForUser($ansId, $userId, $countryId);
            }
            elseif ($queId == 2) // Careers
            {
                $basketsData = $this->baskets->getBasketsAndProfessionWithAttemptedProfessionByProfessionIdForUser($ansId, $userId, $countryId);
                
            } 
        }
        else // All Industry with Careers
        {         
            $basketsData = $this->baskets->getBasketsAndProfessionWithAttemptedProfessionByUserId($userId, $countryId);
        }
        $professionImagePath = Config('constant.PROFESSION_ORIGINAL_IMAGE_UPLOAD_PATH');
        return view('teenager.basic.searchdMyCareers', compact('basketsData', 'professionImagePath'));
    }

}