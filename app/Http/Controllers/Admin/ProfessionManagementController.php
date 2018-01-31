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
    }

    public function index() {
        $professions = $this->professionsRepository->getAllProfessions();
        $uploadProfessionThumbPath = $this->professionThumbImageUploadPath;
        Helpers::createAudit($this->loggedInUser->user()->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_READ'), $this->controller . "@index", $_SERVER['REQUEST_URI'], Config::get('constant.AUDIT_ORIGIN_WEB'), '', '', $_SERVER['REMOTE_ADDR']);
        return view('admin.ListProfession', compact('professions', 'uploadProfessionThumbPath'));
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
                $count = 1;
                foreach ($value as $k => $v) {
                    if($k != 'profession_name' && $v == "Yes"){
                        $certificateData = $this->objCertification->getProfessionCertificationByName($excelHeaderData[0][$count]);
                        if ($certificateData){
                            $data = [];
                            $data['profession_id'] = $professionsData->id;
                            $data['certificate_id'] = $certificateData->id;
                            $response = $this->objProfessionWiseCertification->insertUpdate($data);
                        }
                        $count++;
                    }
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
        // echo "<pre>"; print_r($results); exit;
        foreach ($excelHeaderData[0] as $key => $value) {
            if($value != 'profession_name'){
                $certificateData = $this->objSubject->getProfessionSubjectByName($value);
                if (!$certificateData){
                    return Redirect::to("admin/professions")->with('error',$value.' '.trans('labels.professionwiseSubjectbulkuploadcertificatenotfound'));
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
                $count = 1;
                foreach ($value as $k => $v) {
                    if($k != 'profession_name'){
                        $subjectData = $this->objSubject->getProfessionSubjectByName($excelHeaderData[0][$count]);
                        if ($subjectData){
                            $data = [];
                            $data['profession_id'] = $professionsData->id;
                            $data['subject_id'] = $subjectData->id;
                            $data['parameter_grade'] = $v;
                            $response = $this->objProfessionWiseSubject->insertUpdate($data);
                        }
                        $count++;
                    }
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
                $count = 1;
                foreach ($value as $k => $v) {
                    if($k != 'profession_name' && $v == "Yes"){
                        $tagData = $this->objTag->getProfessionTagByName($excelHeaderData[0][$count]);
                        if ($tagData){
                            $data = [];
                            $data['profession_id'] = $professionsData->id;
                            $data['tag_id'] = $tagData->id;
                            $response = $this->objProfessionWiseTag->insertUpdate($data);
                        }
                        $count++;
                    }
                }
        }
        
        if($response) {
            return Redirect::to("admin/professions")->with('success', trans('labels.professionwisetagbulkuploadsuccess'));
        } else {
            return Redirect::to("admin/professions")->with('error', trans('labels.commonerrormessage'));
        }
    }
}
