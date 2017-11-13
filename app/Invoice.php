<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use Config;

class Invoice extends Model
{

    protected $table = 'pro_i_invoice';
    
    protected $guarded = [];
    
    public function saveInvoice($invoiceDetail) {
        if (isset($invoiceDetail['id']) && $invoiceDetail['id'] > 0) {
            $response = $this->where('id', $invoiceDetail['id'])->update($invoiceDetail);
        } else {
            $response = $this->create($invoiceDetail);
        }

        return $response;
    }

    public function getAllInvoice($searchParamArray) {
        $whereStr = '';
        $orderStr = '';

        $whereArray = [];
        $whereArray[] = 'invoice.id != ""';
        if (isset($searchParamArray) && !empty($searchParamArray)) {
            if (isset($searchParamArray['searchBy']) && isset($searchParamArray['searchText']) && $searchParamArray['searchBy'] != '' && $searchParamArray['searchText'] != '') {
                $whereArray[] = $searchParamArray['searchBy'] . " LIKE '%" . $searchParamArray['searchText'] . "%'";
                $whereArray[] = 'trans.tn_user_type = ' . $searchParamArray['type'];
            }

            if (isset($searchParamArray['orderBy']) && isset($searchParamArray['sortOrder']) && $searchParamArray['orderBy'] != '' && $searchParamArray['sortOrder'] != '') {
                $orderStr = " ORDER BY " . $searchParamArray['orderBy'] . " " . $searchParamArray['sortOrder'];
            }
        }

        if (!empty($whereArray)) {
            $whereStr = implode(" AND ", $whereArray);
        }

        if ($searchParamArray['type'] == 1){
            $invoice =DB::table(config::get('databaseconstants.TBL_INVOICE') . " AS invoice ")
                    ->leftjoin(config::get('databaseconstants.TBL_TEENAGER_TRANSACTION') . " AS trans ", 'trans.tn_transaction_id', '=', 'invoice.i_transaction_id')
                    ->leftjoin(config::get('databaseconstants.TBL_TEENAGERS') . " AS teen ", 'teen.id', '=', 'trans.tn_userid')
                    ->selectRaw('invoice.* , trans.tn_email,trans.tn_billing_name,trans.tn_amount,trans.tn_coins,teen.t_name')
                    ->whereRaw($whereStr)
                    ->groupBy('invoice.i_invoice_id')
                    ->paginate(Config::get('constant.ADMIN_RECORD_PER_PAGE'));
        } elseif($searchParamArray['type'] == 2) {
            $invoice =DB::table(config::get('databaseconstants.TBL_INVOICE') . " AS invoice ")
                    ->leftjoin(config::get('databaseconstants.TBL_TEENAGER_TRANSACTION') . " AS trans ", 'trans.tn_transaction_id', '=', 'invoice.i_transaction_id')
                    ->leftjoin(config::get('databaseconstants.TBL_PARENTS') . " AS parent ", 'parent.id', '=', 'trans.tn_userid')
                    ->selectRaw('invoice.* , trans.tn_email,trans.tn_billing_name,trans.tn_amount,trans.tn_coins,parent.p_first_name')
                    ->whereRaw($whereStr)
                    ->groupBy('invoice.i_invoice_id')
                    ->paginate(Config::get('constant.ADMIN_RECORD_PER_PAGE'));
        } elseif($searchParamArray['type'] == 4) {
            $invoice =DB::table(config::get('databaseconstants.TBL_INVOICE') . " AS invoice ")
                    ->leftjoin(config::get('databaseconstants.TBL_TEENAGER_TRANSACTION') . " AS trans ", 'trans.tn_transaction_id', '=', 'invoice.i_transaction_id')
                    ->leftjoin(config::get('databaseconstants.TBL_SPONSORS') . " AS sponsor ", 'sponsor.id', '=', 'trans.tn_userid')
                    ->selectRaw('invoice.* , trans.tn_email,trans.tn_billing_name,trans.tn_amount,trans.tn_coins,sponsor.sp_admin_name')
                    ->whereRaw($whereStr)
                    ->groupBy('invoice.i_invoice_id')
                    ->paginate(Config::get('constant.ADMIN_RECORD_PER_PAGE'));
        } else {
            $invoice = DB::table(config::get('databaseconstants.TBL_INVOICE') . " AS invoice ")
                    ->leftjoin(config::get('databaseconstants.TBL_TEENAGER_TRANSACTION') . " AS trans ", 'trans.tn_transaction_id', '=', 'invoice.i_transaction_id')
                    ->selectRaw('invoice.* , trans.tn_email,trans.tn_billing_name,trans.tn_amount,trans.tn_coins')
                    ->whereRaw($whereStr)
                    ->groupBy('invoice.i_invoice_id')
                    ->paginate(Config::get('constant.ADMIN_RECORD_PER_PAGE'));
        }


        return $invoice;
    }

