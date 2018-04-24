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
use App\Apptitude;
use App\Personality;
use App\CareerMapping;
use App\Services\Professions\Contracts\ProfessionsRepository;
use Input;
use App\TeenagerPromiseScore;

class MultipleIntelligenceManagementController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(ProfessionsRepository $professionsRepository)
    {
        $this->objMultipleIntelligent = new MultipleIntelligent();
        $this->objApptitude = new Apptitude();
        $this->objPersonality = new Personality();
        $this->miThumbImageUploadPath = Config::get('constant.MI_THUMB_IMAGE_UPLOAD_PATH');
        $this->apptitudeThumbImageUploadPath = Config::get('constant.APPTITUDE_THUMB_IMAGE_UPLOAD_PATH');
        $this->personalityThumbImageUploadPath = Config::get('constant.PERSONALITY_THUMB_IMAGE_UPLOAD_PATH');
        $this->objCareerMapping = new CareerMapping;
        $this->professionsRepository = $professionsRepository;
        $this->objTeenagerPromiseScore = new TeenagerPromiseScore;
    }

    /**
     * Returns Strength Details Page
     */
    public function index($type, $slug) 
    {       
        if (!empty($type) || !empty($slug)) {
            $userId = Auth::guard('teenager')->user()->id;
            $multipleIntelligence = new \stdClass();
            switch($type) {
                case Config::get('constant.MULTI_INTELLIGENCE_TYPE'):
                    $mi = $this->objMultipleIntelligent->getMultipleIntelligenceDetailBySlug($slug);
                    if(isset($mi) && count($mi) > 0) {
                        $multipleIntelligence->title = $mi->mit_name;
                        $multipleIntelligence->slug = $mi->mi_slug;
                        $multipleIntelligence->logo = $mi->mit_logo;
                        $multipleIntelligence->video = $mi->mi_video;
                        $multipleIntelligence->description = $mi->mi_information;
                        $multipleIntelligence->type = Config::get('constant.MULTI_INTELLIGENCE_TYPE');
                        $miThumbImageUploadPath = $this->miThumbImageUploadPath;
                    }else{
                        return Redirect::to("teenager/home")->withErrors("Invalid data passed to URL");
                    }
                    break;

                case Config::get('constant.APPTITUDE_TYPE'):
                    $apptitude = $this->objApptitude->getApptitudeDetailBySlug($slug);
                    if(isset($apptitude) && count($apptitude) > 0) {
                        $multipleIntelligence->title = $apptitude->apt_name;
                        $multipleIntelligence->slug = $apptitude->apt_slug;
                        $multipleIntelligence->logo = $apptitude->apt_logo;
                        $multipleIntelligence->video = $apptitude->apt_video;
                        $multipleIntelligence->description = $apptitude->ap_information;
                        $multipleIntelligence->type = Config::get('constant.APPTITUDE_TYPE');
                        $miThumbImageUploadPath = $this->apptitudeThumbImageUploadPath;
                    }else{
                        return Redirect::to("teenager/home")->withErrors("Invalid data passed to URL");
                    }
                    break;

                case Config::get('constant.PERSONALITY_TYPE'):
                    $personality =$this->objPersonality->getPersonalityDetailBySlug($slug);
                    if(isset($personality) && count($personality) > 0) {
                        $multipleIntelligence->title = $personality->pt_name;
                        $multipleIntelligence->slug = $personality->pt_slug;
                        $multipleIntelligence->logo = $personality->pt_logo;
                        $multipleIntelligence->video = $personality->pt_video;
                        $multipleIntelligence->description = $personality->pt_information;
                        $multipleIntelligence->type = Config::get('constant.PERSONALITY_TYPE');
                        $miThumbImageUploadPath = $this->personalityThumbImageUploadPath;
                    }else{
                        return Redirect::to("teenager/home")->withErrors("Invalid data passed to URL");
                    }
                    break;

                default:
                    return redirect::back()->with('error', trans('labels.commonerrormessage'));
            };
            $careersDetails = Helpers::getCareerMapColumnName();
            $relatedCareers = $this->objCareerMapping->getRelatedCareers($careersDetails[$slug]);
            
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
            
            $relatedCareersCount = $this->objCareerMapping->getRelatedCareersCount($careersDetails[$slug]);
            $reasoningGurus = $this->objTeenagerPromiseScore->getTeenagersWithHighestPromiseScore($slug);
            $nextReasoningGurus = $this->objTeenagerPromiseScore->getTeenagersWithHighestPromiseScore($slug, 1);
            if (isset($nextReasoningGurus) && count($nextReasoningGurus) > 0) {
                $nextSlotExist = 1;
            } else {
                $nextSlotExist = -1;
            }
            return view('teenager.multipleIntelligence', compact('matchScaleCount', 'multipleIntelligence', 'miThumbImageUploadPath', 'relatedCareers', 'attemptedProfessions', 'relatedCareersCount', 'reasoningGurus', 'nextSlotExist'));

        } else {
            return redirect::back()->with('error', trans('labels.commonerrormessage'));
        }
    }

    /**
     * Returns array of related careers
     */
    public function seeMoreRelatedCareers()
    {
        $lastCareerId = Input::get('lastCareerId');
        $slug = Input::get('slug');
        $userId = Auth::guard('teenager')->user()->id;
        
        $careersDetails = Helpers::getCareerMapColumnName();
        $relatedCareers = $this->objCareerMapping->getRelatedCareers($careersDetails[$slug], $lastCareerId);
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
        $relatedCareersCount = $this->objCareerMapping->getRelatedCareersCount($careersDetails[$slug], $lastCareerId);
        return view('teenager.relatedCareers', compact('matchScaleCount', 'relatedCareers', 'relatedCareersCount'));
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
