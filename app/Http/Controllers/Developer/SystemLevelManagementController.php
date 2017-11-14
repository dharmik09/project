<?php

namespace App\Http\Controllers\Developer;

use Auth;
use Input;
use Config;
use Request;
use Redirect;
use App\SystemLevels;
use App\Http\Controllers\Controller;
use App\Http\Requests\SystemLevelRequest;

class SystemLevelManagementController extends Controller
{
    public function __construct() {
        //$this->middleware('auth.developer');
        $this->objSystemLevels = new SystemLevels();
    }

    public function index()
    {
        $searchParamArray = Input::all();
        $systemlevels = $this->objSystemLevels->getAllSystemLevels($searchParamArray);
        return view('developer.ListSystemLevel' , compact('systemlevels'));
    }

    public function add()
    {
        $systemlevelDetail = [];

        return view('developer.EditSystemLevel' , compact('systemlevelDetail'));
    }

    public function edit($id)
    {
        $systemlevelDetail = $this->objSystemLevels->find($id);;

        return view('developer.EditSystemLevel' , compact('systemlevelDetail'));
    }

    public function save(SystemLevelRequest $systemLevelRequest)
    {
        $systemlevelDetail = [];

        $systemlevelDetail['id'] = e(Input::get('id'));
        $systemlevelDetail['sl_name'] = e(Input::get('sl_name'));
        $systemlevelDetail['sl_info'] = Input::get('sl_info');
        $systemlevelDetail['sl_boosters'] = e(input::get('sl_boosters'));
        $systemlevelDetail['deleted'] = e(Input::get('deleted'));

        $response = $this->objSystemLevels->saveSystemLevelDetail($systemlevelDetail);
        if($response)
        {
             return Redirect::to("developer/systemLevel")->with('success',trans('labels.systemlevelupdatesuccess'));
        }
        else
        {
            return Redirect::to("developer/systemLevel")->with('error', trans('labels.commonerrormessage'));
        }
    }

    public function delete($id)
    {
        $return = $this->objSystemLevels->deleteSystemLevel($id);
        if ($return)
        {
           return Redirect::to("developer/systemLevel")->with('success', trans('labels.systemleveldeletesuccess'));
        }
        else
        {
            return Redirect::to("developer/systemLevel")->with('error', trans('labels.commonerrormessage'));
        }
    }

}

