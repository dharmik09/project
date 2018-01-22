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

    public function __construct(TeenagersRepository $TeenagersRepository, SponsorsRepository $SponsorsRepository, Level2ActivitiesRepository $Level2ActivitiesRepository) {
        $this->middleware('teenager');
        $this->objTeenagers = new Teenagers();
        $this->TeenagersRepository = $TeenagersRepository;
        $this->objSponsors = new Sponsors();
        $this->SponsorsRepository = $SponsorsRepository;
        $this->Level2ActivitiesRepository = $Level2ActivitiesRepository;
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

        $totalQuestion = $this->Level2ActivitiesRepository->getNoOfTotalQuestionsAttemptedQuestion($user->id);

        $level2TotalTime = $this->level2TotalTime;

        if (isset($totalQuestion[0]->NoOfAttemptedQuestions) && $totalQuestion[0]->NoOfAttemptedQuestions > 0) {
            $getLastAttemptedQuestionData = $this->Level2ActivitiesRepository->getLastAttemptedQuestionData($user->id);
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
            $response['timer'] = 0;
        } else{
            $response['timer'] = $timer;
        }

        $sectionPercentageCollection = $this->Level2ActivitiesRepository->getNoOfTotalQuestionsAttemptedQuestionBySection($user->id,$section);      
        $sectionPercentage = 0;
        if($sectionPercentageCollection[0]->NoOfTotalQuestions != 0){
            $sectionPercentage = ($sectionPercentageCollection[0]->NoOfAttemptedQuestions*100)/$sectionPercentageCollection[0]->NoOfTotalQuestions;
        }
        if($sectionPercentage == 0){
            $response['sectionPercentage'] = 'Begin now';
        }
        else{
            $response['sectionPercentage'] = number_format((float)$sectionPercentage, 0, '.', '').'% Complete';
        }
        
        $activities = $this->Level2ActivitiesRepository->getNotAttemptedActivitiesBySection($user->id,$section);

        if (isset($activities) && !empty($activities)) {

            $activitiesHTML = '<div class="quiz_view">
            <div class="clearfix time_noti_view">
                <span class="time_type pull-left">
                <i class="icon-alarm"></i>
                <span class="time-tag" id="blackhole"></span>
                </span>
                <span class="sec-popup help_noti"><a href="javascript:void(0);" data-toggle="clickover" data-popover-content="#pop1" class="help-icon custompop" rel="popover" data-placement="bottom"><i class="icon-question"></i></a></span>
                                                    <div class="hide popoverContent">
                                                        Lorem ipsum dolor sit amet, consectetur adipisicing elit. Nisi eos, earum ipsum illum libero, beatae vitae, quos sit cum voluptate iste placeat distinctio porro nobis incidunt rem nesciunt. Cupiditate, animi.
                                                    </div>
            </div>
            <div class="quiz-que">
                <p class="que">
            <i class="icon-arrow-simple"></i>';
            
            $activitiesHTML .= $activities[0]->l2ac_text.'</p><div class="quiz-ans">';
            if ($activities[0]->l2ac_image) {
                if ($activities[0]->l2ac_image != '' && Storage::url($this->level2ActivityOriginalImageUploadPath . $activities[0]->l2ac_image)) {
                    $activitiesHTML .= '<div class="question-img"><img src="'.Storage::url($this->level2ActivityOriginalImageUploadPath . $activities[0]->l2ac_image).'" title="Click to enlarge image" class="pop-me"></div>';
                }
            }
            $activitiesHTML .= '<div class="radio">';

            foreach ($activities[0]->options as $key => $value) {
                $activitiesHTML .= '<label><input type="radio" name="'.$activities[0]->activityID.'l2AnsId" onclick="saveAns('.$activities[0]->activityID.')" value="'.$value['optionId'].'" ><span class="checker"></span><em>'.$value['optionText'].'</em></label>';
            }
                $activitiesHTML .= '<input type="hidden" id="'.$activities[0]->activityID.'l2AnsSection" value="'.$section.'">';
            
            $activitiesHTML .= '</div><div class="clearfix"><span class="next-que pull-right"><i class="icon-hand"></i></span></div></div></div></div>';

            $response['activities'] = $activitiesHTML;
        }
        else{
            $response['activities'] = "<center><h3>You have successfully completed this Quiz</h3></center>";
        }
        return $response;
    }

    public function saveLevel2Ans() {
        $user = Auth::guard('teenager')->user();
        $section = Input::get('section_id');
        $answerID = Input::get('answerID');
        $questionID = Input::get('questionID');
        $timer = Input::get('timer');
        $points = $this->Level2ActivitiesRepository->getPointsbyQuestion($questionID);
        
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
            $questionsArray = $this->Level2ActivitiesRepository->saveTeenagerActivityResponseOneByOne($user->id, $answers);
            if($questionsArray){
                return $this->index();
            }else{
                $response['activities']='<center><h3>Please try again</h3></center>';
            }
        }else{
            $response['activities']='<center><h3>Please try again</h3></center>';
        }
        return $response;
    }

}
