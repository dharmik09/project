<?php

namespace App\Http\Controllers\Admin;

use File;
use Image;
use Auth;
use Input;
use Config;
use Request;
use Redirect;
use App\ProfessionSubject;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProfessionSubjectRequest;
use Helpers;
use App\Services\FileStorage\Contracts\FileStorageRepository;

class ProfessionSubjectManagementController extends Controller
{
    public function __construct(FileStorageRepository $fileStorageRepository) {
        $this->fileStorageRepository = $fileStorageRepository;
        $this->objSubject = new ProfessionSubject;
        $this->subjectOriginalImageUploadPath = Config::get("constant.PROFESSION_SUBJECT_ORIGINAL_IMAGE_UPLOAD_PATH");
        $this->subjectThumbImageUploadPath = Config::get("constant.PROFESSION_SUBJECT_THUMB_IMAGE_UPLOAD_PATH");
        $this->subjectThumbImageHeight = Config::get("constant.PROFESSION_SUBJECT_THUMB_IMAGE_HEIGHT");
        $this->subjectThumbImageWidth = Config::get("constant.PROFESSION_SUBJECT_THUMB_IMAGE_WIDTH");
    }

    public function index() {
        $subjectThumbImageUploadPath = $this->subjectThumbImageUploadPath;
        $subjects = $this->objSubject->getAllProfessionSubjects();
        return view('admin.ListSubject', compact('subjects', 'subjectThumbImageUploadPath'));
    }

    public function add() {
        $subject = [];
        $subjectThumbImageUploadPath = $this->subjectThumbImageUploadPath;
        return view('admin.EditSubject', compact('subject', 'subjectThumbImageUploadPath'));
    }

    public function edit($id) {
        $subject = $this->objSubject->find($id);
        $subjectThumbImageUploadPath = $this->subjectThumbImageUploadPath;
        return view('admin.EditSubject', compact('subject', 'subjectThumbImageUploadPath'));
    }

    public function save(ProfessionSubjectRequest $professionSubjectRequest) {
        $subjectDetail = [];
        $hiddenLogo     = e(input::get('hidden_logo'));
        $subjectDetail['id'] = e(Input::get('id'));
        $subjectDetail['ps_name'] = e(Input::get('ps_name'));
        $subjectDetail['deleted'] = e(Input::get('deleted'));

        if (Input::file())
        {
            $file = Input::file('ps_image');
            if(!empty($file))
            {
                //Check image valid extension 
                $validationPass = Helpers::checkValidImageExtension($file);
                if($validationPass)
                {
                    $fileName = 'professionSubject_' . time() . '.' . $file->getClientOriginalExtension();
                    $pathOriginal = public_path($this->subjectOriginalImageUploadPath . $fileName);
                    $pathThumb = public_path($this->subjectThumbImageUploadPath . $fileName);
                    Image::make($file->getRealPath())->save($pathOriginal);
                    Image::make($file->getRealPath())->resize($this->subjectThumbImageWidth, $this->subjectThumbImageHeight)->save($pathThumb);
                    
                    if ($hiddenLogo != '')
                    {
                        $originalImageDelete = $this->fileStorageRepository->deleteFileToStorage($hiddenLogo, $this->subjectOriginalImageUploadPath, "s3");
                        $thumbImageDelete = $this->fileStorageRepository->deleteFileToStorage($hiddenLogo, $this->subjectThumbImageUploadPath, "s3");
                    }

                    //Uploading on AWS
                    $originalImage = $this->fileStorageRepository->addFileToStorage($fileName, $this->subjectOriginalImageUploadPath, $pathOriginal, "s3");
                    $thumbImage = $this->fileStorageRepository->addFileToStorage($fileName, $this->subjectThumbImageUploadPath, $pathThumb, "s3");
                    \File::delete($this->subjectOriginalImageUploadPath . $fileName);
                    \File::delete($this->subjectThumbImageUploadPath . $fileName);
                    $subjectDetail['ps_image'] = $fileName;
                }                
            }
        }
        $response = $this->objSubject->saveProfessionSubjectDetail($subjectDetail);
        if ($response) {
             return Redirect::to("admin/professionSubjects")->with('success',trans('labels.professionsubjectupdatesuccess'));
        } else {
            return Redirect::to("admin/professionSubjects")->with('error', trans('labels.commonerrormessage'));
        }
    }

    public function delete($id) {
        $return = $this->objSubject->deleteSubject($id);
        if ($return){
           return Redirect::to("admin/professionSubjects")->with('success', trans('labels.professionsubjectdeletesuccess'));
        } else {
            return Redirect::to("admin/professionSubjects")->with('error', trans('labels.commonerrormessage'));
        }
    }

}

