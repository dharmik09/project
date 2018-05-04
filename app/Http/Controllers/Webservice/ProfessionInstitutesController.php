<?php

namespace App\Http\Controllers\Webservice;

use App\Http\Controllers\Controller;
use App\Services\Teenagers\Contracts\TeenagersRepository;
use Illuminate\Http\Request;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use App\ProfessionInstitutes;
use App\ProfessionInstitutesSpeciality;
use App\State;
use Auth;
use Redirect;
use Helpers;
use Input;
use Config;
use Storage;

class ProfessionInstitutesController extends Controller {

    public function __construct(TeenagersRepository $teenagersRepository) 
    {
        $this->teenagersRepository = $teenagersRepository;
        $this->objProfessionInstitutes = new ProfessionInstitutes();
        $this->objProfessionInstitutesSpeciality = new ProfessionInstitutesSpeciality();
        $this->log = new Logger('api-level3-activity-controller');
        $this->log->pushHandler(new StreamHandler(storage_path().'/logs/monolog-'.date('m-d-Y').'.log')); 
        $this->professionInstituteOriginalImageUploadPath = Config::get('constant.PROFESSION_INSTITUTE_PHOTO_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->professionInstituteThumbImageUploadPath = Config::get('constant.PROFESSION_INSTITUTE_PHOTO_THUMB_IMAGE_UPLOAD_PATH');
        $this->professionInstituteThumbImageHeight = Config::get('constant.PROFESSION_INSTITUTE_PHOTO_THUMB_IMAGE_HEIGHT');
        $this->professionInstituteThumbImageWidth = Config::get('constant.PROFESSION_INSTITUTE_PHOTO_THUMB_IMAGE_WIDTH');
        $this->objState = new State();
    }

    public function getProfessionInstituteFilter(Request $request){
        $response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg')];
        $teenager = $this->teenagersRepository->getTeenagerById($request->userId);
        $this->log->info('Get teenager detail for userId'.$request->userId , array('api-name'=> 'getProfessionInstituteFilter'));
        if($request->userId != "" && $teenager) {
            $country = $teenager->t_view_information;
            $filterData = Helpers::getProfessionInstituteFilter($country);
            if(count($filterData)>0){
                $response['data'] = $filterData;
            }
            else{
                $response['data'] = trans('appmessages.data_empty_msg');
            }

            $response['status'] = 1;
            $response['login'] = 1;
            $response['message'] = trans('appmessages.default_success_msg');

            $this->log->info('Response for get Profession Institute Filter API' , array('api-name'=> 'getProfessionInstituteFilter'));
        } else {
            $this->log->error('Parameter missing error' , array('api-name'=> 'getProfessionInstituteFilter'));
            $response['message'] = trans('appmessages.missing_data_msg');
        }
        return response()->json($response, 200);
    }

    public function getProfessionInstituteFilterData(Request $request){
        $response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg')];
        $teenager = $this->teenagersRepository->getTeenagerById($request->userId);
        $this->log->info('Get teenager detail for userId'.$request->userId , array('api-name'=> 'getProfessionInstituteFilterData'));
        if($request->userId != "" && $teenager && $request->filterType != "") {
            
            $allData = [];
            
            $countryId = ($teenager->t_view_information == 1) ? 2 /* United States */ : 1 /* India */;

            $questionType = $request->filterType;
            if($questionType == "Speciality"){
                $institutesSpecialityData = $this->objProfessionInstitutesSpeciality->getAllProfessionInstitutesSpeciality($countryId);                
                $dataArray1 = [];
                foreach ($institutesSpecialityData as $key => $value) {
                    $data['label'] = (string) $value->pis_name;
                    $data['value'] = (string) $value->pis_name;
                    $dataArray1[] = $data;
                }
                $allData['dataArray1'] = $dataArray1;
                
                $response['arrayCount'] = 1;
            }
            elseif($questionType == "Institute_Affiliation"){
                $institutesData = $this->objProfessionInstitutes->getProfessionInstitutesUniqueAffiliatUniversityByCountryId($countryId);
                
                $dataArray1 = [];
                foreach ($institutesData as $key => $value) {
                    $data['label'] = (string) $value->affiliat_university;
                    $data['value'] = (string) $value->affiliat_university;
                    $dataArray1[] = $data;
                }
                $allData['dataArray1'] = $dataArray1;
                
                $response['arrayCount'] = 1;
            }
            elseif($questionType == "Management_Category"){
                $institutesData = $this->objProfessionInstitutes->getProfessionInstitutesUniqueManagementByCountryId($countryId);
                
                $dataArray1 = [];
                foreach ($institutesData as $key => $value) {
                    $data['label'] = (string) $value->management;
                    $data['value'] = (string) $value->management;
                    $dataArray1[] = $data;
                }
                $allData['dataArray1'] = $dataArray1;
                
                $response['arrayCount'] = 1;
            }
            elseif($questionType == "Accreditation"){
                $institutesData = $this->objProfessionInstitutes->getProfessionInstitutesUniqueAccreditationBodyByCountryId($countryId);
                
                $dataArray1 = [];
                foreach ($institutesData as $key => $value) {
                    $data['label'] = (string) $value->accreditation_body;
                    $data['value'] = (string) $value->accreditation_body;
                    $dataArray1[] = $data;
                }
                $allData['dataArray1'] = $dataArray1;
                
                $response['arrayCount'] = 1;
            }
            elseif($questionType == "Hostel"){
                
                $hostelArray = [
                                ['label'=>'Not Available', 'value'=>'0'],
                                ['label'=>'Available', 'value'=>'1'],
                            ];
                $allData['dataArray1'] = $hostelArray;
                
                $response['arrayCount'] = 1;
            }
            elseif($questionType == "Gender"){
                
                $GenderArray = [
                                ['label'=>'Co-Ed', 'value'=>'0'],
                                ['label'=>'Girls Only', 'value'=>'1'],
                            ];
                $allData['dataArray1'] = $GenderArray;

                $response['arrayCount'] = 1;
            }
            elseif($questionType == "State"){
                
                if ($teenager->t_view_information == 1) {
                    $countryId = 2; // United States
                } else {
                    $countryId = 1; // India
                }
        
                $stateWiseCityData = $this->objState->getAllStatesWithCityByCountryId($countryId);
                
                $state = [];
                
                foreach ($stateWiseCityData as $key => $value) {
                    $state[] = array('value' => ucwords(strtolower($value->s_name)));
                }
                $allData['dataArray1'] = $state;

                $response['arrayCount'] = 1;
            }
            elseif($questionType == "City"){
                
                if ($teenager->t_view_information == 1) {
                    $countryId = 2; // United States
                } else {
                    $countryId = 1; // India
                }
        
                $stateWiseCityData = $this->objState->getAllStatesWithCityByCountryId($countryId);
                
                $city = [];
                
                foreach ($stateWiseCityData as $key => $value) {
                    foreach ($value->city as $k => $v) {
                        $city[] = array('value' => ucwords(strtolower($v->c_name)));
                    }
                }
                $allData['dataArray1'] = $city;

                $response['arrayCount'] = 1;
            }
            elseif($questionType == "Autonomous"){
                
                $GenderArray = [
                                ['label'=>'UnAutonomous', 'value'=>'0'],
                                ['label'=>'Autonomous', 'value'=>'1'],
                            ];
                $allData['dataArray1'] = $GenderArray;

                $response['arrayCount'] = 1;
            }
            elseif($questionType == "Fees"){
                $minimumFeesData = $this->objProfessionInstitutes->getProfessionInstitutesUniqueMinimumFeeByCountryId($countryId);
                $maximumFeesData = $this->objProfessionInstitutes->getProfessionInstitutesUniqueMaximumFeeByCountryId($countryId);

                $dataArray1 = [];
                foreach ($minimumFeesData as $key => $value) {
                    $data['label'] = (string) number_format((int)$value->minimum_fee, 0, '.', ',');
                    $data['value'] = (string) $value->minimum_fee;
                    $dataArray1[] = $data;
                }
                $allData['dataArray1'] = $dataArray1;
                
                $dataArray2 = [];
                foreach ($maximumFeesData as $key => $value) {
                    $data['label'] = (string) number_format((int)$value->maximum_fee, 0, '.', ',');
                    $data['value'] = (string) $value->maximum_fee;
                    $dataArray2[] = $data;
                }
                $allData['dataArray2'] = $dataArray2;
                
                $response['arrayCount'] = 2;
            }

            $response['data'] = $allData;
            $response['status'] = 1;
            $response['login'] = 1;
            $response['message'] = trans('appmessages.default_success_msg');

            $this->log->info('Response for get Profession Institute Filter\'s data API' , array('api-name'=> 'getProfessionInstituteFilterData'));
        } else {
            $this->log->error('Parameter missing error' , array('api-name'=> 'getProfessionInstituteFilterData'));
            $response['message'] = trans('appmessages.missing_data_msg');
        }
        return response()->json($response, 200);
    }


    public function getProfessionInstitute(Request $request){
        $response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg')];
        $teenager = $this->teenagersRepository->getTeenagerById($request->userId);
        $this->log->info('Get teenager detail for userId'.$request->userId , array('api-name'=> 'getProfessionInstitute'));
        if($request->userId != "" && $teenager) {

            $countryId = ($teenager->t_view_information == 1) ? 2 /* United States */ : 1 /* India */;
            
            $pageNo = 1;
            $questionType = "";
            $answer = "";
            $searchText = "";

            if($request->pageNo != "" && isset($request->pageNo)){
                $pageNo = $request->pageNo;
            }
            if($request->filterType != "" && isset($request->filterType)){
                $questionType = $request->filterType;
            }
            if($request->filterValue != "" && isset($request->filterValue)){
                $answer = $request->filterValue;
            }
            if($request->searchText != "" && isset($request->searchText)){
                $searchText = $request->searchText;
            }

            $record = ($pageNo-1) * 5;

            $nextProfessionInstituteData = $this->objProfessionInstitutes->getProfessionInstitutesWithFilterByCountryId($searchText, $questionType, $answer, ($record+5), $countryId);
            $next = 0;
            if(count($nextProfessionInstituteData)>0){
                $next = 1;
            }
            $professionInstituteData = $this->objProfessionInstitutes->getProfessionInstitutesWithFilterByCountryId($searchText, $questionType, $answer, $record, $countryId);
            $data = [];
            $response['next'] = $next;
            if(count($professionInstituteData)>0){
                foreach ($professionInstituteData as $key => $value) {
                    $instituteWebsite = "";
                    $instituteName = "";
                    $instituteEstablishmentYear = "-";
                    $instituteAddress = "-";
                    $institutePhoto = 'img/insti-logo.png';
                    $instituteLatitute = "";
                    $instituteLongitude = "";
                    $instituteAffiliateUniversity = "-";
                    $instituteManagement = "-";
                    $instituteFeeRange = "-";
                    $instituteHostelCount = "-";
                    $instituteGender = "Co-Ed";
                    $instituteAutonomous = "No";
                    $instituteAccreditationScore = "-";
                    $instituteAccreditationBody = "-";
                    $instituteSpeciality = [];
                    $instituteIsSignup = "";

                    if(isset($value->website) && $value->website != ""){
                        $instituteWebsite = 'http://'.$value->website;
                    }
                    
                    if(isset($value->college_institution) && $value->college_institution != ""){
                        $instituteName = $value->college_institution;
                    }
                    
                    if(isset($value->year_of_establishment) && $value->year_of_establishment != ""){
                        $instituteEstablishmentYear = $value->year_of_establishment;
                    }
                    
                    if(isset($value->address_line1) && $value->address_line1 != ""){
                        $instituteAddress = $value->address_line1.' '.$value->address_line2.', '.$value->city.', '.$value->district;
                    }
                    
                    if(isset($value->latitude) && $value->latitude != "" && $value->latitude != "NA" && isset($value->longitude) && $value->longitude != "" && $value->longitude != "NA"){
                        $instituteLatitute = $value->latitude;
                        $instituteLongitude = $value->longitude;
                    }

                    if(isset($value->affiliat_university) && $value->affiliat_university != ""){
                        $instituteAffiliateUniversity = $value->affiliat_university;
                    }        
                    if(isset($value->management) && $value->management != ""){
                        $instituteManagement = $value->management;
                    }        
                 
                    if(isset($value->minimum_fee) && $value->minimum_fee != "" && isset($value->maximum_fee) && $value->maximum_fee != ""){
                        $instituteFeeRange = number_format((int)$value->minimum_fee, 0, '.', ',') .' - '. number_format((int)$value->maximum_fee, 0, '.', ',');
                    }        
                    if(isset($value->hostel_count) && $value->hostel_count != ""){
                        $instituteHostelCount = $value->hostel_count;
                    }        
//                    if(isset($value->girl_exclusive) && $value->girl_exclusive != ""){
//                        if($value->girl_exclusive = 1){
//                            $instituteGender = "Girls Only";
//                        }
//                    }
//                    if(isset($value->autonomous) && $value->autonomous != ""){
//                        if($value->autonomous = 1){
//                            $instituteAutonomous = "Yes";
//                        }
//                    }
                    
                    if($countryId == 2){
                        $instituteAutonomous = (isset($value->autonomous) && !empty($value->autonomous) && $value->autonomous == 1)?'YCY':'YPY';
                        $instituteGender = (isset($value->girl_exclusive) && !empty($value->girl_exclusive) && $value->girl_exclusive == 1)?'Non Co-Ed':'Co-Ed';
                    }
                    else{              
                        $instituteAutonomous = (isset($value->autonomous) && !empty($value->autonomous) && $value->autonomous == 1)?'Yes':'No';
                        $instituteGender = (isset($value->girl_exclusive) && !empty($value->girl_exclusive) && $value->girl_exclusive == 1)?'Girls Only':'Co-Ed';
                    }   
                    
                    if(isset($value->accreditation_score) && $value->accreditation_score != ""){
                        $instituteAccreditationScore = $value->accreditation_score;
                    }
                    if(isset($value->accreditation_body) && $value->accreditation_body != ""){
                        $instituteAccreditationBody = $value->accreditation_body;
                    }
                    if(isset($value->speciality) && $value->speciality != ""){
                        $instituteSpeciality = explode("#", $value->speciality);
                    }
                    if(isset($value->image) && $value->image != ""){
                        $institutePhoto = $this->professionInstituteThumbImageUploadPath.$value->image;
                    }
                    if(isset($value->is_institute_signup) && $value->is_institute_signup != "" && $value->is_institute_signup == 1){
                        $instituteIsSignup = Storage::url('img/logo.png');
                    }

                    $instituteData['Website'] = $instituteWebsite;
                    $instituteData['Name'] = ucwords(strtolower($instituteName));
                    $instituteData['EstablishmentYear'] = $instituteEstablishmentYear;
                    $instituteData['Address'] = ucwords(strtolower($instituteAddress));
                    $instituteData['Photo'] = Storage::url($institutePhoto);
                    $instituteData['MapLatitute'] = $instituteLatitute;
                    $instituteData['MapLongitude'] = $instituteLongitude;
                    $instituteData['AffiliateUniversity'] = $instituteAffiliateUniversity;
                    $instituteData['Management'] = $instituteManagement;
                    $instituteData['FeeRange'] = $instituteFeeRange;
                    $instituteData['HostelCount'] = $instituteHostelCount;
                    $instituteData['Gender'] = $instituteGender;
                    $instituteData['Autonomous'] = $instituteAutonomous;
                    $instituteData['AccreditationScore'] = $instituteAccreditationScore;
                    $instituteData['AccreditationBody'] = $instituteAccreditationBody;
                    $instituteData['Speciality'] = $instituteSpeciality;
                    $instituteData['IsSignupImage'] = $instituteIsSignup;
                    $data[] = $instituteData;
                }
                $response['data'] = $data;
                $response['message'] = trans('appmessages.default_success_msg');
            }
            else{
                $response['data'] = $data;
                $response['message'] = trans('appmessages.data_empty_msg');
            }
            $response['status'] = 1;
            $response['login'] = 1;

            $this->log->info('Response for get Profession Institute Data API' , array('api-name'=> 'getProfessionInstitute'));
        } else {
            $this->log->error('Parameter missing error' , array('api-name'=> 'getProfessionInstitute'));
            $response['message'] = trans('appmessages.missing_data_msg');
        }
        return response()->json($response, 200);
    }
}