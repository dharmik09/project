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

            return view('teenager.basic.level1ActivityWorldType', compact('qualityDetail', 'isQuestionCompleted'));
        }
    }

    /*
    * Method : playLevel1WorldActivity
    * Response : Not attempted questions collections
    */
    public function playLevel1WorldActivity(Request $request) {
        $userId = Auth::guard('teenager')->user()->id;
        $isQuestionCompleted = 0;
        $type = ($request->type != "") ? $request->type : '0';
        //Get top trending images
        $topTrendingImages = $this->Level1ActivitiesRepository->getAllTopTrendingImages($type);
        $topImages['image'] = $toptrending = [];
        if (!empty($topTrendingImages)) {
            foreach ($topTrendingImages as $key => $val) {
                if ($type == 2) {
                    $topImages['image'] = ($val->ci_image != "") ? Storage::url($this->humanThumbImageUploadPath . $val->ci_image) : Storage::url($this->humanThumbImageUploadPath . 'proteen-logo.png');
                } else if($type == 1) {
                    $topImages['image'] = ($val->ci_image != "") ? Storage::url($this->cartoonThumbImageUploadPath . $val->ci_image) : Storage::url($this->cartoonThumbImageUploadPath . 'proteen-logo.png');
                } else {
                    $topImages['image'] = [];
                }
                $topImages['name'] = $val->ci_name;
                $topImages['category'] = $val->cic_name;
                $topImages['votes'] = $val->timesused;
                $topImages['rank'] = $key+1;
                $toptrending[] = $topImages;
            }
        }
        $mainArray['topTrendingImages'] = $toptrending;

        if($type == 1) {

            return view('teenager.basic.level1ActivityWorldFiction', compact('isQuestionCompleted', 'mainArray'));
        } else if($type == 2) {
            return view('teenager.basic.level1ActivityWorldNonFiction', compact('isQuestionCompleted'));
        } else if($type == 3) {
            return view('teenager.basic.level1ActivityWorldRelation', compact('isQuestionCompleted'));
        } else {
            return view('teenager.basic.level1ActivityWorldType', compact('isQuestionCompleted'));
        }
        return view('teenager.basic.level1ActivityWorldType', compact('isQuestionCompleted'));
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
}