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
use App\ProfessionSubject;
use Storage;
use App\Services\Professions\Contracts\ProfessionsRepository;

class InterestManagementController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(ProfessionsRepository $professionsRepository)
    {
    	$this->objInterest = new Interest;
        $this->objCareerMapping = new CareerMapping;
        $this->interestOriginalImageUploadPath = Config::get('constant.INTEREST_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->interestThumbImageUploadPath = Config::get('constant.INTEREST_THUMB_IMAGE_UPLOAD_PATH');
        $this->objProfessionWiseSubject = new ProfessionWiseSubject;
        $this->objTeenagerPromiseScore = new TeenagerPromiseScore;
        $this->objProfessionSubject = new ProfessionSubject;
        $this->professionsRepository = $professionsRepository;
        $this->subjectOriginalImageUploadPath = Config::get("constant.PROFESSION_SUBJECT_ORIGINAL_IMAGE_UPLOAD_PATH");
    }

    /**
     * Returns interest Details Page
     */
    public function index($slug)
    {
    	$interestThumbImageUploadPath = $this->interestThumbImageUploadPath;
        $interestDetails = $this->objInterest->getInterestDetailBySlug($slug);
        $subSlug = explode('it_', $slug);
        $interest = new \stdClass();
        if (isset($interestDetails) && !empty($interestDetails)) {
            $interest->id = $interestDetails->id;
            $interest->name = $interestDetails->it_name;
            $interest->slug = $interestDetails->it_slug;
            $interest->description = $interestDetails->it_description;
            if ($interestDetails->it_logo != "" && Storage::size($this->interestThumbImageUploadPath . $interestDetails->it_logo) > 0 ) {
                $interest->logo = $this->interestThumbImageUploadPath . $interestDetails->it_logo;
            } else {
                $interest->logo = $this->interestThumbImageUploadPath . 'proteen-logo.png';
            }
            $interest->video = $interestDetails->it_video;
            $reasoningGurus = $this->objTeenagerPromiseScore->getTeenagersWithHighestPromiseScore($slug);
            $nextReasoningGurus = $this->objTeenagerPromiseScore->getTeenagersWithHighestPromiseScore($slug, 1);
        } else {
            $subjectDetails = $this->objProfessionSubject->getSubjectDetailsBySlug($subSlug[1]);
            $interest->id = $subjectDetails->id;
            $interest->name = $subjectDetails->ps_name;
            $interest->slug = $subjectDetails->ps_slug;
            $interest->description = "";
            if ($subjectDetails->ps_image != "" && Storage::size($this->subjectOriginalImageUploadPath . $subjectDetails->ps_image) > 0 ) {
                $interest->logo = $this->subjectOriginalImageUploadPath . $subjectDetails->ps_image;
            } else {
                $interest->logo = $this->subjectOriginalImageUploadPath . 'proteen-logo.png';
            }
            $interest->video = "";
            $reasoningGurus = [];
            $nextReasoningGurus = [];
        }
        
        $relatedCareers = $this->objProfessionWiseSubject->getProfessionsBySubjectSlug($subSlug[1]);
        $relatedCareersCount = $this->objProfessionWiseSubject->getProfessionsCountBySubjectSlug($subSlug[1]);
        
        $userId = Auth::guard('teenager')->user()->id;
        
        $getTeenagerHML = Helpers::getTeenagerMatchScale($userId);
        $matchScaleCount = [];
        if($relatedCareers) {
            $professionAttemptedCount = 0;
            foreach ($relatedCareers as $k => $v) {
                $professionAttempted = $this->professionsRepository->getTeenagerProfessionAttempted($userId, $v->id, null);
                if(count($professionAttempted) > 0){
                    $relatedCareers[$k]['attempted'] = 'yes';
                    $professionAttemptedCount++;
                }
                $matchScale = isset($getTeenagerHML[$v->id]) ? $getTeenagerHML[$v->id] : '';
                if($matchScale == "match") {
                    $relatedCareers[$k]['match_scale'] = "match-strong";
                    $matchScaleCount['match'][] = $v->id;
                } else if($matchScale == "nomatch") {
                    $relatedCareers[$k]['match_scale'] = "match-unlikely";
                    $matchScaleCount['nomatch'][] = $v->id;
                } else if($matchScale == "moderate") {
                    $relatedCareers[$k]['match_scale'] = "match-potential";
                    $matchScaleCount['moderate'][] = $v->id;
                } else {
                    $relatedCareers[$k]['match_scale'] = "career-data-nomatch";
                }
            }
        }
        if (isset($nextReasoningGurus) && count($nextReasoningGurus) > 0) {
            $nextSlotExist = 1;
        } else {
            $nextSlotExist = -1;
        }
        return view('teenager.interest', compact('matchScaleCount', 'interest', 'interestThumbImageUploadPath', 'relatedCareers', 'relatedCareersCount', 'reasoningGurus', 'nextSlotExist'));
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
