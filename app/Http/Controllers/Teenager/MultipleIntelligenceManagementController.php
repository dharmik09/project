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
    }

    public function index($type, $slug) 
    {
        if (!empty($type) || !empty($slug)) {
            $multipleIntelligence = new \stdClass();
            switch($type) {
                case Config::get('constant.MULTI_INTELLIGENCE_TYPE'):
                    $mi = $this->objMultipleIntelligent->getMultipleIntelligenceDetailBySlug($slug);
                    $multipleIntelligence->title = $mi->mit_name;
                    $multipleIntelligence->slug = $mi->mi_slug;
                    $multipleIntelligence->logo = $mi->mit_logo;
                    $multipleIntelligence->video = $mi->mi_video;
                    $multipleIntelligence->description = $mi->mi_information;
                    $miThumbImageUploadPath = $this->miThumbImageUploadPath;
                    break;

                case Config::get('constant.APPTITUDE_TYPE'):
                    $apptitude = $this->objApptitude->getApptitudeDetailBySlug($slug);
                    $multipleIntelligence->title = $apptitude->apt_name;
                    $multipleIntelligence->slug = $apptitude->apt_slug;
                    $multipleIntelligence->logo = $apptitude->apt_logo;
                    $multipleIntelligence->video = $apptitude->apt_video;
                    $multipleIntelligence->description = $apptitude->ap_information;
                    $miThumbImageUploadPath = $this->apptitudeThumbImageUploadPath;
                    break;

                case Config::get('constant.PERSONALITY_TYPE'):
                    $personality =$this->objPersonality->getPersonalityDetailBySlug($slug);
                    $multipleIntelligence->title = $personality->pt_name;
                    $multipleIntelligence->slug = $personality->pt_slug;
                    $multipleIntelligence->logo = $personality->pt_logo;
                    $multipleIntelligence->video = $personality->pt_video;
                    $multipleIntelligence->description = $personality->pt_information;
                    $miThumbImageUploadPath = $this->personalityThumbImageUploadPath;
                    break;

                default:
                    return redirect::back()->with('error', trans('labels.commonerrormessage'));
            };
            $careersDetails = Helpers::getCareerMapColumnName();
            $relatedCareers = $this->objCareerMapping->getRelatedCareers($careersDetails[$slug]);
            $relatedCareersCount = $this->objCareerMapping->getRelatedCareersCount($careersDetails[$slug]);
            return view('teenager.multipleIntelligence', compact('multipleIntelligence', 'miThumbImageUploadPath', 'relatedCareers', 'attemptedProfessions', 'relatedCareersCount'));

        } else {
            return redirect::back()->with('error', trans('labels.commonerrormessage'));
        }
    }

    public function seeMoreRelatedCareers()
    {
        $lastCareerId = Input::get('lastCareerId');
        $relatedCareers = $this->objCareerMapping->getRelatedCareers('tcm_logical_reasoning', $lastCareerId);
        $relatedCareersCount = $this->objCareerMapping->getRelatedCareersCount('tcm_logical_reasoning', $lastCareerId);
        return view('teenager.relatedCareers', compact('relatedCareers', 'relatedCareersCount'));
    }
}
