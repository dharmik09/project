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
use App\Level1CartoonIcon;
use App\Level1CartoonIconCategory;
use App\Http\Controllers\Controller;
use App\Http\Requests\Level1CartoonIconCategoryRequest;
use App\Services\Level1CartoonIcon\Contracts\Level1CartoonIconRepository;

class Level1CartoonIconCategoryManagementController  extends Controller
{

    public function __construct(Level1CartoonIconRepository $level1CartoonIconRepository)
    {
        $this->objLevel1CartoonActivity = new Level1CartoonIcon();
        $this->objLevel1CartoonIconCategory = new Level1CartoonIconCategory();
        $this->level1CartoonIconRepository = $level1CartoonIconRepository;
        $this->controller = 'Level1CartoonIconCategoryManagementController ';
        $this->loggedInUser = Auth::guard('admin');
    }
    public function index()
    {
        $searchParamArray = Input::all();
        if (isset($searchParamArray['clearSearch'])) {
            unset($searchParamArray);
            $searchParamArray = array();
        }
        $categorys = $this->level1CartoonIconRepository->getLeve1CartoonIconCategory($searchParamArray);
        Helpers::createAudit($this->loggedInUser->user()->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_READ'), $this->controller . "@index", $_SERVER['REQUEST_URI'], Config::get('constant.AUDIT_ORIGIN_WEB'), '', '', $_SERVER['REMOTE_ADDR']);
        return view('admin.ListLevel1CartoonIconCategory',compact('categorys','searchParamArray'));
    }
    public function add()
    {
        $cartoonIconCategoryDetail = [];
        Helpers::createAudit($this->loggedInUser->user()->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_READ'), $this->controller . "@add", $_SERVER['REQUEST_URI'], Config::get('constant.AUDIT_ORIGIN_WEB'), '', '', $_SERVER['REMOTE_ADDR']);

        return view('admin.EditLevel1CartoonIconCategory', compact('cartoonIconCategoryDetail'));
    }

    public function edit($id)
    {
        $cartoonIconCategoryDetail = $this->objLevel1CartoonIconCategory->find($id);
        Helpers::createAudit($this->loggedInUser->user()->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_READ'), $this->controller . "@edit", $_SERVER['REQUEST_URI'], Config::get('constant.AUDIT_ORIGIN_WEB'), '', '', $_SERVER['REMOTE_ADDR']);
        return view('admin.EditLevel1CartoonIconCategory', compact('cartoonIconCategoryDetail'));
    }

    public function save(Level1CartoonIconCategoryRequest $Level1CartoonIconCategoryRequest)
    {
        $cartoonIconCategoryDetail = [];

        $cartoonIconCategoryDetail['id'] = e(input::get('id'));
        $cartoonIconCategoryDetail['cic_name'] = e(input::get('cic_name'));
        $cartoonIconCategoryDetail['deleted'] = e(input::get('deleted'));

        $response = $this->level1CartoonIconRepository->saveLevel1CartoonIconCategoryDetail($cartoonIconCategoryDetail);
        if($response)
        {
          Helpers::createAudit($this->loggedInUser->user()->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_UPDATE'), Config::get('databaseconstants.TBL_CARTOON_ICON_CATEGORY'), $response, Config::get('constant.AUDIT_ORIGIN_WEB'),  trans('labels.level1cartooniconcategoryupdatesuccess'), serialize($cartoonIconCategoryDetail), $_SERVER['REMOTE_ADDR']);

          return Redirect::to("admin/cartoonIconsCategory")->with('success', trans('labels.level1cartooniconcategoryupdatesuccess'));
        }
        else
        {
          Helpers::createAudit($this->loggedInUser->user()->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_UPDATE'), Config::get('databaseconstants.TBL_CARTOON_ICON_CATEGORY'), $response, Config::get('constant.AUDIT_ORIGIN_WEB'),  trans('labels.somethingwrong'), serialize($cartoonIconCategoryDetail), $_SERVER['REMOTE_ADDR']);

          return Redirect::to("admin/cartoonIconsCategory")->with('error', trans('labels.commonerrormessage'));
        }
    }

    public function delete($id)
    {
        $return = $this->level1CartoonIconRepository->deleteLevel1CartoonIconCategory($id);
        if ($return)
        {
             Helpers::createAudit($this->loggedInUser->user()->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_DELETE'), Config::get('databaseconstants.TBL_CARTOON_ICON_CATEGORY'), $id, Config::get('constant.AUDIT_ORIGIN_WEB'), trans('labels.level1cartooniconcategorydeletesuccess'), '', $_SERVER['REMOTE_ADDR']);

            return Redirect::to("admin/cartoonIconsCategory")->with('success', trans('labels.level1cartooniconcategorydeletesuccess'));
        }
        else
        {
            Helpers::createAudit($this->loggedInUser->user()->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_DELETE'), Config::get('databaseconstants.TBL_CARTOON_ICON_CATEGORY'), $id, Config::get('constant.AUDIT_ORIGIN_WEB'), trans('labels.somethingwrong'), '', $_SERVER['REMOTE_ADDR']);

            return Redirect::to("admin/cartoonIconsCategory")->with('error', trans('labels.commonerrormessage'));
        }
    }


}
