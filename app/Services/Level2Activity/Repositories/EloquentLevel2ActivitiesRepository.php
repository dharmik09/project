<?php

namespace App\Services\Level2Activity\Repositories;

use DB;
use Config;
use App\Level2Answers;
use App\Level2Options;
use App\Services\Level2Activity\Contracts\Level2ActivitiesRepository;
use App\Services\Repositories\Eloquent\EloquentBaseRepository;

class EloquentLevel2ActivitiesRepository extends EloquentBaseRepository implements Level2ActivitiesRepository
{
    /**
     * @return array of all the active level2 activities
       Parameters
       @$searchParamArray : Array of Searching and Sorting parameters
     */

    public function getAllLeve2Activities()
    {
        $level2activities = DB::table(config::get('databaseconstants.TBL_LEVEL2_ACTIVITY'). " AS activity")
                          ->leftjoin(config::get('databaseconstants.TBL_LEVEL2_OPTIONS') . " AS options", 'activity.id', '=', 'options.l2op_activity')
                          ->leftjoin(config::get('databaseconstants.TBL_LEVEL2_APPTITUDE') . " AS apptitude", 'apptitude.id', '=', 'activity.l2ac_apptitude_type')
                          ->leftjoin(config::get('databaseconstants.TBL_LEVEL2_MI') . " AS mi", 'mi.id', '=', 'activity.l2ac_mi_type')
                          ->leftjoin(config::get('databaseconstants.TBL_LEVEL2_INTEREST') . " AS interest", 'interest.id', '=', 'activity.l2ac_interest')
                          ->leftjoin(config::get('databaseconstants.TBL_LEVEL2_PERSONALITY') . " AS personality", 'personality.id', '=', 'activity.l2ac_personality_type')
                          ->selectRaw('activity.* , GROUP_CONCAT(options.l2op_option) AS l2op_option, GROUP_CONCAT(options.l2op_fraction) AS l2op_fraction , mi.mit_name , interest.it_name , personality.pt_name, apptitude.apt_name')
                          ->where('activity.deleted', '<>', Config::get('constant.DELETED_FLAG'))
                          ->groupBy('activity.id')
                          ->get();
        return $level2activities;
    }

    public function saveLevel2ActivityDetail($activityDetail, $option,$radioval)
    {
        $objOption = new Level2Options();
   
        if($activityDetail['id'] != '' && $activityDetail['id'] > 0)
        {
          $return = $this->model->where('id', $activityDetail['id'])->update($activityDetail);

        }
        else
        {
           $return = $this->model->create($activityDetail);
        }
        if($return)
        {
            if($activityDetail['id'] != '' && $activityDetail['id'] > 0)
            {
               $id = $activityDetail['id'];
               $deleted = $activityDetail['deleted'];
            }
            else
            {
               $id = $return->id;
               $deleted = $return->deleted;
            }

        }

        $data = $objOption->where('l2op_activity',$activityDetail['id'] )->get();
        
        $optionDataLength=sizeof($data);
        $optionLength=sizeof($option);

        $option_new=array();
        $option_old=array();
        $option_oldId=array();

        for($i=0;$i<$optionDataLength;$i++)
               {
                   $option_old[]=$data[$i]['l2op_option'];
                }

        $option_new=$option;

        //$delete_option=array_diff($option_old,$option_new);

//        foreach($delete_option as $del)
//        {
//            $result = $objOption->where('l2op_option' , $del)->delete();
//        }


        $data = $objOption->where('l2op_activity',$activityDetail['id'] )->get();
        
        $optionDataLength=sizeof($data);
        for($i = 0 ; $i < $optionDataLength; ++$i)
        {
            $optionDetail = [];
            $optionDetail['l2op_activity'] = $id;
            $optionDetail['l2op_option'] = $option[$i];
            $optionDetail['deleted'] = $deleted;
            if($radioval==$i)
                $optionDetail['l2op_fraction']=1;

            else
                 $optionDetail['l2op_fraction']=0;

            if($activityDetail['id'] != '' && $activityDetail['id'] > 0)
            {
             
       
                $result = $objOption->where('id',$data[$i]->id)->update($optionDetail);
            }
            else{
                $result = $objOption->create($optionDetail);
            }
        }
        for($i = $optionDataLength;$i<count($option);$i++)
        {
            $optionDetail = [];
            $optionDetail['l2op_activity'] = $id;
            $optionDetail['l2op_option'] = $option[$i];
            $optionDetail['deleted'] = $deleted;
            if($radioval==$i)
                $optionDetail['l2op_fraction']=1;

            else
                $optionDetail['l2op_fraction']=0;

            $result = $objOption->create($optionDetail);

        }
        return $result;

    }



