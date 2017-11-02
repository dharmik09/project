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

class TeenagerManagementController extends Controller {

    public function __construct(TeenagersRepository $TeenagersRepository, SponsorsRepository $SponsorsRepository, ProfessionsRepository $ProfessionsRepository, Level1ActivitiesRepository $Level1ActivitiesRepository, Level2ActivitiesRepository $Level2ActivitiesRepository,TemplatesRepository $TemplatesRepository,Level4ActivitiesRepository $Level4ActivitiesRepository) {
        $this->middleware('auth.admin');
        $this->objTeenagers = new Teenagers();
        $this->TeenagersRepository = $TeenagersRepository;
        $this->SponsorsRepository = $SponsorsRepository;
        $this->ProfessionsRepository = $ProfessionsRepository;
        $this->Level1ActivitiesRepository = $Level1ActivitiesRepository;
        $this->Level2ActivitiesRepository = $Level2ActivitiesRepository;
        $this->Level4ActivitiesRepository = $Level4ActivitiesRepository;
        $this->teenOriginalImageUploadPath = Config::get('constant.TEEN_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->teenThumbImageUploadPath = Config::get('constant.TEEN_THUMB_IMAGE_UPLOAD_PATH');
        $this->professionThumbImageUploadPath = Config::get('constant.PROFESSION_THUMB_IMAGE_UPLOAD_PATH');
        $this->professionOriginalImageUploadPath = Config::get('constant.PROFESSION_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->teenThumbImageHeight = Config::get('constant.TEEN_THUMB_IMAGE_HEIGHT');
        $this->teenThumbImageWidth = Config::get('constant.TEEN_THUMB_IMAGE_WIDTH');
        $this->controller = 'TeenagerManagementController';
        $this->loggedInUser = Auth::admin()->get();
        $this->interestOriginalImageUploadPath = Config::get('constant.INTEREST_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->apptitudeOriginalImageUploadPath = Config::get('constant.APPTITUDE_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->miOriginalImageUploadPath = Config::get('constant.MI_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->personalityOriginalImageUploadPath = Config::get('constant.PERSONALITY_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->TemplateRepository = $TemplatesRepository;
        $this->cartoonThumbImageUploadPath = Config::get('constant.CARTOON_THUMB_IMAGE_UPLOAD_PATH');
        $this->cartoonOriginalImageUploadPath = Config::get('constant.CARTOON_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->humanThumbImageUploadPath = Config::get('constant.HUMAN_THUMB_IMAGE_UPLOAD_PATH');
        $this->humanOriginalImageUploadPath = Config::get('constant.HUMAN_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->relationIconOriginalImageUploadPath = Config::get('constant.RELATION_ICON_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->relationIconThumbImageUploadPath = Config::get('constant.RELATION_ICON_THUMB_IMAGE_UPLOAD_PATH');
        $this->learningStyleThumbImageUploadPath = Config::get('constant.LEARNING_STYLE_THUMB_IMAGE_UPLOAD_PATH');

    }

    public function index() {
        $searchParamArray = Input::all();
        
        $currentPage = (isset($searchParamArray['gotopage']) && $searchParamArray['gotopage'] > 0 )?$searchParamArray['gotopage']:0;
        if (isset($searchParamArray['clearSearch'])) {
            unset($searchParamArray);
            Cache::forget('searchArray');
            Cache::forget('teenagerDetail');
            $searchParamArray = array();
        }
        if (!empty($searchParamArray)) {
            Cache::forget('teenagerDetail');
            if (isset($searchParamArray['page'])) {
                if (Cache::has('searchArray')) {
                    $searchParamArray = Cache::get('searchArray');
                } else {
                    Cache::forget('searchArray');
                }
            } else {
                Cache::forget('searchArray');
            }
            $teenagers = $this->TeenagersRepository->getAllTeenagers($searchParamArray,$currentPage);
        } else {
            if (Cache::has('searchArray')) {
                $searchParamArray = Cache::get('searchArray');
            }
            if (Cache::has('teenagerDetail')) {
                $teenagers = Cache::get('teenagerDetail');
            } else {
                $teenagers = $this->TeenagersRepository->getAllTeenagers($searchParamArray,$currentPage);
                Cache::forever('teenagerDetail', $teenagers);
            }
        }
        Helpers::createAudit($this->loggedInUser->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_READ'), $this->controller . "@index", $_SERVER['REQUEST_URI'], Config::get('constant.AUDIT_ORIGIN_WEB'), '', '', $_SERVER['REMOTE_ADDR']);

        return view('admin.ListTeenager', compact('teenagers', 'searchParamArray','currentPage'));
    }

    public function add() {
        $teenagerDetail = [];
        Helpers::createAudit($this->loggedInUser->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_READ'), $this->controller . "@add", $_SERVER['REQUEST_URI'], Config::get('constant.AUDIT_ORIGIN_WEB'), '', '', $_SERVER['REMOTE_ADDR']);
        $sponsorDetail = $this->SponsorsRepository->getApprovedSponsors();
        return view('admin.EditTeenager', compact('teenagerDetail', 'sponsorDetail'));
    }

    public function edit($id, $sid) {
        $uploadTeenagerThumbPath = $this->teenThumbImageUploadPath;
        //$teenagerDetail = $this->objTeenagers->find($id);
        $teenagerDetail = $this->TeenagersRepository->getTeenagerById($id);
        Helpers::createAudit($this->loggedInUser->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_READ'), $this->controller . "@edit", $_SERVER['REQUEST_URI'], Config::get('constant.AUDIT_ORIGIN_WEB'), '', '', $_SERVER['REMOTE_ADDR']);
        $selectedSponsors = array();
        if (isset($teenagerDetail->t_sponsors) && !empty($teenagerDetail->t_sponsors)) {
            foreach ($teenagerDetail->t_sponsors as $key => $val) {
                $selectedSponsors[] = $val->sponsor_id;
            }
        }
        $sponsorDetail = $this->SponsorsRepository->getApprovedSponsors();
        return view('admin.EditTeenager', compact('teenagerDetail', 'sid', 'uploadTeenagerThumbPath','sponsorDetail', 'selectedSponsors'));
    }

    public function save(TeenagerRequest $TeenagerRequest) {

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
        $teenagerDetail['t_credit'] = e(Input::get('fu_address'));
        $teenagerDetail['t_boosterpoints'] = e(Input::get('t_boosterpoints'));
        $teenagerDetail['t_isfirstlogin'] = e(Input::get('t_isfirstlogin'));
        $teenagerDetail['t_sponsor_choice'] = e(Input::get('t_sponsor_choice'));
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
                        $imageOriginal = public_path($this->teenOriginalImageUploadPath . $hiddenProfile);
                        $imageThumb = public_path($this->teenThumbImageUploadPath . $hiddenProfile);
                        if(file_exists($imageOriginal) && $hiddenProfile != ''){File::delete($imageOriginal);}
                        if(file_exists($imageThumb) && $hiddenProfile != ''){File::delete($imageThumb);}
                    }

                    $teenagerDetail['t_photo'] = $fileName;
                }
            }
        }
        if (isset($teenagerDetail['t_email']) && $teenagerDetail['t_email'] != '') {
            $teenagerEmailExist = $this->TeenagersRepository->checkActiveEmailExist($teenagerDetail['t_email'], $teenagerDetail['id']);
        }
        if (isset($teenagerDetail['t_phone']) && $teenagerDetail['t_phone'] != '') {
            $teenagerMobileExist = $this->TeenagersRepository->checkActivePhoneExist($teenagerDetail['t_phone'], $teenagerDetail['id']);
        }
        else
        {
            $teenagerMobileExist = '';
        }

        $response = ($teenagerEmailExist == '' && $teenagerMobileExist == '') ? $this->TeenagersRepository->saveTeenagerDetail($teenagerDetail) : '0';
        Cache::forget('teenagerDetail');
        if ($response) {
            if (isset($sponsors) && !empty($sponsors) && Input::get('t_sponsor_choice') == 2) {
                $sponsorDetail = $this->TeenagersRepository->saveTeenagerSponserId($response->id, implode(',', $sponsors));
            }
            Helpers::createAudit($this->loggedInUser->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_UPDATE'), Config::get('databaseconstants.TBL_TEENAGERS'), $response, Config::get('constant.AUDIT_ORIGIN_WEB'), trans('labels.teenupdatesuccess'), serialize($teenagerDetail), $_SERVER['REMOTE_ADDR']);
            if(isset($sid) && !empty($sid) && $sid > 0)
            {
                return Redirect::to("/admin/viewstudentlist/$sid")->with('success', trans('labels.teenupdatesuccess'));
            }
            else
            {
                return Redirect::to("admin/teenagers".$postData['pageRank'])->with('success', trans('labels.teenupdatesuccess'));
            }
        } else {
            Helpers::createAudit($this->loggedInUser->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_UPDATE'), Config::get('databaseconstants.TBL_TEENAGERS'), $response, Config::get('constant.AUDIT_ORIGIN_WEB'), trans('labels.somethingwrong'), serialize($teenagerDetail), $_SERVER['REMOTE_ADDR']);
            if(isset($sid) && !empty($sid) && $sid > 0)
            {
                return Redirect::to("/admin/viewstudentlist/$sid")->with('success', trans('labels.teenagererrormessage'));
            }
            else
            {
                return Redirect::to("admin/teenagers".$postData['pageRank'])->with('error', trans('labels.teenagererrormessage'));
            }
        }
    }

    public function delete($id) {
        $return = $this->TeenagersRepository->deleteTeenager($id);
        if ($return) {
            Helpers::createAudit($this->loggedInUser->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_DELETE'), Config::get('databaseconstants.TBL_TEENAGERS'), $id, Config::get('constant.AUDIT_ORIGIN_WEB'), trans('labels.teendeletesuccess'), '', $_SERVER['REMOTE_ADDR']);

            return Redirect::to("admin/teenagers")->with('success', trans('labels.teendeletesuccess'));
        } else {
            Helpers::createAudit($this->loggedInUser->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_DELETE'), Config::get('databaseconstants.TBL_TEENAGERS'), $id, Config::get('constant.AUDIT_ORIGIN_WEB'), trans('labels.somethingwrong'), '', $_SERVER['REMOTE_ADDR']);

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
                        $teenagerEmailExist = $this->TeenagersRepository->checkActiveEmailExist($data['t_email']);
                    }
                    if (isset($data['t_phone']) && $data['t_phone'] != '') {
                        $teenagerMobileExist = $this->TeenagersRepository->checkActivePhoneExist($data['t_phone']);
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
                $data['t_country'] = $this->TeenagersRepository->getCountryIdByName($data['t_country']);
                $data['t_country'] = (isset($data['t_country']->id)) ? $data['t_country']->id : '0';
            }
            if (isset($data['t_school'])) {
                $data['t_school'] = $this->TeenagersRepository->getSchoolIdByName($data['t_school']);
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
            $response = $this->TeenagersRepository->saveTeenagerDetail($data);
        }
        if ($response) {
            Helpers::createAudit($this->loggedInUser->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_UPDATE'), Config::get('databaseconstants.TBL_TEENAGERS'), $response, Config::get('constant.AUDIT_ORIGIN_WEB'), trans('labels.teenupdatesuccess'), serialize($teenagerDetail), $_SERVER['REMOTE_ADDR']);

            return Redirect::to("admin/teenagers")->with('success', trans('labels.teenaddsuccess'));
        } else {
            Helpers::createAudit($this->loggedInUser->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_UPDATE'), Config::get('databaseconstants.TBL_TEENAGERS'), $response, Config::get('constant.AUDIT_ORIGIN_WEB'), trans('labels.somethingwrong'), serialize($teenagerDetail), $_SERVER['REMOTE_ADDR']);

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
        exit;
    }

    public function exportData() {
        ob_start();
        $teenagerData = $this->TeenagersRepository->getAllTeenagersExport();


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
            $boosterPoints = $this->TeenagersRepository->getTeenagerBoosterPoints($teendata->id);
            //get teen parents/counsellor
            $parentCounsellor = $this->TeenagersRepository->getTeenParents($teendata->id);
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
            $sponsors = $this->TeenagersRepository->getTeenagerById($teendata->id);

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
                $checkIfMailSent = $this->TeenagersRepository->checkMailSentOrNot($teendata->id);                 
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
        header('Content-type: application/csv');
        header('Content-Disposition: attachment; filename=' . $filename);
        exit;
    }

    public function viewDetail($id){
        $uploadTeenagerThumbPath = $this->teenThumbImageUploadPath;
        $viewTeenDetail = $this->TeenagersRepository->getTeenagerById($id);
        $l1Activity = $this->Level1ActivitiesRepository->getLevel1ActivityWithAnswer($id);
        $l2Activity = $this->Level2ActivitiesRepository->getLevel2ActivityWithAnswer($id);
        $l3Activity = $this->ProfessionsRepository->getLevel3ActivityWithAnswer($id);
        $boosterPoints = $this->TeenagersRepository->getTeenagerBoosterPoints($id);
        $teenagerAPIData = Helpers::getTeenAPIScore($id);

        $totalQuestion = $this->Level2ActivitiesRepository->getNoOfTotalQuestionsAttemptedQuestion($id);
        if (isset($totalQuestion[0]->NoOfAttemptedQuestions) && $totalQuestion[0]->NoOfAttemptedQuestions > 0) {
        $response['NoOfAttemptedQuestionsLevel2'] = $totalQuestion[0]->NoOfAttemptedQuestions;
        $getTeenagerAttemptedProfession = $this->ProfessionsRepository->getTeenagerAttemptedProfession($id);
        if (isset($getTeenagerAttemptedProfession) && !empty($getTeenagerAttemptedProfession)) {
            $response['teenagerAttemptedProfession'] = $getTeenagerAttemptedProfession;
        } else {
            $response['teenagerAttemptedProfession'] = array();
        }
        $getLevel2AssessmentResult = Helpers::getTeenAPIScore($id);
        $getCareerMappingFromSystem = Helpers::getCareerMappingFromSystem();

        if (isset($getTeenagerAttemptedProfession) && !empty($getTeenagerAttemptedProfession)) {
            foreach ($getTeenagerAttemptedProfession as $keyProfession => $professionName) {
                $getProfessionIdFromProfessionName = $this->ProfessionsRepository->getProfessionIdByName($professionName->pf_name);
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
                $pingo = $this->ProfessionsRepository->getProfessionsByProfessionId($key);

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
                $getTeenagerAllTypeBadges = $this->TeenagersRepository->getTeenagerAllTypeBadges($id, $key);

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


        //get teen parent/counsellor data
        $parentCounsellor = $this->TeenagersRepository->getTeenParents($id);
        $viewTeenDetail->parentcounsellor = $parentCounsellor;

        $teenagerMyIcons = array();
        //Get teenager choosen Icon
        $teenagerIcons = $this->TeenagersRepository->getTeenagerSelectedIcon($id);

        $relationIcon = array();
        $fictionIcon = array();
        $nonFiction = array();

        if (isset($teenagerIcons) && !empty($teenagerIcons)) {
            foreach ($teenagerIcons as $key => $icon) {
                if ($icon->ti_icon_type == 1) {
                    if ($icon->fiction_image != '' && file_exists($this->cartoonOriginalImageUploadPath . $icon->fiction_image)) {
                        $fictionIcon[$key]['image'] = asset($this->cartoonOriginalImageUploadPath . $icon->fiction_image);
                    } else {
                        $fictionIcon[$key]['image'] = asset($this->cartoonOriginalImageUploadPath . 'proteen-logo.png');
                    }
                    $fictionIcon[$key]['iconname'] = $icon->ci_name;
                    $fictionIcon[$key]['category'] = $icon->cic_name;
                } elseif ($icon->ti_icon_type == 2) {
                    if ($icon->nonfiction_image != '' && file_exists($this->humanOriginalImageUploadPath . $icon->nonfiction_image)) {
                        $nonFiction[$key]['image'] = asset($this->humanOriginalImageUploadPath . $icon->nonfiction_image);
                    } else {
                        $nonFiction[$key]['image'] = asset($this->humanOriginalImageUploadPath . 'proteen-logo.png');
                    }
                    $nonFiction[$key]['iconname'] = $icon->hi_name;
                    $nonFiction[$key]['category'] = $icon->hic_name;
                } else {
                    if ($icon->ti_icon_image != '' && file_exists($this->relationIconOriginalImageUploadPath . $icon->ti_icon_image)) {
                        $relationIcon[$key]['image'] = asset($this->relationIconOriginalImageUploadPath . $icon->ti_icon_image);
                    }else{
                        $relationIcon[$key]['image'] = asset($this->relationIconOriginalImageUploadPath . 'proteen-logo.png');
                    }
                    $relationIcon[$key]['iconname'] = $icon->ti_icon_name;
                    $relationIcon[$key]['category'] = $icon->rel_name;
                }
            }
            $teenagerMyIcons = array_merge($fictionIcon, $nonFiction, $relationIcon);
            //$response['data']['fiction'] = $fictionIcon;
        }

        //Teenager API Data
        $teenagerInterest = array();
        $teenagerApptitude = array();
        $teenagerPersonality = array();
        $teenagerMI = array();
        $level4Data = array();
        $finalMIParameters = array();
        $teenagerAPIData = Helpers::getTeenAPIScore($id);

        if (isset($teenagerAPIData) && !empty($teenagerAPIData)) {
            $i = 1;
            // Teenager interest data
            foreach ($teenagerAPIData['APIscore']['interest'] as $interest => $val) {
                if ($val == 1) {
                    $interestImage = Helpers::getInterestData($interest);
                    if (!empty($interestImage)) {
                        if ($interestImage->it_logo != '' && file_exists($this->interestOriginalImageUploadPath . $interestImage->it_logo)) {
                            $image = asset($this->interestOriginalImageUploadPath . $interestImage->it_logo);
                        } else {
                            $image = asset($this->interestOriginalImageUploadPath . 'proteen-logo.png');
                        }
                    }
                    $teenagerInterest[] = array('image' => $image, 'interest' => $interest);
                }
                $i++;
            }

            // Teenager Apptitude data
            $k = 1;
            foreach ($teenagerAPIData['APIscore']['aptitude'] as $aptitude => $val) {
                 $aptitudemage = Helpers::getApptitudeData($aptitude);
                    if (!empty($aptitudemage)) {
                        if ($aptitudemage->apt_logo != '' && file_exists($this->apptitudeOriginalImageUploadPath . $aptitudemage->apt_logo)) {
                            $image = asset($this->apptitudeOriginalImageUploadPath . $aptitudemage->apt_logo);
                        } else {
                            $image = asset($this->apptitudeOriginalImageUploadPath . 'proteen-logo.png');
                        }
                    }

                    $aptitudescale = $teenagerAPIData['APIscale']['aptitude'][$aptitude];
                    $teenagerApptitude[] = array('image' => $image, 'aptitude' => $aptitude, 'scale' => $aptitudescale, 'score' => $val);
                $k++;
            }

            // Teenager MI Data
            foreach ($teenagerAPIData['APIscore']['MI'] as $mi => $val) {
                    $miimage = Helpers::getMIData($mi);
                    if (!empty($miimage)) {
                        if ($miimage->mit_logo != '' && file_exists($this->miOriginalImageUploadPath . $miimage->mit_logo)) {
                            $image = asset($this->miOriginalImageUploadPath . $miimage->mit_logo);
                        } else {
                            $image = asset($this->miOriginalImageUploadPath . 'proteen-logo.png');
                        }
                    }
                    $miscale = $teenagerAPIData['APIscale']['MI'][$mi];
                    $teenagerMI[] = array('image' => $image, 'aptitude' => $mi, 'scale' => $miscale, 'score' => $val);
            }
            // Teenager personality Data
            foreach ($teenagerAPIData['APIscore']['personality'] as $personality => $val) {
                    $personalityimage = Helpers::getPersonalityData($personality);
                    if (!empty($personalityimage)) {
                        if ($personalityimage->pt_logo != '' && file_exists($this->personalityOriginalImageUploadPath . $personalityimage->pt_logo)) {
                            $image = asset($this->personalityOriginalImageUploadPath . $personalityimage->pt_logo);
                        } else {
                            $image = asset($this->personalityOriginalImageUploadPath . 'proteen-logo.png');
                        }
                    }
                    $personalityscale = $teenagerAPIData['APIscale']['personality'][$personality];
                    $teenagerPersonality[] = array('image' => $image, 'aptitude' => $personality, 'scale' => $personalityscale, 'score' => $val);
            }
            $finalMIParameters = array_merge($teenagerApptitude,$teenagerMI,$teenagerPersonality);
        }

        //Get Level4 points for attempted professons
        if(isset($l3Activity) && !empty($l3Activity)){
            foreach($l3Activity as $key=>$val){
                $level4Data[$val->id] = $this->TeenagersRepository->getTeenagerAllTypeBadges($id, $val->id);
                $level4Data[$val->id]['pf_name'] = $val->pf_name;
                $level4Data[$val->id]['pf_logo'] = $val->pf_logo;
            }
        }

        //get user learning data
        $professionArray = $this->ProfessionsRepository->getTeenagerAttemptedProfession($id);
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
                if ($photo != '' && file_exists($this->learningStyleThumbImageUploadPath . $photo)) {
                    $value->ls_image = asset($this->learningStyleThumbImageUploadPath . $photo);
                } else {
                    $value->ls_image = asset("/frontend/images/proteen-logo.png");
                }
            }

            if (isset($professionArray) && !empty($professionArray)) {
                foreach ($professionArray as $key => $val) {
                    $professionId = $val->id;
                    $getTeenagerAllTypeBadges = $this->TeenagersRepository->getTeenagerAllTypeBadges($id, $professionId);
                    $level4Booster = Helpers::level4Booster($professionId, $id);
                    $l4BTotal = (isset($getTeenagerAllTypeBadges['level4Basic']) && !empty($getTeenagerAllTypeBadges['level4Basic'])) ? $getTeenagerAllTypeBadges['level4Basic']['basicAttemptedTotalPoints'] : '';
                    $l4ATotal = (isset($getTeenagerAllTypeBadges['level4Advance']) && !empty($getTeenagerAllTypeBadges['level4Advance'])) ? $getTeenagerAllTypeBadges['level4Advance']['earnedPoints'] : '';
                    $UserLerningStyle = [];
                    foreach ($userLearningData as $k => $value ) {
                        $userLData = $objLearningStyle->getLearningStyleDetailsByProfessionId($professionId,$value->parameterId,$id);

                        if (!empty($userLData)) {
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

        $uploadProfessionThumbPath = $this->professionThumbImageUploadPath;
        $professionOriginalImageUploadPath = $this->professionOriginalImageUploadPath;
        return view('admin.viewTeenagerDetail', compact('viewTeenDetail','uploadTeenagerThumbPath','l1Activity','l2Activity','l3Activity','boosterPoints','uploadProfessionThumbPath','finalMIParameters','professionOriginalImageUploadPath','level4Data','teenagerMyIcons','response','userLearningData'));
    }

    public function editUserPaymentApproved($id)
    {
        $userid = intval($id);
        $teenagerDetailbyId = $this->TeenagersRepository->getTeenagerById($id);
        if($teenagerDetailbyId->t_payment_status == 1)
        {
            $teenagerPaymentDetail = array();
            $teenagerPaymentDetail['t_payment_status'] = 2;
            $teenagerPaymentDetail['t_isverified'] = 1;
            $teenagerPaymentDetail['t_sponsor_choice'] = 1;
            $return = $this->TeenagersRepository->updatePaymentStatus($userid,$teenagerPaymentDetail);
            $this->TeenagersRepository->deleteTeenagerSponsors($userid);
            if($return)
            {            
                // --------------------start sending mail -----------------------------//
                $replaceArray = array();
                $replaceArray['TEEN_NAME'] = $teenagerDetailbyId->t_name;

                //If user has selected Payment option                       
                $emailTemplateContent = $this->TemplateRepository->getEmailTemplateDataByName(Config::get('constant.PAYMENT_APPROVED_TEMPLATE'));
                $content = $this->TemplateRepository->getEmailContent($emailTemplateContent->et_body, $replaceArray);
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
        $teenData = $this->TeenagersRepository->getTeenagerById($id);
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

        $l3Activity = $this->ProfessionsRepository->getLevel3ActivityWithAnswer($id);
        $level4Data = array();
        if(isset($l3Activity) && !empty($l3Activity)){
            fputcsv($fp, $teenArray);
            fputcsv($fp, $blankArray);
            fputcsv($fp, $FieldArray);
            foreach($l3Activity as $key=>$val){
                $FieldArray['total'] = 0;
                $level4Data = $this->TeenagersRepository->getTeenagerAllTypeBadges($id, $val->id);
                $FieldArray['advanceactivity'] = 'Approved Photos - '.$level4Data['level4Advance']['snap']."\n"."Approved Document - ".$level4Data['level4Advance']['report']."\n"."Approved Video - ".$level4Data['level4Advance']['shoot'];
                if(isset($level4Data['level4Intermediate']['templateWiseEarnedPoint']) && !empty($level4Data['level4Intermediate']['templateWiseEarnedPoint'])){
                    $level4concept = '';
                    foreach($level4Data['level4Intermediate']['templateWiseEarnedPoint'] as $templateId=>$point){
                       $level4TemplateDetail = '';
                       $level4TemplateDetail = $this->Level4ActivitiesRepository->getGamificationTemplateById($templateId);
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
        header('Content-type: application/csv');
        header('Content-Disposition: attachment; filename=' . $filename);
        exit;
    }

    public function addCoinsDataForTeenager() {
        $teenager_Id = $_REQUEST['teenid'];
        $data = [];
        $data['teenager_Id'] = $teenager_Id;
        $data['searchBy'] = $_REQUEST['searchBy'];
        $data['searchText'] = $_REQUEST['searchText'];
        $data['orderBy'] = $_REQUEST['orderBy'];
        $data['sortOrder'] = $_REQUEST['sortOrder'];
        $data['page'] = $_REQUEST['page'];
        $teenagerDetail = $this->TeenagersRepository->getTeenagerById($teenager_Id);
        return view('admin.AddCoinsDataForTeenager',compact('teenagerDetail','data'));
    }

    public function saveCoinsDataForTeen() {

        $id = e(Input::get('id'));
        $coins = e(Input::get('t_coins'));
        $giftCoins = e(Input::get('t_coins'));

        $searchParamArray = [];
        $searchParamArray['searchBy'] = e(Input::get('searchBy'));
        $searchParamArray['searchText'] = Input::get('searchText');
        $searchParamArray['orderBy'] = e(Input::get('orderBy'));
        $searchParamArray['sortOrder'] = e(Input::get('sortOrder'));
        $page = e(Input::get('page'));
        $postData['pageRank'] = '?page='.$page;
        if (!empty($searchParamArray)) {
            Cache::forever('searchArray', $searchParamArray);
        } else {
            Cache::forget('searchArray');
        }
        $flag = 0;
        $userData = $this->TeenagersRepository->getUserDataForCoinsDetail($id);
        if (!empty($userData)) {
            if (substr($coins, 0, 1) === '-') {
                $coins = preg_replace('/[-?]/', '', $coins);
                if ($userData['t_coins'] > 0 && $coins <= $userData['t_coins']) {
                    $coins = $userData['t_coins']-$coins;
                } else {
                    return Redirect::to("admin/teenagers".$postData['pageRank'])->with('error', trans('labels.commonerrormessage'));
                }
            } else if (is_numeric($coins)) {
                $coins += $userData['t_coins'];
                $flag++;
            }
        }
        $result = $this->TeenagersRepository->updateTeenagerCoinsDetail($id, $coins);
        $userArray = $this->TeenagersRepository->getTeenagerByTeenagerId($id);
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
            $replaceArray['FROM_USER'] = Auth::admin()->get()->name;
            $emailTemplateContent = $this->TemplateRepository->getEmailTemplateDataByName(Config::get('constant.COINS_RECEIBED_TEMPLATE'));
            $content = $this->TemplateRepository->getEmailContent($emailTemplateContent->et_body, $replaceArray);

            $data = array();
            $data['subject'] = $emailTemplateContent->et_subject;
            $data['toEmail'] = $userArray['t_email'];
            $data['toName'] = $userArray['t_name'];
            $data['content'] = $content;

            Mail::send(['html' => 'emails.Template'], $data , function ($m) use ($data) {
                $m->from(Config::get('constant.FROM_MAIL_ID'), 'Gift ProCoins');
                $m->subject($data['subject']);
                $m->to($data['toEmail'], $data['toName']);
            });
        }

        return Redirect::to("admin/teenagers".$postData['pageRank'])->with('success', trans('labels.coinsaddsuccess'));
    }

    public function addCoinsForAllTeenager(){
        $coins = e(Input::get('t_coins'));
        $userData = $this->TeenagersRepository->getAllUsersCoinsDetail();
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
            $result = $this->TeenagersRepository->updateTeenagerCoinsDetail($val['id'], $val['t_coins']);
        }
        return Redirect::to("admin/teenagers")->with('success', trans('labels.coinsaddsuccess'));
    }

    public function clearCache() {
        if (Cache::has('teenagerDetail')) {
            Cache::forget('teenagerDetail');
        }
        return Redirect::to("admin/teenagers");
    }
}
