<?php

namespace App\Http\Controllers\Admin;

use Auth;
use Input;
use Config;
use File;
use Image;
use Helpers;
use Redirect;
use Illuminate\Pagination\Paginator;
use App\Parents;
use App\Http\Controllers\Controller;
use App\Http\Requests\ParentRequest;
use App\Services\Parents\Contracts\ParentsRepository;
use App\Services\Teenagers\Contracts\TeenagersRepository;
use App\Services\Template\Contracts\TemplatesRepository;
use App\TeenagerCoinsGift;
use Mail;
use App\Services\FileStorage\Contracts\FileStorageRepository;

class ParentManagementController extends Controller {

    public function __construct(FileStorageRepository $fileStorageRepository, ParentsRepository $parentsRepository, TeenagersRepository $teenagersRepository, TemplatesRepository $templatesRepository) {
        $this->objParents = new Parents();
        $this->parentsRepository = $parentsRepository;
        $this->teenagersRepository = $teenagersRepository;
        $this->templateRepository = $templatesRepository;
        $this->controller = 'ParentManagementController';
        $this->loggedInUser = Auth::guard('admin');
        $this->fileStorageRepository = $fileStorageRepository;
        $this->parentOriginalImageUploadPath = Config::get('constant.PARENT_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->parentThumbImageUploadPath = Config::get('constant.PARENT_THUMB_IMAGE_UPLOAD_PATH');
        $this->parentThumbImageHeight = Config::get('constant.PARENT_THUMB_IMAGE_HEIGHT');
        $this->parentThumbImageWidth = Config::get('constant.PARENT_THUMB_IMAGE_WIDTH');
    }

    public function index($type) {
        $searchParamArray = [];
        $parents = $this->parentsRepository->getAllParents($searchParamArray, $type);
        //Helpers::createAudit($this->loggedInUser->user()->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_READ'), $this->controller . "@index", $_SERVER['REQUEST_URI'], Config::get('constant.AUDIT_ORIGIN_WEB'), '', '', $_SERVER['REMOTE_ADDR']);
        return view('admin.ListParent', compact('parents', 'type'));
    }

    public function add() {
        $parentDetail = [];
        $cities = [];
        $states = [];
        //Helpers::createAudit($this->loggedInUser->user()->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_READ'), $this->controller . "@add", $_SERVER['REQUEST_URI'], Config::get('constant.AUDIT_ORIGIN_WEB'), '', '', $_SERVER['REMOTE_ADDR']);

        return view('admin.EditParent', compact('parentDetail','states','cities'));
    }

    public function edit($id) {
        $parentDetail = $this->objParents->find($id);
        $tokenDetail = $this->objParents->getUniqueId($id);
        $uploadParentThumbPath = $this->parentThumbImageUploadPath;
        //Helpers::createAudit($this->loggedInUser->user()->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_READ'), $this->controller . "@edit", $_SERVER['REQUEST_URI'], Config::get('constant.AUDIT_ORIGIN_WEB'), '', '', $_SERVER['REMOTE_ADDR']);
        $states =  Helpers::getStates($parentDetail->p_country);
        $cities =  Helpers::getCities($parentDetail->p_state);
        return view('admin.EditParent', compact('parentDetail', 'uploadParentThumbPath', 'tokenDetail','states','cities'));
    }

    public function save(ParentRequest $ParentRequest) {
        $parentDetail = [];
        $parentDetail['id'] = e(input::get('id'));
        $parentDetail['p_uniqueid'] = e(input::get('p_uniqueid'));
        $hiddenPhoto = e(input::get('hidden_photo'));
        $parentDetail['p_photo'] = $hiddenPhoto;
        $parentDetail['p_first_name'] = e(input::get('p_first_name'));
        $parentDetail['p_last_name'] = e(input::get('p_last_name'));
        $parentDetail['p_address1'] = e(input::get('p_address1'));
        $parentDetail['p_address2'] = e(input::get('p_address2'));
        $parentDetail['p_pincode'] = e(input::get('p_pincode'));
        $parentDetail['p_country'] = e(input::get('p_country'));
        $parentDetail['p_state'] = e(input::get('p_state'));
        $parentDetail['p_city'] = e(input::get('p_city'));
        $parentDetail['p_gender'] = e(input::get('p_gender'));
        $parentDetail['p_email'] = e(input::get('p_email'));
        $parentDetail['p_user_type'] = e(input::get('p_user_type'));
        $hiddenPassword = e(Input::get('hidden_password'));
        $password = e(Input::get('password'));
        $confirm_password = e(Input::get('confirm_password'));
        //$parentDetail['p_teenager_id']   = e(input::get('p_teenager_id'));        
        $parentDetail['deleted'] = e(input::get('deleted'));

        if ($hiddenPassword != '' && $password == '') {
            $parentDetail['password'] = $hiddenPassword;
        } else {
            $parentDetail['password'] = bcrypt($password);
        }
        if (Input::file()) {
            $file = Input::file('p_photo');

            if (!empty($file)) {
                //Check image valid extension 
                $validationPass = Helpers::checkValidImageExtension($file);
                if($validationPass)
                {
                    $fileName = 'parent_' . time() . '.' . $file->getClientOriginalExtension();
                    $pathOriginal = public_path($this->parentOriginalImageUploadPath . $fileName);
                    $pathThumb = public_path($this->parentThumbImageUploadPath . $fileName);

                    Image::make($file->getRealPath())->save($pathOriginal);
                    Image::make($file->getRealPath())->resize($this->parentThumbImageWidth, $this->parentThumbImageHeight)->save($pathThumb);

                    if ($hiddenPhoto != '') {
                        $originalImageDelete = $this->fileStorageRepository->deleteFileToStorage($hiddenPhoto, $this->parentOriginalImageUploadPath, "s3");
                        $thumbImageDelete = $this->fileStorageRepository->deleteFileToStorage($hiddenPhoto, $this->parentOriginalImageUploadPath, "s3");
                    }

                    $originalImage = $this->fileStorageRepository->addFileToStorage($fileName, $this->parentOriginalImageUploadPath, $pathOriginal, "s3");
                    $thumbImage = $this->fileStorageRepository->addFileToStorage($fileName, $this->parentThumbImageUploadPath, $pathThumb, "s3");
                    \File::delete($this->parentOriginalImageUploadPath . $fileName);
                    \File::delete($this->parentThumbImageUploadPath . $fileName);

                    $parentDetail['p_photo'] = $fileName;
                }
            } 
        }

        $response = $this->parentsRepository->saveParentDetail($parentDetail);
        if ($response) {
            Helpers::createAudit($this->loggedInUser->user()->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_UPDATE'), Config::get('databaseconstants.TBL_PARENTS'), $response, Config::get('constant.AUDIT_ORIGIN_WEB'), trans('labels.parentupdatesuccess'), serialize($parentDetail), $_SERVER['REMOTE_ADDR']);
            if ($parentDetail['p_user_type'] == 1) {
                return Redirect::to("admin/parents/1")->with('success', trans('labels.parentupdatesuccess'));
            } else {
                return Redirect::to("admin/counselors/2")->with('success', trans('labels.counselorupdatesuccess'));
            }
        } else {
            //Helpers::createAudit($this->loggedInUser->user()->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_UPDATE'), Config::get('databaseconstants.TBL_PARENTS'), $response, Config::get('constant.AUDIT_ORIGIN_WEB'), trans('labels.somethingwrong'), serialize($parentDetail), $_SERVER['REMOTE_ADDR']);
            return Redirect::to("admin/parents/1")->with('error', trans('labels.commonerrormessage'));
        }
    }

    public function delete($id, $type) {
        $return = $this->parentsRepository->deleteParent($id, $type);
        if ($return) {
            Helpers::createAudit($this->loggedInUser->user()->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_DELETE'), Config::get('databaseconstants.TBL_PARENTS'), $id, Config::get('constant.AUDIT_ORIGIN_WEB'), trans('labels.parentdeletesuccess'), '', $_SERVER['REMOTE_ADDR']);
            if ($type == 1) {
                return Redirect::to("admin/parents/1")->with('success', trans('labels.parentdeletesuccess'));
            } else {
                return Redirect::to("admin/counselors/2")->with('success', trans('labels.counselordeletesuccess'));
            }
        } else {
            Helpers::createAudit($this->loggedInUser->user()->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_DELETE'), Config::get('databaseconstants.TBL_PARENTS'), $id, Config::get('constant.AUDIT_ORIGIN_WEB'), trans('labels.somethingwrong'), '', $_SERVER['REMOTE_ADDR']);
            return Redirect::to("admin/parents/1")->with('error', trans('labels.commonerrormessage'));
        }
    }

    public function viewparentteen($id) {
        $teenagersIds = $this->parentsRepository->getAllVerifiedTeenagers($id);
        $parentDetail = $this->parentsRepository->getParentById($id);
        $final = array();
        if (isset($teenagersIds) && !empty($teenagersIds)) {
            foreach ($teenagersIds as $key => $data) {
                $checkuserexist = $this->teenagersRepository->checkActiveTeenager($data->ptp_teenager);
                if (isset($checkuserexist) && $checkuserexist) {
                    $teengersDetail = $this->teenagersRepository->getTeenagerById($data->ptp_teenager);
                    $teengersBooster = $this->teenagersRepository->getTeenagerBoosterPoints($data->ptp_teenager);
                    $final[] = $teengersDetail;
                }
            }
        }        
        return view('admin.ListParentTeens', compact('final','parentDetail'));               
    }
    
    public function exportparent($type) {
        $searchParamArray = Input::all();
        $parents = $this->parentsRepository->getAllParents($searchParamArray, $type);
        ob_start();
        
        $filename = ($type == 1)?'Parent_Data.csv':'Counsellor_Data.csv';
        $fp = fopen('php://output', 'w');
        $FieldArray = [];
        $FieldArray['p_first_name'] = 'First Name';
        $FieldArray['p_last_name'] = 'Last Name';
        $FieldArray['p_address1'] = 'Address 1';
        $FieldArray['p_address2'] = 'Address 2';        
        $FieldArray['p_pincode'] = 'ZipCode';        
        $FieldArray['p_city'] = 'City';        
        $FieldArray['p_state'] = 'State';
        $FieldArray['p_country'] = 'Country';
        $FieldArray['p_gender'] = 'Gender';
        $FieldArray['p_email'] = 'Email';
        $FieldArray['associated_teens'] = 'Associated Teens';
        $FieldArray['p_isverified'] = 'Verified Status';
        $FieldArray['deleted'] = 'Active Status';
        
        fputcsv($fp, $FieldArray);
        foreach ($parents as $key => $parent) {  
            $parentDetail = $this->parentsRepository->getParentById($parent->id);
            
            //Get associated teens of parent
            $teenagersIds = $this->parentsRepository->getAllVerifiedTeenagers($parent->id);
            $final = array();
            $teens = '';
            if (isset($teenagersIds) && !empty($teenagersIds)) {
                foreach ($teenagersIds as $key => $data) {
                    $checkuserexist = $this->teenagersRepository->checkActiveTeenager($data->ptp_teenager);
                    if (isset($checkuserexist) && $checkuserexist) {
                        $teengersDetail = $this->teenagersRepository->getTeenagerById($data->ptp_teenager);
                        $teens .= $teengersDetail->t_name.'--'.$teengersDetail->t_email."\n";
                    }
                }
            }else{
                $teens = 'None';
            }
           
            $FieldArray = [];
            $FieldArray['p_first_name'] = $parent->p_first_name;
            $FieldArray['p_last_name'] = $parent->p_last_name;
            $FieldArray['p_address1'] = $parent->p_address1;
            $FieldArray['p_address2'] = $parent->p_address2;
            $FieldArray['p_pincode'] = $parent->p_pincode;
            $FieldArray['p_city'] = $parentDetail->city;
            $FieldArray['p_state'] = $parentDetail->s_name;
            $FieldArray['p_country'] = $parentDetail->c_name;
            $gender = '';
            if ($parent->p_gender == '1') {
                $gender = 'Male';
            } else if (($parent->p_gender == '2')) {
                $gender = 'Female';
            }
            $FieldArray['p_gender'] = $gender;            
            $FieldArray['p_email'] = $parent->p_email;                       
            $FieldArray['associated_teens'] = $teens;                       
            
            if ($parent->p_isverified == '1') {
                $verified_status = 'Yes';
            } else {
                $verified_status = 'No';
            }
            $FieldArray['p_isverified'] = $verified_status;

            if ($parent->deleted == '1') {
                $active_status = 'Yes';
            } else if (($parent->deleted == '2')) {
                $active_status = 'No';
            }
            $FieldArray['deleted'] = $active_status;
            fputcsv($fp, $FieldArray);
        }
        header('Content-type: application/csv');
        header('Content-Disposition: attachment; filename=' . $filename);
        exit;
    }

    public function addCoinsDataForParent() {
        $parent_Id = $_REQUEST['parentid'];
        $type = $_REQUEST['typeData'];
        $parentDetail = $this->objParents->find($parent_Id);

        return view('admin.AddCoinsDataForParent',compact('parentDetail', 'type'));
    }

    public function saveCoinsDataForParent() {

        $id = e(Input::get('id'));
        $coins = e(Input::get('p_coins'));
        $giftCoins = e(Input::get('p_coins'));
        $type = e(Input::get('type'));
        $flag = 0;
        $parentDetail = $this->parentsRepository->getParentDataForCoinsDetail($id);
        if (!empty($parentDetail)) {
            if (substr($coins, 0, 1) === '-') {
                $coins = preg_replace('/[-?]/', '', $coins);
                if ($parentDetail['p_coins'] > 0 && $coins <= $parentDetail['p_coins']) {
                    $coins = $parentDetail['p_coins']-$coins;
                } else {
                    if ($type == 1){
                        return Redirect::to("admin/parents/1")->with('error', trans('labels.commonerrormessage'));
                    } else {
                        return Redirect::to("admin/counselors/2")->with('error', trans('labels.commonerrormessage'));
                    }
                }
            } else if (is_numeric($coins)) {
                $coins += $parentDetail['p_coins'];
                $flag++;
            }
        }
        $result = $this->parentsRepository->updateParentCoinsDetail($id, $coins);
        $userArray = $this->parentsRepository->getParentDetailByParentId($id);
        $objGiftUser = new TeenagerCoinsGift();
        if($flag) {
            $saveData = [];
            $saveData['tcg_sender_id'] = 0;
            $saveData['tcg_reciver_id'] = $id;
            $saveData['tcg_total_coins'] = $giftCoins;
            $saveData['tcg_gift_date'] = date('Y-m-d');
            $saveData['tcg_user_type'] = 2;

            $return = $objGiftUser->saveTeenagetGiftCoinsDetail($saveData);

            $replaceArray = array();
            $replaceArray['TEEN_NAME'] = $userArray['p_first_name'];
            $replaceArray['COINS'] = $giftCoins;
            $replaceArray['FROM_USER'] = $this->loggedInUser->user()->name;
            $emailTemplateContent = $this->templateRepository->getEmailTemplateDataByName(Config::get('constant.COINS_RECEIBED_TEMPLATE'));
            $content = $this->templateRepository->getEmailContent($emailTemplateContent->et_body, $replaceArray);

            $data = array();
            $data['subject'] = $emailTemplateContent->et_subject;
            $data['toEmail'] = $userArray['p_email'];
            $data['toName'] = $userArray['p_first_name'];
            $data['content'] = $content;

            Mail::send(['html' => 'emails.Template'], $data , function ($m) use ($data) {
                $m->from(Config::get('constant.FROM_MAIL_ID'), 'Gift ProCoins');
                $m->subject($data['subject']);
                $m->to($data['toEmail'], $data['toName']);
            });
        }
        if ($type == 1){
            return Redirect::to("admin/parents/1")->with('success', trans('labels.coinsaddsuccess'));
        } else {
            return Redirect::to("admin/counselors/2")->with('success', trans('labels.coinsaddsuccess'));
        }
    }
}
