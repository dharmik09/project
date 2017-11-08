<?php

namespace App\Http\Controllers\Admin;

use App\Sponsors;
use App\Http\Controllers\Controller;
use Auth;
use Config;
use Helpers;
use Illuminate\Http\Request;
use Input;
use App\Services\Level1Activity\Contracts\Level1ActivitiesRepository;
use App\Services\Level2Activity\Contracts\Level2ActivitiesRepository;
use App\Services\Professions\Contracts\ProfessionsRepository;

class AjaxController extends Controller
{
    public function __construct(Level1ActivitiesRepository $level1ActivitiesRepository, Level2ActivitiesRepository $level2ActivitiesRepository, ProfessionsRepository $professionsRepository)
    {
        $this->loggedInUser = Auth::guard('admin');
        $this->controller   = 'AjaxController';
        $this->level1ActivitiesRepository = $level1ActivitiesRepository;
        $this->level2ActivitiesRepository = $level2ActivitiesRepository;
        $this->professionsRepository = $professionsRepository;
    }

    public function getSponsor(Request $request)
    {
        $fieldName   = $request->input('field_name');
        $objSponsor = new Sponsors();
        $sponsor    = $objSponsor->getActiveSponsors();

        $string = '';
        $string .='<label for="sponsor_choice" class="col-sm-2 control-label">'.trans('labels.formlblselectsponsor').'</label>
                        <div class="col-sm-6">';
        $string .='<select class="form-control" id="' . $fieldName . '" name="' . $fieldName . '" multiple>
                        <option value="">'.trans('labels.formlblselectsponsor').'</option>';
        if (isset($sponsor) && !empty($sponsor)) {
            foreach ($sponsor as $k=>$v) {
                $string .='<option value="' . $v->id . '">' . $v->sp_company_name . '</option>';
            }
        }
        $string .='</select>';
        echo $string;
        exit;
    }

    public function generateTeenagerUniqueId()
    {
        $uniqueId = Helpers::getTeenagerUniqueId();
        echo $uniqueId;
        exit;
    }
    
    public function getLevelActivity()
    {
        $selectedLevel = Input::get('field_name');
        $data_id = Input::get('data_id');
        if($selectedLevel == 'Level1')
        {
            $level1Question = $this->level1ActivitiesRepository->getLevel1AllActiveQuestion();
            return view('admin.ajaxLevel1Question',compact('level1Question','data_id'));
            exit;
        }
        elseif($selectedLevel == 'Level2')
        {
            $level2Question = $this->level2ActivitiesRepository->getLevel2AllActiveQuestion();
            return view('admin.ajaxLevel2Question',compact('level2Question','data_id'));
            exit;
        }
        elseif($selectedLevel == 'profession-detail')
        {
            $allActiveProfessions = $this->professionsRepository->getAllActiveProfession();
            return view('admin.ajaxAllProfession',compact('allActiveProfessions','data_id'));
            exit;
        }        
    }
}
