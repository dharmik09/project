<?php

namespace App\Http\Controllers\Developer;

use File;
use Image;
use Auth;
use Input;
use Config;
use Request;
use Redirect;
use App\Personality;
use App\Http\Controllers\Controller;
use App\Http\Requests\PersonalityTypeRequest;
use App\Services\FileStorage\Contracts\FileStorageRepository;

class PersonalityTypeManagementController extends Controller
{
    public function __construct(FileStorageRepository $fileStorageRepository) {
        //$this->middleware('auth.developer');
        $this->objPersonality = new Personality();
        $this->fileStorageRepository = $fileStorageRepository;
        $this->personalityOriginalImageUploadPath = Config::get('constant.PERSONALITY_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->personalityThumbImageUploadPath = Config::get('constant.PERSONALITY_THUMB_IMAGE_UPLOAD_PATH');
        $this->personalityThumbImageHeight = Config::get('constant.PERSONALITY_THUMB_IMAGE_HEIGHT');
        $this->personalityThumbImageWidth = Config::get('constant.PERSONALITY_THUMB_IMAGE_WIDTH'); 
    }
    public function index()
    {
        $personalitytypes = $this->objPersonality->getAllPersonalityTypes();
        $personalityThumbPath = $this->personalityThumbImageUploadPath;
        return view('developer.ListPersonalityTypes', compact('personalitytypes','personalityThumbPath'));
    }

    public function add()
    {
        $personalityDetail = [];

        return view('developer.EditPersonalityType', compact('personalityDetail'));
    }

    public function edit($id)
    {
        $personalityDetail = $this->objPersonality->find($id);
        $personalityThumbPath = $this->personalityThumbImageUploadPath;
        return view('developer.EditPersonalityType', compact('personalityDetail','personalityThumbPath'));
    }

    public function save(PersonalityTypeRequest $personalityTypeRequest)
    {
        $personalityDetail = [];

        $personalityDetail['id'] = e(Input::get('id'));
        $personalityDetail['pt_name'] = e(Input::get('pt_name'));
        $personalityDetail['pt_slug'] = e(Input::get('pt_slug'));
        $personalityDetail['pt_video'] = e(Input::get('pt_video'));
        $personalityDetail['pt_information'] = e(Input::get('pit_information'));
        $hiddenLogo = e(input::get('hidden_logo'));
        $personalityDetail['deleted'] = e(Input::get('deleted'));

        /* start upload image of cartoons */
        if (Input::file())
        {
            $file = Input::file('pt_logo');
            if(!empty($file))
            {
                $fileName = 'personality_' . time() . '.' . $file->getClientOriginalExtension();
                $pathOriginal = public_path($this->personalityOriginalImageUploadPath . $fileName);
                $pathThumb = public_path($this->personalityThumbImageUploadPath . $fileName);
                
                Image::make($file->getRealPath())->save($pathOriginal);
                Image::make($file->getRealPath())->resize($this->personalityThumbImageWidth, $this->personalityThumbImageHeight)->save($pathThumb);
                
                if ($hiddenLogo != '')
                {
                    $originalImageDelete = $this->fileStorageRepository->deleteFileToStorage($hiddenLogo, $this->personalityOriginalImageUploadPath, "s3");
                        $thumbImageDelete = $this->fileStorageRepository->deleteFileToStorage($hiddenLogo, $this->personalityThumbImageUploadPath, "s3");
                }
                
                //Uploading on AWS
                $originalImage = $this->fileStorageRepository->addFileToStorage($fileName, $this->personalityOriginalImageUploadPath, $pathOriginal, "s3");
                $thumbImage = $this->fileStorageRepository->addFileToStorage($fileName, $this->personalityThumbImageUploadPath, $pathThumb, "s3");
                
                \File::delete($this->personalityOriginalImageUploadPath . $fileName);
                \File::delete($this->personalityThumbImageUploadPath . $fileName);
                $personalityDetail['pt_logo'] = $fileName;
            }
        }
        /* stop upload image of cartoons */
        $response = $this->objPersonality->savePersonalityDetail($personalityDetail);
        if($response)
        {
             return Redirect::to("developer/personalityType")->with('success',trans('labels.personalityupdatesuccess'));
        }
        else
        {
            return Redirect::to("developer/personalityType")->with('error', trans('labels.commonerrormessage'));
        }
    }

    public function delete($id)
    {
        $return = $this->objPersonality->deletePersonalityType($id);
        if ($return)
        {
           return Redirect::to("developer/personalityType")->with('success', trans('labels.personalitydeletesuccess'));
        }
        else
        {
            return Redirect::to("developer/personalityType")->with('error', trans('labels.commonerrormessage'));
        }
    }
}

