<?php

namespace App\Http\Controllers\Admin;

use Excel;
use App\Item;
use Session;
use Auth;
use File;
use Image;
use Input;
use Config;
use Request;
use Helpers;
use Redirect;
use Illuminate\Pagination\Paginator;
use App\Teenagers;
use App\Http\Controllers\Controller;
use App\Http\Requests\TeenagerRequest;
use App\Http\Requests\TeenagerBulkRequest;
use App\Services\Teenagers\Contracts\TeenagersRepository;
use App\Services\Professions\Contracts\ProfessionsRepository;
use App\Services\Sponsors\Contracts\SponsorsRepository;
use App\Services\Level1Activity\Contracts\Level1ActivitiesRepository;
use App\Services\Level2Activity\Contracts\Level2ActivitiesRepository;
use App\Services\Template\Contracts\TemplatesRepository;
use App\Services\Level4Activity\Contracts\Level4ActivitiesRepository;
use Mail;
use Cache;
use App\LearningStyle;
use App\Professions;
use App\TeenagerCoinsGift;
use App\Services\FileStorage\Contracts\FileStorageRepository;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class TeenagerManagementController extends Controller {

    public function __construct(FileStorageRepository $fileStorageRepository, TeenagersRepository $teenagersRepository, SponsorsRepository $sponsorsRepository, ProfessionsRepository $professionsRepository, Level1ActivitiesRepository $level1ActivitiesRepository, Level2ActivitiesRepository $level2ActivitiesRepository, TemplatesRepository $templatesRepository, Level4ActivitiesRepository $level4ActivitiesRepository) {
        //$this->objTeenagers = new Teenagers();
        $this->teenagersRepository = $teenagersRepository;
        $this->sponsorsRepository = $sponsorsRepository;
        $this->professionsRepository = $professionsRepository;
        $this->level1ActivitiesRepository = $level1ActivitiesRepository;
        $this->level2ActivitiesRepository = $level2ActivitiesRepository;
        $this->level4ActivitiesRepository = $level4ActivitiesRepository;
        $this->fileStorageRepository = $fileStorageRepository;
        $this->teenOriginalImageUploadPath = Config::get('constant.TEEN_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->teenThumbImageUploadPath = Config::get('constant.TEEN_THUMB_IMAGE_UPLOAD_PATH');
        $this->professionThumbImageUploadPath = Config::get('constant.PROFESSION_THUMB_IMAGE_UPLOAD_PATH');
        $this->professionOriginalImageUploadPath = Config::get('constant.PROFESSION_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->teenThumbImageHeight = Config::get('constant.TEEN_THUMB_IMAGE_HEIGHT');
        $this->teenThumbImageWidth = Config::get('constant.TEEN_THUMB_IMAGE_WIDTH');
        $this->controller = 'TeenagerManagementController';
        $this->loggedInUser = Auth::guard('admin');
        $this->interestOriginalImageUploadPath = Config::get('constant.INTEREST_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->apptitudeOriginalImageUploadPath = Config::get('constant.APPTITUDE_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->miOriginalImageUploadPath = Config::get('constant.MI_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->personalityOriginalImageUploadPath = Config::get('constant.PERSONALITY_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->templateRepository = $templatesRepository;
        $this->cartoonThumbImageUploadPath = Config::get('constant.CARTOON_THUMB_IMAGE_UPLOAD_PATH');
        $this->cartoonOriginalImageUploadPath = Config::get('constant.CARTOON_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->humanThumbImageUploadPath = Config::get('constant.HUMAN_THUMB_IMAGE_UPLOAD_PATH');
        $this->humanOriginalImageUploadPath = Config::get('constant.HUMAN_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->relationIconOriginalImageUploadPath = Config::get('constant.RELATION_ICON_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->relationIconThumbImageUploadPath = Config::get('constant.RELATION_ICON_THUMB_IMAGE_UPLOAD_PATH');
        $this->learningStyleThumbImageUploadPath = Config::get('constant.LEARNING_STYLE_THUMB_IMAGE_UPLOAD_PATH');
         
        $this->log = new Logger('admin-teenager');
        $this->log->pushHandler(new StreamHandler(storage_path().'/logs/monolog-'.date('m-d-Y').'.log'));
    }

    public function index() {
        //$teenagers = $this->teenagersRepository->getAllTeenagersData
          return view('admin.ListTeenager');
    }

    public function getIndex(){
        $this->log->info('Admin teenager listing page',array('userid'=>$this->loggedInUser->user()->id));
        Helpers::createAudit($this->loggedInUser->user()->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_READ'), $this->controller . "@index", $_SERVER['REQUEST_URI'], Config::get('constant.AUDIT_ORIGIN_WEB'), '', '', $_SERVER['REMOTE_ADDR']);
        if(Input::get('start_date') && Input::get('end_date')){
            $teenagers = $this->teenagersRepository->getAllTeenagersDataByDate(Input::get('start_date'), Input::get('end_date'))->get()->count();
        }
        else{
            $teenagers = $this->teenagersRepository->getAllTeenagersData()->get()->count();
        }
        $records = array();
        $columns = array(
            0 => 'id',
            1 => 't_name',
            2 => 't_email',
            3 => 't_coins',
            4 => 't_phone',
            6 => 'deleted',
            8 => 'created_at',
        );
        
        $order = Input::get('order');
        $search = Input::get('search');
        $records["data"] = array();
        $iTotalRecords = $teenagers;
        $iTotalFiltered = $iTotalRecords;
        $iDisplayLength = intval(Input::get('length')) <= 0 ? $iTotalRecords : intval(Input::get('length'));
        $iDisplayStart = intval(Input::get('start'));
        $sEcho = intval(Input::get('draw'));

        if(Input::get('start_date') && Input::get('end_date')){
            $end_date = date('Y-m-d H:i:s', strtotime(Input::get('end_date') . ' +1 day'));
            $records["data"] = $this->teenagersRepository->getAllTeenagersDataByDate(Input::get('start_date'), $end_date);
        }
        else{
            $records["data"] = $this->teenagersRepository->getAllTeenagersData();
        }
        if (!empty($search['value'])) {
            $val = $search['value'];
            $records["data"]->where(function($query) use ($val) {
                $query->where('teenager.t_name', "Like", "%$val%");
                $query->orWhere('teenager.created_at', "Like", "%$val%");
                $query->orWhere('teenager.t_nickname', "Like", "%$val%");
                $query->orWhere('teenager.t_email', "Like", "%$val%");
            });

            // No of record after filtering
            $iTotalFiltered = $records["data"]->where(function($query) use ($val) {
                    $query->where('teenager.t_name', "Like", "%$val%");
                    $query->orWhere('teenager.created_at', "Like", "%$val%");
                    $query->orWhere('teenager.t_nickname', "Like", "%$val%");
                    $query->orWhere('teenager.t_email', "Like", "%$val%");
                })->count();
        }
        
        //order by
        foreach ($order as $o) {
            $records["data"] = $records["data"]->orderBy($columns[$o['column']], $o['dir']);
        }

        //limit
        $records["data"] = $records["data"]->take($iDisplayLength)->offset($iDisplayStart)->get();
        // this $sid use for school edit teenager and admin edit teenager
        $sid = 0;
        if (!empty($records["data"])) {
            foreach ($records["data"] as $key => $_records) {
                $records["data"][$key]->t_name = "<a target='_blank' href='".url('/admin/view-teenager')."/".$_records->id."/basicdetails'>".$_records->t_name."</a>";
                $records["data"][$key]->action = '<a href="'.url('/admin/edit-teenager').'/'.$_records->id.'/'.$sid.'"><i class="fa fa-edit"></i> &nbsp;&nbsp;</a>
                                                    <a onClick="return confirm(\'Are you sure want to delete?\')" href="'.url('/admin/delete-teenager').'/'.$_records->id.'"><i class="i_delete fa fa-trash"></i> &nbsp;&nbsp;</a>
                                                    <a href="#" onClick="add_details(\''.$_records->id.'\');" data-toggle="modal" id="#userCoinsData" data-target="#userCoinsData"><i class="fa fa-database" aria-hidden="true"></i></a>';
                $records["data"][$key]->deleted = ($_records->deleted == 1) ? "<i class='s_active fa fa-square'></i>" : "<i class='s_inactive fa fa-square'></i>";
                $records["data"][$key]->importData = "<a href='".url('/admin/export-l4-data')."/".$_records->id."'><i class='fa fa-file-excel-o' aria-hidden='true'></i></a>";
                $records["data"][$key]->t_name = trim($_records->t_name);
                $records["data"][$key]->t_birthdate = date('d/m/Y',strtotime($_records->t_birthdate));
                $records["data"][$key]->created_at = date('d/m/Y',strtotime($_records->created_at));
            }
        }

        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalFiltered;

        return \Response::json($records);
        exit;
    }

    public function add() {        
        $teenagerDetail = [];
        Helpers::createAudit($this->loggedInUser->user()->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_READ'), $this->controller . "@add", $_SERVER['REQUEST_URI'], Config::get('constant.AUDIT_ORIGIN_WEB'), '', '', $_SERVER['REMOTE_ADDR']);
        $sponsorDetail = $this->sponsorsRepository->getApprovedSponsors();
        $this->log->info('Admin teenager add page',array('userid'=>$this->loggedInUser->user()->id));        
        return view('admin.EditTeenager', compact('teenagerDetail', 'sponsorDetail'));
    }

    public function edit($id, $sid) {
        $uploadTeenagerThumbPath = $this->teenThumbImageUploadPath;
        //$teenagerDetail = $this->objTeenagers->find($id);
        $teenagerDetail = $this->teenagersRepository->getTeenagerById($id);
        Helpers::createAudit($this->loggedInUser->user()->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_READ'), $this->controller . "@edit", $_SERVER['REQUEST_URI'], Config::get('constant.AUDIT_ORIGIN_WEB'), '', '', $_SERVER['REMOTE_ADDR']);
        $selectedSponsors = array();
        if (isset($teenagerDetail->t_sponsors) && !empty($teenagerDetail->t_sponsors)) {
            foreach ($teenagerDetail->t_sponsors as $key => $val) {
                $selectedSponsors[] = $val->sponsor_id;
            }
        }
        $sponsorDetail = $this->sponsorsRepository->getApprovedSponsors();
        $this->log->info('Admin teenager edit page',array('userid'=>$this->loggedInUser->user()->id));
        
        return view('admin.EditTeenager', compact('teenagerDetail', 'sid', 'uploadTeenagerThumbPath','sponsorDetail', 'selectedSponsors'));
    }

    public function save(TeenagerRequest $teenagerRequest) {
        
        $teenagerDetail = [];

        $teenagerDetail['id'] = e(Input::get('id'));
        $sid = e(Input::get('sid'));
        $hiddenProfile = trim(Input::get('hidden_profile'));
        $teenagerDetail['t_photo'] = $hiddenProfile;
        $teenagerDetail['t_name'] = e(Input::get('t_name'));
        $teenagerDetail['t_nickname'] = e(Input::get('t_nickname'));
        $teenagerDetail['t_email'] = e(Input::get('t_email'));
        $teenagerDetail['t_uniqueid'] = e(Input::get('t_uniqueid'));
        $teenagerDetail['t_school'] = e(Input::get('t_school'));
        $hiddenPassword = e(Input::get('hidden_password'));
        $password = e(Input::get('password'));
        $confirm_password = e(Input::get('confirm_password'));
        $teenagerDetail['t_gender'] = e(Input::get('t_gender'));
        $teenagerDetail['t_social_provider'] = e(Input::get('t_social_provider'));
        $teenagerDetail['t_social_identifier'] = e(Input::get('t_social_identifier'));
        $teenagerDetail['t_phone'] = e(Input::get('t_phone'));
        //$teenagerDetail['t_birthdate'] = e(Input::get('t_birthdate'));
        $teenagerDetail['t_country'] = e(Input::get('t_country'));
        $teenagerDetail['t_pincode'] = e(Input::get('t_pincode'));
        $teenagerDetail['t_location'] = e(Input::get('t_location'));
        $teenagerDetail['t_level'] = e(Input::get('t_level'));
        //$teenagerDetail['t_credit'] = e(Input::get('fu_address'));
        $teenagerDetail['t_boosterpoints'] = (Input::get('t_boosterpoints') != "") ? Input::get('t_boosterpoints') : 0;
        $teenagerDetail['t_isfirstlogin'] = (Input::get('t_isfirstlogin') != "") ? Input::get('t_isfirstlogin') : 1;
        $teenagerDetail['t_sponsor_choice'] = (Input::get('t_sponsor_choice') != 0) ? Input::get('t_sponsor_choice') : 0;
        $teenagerDetail['t_isverified'] = e(Input::get('t_isverified'));
        $teenagerDetail['t_device_token'] = e(Input::get('t_device_token'));
        $teenagerDetail['t_device_type'] = e(Input::get('t_device_type'));
        $teenagerDetail['deleted'] = e(Input::get('deleted'));
        $sponsors = Input::get('selected_sponsor');
        $postData['pageRank'] = Input::get('pageRank');

        if (Input::get('t_birthdate') != '') {
            $dob = Input::get('t_birthdate');
            $dobDate = str_replace('/', '-', $dob);
            $teenagerDetail['t_birthdate'] = date("Y-m-d", strtotime($dobDate));
        }

        if ($hiddenPassword != '' && $password == '') {
            $teenagerDetail['password'] = $hiddenPassword;
        } else {
            $teenagerDetail['password'] = bcrypt($password);
        }

        if (Input::file()) {
            $file = Input::file('t_photo');
            if (!empty($file)) {
                
                $validationPass = Helpers::checkValidImageExtension($file);
                
                if($validationPass)
                {
                    $fileName = 'teenager_' . time() . '.' . $file->getClientOriginalExtension();
                    $pathOriginal = public_path($this->teenOriginalImageUploadPath . $fileName);
                    $pathThumb = public_path($this->teenThumbImageUploadPath . $fileName);

                    Image::make($file->getRealPath())->save($pathOriginal);
                    Image::make($file->getRealPath())->resize($this->teenThumbImageWidth, $this->teenThumbImageHeight)->save($pathThumb);

                    if ($hiddenProfile != '' && $hiddenProfile != "proteen-logo.png") {
                        $originalImageDelete = $this->fileStorageRepository->deleteFileToStorage($hiddenProfile, $this->teenOriginalImageUploadPath, "s3");
                        $thumbImageDelete = $this->fileStorageRepository->deleteFileToStorage($hiddenProfile, $this->teenThumbImageUploadPath, "s3");
                    }
                    //Uploading on AWS
                    $originalImage = $this->fileStorageRepository->addFileToStorage($fileName, $this->teenOriginalImageUploadPath, $pathOriginal, "s3");
                    $thumbImage = $this->fileStorageRepository->addFileToStorage($fileName, $this->teenThumbImageUploadPath, $pathThumb, "s3");
                    //Deleting Local Files
                    \File::delete($this->teenOriginalImageUploadPath . $fileName);
                    \File::delete($this->teenThumbImageUploadPath . $fileName);
                    $this->log->info('View Admin teenager profile image deleted',array('userid'=>$this->loggedInUser->user()->id,'imagename'=>$fileName));
                    $teenagerDetail['t_photo'] = $fileName;
                }
            }
        }
        if (isset($teenagerDetail['t_email']) && $teenagerDetail['t_email'] != '') {
            $teenagerEmailExist = $this->teenagersRepository->checkActiveEmailExist($teenagerDetail['t_email'], $teenagerDetail['id']);
        }
        if (isset($teenagerDetail['t_phone']) && $teenagerDetail['t_phone'] != '') {
            $teenagerMobileExist = $this->teenagersRepository->checkActivePhoneExist($teenagerDetail['t_phone'], $teenagerDetail['id']);
        }
        else
        {
            $teenagerMobileExist = '';
        }

        $response = ($teenagerEmailExist == '' && $teenagerMobileExist == '') ? $this->teenagersRepository->saveTeenagerDetail($teenagerDetail) : '0';
        Cache::forget('teenagerDetail');
        if ($response) {
            if (isset($sponsors) && !empty($sponsors) && Input::get('t_sponsor_choice') == 2) {
                $sponsorDetail = $this->teenagersRepository->saveTeenagerSponserId($response->id, implode(',', $sponsors));
            }
            //Helpers::createAudit($this->loggedInUser->user()->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_UPDATE'), Config::get('databaseconstants.TBL_TEENAGERS'), $response, Config::get('constant.AUDIT_ORIGIN_WEB'), trans('labels.teenupdatesuccess'), serialize($teenagerDetail), $_SERVER['REMOTE_ADDR']);
            if(isset($sid) && !empty($sid) && $sid > 0)
            {
                $this->log->info('Admin Teen added/updated successfully',array('teenid' => $teenagerDetail['id']));
                return Redirect::to("/admin/view-student-list/$sid")->with('success', trans('labels.teenupdatesuccess'));
            }
            else
            {
                $this->log->info('Admin Teen added/updated successfully',array('teenid' => $teenagerDetail['id']));
                return Redirect::to("admin/teenagers".$postData['pageRank'])->with('success', trans('labels.teenupdatesuccess'));
            }
        } else {
            //Helpers::createAudit($this->loggedInUser->user()->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_UPDATE'), Config::get('databaseconstants.TBL_TEENAGERS'), $response, Config::get('constant.AUDIT_ORIGIN_WEB'), trans('labels.somethingwrong'), serialize($teenagerDetail), $_SERVER['REMOTE_ADDR']);
            if(isset($sid) && !empty($sid) && $sid > 0)
            {
                $this->log->error('Admin something went wrong while adding/updating teen',array('teenid' => $teenagerDetail['id']));
                return Redirect::to("/admin/view-student-list/$sid")->with('success', trans('labels.teenagererrormessage'));
            }
            else
            {
                $this->log->error('Admin something went wrong while adding/updating teen',array('teenid' => $teenagerDetail['id']));                
                return Redirect::to("admin/teenagers".$postData['pageRank'])->with('error', trans('labels.teenagererrormessage'));
            }
        }
    }

    public function delete($id) {
        $return = $this->teenagersRepository->deleteTeenager($id);
        if ($return) {
            Helpers::createAudit($this->loggedInUser->user()->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_DELETE'), Config::get('databaseconstants.TBL_TEENAGERS'), $id, Config::get('constant.AUDIT_ORIGIN_WEB'), trans('labels.teendeletesuccess'), '', $_SERVER['REMOTE_ADDR']);
            $this->log->info('Admin Teen deleted successfully',array('userid'=>$this->loggedInUser->user()->id,'teenid' => $id));
            return Redirect::to("admin/teenagers")->with('success', trans('labels.teendeletesuccess'));
        } else {
            Helpers::createAudit($this->loggedInUser->user()->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_DELETE'), Config::get('databaseconstants.TBL_TEENAGERS'), $id, Config::get('constant.AUDIT_ORIGIN_WEB'), trans('labels.somethingwrong'), '', $_SERVER['REMOTE_ADDR']);
            $this->log->error('Admin something went wrong while deleting teen',array('userid'=>$this->loggedInUser->user()->id,'teenid' => $id));
            return Redirect::to("admin/teenagers")->with('error', trans('labels.commonerrormessage'));
        }
    }

    public function addbulk() {
        return view('admin.AddTeenagerBulk');
    }

    public function savebulkdata(TeenagerBulkRequest $TeenagerBulkRequest) {
        $teenager = Input::file('teenager_bulk');

        $i = 0;
        $column = array();

        $file = fopen($teenager, "r");
        $count = 1;
        $teenagersDataArray = [];
        while (!feof($file)) {
            $data = fgetcsv($file);
            if ($count == 1) {
                $fields = $data;
                foreach ($fields as $field) {
                    if ($field != '') {
                        $column[] = $field;
                    }
                }
            } else {
                $associativeArray = [];
                if (isset($data) && !empty($data)) {
                    foreach ($data as $key => $value) {
                        $associativeArray[$fields[$key]] = $value;
                    }
                    $teenagersDataArray[] = $associativeArray;
                }
            }
            $count++;
        }

        Session::set('teenagersDataArray', $teenagersDataArray);
        fclose($file);

        return view('admin.SetTeenagersField', compact('column', 'teenagerDetail'));
    }

    public function mapteenager() {
        $teenagersDataArray = Session::get('teenagersDataArray');
        $teenagers = array();
        $teenagerArray = Input::all();

        if (isset($teenagerArray['checkmap']) && isset($teenagerArray['checkmap']) != '') {
            $teenagerValidArray = array();
            $teenagerInvalidArray = array();

            $mappedData = Session::get('mappedData');
            foreach ($mappedData as $data) {
                if (!filter_var($data['t_email'], FILTER_VALIDATE_EMAIL)) {
                    $teenagerInvalidArray[] = $data;
                } else {

                    if (isset($data['t_email']) && $data['t_email'] != '') {
                        $teenagerEmailExist = $this->teenagersRepository->checkActiveEmailExist($data['t_email']);
                    }
                    if (isset($data['t_phone']) && $data['t_phone'] != '') {
                        $teenagerMobileExist = $this->teenagersRepository->checkActivePhoneExist($data['t_phone']);
                    }

                    if (isset($teenagerEmailExist) && $teenagerEmailExist) {
                        $teenagerInvalidArray[] = $data;
                    } elseif ((isset($teenagerMobileExist) && $teenagerMobileExist) || (!is_numeric($data['t_phone']))) {
                        $teenagerInvalidArray[] = $data;
                    } else {
                        if (!isset($data['password'])) {
                            $data['password'] = Helpers::generateRandomPassword();
                        }

                        $teenagerValidArray[] = $data;
                    }
                }
            }
            Session::set('teenagerValidData', $teenagerValidArray);
            return view('admin.CheckTeenagersData', compact('teenagerValidArray', 'teenagerInvalidArray'));
        } else {
            $mappedData = [];
            foreach ($teenagersDataArray as $data) {
                $teenMappingData = [];
                foreach ($teenagerArray as $key => $value) {
                    if (isset($data[$teenagerArray[$key]]) && $data[$teenagerArray[$key]] != '') {
                        $teenMappingData[$key] = $data[$teenagerArray[$key]];
                    }
                }

                $mappedData[] = $teenMappingData;
                Session::set('mappedData', $mappedData);
            }
            return view('admin.MapTeenagersField', compact('mappedData'));
        }
    }

    public function insertMapTeenager() {
        $teenagerDetail = Session::get('teenagerValidData');
        foreach ($teenagerDetail as $data) {
            if (isset($data['t_country'])) {
                $data['t_country'] = $this->teenagersRepository->getCountryIdByName($data['t_country']);
                $data['t_country'] = (isset($data['t_country']->id)) ? $data['t_country']->id : '0';
            }
            if (isset($data['t_school'])) {
                $data['t_school'] = $this->teenagersRepository->getSchoolIdByName($data['t_school']);
                $data['t_school'] = (isset($data['t_school']->id)) ? $data['t_school']->id : '0';
            }
            if (isset($data['t_gender'])) {
                $data['t_gender'] = (strtolower($data['t_gender']) == 'male') ? '1' : '2';
            }
            if (isset($data['t_birthdate'])) {
                $birthdate = explode('-', $data['t_birthdate']); // dd-mm-yyyy
                $data['t_birthdate'] = (isset($birthdate) && !empty($birthdate)) ? $birthdate[2] . "-" . $birthdate[1] . "-" . $birthdate[0] : "0000-00-00";
            }
            $data['t_uniqueid'] = Helpers::getTeenagerUniqueId();
            $data['password'] = bcrypt($data['password']);
            $response = $this->teenagersRepository->saveTeenagerDetail($data);
        }
        if ($response) {
            Helpers::createAudit($this->loggedInUser->user()->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_UPDATE'), Config::get('databaseconstants.TBL_TEENAGERS'), $response, Config::get('constant.AUDIT_ORIGIN_WEB'), trans('labels.teenupdatesuccess'), serialize($teenagerDetail), $_SERVER['REMOTE_ADDR']);

            return Redirect::to("admin/teenagers")->with('success', trans('labels.teenaddsuccess'));
        } else {
            Helpers::createAudit($this->loggedInUser->user()->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_UPDATE'), Config::get('databaseconstants.TBL_TEENAGERS'), $response, Config::get('constant.AUDIT_ORIGIN_WEB'), trans('labels.somethingwrong'), serialize($teenagerDetail), $_SERVER['REMOTE_ADDR']);

            return Redirect::to("admin/teenagers")->with('error', trans('labels.commonerrormessage'));
        }
    }

    public function downloadExcel() {
        $filename = trans('labels.csv_file');
        $fp = fopen('php://output', 'w');
        $FieldArray = [];
        $result = Session::get('teenagerValidData');
        foreach ($result[0] as $key => $teenkey) {
            $FieldArray[] = ucfirst(str_replace("t_", "", $key));
        }
        header('Content-type: application/csv');
        header('Content-Disposition: attachment; filename=' . $filename);
        fputcsv($fp, $FieldArray);
        foreach ($result as $row) {
            fputcsv($fp, $row);
        }
        $this->log->info('Admin download teen excel file',array('userid'=>$this->loggedInUser->user()->id));
        exit;
    }

    public function exportData() {
        ob_start();
        $teenagerData = $this->teenagersRepository->getAllTeenagersExport();

        $filename = trans('labels.teenagerdata');
        $fp = fopen('php://output', 'w');
        $FieldArray = [];
        $FieldArray['t_name'] = 'Name';
        $FieldArray['t_nickname'] = 'NickName';
        $FieldArray['t_email'] = 'Email Id';
        $FieldArray['t_gender'] = 'Gender';
        $FieldArray['t_uniqueid'] = 'Unique Id';
        $FieldArray['t_school'] = 'School';
        $FieldArray['t_phone'] = 'Phone No';
        $FieldArray['t_birthdate'] = 'Birth Date';
        $FieldArray['t_country'] = 'Country';
        $FieldArray['t_pincode'] = 'Zip Code';
        $FieldArray['t_sponsor_choice'] = 'Sponsor Choice';
        $FieldArray['sponsor'] = 'Selected Sponsors';
        $FieldArray['parent'] = 'Selected Parent';
        $FieldArray['counsellor'] = 'Selected Counsellor';
        $FieldArray['t_device_type'] = 'Device Type';
        $FieldArray['t_social_provider'] = 'Registration Type';
        $FieldArray['t_isverified'] = 'Verified Status';
        $FieldArray['school_validate_status'] = 'School Validate Status';
        $FieldArray['deleted'] = 'Active Status';
        $FieldArray['t_last_activity'] = 'Last Activity';
        $FieldArray['t_signup_date'] = 'Sign Up Date';
        $FieldArray['Level1'] = 'Level1 Points';
        $FieldArray['Level2'] = 'Level2 Points';
        $FieldArray['Level3'] = 'Level3 Points';
        $FieldArray['Level4'] = 'Level4 Points';
        $FieldArray['total'] = 'Total Points';
        fputcsv($fp, $FieldArray);
        $finalEmailArr = $emailDetails = array();
                
        foreach ($teenagerData as $key => $teendata) {
            $boosterPoints = $this->teenagersRepository->getTeenagerBoosterPoints($teendata->id);
            //get teen parents/counsellor
            $parentCounsellor = $this->teenagersRepository->getTeenParents($teendata->id);
            if(isset($parentCounsellor) && !empty($parentCounsellor)){
                $parent = '';
                $counsellor = '';
                foreach($parentCounsellor as $key=>$val)
                {
                    if($val->p_user_type == 1){
                    $parent .= $val->p_email."\n";
                    }else{
                      $counsellor .= $val->p_email."\n";
                    }
                }
            }else{
                $parent = 'None';
                $counsellor = 'None';
            }
            $sponsors = $this->teenagersRepository->getTeenagerById($teendata->id);

            if(isset($sponsors->t_sponsors) && !empty($sponsors->t_sponsors)){
                $sponsor = '';
                foreach($sponsors->t_sponsors as $key1=>$spon)
                {
                    $sponsor .= $spon->sp_company_name."\n";
                }
            }else{
                $sponsor = 'None';
            }

            $FieldArray = [];
            $FieldArray['t_name'] = $teendata->t_name;
            $FieldArray['t_nickname'] = $teendata->t_nickname;
            $FieldArray['t_email'] = $teendata->t_email;
            if ($teendata->sc_name != '') {
                $school = $teendata->sc_name;
            } else {
                $school = 'N/A';
            }
            $gender = '';
            if ($teendata->t_gender == '1') {
                $gender = 'Male';
            } else if (($teendata->t_gender == '2')) {
                $gender = 'Female';
            }

            if ($teendata->t_sponsor_choice == 1) {
                $sponsor_choice = 'Self';
            } elseif($teendata->t_sponsor_choice == 2) {
                $sponsor_choice = 'Sponsor';
            }else{
                $sponsor_choice = 'None';
            }

            $FieldArray['t_gender'] = $gender;
            $FieldArray['t_uniqueid'] = $teendata->t_uniqueid;
            $FieldArray['t_school'] = $school;
            $FieldArray['t_phone'] = $teendata->t_phone;
            $FieldArray['t_birthdate'] = $teendata->t_birthdate;
            $FieldArray['t_country'] = $teendata->c_name;
            $FieldArray['t_pincode'] = $teendata->t_pincode;
            $FieldArray['t_sponsor_choice'] = $sponsor_choice;
            $FieldArray['sponsor'] = $sponsor;
            $FieldArray['parent'] = $parent;
            $FieldArray['counsellor'] = $counsellor;

            if ($teendata->t_device_type == 1) {
                $device = 'iOS';
            } elseif($teendata->t_device_type == 2) {
                $device = 'Android';
            }else{
                $device = 'Web';
            }
            $FieldArray['t_device_type'] = $device;
            if ($teendata->t_isverified == '1') {
                $verified_status = 'Yes';
            } else {
                $verified_status = 'No';
            }
            $FieldArray['t_social_provider'] = $teendata->t_social_provider;
            $FieldArray['t_isverified'] = $verified_status;
            
            if ($teendata->sc_name != '') {                
                //Now check if school has validated 
                $checkIfMailSent = $this->teenagersRepository->checkMailSentOrNot($teendata->id);                 
                $FieldArray['school_validate_status'] = (empty($checkIfMailSent))? "No":"Yes";                                            
            } else {
                $FieldArray['school_validate_status'] = 'N/A';        
            }
            
            if ($teendata->deleted == '1') {
                $active_status = 'Yes';
            } else if (($teendata->deleted == '2')) {
                $active_status = 'No';
            }
            $FieldArray['deleted'] = $active_status;
            if($teendata->t_last_activity > 0) {
                $FieldArray['t_last_activity'] = date('d/m/Y', $teendata->t_last_activity);
            } else {
                $FieldArray['t_last_activity'] = '';
            }
              
            $FieldArray['t_signup_date'] = date('d/m/Y',strtotime($teendata->created_at));
            $FieldArray['Level1'] = $boosterPoints['Level1'];
            $FieldArray['Level2'] = $boosterPoints['Level2'];
            $FieldArray['Level3'] = $boosterPoints['Level3'];
            $FieldArray['Level4'] = $boosterPoints['Level4'];
            $FieldArray['total'] = $boosterPoints['total'];
            fputcsv($fp, $FieldArray);
        }
        $this->log->info('Admin export teen data',array('userid'=>$this->loggedInUser->user()->id));
        
        header('Content-type: application/csv');
        header('Content-Disposition: attachment; filename=' . $filename);
        exit;
    }

    public function viewDetail($id,$type){

        if($type == "basicdetails"){
            $viewTeenDetail = $this->teenagersRepository->getTeenagerById($id);
            $this->log->info('Admin view teen basic data',array('userid'=>$this->loggedInUser->user()->id,'teenid'=>$id));
       
            return view('admin.ViewTeenagerDetail', compact('viewTeenDetail','id','type'));
        }
        elseif($type == "level1"){
            $l1Activity = $this->level1ActivitiesRepository->getLevel1ActivityWithAnswer($id);
            $boosterPoints = $this->teenagersRepository->getTeenagerBoosterPoints($id);

            $teenagerMyIcons = array();
            //Get teenager choosen Icon
            $teenagerIcons = $this->teenagersRepository->getTeenagerSelectedIcon($id);

            $relationIcon = array();
            $fictionIcon = array();
            $nonFiction = array();

            if (isset($teenagerIcons) && !empty($teenagerIcons)) {
                foreach ($teenagerIcons as $key => $icon) {
                    if ($icon->ti_icon_type == 1) {
                        $fictionIcon[$key]['image'] = ( $icon->fiction_image != '' ) ? Config::get('constant.DEFAULT_AWS').$this->cartoonOriginalImageUploadPath . $icon->fiction_image : asset($this->cartoonOriginalImageUploadPath . 'proteen-logo.png');
                        $fictionIcon[$key]['iconname'] = $icon->ci_name;
                        $fictionIcon[$key]['category'] = $icon->cic_name;
                    } elseif ($icon->ti_icon_type == 2) {
                        $nonFiction[$key]['image'] = ( $icon->nonfiction_image != '' ) ? Config::get('constant.DEFAULT_AWS').$this->humanOriginalImageUploadPath . $icon->nonfiction_image : asset($this->humanOriginalImageUploadPath . 'proteen-logo.png');
                        $nonFiction[$key]['iconname'] = $icon->hi_name;
                        $nonFiction[$key]['category'] = $icon->hic_name;
                    } else {
                        $relationIcon[$key]['image'] = ($icon->ti_icon_image != '') ? Config::get('constant.DEFAULT_AWS') . $this->relationIconOriginalImageUploadPath . $icon->ti_icon_image : asset($this->relationIconOriginalImageUploadPath . 'proteen-logo.png');
                        $relationIcon[$key]['iconname'] = $icon->ti_icon_name;
                        $relationIcon[$key]['category'] = $icon->rel_name;
                    }
                }
                $teenagerMyIcons = array_merge($fictionIcon, $nonFiction, $relationIcon);
            }
            $this->log->info('Admin view teen Level1 detail',array('userid'=>$this->loggedInUser->user()->id,'teenid'=>$id));
            return view('admin.ViewTeenagerDetail', compact('l1Activity','boosterPoints','id','type','teenagerMyIcons'));
        }
        elseif($type == "level2"){
            $l2Activity = $this->level2ActivitiesRepository->getLevel2ActivityWithAnswer($id);
            $boosterPoints = $this->teenagersRepository->getTeenagerBoosterPoints($id);
            $this->log->info('Admin view teen Level2 detail',array('userid'=>$this->loggedInUser->user()->id,'teenid'=>$id));
            return view('admin.ViewTeenagerDetail', compact('l2Activity','boosterPoints','id','type'));
        }
        elseif($type == "promisescore"){
            $finalMIParameters = array();
            $teenagerAPIData = Helpers::getTeenAPIScore($id);

            if (isset($teenagerAPIData) && !empty($teenagerAPIData)) {
                $i = 1;
                // Teenager interest data
                foreach ($teenagerAPIData['APIscore']['interest'] as $interest => $val) {
                    if ($val == 1) {
                        $interestImage = Helpers::getInterestData($interest);
                        $image = (isset($interestImage->it_logo) && $interestImage->it_logo != '') ? Config::get('constant.DEFAULT_AWS'). $this->interestOriginalImageUploadPath . $interestImage->it_logo : asset($this->interestOriginalImageUploadPath . 'proteen-logo.png');
                        $teenagerInterest[] = array('image' => $image, 'interest' => $interest);
                    }
                    $i++;
                }

                // Teenager Apptitude data
                $k = 1;
                foreach ($teenagerAPIData['APIscore']['aptitude'] as $aptitude => $val) {
                     $aptitudemage = Helpers::getApptitudeData($aptitude);
                        $image = (isset($aptitudemage->apt_logo) && $aptitudemage->apt_logo != '') ? Config::get('constant.DEFAULT_AWS'). $this->apptitudeOriginalImageUploadPath . $aptitudemage->apt_logo : asset($this->apptitudeOriginalImageUploadPath . 'proteen-logo.png');
                        $aptitudescale = $teenagerAPIData['APIscale']['aptitude'][$aptitude];
                        $teenagerApptitude[] = array('image' => $image, 'aptitude' => $aptitude, 'scale' => $aptitudescale, 'score' => $val);
                    $k++;
                }

                // Teenager MI Data
                foreach ($teenagerAPIData['APIscore']['MI'] as $mi => $val) {
                        $miimage = Helpers::getMIData($mi);
                        
                        $image = ( isset($miimage->mit_logo) && $miimage->mit_logo != ''  ) ? Config::get('constant.DEFAULT_AWS'). $this->miOriginalImageUploadPath . $miimage->mit_logo : asset($this->miOriginalImageUploadPath . 'proteen-logo.png');
                        $miscale = $teenagerAPIData['APIscale']['MI'][$mi];
                        $teenagerMI[] = array('image' => $image, 'aptitude' => $mi, 'scale' => $miscale, 'score' => $val);
                }
                // Teenager personality Data
                foreach ($teenagerAPIData['APIscore']['personality'] as $personality => $val) {
                        $personalityimage = Helpers::getPersonalityData($personality);
                        
                        $image = ( isset($personalityimage->pt_logo) && $personalityimage->pt_logo != '' ) ? Config::get('constant.DEFAULT_AWS'). $this->personalityOriginalImageUploadPath . $personalityimage->pt_logo : asset($this->personalityOriginalImageUploadPath . 'proteen-logo.png');
                        $personalityscale = $teenagerAPIData['APIscale']['personality'][$personality];
                        $teenagerPersonality[] = array('image' => $image, 'aptitude' => $personality, 'scale' => $personalityscale, 'score' => $val);
                }
                $finalMIParameters = array_merge($teenagerApptitude,$teenagerMI,$teenagerPersonality);
            }
            $this->log->info('Admin view teen MI score detail',array('userid'=>$this->loggedInUser->user()->id,'teenid'=>$id));
            
            return view('admin.ViewTeenagerDetail', compact('finalMIParameters','id','type'));
        }
        elseif($type == "level3"){
            $professionOriginalImageUploadPath = $this->professionOriginalImageUploadPath;
            $boosterPoints = $this->teenagersRepository->getTeenagerBoosterPoints($id);
            $totalQuestion = $this->level2ActivitiesRepository->getNoOfTotalQuestionsAttemptedQuestion($id);
    
            if (isset($totalQuestion[0]->NoOfAttemptedQuestions) && $totalQuestion[0]->NoOfAttemptedQuestions > 0) {
            $response['NoOfAttemptedQuestionsLevel2'] = $totalQuestion[0]->NoOfAttemptedQuestions;
            $getTeenagerAttemptedProfession = $this->professionsRepository->getTeenagerAttemptedProfession($id);
            
            if (isset($getTeenagerAttemptedProfession) && !empty($getTeenagerAttemptedProfession)) {
                $response['teenagerAttemptedProfession'] = $getTeenagerAttemptedProfession;
            } else {
                $response['teenagerAttemptedProfession'] = array();
            }
            $getLevel2AssessmentResult = Helpers::getTeenAPIScore($id);
            $getCareerMappingFromSystem = Helpers::getCareerMappingFromSystem();

            if (isset($getTeenagerAttemptedProfession) && !empty($getTeenagerAttemptedProfession)) {
                foreach ($getTeenagerAttemptedProfession as $keyProfession => $professionName) {
                    $getProfessionIdFromProfessionName = $this->professionsRepository->getProfessionIdByName($professionName->pf_name);
                    
                    if (isset($getProfessionIdFromProfessionName) && $getProfessionIdFromProfessionName > 0) {
                        $compareLogic = array('HL', 'HM', 'HH', 'ML', 'MM', 'MH', 'LL', 'LM', 'LH');
                        //FOR COMPARE LOGIC RESULT, L ='nomatch', M = 'moderate', H ='match'
                        $compareLogicResult = array('L', 'M', 'H', 'L', 'H', 'H', 'H', 'H', 'H');
                        $value = Helpers::getSpecificCareerMappingFromSystem($getProfessionIdFromProfessionName);
                        if (!empty($value)) {

                            $value->tcm_scientific_reasoning = (isset($value->tcm_scientific_reasoning) && $value->tcm_scientific_reasoning != '') ? $value->tcm_scientific_reasoning : 'L';
                            $value->tcm_verbal_reasoning = (isset($value->tcm_verbal_reasoning) && $value->tcm_verbal_reasoning != '') ? $value->tcm_verbal_reasoning : 'L';
                            $value->tcm_numerical_ability = (isset($value->tcm_numerical_ability) && $value->tcm_numerical_ability != '') ? $value->tcm_numerical_ability : 'L';
                            $value->tcm_logical_reasoning = (isset($value->tcm_logical_reasoning) && $value->tcm_logical_reasoning != '') ? $value->tcm_logical_reasoning : 'L';
                            $value->tcm_social_ability = (isset($value->tcm_social_ability) && $value->tcm_social_ability != '') ? $value->tcm_social_ability : 'L';
                            $value->tcm_artistic_ability = (isset($value->tcm_artistic_ability) && $value->tcm_artistic_ability != '') ? $value->tcm_artistic_ability : 'L';
                            $value->tcm_spatial_ability = (isset($value->tcm_spatial_ability) && $value->tcm_spatial_ability != '') ? $value->tcm_spatial_ability : 'L';
                            $value->tcm_creativity = (isset($value->tcm_creativity) && $value->tcm_creativity != '') ? $value->tcm_creativity : 'L';
                            $value->tcm_clerical_ability = (isset($value->tcm_clerical_ability) && $value->tcm_clerical_ability != '') ? $value->tcm_clerical_ability : 'L';
                            $value->tcm_doers_realistic = (isset($value->tcm_doers_realistic) && $value->tcm_doers_realistic != '') ? $value->tcm_doers_realistic : 'L';
                            $value->tcm_thinkers_investigative = (isset($value->tcm_thinkers_investigative) && $value->tcm_thinkers_investigative != '') ? $value->tcm_numerical_ability : 'L';
                            $value->tcm_creators_artistic = (isset($value->tcm_creators_artistic) && $value->tcm_creators_artistic != '') ? $value->tcm_creators_artistic : 'L';
                            $value->tcm_helpers_social = (isset($value->tcm_helpers_social) && $value->tcm_helpers_social != '') ? $value->tcm_helpers_social : 'L';
                            $value->tcm_persuaders_enterprising = (isset($value->tcm_persuaders_enterprising) && $value->tcm_persuaders_enterprising != '') ? $value->tcm_persuaders_enterprising : 'L';
                            $value->tcm_organizers_conventional = (isset($value->tcm_organizers_conventional) && $value->tcm_organizers_conventional != '') ? $value->tcm_organizers_conventional : 'L';
                            $value->tcm_linguistic = (isset($value->tcm_linguistic) && $value->tcm_linguistic != '') ? $value->tcm_linguistic : 'L';
                            $value->tcm_logical = (isset($value->tcm_logical) && $value->tcm_logical != '') ? $value->tcm_logical : 'L';
                            $value->tcm_musical = (isset($value->tcm_musical) && $value->tcm_musical != '') ? $value->tcm_musical : 'L';
                            $value->tcm_spatial = (isset($value->tcm_spatial) && $value->tcm_spatial != '') ? $value->tcm_spatial : 'L';
                            $value->tcm_bodily_kinesthetic = (isset($value->tcm_bodily_kinesthetic) && $value->tcm_bodily_kinesthetic != '') ? $value->tcm_bodily_kinesthetic : 'L';
                            $value->tcm_naturalist = (isset($value->tcm_naturalist) && $value->tcm_naturalist != '') ? $value->tcm_naturalist : 'L';
                            $value->tcm_interpersonal = (isset($value->tcm_interpersonal) && $value->tcm_interpersonal != '') ? $value->tcm_interpersonal : 'L';
                            $value->tcm_intrapersonal = (isset($value->tcm_intrapersonal) && $value->tcm_intrapersonal != '') ? $value->tcm_intrapersonal : 'L';
                            $value->tcm_existential = (isset($value->tcm_existential) && $value->tcm_existential != '') ? $value->tcm_existential : 'L';

                            $variable0 = array_keys($compareLogic, $value->tcm_scientific_reasoning . $getLevel2AssessmentResult['APIscale']['aptitude']['Scientific Reasoning']);
                            $variable1 = array_keys($compareLogic, $value->tcm_verbal_reasoning . $getLevel2AssessmentResult['APIscale']['aptitude']['Verbal Reasoning']);
                            $variable2 = array_keys($compareLogic, $value->tcm_numerical_ability . $getLevel2AssessmentResult['APIscale']['aptitude']['Numerical Ability']);
                            $variable3 = array_keys($compareLogic, $value->tcm_logical_reasoning . $getLevel2AssessmentResult['APIscale']['aptitude']['Logical Reasoning']);
                            $variable4 = array_keys($compareLogic, $value->tcm_social_ability . $getLevel2AssessmentResult['APIscale']['aptitude']['Social Ability']);
                            $variable5 = array_keys($compareLogic, $value->tcm_artistic_ability . $getLevel2AssessmentResult['APIscale']['aptitude']['Artistic Ability']);
                            $variable6 = array_keys($compareLogic, $value->tcm_spatial_ability . $getLevel2AssessmentResult['APIscale']['aptitude']['Spatial Ability']);
                            $variable7 = array_keys($compareLogic, $value->tcm_creativity . $getLevel2AssessmentResult['APIscale']['aptitude']['Creativity']);
                            $variable8 = array_keys($compareLogic, $value->tcm_clerical_ability . $getLevel2AssessmentResult['APIscale']['aptitude']['Clerical Ability']);

                            $variable9 = array_keys($compareLogic, $value->tcm_doers_realistic . $getLevel2AssessmentResult['APIscale']['personality']['Mechanical']);
                            $variable10 = array_keys($compareLogic, $value->tcm_thinkers_investigative . $getLevel2AssessmentResult['APIscale']['personality']['Investigative']);
                            $variable11 = array_keys($compareLogic, $value->tcm_creators_artistic . $getLevel2AssessmentResult['APIscale']['personality']['Artistic']);
                            $variable12 = array_keys($compareLogic, $value->tcm_helpers_social . $getLevel2AssessmentResult['APIscale']['personality']['Social']);
                            $variable13 = array_keys($compareLogic, $value->tcm_persuaders_enterprising . $getLevel2AssessmentResult['APIscale']['personality']['Enterprising']);
                            $variable14 = array_keys($compareLogic, $value->tcm_organizers_conventional . $getLevel2AssessmentResult['APIscale']['personality']['Conventional']);

                            $variable15 = array_keys($compareLogic, $value->tcm_linguistic . $getLevel2AssessmentResult['APIscale']['MI']['Linguistic']);
                            $variable16 = array_keys($compareLogic, $value->tcm_logical . $getLevel2AssessmentResult['APIscale']['MI']['Logical']);
                            $variable17 = array_keys($compareLogic, $value->tcm_musical . $getLevel2AssessmentResult['APIscale']['MI']['Musical']);
                            $variable18 = array_keys($compareLogic, $value->tcm_spatial . $getLevel2AssessmentResult['APIscale']['MI']['Spatial']);
                            $variable19 = array_keys($compareLogic, $value->tcm_bodily_kinesthetic . $getLevel2AssessmentResult['APIscale']['MI']['Bodily-Kinesthetic']);
                            $variable20 = array_keys($compareLogic, $value->tcm_naturalist . $getLevel2AssessmentResult['APIscale']['MI']['Naturalist']);
                            $variable21 = array_keys($compareLogic, $value->tcm_interpersonal . $getLevel2AssessmentResult['APIscale']['MI']['Interpersonal']);
                            $variable22 = array_keys($compareLogic, $value->tcm_intrapersonal . $getLevel2AssessmentResult['APIscale']['MI']['Intrapersonal']);
                            $variable23 = array_keys($compareLogic, $value->tcm_existential . $getLevel2AssessmentResult['APIscale']['MI']['Existential']);

                            $arrayCombinePoint[$getProfessionIdFromProfessionName][] = $compareLogicResult[$variable0[0]];
                            $arrayCombinePoint[$getProfessionIdFromProfessionName][] = $compareLogicResult[$variable1[0]];
                            $arrayCombinePoint[$getProfessionIdFromProfessionName][] = $compareLogicResult[$variable2[0]];
                            $arrayCombinePoint[$getProfessionIdFromProfessionName][] = $compareLogicResult[$variable3[0]];
                            $arrayCombinePoint[$getProfessionIdFromProfessionName][] = $compareLogicResult[$variable4[0]];
                            $arrayCombinePoint[$getProfessionIdFromProfessionName][] = $compareLogicResult[$variable5[0]];
                            $arrayCombinePoint[$getProfessionIdFromProfessionName][] = $compareLogicResult[$variable6[0]];
                            $arrayCombinePoint[$getProfessionIdFromProfessionName][] = $compareLogicResult[$variable7[0]];
                            $arrayCombinePoint[$getProfessionIdFromProfessionName][] = $compareLogicResult[$variable8[0]];
                            $arrayCombinePoint[$getProfessionIdFromProfessionName][] = $compareLogicResult[$variable9[0]];
                            $arrayCombinePoint[$getProfessionIdFromProfessionName][] = $compareLogicResult[$variable10[0]];
                            $arrayCombinePoint[$getProfessionIdFromProfessionName][] = $compareLogicResult[$variable11[0]];
                            $arrayCombinePoint[$getProfessionIdFromProfessionName][] = $compareLogicResult[$variable12[0]];
                            $arrayCombinePoint[$getProfessionIdFromProfessionName][] = $compareLogicResult[$variable13[0]];
                            $arrayCombinePoint[$getProfessionIdFromProfessionName][] = $compareLogicResult[$variable14[0]];
                            $arrayCombinePoint[$getProfessionIdFromProfessionName][] = $compareLogicResult[$variable15[0]];
                            $arrayCombinePoint[$getProfessionIdFromProfessionName][] = $compareLogicResult[$variable16[0]];
                            $arrayCombinePoint[$getProfessionIdFromProfessionName][] = $compareLogicResult[$variable17[0]];
                            $arrayCombinePoint[$getProfessionIdFromProfessionName][] = $compareLogicResult[$variable18[0]];
                            $arrayCombinePoint[$getProfessionIdFromProfessionName][] = $compareLogicResult[$variable19[0]];
                            $arrayCombinePoint[$getProfessionIdFromProfessionName][] = $compareLogicResult[$variable20[0]];
                            $arrayCombinePoint[$getProfessionIdFromProfessionName][] = $compareLogicResult[$variable21[0]];
                            $arrayCombinePoint[$getProfessionIdFromProfessionName][] = $compareLogicResult[$variable22[0]];
                            $arrayCombinePoint[$getProfessionIdFromProfessionName][] = $compareLogicResult[$variable23[0]];
                        }
                    }
                }
            }
            $arrayResult = $total = [];
            if (isset($arrayCombinePoint) && !empty($arrayCombinePoint)) {
                foreach ($arrayCombinePoint as $key => $val) {
                    $point = array_count_values($val);
                    $answer['professionId'] = $key;
                    $pingo = $this->professionsRepository->getProfessionsByProfessionId($key);

                    $answer['professionName'] = $pingo[0]->pf_name;
                    $answer['pf_logo'] = $pingo[0]->pf_logo;
                    $L = (isset($point['L'])) ? $point['L'] : 0;
                    $H = (isset($point['H'])) ? $point['H'] : 0;
                    $M = (isset($point['M'])) ? $point['M'] : 0;
                    //c=match, b=moderate, a=nomatch
                    if ($L > 0) {
                        $total[$key] = "A";
                        $answer['matchScale'] = "Tough";
                    } else if ($M > 0 && $L < 1) {
                        $total[$key] = "B";
                        $answer['matchScale'] = "Medium";
                    } else if ($L == 0 && $M == 0) {
                        $total[$key] = "C";
                        $answer['matchScale'] = "Easy";
                    } else {
                        $total[$key] = "C";
                        $answer['matchScale'] = "";
                    }
                    $level4Booster = Helpers::level4Booster($key, $id);
                    $getTeenagerAllTypeBadges = $this->teenagersRepository->getTeenagerAllTypeBadges($id, $key);

                    $totalPoints = 0;
                    if (!empty($getTeenagerAllTypeBadges)) {
                        if ($getTeenagerAllTypeBadges['level4Basic']['noOfAttemptedQuestion'] != 0) {
                            $totalPoints += $getTeenagerAllTypeBadges['level4Basic']['basicAttemptedTotalPoints'];
                        }
                        if ($getTeenagerAllTypeBadges['level4Intermediate']['noOfAttemptedQuestion'] != 0) {
                            foreach ($getTeenagerAllTypeBadges['level4Intermediate']['templateWiseEarnedPoint'] AS $k => $val) {
                               // if ($getTeenagerAllTypeBadges['level4Intermediate']['templateWiseEarnedPoint'][$k] != 0) {
                                    $totalPoints += $getTeenagerAllTypeBadges['level4Intermediate']['templateWiseTotalAttemptedPoint'][$k];
                              //  }
                            }
                        }
                        if ($getTeenagerAllTypeBadges['level4Advance']['earnedPoints'] != 0) {
                            $totalPoints += $getTeenagerAllTypeBadges['level4Advance']['advanceTotalPoints'];
                        }
                    }

                    $level2Data = '';
                    $level4PromisePlus = '';
                    $flag = false;
                    if ($totalPoints != 0) {
                        $level4PromisePlus = Helpers::calculateLevel4PromisePlus($level4Booster['yourScore'], $totalPoints);
                        $flag = true;
                    }

                    $PromisePlus = 0;
                    if ($flag) {
                        if ($level4PromisePlus >= Config::get('constant.NOMATCH_MIN_RANGE') && $level4PromisePlus <= Config::get('constant.NOMATCH_MAX_RANGE') ) {
                            $PromisePlus = "Easy";
                        } else if ($level4PromisePlus >= Config::get('constant.MODERATE_MIN_RANGE') && $level4PromisePlus <= Config::get('constant.MODERATE_MAX_RANGE') ) {
                        $PromisePlus = "Medium";
                        } else if ($level4PromisePlus >= Config::get('constant.MATCH_MIN_RANGE') && $level4PromisePlus <= Config::get('constant.MATCH_MAX_RANGE') ) {
                        $PromisePlus = "Tough";
                        } else {
                            $PromisePlus = "";
                        }
                    } else {
                         $PromisePlus = "";
                    }
                    $answer['promisePlus'] = $PromisePlus;
                    $arrayResult[$key] = $answer;
                }
            }

            $arrayResult2 = [];
            if (isset($total) && !empty($total)) {
                arsort($total);
                foreach ($total as $keyId => $keyValue) {
                    if (isset($arrayResult[$keyId])) {
                        $arrayResult2[] = $arrayResult[$keyId];
                    }
                }
            }
            //$response['systemMatchedProfession'] = $arrayResult;
            $response['systemMatchedProfession'] = $arrayResult2;
            } else {
                $response['systemMatchedProfession'] = [];
            }
            $this->log->info('Admin view teen Level3 detail',array('userid'=>$this->loggedInUser->user()->id,'teenid'=>$id));
            
            return view('admin.ViewTeenagerDetail', compact('response','professionOriginalImageUploadPath','boosterPoints','id','type'));
        }
        elseif($type == "level4"){
            $professionOriginalImageUploadPath = $this->professionOriginalImageUploadPath;
            $boosterPoints = $this->teenagersRepository->getTeenagerBoosterPoints($id);
            $l3Activity = $this->professionsRepository->getLevel3ActivityWithAnswer($id);
            $level4Data = array();

            //Get Level4 points for attempted professons
            if(isset($l3Activity) && !empty($l3Activity)){
                foreach($l3Activity as $key=>$val){
                    $level4Data[$val->id] = $this->teenagersRepository->getTeenagerAllTypeBadges($id, $val->id);
                    $level4Data[$val->id]['pf_name'] = $val->pf_name;
                    $level4Data[$val->id]['pf_logo'] = $val->pf_logo;
                }
            }
            $this->log->info('Admin view teen Level4 detail',array('userid'=>$this->loggedInUser->user()->id,'teenid'=>$id));
            return view('admin.ViewTeenagerDetail', compact('level4Data','professionOriginalImageUploadPath','boosterPoints','id','type'));
        }
        elseif($type == "points"){
            $boosterPoints = $this->teenagersRepository->getTeenagerBoosterPoints($id);
            $this->log->info('Admin view teen points detail',array('userid'=>$this->loggedInUser->user()->id,'teenid'=>$id));
            return view('admin.ViewTeenagerDetail', compact('boosterPoints','id','type'));
        }
        elseif($type == "learningstyle"){
            $professionArray = $this->professionsRepository->getTeenagerAttemptedProfession($id);
            $finalProfessionArray = [];
            $objLearningStyle = new LearningStyle();

            $userLearningData = $objLearningStyle->getLearningStyleDetails();
            $objProfession =  new Professions();
            $AllProData = $objProfession->getActiveProfessions();

            $TotalAttemptedP = 0;
            $allp = count($AllProData);
            $attemptedp = count($professionArray);
            $TotalAttemptedP = ($attemptedp * 100) / $allp;
            if (!empty($userLearningData)) {
                foreach ($userLearningData as $k => $value ) {
                    $userLearningData[$k]->earned_points = 0;
                    $userLearningData[$k]->total_points = 0;
                    $userLearningData[$k]->percentage = '';
                    $userLearningData[$k]->interpretationrange = '';
                    $userLearningData[$k]->totalAttemptedP = round($TotalAttemptedP);
                    $photo = $value->ls_image;
                    $value->ls_image = ($value->ls_image != "") ? Config::get('constant.DEFAULT_AWS') . $this->learningStyleThumbImageUploadPath . $photo : asset("/frontend/images/proteen-logo.png");
                }

                if (isset($professionArray) && !empty($professionArray)) {
                    foreach ($professionArray as $key => $val) {
                        $professionId = $val->id;
                        $getTeenagerAllTypeBadges = $this->teenagersRepository->getTeenagerAllTypeBadges($id, $professionId);
                        $level4Booster = Helpers::level4Booster($professionId, $id);
                        $l4BTotal = (isset($getTeenagerAllTypeBadges['level4Basic']) && !empty($getTeenagerAllTypeBadges['level4Basic'])) ? $getTeenagerAllTypeBadges['level4Basic']['basicAttemptedTotalPoints'] : '';
                        $l4ATotal = (isset($getTeenagerAllTypeBadges['level4Advance']) && !empty($getTeenagerAllTypeBadges['level4Advance'])) ? $getTeenagerAllTypeBadges['level4Advance']['earnedPoints'] : '';
                        $UserLerningStyle = [];
                        foreach ($userLearningData as $k => $value ) {
                            $userLData = $objLearningStyle->getLearningStyleDetailsByProfessionId($professionId,$value->parameterId,$id);

                            if (count($userLData)>0) {
                                $points = '';
                                $LAPoints = '';
                                $points = $userLData[0]->uls_earned_points;
                                $userLearningData[$k]->earned_points += $userLData[0]->uls_earned_points;
                                $activityName = $userLData[0]->activity_name;
                                if (strpos($activityName, ',') !== false) {
                                    $Activities = explode(",",$activityName);
                                    foreach ($Activities As $Akey => $acty) {
                                        if ($acty == 'L4B') {
                                                $userLearningData[$k]->total_points += $l4BTotal;
                                        } else if ($acty == 'L4AV') {
                                            if ($l4ATotal != 0) {
                                                $userLearningData[$k]->total_points += Config::get('constant.USER_L4_VIDEO_POINTS');
                                            }
                                        }else if ($acty == 'L4AP') {
                                            if ($l4ATotal != 0) {
                                                $userLearningData[$k]->total_points += Config::get('constant.USER_L4_PHOTO_POINTS');
                                            }
                                        }else if ($acty == 'L4AD') {
                                            if ($l4ATotal != 0) {
                                                $userLearningData[$k]->total_points += Config::get('constant.USER_L4_DOCUMENT_POINTS');
                                            }
                                        } else if ($acty == 'N/A') {
                                            if ($points != 0) {
                                                $userLearningData[$k]->total_points += '';
                                            }
                                        } else {
                                            if ($acty != '' && intval($acty) > 0) {
                                                $TotalPoints = $getTeenagerAllTypeBadges['level4Intermediate']['templateWiseTotalAttemptedPoint'][$acty];
                                                $userLearningData[$k]->total_points += $TotalPoints;
                                            }

                                        }
                                    }
                              } else {
                                  if ($activityName == 'L4B') {
                                        $userLearningData[$k]->total_points += $l4BTotal;
                                  } else if ($activityName == 'L4AV') {
                                      if ($l4ATotal != 0) {
                                          $userLearningData[$k]->total_points += Config::get('constant.USER_L4_VIDEO_POINTS');
                                      }
                                  }else if ($activityName == 'L4AP') {
                                      if ($l4ATotal != 0) {
                                          $userLearningData[$k]->total_points += Config::get('constant.USER_L4_PHOTO_POINTS');
                                      }
                                  }else if ($activityName == 'L4AD') {
                                      if ($l4ATotal != 0) {
                                          $userLearningData[$k]->total_points += Config::get('constant.USER_L4_DOCUMENT_POINTS');
                                      }
                                  } else if ($activityName == 'N/A') {
                                      if ($points != 0) {
                                          $userLearningData[$k]->total_points += '';
                                      }
                                  } else {
                                      if (intval($activityName) > 0) {
                                          $TotalPoints = $getTeenagerAllTypeBadges['level4Intermediate']['templateWiseTotalAttemptedPoint'][$activityName];
                                          $userLearningData[$k]->total_points += $TotalPoints;
                                      }
                                  }
                            }
                            if ($userLearningData[$k]->total_points != 0) {
                                $LAPoints = ($value->earned_points * 100) / $userLearningData[$k]->total_points;
                            }
                            $range = '';
                            $LAPoints = round($LAPoints);
                            if ($LAPoints >= Config::get('constant.LS_LOW_MIN_RANGE') && $LAPoints <= Config::get('constant.LS_LOW_MAX_RANGE') ) {
                                $range = "Low";
                            } else if ($LAPoints >= Config::get('constant.LS_MEDIUM_MIN_RANGE') && $LAPoints <= Config::get('constant.LS_MEDIUM_MAX_RANGE') ) {
                                $range = "Medium";
                            } else if ($LAPoints >= Config::get('constant.LS_HIGH_MIN_RANGE') && $LAPoints <= Config::get('constant.LS_HIGH_MAX_RANGE') ) {
                                $range = "High";
                            }
                            $userLearningData[$k]->interpretationrange = $range;
                            $userLearningData[$k]->percentage = $LAPoints;
                            }
                        }
                    }
                }
            }
            $this->log->info('Admin view teen learning guidance detail',array('userid'=>$this->loggedInUser->user()->id,'teenid'=>$id));
            return view('admin.ViewTeenagerDetail', compact('userLearningData','id','type'));
        }
    }

    public function editUserPaymentApproved($id)
    {
        $userid = intval($id);
        $teenagerDetailbyId = $this->teenagersRepository->getTeenagerById($id);
        if($teenagerDetailbyId->t_payment_status == 1)
        {
            $teenagerPaymentDetail = array();
            $teenagerPaymentDetail['t_payment_status'] = 2;
            $teenagerPaymentDetail['t_isverified'] = 1;
            $teenagerPaymentDetail['t_sponsor_choice'] = 1;
            $return = $this->teenagersRepository->updatePaymentStatus($userid,$teenagerPaymentDetail);
            $this->teenagersRepository->deleteTeenagerSponsors($userid);
            if($return)
            {            
                // --------------------start sending mail -----------------------------//
                $replaceArray = array();
                $replaceArray['TEEN_NAME'] = $teenagerDetailbyId->t_name;

                //If user has selected Payment option                       
                $emailTemplateContent = $this->templateRepository->getEmailTemplateDataByName(Config::get('constant.PAYMENT_APPROVED_TEMPLATE'));
                $content = $this->templateRepository->getEmailContent($emailTemplateContent->et_body, $replaceArray);
                $data = array();
                $data['subject'] = $emailTemplateContent->et_subject;
                $data['toEmail'] = $teenagerDetailbyId->t_email;
                $data['toName'] = $teenagerDetailbyId->t_name;
                $data['content'] = $content;
                $data['teen_id'] = $teenagerDetailbyId->id;
                Mail::send(['html' => 'emails.Template'], $data, function($message) use ($data) {
                    $message->subject($data['subject']);
                    $message->to($data['toEmail'], $data['toName']);
                });
                return Redirect::to("admin/teenagers")->with('success', 'User\'s Payment verified successfully');
            }
            else
            {
                return Redirect::to("admin/teenagers")->with('error', trans('labels.commonerrormessage'));
            }
        }else{
            return Redirect::to("admin/teenagers")->with('error', 'This user has already verified');
        }
    }
    
    public function exportl4data($id)
    {
        ob_start();
        $teenData = $this->teenagersRepository->getTeenagerById($id);
        $filename = trans('labels.teenagerdata');
        $fp = fopen('php://output', 'w');
        $FieldArray = [];
        $teenArray = [];
        $blankArray = [];

        $teenArray['Teen'] = 'Teen';
        $teenArray['Name'] = $teenData->t_name;
        $teenArray['Email'] = $teenData->t_email;
        $teenArray['Uniqueid'] = 'Unique Id';
        $teenArray['Unique Id'] = $teenData->t_uniqueid;

        $blankArray['Teen'] = ' ';
        $blankArray['Name'] = ' ';
        $blankArray['Email'] = ' ';
        $blankArray['Uniqueid'] = ' ';
        $blankArray['Unique Id'] = ' ';

        $FieldArray['profession'] = 'Profession';
        $FieldArray['level4b'] = 'Level4 Basic';
        $FieldArray['level4i'] = 'Level4 Intermediate';
        $FieldArray['level4a'] = 'Level4 Advance';
        $FieldArray['total'] = 'Total';
        $FieldArray['concepts'] = 'Concepts Points';
        $FieldArray['advanceactivity'] = 'Advance Activities';

        $l3Activity = $this->professionsRepository->getLevel3ActivityWithAnswer($id);
        $level4Data = array();
        if(isset($l3Activity) && !empty($l3Activity)){
            fputcsv($fp, $teenArray);
            fputcsv($fp, $blankArray);
            fputcsv($fp, $FieldArray);
            foreach($l3Activity as $key=>$val){
                $FieldArray['total'] = 0;
                $level4Data = $this->teenagersRepository->getTeenagerAllTypeBadges($id, $val->id);
                $FieldArray['advanceactivity'] = 'Approved Photos - '.$level4Data['level4Advance']['snap']."\n"."Approved Document - ".$level4Data['level4Advance']['report']."\n"."Approved Video - ".$level4Data['level4Advance']['shoot'];
                if(isset($level4Data['level4Intermediate']['templateWiseEarnedPoint']) && !empty($level4Data['level4Intermediate']['templateWiseEarnedPoint'])){
                    $level4concept = '';
                    foreach($level4Data['level4Intermediate']['templateWiseEarnedPoint'] as $templateId=>$point){
                       $level4TemplateDetail = '';
                       $level4TemplateDetail = $this->level4ActivitiesRepository->getGamificationTemplateById($templateId);
                       $level4concept .= $level4TemplateDetail->gt_template_title.' -- '.$point."\n";
                    }
                }else{
                    $level4concept = 'N/A';
                }
                $FieldArray['profession'] = $val->pf_name;
                $FieldArray['level4b'] = $level4Data['level4Basic']['earnedPoints'];
                $FieldArray['level4i'] = $level4Data['level4Intermediate']['earnedPoints'];
                $FieldArray['level4a'] = $level4Data['level4Advance']['earnedPoints'];
                $FieldArray['total'] = $level4Data['level4Basic']['earnedPoints']+$level4Data['level4Intermediate']['earnedPoints']+$level4Data['level4Advance']['earnedPoints'];
                $FieldArray['concepts'] = $level4concept;
                fputcsv($fp, $FieldArray);
            }
        }
        $this->log->info('Admin view teen L4 Data',array('userid'=>$this->loggedInUser->user()->id,'teenid'=>$id));
        header('Content-type: application/csv');
        header('Content-Disposition: attachment; filename=' . $filename);
        exit;
    }

    public function addCoinsDataForTeenager() {
        $teenager_Id = $_REQUEST['teenid'];
        $data = [];
        $data['teenager_Id'] = $teenager_Id;
        // $data['searchBy'] = $_REQUEST['searchBy'];
        // $data['searchText'] = $_REQUEST['searchText'];
        // $data['orderBy'] = $_REQUEST['orderBy'];
        // $data['sortOrder'] = $_REQUEST['sortOrder'];
        //$data['page'] = $_REQUEST['page'];
        $teenagerDetail = $this->teenagersRepository->getTeenagerById($teenager_Id);
        $this->log->info('Admin add ProCoins data for teen',array('userid'=>$this->loggedInUser->user()->id,'teenid'=>$teenager_Id));
        return view('admin.AddCoinsDataForTeenager', compact('teenagerDetail','data'));
    }

    public function saveCoinsDataForTeen() {

        $id = e(Input::get('id'));
        $coins = e(Input::get('t_coins'));
        $giftCoins = e(Input::get('t_coins'));

        $searchParamArray = [];
        //$searchParamArray['searchBy'] = e(Input::get('searchBy'));
        // $searchParamArray['searchText'] = Input::get('searchText');
        // $searchParamArray['orderBy'] = e(Input::get('orderBy'));
        // $searchParamArray['sortOrder'] = e(Input::get('sortOrder'));
        
        if (!empty($searchParamArray)) {
            Cache::forever('searchArray', $searchParamArray);
        } else {
            Cache::forget('searchArray');
        }
        $flag = 0;
        $userData = $this->teenagersRepository->getUserDataForCoinsDetail($id);
        if (!empty($userData)) {
            if (substr($coins, 0, 1) === '-') {
                $coins = preg_replace('/[-?]/', '', $coins);
                if ($userData['t_coins'] > 0 && $coins <= $userData['t_coins']) {
                    $coins = $userData['t_coins']-$coins;
                } else {
                    return Redirect::to("admin/teenagers")->with('error', trans('labels.commonerrormessage'));
                }
            } else if (is_numeric($coins)) {
                $coins += $userData['t_coins'];
                $flag++;
            }
        }
        $result = $this->teenagersRepository->updateTeenagerCoinsDetail($id, $coins);
        $userArray = $this->teenagersRepository->getTeenagerByTeenagerId($id);
        $objGiftUser = new TeenagerCoinsGift();
        if($flag) {
            $saveData = [];
            $saveData['tcg_sender_id'] = 0;
            $saveData['tcg_reciver_id'] = $id;
            $saveData['tcg_total_coins'] = $giftCoins;
            $saveData['tcg_gift_date'] = date('Y-m-d');
            $saveData['tcg_user_type'] = 1;

            $return = $objGiftUser->saveTeenagetGiftCoinsDetail($saveData);

            $replaceArray = array();
            $replaceArray['TEEN_NAME'] = $userArray['t_name'];
            $replaceArray['COINS'] = $giftCoins;
            $replaceArray['FROM_USER'] = $this->loggedInUser->user()->name;
            $emailTemplateContent = $this->templateRepository->getEmailTemplateDataByName(Config::get('constant.COINS_RECEIBED_TEMPLATE'));
            $content = $this->templateRepository->getEmailContent($emailTemplateContent->et_body, $replaceArray);

            $data = array();
            $data['subject'] = $emailTemplateContent->et_subject;
            $data['toEmail'] = $userArray['t_email'];
            $data['toName'] = $userArray['t_name'];
            $data['content'] = $content;

            Mail::send(['html' => 'emails.Template'], $data , function ($m) use ($data) {
               // $m->from(env("MAIL_USERNAME", "custom.owncloud@gmail.com"), 'Gift ProCoins');
                $m->subject($data['subject']);
                $m->to($data['toEmail'], $data['toName']);
            });
        }
        $this->log->info('Admin save ProCoins data for teen',array('userid'=>$this->loggedInUser->user()->id,'teenid'=>$id));
        return Redirect::to("admin/teenagers")->with('success', trans('labels.coinsaddsuccess'));
    }

    public function addCoinsForAllTeenager(){
        $coins = e(Input::get('t_coins'));
        $userData = $this->teenagersRepository->getAllUsersCoinsDetail();
        $saveData = [];
        if (!empty($userData)) {
            foreach ($userData AS $key => $value) {
                $data = [];
                $data['id'] = $value['id'];
                $data['t_coins'] = $value['t_coins'] + $coins;
                $saveData[] = $data;
            }
        }
        foreach ($saveData AS $k => $val) {
            $result = $this->teenagersRepository->updateTeenagerCoinsDetail($val['id'], $val['t_coins']);
        }
        $this->log->info('Admin gif ProCoins data to all teenagers',array('userid'=>$this->loggedInUser->user()->id));
        return Redirect::to("admin/teenagers")->with('success', trans('labels.coinsaddsuccess'));
    }

    public function clearCache() {
        if (Cache::has('teenagerDetail')) {
            Cache::forget('teenagerDetail');
        }
        $this->log->info('Admin clear cached data',array('userid'=>$this->loggedInUser->user()->id));
        return Redirect::to("admin/teenagers");
    }
}
