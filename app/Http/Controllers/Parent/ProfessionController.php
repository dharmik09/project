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
use App\Services\Level4Activity\Contracts\Level4ActivitiesRepository;
use App\TemplateDeductedCoins;
use App\Services\Teenagers\Contracts\TeenagersRepository;
use Input;
use App\Services\Parents\Contracts\ParentsRepository;
use App\PaidComponent;
use App\DeductedCoins;

class ProfessionController extends Controller {

    public function __construct(ProfessionsRepository $professionsRepository, Level4ActivitiesRepository $level4ActivitiesRepository, TeenagersRepository $teenagersRepository, ParentsRepository $parentsRepository) 
    {
        $this->professionsRepository = $professionsRepository;
        $this->professions = new Professions;
        $this->objApptitude = new Apptitude;
        $this->level4ActivitiesRepository = $level4ActivitiesRepository;
        $this->teenagersRepository = $teenagersRepository;
        $this->aptitudeThumb = Config::get('constant.APPTITUDE_THUMB_IMAGE_UPLOAD_PATH');
        $this->parentsRepository = $parentsRepository;
        $this->objPaidComponent = new PaidComponent;
        $this->objDeductedCoins = new DeductedCoins;
        $this->log = new Logger('parent-profession-controller');
        $this->log->pushHandler(new StreamHandler(storage_path().'/logs/monolog-'.date('m-d-Y').'.log'));
    }

    /*
     * Returns career details page
     */
    public function careerDetails($slug, $teenId)
    {
        $user = Auth::guard('parent')->user();
        //1=India, 2=US
        $countryId = ($user->p_country == 1) ? 1 : 2;

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

        //Intermediate template details
        $getQuestionTemplateForProfession = $this->level4ActivitiesRepository->getQuestionTemplateForProfession($professionsData->id);

        $objTemplateDeductedCoins = new TemplateDeductedCoins();

        if (!empty($getQuestionTemplateForProfession)) {
            
            foreach ($getQuestionTemplateForProfession As $key => $value) {
                $deductedCoinsDetail = $objTemplateDeductedCoins->getDeductedCoinsDetailById($user->id,$professionsData->id,$value->gt_template_id,2);
                $days = 0;

                if (!empty($deductedCoinsDetail->toArray())) {
                    $days = Helpers::calculateRemainingDays($deductedCoinsDetail[0]->tdc_end_date);
                }
                $getQuestionTemplateForProfession[$key]->remaningDays = $days;
                $intermediateActivities = [];
                $intermediateActivities = $this->level4ActivitiesRepository->getNotAttemptedIntermediateActivitiesForParent($user->id, $professionsData->id, $value->gt_template_id);
                $totalIntermediateQuestion = $this->level4ActivitiesRepository->getNoOfTotalIntermediateQuestionsAttemptedQuestionForParent($user->id, $professionsData->id, $value->gt_template_id);
                $response['NoOfTotalQuestions'] = $totalIntermediateQuestion[0]->NoOfTotalQuestions;
                $response['NoOfAttemptedQuestions'] = $totalIntermediateQuestion[0]->NoOfAttemptedQuestions;
                if (empty($intermediateActivities) || ($response['NoOfTotalQuestions'] == $response['NoOfAttemptedQuestions']) || ($response['NoOfTotalQuestions'] < $response['NoOfAttemptedQuestions'])) {
                   $getQuestionTemplateForProfession[$key]->attempted = 'yes';
                } else {
                    $getQuestionTemplateForProfession[$key]->attempted = 'no';
                }
            }
        }

        if (isset($getQuestionTemplateForProfession)) {
            $response['questionTemplate'] = $getQuestionTemplateForProfession;
        } else {
            $response['questionTemplate'] = [];
        }

        //Promise plus coins consumption details
        $promisePlusComponent = $this->objPaidComponent->getPaidComponentsData(Config::get('constant.PROMISE_PLUS'));
        $promisePluseDeductedCoinsDetail = $this->objDeductedCoins->getDeductedCoinsDetailById($user->id, $promisePlusComponent->id, 2, $professionsData->id);
        $promisePlusRemainingDays = 0;
        if (count($promisePluseDeductedCoinsDetail) > 0) {
            $promisePlusRemainingDays = Helpers::calculateRemainingDays($promisePluseDeductedCoinsDetail[0]->dc_end_date);
        }

        //Profile complete calculations
        $professionCompletePercentage = Helpers::getProfessionCompletePercentageForParent($user->id, $professionsData->id);
        return view('parent.careerDetail', compact('professionsData', 'countryId', 'teenId', 'getQuestionTemplateForProfession', 'promisePlusComponent', 'promisePlusRemainingDays', 'professionCompletePercentage'));
    }

