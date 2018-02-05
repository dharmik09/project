<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Http\Controllers;
use Illuminate\Http\Request;
use App\Configurations;
use App\Transactions;
use App\PurchasedCoins;
use App\Invoice;
use App\Services\Teenagers\Contracts\TeenagersRepository;
use App\Services\Template\Contracts\TemplatesRepository;
use App\Services\Coin\Contracts\CoinRepository;
use App\Services\Parents\Contracts\ParentsRepository;
use App\Services\Schools\Contracts\SchoolsRepository;
use App\Services\Sponsors\Contracts\SponsorsRepository;
use App\Teenagers;
use App\Templates;
use Config;
use Indipay;
use Auth;
use Redirect;
use Mail;
use PDF;

class PaymentController extends Controller {

	use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

	public function __construct(TeenagersRepository $teenagersRepository,  TemplatesRepository $templatesRepository, CoinRepository $coinRepository,ParentsRepository $parentsRepository, SchoolsRepository $schoolsRepository, SponsorsRepository $sponsorsRepository)
    {
        $this->objTeenagers = new Teenagers;
        $this->teenagersRepository = $teenagersRepository;
        $this->templateRepository = $templatesRepository;
        $this->coinRepository =  $coinRepository;
        $this->parentsRepository = $parentsRepository;
        $this->sponsorsRepository = $sponsorsRepository;
        $this->schoolsRepository = $schoolsRepository;
        $this->objTemplates = new Templates();
        //$this->middleware('auth.teenager');
        $this->invoiceUploadedPath = Config::get('constant.INVOICE_UPLOAD_PATH');
        $this->objConfigurations = new Configurations;
        $this->objTransactions = new Transactions;
        $this->objPurchasedCoins = new PurchasedCoins;
        $this->objInvoice = new Invoice;
    }
    public function orderResponse(Request $request)
    {
    	// For default Gateway
        $response = Indipay::response($request);

        $saveTransactionDetail = [];
        $saveTransactionDetail['tn_transaction_id'] = $response['order_id'];
        $saveTransactionDetail['tn_tracking_id'] = $response['tracking_id'];
        $saveTransactionDetail['tn_bank_ref_no'] = $response['bank_ref_no'];
        $saveTransactionDetail['tn_order_status'] = $response['order_status'];
        $saveTransactionDetail['tn_payment_mode'] = $response['payment_mode'];
        $saveTransactionDetail['tn_currency'] = $response['currency'];
        $saveTransactionDetail['tn_amount'] = $response['amount'];
        $saveTransactionDetail['tn_billing_name'] = $response['billing_name'];
        $saveTransactionDetail['tn_billing_address'] = $response['billing_address'];
        $saveTransactionDetail['tn_billing_city'] = $response['billing_city'];
        $saveTransactionDetail['tn_billing_state'] = $response['billing_state'];
        $saveTransactionDetail['tn_billing_zip'] = $response['billing_zip'];
        $saveTransactionDetail['tn_billing_country'] = $response['billing_country'];
        $saveTransactionDetail['tn_billing_phone'] = $response['billing_tel'];
        $saveTransactionDetail['tn_email'] = $response['billing_email'];
        $saveTransactionDetail['tn_extra'] = $response['billing_notes'];
        $date = $response['trans_date'];
        $trans_date = str_replace('/', '-', $date);
        $saveTransactionDetail['tn_trans_date'] = date("Y-m-d", strtotime($trans_date));
        $saveTransactionDetail['tn_device_type'] = 3;
        $packageId = $response['merchant_param2'];
        $saveTransactionDetail['tn_package_id'] = $packageId;

        //get coin package details by package id
		$coinsDetail = $this->coinRepository->getAllCoinsDetailByid($packageId);

		//Initialize initial procoins
        $initialProCoins = Config::get('constant.INITIAL_PRO_COINS'); //$this->objConfigurations->getCreditValue(Config::get('constant.INITIAL_COINS'));

        $coins = 0;
        if (!empty($coinsDetail)) {
            $coins = $coinsDetail[0]->c_coins;
            $saveTransactionDetail['tn_coins'] = $coins;
        }
        if ($response['merchant_param1'] == Config::get('constant.TEENAGER_USER_TYPE_FLAG')) {
			if (Auth::guard('teenager')->check()) {
                $teenId = Auth::guard('teenager')->user()->id;
				$saveTransactionDetail['tn_userid'] = $teenId;
                $saveTransactionDetail['tn_user_type'] = Config::get('constant.TEENAGER_USER_TYPE_FLAG');

                //Save transactions details for teenagers
                $saveTransaction = $this->objTransactions->saveTransation($saveTransactionDetail);

                if ($response['order_status'] == 'Success' || $response['order_status'] == 'Initiated') {
                    $saveCoinsData = [];
					$saveCoinsData['pc_user_id'] = $teenId;
                    $saveCoinsData['pc_purchased_date'] = date('Y-m-d');
                    $saveCoinsData['pc_total_coins'] = $coins;
                    $saveCoinsData['pc_total_price'] = $coinsDetail[0]->c_price;
                    $saveCoinsData['pc_user_type'] = Config::get('constant.TEENAGER_USER_TYPE_FLAG');
					$coins = $coins - $initialProCoins;

					//Save purchased coins details
                    $result = $this->objPurchasedCoins->savePurchasedCoinsDetail($saveCoinsData);

                    //get teenager details
                    $userDetail = $this->teenagersRepository->getUserDataForCoinsDetail($teenId);
                    if (!empty($userDetail)) {
                        $coins += $userDetail['t_coins'];
                    }

                    //Add purchased coins to teenager's existing coins
                    $return = $this->teenagersRepository->updateTeenagerCoinsDetail($teenId, $coins);

                    //get teenager updated details
                    $userArray = $this->teenagersRepository->getTeenagerByTeenagerId($teenId);

                    //Send Mail to teenager
                    $replaceArray = array();
                    $replaceArray['USER_NAME'] = $userArray['t_name'];
                    $replaceArray['PACKAGE_NAME'] = $coinsDetail[0]->c_package_name;
                    $replaceArray['COINS'] = $coinsDetail[0]->c_coins;
                    $emailTemplateContent = $this->templateRepository->getEmailTemplateDataByName(Config::get('constant.PURCHASED_COINS'));
                    $content = $this->templateRepository->getEmailContent($emailTemplateContent->et_body, $replaceArray);

                    $data = array();
                    $data['subject'] = $emailTemplateContent->et_subject;
                    $data['toEmail'] = $userArray['t_email'];
                    $data['toName'] = $userArray['t_name'];
                    $data['content'] = $content;

                    Mail::send(['html' => 'emails.Template'], $data , function ($m) use ($data) {
                        $m->from(Config::get('constant.FROM_MAIL_ID'), 'Purchased ProCoins');
                        $m->subject($data['subject']);
                        $m->to($data['toEmail'], $data['toName']);
                    });

                    //Generate Invoice
                    $fileName = $teenId . '_' . time().'.pdf';
                    $invoiceDetail = [];
                    $invoiceDetail['id'] = 0;
                    $invoiceDetail['i_invoice_id'] = $teenId . '_' . time();
                    $invoiceDetail['i_transaction_id'] = $response['order_id'];
                    $invoiceDetail['i_invoice_name'] = $fileName;

         			//Store invoice details
                    $return = $this->objInvoice->saveInvoice($invoiceDetail);
                    $saveTransactionDetail['i_invoice_id'] = $teenId . '_' . time();
                    $saveTransactionDetail['c_package_name'] = $coinsDetail[0]->c_package_name;
                    $saveTransactionDetail['name'] = $userArray['t_name'];
                    $responseData['transactionDetail'] = $saveTransactionDetail;
					PDF::loadView('exportInvoicePDF', $responseData)->save($this->invoiceUploadedPath.$fileName);
                    if ($result) {
                        return Redirect::to("teenager/buy-procoins")->with('success', trans('labels.coinpurchasedsuccess'));
                    } else {
                        return Redirect::to("teenager/buy-procoins")->with('error', trans('labels.commonerrormessage'));
                    }
                } else {
                    return Redirect::to("teenager/buy-procoins")->with('error', trans('labels.paymentfailmessage'));
                }
            } else {
                Auth::guard('teenager')->logout();
        		return redirect()->to(route('login'));
            }
        } else if ($response['merchant_param1'] == Config::get('constant.PARENT_USER_TYPE_FLAG')) {
            if (Auth::guard('parent')->check()) {
                $parentId = Auth::guard('parent')->user()->id;
				$saveTransactionDetail['tn_userid'] = $parentId;
                $saveTransactionDetail['tn_user_type'] = Config::get('constant.PARENT_USER_TYPE_FLAG');

                //Store transaction details for parents
                $saveTransaction = $this->objTransactions->saveTransation($saveTransactionDetail);

                if ($response['order_status'] == 'Success' || $response['order_status'] == 'Initiated') {
                    $saveCoinsData = [];
					$saveCoinsData['pc_user_id'] = $parentId;
                    $saveCoinsData['pc_purchased_date'] = date('Y-m-d');
                    $saveCoinsData['pc_total_coins'] = $coins;
                    $saveCoinsData['pc_total_price'] = $coinsDetail[0]->c_price;
                    $saveCoinsData['pc_user_type'] = Config::get('constant.PARENT_USER_TYPE_FLAG');

                    $coins = $coins - $initialProCoins;

                    //Store purchased coins details
                    $result = $this->objPurchasedCoins->savePurchasedCoinsDetail($saveCoinsData);

                    //Retrieve parent details
                    $userDetail = $this->parentsRepository->getParentDataForCoinsDetail($parentId);
                    if (!empty($userDetail)) {
                        $coins += $userDetail['p_coins'];
                    }

                    //Add purchased coins to parent's existing coins 
                    $responseArray = $this->parentsRepository->updateParentCoinsDetail($parentId, $coins);

                    //Retrieve parent details with updated coins
                    $userArray = $this->parentsRepository->getParentById($parentId);

                    //Send Mail to parent user
                    $replaceArray = array();
                    $replaceArray['USER_NAME'] = $userArray->p_first_name. " " .$userArray->p_last_name;
                    $replaceArray['PACKAGE_NAME'] = $coinsDetail[0]->c_package_name;
                    $replaceArray['COINS'] = $coinsDetail[0]->c_coins;
                    $emailTemplateContent = $this->templateRepository->getEmailTemplateDataByName(Config::get('constant.PURCHASED_COINS'));

                    $content = $this->templateRepository->getEmailContent($emailTemplateContent->et_body, $replaceArray);
					$data = array();
                    $data['subject'] = $emailTemplateContent->et_subject;
                    $data['toEmail'] = $userArray->p_email;
                    $data['toName'] = $userArray->p_first_name. " " .$userArray->p_last_name;
                    $data['content'] = $content;

                    Mail::send(['html' => 'emails.Template'], $data , function ($m) use ($data) {
                        $m->from(Config::get('constant.FROM_MAIL_ID'), 'Purchased ProCoins');
                        $m->subject($data['subject']);
                        $m->to($data['toEmail'], $data['toName']);
                    });

                    //Generate Invoice
                    $fileName = $parentId . '_' . time().'.pdf';
                    $invoiceDetail = [];
                    $invoiceDetail['id'] = 0;
                    $invoiceDetail['i_invoice_id'] = $parentId . '_' . time();
                    $invoiceDetail['i_transaction_id'] = $response['order_id'];
                    $invoiceDetail['i_invoice_name'] = $fileName;
                    $return = $this->objInvoice->saveInvoice($invoiceDetail);
                    $saveTransactionDetail['i_invoice_id'] = $parentId . '_' . time();
                    $saveTransactionDetail['c_package_name'] = $coinsDetail[0]->c_package_name;
                    $saveTransactionDetail['name'] = $userArray->p_first_name;
                    $responseData['transactionDetail'] = $saveTransactionDetail;

                    PDF::loadView('exportInvoicePDF',$responseData)->save($this->invoiceUploadedPath.$fileName);
                    if ($result) {
                        return Redirect::to("parent/my-coins")->with('success', trans('labels.coinpurchasedsuccess'));
                    } else {
                        return Redirect::to("parent/my-coins")->with('error', trans('labels.commonerrormessage'));
                    }
                } else {
                    return Redirect::to("parent/my-coins")->with('error', trans('labels.paymentfailmessage'));
                }
            } else {
                Auth::guard('parent')->logout();
        		return redirect()->to(route('login'));
            }
        } else if ($response['merchant_param1'] == Config::get('constant.SPONSOR_USER_TYPE_FLAG')) {
            $flag = 0;
            if (Auth::guard('sponsor')->check()) {
                $sponsorId = Auth::guard('sponsor')->user()->id;
                $flag = 1;
            } else {
                $sponsorId = $response['merchant_param3'];
            }

            $saveTransactionDetail['tn_userid'] = $sponsorId;
            $saveTransactionDetail['tn_user_type'] = Config::get('constant.SPONSOR_USER_TYPE_FLAG');

            //Store transaction details for sponsor
            $saveTransaction = $this->objTransactions->saveTransation($saveTransactionDetail);

            if ($response['order_status'] == 'Success' || $response['order_status'] == 'Initiated') {
                $saveCoinsData = [];

                $saveCoinsData['pc_user_id'] = $sponsorId;
                $saveCoinsData['pc_purchased_date'] = date('Y-m-d');
                $saveCoinsData['pc_total_coins'] = $coins;
                $saveCoinsData['pc_total_price'] = $coinsDetail[0]->c_price;
                $saveCoinsData['pc_user_type'] = Config::get('constant.SPONSOR_USER_TYPE_FLAG');
                $schoolCoins = $coins;

                //Store purchased coins details for sponsor
                $result = $this->objPurchasedCoins->savePurchasedCoinsDetail($saveCoinsData);

                //Retrieve sponsor details
                $sponsorData = $this->sponsorsRepository->getSponsorDataForCoinsDetail($sponsorId);

                if (!empty($sponsorData)) {
                    $coins += $sponsorData['sp_credit'];
                }

                //Update sponsor's credit
                $result = $this->sponsorsRepository->updateSponsorCoinsDetail($sponsorId, $coins);

                //Retrieve sponsor details
                $userArray = $this->sponsorsRepository->getSponsorById($sponsorId);
				if (!empty($userArray)) {
                    if ($userArray->sp_sc_uniqueid != '') {
                    	//Retrieve school details
                        $schoolData = $this->schoolsRepository->getSchoolDataForCoinsDetailByUniqueid($userArray->sp_sc_uniqueid);

                        if (!empty($schoolData)) {
                            $schoolCoins += $schoolData['sc_coins'];
                        }
                        //Update coins details for school
                        $result = $this->schoolsRepository->updateSchoolCoinsDetailByUniqueid($userArray->sp_sc_uniqueid, $schoolCoins);
                    }
                }
                //Send Mail
                $replaceArray = array();
                $replaceArray['USER_NAME'] = $userArray->sp_admin_name;
                $replaceArray['PACKAGE_NAME'] = $coinsDetail[0]->c_package_name;
                $replaceArray['COINS'] = $coinsDetail[0]->c_coins;
                $emailTemplateContent = $this->templateRepository->getEmailTemplateDataByName(Config::get('constant.PURCHASED_COINS'));
                $content = $this->templateRepository->getEmailContent($emailTemplateContent->et_body, $replaceArray);

                $data = array();
                $data['subject'] = $emailTemplateContent->et_subject;
                $data['toEmail'] = $userArray->sp_email;
                $data['toName'] = $userArray->sp_admin_name;
                $data['content'] = $content;

                Mail::send(['html' => 'emails.Template'], $data , function ($m) use ($data) {
                    $m->from(Config::get('constant.FROM_MAIL_ID'), 'Purchased ProCoins');
                    $m->subject($data['subject']);
                    $m->to($data['toEmail'], $data['toName']);
                });

                //Generate Invoice
                $fileName = $sponsorId . '_' . time().'.pdf';
                $invoiceDetail = [];
                $invoiceDetail['id'] = 0;
                $invoiceDetail['i_invoice_id'] = $sponsorId . '_' . time();
                $invoiceDetail['i_transaction_id'] = $response['order_id'];
                $invoiceDetail['i_invoice_name'] = $fileName;
                $return = $this->objInvoice->saveInvoice($invoiceDetail);
                $saveTransactionDetail['i_invoice_id'] = $sponsorId . '_' . time();
                $saveTransactionDetail['c_package_name'] = $coinsDetail[0]->c_package_name;
                $saveTransactionDetail['name'] = $userArray->sp_admin_name;
                $responseData['transactionDetail'] = $saveTransactionDetail;

                PDF::loadView('exportInvoicePDF', $responseData)->save($this->invoiceUploadedPath.$fileName);
                if ($result) {
                    if($flag) {
                        return Redirect::to("sponsor/my-coins")->with('success', trans('labels.coinpurchasedsuccess'));
                    } else {
                        return Redirect::to("sponsor/login")->with('success', trans('labels.aftercoinspurchasemsg'));
                    }
                } else {
                    if($flag) {
                        return Redirect::to("sponsor/my-coins")->with('error', trans('labels.commonerrormessage'));
                    } else {
                        return Redirect::to("sponsor/login")->with('error', trans('labels.commonerrormessage'));
                    }
                }
            } else {
                if($flag) {
                    return Redirect::to("sponsor/my-coins")->with('error', trans('labels.paymentfailmessage'));
                } else {
                    return Redirect::to("sponsor/login")->with('error', trans('labels.paymentfailmessage'));
                }
            }
        } else {
            dd($response);
        }
    }
}
