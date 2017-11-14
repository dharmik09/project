<?php

namespace App\Http\Controllers\Developer;

use Auth;
use Input;
use Config;
use Request;
use Redirect;
use App\PersonalityScale; 
use App\Personality;
use App\Http\Controllers\Controller;
use App\Http\Requests\PersonalityTypeScaleRequest;

class PersonalityTypeScaleManagementController extends Controller
{
    public function __construct() {
        //$this->middleware('auth.developer');
        $this->objPersonalityscale = new PersonalityScale();
        $this->objPersonality = new Personality();
    }

    public function index()
    {
        $personalitytypescales = $this->objPersonalityscale->getAllPersonalityTypes();
        return view('developer.ListPersonalityTypeScale', compact('personalitytypescales'));
    }

    public function add()
    {
        $personalityDetail = [];

        return view('developer.EditPersonalityTypeScale', compact('personalityDetail'));
    }

    public function edit()
    {
        $personalityDetail = $this->objPersonalityscale->getAllPersonalityTypes();
        
        return view('developer.EditPersonalityTypeScale', compact('personalityDetail'));
    }

    public function save(PersonalityTypeScaleRequest $personalityTypeScaleRequest)
    {
        $personalityDetail = [];

        $personalityDetail['id'] = (Input::get('id'));
        $personalityDetail['pts_personality_type_id'] = (Input::get('pts_name'));
        $personalityDetail['pts_high_min_score'] = (Input::get('pts_high_min_score'));
        $personalityDetail['pts_high_max_score'] = (Input::get('pts_high_max_score'));
        $personalityDetail['pts_moderate_min_score'] = (Input::get('pts_moderate_min_score'));
        $personalityDetail['pts_moderate_max_score'] = (Input::get('pts_moderate_max_score'));
        $personalityDetail['pts_low_min_score'] = (Input::get('pts_low_min_score'));
        $personalityDetail['pts_low_max_score'] = (Input::get('pts_low_max_score'));
        
        $response = $this->objPersonalityscale->savePersonalityScaleDetail($personalityDetail);
        if($response)
        {
            return Redirect::to("developer/personalityTypeScale")->with('success',trans('labels.personalitytypescaleupdatesuccess'));
        }
        else
        {
            return Redirect::to("developer/personalityTypeScale")->with('error', trans('labels.commonerrormessage'));
        }
    }

    public function delete($id)
    {
        $return = $this->objPersonalityscale->deletePersonalityTypeScale($id);
        if ($return)
        {
           return Redirect::to("developer/personalityTypeScale")->with('success', trans('labels.personalitydeletesuccess')); 
        }
        else
        {
            return Redirect::to("developer/personalityTypeScale")->with('error', trans('labels.commonerrormessage')); 
        }
    }

}

