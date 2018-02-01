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
use App\TeenagerPromiseScore;

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
        $this->objTeenagerPromiseScore = new TeenagerPromiseScore;
    }

    /**
     * Returns interest Details Page
     */
    public function index($slug)
    {
    	$interestThumbImageUploadPath = $this->interestThumbImageUploadPath;
        $interest = $this->objInterest->getInterestDetailBySlug($slug);
        $subSlug = explode('it_', $slug);
        $relatedCareers = $this->objProfessionWiseSubject->getProfessionsBySubjectSlug($subSlug[1]);
        $relatedCareersCount = $this->objProfessionWiseSubject->getProfessionsCountBySubjectSlug($subSlug[1]);
        $reasoningGurus = $this->objTeenagerPromiseScore->getTeenagersWithHighestPromiseScore($slug);
        $nextReasoningGurus = $this->objTeenagerPromiseScore->getTeenagersWithHighestPromiseScore($slug, 1);
        if (isset($nextReasoningGurus) && count($nextReasoningGurus) > 0) {
            $nextSlotExist = 1;
        } else {
            $nextSlotExist = -1;
        }
        return view('teenager.interest', compact('interest', 'interestThumbImageUploadPath', 'relatedCareers', 'relatedCareersCount', 'reasoningGurus', 'nextSlotExist'));
    }

    /**
     * Returns array of related careers
     */
    public function seeMoreRelatedCareers()
    {
        $lastCareerId = Input::get('lastCareerId');
        $slug = Input::get('slug');
        $subSlug = explode('it_', $slug);
        $relatedCareers = $this->objProfessionWiseSubject->getProfessionsBySubjectSlug($subSlug[1], $lastCareerId);
        $relatedCareersCount = $this->objProfessionWiseSubject->getProfessionsCountBySubjectSlug($subSlug[1], $lastCareerId);
        return view('teenager.relatedCareers', compact('relatedCareers', 'relatedCareersCount'));
    }

    /**
     * Returns array of teenagers with highest promise score
     */
    public function seeMoreGurus()
    {
        $slot = Input::get('slot');
        $slug = Input::get('slug');
        $reasoningGurus = $this->objTeenagerPromiseScore->getTeenagersWithHighestPromiseScore($slug, $slot);
        if ($slot < 2) {
            $nextReasoningGurus = $this->objTeenagerPromiseScore->getTeenagersWithHighestPromiseScore($slug, $slot + 1);
        } else {
            $nextReasoningGurus = [];
        }
        if (!empty($nextReasoningGurus) && count($nextReasoningGurus) > 0) {
            $nextSlotExist = $slot;
        } else {
            $nextSlotExist = -1;
        }
        return view('teenager.listingGurus', compact('reasoningGurus', 'nextSlotExist'));
    }
}
