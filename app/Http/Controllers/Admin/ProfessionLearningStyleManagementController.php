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
use App\Http\Controllers\Controller;
use App\Teenagers;
use App\ProfessionLearningStyle;
use App\GamificationTemplate;
use App\Professions;
use App\LearningStyle;
use Cache;

class ProfessionLearningStyleManagementController extends Controller {

    public function __construct() {
        $this->objTeenagers = new Teenagers();
        $this->objLearningStyle = new ProfessionLearningStyle();
        $this->loggedInUser = Auth::guard('admin');
    }

    public function index() {
        $learningstyledetail = $this->objLearningStyle->getAllProfessionLearningStyle();
        return view('admin.ListProfessionLearningStyle', compact('learningstyledetail'));
    }

    public function add() {
        $learningstyleDetail = [];
        return view('admin.EditProfessionLearningStyle', compact('learningstyleDetail'));
    }

    public function edit($id) {
        $learningstyleDetail = $this->objLearningStyle->getLearningStyleDetailsById($id);
        return view('admin.EditProfessionLearningStyle', compact('learningstyleDetail'));
    }

    public function save() {
        $perameterId = Input::get('pls_parameter_id');
        $activityName = Input::get('activity_name');
        
        $postData['pageRank'] = e(Input::get('pageRank'));
        $size = count($perameterId);
        $learningStyleData = array();
        $learningStyleData['pls_profession_id'] = e(Input::get('id'));
     
        for ($i = 0; $i < $size; $i++) 
        {            
            $activityResult = array();
            $learningStyleData['pls_parameter_id'] = $perameterId[$i];
                                     
            if(strpos($activityName[$i], '##') !== false) 
            {                 
                $Activities = explode("##",$activityName[$i]);   
                $actiArray = array('L4B'=>'L4B','L4AP'=>'L4AP','L4AD'=>'L4AD','L4AV'=>'L4AV','N/A'=>'N/A');
                  
                foreach ($Activities as $Akey => $acty) {                    
                    if ($acty != '') { 
                       if(!in_array($acty, $actiArray))
                       {
                            $objGamificationTemplate = new GamificationTemplate();
                            $activityId = $objGamificationTemplate->getActivityIdByName($acty,$learningStyleData['pls_profession_id']); 
                            if (isset($activityId->id) && intval($activityId->id) > 0) {
                                $activityResult[] = $activityId->id;
                            } else {
                                $activityResult[] = 'N/A';
                            }
                       }else{
                           $activityResult[] = $acty;
                       } 
                        
                    } else {
                        $activityResult[] = 'N/A';
                    }
                }   
             
                $ActivityName = implode(',',$activityResult);
                
                $learningStyleData['pls_activity_name'] = $ActivityName;
             
                if(!empty($learningStyleData)) {
                    $objProfessionLS = new ProfessionLearningStyle();
                    $response = $objProfessionLS->saveProfessionLearningStyle($learningStyleData);
                }
            }
            else 
            {                
                $actiArray = array('L4B'=>'L4B','L4AP'=>'L4AP','L4AD'=>'L4AD','L4AV'=>'L4AV','N/A'=>'N/A');             
                if ($activityName[$i] != '' && !in_array($activityName[$i], $actiArray)) 
                {           
                    $objGamificationTemplate = new GamificationTemplate();
                     $ActivityResult = $objGamificationTemplate->getActivityIdByName($activityName[$i],$learningStyleData['pls_profession_id']);    
                     if(isset($ActivityResult->id) && intval($ActivityResult->id) > 0){
                         $learningStyleData['pls_activity_name'] = $ActivityResult->id;
                     }else{
                         $learningStyleData['pls_activity_name'] = 'N/A';
                     }
                }else{
                     $learningStyleData['pls_activity_name'] = $activityName[$i];   
                }
                
//                if (isset($ActivityResult['id']) && $ActivityResult['id'] != '' && $ActivityResult['id'] > 0) {
//                    $learningStyleData['pls_activity_name'] = $ActivityResult['id'];
//                } else {
//                    if (trim($activityName[$i]) != '') {
//                        $learningStyleData['pls_activity_name'] = $activityName[$i];
//                    } else {
//                        $learningStyleData['pls_activity_name'] = 'N/A';
//                    }
//                }
                
                if(!empty($learningStyleData)) {
                    $objProfessionLS = new ProfessionLearningStyle();
                    $response = $objProfessionLS->saveProfessionLearningStyle($learningStyleData);
                }
            }
        }
        //Cache::forget('learningstyledetail');
        return Redirect::to("admin/professionLearningStyle".$postData['pageRank'])->with('success', trans('labels.professionleaningstyleaddsuccess'));
    }
    public function importExcel() {
        return view('admin.AddLeaningStyleBulk');
    }

