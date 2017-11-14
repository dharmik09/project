<?php

namespace App\Http\Controllers\Admin;

use Auth;
use Input;
use Config;
use Helpers;
use Redirect;
use App\Templates;
use Illuminate\Pagination\Paginator;
use App\Http\Controllers\Controller;
use App\Http\Requests\TemplateRequest;
use App\Services\Template\Contracts\TemplatesRepository;

class TemplateManagementController extends Controller
{
    public function __construct(TemplatesRepository $templatesRepository)
    {
        //$this->middleware('auth.admin');
        $this->objTemplate                = new Templates();
        $this->templatesRepository         = $templatesRepository;
        $this->controller = 'TemplateManagementController';
        $this->loggedInUser = Auth::guard('admin');
    }

    public function index()
    {
        $template = $this->templatesRepository->getAllTemplates();
        Helpers::createAudit($this->loggedInUser->user()->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_READ'), $this->controller . "@index", $_SERVER['REQUEST_URI'], Config::get('constant.AUDIT_ORIGIN_WEB'), '', '', $_SERVER['REMOTE_ADDR']);
        return view('admin.ListEmailTemplate',compact('template'));
    }

    public function add()
    {
        $templateDetail =[];
        Helpers::createAudit($this->loggedInUser->user()->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_READ'), $this->controller . "@add", $_SERVER['REQUEST_URI'], Config::get('constant.AUDIT_ORIGIN_WEB'), '', '', $_SERVER['REMOTE_ADDR']);

        return view('admin.EditTemplate', compact('templateDetail'));
    }

    public function edit($id)
    {
        $templateDetail = $this->objTemplate->find($id);
        Helpers::createAudit($this->loggedInUser->user()->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_READ'), $this->controller . "@edit", $_SERVER['REQUEST_URI'], Config::get('constant.AUDIT_ORIGIN_WEB'), '', '', $_SERVER['REMOTE_ADDR']);

        return view('admin.EditTemplate', compact('templateDetail'));
    }

    public function save(TemplateRequest $templateRequest)
    {
        $templateDetail = [];

        $templateDetail['id']  = e(input::get('id'));
        $templateDetail['et_templatename']   = e(input::get('et_templatename'));
        $templateDetail['et_templatepseudoname']   = e(input::get('et_templatepseudoname'));
        $templateDetail['et_subject']  = input::get('et_subject');
        $templateDetail['et_body']  = input::get('et_body');
        $templateDetail['deleted']  = e(input::get('deleted'));

        $response = $this->templatesRepository->saveTemplateDetail($templateDetail);
        if($response)
        {
            Helpers::createAudit($this->loggedInUser->user()->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_UPDATE'), Config::get('databaseconstants.TBL_TEMPLATE'), $response, Config::get('constant.AUDIT_ORIGIN_WEB'), trans('labels.tempalteupdatesuccess'),serialize($templateDetail), $_SERVER['REMOTE_ADDR']);

            return Redirect::to("admin/templates")->with('success',trans('labels.templateupdatesuccess'));
        }
        else
        {
            Helpers::createAudit($this->loggedInUser->user()->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_UPDATE'), Config::get('databaseconstants.TBL_TEMPLATE'), $response, Config::get('constant.AUDIT_ORIGIN_WEB'),trans('labels.somethingwrong'),serialize($templateDetail), $_SERVER['REMOTE_ADDR']);
            return Redirect::to("admin/templates")->with('error', trans('labels.commonerrormessage'));
        }
    }

    public function delete($id)
    {
        $return = $this->templatesRepository->deleteTemplate($id);
        if($return)
        {
             Helpers::createAudit($this->loggedInUser->user()->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_DELETE'), Config::get('databaseconstants.TBL_TEMPLATE'), $id, Config::get('constant.AUDIT_ORIGIN_WEB'), trans('labels.templatedeletesuccess'), '', $_SERVER['REMOTE_ADDR']);

            return Redirect::to("admin/templates")->with('success', trans('labels.templatedeletesuccess'));
        }
        else
        {
            Helpers::createAudit($this->loggedInUser->user()->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_DELETE'), Config::get('databaseconstants.TBL_TEMPLATE'), $id, Config::get('constant.AUDIT_ORIGIN_WEB'),trans('labels.somethingwrong'), '', $_SERVER['REMOTE_ADDR']);

            return Redirect::to("admin/templates")->with('error', trans('labels.commonerrormessage'));
        }
    }

}