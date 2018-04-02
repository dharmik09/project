<?php

namespace App\Http\Controllers\Admin;

use Auth;
use Input;
use Config;
use File;
use Image;
use Helpers;
use Redirect;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Pagination\Paginator;
use App\Professions;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProfessionRequest;
use App\Services\Professions\Contracts\ProfessionsRepository;
use App\Services\ProfessionHeaders\Contracts\ProfessionHeadersRepository;
use App\Services\Baskets\Contracts\BasketsRepository;
use App\Services\Teenagers\Contracts\TeenagersRepository;
use Cache;
use App\Notifications;
use App\Services\FileStorage\Contracts\FileStorageRepository;
use App\ProfessionSubject;
use App\Certification;
use App\ProfessionWiseCertification;
use App\ProfessionWiseSubject;
use App\ProfessionTag;
use App\ProfessionWiseTag;
use App\Country;
use App\ProfessionInstitutes;
use App\ManageExcelUpload;
use Storage;
use Artisan;

class ProfessionManagementController extends Controller {

    public function __construct(FileStorageRepository $fileStorageRepository, ProfessionHeadersRepository $professionsHeadersRepository, ProfessionsRepository $professionsRepository, BasketsRepository $basketsRepository,TeenagersRepository $teenagersRepository) {
        $this->objProfession = new Professions();
        $this->professionsRepository = $professionsRepository;
        $this->professionHeadersRepository = $professionsHeadersRepository;
        $this->basketsRepository = $basketsRepository;
        $this->teenagersRepository = $teenagersRepository;
        $this->fileStorageRepository = $fileStorageRepository;
        $this->professionOriginalImageUploadPath = Config::get('constant.PROFESSION_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->professionThumbImageUploadPath = Config::get('constant.PROFESSION_THUMB_IMAGE_UPLOAD_PATH');
        $this->professionThumbImageHeight = Config::get('constant.PROFESSION_THUMB_IMAGE_HEIGHT');
        $this->professionThumbImageWidth = Config::get('constant.PROFESSION_THUMB_IMAGE_WIDTH');
        $this->professionVideoUploadPath = Config::get('constant.PROFESSION_ORIGINAL_VIDEO_UPLOAD_PATH');
        $this->controller = 'ProfessionManagementController';
        $this->basketOriginalImageUploadPath = Config::get('constant.BASKET_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->basketThumbImageUploadPath = Config::get('constant.BASKET_THUMB_IMAGE_UPLOAD_PATH');
        $this->basketThumbImageHeight = Config::get('constant.BASKET_THUMB_IMAGE_HEIGHT');
        $this->basketThumbImageWidth = Config::get('constant.BASKET_THUMB_IMAGE_WIDTH');
        $this->getProfessionImagePath = Config::get('constant.GET_PROFESSION_IMAGES');
        $this->objSubject = new ProfessionSubject;
        $this->objCertification = new Certification;
        $this->objProfessionWiseCertification = new ProfessionWiseCertification;
        $this->objProfessionWiseSubject = new ProfessionWiseSubject;
        $this->objTag = new ProfessionTag;
        $this->objProfessionWiseTag = new ProfessionWiseTag;
        $this->objCountry = new Country;
        $this->loggedInUser = Auth::guard('admin');
        $this->objNotifications = new Notifications();
        $this->objProfessionInstitutes = new ProfessionInstitutes();
        $this->professionInstituteOriginalImageUploadPath = Config::get('constant.PROFESSION_INSTITUTE_PHOTO_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->professionInstituteThumbImageUploadPath = Config::get('constant.PROFESSION_INSTITUTE_PHOTO_THUMB_IMAGE_UPLOAD_PATH');
        $this->professionInstituteThumbImageHeight = Config::get('constant.PROFESSION_INSTITUTE_PHOTO_THUMB_IMAGE_HEIGHT');
        $this->professionInstituteThumbImageWidth = Config::get('constant.PROFESSION_INSTITUTE_PHOTO_THUMB_IMAGE_WIDTH');
        $this->objManageExcelUpload = new ManageExcelUpload();
    }

    public function index() {
        // $professions = $this->professionsRepository->getAllProfessions();
        // $uploadProfessionThumbPath = $this->professionThumbImageUploadPath;
        // Helpers::createAudit($this->loggedInUser->user()->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_READ'), $this->controller . "@index", $_SERVER['REQUEST_URI'], Config::get('constant.AUDIT_ORIGIN_WEB'), '', '', $_SERVER['REMOTE_ADDR']);
        return view('admin.ListProfession');
    }

    public function add() {
        $professionDetail = [];
        $subjects = $this->objSubject->getAllProfessionSubjects();
        $certificateList = $this->objCertification->getAllProfessionCertifications();
        $tagList = $this->objTag->getAllProfessionTags();
        $subjectData = $this->objSubject->getAllProfessionSubjects();

        $parameterGrade = ['H','M','L'];
        foreach ($subjectData as $key => $value) {
            $data = array();
            for($i=0;$i<3;$i++){
                $data[$value->id.'_'.$parameterGrade[$i]] = $value->ps_name.'('.$parameterGrade[$i].')';
            }
            $subjectData[$key]['data'] = $data;
        }

        Helpers::createAudit($this->loggedInUser->user()->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_READ'), $this->controller . "@add", $_SERVER['REQUEST_URI'], Config::get('constant.AUDIT_ORIGIN_WEB'), '', '', $_SERVER['REMOTE_ADDR']);
        return view('admin.EditProfession', compact('professionDetail', 'subjects', 'certificateList','subjectList','subjectData','tagList'));
    }

    public function edit($id) {
        $professionDetail = $this->objProfession->find($id);
        $uploadProfessionThumbPath = $this->professionThumbImageUploadPath;
        $uploadVideoPath = $this->professionVideoUploadPath;
        $subjects = $this->objSubject->getAllProfessionSubjects();
        
        $certificateList = $this->objCertification->getAllProfessionCertifications();
        $professionWiseCertificationData = $this->objProfessionWiseCertification->getProfessionWiseCertificationByProfessionId($id);
        $professionDetail['certificate_id'] = $professionWiseCertificationData['certificate_id'];

        $subjectData = $this->objSubject->getAllProfessionSubjects();
        $professionWiseSubjectData = $this->objProfessionWiseSubject->getProfessionWiseSubjectByProfessionId($id);
        $professionDetail['subject_id'] = $professionWiseSubjectData['subject_id'];

        $parameterGrade = ['H','M','L'];
        foreach ($subjectData as $key => $value) {
            $data = array();
            for($i=0;$i<3;$i++){
                $data[$value->id.'_'.$parameterGrade[$i]] = $value->ps_name.'('.$parameterGrade[$i].')';
            }
            $subjectData[$key]['data'] = $data;
        }

        $tagList = $this->objTag->getAllProfessionTags();
        $professionWiseTagData = $this->objProfessionWiseTag->getProfessionWiseTagByProfessionId($id);
        $professionDetail['tag_id'] = $professionWiseTagData['tag_id'];

        Helpers::createAudit($this->loggedInUser->user()->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_READ'), $this->controller . "@edit", $_SERVER['REQUEST_URI'], Config::get('constant.AUDIT_ORIGIN_WEB'), '', '', $_SERVER['REMOTE_ADDR']);
        return view('admin.EditProfession', compact('professionDetail', 'uploadProfessionThumbPath', 'uploadVideoPath', 'subjects', 'certificateList','subjectData','tagList'));
    }

    public function save(ProfessionRequest $professionRequest) {

        $professionDetail = [];

        $professionDetail['id'] = e(input::get('id'));
        $hiddenLogo = e(input::get('hidden_logo'));
        $professionDetail['pf_logo'] = $hiddenLogo;
        $hiddenVideo = e(input::get('hidden_video'));
        $videotype = e(Input::get('pf_video_type'));
        $postData['pageRank'] = Input::get('pageRank');
        if ($videotype == '1') {
            $professionDetail['pf_video'] = $hiddenVideo;
        } else if ($videotype == '2') {
            $professionDetail['pf_video'] = trim(e(input::get('youtube')));
            if ($hiddenVideo != '') {
                $videoOriginal = public_path($this->professionVideoUploadPath . $hiddenVideo);
                File::delete($videoOriginal);
            }
        } else if ($videotype == '3') {
            $professionDetail['pf_video'] = e(input::get('vimeo'));
            if ($hiddenVideo != '') {
                $videoOriginal = public_path($this->professionVideoUploadPath . $hiddenVideo);
                File::delete($videoOriginal);
            }
        }
        $professionDetail['pf_video_type'] = $videotype;
        $professionDetail['pf_name'] = trim(input::get('pf_name'));
        $professionDetail['pf_slug'] = e(input::get('pf_slug'));
        $professionDetail['pf_basket'] = e(input::get('pf_basket'));
        $professionDetail['deleted'] = e(input::get('deleted'));
        $professionDetail['pf_profession_alias'] = e(input::get('pf_profession_alias'));
        $secondary_baskets = input::get('pf_related_basket');
            
        if (Input::file()) {
            $file = Input::file('pf_logo');
            $videoFile = Input::file('normal');
            if (!empty($file)) {
                $fileName = 'profession_' . time() . '.' . $file->getClientOriginalExtension();
                $pathOriginal = public_path($this->professionOriginalImageUploadPath . $fileName);
                $pathThumb = public_path($this->professionThumbImageUploadPath . $fileName);

                Image::make($file->getRealPath())->save($pathOriginal);
                Image::make($file->getRealPath())->resize($this->professionThumbImageWidth, $this->professionThumbImageHeight)->save($pathThumb);

                if ($hiddenLogo != '') {
                    $originalImageDelete = $this->fileStorageRepository->deleteFileToStorage($hiddenLogo, $this->professionOriginalImageUploadPath, "s3");
                        $thumbImageDelete = $this->fileStorageRepository->deleteFileToStorage($hiddenLogo, $this->professionThumbImageUploadPath, "s3");
                }

                //Uploading on AWS
                $originalImage = $this->fileStorageRepository->addFileToStorage($fileName, $this->professionOriginalImageUploadPath, $pathOriginal, "s3");
                $thumbImage = $this->fileStorageRepository->addFileToStorage($fileName, $this->professionThumbImageUploadPath, $pathThumb, "s3");
                
                \File::delete($this->professionOriginalImageUploadPath . $fileName);
                \File::delete($this->professionThumbImageUploadPath . $fileName);

                $professionDetail['pf_logo'] = $fileName;
            }
            if (!empty($videoFile)) {
                $fileName = 'profession_' . time() . '.' . $videoFile->getClientOriginalExtension();
                $pathOriginal = public_path($this->professionVideoUploadPath);

                $videoFile->move($pathOriginal, $fileName);
                if ($hiddenVideo != '') {
                    $videoOriginal = public_path($this->professionVideoUploadPath . $hiddenVideo);
                    File::delete($videoOriginal);
                }

                $professionDetail['pf_video'] = $fileName;
            }
        }
        
        //Check if profession name already exist
        if(isset($secondary_baskets) && !empty($secondary_baskets))
        {
            $professionDetail['pf_related_basket'] = implode(',', $secondary_baskets);            
        }else{
            $professionDetail['pf_related_basket'] = '0';
        }
        $response = $this->professionsRepository->saveProfessionDetail($professionDetail); 
        Cache::forget('professions');
        if ($response) {

            if(isset($professionDetail['id']) && $professionDetail['id'] != '' && $professionDetail['id'] > 0){
                $profession_Id = Input::get('id');
                $this->objProfessionWiseCertification->deleteProfessionWiseCertificationByProfessionId($profession_Id);
                $this->objProfessionWiseSubject->deleteProfessionWiseSubjectByProfessionId($profession_Id);
                $this->objProfessionWiseTag->deleteProfessionWiseTagByProfessionId($profession_Id);
            }
            else{
                $profession_Id = $response->id;
                $checkProfessionWiseCertificationByProfessionId = $this->objProfessionWiseCertification->getProfessionWiseCertificationByProfessionId($profession_Id);
                if(count($checkProfessionWiseCertificationByProfessionId)>0){
                    return Redirect::to("admin/professions".$postData['pageRank'])->with('error',trans('labels.professionwisecertificationdataalreadyaddedforprofession'));
                }
            }

            $certificateId = Input::get('certificate_id');
            if(!empty($certificateId)){
                foreach ($certificateId as $value) {
                    $professionWiseCertificationData = [];
                    $professionWiseCertificationData['profession_id'] = $profession_Id;
                    $professionWiseCertificationData['certificate_id'] = $value;
                    $response = $this->objProfessionWiseCertification->insertUpdate($professionWiseCertificationData);
                }
            }

            $subjectId = Input::get('subject_id');
            if(!empty($subjectId)){
                foreach ($subjectId as $value) {
                    $valueArray = explode('_', $value);
                    $ProfessionWiseSubjectData = [];
                    $ProfessionWiseSubjectData['profession_id'] = $profession_Id;
                    $ProfessionWiseSubjectData['subject_id'] = $valueArray[0];
                    $ProfessionWiseSubjectData['parameter_grade'] = $valueArray[1];
                    $response = $this->objProfessionWiseSubject->insertUpdate($ProfessionWiseSubjectData);
                }
            }

            $tagId = Input::get('tag_id');
            if(!empty($tagId)){
                foreach ($tagId as $value) {
                    $professionWiseTagData = [];
                    $professionWiseTagData['profession_id'] = $profession_Id;
                    $professionWiseTagData['tag_id'] = $value;
                    $response = $this->objProfessionWiseTag->insertUpdate($professionWiseTagData);
                }
            }

            $notificationData['n_sender_id'] = '0';
            $notificationData['n_sender_type'] = Config::get('constant.NOTIFICATION_ADMIN');
            $notificationData['n_receiver_id'] = 0;
            $notificationData['n_receiver_type'] = Config::get('constant.NOTIFICATION_TEENAGER');
            $notificationData['n_notification_type'] = Config::get('constant.NOTIFICATION_TYPE_ADD_PROFESSION');
            $notificationData['n_notification_text'] = '<strong> Admin </strong> added new profession <strong>'.$professionDetail['pf_name'].'</strong>';
            $this->objNotifications->insertUpdate($notificationData);
            
            Helpers::createAudit($this->loggedInUser->user()->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_UPDATE'), Config::get('databaseconstants.TBL_PROFESSIONS'), $response, Config::get('constant.AUDIT_ORIGIN_WEB'), trans('labels.professionupdatesuccess'), serialize($professionDetail), $_SERVER['REMOTE_ADDR']);
            return Redirect::to("admin/professions".$postData['pageRank'])->with('success', trans('labels.professionupdatesuccess'));
        } else {
            Helpers::createAudit($this->loggedInUser->user()->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_UPDATE'), Config::get('databaseconstants.TBL_PROFESSIONS'), $response, Config::get('constant.AUDIT_ORIGIN_WEB'), trans('labels.somethingwrong'), serialize($professionDetail), $_SERVER['REMOTE_ADDR']);
            return Redirect::to("admin/professions".$postData['pageRank'])->with('error', trans('labels.commonerrormessage'));
        }
    }

    public function delete($id) {
        $return = $this->professionsRepository->deleteProfession($id);
        $return2 = $this->professionHeadersRepository->deleteProfessionHeader($id);
        if ($return && $return2) {
            Helpers::createAudit($this->loggedInUser->user()->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_DELETE'), Config::get('databaseconstants.TBL_PROFESSIONS'), $id, Config::get('constant.AUDIT_ORIGIN_WEB'), trans('labels.professiondeletesuccess'), '', $_SERVER['REMOTE_ADDR']);

            return Redirect::to("admin/professions")->with('success', trans('labels.professionandheaderdeletesuccess'));
        } 
        else if ($return) 
        {
            return Redirect::to("admin/professions")->with('success', trans('labels.professiondeletesuccess'));
        }else {
            Helpers::createAudit($this->loggedInUser->user()->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_DELETE'), Config::get('databaseconstants.TBL_PROFESSIONS'), $id, Config::get('constant.AUDIT_ORIGIN_WEB'), trans('labels.somethingwrong'), '', $_SERVER['REMOTE_ADDR']);
            return Redirect::to("admin/professions")->with('error', trans('labels.commonerrormessage'));
        }
    }

    public function addbulk() {
        $countryList = $this->objCountry->getAllCounries();
        return view('admin.AddProfessionBulk',compact('countryList'));
    }

    public function saveprofessionbulk() {
        $response = '';
        $profession = Input::file('p_bulk');
        Excel::selectSheetsByIndex(0)->load($profession, function($reader) {
            foreach ($reader->toArray() as $row) {
                if ($row['basket_name'] != '' && $row['profession_name'] != '') {
                    
                    $professtionDetail = [];
                    $professtionDetail['pf_name'] = trim($row['profession_name']);
                    // $professtionDetail['pf_video'] = trim($row['profession_video']);

                    $basketDetail = [];
                    $basketDetail['b_name'] = $row['basket_name'];
                    // $basketDetail['b_video'] = $row['basket_video'];

                    $headerTitle = [];
                    $headerDetail = [];
                    foreach ($row as $key => $value) {
                        if($key != "profession_name" && $key != "basket_name"){
                            $headerTitle[] = $key;
                            $headerDetail[] = $value;
                        }
                    }
                    $response = $this->professionsRepository->saveProfessionBulkDetail($professtionDetail, $basketDetail, $headerTitle, $headerDetail, Input::get('p_country'));
                }
            }
        });
        return Redirect::to("admin/professions")->with('success', trans('labels.professionbulkupdatesuccess'));
    }

    public function getProfessionImageByName() {
        $allProfessions = Professions::where('deleted', 1)->get();
        $professions = $allProfessions->toArray();
        if (!empty($professions)) {
            foreach ($professions as $key => $data) {
                $imageName = $data['pf_name'];
                $replacedImage = str_replace(' ', '_', $imageName);
                $imageUrl = asset($this->getProfessionImagePath . $replacedImage . '.png');
                if (file_exists($this->getProfessionImagePath . $replacedImage . '.png')) {
                    $profession_logo_path = public_path($this->professionOriginalImageUploadPath . $replacedImage . '.png');
                    $profession_logo_thumb_path = public_path($this->professionThumbImageUploadPath . $replacedImage . '.png');
                    $img = Image::make($imageUrl);
                    $img->save($profession_logo_path);
                    $img->resize($this->professionThumbImageWidth, $this->professionThumbImageHeight)->save($profession_logo_thumb_path);

                    Professions::where('id', $data['id'])->update(['pf_logo' => $replacedImage . '.png']);
                } else {
                    $notImported[] = array('id' => $data['id'], 'profession' => $data['pf_name']);
                }
            }
        }
        echo "<pre>";
        print_r($notImported);
        exit;
    }

    public function exportData() {
        $result = $this->professionsRepository->getExportProfession();
        Excel::create('profession', function($excel) use($result) {
            $excel->sheet('Sheet 1', function($sheet) use($result) {
                $sheet->fromArray($result);
            });
        })->export('xlsx');
    }

    public function getUserCompetitorsData() {
        $profession_Id = $_REQUEST['Professionid'];
        $pf_name = $this->professionsRepository->getProfessionNameById($profession_Id);
        $data = Helpers::getCompetingUserList($profession_Id);

        return view('admin.UserCompetitorspopupDetail',compact('data','pf_name'));
    }

    public function exportCompetotorsData($id) {

         $data = Helpers::getCompetingUserList($id);
         $AllData = [];
         foreach ($data AS $key => $value) {
            $ProData = [];
            $ProData['Name'] = $value['name'];
            $ProData['Phone No'] = $value['t_phone'];
            $ProData['Email Id'] = $value['t_email'];
            if ($value['yourScore'] == '') {
                $value['yourScore'] = 'N/A';
            }
            $ProData['Score'] = $value['yourScore'];
            $ProData['Rank'] = $value['rank'];
            $AllData[] = $ProData;
         }
                Excel::create('competitors', function($excel) use($AllData) {
            $excel->sheet('Sheet 1', function($sheet) use($AllData) {
                $sheet->fromArray($AllData);
            });
        })->export('xlsx');
    }

    public function professionWiseCertificationAddBulk() {
        return view('admin.AddProfessionWiseCertificateBulk');
    }
    
    public function professionWiseCertificationSaveBulk() {
        $response = '';        
        $path = Input::file('p_bulk')->getRealPath();

        $results = Excel::load($path, function($reader) {})->get();
        $excelHeaderData = Excel::load($path, function($reader) { $reader->noHeading = true; }, 'ISO-8859-1')->get();

        foreach ($excelHeaderData[0] as $key => $value) {
            if($value != 'profession_name'){
                $certificateData = $this->objCertification->getProfessionCertificationByName($value);
                if (!$certificateData){
                    return Redirect::to("admin/professions")->with('error',$value.' '.trans('labels.professionwisecertificationbulkuploadcertificatenotfound'));
                }
            }
        }

        foreach ($results as $key => $value) {
                $professionsData = $this->objProfession->getProfessionByName($value['profession_name']);
                if (!$professionsData){
                    return Redirect::to("admin/professions")->with('error',$value['profession_name'].' '.trans('labels.professionwisecertificationbulkuploadprofessionnotfound'));
                }
        }

        foreach ($results as $key => $value) {
                $professionsData = $this->objProfession->getProfessionByName($value['profession_name']);
                $data = [];
                $count = 0;
                foreach ($value as $k => $v) {
                    if($k != 'profession_name' && strtolower($v) == "yes"){
                        $certificateData = $this->objCertification->getProfessionCertificationByName($excelHeaderData[0][$count]);
                        if ($certificateData){
                            $data = [];
                            $data['profession_id'] = $professionsData->id;
                            $data['certificate_id'] = $certificateData->id;

                            $checkIfRecordExist = $this->objProfessionWiseCertification->checkProfessionWiseCertificateByCertificateIdAndProfessionId($certificateData->id,$professionsData->id);
                            if(count($checkIfRecordExist)>0)
                            {
                                $data['id'] = $checkIfRecordExist->id;
                            }
                            $response = $this->objProfessionWiseCertification->insertUpdate($data);
                        }
                    }
                    $count++;
                }
        }
        
        if($response) {
            return Redirect::to("admin/professions")->with('success', trans('labels.professionwisecertificationbulkuploadsuccess'));
        } else {
            return Redirect::to("admin/professions")->with('error', trans('labels.commonerrormessage'));
        }
    }

    public function professionWiseSubjectAddBulk() {
        return view('admin.AddProfessionWiseSubjectBulk');
    }

    public function professionWiseSubjectSaveBulk() {
        $response = '';        
        $path = Input::file('p_bulk')->getRealPath();

        $results = Excel::load($path, function($reader) {})->get();
        $excelHeaderData = Excel::load($path, function($reader) { $reader->noHeading = true; }, 'ISO-8859-1')->get();

        foreach ($excelHeaderData[0] as $key => $value) {
            if($value != 'profession_name'){
                $subjectData = $this->objSubject->getProfessionSubjectByName($value);
                if (!$subjectData){
                    return Redirect::to("admin/professions")->with('error',$value.' '.trans('labels.professionwiseSubjectbulkuploadsubjectnotfound'));
                }
            }
        }

        foreach ($results as $key => $value) {
                $professionsData = $this->objProfession->getProfessionByName($value['profession_name']);
                if (!$professionsData){
                    return Redirect::to("admin/professions")->with('error',$value['profession_name'].' '.trans('labels.professionwiseSubjectbulkuploadprofessionnotfound'));
                }
        }

        foreach ($results as $key => $value) {
                $professionsData = $this->objProfession->getProfessionByName($value['profession_name']);
                $data = [];
                $count = 0;
                foreach ($value as $k => $v) {
                    if($k != 'profession_name'){
                        $subjectData = $this->objSubject->getProfessionSubjectByName($excelHeaderData[0][$count]);
                        if ($subjectData){
                            
                            $data = [];
                            $data['profession_id'] = $professionsData->id;
                            $data['subject_id'] = $subjectData->id;
                            $data['parameter_grade'] = $v;
                            
                            $checkIfRecordExist = $this->objProfessionWiseSubject->checkProfessionWiseSubjectBySubjectIdAndProfessionId($subjectData->id,$professionsData->id);
                            if(count($checkIfRecordExist)>0)
                            {
                                $data['id'] = $checkIfRecordExist->id;
                            }
                            
                            $response = $this->objProfessionWiseSubject->insertUpdate($data);
                        }
                    }
                    $count++;
                }
        }
        
        if($response) {
            return Redirect::to("admin/professions")->with('success', trans('labels.professionwisesubjectbulkuploadsuccess'));
        } else {
            return Redirect::to("admin/professions")->with('error', trans('labels.commonerrormessage'));
        }
    }

    public function professionWiseTagAddBulk() {
        return view('admin.AddProfessionWiseTagBulk');
    }
    
    public function professionWiseTagSaveBulk() {
        $response = '';        
        $path = Input::file('p_bulk')->getRealPath();

        $results = Excel::load($path, function($reader) {})->get();
        $excelHeaderData = Excel::load($path, function($reader) { $reader->noHeading = true; }, 'ISO-8859-1')->get();

        foreach ($excelHeaderData[0] as $key => $value) {
            if($value != 'profession_name'){
                $tagData = $this->objTag->getProfessionTagByName($value);
                if (!$tagData){
                    return Redirect::to("admin/professions")->with('error',$value.' '.trans('labels.professionwisetagbulkuploadtagnotfound'));
                }
            }
        }

        foreach ($results as $key => $value) {
                $professionsData = $this->objProfession->getProfessionByName($value['profession_name']);
                if (!$professionsData){
                    return Redirect::to("admin/professions")->with('error',$value['profession_name'].' '.trans('labels.professionwiseTagbulkuploadprofessionnotfound'));
                }
        }

        foreach ($results as $key => $value) {
                $professionsData = $this->objProfession->getProfessionByName($value['profession_name']);
                $data = [];
                $count = 0;
                foreach ($value as $k => $v) {
                    if($k != 'profession_name' && strtolower($v) == "yes"){
                        $tagData = $this->objTag->getProfessionTagByName($excelHeaderData[0][$count]);
                        if ($tagData){
                            $data = [];
                            $data['profession_id'] = $professionsData->id;
                            $data['tag_id'] = $tagData->id;
                            
                            $checkIfRecordExist = $this->objProfessionWiseTag->checkProfessionWiseTagByTagIdAndProfessionId($tagData->id,$professionsData->id);
                            if(count($checkIfRecordExist)>0)
                            {
                                $data['id'] = $checkIfRecordExist->id;
                            }
                            $response = $this->objProfessionWiseTag->insertUpdate($data);
                        }
                    }
                    $count++;
                }
        }
        
        if($response) {
            return Redirect::to("admin/professions")->with('success', trans('labels.professionwisetagbulkuploadsuccess'));
        } else {
            return Redirect::to("admin/professions")->with('error', trans('labels.commonerrormessage'));
        }
    }

    public function professionInstitutes() {
        return view('admin.ListProfessionInstitutes');
    }

    public function getProfessionInstitutesListAjax() {
        $professionInstitutes = $this->objProfessionInstitutes->getAllProfessionInstitutesForAjax()->get()->count();
        
        $records = array();
        $columns = array(
            0 => 'school_id',
            1 => 'college_institution',
            2 => 'institute_state',
            3 => 'pin_code',
            4 => 'affiliat_university',
            5 => 'management',
            6 => 'accreditation_body',
            7 => 'accreditation_score',
        );
        
        $order = Input::get('order');
        $search = Input::get('search');
        $records["data"] = array();
        $iTotalRecords = $professionInstitutes;
        $iTotalFiltered = $iTotalRecords;
        $iDisplayLength = intval(Input::get('length')) <= 0 ? $iTotalRecords : intval(Input::get('length'));
        $iDisplayStart = intval(Input::get('start'));
        $sEcho = intval(Input::get('draw'));

        $records["data"] = $this->objProfessionInstitutes->getAllProfessionInstitutesForAjax();
        
        if (!empty($search['value'])) {
            $val = $search['value'];
            $records["data"]->where(function($query) use ($val) {
                $query->where('school_id', "Like", "%$val%");
                $query->orWhere('college_institution', "Like", "%$val%");
                $query->orWhere('institute_state', "Like", "%$val%");
                $query->orWhere('pin_code', "Like", "%$val%");
                $query->orWhere('affiliat_university', "Like", "%$val%");
                $query->orWhere('management', "Like", "%$val%");
                $query->orWhere('accreditation_body', "Like", "%$val%");
                $query->orWhere('accreditation_score', "Like", "%$val%");
            });

            // No of record after filtering
            $iTotalFiltered = $records["data"]->where(function($query) use ($val) {
                    $query->where('school_id', "Like", "%$val%");
                    $query->orWhere('college_institution', "Like", "%$val%");
                    $query->orWhere('institute_state', "Like", "%$val%");
                    $query->orWhere('pin_code', "Like", "%$val%");
                    $query->orWhere('affiliat_university', "Like", "%$val%");
                    $query->orWhere('management', "Like", "%$val%");
                    $query->orWhere('accreditation_body', "Like", "%$val%");
                    $query->orWhere('accreditation_score', "Like", "%$val%");
                })->count();
        }
        
        //order by
        foreach ($order as $o) {
            $records["data"] = $records["data"]->orderBy($columns[$o['column']], $o['dir']);
        }

        //limit
        $records["data"] = $records["data"]->take($iDisplayLength)->offset($iDisplayStart)->get();
        $sid = 0;

        if (!empty($records["data"])) {
            foreach ($records["data"] as $key => $_records) {                
                $records["data"][$key]->action = '<a onClick="showProfessionImageUploadModel('.$_records->id.','.$_records->school_id.','.$_records->image.')" class="btn btn-primary">'.trans("labels.lblupdatephoto").'</a>';
                $records["data"][$key]->image = ($_records->image != '' && Storage::size($this->professionInstituteThumbImageUploadPath . $_records->image) > 0) ? "<img src='". Storage::url($this->professionInstituteThumbImageUploadPath . $_records->image) ."' height = 50 width = 50 >" : "<img src='". Storage::url('img/insti-logo.png') ."'   height = 50 width= 50>";
            }
        }

        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalFiltered;

        return \Response::json($records);
        exit;
    }

    public function professionInstitutesListAdd() {
        $basicExcelData = $this->objManageExcelUpload->getLatestRecordByExcelType('1');
        $accreditationExcelData = $this->objManageExcelUpload->getLatestRecordByExcelType('2');
        return view('admin.AddProfessionInstitutes', compact('basicExcelData','accreditationExcelData'));
    }
    
    public function professionInstitutesListSave() {
        $uploadType = Input::get('ps_upload_type');
        if($uploadType == 1) // Upload Basic information Excel to uploads/excel
        {
            $getFileName = "ProfessionInstituteBasic.".Input::file('ps_bulk')->getClientOriginalExtension();
        }
        elseif($uploadType == 2) // Upload Accreditation Excel to uploads/excel
        {
            $getFileName = "ProfessionInstituteAccreditation.".Input::file('ps_bulk')->getClientOriginalExtension();
        }
        if (file_exists(public_path('uploads/excel/'.$getFileName)))
        {
            \File::delete('uploads/excel/'.$getFileName);
        }
        $checkFile = Input::file('ps_bulk')->move(public_path('uploads/excel/'), $getFileName);

        if($checkFile) {
            return Redirect::to("admin/addProfessionInstituteCourseList")->with('success', trans('labels.professioninstituesexceluploadsuccess'));
        } else {
            return Redirect::to("admin/addProfessionInstituteCourseList")->with('error', trans('labels.commonerrormessage'));
        }
    }

    public function professionInstitutesArtisanUpload($uploadType) {
        
        if($uploadType == "1") // Upload Basic information Excel to uploads/excel
        {
            $fileName = 'uploads/excel/ProfessionInstituteBasic.xlsx';
        }
        elseif($uploadType == "2") // Upload Accreditation Excel to uploads/excel
        {
            $fileName = 'uploads/excel/ProfessionInstituteAccreditation.xlsx';
        }

        if (file_exists(public_path($fileName)))
        {
            Artisan::call("ProfessionInstituteUpload", [
                '--file' => public_path($fileName),
                '--uploadType' => $uploadType
            ]);
        }
        else{
            return Redirect::to("admin/addProfessionInstituteCourseList")->with('error', trans('labels.professioninstituesexcelnotfound'));
        }

        return Redirect::to("admin/addProfessionInstituteCourseList");
    }

    public function deleteAllProfessionInstitutes() {
        $this->objProfessionInstitutes->truncate();
        return Redirect::to("admin/addProfessionInstituteCourseList")->with('success', trans('labels.professioninstituesdeleteallsuccess'));
    }

    public function professionInstitutesPhotoUpdate() {
        $file = Input::file('institute_photo');
        $hiddenLogo = Input::file('oldimage');
        $validationPass = Helpers::checkValidImageExtension($file);
        if($validationPass)
        {
            $fileName = 'ProfessionInstitute_' . time() . '.' . $file->getClientOriginalExtension();
            $pathOriginal = public_path($this->professionInstituteOriginalImageUploadPath . $fileName);
            $pathThumb = public_path($this->professionInstituteThumbImageUploadPath . $fileName);
            Image::make($file->getRealPath())->save($pathOriginal);
            Image::make($file->getRealPath())->resize($this->professionInstituteThumbImageWidth, $this->professionInstituteThumbImageHeight)->save($pathThumb);
            
            if ($hiddenLogo != '')
            {
                $originalImageDelete = $this->fileStorageRepository->deleteFileToStorage($hiddenLogo, $this->professionInstituteOriginalImageUploadPath, "s3");
                $thumbImageDelete = $this->fileStorageRepository->deleteFileToStorage($hiddenLogo, $this->professionInstituteThumbImageUploadPath, "s3");
            }

            //Uploading on AWS
            $originalImage = $this->fileStorageRepository->addFileToStorage($fileName, $this->professionInstituteOriginalImageUploadPath, $pathOriginal, "s3");
            $thumbImage = $this->fileStorageRepository->addFileToStorage($fileName, $this->professionInstituteThumbImageUploadPath, $pathThumb, "s3");
            \File::delete($this->professionInstituteOriginalImageUploadPath . $fileName);
            \File::delete($this->professionInstituteThumbImageUploadPath . $fileName);
            
            $data['image'] = $fileName;
            $data['id'] = Input::get('institute_id');
            $response = $this->objProfessionInstitutes->insertUpdate($data);
            if($response) {
                return Redirect::to("admin/professionInstitute")->with('success', trans('labels.professioninstituesphotouploadsuccess'));
            } else {
                return Redirect::to("admin/professionInstitute")->with('error', trans('labels.commonerrormessage'));
            }
        } else {
            return Redirect::to("admin/professionInstitute")->with('error', trans('labels.professioninstituesphotonotvalid'));
        }
    }

    public function listWithAjax() {
        $professions = $this->professionsRepository->getAllProfessionsData()->get()->count();
        
        $records = array();
        $columns = array(
            0 => 'id',
            1 => 'pf_name',
            2 => 'b_name',
            3 => 'pf_logo',
            4 => 'deleted',
        );
        
        $order = Input::get('order');
        $search = Input::get('search');
        $records["data"] = array();
        $iTotalRecords = $professions;
        $iTotalFiltered = $iTotalRecords;
        $iDisplayLength = intval(Input::get('length')) <= 0 ? $iTotalRecords : intval(Input::get('length'));
        $iDisplayStart = intval(Input::get('start'));
        $sEcho = intval(Input::get('draw'));

        $records["data"] = $this->professionsRepository->getAllProfessionsData();
        
        if (!empty($search['value'])) {
            $val = $search['value'];
            $records["data"]->where(function($query) use ($val) {
                $query->where('profession.pf_name', "Like", "%$val%");
                $query->orWhere('profession.created_at', "Like", "%$val%");
                $query->orWhere('basket.b_name', "Like", "%$val%");
            });

            // No of record after filtering
            $iTotalFiltered = $records["data"]->where(function($query) use ($val) {
                    $query->where('profession.pf_name', "Like", "%$val%");
                    $query->orWhere('profession.created_at', "Like", "%$val%");
                    $query->orWhere('basket.b_name', "Like", "%$val%");
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
                $records["data"][$key]->pf_logo = ($_records->pf_logo != '' && Storage::size($this->professionThumbImageUploadPath . $_records->pf_logo) > 0) ? "<img src='". Storage::url($this->professionThumbImageUploadPath . $_records->pf_logo) ."' height = 50 width = 50 >" : "<img src='". Storage::url($this->professionThumbImageUploadPath . 'proteen-logo.png') ."'   height = 50 width= 50>";
                $records["data"][$key]->action = '<a href="'.url('/admin/editProfession').'/'.$_records->id.'"><i class="fa fa-edit"></i> &nbsp;&nbsp;</a>
                                                    <a onClick="return confirm(\'Are you sure want to delete?\')" href="'.url('/admin/deleteProfession').'/'.$_records->id.'"><i class="i_delete fa fa-trash"></i> &nbsp;&nbsp;</a>';
                $records["data"][$key]->deleted = ($_records->deleted == 1) ? "<i class='s_active fa fa-square'></i>" : "<i class='s_inactive fa fa-square'></i>";
                $records["data"][$key]->competitors = '<a href="javascript:void(0);" onClick="fetch_competitors_details('.$_records->id.');" data-toggle="modal" id="#userCompetotorsData" data-target="#userCompetotorsData"><i class="fa fa-eye" aria-hidden="true"></i>&nbsp;&nbsp;</a><a href="'.url('/admin/exportCompetitors').'/'.$_records->id.'"><i class="fa fa-file-excel-o" aria-hidden="true"></i></a>';
            }
        }

        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalFiltered;

        return \Response::json($records);
        exit;
    }
    
    public function exportInstitute() 
    {
        ini_set('memory_limit', '20000M');
        ini_set('max_execution_time', 0);
        
        $result = $this->objProfessionInstitutes->getAllProfessionInstitutes();
        $finalData = $result->toArray();
        if(isset($finalData) && !empty($finalData))
        {
           
            foreach($finalData as $key=>$data){
                unset($data['id']);
                unset($data['location']);
                unset($data['image']);
                unset($data['created_at']);
                unset($data['updated_at']);
                unset($data['deleted']);                
                $exportData[] = $data;
            }                       
            Excel::create('institute', function($excel) use($exportData) {
                $excel->sheet('Sheet 1', function($sheet) use($exportData) {
                    $sheet->fromArray($exportData);
                });
            })->export('xlsx');
        }else{
            return Redirect::to("admin/professionInstitute")->with('error', trans('labels.commonerrormessage'));
        }
        
        
        
    }
}
