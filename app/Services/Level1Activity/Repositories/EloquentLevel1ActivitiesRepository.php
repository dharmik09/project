<?php

namespace App\Services\Level1Activity\Repositories;

use DB;
use Config;
use App\Level1Answers;
use App\Level1Options;
use App\Teenagers;
use App\Level1Traits;
use App\Level1TraitsOptions;
use Helpers;
use App\Level1TraitsAnswers;
use App\Services\Level1Activity\Contracts\Level1ActivitiesRepository;
use App\Services\Repositories\Eloquent\EloquentBaseRepository;

class EloquentLevel1ActivitiesRepository extends EloquentBaseRepository implements Level1ActivitiesRepository {

    public function getLevel1AllActiveQuestion() {
        $result = DB::table(config::get('databaseconstants.TBL_LEVEL1_ACTIVITY'))->where("deleted", 1)->get();
        return $result;
    }

    /**
     * @return array of all the active level1 activities
      Parameters
      @$searchParamArray : Array of Searching and Sorting parameters
     */
    public function getAllLeve1Activities() {
        $level1activities = DB::table(config::get('databaseconstants.TBL_LEVEL1_ACTIVITY') . " AS activity")
                ->join(config::get('databaseconstants.TBL_LEVEL1_OPTIONS') . " AS options", 'activity.id', '=', 'options.l1op_activity')
                ->selectRaw('activity.*, GROUP_CONCAT(options.l1op_option)  AS l1op_option')
                ->where('activity.deleted', '<>', Config::get('constant.DELETED_FLAG'))
                ->groupBy('activity.id')
                ->get();
        return $level1activities;
    }

    /**
     * @return Level1 Activity details object
      Parameters
      @$activityDetail : Array of Level1 Activity detail from front
     */
    public function saveLevel1ActivityDetail($activityDetail, $optionDetail) {
        $objOption = new Level1Options();
        if ($activityDetail['id'] != '' && $activityDetail['id'] > 0) {
            $return = $this->model->where('id', $activityDetail['id'])->update($activityDetail);
        } else {
            $return = $this->model->create($activityDetail);
        }

        if ($return) {
            if ($activityDetail['id'] != '' && $activityDetail['id'] > 0) {
                $id = $activityDetail['id'];
                $deleted = $activityDetail['deleted'];
            } else {
                $id = $return->id;
                $deleted = $return->deleted;
            }
        }

        $data = $objOption->where('l1op_activity', $activityDetail['id'])->get();
        $j = 0;
        $countOption = count($optionDetail['l1op_option']);
        $countData = count($data);
        if ($countOption >= $countData) {
            for ($i = 0; $i < $countOption; ++$i) {
                $optionData = [];
                $optionData['l1op_activity'] = $id;
                $optionData['l1op_option'] = $optionDetail['l1op_option'][$i];
                $optionData['deleted'] = $deleted;
                if ($i == $optionDetail['l1op_fraction']) {
                    $optionData['l1op_fraction'] = '1';
                } else {
                    $optionData['l1op_fraction'] = '0';
                }
                if ($j < count($data)) {
                    if ($activityDetail['id'] != '' && $activityDetail['id'] > 0) {
                        $result = $objOption->where('id', $data[$i]->id)->update($optionData);
                    }
                    $j++;
                } else {
                    $result = $objOption->create($optionData);
                }
            }
        } else {
            for ($i = 0; $i < count($data); ++$i) {
                for ($i = 0; $i < $countOption; ++$i) {
                    $optionData = [];
                    $optionData['l1op_activity'] = $id;
                    $optionData['l1op_option'] = $optionDetail['l1op_option'][$i];
                    $optionData['deleted'] = $deleted;
                    if ($i == $optionDetail['l1op_fraction']) {
                        $optionData['l1op_fraction'] = '1';
                    } else {
                        $optionData['l1op_fraction'] = '0';
                    }
                    if ($j < count($data) - 1) {
                        if ($activityDetail['id'] != '' && $activityDetail['id'] > 0) {
                            $result = $objOption->where('id', $data[$i]->id)->update($optionData);
                        }
                        $j++;
                    }
                }
                $result = $objOption->where('id', $data[$i]->id)->delete($optionData);
            }
        }
        return $result;
    }

