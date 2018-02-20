<?php

namespace App\Http\Controllers\Teenager;

use App\Http\Controllers\Controller;
use Auth;
use Config;
use Storage;
use Input;
use Mail;
use Helpers;
use App\Services\Teenagers\Contracts\TeenagersRepository;
use Redirect;
use Request;
use App\Teenagers;
use App\ProfessionLearningStyle;
use App\UserLearningStyle;
use Carbon\Carbon;  
use App\TeenagerBoosterPoint;
use App\Services\Professions\Contracts\ProfessionsRepository;
use App\Services\Level4Activity\Contracts\Level4ActivitiesRepository;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class Level4AdvanceActivityController extends Controller {

    public function __construct(Level4ActivitiesRepository $level4ActivitiesRepository, TeenagersRepository $teenagersRepository, ProfessionsRepository $professionsRepository) 
    {        
        $this->professionsRepository = $professionsRepository;
        $this->teenagersRepository = $teenagersRepository;   
        $this->level4ActivitiesRepository = $level4ActivitiesRepository;   
        $this->teenagerBoosterPoint = new TeenagerBoosterPoint();
        $this->extraQuestionDescriptionTime = Config::get('constant.EXTRA_QUESTION_DESCRIPTION');
        $this->questionDescriptionORIGINALImage = Config::get('constant.LEVEL4_INTERMEDIATE_QUESTION_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->questionDescriptionTHUMBImage = Config::get('constant.LEVEL4_INTERMEDIATE_QUESTION_THUMB_IMAGE_UPLOAD_PATH');
        $this->optionORIGINALImage = Config::get('constant.LEVEL4_INTERMEDIATE_ANSWER_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->optionTHUMBImage = Config::get('constant.LEVEL4_INTERMEDIATE_ANSWER_THUMB_IMAGE_UPLOAD_PATH');
        $this->answerResponseImageOriginal = Config::get('constant.LEVEL4_INTERMEDIATE_RESPONSE_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->level4MinimumPointsRequirements = Config::get('constant.LEVEL4_MINIMUM_POINTS_REQUIREMENTS');
        $this->level4AdvanceThumbImageUploadPath = Config::get('constant.LEVEL4_ADVANCE_THUMB_IMAGE_UPLOAD_PATH');
        $this->level4AdvanceOriginalImageUploadPath = Config::get('constant.LEVEL4_ADVANCE_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->log = new Logger('teenager-level4-advance-activity-controller');
        $this->log->pushHandler(new StreamHandler(storage_path().'/logs/monolog-'.date('m-d-Y').'.log'));
    }

    /*
     * Get L4 advance level question data
     */
    public function getQuestionDataAdvanceLevel()
    {
        $type = Input::get('activityType');
        $professionId = Input::get('professionId');
        $professionDetail = $this->professionsRepository->getProfessionsDataFromId($professionId);
        $activityData = $this->level4ActivitiesRepository->getLevel4AdvanceActivityByType($type);
        //Store log in System
        $this->log->info('Retrieve L4 advance activity data', array('userId' => Auth::guard('teenager')->user()->id));
        return view('teenager.basic.careerAdvanceQuizData', compact('activityData', 'professionDetail', 'type'));
    }

    /*
     * Returns L4 upload media section view
     */
    public function getMediaUploadSection()
    {
        return view('teenager.basic.careerAdvanceQuizSection');
    }

    /*
     * 
     */
    public function getLevel4AdvanceStep2Details() {
        $userId = Auth::guard('teenager')->user()->id;
        $professionId = Input::get('professionId');
        $typeId = Input::get('type');
        $total = $this->teenagersRepository->getTeenagerTotalBoosterPoints($userId);
        if ($total > $this->level4MinimumPointsRequirements) {
            $getTeenagerAttemptedProfession = $this->professionsRepository->getTeenagerAttemptedProfession($userId);
            if (isset($getTeenagerAttemptedProfession) && !empty($getTeenagerAttemptedProfession)) {
                foreach ($getTeenagerAttemptedProfession as $keyId) {
                    $professionList[] = $keyId->id;
                }
            } else {
                $professionList = array();
            }
            if (in_array($professionId, $professionList)) {
                $professionId = intval($professionId);
                $totalBasicQuestion = $this->level4ActivitiesRepository->getNoOfTotalQuestionsAttemptedQuestion($userId, $professionId);
                //$totalBasicQuestion[0]->NoOfAttemptedQuestions = 6;
                if ($totalBasicQuestion[0]->NoOfTotalQuestions == 0) {
                    $response['status'] = 0;
                    $response['message'] = "Profession Doesn't have any basic questions"; 
                    return response()->json($response, 200);
                    exit;
                } else if ($totalBasicQuestion[0]->NoOfTotalQuestions > $totalBasicQuestion[0]->NoOfAttemptedQuestions) {
                    $response['status'] = 0;
                    $response['message'] = "Play Basic to get to play Intermediate."; 
                    return response()->json($response, 200);
                    exit;
                } else {
                    $validTypeArr = array(Config::get('constant.ADVANCE_IMAGE_TYPE'), Config::get('constant.ADVANCE_DOCUMENT_TYPE'), Config::get('constant.ADVANCE_VIDEO_TYPE'));
                    $typeId = intval($typeId);
                    if (in_array($typeId, $validTypeArr)) {
                        $typeId = $typeId;
                    } else {
                        $typeId = Config::get('constant.ADVANCE_IMAGE_TYPE');
                    }
                    //Get User activity
                    $professionDetail = $this->professionsRepository->getProfessionsDataFromId($professionId);
                    $level4AdvanceThumbImageUploadPath = $this->level4AdvanceThumbImageUploadPath;
                    $level4AdvanceOriginalImageUploadPath = $this->level4AdvanceOriginalImageUploadPath;
                    $userLevel4AdvanceImageTask = $this->level4ActivitiesRepository->getLevel4AdvanceActivityTaskByUser(Auth::guard('teenager')->user()->id, $professionId, Config::get('constant.ADVANCE_IMAGE_TYPE'));
                    $userLevel4AdvanceDocumentTask = $this->level4ActivitiesRepository->getLevel4AdvanceActivityTaskByUser(Auth::guard('teenager')->user()->id, $professionId, Config::get('constant.ADVANCE_DOCUMENT_TYPE'));
                    $userLevel4AdvanceVideoTask = $this->level4ActivitiesRepository->getLevel4AdvanceActivityTaskByUser(Auth::guard('teenager')->user()->id, $professionId, Config::get('constant.ADVANCE_VIDEO_TYPE'));
                    //$level4Booster = Helpers::level4Booster($professionId, $userid);
                    $level4Booster['total'] = $total;
                    $response['level4Booster'] = $level4Booster;
                    $response['boosterPoints'] = '';
                    $response['status'] = 1;
                    $response['message'] = trans('appmessages.default_success_msg');
                    return view('teenager.basic.careerAdvanceQuizStep2Activity', compact('response', 'typeId', 'professionId', 'professionDetail', 'userLevel4AdvanceImageTask', 'level4AdvanceThumbImageUploadPath', 'level4AdvanceOriginalImageUploadPath', 'userLevel4AdvanceDocumentTask', 'userLevel4AdvanceVideoTask'));
                    exit;
                }
            } else {
                $response['status'] = 0;
                $response['message'] = "Please, attempt profession first"; 
                return response()->json($response, 200);
                exit;
            }
        } else {
            $response['status'] = 0;
            $response['message'] = trans('appmessages.not_sufficient_booster_points_msg');
            return response()->json($response, 200);
            exit;
        }
    }


}