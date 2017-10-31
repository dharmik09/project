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

class PersonalityTypeManagementController extends Controller
{
    public function __construct() {
        //$this->middleware('auth.developer');
        $this->objPersonality = new Personality();
        $this->personalityOriginalImageUploadPath = Config::get('constant.PERSONALITY_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->personalityThumbImageUploadPath = Config::get('constant.PERSONALITY_THUMB_IMAGE_UPLOAD_PATH');
        $this->personalityThumbImageHeight = Config::get('constant.PERSONALITY_THUMB_IMAGE_HEIGHT');
        $this->personalityThumbImageWidth = Config::get('constant.PERSONALITY_THUMB_IMAGE_WIDTH'); 
    }
    public function index()
    {
        $searchParamArray = Input::all();
        $personalitytypes = $this->objPersonality->getAllPersonalityTypes($searchParamArray);
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

    public function save(PersonalityTypeRequest $PersonalityTypeRequest)
    {
        $personalityDetail = [];

        $personalityDetail['id'] = e(Input::get('id'));
        $personalityDetail['pt_name'] = e(Input::get('pt_name'));
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
                    $imageOriginal = public_path($this->personalityOriginalImageUploadPath . $hiddenLogo);
                    $imageThumb = public_path($this->personalityThumbImageUploadPath . $hiddenLogo);
                    File::delete($imageOriginal, $imageThumb);
                }
                
                $personalityDetail['pt_logo'] = $fileName;
            }
        }
        /* stop upload image of cartoons */
        $response = $this->objPersonality->savePersonalityDetail($personalityDetail);
        if($response)
        {
             return Redirect::to("developer/personalitytype")->with('success',trans('labels.personalityupdatesuccess'));
        }
        else
        {
            return Redirect::to("developer/personalitytype")->with('error', trans('labels.commonerrormessage'));
        }
    }

    public function delete($id)
    {
        $return = $this->objPersonality->deletePersonalityType($id);
        if ($return)
        {
           return Redirect::to("developer/personalitytype")->with('success', trans('labels.personalitydeletesuccess'));
        }
        else
        {
            return Redirect::to("developer/personalitytype")->with('error', trans('labels.commonerrormessage'));
        }
    }
}

