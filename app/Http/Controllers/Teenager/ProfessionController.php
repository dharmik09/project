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
use App\StarRatedProfession; 

class ProfessionController extends Controller {

    public function __construct(ProfessionsRepository $professionsRepository, BasketsRepository $basketsRepository) 
    {
        $this->professionsRepository = $professionsRepository;
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

        $teenagerAPIData = Helpers::getTeenInterestAndStregnthDetails(Auth::guard('teenager')->user()->id);
        $teenagerAPIMaxScore = Helpers::getTeenInterestAndStregnthMaxScore();
        $teenagerMI = isset($teenagerAPIData['APIscore']['MI']) ? $teenagerAPIData['APIscore']['MI'] : [];

        foreach($teenagerMI as $miKey => $miVal) {
            $mitName = Helpers::getMIBySlug($miKey);
            $teenMIScore = $this->getTeenScoreInPercentage($teenagerAPIMaxScore['MI'][$miKey], $miVal);
                $teenagerMI[$miKey] = (array('score' => $teenMIScore, 'name' => $mitName, 'type' => Config::get('constant.MULTI_INTELLIGENCE_TYPE'), 'max' => $teenagerAPIMaxScore['MI'][$miKey]));
        }

        $teenagerAptitude = isset($teenagerAPIData['APIscore']['aptitude']) ? $teenagerAPIData['APIscore']['aptitude'] : [];
        foreach($teenagerAptitude as $apptitudeKey => $apptitudeVal) {
            $aptName = Helpers::getApptitudeBySlug($apptitudeKey);
            $teenAptScore = $this->getTeenScoreInPercentage($teenagerAPIMaxScore['aptitude'][$apptitudeKey], $apptitudeVal);
            $teenagerAptitude[$apptitudeKey] = (array('score' => $teenAptScore, 'name' => $aptName, 'type' => Config::get('constant.APPTITUDE_TYPE'), 'max' => $teenagerAPIMaxScore['aptitude'][$apptitudeKey]));
        }
        $teenagerPersonality = isset($teenagerAPIData['APIscore']['personality']) ? $teenagerAPIData['APIscore']['personality'] : [];
        foreach($teenagerPersonality as $personalityKey => $personalityVal) {
            $ptName = Helpers::getPersonalityBySlug($personalityKey);
            $teenPtScore = $this->getTeenScoreInPercentage($teenagerAPIMaxScore['personality'][$personalityKey], $personalityVal);
            $teenagerPersonality[$personalityKey] = (array('score' => $teenPtScore, 'name' => $ptName, 'type' => Config::get('constant.PERSONALITY_TYPE'), 'max' => $teenagerAPIMaxScore['personality'][$personalityKey]));
        }
        $teenagerStrength = array_merge($teenagerAptitude, $teenagerPersonality, $teenagerMI);
        $professionCertificationImagePath = Config('constant.PROFESSION_CERTIFICATION_ORIGINAL_IMAGE_UPLOAD_PATH');
        $professionSubjectImagePath = Config('constant.PROFESSION_SUBJECT_ORIGINAL_IMAGE_UPLOAD_PATH');
        return view('teenager.careerDetail', compact('getTeenagerHML', 'professionsData', 'countryId','professionCertificationImagePath','professionSubjectImagePath','teenagerStrength'));
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
        elseif ($queId == 2) // Careers
        {
            $data = $this->professions->getActiveProfessionsOrderByName();
            $return .= '<div class="form-group custom-select bg-blue" id="answerDropdown"><select tabindex="8" class="form-control" id="answerId" onchange="fetchDropdownResult();">
                <option value="0">All Careers</option>';
            foreach ($data as $key => $value) {
                    $return .= '<option value="'.$value->id.'">'.$value->pf_name.'</option>';
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
        return view('teenager.myCareers', compact('basketsData', 'professionImagePath','teenagerTotalProfessionAttemptedCount','teenagerTotalProfessionStarRatedCount'));
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
}