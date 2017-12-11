<?php

namespace App\Http\Controllers\Admin;

use File;
use Auth;
use Input;
use Config;
use Request;
use Redirect;
use App\Helptext;
use App\Http\Controllers\Controller;
use App\Http\Requests\HelptextRequest;
use Helpers;

class HelpTextManagementController extends Controller
{
    public function __construct() {
        $this->objHelptext = new Helptext;
    }

    public function index() {
        $helptexts = $this->objHelptext->getAllHelptexts();
        return view('admin.ListHelptext', compact('helptexts'));
    }

    public function add() {
        $helptext = [];
        return view('admin.EditHelptext', compact('helptext'));
    }

    public function edit($id) {
        $helptext = $this->objHelptext->find($id);
        return view('admin.EditHelptext', compact('helptext'));
    }

    public function save(HelptextRequest $helptextRequest) {
        $helpTextDetail = [];
        $helpTextDetail['id'] = e(Input::get('id'));
        $helpTextDetail['h_title'] = e(Input::get('h_title'));
        $helpTextDetail['h_slug'] = e(Input::get('h_slug'));
        $helpTextDetail['h_description'] = e(Input::get('h_description'));
        $helpTextDetail['h_page'] = e(Input::get('h_page'));
        $helpTextDetail['deleted'] = e(Input::get('deleted'));
        $response = $this->objHelptext->saveHelptextDetail($helpTextDetail);
        if ($response) {
             return Redirect::to("admin/helpText")->with('success',trans('labels.helptextupdatesuccess'));
        } else {
            return Redirect::to("admin/helpText")->with('error', trans('labels.commonerrormessage'));
        }
    }

    public function delete($id) {
        $return = $this->objHelptext->deleteHelptext($id);
        if ($return){
           return Redirect::to("admin/helpText")->with('success', trans('labels.helptextdeletesuccess'));
        } else {
            return Redirect::to("admin/helpText")->with('error', trans('labels.commonerrormessage'));
        }
    }

}

