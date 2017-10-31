<?php

namespace App\Http\Controllers\Developer;

use Auth;
use Input;
use Config;
use Request;
use Redirect;
use App\MultipleIntelligentScale;
use App\MultipleIntelligent;
use App\Http\Controllers\Controller;
use App\Http\Requests\MultipleIntelligenceTypeScaleRequest;

class MultipleIntelligenceTypeScaleManagementController extends Controller
{
    public function __construct() {
        //$this->middleware('auth.developer');
        $this->objMultipleIntelligentscale = new MultipleIntelligentScale();
        $this->objMultipleIntelligent = new MultipleIntelligent();
    }

    public function index()
    {
        $searchParamArray = Input::all();
        $multipleintelligencetypescale = $this->objMultipleIntelligentscale->getAllMultipleIntelligenceTypes($searchParamArray);
        return view('developer.ListMultipleIntelligenceTypeScale' , compact('multipleintelligencetypescale'));
    }

    public function add()
    {
        $miDetail = [];

        return view('developer.EditMultipleIntelligenceTypeScale', compact('miDetail'));
    }

    public function edit()
    {
        $miDetail = $this->objMultipleIntelligentscale->getAllMultipleIntelligenceTypes();
        
        return view('developer.EditMultipleIntelligenceTypeScale', compact('miDetail'));
    }

    public function save(MultipleIntelligenceTypeScaleRequest $MultipleIntelligenceTypeScaleRequest)
    {
        $multipleintelligenceDetail = [];

        $multipleintelligenceDetail['id'] = (Input::get('id'));
        $multipleintelligenceDetail['mts_mi_type_id'] = (Input::get('mit_name'));
        $multipleintelligenceDetail['mts_high_min_score'] = (Input::get('mts_high_min_score'));
        $multipleintelligenceDetail['mts_high_max_score'] = (Input::get('mts_high_max_score'));
        $multipleintelligenceDetail['mts_moderate_min_score'] = (Input::get('mts_moderate_min_score'));
        $multipleintelligenceDetail['mts_moderate_max_score'] = (Input::get('mts_moderate_max_score'));
        $multipleintelligenceDetail['mts_low_min_score'] = (Input::get('mts_low_min_score'));
        $multipleintelligenceDetail['mts_low_max_score'] = (Input::get('mts_low_max_score'));
        
        $response = $this->objMultipleIntelligentscale->saveMultipleIntelligenceScaleDetail($multipleintelligenceDetail);
        if($response)
        {
            return Redirect::to("developer/multipleintelligencetypescale")->with('success',trans('labels.multipleintelligencetypescaleupdatesuccess'));
        }
        else
        {
            return Redirect::to("developer/multipleintelligencetypescale")->with('error', trans('labels.commonerrormessage'));
        }
    }

    public function delete($id)
    {
        $return = $this->objMultipleIntelligentscale->deleteMultipleIntelligenceTypeScale($id);
        if ($return)
        {
           return Redirect::to("developer/multipleintelligencetypescale")->with('success', trans('labels.multipleintelligencedeletesuccess')); 
        }
        else
        {
            return Redirect::to("developer/multipleintelligencetypescale")->with('error', trans('labels.commonerrormessage')); 
        }
    }

}

