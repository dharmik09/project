<?php

namespace App\Http\Controllers\Developer;

use Auth;
use Input;
use Config;
use Request;
use Redirect;
use App\ApptitudeTypeScale;
use App\Http\Controllers\Controller;

class ApptitudeTypeScaleManagementController extends Controller
{
    public function __construct() {
        //$this->middleware('auth.developer');
        $this->objApptitudeScale = new ApptitudeTypeScale();
    }

    public function index()
    {
       $searchParamArray = Input::all();
        $apptitudetypescales = $this->objApptitudeScale->getAllApptitudeTypesScale($searchParamArray);
        return view('developer.ListApptitudeTypesScale' , compact('apptitudetypescales'));
    }

    public function add()
    {
        $apptitudeScaleDetail = [];

        return view('developer.EditApptitudeTypeScale', compact('apptitudeScaleDetail'));
    }

    public function edit()
    {
        $apptitudeScaleDetail =  $this->objApptitudeScale->getAllApptitudeTypesScale();;

        return view('developer.EditApptitudeTypeScale', compact('apptitudeScaleDetail'));
    }

    public function save()
    {
        $apptitudeScaleDetail = [];
        $apptitudeScaleDetail['id'] = Input::get('id');
        $apptitudeScaleDetail['ats_apptitude_type_id'] = Input::get('ats_apptitude_type_id');
        $apptitudeScaleDetail['ats_high_min_score'] = Input::get('ats_high_min_score');
        $apptitudeScaleDetail['ats_high_max_score'] = Input::get('ats_high_max_score');
        $apptitudeScaleDetail['ats_moderate_min_score'] = Input::get('ats_moderate_min_score');
        $apptitudeScaleDetail['ats_moderate_max_score'] = Input::get('ats_moderate_max_score');
        $apptitudeScaleDetail['ats_low_min_score'] = Input::get('ats_low_min_score');
        $apptitudeScaleDetail['ats_low_max_score'] = Input::get('ats_low_max_score');

        $response = $this->objApptitudeScale->saveApptitudeTypeScaleDetail($apptitudeScaleDetail);
        if($response)
        {
             return Redirect::to("developer/apptitudetypescale")->with('success',trans('labels.apptitudetypescaleupdatesuccess'));
        }
        else
        {
            return Redirect::to("developer/apptitudetypescale")->with('error', trans('labels.commonerrormessage'));
        }
    }

    /*public function delete($id)
    {
        $return = $this->objApptitudeScale->deleteApptitudeTypeScale($id);
        if ($return)
        {
           return Redirect::to("developer/apptitudetypescale")->with('success', trans('labels.apptitudetypescaledeletesuccess'));
        }
        else
        {
            return Redirect::to("developer/apptitudetypescale")->with('error', trans('labels.commonerrormessage'));
        }
    }*/

}

