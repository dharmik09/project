<?php

namespace App\Http\Controllers\Developer;

use File;
use Image;
use Auth;
use Input;
use Config;
use Request;
use Redirect;
use App\Apptitude;
use App\Http\Controllers\Controller;
use App\Http\Requests\ApptitudeTypeRequest;

class ApptitudeTypeManagementController extends Controller
{
    public function __construct() {
        //$this->middleware('auth.developer');
        $this->objApptitude = new Apptitude();
        $this->apptitudeOriginalImageUploadPath = Config::get('constant.APPTITUDE_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->apptitudeThumbImageUploadPath = Config::get('constant.APPTITUDE_THUMB_IMAGE_UPLOAD_PATH');
        $this->apptitudeThumbImageHeight = Config::get('constant.APPTITUDE_THUMB_IMAGE_HEIGHT');
        $this->apptitudeThumbImageWidth = Config::get('constant.APPTITUDE_THUMB_IMAGE_WIDTH');
    }

    public function index()
    {
        $searchParamArray = Input::all();
        $apptitudetypes = $this->objApptitude->getAllApptitudeTypes($searchParamArray);
        $apptitudeThumbPath = $this->apptitudeThumbImageUploadPath;
        return view('developer.ListApptitudeTypes' , compact('apptitudetypes','apptitudeThumbPath'));
    }

    public function add()
    {
        $apptitudeDetail = [];

        return view('developer.EditApptitudeType', compact('apptitudeDetail'));
    }

    public function edit($id)
    {
        $apptitudeDetail = $this->objApptitude->find($id);
        $apptitudeThumbPath = $this->apptitudeThumbImageUploadPath;
        return view('developer.EditApptitudeType', compact('apptitudeDetail','apptitudeThumbPath'));
    }

    public function save(ApptitudeTypeRequest $ApptitudeTypeRequest)
    {
        $apptitudeDetail = [];
        
        $apptitudeDetail['id'] = e(Input::get('id'));
        $apptitudeDetail['apt_name'] = e(Input::get('apt_name'));
        $apptitudeDetail['apt_video'] = e(Input::get('ap_video'));
        $apptitudeDetail['ap_information'] = e(Input::get('apt_information'));
        $hiddenLogo  = e(input::get('hidden_logo'));
        $apptitudeDetail['deleted'] = e(Input::get('deleted'));
        
        /* start upload image of cartoons */
        if (Input::file())
        {
            $file = Input::file('apt_logo');
            if(!empty($file))
            {
                $fileName = 'apptitude_' . time() . '.' . $file->getClientOriginalExtension();
                $pathOriginal = public_path($this->apptitudeOriginalImageUploadPath . $fileName);
                $pathThumb = public_path($this->apptitudeThumbImageUploadPath . $fileName);
                
                Image::make($file->getRealPath())->save($pathOriginal);
                Image::make($file->getRealPath())->resize($this->apptitudeThumbImageWidth, $this->apptitudeThumbImageHeight)->save($pathThumb);
                
                if ($hiddenLogo != '')
                {
                    $imageOriginal = public_path($this->apptitudeOriginalImageUploadPath . $hiddenLogo);
                    $imageThumb = public_path($this->apptitudeThumbImageUploadPath . $hiddenLogo);
                    File::delete($imageOriginal, $imageThumb);
                }
                
                $apptitudeDetail['apt_logo'] = $fileName;
            }
        }
        /* stop upload image of cartoons */
        
        $response = $this->objApptitude->saveApptitudeDetail($apptitudeDetail);
        if($response)
        {
             return Redirect::to("developer/apptitudetype")->with('success',trans('labels.apptitudeupdatesuccess'));
        }
        else
        {
            return Redirect::to("developer/apptitudetype")->with('error', trans('labels.commonerrormessage'));
        }
    }

    public function delete($id)
    {
        $return = $this->objApptitude->deleteApptitudeType($id);
        if ($return)
        {
           return Redirect::to("developer/apptitudetype")->with('success', trans('labels.apptitudedeletesuccess'));
        }
        else
        {
            return Redirect::to("developer/apptitudetype")->with('error', trans('labels.commonerrormessage'));
        }
    }

}

