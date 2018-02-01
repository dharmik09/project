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

        $professionAttemptedCount = 0;
        foreach ($basketsData->profession as $k => $v) {
            $professionAttempted = $this->professionsRepository->getTeenagerProfessionAttempted($userid, $v->id,null);
            if(count($professionAttempted)>0){
                $basketsData['profession'][$k]['attempted'] = 'yes';
                $professionAttemptedCount++;
            }
        }
        return view('teenager.basic.basketProfession', compact('basketsData', 'professionAttemptedCount'));
    }

    public function gridGetIndex(){
        $user = Auth::guard('teenager')->user();
        $userid = $user->id;

        if($user->t_view_information == 1){
            $this->countryId = 2; // United States
        }else{
            $this->countryId = 1; // India
        }

        $basketsData = $this->baskets
                        ->with(['profession' => function ($query) {
                            $query->with(['professionHeaders' => function ($query) {
                                $query->where('country_id',$this->countryId);
                            }])->where('deleted' ,config::get('constant.ACTIVE_FLAG'));
                        }])
                        ->where('deleted' ,config::get('constant.ACTIVE_FLAG'))
                        ->find(Input::get('basket_id'));
        $professionAttemptedCount = 0;
        foreach ($basketsData->profession as $k => $v) {
            $professionAttempted = $this->professionsRepository->getTeenagerProfessionAttempted($userid, $v->id,null);
            if(count($professionAttempted)>0){
                $basketsData['profession'][$k]['attempted'] = 'yes';
                $professionAttemptedCount++;
            }
        }

        $video = Helpers::youtube_id_from_url($basketsData->b_video);

        $return = '<div class="banner-landing banner-career" style="background-image:url('.Storage::url(Config::get('constant.BASKET_ORIGINAL_IMAGE_UPLOAD_PATH').$basketsData->b_logo).');">
                        <div class="">
                            <div class="play-icon"><a id="link'.$basketsData->id.'" onclick="playVideo(this.id,\''.$video.'\');" class="play-btn" id="iframe-video"><img src="'.Storage::url('img/play-icon.png').'" alt="play icon"></a></div>
                        </div><iframe width="100%" height="100%" frameborder="0" allowfullscreen class="iframe" id="iframe-video-link'.$basketsData->id.'"></iframe>
                    </div>
                    <section class="sec-category"><div class="row">
                        <div class="col-md-6">
                            <p>You have completed <strong>'.$professionAttemptedCount.' of '.count($basketsData->profession).'</strong> careers</p>
                        </div>
                        <div class="col-md-6">
                            <div class="pull-right">
                                <ul class="match-list">
                                    <li><span class="number match-strong">4</span> Strong match</li>
                                    <li><span class="number match-potential">5</span> Potential match</li>
                                    <li><span class="number match-unlikely">4</span> Unlikely match</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="career-map">
                        <div class="row">';

        foreach($basketsData->profession as $k => $v){
            $average_per_year_salary = $v->professionHeaders->filter(function($item) {
                                return $item->pfic_title == 'average_per_year_salary';
                            })->first();
            $profession_outlook = $v->professionHeaders->filter(function($item) {
                                return $item->pfic_title == 'profession_outlook';
                            })->first();

            $return .= '<div class="col-md-4 col-sm-6">
                            <div class="category match-strong"><a href="'.url('teenager/career-detail/'.$v->pf_slug).'" title="'.$v->pf_name.'">'.$v->pf_name.'</a>
                                                            ';
                if(isset($v->attempted)){
                    $return .= ' <span class="complete"><a href="#" title="Completed"><i class="icon-thumb"></i></a></span>';
                }
                
                $return .= '<div class="overlay">';

                    if(isset($average_per_year_salary)){
                        $return .= '<span class="salary">Average Salary per year : ';
                                    if($this->countryId == 1){
                                        $return .= "₹";
                                    }
                                    elseif($this->countryId == 2){
                                        $return .= "$";
                                    }
                                    $return .= (isset($average_per_year_salary->pfic_content) && !empty($average_per_year_salary->pfic_content)) ? strip_tags($average_per_year_salary->pfic_content) : '';
                        $return .= '</span>';
                    }else{
                        $return .= '<span class="salary">Average Salary per year : N/A</span>';
                    }

                    if(isset($profession_outlook)){
                        $return .= '<span class="assessment">Outlook : ';
                            $return .= (isset($profession_outlook->pfic_content) && !empty($profession_outlook->pfic_content)) ? strip_tags($profession_outlook->pfic_content) : '';
                        $return .= '</span>';
                    }else{
                        $return .= '<span class="assessment">Outlook : N/A</span>';
                    }
                    
                $return .= '</div>';

            $return .='</div>
                </div>';
        }

        $return .= '</div></div></section>';
        
        return $return;
    }

    public function gridGetSearch(){
        $user = Auth::guard('teenager')->user();
        $userid = $user->id;
        $this->value = Input::get('search_text');

        if($user->t_view_information == 1){
            $this->countryId = 2; // United States
        }else{
            $this->countryId = 1; // India
        }

        $basketsData = $this->baskets
                        ->with(['profession' => function ($query) {
                            $query->with(['professionHeaders' => function ($query) {
                                $query->where('country_id',$this->countryId);
                            }])->where('pf_name', 'like', '%'.$this->value.'%')
                            ->where('deleted' ,config::get('constant.ACTIVE_FLAG'));
                        }])
                        ->whereHas('profession', function ($query) {
                            $query->where('pf_name', 'like', '%'.$this->value.'%')
                            ->where('deleted' ,config::get('constant.ACTIVE_FLAG'));
                        })
                        ->where('deleted' ,config::get('constant.ACTIVE_FLAG'))
                        ->get();
        $return = '';
        if(count($basketsData)>0)
        {
        
            foreach ($basketsData as $key => $value) {
                $professionAttemptedCount = 0;
                foreach($value->profession as $k => $v){
                    $professionAttempted = $this->professionsRepository->getTeenagerProfessionAttempted($userid, $v->id,null);
                    if(count($professionAttempted)>0){
                        $basketsData[$key]['profession'][$k]['attempted'] = 'yes';
                        $professionAttemptedCount++;
                    }
                }
                $return .= '<div class="panel panel-default">
                                <div class="panel-heading">
                                    <h4 class="panel-title"><a data-parent="#accordion" data-toggle="collapse" href="#accordion'.$value->id.'" id="'.$value->id.'" class="collapsed">'.$value->b_name.'</a> <a href="'. url('teenager/list-career') .'" title="Grid view" class="grid"><i class="icon-list"></i></a></h4>
                                </div>
                                <div class="panel-collapse collapse in" id="accordion'.$value->id.'">
                                    <div class="panel-body">
                                        <section class="career-content">
                                            <div class="bg-white">
                                                <div id="profession'.$value->id.'">';

                $return .= '<section class="sec-category"><div class="row">
                                <div class="col-md-6">
                                </div>
                                <div class="col-md-6">
                                    <div class="pull-right">
                                        <ul class="match-list">
                                            <li><span class="number match-strong">4</span> Strong match</li>
                                            <li><span class="number match-potential">5</span> Potential match</li>
                                            <li><span class="number match-unlikely">4</span> Unlikely match</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="career-map">
                                <div class="row">';

                foreach($value->profession as $k => $v){

                $average_per_year_salary = $v->professionHeaders->filter(function($item) {
                                                return $item->pfic_title == 'average_per_year_salary';
                                            })->first();
                $profession_outlook = $v->professionHeaders->filter(function($item) {
                                            return $item->pfic_title == 'profession_outlook';
                                        })->first();

                    $return .= '<div class="col-md-4 col-sm-6">
                                    <div class="category match-strong"><a href="'.url('teenager/career-detail/'.$v->pf_slug).'" title="'.$v->pf_name.'">'.$v->pf_name.'</a>
                                                                    ';
                        if(isset($v->attempted)){
                            $return .= ' <span class="complete"><a href="#" title="Completed"><i class="icon-thumb"></i></a></span>';
                        }
                        $return .= '<div class="overlay">';

                            if(isset($average_per_year_salary)){
                                $return .= '<span class="salary">Average Salary per year : ';
                                            if($this->countryId == 1){
                                                $return .= "₹";
                                            }
                                            elseif($this->countryId == 2){
                                                $return .= "$";
                                            }
                                            $return .= (isset($average_per_year_salary->pfic_content) && !empty($average_per_year_salary->pfic_content)) ? strip_tags($average_per_year_salary->pfic_content) : '';
                                $return .= '</span>';
                            }else{
                                $return .= '<span class="salary">Average Salary per year : N/A</span>';
                            }

                            if(isset($profession_outlook)){
                                $return .= '<span class="assessment">Outlook : ';
                                    $return .= (isset($profession_outlook->pfic_content) && !empty($profession_outlook->pfic_content)) ? strip_tags($profession_outlook->pfic_content) : '';
                                $return .= '</span>';
                            }else{
                                $return .= '<span class="assessment">Outlook : N/A</span>';
                            }
                            
                        $return .= '</div></div></div>';
                }

                $return .= '</div>
                                    </div>
                                    </section>
                                    </div>
                                </div>
                            </section>
                        </div>
                    </div>
                </div>';
            }
        }
        else{
            // $return = '<center><h3>No result Found</h3></center>';
            $return = '<div class="sec-forum"><span>No result Found</span></div>';
        }
        return $return;
    }

    public function listGetSearch(){
        $userid = Auth::guard('teenager')->user()->id;
        $this->value = Input::get('search_text');
        $basketsData = $this->baskets
                        ->with(['profession' => function ($query) {
                            $query->where('pf_name', 'like', '%'.$this->value.'%')
                            ->where('deleted' ,config::get('constant.ACTIVE_FLAG'));
                        }])
                        ->whereHas('profession', function ($query) {
                            $query->where('pf_name', 'like', '%'.$this->value.'%')
                            ->where('deleted' ,config::get('constant.ACTIVE_FLAG'));
                        })
                        ->where('deleted' ,config::get('constant.ACTIVE_FLAG'))
                        ->get();
        $return = '';
        if(count($basketsData)>0)
        {
        
            foreach ($basketsData as $key => $value) {
                $professionAttemptedCount = 0;
                foreach($value->profession as $k => $v){
                    $professionAttempted = $this->professionsRepository->getTeenagerProfessionAttempted($userid, $v->id,null);
                    if(count($professionAttempted)>0){
                        $basketsData[$key]['profession'][$k]['attempted'] = 'yes';
                        $professionAttemptedCount++;
                    }
                }
                $return .= '<div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="panel-title"><a data-parent="#accordion" data-toggle="collapse" href="#accordion'.$value->id.'" id="'.$value->id.'" class="collapsed">'.$value->b_name.'</a> <a href="'. url('teenager/career-grid') .'" title="Grid view" class="grid"><i class="icon-grid"></i></a></h4>
                            </div>
                            <div class="panel-collapse collapse in" id="accordion'.$value->id.'">
                            <div id="profession'.$value->id.'">';

                $return .= '<div class="panel-body">
                                <div class="related-careers careers-tag">
                                    <div class="career-heading clearfix">
                                        <div class="row">
                                            <div class="col-md-6">
                                            </div>
                                            <div class="col-md-6">
                                                <div class="pull-right">
                                                    <ul class="match-list">
                                                        <li><span class="number match-strong">4</span> Strong match</li>
                                                        <li><span class="number match-potential">5</span> Potential match</li>
                                                        <li><span class="number match-unlikely">4</span> Unlikely match</li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <ul class="career-list">';

                foreach($value->profession as $k => $v){

                    $return .= '<li class="match-strong complete-feild"><a href="'.url('teenager/career-detail/'.$v->pf_slug).'" title="'.$v->pf_name.'">'.$v->pf_name.'</a>';
                        if(isset($v->attempted)){
                            $return .= '<a class="complete"><span>Complete <i class="icon-thumb"></i></span></a>';
                        }
                    $return .= '</li>';
                }

                $return .= '</ul></div></div>';
                $return .= '</div>
                        </div>
                    </div>';    
            }
        }
        else{
            $return = '<div class="sec-forum"><span>No result Found</span></div>';
        }
        return $return;
    }

    public function careerDetails($slug)
    {
        $user = Auth::guard('teenager')->user();
        
        if($user->t_view_information == 1){
            $countryId = 2; // United States
        }else{
            $countryId = 1; // India
        }

        $professionsData = $this->professions->getProfessionBySlugWithHeadersAndCertificatesAndTags($slug, $countryId, $user->id);
        $careerMapHelperArray = Helpers::getCareerMapColumnName();
        $careerMappingdata = [];
        
        foreach ($careerMapHelperArray as $key => $value) {
            $data = [];
            if($professionsData->careerMapping[$value] != 'L'){
                $arr = explode("_", $key);
                if($arr[0] == 'apt'){
                    $apptitudeData = $this->objApptitude->getApptitudeDetailBySlug($key);
                    $data['cm_name'] = $apptitudeData->apt_name;   
                    $data['cm_image_url'] = Storage::url($this->aptitudeThumb . $apptitudeData->apt_logo);
                    $data['cm_slug_url'] = url('/teenager/multi-intelligence/'.Config::get('constant.APPTITUDE_TYPE').'/'.$apptitudeData->apt_slug);   
                }
                elseif($arr[0] == 'mit'){
                    $multipleIntelligentData = $this->objMultipleIntelligent->getMultipleIntelligenceDetailBySlug($key);
                    $data['cm_name'] = $multipleIntelligentData->mit_name;
                    $data['cm_image_url'] = Storage::url($this->miThumb.$multipleIntelligentData->mit_logo);
                    $data['cm_slug_url'] = url('/teenager/multi-intelligence/'.Config::get('constant.MULTI_INTELLIGENCE_TYPE').'/'.$multipleIntelligentData->mi_slug);
                }
                elseif($arr[0] == 'pt'){
                    $personalityData = $this->objPersonality->getPersonalityDetailBySlug($key);
                    $data['cm_name'] = $personalityData->pt_name;
                    $data['cm_image_url'] = Storage::url($this->personalityThumb.$personalityData->pt_logo);
                    $data['cm_slug_url'] = url('/teenager/multi-intelligence/'.Config::get('constant.PERSONALITY_TYPE').'/'.$personalityData->pt_slug);
                }
            $careerMappingdata[] = $data;
            }
        }
        
        unset($professionsData->careerMapping);
        $professionsData->ability = $careerMappingdata;
        // echo "<pre>";
        // print_r($professionsData->ability);
        // exit;

        $professionCertificationImagePath = Config('constant.PROFESSION_CERTIFICATION_ORIGINAL_IMAGE_UPLOAD_PATH');
        $professionSubjectImagePath = Config('constant.PROFESSION_SUBJECT_ORIGINAL_IMAGE_UPLOAD_PATH');
        return view('teenager.careerDetail', compact('professionsData', 'countryId','professionCertificationImagePath','professionSubjectImagePath'));
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