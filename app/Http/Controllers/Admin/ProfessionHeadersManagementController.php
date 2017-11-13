<?php

namespace App\Http\Controllers\Admin;

use Auth;
use Input;
use Config;
use Request;
use Helpers;
use Redirect;
use App\ProfessionHeaders;
use Illuminate\Pagination\Paginator;
use App\Level1Activity;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProfessionHeadersRequest;
use App\Services\ProfessionHeaders\Contracts\ProfessionHeadersRepository;
use Cache;

class ProfessionHeadersManagementController extends Controller
{

    public function __construct(ProfessionHeadersRepository $ProfessionHeadersRepository)
    {
        $this->objProfessionHeaders                = new ProfessionHeaders();
        $this->ProfessionHeadersRepository         = $ProfessionHeadersRepository;
        $this->controller = 'ProfessionHeadersManagementController';
        $this->loggedInUser = Auth::guard('admin');
    }
    public function index()
    {
        $headers = $this->ProfessionHeadersRepository->getAllProfessionHeaders();
        Helpers::createAudit($this->loggedInUser->user()->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_READ'), $this->controller . "@index", $_SERVER['REQUEST_URI'], Config::get('constant.AUDIT_ORIGIN_WEB'), '', '', $_SERVER['REMOTE_ADDR']);

        return view('admin.ListProfessionHeaders',compact('headers'));
    }

    public function add()
    {
        $headerDetail =[];
        Helpers::createAudit($this->loggedInUser->user()->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_READ'), $this->controller . "@add", $_SERVER['REQUEST_URI'], Config::get('constant.AUDIT_ORIGIN_WEB'), '', '', $_SERVER['REMOTE_ADDR']);

        return view('admin.EditProfessionHeader', compact('headerDetail'));
    }

    public function edit($id)
    {
        $headerDetail = $this->objProfessionHeaders->getActiveProfessionHeader($id);
        //echo "<pre>"; print_r($headerDetail); exit;
        Helpers::createAudit($this->loggedInUser->user()->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_READ'), $this->controller . "@edit", $_SERVER['REQUEST_URI'], Config::get('constant.AUDIT_ORIGIN_WEB'), '', '', $_SERVER['REMOTE_ADDR']);
        return view('admin.EditProfessionHeader', compact('headerDetail','id'));
    }

    public function save(ProfessionHeadersRequest $ProfessionHeadersRequest)
    {
        $headerData = Input::All();
        
        $response = $this->ProfessionHeadersRepository->saveProfessionHeaderFromAdmin($headerData);
        Cache::forget('professionHeaders');
        if($response)
        {
            //Helpers::createAudit($this->loggedInUser->user()->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_UPDATE'), Config::get('databaseconstants.TBL_PROFESSION_HEADER'), $response, Config::get('constant.AUDIT_ORIGIN_WEB'),  trans('labels.headerupdatesuccess'), serialize($headerData), $_SERVER['REMOTE_ADDR']);

            return Redirect::to("admin/headers".$headerData['pageRank'])->with('success',trans('labels.headerupdatesuccess'));
        }
        else
        {
           //Helpers::createAudit($this->loggedInUser->user()->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_UPDATE'), Config::get('databaseconstants.TBL_PROFESSION_HEADER'), $response, Config::get('constant.AUDIT_ORIGIN_WEB'),  trans('labels.somethingwrong'), serialize($headerData), $_SERVER['REMOTE_ADDR']);

           return Redirect::to("admin/headers".$headerData['pageRank'])->with('error', trans('labels.commonerrormessage'));
        }
    }
    public function delete($id)
    {
        $return = $this->ProfessionHeadersRepository->deleteProfessionHeader($id);
        if($return)
        {
           Helpers::createAudit($this->loggedInUser->user()->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_DELETE'), Config::get('databaseconstants.TBL_PROFESSION_HEADER'), $id, Config::get('constant.AUDIT_ORIGIN_WEB'), trans('labels.headerdeletesuccess'), '', $_SERVER['REMOTE_ADDR']);

            return Redirect::to("admin/headers")->with('success', trans('labels.headerdeletesuccess'));
        }
        else
        {
            Helpers::createAudit($this->loggedInUser->user()->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_DELETE'), Config::get('databaseconstants.TBL_PROFESSION_HEADER'), $id, Config::get('constant.AUDIT_ORIGIN_WEB'), trans('labels.somethingwrong'), '', $_SERVER['REMOTE_ADDR']);

            return Redirect::to("admin/headers")->with('error', trans('labels.commonerrormessage'));
        }
    }
}