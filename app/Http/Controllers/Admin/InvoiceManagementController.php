<?php

namespace App\Http\Controllers\Admin;

use File;
use Image;
use Auth;
use Input;
use Config;
use Request;
use Redirect;
use App\Invoice;
use App\Http\Controllers\Controller;
use App\Http\Requests\PaidComponentsRequest;

class InvoiceManagementController extends Controller
{
    public function __construct() {
        //$this->middleware('auth.admin');
        $this->objInvoice = new Invoice();
    }

    public function index() {
        $searchParamArray = Input::get('type');
        // if (isset($searchParamArray['clearSearch'])) {
        //     unset($searchParamArray);
        //     $searchParamArray = array();
        // }
        //if (isset($searchParamArray['type'])) {
            //if($searchParamArray['type'] == 1) {
            //     $invoiceDetailForTeenager = $this->objInvoice->getAllInvoice($searchParamArray);
            //     $invoiceDetailForParent = $this->objInvoice->getAllInvoiceForParent();
            //     $invoiceDetailForSponsor = $this->objInvoice->getAllInvoiceForSponsor();
            // } else if($searchParamArray['type'] == 2) {
            //     $invoiceDetailForTeenager = $this->objInvoice->getAllInvoiceTeenager();
            //     $invoiceDetailForParent = $this->objInvoice->getAllInvoice($searchParamArray);
            //     $invoiceDetailForSponsor = $this->objInvoice->getAllInvoiceForSponsor();
            // } else if($searchParamArray['type'] == 4) {
            //     $invoiceDetailForTeenager = $this->objInvoice->getAllInvoiceTeenager();
            //     $invoiceDetailForParent = $this->objInvoice->getAllInvoiceForParent();
            //     $invoiceDetailForSponsor = $this->objInvoice->getAllInvoice($searchParamArray);
            // }
        //} else {
            $invoiceDetailForTeenager = $this->objInvoice->getAllInvoiceTeenager();
            $invoiceDetailForParent = $this->objInvoice->getAllInvoiceForParent();
            $invoiceDetailForSponsor = $this->objInvoice->getAllInvoiceForSponsor();
        //}
        return view('admin.ListInvoice' , compact('invoiceDetailForTeenager','invoiceDetailForParent','invoiceDetailForSponsor','searchParamArray'));
    }
}