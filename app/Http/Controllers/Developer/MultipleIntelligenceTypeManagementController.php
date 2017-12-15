<?php

namespace App\Http\Controllers\Developer;

use File;
use Image;
use Auth;
use Input;
use Config;
use Request;
use Redirect;
use App\MultipleIntelligent;
use App\Http\Controllers\Controller;
use App\Http\Requests\MultipleIntelligenceTypeRequest;
use App\Services\FileStorage\Contracts\FileStorageRepository;

class MultipleIntelligenceTypeManagementController extends Controller
{
    public function __construct(FileStorageRepository $fileStorageRepository) {
        //$this->middleware('auth.developer');
        $this->objMultipleIntelligent = new MultipleIntelligent();
        $this->fileStorageRepository = $fileStorageRepository;
        $this->miOriginalImageUploadPath = Config::get('constant.MI_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->miThumbImageUploadPath = Config::get('constant.MI_THUMB_IMAGE_UPLOAD_PATH');
        $this->miThumbImageHeight = Config::get('constant.MI_THUMB_IMAGE_HEIGHT');
        $this->miThumbImageWidth = Config::get('constant.MI_THUMB_IMAGE_WIDTH');
    }

    public function index()
    {
        $miThumbPath = $this->miThumbImageUploadPath;
        $multipleintelligencetype = $this->objMultipleIntelligent->getAllMultipleIntelligenceTypes();
        return view('developer.ListMultipleIntelligenceTypes' , compact('multipleintelligencetype','miThumbPath'));
    }

    public function add()
    {
        $multipleintelligenceDetail = [];

        return view('developer.EditMultipleIntelligenceType', compact('multipleintelligenceDetail'));
    }

    public function edit($id)
    {
        $multipleintelligenceDetail = $this->objMultipleIntelligent->find($id);
        $miThumbPath = $this->miThumbImageUploadPath;
        return view('developer.EditMultipleIntelligenceType', compact('multipleintelligenceDetail','miThumbPath'));
    }

    public function save(MultipleIntelligenceTypeRequest $multipleIntelligenceTypeRequest)
    {
        $multipleintelligenceDetail = [];

        $multipleintelligenceDetail['id'] = e(Input::get('id'));
        $multipleintelligenceDetail['mit_name'] = e(Input::get('mit_name'));
        $multipleintelligenceDetail['mi_slug'] = e(Input::get('mi_slug'));
        $multipleintelligenceDetail['mi_video'] = e(Input::get('mi_video'));
        $multipleintelligenceDetail['mi_information'] = e(Input::get('mit_information'));
        $hiddenLogo  = e(input::get('hidden_logo'));
        $multipleintelligenceDetail['deleted'] = e(Input::get('deleted'));
        
        /* start upload image of cartoons */
        if (Input::file())
        {
            $file = Input::file('mit_logo');
            if(!empty($file))
            {
                $fileName = 'mi_' . time() . '.' . $file->getClientOriginalExtension();
                $pathOriginal = public_path($this->miOriginalImageUploadPath . $fileName);
                $pathThumb = public_path($this->miThumbImageUploadPath . $fileName);
                        
                Image::make($file->getRealPath())->save($pathOriginal);
                Image::make($file->getRealPath())->resize($this->miThumbImageWidth, $this->miThumbImageHeight)->save($pathThumb);
                
                if ($hiddenLogo != '')
                {
                    $originalImageDelete = $this->fileStorageRepository->deleteFileToStorage($hiddenLogo, $this->miOriginalImageUploadPath, "s3");
                        $thumbImageDelete = $this->fileStorageRepository->deleteFileToStorage($hiddenLogo, $this->miThumbImageUploadPath, "s3");
                }

                //Uploading on AWS
                $originalImage = $this->fileStorageRepository->addFileToStorage($fileName, $this->miOriginalImageUploadPath, $pathOriginal, "s3");
                $thumbImage = $this->fileStorageRepository->addFileToStorage($fileName, $this->miThumbImageUploadPath, $pathThumb, "s3");
                
                \File::delete($this->miOriginalImageUploadPath . $fileName);
                \File::delete($this->miThumbImageUploadPath . $fileName);
                $multipleintelligenceDetail['mit_logo'] = $fileName;
                
            }
        }
        /* stop upload image of cartoons */
        $response = $this->objMultipleIntelligent->saveMultipleIntelligenceDetail($multipleintelligenceDetail);
        if($response)
        {
             return Redirect::to("developer/multipleintelligenceType")->with('success',trans('labels.multipleintelligenceupdatesuccess'));
        }
        else
        {
            return Redirect::to("developer/multipleintelligenceType")->with('error', trans('labels.commonerrormessage'));
        }
    }

    public function delete($id)
    {
        $return = $this->objMultipleIntelligent->deleteMultipleIntelligenceType($id);
        if ($return)
        {
           return Redirect::to("developer/multipleintelligenceType")->with('success', trans('labels.multipleintelligencedeletesuccess'));
        }
        else
        {
            return Redirect::to("developer/multipleintelligenceType")->with('error', trans('labels.commonerrormessage'));
        }
    }

}

