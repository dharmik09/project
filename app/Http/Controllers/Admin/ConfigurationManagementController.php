<?php


namespace App\Http\Controllers\Admin;

use Excel;
use App\Item;
use Session;
use Auth;
use File;
use Image;
use Input;
use Config;
use Request;
use Helpers;
use Redirect;
use Illuminate\Pagination\Paginator;
use App\Configurations;
use App\Http\Controllers\Controller;
use App\Http\Requests\ConfigurationRequest;
use App\Services\Configurations\Contracts\ConfigurationsRepository;

class ConfigurationManagementController extends Controller {

    public function __construct(ConfigurationsRepository $configurationsRepository) {
        //$this->middleware('auth.admin');
        $this->objConfigurations = new Configurations();
        $this->configurationsRepository = $configurationsRepository;
        $this->controller = 'ConfigurationManagementController';
        $this->loggedInUser = Auth::guard('admin');
    }

    public function index() {
        $configurations = $this->configurationsRepository->getAllConfigurations();
        return view('admin.ListConfiguration', compact('configurations'));
    }

    public function add() {
        $configurationDetail = [];
        return view('admin.EditConfiguration', compact('configurationDetail'));
    }

    public function edit($id) {
        $configurationDetail = $this->objConfigurations->find($id);
        return view('admin.EditConfiguration', compact('configurationDetail'));
    }

    public function save(ConfigurationRequest $configurationRequest) {
        $configurationDetail = [];
        $configurationDetail['id'] = e(Input::get('id'));
        $configurationDetail['cfg_key'] = e(Input::get('cfg_key'));
        $configurationDetail['cfg_value'] = e(Input::get('cfg_value'));
        $configurationDetail = $this->configurationsRepository->saveConfigurationDetail($configurationDetail);
        return Redirect::to("admin/configurations")->with('success', trans('labels.configurationupdatesuccess'));
    }
}
