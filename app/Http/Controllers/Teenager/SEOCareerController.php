<?php

namespace App\Http\Controllers\Teenager;

use App\Http\Controllers\Controller;
use App\Services\Professions\Contracts\ProfessionsRepository;
use App\Services\Baskets\Contracts\BasketsRepository;
use App\Baskets;
use App\Professions;
use App\ProfessionHeaders;
use App\MultipleIntelligent;
use App\Apptitude;
use App\Personality;
use App\Teenagers;
use Auth;
use Config;
use Storage;
use Input;
use Mail;
use Helpers;
use Redirect;
use Request; 
use PDF;  
use App\StarRatedProfession; 
use App\TeenagerPromiseScore;
use App\PromiseParametersMaxScore;
use App\Services\Teenagers\Contracts\TeenagersRepository;
use App\Services\Level4Activity\Contracts\Level4ActivitiesRepository;
use App\SponsorsActivity;
use App\TeenagerScholarshipProgram;

class SEOCareerController extends Controller {

    public function __construct(Level4ActivitiesRepository $level4ActivitiesRepository, ProfessionsRepository $professionsRepository, BasketsRepository $basketsRepository, TeenagersRepository $teenagersRepository) 
    {
        $this->professionsRepository = $professionsRepository;
        $this->level4ActivitiesRepository = $level4ActivitiesRepository;
        $this->baskets = new Baskets();
        $this->professions = new Professions();
        $this->professionHeaders = new ProfessionHeaders();
        $this->objStarRatedProfession = new StarRatedProfession;
        $this->objMultipleIntelligent = new MultipleIntelligent;
        $this->objApptitude = new Apptitude;
        $this->objPersonality = new Personality;
        $this->objTeenagers = new Teenagers;
        $this->aptitudeThumb = Config::get('constant.APPTITUDE_THUMB_IMAGE_UPLOAD_PATH');
        $this->miThumb = Config::get('constant.MI_THUMB_IMAGE_UPLOAD_PATH');
        $this->personalityThumb = Config::get('constant.PERSONALITY_THUMB_IMAGE_UPLOAD_PATH');
        $this->objTeenagerPromiseScore = new TeenagerPromiseScore();
        $this->careerDetailsPdfUploadedPath = Config::get('constant.CAREER_DETAILS_PDF_UPLOAD_PATH');
        $this->objPromiseParametersMaxScore = new PromiseParametersMaxScore();
        $this->teenagersRepository = $teenagersRepository;
        $this->objSponsorsActivity = new SponsorsActivity;
        $this->objTeenagerScholarshipProgram = new TeenagerScholarshipProgram;
    }

    public function careers(){
        $professionRandom = $this->professions->getRandomProfession();
        
        //get all profession data for search
        $allProfessions = $this->professions->getActiveProfessions();  
        
        return view('teenager.seoTeaserSearch',compact('allProfessions'));
        //return redirect('career-detail/'.$professionRandom->pf_slug);       
    }

    public function careerDetails($slug)
    {   
        $countryId = 1;
        
        //get all profession data for search
        $allProfessions = $this->professions->getActiveProfessions();              
        
        //get profession data by slug
        $professionsData = $this->professions->getProfessionDetailBySlug($slug, $countryId);       
        $professionsData = ($professionsData) ? $professionsData : [];
        if(!$professionsData) {
            return Redirect::to("teenager/list-career")->withErrors("Invalid professions data");
        }
        
        return view('teenager.seoTeaser',compact('professionsData','countryId','allProfessions','slug'));
    }

    /* @getStrengthDetails
     * @params : $type, $slug
     * @response : Returns view for SEO Multi-intelligence page
     */
    public function getStrengthDetails($type, $slug)
    {
        if (!empty($type) || !empty($slug)) {
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
                        $miThumbImageUploadPath = $this->miThumb;
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
                        $miThumbImageUploadPath = $this->aptitudeThumb;
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
                        $miThumbImageUploadPath = $this->personalityThumb;
                    }else{
                        return Redirect::to("teenager/home")->withErrors("Invalid data passed to URL");
                    }
                    break;

                default:
                    return redirect::back()->with('error', trans('labels.commonerrormessage'));
            };
            return view('teenager.seoMi', compact('multipleIntelligence', 'miThumbImageUploadPath'));
        } else {
            return redirect::back()->with('error', trans('labels.commonerrormessage'));
        }
    }
}