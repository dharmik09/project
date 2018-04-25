<?php

namespace App\Http\Controllers\Teenager;

use App\Http\Controllers\Controller;
use App\ProfessionInstitutes;
use App\ProfessionInstitutesSpeciality;
use App\State;
use Auth;
use Redirect;
use Request;
use Input;
use App\Professions;
use App\PaidComponent;
use Config;
use App\DeductedCoins;
use Helpers;

class ProfessionInstitutesController extends Controller {

    public function __construct() 
    {
        $this->objProfessionInstitutes = new ProfessionInstitutes();
        $this->objProfessionInstitutesSpeciality = new ProfessionInstitutesSpeciality();
        $this->objState = new State();
        $this->objPaidComponent = new PaidComponent;
        $this->objDeductedCoins = new DeductedCoins; 
    }

    public function index(){
        $speciality = '';
      
        $user = Auth::guard('teenager')->user();
        
        //Institute Finder coins consumption details
        $instituteComponent = $this->objPaidComponent->getPaidComponentsData(Config::get('constant.INSTITUTE_FINDER'));
        $instituteDeductedCoinsDetail = (isset($instituteComponent->id)) ? $this->objDeductedCoins->getDeductedCoinsDetailByIdForLS($user->id, $instituteComponent->id, 1) : [];
        $instituteRemainingDays = 0;
        if (count($instituteDeductedCoinsDetail) > 0) {
            $instituteRemainingDays = Helpers::calculateRemainingDays($instituteDeductedCoinsDetail[0]->dc_end_date);
        }
        if ($instituteRemainingDays && $instituteRemainingDays > 0) {
            if ($user->t_view_information == 1) {
                $countryId = 2; // United States
            } else {
                $countryId = 1; // India
            }
    
            $stateWiseCityData = $this->objState->getAllStatesWithCityByCountryId($countryId);
            
            $state = [];
            $city = [];
            
            foreach ($stateWiseCityData as $key => $value) {
                $state[] = array('value' => $value->s_name);
                foreach ($value->city as $k => $v) {
                    $city[] = array('value' => $v->c_name);
                }
            }

            if (Input::get('speciality')){
                $speciality = Input::get('speciality');
                $institutesSpecialityData = $this->objProfessionInstitutesSpeciality->getAllProfessionInstitutesSpeciality($countryId);
                return view ('teenager.professionInstitutes', compact('speciality','city','state','institutesSpecialityData','countryId'));
            }
            return view('teenager.professionInstitutes', compact('speciality','city','state','countryId'));
        } else {
            return Redirect::to('teenager/home')->with('error', 'Sorry, you have to consume ProCoins to view institute list');
        }
    }

    public function getIndex(){
        $pageNo = Input::get('page_no');
        $answerName = Input::get('answerName');
        $questionType = Input::get('questionType');
        $answer = Input::get('answer');
        $record = $pageNo * 5;

        $user = Auth::guard('teenager')->user();
        $countryId = ($user->t_view_information == 1) ? 2 /* United States */ : 1 /* India */;
        
        $institutesData = $this->objProfessionInstitutes->getProfessionInstitutesWithFilterByCountryId($answerName, $questionType, $answer, $record, $countryId);

        $view = view('teenager.basic.professionInstitutesData',compact('institutesData','countryId'));
        $response['instituteCount'] = count($institutesData);
        $response['institutes'] = $view->render();
        $response['pageNo'] = $pageNo+1;
        return $response;
    }

