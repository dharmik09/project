<?php

namespace App\Http\Controllers\Admin;

use File;
use Image;
use Auth;
use Input;
use Config;
use Request;
use Redirect;
use App\Certification;
use App\Professions;
use App\ProfessionWiseCertification;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProfessionCertificationRequest;
use App\Http\Requests\ProfessionWiseCertificationRequest;
use Helpers;
use App\Services\FileStorage\Contracts\FileStorageRepository;
use Maatwebsite\Excel\Facades\Excel;

class CertificationManagementController extends Controller
{
    public function __construct(FileStorageRepository $fileStorageRepository) {
        $this->fileStorageRepository = $fileStorageRepository;
        $this->objCertification = new Certification;
        $this->objProfessions = new Professions;
        $this->objProfessionWiseCertification = new ProfessionWiseCertification;
        $this->certificationOriginalImageUploadPath = Config::get("constant.PROFESSION_CERTIFICATION_ORIGINAL_IMAGE_UPLOAD_PATH");
        $this->certificationThumbImageUploadPath = Config::get("constant.PROFESSION_CERTIFICATION_THUMB_IMAGE_UPLOAD_PATH");
        $this->certificationThumbImageHeight = Config::get("constant.PROFESSION_CERTIFICATION_THUMB_IMAGE_HEIGHT");
        $this->certificationThumbImageWidth = Config::get("constant.PROFESSION_CERTIFICATION_THUMB_IMAGE_WIDTH");
    }

    public function index() {
        $certificationThumbImageUploadPath = $this->certificationThumbImageUploadPath;
        $certifications = $this->objCertification->getAllProfessionCertifications();
        return view('admin.ListCertification', compact('certifications', 'certificationThumbImageUploadPath'));
    }

    public function add() {
        $certification = [];
        $certificationThumbImageUploadPath = $this->certificationThumbImageUploadPath;
        return view('admin.EditCertification', compact('certification', 'certificationThumbImageUploadPath'));
    }

    public function edit($id) {
        $certification = $this->objCertification->find($id);
        $certificationThumbImageUploadPath = $this->certificationThumbImageUploadPath;
        return view('admin.EditCertification', compact('certification', 'certificationThumbImageUploadPath'));
    }

    public function save(ProfessionCertificationRequest $professionCertificationRequest) {
        $certificationDetail = [];
        $hiddenLogo     = e(input::get('hidden_logo'));
        $certificationDetail['id'] = e(Input::get('id'));
        $certificationDetail['pc_name'] = e(Input::get('pc_name'));
        $certificationDetail['deleted'] = e(Input::get('deleted'));

        if (Input::file())
        {
            $file = Input::file('pc_image');
            if(!empty($file))
            {
                //Check image valid extension 
                $validationPass = Helpers::checkValidImageExtension($file);
                if($validationPass)
                {
                    $fileName = 'professionCertification_' . time() . '.' . $file->getClientOriginalExtension();
                    $pathOriginal = public_path($this->certificationOriginalImageUploadPath . $fileName);
                    $pathThumb = public_path($this->certificationThumbImageUploadPath . $fileName);
                    Image::make($file->getRealPath())->save($pathOriginal);
                    Image::make($file->getRealPath())->resize($this->certificationThumbImageWidth, $this->certificationThumbImageHeight)->save($pathThumb);
                    
                    if ($hiddenLogo != '')
                    {
                        $originalImageDelete = $this->fileStorageRepository->deleteFileToStorage($hiddenLogo, $this->certificationOriginalImageUploadPath, "s3");
                        $thumbImageDelete = $this->fileStorageRepository->deleteFileToStorage($hiddenLogo, $this->certificationThumbImageUploadPath, "s3");
                    }

                    //Uploading on AWS
                    $originalImage = $this->fileStorageRepository->addFileToStorage($fileName, $this->certificationOriginalImageUploadPath, $pathOriginal, "s3");
                    $thumbImage = $this->fileStorageRepository->addFileToStorage($fileName, $this->certificationThumbImageUploadPath, $pathThumb, "s3");
                    \File::delete($this->certificationOriginalImageUploadPath . $fileName);
                    \File::delete($this->certificationThumbImageUploadPath . $fileName);
                    $certificationDetail['pc_image'] = $fileName;
                }                
            }
        }
        $response = $this->objCertification->saveProfessionCertificationDetail($certificationDetail);
        if ($response) {
             return Redirect::to("admin/professionCertifications")->with('success',trans('labels.professioncertificationupdatesuccess'));
        } else {
            return Redirect::to("admin/professionCertifications")->with('error', trans('labels.commonerrormessage'));
        }
    }

    public function delete($id) {
        $return = $this->objCertification->deleteCertification($id);
        if ($return){
           return Redirect::to("admin/professionCertifications")->with('success', trans('labels.professioncertificationdeletesuccess'));
        } else {
            return Redirect::to("admin/professionCertifications")->with('error', trans('labels.commonerrormessage'));
        }
    }