    /**
     * @return Boolean True/False
      Parameters
      @$id : Level1 Activity ID
     */
    public function deleteLevel1Activity($id) {
        $option = new Level1Options();
        $flag = true;
        $level1activity = $this->model->find($id);
        $level1activity->deleted = config::get('constant.DELETED_FLAG');
        $response = $level1activity->save();
        $deleted = config::get('constant.DELETED_FLAG');
        $option = $option->where('l1op_activity', $id)->update(['deleted' => $deleted]);
        if ($response && $option) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Parameter : $teenagerId
     * return : array/object of the activities which are not attempted by teenager passed
     */
    public function getNotAttemptedActivities($teenagerId) {
        $activities = DB::select(DB::raw("SELECT
                        tmp.*
                        FROM (SELECT
                            L1AC.id AS activityID,
                            l1ac_text,
                            l1ac_points,
                            l1ac_image,
                            GROUP_CONCAT(L1OP.id) AS optionIds,
                            GROUP_CONCAT(l1op_option) AS options,
                            L1AC.deleted
                        FROM
                            " . config::get('databaseconstants.TBL_LEVEL1_ACTIVITY') . " AS L1AC
                        INNER JOIN " . config::get('databaseconstants.TBL_LEVEL1_OPTIONS') . " AS L1OP ON L1OP.l1op_activity = L1AC.id
                        GROUP BY
                            L1AC.id) AS tmp
                        LEFT JOIN " . config::get('databaseconstants.TBL_LEVEL1_ANSWERS') . " AS L1ANS ON L1ANS.l1ans_activity = tmp.activityID AND L1ANS.l1ans_teenager = $teenagerId
                        WHERE tmp.deleted=1 and L1ANS.id IS NULL AND L1ANS.l1ans_teenager IS NULL AND L1ANS.l1ans_activity IS NULL AND L1ANS.l1ans_answer IS NULL"), array());

        foreach ($activities as $key => $activity) {
            $optionIds = explode(",", $activity->optionIds);
            $options = explode(",", $activity->options);
            unset($activity->optionIds);
            unset($activity->options);

            $optionsWithId = [];
            foreach ($options as $key1 => $option) {
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
     * @return array
      Parameters
      @$id : Searchtext
     */
    public function getsearchByText($serachtext) {
        $cartoonicons = DB::select(DB::raw("select ci_name,ci_image,'cartoon' as 'type' from " . config::get('databaseconstants.TBL_LEVEL1_CARTOON_ICON') . "  
                                                     where ci_name like '%" . $serachtext . "%'"), array());
        $humanicons = DB::select(DB::raw("select hi_name,hi_image,'human' as 'type' from " . config::get('databaseconstants.TBL_LEVEL1_HUMAN_ICON') . "  
                                                     where hi_name like '%" . $serachtext . "%'"), array());
        $mainArray = [];
        foreach ($cartoonicons as $key => $icon) {
            $iconArray = [];
            $iconArray['name'] = $cartoonicons[$key]->ci_name;
            $iconArray['icon'] = $cartoonicons[$key]->ci_image;
            $iconArray['type'] = $cartoonicons[$key]->type;
            $mainArray[] = $iconArray;
        }
        foreach ($humanicons as $key => $icon) {
            $iconArray = [];
            $iconArray['name'] = $humanicons[$key]->hi_name;
            $iconArray['icon'] = $humanicons[$key]->hi_image;
            $iconArray['type'] = $humanicons[$key]->type;
            $mainArray[] = $iconArray;
        }
        return $mainArray;
    }

    /**
     * Parameter : $teenagerId
     * Parameter : $teenagerId
     * return : array/object of the activities which are attempted by teenager passed and total no of questions
     */
    public function getNoOfTotalQuestionsAttemptedQuestion($teenagerId) {
        $result = DB::select(DB::raw("select (SELECT count(*) FROM " . config::get('databaseconstants.TBL_LEVEL1_ACTIVITY') . " where deleted=1) as 'NoOfTotalQuestions', (select count(*) from " . config::get('databaseconstants.TBL_LEVEL1_ANSWERS') . " where l1ans_teenager=" . $teenagerId . ") as 'NoOfAttemptedQuestions' "), array());
        return $result;
    }

    /**
     * Parameter : $teenagerId
     * Parameter : $teenagerId
     * return : array/object of the activities which are not attempted by teenager passed
     */
    public function saveTeenagerActivityResponse($teenagerId, $responses) {
        $points = 0;
        $questionsID = [];

        foreach ($responses as $response) {

            $row = [];
            $row['l1ans_teenager'] = $teenagerId;
            $row['l1ans_activity'] = $response['questionID'];
            $row['l1ans_answer'] = $response['answerID'];

            $objLevel1Answers = new Level1Answers();

            $answered = $objLevel1Answers->where("l1ans_teenager", $teenagerId)->where("l1ans_activity", $response['questionID'])->first();
            if ($answered) {
                $answered = $answered->toArray();
                $objLevel1Answers->where('id', $answered['id'])->update($row);
            } else {
                $res = $objLevel1Answers->create($row);
                if ($res) {
                    $points += $response['points'];
                }
            }
            $questionsID[] = $response['questionID'];
        }

        $teenagerLevel1PointsRow = [];
        $teenagerLevel1PointsRow['tlb_teenager'] = $teenagerId;
        $teenagerLevel1PointsRow['tlb_level'] = config::get('constant.LEVEL1_ID');

        $teenagerLevelPoints = DB::table(config::get('databaseconstants.TBL_TEENAGER_LEVEL_BOOSTERS'))->where("tlb_teenager", $teenagerId)->where("tlb_level", config::get('constant.LEVEL1_ID'))->first();
        if ($teenagerLevelPoints) {
            $teenagerLevelPoints = (array) $teenagerLevelPoints;
            $teenagerLevel1PointsRow['tlb_points'] = $teenagerLevelPoints['tlb_points'] + $points;
            DB::table(config::get('databaseconstants.TBL_TEENAGER_LEVEL_BOOSTERS'))->where('id', $teenagerLevelPoints['id'])->update($teenagerLevel1PointsRow);
        } else {
            $teenagerLevel1PointsRow['tlb_points'] = $points;
            DB::table(config::get('databaseconstants.TBL_TEENAGER_LEVEL_BOOSTERS'))->insert($teenagerLevel1PointsRow);
        }

        $returnArray = [];
        $returnArray['questionsID'] = $questionsID;
        $returnArray['total_Points'] = $points;
        return $returnArray;
    }

    /**
     * Save teen ans one by one 
     */
    public function saveTeenagerActivityResponseOneByOne($teenagerId, $response) {

        $points = 0;
        $questionsID = [];

        $row = [];
        $row['l1ans_teenager'] = $teenagerId;
        $row['l1ans_activity'] = $response['questionID'];
        $row['l1ans_answer'] = $response['answerID'];

        $objLevel1Answers = new Level1Answers();

        $answered = $objLevel1Answers->where("l1ans_teenager", $teenagerId)->where("l1ans_activity", $response['questionID'])->first();
        if ($answered) {
            $answered = $answered->toArray();
            $objLevel1Answers->where('id', $answered['id'])->update($row);
        } else {
            $res = $objLevel1Answers->create($row);
            if ($res) {
                $points += $response['points'];
                //Saving the pro coins data
                $proCoins = Teenagers::find($teenagerId);
                $configValue = Helpers::getConfigValueByKey('PROCOINS_FACTOR_L1');
                if($proCoins) {
                    $proCoins->t_coins = (int)$proCoins->t_coins + ( $response['points'] * $configValue );
                    $proCoins->save();
                }
            }
        }
        $questionsID[] = $response['questionID'];

        $teenagerLevel1PointsRow = [];
        $teenagerLevel1PointsRow['tlb_teenager'] = $teenagerId;
        $teenagerLevel1PointsRow['tlb_level'] = config::get('constant.LEVEL1_ID');

        $teenagerLevelPoints = DB::table(config::get('databaseconstants.TBL_TEENAGER_LEVEL_BOOSTERS'))->where("tlb_teenager", $teenagerId)->where("tlb_level", config::get('constant.LEVEL1_ID'))->first();
        if ($teenagerLevelPoints) {
            $teenagerLevelPoints = (array) $teenagerLevelPoints;
            $teenagerLevel1PointsRow['tlb_points'] = $teenagerLevelPoints['tlb_points'] + $points;
            DB::table(config::get('databaseconstants.TBL_TEENAGER_LEVEL_BOOSTERS'))->where('id', $teenagerLevelPoints['id'])->update($teenagerLevel1PointsRow);
        } else {
            $teenagerLevel1PointsRow['tlb_points'] = $points;
            DB::table(config::get('databaseconstants.TBL_TEENAGER_LEVEL_BOOSTERS'))->insert($teenagerLevel1PointsRow);
        }

        $returnArray = [];
        $returnArray['questionsID'] = $questionsID;
        $returnArray['total_Points'] = $points;
        return $returnArray;
    }

    /**
     * Parameter : $teenagerId
     * return : array/object of the level1 Qualities
     */
    public function getLevel1qualities() {
        $result = DB::select(DB::raw("SELECT *  FROM " . config::get('databaseconstants.TBL_LEVEL1_QUALITY')." where deleted = 1"), array());
        return $result;
    }
    
    public function getLevel1AttemptedQuality($teenagerId) {
        $result = DB::table(config::get('databaseconstants.TBL_TEENAGER_ICON'))->where(['ti_teenager' => $teenagerId , 'deleted' => 1])->first();
        return $result;
    }

    public function getTeenAttemptedQualityType($teenagerId) {
        $result = DB::table(config::get('databaseconstants.TBL_TEENAGER_ICON'))->where(['ti_teenager' => $teenagerId , 'deleted' => 1])->groupBy('ti_icon_type')->pluck('ti_icon_type');
        return $result;
    }
    
    
    /**
     * Parameter : $teenagerId
     * return : array/object of the level1 Fiction Cartoon Icon
     */
    public function getLevel1FictionCartoon()
    {
            $result = DB::select(DB::raw("SELECT *  FROM " . config::get('databaseconstants.TBL_LEVEL1_CARTOON_ICON')." WHERE deleted = 1"), array());
            return $result;

    }

    /**
     * Parameter : $teenagerId
     * return : array/object of the level1 Fiction Cartoon Icon Category
     */
    public function getLevel1FictionCartoonCategory()
    {
            $result = DB::select(DB::raw("SELECT *  FROM " . config::get('databaseconstants.TBL_CARTOON_ICON_CATEGORY')). " WHERE deleted = 1 ORDER BY cic_name asc", array());
            return $result;

    }
    /**
     * Parameter : $teenagerId
     * return : array/object of the level1 Non Fictional Human Icon
     */
    public function getLevel1NonFictionHuman()
    {
            $result = DB::select(DB::raw("SELECT *  FROM " . config::get('databaseconstants.TBL_LEVEL1_HUMAN_ICON')." WHERE deleted = 1"), array());
            return $result;

    }

    /**
     * return : array/object of the level1 Non Fictional Human Icon Category
     */   
    public function getLevel1NonFictionHumanCategory()
    {
            $result = DB::select(DB::raw("SELECT *  FROM " . config::get('databaseconstants.TBL_HUMAN_ICON_CATEGORY'))." WHERE deleted = 1 ORDER BY hic_name asc ", array());
            return $result;

    }

    /**
     * return : array/object of the level1 Relation
     */
    public function getLevel1Relation() {
        $result = DB::select(DB::raw("SELECT *  FROM " . config::get('databaseconstants.TBL_LEVEL1_RELATION')), array());
        return $result;
    }

    /**
     * Parameter : $teenagerId
     * return : array/object of the Level1Part2 response
     */
    public function saveTeenagerLevel1Part2($teenIconSelection) {
      $result = DB::table(config::get('databaseconstants.TBL_TEENAGER_ICON'))->where("ti_teenager", $teenIconSelection['ti_teenager'])->where("ti_icon_type", $teenIconSelection['ti_icon_type'])->where("ti_icon_id", $teenIconSelection['ti_icon_id'])->get();
      if (!empty($result) && count($result) > 0) {
        $id = DB::table(config::get('databaseconstants.TBL_TEENAGER_ICON'))->where("ti_teenager", $teenIconSelection['ti_teenager'])->where("ti_icon_type", $teenIconSelection['ti_icon_type'])->where("ti_icon_id", $teenIconSelection['ti_icon_id'])->update($teenIconSelection);
        return $id;
      } else {
        $id = DB::table(config::get('databaseconstants.TBL_TEENAGER_ICON'))->insertGetId($teenIconSelection);
        return $id;
      }
    }

    public function saveTeenagerLevel1Part2Qualities($qualityResponseData) {
        $id = DB::table(config::get('databaseconstants.TBL_TEENAGER_ICON_QUALITIES'))->insert($qualityResponseData);
        return $id;
        //echo "<pre>"; print_r($qualityResponseData); exit;
    }

    public function deleteAnswerbyTeenagerId($userid) {
        DB::table(config::get('databaseconstants.TBL_LEVEL1_ANSWERS'))->where('l1ans_teenager', $userid)->delete();
    }

    public function getTopTrendingImages() {
        $result = DB::select(DB::raw("SELECT ti_icon_id,COUNT(ti_icon_id) AS timesused,pro_hi_human_icons.hi_image as ci_image,pro_hi_human_icons.hi_name as ci_name,pro_hi_human_icons_category.hic_name as cic_name FROM " . config::get('databaseconstants.TBL_TEENAGER_ICON') . " JOIN pro_hi_human_icons ON pro_ti_teenager_icons.ti_icon_id = pro_hi_human_icons.id JOIN pro_hi_human_icons_category ON pro_hi_human_icons.hi_category = pro_hi_human_icons_category.id WHERE ti_icon_type = 2 AND pro_hi_human_icons.deleted = 1 GROUP BY ti_icon_id ORDER BY timesused DESC LIMIT 0,10"), array());
        return $result;
    }

    public function getIconName($textName = null, $ci_category_id, $tableName) {
        if ($tableName == "pro_ci_cartoon_icons") {
            $iconName = "ci_name";
            $category = "ci_category";
        } else {
            $iconName = "hi_name";
            $category = "hi_category";
        }
        $iconCategory = DB::table($tableName)->where($category, $ci_category_id)->where("deleted", 1)->get();
        return $iconCategory;
    }

    public function getLevel1ActitvityAnswer($questionId) {
        $answers = DB::table('pro_l1ans_level1_answers')->where('l1ans_activity', $questionId)->get();
        return $answers;
    }

    public function getLevel1ActivityWithAnswer($id) {
        $level1activities = DB::table(config::get('databaseconstants.TBL_LEVEL1_ANSWERS') . " AS answer")
                ->join(config::get('databaseconstants.TBL_LEVEL1_ACTIVITY') . " AS activity", 'answer.l1ans_activity', '=', 'activity.id')
                ->join(config::get('databaseconstants.TBL_LEVEL1_OPTIONS') . " AS options", 'answer.l1ans_answer', '=', 'options.id')
                ->selectRaw('activity.* , answer.*, options.*, activity.id as activityid')
                ->where('answer.l1ans_teenager', '=', $id)
                ->get();
        return $level1activities;
    }

    public function getTopSelectedIcons($gender) {
        $whereStr = '';
        if (isset($gender) && $gender != '') {
            $whereStr = 'AND pro_t_teenagers.t_gender = '.$gender;
        }
        $humanIcon = DB::select(DB::raw("SELECT ti_icon_id,COUNT(ti_icon_id) AS timesused,pro_hi_human_icons.hi_image as hi_image,pro_hi_human_icons.hi_name as hi_name,pro_hi_human_icons_category.hic_name as hic_name FROM " . config::get('databaseconstants.TBL_TEENAGER_ICON') . " JOIN pro_hi_human_icons ON pro_ti_teenager_icons.ti_icon_id = pro_hi_human_icons.id JOIN pro_hi_human_icons_category ON pro_hi_human_icons.hi_category = pro_hi_human_icons_category.id JOIN pro_t_teenagers ON pro_t_teenagers.id = pro_ti_teenager_icons.ti_teenager WHERE ti_icon_type = 2 AND pro_hi_human_icons.deleted = 1 ".$whereStr." GROUP BY ti_icon_id ORDER BY timesused DESC LIMIT 0,10"), array());
        $cartoonIcon = DB::select(DB::raw("SELECT ti_icon_id,COUNT(ti_icon_id) AS timesused,pro_ci_cartoon_icons.ci_image,pro_ci_cartoon_icons.ci_name,pro_cic_cartoon_icons_category.cic_name FROM " . config::get('databaseconstants.TBL_TEENAGER_ICON') . " JOIN pro_ci_cartoon_icons ON pro_ti_teenager_icons.ti_icon_id = pro_ci_cartoon_icons.id JOIN pro_cic_cartoon_icons_category ON pro_ci_cartoon_icons.ci_category = pro_cic_cartoon_icons_category.id JOIN pro_t_teenagers ON pro_t_teenagers.id = pro_ti_teenager_icons.ti_teenager WHERE ti_icon_type = 1 AND pro_ci_cartoon_icons.deleted = 1 ".$whereStr." GROUP BY ti_icon_id ORDER BY timesused DESC LIMIT 0,10"), array());
        $icons = array('human'=>$humanIcon,'cartoon'=>$cartoonIcon);

        return $icons;
    }

    public function getAllSelectedIcons($category,$gender) {
        $whereStr = '';
        if (isset($gender) && $gender != '') {
            $whereStr = 'AND pro_t_teenagers.t_gender = '.$gender;
        }
        if($category == 2){
            $dataIcon = DB::select(DB::raw("SELECT ti_icon_id,COUNT(ti_icon_id) AS timesused,pro_hi_human_icons.hi_image as hi_image,pro_hi_human_icons.hi_name as hi_name,pro_hi_human_icons_category.hic_name as hic_name FROM " . config::get('databaseconstants.TBL_TEENAGER_ICON') . " JOIN pro_hi_human_icons ON pro_ti_teenager_icons.ti_icon_id = pro_hi_human_icons.id JOIN pro_hi_human_icons_category ON pro_hi_human_icons.hi_category = pro_hi_human_icons_category.id JOIN pro_t_teenagers ON pro_t_teenagers.id = pro_ti_teenager_icons.ti_teenager WHERE ti_icon_type = 2 AND pro_hi_human_icons.deleted = 1 ".$whereStr." GROUP BY ti_icon_id ORDER BY timesused DESC"), array());
        }else{
            $dataIcon = DB::select(DB::raw("SELECT ti_icon_id,COUNT(ti_icon_id) AS timesused,pro_ci_cartoon_icons.ci_image,pro_ci_cartoon_icons.ci_name,pro_cic_cartoon_icons_category.cic_name FROM " . config::get('databaseconstants.TBL_TEENAGER_ICON') . " JOIN pro_ci_cartoon_icons ON pro_ti_teenager_icons.ti_icon_id = pro_ci_cartoon_icons.id JOIN pro_cic_cartoon_icons_category ON pro_ci_cartoon_icons.ci_category = pro_cic_cartoon_icons_category.id JOIN pro_t_teenagers ON pro_t_teenagers.id = pro_ti_teenager_icons.ti_teenager WHERE ti_icon_type = 1 AND pro_ci_cartoon_icons.deleted = 1 ".$whereStr." GROUP BY ti_icon_id ORDER BY timesused DESC"), array());
        }
        return $dataIcon;
    }

    public function getIconNameById($ci_category_id, $tableName) {
        if ($tableName == "pro_ci_cartoon_icons") {
            $iconName = "ci_name";
            $category = "id";
        } else {
            $iconName = "hi_name";
            $category = "id";
        }
        $iconCategory = DB::table($tableName)->where($category, $ci_category_id)->where("deleted", 1)->get();
        return $iconCategory;
    }

    public function searchIconName($ci_category_id, $tableName,$search) {
        if ($tableName == "pro_ci_cartoon_icons") {
            $iconName = "ci_name";
            $category = "ci_category";
        } else {
            $iconName = "hi_name";
            $category = "hi_category";
        }
        $iconCategory = DB::table($tableName)->where($category, $ci_category_id)->where("deleted", 1)->where($iconName,'like', '%' . $search . '%')->paginate(Config::get('constant.RECORD_PER_PAGE_ICON'));
        return $iconCategory;
    }

    public function getIconNameWithPagination($textName = null, $ci_category_id, $tableName) {
        if ($tableName == "pro_ci_cartoon_icons") {
            $iconName = "ci_name";
            $category = "ci_category";
        } else {
            $iconName = "hi_name";
            $category = "hi_category";
        }
        $iconCategory = DB::table($tableName)->where($category, $ci_category_id)->where("deleted", 1)->paginate(Config::get('constant.RECORD_PER_PAGE_ICON'));
        return $iconCategory;
    }

    public function searchIconNameWithPagination($ci_category_id, $tableName, $search = null) {
        if ($tableName == "pro_ci_cartoon_icons") {
            $iconName = "ci_name";
            $category = "ci_category";
        } else {
            $iconName = "hi_name";
            $category = "hi_category";
        }
        if($search != "" && $search != null) {
            $iconCategory = DB::table($tableName)->where($category, $ci_category_id)->where("deleted", 1)->where($iconName,'like', '%' . $search . '%')->paginate(Config::get('constant.RECORD_PER_PAGE_ICON'));
        } else {
            $iconCategory = DB::table($tableName)->where($category, $ci_category_id)->where("deleted", 1)->paginate(Config::get('constant.RECORD_PER_PAGE_ICON'));
        }
        return $iconCategory;
    }

    public function getTeenagerLevel1Part2Icon($userId, $category) {
        $result = DB::table(config::get('databaseconstants.TBL_TEENAGER_ICON'))->selectRaw('ti_icon_type')->where("deleted", 1)->where('ti_teenager',$userId)->whereIn('ti_icon_type',$category)->get();
        return $result;
    }

    public function getAllTopTrendingImages($icon_type) {
        if ($icon_type == 1){
           $result = DB::select(DB::raw("SELECT ti_icon_id,COUNT(ti_icon_id) AS timesused,pro_ci_cartoon_icons.ci_image as ci_image,pro_ci_cartoon_icons.ci_name as ci_name,pro_cic_cartoon_icons_category.cic_name as cic_name FROM " . config::get('databaseconstants.TBL_TEENAGER_ICON') . " JOIN pro_ci_cartoon_icons ON pro_ti_teenager_icons.ti_icon_id = pro_ci_cartoon_icons.id JOIN pro_cic_cartoon_icons_category ON pro_ci_cartoon_icons.ci_category = pro_cic_cartoon_icons_category.id WHERE ti_icon_type = " .$icon_type . " AND pro_ci_cartoon_icons.deleted = 1 GROUP BY ti_icon_id ORDER BY timesused DESC LIMIT 0,10"), array());
        } else if($icon_type == 2) {
            $result = DB::select(DB::raw("SELECT ti_icon_id,COUNT(ti_icon_id) AS timesused,pro_hi_human_icons.hi_image as ci_image,pro_hi_human_icons.hi_name as ci_name,pro_hi_human_icons_category.hic_name as cic_name FROM " . config::get('databaseconstants.TBL_TEENAGER_ICON') . " JOIN pro_hi_human_icons ON pro_ti_teenager_icons.ti_icon_id = pro_hi_human_icons.id JOIN pro_hi_human_icons_category ON pro_hi_human_icons.hi_category = pro_hi_human_icons_category.id WHERE ti_icon_type = " .$icon_type . " AND pro_hi_human_icons.deleted = 1 GROUP BY ti_icon_id ORDER BY timesused DESC LIMIT 0,10"), array());
        } else {
            $result = '';
        }
        return $result;
    }

    public function getLevel1FictionCartoonById($slot,$category_id){
        if ($slot > 0) {
            $slot = $slot * config::get('constant.RECORD_PER_PAGE_ICON');
        }
        $result = DB::table(config::get('databaseconstants.TBL_LEVEL1_CARTOON_ICON'))
                    ->selectRaw('*')
                    ->where('deleted',1)
                    ->where('ci_category',$category_id)
                    ->skip($slot)
                    ->take(config::get('constant.RECORD_PER_PAGE_ICON'))
                    ->get();
        return $result;
    }

    public function getLevel1NonFictionHumanById($slot,$category_id) {
        if ($slot > 0) {
            $slot = $slot * config::get('constant.RECORD_PER_PAGE_ICON');
        }
        $result = DB::table(config::get('databaseconstants.TBL_LEVEL1_HUMAN_ICON'))
                    ->selectRaw('*')
                    ->where('hi_category',$category_id)
                    ->where('deleted',1)
                    ->skip($slot)
                    ->take(config::get('constant.RECORD_PER_PAGE_ICON'))
                    ->get();
        return $result;
    }

    public function getLevel1FictionCartoonByIdForSearch($slot,$category_id,$search){
        if ($slot > 0) {
            $slot = $slot * config::get('constant.RECORD_PER_PAGE_ICON');
        }
        $result = DB::table(config::get('databaseconstants.TBL_LEVEL1_CARTOON_ICON'))
                    ->selectRaw('*')
                    ->where('deleted',1)
                    ->where('ci_category',$category_id)
                    ->where('ci_name','like', '%' . $search . '%')
                    ->skip($slot)
                    ->take(config::get('constant.RECORD_PER_PAGE_ICON'))
                    ->get();
        return $result;
    }

    public function getLevel1NonFictionHumanByIdForSearch($slot,$category_id,$search) {
        if ($slot > 0) {
            $slot = $slot * config::get('constant.RECORD_PER_PAGE_ICON');
        }
        $result = DB::table(config::get('databaseconstants.TBL_LEVEL1_HUMAN_ICON'))
                    ->selectRaw('*')
                    ->where('hi_category',$category_id)
                    ->where('hi_name','like', '%' . $search . '%')
                    ->where('deleted',1)
                    ->skip($slot)
                    ->take(config::get('constant.RECORD_PER_PAGE_ICON'))
                    ->get();
        return $result;
    }

    /**
     * @return array of all the active level1 traits
      Parameters
      @$searchParamArray : Array of Searching and Sorting parameters
     */
    public function getAllLeve1Traits() {
        $level1Traits = DB::table(config::get('databaseconstants.TBL_TRAITS_QUALITY_ACTIVITY') . " AS traits")
                ->join(config::get('databaseconstants.TBL_TRAITS_QUALITY_OPTIONS') . " AS options", 'traits.id', '=', 'options.tqq_id')
                ->selectRaw('traits.*, GROUP_CONCAT(options.tqo_option)  AS tqo_option')
                ->where('traits.deleted', '<>', Config::get('constant.DELETED_FLAG'))
                ->groupBy('traits.id')
                ->get();
        return $level1Traits;
    }

    /**
     * @return Boolean True/False
      Parameters
      @$id : Level1 Trait ID
     */
    public function deleteLevel1Trait($id) {
        $objTraits = new Level1Traits();
        $objOption = new Level1TraitsOptions();
        $flag = true;
        $level1traits = $objTraits->find($id);
        $level1traits->deleted = config::get('constant.DELETED_FLAG');
        $response = $level1traits->save();
        $deleted = config::get('constant.DELETED_FLAG');
        $objOption = $objOption->where('tqq_id', $id)->update(['deleted' => $deleted]);
        if ($response && $objOption) {
            return true;
        } else {
            return false;
        }
    }


    /**
     * @return Level1 Traits details object
      Parameters
      @$traitsDetail : Array of Level1 Traits detail from front
     */
    public function saveLevel1TraitsDetail($traitsDetail, $optionDetail) {
        $objTraits = new Level1Traits();
        $objOption = new Level1TraitsOptions();
        if ($traitsDetail['id'] != '' && $traitsDetail['id'] > 0) {
            $return = $objTraits->where('id', $traitsDetail['id'])->update($traitsDetail);
        } else {
            $return = $objTraits->create($traitsDetail);
        }

        if ($return) {
            if ($traitsDetail['id'] != '' && $traitsDetail['id'] > 0) {
                $id = $traitsDetail['id'];
                $deleted = $traitsDetail['deleted'];
            } else {
                $id = $return->id;
                $deleted = $return->deleted;
            }
        }

        $data = $objOption->where('tqq_id', $traitsDetail['id'])->get();
        $j = 0;
        $countOption = count($optionDetail['tqo_option']);
        $countData = count($data);
        $result = '';
        if ($countOption >= $countData) {
            for ($i = 0; $i < $countOption; ++$i) {
                $optionData = [];
                $optionData['tqq_id'] = $id;
                $optionData['tqo_option'] = $optionDetail['tqo_option'][$i];
                $optionData['deleted'] = $deleted;
                if ($j < count($data)) {
                    if ($traitsDetail['id'] != '' && $traitsDetail['id'] > 0) {
                        $result = $objOption->where('id', $data[$i]->id)->update($optionData);
                    }
                    $j++;
                } else {
                    $result = $objOption->create($optionData);
                }
            }
        } else {
            for ($i = 0; $i < count($data); ++$i) {
                for ($i = 0; $i < $countOption; ++$i) {
                    $optionData = [];
                    $optionData['tqq_id'] = $id;
                    $optionData['tqo_option'] = $optionDetail['tqo_option'][$i];
                    $optionData['deleted'] = $deleted;
                    if ($j < count($data) - 1) {
                        if ($traitsDetail['id'] != '' && $traitsDetail['id'] > 0) {
                            $result = $objOption->where('id', $data[$i]->id)->update($optionData);
                        }
                        $j++;
                    }
                }
                $result = $objOption->where('id', $data[$i]->id)->delete($optionData);
            }
        }
        return $result;
    }

    /**
     * Parameter : $teenagerId
     * Parameter : $section
     * return : Traits which is not attempted by teenager passed
    */
    public function getAllNotAttemptedTraits($teenagerId,$toUserID)
    {
        $activities = DB::select(DB::raw("SELECT tmp.*
                                            FROM (SELECT
                                                tqq.id AS activityID,
                                                tqq_text,
                                                tqq_points,
                                                tqq_image,
                                                tqq_is_multi_select,
                                                GROUP_CONCAT(tqo.id) AS optionIds,
                                                GROUP_CONCAT(tqo_option) AS options,
                                                tqq.deleted,
                                                count(*) as 'NoOfTotalQuestions'
                                                FROM
                                                " . config::get('databaseconstants.TBL_TRAITS_QUALITY_ACTIVITY') . " AS tqq
                                            INNER JOIN " . config::get('databaseconstants.TBL_TRAITS_QUALITY_OPTIONS') . " AS tqo ON tqo.tqq_id = tqq.id
                                            GROUP BY
                                                tqq.id) AS tmp
                                            LEFT JOIN " . config::get('databaseconstants.TBL_TRAITS_QUALITY_ANSWER') . " AS tqa ON tqa.tqq_id = tmp.activityID AND tqa.tqa_from = $teenagerId AND tqa.tqa_to = $toUserID
                                            WHERE tmp.deleted=1 and tqa.id IS NULL AND tqa.tqa_from IS NULL AND tqa.tqa_to IS NULL AND tqa.tqq_id IS NULL AND tqa.tqa_to IS NULL "), array());


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
     * Parameter : $section
     * return : Traits which is not attempted by teenager passed
    */
    public function getLastNotAttemptedTraits($teenagerId,$toUserID)
    {
        $activities = DB::select(DB::raw("SELECT tmp.*
                                            FROM (SELECT
                                                tqq.id AS activityID,
                                                tqq_text,
                                                tqq_points,
                                                tqq_image,
                                                tqq_is_multi_select,
                                                GROUP_CONCAT(tqo.id) AS optionIds,
                                                GROUP_CONCAT(tqo_option) AS options,
                                                tqq.deleted,
                                                count(*) as 'NoOfTotalQuestions'
                                                FROM
                                                " . config::get('databaseconstants.TBL_TRAITS_QUALITY_ACTIVITY') . " AS tqq
                                            INNER JOIN " . config::get('databaseconstants.TBL_TRAITS_QUALITY_OPTIONS') . " AS tqo ON tqo.tqq_id = tqq.id
                                            GROUP BY
                                                tqq.id) AS tmp
                                            LEFT JOIN " . config::get('databaseconstants.TBL_TRAITS_QUALITY_ANSWER') . " AS tqa ON tqa.tqq_id = tmp.activityID AND tqa.tqa_from = $teenagerId AND tqa.tqa_to = $toUserID
                                            WHERE tmp.deleted=1 and tqa.id IS NULL AND tqa.tqa_from IS NULL AND tqa.tqa_to IS NULL AND tqa.tqq_id IS NULL AND tqa.tqa_to IS NULL LIMIT 1"));


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
     * @return Level1 Traits details object
      Parameters
      @$traitsAnswerDetail : Array of Level1 Traits Answer Details from front
     */
    public function saveLevel1TraitsAnswer($traitsAnswerDetail) {
        $objLevel1TraitsAnswers = new Level1TraitsAnswers();
        $checkIfAnswered = $objLevel1TraitsAnswers->where([['tqq_id','=',$traitsAnswerDetail['tqq_id']],['tqo_id','=',$traitsAnswerDetail['tqo_id']],['tqa_from','=',$traitsAnswerDetail['tqa_from']],['tqa_to','=',$traitsAnswerDetail['tqa_to']]])->count();
        $return = 1;
        if ($checkIfAnswered == 0){
            $return = $objLevel1TraitsAnswers->create($traitsAnswerDetail);
        }
        return $return;
    }

    /**
     * @return Teenager Trait Answer 
      Parameters
      @$teenagerId : TeenagerId
     */
    public function getTeenagerTraitAnswerCount($teenagerId) {
        $data = DB::table('pro_tqa_traits_quality_answer As trait_answer')
                ->join('pro_tqo_traits_quality_options As trait_option', 'trait_option.id', '=', 'trait_answer.tqo_id')
                ->select('trait_answer.*', DB::raw('count(trait_answer.tqo_id) AS options_count'), 'trait_option.tqo_option As options_text')
                ->where('trait_answer.tqa_to', $teenagerId)
                ->groupBy('trait_answer.tqo_id')
                ->get();
        return $data;
    }    
}
