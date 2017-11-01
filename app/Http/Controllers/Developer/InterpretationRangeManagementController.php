<?php

namespace App\Http\Controllers\Developer;

use Auth;
use Input;
use Config;
use Request;
use Redirect;
use App\Http\Controllers\Controller;
use App\InterpretationRange;

class InterpretationRangeManagementController extends Controller
{
    public function __construct() {
        //$this->middleware('auth.developer');
        $this->objInterpretationRange = new InterpretationRange();
    }

    public function index() {
        $interpretationRangeDetail = $this->objInterpretationRange->getActiveInterpretationRange();;
        return view('developer.ListInterpretationRange' , compact('interpretationRangeDetail'));
    }

    public function add() {
        $interpretationRangeDetail = [];

        return view('developer.EditInterpretationRange', compact('interpretationRangeDetail'));
    }

    public function save() {
        $interpretationRangeDetail = [];
        $interpretationRangeDetail['ir_text'] = (Input::get('ir_text'));
        $interpretationRangeDetail['ir_min_score'] = (Input::get('ir_min_score'));
        $interpretationRangeDetail['ir_max_score'] = (Input::get('ir_max_score'));

        $response = $this->objInterpretationRange->saveInterpretationRangeDetail($interpretationRangeDetail);

        if ($response) {
            return Redirect::to("developer/interpretationRange")->with('success',trans('labels.interpretationrangeupdatesuccess'));
        } else {
            return Redirect::to("developer/interpretationRange")->with('error', trans('labels.commonerrormessage'));
        }
    }

    public function edit() {
        $interpretationRangeDetail = $this->objInterpretationRange->getActiveInterpretationRange();

        return view('developer.EditInterpretationRange', compact('interpretationRangeDetail'));
    }
}