    /*
     * Returns Teenagers list whose challeged to parent or mentor
     */
    public function getTeenagersChallengedToParent() { 
        $teenUniqueId = Input::get('teenId');
        $professionId = Input::get('professionId');
        $parentId = Auth::guard('parent')->user()->id;
        if ($parentId > 0 && $professionId != '' && $teenUniqueId != '') {
            $teenDetails = $this->teenagersRepository->getTeenagerByUniqueId($teenUniqueId);
            $getCompetingUserList = [];
            if (isset($teenDetails) && !empty($teenDetails)) {
                $level4Booster = Helpers::level4Booster($professionId, $teenDetails->id);
                if (isset($level4Booster) && !empty($level4Booster)) {
                    $getCompetingUserList = Helpers::getCompetingUserListForParent($professionId, $parentId);
                    $response['data'][] = $getCompetingUserList;
                    return view('parent.basic.careerChallengePlaySection', compact('getCompetingUserList'));
                    exit;
                } else {
                    $response['status'] = 0;
                    $response['message'] = "No Records Found";
                    return response()->json($response, 200);
                    exit;
                }
            } else {
                $response['status'] = 0;
                $response['message'] = "No Records Found";
                return response()->json($response, 200);
                exit;
            }
        } else {
            $response['status'] = 0;
            $response['message'] = "Something went wrong";
            return response()->json($response, 200);
            exit;
        }
    }

    /*
     * Returns view of teen-challenge score box
     */
    public function showCompetitorData() {
        $parentId = Auth::guard('parent')->user()->id;
        $professionId = Input::get('professionId');
        $teenId = Input::get('teenId');
        if ($parentId > 0 && $professionId != '' && $teenId != '') {
            $professionName = '';
            $getProfessionNameFromProfessionId = $this->professionsRepository->getProfessionsByProfessionId($professionId);
            if (isset($getProfessionNameFromProfessionId[0]) && !empty($getProfessionNameFromProfessionId[0])) {
                $professionName = $getProfessionNameFromProfessionId[0]->pf_name;
            }
            $level4Booster = Helpers::level4Booster($professionId, $teenId);
            $level4ParentBooster = Helpers::level4ParentBooster($professionId, $parentId);
            $teenDetail = $this->teenagersRepository->getTeenagerByTeenagerId($teenId);
            $parentDetail = $this->parentsRepository->getParentDetailByParentId($parentId);
            $rank = 0;
            foreach($level4ParentBooster['allData'] AS $key => $value) {
                if ($level4Booster['yourScore'] != 0) {
                  if ($level4Booster['yourScore'] == $value) {
                    $rank = $key+1;
                  }
                } else{
                    $rank = 0;
                }
            }
            return view('parent.basic.careerChallengeScoreBox', compact('level4Booster', 'level4ParentBooster', 'professionName', 'teenDetail', 'parentDetail', 'rank'));
            exit;
        } else {
            $response['status'] = 0;
            $response['message'] = "Something went wrong!";
            $response['reload'] = 1;
            $response['redirect'] = '/parent';
            return response()->json($response, 200);
            exit;
        }
    }

    /*
     * Returns leaderboard details.
     */
    public function getLeaderBoardDetails()
    {
        $slot = Input::get('slot');
        $professionId = Input::get('professionId');
        $leaderboardTeenagers = $this->teenagersRepository->getTeenagerListingWithBoosterPointsByProfession($professionId, $slot);
        $nextleaderboardTeenagers = $this->teenagersRepository->getTeenagerListingWithBoosterPointsByProfession($professionId, $slot + 1);
        return view('parent.basic.careerDetailsLeaderBoard', compact('leaderboardTeenagers', 'nextleaderboardTeenagers'));
    }

    /*
     * Returns profession completion percentage.
     */
    public function getProfessionCompletionPercentage()
    {
        $professionId = Input::get('professionId');
        $response['status'] = 0;
        $response['message'] = 'Something went wrong!';
        $response['percentage'] = 0;
        if ($professionId != '') {
            $completionPercentage = Helpers::getProfessionCompletePercentageForParent(Auth::guard('parent')->user()->id, $professionId);
            $professionComplete = (isset($completionPercentage) && !empty($completionPercentage)) ? $completionPercentage : 0;
            $response['status'] = 1;
            $response['message'] = 'success';
            $response['percentage'] = $professionComplete;
        }
        return response()->json($response, 200);
        exit;
    }

}