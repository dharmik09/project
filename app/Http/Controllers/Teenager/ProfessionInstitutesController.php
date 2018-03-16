<?php

namespace App\Http\Controllers\Teenager;

use App\Http\Controllers\Controller;
use App\ProfessionInstitutes;
use Auth;
use Redirect;
use Request;
use Input;

class ProfessionInstitutesController extends Controller {

    public function __construct() 
    {
        $this->objProfessionInstitutes = new ProfessionInstitutes();
    }

    public function index(){
        return view('teenager.professionInstitutes');
    }

    public function getIndex(){
        $pageNo = Input::get('page_no');
        $questionType = Input::get('questionType');
        $answer = Input::get('answer');
        $record = $pageNo * 5;

        $institutesData = $this->objProfessionInstitutes->skip($record)->take(5);

        if($questionType == "Institute_Affiliation"){
            $institutesData = $institutesData->where('affiliat_university',$answer)->get();
        }
        elseif($questionType == "State"){
            $institutesData = $institutesData->where('institute_state','like', '%'.$answer.'%')->get();
        }
        elseif($questionType == "City"){
            $institutesData = $institutesData->where('city','like', '%'.$answer.'%')->get();
        }
        elseif($questionType == "Pincode"){
            $institutesData = $institutesData->where('pin_code','like', '%'.$answer.'%')->get();
        }
        elseif($questionType == "Management_Category"){
            $institutesData = $institutesData->where('management',$answer)->get();
        }
        elseif($questionType == "Accreditation"){
            $institutesData = $institutesData->where('accreditation_body',$answer)->get();
        }
        elseif($questionType == "Hostel"){
            if($answer == "0"){
                $institutesData = $institutesData->where('hostel_count',"0")->get();
            }
            else{
                $institutesData = $institutesData->where('hostel_count',"<>","0")->get();
            }
        }
        elseif($questionType == "Gender"){
            if($answer == "0"){
                $institutesData = $institutesData->where('girl_exclusive',$answer)->get();
            }
            else{
                $institutesData = $institutesData->where('girl_exclusive',$answer)->get();
            }
        }
        elseif($questionType == "Fees"){
            $institutesData = $institutesData->where('minimum_fee',$answer['minimumFees'])->where('maximum_fee',$answer['maximumFees'])->get();
        }
        else{
            $institutesData = $institutesData->get();
        }

        $view = view('teenager.basic.professionInstitutesData',compact('institutesData'));
        $response['instituteCount'] = count($institutesData);
        $response['institutes'] = $view->render();
        $response['pageNo'] = $pageNo+1;
        return $response;
    }

    public function getInstituteFilter(){
    	$questionType = Input::get('question_type');
        if($questionType == "Institute_Affiliation"){
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
                            <select id="answerDropdownMinimumFees" onchange="fetchInstituteFilter()" tabindex="8" class="form-control">';
            foreach ($minimumFeesData as $key => $value) {
                $response .= '<option value="'.$value->minimum_fee.'">'.$value->minimum_fee.'</option>';
            }
            $response .= '</select></div></div>';
            $response .= '<div class="col-sm-6"><div class="form-group custom-select">
                            <select id="answerDropdownMaximumFees" onchange="fetchInstituteFilter()" tabindex="8" class="form-control">';
            foreach ($maximumFeesData as $key => $value) {
                $response .= '<option value="'.$value->maximum_fee.'">'.$value->maximum_fee.'</option>';
            }
            $response .= '</select></div></div>';
        }
        return $response;
    }
}