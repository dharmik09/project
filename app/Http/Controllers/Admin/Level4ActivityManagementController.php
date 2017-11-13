<?php

namespace App\Http\Controllers\Admin;

use Auth;
use Input;
use Image;
use File;
use Config;
use Request;
use Helpers;
use Redirect;
use App\Services\Professions\Contracts\ProfessionsRepository;
use Illuminate\Pagination\Paginator;
use App\Level4Activity;
use App\Http\Requests\Level4ActivityRequest;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Services\Level4Activity\Contracts\Level4ActivitiesRepository;
use Cache;

class Level4ActivityManagementController extends Controller {

    public function __construct(ProfessionsRepository $ProfessionsRepository, Level4ActivitiesRepository $Level4ActivitiesRepository) {
        $this->ProfessionsRepository = $ProfessionsRepository;
        $this->objLevel4Activities = new Level4Activity();
        $this->Level4ActivitiesRepository = $Level4ActivitiesRepository;
        $this->level4PointsForQuestions = Config::get('constant.LEVEL4_POINTS_FOR_QUESTION');
        $this->level4TimerForQuestions = Config::get('constant.LEVEL4_TIMER_FOR_QUESTION');
    }

    public function index() {       
        $searchParamArray = Input::all();   // for getting all records from database
        if (isset($searchParamArray['clearSearch'])) {
            unset($searchParamArray);
            $searchParamArray = array();
        }
        if (!empty($searchParamArray)) {
            Cache::forget('l4BasicActivity');
            $leve4activities = $this->Level4ActivitiesRepository->getLevel4Details($searchParamArray);
        } else {
            if (Cache::has('l4BasicActivity')) {
                $leve4activities = Cache::get('l4BasicActivity');
            }else{
                $leve4activities = $this->Level4ActivitiesRepository->getLevel4Details($searchParamArray);
                //Cache::add('l4basic', $leve4activities, 60);
                Cache::forever('l4BasicActivity', $leve4activities);
            }
        }
        return view('admin.ListLevel4Activity',compact('leve4activities','searchParamArray'));
    }

    public function add()
    {
        $activity4Detail = [];
        $allActiveProfessions = $this->ProfessionsRepository->getAllActiveProfession();
        
        return view('admin.EditLevel4Activity', compact('activity4Detail','allActiveProfessions'));
    }

    public function edit($id)
    {
        $activity4Detail = $this->objLevel4Activities->getActiveLevel4Activity($id);
        $allActiveProfessions = $this->ProfessionsRepository->getAllActiveProfession();
        
        return view('admin.EditLevel4Activity', compact('activity4Detail','allActiveProfessions'));
    }

