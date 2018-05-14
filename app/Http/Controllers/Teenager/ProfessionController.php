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
use App\TemplateDeductedCoins;
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
use App\MultipleIntelligentScale;
use App\ApptitudeTypeScale;
use App\PersonalityScale;
use App\Services\FileStorage\Contracts\FileStorageRepository;

class ProfessionController extends Controller {

    public function __construct(Level4ActivitiesRepository $level4ActivitiesRepository, ProfessionsRepository $professionsRepository, BasketsRepository $basketsRepository, TeenagersRepository $teenagersRepository, ParentsRepository $parentsRepository, TemplatesRepository $templatesRepository, FileStorageRepository $fileStorageRepository) 
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
        $this->objMIScale = new MultipleIntelligentScale();
        $this->objApptitudeScale = new ApptitudeTypeScale();
        $this->objPersonalityScale = new PersonalityScale();
        $this->fileStorageRepository = $fileStorageRepository;
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
            // $professionAttempted = $this->professionsRepository->getTeenagerProfessionAttempted($userid, $v->id, null);
            // if(count($professionAttempted) > 0){
            //     $basketsData['profession'][$k]['attempted'] = 'yes';
            //     $professionAttemptedCount++;
            // }

            $professionAttempted = Helpers::getProfessionCompletePercentage(Auth::guard('teenager')->user()->id, $v->id);
            if(isset($professionAttempted) && $professionAttempted == 100){
                $basketsData['profession'][$k]['attempted'] = 'yes';
                $professionAttemptedCount++;
            } else {
                $basketsData['profession'][$k]['attempted'] = '';
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
                                $query->where('country_id',$countryId)->whereIn('pfic_title', ['average_per_year_salary', 'profession_outlook']);
                            }])->where('deleted' ,config::get('constant.ACTIVE_FLAG'));
                        }])
                        ->where('deleted' ,config::get('constant.ACTIVE_FLAG'))
                        ->find(Input::get('basket_id'));
        
        $getTeenagerHML = Helpers::getTeenagerMatchScale($userid);
        $professionAttemptedCount = 0;
        $matchScaleCount = [];
        
        foreach ($basketsData->profession as $k => $v) {
            $professionAttempted = Helpers::getProfessionCompletePercentage($userid, $v->id); 
            if(isset($professionAttempted) && $professionAttempted == 100) {
               $basketsData['profession'][$k]['attempted'] = 1; 
               $professionAttemptedCount++;
            } else {
                $basketsData['profession'][$k]['attempted'] = 0; 
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
                            }])->where(function($query) use($searchValue) {
                                    $query->where('pf_name', 'like', '%'.$searchValue.'%')
                                        ->where('deleted' ,config::get('constant.ACTIVE_FLAG'));   
                                })
                             ->orWhere(function($query) use($searchValue) {
                                    $query->orWhere('pf_profession_alias', 'LIKE', '%'.$searchValue.'%')         
                                        ->where('deleted' ,config::get('constant.ACTIVE_FLAG'));
                                });
                        }])
                        ->whereHas('profession', function ($query) use($searchValue, $countryId) {
                            $query->where(function($query) use($searchValue) {
                                    $query->where('pf_name', 'like', '%'.$searchValue.'%')
                                        ->where('deleted' ,config::get('constant.ACTIVE_FLAG'));   
                                })
                             ->orWhere(function($query) use($searchValue) {
                                    $query->orWhere('pf_profession_alias', 'LIKE', '%'.$searchValue.'%')         
                                        ->where('deleted' ,config::get('constant.ACTIVE_FLAG'));
                                });
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
                    // $professionAttempted = $this->professionsRepository->getTeenagerProfessionAttempted($userid, $v->id,null);
                    // if(count($professionAttempted)>0){
                    //     $basketsData[$key]['profession'][$k]['attempted'] = 'yes';
                    //     $professionAttemptedCount++;
                    // }
                    $professionAttempted = Helpers::getProfessionCompletePercentage($userid, $v->id);
                    if(isset($professionAttempted) && $professionAttempted == 100){
                        $basketsData[$key]['profession'][$k]['attempted'] = 1;
                        $professionAttemptedCount++;
                    } else {
                        $basketsData[$key]['profession'][$k]['attempted'] = 0;
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
                            $query->where(function($query) use($searchValue) {
                                    $query->where('pf_name', 'like', '%'.$searchValue.'%')
                                        ->where('deleted' ,config::get('constant.ACTIVE_FLAG'));   
                                })
                             ->orWhere(function($query) use($searchValue) {
                                    $query->orWhere('pf_profession_alias', 'LIKE', '%'.$searchValue.'%')         
                                        ->where('deleted' ,config::get('constant.ACTIVE_FLAG'));
                                });
                        }])
                        ->whereHas('profession', function ($query) use($searchValue) {
                            $query->where(function($query) use($searchValue) {
                                    $query->where('pf_name', 'like', '%'.$searchValue.'%')
                                        ->where('deleted' ,config::get('constant.ACTIVE_FLAG'));   
                                })
                             ->orWhere(function($query) use($searchValue) {
                                    $query->orWhere('pf_profession_alias', 'LIKE', '%'.$searchValue.'%')         
                                        ->where('deleted' ,config::get('constant.ACTIVE_FLAG'));
                                });
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
                    // $professionAttempted = $this->professionsRepository->getTeenagerProfessionAttempted($userid, $v->id,null);
                    // if(count($professionAttempted)>0){
                    //     $basketsData[$key]['profession'][$k]['attempted'] = 'yes';
                    //     $professionAttemptedCount++;
                    // }


                    $professionAttempted = Helpers::getProfessionCompletePercentage($userid, $v->id);
                    if(isset($professionAttempted) && $professionAttempted == 100){
                        $basketsData[$key]['profession'][$k]['attempted'] = 'yes';
                        $professionAttemptedCount++;
                    } else {
                        $basketsData[$key]['profession'][$k]['attempted'] = '';
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
        
        $totalBasicQuestion = $this->level4ActivitiesRepository->getNoOfTotalQuestionsAttemptedQuestion($user->id, $professionsData->id);
        $level4BasicPlayed = 0;
        if(isset($totalBasicQuestion[0]->NoOfTotalQuestions) && $totalBasicQuestion[0]->NoOfTotalQuestions > 0 && ($totalBasicQuestion[0]->NoOfTotalQuestions <= $totalBasicQuestion[0]->NoOfAttemptedQuestions) ) {
            $level4BasicPlayed = 1;
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
        
        $professionPromiseParameters = Helpers::getCareerMapColumnName();
        
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
                    $careerMappingKey = $professionPromiseParameters[$paramkey];
                    $careerMappingHML = $professionsData->careerMapping->$careerMappingKey;
                    //get aptitude detail 
                    $aptitudeDetail =  $this->objApptitude->getApptitudeDetailBySlug($paramkey); 
                    $aptituteScale = $this->objApptitudeScale->getApptitudeScaleById($aptitudeDetail->id);
                    if($careerMappingHML == 'H'){
                        if($aptituteScale['ats_high_min_score'] == $aptituteScale['ats_high_max_score']){
                            $blueBand = $aptituteScale['ats_moderate_max_score'];
                        }else{
                            $blueBand = $aptituteScale['ats_high_min_score']; 
                        }  
                    }elseif($careerMappingHML == 'M')
                    {
                        $blueBand = $aptituteScale['ats_moderate_min_score'];
                    }else{
                        $blueBand = $aptituteScale['ats_low_max_score']/2;
                    }
                   
                    $teenAptScore = $this->getTeenScoreInPercentage($arraypromiseParametersMaxScoreBySlug[$paramkey]['parameter_max_score'], $paramvalue);
                    $teenagerStrength[] = (array('score' => $teenAptScore, 'name' => $arraypromiseParametersMaxScoreBySlug[$paramkey]['parameter_name'], 'type' => Config::get('constant.APPTITUDE_TYPE'), 'lowscoreH' => ((100*$blueBand)/$arraypromiseParametersMaxScoreBySlug[$paramkey]['parameter_max_score']), 'slug' => $paramkey));
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
                            $blueBand = $personalityScale['pts_high_min_score']; 
                        }  
                    }elseif($careerMappingHML == 'M')
                    {
                        $blueBand = $personalityScale['pts_moderate_min_score'];
                    }else{
                        $blueBand = $personalityScale['pts_low_max_score']/2;
                    }
                    $teenAptScore = $this->getTeenScoreInPercentage($arraypromiseParametersMaxScoreBySlug[$paramkey]['parameter_max_score'], $paramvalue);
                    $teenagerStrength[] = (array('score' => $teenAptScore, 'name' => $arraypromiseParametersMaxScoreBySlug[$paramkey]['parameter_name'], 'type' => Config::get('constant.PERSONALITY_TYPE'), 'lowscoreH' => ((100*$blueBand)/$arraypromiseParametersMaxScoreBySlug[$paramkey]['parameter_max_score']), 'slug' => $paramkey));               
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
                            $blueBand = $miScale['mts_high_min_score']; 
                        }  
                    }elseif($careerMappingHML == 'M')
                    {
                        $blueBand = $miScale['mts_moderate_min_score'];
                    }else{
                        $blueBand = $miScale['mts_low_max_score']/2;
                    }
                    $teenAptScore = $this->getTeenScoreInPercentage($arraypromiseParametersMaxScoreBySlug[$paramkey]['parameter_max_score'], $paramvalue);
                    $teenagerStrength[] = (array('score' => $teenAptScore, 'name' => $arraypromiseParametersMaxScoreBySlug[$paramkey]['parameter_name'], 'type' => Config::get('constant.MULTI_INTELLIGENCE_TYPE'), 'lowscoreH' => ((100*$blueBand)/$arraypromiseParametersMaxScoreBySlug[$paramkey]['parameter_max_score']), 'slug' => $paramkey));
           
                }
            }
        }
        
        $professionCertificationImagePath = Config('constant.PROFESSION_CERTIFICATION_THUMB_IMAGE_UPLOAD_PATH');
        $professionSubjectImagePath = Config('constant.PROFESSION_SUBJECT_THUMB_IMAGE_UPLOAD_PATH');
        $adsDetails = Helpers::getAds($user->id);
        $mediumAdImages = [];
        $largeAdImages = [];
        $bannerAdImages = [];
        
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

        //Advance Activity Coins consumption details
        $componentsData = $this->objPaidComponent->getPaidComponentsData(Config::get('constant.ADVANCE_ACTIVITY'));
        $deductedCoinsDetail = (isset($componentsData->id)) ? $this->objDeductedCoins->getDeductedCoinsDetailById($user->id, $componentsData->id, 1, $professionsData->id) : [];
        $remainingDaysForActivity = 0;
        if (!empty($deductedCoinsDetail[0])) {
            $remainingDaysForActivity = Helpers::calculateRemainingDays($deductedCoinsDetail[0]->dc_end_date);
        }

        //Challenge parents details
        $teenagerParents = $this->teenagersRepository->getTeenParents($user->id);
        $challengedAcceptedParents = $this->objTeenParentChallenge->getChallengedParentAndMentorList($professionsData->id, $user->id);

        //Leaderboard details
        $leaderboardTeenagers = $this->teenagersRepository->getTeenagerListingWithBoosterPointsByProfession($professionsData->id, 0);
        $nextleaderboardTeenagers = $this->teenagersRepository->getTeenagerListingWithBoosterPointsByProfession($professionsData->id, 1);

        //Promise plus coins consumption details
        $promisePlusComponent = $this->objPaidComponent->getPaidComponentsData(Config::get('constant.PROMISE_PLUS'));
        $promisePluseDeductedCoinsDetail = (isset($promisePlusComponent->id)) ? $this->objDeductedCoins->getDeductedCoinsDetailById($user->id, $promisePlusComponent->id, 1, $professionsData->id) : [];
        $promisePlusRemainingDays = 0;
        if (count($promisePluseDeductedCoinsDetail) > 0) {
            $promisePlusRemainingDays = Helpers::calculateRemainingDays($promisePluseDeductedCoinsDetail[0]->dc_end_date);
        }

        //Institute Finder coins consumption details
        $instituteComponent = $this->objPaidComponent->getPaidComponentsData(Config::get('constant.INSTITUTE_FINDER'));
        $instituteDeductedCoinsDetail = (isset($instituteComponent->id)) ? $this->objDeductedCoins->getDeductedCoinsDetailByIdForLS($user->id, $instituteComponent->id, 1) : [];
        $instituteRemainingDays = 0;
        if (count($instituteDeductedCoinsDetail) > 0) {
            $instituteRemainingDays = Helpers::calculateRemainingDays($instituteDeductedCoinsDetail[0]->dc_end_date);
        }

        $professionCompletePercentage = Helpers::getProfessionCompletePercentage($user->id, $professionsData->id);
        $getQuestionTemplateForProfession = [];

        return view('teenager.careerDetail', compact('level4BasicPlayed', 'professionCompletePercentage', 'getTeenagerHML', 'professionsData', 'countryId', 'professionCertificationImagePath', 'professionSubjectImagePath', 'teenagerStrength', 'mediumAdImages', 'largeAdImages', 'bannerAdImages', 'scholarshipPrograms', 'exceptScholarshipIds', 'scholarshipProgramIds', 'expiredActivityIds', 'remainingDaysForActivity', 'componentsData', 'teenagerParents', 'challengedAcceptedParents', 'leaderboardTeenagers', 'nextleaderboardTeenagers', 'promisePlusComponent', 'promisePlusRemainingDays', 'instituteRemainingDays', 'instituteComponent'));
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
        $favoriteStatus = Input::get('favoriteStatus');
        $careerDetails['srp_teenager_id'] = Auth::guard('teenager')->user()->id;
        $careerDetails['srp_profession_id'] = $careerId;
        if ($favoriteStatus == Config::get('constant.ADD_STAR_TO_CAREER')) {
            $return = $this->objStarRatedProfession->addStarToCareer($careerDetails);
            $response['message'] = "Added";
        } else {
            $return = $this->objStarRatedProfession->deleteRecord($careerDetails);
            $response['message'] = "Removed";
        }
        $response['status'] = 1;
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
                    $return .= '<option value="'.$value->it_slug.'">'.$value->it_name.'</option>';
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
                    $return .= '<option value="'.$value['slug'].'">'.$value['name'].'</option>';
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
        else if ($queId == 7) // Match scale
        {
            $data = [1 => 'Strong match', 2 => 'Potential match', 3 => 'Unlikely match'];
            $return .= '<div class="form-group custom-select bg-blue" id="answerDropdown"><select tabindex="8" class="form-control" id="answerId" onchange="fetchDropdownResult();">
                <option value="0">Select Scale</option>';
            foreach ($data as $key => $value) {
                    $return .= '<option value="'.$key.'">'.$value.'</option>';
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

        if($ansId != 0) {
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
            elseif ($queId == 7) // Match Scale
            {
                $getTeenagerHML2 = Helpers::getTeenagerMatchScale($userid);
                $scale = [];
                foreach($getTeenagerHML2 as $key => $value) {
                    if($value == "match") {
                        $scale[1][] = $key;
                    } else if($value == "moderate") {
                        $scale[2][] = $key;
                    } else if($value == "nomatch") {
                        $scale[3][] = $key;
                    }
                }
                $professionArray = isset($scale[$ansId]) ? $scale[$ansId] : [];
                $basketsData = $this->baskets->getBasketsAndProfessionWithSelectedHMLProfessionByBasketId($countryId, $professionArray);
                //echo "<pre/>"; print_r($basketsData->toArray()); die();
                if($view == 'GRID') {
                    return view('teenager.basic.level3CareerGridView', compact('basketsData','view','countryId'));
                } elseif($view == 'LIST') {
                    return view('teenager.basic.level3CareerListView', compact('basketsData','view'));
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
                $response['message'] = "Build your profile to know careers to consider!";
                return response()->json($response, 200);
                exit;
            }

            $teenagerCareersIds = (isset($teenagerCareers[0]) && count($teenagerCareers[0]) > 0) ? Helpers::getTeenagerCareersIds($user->id)->toArray() : [];

            $match = $nomatch = $moderate = $matchHigh = $matchLow = $moderateHigh = $moderateLow = $nomatchHign = $nomatchLow = $matchSecondHigh = $matchSecondLow = $moderateSecondHigh = $moderateSecondLow = [];

            if($getAllActiveProfessions) {
                //Check career consider section coins consumption details
                $componentsCareerConsider = $this->objPaidComponent->getPaidComponentsData(Config::get('constant.CAREER_TO_CONSIDER'));
                $deductedCoinsCareerConsider = $this->objDeductedCoins->getDeductedCoinsDetailByIdForLS($user->id, $componentsCareerConsider->id, 1);
                $remainingDaysForCareerConsider = 0;
                if (!empty($deductedCoinsCareerConsider[0])) {
                    $remainingDaysForCareerConsider = Helpers::calculateRemainingDays($deductedCoinsCareerConsider[0]->dc_end_date);
                }
                // if ($remainingDaysForCareerConsider == 0) {
                //     $response['status'] = 0;
                //     $response['message'] = "Please consume your procoins to view your career suggestions!";
                //     return response()->json($response, 200);
                //     exit;
                // }

                foreach($getAllActiveProfessions as $profession) {
                    $array = [];
                    $array['id'] = $profession->id;
                    $array['match_scale'] = isset($getTeenagerHML[$profession->id]) ? $getTeenagerHML[$profession->id] : '';
                    $array['added_my_career'] = (in_array($profession->id, $teenagerCareersIds)) ? 1 : 0;
                    $array['is_completed'] = 0;
                    $array['pf_name'] = $profession->pf_name;
                    $array['pf_slug'] = $profession->pf_slug;
                    
                    if($array['match_scale'] == "match") {
                        $getCareerMappingFromSystem = Helpers::getCareerMappingFromSystemByProfession($profession->id);
                        if($getCareerMappingFromSystem) {
                            $mappingArray = [];
                            unset($getCareerMappingFromSystem->created_at);
                            unset($getCareerMappingFromSystem->updated_at);
                            unset($getCareerMappingFromSystem->deleted);
                            
                            $mappingArray = array_count_values((array)$getCareerMappingFromSystem);
                            $match[$profession->id] = $array;
                            
                            if(isset($mappingArray['H']) && isset($mappingArray['M']) && $mappingArray['M'] > 0 && $mappingArray['H'] > 0) { 
                                if($mappingArray['H'] > 0) {
                                    $matchHigh[$profession->id] = $mappingArray['H'];
                                } else {
                                    $matchLow[$profession->id] = $mappingArray['M'];
                                }       
                            } else {
                                if(isset($mappingArray['H']) && $mappingArray['H'] > 0) {
                                    $matchSecondHigh[$profession->id] = $mappingArray['H'];
                                } else {
                                    $matchSecondLow[$profession->id] = $mappingArray['L'];
                                }
                            }
                        }
                    } else if($array['match_scale'] == "nomatch") {
                        $nomatch[$profession->id] = $array;
                        // if($mappingArray['H'] > 0) {
                        //     $nomatchHigh[$profession->id] = $mappingArray['H'];
                        // } else {
                        //     $nomatchLow[$profession->id] = $mappingArray['M'];
                        // }
                    } else if($array['match_scale'] == "moderate") {
                        $getCareerMappingFromSystem = Helpers::getCareerMappingFromSystemByProfession($profession->id);
                        if($getCareerMappingFromSystem) {
                            $mappingArray = [];
                            unset($getCareerMappingFromSystem->created_at);
                            unset($getCareerMappingFromSystem->updated_at);
                            unset($getCareerMappingFromSystem->deleted);
                            
                            $mappingArray = array_count_values((array)$getCareerMappingFromSystem);
                            $moderate[$profession->id] = $array;
                            if(isset($mappingArray['H']) && isset($mappingArray['M']) && $mappingArray['M'] > 0 && $mappingArray['H'] > 0) {
                                if($mappingArray['H'] > 0) {
                                    $moderateHigh[$profession->id] = $mappingArray['H'];
                                } else {
                                    $moderateLow[$profession->id] = $mappingArray['M'];
                                }
                            } else {
                                if(isset($mappingArray['H']) && $mappingArray['H'] > 0) {
                                    $moderateSecondHigh[$profession->id] = $mappingArray['H'];
                                } else {
                                    $moderateSecondLow[$profession->id] = $mappingArray['L'];
                                }
                            }
                        }
                    } else {
                        $notSetArray[$profession->id] = $array;
                    }
                }

                if(count($match) < 1 && count($moderate) < 1 && count($nomatch) > 0) {
                    // asort($nomatchHigh);
                    // asort($nomatchLow);
                    // $mergeAllSortArray = array_merge($nomatchHigh, $nomatchLow);
                    // $careerConsiderationTemp = $nomatch;
                    // foreach($mergeAllSortArray as $key => $sortArray) {
                    //     if(isset($careerConsiderationTemp[$key])) {
                    //         $finalArray[$key] =  $careerConsiderationTemp[$key]; 
                    //     }
                    // }
                    $finalArray = [];
                    $careerConsideration = $finalArray;

                } else if(count($match) > 0 || count($moderate) > 0) {
                    if( count($matchHigh) > 0 || count($moderateHigh) > 0 || count($moderateLow) > 0 || count($matchLow) > 0 ) {
                        arsort($matchHigh);
                        arsort($matchLow);
                        arsort($moderateHigh);
                        arsort($moderateLow);
                         
                        $careerConsiderationTemp = $match + $moderate;
                        $finalArray1 = $finalArray2 = [];
                        $mergeMatchSortArray = $matchHigh + $matchLow;
                        
                        foreach($mergeMatchSortArray as $keyH => $sortArray) {
                            if(isset($careerConsiderationTemp[$keyH])) {
                                $finalArray1[] =  $careerConsiderationTemp[$keyH]; 
                            }
                        }
                      
                        $mergeModerateSortArray = $moderateHigh + $moderateLow;
                        foreach($mergeModerateSortArray as $keyM => $sortArray) {
                            if(isset($careerConsiderationTemp[$keyM])) {
                                $finalArray2[] =  $careerConsiderationTemp[$keyM]; 
                            }
                        }
                       
                        $finalArray = array_merge($finalArray1,$finalArray2);
                        
                    } else {
                        arsort($matchSecondHigh);
                        arsort($matchSecondLow);
                        arsort($moderateSecondHigh);
                        arsort($moderateSecondLow);
                       
                        $mergeAllSortArray = $matchSecondHigh + $matchSecondLow + $moderateSecondHigh + $moderateSecondLow;
                        $careerConsiderationTemp = $match + $moderate;
                        foreach($mergeAllSortArray as $key => $sortArray) {
                            if(isset($careerConsiderationTemp[$key])) {
                                $finalArray[] =  $careerConsiderationTemp[$key]; 
                            }
                        }
                    }
                    $careerConsideration = $finalArray;
                } else {
                    $careerConsideration = $notSetArray;
                }
                
                return view('teenager.basic.careerConsideration', compact('careerConsideration', 'getTeenagerHML', 'remainingDaysForCareerConsider', 'componentsCareerConsider'));
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
        $response = Helpers::getCareerPdf($user, $slug);
        if ($response['status'] == 1) {
            $pdfPath = public_path(Config::get('constant.CAREER_DETAILS_PDF_UPLOAD_PATH').$response['fileName']);        
            //Uploading on AWS
            $originalPdf = $this->fileStorageRepository->addFileToStorage($response['fileName'], Config::get('constant.CAREER_DETAILS_PDF_UPLOAD_PATH'), $pdfPath, "s3");
            //Deleting Local Files
            \File::delete(Config::get('constant.CAREER_DETAILS_PDF_UPLOAD_PATH') . $response['fileName']);   
            header('Content-type: application/pdf');
            header('Content-Disposition: inline; filename='.$response['fileName']);
            header('Content-Transfer-Encoding: binary');
            //$fileContent = Storage::url($this->careerDetailsPdfUploadedPath.$response['fileName']);
            //header('Content-Length: ' . filesize($fileContent));
            @readfile(Storage::url($this->careerDetailsPdfUploadedPath.$response['fileName']));
            exit;
        } else {
            return redirect::to('teenager/career-detail/'.$slug)->with('error', $response['message']);
        }
    }

    //Store records of teenager scholarship program
    public function applyForScholarshipProgram()
    {
        $professionId = Input::get('professionId');
        $activityId = Input::get('activityId');
        if ($activityId != '' && $professionId != '') {
            $professionComplete = Helpers::getProfessionCompletePercentage(Auth::guard('teenager')->user()->id, $professionId);
            if ($professionComplete && $professionComplete != '' && $professionComplete == 100) {
                $activityDetails = [];
                $activityDetails['tsp_activity_id'] = $activityId;
                $activityDetails['tsp_teenager_id'] = Auth::guard('teenager')->user()->id;
                $checkIfAlreadyApplied = $this->objTeenagerScholarshipProgram->getScholarshipProgramDetailsByActivity($activityDetails);
                if (isset($checkIfAlreadyApplied) && !empty($checkIfAlreadyApplied)) {
                    $response['status'] = 1;
                    $response['message'] = "applied";
                } else {
                    $appliedForScholarship = $this->objTeenagerScholarshipProgram->StoreDetailsForScholarshipProgram($activityDetails);
                    $response['status'] = 1;
                    $response['message'] = "success"; 
                }
            } else {
                $response['status'] = 0;
                $response['message'] = "Please complete all activities of profession";
            }
        } else {
            $response['status'] = 0;
            $response['message'] = "Something went wrong!";
        }
        return response()->json($response, 200);
        exit;
        //return $message;
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
        $searchText = Input::get('searchText');
        $userId = $user->id;
        if($user->t_view_information == 1){
            $countryId = 2; // United States
        }else{
            $countryId = 1; // India
        }

        if($ansId != '' || $ansId != 0){
            if($queId == 1) // Industry
            {
                $basketsData = $this->baskets->getBasketsAndProfessionWithAttemptedProfessionByBasketIdForUser($ansId, $userId, $countryId, $searchText);
            }
            else if ($queId == 2) // Careers
            {
                $basketsData = $this->baskets->getBasketsAndProfessionWithAttemptedProfessionByProfessionIdForUser($ansId, $userId, $countryId, $searchText);
            } 
            else if ($queId == 3) // Interest
            {
                $basketsData = $this->baskets->getProfessionBasketsByInterestDetailsForUser($ansId, $userId, $countryId, $searchText);
            } 
            else if ($queId == 4) // Strength
            {
                $careersDetails = Helpers::getCareerMapColumnName();
                $basketsData = $this->baskets->getProfessionBasketsByStrengthDetailsForUser($careersDetails[$ansId], $userId, $countryId, $searchText);
            } 
            else if ($queId == 5) // Subjects
            {
                $basketsData = $this->baskets->getProfessionBasketsBySubjectForUser($ansId, $userId, $countryId, $searchText);
            } 
            else if ($queId == 6) // Tags
            {
                $basketsData = $this->baskets->getProfessionBasketsByTagForUser($ansId, $userId, $countryId, $searchText);
            } 
        }
        else // All Records
        {         
            $basketsData = $this->baskets->getStarredBasketsAndProfessionByUserId($userId, $countryId, $searchText);
        }
        $professionImagePath = Config('constant.PROFESSION_ORIGINAL_IMAGE_UPLOAD_PATH');
        return view('teenager.basic.searchdMyCareers', compact('basketsData', 'professionImagePath'));
    }

    public function loadMoreLeaderboardTeenagers() {
        $slot = Input::get('slot');
        $professionId = Input::get('professionId');
        $leaderboardTeenagers = $this->teenagersRepository->getTeenagerListingWithBoosterPointsByProfession($professionId, $slot);
        $nextleaderboardTeenagers = $this->teenagersRepository->getTeenagerListingWithBoosterPointsByProfession($professionId, $slot + 1);
        return view('teenager.basic.careerDetailsLeaderBoard', compact('leaderboardTeenagers', 'nextleaderboardTeenagers'));
    }

    public function getProfessionCompletionPercentage()
    {
        $professionId = Input::get('professionId');
        $response['status'] = 0;
        $response['message'] = 'Something went wrong!';
        $response['percentage'] = 0;
        if ($professionId != '') {
            $completionPercentage = Helpers::getProfessionCompletePercentage(Auth::guard('teenager')->user()->id, $professionId);
            $professionComplete = (isset($completionPercentage) && !empty($completionPercentage)) ? $completionPercentage : 0;
            $response['status'] = 1;
            $response['message'] = 'success';
            $response['percentage'] = $professionComplete;
        }
        return response()->json($response, 200);
        exit;
    }

    /*
    * Returns professions array with details
    */
    public function getProfessionsDetails()
    {
        $searchText = Input::get('searchText');
        $filterBy = Input::get('filterBy');
        $filterOption = Input::get('filterOption');
        $userId = Auth::guard('teenager')->user()->id;
        $basketId = Input::get('basketId'); 
        if (empty($searchText) && $filterBy == 0 && $filterOption == 0) {
            $showElement = 1;
            $industryImageShow = 1;
        } else {
            $showElement = 0;
            if ($filterBy == 1 || $filterBy == 7) {
                $industryImageShow = 1; 
            } else {
                $industryImageShow = 0;
            }
        }
        if ($filterBy == 7) {
            if(Auth::guard('teenager')->user()->t_view_information == 1) {
                $countryId = 2; // United States
            } else {
                $countryId = 1; // India
            }
            $getTeenagerHML2 = Helpers::getTeenagerMatchScale($userId);
            $scale = [];
            foreach($getTeenagerHML2 as $key => $value) {
                if($value == "match") {
                    $scale[1][] = $key;
                } else if($value == "moderate") {
                    $scale[2][] = $key;
                } else if($value == "nomatch") {
                    $scale[3][] = $key;
                }
            }
            $professionArray = isset($scale[$filterOption]) ? $scale[$filterOption] : [];
            //$basketDetails = $this->baskets->getBasketsAndProfessionWithSelectedHMLProfessionByBasketId($countryId, $professionArray);
        } else {
            $professionArray = [];
            
        }
        $basketDetails = $this->baskets->getProfessionDetails($searchText, $filterBy, $filterOption, $professionArray);
        $getTeenagerHML = Helpers::getTeenagerMatchScale($userId);
        $professionAttemptedCount = 0;
        $matchScaleCount = [];
        $shownBasketId = [];
        foreach($basketDetails as $basket) {
            $professionAttemptedCount = 0;
            $match = [];
            $nomatch = [];
            $moderate = [];
            if (isset($basketId)) {
                $shownBasketId[] = $basketId;
            } else {
                if (empty($basketId) && $basketId == "") {
                    if ($searchText != "" && $filterBy == 0 && $filterOption == 0) {
                        $shownBasketId[] = $basket->id;
                    } else {
                        if ($basketDetails->first() == $basket) {
                            $shownBasketId[] = $basket->id;
                        } else {
                            //$shownBasketId = '';
                            break;
                        }
                    }
                }
            }
            // if(empty($basketId) && $basketId == "") {
            //     $basketId = $basket->id;
            // } 
            if (in_array($basket->id, $shownBasketId)) {
                foreach ($basket->profession as $key => $profession) {
                    $professionAttempted = Helpers::getProfessionCompletePercentage($userId, $profession->id);
                    if(isset($professionAttempted) && $professionAttempted == 100){
                        $profession->attempted = 1;
                        $professionAttemptedCount++;
                    } else {
                        $profession->attempted = 0;
                    }
                    $matchScale = isset($getTeenagerHML[$profession->id]) ? $getTeenagerHML[$profession->id] : '';
                    if($matchScale == "match") {
                        $basket->profession[$key]->match_scale = "match-strong";
                        //$matchScaleCount['match'][] = $profession->id;
                        $match[] = $profession->id; 
                    } else if($matchScale == "nomatch") {
                        $basket->profession[$key]->match_scale = "match-unlikely";
                        //$matchScaleCount['nomatch'][] = $profession->id;
                        $nomatch[] = $profession->id;
                    } else if($matchScale == "moderate") {
                        $basket->profession[$key]->match_scale = "match-potential";
                        //$matchScaleCount['moderate'][] = $profession->id;
                        $moderate[] = $profession->id;
                    } else {
                        $basket->profession[$key]->match_scale = "career-data-nomatch";
                    }
                }
            }
            $basket->match = $match;
            $basket->nomatch = $nomatch;
            $basket->moderate = $moderate;
            $basket->professionAttemptedCount = $professionAttemptedCount;
            // if ($getFromAllRecords == 0) {
            //     break;
            // }
        }
        if (isset($basketId) && !empty($basketId) && $basketId != "") {
            $basket = $basketDetails->find($basketId);
            return view('teenager.basic.careerListGridSection', compact('basketDetails', 'searchText', 'filterBy', 'filterOption', 'matchScaleCount', 'professionAttemptedCount', 'showElement', 'industryImageShow', 'shownBasketId', 'basket'));
        } else {
            return view('teenager.basic.careerPageLayout', compact('basketDetails', 'searchText', 'filterBy', 'filterOption', 'matchScaleCount', 'professionAttemptedCount', 'showElement', 'industryImageShow', 'shownBasketId'));
        }
    }

}