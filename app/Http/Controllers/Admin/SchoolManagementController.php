<?php

namespace App\Http\Controllers\Admin;

use Auth;
use Input;
use File;
use Image;
use Config;
use Helpers;
use Redirect;
use Illuminate\Pagination\Paginator;
use App\Schools;
use App\Http\Controllers\Controller;
use App\Http\Requests\SchoolRequest;
use App\Services\Template\Contracts\TemplatesRepository;
use App\Services\Schools\Contracts\SchoolsRepository;
use App\Services\Teenagers\Contracts\TeenagersRepository;
use App\Services\FileStorage\Contracts\FileStorageRepository;
use Mail;
use App\TeenagerCoinsGift;
use Illuminate\Support\Facades\Storage;

class SchoolManagementController extends Controller
{
    public function __construct(FileStorageRepository $fileStorageRepository, SchoolsRepository $schoolsRepository, TemplatesRepository $templatesRepository, TeenagersRepository $teenagersRepository)
    {
        $this->objSchools                = new Schools();
        $this->schoolsRepository         = $schoolsRepository;
        $this->teenagersRepository       = $teenagersRepository;
        $this->templateRepository = $templatesRepository;
        $this->fileStorageRepository = $fileStorageRepository;
        $this->schoolOriginalImageUploadPath = Config::get('constant.SCHOOL_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->schoolThumbImageUploadPath = Config::get('constant.SCHOOL_THUMB_IMAGE_UPLOAD_PATH');
        $this->schoolThumbImageHeight = Config::get('constant.SCHOOL_THUMB_IMAGE_HEIGHT');
        $this->schoolThumbImageWidth = Config::get('constant.SCHOOL_THUMB_IMAGE_WIDTH');
        $this->schoolOriginalPhotoImageUploadPath = Config::get('constant.CONTACT_PERSON_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->schoolThumbPhotoImageUploadPath = Config::get('constant.CONTACT_PERSON_THUMB_IMAGE_UPLOAD_PATH');
        $this->schoolThumbPhotoImageHeight = Config::get('constant.CONTACT_PERSON_THUMB_IMAGE_HEIGHT');
        $this->schoolThumbPhotoImageWidth = Config::get('constant.CONTACT_PERSON_THUMB_IMAGE_WIDTH');
        $this->controller = 'SchoolManagementController';
        $this->loggedInUser = Auth::guard('admin');
    }

    public function index()
    {
        return view('admin.ListSchool');
    }

    public function getIndex() {
        $schools = $this->schoolsRepository->getAllSchoolsDataObj()->get()->count();
        $records = array();
        $columns = array(
            0 => 'id',
            1 => 'sc_name',
            2 => 'sc_email',
            3 => 'sc_phone',
            5 => 'sc_isapproved',
            9 => 'created_at'
        );
        $order = Input::get('order');
        $search = Input::get('search');
        $records["data"] = array();
        $iTotalRecords = $schools;
        $iTotalFiltered = $iTotalRecords;
        $iDisplayLength = intval(Input::get('length')) <= 0 ? $iTotalRecords : intval(Input::get('length'));
        $iDisplayStart = intval(Input::get('start'));
        $sEcho = intval(Input::get('draw'));

        $records["data"] = $this->schoolsRepository->getAllSchoolsDataObj();
        if (!empty($search['value'])) {
            $val = $search['value'];
            $records["data"]->where(function($query) use ($val) {
                $query->where('school.sc_name', "Like", "%$val%");
                $query->orWhere('school.sc_phone', "Like", "%$val%");
                $query->orWhere('school.sc_email', "Like", "%$val%");
            });

            // No of record after filtering
            $iTotalFiltered = $records["data"]->where(function($query) use ($val) {
                    $query->where('school.sc_name', "Like", "%$val%");
                    $query->orWhere('school.sc_phone', "Like", "%$val%");
                    $query->orWhere('school.sc_email', "Like", "%$val%");
                })->count();
        }
        //order by
        foreach ($order as $o) {
            $records["data"] = $records["data"]->orderBy($columns[$o['column']], $o['dir']);
        }
        //limit
        $records["data"] = $records["data"]->take($iDisplayLength)->offset($iDisplayStart)->get();
        
        if (!empty($records["data"])) {
            foreach ($records["data"] as $key => $_records) {
                $records["data"][$key]->action = '<a href="'.url('/admin/edit-school').'/'.$_records->id.'"><i class="fa fa-edit"></i> &nbsp;&nbsp;</a><a onclick="return confirm(\'Are you sure you want to delete ?\');" href="'.url('/admin/delete-school').'/'.$_records->id.'"><i class="i_delete fa fa-trash"></i> &nbsp;&nbsp;</a><a target="_blank" href="'.url('/admin/view-student-list').'/'.$_records->id.'"><i class="fa fa-eye"></i>&nbsp;&nbsp;</a><a href="" onClick="add_coins_details('.$_records->id.');" data-toggle="modal" id="#schoolCoinsData" data-target="#schoolCoinsData"><i class="fa fa-database" aria-hidden="true"></i></a>';
                $records["data"][$key]->deleted = ($_records->deleted == 1) ? "<i class='s_active fa fa-square'></i>" : "<i class='s_inactive fa fa-square'></i>";
                $records["data"][$key]->sc_isapproved = ($_records->sc_isapproved == 1) ? "Yes" : '<a onclick="return confirm(\'Are you sure you want to Approve  ?\')" href="'.url('/admin/edit-school-approved').'/'.$_records->id.'" class="btn btn-primary btn-xs">Approve</a>';
                $records["data"][$key]->sc_logo = ($_records->sc_logo != '' && Storage::disk('s3')->exists($this->schoolThumbImageUploadPath . $_records->sc_logo) ) ? '<img src="'.Config::get('constant.DEFAULT_AWS').$this->schoolThumbImageUploadPath.$_records->sc_logo.'" height="'.Config::get('constant.DEFAULT_IMAGE_HEIGHT').'" width="'.Config::get('constant.DEFAULT_IMAGE_WIDTH').'">' : '<img src="'.asset('/backend/images/proteen_logo.png').'" class="user-image" alt="Default Image" height="'.Config::get('constant.DEFAULT_IMAGE_HEIGHT').'" width="'.Config::get('constant.DEFAULT_IMAGE_WIDTH').'">';
                $records["data"][$key]->created_at = date('d/m/Y',strtotime($_records->created_at));
            }
        }

        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalFiltered;
        
        return \Response::json($records);
        exit;
    }
    public function add()
    {
        $schoolDetail =[];
        Helpers::createAudit($this->loggedInUser->user()->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_READ'), $this->controller . "@add", $_SERVER['REQUEST_URI'], Config::get('constant.AUDIT_ORIGIN_WEB'), '', '', $_SERVER['REMOTE_ADDR']);
        $states = [];
        $cities = [];
        return view('admin.EditSchool', compact('schoolDetail','cities','states'));
    }

    public function edit($id)
    {
        $schoolDetail = $this->objSchools->find($id);
        $uploadSchoolThumbPath = $this->schoolThumbImageUploadPath;
        $uploadSchoolPhotoThumbPath = $this->schoolThumbPhotoImageUploadPath;
        Helpers::createAudit($this->loggedInUser->user()->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_READ'), $this->controller . "@edit", $_SERVER['REQUEST_URI'], Config::get('constant.AUDIT_ORIGIN_WEB'), '', '', $_SERVER['REMOTE_ADDR']);
        $states =  Helpers::getStates($schoolDetail->sc_country);
        $cities = Helpers::getCities($schoolDetail->sc_state);
        return view('admin.EditSchool', compact('schoolDetail', 'uploadSchoolThumbPath','uploadSchoolPhotoThumbPath','cities','states'));
    }

    public function save(SchoolRequest $schoolRequest)
    {
        $schoolDetail = [];

        $schoolDetail['id']   = e(input::get('id'));
        $hiddenLogo     = e(input::get('hidden_logo'));
        $hiddenPhoto     = e(input::get('hidden_photo'));
        $schoolDetail['sc_logo']    = $hiddenLogo;
        $schoolDetail['sc_photo']    = $hiddenPhoto;
        $schoolDetail['sc_name']   = e(input::get('sc_name'));
        $schoolDetail['sc_email'] =    e(input::get('sc_email'));
        $schoolDetail['sc_address1'] =    e(input::get('sc_address1'));
        $schoolDetail['sc_address2'] =    e(input::get('sc_address2'));
        $schoolDetail['sc_pincode'] =    e(input::get('sc_pincode'));
        $schoolDetail['sc_city'] =    e(input::get('sc_city'));
        $schoolDetail['sc_state'] =    e(input::get('sc_state'));
        $hiddenPassword = e(Input::get('hidden_password'));
        $password = e(Input::get('password'));
        $confirm_password       = e(Input::get('confirm_password'));
        $schoolDetail['sc_phone']  = e(input::get('sc_phone'));
        $schoolDetail['sc_country'] = e(input::get('sc_country'));
        $schoolDetail['sc_first_name'] = e(input::get('sc_first_name'));
        $schoolDetail['sc_last_name'] = e(input::get('sc_last_name'));
        $schoolDetail['sc_title'] = e(input::get('sc_title'));
        $schoolDetail['sc_isapproved']  = e(input::get('sc_isapproved'));
        $schoolDetail['deleted']   = e(input::get('deleted'));
        $schoolDetail['sc_uniqueid']   = e(input::get('sc_uniqueid'));
        if($hiddenPassword != '' && $password == '')
        {
            $schoolDetail['password']   = $hiddenPassword;
        }
        else
        {
            $schoolDetail['password']   = bcrypt($password);
        }
        if (Input::file())
        {
            $file_logo = Input::file('sc_logo');
            if(!empty($file_logo))
            {
                //Check image valid extension
                $validationPass = Helpers::checkValidImageExtension($file_logo);
                if($validationPass)
                {
                    $fileName = 'school_' . time() . '.' . $file_logo->getClientOriginalExtension();
                    $pathOriginal = public_path($this->schoolOriginalImageUploadPath . $fileName);
                    $pathThumb = public_path($this->schoolThumbImageUploadPath . $fileName);
                    Image::make($file_logo->getRealPath())->save($pathOriginal);
                    Image::make($file_logo->getRealPath())->resize($this->schoolThumbImageWidth, $this->schoolThumbImageHeight)->save($pathThumb);

                    if ($hiddenLogo != '')
                    {
                        $originalImageDelete = $this->fileStorageRepository->deleteFileToStorage($hiddenLogo, $this->schoolOriginalImageUploadPath, "s3");
                        $thumbImageDelete = $this->fileStorageRepository->deleteFileToStorage($hiddenLogo, $this->schoolThumbImageUploadPath, "s3");
                    }
                    //Uploading on AWS
                    $originalImage = $this->fileStorageRepository->addFileToStorage($fileName, $this->schoolOriginalImageUploadPath, $pathOriginal, "s3");
                    $thumbImage = $this->fileStorageRepository->addFileToStorage($fileName, $this->schoolThumbImageUploadPath, $pathThumb, "s3");
                    //Deleting Local Files
                    \File::delete($this->schoolOriginalImageUploadPath . $fileName);
                    \File::delete($this->schoolThumbImageUploadPath . $fileName);

                    $schoolDetail['sc_logo'] = $fileName;
                }
            }
        }

        if (Input::file())
        {
            $file_photo = Input::file('sc_photo');
            if(!empty($file_photo))
            {
                //Check image valid extension
                $validationPass = Helpers::checkValidImageExtension($file_photo);
                if($validationPass)
                {
                    $fileName = 'school_' . time() . '.' . $file_photo->getClientOriginalExtension();
                    $pathOriginal = public_path($this->schoolOriginalPhotoImageUploadPath . $fileName);
                    $pathThumb = public_path($this->schoolThumbPhotoImageUploadPath . $fileName);

                    Image::make($file_photo->getRealPath())->save($pathOriginal);
                    Image::make($file_photo->getRealPath())->resize($this->schoolThumbPhotoImageWidth, $this->schoolThumbPhotoImageHeight)->save($pathThumb);

                    if ($hiddenPhoto != '')
                    {
                        $imageOriginalDelete = $this->fileStorageRepository->deleteFileToStorage($hiddenPhoto, $this->schoolOriginalPhotoImageUploadPath, "s3");
                        $imageThumbDelete = $this->fileStorageRepository->deleteFileToStorage($hiddenPhoto, $this->schoolThumbPhotoImageUploadPath, "s3");
                    }
                    //Uploading on AWS
                    $originalImage = $this->fileStorageRepository->addFileToStorage($fileName, $this->schoolOriginalPhotoImageUploadPath, $pathOriginal, "s3");
                    $thumbImage = $this->fileStorageRepository->addFileToStorage($fileName, $this->schoolThumbPhotoImageUploadPath, $pathThumb, "s3");
                    //Deleting Local Files
                    \File::delete($this->schoolOriginalPhotoImageUploadPath . $fileName);
                    \File::delete($this->schoolThumbPhotoImageUploadPath . $fileName);

                    $schoolDetail['sc_photo'] = $fileName;
                }
            }
        }
        $response = $this->schoolsRepository->saveSchoolDetail($schoolDetail);
        if($response)
        {
            Helpers::createAudit($this->loggedInUser->user()->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_UPDATE'), Config::get('databaseconstants.TBL_SCHOOLS'), $response, Config::get('constant.AUDIT_ORIGIN_WEB'),  trans('labels.schoolupdatesuccess'), serialize($schoolDetail), $_SERVER['REMOTE_ADDR']);
            return Redirect::to("admin/schools")->with('success',trans('labels.schoolupdatesuccess'));
        }
        else
        {
            Helpers::createAudit($this->loggedInUser->user()->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_UPDATE'), Config::get('databaseconstants.TBL_SCHOOLS'), $response, Config::get('constant.AUDIT_ORIGIN_WEB'),  trans('labels.somethingwrong'), serialize($schoolDetail), $_SERVER['REMOTE_ADDR']);
            return Redirect::to("admin/schools")->with('error', trans('labels.commonerrormessage'));
        }
    }

    public function delete($id)
    {
        $return = $this->schoolsRepository->deleteSchool($id);
        if($return)
        {
            Helpers::createAudit($this->loggedInUser->user()->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_DELETE'), Config::get('databaseconstants.TBL_SCHOOLS'), $id, Config::get('constant.AUDIT_ORIGIN_WEB'), trans('labels.schooldeletesuccess'), '', $_SERVER['REMOTE_ADDR']);
            return Redirect::to("admin/schools")->with('success', trans('labels.schooldeletesuccess'));
        }
        else
        {

            Helpers::createAudit($this->loggedInUser->user()->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_DELETE'), Config::get('databaseconstants.TBL_SCHOOLS'), $id, Config::get('constant.AUDIT_ORIGIN_WEB'),trans('labels.somethingwrong'), '', $_SERVER['REMOTE_ADDR']);
            return Redirect::to("admin/schools")->with('error', trans('labels.commonerrormessage'));
        }
    }

    public function editToApproved($id)
    {
        $return = $this->schoolsRepository->editToApprovedSchool($id);
        if($return)
        {
            Helpers::createAudit($this->loggedInUser->user()->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_DELETE'), Config::get('databaseconstants.TBL_SCHOOLS'), $id, Config::get('constant.AUDIT_ORIGIN_WEB'), trans('labels.schoolapprovesuccess'), '', $_SERVER['REMOTE_ADDR']);
            $SchoolDetailbyId = $this->schoolsRepository->getSchoolById($id);
            /*$teenagers = $this->teenagersRepository->getAllActiveTeenagersForNotification();
            foreach ($teenagers AS $key => $value) {
                $message = 'New school "' .$SchoolDetailbyId->sc_name.'" has been added into ProTeen!';
                $return = Helpers::saveAllActiveTeenagerForSendNotifivation($value->id, $message);
            }*/
                        // --------------------start sending mail -----------------------------//
                        $replaceArray = array();
                        $replaceArray['SCHOOL_NAME'] = $SchoolDetailbyId->sc_first_name;
                        $replaceArray['SCHOOL_LOGIN_URL'] = url('school/login');

                        $emailTemplateContent = $this->templateRepository->getEmailTemplateDataByName(Config::get('constant.SCHOOL_VAIRIFIED_EMAIL_TEMPLATE_NAME'));
                        $content = $this->templateRepository->getEmailContent($emailTemplateContent->et_body, $replaceArray);
                        $data = array();
                        $data['subject'] = $emailTemplateContent->et_subject;
                        $data['toEmail'] = $SchoolDetailbyId->sc_email;
                        $data['toName'] = $SchoolDetailbyId->sc_first_name;
                        $data['content'] = $content;
                        Mail::send(['html' => 'emails.Template'], $data, function($message) use ($data) {
                                    $message->subject($data['subject']);
                                    $message->to($data['toEmail'], $data['toName']);
                                 });
            return Redirect::to("admin/schools")->with('success', trans('labels.schoolapprovesuccess'));
        }
        else
        {

            Helpers::createAudit($this->loggedInUser->user()->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_DELETE'), Config::get('databaseconstants.TBL_SCHOOLS'), $id, Config::get('constant.AUDIT_ORIGIN_WEB'),trans('labels.somethingwrong'), '', $_SERVER['REMOTE_ADDR']);
            return Redirect::to("admin/schools")->with('error', trans('labels.commonerrormessage'));
        }
    }
    public function getStudentDetail($id)
    {
        $searchParamArray = Input::all();
        //print_r($searchParamArray); die;
        if (isset($searchParamArray['clearSearch'])) {
            unset($searchParamArray);
            $searchParamArray = array();
        }
        $teenagers = $this->schoolsRepository->getStudentDetailAsPerSchool($id, $searchParamArray);
        
        $emailDetails = $this->teenagersRepository->getEmailDataOfStudent($id);
        $finalEmailArr = array();
        if(!empty($emailDetails)){
            foreach ($emailDetails as $data) {
                $userid = $data->id;
                $email = $data->t_email;
                $checkIfMailSent = $this->teenagersRepository->checkMailSentOrNot($userid);
                if (empty($checkIfMailSent)) {
                    $finalEmailArr[] = $email;
                }
            }
        }
        
        if(!empty($teenagers)){
            foreach ($teenagers as $info) {
                $info->email_sent = (in_array($info->t_email, $finalEmailArr))? "No":"Yes";
            }
        }
        
            
        return view('admin.ListTeenagerAsPerSchool', compact('teenagers', 'searchParamArray','id'));
    }

    public function getClass($id)
    {
       $result = $this->schoolsRepository->getClassDetail($id);
       return json_encode($result);
    }
    
    public function exportschool() {
        $searchParamArray = Input::all();
        $schools = $this->schoolsRepository->getAllSchools($searchParamArray,$isExport=true); 
        ob_start();
        
        $filename = 'School_Data.csv';
        $fp = fopen('php://output', 'w');
        $FieldArray = [];
        $FieldArray['sc_name'] = 'School Name';
        $FieldArray['sc_address1'] = 'Address 1';
        $FieldArray['sc_address2'] = 'Address 2';
        $FieldArray['sc_pincode'] = 'ZipCode';        
        $FieldArray['sc_city'] = 'City';        
        $FieldArray['sc_state'] = 'State';
        $FieldArray['sc_country'] = 'Country';
        $FieldArray['sc_first_name'] = 'First Name';
        $FieldArray['sc_last_name'] = 'Last Name';
        $FieldArray['sc_title'] = 'Title';
        $FieldArray['sc_phone'] = 'Phone';
        $FieldArray['sc_email'] = 'Email';
        $FieldArray['student_count'] = 'Student Count';
        $FieldArray['sc_isapproved'] = 'Approved Status';
        $FieldArray['deleted'] = 'Active Status';
        
        fputcsv($fp, $FieldArray);
        foreach ($schools as $key => $school) {  
            $schoolDetail = $this->schoolsRepository->getSchoolById($school->id);
            
            $FieldArray = [];
            $FieldArray['sc_name'] = $school->sc_name;
            $FieldArray['sc_address1'] = $school->sc_address1;
            $FieldArray['sc_address2'] = $school->sc_address2;
            $FieldArray['sc_pincode'] = $school->sc_pincode;
            $FieldArray['sc_city'] = $schoolDetail->city;
            $FieldArray['sc_state'] = $schoolDetail->s_name;
            $FieldArray['sc_country'] = $schoolDetail->c_name;
            $FieldArray['sc_first_name'] = $school->sc_first_name;
            $FieldArray['sc_last_name'] = $school->sc_last_name;
            $FieldArray['sc_title'] = $school->sc_title;
            $FieldArray['sc_phone'] = $school->sc_phone;
            $FieldArray['sc_email'] = $school->sc_email;
            $FieldArray['student_count'] = $school->studentcount;
                                             
            if ($school->sc_isapproved == '1') {
                $verified_status = 'Yes';
            } else {
                $verified_status = 'No';
            }
            $FieldArray['sc_isapproved'] = $verified_status;
            
            if ($school->deleted == '1') {
                $active_status = 'Yes';
            } else if (($school->deleted == '2')) {
                $active_status = 'No';
            }
            $FieldArray['deleted'] = $active_status;
            fputcsv($fp, $FieldArray);
        }
        header('Content-type: application/csv');
        header('Content-Disposition: attachment; filename=' . $filename);
        exit;
    }

    public function addCoinsDataForSchool() {
        $school_Id = $_REQUEST['schoolid'];
        $schoolDetail = $this->objSchools->find($school_Id);
        return view('admin.AddCoinsDataForSchool',compact('schoolDetail'));
    }

    public function saveCoinsDataForSchool() {

        $id = e(Input::get('id'));
        $coins = e(Input::get('sc_coins'));
        $giftCoins = e(Input::get('sc_coins'));
        $flag = 0;
        $schoolData = $this->schoolsRepository->getSchoolDataForCoinsDetail($id);
        if (!empty($schoolData)) {
            if (substr($coins, 0, 1) === '-') {
                $coins = preg_replace('/[-?]/', '', $coins);
                if ($schoolData['sc_coins'] > 0 && $coins <= $schoolData['sc_coins']) {
                    $coins = $schoolData['sc_coins']-$coins;
                } else {
                    return Redirect::to("admin/schools")->with('error', trans('labels.commonerrormessage'));
                }
            } else if (is_numeric($coins)) {
                $coins += $schoolData['sc_coins'];
                $flag++;
            }
        }
        $result = $this->schoolsRepository->updateSchoolCoinsDetail($id, $coins);
        $userArray = $this->schoolsRepository->getSchoolBySchoolId($id);
        $objGiftUser = new TeenagerCoinsGift();
        if($flag) {
            $saveData = [];
            $saveData['tcg_sender_id'] = 0;
            $saveData['tcg_reciver_id'] = $id;
            $saveData['tcg_total_coins'] = $giftCoins;
            $saveData['tcg_gift_date'] = date('Y-m-d');
            $saveData['tcg_user_type'] = 3;

            $return = $objGiftUser->saveTeenagetGiftCoinsDetail($saveData);

            $replaceArray = array();
            $replaceArray['TEEN_NAME'] = $userArray[0]['sc_name'];
            $replaceArray['COINS'] = $giftCoins;
            $replaceArray['FROM_USER'] = $this->loggedInUser->user()->name;
            $emailTemplateContent = $this->templateRepository->getEmailTemplateDataByName(Config::get('constant.COINS_RECEIBED_TEMPLATE'));
            $content = $this->templateRepository->getEmailContent($emailTemplateContent->et_body, $replaceArray);

            $data = array();
            $data['subject'] = $emailTemplateContent->et_subject;
            $data['toEmail'] = $userArray[0]['sc_email'];
            $data['toName'] = $userArray[0]['sc_name'];
            $data['content'] = $content;

            Mail::send(['html' => 'emails.Template'], $data , function ($m) use ($data) {
                $m->from(Config::get('constant.FROM_MAIL_ID'), 'Gift ProCoins');
                $m->subject($data['subject']);
                $m->to($data['toEmail'], $data['toName']);
            });
        }

        return Redirect::to("admin/schools")->with('success', trans('labels.coinsaddsuccess'));
    }
}