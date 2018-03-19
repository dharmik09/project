<?php

namespace App\Http\Controllers\Webservice;

use App\Http\Controllers\Controller;
use App\Services\Teenagers\Contracts\TeenagersRepository;
use App\ProfessionInstitutes;
use Auth;
use Redirect;
use Illuminate\Http\Request;
use Helpers;
use Input;
use Config;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class ProfessionInstitutesController extends Controller {

    public function __construct(TeenagersRepository $teenagersRepository) 
    {
        $this->teenagersRepository = $teenagersRepository;
        $this->objProfessionInstitutes = new ProfessionInstitutes();
        $this->log = new Logger('api-level3-activity-controller');
        $this->log->pushHandler(new StreamHandler(storage_path().'/logs/monolog-'.date('m-d-Y').'.log')); 
    }

    public function getProfessionInstituteFilter(Request $request){
        $response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg')];
        $teenager = $this->teenagersRepository->getTeenagerById($request->userId);
        $this->log->info('Get teenager detail for userId'.$request->userId , array('api-name'=> 'getProfessionInstituteFilter'));
        if($request->userId != "" && $teenager) {

            $filterData = Helpers::getProfessionInstituteFilter();
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

            $questionType = $request->filterType;
            if($questionType == "Institute_Affiliation"){
                $institutesData = $this->objProfessionInstitutes->getProfessionInstitutesUniqueAffiliatUniversity();
                
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
                $institutesData = $this->objProfessionInstitutes->getProfessionInstitutesUniqueManagement();
                
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
                $institutesData = $this->objProfessionInstitutes->getProfessionInstitutesUniqueAccreditationBody();
                
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
                                ['label'=>'General', 'value'=>'0'],
                                ['label'=>'Girls Only', 'value'=>'1'],
                            ];
                $allData['dataArray1'] = $GenderArray;

                $response['arrayCount'] = 1;
            }
            elseif($questionType == "Fees"){
                $minimumFeesData = $this->objProfessionInstitutes->getProfessionInstitutesUniqueMinimumFee();
                $maximumFeesData = $this->objProfessionInstitutes->getProfessionInstitutesUniqueMaximumFee();

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

            $professionInstituteData = $this->objProfessionInstitutes->getProfessionInstitutesWithFilter($searchText, $questionType, $answer, $record);
            $data = [];
            if(count($professionInstituteData)>0){
                foreach ($professionInstituteData as $key => $value) {
                    $instituteWebsite = "";
                    $instituteName = "";
                    $instituteEstablishmentYear = "-";
                    $instituteAddress = "-";
                    $institutePhoto = Config::get('constant.TEEN_THUMB_IMAGE_UPLOAD_PATH').'proteen-logo.png';
                    $instituteLatitute = "";
                    $instituteLongitude = "";
                    $instituteAffiliateUniversity = "-";
                    $instituteManagement = "-";
                    $instituteFeeRange = "-";
                    $instituteHostelCount = "-";
                    $instituteGender = "General";
                    $instituteAccreditationScore = "-";
                    $instituteAccreditationBody = "-";
                    $instituteSpeciality = [];

                    if(isset($value->website) && $value->website != ""){
                        $instituteWebsite = 'http://'.$value->website;
                    }
                    
                    if(isset($value->college_institution) && $value->college_institution != ""){
                        $instituteName = $value->college_institution;
                    }
                    
                    if(isset($value->year_of_establishment) && $value->year_of_establishment != ""){
                        $instituteEstablishmentYear = "Establish in ".$value->year_of_establishment;
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
                    if(isset($value->girl_exclusive) && $value->girl_exclusive != ""){
                        if($value->girl_exclusive = 1){
                            $instituteGender = "Girls Only";
                        }
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

                    $instituteData['Website'] = $instituteWebsite;
                    $instituteData['Name'] = ucwords(strtolower($instituteName));
                    $instituteData['EstablishmentYear'] = $instituteEstablishmentYear;
                    $instituteData['Address'] = ucwords(strtolower($instituteAddress));
                    $instituteData['Photo'] = $institutePhoto;
                    $instituteData['MapLatitute'] = $instituteLatitute;
                    $instituteData['MapLongitude'] = $instituteLongitude;
                    $instituteData['AffiliateUniversity'] = $instituteAffiliateUniversity;
                    $instituteData['Management'] = $instituteManagement;
                    $instituteData['FeeRange'] = $instituteFeeRange;
                    $instituteData['HostelCount'] = $instituteHostelCount;
                    $instituteData['Gender'] = $instituteGender;
                    $instituteData['AccreditationScore'] = $instituteAccreditationScore;
                    $instituteData['AccreditationBody'] = $instituteAccreditationBody;
                    $instituteData['Speciality'] = $instituteSpeciality;
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