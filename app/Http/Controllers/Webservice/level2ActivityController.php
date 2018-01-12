<?php

namespace App\Http\Controllers\Webservice;

use App\Http\Controllers\Controller;
use Auth;
use Input;
use Redirect;
use Config;
use Illuminate\Http\Request;
use App\Transactions;
use App\Teenagers;
use App\Sponsors;
use App\Country;
use Helpers;
use App\Services\Teenagers\Contracts\TeenagersRepository;
use App\Services\Sponsors\Contracts\SponsorsRepository;
use App\Services\Level2Activity\Contracts\Level2ActivitiesRepository;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class level2ActivityController extends Controller {

    public function __construct(TeenagersRepository $teenagersRepository, Level2ActivitiesRepository $Level2ActivitiesRepository) {
        $this->teenagersRepository = $teenagersRepository;
        $this->Level2ActivitiesRepository = $Level2ActivitiesRepository;
        $this->level2TotalTime = Config::get('constant.LEVEL2_TOTAL_TIME');
        $this->log = new Logger('api-level1-activity-controller');
        $this->log->pushHandler(new StreamHandler(storage_path().'/logs/monolog-'.date('m-d-Y').'.log'));    
    }

    public function getLevel2Activity(Request $request) {
        $response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg')];
        $teenager = $this->teenagersRepository->getTeenagerById($request->userId);
        $this->log->info('Get teenager detail for userId'.$request->userId , array('api-name'=> 'getLevel2Activity'));
        if($request->userId != "" && $teenager) {
            $totalQuestion = $this->Level2ActivitiesRepository->getNoOfTotalQuestionsAttemptedQuestion($request->userId);

            $level2TotalTime = $this->level2TotalTime;

            if (isset($totalQuestion[0]->NoOfAttemptedQuestions) && $totalQuestion[0]->NoOfAttemptedQuestions > 0) {
                $getLastAttemptedQuestionData = $this->Level2ActivitiesRepository->getLastAttemptedQuestionData($request->userId);
                if (isset($getLastAttemptedQuestionData->l2ans_answer_timer)) {
                    if($getLastAttemptedQuestionData->l2ans_answer_timer >= $level2TotalTime){
                        $timer = $level2TotalTime - $getLastAttemptedQuestionData->l2ans_answer_timer;
                    } else {
                        $timer = $level2TotalTime - $getLastAttemptedQuestionData->l2ans_answer_timer;
                    }
                    
                } else {
                    $timer = $level2TotalTime;
                }
            } else {
                $timer = $level2TotalTime;
            }

            if($timer < 0){
                $data['timer'] = 0;
            } else{
                $data['timer'] = $timer;
            }

            $section1Collection = $this->Level2ActivitiesRepository->getNoOfTotalQuestionsAttemptedQuestionBySection($request->userId,Config::get('constant.LEVEL2_SECTION_1'));
            $section2Collection = $this->Level2ActivitiesRepository->getNoOfTotalQuestionsAttemptedQuestionBySection($request->userId,Config::get('constant.LEVEL2_SECTION_2'));
            $section3Collection = $this->Level2ActivitiesRepository->getNoOfTotalQuestionsAttemptedQuestionBySection($request->userId,Config::get('constant.LEVEL2_SECTION_3'));

            $section1Percentage = 0;
            $section2Percentage = 0;
            $section3Percentage = 0;
            
            if($section1Collection[0]->NoOfTotalQuestions != 0){
                $section1Percentage = ($section1Collection[0]->NoOfAttemptedQuestions*100)/$section1Collection[0]->NoOfTotalQuestions;
            }
            if($section2Collection[0]->NoOfTotalQuestions != 0){
                $section2Percentage = ($section2Collection[0]->NoOfAttemptedQuestions*100)/$section2Collection[0]->NoOfTotalQuestions;
            }
            if($section3Collection[0]->NoOfTotalQuestions != 0){
                $section3Percentage = ($section3Collection[0]->NoOfAttemptedQuestions*100)/$section3Collection[0]->NoOfTotalQuestions;
            }

            $data['section_1_Percentage'] = number_format((float)$section1Percentage, 0, '.', '');
            $data['section_2_Percentage'] = number_format((float)$section2Percentage, 0, '.', '');
            $data['section_3_Percentage'] = number_format((float)$section3Percentage, 0, '.', '');

            $data['section_1'] = $this->Level2ActivitiesRepository->getAllNotAttemptedActivitiesBySection($request->userId,Config::get('constant.LEVEL2_SECTION_1'));
            $data['section_2'] = $this->Level2ActivitiesRepository->getAllNotAttemptedActivitiesBySection($request->userId,Config::get('constant.LEVEL2_SECTION_2'));
            $data['section_3'] = $this->Level2ActivitiesRepository->getAllNotAttemptedActivitiesBySection($request->userId,Config::get('constant.LEVEL2_SECTION_3'));

            $response['status'] = 1;
            $response['login'] = 1;
            $response['message'] = trans('appmessages.default_success_msg');
            $response['data'] = $data;
            $this->log->info('Response for Level2questions' , array('api-name'=> 'getLevel2Activity'));
        } else {
            $this->log->error('Parameter missing error' , array('api-name'=> 'getLevel2Activity'));
            $response['message'] = trans('appmessages.missing_data_msg');
        }
        
        return response()->json($response, 200);
        
    }

    public function saveLevel2Activity(Request $request) {
        $response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg')];
        $teenager = $this->teenagersRepository->getTeenagerById($request->userId);
        $this->log->info('Get teenager detail for userId'.$request->userId , array('api-name'=> 'getLevel2Activity'));
        if($request->userId != "" && $request->activityID != "" && $request->optionId != "" && $request->timer != "" && $teenager) {

            $questionID = $request->activityID;
            $answerID = $request->optionId;
            $timer = $request->timer;
            $points = $this->Level2ActivitiesRepository->getPointsbyQuestion($questionID);
            
            $answers = [];
            $answers['answerID'] = $answerID;
            $answers['questionID'] = $questionID;
            $answers['timer'] = $timer;
            $answers['points'] = $points->l2ac_points;

            if (isset($request->userId) && $request->userId > 0 && isset($answers['timer']) && $answers['timer'] != '' && isset($answerID) && isset($questionID) && $questionID != 0 && $answerID != 0) {
                $questionsArray = $this->Level2ActivitiesRepository->saveTeenagerActivityResponseOneByOne($request->userId, $answers);
                if($questionsArray){
                    $response['status'] = 1;
                    $response['login'] = 1;
                    $section1Collection = $this->Level2ActivitiesRepository->getNoOfTotalQuestionsAttemptedQuestionBySection($request->userId,Config::get('constant.LEVEL2_SECTION_1'));
                    $section2Collection = $this->Level2ActivitiesRepository->getNoOfTotalQuestionsAttemptedQuestionBySection($request->userId,Config::get('constant.LEVEL2_SECTION_2'));
                    $section3Collection = $this->Level2ActivitiesRepository->getNoOfTotalQuestionsAttemptedQuestionBySection($request->userId,Config::get('constant.LEVEL2_SECTION_3'));

                    $section1Percentage = 0;
                    $section2Percentage = 0;
                    $section3Percentage = 0;
                    
                    if($section1Collection[0]->NoOfTotalQuestions != 0){
                        $section1Percentage = ($section1Collection[0]->NoOfAttemptedQuestions*100)/$section1Collection[0]->NoOfTotalQuestions;
                    }
                    if($section2Collection[0]->NoOfTotalQuestions != 0){
                        $section2Percentage = ($section2Collection[0]->NoOfAttemptedQuestions*100)/$section2Collection[0]->NoOfTotalQuestions;
                    }
                    if($section3Collection[0]->NoOfTotalQuestions != 0){
                        $section3Percentage = ($section3Collection[0]->NoOfAttemptedQuestions*100)/$section3Collection[0]->NoOfTotalQuestions;
                    }

                    $data['section_1_Percentage'] = number_format((float)$section1Percentage, 0, '.', '');
                    $data['section_2_Percentage'] = number_format((float)$section2Percentage, 0, '.', '');
                    $data['section_3_Percentage'] = number_format((float)$section3Percentage, 0, '.', '');
                    $response['message'] = trans('appmessages.default_success_msg');
                    $response['data'] = $data;
                } else {
                    $response['message'] = trans('appmessages.default_error_msg');
                }
            } else {
                $response['message'] = trans('appmessages.default_error_msg');
            }
        } else {
            $this->log->error('Parameter missing error' , array('api-name'=> 'getLevel2Activity'));
            $response['message'] = trans('appmessages.missing_data_msg');
        }
        
        return response()->json($response, 200);
    }

}
