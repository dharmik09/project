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

    public function __construct(ProfessionsRepository $professionsRepository, Level4ActivitiesRepository $level4ActivitiesRepository) {
        $this->professionsRepository = $professionsRepository;
        $this->objLevel4Activities = new Level4Activity();
        $this->level4ActivitiesRepository = $level4ActivitiesRepository;
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
            $leve4activities = $this->level4ActivitiesRepository->getLevel4Details($searchParamArray);
        } else {
            if (Cache::has('l4BasicActivity')) {
                $leve4activities = Cache::get('l4BasicActivity');
            }else{
                $leve4activities = $this->level4ActivitiesRepository->getLevel4Details($searchParamArray);
                //Cache::add('l4basic', $leve4activities, 60);
                Cache::forever('l4BasicActivity', $leve4activities);
            }
        }
        return view('admin.ListLevel4Activity',compact('leve4activities','searchParamArray'));
    }

    public function getIndex()
    {
        $leve4activities = $this->level4ActivitiesRepository->getLevel4DetailsDataObj()->get()->count();
        $records = array();
        $columns = array(
            0 => 'id',
            1 => 'pf_name',
            2 => 'question_text'
        );
        
        $order = Input::get('order');
        $search = Input::get('search');
        $records["data"] = array();
        $iTotalRecords = $leve4activities;
        $iTotalFiltered = $iTotalRecords;
        $iDisplayLength = intval(Input::get('length')) <= 0 ? $iTotalRecords : intval(Input::get('length'));
        $iDisplayStart = intval(Input::get('start'));
        $sEcho = intval(Input::get('draw'));

        $records["data"] = $this->level4ActivitiesRepository->getLevel4DetailsDataObj();
        if (!empty($search['value'])) {
            $val = $search['value'];
            $records["data"]->where(function($query) use ($val) {
                $query->where('profession.pf_name', "Like", "%$val%");
                $query->where('activity.question_text', "Like", "%$val%");
            });

            // No of record after filtering
            $iTotalFiltered = $records["data"]->where(function($query) use ($val) {
                    $query->where('profession.pf_name', "Like", "%$val%");
                    $query->where('activity.question_text', "Like", "%$val%");
                })->count();
        }
        
        //order by
        foreach ($order as $o) {
            $records["data"] = $records["data"]->orderBy($columns[$o['column']], $o['dir']);
        }

        //limit
        $records["data"] = $records["data"]->take($iDisplayLength)->offset($iDisplayStart)->get();
        // this $sid use for school edit teenager and admin edit teenager
        $sid = 0;
        if (!empty($records["data"])) {
            foreach ($records["data"] as $key => $_records) {
                $records["data"][$key]->action = "yes";
                //$records["data"][$key]->created_at = date('d/m/Y',strtotime($_records->created_at));
            }
        }

        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalFiltered;

        return \Response::json($records);
        exit;
    }
    public function add()
    {
        $activity4Detail = [];
        $allActiveProfessions = $this->professionsRepository->getAllActiveProfession();
        
        return view('admin.EditLevel4Activity', compact('activity4Detail','allActiveProfessions'));
    }

    public function edit($id)
    {
        $activity4Detail = $this->objLevel4Activities->getActiveLevel4Activity($id);
        $allActiveProfessions = $this->professionsRepository->getAllActiveProfession();
        
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
      $response = $this->level4ActivitiesRepository->saveLevel4ActivityDetail($activity4Detail,$options,$radio_val);
      Cache::forget('l4BasicActivity');
      if($response)
      {
        return Redirect::to("admin/level4Activity".$pageRank)->with('success', trans('labels.level4activityupdatesuccess'));
      }
      else
      {
        Helpers::createAudit($this->loggedInUser->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_UPDATE'), Config::get('databaseconstants.TBL_LEVEL2_ACTIVITY'), $response, Config::get('constant.AUDIT_ORIGIN_WEB'),  trans('labels.somethingwrong'), serialize($activityDetail), $_SERVER['REMOTE_ADDR']);
        return Redirect::to("admin/level4Activity".$pageRank)->with('error', trans('labels.commonerrormessage'));
      }
    }

    public function delete($id)
    {
        $return = $this->level4ActivitiesRepository->deleteLevel4Activity($id);
        if ($return)
        {

            return Redirect::to("admin/level4Activity")->with('success', trans('labels.level4activitydeletesuccess'));
        }
        else
        {
            return Redirect::to("admin/level4Activity")->with('error', trans('labels.commonerrormessage'));
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
                                        $getProfessionId = $this->professionsRepository->getProfessionsData(trim($row['profession_name']));
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
                                    $saveLevel4Question = $this->level4ActivitiesRepository->saveLevel4Question($dataQuestionTable);

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
                                        $saveLevel4Options = $this->level4ActivitiesRepository->saveLevel4Options($dataQuestionOptionTable);
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
            return Redirect::to("admin/level4Activity")->with('success', 'Level4 Basic Activity uploaded successfully...');
            exit;
        }else{
            return Redirect::to("admin/level4Activity")->with('error', trans('labels.commonerrormessage'));
        }
    }
}