    /**
     * @return Boolean True/False
       Parameters
       @$id : Level2 Activity ID
     */
    public function deleteLevel2Activity($id)
    {
        $option = new Level2Options();
        $flag              = true;
        $level2activity          = $this->model->find($id);
        $level2activity->deleted = config::get('constant.DELETED_FLAG');
        $response          = $level2activity->save();
        $deleted = config::get('constant.DELETED_FLAG');
        $option       = $option->where('l2op_activity' , $id)->update(['deleted' => $deleted]);
        if($response && $option)
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    /**
     * Parameter : $teenagerId
     * Parameter : $teenagerId
     * return : array/object of the activities which are not attempted by teenager passed
    */
    public function saveTeenagerActivityResponse($teenagerId, $responses)
    {
        $points = 0;
        $questionsID = [];
        $level2TotalTime = 2700;
        foreach($responses as $response)
        {
            $timerFrom = '';
            $row = [];
            $row['l2ans_teenager'] = $teenagerId;
            $row['l2ans_activity'] = $response['questionID'];
            $row['l2ans_answer'] = $response['answerID'];
            $timerFrom = (isset($response['timer']))? $response['timer'] : 0 ;
            $row['l2ans_answer_timer'] = $level2TotalTime - $timerFrom; 
            $objLevel2Answers = new Level2Answers();
            $answered = $objLevel2Answers->where("l2ans_teenager", $teenagerId)->where("l2ans_activity", $response['questionID'])->first();
            if($answered)
            {
                $answered = $answered->toArray();
                $objLevel2Answers->where('id', $answered['id'])->update($row);
            }
            else
            {
                $res = $objLevel2Answers->create($row);
                if($res)
                {
                    $points += $response['points'];
                }
            }
            $questionsID[] = $response['questionID'];
        }

        $teenagerLevel2PointsRow = [];
        $teenagerLevel2PointsRow['tlb_teenager'] = $teenagerId;
        $teenagerLevel2PointsRow['tlb_level'] = config::get('constant.LEVEL2_ID');

        $teenagerLevelPoints = DB::table(config::get('databaseconstants.TBL_TEENAGER_LEVEL_BOOSTERS'))->where("tlb_teenager", $teenagerId)->where("tlb_level", config::get('constant.LEVEL2_ID'))->first();
        if($teenagerLevelPoints)
        {
            $teenagerLevelPoints = (array) $teenagerLevelPoints;
            $teenagerLevel2PointsRow['tlb_points'] = $teenagerLevelPoints['tlb_points'] + $points;
            DB::table(config::get('databaseconstants.TBL_TEENAGER_LEVEL_BOOSTERS'))->where('id', $teenagerLevelPoints['id'])->update($teenagerLevel2PointsRow);
        }
        else
        {
            $teenagerLevel2PointsRow['tlb_points'] = $points;
            DB::table(config::get('databaseconstants.TBL_TEENAGER_LEVEL_BOOSTERS'))->insert($teenagerLevel2PointsRow);
        }

        $returnArray = [];
        $returnArray['questionsID'] = $questionsID;
        $returnArray['total_Points'] = $points;
        return $returnArray;
    }
    /**
     *Save teen ans one by one 
     */
    public function saveTeenagerActivityResponseOneByOne($teenagerId, $response)
    {
        $points = 0;
        $questionsID = [];
        $level2TotalTime = 2700;
        
            $row = [];
            $row['l2ans_teenager'] = $teenagerId;
            $row['l2ans_activity'] = $response['questionID'];
            $row['l2ans_answer'] = $response['answerID'];
            $timerFrom = $response['timer'];
            $row['l2ans_answer_timer'] = $level2TotalTime - $timerFrom; 
            
            $objLevel2Answers = new Level2Answers();

            $answered = $objLevel2Answers->where("l2ans_teenager", $teenagerId)->where("l2ans_activity", $response['questionID'])->first();
            if($answered)
            {
                $answered = $answered->toArray();
                $objLevel2Answers->where('id', $answered['id'])->update($row);
            }
            else
            {
                $res = $objLevel2Answers->create($row);
                if($res)
                {
                    $points += $response['points'];
                }
            }
            $questionsID[] = $response['questionID'];

        $teenagerLevel2PointsRow = [];
        $teenagerLevel2PointsRow['tlb_teenager'] = $teenagerId;
        $teenagerLevel2PointsRow['tlb_level'] = config::get('constant.LEVEL2_ID');

        $teenagerLevelPoints = DB::table(config::get('databaseconstants.TBL_TEENAGER_LEVEL_BOOSTERS'))->where("tlb_teenager", $teenagerId)->where("tlb_level", config::get('constant.LEVEL2_ID'))->first();
        if($teenagerLevelPoints)
        {
            $teenagerLevelPoints = (array) $teenagerLevelPoints;
            $teenagerLevel2PointsRow['tlb_points'] = $teenagerLevelPoints['tlb_points'] + $points;
            DB::table(config::get('databaseconstants.TBL_TEENAGER_LEVEL_BOOSTERS'))->where('id', $teenagerLevelPoints['id'])->update($teenagerLevel2PointsRow);
        }
        else
        {
            $teenagerLevel2PointsRow['tlb_points'] = $points;
            DB::table(config::get('databaseconstants.TBL_TEENAGER_LEVEL_BOOSTERS'))->insert($teenagerLevel2PointsRow);
        }

        $returnArray = [];
        $returnArray['questionsID'] = $questionsID;
        $returnArray['total_Points'] = $points;
        return $returnArray;
    }

    /**
     * Parameter : $teenagerId
     * return : array/object of the activities which are not attempted by teenager passed
    */
    public function getNotAttemptedActivities($teenagerId)
    {
        $activities = DB::select(DB::raw("SELECT tmp.*
                                            FROM (SELECT
                                                L2AC.id AS activityID,
                                                l2ac_text,
                                                l2ac_points,
                                                l2ac_image,
                                                GROUP_CONCAT(L2OP.id) AS optionIds,
                                                GROUP_CONCAT(l2op_option) AS options,
                                                L2AC.deleted,
                                                count(*) as 'NoOfTotalQuestions'
                                                FROM
                                                " . config::get('databaseconstants.TBL_LEVEL2_ACTIVITY') . " AS L2AC
                                            INNER JOIN " . config::get('databaseconstants.TBL_LEVEL2_OPTIONS') . " AS L2OP ON L2OP.l2op_activity = L2AC.id
                                            GROUP BY
                                                L2AC.id) AS tmp
                                            LEFT JOIN " . config::get('databaseconstants.TBL_LEVEL2_ANSWERS') . " AS L2ANS ON L2ANS.l2ans_activity = tmp.activityID AND L2ANS.l2ans_teenager = $teenagerId
                                            WHERE tmp.deleted=1 and L2ANS.id IS NULL AND L2ANS.l2ans_teenager IS NULL AND L2ANS.l2ans_activity IS NULL AND L2ANS.l2ans_answer IS NULL"), array());

        foreach($activities as $key => $activity)
        {
            $optionIds = explode(",", $activity->optionIds);
            $options = explode(",", $activity->options);
            unset($activity->optionIds);
            unset($activity->options);

            $optionsWithId = [];
            foreach($options as $key1 => $option)
            {
                $temp = [];
                $temp['optionId'] = $optionIds[$key1];
                $temp['optionText'] = $option;
                $optionsWithId[] = $temp;
            }
            $activities[$key]->options = $optionsWithId;
        }
        
        return $activities;  
    }
     /**
     * Parameter : $teenagerId
     * Parameter : $teenagerId
     * return : array/object of the activities which are attempted by teenager passed and total no of questions
    */
    public function getNoOfTotalQuestionsAttemptedQuestion($teenagerId)
    {
        $result = DB::select(DB::raw("select (SELECT count(*) FROM ".config::get('databaseconstants.TBL_LEVEL2_ACTIVITY')." where deleted=1) as 'NoOfTotalQuestions', (select count(*) from ".config::get('databaseconstants.TBL_LEVEL2_ANSWERS')." where l2ans_teenager=".$teenagerId.") as 'NoOfAttemptedQuestions' "), array());
        return $result;
    }
    
    public function getLastAttemptedQuestionData($teenagerId)
    {
        $result = DB::table(config::get('databaseconstants.TBL_LEVEL2_ANSWERS'))->where('l2ans_teenager', $teenagerId)->orderBy('created_at', 'desc')->first();
        return $result;
    }
    
    
    
    
    
    
    public function  deleteAnswerbyTeenagerId($userid)
    {
        DB::table(config::get('databaseconstants.TBL_LEVEL2_ANSWERS'))->where('l2ans_teenager', $userid)->delete(); 
    }
    
    public function getLevel2ActivityWithAnswer($id)
    {
        $level2activities = DB::table(config::get('databaseconstants.TBL_LEVEL2_ANSWERS'). " AS answer ")
                              ->join(config::get('databaseconstants.TBL_LEVEL2_ACTIVITY'). " AS activity", 'answer.l2ans_activity', '=', 'activity.id')
                              ->join(config::get('databaseconstants.TBL_LEVEL2_OPTIONS') . " AS options ", 'answer.l2ans_answer', '=', 'options.id')
                              ->selectRaw('activity.* , answer.*, options.*')
                              ->where('answer.l2ans_teenager', '=', $id)
                              ->get();
        return $level2activities;
    }
    
    public function getTotalTimeForAttemptedQuestion($teenagerId){
        $totalTime = DB::table(config::get('databaseconstants.TBL_LEVEL2_ANSWERS'))->selectRaw('sum(l2ans_answer_timer) as sum')->where('l2ans_teenager', $teenagerId)->get();
        return $totalTime;
    }
    
    public function getLevel2AllActiveQuestion(){
            $result = DB::table(config::get('databaseconstants.TBL_LEVEL2_ACTIVITY'))->where("deleted", 1)->get();
            return $result;  
    }
    
}
