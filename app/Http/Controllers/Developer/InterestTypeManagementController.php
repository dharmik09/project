<?php

namespace App\Http\Controllers\Developer;

use File;
use Image;
use Auth;
use Input;
use Config;
use Request;
use Redirect;
use App\Interest;
use App\Http\Controllers\Controller;
use App\Http\Requests\InterestTypeRequest;

class InterestTypeManagementController extends Controller
{
    public function __construct() {
        //$this->middleware('auth.developer');
        $this->objInterest = new Interest();
        $this->interestOriginalImageUploadPath = Config::get('constant.INTEREST_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->interestThumbImageUploadPath = Config::get('constant.INTEREST_THUMB_IMAGE_UPLOAD_PATH');
        $this->interestThumbImageHeight = Config::get('constant.INTEREST_THUMB_IMAGE_HEIGHT');
        $this->interestThumbImageWidth = Config::get('constant.INTEREST_THUMB_IMAGE_WIDTH');
    }

    public function index()
    {
        $interestThumbPath = $this->interestThumbImageUploadPath;
        $interesttypes = $this->objInterest->getAllInterestTypes();
        return view('developer.ListInterestTypes', compact('interesttypes','interestThumbPath'));
    }

    public function add()
    {
        $interestDetail = [];

        return view('developer.EditInterestType', compact('interestDetail'));
    }

    public function edit($id)
    {
        $interestDetail = $this->objInterest->find($id);
        $interestThumbPath = $this->interestThumbImageUploadPath;
        return view('developer.EditInterestType', compact('interestDetail','interestThumbPath'));
    }

    public function save(InterestTypeRequest $InterestTypeRequest)
    {
        $interestDetail = [];

        $interestDetail['id'] = e(Input::get('id'));
        $interestDetail['it_name'] = e(Input::get('it_name'));
        $hiddenLogo  = e(input::get('hidden_logo'));
        $interestDetail['deleted'] = e(Input::get('deleted'));
        
        /* start upload logo of interest */
        if (Input::file())
        {
            $file = Input::file('it_logo');
            if(!empty($file))
            {
                $fileName = 'it_' . time() . '.' . $file->getClientOriginalExtension();
                $pathOriginal = public_path($this->interestOriginalImageUploadPath . $fileName);
                $pathThumb = public_path($this->interestThumbImageUploadPath . $fileName);
                
                Image::make($file->getRealPath())->save($pathOriginal);
                Image::make($file->getRealPath())->resize($this->interestThumbImageWidth, $this->interestThumbImageHeight)->save($pathThumb);
                
                if ($hiddenLogo != '')
                {
                    $imageOriginal = public_path($this->interestOriginalImageUploadPath . $hiddenLogo);
                    $imageThumb = public_path($this->interestThumbImageUploadPath . $hiddenLogo);
                    File::delete($imageOriginal, $imageThumb);
                }
                $interestDetail['it_logo'] = $fileName;
            } 
        }
        /* stop upload logo of interest */ 
        
        $response = $this->objInterest->saveInterestDetail($interestDetail);
        if($response)
        {
            return Redirect::to("developer/interestType")->with('success',trans('labels.interestupdatesuccess'));
        }
        else
        {
            return Redirect::to("developer/interestType")->with('error', trans('labels.commonerrormessage'));
        }
    }

    public function delete($id)
    {
        $return = $this->objInterest->deleteInterestType($id);
        if ($return)
        {
           return Redirect::to("developer/interestType")->with('success', trans('labels.interestdeletesuccess'));
        }
        else
        {
            return Redirect::to("developer/interestType")->with('error', trans('labels.commonerrormessage'));
        }
    }

}

