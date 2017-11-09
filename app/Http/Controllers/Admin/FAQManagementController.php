<?php

namespace App\Http\Controllers\Admin;

use Auth;
use Input;
use Image;
use DB;
use File;
use Config;
use Request;
use Helpers;
use Redirect;
use Illuminate\Pagination\Paginator;
use App\Http\Controllers\Controller;
use App\Http\Requests\FAQRequest;
use App\FAQ;
use App\Services\FileStorage\Contracts\FileStorageRepository;

class FAQManagementController extends Controller {

    public function __construct(FileStorageRepository $fileStorageRepository) {
        //$this->middleware('auth.admin');
        $this->objFAQ = new FAQ();
        $this->fileStorageRepository = $fileStorageRepository;
        $this->faqOriginalImageUploadPath = Config::get('constant.FAQ_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->faqThumbImageUploadPath = Config::get('constant.FAQ_THUMB_IMAGE_UPLOAD_PATH');
        $this->faqThumbImageHeight = Config::get('constant.FAQ_THUMB_IMAGE_HEIGHT');
        $this->faqThumbImageWidth = Config::get('constant.FAQ_THUMB_IMAGE_WIDTH');
    }

    public function index() {
        $faqDetail = $this->objFAQ->getAllFAQ();
        $uploadFAQThumbPath = $this->faqThumbImageUploadPath;
        return view('admin.ListFAQ', compact('faqDetail','uploadFAQThumbPath'));
    }

    public function add() {
        $faqDetail = [];
        return view('admin.EditFAQ', compact('faqDetail'));
    }

    public function edit($id) {
        $faqDetail = $this->objFAQ->find($id);
        $uploadFAQThumbPath = $this->faqThumbImageUploadPath;
        return view('admin.EditFAQ', compact('faqDetail','uploadFAQThumbPath'));
    }

    public function save(FAQRequest $FAQRequest) {
        $faqData = [];
        $faqData['id'] = e(Input::get('id'));
        $faqData['f_question_text'] = Input::get('f_question_text');
        $faqData['f_que_answer'] = Input::get('f_que_answer');
        $faqData['f_que_group'] = Input::get('f_que_group');
        $faqData['deleted'] = Input::get('deleted');
        $hiddenPhoto = trim(Input::get('hidden_photo'));
        $faqData['f_photo'] = $hiddenPhoto;

        if (Input::file()) {
            $file = Input::file('f_photo');
            if (!empty($file)) {
                $fileName = 'faq_' . time() . '.' . $file->getClientOriginalExtension();
                $pathOriginal = public_path($this->faqOriginalImageUploadPath . $fileName);
                $pathThumb = public_path($this->faqThumbImageUploadPath . $fileName);

                Image::make($file->getRealPath())->save($pathOriginal);
                Image::make($file->getRealPath())->resize($this->faqThumbImageWidth, $this->faqThumbImageHeight)->save($pathThumb);

                if ($hiddenPhoto != '' && $hiddenPhoto != "proteen-logo.png") {
                    $originalImageDelete = $this->fileStorageRepository->deleteFileToStorage($hiddenPhoto, $this->faqOriginalImageUploadPath, "s3");
                        $thumbImageDelete = $this->fileStorageRepository->deleteFileToStorage($hiddenPhoto, $this->faqThumbImageUploadPath, "s3");
                }

                //Uploading on AWS
                $originalImage = $this->fileStorageRepository->addFileToStorage($fileName, $this->faqOriginalImageUploadPath, $pathOriginal, "s3");
                $thumbImage = $this->fileStorageRepository->addFileToStorage($fileName, $this->faqThumbImageUploadPath, $pathThumb, "s3");
                
                \File::delete($this->faqOriginalImageUploadPath . $fileName);
                \File::delete($this->faqThumbImageUploadPath . $fileName);
                $faqData['f_photo'] = $fileName;
            }
        }

        $response = $this->objFAQ->saveFAQDetail($faqData);
            if ($response) {
                return Redirect::to("admin/faq")->with('success', trans('labels.faqupdatesuccess'));
            } else {
                return Redirect::to("admin/faq")->with('error', trans('labels.commonerrormessage'));
            }
    }

    public function delete($id) {
        $return = $this->objFAQ->deleteFAQ($id);
        if ($return) {
            return Redirect::to("admin/faq")->with('success', trans('labels.faqdeletesuccess'));
        } else {
            return Redirect::to("admin/faq")->with('error', trans('labels.commonerrormessage'));
        }
    }
}
