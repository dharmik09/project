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

class ProfessionInstitutesController extends Controller {

    public function __construct() 
    {
        $this->objProfessionInstitutes = new ProfessionInstitutes();
        $this->objProfessionInstitutesSpeciality = new ProfessionInstitutesSpeciality();
        $this->objState = new State();
    }

    public function index(){
        $speciality = '';
      
        $user = Auth::guard('teenager')->user();
        
        if($user->t_view_information == 1){
            $countryId = 2; // United States
        }else{
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

        if(Input::get('speciality')){
            $speciality = Input::get('speciality');
            $institutesSpecialityData = $this->objProfessionInstitutesSpeciality->getAllProfessionInstitutesSpeciality();
            return view('teenager.professionInstitutes', compact('speciality','city','state','institutesSpecialityData'));
        }
        return view('teenager.professionInstitutes', compact('speciality','city','state'));
    }

    public function getIndex(){
        $pageNo = Input::get('page_no');
        $answerName = Input::get('answerName');
        $questionType = Input::get('questionType');
        $answer = Input::get('answer');
        $record = $pageNo * 5;

        $institutesData = $this->objProfessionInstitutes->getProfessionInstitutesWithFilter($answerName, $questionType, $answer, $record);

        $view = view('teenager.basic.professionInstitutesData',compact('institutesData'));
        $response['instituteCount'] = count($institutesData);
        $response['institutes'] = $view->render();
        $response['pageNo'] = $pageNo+1;
        return $response;
    }

    public function getInstituteFilter(){
    	$questionType = Input::get('question_type');
        if($questionType == "Speciality"){
            $institutesSpecialityData = $this->objProfessionInstitutesSpeciality->getAllProfessionInstitutesSpeciality();
            $response = '<div class="form-group custom-select">
                            <select id="answerDropdown" onchange="fetchInstituteFilter()" tabindex="8" class="form-control">
                            <option disabled selected>Select Education Stream</option>';
            foreach ($institutesSpecialityData as $key => $value) {
                $response .= '<option value="'.$value->pis_name.'">'.$value->pis_name.'</option>';
            }
            $response .= '</select></div>';
        }
        elseif($questionType == "Institute_Affiliation"){
            $institutesData = $this->objProfessionInstitutes->getProfessionInstitutesUniqueAffiliatUniversity();
            $response = '<div class="form-group custom-select">
                            <select id="answerDropdown" onchange="fetchInstituteFilter()" tabindex="8" class="form-control">
                            <option disabled selected>Select Affiliation</option>';
            foreach ($institutesData as $key => $value) {
                $response .= '<option value="'.$value->affiliat_university.'">'.$value->affiliat_university.'</option>';
            }
            $response .= '</select></div>';
        }
        elseif($questionType == "Management_Category"){
            $institutesData = $this->objProfessionInstitutes->getProfessionInstitutesUniqueManagement();
            $response = '<div class="form-group custom-select">
                            <select id="answerDropdown" onchange="fetchInstituteFilter()" tabindex="8" class="form-control">
                            <option disabled selected>Select Category</option>';
            foreach ($institutesData as $key => $value) {
                $response .= '<option value="'.$value->management.'">'.$value->management.'</option>';
            }
            $response .= '</select></div>';
        }
        elseif($questionType == "Accreditation"){
            $institutesData = $this->objProfessionInstitutes->getProfessionInstitutesUniqueAccreditationBody();
            $response = '<div class="form-group custom-select">
                            <select id="answerDropdown" onchange="fetchInstituteFilter()" tabindex="8" class="form-control">
                            <option disabled selected>Select Accreditation</option>';
            foreach ($institutesData as $key => $value) {
                $response .= '<option value="'.$value->accreditation_body.'">'.$value->accreditation_body.'</option>';
            }
            $response .= '</select></div>';
        }
        elseif($questionType == "Hostel"){
            $response = '<div class="form-group custom-select">
                            <select id="answerDropdown" onchange="fetchInstituteFilter()" tabindex="8" class="form-control">
                                <option disabled selected>Select Type</option>
                                <option value="0">Not Available</option>
                                <option value="1">Available</option>
                            </select>
                        </div>';
        }
        elseif($questionType == "Gender"){
            $response = '<div class="form-group custom-select">
                            <select id="answerDropdown" onchange="fetchInstituteFilter()" tabindex="8" class="form-control">
                                <option disabled selected>Select Type</option>
                                <option value="0">General</option>
                                <option value="1">Girls Only</option>
                            </select>
                        </div>';
        }
        elseif($questionType == "Fees"){
            $minimumFeesData = $this->objProfessionInstitutes->getProfessionInstitutesUniqueMinimumFee();
            $maximumFeesData = $this->objProfessionInstitutes->getProfessionInstitutesUniqueMaximumFee();
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