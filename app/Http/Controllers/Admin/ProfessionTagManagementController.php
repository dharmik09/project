<?php

namespace App\Http\Controllers\Admin;

use File;
use Image;
use Auth;
use Input;
use Config;
use Request;
use Redirect;
use App\ProfessionTag;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProfessionTagRequest;
use Helpers;
use App\Services\FileStorage\Contracts\FileStorageRepository;

class ProfessionTagManagementController extends Controller
{
    public function __construct(FileStorageRepository $fileStorageRepository) {
        $this->fileStorageRepository = $fileStorageRepository;
        $this->objTag = new ProfessionTag;
        $this->tagOriginalImageUploadPath = Config::get("constant.PROFESSION_TAG_ORIGINAL_IMAGE_UPLOAD_PATH");
        $this->tagThumbImageUploadPath = Config::get("constant.PROFESSION_TAG_THUMB_IMAGE_UPLOAD_PATH");
        $this->tagThumbImageHeight = Config::get("constant.PROFESSION_TAG_THUMB_IMAGE_HEIGHT");
        $this->tagThumbImageWidth = Config::get("constant.PROFESSION_TAG_THUMB_IMAGE_WIDTH");
    }

    public function index() {
        $tagThumbImageUploadPath = $this->tagThumbImageUploadPath;
        $tags = $this->objTag->getAllProfessionTags();
        return view('admin.ListTag', compact('tags', 'tagThumbImageUploadPath'));
    }

    public function add() {
        $tags = [];
        $tagThumbImageUploadPath = $this->tagThumbImageUploadPath;
        return view('admin.EditTag', compact('tags', 'tagThumbImageUploadPath'));
    }

    public function edit($id) {
        $tags = $this->objTag->find($id);
        $tagThumbImageUploadPath = $this->tagThumbImageUploadPath;
        return view('admin.EditTag', compact('tags', 'tagThumbImageUploadPath'));
    }

    public function save(ProfessionTagRequest $ProfessionTagRequest) {
        $tagDetail = [];
        $hiddenLogo = e(input::get('hidden_logo'));
        $tagDetail['id'] = e(Input::get('id'));
        $tagDetail['pt_name'] = e(Input::get('pt_name'));
        $tagDetail['pt_slug'] = e(Input::get('pt_slug'));
        $tagDetail['pt_description'] = e(Input::get('pt_description'));
        $tagDetail['deleted'] = e(Input::get('deleted'));

        if (Input::file())
        {
            $file = Input::file('pt_image');
            if(!empty($file))
            {
                //Check image valid extension 
                $validationPass = Helpers::checkValidImageExtension($file);
                if($validationPass)
                {
                    $fileName = 'ProfessionTag_' . time() . '.' . $file->getClientOriginalExtension();
                    $pathOriginal = public_path($this->tagOriginalImageUploadPath . $fileName);
                    $pathThumb = public_path($this->tagThumbImageUploadPath . $fileName);
                    Image::make($file->getRealPath())->save($pathOriginal);
                    Image::make($file->getRealPath())->resize($this->tagThumbImageWidth, $this->tagThumbImageHeight)->save($pathThumb);
                    
                    if ($hiddenLogo != '')
                    {
                        $originalImageDelete = $this->fileStorageRepository->deleteFileToStorage($hiddenLogo, $this->tagOriginalImageUploadPath, "s3");
                        $thumbImageDelete = $this->fileStorageRepository->deleteFileToStorage($hiddenLogo, $this->tagThumbImageUploadPath, "s3");
                    }

                    //Uploading on AWS
                    $originalImage = $this->fileStorageRepository->addFileToStorage($fileName, $this->tagOriginalImageUploadPath, $pathOriginal, "s3");
                    $thumbImage = $this->fileStorageRepository->addFileToStorage($fileName, $this->tagThumbImageUploadPath, $pathThumb, "s3");
                    \File::delete($this->tagOriginalImageUploadPath . $fileName);
                    \File::delete($this->tagThumbImageUploadPath . $fileName);
                    $tagDetail['pt_image'] = $fileName;
                }                
            }
        }
        $response = $this->objTag->saveProfessionTagDetail($tagDetail);
        if ($response) {
             return Redirect::to("admin/professionTags")->with('success',trans('labels.professiontagupdatesuccess'));
        } else {
            return Redirect::to("admin/professionTags")->with('error', trans('labels.commonerrormessage'));
        }
    }

    public function delete($id) {
        $return = $this->objTag->deleteTag($id);
        if ($return){
           return Redirect::to("admin/professionTags")->with('success', trans('labels.professiontagdeletesuccess'));
        } else {
            return Redirect::to("admin/professionTags")->with('error', trans('labels.commonerrormessage'));
        }
    }

}

