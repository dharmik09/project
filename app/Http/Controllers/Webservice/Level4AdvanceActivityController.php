<?php

namespace App\Http\Controllers\Webservice;

use App\Http\Controllers\Controller;
use App\Services\Teenagers\Contracts\TeenagersRepository;
use App\Services\Professions\Contracts\ProfessionsRepository;
use Config;
use Storage;
use Helpers;  
use Auth;
use Input;
use Redirect;
use Illuminate\Http\Request;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use App\Services\Template\Contracts\TemplatesRepository;
use App\Services\Level4Activity\Contracts\Level4ActivitiesRepository;
use Mail;

class Level4AdvanceActivityController extends Controller {

    public function __construct(TeenagersRepository $teenagersRepository, ProfessionsRepository $professionsRepository, TemplatesRepository $templatesRepository, Level4ActivitiesRepository $level4ActivitiesRepository) 
    {
        $this->teenagersRepository = $teenagersRepository;
        $this->professionsRepository = $professionsRepository;
        $this->templatesRepository = $templatesRepository;
        $this->level4ActivitiesRepository = $level4ActivitiesRepository;
        $this->log = new Logger('api-level4-advance-activity-controller');
        $this->log->pushHandler(new StreamHandler(storage_path().'/logs/monolog-'.date('m-d-Y').'.log'));    
        
    }

    /* Request Params : getL4AdvanceActivityMediaWiseDescription
     *  loginToken, userId, careerId, mediaType
     */
    public function getL4AdvanceActivityMediaWiseDescription(Request $request) 
    {
        $response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg') ] ;
        $teenager = $this->teenagersRepository->getTeenagerById($request->userId);
        if ($teenager) {
            if ($request->careerId != "" && $request->mediaType != "") {
                $data = [];
                $professionDetail = $this->professionsRepository->getProfessionsDataFromId($request->careerId);
                $activityData = $this->level4ActivitiesRepository->getLevel4AdvanceActivityByType($request->mediaType);
                if (strpos($activityData[0]->l4aa_description, '[PROFESSION_NAME]') !== false) {
                    $professionName = (isset($professionDetail) && !empty($professionDetail)) ? $professionDetail[0]->pf_name : '';
                    $descText = str_replace('[PROFESSION_NAME]', $professionName, $activityData[0]->l4aa_description);
                } else {
                    $descText = $activityData[0]->l4aa_description;
                }
                $data['careerId'] = $request->careerId;
                $data['title'] = $descText;
                $questionData = [];
                foreach($activityData as $key => $val) {
                    $questionArr = [];
                    $questionArr['id'] = $val->id;
                    if (strpos($val->l4aa_text, '[PROFESSION_NAME]') !== false) {
                        $professionName = (isset($professionDetail) && !empty($professionDetail)) ? $professionDetail[0]->pf_name : '';
                        $questionArr['mediaDescription'] = str_replace('[PROFESSION_NAME]', $professionName, $val->l4aa_text);
                    } else {
                        $questionArr['mediaDescription'] = $val->l4aa_text;
                    }
                    $questionArr['mediaType'] = $val->l4aa_type;
                    $questionData[] = $questionArr;
                }
                $data['details'] = $questionData;
                //Store log in System
                $this->log->info('Retrieve L4 advance activity data', array('userId' => $request->userId));
                $response['login'] = 1;
                $response['status'] = 1;
                $response['message'] = trans('appmessages.default_success_msg');
                $response['data'] = $data;
            } else {
                $response['login'] = 1;
                $response['status'] = 0;
                $response['message'] = trans('appmessages.missing_data_msg');
            }
        } else {
            $response['message'] = trans('appmessages.invalid_userid_msg') . ' or ' . trans('appmessages.notvarified_user_msg');
        }
        return response()->json($response, 200);
        exit;
    }

}
