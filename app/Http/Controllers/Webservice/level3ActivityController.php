<?php

namespace App\Http\Controllers\Webservice;

use App\Http\Controllers\Controller;
use App\Services\Teenagers\Contracts\TeenagersRepository;
use App\Services\Professions\Contracts\ProfessionsRepository;
use App\Services\Baskets\Contracts\BasketsRepository;
use App\Baskets;
use App\Professions;
use App\ProfessionHeaders;
use App\StarRatedProfession;
use App\MultipleIntelligent;
use App\Apptitude;
use App\Personality;
use App\Teenagers;
use Config;
use Storage;
use Helpers;  
use Auth;
use Input;
use Redirect;
use App\Interest;
use Illuminate\Http\Request;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use App\ProfessionSubject;
use App\ProfessionTag;

class level3ActivityController extends Controller {

    public function __construct(TeenagersRepository $teenagersRepository, ProfessionsRepository $professionsRepository, BasketsRepository $basketsRepository) {
        $this->teenagersRepository = $teenagersRepository;
        $this->professionsRepository = $professionsRepository;
        $this->baskets = new Baskets();
        $this->professions = new Professions();
        $this->professionHeaders = new ProfessionHeaders();
        $this->objTeenagers = new Teenagers;
        $this->objStarRatedProfession = new StarRatedProfession;
        $this->basketThumbUrl = Config::get('constant.BASKET_THUMB_IMAGE_UPLOAD_PATH');
        $this->professionThumbUrl = Config::get('constant.PROFESSION_THUMB_IMAGE_UPLOAD_PATH');
        $this->basketDefaultProteenImage = 'proteen-logo.png';
        $this->professionDefaultProteenImage = 'proteen-logo.png';
        $this->objMultipleIntelligent = new MultipleIntelligent;
        $this->objApptitude = new Apptitude;
        $this->objPersonality = new Personality;
        $this->aptitudeThumb = Config::get('constant.APPTITUDE_THUMB_IMAGE_UPLOAD_PATH');
        $this->miThumb = Config::get('constant.MI_THUMB_IMAGE_UPLOAD_PATH');
        $this->personalityThumb = Config::get('constant.PERSONALITY_THUMB_IMAGE_UPLOAD_PATH');
        $this->professionCertificationImagePath = Config('constant.PROFESSION_CERTIFICATION_THUMB_IMAGE_UPLOAD_PATH');
        $this->professionSubjectImagePath = Config('constant.PROFESSION_SUBJECT_THUMB_IMAGE_UPLOAD_PATH');
        $this->professionTagImagePath = Config('constant.PROFESSION_TAG_THUMB_IMAGE_UPLOAD_PATH');
        $this->saSmallImagePath = Config::get('constant.SA_SMALL_IMAGE_UPLOAD_PATH');
        $this->saBannerImagePath = Config::get('constant.SA_BANNER_IMAGE_UPLOAD_PATH');
        $this->saOrigionalImagePath = Config::get('constant.SA_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->objTeenagers = new Teenagers;
        $this->log = new Logger('api-level3-activity-controller');
        $this->log->pushHandler(new StreamHandler(storage_path().'/logs/monolog-'.date('m-d-Y').'.log')); 
        $this->objInterest = new Interest;   
        $this->objSubject = new ProfessionSubject;
        $this->objTag = new ProfessionTag;
    }

    public function getAllBasktes(Request $request) {
        $response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg')];
        $teenager = $this->teenagersRepository->getTeenagerById($request->userId);
        $this->log->info('Get teenager detail for userId'.$request->userId , array('api-name'=> 'getAllBasktes'));
        if($request->userId != "" && $teenager) {

            $data = $this->baskets->where('deleted',config::get('constant.ACTIVE_FLAG'))->get();

            foreach ($data as $key => $value) {
                if($value->b_logo != '' && Storage::size($this->basketThumbUrl . $value->b_logo) > 0){
                    $data[$key]->b_logo = Storage::url($this->basketThumbUrl . $value->b_logo);
                }
                else{
                    $data[$key]->b_logo = Storage::url($this->basketThumbUrl . $this->basketDefaultProteenImage);
                }

                $youtubeId = Helpers::youtube_id_from_url($value->b_video);
                if($youtubeId != ''){
                    $data[$key]->b_video = $youtubeId;
                    $data[$key]->type_video = '1'; //Youtube
                }
                else{
                    $data[$key]->type_video = '2'; //Dropbox
                }
            }
            
            if($data){
                $response['data']['baskets'] = $data;
                $response['data']['total_profession'] = '200';
                $response['data']['completed_profession'] = '123';
            }
            else{
                $response['data'] = trans('appmessages.data_empty_msg');
            }

            $response['status'] = 1;
            $response['login'] = 1;
            $response['message'] = trans('appmessages.default_success_msg');

            $this->log->info('Response for Level3 Get All basket' , array('api-name'=> 'getAllBasktes'));
        } else {
            $this->log->error('Parameter missing error' , array('api-name'=> 'getAllBasktes'));
            $response['message'] = trans('appmessages.missing_data_msg');
        }
        return response()->json($response, 200);
    }
    
    public function getAllCareers(Request $request) {
        $response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg')];
        $teenager = $this->teenagersRepository->getTeenagerById($request->userId);
        $this->log->info('Get teenager detail for userId'.$request->userId , array('api-name'=> 'getAllCareers'));
        
        if($request->userId != "" && $teenager) {
            $getTeenagerHML = Helpers::getTeenagerMatchScale($request->userId);
            $match = $nomatch = $moderate = [];
            $data = $this->professions->getActiveProfessionsOrderByName();

            if($data) {
                foreach ($data as $key => $value) {
                    $value->matched = isset($getTeenagerHML[$value->id]) ? $getTeenagerHML[$value->id] : '';
                    if($value->matched == "match") {
                        $match[] = $value->id;
                    } else if($value->matched == "nomatch") {
                        $nomatch[] = $value->id;
                    } else if($value->matched == "moderate") {
                        $moderate[] = $value->id;
                    } else {
                        $notSetArray[] = $value->id;
                    }
                }
            }
            
            $response['strong'] = count($match);
            $response['potential'] = count($moderate);
            $response['unlikely'] = count($nomatch);
            
            $response['data'] = $data;
            $response['status'] = 1;
            $response['login'] = 1;
            $response['message'] = trans('appmessages.default_success_msg');
            $this->log->info('Response for Level3 get All careers' , array('api-name'=> 'getAllCareers'));

        } else {
            $this->log->error('Parameter missing error' , array('api-name'=> 'getAllCareers'));
            $response['message'] = trans('appmessages.missing_data_msg');
        }
        return response()->json($response, 200);
    }  
    
    public function getCareersByBasketId(Request $request) {
        $response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg')];
        $teenager = $this->teenagersRepository->getTeenagerById($request->userId);
        $this->log->info('Get teenager detail for userId'.$request->userId , array('api-name'=> 'getCareersByBasketId'));
        if($request->userId != "" && $teenager) {
            
            $this->countryId = ($teenager->t_view_information == 1) ? 2 : 1;
            $basketId = $request->basketId;

            $careersData = $this->baskets->getBasketsAndProfessionByBaketIdAndCountryId($basketId, $this->countryId);

            if($careersData) {
                if($careersData->b_logo != '' && Storage::size($this->basketThumbUrl.$careersData->b_logo) > 0) {
                    $careersData->b_logo = Storage::url($this->basketThumbUrl.$careersData->b_logo);
                } else {
                    $careersData->b_logo = Storage::url($this->basketThumbUrl.$this->basketDefaultProteenImage);
                }
                $youtubeId = Helpers::youtube_id_from_url($careersData->b_video);
                if($youtubeId != '') {
                    $careersData->b_video = $youtubeId;
                    $careersData->type_video = '1'; //Youtube
                } else {
                    $careersData->type_video = '2'; //Dropbox
                }

                $careersData->total_basket_profession = count($careersData->profession);
                $careersData->basket_completed_profession = '12';
        
                $getTeenagerHML = Helpers::getTeenagerMatchScale($request->userId);
                $match = $nomatch = $moderate = [];

                foreach ($careersData->profession as $key => $value) {
                    if($value->pf_logo != '' && Storage::size($this->professionThumbUrl . $value->pf_logo) > 0) {
                        $careersData->profession[$key]->pf_logo = Storage::url($this->professionThumbUrl . $value->pf_logo);
                    } else {
                        $careersData->profession[$key]->pf_logo = Storage::url($this->professionThumbUrl . $this->professionDefaultProteenImage);
                    }
                    //H,M,L Data
                    $careersData->profession[$key]->matched = isset($getTeenagerHML[$value->id]) ? $getTeenagerHML[$value->id] : '';
                    if($careersData->profession[$key]->matched == "match") {
                        $match[] = $careersData->profession[$key]->matched;
                    } else if($careersData->profession[$key]->matched == "nomatch") {
                        $nomatch[] = $careersData->profession[$key]->matched;
                    } else if($careersData->profession[$key]->matched == "moderate") {
                        $moderate[] = $careersData->profession[$key]->matched;
                    } else {
                        $notSetcareersArr[] = $careersData->profession[$key]->matched;
                    }

                    $average_per_year_salaryData = $value->professionHeaders->filter(function($item) {
                                                    return $item->pfic_title == 'average_per_year_salary';
                                                })->first();

                    $profession_outlookData = $value->professionHeaders->filter(function($item) {
                                                return $item->pfic_title == 'profession_outlook';
                                            })->first();
                    
                    $average_per_year_salary = '';
                    $profession_outlook = '';
                    
                    if(count($average_per_year_salaryData)>0){
                        $average_per_year_salary = $average_per_year_salaryData->pfic_content;
                    }
                    
                    if(count($profession_outlookData)>0){
                        $profession_outlook = $profession_outlookData->pfic_content;
                    }
                    
                    $careersData->profession[$key]['average_per_year_salary'] = $average_per_year_salary;
                    $careersData->profession[$key]['profession_outlook'] = $profession_outlook;
                    $careersData->profession[$key]['completed'] = rand(0,1);
                    
                    unset($careersData->profession[$key]->professionHeaders);
                }

                $careersData->strong_match = count($match);
                $careersData->potential_match = count($moderate);
                $careersData->unlikely_match = count($nomatch);

                $response['data']['baskets'] = $careersData;
                $response['data']['total_profession'] = '200';
                $response['data']['completed_profession'] = '123';
            }
            else{
                $response['data'] = trans('appmessages.data_empty_msg');
            }

            $response['status'] = 1;
            $response['login'] = 1;
            $response['message'] = trans('appmessages.default_success_msg');

            $this->log->info('Response for Level3 get Careers By Basket Id' , array('api-name'=> 'getCareersByBasketId'));
        } else {
            $this->log->error('Parameter missing error' , array('api-name'=> 'getCareersByBasketId'));
            $response['message'] = trans('appmessages.missing_data_msg');
        }
        return response()->json($response, 200);
    }

    public function getCareersSearch(Request $request) {
        $response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg')];
        $teenager = $this->teenagersRepository->getTeenagerById($request->userId);
        $this->log->info('Get teenager detail for userId'.$request->userId , array('api-name'=> 'searchCareers'));
        
        if($request->userId != "" && $teenager) {
            if($request->searchText != "") {
                $searchValue = $request->searchText;
                $data = $this->baskets->getBasketsAndProfessionBySearchValue($searchValue);
                if($data) {
                    
                    $getTeenagerHML = Helpers::getTeenagerMatchScale($request->userId);

                    foreach ($data as $key => $value) {
                        //print_r($value); die();
                        if($value->b_logo != '' && Storage::size($this->basketThumbUrl . $value->b_logo) > 0){
                            $data[$key]->b_logo = Storage::url($this->basketThumbUrl . $value->b_logo);
                        } else {
                            $data[$key]->b_logo = Storage::url($this->basketThumbUrl . $this->basketDefaultProteenImage);
                        }

                        $youtubeId = Helpers::youtube_id_from_url($value->b_video);
                        if($youtubeId != ''){
                            $data[$key]->b_video = $youtubeId;
                            $data[$key]->type_video = '1'; //Youtube
                        } else {
                            $data[$key]->type_video = '2'; //Dropbox
                        }

                        $data[$key]->total_basket_profession = count($value->profession);
                        $data[$key]->basket_completed_profession = '12';
                        
                        $match = $nomatch = $moderate = [];
                    
                        foreach ($value->profession as $k => $v) {
                            if($v->pf_logo != '' && Storage::size($this->professionThumbUrl . $v->pf_logo) > 0){
                                $data[$key]->profession[$k]->pf_logo = Storage::url($this->professionThumbUrl . $v->pf_logo);
                            } else {
                                $data[$key]->profession[$k]->pf_logo = Storage::url($this->professionThumbUrl . $this->professionDefaultProteenImage);
                            }
                            $data[$key]->profession[$k]->completed = rand(0,1);

                            $data[$key]->profession[$k]->matched = isset($getTeenagerHML[$v->id]) ? $getTeenagerHML[$v->id] : '';
                            
                            if($data[$key]->profession[$k]->matched == "match") {
                                $match[] = $data[$key]->profession[$k]->matched;
                            } else if($data[$key]->profession[$k]->matched == "nomatch") {
                                $nomatch[] = $data[$key]->profession[$k]->matched;
                            } else if($data[$key]->profession[$k]->matched == "moderate") {
                                $moderate[] = $data[$key]->profession[$k]->matched;
                            } else {
                                $notSetcareersArr[] = $data[$key]->profession[$k]->matched;
                            }
                        }

                        $data[$key]->strong_match = count($match);
                        $data[$key]->potential_match = count($nomatch);
                        $data[$key]->unlikely_match = count($moderate);
                    }

                    $response['data']['baskets'] = $data;
                    //$response['data']['total_profession'] = '200';
                    //$response['data']['completed_profession'] = '123';
                } else {
                    $response['data'] = trans('appmessages.data_empty_msg');
                }

                $response['status'] = 1;
                $response['login'] = 1;
                $response['message'] = trans('appmessages.default_success_msg');

                $this->log->info('Response for Level3 search Career' , array('api-name'=> 'searchCareers'));
            } else {
                $this->log->error('Parameter missing error' , array('api-name'=> 'searchCareers'));
                $response['message'] = trans('appmessages.missing_data_msg');
            }
        } else {
            $this->log->error('Parameter missing error' , array('api-name'=> 'searchCareers'));
            $response['message'] = trans('appmessages.missing_data_msg');
        }
        return response()->json($response, 200);
    }

    public function getTeenagerCareersWithBaket(Request $request) {
        $response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg')];
        $teenager = $this->teenagersRepository->getTeenagerById($request->userId);
        $this->log->info('Get teenager detail for userId'.$request->userId , array('api-name'=> 'getTeenagerCareersWithBaket'));
        if($request->userId != "" && $teenager) {
            $queId = (isset($request->sortBy) && !empty($request->sortBy)) ? $request->sortBy : '';
            $ansId = (isset($request->sortOption) && !empty($request->sortOption)) ? $request->sortOption : '';
            //$userId = $request->userId;
            if($teenager->t_view_information == 1) {
                $countryId = 2; // United States
            } else {
                $countryId = 1; // India
            }
            $data = [];
            if($ansId != '' || $ansId != 0){
                if($queId == 1) // Industry
                {
                    $data = $this->baskets->getBasketsAndProfessionWithAttemptedProfessionByBasketIdForUser($ansId, $request->userId, $countryId);
                }
                else if ($queId == 2) // Careers
                {
                    $data = $this->baskets->getBasketsAndProfessionWithAttemptedProfessionByProfessionIdForUser($ansId, $request->userId, $countryId);
                } 
                else if ($queId == 3) // Interest
                {
                    $data = $this->baskets->getProfessionBasketsByInterestDetailsForUser($ansId, $request->userId, $countryId);
                } 
                else if ($queId == 4) // Strength
                {
                    $careersDetails = Helpers::getCareerMapColumnName();
                    $data = $this->baskets->getProfessionBasketsByStrengthDetailsForUser($careersDetails[$ansId], $request->userId, $countryId);
                } 
                else if ($queId == 5) // Subjects
                {
                    $data = $this->baskets->getProfessionBasketsBySubjectForUser($ansId, $request->userId, $countryId);
                } 
                else if ($queId == 6) // Tags
                {
                    $data = $this->baskets->getProfessionBasketsByTagForUser($ansId, $request->userId, $countryId);
                }
            } else {
                $data = $this->baskets->getStarredBasketsAndProfessionByUserId($request->userId, $countryId);
            }
            if($data) {
                $getTeenagerHML = Helpers::getTeenagerMatchScale($request->userId);
                foreach ($data as $key => $value) {
                    $match = $nomatch = $moderate = [];
                    if($value->b_logo != '' && Storage::size($this->basketThumbUrl . $value->b_logo) > 0){
                        $data[$key]->b_logo = Storage::url($this->basketThumbUrl . $value->b_logo);
                    }
                    else{
                        $data[$key]->b_logo = Storage::url($this->basketThumbUrl . $this->basketDefaultProteenImage);
                    }
                    $youtubeId = Helpers::youtube_id_from_url($value->b_video);
                    if($youtubeId != '') {
                        $data[$key]->b_video = $youtubeId;
                        $data[$key]->type_video = '1'; //Youtube
                    } else {
                        $data[$key]->type_video = '2'; //Dropbox
                    }
                    
                    $data[$key]->total_basket_profession = count($value->profession);
                    $data[$key]->basket_completed_profession = '12';
                    
                    foreach ($value->profession as $k => $v) {
                        if($v->pf_logo != '' && Storage::size($this->professionThumbUrl . $v->pf_logo) > 0){
                            $data[$key]->profession[$k]->pf_logo = Storage::url($this->professionThumbUrl . $v->pf_logo);
                        }
                        else{
                            $data[$key]->profession[$k]->pf_logo = Storage::url($this->professionThumbUrl . $this->professionDefaultProteenImage);
                        }
                        $data[$key]->profession[$k]->completed = rand(0,1);
                        
                        $data[$key]->profession[$k]->matched = isset($getTeenagerHML[$v->id]) ? $getTeenagerHML[$v->id] : '';
                        if($data[$key]->profession[$k]->matched == "match") {
                            $match[] = $v->id;
                        } else if($data[$key]->profession[$k]->matched == "nomatch") {
                            $nomatch[] = $v->id;
                        } else if($data[$key]->profession[$k]->matched == "moderate") {
                            $moderate[] = $v->id;
                        } else {
                            $notSetArray[] = $v->id;
                        }
                    }
                    $data[$key]->strong_match = count($match);
                    $data[$key]->potential_match = count($moderate);
                    $data[$key]->unlikely_match = count($nomatch);
                }
                $response['data']['baskets'] = $data;
                $response['data']['total_profession'] = '200';
                $response['data']['completed_profession'] = '123';
            }
            else{
                $response['data'] = trans('appmessages.data_empty_msg');
            }

            $response['status'] = 1;
            $response['login'] = 1;
            $response['message'] = trans('appmessages.default_success_msg');

            $this->log->info('Response for Level3 get Teenager Careers With Baket' , array('api-name'=> 'getTeenagerCareersWithBaket'));
        } else {
            $this->log->error('Parameter missing error' , array('api-name'=> 'getTeenagerCareersWithBaket'));
            $response['message'] = trans('appmessages.missing_data_msg');
        }
        return response()->json($response, 200);
    }

    public function getCareersDetailsByCareerSlug(Request $request) {
        $response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg')];
        $teenager = $this->teenagersRepository->getTeenagerById($request->userId);
        $this->log->info('Get teenager detail for userId'.$request->userId , array('api-name'=> 'getCareersDetails'));
        if($request->userId != "" && $teenager) {
            if($request->careerSlug != "") {
                
                $slug = $request->careerSlug;

                if($teenager->t_view_information == 1){
                    $countryId = 2; // United States
                    $currencySymbol = '$';
                }else{
                    $countryId = 1; // India
                    $currencySymbol = '₹';
                }

                $professionsData = $this->professions->getProfessionBySlugWithHeadersAndCertificatesAndTags($slug, $countryId, $teenager->id);
                
                
                
                if($professionsData){
                    
                    $getTeenagerHML = Helpers::getTeenagerMatchScale($teenager->id);
                    
                    $professionHMLScale = isset($getTeenagerHML[$professionsData->id])?$getTeenagerHML[$professionsData->id]:'nomatch';
                    
                    $professionsData->countryId = $countryId;
                    if($professionsData->pf_logo != '' && Storage::size($this->professionThumbUrl . $professionsData->pf_logo) > 0){
                        $professionsData['pf_logo'] = Storage::url($this->professionThumbUrl . $professionsData->pf_logo);
                    }
                    else{
                        $professionsData['pf_logo'] = Storage::url($this->professionThumbUrl . $this->professionDefaultProteenImage);
                    }
                    
                    $professionsData['professionMatchScale'] = $professionHMLScale;
                    
                    $youtubeId = Helpers::youtube_id_from_url($professionsData->pf_video);
                    if($youtubeId != ''){
                        $professionsData->pf_video = $youtubeId;
                        $professionsData->type_video = '1'; //Youtube
                    }
                    else{
                        $professionsData->type_video = '2'; //Dropbox
                    }

                    if(count($professionsData->starRatedProfession)>0){
                        $professionsData->star_rated = 1;
                    }
                    else{
                        $professionsData->star_rated = 0;
                    }

                    $average_per_year_salary = $professionsData->professionHeaders->filter(function($item) {
                                    return $item->pfic_title == 'average_per_year_salary';
                                })->first();
                    $work_hours_per_week = $professionsData->professionHeaders->filter(function($item) {
                                    return $item->pfic_title == 'work_hours_per_week';
                                })->first();
                    $positions_current = $professionsData->professionHeaders->filter(function($item) {
                                    return $item->pfic_title == 'positions_current';
                                })->first();
                    $positions_projected = $professionsData->professionHeaders->filter(function($item) {
                                    return $item->pfic_title == 'positions_projected';
                                })->first();
                    $profession_description = $professionsData->professionHeaders->filter(function($item) {
                                    return $item->pfic_title == 'profession_description';
                                })->first();
                    $profession_outlook = $professionsData->professionHeaders->filter(function($item) {
                                    return $item->pfic_title == 'profession_outlook';
                                })->first();
                    $AI_redundancy_threat = $professionsData->professionHeaders->filter(function($item) {
                                    return $item->pfic_title == 'ai_redundancy_threat';
                                })->first();
                    $profession_job_activities = $professionsData->professionHeaders->filter(function($item) {
                                    return $item->pfic_title == 'profession_job_activities';
                                })->first();
                    $profession_workplace = $professionsData->professionHeaders->filter(function($item) {
                                    return $item->pfic_title == 'profession_workplace';
                                })->first();
                    $profession_skills = $professionsData->professionHeaders->filter(function($item) {
                                    return $item->pfic_title == 'profession_skills';
                                })->first();
                    $profession_personality = $professionsData->professionHeaders->filter(function($item) {
                                    return $item->pfic_title == 'profession_personality';
                                })->first();
                    $profession_education_path = $professionsData->professionHeaders->filter(function($item) {
                                    return $item->pfic_title == 'profession_education_path';
                                })->first();
                    $profession_licensing = $professionsData->professionHeaders->filter(function($item) {
                                    return $item->pfic_title == 'profession_licensing';
                                })->first();
                    $profession_experience = $professionsData->professionHeaders->filter(function($item) {
                                    return $item->pfic_title == 'profession_experience';
                                })->first();
                    $profession_growth_path = $professionsData->professionHeaders->filter(function($item) {
                                    return $item->pfic_title == 'profession_growth_path';
                                })->first();
                    $salary_range = $professionsData->professionHeaders->filter(function($item) {
                                    return $item->pfic_title == 'salary_range';
                                })->first();
                    $profession_bridge = $professionsData->professionHeaders->filter(function($item) {
                                    return $item->pfic_title == 'profession_bridge';
                                })->first();
                    $trends_infolinks_usa = $professionsData->professionHeaders->filter(function($item) {
                                    return $item->pfic_title == 'trends_infolinks';
                                })->first();
                    $high_school_req = $professionsData->professionHeaders->filter(function($item) {
                                    return $item->pfic_title == 'high_school_req';
                                })->first();
                    $junior_college_req = $professionsData->professionHeaders->filter(function($item) {
                                    return $item->pfic_title == 'junior_college_req';
                                })->first();
                    $bachelor_degree_req = $professionsData->professionHeaders->filter(function($item) {
                                    return $item->pfic_title == 'bachelor_degree_req';
                                })->first();
                    $masters_degree_req = $professionsData->professionHeaders->filter(function($item) {
                                    return $item->pfic_title == 'masters_degree_req';
                                })->first();
                    $PhD_req = $professionsData->professionHeaders->filter(function($item) {
                                    return $item->pfic_title == 'phd_req';
                                })->first();

                    $professionsData->average_per_year_salary = (isset($average_per_year_salary->pfic_content) && !empty($average_per_year_salary->pfic_content)) ? $average_per_year_salary->pfic_content : '';
                    $professionsData->work_hours_per_week = (isset($work_hours_per_week->pfic_content) && !empty($work_hours_per_week->pfic_content)) ? $work_hours_per_week->pfic_content : '';
                    $professionsData->positions_current = (isset($positions_current->pfic_content) && !empty($positions_current->pfic_content)) ? $positions_current->pfic_content : '';
                    $professionsData->positions_projected = (isset($positions_projected->pfic_content) && !empty($positions_projected->pfic_content)) ? $positions_projected->pfic_content : '';
                    $professionsData->profession_description = (isset($profession_description->pfic_content) && !empty($profession_description->pfic_content)) ? $profession_description->pfic_content : '';
                    $professionsData->profession_outlook = (isset($profession_outlook->pfic_content) && !empty($profession_outlook->pfic_content)) ? $profession_outlook->pfic_content : '';
                    $professionsData->AI_redundancy_threat = (isset($AI_redundancy_threat->pfic_content) && !empty($AI_redundancy_threat->pfic_content)) ? $AI_redundancy_threat->pfic_content : '';
                    $professionsData->profession_job_activities = (isset($profession_job_activities->pfic_content) && !empty($profession_job_activities->pfic_content)) ? $profession_job_activities->pfic_content : '';
                    $professionsData->profession_workplace = (isset($profession_workplace->pfic_content) && !empty($profession_workplace->pfic_content)) ? $profession_workplace->pfic_content : '';
                    $professionsData->profession_skills = (isset($profession_skills->pfic_content) && !empty($profession_skills->pfic_content)) ? $profession_skills->pfic_content : '';
                    $professionsData->profession_personality = (isset($profession_personality->pfic_content) && !empty($profession_personality->pfic_content)) ? $profession_personality->pfic_content : '';
                    
                    $professionsData->profession_licensing = (isset($profession_licensing->pfic_content) && !empty($profession_licensing->pfic_content)) ? $profession_licensing->pfic_content : '';
                    $professionsData->profession_experience = (isset($profession_experience->pfic_content) && !empty($profession_experience->pfic_content)) ? $profession_experience->pfic_content : '';
                    $professionsData->profession_growth_path = (isset($profession_growth_path->pfic_content) && !empty($profession_growth_path->pfic_content)) ? $profession_growth_path->pfic_content : '';
                    $professionsData->salary_range = (isset($salary_range->pfic_content) && !empty($salary_range->pfic_content)) ? $currencySymbol.$salary_range->pfic_content : '';
                    $professionsData->profession_bridge = (isset($profession_bridge->pfic_content) && !empty($profession_bridge->pfic_content)) ? $profession_bridge->pfic_content : '';
                    $professionsData->trends_infolinks_usa = (isset($trends_infolinks_usa->pfic_content) && !empty($trends_infolinks_usa->pfic_content)) ? $trends_infolinks_usa->pfic_content : '';

                    if(isset($high_school_req->pfic_content)){
                        if($countryId == 1){ // India
                            if(strip_tags($high_school_req->pfic_content) == 0){
                                $high_school = 10;
                            }elseif(strip_tags($high_school_req->pfic_content) == 1){
                                $high_school = 20;
                            }else{
                                $high_school = strip_tags($high_school_req->pfic_content);
                            }
                        }
                        elseif($countryId == 2){ // United States
                            $high_school = strip_tags($high_school_req->pfic_content);
                        }
                    }else{
                        $high_school = 0;
                    }

                    if(isset($junior_college_req->pfic_content)){
                        if($countryId == 1){ // India
                            if(strip_tags($junior_college_req->pfic_content) == 0){
                                $junior_college = 10;
                            }elseif(strip_tags($junior_college_req->pfic_content) == 1){
                                $junior_college = 20;
                            }else{
                                $junior_college = strip_tags($junior_college_req->pfic_content);
                            }
                        }
                        elseif($countryId == 2){ // United States
                            $junior_college = strip_tags($junior_college_req->pfic_content);
                        }
                    }else{
                        $junior_college = 0;
                    }

                    if(isset($bachelor_degree_req->pfic_content)){
                        if($countryId == 1){ // India
                            if(strip_tags($bachelor_degree_req->pfic_content) == 0){
                                $bachelor_degree = 10;
                            }elseif(strip_tags($bachelor_degree_req->pfic_content) == 1){
                                $bachelor_degree = 20;
                            }else{
                                $bachelor_degree = strip_tags($bachelor_degree_req->pfic_content);
                            }
                        }
                        elseif($countryId == 2){ // United States
                            $bachelor_degree = strip_tags($bachelor_degree_req->pfic_content);
                        }
                    }else{
                        $bachelor_degree = 0;
                    }

                    if(isset($masters_degree_req->pfic_content)){
                        if($countryId == 1){ // India
                            if(strip_tags($masters_degree_req->pfic_content) == 0){
                                $masters_degree = 10;
                            }elseif(strip_tags($masters_degree_req->pfic_content) == 1){
                                $masters_degree = 20;
                            }else{
                                $masters_degree = strip_tags($masters_degree_req->pfic_content);
                            }
                        }
                        elseif($countryId == 2){ // United States
                            $masters_degree = strip_tags($masters_degree_req->pfic_content);
                        }
                    }else{
                        $masters_degree = 0;
                    }

                    if(isset($PhD_req->pfic_content)){
                        if($countryId == 1){ // India
                            if(strip_tags($PhD_req->pfic_content) == 0){
                                $phd_degree = 10;
                            }elseif(strip_tags($PhD_req->pfic_content) == 1){
                                $phd_degree = 20;
                            }else{
                                $phd_degree = strip_tags($PhD_req->pfic_content);
                            }
                        }
                        elseif($countryId == 2){ // United States
                            $phd_degree = strip_tags($PhD_req->pfic_content);
                        }
                    }else{
                        $phd_degree = 0;
                    }

                    $education['profession_education_path'] = (isset($profession_education_path->pfic_content) && !empty($profession_education_path->pfic_content)) ? $profession_education_path->pfic_content : '';
                    $education['high_school_req'] = number_format( (float) $high_school, 2, '.', '');
                    $education['junior_college_req'] = number_format( (float) $junior_college, 2, '.', '');
                    $education['bachelor_degree_req'] = number_format( (float) $bachelor_degree, 2, '.', '');
                    $education['masters_degree_req'] = number_format( (float) $masters_degree, 2, '.', '');
                    $education['PhD_req'] = number_format( (float) $phd_degree, 2, '.', '');

                    $professionsData->education = $education;

                    $careerMapHelperArray = Helpers::getCareerMapColumnName();
                    $careerMappingdata = [];
                    if(count($professionsData->careerMapping)>0){
                        foreach ($careerMapHelperArray as $key => $value) {
                            $data = [];
                            if($professionsData->careerMapping[$value] != 'L'){
                                $arr = explode("_", $key);
                                if($arr[0] == 'apt'){
                                    $apptitudeData = $this->objApptitude->getApptitudeDetailBySlug($key);
                                    $data['cm_name'] = $apptitudeData->apt_name;   
                                    $data['cm_image_url'] = Storage::url($this->aptitudeThumb . $apptitudeData->apt_logo);
                                    $data['cm_type'] = Config::get('constant.APPTITUDE_TYPE');   
                                    $data['cm_slug'] = $apptitudeData->apt_slug;   
                                    $careerMappingdata[] = $data;
                                }
                                // elseif($arr[0] == 'mit'){
                                //     $multipleIntelligentData = $this->objMultipleIntelligent->getMultipleIntelligenceDetailBySlug($key);
                                //     $data['cm_name'] = $multipleIntelligentData->mit_name;
                                //     $data['cm_image_url'] = Storage::url($this->miThumb.$multipleIntelligentData->mit_logo);
                                //     $data['cm_type'] = Config::get('constant.MULTI_INTELLIGENCE_TYPE');
                                //     $data['cm_slug'] = $multipleIntelligentData->mi_slug;
                                //     $careerMappingdata[] = $data;
                                // }
                                // elseif($arr[0] == 'pt'){
                                //     $personalityData = $this->objPersonality->getPersonalityDetailBySlug($key);
                                //     $data['cm_name'] = $personalityData->pt_name;
                                //     $data['cm_image_url'] = Storage::url($this->personalityThumb.$personalityData->pt_logo);
                                //     $data['cm_type'] = Config::get('constant.PERSONALITY_TYPE');
                                //     $data['cm_slug'] = $personalityData->pt_slug;
                                //     $careerMappingdata[] = $data;
                                // }
                            }
                        }    
                    }
                    $professionsData->ability = $careerMappingdata;

                    $certificates = [];
                    if(count($professionsData->professionCertificates)>0){
                        foreach ($professionsData->professionCertificates as $key => $value){
                            $data = [];
                            
                            $data = $value->certificate;
                            if($value->certificate['pc_image'] != '' && Storage::size($this->professionCertificationImagePath . $value->certificate['pc_image']) > 0){
                                $data['pc_image'] = Storage::url($this->professionCertificationImagePath . $value->certificate['pc_image']);
                            }
                            else{
                                $data['pc_image'] = Storage::url($this->professionCertificationImagePath . $this->professionDefaultProteenImage);
                            }
                            
                            $certificates[] = $data;
                        }
                    }
                    $professionsData->certificates = $certificates;

                    $subjects = [];
                    if(count($professionsData->professionSubject)>0){
                        foreach ($professionsData->professionSubject as $key => $value){
                            if($value->parameter_grade == 'M' || $value->parameter_grade == 'H')
                            {
                                $subjectData = [];
                                
                                $subjectData = $value->subject;
                                if($value->subject['ps_image'] != '' && Storage::size($this->professionSubjectImagePath . $value->subject['ps_image']) > 0){
                                    $subjectData['ps_image'] = Storage::url($this->professionSubjectImagePath . $value->subject['ps_image']);
                                }
                                else{
                                    $subjectData['ps_image'] = Storage::url($this->professionSubjectImagePath . $this->professionDefaultProteenImage);
                                }
                                $subjectData['ps_type'] = Config::get('constant.INTEREST_TYPE');   
                                $subjects[] = $subjectData;
                            }
                        }
                    }
                    $professionsData->subjects = $subjects;

                    $tags = [];
                    if(count($professionsData->professionTags)>0){
                        foreach ($professionsData->professionTags as $key => $value){
                            $data = [];
                            
                            $data = $value->tag;
                            if($value->tag['pt_image'] != '' && Storage::size($this->professionTagImagePath . $value->tag['pt_image']) > 0){
                                $data['pt_image'] = Storage::url($this->professionTagImagePath . $value->tag['pt_image']);
                            }
                            else{
                                $data['pt_image'] = Storage::url($this->professionTagImagePath . $this->professionDefaultProteenImage);
                            }
                            
                            $tags[] = $data;
                        }
                    }
                    $professionsData->tags = $tags;
                    $adsDetails = Helpers::getAds($request->userId);
                    $mediumAdImages = [];
                    $bannerAdImages = [];
                    if (isset($adsDetails) && !empty($adsDetails)) {
                        foreach ($adsDetails as $ad) {
                            if ($ad['image'] != '') {
                                $ad['image'] = Storage::url($this->saOrigionalImagePath . $ad['image']);
                            } else {
                                $ad['image'] = Storage::url($this->saOrigionalImagePath . 'proteen-logo.png');
                            }
                            switch ($ad['sizeType']) {
                                case '1':
                                    $mediumAdImages[] = $ad;
                                    break;
                                
                                case '3':
                                    $bannerAdImages[] = $ad;
                                    break;

                                default:
                                    break;
                            };
                        }
                    }
                    $professionsData->mediumSizeAds = $mediumAdImages;
                    $professionsData->bannerSizeAds = $bannerAdImages;

                    unset($professionsData->careerMapping);
                    unset($professionsData->professionHeaders);
                    unset($professionsData->professionCertificates);
                    unset($professionsData->professionTags);
                    unset($professionsData->professionSubject);
                    unset($professionsData->starRatedProfession);

                    $response['data'] = $professionsData;
                }
                else{
                    $response['data'] = [];
                }

                $response['status'] = 1;
                $response['login'] = 1;
                $response['message'] = trans('appmessages.default_success_msg');

                $this->log->info('Response for Level3 get Careers Details' , array('api-name'=> 'getCareersDetails'));
            } else {
                $this->log->error('Parameter missing error' , array('api-name'=> 'getCareersDetails'));
                $response['message'] = trans('appmessages.missing_data_msg');
            }
        } else {
            $this->log->error('Parameter missing error' , array('api-name'=> 'getCareersDetails'));
            $response['message'] = trans('appmessages.missing_data_msg');
        }
        return response()->json($response, 200,[],JSON_UNESCAPED_SLASHES);
        //echo json_encode($response, JSON_UNESCAPED_SLASHES);
        //exit;
    }

    public function getBasketByCareerId(Request $request) {
        $response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg')];
        $teenager = $this->teenagersRepository->getTeenagerById($request->userId);
        $this->log->info('Get teenager detail for userId'.$request->userId , array('api-name'=> 'getBasketByCareerId'));
        if($request->userId != "" && $teenager) {
            if($request->careerId != "") {
                $careerId = $request->careerId;
                if($teenager->t_view_information == 1){
                    $countryId = 2; // United States
                } else { 
                    $countryId = 1; // India
                }

                $data = $this->baskets->getBasketsAndProfessionByProfessionId($careerId, $teenager->id, $countryId);
                            
                if($data){
                        
                        if($data->b_logo != '' && Storage::size($this->basketThumbUrl . $data->b_logo) > 0){
                            $data->b_logo = Storage::url($this->basketThumbUrl . $data->b_logo);
                        }
                        else{
                            $data->b_logo = Storage::url($this->basketThumbUrl . $this->basketDefaultProteenImage);
                        }
        
                        $youtubeId = Helpers::youtube_id_from_url($data->b_video);
                        if($youtubeId != ''){
                            $data->b_video = $youtubeId;
                            $data->type_video = '1'; //Youtube
                        }
                        else{
                            $data->type_video = '2'; //Dropbox
                        }
        
                        $data->total_basket_profession = count($data->profession);
                        $data->basket_completed_profession = '12';
                        
                        $match = $nomatch = $moderate = [];
                        $getTeenagerHML = Helpers::getTeenagerMatchScale($request->userId);

                        foreach ($data->profession as $k => $v) {
                            if($v->pf_logo != '' && Storage::size($this->professionThumbUrl . $v->pf_logo) > 0){
                                $data->profession[$k]->pf_logo = Storage::url($this->professionThumbUrl . $v->pf_logo);
                            }
                            else{
                                $data->profession[$k]->pf_logo = Storage::url($this->professionThumbUrl . $this->professionDefaultProteenImage);
                            }
                            $data->profession[$k]->completed = rand(0,1);

                            $data->profession[$k]->matched = isset($getTeenagerHML[$v->id]) ? $getTeenagerHML[$v->id] : '';
                            if($data->profession[$k]->matched == "match") {
                                $match[] = $v->id;
                            } else if($data->profession[$k]->matched == "nomatch") {
                                $nomatch[] = $v->id;
                            } else if($data->profession[$k]->matched == "moderate") {
                                $moderate[] = $v->id;
                            } else {
                                $notSetArray[] = $v->id;
                            }
                        }

                        $data->strong_match = count($match);
                        $data->potential_match = count($moderate);
                        $data->unlikely_match = count($nomatch);

                    $response['data']['baskets'] = $data;
                }
                else{
                    $response['data'] = trans('appmessages.data_empty_msg');
                }

                $response['status'] = 1;
                $response['login'] = 1;
                $response['message'] = trans('appmessages.default_success_msg');

                $this->log->info('Response for Level3 get Basket By Career Id' , array('api-name'=> 'getBasketByCareerId'));
            } else {
                $this->log->error('Parameter missing error' , array('api-name'=> 'getBasketByCareerId'));
                $response['message'] = trans('appmessages.missing_data_msg');
            }
        } else {
            $this->log->error('Parameter missing error' , array('api-name'=> 'getBasketByCareerId'));
            $response['message'] = trans('appmessages.missing_data_msg');
        }
        return response()->json($response, 200);
    }

    public function getTeenagerCareersSearch(Request $request) {
        $response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg')];
        $teenager = $this->teenagersRepository->getTeenagerById($request->userId);
        $this->log->info('Get teenager detail for userId'.$request->userId , array('api-name'=> 'getTeenagerCareersWithBaket'));
        if($request->userId != "" && $teenager) {
            $data = [];
            if($request->searchText != "" && $teenager) {
                $searchText = $request->searchText;
                $queId = (isset($request->sortBy) && !empty($request->sortBy)) ? $request->sortBy : '';
                $ansId = (isset($request->sortOption) && !empty($request->sortOption)) ? $request->sortOption : '';
                //$userId = $request->userId;
                if($teenager->t_view_information == 1) {
                    $countryId = 2; // United States
                } else {
                    $countryId = 1; // India
                }
                if($ansId != '' || $ansId != 0){
                    if($queId == 1) // Industry
                    {
                        $data = $this->baskets->getBasketsAndProfessionWithAttemptedProfessionByBasketIdForUser($ansId, $request->userId, $countryId, $searchText);
                    }
                    else if ($queId == 2) // Careers
                    {
                        $data = $this->baskets->getBasketsAndProfessionWithAttemptedProfessionByProfessionIdForUser($ansId, $request->userId, $countryId, $searchText);
                    } 
                    else if ($queId == 3) // Interest
                    {
                        $data = $this->baskets->getProfessionBasketsByInterestDetailsForUser($ansId, $request->userId, $countryId, $searchText);
                    } 
                    else if ($queId == 4) // Strength
                    {
                        $careersDetails = Helpers::getCareerMapColumnName();
                        $data = $this->baskets->getProfessionBasketsByStrengthDetailsForUser($careersDetails[$ansId], $request->userId, $countryId, $searchText);
                    } 
                    else if ($queId == 5) // Subjects
                    {
                        $data = $this->baskets->getProfessionBasketsBySubjectForUser($ansId, $request->userId, $countryId, $searchText);
                    } 
                    else if ($queId == 6) // Tags
                    {
                        $data = $this->baskets->getProfessionBasketsByTagForUser($ansId, $request->userId, $countryId, $searchText);
                    }
                } else {
                    $data = $this->baskets->getStarredBasketsAndProfessionByUserId($request->userId, $countryId, $searchText);
                }
                //$data = $this->baskets->getBasketsAndStarRatedProfessionByUserIdAndSearchValue($teenager->id,$searchText);
                $getTeenagerHML = Helpers::getTeenagerMatchScale($teenager->id);
                
                if($data) {
                    foreach ($data as $key => $value) {
                        if($value->b_logo != '' && Storage::size($this->basketThumbUrl . $value->b_logo) > 0){
                            $data[$key]->b_logo = Storage::url($this->basketThumbUrl . $value->b_logo);
                        } else {
                            $data[$key]->b_logo = Storage::url($this->basketThumbUrl . $this->basketDefaultProteenImage);
                        }
                        
                        $youtubeId = Helpers::youtube_id_from_url($value->b_video);
                        if($youtubeId != '') {
                            $data[$key]->b_video = $youtubeId;
                            $data[$key]->type_video = '1'; //Youtube
                        } else {
                            $data[$key]->type_video = '2'; //Dropbox
                        }

                        $match = $nomatch = $moderate = [];
                        foreach ($value->profession as $k => $v) {
                            if($v->pf_logo != '' && Storage::size($this->professionThumbUrl . $v->pf_logo) > 0){
                                $data[$key]->profession[$k]->pf_logo = Storage::url($this->professionThumbUrl . $v->pf_logo);
                            }
                            else{
                                $data[$key]->profession[$k]->pf_logo = Storage::url($this->professionThumbUrl . $this->professionDefaultProteenImage);
                            }
                            $data[$key]->profession[$k]->completed = rand(0,1);

                            $data[$key]->profession[$k]->matched = isset($getTeenagerHML[$v->id]) ? $getTeenagerHML[$v->id] : '';
                            if($data[$key]->profession[$k]->matched == "match") {
                                $match[] = $v->id;
                            } else if($data[$key]->profession[$k]->matched == "nomatch") {
                                $nomatch[] = $v->id;
                            } else if($data[$key]->profession[$k]->matched == "moderate") {
                                $moderate[] = $v->id;
                            } else {
                                $notSetArray[] = $v->id;
                            }

                        }
                        
                        $data[$key]->total_basket_profession = count($value->profession);
                        $data[$key]->basket_completed_profession = '12';
                        $data[$key]->strong_match = count($match);
                        $data[$key]->potential_match = count($moderate);
                        $data[$key]->unlikely_match = count($nomatch);

                    }
                    $response['data']['baskets'] = $data;
                }
                else{
                    $response['data'] = trans('appmessages.data_empty_msg');
                }
                $response['status'] = 1;
                $response['login'] = 1;
                $response['message'] = trans('appmessages.default_success_msg');

                $this->log->info('Response for Level3 get Teenager Careers With Baket' , array('api-name'=> 'getTeenagerCareersWithBaket'));
            } else {
                $this->log->error('Parameter missing error' , array('api-name'=> 'getTeenagerCareersWithBaket'));
                $response['message'] = trans('appmessages.missing_data_msg');
            }
        } else {
            $this->log->error('Parameter missing error' , array('api-name'=> 'getTeenagerCareersWithBaket'));
            $response['message'] = trans('appmessages.missing_data_msg');
        }
        return response()->json($response, 200);
    }

    public function addStarToCareer(Request $request) 
    {
        $response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg')];
        $teenager = $this->teenagersRepository->getTeenagerById($request->userId);
        $this->log->info('Add Career to my career'.$request->userId , array('api-name'=> 'addStarToCareer'));
        if($request->userId != "" && $teenager) {
            $careerId = $request->careerId;
            $careerDetails['srp_teenager_id'] = $request->userId;
            $careerDetails['srp_profession_id'] = $careerId;
            $return = $this->objStarRatedProfession->addStarToCareer($careerDetails);
            
            $response['status'] = 1;
            $response['login'] = 1;
            $response['message'] = "Added";
            $response['data'] = ['careerId' => $request->careerId];
        } else {
            $this->log->error('Parameter missing error' , array('api-name'=> 'addStarToCareer'));
            $response['message'] = trans('appmessages.missing_data_msg');
        }
        return response()->json($response, 200);
        exit;
    }

    
    public function getCareerFansPageWise(Request $request) {
        $response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg')];
        $teenager = $this->teenagersRepository->getTeenagerById($request->userId);
        $this->log->info('Get teenager detail for userId'.$request->userId , array('api-name'=> 'getAllCareers'));
        if($request->userId != "" && $teenager) {
            if($request->careerId != "") {            
                $record = 0;
                
                if($request->pageNo != '' && $request->pageNo > 1){
                    $record = ($request->pageNo-1) * 10;
                }

                $data = $this->objTeenagers->getAllTeenWhoStarRatedCareer($record, $request->careerId, $teenager->id);
                $dataNextPage = $this->objTeenagers->getAllTeenWhoStarRatedCareer(($record+10), $request->careerId, $teenager->id);
                
                if(count($dataNextPage)>0){
                    $response['next'] = 1;
                }
                else{
                    $response['next'] = 0;
                }
                
                if($data){
                    foreach($data as $key => $value){
                        if(isset($value->t_photo) && $value->t_photo != '' && Storage::size(Config::get('constant.TEEN_THUMB_IMAGE_UPLOAD_PATH').$value->t_photo) > 0) {
                                $teenPhoto = Config::get('constant.TEEN_THUMB_IMAGE_UPLOAD_PATH').$value->t_photo;
                        } else {
                            $teenPhoto = Config::get('constant.TEEN_THUMB_IMAGE_UPLOAD_PATH').'proteen-logo.png';
                        }
                        $data[$key]->t_photo = Storage::url($teenPhoto);
                    }
                    $response['data'] = $data;
                } else {
                    $response['data'] = trans('appmessages.data_empty_msg');
                }

                $response['status'] = 1;
                $response['login'] = 1;
                $response['message'] = trans('appmessages.default_success_msg');
            } else {
                $response['status'] = 0;
                $response['login'] = 1;
                $this->log->error('Parameter missing error' , array('api-name'=> 'getAllCareers'));
                $response['message'] = trans('appmessages.missing_data_msg');
            }
            $this->log->info('Response for Level3 get All careers' , array('api-name'=> 'getAllCareers'));
        } else {
            $this->log->error('Parameter missing error' , array('api-name'=> 'getAllCareers'));
            $response['message'] = trans('appmessages.missing_data_msg');
        }
        return response()->json($response, 200);
    }  

    public function getMyCareerPageFilterDetails(Request $request)
    {
        $response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg')];
        $teenager = $this->teenagersRepository->getTeenagerById($request->userId);
        if($request->userId != "" && $teenager) {
            $filterData = Helpers::getMyCareerPageFilter();
            $industryData = $this->baskets->getActiveBasketsOrderByName();//Industry
            $industryDetails = [];
            foreach($industryData as $industry) {
                $industryArr = [];
                $industryArr['id'] = $industry->id;
                $industryArr['name'] = $industry->b_name;
                $industryDetails[] = $industryArr;
            }
            $careersData = $this->professions->getActiveProfessionsOrderByName();//Careers
            $careerDetails = [];
            foreach($careersData as $career) {
                $careerArr = [];
                $careerArr['id'] = $career->id;
                $careerArr['name'] = $career->pf_name;
                $careerDetails[] = $careerArr;
            }
            $interestData = $data = $this->objInterest->getActiveInterest(); // Interest
            $interestDetails = [];
            foreach($interestData as $interest) {
                $interestArr = [];
                $interestArr['id'] = $interest->it_slug;
                $interestArr['name'] = $interest->it_name;
                $interestDetails[] = $interestArr;
            }

            //Get strength details array 
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
            $strengthData = array_merge($aptData, $ptData, $miData); //Strength
            $strengthDetails = [];
            foreach($strengthData as $strength) {
                $strengthArr = [];
                $strengthArr['id'] = $strength['slug'];
                $strengthArr['name'] = $strength['name'];
                $strengthDetails[] = $strengthArr;
            }

            $subjectData = $this->objSubject->getAllProfessionSubjects(); // Subjects
            $subjectDetails = [];
            foreach($subjectData as $subject) {
                $subjectArr = [];
                $subjectArr['id'] = $subject->id;
                $subjectArr['name'] = $subject->ps_name;
                $subjectDetails[] = $subjectArr;
            }
            $tagsData = $this->objTag->getAllProfessionTags(); // Tags
            $tagDetails = [];
            foreach($tagsData as $tag) {
                $tagArr = [];
                $tagArr['id'] = $tag->id;
                $tagArr['name'] = $tag->pt_name;
                $tagDetails[] = $tagArr;
            }
            $sortByArr = [];
            foreach ($filterData as $filterKey => $filterVal) {
                if ($filterKey == 1) {
                    $sortByArr[] = array('id' => $filterKey, 'name' => $filterVal, 'sortData' => $industryDetails);
                } else if ($filterKey == 2) {
                    $sortByArr[] = array('id' => $filterKey, 'name' => $filterVal, 'sortData' => $careerDetails);
                } else if ($filterKey == 3) {
                    $sortByArr[] = array('id' => $filterKey, 'name' => $filterVal, 'sortData' => $interestDetails);
                } else if ($filterKey == 4) {
                    $sortByArr[] = array('id' => $filterKey, 'name' => $filterVal, 'sortData' => $strengthDetails);
                } else if ($filterKey == 5) {
                    $sortByArr[] = array('id' => $filterKey, 'name' => $filterVal, 'sortData' => $subjectDetails);
                } else if ($filterKey == 6) {
                    $sortByArr[] = array('id' => $filterKey, 'name' => $filterVal, 'sortData' => $tagDetails);
                } else {
                    $sortByArr = [];
                }
            }
            $this->log->info('User retrieved filter details of My career Page', array('userId' => $request->userId, 'api-name'=> 'getMyCareerPageFilterDetails'));
            $response['data']['sortBy'] = $sortByArr;
            $response['status'] = 1;
            $response['login'] = 1;
            $response['message'] = trans('appmessages.default_success_msg');
        } else {
            $this->log->error('Parameter missing error', array('userId' => $request->userId, 'api-name'=> 'getMyCareerPageFilterDetails'));
            $response['message'] = trans('appmessages.missing_data_msg');
        }
        return response()->json($response, 200);
    }
    
    /*
     * Save teen data for L3 career attempt 
     */
    public function level3CareerResearch()
    {
        $response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg')];
        $teenager = $this->teenagersRepository->getTeenagerById($request->userId);
        $this->log->info('Save teenager L3 career research booster point for userId'.$request->userId , array('api-name'=> 'level3CareerResearch'));
        
        if($request->userId != "" && $teenager) {
            if($request->careerId != "") {
                $teenagerId = Auth::guard('teenager')->user()->id;
                $professionId = Input::get('professionId');
                $type = Input::get('type');
                $isYouTube = Input::get('isYouTube');
                $points = ($isYouTube == 1)?config::get('constant.LEVEL3_PROFESSION_POINTS'):(2*config::get('constant.LEVEL3_PROFESSION_POINTS'));

                $teenagerLevelPoints = $this->teenagerBoosterPoint->getTeenagerBoosterPoint($teenagerId,config::get('constant.LEVEL3_ID'));

                $addPoint = "no";
                $teenagerProfessionAttempted = $this->professionsRepository->getTeenagerProfessionAttempted($teenagerId, $professionId, $type);

                if (count($teenagerProfessionAttempted) > 0) {
                   $addedtype = $teenagerProfessionAttempted[0]->tpa_type;
                    $addedtypeArr = explode(',', $addedtype);
                    if (!in_array($type, $addedtypeArr)) {
                        if ($addedtypeArr[0] == '') {
                            $addedtype = $type;
                        } else {
                            $addedtype = $addedtypeArr[0] . ',' . $type;
                        }
                        $addData = $this->professionsRepository->addTeenagerProfessionAttempted($teenagerId, $professionId, $addedtype, $operation = 'update');
                        $addPoint = "yes";
                    } else {
                        $addPoint = "no";
                    } 
                } else {
                    $addData = $this->professionsRepository->addTeenagerProfessionAttempted($teenagerId, $professionId, $type, $operation = 'add');
                    $addPoint = "yes";            
                }

                $teenagerLevel3PointsRow['tlb_teenager'] = $teenagerId;
                $teenagerLevel3PointsRow['tlb_level'] = config::get('constant.LEVEL3_ID');

                if ($addPoint == "yes") {            
                    if (isset($teenagerLevelPoints) && !empty($teenagerLevelPoints)) {                               
                        $teenagerLevel3PointsRow['tlb_points'] = $teenagerLevelPoints->tlb_points + $points;   
                        unset($teenagerLevel3PointsRow['updated_at']);
                        $teenagerLevelPoints = $this->teenagerBoosterPoint->updateTeenagerBoosterPoint($teenagerLevelPoints->id,$teenagerLevel3PointsRow);
                    } else {
                        $teenagerLevel3PointsRow['tlb_points'] = $points;
                        $teenagerLevelPoints = $this->teenagerBoosterPoint->addTeenagerBoosterPoint($teenagerLevel3PointsRow);                
                    }
                    $proCoins = Teenagers::find($teenagerId);
                    $configValue = Helpers::getConfigValueByKey('PROCOINS_FACTOR_L1');
                    if($proCoins) {
                        $proCoins->t_coins = (int)$proCoins->t_coins + ( $points * $configValue );
                        $proCoins->save();
                    }
                } 
                $response['data'] = trans('appmessages.data_empty_msg');
                $response['status'] = 1;
                $response['login'] = 1;
                $response['message'] = trans('appmessages.default_success_msg');
            }else{
                $response['status'] = 0;
                $response['login'] = 1;
                $this->log->error('Parameter missing error' , array('api-name'=> 'level3CareerResearch'));
                $response['message'] = trans('appmessages.missing_data_msg');
            }
        }else 
        {
            $this->log->error('Parameter missing error' , array('api-name'=> 'level3CareerResearch'));
            $response['message'] = trans('appmessages.missing_data_msg');
        }
        return response()->json($response, 200);  
    }    
}
