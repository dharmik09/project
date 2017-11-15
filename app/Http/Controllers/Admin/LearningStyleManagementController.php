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
use App\LearningStyle;
use App\Professions;
use App\GamificationTemplate;
use App\ProfessionLearningStyle;
use Illuminate\Pagination\Paginator;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests\LearningStyleRequest;
use App\Services\LearningStyle\Contracts\LearningStyleRepository;
use App\Services\FileStorage\Contracts\FileStorageRepository;

class LearningStyleManagementController extends Controller {

    public function __construct(LearningStyleRepository $learningStyleRepository, FileStorageRepository $fileStorageRepository) {
        $this->objLearningStyle = new LearningStyle();
        $this->learningStyleRepository = $learningStyleRepository;
        $this->fileStorageRepository = $fileStorageRepository;
        $this->learningStyleOriginalImageUploadPath = Config::get('constant.LEARNING_STYLE_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->learningStyleThumbImageUploadPath = Config::get('constant.LEARNING_STYLE_THUMB_IMAGE_UPLOAD_PATH');
        $this->learningStyleThumbImageHeight = Config::get('constant.LEARNING_STYLE_THUMB_IMAGE_HEIGHT');
        $this->learningStyleThumbImageWidth = Config::get('constant.LEARNING_STYLE_THUMB_IMAGE_WIDTH');
    }

    public function index() {
        $learningStyle = $this->learningStyleRepository->getAllLearningStyle();
        $uploadLearningStyleThumbPath = $this->learningStyleThumbImageUploadPath;
        return view('admin.ListLearningStyle', compact('learningStyle', 'uploadLearningStyleThumbPath'));
    }

    public function add() {
        $learningStyleDetail = [];
        return view('admin.EditLearningStyle', compact('learningStyleDetail'));
    }

    public function editLearningStyle($id) {
        $learningStyleDetail = $this->objLearningStyle->find($id);
        $uploadLearningStyleThumbPath = $this->learningStyleThumbImageUploadPath;
        return view('admin.EditLearningStyle', compact('learningStyleDetail' , 'uploadLearningStyleThumbPath'));
    }

    public function saveLearningStyle(LearningStyleRequest $learningStyleRequest) {
        $saveData = [];
        $allPostdata = Input::All();
        $postData['pageRank'] = $allPostdata['pageRank'];

        $hiddenImage = trim($allPostdata['hidden_image']);
        if (isset($allPostdata)) {
            $saveData['id'] = $allPostdata['id'];
            $saveData['ls_name'] = $allPostdata['ls_name'];
            $saveData['ls_description'] = $allPostdata['ls_description'];
            $saveData['deleted'] = $allPostdata['deleted'];
            $saveData['ls_image'] = $hiddenImage;
        }
        if (Input::file()) {
            $file = Input::file('ls_image');
            if (!empty($file)) {
                $validationPass = Helpers::checkValidImageExtension($file);
                if($validationPass)
                {
                    $fileName = 'learningStyle_' . time() . '.' . $file->getClientOriginalExtension();
                    $pathOriginal = public_path($this->learningStyleOriginalImageUploadPath . $fileName);
                    $pathThumb = public_path($this->learningStyleThumbImageUploadPath . $fileName);

                    Image::make($file->getRealPath())->save($pathOriginal);
                    Image::make($file->getRealPath())->resize($this->learningStyleThumbImageWidth, $this->learningStyleThumbImageHeight)->save($pathThumb);

                    if ($hiddenImage != '' && $hiddenImage != "proteen-logo.png") {
                        $originalImageDelete = $this->fileStorageRepository->deleteFileToStorage($hiddenImage, $this->learningStyleOriginalImageUploadPath, "s3");
                        $thumbImageDelete = $this->fileStorageRepository->deleteFileToStorage($hiddenImage, $this->learningStyleThumbImageUploadPath, "s3");
                    }

                    //Uploading on AWS
                    $originalImage = $this->fileStorageRepository->addFileToStorage($fileName, $this->learningStyleOriginalImageUploadPath, $pathOriginal, "s3");
                    $thumbImage = $this->fileStorageRepository->addFileToStorage($fileName, $this->learningStyleThumbImageUploadPath, $pathThumb, "s3");
                    
                    \File::delete($this->learningStyleOriginalImageUploadPath . $fileName);
                    \File::delete($this->learningStyleThumbImageUploadPath . $fileName);
                    $saveData['ls_image'] = $fileName;
                }
            }
        }
        $response = $this->learningStyleRepository->saveLearningStyleDetail($saveData);
        if ($response) {
            return Redirect::to("admin/level4LearningStyle".$postData['pageRank'])->with('success', trans('labels.learningstyleupdatesuccess'));
        } else {
            return Redirect::to("admin/level4LearningStyle".$postData['pageRank'])->with('error', trans('labels.commonerrormessage'));
        }
    }
}