    public function getInstituteFilter(){
        $user = Auth::guard('teenager')->user();
        $countryId = ($user->t_view_information == 1) ? 2 /* United States */ : 1 /* India */;

    	$questionType = Input::get('question_type');
        if($questionType == "Speciality"){
            $institutesSpecialityData = $this->objProfessionInstitutesSpeciality->getAllProfessionInstitutesSpeciality($countryId);
            $response = '<div class="form-group custom-select">
                            <select id="answerDropdown" onchange="fetchInstituteFilter()" tabindex="8" class="form-control">
                            <option disabled selected>Select Education Stream</option>';
            foreach ($institutesSpecialityData as $key => $value) {
                $response .= '<option value="'.$value->pis_name.'">'.$value->pis_name.'</option>';
            }
            $response .= '</select></div>';
        }
        elseif($questionType == "Institute_Affiliation"){
            $institutesData = $this->objProfessionInstitutes->getProfessionInstitutesUniqueAffiliatUniversityByCountryId($countryId);
            $response = '<div class="form-group custom-select">
                            <select id="answerDropdown" onchange="fetchInstituteFilter()" tabindex="8" class="form-control">
                            <option disabled selected>Select Affiliation By</option>';
            foreach ($institutesData as $key => $value) {
                $response .= '<option value="'.$value->affiliat_university.'">'.$value->affiliat_university.'</option>';
            }
            $response .= '</select></div>';
        }
        elseif($questionType == "Management_Category"){
            $institutesData = $this->objProfessionInstitutes->getProfessionInstitutesUniqueManagementByCountryId($countryId);
            $response = '<div class="form-group custom-select">
                            <select id="answerDropdown" onchange="fetchInstituteFilter()" tabindex="8" class="form-control">
                            <option disabled selected>Select Category</option>';
            foreach ($institutesData as $key => $value) {
                $response .= '<option value="'.$value->management.'">'.$value->management.'</option>';
            }
            $response .= '</select></div>';
        }
        elseif($questionType == "Accreditation"){
            $institutesData = $this->objProfessionInstitutes->getProfessionInstitutesUniqueAccreditationBodyByCountryId($countryId);
            $response = '<div class="form-group custom-select">
                            <select id="answerDropdown" onchange="fetchInstituteFilter()" tabindex="8" class="form-control">
                            <option disabled selected>Select Accreditation By</option>';
            foreach ($institutesData as $key => $value) {
                if($value != ''){
                    $response .= '<option value="'.$value->accreditation_body.'">'.$value->accreditation_body.'</option>';
                }
            }
            $response .= '</select></div>';
        }
        elseif($questionType == "Hostel"){
            $response = '<div class="form-group custom-select">
                            <select id="answerDropdown" onchange="fetchInstituteFilter()" tabindex="8" class="form-control">
                                <option disabled selected>Select Availability</option>
                                <option value="0">Not Available</option>
                                <option value="1">Available</option>
                            </select>
                        </div>';
        }
        elseif($questionType == "Gender"){
            $response = '<div class="form-group custom-select">
                            <select id="answerDropdown" onchange="fetchInstituteFilter()" tabindex="8" class="form-control">
                                <option disabled selected>Select Status</option>
                                <option value="0">Co-Ed</option>
                                <option value="1">Girls Only</option>
                            </select>
                        </div>';
        }
        elseif($questionType == "Autonomous"){
            $response = '<div class="form-group custom-select">
                            <select id="answerDropdown" onchange="fetchInstituteFilter()" tabindex="8" class="form-control">
                                <option disabled selected>Select Type</option>
                                <option value="0">UnAutonomous</option>
                                <option value="1">Autonomous</option>
                            </select>
                        </div>';
        }
        elseif($questionType == "Fees"){
            $minimumFeesData = $this->objProfessionInstitutes->getProfessionInstitutesUniqueMinimumFeeByCountryId($countryId);
            $maximumFeesData = $this->objProfessionInstitutes->getProfessionInstitutesUniqueMaximumFeeByCountryId($countryId);
            $response = '<div class="col-sm-6"><div class="form-group custom-select">
                            <select id="answerDropdownMinimumFees" onchange="fetchInstituteFilter()" tabindex="8" class="form-control">
                            <option value="##" disabled selected>Min Fee</option>';
            foreach ($minimumFeesData as $key => $value) {
                $response .= '<option value="'.$value->minimum_fee.'">'.number_format((int)$value->minimum_fee, 0, '.', ',').'</option>';
            }
            $response .= '</select></div></div>';
            $response .= '<div class="col-sm-6"><div class="form-group custom-select">
                            <select id="answerDropdownMaximumFees" onchange="fetchInstituteFilter()" tabindex="8" class="form-control">
                            <option value="##" disabled selected>Max Fee</option>';
            foreach ($maximumFeesData as $key => $value) {
                $response .= '<option value="'.$value->maximum_fee.'">'.number_format((int)$value->maximum_fee, 0, '.', ',').'</option>';
            }
            $response .= '</select></div></div>';
        }
        return $response;
    }
}