    public function save(Level4ActivityRequest $Level4ActivityRequest)
    {
      $activity4Detail = [];
      $options = input::get('options_text');

      $hiddenLogo     = e(input::get('hidden_logo'));
      $activity4Detail['id'] = e(input::get('id'));
      $activity4Detail['question_text'] = e(input::get('question_text'));
      $activity4Detail['points'] = input::get('points');
      $activity4Detail['profession_id'] = input::get('profession_id');
      $activity4Detail['deleted'] = e(input::get('deleted'));
      $activity4Detail['timer'] = 30;
      $activity4Detail['type'] = input::get('question_type');
      $pageRank = input::get('pageRank');
      $points = e(input::get('points'));
      $point = e(input::get('hidden_points'));
      if(!empty($points))
      {
        $activity4Detail['points'] = $points;
      }
      else
      {
        $activity4Detail['points'] = $point;
      }

      $radio_val = input::get('correct_option');
      $response = $this->Level4ActivitiesRepository->saveLevel4ActivityDetail($activity4Detail,$options,$radio_val);
      Cache::forget('l4BasicActivity');
      if($response)
      {
        return Redirect::to("admin/level4activity".$pageRank)->with('success', trans('labels.level4activityupdatesuccess'));
      }
      else
      {
        Helpers::createAudit($this->loggedInUser->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_UPDATE'), Config::get('databaseconstants.TBL_LEVEL2_ACTIVITY'), $response, Config::get('constant.AUDIT_ORIGIN_WEB'),  trans('labels.somethingwrong'), serialize($activityDetail), $_SERVER['REMOTE_ADDR']);
        return Redirect::to("admin/level4activity".$pageRank)->with('error', trans('labels.commonerrormessage'));
      }
    }

    public function delete($id)
    {
        $return = $this->Level4ActivitiesRepository->deleteLevel4Activity($id);
        if ($return)
        {

            return Redirect::to("admin/level4activity")->with('success', trans('labels.level4activitydeletesuccess'));
        }
        else
        {
            return Redirect::to("admin/level4activity")->with('error', trans('labels.commonerrormessage'));
        }
    }

    public function addbulk() {
        return view('admin.Addlevel4questionbulk');
    }

    public function saveLevel4QuestionBulk() {
        $response = '';
        $question = Input::file('q_bulk');
        if(isset($question) && !empty($question)){
            Excel::selectSheetsByIndex(0)->load($question, function($reader) {
                        $flag5 = 0;
                        $dataQuestionTable = $dataQuestionOptionTable = $questionOptions = $checkCorrectAns = [];
                        foreach ($reader->toArray() as $row) {
                            if ($flag5 < 5) {
                                if ($flag5 == 0) {
                                    if (isset($row['profession_name']) && $row['profession_name'] != '') {
                                        $getProfessionId = $this->ProfessionsRepository->getProfessionsData(trim($row['profession_name']));
                                    }
                                }
                                $flag5++;
                                if (isset($getProfessionId[0]) && $getProfessionId[0] != '') {
                                    $dataQuestionTable['profession_id'] = $getProfessionId[0]->id;
                                    $dataQuestionTable['question_text'] = (isset($row['l4_basic_task_questions']) && $row['l4_basic_task_questions'] != '') ? $row['l4_basic_task_questions'] : '';
                                    $dataQuestionTable['points'] = $this->level4PointsForQuestions;
                                    $dataQuestionTable['timer'] = $this->level4TimerForQuestions;
                                    // 0 for multiple type question and 1 for single selection.
                                    $dataQuestionTable['type'] = (isset($row['truefalse_qnaidentified']) && $row['truefalse_qnaidentified'] != '') ? $row['truefalse_qnaidentified'] : 0;
                                    $dataQuestionTable['deleted'] = 1;
                                    //save level 4 question in question table
                                    $saveLevel4Question = $this->Level4ActivitiesRepository->saveLevel4Question($dataQuestionTable);

                                    if (isset($saveLevel4Question) && $saveLevel4Question != '') {
                                        $questionOptions[1] = (isset($row['answer_choice_1']) && $row['answer_choice_1'] != '')? $row['answer_choice_1'] : '' ;
                                        $questionOptions[2] = (isset($row['answer_choice_2']) && $row['answer_choice_2'] != '')? $row['answer_choice_2'] : '' ;
                                        $questionOptions[3] = (isset($row['answer_choice_3']) && $row['answer_choice_3'] != '')? $row['answer_choice_3'] : '' ;

                                        $checkCorrectAns = explode(',',$row['answer_key_either_correct_answers_or_correct_answer_choices']);

                                        $dataQuestionOptionTable['deleted'] = 1;
                                        $dataQuestionOptionTable['activity_id'] = $saveLevel4Question;
                                        $dataQuestionOptionTable['options_text'] = $questionOptions;
                                        $dataQuestionOptionTable['correct_option'] = $checkCorrectAns;

                                        //save level 4 question options in option table
                                        $saveLevel4Options = $this->Level4ActivitiesRepository->saveLevel4Options($dataQuestionOptionTable);
                                    }
                                }
                            }
                            if ($flag5 == 5) {
                                $flag5 = 0;
                                $getProfessionId = '';
                                unset($dataQuestionTable);
                                unset($dataQuestionOptionTable);
                            }
                        }
                        Cache::forget('l4BasicActivity');
                    });
            return Redirect::to("admin/level4activity")->with('success', 'Level4 Basic Activity uploaded successfully...');
            exit;
        }else{
            return Redirect::to("admin/level4activity")->with('error', trans('labels.commonerrormessage'));
        }
    }
}