<?php

namespace App\Http\Controllers\Teenager;

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
use Storage;
use App\Services\Teenagers\Contracts\TeenagersRepository;
use App\Services\Sponsors\Contracts\SponsorsRepository;
use App\Services\Level2Activity\Contracts\Level2ActivitiesRepository;

class Level2ActivityController extends Controller {

    public function __construct(TeenagersRepository $teenagersRepository, SponsorsRepository $sponsorsRepository, Level2ActivitiesRepository $level2ActivitiesRepository) {
        $this->middleware('teenager');
        $this->objTeenagers = new Teenagers();
        $this->teenagersRepository = $teenagersRepository;
        $this->objSponsors = new Sponsors();
        $this->sponsorsRepository = $sponsorsRepository;
        $this->level2ActivitiesRepository = $level2ActivitiesRepository;
        $this->level2TotalTime = Config::get('constant.LEVEL2_TOTAL_TIME');        
        $this->teenOriginalImageUploadPath = Config::get('constant.TEEN_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->teenThumbImageUploadPath = Config::get('constant.TEEN_THUMB_IMAGE_UPLOAD_PATH');
        $this->teenThumbImageHeight = Config::get('constant.TEEN_THUMB_IMAGE_HEIGHT');
        $this->teenThumbImageWidth = Config::get('constant.TEEN_THUMB_IMAGE_WIDTH');
        $this->level2QuestionOptionImagePath = Config::get('constant.LEVEL2_QUESTION_OPTION_IMAGE_PATH');
        $this->level2ActivityThumbImageUploadPath = Config::get('constant.LEVEL2_ACTIVITY_THUMB_IMAGE_UPLOAD_PATH');
        $this->level2ActivityOriginalImageUploadPath = Config::get('constant.LEVEL2_ACTIVITY_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->level2TotalTime = Config::get('constant.LEVEL2_TOTAL_TIME');
        $this->interestOriginalImageUploadPath = Config::get('constant.INTEREST_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->miOriginalImageUploadPath = Config::get('constant.MI_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->personalityOriginalImageUploadPath = Config::get('constant.PERSONALITY_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->apptitudeOriginalImageUploadPath = Config::get('constant.APPTITUDE_ORIGINAL_IMAGE_UPLOAD_PATH');
    }

    public function index() {
        $section = Input::get('section_id');
        $user = Auth::guard('teenager')->user();

        $totalQuestion = $this->level2ActivitiesRepository->getNoOfTotalQuestionsAttemptedQuestion($user->id);

        $level2TotalTime = $this->level2TotalTime;

        if (isset($totalQuestion[0]->NoOfAttemptedQuestions) && $totalQuestion[0]->NoOfAttemptedQuestions > 0) {
            $getLastAttemptedQuestionData = $this->level2ActivitiesRepository->getLastAttemptedQuestionData($user->id);
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

        $response['timer'] = ($timer < 0) ? 0 : $timer;
        $timer = $response['timer'];

        $sectionPercentageCollection = $this->level2ActivitiesRepository->getNoOfTotalQuestionsAttemptedQuestionBySection($user->id,$section);      
        $sectionPercentage = 0;
        if($sectionPercentageCollection[0]->NoOfTotalQuestions != 0){
            $sectionPercentage = ($sectionPercentageCollection[0]->NoOfAttemptedQuestions >= $sectionPercentageCollection[0]->NoOfTotalQuestions) ? 100 : ($sectionPercentageCollection[0]->NoOfAttemptedQuestions*100)/$sectionPercentageCollection[0]->NoOfTotalQuestions;
        }
        if($sectionPercentage == 0){
            $response['sectionPercentage'] = 'Begin now';
        }
        else{
            $response['sectionPercentage'] = number_format((float)$sectionPercentage, 0, '.', '').'% Complete';
        }
        $sectionPercentage = $response['sectionPercentage'];

        $activities = $this->level2ActivitiesRepository->getNotAttemptedActivitiesBySection($user->id, $section);
        
        if(isset($activities) && !empty($activities))
        {
            $isSectionCompleted = false;
        }else{
            $isSectionCompleted = true;
        }
        
        $dispatchJob = Helpers::professionMatchScaleCalculate($sectionPercentageCollection, $user->id);
        $level2ActivityOriginalImageUploadPath = $this->level2ActivityOriginalImageUploadPath;

        return view('teenager.basic.level2ActivitySection', compact('timer', 'activities', 'section', 'level2ActivityOriginalImageUploadPath', 'sectionPercentage','isSectionCompleted'));
    }

    public function saveLevel2Ans() {
        $user = Auth::guard('teenager')->user();
        $section = Input::get('section_id');
        $answerID = Input::get('answerID');
        $questionID = Input::get('questionID');
        $timer = Input::get('timer');
        $points = $this->level2ActivitiesRepository->getPointsbyQuestion($questionID);
        
        $answers = [];
        $answers['answerID'] = $answerID;
        $answers['questionID'] = $questionID;
        $answers['timer'] = $timer;
        $answers['points'] = (isset($points->l2ac_points)) ? $points->l2ac_points : 0;

        if(isset($answers['timer']) && $answers['timer'] == ''){
            echo "Reloading...";
            exit;
        }

        if (isset($user->id) && $user->id > 0 && isset($answers['timer']) && $answers['timer'] != '' && isset($answerID) && isset($questionID) && $questionID != 0 && $answerID != 0) {
            $questionsArray = $this->level2ActivitiesRepository->saveTeenagerActivityResponseOneByOne($user->id, $answers);
            if($questionsArray) {
                return $this->index();
            } else {
                $response['activities']='<center><h3>Please try again</h3></center>';
            }
        } else {
            $response['activities']='<center><h3>Please try again</h3></center>';
        }
        return $response;
    }

}
