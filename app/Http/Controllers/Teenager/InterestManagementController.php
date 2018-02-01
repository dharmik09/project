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
use App\Interest;
use App\CareerMapping;
use App\ProfessionWiseSubject;
use Input;

class InterestManagementController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    	$this->objInterest = new Interest;
        $this->objCareerMapping = new CareerMapping;
        $this->interestOriginalImageUploadPath = Config::get('constant.INTEREST_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->interestThumbImageUploadPath = Config::get('constant.INTEREST_THUMB_IMAGE_UPLOAD_PATH');
        $this->objProfessionWiseSubject = new ProfessionWiseSubject;
    }

    public function index($slug)
    {
    	$interestThumbImageUploadPath = $this->interestThumbImageUploadPath;
        $interest = $this->objInterest->getInterestDetailBySlug($slug);
        $subSlug = explode('it_', $slug);
        $relatedCareers = $this->objProfessionWiseSubject->getProfessionsBySubjectSlug($subSlug[1]);
        $relatedCareersCount = $this->objProfessionWiseSubject->getProfessionsCountBySubjectSlug($subSlug[1]);
        return view('teenager.interest', compact('interest', 'interestThumbImageUploadPath', 'relatedCareers', 'relatedCareersCount'));
    }

    public function seeMoreRelatedCareers()
    {
        $lastCareerId = Input::get('lastCareerId');
        $slug = Input::get('slug');
        $subSlug = explode('it_', $slug);
        $relatedCareers = $this->objProfessionWiseSubject->getProfessionsBySubjectSlug($subSlug[1], $lastCareerId);
        $relatedCareersCount = $this->objProfessionWiseSubject->getProfessionsCountBySubjectSlug($subSlug[1], $lastCareerId);
        return view('teenager.relatedCareers', compact('relatedCareers', 'relatedCareersCount'));
    }

    

}