    public function professionWiseCertificationIndex() {
        $data = $this->objProfessionWiseCertification->getAllProfessionWiseCertification();
        return view('admin.ListProfessionWiseCertification', compact('data'));
    }

    public function professionWiseCertificationAdd() {
        $data = [];
        $profession = $this->objProfessions->getActiveProfessions();
        $certificateList = $this->objCertification->getAllProfessionCertifications();
        $professionList = $profession->sortBy('pf_name');
        return view('admin.EditProfessionWiseCertification', compact('data','professionList','certificateList'));
    }

    public function professionWiseCertificationAddBulk() {
        return view('admin.AddProfessionWiseCertificateBulk');
    }

    public function professionWiseCertificationEdit($id) {
        $data = $this->objProfessionWiseCertification->getProfessionWiseCertificationByProfessionId($id);
        $profession = $this->objProfessions->getActiveProfessions();
        $certificateList = $this->objCertification->getAllProfessionCertifications();
        $professionList = $profession->sortBy('pf_name');
        // echo "<pre>"; print_r($data); exit;
        return view('admin.EditProfessionWiseCertification', compact('data','professionList','certificateList'));
    }

    public function professionWiseCertificationSave(ProfessionWiseCertificationRequest $professionWiseCertificationRequest) {

        if(Input::get('delete_id'))
        {
            $this->objProfessionWiseCertification->deleteProfessionWiseCertificationByProfessionId(Input::get('delete_id'));
        }
        else
        {
            $checkProfessionWiseCertificationByProfessionId = $this->objProfessionWiseCertification->getProfessionWiseCertificationByProfessionId(Input::get('profession_id'));
            if(count($checkProfessionWiseCertificationByProfessionId)>0){
                return Redirect::to("admin/professionWiseCertifications")->with('error',trans('labels.professionwisecertificationdataalreadyaddedforprofession'));
            }
        }
        $certificateId = Input::get('certificate_id');
        foreach ($certificateId as $value) {
            $data = [];
            $data['profession_id'] = e(Input::get('profession_id'));
            $data['certificate_id'] = $value;
            $response = $this->objProfessionWiseCertification->insertUpdate($data);
        }
        if ($response) {
             return Redirect::to("admin/professionWiseCertifications")->with('success',trans('labels.professionwisecertificationupdatesuccess'));
        } else {
            return Redirect::to("admin/professionWiseCertifications")->with('error', trans('labels.commonerrormessage'));
        }
    }

    public function professionWiseCertificationSaveBulk() {
        $response = '';        
        $path = Input::file('p_bulk')->getRealPath();

        $results = Excel::load($path, function($reader) {})->get();
        $data = Excel::load($path, function($reader) { $reader->noHeading = true; }, 'ISO-8859-1')->get();

        foreach ($data[0] as $key => $value) {
            if($value != 'profession_name'){
                $certificateData = $this->objCertification->getProfessionCertificationByName($value);
                if (!$certificateData){
                    return Redirect::to("admin/professions")->with('error',$value.' '.trans('labels.professionwisecertificationbulkuploadcertificatenotfound'));
                }
            }
        }

        foreach ($results as $key => $value) {
                $professionsData = $this->objProfessions->getProfessionByName($value['profession_name']);
                if (!$professionsData){
                    return Redirect::to("admin/professions")->with('error',$value['profession_name'].' '.trans('labels.professionwisecertificationbulkuploadprofessionnotfound'));
                }
        }

        foreach ($results as $key => $value) {
                $professionsData = $this->objProfessions->getProfessionByName($value['profession_name']);
                $data = [];
                foreach ($value as $k => $v) {
                    if($k != 'profession_name' && $v == "Yes"){
                        $certificateData = $this->objCertification->getProfessionCertificationByName($k);
                        if ($certificateData){
                            $data['profession_id'] = $professionsData->id;
                            $data['certificate_id'] = $certificateData->id;
                            $response = $this->objProfessionWiseCertification->insertUpdate($data);
                        }
                    }
                }
        }
        
        if($response) {
            return Redirect::to("admin/professions")->with('success', trans('labels.professionwisecertificationbulkuploadsuccess'));
        } else {
            return Redirect::to("admin/professions")->with('error', trans('labels.commonerrormessage'));
        }
    }

    public function professionWiseCertificationDelete($id) {
        $return = $this->objProfessionWiseCertification->deleteProfessionWiseCertificationByProfessionId($id);
        if ($return){
           return Redirect::to("admin/professionWiseCertifications")->with('success', trans('labels.professionwisecertificationdeletesuccess'));
        } else {
            return Redirect::to("admin/professionWiseCertifications")->with('error', trans('labels.commonerrormessage'));
        }
    }

}