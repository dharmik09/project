<?php

namespace App\Http\Controllers\Developer;

use Auth;
use Input;
use Config;
use Request;
use Redirect;
use App\Level1Qualities;
use App\Http\Controllers\Controller;
use App\Http\Requests\Level1QualitiesRequest;

class Level1QualityManagementController  extends Controller
{
    public function __construct()
    {
        //$this->middleware('auth.developer');
        $this->objLevel1Quality = new Level1Qualities();
    }

    public function index()
    {
        $level1qualities = $this->objLevel1Quality->getAllLevel1Qualities();
        return view('developer.ListLevel1Qualities' , compact('level1qualities'));
    }

    public function add()
    {
        $qualityDetail = [];

        return view('developer.EditLevel1Quality', compact('qualityDetail'));
    }

    public function edit($id)
    {
        $qualityDetail = $this->objLevel1Quality->find($id);

        return view('developer.EditLevel1Quality', compact('qualityDetail'));
    }

    public function save(Level1QualitiesRequest $level1QualitiesRequest)
    {
        $qualityDetail = [];

        $qualityDetail['id'] = e(Input::get('id'));
        $qualityDetail['l1qa_name'] = e(Input::get('l1qa_name'));
        $qualityDetail['deleted'] = e(Input::get('deleted'));

        $response = $this->objLevel1Quality->saveLevel1QualityDetail($qualityDetail);
        if($response)
        {
             return Redirect::to("developer/level1Qualities")->with('success',trans('labels.qualityupdatesuccess'));
        }
        else
        {
            return Redirect::to("developer/level1Qualities")->with('error', trans('labels.commonerrormessage'));
        }
    }

    public function delete($id)
    {
        $return = $this->objLevel1Quality->deleteLevel1QualityType($id);
        if ($return)
        {
           return Redirect::to("developer/level1Qualities")->with('success', trans('labels.qualitydeletesuccess'));
        }
        else
        {
            return Redirect::to("developer/level1Qualities")->with('error', trans('labels.commonerrormessage'));
        }
    }

}

