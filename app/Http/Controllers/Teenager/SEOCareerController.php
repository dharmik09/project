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
}