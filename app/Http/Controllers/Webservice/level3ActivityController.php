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
use Config;
use Storage;
use Helpers;  
use Auth;
use Input;
use Redirect;
use Illuminate\Http\Request;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class level3ActivityController extends Controller {

    public function __construct(TeenagersRepository $teenagersRepository, ProfessionsRepository $professionsRepository, BasketsRepository $basketsRepository) {
        $this->teenagersRepository = $teenagersRepository;
        $this->professionsRepository = $professionsRepository;
        $this->baskets = new Baskets();
        $this->professions = new Professions();
        $this->professionHeaders = new ProfessionHeaders();
        $this->objStarRatedProfession = new StarRatedProfession;
        $this->log = new Logger('api-level1-activity-controller');
        $this->log->pushHandler(new StreamHandler(storage_path().'/logs/monolog-'.date('m-d-Y').'.log'));    
    }

    public function getAllBasktes(Request $request) {
        $response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg')];
        $teenager = $this->teenagersRepository->getTeenagerById($request->userId);
        $this->log->info('Get teenager detail for userId'.$request->userId , array('api-name'=> 'getLevel2Activity'));
        if($request->userId != "" && $teenager) {

            $data = $this->baskets->where('deleted',config::get('constant.ACTIVE_FLAG'))->get();
            
            if($data){
                $response['data'] = $data;
            }
            else{
                $response['data'] = trans('appmessages.data_empty_msg');
            }

            $response['status'] = 1;
            $response['login'] = 1;
            $response['message'] = trans('appmessages.default_success_msg');

            $this->log->info('Response for Level2questions' , array('api-name'=> 'getLevel2Activity'));
        } else {
            $this->log->error('Parameter missing error' , array('api-name'=> 'getLevel2Activity'));
            $response['message'] = trans('appmessages.missing_data_msg');
        }
        return response()->json($response, 200);
    }
    
    public function getAllCareers(Request $request) {
        $response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg')];
        $teenager = $this->teenagersRepository->getTeenagerById($request->userId);
        $this->log->info('Get teenager detail for userId'.$request->userId , array('api-name'=> 'getLevel2Activity'));
        if($request->userId != "" && $teenager) {

            $data = $this->professions->getActiveProfessionsOrderByName();
            
            if($data){
                $response['data'] = $data;
            }
            else{
                $response['data'] = trans('appmessages.data_empty_msg');
            }

            $response['status'] = 1;
            $response['login'] = 1;
            $response['message'] = trans('appmessages.default_success_msg');

            $this->log->info('Response for Level2questions' , array('api-name'=> 'getLevel2Activity'));
        } else {
            $this->log->error('Parameter missing error' , array('api-name'=> 'getLevel2Activity'));
            $response['message'] = trans('appmessages.missing_data_msg');
        }
        return response()->json($response, 200);
    }  
    
    public function getCareersByBasketId(Request $request) {
        $response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg')];
        $teenager = $this->teenagersRepository->getTeenagerById($request->userId);
        $this->log->info('Get teenager detail for userId'.$request->userId , array('api-name'=> 'getLevel2Activity'));
        if($request->userId != "" && $teenager) {

            if($teenager->t_view_information == 1){
                $this->countryId = 2; // United States
            }else{
                $this->countryId = 1; // India
            }

            $basketId = $request->basketId;

            $careersData = $this->baskets->getBasketsAndProfessionByBaketIdAndCountryId($basketId,$this->countryId);

            if($careersData){
                foreach ($careersData->profession as $key => $value) {
                    
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
                    
                    unset($careersData->profession[$key]->professionHeaders);
                }

                $response['data'] = $careersData;
            }
            else{
                $response['data'] = trans('appmessages.data_empty_msg');
            }

            $response['status'] = 1;
            $response['login'] = 1;
            $response['message'] = trans('appmessages.default_success_msg');

            $this->log->info('Response for Level2questions' , array('api-name'=> 'getLevel2Activity'));
        } else {
            $this->log->error('Parameter missing error' , array('api-name'=> 'getLevel2Activity'));
            $response['message'] = trans('appmessages.missing_data_msg');
        }
        return response()->json($response, 200);
    }

    public function getCareersSearch(Request $request) {
        $response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg')];
        $teenager = $this->teenagersRepository->getTeenagerById($request->userId);
        $this->log->info('Get teenager detail for userId'.$request->userId , array('api-name'=> 'getLevel2Activity'));
        if($request->userId != "" && $teenager) {

            $searchValue = $request->searchText;
            
            $data = $this->baskets->getBasketsAndProfessionBySearchValue($searchValue);
                        
            if($data){
                $response['data'] = $data;
            }
            else{
                $response['data'] = trans('appmessages.data_empty_msg');
            }

            $response['status'] = 1;
            $response['login'] = 1;
            $response['message'] = trans('appmessages.default_success_msg');

            $this->log->info('Response for Level2questions' , array('api-name'=> 'getLevel2Activity'));
        } else {
            $this->log->error('Parameter missing error' , array('api-name'=> 'getLevel2Activity'));
            $response['message'] = trans('appmessages.missing_data_msg');
        }
        return response()->json($response, 200);
    }

    public function getTeenagerCareersWithBaket(Request $request) {
        $response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg')];
        $teenager = $this->teenagersRepository->getTeenagerById($request->userId);
        $this->log->info('Get teenager detail for userId'.$request->userId , array('api-name'=> 'getLevel2Activity'));
        if($request->userId != "" && $teenager) {

            $data = $this->baskets->getStarredBasketsAndProfessionByUserId($teenager->id);

            if($data){
                $response['data'] = $data;
            }
            else{
                $response['data'] = trans('appmessages.data_empty_msg');
            }

            $response['status'] = 1;
            $response['login'] = 1;
            $response['message'] = trans('appmessages.default_success_msg');

            $this->log->info('Response for Level2questions' , array('api-name'=> 'getLevel2Activity'));
        } else {
            $this->log->error('Parameter missing error' , array('api-name'=> 'getLevel2Activity'));
            $response['message'] = trans('appmessages.missing_data_msg');
        }
        return response()->json($response, 200);
    }
}