    public function getInvoiceNameByTransactionId($transId) {
         $invoice = DB::table(config::get('databaseconstants.TBL_INVOICE'))
                ->selectRaw('i_invoice_name')
                ->where('i_transaction_id',$transId)
                ->get();
         return $invoice;
    }

    public function getInvoiceData($transId) {
        $invoice = DB::table(config::get('databaseconstants.TBL_INVOICE'). " AS invoice")
                ->leftjoin(config::get('databaseconstants.TBL_TEENAGER_TRANSACTION') . " AS trans", 'trans.tn_transaction_id', '=', 'invoice.i_transaction_id')
                ->leftjoin(config::get('databaseconstants.TBL_TEENAGERS') . " AS teen", 'teen.id', '=', 'trans.tn_userid')
                ->selectRaw('invoice.* , tn_email, t_name,trans.tn_amount,trans.tn_coins')
                ->where('i_transaction_id',$transId)
                ->get();
         return $invoice;
    }

    public function getAllInvoiceTeenager() {

        $invoice = DB::table(config::get('databaseconstants.TBL_INVOICE') . " AS invoice")
                ->leftjoin(config::get('databaseconstants.TBL_TEENAGER_TRANSACTION') . " AS trans", 'trans.tn_transaction_id', '=', 'invoice.i_transaction_id')
                ->leftjoin(config::get('databaseconstants.TBL_TEENAGERS') . " AS teen", 'teen.id', '=', 'trans.tn_userid')
                ->selectRaw('invoice.* , trans.tn_email,trans.tn_billing_name,trans.tn_amount,trans.tn_coins,teen.t_name')
                ->where('trans.tn_user_type', 1)
                ->groupBy('invoice.i_invoice_id')
                ->get();

        return $invoice;
    }

    public function getAllInvoiceForParent() {

        $invoice = DB::table(config::get('databaseconstants.TBL_INVOICE') . " AS invoice")
                ->leftjoin(config::get('databaseconstants.TBL_TEENAGER_TRANSACTION') . " AS trans", 'trans.tn_transaction_id', '=', 'invoice.i_transaction_id')
                ->leftjoin(config::get('databaseconstants.TBL_PARENTS') . " AS parent", 'parent.id', '=', 'trans.tn_userid')
                ->selectRaw('invoice.* , trans.tn_email,trans.tn_billing_name,trans.tn_amount,trans.tn_coins,parent.p_first_name')
                ->where('trans.tn_user_type',2)
                ->groupBy('invoice.i_invoice_id')
                ->paginate(Config::get('constant.ADMIN_RECORD_PER_PAGE'));

        return $invoice;
    }

    public function getAllInvoiceForSponsor() {

        $invoice = DB::table(config::get('databaseconstants.TBL_INVOICE') . " AS invoice")
                ->leftjoin(config::get('databaseconstants.TBL_TEENAGER_TRANSACTION') . " AS trans", 'trans.tn_transaction_id', '=', 'invoice.i_transaction_id')
                ->leftjoin(config::get('databaseconstants.TBL_SPONSORS') . " AS sponsor", 'sponsor.id', '=', 'trans.tn_userid')
                ->selectRaw('invoice.* , trans.tn_email,trans.tn_billing_name,trans.tn_amount,trans.tn_coins,sponsor.sp_admin_name')
                ->where('trans.tn_user_type',4)
                ->groupBy('invoice.i_invoice_id')
                ->paginate(Config::get('constant.ADMIN_RECORD_PER_PAGE'));

        return $invoice;
    }
}
