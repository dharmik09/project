<?php

namespace App\Http\Controllers\Admin;

use Auth;
use Input;
use Image;
use DB;
use File;
use Config;
use Request;
use Helpers;
use Redirect;
use Illuminate\Pagination\Paginator;
use App\Http\Controllers\Controller;
use App\Services\Coin\Contracts\CoinRepository;
use App\Coins;
use App\Http\Requests\CoinsRequest;
use App\Transactions;
use App\Invoice;
use PDF;
use Mail;
use App\Services\FileStorage\Contracts\FileStorageRepository;

class CoinsManagementController extends Controller {

    public function __construct(FileStorageRepository $fileStorageRepository, CoinRepository $CoinRepository) {
        $this->objCoins = new Coins();
        $this->CoinRepository =  $CoinRepository;
        $this->fileStorageRepository = $fileStorageRepository;
        $this->invoiceUploadedPath = Config::get('constant.INVOICE_UPLOAD_PATH');
        $this->coinsOriginalImageUploadPath = Config::get('constant.COINS_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->coinsThumbImageUploadPath = Config::get('constant.COINS_THUMB_IMAGE_UPLOAD_PATH');
        $this->coinsThumbImageHeight = Config::get('constant.COINS_THUMB_IMAGE_HEIGHT');
        $this->coinsThumbImageWidth = Config::get('constant.COINS_THUMB_IMAGE_WIDTH');
    }

    public function index() {
        $coinsDetail = $this->CoinRepository->getAllCoins();
        return view('admin.ListCoinsPackage', compact('coinsDetail'));
    }

    public function add() {
        $coinsDetail = [];
        return view('admin.EditCoinsPackage', compact('coinsDetail'));
    }

    public function edit($id) {
        $coinsDetail = $this->objCoins->find($id);
        $uploadCoinsThumbPath = $this->coinsThumbImageUploadPath;
        return view('admin.EditCoinsPackage', compact('coinsDetail','uploadCoinsThumbPath'));
    }

    public function save(CoinsRequest $CoinsRequest) {
        $coinData = [];
        $coinData['id'] = e(Input::get('id'));
        $coinData['c_package_name'] = e(Input::get('c_package_name'));
        $coinData['c_coins'] = e(Input::get('c_coins'));
        $coinData['c_currency'] = e(Input::get('c_currency'));
        $coinData['c_price'] = e(Input::get('c_price'));
        $coinData['c_user_type'] = e(Input::get('c_user_type'));
        $coinData['c_valid_for'] = e(Input::get('c_valid_for'));
        $coinData['c_description'] = Input::get('c_description');
        $coinData['deleted'] = Input::get('deleted');
        $hiddenProfile = trim(Input::get('hidden_image'));
        $coinData['c_image'] = $hiddenProfile;
        if ($coinData['c_valid_for'] == 0 || $coinData['c_valid_for'] == '') {
            $coinData['c_valid_for'] == 30;
        }

        if (Input::file()) {
            $file = Input::file('c_image');
            if (!empty($file)) {
                $validationPass = Helpers::checkValidImageExtension($file);
                if($validationPass)
                {
                    $fileName = 'coins_' . time() . '.' . $file->getClientOriginalExtension();
                    $pathOriginal = public_path($this->coinsOriginalImageUploadPath . $fileName);
                    $pathThumb = public_path($this->coinsThumbImageUploadPath . $fileName);

                    Image::make($file->getRealPath())->save($pathOriginal);
                    Image::make($file->getRealPath())->resize($this->coinsThumbImageWidth, $this->coinsThumbImageHeight)->save($pathThumb);

                    if ($hiddenProfile != '' && $hiddenProfile != "proteen-logo.png") {
                        $originalImageDelete = $this->fileStorageRepository->deleteFileToStorage($hiddenProfile, $this->coinsOriginalImageUploadPath, "s3");
                        $thumbImageDelete = $this->fileStorageRepository->deleteFileToStorage($hiddenProfile, $this->coinsThumbImageUploadPath, "s3");
                    }

                    //Uploading on AWS
                    $originalImage = $this->fileStorageRepository->addFileToStorage($fileName, $this->coinsOriginalImageUploadPath, $pathOriginal, "s3");
                    $thumbImage = $this->fileStorageRepository->addFileToStorage($fileName, $this->coinsThumbImageUploadPath, $pathThumb, "s3");
                    
                    \File::delete($this->coinsOriginalImageUploadPath . $fileName);
                    \File::delete($this->coinsThumbImageUploadPath . $fileName);
                    $coinData['c_image'] = $fileName;
                }
            }
        }

        $response = $this->CoinRepository->saveCoinDetail($coinData);
        if ($response) {
            return Redirect::to("admin/coins")->with('success', trans('labels.coinupdatesuccess'));
        } else {
            return Redirect::to("admin/coins")->with('error', trans('labels.commonerrormessage'));
        }
    }
    public function delete($id) {
        $return = $this->CoinRepository->deleteCoins($id);
        if ($return) {
           return Redirect::to("admin/coins")->with('success', trans('labels.coindeletesuccess'));
        } else {
            return Redirect::to("admin/coins")->with('error', trans('labels.commonerrormessage'));
        }
    }

    public function storePaymentData() {
        $objTransactions = new Transactions();
        $transactionDetail = [];
        $transactionDetail['id'] = 0;
        $transactionDetail['tn_userid'] = 1;
        $transactionDetail['tn_user_type'] = 1;
        $transactionDetail['tn_email'] = 'dhara.gadhiya@gmail.com';
        $transactionDetail['tn_transaction_id'] = 'KJKS232KSD';
        $transactionDetail['tn_order_status'] = 'Success';
        $transactionDetail['tn_amount'] = 200;
        $transactionDetail['tn_coins'] = 50;
        $transactionDetail['tn_currency'] = 'INR';
        $transactionDetail['tn_package_id'] = 1;
        $transactionDetail['tn_device_type'] = 3;

        $transactionDetail['tn_tracking_id'] = '509675';
        $transactionDetail['tn_bank_ref_no'] = '89765454';
        $transactionDetail['tn_order_status'] = 'Success';
        $transactionDetail['tn_payment_mode'] = 'Net banking';
        $transactionDetail['tn_billing_name'] = 'Dhara';
        $transactionDetail['tn_billing_address'] = 'Ahmedabad';
        $transactionDetail['tn_billing_city'] = 'Ahmedabad';
        $transactionDetail['tn_billing_state'] = 'Gujrat';
        $transactionDetail['tn_billing_zip'] = '586952';
        $transactionDetail['tn_billing_country'] = 'India';
        $transactionDetail['tn_billing_phone'] = '9865235865';
        $transactionDetail['tn_extra'] = '';
        $transactionDetail['tn_trans_date'] = date("Y-m-d");
        $transactionDetail['c_package_name'] = 'Gold';
        $transactionDetail['name'] = 'Dhara';

        $objInvoice = new Invoice();
        $return = 1;
        //$return = $objTransactions->saveTransation($transactionDetail);
        if ($return) {
            $fileName = 1 . '_' . time().'.pdf';
            $invoiceDetail = [];
            $invoiceDetail['id'] = 0;
            $invoiceDetail['i_invoice_id'] = 1 . '_' . time();
            $invoiceDetail['i_transaction_id'] = 'KJKS232KSD';
            $invoiceDetail['i_invoice_name'] = $fileName;
            //$result = $objInvoice->saveInvoice($invoiceDetail);
        }
        $transactionDetail['i_invoice_id'] = 1 . '_' . time();;
        $response['transactionDetail'] = $transactionDetail;

        $pdf=PDF::loadView('admin.ExportInvoicePDF',$response);
        return $pdf->stream('Invoice.pdf');
        //PDF::loadView('admin.ExportInvoicePDF',$response)->save($this->invoiceUploadedPath.$fileName);
    }

    public function getTransaction() {
        $objTransactions = new Transactions();
        $transactionDetail = $objTransactions->getTransactionsDetailForAdmin(1);

        return view('admin.ShowTransaction', compact('transactionDetail'));
    }

    public function viewInvoiceData($transId) {
        $objInvoice = new Invoice();
        $invoice_name = $objInvoice->getInvoiceNameByTransactionId($transId);
        $fileName = '';
        if (!empty($invoice_name)) {
            $fileName = $invoice_name[0]->i_invoice_name;
        }
        if ($fileName != '') {
            header('Content-type: application/pdf');
            readfile($this->invoiceUploadedPath.$fileName);
        } else {
            return Redirect::to("admin/invoice")->with('error', trans('labels.commonerrormessage'));
        }
    }

    public function sendEmailForInvoice($transId) {
        $objInvoice = new Invoice();
        $invoiceData = $objInvoice->getInvoiceData($transId);
        $fileName = '';
        if (!empty($invoiceData)) {
            $fileName = $invoiceData[0]->i_invoice_name;
        }

        $file =  asset($this->invoiceUploadedPath.$fileName);

        $content = "Your Invoice Data";
        $data = array();
        $data['subject'] = 'Invoice';
        $data['toEmail'] = $invoiceData[0]->tn_email;
        $data['toName'] = $invoiceData[0]->t_name;
        $data['content'] = $content;
        $data['file'] = $file;

        Mail::send(['html' => 'emails.Template'], $data , function ($m) use ($data) {
            $m->from(Config::get('constant.FROM_MAIL_ID'), 'Invoice');
            $m->subject($data['subject']);
            $m->to($data['toEmail'], $data['toName']);
            $m->attach($data['file']);
        });
        return Redirect::to("admin/invoice")->with('success', trans('labels.mailsendsuccess'));
    }
    public function printInvoice() {
        $objInvoice = new Invoice();
        $transId = Input::get('id');
        $invoice_name = $objInvoice->getInvoiceNameByTransactionId($transId);
        $fileName = '';
        if (!empty($invoice_name)) {
            $fileName = $invoice_name[0]->i_invoice_name;
        }
        return $fileName;
        exit;
    }
}
