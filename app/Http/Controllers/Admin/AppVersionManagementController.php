<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\AppVersionRequest;
use App\Http\Controllers\Controller;
use App\VersionsList;
use Auth;
use Input;
use Config;
use Request;
use Redirect;

class AppVersionManagementController extends Controller
{
    public function __construct() {
        $this->objAppVersionsList = new VersionsList;
    }

    public function index() {
        $data = $this->objAppVersionsList->getAllVersionsList();
        return view('admin.ListAppVersion', compact('data'));
    }

    public function add() {
        $data = [];
        return view('admin.EditAppVersion', compact('data'));
    }

    public function edit($id) {
        $data = $this->objAppVersionsList->find($id);
        return view('admin.EditAppVersion', compact('data'));
    }

    public function save(AppVersionRequest $AppVersionRequest) {
        $data = [];
        
        $data['id'] = e(Input::get('id'));
        $data['force_update'] = e(Input::get('force_update'));
        $data['device_type'] = e(Input::get('device_type'));
        $data['message'] = e(Input::get('message'));
        $data['app_version'] = e(Input::get('app_version'));

        $response = $this->objAppVersionsList->saveVersionsListDetail($data);
        if ($response) {
             return Redirect::to("admin/appVersions")->with('success',trans('labels.professiontagupdatesuccess'));
        } else {
            return Redirect::to("admin/appVersions")->with('error', trans('labels.commonerrormessage'));
        }
    }

}

