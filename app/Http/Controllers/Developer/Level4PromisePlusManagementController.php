<?php

namespace App\Http\Controllers\Developer;

use Auth;
use Input;
use Config;
use Request;
use Redirect;
use App\Http\Controllers\Controller;
use App\PromisePlus;

class Level4PromisePlusManagementController extends Controller
{
    public function __construct() {
        //$this->middleware('auth.developer');
        $this->objPromisePlus = new PromisePlus();
    }

    public function index() {
        $promiseplusDetail = $this->objPromisePlus->getActivePromisePlus();;
        return view('developer.ListLevel4PromisePlus' , compact('promiseplusDetail'));
    }

    public function add() {
        $promiseplusDetail = [];

        return view('developer.EditLevel4PromisePlus', compact('promiseplusDetail'));
    }

    public function save() {
        $promiseplusDetail = [];
        $promiseplusDetail['ps_text'] = (Input::get('ps_text'));
        $promiseplusDetail['ps_description'] = (Input::get('ps_description'));

        $response = $this->objPromisePlus->savePromisePlusDetail($promiseplusDetail);

        if ($response) {
            return Redirect::to("developer/level4PromisePlus")->with('success',trans('labels.promiseplusupdatesuccess'));
        } else {
            return Redirect::to("developer/level4PromisePlus")->with('error', trans('labels.commonerrormessage'));
        }
    }

    public function edit() {
        $promiseplusDetail = $this->objPromisePlus->getActivePromisePlus();

        return view('developer.EditLevel4PromisePlus', compact('promiseplusDetail'));
    }
}

