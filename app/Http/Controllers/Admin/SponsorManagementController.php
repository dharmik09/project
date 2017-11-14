<?php

namespace App\Http\Controllers\Admin;

use Auth;
use Input;
use File;
use Image;
use Config;
use Helpers;
use App\Country;
use App\State;
use App\City;
use Redirect;
use Illuminate\Pagination\Paginator;
use App\Sponsors;
use App\Http\Controllers\Controller;
use App\Http\Requests\SponsorRequest;
use App\Services\Sponsors\Contracts\SponsorsRepository;
use App\Services\Template\Contracts\TemplatesRepository;
use App\Services\Teenagers\Contracts\TeenagersRepository;
use Mail;
use App\TeenagerCoinsGift;
use App\Services\FileStorage\Contracts\FileStorageRepository;

class SponsorManagementController extends Controller
{
    public function __construct(FileStorageRepository $fileStorageRepository, SponsorsRepository $sponsorsRepository,TemplatesRepository $templatesRepository,TeenagersRepository $teenagersRepository)
    {
        $this->objSponsors                = new Sponsors();
        $this->sponsorsRepository         = $sponsorsRepository;
        $this->teenagersRepository        = $teenagersRepository;
        $this->templateRepository = $templatesRepository;
        $this->fileStorageRepository = $fileStorageRepository;
        $this->sponsorOriginalImageUploadPath = Config::get('constant.SPONSOR_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->sponsorThumbImageUploadPath = Config::get('constant.SPONSOR_THUMB_IMAGE_UPLOAD_PATH');
        $this->sponsorThumbImageHeight = Config::get('constant.SPONSOR_THUMB_IMAGE_HEIGHT');
        $this->sponsorThumbImageWidth = Config::get('constant.SPONSOR_THUMB_IMAGE_WIDTH');
        $this->sponsorActivityOriginalImageUploadPath = Config::get('constant.SA_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->sponsorActivityThumbImageUploadPath = Config::get('constant.SA_THUMB_IMAGE_UPLOAD_PATH');
        $this->saThumbImageWidth = Config::get('constant.SA_THUMB_IMAGE_WIDTH');
        $this->saThumbImageHeight = Config::get('constant.SA_THUMB_IMAGE_HEIGHT');
        $this->contactOriginalImageUploadPath = Config::get('constant.CONTACT_PHOTO_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->contactThumbImageUploadPath = Config::get('constant.CONTACT_PHOTO_THUMB_IMAGE_UPLOAD_PATH');
        $this->contactThumbImageHeight = Config::get('constant.CONTACT_PHOTO_THUMB_IMAGE_HEIGHT');
        $this->contactThumbImageWidth = Config::get('constant.CONTACT_PHOTO_THUMB_IMAGE_WIDTH');
        $this->controller = 'SponsorManagementController';
        $this->loggedInUser = Auth::guard('admin');
    }

    public function index()
    {
        $uploadSponsorThumbPath = $this->sponsorThumbImageUploadPath;
        $uploadSponsorPhotoThumbPath = $this->contactThumbImageUploadPath;
        $sponsors = $this->sponsorsRepository->getAllSponsors();
        Helpers::createAudit($this->loggedInUser->user()->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_READ'), $this->controller . "@index", $_SERVER['REQUEST_URI'], Config::get('constant.AUDIT_ORIGIN_WEB'), '', '', $_SERVER['REMOTE_ADDR']);
        return view('admin.ListSponsor',compact('sponsors', 'uploadSponsorThumbPath','uploadSponsorPhotoThumbPath'));
    }

    public function add()
    {
        $sponsorDetail =[];
        $newuser = array();
        $states = [];
        $cities = [];
        Helpers::createAudit($this->loggedInUser->user()->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_READ'), $this->controller . "@add", $_SERVER['REQUEST_URI'], Config::get('constant.AUDIT_ORIGIN_WEB'), '', '', $_SERVER['REMOTE_ADDR']);

        return view('admin.EditSponsor', compact('newuser','sponsorDetail','cities','states'));

    }

    public function edit($id)
    {
        $newuser = array();
        $uploadSponsorThumbPath = $this->sponsorThumbImageUploadPath;
        $uploadSponsorPhotoThumbPath = $this->contactThumbImageUploadPath; ;
        $sponsorDetail = $this->objSponsors->find($id);
        $cities =  Helpers::getCities($sponsorDetail->sp_state);
        $states =  Helpers::getStates($sponsorDetail->sp_country);
        Helpers::createAudit($this->loggedInUser->user()->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_READ'), $this->controller . "@edit", $_SERVER['REQUEST_URI'], Config::get('constant.AUDIT_ORIGIN_WEB'), '', '', $_SERVER['REMOTE_ADDR']);

        return view('admin.EditSponsor', compact('sponsorDetail', 'uploadSponsorThumbPath','uploadSponsorPhotoThumbPath','states','cities'));
    }

    public function save(SponsorRequest $SponsorRequest)
    {
        $sponsorDetail = [];

        $sponsorDetail['id']   = e(input::get('id'));
        $hiddenLogo     = e(input::get('hidden_logo'));
        $hiddenPhoto     = e(input::get('hidden_photo'));
        $sponsorDetail['sp_logo']    = $hiddenLogo;
        $sponsorDetail['sp_photo']    = $hiddenPhoto;
        $sponsorDetail['sp_company_name']   = e(input::get('sp_company_name'));
        $sponsorDetail['sp_email'] =    e(input::get('sp_email'));
        $sponsorDetail['sp_admin_name'] =    e(input::get('sp_admin_name'));
        $hiddenPassword = e(Input::get('hidden_password'));
        $password = e(Input::get('password'));
        $confirm_password       = e(Input::get('confirm_password'));
        $sponsorDetail['sp_address1']  = e(input::get('sp_address1'));
        $sponsorDetail['sp_address2'] = e(input::get('sp_address2'));
        $sponsorDetail['sp_pincode'] = e(input::get('sp_pincode'));
        $sponsorDetail['sp_city'] = e(input::get('sp_city'));
        $sponsorDetail['sp_state'] = e(input::get('sp_state'));
        $sponsorDetail['sp_country'] = e(input::get('sp_country'));
        $sponsorDetail['sp_credit']    = e(input::get('sp_credit'));
        $sponsorDetail['sp_first_name']    = e(input::get('sp_first_name'));
        $sponsorDetail['sp_last_name']    = e(input::get('sp_last_name'));
        $sponsorDetail['sp_title']    = e(input::get('sp_title'));
        $sponsorDetail['sp_phone']    = e(input::get('sp_phone'));
        $sponsorDetail['sp_isapproved']  = e(input::get('sp_isapproved'));
        $sponsorDetail['deleted']   = e(input::get('deleted'));
        $sponsorDetail['sp_uniqueid']   = e(input::get('sp_uniqueid'));
        if($hiddenPassword != '' && $password == '')
        {
            $sponsorDetail['password']   = $hiddenPassword;
        }
        else
        {
            $sponsorDetail['password']   = bcrypt($password);
        }
        if (Input::file())
        {
            $sp_logo = Input::file('sp_logo');
            if(!empty($sp_logo))
            {
                //Check image valid extension
                $validationPass = Helpers::checkValidImageExtension($sp_logo);
                if($validationPass)
                {
                    $fileName = 'sponsor_' . time() . '.' . $sp_logo->getClientOriginalExtension();
                    $pathOriginal = public_path($this->sponsorOriginalImageUploadPath . $fileName);
                    $pathThumb = public_path($this->sponsorThumbImageUploadPath . $fileName);

                    Image::make($sp_logo->getRealPath())->save($pathOriginal);
                    Image::make($sp_logo->getRealPath())->resize($this->sponsorThumbImageWidth, $this->sponsorThumbImageHeight)->save($pathThumb);

                    if ($hiddenLogo != '')
                    {
                        $originalImageDelete = $this->fileStorageRepository->deleteFileToStorage($hiddenLogo, $this->sponsorOriginalImageUploadPath, "s3");
                        $thumbImageDelete = $this->fileStorageRepository->deleteFileToStorage($hiddenLogo, $this->sponsorThumbImageUploadPath, "s3");
                    }
                    $originalImage = $this->fileStorageRepository->addFileToStorage($fileName, $this->sponsorOriginalImageUploadPath, $pathOriginal, "s3");
                    $thumbImage = $this->fileStorageRepository->addFileToStorage($fileName, $this->sponsorThumbImageUploadPath, $pathThumb, "s3");
                    \File::delete($this->sponsorOriginalImageUploadPath . $fileName);
                    \File::delete($this->sponsorThumbImageUploadPath . $fileName);
                    
                    $sponsorDetail['sp_logo'] = $fileName;
                }
            }
        }
         //sponsor photo
        if (Input::file())
        {
            $sp_photo = Input::file('sp_photo');
            if(!empty($sp_photo))
            {
                //Check image valid extension
                $validationPass = Helpers::checkValidImageExtension($sp_photo);
                if($validationPass)
                {
                    $fileName = 'sponsor_' . time() . '.' . $sp_photo->getClientOriginalExtension();
                    $pathOriginal = public_path($this->contactOriginalImageUploadPath . $fileName);
                    $pathThumb = public_path($this->contactThumbImageUploadPath . $fileName);

                    Image::make($sp_photo->getRealPath())->save($pathOriginal);
                    Image::make($sp_photo->getRealPath())->resize($this->contactThumbImageWidth, $this->contactThumbImageHeight)->save($pathThumb);

                    if ($hiddenPhoto != '')
                    {
                        $originalImageDelete = $this->fileStorageRepository->deleteFileToStorage($hiddenPhoto, $this->contactOriginalImageUploadPath, "s3");
                        $thumbImageDelete = $this->fileStorageRepository->deleteFileToStorage($hiddenPhoto, $this->contactThumbImageUploadPath, "s3");
                    }
                    $originalImage = $this->fileStorageRepository->addFileToStorage($fileName, $this->contactOriginalImageUploadPath, $pathOriginal, "s3");
                    $thumbImage = $this->fileStorageRepository->addFileToStorage($fileName, $this->contactThumbImageUploadPath, $pathThumb, "s3");
                    \File::delete($this->contactOriginalImageUploadPath . $fileName);
                    \File::delete($this->contactThumbImageUploadPath . $fileName);
                    
                    $sponsorDetail['sp_photo'] = $fileName;
                }
            }
        }

        $response = $this->sponsorsRepository->saveSponsorDetail($sponsorDetail);
        if($response)
        {
            Helpers::createAudit($this->loggedInUser->user()->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_UPDATE'), Config::get('databaseconstants.TBL_SPONSORS'), $response, Config::get('constant.AUDIT_ORIGIN_WEB'),  trans('labels.sponsorupdatesuccess'), serialize($sponsorDetail), $_SERVER['REMOTE_ADDR']);
            return Redirect::to("admin/sponsors")->with('success',trans('labels.sponsorupdatesuccess'));
        }
        else
        {
            Helpers::createAudit($this->loggedInUser->user()->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_UPDATE'), Config::get('databaseconstants.TBL_SPONSORS'), $response, Config::get('constant.AUDIT_ORIGIN_WEB'),  trans('labels.somethingwrong'), serialize($sponsorDetail), $_SERVER['REMOTE_ADDR']);
            return Redirect::to("admin/sponsors")->with('error', trans('labels.commonerrormessage'));
        }

    }

    public function delete($id)
    {
        $teen = $this->sponsorsRepository->checkForSponsorToTeen($id);
        $coupon = $this->sponsorsRepository->checkForSponsorToCoupon($id);
        $sponsoractivity = $this->sponsorsRepository->checkForSponsorToSponsorActivity($id);
       
        if(empty($teen) && empty($coupon) && empty($sponsoractivity))
        {
            $return = $this->sponsorsRepository->deleteSponsor($id);
            if($return)
            {
                return Redirect::to("admin/sponsors")->with('success', trans('labels.sponsordeletesuccess'));
            }
            else
            {
                return Redirect::to("admin/sponsors")->with('error', trans('labels.commonerrormessage'));
            }
        }
        else
        {
            if(!empty($teen))
            {
                 return Redirect::to("admin/sponsors")->with('error', trans('labels.thissponsorisselectedbyteen'));
            }
            
            if(!empty($coupon))
            {
                return Redirect::to("admin/sponsors")->with('error', trans('labels.thissponsorisallocatedtocoupon'));
                
            }
            
            if(!empty($sponsoractivity))
            {
               
                return Redirect::to("admin/sponsors")->with('error', trans('labels.thissponsorisallocatedtosponsoractivity'));
            }
        }
//        if($return)
//        {
//            Helpers::createAudit($this->loggedInUser->user()->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_DELETE'), Config::get('databaseconstants.TBL_SPONSORS'), $id, Config::get('constant.AUDIT_ORIGIN_WEB'), trans('labels.sponsordeletesuccess'), '', $_SERVER['REMOTE_ADDR']);
//            return Redirect::to("admin/sponsors")->with('success', trans('labels.sponsordeletesuccess'));
//        }
//        else
//        {
//
//            Helpers::createAudit($this->loggedInUser->user()->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_DELETE'), Config::get('databaseconstants.TBL_SPONSORS'), $id, Config::get('constant.AUDIT_ORIGIN_WEB'),trans('labels.somethingwrong'), '', $_SERVER['REMOTE_ADDR']);
//            return Redirect::to("admin/sponsors")->with('error', trans('labels.commonerrormessage'));
//        }
    }

    public function editToApproved($id)
    {
        $return = $this->sponsorsRepository->editToApprovedSponser($id);
        if($return)
        {
            Helpers::createAudit($this->loggedInUser->user()->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_DELETE'), Config::get('databaseconstants.TBL_SPONSORS'), $id, Config::get('constant.AUDIT_ORIGIN_WEB'), trans('labels.sponsorapprovesuccess'), '', $_SERVER['REMOTE_ADDR']);
            $SponsorDetailbyId = $this->sponsorsRepository->getSponsorById($id);

//            $teenagers = $this->teenagersRepository->getAllActiveTeenagersForNotification();
//            foreach ($teenagers AS $key => $value) {
//                $message = 'New enterprise "' .$SponsorDetailbyId->sp_company_name.'" has been added into ProTeen!';
//                $return = Helpers::saveAllActiveTeenagerForSendNotifivation($value->id, $message);
//            }
                        // --------------------start sending mail -----------------------------//
                        $replaceArray = array();
                        $replaceArray['SPONSOR_NAME'] = $SponsorDetailbyId->sp_first_name;
                        $replaceArray['SPONSOR_LOGIN_URL'] = url('sponsor/login');

                        $emailTemplateContent = $this->templateRepository->getEmailTemplateDataByName(Config::get('constant.SPONSOR_VAIRIFIED_EMAIL_TEMPLATE_NAME'));
                        $content = $this->templateRepository->getEmailContent($emailTemplateContent->et_body, $replaceArray);
                        $data = array();
                        $data['subject'] = $emailTemplateContent->et_subject;
                        $data['toEmail'] = $SponsorDetailbyId->sp_email;
                        $data['toName'] = $SponsorDetailbyId->sp_first_name;
                        $data['content'] = $content;
                        Mail::send(['html' => 'emails.Template'], $data, function($message) use ($data) {
                                    $message->subject($data['subject']);
                                    $message->to($data['toEmail'], $data['toName']);
                                 });
            return Redirect::to("admin/sponsors")->with('success', trans('labels.sponsorapprovesuccess'));
        }
        else
        {

            Helpers::createAudit($this->loggedInUser->user()->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_DELETE'), Config::get('databaseconstants.TBL_SPONSORS'), $id, Config::get('constant.AUDIT_ORIGIN_WEB'),trans('labels.somethingwrong'), '', $_SERVER['REMOTE_ADDR']);
            return Redirect::to("admin/sponsors")->with('error', trans('labels.commonerrormessage'));
        }
    }
    
    public function viewSponsorActivity($id)
    {
        $sponsorsActivities = $this->sponsorsRepository->getActiveSponsorActivityDetail($id);
        $sponsorActivityThumbImageUploadPath = $this->sponsorActivityThumbImageUploadPath;
        return view('admin.ListSponsorActivity', compact('sponsorsActivities','sponsorActivityThumbImageUploadPath'));
    }
    
    public function sponsorActivity($id)
    {
        $sponsorsActivities = $this->sponsorsRepository->getActivityById($id);
        $sponsorActivityThumbImageUploadPath = $this->sponsorActivityThumbImageUploadPath;
        return view('admin.ViewSponsorActivity', compact('sponsorsActivities','sponsorActivityThumbImageUploadPath'));
    }
    
    public function editSponsorActivity($id)
    {
        $sponsorsActivities = $this->sponsorsRepository->getActivityById($id);
        $uploadSAThumbPath = $this->sponsorActivityThumbImageUploadPath;
        return view('admin.EditSponsorActivity',compact('sponsorsActivities','uploadSAThumbPath'));
    }
    
    public function saveSponsorActivity()
    {
        $response = '';
        $activityDetail = [];
        $activityDetail['id'] = e(input::get('id'));   
        $hiddenLogo     = e(input::get('hidden_logo'));
        $sponsorId = e(input::get('hidden_sponsor_id'));
        $activityDetail['sa_sponsor_id'] = $sponsorId;
        $activityDetail['sa_image']    = $hiddenLogo;
        $activityDetail['sa_type']    = e(input::get('sa_type'));
        $activityDetail['sa_name']   = e(input::get('sa_name'));
        $activityDetail['sa_apply_level'] =    e(input::get('sa_apply_level'));
        $activityDetail['sa_location']  = e(input::get('sa_location'));
        //$startdate = input::get('sa_start_date');
        if (Input::get('sa_start_date') != '') {
            $sdate = Input::get('sa_start_date');
            $startdate = str_replace('/', '-', $sdate);
            $activityDetail['sa_start_date'] = date("Y-m-d", strtotime($startdate));            
        }
        //$enddate = input::get('sa_end_date');
        if (Input::get('sa_end_date') != '') {
            $edate = Input::get('sa_end_date');
            $enddate = str_replace('/', '-', $edate);
            $activityDetail['sa_end_date'] = date("Y-m-d", strtotime($enddate));            
        }
        //$credit = e(input::get('creditdeducted'));
        //$totalCredit = Auth::sponsor()->get()->sp_credit;
        //$availableCredit = $totalCredit - $credit;
        //$arr[] = '';
        //$arr['id'] = Auth::sponsor()->get()->id;
        //$arr['sp_credit'] = $availableCredit;
        //$activityDetail['sa_credit_used'] = $credit;
        $activityDetail['deleted']   = e(input::get('status'));
        $file = Input::file('sa_image');
        if (!empty($file)) {
            $fileName = 'sponsoractivity_' . time() . '.' . $file->getClientOriginalExtension();                
            $width = Image::make($file->getRealPath())->width();
            $height = Image::make($file->getRealPath())->height();
            if($width != 730 && $height != 50 && $activityDetail['sa_type'] == 1)
            {
                //$id = $sponsorId;
                return Redirect::to("admin/edit-sponsor-activity/".$activityDetail['id'])->withErrors('Image width must be 730px and Height 50px')->withInput();
                exit;   
            }
            else{
                $pathOriginal = public_path($this->sponsorActivityOriginalImageUploadPath . $fileName);
                $pathThumb = public_path($this->sponsorActivityThumbImageUploadPath . $fileName);
                Image::make($file->getRealPath())->save($pathOriginal);
                Image::make($file->getRealPath())->resize($this->saThumbImageWidth, $this->saThumbImageHeight)->save($pathThumb);

                if ($hiddenLogo != '') {
                    $originalImageDelete = $this->fileStorageRepository->deleteFileToStorage($hiddenLogo, $this->sponsorActivityOriginalImageUploadPath, "s3");
                    $thumbImageDelete = $this->fileStorageRepository->deleteFileToStorage($hiddenLogo, $this->sponsorActivityThumbImageUploadPath, "s3");
                }
                //Uploading on AWS
                $originalImage = $this->fileStorageRepository->addFileToStorage($fileName, $this->sponsorActivityOriginalImageUploadPath, $pathOriginal, "s3");
                $thumbImage = $this->fileStorageRepository->addFileToStorage($fileName, $this->sponsorActivityThumbImageUploadPath, $pathThumb, "s3");
                //Deleting Local Files
                \File::delete($this->sponsorActivityOriginalImageUploadPath . $fileName);
                \File::delete($this->sponsorActivityThumbImageUploadPath . $fileName);
                $activityDetail['sa_image'] = $fileName;  
            }
        }
        $response = $this->sponsorsRepository->saveSponsorActivityDetail($activityDetail);
        //print_r($response);
        if($response)
        {
            $id = $sponsorId;
            return Redirect::to("/admin/sponsor-activity/$id")->with('success', trans('labels.sponsoractivityupdatesuccess'));
        }
        else
        {
            $id = $sponsorId;
            return Redirect::to("/admin/sponsor-activity/$id")->with('error', trans('labels.commonerrormessage'));
        }
    }
    
    public function exportsponsor() 
    {
        $searchParamArray = Input::all();
        $sponsors = $this->sponsorsRepository->getAllSponsors($searchParamArray);
        ob_start();
        
        $filename = 'Sponsor_Data.csv';
        $fp = fopen('php://output', 'w');
        $FieldArray = [];
        $FieldArray['sp_email'] = 'Email';
        $FieldArray['sp_company_name'] = 'Company Name';
        $FieldArray['sp_admin_name'] = 'Admin Name';
        $FieldArray['sp_address1'] = 'Address 1';
        $FieldArray['sp_address2'] = 'Address 2';
        $FieldArray['sp_pincode'] = 'ZipCode';        
        $FieldArray['sp_city'] = 'City';        
        $FieldArray['sp_state'] = 'State';
        $FieldArray['sp_country'] = 'Country';
        $FieldArray['sp_first_name'] = 'First Name';
        $FieldArray['sp_last_name'] = 'Last Name';
        $FieldArray['sp_title'] = 'Title';
        $FieldArray['sp_phone'] = 'Phone';
        $FieldArray['sp_credit'] = 'Available Credit';
        $FieldArray['sp_isapproved'] = 'Approved Status';
        $FieldArray['deleted'] = 'Active Status';
        
        fputcsv($fp, $FieldArray);
        foreach ($sponsors as $key => $sponsor) {  
            $sponsorDetail = $this->sponsorsRepository->getSponsorById($sponsor->id);
            
            $FieldArray = [];
            $FieldArray['sp_email'] = $sponsor->sp_email;
            $FieldArray['sp_company_name'] = $sponsor->sp_company_name;
            $FieldArray['sp_admin_name'] = $sponsor->sp_admin_name;
            $FieldArray['sp_address1'] = $sponsor->sp_address1;
            $FieldArray['sp_address2'] = $sponsor->sp_address2;
            $FieldArray['sp_pincode'] = $sponsor->sp_pincode;
            $FieldArray['sp_city'] = $sponsorDetail->city;
            $FieldArray['sp_state'] = $sponsorDetail->s_name;
            $FieldArray['sp_country'] = $sponsorDetail->c_name;
            $FieldArray['sp_first_name'] = $sponsor->sp_first_name;
            $FieldArray['sp_last_name'] = $sponsor->sp_last_name;
            $FieldArray['sp_title'] = $sponsor->sp_title;
            $FieldArray['sp_phone'] = $sponsor->sp_phone;
            $FieldArray['sp_credit'] = $sponsor->sp_credit;

            if ($sponsor->sp_isapproved == '1') {
                $verified_status = 'Yes';
            } else {
                $verified_status = 'No';
            }
            $FieldArray['sp_isapproved'] = $verified_status;

            if ($sponsor->deleted == '1') {
                $active_status = 'Yes';
            } else if (($sponsor->deleted == '2')) {
                $active_status = 'No';
            }
            $FieldArray['deleted'] = $active_status;
            fputcsv($fp, $FieldArray);
        }
        header('Content-type: application/csv');
        header('Content-Disposition: attachment; filename=' . $filename);
        exit;
    }
    public function addCoinsDataForSponsor() {
        $sponsor_Id = $_REQUEST['sponsorid'];
        $sponsorDetail = $this->objSponsors->find($sponsor_Id);
        return view('admin.AddCoinsDataForSponsor',compact('sponsorDetail'));
    }

    public function saveCoinsDataForSponsor() {

        $id = e(Input::get('id'));
        $coins = e(Input::get('sp_credit'));
        $giftCoins = e(Input::get('sp_credit'));
        $flag = 0;
        $sponsorData = $this->sponsorsRepository->getSponsorDataForCoinsDetail($id);
        if (!empty($sponsorData)) {
            if (substr($coins, 0, 1) === '-') {
                $coins = preg_replace('/[-?]/', '', $coins);
                if ($sponsorData['sp_credit'] > 0 && $coins <= $sponsorData['sp_credit']) {
                    $coins = $sponsorData['sp_credit']-$coins;
                } else {
                    return Redirect::to("admin/schools")->with('error', trans('labels.commonerrormessage'));
                }
            } else if (is_numeric($coins)) {
                $coins += $sponsorData['sp_credit'];
                $flag++;
            }
        }
        $result = $this->sponsorsRepository->updateSponsorCoinsDetail($id, $coins);
        $userArray = $this->sponsorsRepository->getSponsorBySponsorId($id);
        $objGiftUser = new TeenagerCoinsGift();
        if($flag) {
            $saveData = [];
            $saveData['tcg_sender_id'] = 0;
            $saveData['tcg_reciver_id'] = $id;
            $saveData['tcg_total_coins'] = $giftCoins;
            $saveData['tcg_gift_date'] = date('Y-m-d');
            $saveData['tcg_user_type'] = 4;

            $return = $objGiftUser->saveTeenagetGiftCoinsDetail($saveData);

            $replaceArray = array();
            $replaceArray['TEEN_NAME'] = $userArray[0]['sp_admin_name'];
            $replaceArray['COINS'] = $giftCoins;
            $replaceArray['FROM_USER'] = $this->loggedInUser->user()->name;
            $emailTemplateContent = $this->templateRepository->getEmailTemplateDataByName(Config::get('constant.COINS_RECEIBED_TEMPLATE'));
            $content = $this->templateRepository->getEmailContent($emailTemplateContent->et_body, $replaceArray);

            $data = array();
            $data['subject'] = $emailTemplateContent->et_subject;
            $data['toEmail'] = $userArray[0]['sp_email'];
            $data['toName'] = $userArray[0]['sp_admin_name'];
            $data['content'] = $content;

            Mail::send(['html' => 'emails.Template'], $data , function ($m) use ($data) {
                $m->from(Config::get('constant.FROM_MAIL_ID'), 'Gift ProCoins');
                $m->subject($data['subject']);
                $m->to($data['toEmail'], $data['toName']);
            });
        }

        return Redirect::to("admin/sponsors")->with('success', trans('labels.coinsaddsuccess'));
    }
}