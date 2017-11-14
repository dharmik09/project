<?php

namespace App\Http\Controllers\Admin;

use Excel;
use App\Item;
use Session;
use Auth;
use File;
use Image;
use Input;
use Config;
use Request;
use Helpers;
use Redirect;
use Illuminate\Pagination\Paginator;
use App\Teenagers;
use App\Http\Controllers\Controller;
use App\Http\Requests\TeenagerRequest;
use App\Http\Requests\TeenagerBulkRequest;
use App\Http\Requests\CareerMappingRequest;
use App\Services\Teenagers\Contracts\TeenagersRepository;
use App\Services\Professions\Contracts\ProfessionsRepository;
use App\Services\CareerMapping\Contracts\CareerMappingRepository;
use Cache;

class CareerMappingManagementController extends Controller {

    public function __construct(TeenagersRepository $teenagersRepository, ProfessionsRepository $professionsRepository, CareerMappingRepository $careerMappingRepository) {
        $this->objTeenagers = new Teenagers();
        $this->teenagersRepository = $teenagersRepository;
        $this->professionsRepository = $professionsRepository;
        $this->careerMappingRepository = $careerMappingRepository;
        $this->teenOriginalImageUploadPath = Config::get('constant.TEEN_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->teenThumbImageUploadPath = Config::get('constant.TEEN_THUMB_IMAGE_UPLOAD_PATH');
        $this->teenThumbImageHeight = Config::get('constant.TEEN_THUMB_IMAGE_HEIGHT');
        $this->teenThumbImageWidth = Config::get('constant.TEEN_THUMB_IMAGE_WIDTH');
        $this->controller = 'TeenagerManagementController';
        $this->loggedInUser = Auth::guard('admin');
    }

    public function index() {
        $careerdetail = $this->teenagersRepository->getAllTeenagerCareerMap();
        Helpers::createAudit($this->loggedInUser->user()->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_READ'), $this->controller . "@index", $_SERVER['REQUEST_URI'], Config::get('constant.AUDIT_ORIGIN_WEB'), '', '', $_SERVER['REMOTE_ADDR']);

        return view('admin.ListCareerMapping', compact('careerdetail'));
    }

    public function importExcel() {
        return view('admin.AddCareerBulk');
    }

    public function addimportExcel() {
        $filename = Input::file('importfile');
        Excel::load($filename, function($reader) {

            // Getting all results
            $results = $reader->get();

            // ->all() is a wrapper for ->get() and will work the same
            $results = $reader->all();
            $results = $results->toArray();
            $mainArray = array();
            //echo "<pre>"; print_r($results); exit;
            foreach ($results as $result) {
                $tcm_profession = 0;
                $professionid = $this->professionsRepository->getProfessionIdByName($result['profession_name']);
                $mainArray['tcm_profession'] = $professionid;
                $mainArray['tcm_scientific_reasoning'] = $result['scientific_reasoning'];
                $mainArray['tcm_verbal_reasoning'] = $result['verbal_reasoning'];
                $mainArray['tcm_numerical_ability'] = $result['numericalability'];
                $mainArray['tcm_logical_reasoning'] = $result['logicalreasoning'];
                $mainArray['tcm_social_ability'] = $result['socialability'];
                $mainArray['tcm_artistic_ability'] = $result['artisticability'];
                $mainArray['tcm_spatial_ability'] = $result['spatialability'];
                $mainArray['tcm_creativity'] = $result['creativity'];
                $mainArray['tcm_clerical_ability'] = $result['clericalability'];
                $mainArray['tcm_doers_realistic'] = $result['doersrealistic'];
                $mainArray['tcm_thinkers_investigative'] = $result['thinkersinvestigative'];
                $mainArray['tcm_creators_artistic'] = $result['creatorsartistic'];
                $mainArray['tcm_helpers_social'] = $result['helperssocial'];
                $mainArray['tcm_persuaders_enterprising'] = $result['persuadersenterprising'];
                $mainArray['tcm_organizers_conventional'] = $result['organizersconventional'];
                $mainArray['tcm_linguistic'] = $result['linguistic'];
                $mainArray['tcm_logical'] = $result['logical'];
                $mainArray['tcm_musical'] = $result['musical'];
                $mainArray['tcm_spatial'] = $result['spatial'];
                $mainArray['tcm_bodily_kinesthetic'] = $result['bodilykinesthetic'];
                $mainArray['tcm_naturalist'] = $result['naturalist'];
                $mainArray['tcm_interpersonal'] = $result['interpersonal'];
                $mainArray['tcm_intrapersonal'] = $result['intrapersonal'];
                $mainArray['tcm_existential'] = $result['existential'];
                if ($professionid > 0) {
                    $checkprofessionid = $this->teenagersRepository->checkTeenCareerMappingProfessionId($professionid);
                    if (count($checkprofessionid) > 0) {
                        $this->teenagersRepository->UpdateTeenCareerMapping($mainArray, $professionid);
                    } else {
                        $mainArray['tcm_profession'] = $professionid;
                        $this->teenagersRepository->addTeenCareerMapping($mainArray);
                    }
                }
            }
        });
        return Redirect::to("admin/careerMapping")->with('success', trans('labels.teencareermapaddsuccess'));
    }

    public function add() {
        $cmDetails = [];
        return view('admin.EditCareerMapping', compact('cmDetails'));
    }

    public function save(CareerMappingRequest $careerMappingRequest) {
        $cmDetails = [];
        //$cmDetails['id'] = Input::get('id');
        $cmDetails['tcm_profession'] = e(Input::get('tcm_profession_id'));
        $cmDetails['tcm_scientific_reasoning'] = e(Input::get('tcm_scientific_reasoning'));
        $cmDetails['tcm_verbal_reasoning'] = e(Input::get('tcm_verbal_reasoning'));
        $cmDetails['tcm_numerical_ability'] = e(Input::get('tcm_numerical_ability'));
        $cmDetails['tcm_logical_reasoning'] = e(Input::get('tcm_logical_reasoning'));
        $cmDetails['tcm_social_ability'] = e(Input::get('tcm_social_ability'));
        $cmDetails['tcm_artistic_ability'] = e(Input::get('tcm_artistic_ability'));
        $cmDetails['tcm_spatial_ability'] = e(Input::get('tcm_spatial_ability'));
        $cmDetails['tcm_creativity'] = e(Input::get('tcm_creativity'));
        $cmDetails['tcm_clerical_ability'] = e(Input::get('tcm_clerical_ability'));
        $cmDetails['tcm_doers_realistic'] = e(Input::get('tcm_doers_realistic'));
        $cmDetails['tcm_thinkers_investigative'] = e(Input::get('tcm_thinkers_investigative'));
        $cmDetails['tcm_creators_artistic'] = e(Input::get('tcm_creators_artistic'));
        $cmDetails['tcm_helpers_social'] = e(Input::get('tcm_helpers_social'));
        $cmDetails['tcm_persuaders_enterprising'] = e(Input::get('tcm_persuaders_enterprising'));
        $cmDetails['tcm_organizers_conventional'] = e(Input::get('tcm_organizers_conventional'));
        $cmDetails['tcm_linguistic'] = e(Input::get('tcm_linguistic'));
        $cmDetails['tcm_logical'] = e(Input::get('tcm_logical'));
        $cmDetails['tcm_musical'] = e(Input::get('tcm_musical'));
        $cmDetails['tcm_spatial'] = e(Input::get('tcm_spatial'));
        $cmDetails['tcm_bodily_kinesthetic'] = e(Input::get('tcm_bodily_kinesthetic'));
        $cmDetails['tcm_naturalist'] = e(Input::get('tcm_naturalist'));
        $cmDetails['tcm_interpersonal'] = e(Input::get('tcm_interpersonal'));
        $cmDetails['tcm_intrapersonal'] = e(Input::get('tcm_intrapersonal'));
        $cmDetails['tcm_existential'] = e(Input::get('tcm_existential'));
        $cmDetails['deleted'] = 1;
        $postData['pageRank'] = Input::get('pageRank');
        $result = $this->careerMappingRepository->saveCareerMapping($cmDetails);
        Cache::forget('careerdetail');
        if ($result) {
            return Redirect::to("admin/careerMapping".$postData['pageRank'])->with('success', trans('labels.careearmappingaddsuccess'));
        } else {
            return Redirect::to("admin/careerMapping".$postData['pageRank'])->with('error', trans('labels.commonerrormessage'));
        }
    }
    
    public function edit($id){
        $cmDetails = $this->careerMappingRepository->getCareerMappingDetailsById($id);
        
        return view('admin.EditCareerMapping', compact('cmDetails'));
    }
    

}
