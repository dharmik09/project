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
use Carbon\Carbon;  
use App\TeenagerBoosterPoint;
use App\Services\Professions\Contracts\ProfessionsRepository;
use App\Services\Level4Activity\Contracts\Level4ActivitiesRepository;

class Level4ActivityController extends Controller {

    public function __construct(Level4ActivitiesRepository $level4ActivitiesRepository, TeenagersRepository $teenagersRepository, ProfessionsRepository $professionsRepository) 
    {        
        $this->professionsRepository = $professionsRepository;
        $this->teenagersRepository = $teenagersRepository;   
        $this->level4ActivitiesRepository = $level4ActivitiesRepository;   
        $this->teenagerBoosterPoint = new TeenagerBoosterPoint();
    }

    /*
     * Get L4 Basic career questions
    */
    public function professionBasicQuestion() {
        $professionId = Input::get('professionId');
        $userId = Auth::guard('teenager')->user()->id;
        if($userId > 0 && $professionId != '') {
            $totalQuestion = $this->level4ActivitiesRepository->getNoOfTotalQuestionsAttemptedQuestion($userId, $professionId);
            
            $basicCompleted = 0; 
            if(isset($totalQuestion[0]->NoOfTotalQuestions) && $totalQuestion[0]->NoOfTotalQuestions > 0 && ($totalQuestion[0]->NoOfTotalQuestions == $totalQuestion[0]->NoOfAttemptedQuestions) ) {
                $basicCompleted = 1;
            }
            
            $activities = $this->level4ActivitiesRepository->getNotAttemptedActivities($userId, $professionId);
            if (isset($activities[0]) && !empty($activities[0])) {
                $activity = $activities[0];
                $timer = $activities[0]->timer;
            } else {
                $activity = [];
                $timer = 0;
            }
            $response = [];
            $response['basicCompleted'] = $basicCompleted;
            $response['data'] = $activity;
            $response['timer'] = $timer;
            $response['professionId'] = $professionId;
            $response['status'] = 1;
            return view('teenager.basic.careerBasicQuizSection', compact('response'));
        }
        $response['status'] = 0;
        $response['message'] = "Something went wrong!";

        return response()->json($response, 200);
        exit;
    }  

    public function saveBasicLevelActivity() {
        $response = [];
        $userId = Auth::guard('teenager')->user()->id;
        if($userId) {
            $body = Input::all();
            $timer = (isset($body['timer']) && $body['timer'] > 0) ? $body['timer'] : 0;
            $answerArray = (isset($body['answerID'])) ? $body['answerID'] : [];
            $answerID = (count($answerArray) > 0) ? implode(',', $answerArray) : $answerArray;

            $questionID = (isset($body['questionID']) && $body['questionID'] > 0) ? $body['questionID'] : '';
            $getAllQuestionRelatedDataFromQuestionId = $this->level4ActivitiesRepository->getAllQuestionRelatedDataFromQuestionId($questionID);

            $array = [];

            if ($getAllQuestionRelatedDataFromQuestionId && !empty($getAllQuestionRelatedDataFromQuestionId)) {
                $points = $getAllQuestionRelatedDataFromQuestionId->points;
                $type = $getAllQuestionRelatedDataFromQuestionId->type;
                $professionId = $getAllQuestionRelatedDataFromQuestionId->profession_id;
                
                $ansCorrect = $this->level4ActivitiesRepository->checkQuestionRightOrWrong($questionID, $answerID);
                
                if ($timer != 0 && $getAllQuestionRelatedDataFromQuestionId->timer < $timer) {
                    $array['points'] = 0;
                    $array['timer'] = 0;
                    $array['answerID'] = 0;
                    $answerID == 0;
                    $timer == 0;
                }

                if ($answerID == 0 && $timer == 0) {
                    $array['points'] = 0;
                    $array['timer'] = 0;
                    $array['answerID'] = 0;
                } else {
                    if (isset($ansCorrect) && $ansCorrect == 1) {
                        $array['points'] = (isset($points)) ? $points : 0;
                        $array['answerID'] = $answerID;
                        $array['timer'] = $timer;
                    } else {
                        $array['points'] = 0;
                        $array['answerID'] = $answerID;
                        $array['timer'] = $timer;
                    }
                }

                $array['questionID'] = $questionID;
                $array['earned_points'] = $array['points'];
                $array['profession_id'] = $professionId;

                $data['answers'][] = $array;

                //Save user response data for basic question
                $questionsArray = $this->level4ActivitiesRepository->saveTeenagerActivityResponse($userId, $data['answers']);

                $getQuestionOPtionFromQuestionId = $this->level4ActivitiesRepository->getQuestionOPtionFromQuestionId($questionID);
                $answerArrayId = explode(',', $getQuestionOPtionFromQuestionId->options_id);
                $answerArrayOptionCorrect = explode(',', $getQuestionOPtionFromQuestionId->correct_option);
                
                $activities = [];
                foreach ($answerArrayId as $keyValueP => $idOption) {
                    $activities[$idOption] = isset($answerArrayOptionCorrect[$keyValueP]) ? $answerArrayOptionCorrect[$keyValueP] : '';
                }

                $response['status'] = 1;
                $response['profession_id'] = $professionId;
                $response['message'] = trans('appmessages.default_success_msg');
                $response['data'] = $activities;
            } else {
                $response['data'] = '';
                $response['status'] = 0;
                $response['message'] = "Wrong Question Submitted!";
            }
        } else {
            $response['status'] = 0;
            $response['message'] = "Something went wrong!";
        }

        return response()->json($response, 200);
        exit;
    }  
}