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
use App\SponsorsActivity;
use App\TeenagerScholarshipProgram;

class Level4ActivityController extends Controller {

    public function __construct(TeenagersRepository $teenagersRepository, ProfessionsRepository $professionsRepository) 
    {
        $this->teenagersRepository = $teenagersRepository;
        $this->professionsRepository = $professionsRepository;
        $this->objSponsorsActivity = new SponsorsActivity; 
        $this->objTeenagerScholarshipProgram = new TeenagerScholarshipProgram;
        $this->log = new Logger('api-level4-activity-controller');
        $this->log->pushHandler(new StreamHandler(storage_path().'/logs/monolog-'.date('m-d-Y').'.log'));    
    }

    /* Request Params : getScholarshipProgramsDetails
     *  loginToken, userId
     */
    public function getScholarshipProgramsDetails(Request $request) 
    {
        $response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg') ] ;
        $teenager = $this->teenagersRepository->getTeenagerById($request->userId);
        if ($teenager) {
            //Get tenager sponsor's list
            $teenagerSponsors = $this->teenagersRepository->getTeenagerSelectedSponsor($request->userId);
            $sponsorArr = [];
            if (isset($teenagerSponsors) && count($teenagerSponsors) > 0) {
                foreach ($teenagerSponsors as $key => $val) {
                    $sponsorArr[] = $val->ts_sponsor;
                }
            }
            //Get scholarship programs list
            $scholarshipPrograms = [];
            if (!empty($sponsorArr)) {
                if (!empty($sponsorArr)) {
                    $scholarshipPrograms = $this->objSponsorsActivity->getActivityByTypeAndSponsor($sponsorArr, 3);
                }
            }
            //Get list for applied scholarship programs
            $appliedScholarshipDetails = $this->objTeenagerScholarshipProgram->getAllScholarshipProgramsByTeenId($request->userId);
            $scholarshipProgramIds = [];
            if (isset($appliedScholarshipDetails) && count($appliedScholarshipDetails) > 0) {
                foreach ($appliedScholarshipDetails as $appliedScholarshipDetail) {
                    $scholarshipProgramIds[] = $appliedScholarshipDetail->tsp_activity_id;
                }
            }
            //Get list for expired scholarship programs
            $expiredScholarshipPrograms = $this->objSponsorsActivity->getExpiredActivityByTypeAndSponsor($sponsorArr, 3);
            $expiredActivityIds = [];
            if (isset($expiredScholarshipPrograms) && count($expiredScholarshipPrograms) > 0) {
                foreach ($expiredScholarshipPrograms as $expiredScholarshipProgram) {
                    $expiredActivityIds[] = $expiredScholarshipProgram->id;
                }
            }
            $exceptScholarshipIds = array_unique(array_merge($scholarshipProgramIds, $expiredActivityIds));
            $scholarshipDetailsArr = [];
            foreach ($scholarshipPrograms as $scholarshipProgram) {
                $scholarshipData = [];
                $scholarshipData['id'] = $scholarshipProgram->id;
                if ($scholarshipProgram->sa_image != "" && Storage::size(Config::get('constant.SA_ORIGINAL_IMAGE_UPLOAD_PATH') . $scholarshipProgram->sa_image) > 0) {
                    $scholarshipData['image'] = Storage::url(Config::get('constant.SA_THUMB_IMAGE_UPLOAD_PATH') . $scholarshipProgram->sa_image);
                } else {
                    $scholarshipData['image'] = Storage::url(Config::get('constant.SA_THUMB_IMAGE_UPLOAD_PATH') . 'proteen-logo.png');
                }
                $scholarshipData['companyName'] = $scholarshipProgram->sp_company_name;
                $scholarshipData['title'] = $scholarshipProgram->sa_name;
                $scholarshipData['details'] = $scholarshipProgram->sa_description;
                $scholarshipData['learnMoreLink'] = url('teenager/learnMoreL4');
                $scholarshipData['is_applied'] = (!empty($exceptScholarshipIds) && in_array($scholarshipProgram->id, $exceptScholarshipIds)) ? 1 : 0;
                $scholarshipDetailsArr[] = $scholarshipData;
            }
            //Store log in System
            $this->log->info('Retrieve scholarship programs details', array('userid'=>$request->userId));
            $data['information'] = "Instructions: Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur congue velit vel nisi vulputate, eu faucibus eros porttitor. Nam nec placerat nunc. Suspendisse scelerisque luctus libero, ut tincidunt mi. Fusce quis tincidunt justo, at bibendum lorem.";
            $data['is_active'] = rand(0, 1);
            $data['scholarshipPrograms'] = $scholarshipDetailsArr;
            $response['login'] = 1;
            $response['status'] = 1;
            $response['message'] = trans('appmessages.default_success_msg');
            $response['data'] = $data;
        } else {
            $response['message'] = trans('appmessages.invalid_userid_msg') . ' or ' . trans('appmessages.notvarified_user_msg');
        }
        return response()->json($response, 200);
        exit;
    }

    /* Request Params : applyForScholarshipProgram
     *  loginToken, userId, activityId
     */
    public function applyForScholarshipProgram(Request $request) 
    {
        $response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg') ] ;
        $teenager = $this->teenagersRepository->getTeenagerById($request->userId);
        if ($teenager) {
            if (isset($request->activityId) && !empty($request->activityId)) {
                $activityDetails = [];
                $activityDetails['tsp_activity_id'] = $request->activityId;
                $activityDetails['tsp_teenager_id'] = $request->userId;
                //Check if user already applied for this scholarship program
                $checkIfAlreadyApplied = $this->objTeenagerScholarshipProgram->getScholarshipProgramDetailsByActivity($activityDetails);
                if (isset($checkIfAlreadyApplied) && !empty($checkIfAlreadyApplied)) {
                    $response['message'] = "You have already applied for this scholarship program";
                    //Store log in System
                    $this->log->info('User already registered for scholarship program.', array('userId'=>$request->userId, 'scholarshipId' => $request->activityId));
                } else {
                    //Store scholarship application details
                    $appliedForScholarship = $this->objTeenagerScholarshipProgram->StoreDetailsForScholarshipProgram($activityDetails);
                    if (isset($appliedForScholarship) && !empty($appliedForScholarship)) {
                        $response['message'] = "You have successfully applied for this scholarship program";
                        //Store log in System
                        $this->log->info('User successfully registered for scholarship program', array('userId' => $request->userId, 'scholarshipId' => $request->activityId));
                    } else {
                        $response['message'] = "Failed to applied for this scholarship program, Please try again later";
                        //Store log in System
                        $this->log->info('User failed to registered for scholarship program', array('userId' => $request->userId, 'scholarshipId' => $request->activityId));
                    } 
                }
                $response['status'] = 1;
            } else {
               $response['message'] = trans('appmessages.missing_data_msg'); 
               $response['status'] = 0;
            }
            $response['login'] = 1;
        } else {
            $response['message'] = trans('appmessages.invalid_userid_msg') . ' or ' . trans('appmessages.notvarified_user_msg');
        }
        return response()->json($response, 200);
        exit;
    }

}
