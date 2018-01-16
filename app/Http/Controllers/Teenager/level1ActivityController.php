<?php

namespace App\Http\Controllers\Teenager;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Services\Level1Activity\Contracts\Level1ActivitiesRepository;
use Auth;
use Illuminate\Http\Request;
use Config;
use Input;
use Redirect;
use Image;
use Helpers;
use App\Level1Activity;
use App\Level1Traits;

class Level1ActivityController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Level1ActivitiesRepository $level1ActivitiesRepository)
    {
        $this->level1ActivitiesRepository = $level1ActivitiesRepository;
        $this->objLevel1Activity = new Level1Activity;
        $this->objTraits = new Level1Traits;
    }

    /*
    * Method : playLevel1Activity
    * Response : Not attempted questions collections
    */
    public function playLevel1Activity(Request $request) {
        $userId = Auth::guard('teenager')->user()->id;
        $level1Activities = $this->level1ActivitiesRepository->getNotAttemptedActivities($userId);
        $totalQuestion = $this->level1ActivitiesRepository->getNoOfTotalQuestionsAttemptedQuestion($userId);
        if($level1Activities && isset($totalQuestion[0]->NoOfTotalQuestions) && $totalQuestion[0]->NoOfTotalQuestions > 0 && $totalQuestion[0]->NoOfAttemptedQuestions < $totalQuestion[0]->NoOfTotalQuestions) {
            return view('teenager.basic.level1Question', compact('level1Activities'));
        } else {
            $qualityDetail = $this->level1ActivitiesRepository->getLevel1qualities();
            //$noOfAttemptedQuestions = $totalQuestion[0]->NoOfAttemptedQuestions;
            //$noOfTotalQuestions = $totalQuestion[0]->NoOfTotalQuestions;
            $isQuestionCompleted = 1;

            return view('teenager.basic.level1ActivityWorld', compact('qualityDetail', 'isQuestionCompleted'));
        }
    }

    public function saveFirstLevelActivity(Request $request) {
        $userId = Auth::guard('teenager')->user()->id;
        $questionOption = $this->objLevel1Activity->questionOptions($request->questionId);
        
        if($questionOption->toArray() && isset($questionOption[0]->options) && in_array($request->answerId, array_column($questionOption[0]->options->toArray(), 'id')) ) {
            $answers = [];
            $answers['answerID'] = $request->answerId;
            $answers['questionID'] = $questionOption[0]->id;
            $answers['points'] = $questionOption[0]->l1ac_points;
            $questionsArray = $this->level1ActivitiesRepository->saveTeenagerActivityResponseOneByOne($userId, $answers);
            $response['status'] = 1;
            $response['message'] = trans('appmessages.default_success_msg');
            $response['dataOfLastAttempt'] = $questionsArray;
        } else {
            $response['status'] == 0;
            $response['message'] = trans('appmessages.invalid_userid_msg');
        }
        return response()->json($response, 200);
        exit;
    }

    public function getLevel1Trait(){
        $userId = Auth::guard('teenager')->user()->id;
        $traitQuestion = $this->level1ActivitiesRepository->getLastNotAttemptedTraits($userId);
        if(count($traitQuestion)>0){
            $return = '<div class="survey-list">
                <div class="qualities-sec">
                    <p>'.$traitQuestion[0]->tqq_text.'</p>
                    <input type="hidden" id="traitQue" value="'.$traitQuestion[0]->activityID.'">
                    <div class="row">';
            foreach ($traitQuestion[0]->options as $key => $value) {
                $return .= '<div class="col-md-4 col-sm-6 col-xs-6">
                                <div class="ck-button">
                                    <label><input type="checkbox" name="traitAns" value="'.$value['optionId'].'"><span>'.$value['optionText'].'</span></label>
                                </div>
                            </div>';
            }
            $return .= '</div>
                </div>
                <div class="form-btn">
                    <span class="icon"><i class="icon-arrow-spring"></i></span>
                    <a onclick="saveLevel1TraitQuestion();" title="Next">Next</a>
                </div>
            </div>';
        }
        else{
            $return = '<h3>'.trans('labels.traitscompletionmessage').'</h3>';
        }
        return $return;
    }

    public function saveLevel1Trait(){
        $userId = Auth::guard('teenager')->user()->id;
        $questionID = Input::get('questionID');
        $answerArray = Input::get('answerID');

        if (isset($userId) && $userId > 0 && isset($questionID) && $questionID != 0) {
            $answerType = $this->objTraits->find($questionID)->tqq_is_multi_select;
            if($answerType == 0){
                if(count($answerArray)>1){
                    $return = trans('appmessages.onlyoneoptionallowedforthisquestion');
                    return $return;
                }
            }
            $questionsArray = '';
            foreach ($answerArray as $key => $value) {
                $answers = [];
                $answers['tqq_id'] = $questionID;
                $answers['tqo_id'] = $value;
                $answers['tqa_from'] = $userId;
                $answers['tqa_to'] = $userId;
                $questionsArray = $this->level1ActivitiesRepository->saveLevel1TraitsAnswer($answers);
            }
            if($questionsArray){
                return $this->getLevel1Trait();
            } else {
                $return = trans('appmessages.default_error_msg');
                return $return;
            }
        } else {
            $return = trans('appmessages.default_error_msg');
            return $return;
        }
    }
}