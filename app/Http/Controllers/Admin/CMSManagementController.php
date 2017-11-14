<?php

namespace App\Http\Controllers\Admin;

use Auth;
use Input;
use Config;
use Helpers;
use Redirect;
use App\CMS;
use Illuminate\Pagination\Paginator;
use App\Http\Controllers\Controller;
use App\Http\Requests\CMSRequest;
use App\Services\CMS\Contracts\CMSRepository;

class CMSManagementController extends Controller
{
    public function __construct(CMSRepository $cmsRepository)
    {
        //$this->middleware('auth.admin');
        $this->objCMS                = new CMS();
        $this->cmsRepository         = $cmsRepository;
        $this->controller = 'CMSManagementController';
        $this->loggedInUser = Auth::guard('admin');
    }

    public function index()
    {
        $cms = $this->cmsRepository->getAllCMS();
        Helpers::createAudit($this->loggedInUser->user()->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_READ'), $this->controller . "@index", $_SERVER['REQUEST_URI'], Config::get('constant.AUDIT_ORIGIN_WEB'), '', '', $_SERVER['REMOTE_ADDR']);
        return view('admin.ListCMS', compact('cms'));
    }

    public function add()
    {
        $cmsDetail =[];
        Helpers::createAudit($this->loggedInUser->user()->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_READ'), $this->controller . "@add", $_SERVER['REQUEST_URI'], Config::get('constant.AUDIT_ORIGIN_WEB'), '', '', $_SERVER['REMOTE_ADDR']);

        return view('admin.EditCMS', compact('cmsDetail'));
    }

    public function edit($id)
    {
        $cmsDetail = $this->objCMS->find($id);
        Helpers::createAudit($this->loggedInUser->user()->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_READ'), $this->controller . "@edit", $_SERVER['REQUEST_URI'], Config::get('constant.AUDIT_ORIGIN_WEB'), '', '', $_SERVER['REMOTE_ADDR']);

        return view('admin.EditCMS', compact('cmsDetail'));
    }

    public function save(CMSRequest $cmsRequest)
    {
        $cmsDetail = [];

        $cmsDetail['id']  = e(input::get('id'));
        $cmsDetail['cms_subject']   = e(input::get('cms_subject'));
        $cmsDetail['cms_slug']   = e(input::get('cms_slug'));
        $cmsDetail['cms_body']  = input::get('cms_body');
        $cmsDetail['deleted']  = e(input::get('deleted'));

        $response = $this->cmsRepository->saveCMSDetail($cmsDetail);
        if($response)
        {
            Helpers::createAudit($this->loggedInUser->user()->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_UPDATE'), Config::get('databaseconstants.TBL_CMS'), $response, Config::get('constant.AUDIT_ORIGIN_WEB'), trans('labels.cmsupdatesuccess'),serialize($cmsDetail), $_SERVER['REMOTE_ADDR']);

            return Redirect::to("admin/cms")->with('success',trans('labels.cmsupdatesuccess'));
        }
        else
        {
            Helpers::createAudit($this->loggedInUser->user()->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_UPDATE'),Config::get('databaseconstants.TBL_CMS'), $response, Config::get('constant.AUDIT_ORIGIN_WEB'),trans('labels.somethingwrong'), serialize($cmsDetail), $_SERVER['REMOTE_ADDR']);
            return Redirect::to("admin/cms")->with('error', trans('labels.commonerrormessage'));
        }
    }
    public function delete($id)
    {
        $return = $this->cmsRepository->deleteCMS($id);
        if($return)
        {
             Helpers::createAudit($this->loggedInUser->user()->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_DELETE'), Config::get('databaseconstants.TBL_CMS'), $id, Config::get('constant.AUDIT_ORIGIN_WEB'), trans('labels.cmsdeletesuccess'), '', $_SERVER['REMOTE_ADDR']);

            return Redirect::to("admin/cms")->with('success', trans('labels.cmsdeletesuccess'));
        }
        else
        {
            Helpers::createAudit($this->loggedInUser->user()->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_DELETE'), Config::get('databaseconstants.TBL_CMS'), $id, Config::get('constant.AUDIT_ORIGIN_WEB'),trans('labels.somethingwrong'), '', $_SERVER['REMOTE_ADDR']);

            return Redirect::to("admin/cms")->with('error', trans('labels.commonerrormessage'));
        }
    }

}