    public function addimportExcel() {
        $filename = Input::file('importfile');
        Excel::load($filename, function($reader) {

            // Getting all results
            $results = $reader->get();

            $results = $reader->all();
            $results = $results->toArray();
            $mainArray = array();
            $objProfession = new Professions();
            $objLearningStyle = new LearningStyle();
            foreach ($results AS $key1 => $learningDetail) {
                    $professionName = $learningDetail['profession_name'];
                    $professionId = $objProfession->getProfessionIdByName($professionName);
                foreach ($learningDetail As $key => $value) {
                    $learningStyleData = [];
                    $ActivityResult = array();
                    if ($key != 'profession_name') {
                        $result = $objLearningStyle->getLearningStyleId($key);
                        $objGamificationTemplate = new GamificationTemplate();
                        if (strpos($value, '##') !== false) {
                            $actiArray = array('L4B'=>'L4B','L4AP'=>'L4AP','L4AD'=>'L4AD','L4AV'=>'L4AV','N/A'=>'N/A');
                            $ActivityResult = [];
                            $Activities = explode("##",$value);
                            foreach ($Activities As $Akey => $acty) {
                                if ($acty != '') {
                                    if(!in_array($acty, $actiArray))
                                    {
                                        $activityId = $objGamificationTemplate->getActivityIdByName($acty,$professionId['id']);
                                        if (isset($activityId->id) && intval($activityId->id) > 0) {
                                            $ActivityResult[] = $activityId->id;
                                        } else {
                                            $ActivityResult[] = 'N/A';
                                        }
                                    }else{
                                        $ActivityResult[] = $acty;
                                    }                                                                         
                                } else {
                                    $ActivityResult[] = 'N/A';
                                }
                            }
                            $ActivityName = implode(',',$ActivityResult);
                            $learningStyleData['pls_activity_name'] = $ActivityName;
                            $learningStyleData['pls_profession_id'] = $professionId['id'];
                            $learningStyleData['pls_parameter_id'] = $result['id'];
                            if(!empty($learningStyleData)) {
                                $objProfessionLS = new ProfessionLearningStyle();
                                $response = $objProfessionLS->saveProfessionLearningStyle($learningStyleData);
                            }
                        } else {
                            $actiArray = array('L4B'=>'L4B','L4AP'=>'L4AP','L4AD'=>'L4AD','L4AV'=>'L4AV','N/A'=>'N/A'); 
                            if ($value != '' && !in_array($value, $actiArray)) 
                            {
                                $ActivityResult = $objGamificationTemplate->getActivityIdByName($value,$professionId['id']);
                                if(isset($ActivityResult->id) && intval($ActivityResult->id) > 0){
                                    $learningStyleData['pls_activity_name'] = $ActivityResult->id;
                                }else{
                                    $learningStyleData['pls_activity_name'] = 'N/A';
                                }
                            }
                            else
                            {
                                $learningStyleData['pls_activity_name'] = $value;   
                            }
                            
//                            if ($ActivityResult['id'] != '') {
//                                $learningStyleData['pls_activity_name'] = $ActivityResult['id'];
//                            } else {
//                                if (trim($value) != '') {
//                                    $learningStyleData['pls_activity_name'] = $value;
//                                } else {
//                                    $learningStyleData['pls_activity_name'] = 'N/A';
//                                }
//                            }
                            
                            $learningStyleData['pls_profession_id'] = $professionId['id'];
                            $learningStyleData['pls_parameter_id'] = $result['id'];

                            if(!empty($learningStyleData)) {
                                $objProfessionLS = new ProfessionLearningStyle();
                                $response = $objProfessionLS->saveProfessionLearningStyle($learningStyleData);
                            }
                        }
                    }
                }
            }
        });
        return Redirect::to("admin/professionLearningStyle")->with('success', trans('labels.leaningstyleaddsuccess'));
    }
}