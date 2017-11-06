<?php

namespace App\Http\Controllers\Admin;

use File;
use Image;
use Auth;
use Input;
use Config;
use Request;
use Helpers;
use Redirect;
use Validator;
use Illuminate\Pagination\Paginator;
use App\Level1HumanIcon;
use App\Level1HumanIconCategory;
use App\Http\Controllers\Controller;
use App\Http\Requests\Level1HumanIconCategoryRequest;
use App\Services\Level1HumanIcon\Contracts\Level1HumanIconRepository;

class Level1HumanIconCategoryManagementController  extends Controller
{

    public function __construct(Level1HumanIconRepository $Level1HumanIconRepository)
    {
        $this->objLevel1HumanActivity = new Level1HumanIcon();
        $this->objLevel1HumanIconCategory = new Level1HumanIconCategory();
        $this->Level1HumanIconRepository = $Level1HumanIconRepository;
        $this->controller = 'Level1HumanIconCategoryManagementController ';
        $this->loggedInUser = Auth::guard('admin');
    }
    public function index()
    {
        $categorys = $this->Level1HumanIconRepository->getLeve1HumanIconCategory();
        Helpers::createAudit($this->loggedInUser->user()->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_READ'), $this->controller . "@index", $_SERVER['REQUEST_URI'], Config::get('constant.AUDIT_ORIGIN_WEB'), '', '', $_SERVER['REMOTE_ADDR']);
        return view('admin.ListLevel1HumanIconCategory',compact('categorys','searchParamArray'));
    }
    public function add()
    {
        $humanIconCategoryDetail = [];
        Helpers::createAudit($this->loggedInUser->user()->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_READ'), $this->controller . "@add", $_SERVER['REQUEST_URI'], Config::get('constant.AUDIT_ORIGIN_WEB'), '', '', $_SERVER['REMOTE_ADDR']);

        return view('admin.EditLevel1HumanIconCategory', compact('humanIconCategoryDetail'));
    }

    public function edit($id)
    {
        $humanIconCategoryDetail = $this->objLevel1HumanIconCategory->find($id);
        Helpers::createAudit($this->loggedInUser->user()->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_READ'), $this->controller . "@edit", $_SERVER['REQUEST_URI'], Config::get('constant.AUDIT_ORIGIN_WEB'), '', '', $_SERVER['REMOTE_ADDR']);
        return view('admin.EditLevel1HumanIconCategory', compact('humanIconCategoryDetail'));
    }

    public function save(Level1HumanIconCategoryRequest $Level1HumanIconCategoryRequest)
    {
        $humanIconCategoryDetail = [];

        $humanIconCategoryDetail['id'] = e(input::get('id'));
        $humanIconCategoryDetail['hic_name'] = e(input::get('hic_name'));
        $humanIconCategoryDetail['deleted'] = e(input::get('deleted'));

        $response = $this->Level1HumanIconRepository->saveLevel1HumanIconCategoryDetail($humanIconCategoryDetail);
        if($response)
        {
          Helpers::createAudit($this->loggedInUser->user()->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_UPDATE'), Config::get('databaseconstants.TBL_HUMAN_ICON_CATEGORY'), $response, Config::get('constant.AUDIT_ORIGIN_WEB'),  trans('labels.level1humaniconcategoryupdatesuccess'), serialize($humanIconCategoryDetail), $_SERVER['REMOTE_ADDR']);

          return Redirect::to("admin/humanIconsCategory")->with('success', trans('labels.level1humaniconcategoryupdatesuccess'));
        }
        else
        {
          Helpers::createAudit($this->loggedInUser->user()->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_UPDATE'), Config::get('databaseconstants.TBL_HUMAN_ICON_CATEGORY'), $response, Config::get('constant.AUDIT_ORIGIN_WEB'),  trans('labels.somethingwrong'), serialize($humanIconCategoryDetail), $_SERVER['REMOTE_ADDR']);

          return Redirect::to("admin/humanIconsCategory")->with('error', trans('labels.commonerrormessage'));
        }
    }

    public function delete($id)
    {
        $return = $this->Level1HumanIconRepository->deleteLevel1HumanIconCategory($id);
        if ($return)
        {
             Helpers::createAudit($this->loggedInUser->user()->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_DELETE'), Config::get('databaseconstants.TBL_HUMAN_ICON_CATEGORY'), $id, Config::get('constant.AUDIT_ORIGIN_WEB'), trans('labels.level1humaniconcategorydeletesuccess'), '', $_SERVER['REMOTE_ADDR']);

            return Redirect::to("admin/humanIconsCategory")->with('success', trans('labels.level1humaniconcategorydeletesuccess'));
        }
        else
        {
            Helpers::createAudit($this->loggedInUser->user()->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_DELETE'), Config::get('databaseconstants.TBL_HUMAN_ICON_CATEGORY'), $id, Config::get('constant.AUDIT_ORIGIN_WEB'), trans('labels.somethingwrong'), '', $_SERVER['REMOTE_ADDR']);

            return Redirect::to("admin/humanIconsCategory")->with('error', trans('labels.commonerrormessage'));
        }
    }


}
