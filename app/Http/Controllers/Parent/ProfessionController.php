<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Services\Professions\Contracts\ProfessionsRepository;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Auth;
use App\Professions;
use Helpers;
use App\Apptitude;
use Storage;
use Config;

class ProfessionController extends Controller {

    public function __construct(ProfessionsRepository $professionsRepository) 
    {
        $this->professionsRepository = $professionsRepository;
        $this->professions = new Professions;
        $this->objApptitude = new Apptitude;
        $this->aptitudeThumb = Config::get('constant.APPTITUDE_THUMB_IMAGE_UPLOAD_PATH');
        $this->log = new Logger('parent-profession-controller');
        $this->log->pushHandler(new StreamHandler(storage_path().'/logs/monolog-'.date('m-d-Y').'.log'));
    }

    /*
     * Returns career details page
     */
    public function careerDetails($slug)
    {
        $user = Auth::guard('parent')->user();
        //1=India, 2=US
        $countryId = ($user->p_country == 1) ? 2 : 1;

        //Profession Details with subjects, certifications and Tags Array
        $professionsData = $this->professions->getProfessionsAllDetails($slug, $countryId);
        $professionsData = ($professionsData) ? $professionsData : [];
        if(!$professionsData) {
            return Redirect::to("parent/my-challengers")->withErrors("Invalid professions data");
        }

        //Profession Ability Array
        $careerMapHelperArray = Helpers::getCareerMapColumnName();
        $careerMappingdata = [];
        
        foreach ($careerMapHelperArray as $key => $value) {
            $data = [];
            if(isset($professionsData->careerMapping[$value]) && $professionsData->careerMapping[$value] != 'L'){
                $arr = explode("_", $key);
                if($arr[0] == 'apt'){
                    $apptitudeData = $this->objApptitude->getApptitudeDetailBySlug($key);
                    $data['cm_name'] = $apptitudeData->apt_name;   
                    $data['cm_image_url'] = Storage::url($this->aptitudeThumb . $apptitudeData->apt_logo);
                    $data['cm_slug_url'] = url('/teenager/multi-intelligence/'.Config::get('constant.APPTITUDE_TYPE').'/'.$apptitudeData->apt_slug); 
                    $careerMappingdata[] = $data;  
                }
            }
        }
        unset($professionsData->careerMapping);
        $professionsData->ability = $careerMappingdata;

        return view('parent.careerDetail', compact('professionsData', 'countryId'));
    }


    
}