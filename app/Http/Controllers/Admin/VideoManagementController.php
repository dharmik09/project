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
use App\Http\Requests\VideoRequest;
use App\Video;
use App\Services\FileStorage\Contracts\FileStorageRepository;

class VideoManagementController extends Controller {

    public function __construct(FileStorageRepository $fileStorageRepository) {
        //$this->middleware('auth.admin');
        $this->objVideo = new Video();
        $this->fileStorageRepository = $fileStorageRepository;
        $this->videoOriginalImageUploadPath = Config::get('constant.VIDEO_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->videoThumbImageUploadPath = Config::get('constant.VIDEO_THUMB_IMAGE_UPLOAD_PATH');
        $this->videoThumbImageHeight = Config::get('constant.VIDEO_THUMB_IMAGE_HEIGHT');
        $this->videoThumbImageWidth = Config::get('constant.VIDEO_THUMB_IMAGE_WIDTH');
    }

    public function index() {                
        $uploadVideoThumbPath = $this->videoThumbImageUploadPath;
        $videoDetail = $this->objVideo->getAllVideo();
        return view('admin.ListVideo', compact('videoDetail','searchParamArray','uploadVideoThumbPath'));
    }

    public function add() {
        $videoDetail = [];
        return view('admin.EditVideo', compact('videoDetail'));
    }

    public function edit($id) {
        $videoDetail = $this->objVideo->find($id);
        $uploadVideoThumbPath = $this->videoThumbImageUploadPath;
        return view('admin.EditVideo', compact('videoDetail','uploadVideoThumbPath'));
    }

    public function save(VideoRequest $videoRequest) {
        $videoData = [];
        $videoData['id'] = e(Input::get('id'));
        $videoData['v_title'] = Input::get('v_title');
        $videoData['v_link'] = Input::get('v_link');
        $videoData['deleted'] = Input::get('deleted');
        $hiddenPhoto = trim(Input::get('hidden_photo'));
        $videoData['v_photo'] = $hiddenPhoto;

        if (Input::file()) {
            $file = Input::file('v_photo');
            if (!empty($file)) {
                $validationPass = Helpers::checkValidImageExtension($file);
                if($validationPass)
                {
                    $fileName = 'video_' . time() . '.' . $file->getClientOriginalExtension();
                    $pathOriginal = public_path($this->videoOriginalImageUploadPath . $fileName);
                    $pathThumb = public_path($this->videoThumbImageUploadPath . $fileName);

                    Image::make($file->getRealPath())->save($pathOriginal);
                    Image::make($file->getRealPath())->resize($this->videoThumbImageWidth, $this->videoThumbImageHeight)->save($pathThumb);

                    if ($hiddenPhoto != '' && $hiddenPhoto != "proteen-logo.png") {
                        $originalImageDelete = $this->fileStorageRepository->deleteFileToStorage($hiddenPhoto, $this->videoOriginalImageUploadPath, "s3");
                        $thumbImageDelete = $this->fileStorageRepository->deleteFileToStorage($hiddenPhoto, $this->videoThumbImageUploadPath, "s3");
                    }

                    //Uploading on AWS
                    $originalImage = $this->fileStorageRepository->addFileToStorage($fileName, $this->videoOriginalImageUploadPath, $pathOriginal, "s3");
                    $thumbImage = $this->fileStorageRepository->addFileToStorage($fileName, $this->videoThumbImageUploadPath, $pathThumb, "s3");
                    
                    \File::delete($this->videoOriginalImageUploadPath . $fileName);
                    \File::delete($this->videoThumbImageUploadPath . $fileName);
                    $videoData['v_photo'] = $fileName;
                }
            }
        }
        $response = $this->objVideo->saveVideoDetail($videoData);
        if ($response) {
            return Redirect::to("admin/video")->with('success', trans('labels.videoupdatesuccess'));
        } else {
            return Redirect::to("admin/video")->with('error', trans('labels.commonerrormessage'));
        }
    }
    public function delete($id) {
        $return = $this->objVideo->deleteVideo($id);
        if ($return) {
           return Redirect::to("admin/video")->with('success', trans('labels.videodeletesuccess'));
        } else {
            return Redirect::to("admin/video")->with('error', trans('labels.commonerrormessage'));
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

        $objInvoice = new Invoice();
        $return = 1;
        $return = $objTransactions->saveTransation($transactionDetail);
        if ($return) {
            $fileName = 1 . '_' . time().'.pdf';
            $invoiceDetail = [];
            $invoiceDetail['id'] = 0;
            $invoiceDetail['i_invoice_id'] = 1 . '_' . time();
            $invoiceDetail['i_transaction_id'] = 'KJKS232KSD';
            $invoiceDetail['i_invoice_name'] = $fileName;
            $result = $objInvoice->saveInvoice($invoiceDetail);
        }
        $transactionDetail['tn_billing_name'] = 'Dhara';
        $transactionDetail['i_invoice_id'] = 1 . '_' . time();;
        $response['transactionDetail'] = $transactionDetail;

        PDF::loadView('admin.ExportInvoicePDF',$response)->save($this->invoiceUploadedPath.$fileName);
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
