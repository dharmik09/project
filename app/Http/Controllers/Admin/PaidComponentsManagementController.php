<?php

namespace App\Http\Controllers\Admin;

use File;
use Image;
use Auth;
use Input;
use Config;
use Request;
use Redirect;
use App\PaidComponent;
use App\Http\Controllers\Controller;
use App\Http\Requests\PaidComponentsRequest;

class PaidComponentsManagementController extends Controller
{
    public function __construct() {
        //$this->middleware('auth.admin');
        $this->objPaidComponent = new PaidComponent();
    }

    public function index() {
        $paidComponents = $this->objPaidComponent->getAllPaidComponents();
        return view('admin.ListPaidComponents' , compact('paidComponents'));
    }

    public function add() {
        $paidComponentsDetail = [];

        return view('admin.EditPaidComponents', compact('paidComponentsDetail'));
    }

    public function edit($id) {
        $paidComponentsDetail = $this->objPaidComponent->find($id);
        return view('admin.EditPaidComponents', compact('paidComponentsDetail'));
    }

    public function save(PaidComponentsRequest $paidComponentsRequest) {
        $componentsDetail = [];

        $componentsDetail['id'] = e(Input::get('id'));
        $componentsDetail['pc_element_name'] = e(Input::get('pc_element_name'));
        $componentsDetail['pc_required_coins'] = e(Input::get('pc_required_coins'));
        $componentsDetail['pc_is_paid'] = e(Input::get('pc_is_paid'));
        $componentsDetail['pc_valid_upto'] = e(Input::get('pc_valid_upto'));
        $componentsDetail['deleted'] = e(Input::get('deleted'));

        $response = $this->objPaidComponent->savePaidComponentsDetail($componentsDetail);
        if ($response) {
             return Redirect::to("admin/paidComponents")->with('success',trans('labels.paidcomponentsupdatesuccess'));
        } else {
            return Redirect::to("admin/paidComponents")->with('error', trans('labels.commonerrormessage'));
        }
    }

    public function delete($id) {
        $return = $this->objPaidComponent->deletePaidComponents($id);
        if ($return){
           return Redirect::to("admin/paidComponents")->with('success', trans('labels.paidcomponentsdeletesuccess'));
        } else {
            return Redirect::to("admin/paidComponents")->with('error', trans('labels.commonerrormessage'));
        }
    }

}

