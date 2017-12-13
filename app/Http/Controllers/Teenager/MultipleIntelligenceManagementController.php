<?php

namespace App\Http\Controllers\Teenager;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Helpers;
use Config;
use Mail;
use Session;
use Redirect;
use Response;
use App\MultipleIntelligent;

class MultipleIntelligenceManagementController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->objMultipleIntelligent = new MultipleIntelligent;
        $this->miOriginalImageUploadPath = Config::get('constant.MI_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->miThumbImageUploadPath = Config::get('constant.MI_THUMB_IMAGE_UPLOAD_PATH');
    }

    //Retrieve multiple intelligence data by name
    public function index($slug) 
    {
        $miThumbImageUploadPath = $this->miThumbImageUploadPath;
        $multipleIntelligence = $this->objMultipleIntelligent->getMultipleIntelligenceDetailBySlug($slug);
        return view('teenager.multipleIntelligence', compact('multipleIntelligence', 'miThumbImageUploadPath'));
    }

}
