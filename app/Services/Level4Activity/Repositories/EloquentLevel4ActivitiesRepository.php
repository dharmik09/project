<?php

namespace App\Services\Level4Activity\Repositories;

use DB;
use Helpers;
use Config;
use App\Level4Answers;
use App\Level4Options;
use App\Services\Level4Activity\Contracts\Level4ActivitiesRepository;
use App\Services\Repositories\Eloquent\EloquentBaseRepository;
use App\Level4ParentAnswers;
use Illuminate\Support\Facades\Storage;

class EloquentLevel4ActivitiesRepository extends EloquentBaseRepository implements Level4ActivitiesRepository {

    public function getLevel4Details($searchParamArray = array()) {

        $whereStr = '';
        $orderStr = '';

        $whereArray = [];
        $whereArray[] = 'activity.deleted IN (1,2)';
        //$groupBy = " GROUP BY activity.id";

        if (isset($searchParamArray) && !empty($searchParamArray)) {
            if (isset($searchParamArray['searchBy']) && isset($searchParamArray['searchText']) && $searchParamArray['searchBy'] != '' && $searchParamArray['searchText'] != '') {
                $whereArray[] = $searchParamArray['searchBy'] . " LIKE '%" . $searchParamArray['searchText'] . "%'";
            }

            if (isset($searchParamArray['orderBy']) && isset($searchParamArray['sortOrder']) && $searchParamArray['orderBy'] != '' && $searchParamArray['sortOrder'] != '') {
                $orderStr = " ORDER BY " . $searchParamArray['orderBy'] . " " . $searchParamArray['sortOrder'];
            }
        }

        if (!empty($whereArray)) {
            $whereStr = implode(" AND ", $whereArray);
        }

        $leve4activities = DB::table(config::get('databaseconstants.TBL_LEVEL4_BASIC_ACTIVITY') . " AS activity")
                ->leftjoin(config::get('databaseconstants.TBL_PROFESSIONS') . " AS profession", 'activity.profession_id', '=', 'profession.id')
                ->leftjoin(config::get('databaseconstants.TBL_LEVEL4_OPTIONS') . " AS options", 'activity.id', '=', 'options.activity_id')
                ->selectRaw('activity.* , GROUP_CONCAT(options.options_text SEPARATOR "#") AS options_text, GROUP_CONCAT(options.correct_option) AS correct_option, profession.pf_name')
                ->whereRaw($whereStr)
                ->groupBy('activity.id')
                ->paginate(Config::get('constant.ADMIN_RECORD_PER_PAGE'));

        return $leve4activities;
    }

    public function getLevel4DetailsDataObj() {
        $leve4activities = DB::table(config::get('databaseconstants.TBL_LEVEL4_BASIC_ACTIVITY') . " AS activity")
                ->leftjoin(config::get('databaseconstants.TBL_PROFESSIONS') . " AS profession", 'activity.profession_id', '=', 'profession.id')
                ->leftjoin(config::get('databaseconstants.TBL_LEVEL4_OPTIONS') . " AS options", 'activity.id', '=', 'options.activity_id')
                ->selectRaw('activity.* , GROUP_CONCAT(options.options_text SEPARATOR "#") AS options_text, GROUP_CONCAT(options.correct_option) AS correct_option, profession.pf_name')
                ->whereIn('activity.deleted', [1,2])
                ->groupBy('activity.id');
        return $leve4activities;
    }

    public function saveLevel4Question($array) {
        $id = '';
        if (isset($array) && !empty($array)) {
            $id = DB::table(config::get('databaseconstants.TBL_LEVEL4_BASIC_ACTIVITY'))->insertGetId($array);
        }
        return $id;
    }

    public function saveLevel4Options($arrayOptions) {
        $arrayData = [];
        $saveQuestionOption = '';
        if (isset($arrayOptions) && !empty($arrayOptions)) {
            if (isset($arrayOptions['options_text']) && !empty($arrayOptions['options_text'])) {
                foreach ($arrayOptions['options_text'] as $key => $option) {
                    if ($option != '') {
                        $arrayData['correct_option'] = (in_array($key, $arrayOptions['correct_option'])) ? 1 : 0;
                        $arrayData['options_text'] = $option;
                        $arrayData['activity_id'] = $arrayOptions['activity_id'];
                        $arrayData['deleted'] = 1;
                        // Insert option one by one
                        $saveQuestionOption = DB::table(config::get('databaseconstants.TBL_LEVEL4_BASIC_ACTIVITY_OPTIONS'))->insertGetId($arrayData);
                    }
                }
            }
        }
        return $saveQuestionOption;
    }

    /**
     * Parameter : $teenagerId
     * return : array/object of the activities which are not attempted by teenager passed
     */
    public function getNotAttemptedActivities($teenagerId, $professionId) {
        $activities = DB::select(DB::raw("SELECT tmp.*
                                            FROM (SELECT L4AC.id AS activityID, question_text, points, type,
                                                timer, profession_id, GROUP_CONCAT(L4OP.id) AS optionIds,
                                                GROUP_CONCAT(L4OP.correct_option) AS correctOption,
                                              GROUP_CONCAT(options_text SEPARATOR '#') AS options,
                                                L4AC.deleted, count(*) as 'NoOfQ' FROM
                                              " . config::get('databaseconstants.TBL_LEVEL4_ACTIVITY') . " AS L4AC
                                                INNER JOIN " . config::get('databaseconstants.TBL_LEVEL4_OPTIONS') . " AS L4OP ON L4OP.activity_id = L4AC.id
                                                GROUP BY
                                              L4AC.id) AS tmp
                                                LEFT JOIN " . config::get('databaseconstants.TBL_LEVEL4_ANSWERS') . " AS L4ANS ON L4ANS.activity_id = tmp.activityID AND L4ANS.teenager_id = $teenagerId
                                                WHERE tmp.profession_id = $professionId and  tmp.deleted=1 and L4ANS.id IS NULL AND L4ANS.teenager_id IS NULL AND L4ANS.activity_id IS NULL AND L4ANS.answer_id IS NULL"), array());

        if (isset($activities) && !empty($activities)) {
            foreach ($activities as $key => $activity) {
                $optionIds = explode(",", $activity->optionIds);
                $correctOption = explode(",", $activity->correctOption);
                $multiOption = array_count_values($correctOption);
                $multiOptionCount = (isset($multiOption['1']))?$multiOption['1'] : 0;
                $activities[$key]->totalCorrectOptions = $multiOptionCount;
                $options = explode("#", $activity->options);
                unset($activity->optionIds);
                unset($activity->options);

                $optionsWithId = [];

                foreach ($options as $key1 => $option) {
                    $temp = [];
                    $temp['optionId'] = $optionIds[$key1];
                    $temp['optionText'] = $option;
                    $temp['correctOption'] = $correctOption[$key1];
                    $optionsWithId[] = $temp;
                }
                $activities[$key]->options = $optionsWithId;
            }
        } else {
            $activities = '';
        }

        return $activities;
    }

    /*
     * Get not attempted intermediate activities
     */

    public function getNotAttemptedIntermediateActivities($teenagerId, $professionId, $templateId) {
        $activities = DB::select(DB::raw("SELECT tmp.*
                                            FROM (SELECT L4_I_AC.id AS activityID, l4ia_shuffle_options, l4ia_options_metrix, l4ia_question_description, l4ia_question_answer_description, l4ia_question_text, l4ia_question_point, l4ia_question_template,
                                                l4ia_question_time, l4ia_profession_id,l4ia_question_popup_image,l4ia_question_audio,l4ia_question_popup_description, GROUP_CONCAT(L4_I_OP.id) AS optionIds,
                                                GROUP_CONCAT(L4_I_OP.l4iao_correct_answer SEPARATOR '###') AS correctOption,
                                                GROUP_CONCAT(L4_I_OP.l4iao_answer_order) AS correctOrder,
                                                GROUP_CONCAT(L4_I_OP.l4iao_answer_image SEPARATOR '###') AS optionAsImage,
                                                GROUP_CONCAT(L4_I_OP.l4iao_answer_image_description SEPARATOR '###') AS optionImageText,
                                                GROUP_CONCAT(L4_I_OP.l4iao_answer_response_text SEPARATOR '###') AS optionResponseText,
                                                GROUP_CONCAT(L4_I_OP.l4iao_answer_response_image SEPARATOR '###') AS optionResponseImage,
                                              GROUP_CONCAT(l4iao_answer_text SEPARATOR '###') AS options,
                                                L4_I_AC.deleted,
                                                count(*) as 'NoOfQ' FROM
                                              " . config::get('databaseconstants.TBL_LEVEL4_INTERMEDIATE_ACTIVITY') . " AS L4_I_AC
                                                INNER JOIN " . config::get('databaseconstants.TBL_LEVEL4_INTERMEDIATE_ACTIVITY_OPTIONS') . " AS L4_I_OP ON L4_I_OP.l4iao_question_id = L4_I_AC.id
                                                GROUP BY L4_I_AC.id) AS tmp
                                                LEFT JOIN " . config::get('databaseconstants.TBL_LEVEL4_INTERMEDIATE_ACTIVITY_ANSWER') . " AS L4_I_ANS ON L4_I_ANS.l4iaua_activity_id = tmp.activityID AND L4_I_ANS.l4iaua_teenager = $teenagerId
                                                WHERE tmp.l4ia_question_template = $templateId and tmp.l4ia_profession_id = $professionId and  tmp.deleted=1 and L4_I_ANS.id IS NULL AND L4_I_ANS.l4iaua_teenager IS NULL AND L4_I_ANS.l4iaua_activity_id IS NULL AND L4_I_ANS.l4iaua_answer IS NULL"), array());
        if (isset($activities) && !empty($activities)) {
            shuffle($activities);
            foreach ($activities as $key => $activity) {
                $optionIds = (isset($activity->optionIds) && $activity->optionIds != '') ? explode(",", $activity->optionIds) : '';
                $correctOption = explode("###", $activity->correctOption);
                $optionAsImage = explode("###", $activity->optionAsImage);
                $correctOrder = explode(",", $activity->correctOrder);

                $optionImageText = explode("###", $activity->optionImageText);
                $optionResponseText = explode("###", $activity->optionResponseText);
                $optionResponseImage = explode("###", $activity->optionResponseImage);


                $multiOption = array_count_values($correctOption);
                $multiOptionCount = (isset($multiOption['1'])) ? $multiOption['1'] : '';
                $activities[$key]->totalCorrectOptions = ($multiOptionCount != '')? $multiOptionCount : 0;
                $options = (isset($activity->options) && $activity->options != '') ? explode("###", $activity->options) : '';
                unset($activity->optionIds);
                unset($activity->options);
                unset($activity->optionResponseImage);
                unset($activity->optionResponseText);
                unset($activity->optionImageText);
                unset($activity->optionAsImage);
                $ext = '';
                $optionsWithId = [];
                if (isset($options) && !empty($options)) {
                    foreach ($options as $key1 => $option) {
                        $temp = [];
                        $temp['optionId'] = $optionIds[$key1];
                        $temp['optionText'] = (isset($option)) ? $option : '';
                        $temp['correctOption'] = (isset($correctOption[$key1])) ? $correctOption[$key1] : 0;
                        $temp['correctOrder'] = (isset($correctOrder[$key1])) ? $correctOrder[$key1] : 0;
                        $temp['optionImageText'] = (isset($optionImageText[$key1])) ? $optionImageText[$key1] : '';
                        $temp['optionResponseText'] = (isset($optionResponseText[$key1])) ? $optionResponseText[$key1] : '';

                        if (isset($optionAsImage[$key1]) && $optionAsImage[$key1] != '') {
                            $ext = strtolower(pathinfo($optionAsImage[$key1], PATHINFO_EXTENSION)); 
                            if($ext == 'gif' && $optionAsImage[$key1] != '' && file_exists(Config::get('constant.LEVEL4_INTERMEDIATE_ANSWER_ORIGINAL_IMAGE_UPLOAD_PATH') . $optionAsImage[$key1])){
                                $temp['optionAsImage'] = asset(Config::get('constant.LEVEL4_INTERMEDIATE_ANSWER_ORIGINAL_IMAGE_UPLOAD_PATH') . $optionAsImage[$key1]);
                            }
                            else if($optionAsImage[$key1] != '' && file_exists(Config::get('constant.LEVEL4_INTERMEDIATE_ANSWER_THUMB_IMAGE_UPLOAD_PATH') . $optionAsImage[$key1])) {
                                $temp['optionAsImage'] = asset(Config::get('constant.LEVEL4_INTERMEDIATE_ANSWER_THUMB_IMAGE_UPLOAD_PATH') . $optionAsImage[$key1]);
                            } else {
                                $temp['optionAsImage'] = asset(Config::get('constant.LEVEL4_INTERMEDIATE_ANSWER_THUMB_IMAGE_UPLOAD_PATH') . "proteen-logo.png");
                            }
                        } else {
                            $temp['optionAsImage'] = '';
                        }
                        if (isset($optionResponseImage[$key1]) && $optionResponseImage[$key1] != '') {
                            $ext = strtolower(pathinfo($optionResponseImage[$key1], PATHINFO_EXTENSION)); 
                            if($ext == 'gif' && $optionResponseImage[$key1] != '' && file_exists(Config::get('constant.LEVEL4_INTERMEDIATE_RESPONSE_ORIGINAL_IMAGE_UPLOAD_PATH') . $optionResponseImage[$key1])){
                                $temp['optionResponseImage'] = asset(Config::get('constant.LEVEL4_INTERMEDIATE_RESPONSE_ORIGINAL_IMAGE_UPLOAD_PATH') . $optionResponseImage[$key1]);
                            }
                            if ($optionResponseImage[$key1] != '' && file_exists(Config::get('constant.LEVEL4_INTERMEDIATE_RESPONSE_THUMB_IMAGE_UPLOAD_PATH') . $optionResponseImage[$key1])) {
                                $temp['optionResponseImage'] = asset(Config::get('constant.LEVEL4_INTERMEDIATE_RESPONSE_THUMB_IMAGE_UPLOAD_PATH') . $optionResponseImage[$key1]);
                            } else {
                                $temp['optionResponseImage'] = asset(Config::get('constant.LEVEL4_INTERMEDIATE_RESPONSE_THUMB_IMAGE_UPLOAD_PATH') . "proteen-logo.png");
                            }
                        } else {
                            $temp['optionResponseImage'] = '';
                        }
                        $optionsWithId[] = $temp;
                    }
                }
                $activities[$key]->options = $optionsWithId;
            }
        } else {
            $activities = '';
        }
        return $activities;
    }

    /**
     * Parameter : $teenagerId
     * Parameter : $teenagerId
     * return : array/object of the activities which are attempted by teenager passed and total no of questions
     */
    public function getNoOfTotalQuestionsAttemptedQuestion($teenagerId, $professionId) {
        //$result = DB::select(DB::raw("select (SELECT count(*) FROM " . config::get('databaseconstants.TBL_LEVEL4_ACTIVITY') . " where deleted=1 and profession_id = $professionId ) as 'NoOfTotalQuestions', (select count(DISTINCT(L4_ANS.activity_id)) from " . config::get('databaseconstants.TBL_LEVEL4_ACTIVITY') . " AS L4_AC join " . config::get('databaseconstants.TBL_LEVEL4_ANSWERS') . " AS L4_ANS on L4_AC.id = L4_ANS.activity_id  where L4_ANS.teenager_id=" . $teenagerId . " AND L4_AC.profession_id = " . $professionId . " ) as 'NoOfAttemptedQuestions' "), array());
        $result = DB::select(DB::raw("select (SELECT count(DISTINCT(l4_aa.id)) FROM ".config::get('databaseconstants.TBL_LEVEL4_ACTIVITY')." AS l4_aa join ".config::get('databaseconstants.TBL_LEVEL4_OPTIONS')." AS l4_an on l4_aa.id = l4_an.activity_id where l4_aa.deleted=1 and l4_aa.profession_id = $professionId ) as 'NoOfTotalQuestions', (select count(DISTINCT(L4_ANS.activity_id)) from " . config::get('databaseconstants.TBL_LEVEL4_ACTIVITY') . " AS L4_AC join " . config::get('databaseconstants.TBL_LEVEL4_ANSWERS') . " AS L4_ANS on L4_AC.id = L4_ANS.activity_id  where L4_ANS.teenager_id=" . $teenagerId . " AND L4_AC.profession_id = " . $professionId . " ) as 'NoOfAttemptedQuestions' "), array());
        return $result;
    }

    /*
     * getNotAttemptedIntermediateActivities for intermediate question of profession
     */

    public function getNoOfTotalIntermediateQuestionsAttemptedQuestion($teenagerId, $professionId, $templateId) {
        $result = DB::select(DB::raw("select (SELECT count(DISTINCT(l4_ic.id)) FROM ".config::get('databaseconstants.TBL_LEVEL4_INTERMEDIATE_ACTIVITY')." AS l4_ic join ".config::get('databaseconstants.TBL_LEVEL4_INTERMEDIATE_ACTIVITY_OPTIONS')." AS l4_ao on l4_ic.id = l4_ao.l4iao_question_id where l4_ic.deleted=1 AND l4_ic.l4ia_profession_id = ".$professionId." AND l4_ic.l4ia_question_template = ".$templateId.") as 'NoOfTotalQuestions', (select count(DISTINCT(L4_I_ANS.l4iaua_activity_id)) from " . config::get('databaseconstants.TBL_LEVEL4_INTERMEDIATE_ACTIVITY') . " AS L4_I_AC join " . config::get('databaseconstants.TBL_LEVEL4_INTERMEDIATE_ACTIVITY_ANSWER') . " AS L4_I_ANS on L4_I_AC.id = L4_I_ANS.l4iaua_activity_id  where L4_I_AC.deleted=1 AND L4_I_ANS.l4iaua_teenager=" . $teenagerId . " AND L4_I_AC.l4ia_profession_id = " . $professionId . " AND L4_I_AC.l4ia_question_template=" . $templateId . ") as 'NoOfAttemptedQuestions' "), array());
        return $result;
    }

    /*
     * Level 4 getQuestionTemplateForProfession
     */

    public function getQuestionTemplateForProfession($professionId) {
        $result = DB::table(config::get('databaseconstants.TBL_LEVEL4_INTERMEDIATE_ACTIVITY') . " AS activity")
                ->distinct()
                ->leftjoin(config::get('databaseconstants.TBL_GAMIFICATION_TEMPLATE') . " AS template", 'activity.l4ia_question_template', '=', 'template.id')
                ->selectRaw('activity.l4ia_profession_id , template.gt_temlpate_answer_type, template.gt_template_title, template.gt_template_image as gt_template_image,template.id as gt_template_id, template.gt_template_title, template.gt_template_descritpion, template.gt_template_descritpion_popup_imge,template.gt_coins')
                ->where('activity.l4ia_profession_id', $professionId)
                ->where('template.deleted', 1)
                ->where('activity.deleted', 1)
                ->get();

        if(isset($result) && !empty($result)){
            foreach($result as $key => $value){
                if(isset($value->gt_template_image) && $value->gt_template_image != ''){
                    $value->gt_template_image = Config::get('constant.CONCEPT_ORIGINAL_IMAGE_UPLOAD_PATH') . $value->gt_template_image;
                }else{
                    $value->gt_template_image = Config::get('constant.CONCEPT_ORIGINAL_IMAGE_UPLOAD_PATH') .  "proteen-logo.png";
                }
                if($value->gt_template_descritpion_popup_imge != ''){
                    if(file_exists(Config::get('constant.CONCEPT_ORIGINAL_IMAGE_UPLOAD_PATH') . $value->gt_template_descritpion_popup_imge)){
                        $value->gt_template_descritpion_popup_imge = asset(Config::get('constant.CONCEPT_ORIGINAL_IMAGE_UPLOAD_PATH') . $value->gt_template_descritpion_popup_imge);
                    }else{
                        $value->gt_template_descritpion_popup_imge = '';
                    }
                }else{
                    $value->gt_template_descritpion_popup_imge = '';
                }
            }
        }else{
            $result = [];
        }

        return $result;
    }

    public function getQuestionDescriptionImage($activityId) {
        $mediaData = [];
        $image = DB::table(config::get('databaseconstants.TBL_LEVEL4_INTERMEDIATE_ACTIVITY_MEDIA'))->where(['l4iam_question_id' => $activityId, 'l4iam_media_type' => "I"])->first();
        if (isset($image) && !empty($image)) {
            $mediaData['image'] = $image->l4iam_media_name;
        }
        return $mediaData;
    }

    public function getQuestionImage($activityId) {
        $mediaData = [];
        $image = DB::table(config::get('databaseconstants.TBL_LEVEL4_INTERMEDIATE_ACTIVITY_MEDIA'))->where(['l4iam_question_id' => $activityId, 'l4iam_media_type' => "I"])->first();
        if (isset($image) && !empty($image)) {
            $mediaData['image'] = $image->l4iam_media_name;
            $mediaData['imageDescription'] = $image->l4iam_media_desc;
        }
        return $mediaData;
    }

    public function getQuestionMultipleImages($activityId) {
        $mediaData = [];
        $QuestionImages = DB::table(config::get('databaseconstants.TBL_LEVEL4_INTERMEDIATE_ACTIVITY_MEDIA'))->where(['l4iam_question_id' => $activityId, 'l4iam_media_type' => "I"])->get();
        if (isset($QuestionImages) && !empty($QuestionImages)) {
            foreach($QuestionImages as $key=>$image){
                $mediaData[$key]['image'] = $image->l4iam_media_name;
                $mediaData[$key]['imageDescription'] = $image->l4iam_media_desc;
            }
        }
        return $mediaData;
    }

    public function getQuestionVideo($activityId) {
        $mediaData = [];
        $video = DB::table(config::get('databaseconstants.TBL_LEVEL4_INTERMEDIATE_ACTIVITY_MEDIA'))->where(['l4iam_question_id' => $activityId, 'l4iam_media_type' => "V"])->first();
        if (isset($video) && !empty($video)) {
            $mediaData['video'] = $video->l4iam_media_name;
        }
        return $mediaData;
    }

    public function getAnswerResponseTextAndImage($answerId) {
        $mediaData = [];
        $data = DB::table(config::get('databaseconstants.TBL_LEVEL4_INTERMEDIATE_ACTIVITY_OPTIONS'))->where(['id' => $answerId, 'deleted' => 1])->first();
        if (isset($data) && !empty($data)) {
            $mediaData['answerResponseImage'] = $data->l4iao_answer_response_image;
            $mediaData['answerResponseText'] = $data->l4iao_answer_response_text;
        }
        return $mediaData;
    }

    public function getOptionTextFromOptionId($optionId) {
        $optionData = DB::table(config::get('databaseconstants.TBL_LEVEL4_INTERMEDIATE_ACTIVITY_OPTIONS'))->where(['id' => $optionId, 'deleted' => "1"])->first();
        if (isset($optionData) && !empty($optionData)) {
            $optionData2['optionText'] = $optionData->l4iao_answer_text;
            $optionData2['optionId'] = $optionId;
        }else{
            $optionData2['optionText'] = '';
            $optionData2['optionId'] = '';
        }
        return $optionData2;
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
            $row['teenager_id'] = $teenagerId;
            $row['activity_id'] = $response['questionID'];
            //$row['answer_id'] = $response['answerID'];
            $answerArray = explode(',', $response['answerID']);
            $row['earned_points'] = $response['earned_points'];
            $objLevel4Answers = new Level4Answers();


            foreach ($answerArray as $ansId) {
                $row['answer_id'] = $ansId;
                $answered = $objLevel4Answers->where("teenager_id", $teenagerId)->where("activity_id", $response['questionID'])->where("answer_id", $row['answer_id'])->first();
                if ($answered) {
                    $answered = $answered->toArray();
                    $objLevel4Answers->where('id', $answered['id'])->update($row);
                } else {
                    $res = $objLevel4Answers->create($row);
                    if ($res) {

                    }
                }
            }
            $points += $response['earned_points'];

            $questionsID[] = $response['questionID'];
            $deductPointsFromBoosterPoints = Helpers::deductTeenagerPoints($teenagerId, config::get('constant.LEVEL4_DEDUCT_POINTS'));
        }

        $teenagerLevel4PointsRow = [];
        $teenagerLevel4PointsRow['tlb_teenager'] = $teenagerId;
        $teenagerLevel4PointsRow['tlb_level'] = config::get('constant.LEVEL4_ID');
        $teenagerLevel4PointsRow['tlb_profession'] = $response['profession_id'];

        $teenagerLevelPoints = DB::table(config::get('databaseconstants.TBL_TEENAGER_LEVEL_BOOSTERS'))->where(["tlb_teenager" => $teenagerId, "tlb_level" => config::get('constant.LEVEL4_ID'), "tlb_profession" => $response['profession_id']])->first();
        if ($teenagerLevelPoints) {
            $teenagerLevelPoints = (array) $teenagerLevelPoints;
            $teenagerLevel4PointsRow['tlb_points'] = $teenagerLevelPoints['tlb_points'] + $points;
            DB::table(config::get('databaseconstants.TBL_TEENAGER_LEVEL_BOOSTERS'))->where('id', $teenagerLevelPoints['id'])->update($teenagerLevel4PointsRow);
        } else {
            $teenagerLevel4PointsRow['tlb_points'] = $points;
            DB::table(config::get('databaseconstants.TBL_TEENAGER_LEVEL_BOOSTERS'))->insert($teenagerLevel4PointsRow);
        }

        $returnArray = [];
        $returnArray['questionsID'] = $questionsID;
        $returnArray['total_Points'] = $points;

        return $returnArray;
    }

    public function saveTeenagerIntermediateActivitySingleLineAnswer($teenagerId, $data) {

        $teenagerIntermediateAnswer = DB::table(config::get('databaseconstants.TBL_LEVEL4_INTERMEDIATE_ACTIVITY_ANSWER'))->where(["l4iaua_teenager" => $data['l4iaua_teenager'], "l4iaua_activity_id" => $data['l4iaua_activity_id'], "l4iaua_profession_id" => $data['l4iaua_profession_id'], "l4iaua_template_id" => $data['l4iaua_template_id']])->first();
        if (isset($teenagerIntermediateAnswer) && !empty($teenagerIntermediateAnswer)) {
            DB::table(config::get('databaseconstants.TBL_LEVEL4_INTERMEDIATE_ACTIVITY_ANSWER'))->where('id', $teenagerIntermediateAnswer->id)->update($data);
        } else {
            DB::table(config::get('databaseconstants.TBL_LEVEL4_INTERMEDIATE_ACTIVITY_ANSWER'))->insert($data);
            $deductPointsFromBoosterPoints = Helpers::deductTeenagerPoints($teenagerId, config::get('constant.LEVEL4_DEDUCT_POINTS'));
        }

        $teenagerLevel4PointsRow = [];
        $teenagerLevel4PointsRow['tlb_teenager'] = $teenagerId;
        $teenagerLevel4PointsRow['tlb_level'] = config::get('constant.LEVEL4_ID');
        $teenagerLevel4PointsRow['tlb_profession'] = $data['l4iaua_profession_id'];

        $teenagerLevelPoints = DB::table(config::get('databaseconstants.TBL_TEENAGER_LEVEL_BOOSTERS'))->where(["tlb_teenager" => $teenagerId, "tlb_level" => config::get('constant.LEVEL4_ID'), "tlb_profession" => $data['l4iaua_profession_id']])->first();

        if ($teenagerLevelPoints) {
            $teenagerLevelPoints = (array) $teenagerLevelPoints;
            $teenagerLevel4PointsRow['tlb_points'] = $teenagerLevelPoints['tlb_points'] + $data['l4iaua_earned_point'];
            DB::table(config::get('databaseconstants.TBL_TEENAGER_LEVEL_BOOSTERS'))->where('id', $teenagerLevelPoints['id'])->update($teenagerLevel4PointsRow);
        } else {
            $teenagerLevel4PointsRow['tlb_points'] = $data['l4iaua_earned_point'];
            DB::table(config::get('databaseconstants.TBL_TEENAGER_LEVEL_BOOSTERS'))->insert($teenagerLevel4PointsRow);
        }
        $returnArray = [];
        $returnArray['questionsID'] = $data['l4iaua_activity_id'];
        $returnArray['total_Points'] = $data['l4iaua_earned_point'];
        return $returnArray;
    }

    public function saveTeenagerIntermediateActivityDropDownAnswer($teenagerId, $data) {

        $teenagerIntermediateAnswer = DB::table(config::get('databaseconstants.TBL_LEVEL4_INTERMEDIATE_ACTIVITY_ANSWER'))->where(["l4iaua_teenager" => $data['l4iaua_teenager'], "l4iaua_activity_id" => $data['l4iaua_activity_id'], "l4iaua_profession_id" => $data['l4iaua_profession_id'], "l4iaua_template_id" => $data['l4iaua_template_id']])->first();
        if (isset($teenagerIntermediateAnswer) && !empty($teenagerIntermediateAnswer)) {
            DB::table(config::get('databaseconstants.TBL_LEVEL4_INTERMEDIATE_ACTIVITY_ANSWER'))->where('id', $teenagerIntermediateAnswer->id)->update($data);
        } else {
            DB::table(config::get('databaseconstants.TBL_LEVEL4_INTERMEDIATE_ACTIVITY_ANSWER'))->insert($data);
            $deductPointsFromBoosterPoints = Helpers::deductTeenagerPoints($teenagerId, config::get('constant.LEVEL4_DEDUCT_POINTS'));
        }

        $teenagerLevel4PointsRow = [];
        $teenagerLevel4PointsRow['tlb_teenager'] = $teenagerId;
        $teenagerLevel4PointsRow['tlb_level'] = config::get('constant.LEVEL4_ID');
        $teenagerLevel4PointsRow['tlb_profession'] = $data['l4iaua_profession_id'];

        $teenagerLevelPoints = DB::table(config::get('databaseconstants.TBL_TEENAGER_LEVEL_BOOSTERS'))->where(["tlb_teenager" => $teenagerId, "tlb_level" => config::get('constant.LEVEL4_ID'), "tlb_profession" => $data['l4iaua_profession_id']])->first();

        if ($teenagerLevelPoints) {
            $teenagerLevelPoints = (array) $teenagerLevelPoints;
            $teenagerLevel4PointsRow['tlb_points'] = $teenagerLevelPoints['tlb_points'] + $data['l4iaua_earned_point'];
            DB::table(config::get('databaseconstants.TBL_TEENAGER_LEVEL_BOOSTERS'))->where('id', $teenagerLevelPoints['id'])->update($teenagerLevel4PointsRow);
        } else {
            $teenagerLevel4PointsRow['tlb_points'] = $data['l4iaua_earned_point'];
            DB::table(config::get('databaseconstants.TBL_TEENAGER_LEVEL_BOOSTERS'))->insert($teenagerLevel4PointsRow);
        }
        $returnArray = [];
        $returnArray['questionsID'] = $data['l4iaua_activity_id'];
        $returnArray['total_Points'] = $data['l4iaua_earned_point'];
        return $returnArray;
    }

    public function saveTeenagerIntermediateActivityFillInBlanksAnswer($teenagerId, $data, $answer) {
        foreach ($answer as $detail) {
            $data['l4iaua_answer'] = $detail;
            $teenagerIntermediateAnswer = DB::table(config::get('databaseconstants.TBL_LEVEL4_INTERMEDIATE_ACTIVITY_ANSWER'))->where(["l4iaua_teenager" => $data['l4iaua_teenager'], "l4iaua_activity_id" => $data['l4iaua_activity_id'], "l4iaua_profession_id" => $data['l4iaua_profession_id'], "l4iaua_template_id" => $data['l4iaua_template_id'], "l4iaua_answer" => $data['l4iaua_answer']])->first();
            if (isset($teenagerIntermediateAnswer) && !empty($teenagerIntermediateAnswer)) {
                DB::table(config::get('databaseconstants.TBL_LEVEL4_INTERMEDIATE_ACTIVITY_ANSWER'))->where('id', $teenagerIntermediateAnswer->id)->update($data);
            } else {
                DB::table(config::get('databaseconstants.TBL_LEVEL4_INTERMEDIATE_ACTIVITY_ANSWER'))->insert($data);
            }
        }

        $deductPointsFromBoosterPoints = Helpers::deductTeenagerPoints($teenagerId, config::get('constant.LEVEL4_DEDUCT_POINTS'));
        $teenagerLevel4PointsRow = [];
        $teenagerLevel4PointsRow['tlb_teenager'] = $teenagerId;
        $teenagerLevel4PointsRow['tlb_level'] = config::get('constant.LEVEL4_ID');
        $teenagerLevel4PointsRow['tlb_profession'] = $data['l4iaua_profession_id'];

        $teenagerLevelPoints = DB::table(config::get('databaseconstants.TBL_TEENAGER_LEVEL_BOOSTERS'))->where(["tlb_teenager" => $teenagerId, "tlb_level" => config::get('constant.LEVEL4_ID'), "tlb_profession" => $data['l4iaua_profession_id']])->first();

        if ($teenagerLevelPoints) {
            $teenagerLevelPoints = (array) $teenagerLevelPoints;
            $teenagerLevel4PointsRow['tlb_points'] = $teenagerLevelPoints['tlb_points'] + $data['l4iaua_earned_point'];
            DB::table(config::get('databaseconstants.TBL_TEENAGER_LEVEL_BOOSTERS'))->where('id', $teenagerLevelPoints['id'])->update($teenagerLevel4PointsRow);
        } else {
            $teenagerLevel4PointsRow['tlb_points'] = $data['l4iaua_earned_point'];
            DB::table(config::get('databaseconstants.TBL_TEENAGER_LEVEL_BOOSTERS'))->insert($teenagerLevel4PointsRow);
        }
        $returnArray = [];
        $returnArray['questionsID'] = $data['l4iaua_activity_id'];
        $returnArray['total_Points'] = $data['l4iaua_earned_point'];
        return $returnArray;
    }

    public function saveTeenagerIntermediateActivityImageReorderAnswer($teenagerId, $data, $answer) {
        foreach ($answer as $key => $detail) {
            $data['l4iaua_answer'] = $detail;
            $data['l4iaua_order'] = $key;
            $teenagerIntermediateAnswer = DB::table(config::get('databaseconstants.TBL_LEVEL4_INTERMEDIATE_ACTIVITY_ANSWER'))->where(["l4iaua_teenager" => $data['l4iaua_teenager'], "l4iaua_activity_id" => $data['l4iaua_activity_id'], "l4iaua_profession_id" => $data['l4iaua_profession_id'], "l4iaua_template_id" => $data['l4iaua_template_id'], "l4iaua_answer" => $data['l4iaua_answer']])->first();
            if (isset($teenagerIntermediateAnswer) && !empty($teenagerIntermediateAnswer)) {
                DB::table(config::get('databaseconstants.TBL_LEVEL4_INTERMEDIATE_ACTIVITY_ANSWER'))->where('id', $teenagerIntermediateAnswer->id)->update($data);
            } else {
                DB::table(config::get('databaseconstants.TBL_LEVEL4_INTERMEDIATE_ACTIVITY_ANSWER'))->insert($data);
            }
        }

        $deductPointsFromBoosterPoints = Helpers::deductTeenagerPoints($teenagerId, config::get('constant.LEVEL4_DEDUCT_POINTS'));
        $teenagerLevel4PointsRow = [];
        $teenagerLevel4PointsRow['tlb_teenager'] = $teenagerId;
        $teenagerLevel4PointsRow['tlb_level'] = config::get('constant.LEVEL4_ID');
        $teenagerLevel4PointsRow['tlb_profession'] = $data['l4iaua_profession_id'];

        $teenagerLevelPoints = DB::table(config::get('databaseconstants.TBL_TEENAGER_LEVEL_BOOSTERS'))->where(["tlb_teenager" => $teenagerId, "tlb_level" => config::get('constant.LEVEL4_ID'), "tlb_profession" => $data['l4iaua_profession_id']])->first();

        if ($teenagerLevelPoints) {
            $teenagerLevelPoints = (array) $teenagerLevelPoints;
            $teenagerLevel4PointsRow['tlb_points'] = $teenagerLevelPoints['tlb_points'] + $data['l4iaua_earned_point'];
            DB::table(config::get('databaseconstants.TBL_TEENAGER_LEVEL_BOOSTERS'))->where('id', $teenagerLevelPoints['id'])->update($teenagerLevel4PointsRow);
        } else {
            $teenagerLevel4PointsRow['tlb_points'] = $data['l4iaua_earned_point'];
            DB::table(config::get('databaseconstants.TBL_TEENAGER_LEVEL_BOOSTERS'))->insert($teenagerLevel4PointsRow);
        }
        $returnArray = [];
        $returnArray['questionsID'] = $data['l4iaua_activity_id'];
        $returnArray['total_Points'] = $data['l4iaua_earned_point'];
        return $returnArray;
    }

    /*
     * get ProfessionId From QuestionId
     */

    public function getProfessionIdFromQuestionId($questionID) {
        $professionid = DB::table(config::get('databaseconstants.TBL_LEVEL4_ACTIVITY'))->where('id', $questionID)->first();
        if (isset($professionid) && !empty($professionid)) {
            return $professionid->profession_id;
        } else {
            return '0';
        }
    }

    /*
     * get questions detail from question id to avoid user's dummy data from front web app inspect elements
     */

    public function getAllQuestionRelatedDataFromQuestionId($questionID) {
        $data = DB::table(config::get('databaseconstants.TBL_LEVEL4_ACTIVITY'))->where('id', $questionID)->first();
        if (isset($data) && !empty($data)) {
            return $data;
        } else {
            $data = [];
            return $data;
        }
    }

    public function getAllIntermediateQuestionRelatedDataFromQuestionId($questionID) {
        if ($questionID != '' && $questionID > 0) {
            $data = DB::table(config::get('databaseconstants.TBL_LEVEL4_INTERMEDIATE_ACTIVITY') . " AS activity")
                    ->leftjoin(config::get('databaseconstants.TBL_LEVEL4_INTERMEDIATE_ACTIVITY_OPTIONS') . " AS options", 'activity.id', '=', 'options.l4iao_question_id')
                    ->selectRaw('activity.* , GROUP_CONCAT(options.id) AS options_id, GROUP_CONCAT(options.l4iao_answer_order) AS option_order, GROUP_CONCAT(options.l4iao_correct_answer) AS correct_option, GROUP_CONCAT(options.l4iao_answer_image SEPARATOR "##") AS option_image,  GROUP_CONCAT(options.l4iao_answer_image_description SEPARATOR "##") AS option_image_description')
                    ->where('activity.id', $questionID)
                    ->groupBy('activity.id')
                    ->first();
        }

        if (isset($data) && !empty($data)) {
            $data->gt_temlpate_answer_type = Helpers::getAnsTypeFromGamificationTemplateId($data->l4ia_question_template);
            return $data;
        } else {
            $data = [];
            return $data;
        }
    }

    public function getQuestionOPtionFromQuestionId($questionID) {
        $leve4activities = DB::table(config::get('databaseconstants.TBL_LEVEL4_ACTIVITY') . " AS activity")
                ->leftjoin(config::get('databaseconstants.TBL_LEVEL4_OPTIONS') . " AS options", 'activity.id', '=', 'options.activity_id')
                ->selectRaw('activity.* , GROUP_CONCAT(options.id) AS options_id,GROUP_CONCAT(options.correct_option) AS correct_option')
                ->where('activity.id', $questionID)
                ->groupBy('activity.id')
                ->first();
        return $leve4activities;
    }

    /*
     * Check question is right or not.
     * Parameter : activity_id and option_answer_id
     */

    public function checkQuestionRightOrWrong($questionID, $answerID) {
        $listOfAnswer = explode(',', $answerID);

        $correct = array();
        foreach ($listOfAnswer as $ansId) {
            $questionCheck = DB::table(config::get('databaseconstants.TBL_LEVEL4_OPTIONS'))->where("activity_id", $questionID)->where("id", $ansId)->first();
            if (isset($questionCheck->correct_option) && $questionCheck->correct_option == 1) {
                $correct[] = 1;
            }
        }
        if (count($listOfAnswer) > 0 && count($correct) > 0 && (count($listOfAnswer) == count($correct))) {
            $point = 1;
        } else {
            $point = 0;
        }
        return $point;
    }

    public function checkIntermediateQuestionRightOrWrong($questionID, $answerID) {
        $listOfAnswer = explode(',', $answerID);

        $correct = array();
        foreach ($listOfAnswer as $ansId) {
            $questionCheck = DB::table(config::get('databaseconstants.TBL_LEVEL4_INTERMEDIATE_ACTIVITY_OPTIONS'))->where("l4iao_question_id", $questionID)->where("id", $ansId)->first();
            if (isset($questionCheck->l4iao_correct_answer) && $questionCheck->l4iao_correct_answer == 1) {
                $correct[] = 1;
            }
        }
        if (count($listOfAnswer) > 0 && count($correct) > 0 && (count($listOfAnswer) == count($correct))) {
            $point = 1;
        } else {
            $point = 0;
        }
        return $point;
    }

    public function saveLevel4ActivityDetail($activity4Detail, $options, $radioval) {
        $objOption = new Level4Options();

        if ($activity4Detail['id'] != '' && $activity4Detail['id'] > 0) {
            $return = $this->model->where('id', $activity4Detail['id'])->update($activity4Detail);
        } else {
            $return = $this->model->create($activity4Detail);
        }
        if ($return) {
            if ($activity4Detail['id'] != '' && $activity4Detail['id'] > 0) {
                $id = $activity4Detail['id'];
                $deleted = $activity4Detail['deleted'];
            } else {
                $id = $return->id;
                $deleted = $return->deleted;
            }
        }

        $data = $objOption->where('activity_id', $activity4Detail['id'])->get();
        $optionDataLength = sizeof($data);
        $optionLength = sizeof($options);

        $option_new = array();
        $option_old = array();
        $option_oldId = array();

        for ($i = 0; $i < $optionDataLength; $i++) {
            $option_old[] = $data[$i]['options_text'];
        }

        $option_new = $options;

        $delete_options = array_diff($option_old, $option_new);

        foreach ($delete_options as $del) {
            $result = $objOption->where('options_text', $del)->delete();
        }


        $data = $objOption->where('activity_id', $activity4Detail['id'])->get();
        $optionDataLength = sizeof($data);


        for ($i = 0; $i < $optionDataLength; ++$i) {
            $optionDetail = [];
            $optionDetail['activity_id'] = $id;
            $optionDetail['options_text'] = $options[$i];
            $optionDetail['deleted'] = $deleted;
            if (in_array($i, $radioval)) {
                $optionDetail['correct_option'] = 1;
            } else {
                $optionDetail['correct_option'] = 0;
            }

            if ($activity4Detail['id'] != '' && $activity4Detail['id'] > 0) {
                $result = $objOption->where('id', $data[$i]->id)->update($optionDetail);
            }
            else
                $result = $objOption->create($optionDetail);
        }

        for ($i = $optionDataLength; $i < count($options); $i++) {
            $optionDetail = [];
            $optionDetail['activity_id'] = $id;
            $optionDetail['options_text'] = $options[$i];
            $optionDetail['deleted'] = $deleted;
            if (in_array($i, $radioval)) {
                $optionDetail['correct_option'] = 1;
            } else {
                $optionDetail['correct_option'] = 0;
            }
            $result = $objOption->create($optionDetail);
        }

        return $result;
    }

    /**
     * @return Boolean True/False
      Parameters
      @$id : Level4 Activity ID
     */
    public function deleteLevel4Activity($id) {

        $option = new Level4Options();
        $flag = true;
        $level4activity = $this->model->find($id);
        $level4activity->deleted = config::get('constant.DELETED_FLAG');
        $response = $level4activity->delete();
        $deleted = config::get('constant.DELETED_FLAG');
        $option = $option->where('activity_id', $id)->update(['deleted' => $deleted]);
        if ($response && $option) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return level4activity details object
      Parameters
      @$hintDetail : Array of level4 advance activity detail from front
     */
    public function saveLevel4AdvanceActivityDetail($saveData) {

        if ($saveData['id'] != '' && $saveData['id'] > 0) {
            $return = DB::table(config::get('databaseconstants.TBL_LEVEL4_ADVANCE_ACTIVITY'))->where('id', $saveData['id'])->update($saveData);
        } else {
            $return = DB::table(config::get('databaseconstants.TBL_LEVEL4_ADVANCE_ACTIVITY'))->insert($saveData);
        }
        return $return;
    }

    /**
     * @return level4activity details object
      Parameters
      @$hintDetail : Array of level4 advance activity detail from front
     */
    public function getAllLevel4AdvanceActivity() {
        $result = DB::table(config::get('databaseconstants.TBL_LEVEL4_ADVANCE_ACTIVITY'))
                ->select('*')
                ->whereIn('deleted', ['1', '2'])
                ->get();
        return $result;
    }

    public function getLevel4AdvanceActivityById($id) {
        $result = DB::table(config::get('databaseconstants.TBL_LEVEL4_ADVANCE_ACTIVITY'))->where('id', $id)->first();
        return $result;
    }

    /**
     * @return Boolean True/False
      Parameters
      @$id : Activity ID
     */
    public function deleteLevel4AdvanceActivity($id) {
        $saveData['deleted'] = config::get('constant.DELETED_FLAG');
        $return = DB::table(config::get('databaseconstants.TBL_LEVEL4_ADVANCE_ACTIVITY'))->where('id', $id)->update($saveData);
        if ($return) {
            return true;
        } else {
            return false;
        }
    }

    /*
     * Get Level4 Template answer types
     */

    public function getLevel4TemplateAnswerTypes() {
        $result = DB::table(config::get('databaseconstants.TBL_LEVEL4_TEMPLATE_ANSWER_TYPE'))->orderBy('tat_display_order')->get();
        return $result;
    }

    /*
     * save gamification template
     */

    public function saveGamificationTemplate($saveData) {
        if ($saveData['id'] != '' && $saveData['id'] > 0) {
            $return = DB::table(config::get('databaseconstants.TBL_GAMIFICATION_TEMPLATE'))->where('id', $saveData['id'])->update($saveData);
        } else {
            $return = DB::table(config::get('databaseconstants.TBL_GAMIFICATION_TEMPLATE'))->insert($saveData);
        }
        return $return;
    }

    /**
     * @return level4activity details object
      Parameters
      @$hintDetail : Array of level4 advance activity detail from front
     */
    public function getAllGamificationTemplate($searchParamArray = array()) {
        $whereStr = '';
        $orderStr = '';

        $whereArray = [];
        $whereArray[] = 'concepttemplate.deleted IN (1,2)';

        if (isset($searchParamArray) && !empty($searchParamArray)) {
            if (isset($searchParamArray['searchBy']) && isset($searchParamArray['searchText']) && $searchParamArray['searchBy'] != '' && $searchParamArray['searchText'] != '') {
                $whereArray[] = $searchParamArray['searchBy'] . ' LIKE "%' . $searchParamArray['searchText'] . '%"';
            }

            if (isset($searchParamArray['orderBy']) && isset($searchParamArray['sortOrder']) && $searchParamArray['orderBy'] != '' && $searchParamArray['sortOrder'] != '') {
                $orderStr = " ORDER BY " . $searchParamArray['orderBy'] . " " . $searchParamArray['sortOrder'];
            }
        }

        if (!empty($whereArray)) {
            $whereStr = implode(" AND ", $whereArray);
        }
        $result = DB::table(config::get('databaseconstants.TBL_GAMIFICATION_TEMPLATE') . " AS concepttemplate")
                ->join(config::get('databaseconstants.TBL_PROFESSIONS') . " AS profession", 'concepttemplate.gt_profession_id', '=', 'profession.id')
                ->join(config::get('databaseconstants.TBL_LEVEL4_TEMPLATE_ANSWER_TYPE') . " AS answertemplate", 'concepttemplate.gt_template_id', '=', 'answertemplate.id')
                ->selectRaw('concepttemplate.*,profession.pf_name,answertemplate.tat_type')
                ->whereRaw($whereStr)
                ->groupBy('concepttemplate.id')
                ->paginate(Config::get('constant.ADMIN_RECORD_PER_PAGE'));
        return $result;
    }

    public function getAllGamificationTemplateObj() {
        $result = DB::table(config::get('databaseconstants.TBL_GAMIFICATION_TEMPLATE') . " AS concepttemplate")
                ->join(config::get('databaseconstants.TBL_PROFESSIONS') . " AS profession", 'concepttemplate.gt_profession_id', '=', 'profession.id')
                ->join(config::get('databaseconstants.TBL_LEVEL4_TEMPLATE_ANSWER_TYPE') . " AS answertemplate", 'concepttemplate.gt_template_id', '=', 'answertemplate.id')
                ->selectRaw('concepttemplate.*, profession.pf_name, answertemplate.tat_type')
                ->whereIn('concepttemplate.deleted', [1,2])
                ->groupBy('concepttemplate.id');

        return $result;
    }

    /*
     * Get Gamification template by id
     */

    public function getGamificationTemplateById($id) {
        $result = DB::table(config::get('databaseconstants.TBL_GAMIFICATION_TEMPLATE'))->where('id', $id)->first();
        return $result;
    }

    /*
     * Delete Gamification template
     */

    public function deleteGamificationTemplate($id) {
        $saveData['deleted'] = config::get('constant.DELETED_FLAG');
        $return = DB::table(config::get('databaseconstants.TBL_GAMIFICATION_TEMPLATE'))->where('id', $id)->update($saveData);
        if ($return) {
            return true;
        } else {
            return false;
        }
    }

    /*
     * Get all active gamification template
     */

    public function getActiveGamificationTemplate() {
        $result = DB::table(config::get('databaseconstants.TBL_GAMIFICATION_TEMPLATE'))->where('deleted', 1)->get();
        return $result;
    }

    /*
     * Save Level4 Intermediate Activity
     */

    public function saveLevel4IntermediateActivity($questionData) {
        if ($questionData['id'] != '' && $questionData['id'] > 0) {
            $id = DB::table(config::get('databaseconstants.TBL_LEVEL4_INTERMEDIATE_ACTIVITY'))->where('id', $questionData['id'])->update($questionData);
        } else {
            $id = DB::table(config::get('databaseconstants.TBL_LEVEL4_INTERMEDIATE_ACTIVITY'))->insertGetId($questionData);
        }
        return $id;
    }

    /*
     * Save Level4 intermediate question media data
     */

    public function saveLevel4IntermediateActivityMedia($questionMedia) {
        $response = DB::table(config::get('databaseconstants.TBL_LEVEL4_INTERMEDIATE_ACTIVITY_MEDIA'))->insert($questionMedia);
        return $response;
    }

    /*
     * Save Level4 intermediate question options data
     */

    public function saveLevel4IntermediateActivityOptions($questionOptionData) {
        if ($questionOptionData['id'] != '' && $questionOptionData['id'] > 0) {
            $response = DB::table(config::get('databaseconstants.TBL_LEVEL4_INTERMEDIATE_ACTIVITY_OPTIONS'))->where('id', $questionOptionData['id'])->update($questionOptionData);
        } else {
            $response = DB::table(config::get('databaseconstants.TBL_LEVEL4_INTERMEDIATE_ACTIVITY_OPTIONS'))->insert($questionOptionData);
        }
        return $response;
    }

    /*
     * Get Level4 intermediate question options data
     */

    public function getLevel4IntermediateActivities($searchParamArray = array()) {
        $whereStr = '';
        $orderStr = '';
        $whereArray = [];

        $whereArray[] = 'intermediateactivity.deleted IN (1,2)';

        if (isset($searchParamArray) && !empty($searchParamArray)) {
            $searchParamArray['searchText'] = (isset($searchParamArray['searchText']) && $searchParamArray['searchText'] != '')? addslashes($searchParamArray['searchText']) : '';
            if (isset($searchParamArray['searchBy']) && isset($searchParamArray['searchText']) && $searchParamArray['searchBy'] != '' && $searchParamArray['searchText'] != '') {
                $whereArray[] = $searchParamArray['searchBy'] . ' LIKE "%'.$searchParamArray['searchText'].'%"';
            }

            if (isset($searchParamArray['orderBy']) && isset($searchParamArray['sortOrder']) && $searchParamArray['orderBy'] != '' && $searchParamArray['sortOrder'] != '') {
                $orderStr = " ORDER BY " . $searchParamArray['orderBy'] . " " . $searchParamArray['sortOrder'];
            }
        }

        if (!empty($whereArray)) {
            $whereStr = implode(" AND ", $whereArray);
        }

        $result = DB::table(config::get('databaseconstants.TBL_LEVEL4_INTERMEDIATE_ACTIVITY') . " AS intermediateactivity")
                ->join(config::get('databaseconstants.TBL_PROFESSIONS') . " AS profession", 'intermediateactivity.l4ia_profession_id', '=', 'profession.id')
                ->join(config::get('databaseconstants.TBL_GAMIFICATION_TEMPLATE') . " AS gamification", 'intermediateactivity.l4ia_question_template', '=', 'gamification.id')
                ->selectRaw('intermediateactivity.*,profession.pf_name,gamification.gt_template_title')
                ->whereRaw($whereStr . $orderStr)
                ->paginate(Config::get('constant.ADMIN_RECORD_PER_PAGE'));

        return $result;
    }
    public function getLevel4IntermediateActivitiesObj() {
        $result = DB::table(config::get('databaseconstants.TBL_LEVEL4_INTERMEDIATE_ACTIVITY') . " AS intermediateactivity")
                    ->join(config::get('databaseconstants.TBL_PROFESSIONS') . " AS profession", 'intermediateactivity.l4ia_profession_id', '=', 'profession.id')
                    ->join(config::get('databaseconstants.TBL_GAMIFICATION_TEMPLATE') . " AS gamification", 'intermediateactivity.l4ia_question_template', '=', 'gamification.id')
                    ->selectRaw('intermediateactivity.*, profession.pf_name, gamification.gt_template_title')
                    ->whereIn('intermediateactivity.deleted', [1,2]);

        return $result;
    }

    /*
     * Delete level 4 intermediate activity
     */

    public function deleteLevel4IntermediateActivity($id) {
        $saveData['deleted'] = config::get('constant.DELETED_FLAG');
        $return = DB::table(config::get('databaseconstants.TBL_LEVEL4_INTERMEDIATE_ACTIVITY'))->where('id', $id)->update($saveData);
        if ($return) {
            return true;
        } else {
            return false;
        }
    }

    /*
     * Get level 4 intermediate activity
     */

    public function getLevel4IntermediateActivityById($id) {


        $result = DB::table(config::get('databaseconstants.TBL_LEVEL4_INTERMEDIATE_ACTIVITY') . " AS intermediateactivity")
                ->join(config::get('databaseconstants.TBL_PROFESSIONS') . " AS profession", 'intermediateactivity.l4ia_profession_id', '=', 'profession.id')
                ->join(config::get('databaseconstants.TBL_GAMIFICATION_TEMPLATE') . " AS gamification", 'intermediateactivity.l4ia_question_template', '=', 'gamification.id')
                ->selectRaw('intermediateactivity.*,profession.pf_name,gamification.gt_template_title,gamification.gt_temlpate_answer_type')
                ->where('intermediateactivity.id', $id)
                ->first();
        //$result = DB::table(config::get('databaseconstants.TBL_LEVEL4_INTERMEDIATE_ACTIVITY'))->where('id', $id)->first();

        return $result;
    }

    /*
     * Save Level4 intermediate question media data
     */

    public function getIntermediateActivityMediaByQuestionId($questionId) {
        $result = DB::table(config::get('databaseconstants.TBL_LEVEL4_INTERMEDIATE_ACTIVITY_MEDIA'))->where('l4iam_question_id', $questionId)->get();
        return $result;
    }

    /*
     * Delete question media
     */

    public function deleteLeve4IntermediateMedia($id) {
        $result = DB::table(config::get('databaseconstants.TBL_LEVEL4_INTERMEDIATE_ACTIVITY_MEDIA'))->where('id', $id)->delete();
        return $result;
    }

    /*
     * Update the intermediate activity media
     */

    public function updateLevel4IntermediateActivityMedia($questionMedia, $id) {
        $return = DB::table(config::get('databaseconstants.TBL_LEVEL4_INTERMEDIATE_ACTIVITY_MEDIA'))->where('id', $id)->update($questionMedia);
        return $return;
    }

    /*
     * Get Intermediate activity media by id
     */
    public function getIntermediateActivityMediaByMediaId($id) {
        $result = DB::table(config::get('databaseconstants.TBL_LEVEL4_INTERMEDIATE_ACTIVITY_MEDIA'))->where('id', $id)->first();
        return $result;
    }

    /*
     * Get last id from question media table
     */
    public function getIntermediateActivityMediaLastId() {
        $result = DB::table(config::get('databaseconstants.TBL_LEVEL4_INTERMEDIATE_ACTIVITY_MEDIA'))->orderBy('id', 'desc')->first();
        return $result;
    }


    /*
     * Get answer option by activity id
     */

    public function getIntermediateActivityAnswerByQuestionId($questionID) {
        $data = DB::table(config::get('databaseconstants.TBL_LEVEL4_INTERMEDIATE_ACTIVITY_OPTIONS') . " AS options")
                ->selectRaw('options.*')
                ->where('options.l4iao_question_id', $questionID)
                ->get();
        return $data;
    }

    public function updateLevel4IntermediateActivityOptions($questionOptionData, $id) {
        $return = DB::table(config::get('databaseconstants.TBL_LEVEL4_INTERMEDIATE_ACTIVITY_OPTIONS'))->where('id', $id)->update($questionOptionData);
        return $return;
    }

    /*
     * Get Level4 Advance Activity By Type
     */
    public function getLevel4AdvanceActivityByType($type) {
        $result = DB::table(config::get('databaseconstants.TBL_LEVEL4_ADVANCE_ACTIVITY'))->where('deleted', 1)->where('l4aa_type', $type)->get();
        return $result;
    }

    /*
     * Save level4 advance activity of user
     */
    public function saveLevel4AdvanceActivityUser($level4AdvanceData) {
        if ($level4AdvanceData['id'] != '' && $level4AdvanceData['id'] > 0) {
            $response = DB::table(config::get('databaseconstants.TBL_LEVEL4_ADVANCE_ACTIVITY_USER_DATA'))->where('id', $level4AdvanceData['id'])->update($level4AdvanceData);
        } else {
            $response = DB::table(config::get('databaseconstants.TBL_LEVEL4_ADVANCE_ACTIVITY_USER_DATA'))->insert($level4AdvanceData);
        }
        return $response;
    }

    /*
     * Get User Level4 Advance activity
     */
    public function getLevel4AdvanceActivityTaskByUser($teengager,$professionId,$mediaType) {

        //$result = DB::table(config::get('databaseconstants.TBL_LEVEL4_ADVANCE_ACTIVITY_USER_DATA'))->where('deleted', 1)->where('l4aaua_teenager', $teengager)->where('l4aaua_profession_id', $professionId)->where('l4aaua_media_type',$mediaType)->get();
        $result = DB::table(config::get('databaseconstants.TBL_LEVEL4_ADVANCE_ACTIVITY_USER_DATA') . " AS activity")
                ->leftjoin(config::get('databaseconstants.TBL_ADMIN_USERS') . " AS admin", 'activity.l4aaua_verified_by', '=', 'admin.id')
                ->selectRaw('activity.* , admin.id as adminid, admin.name as adminname')
                ->where('activity.deleted', 1)
                ->where('activity.l4aaua_teenager', $teengager)
                ->where('activity.l4aaua_profession_id', $professionId)
                ->where('activity.l4aaua_media_type', $mediaType)
    ->get();
        return $result;
    }

    /*
     * Update Level4 advance task of user
     */
    public function updateStatusAdvanceTaskUser($dataid,$updateData)
    {
        if ($dataid != '' && $dataid > 0) {
            $response = DB::table(config::get('databaseconstants.TBL_LEVEL4_ADVANCE_ACTIVITY_USER_DATA'))->where('id', $dataid)->update($updateData);
        }
    }

    /*Get Users all tasks for AdvanceLevel which are submitted for review*/
    public function getUserTaskForAdmin($searchParamArray = array()) {
        $result = DB::table(config::get('databaseconstants.TBL_LEVEL4_ADVANCE_ACTIVITY_USER_DATA') . " AS usertask")
                ->join(config::get('databaseconstants.TBL_PROFESSIONS') . " AS profession", 'usertask.l4aaua_profession_id', '=', 'profession.id')
                ->join(config::get('databaseconstants.TBL_TEENAGERS') . " AS teenager", 'usertask.l4aaua_teenager', '=', 'teenager.id')
                ->selectRaw('usertask.*,profession.pf_name,teenager.t_name')
                ->groupBy('usertask.l4aaua_profession_id')
                ->groupBy('usertask.l4aaua_teenager')
                ->whereIn('usertask.deleted', [1,2])
                ->whereIn('l4aaua_is_verified', [1,2,3])
                ->get();
        return $result;
    }

    /*Get User all tasks for AdvanceLevel by profession*/

    public function getUserAllTasksForAdvanceLevel($teenager,$profession,$type) {

        $result = DB::table(config::get('databaseconstants.TBL_LEVEL4_ADVANCE_ACTIVITY_USER_DATA') . " AS usertask")
                ->join(config::get('databaseconstants.TBL_PROFESSIONS') . " AS profession", 'usertask.l4aaua_profession_id', '=', 'profession.id')
                ->join(config::get('databaseconstants.TBL_TEENAGERS') . " AS teenager", 'usertask.l4aaua_teenager', '=', 'teenager.id')
                ->leftjoin(config::get('databaseconstants.TBL_ADMIN_USERS') . " AS admin", 'usertask.l4aaua_verified_by', '=', 'admin.id')
                ->selectRaw('usertask.*,profession.pf_name,teenager.t_name,admin.id as adminid, admin.name as adminname')
                ->where('usertask.l4aaua_teenager','=',$teenager)
                ->where('usertask.l4aaua_profession_id','=',$profession)
                ->where('usertask.deleted','!=',3)
                ->where('l4aaua_is_verified','!=',0)
                ->where('l4aaua_media_type',$type)
                ->get();
        return $result;
    }

    /*Update user task status
     */
    public function updateUserTaskStatusByAdmin($id,$updateData)
    {
        if ($id != '' && $id > 0) {
            $response = DB::table(config::get('databaseconstants.TBL_LEVEL4_ADVANCE_ACTIVITY_USER_DATA'))->where('id', $id)->update($updateData);
        }
        return $response;
    }

    /*
     * Delete user task
     */
    public function deleteUserAdvanceTask($id)
    {
        if ($id != '' && $id > 0) {
            $response = DB::table(config::get('databaseconstants.TBL_LEVEL4_ADVANCE_ACTIVITY_USER_DATA'))->where('id', $id)->delete();
        }
        return $response;
    }

    /*
     * Get last added activity to select predefine data for the same concept
     */
    public function getLastAddedIntermediateActivity()
    {
        $result = DB::table(config::get('databaseconstants.TBL_LEVEL4_INTERMEDIATE_ACTIVITY'))->where('deleted', 1)->orderBy('id','desc')->first();
        return $result;
    }

    /*Delete Audio and popup image*/
    public function deleteAudioPopupImage($deleteData, $id) {
        $return = DB::table(config::get('databaseconstants.TBL_LEVEL4_INTERMEDIATE_ACTIVITY'))->where('id', $id)->update($deleteData);
        return $return;
    }

    /*Get User all tasks for AdvanceLevel by profession*/

    public function getUserAllVerifiedTasks($teenager,$profession)
    {
        $result = DB::table(config::get('databaseconstants.TBL_LEVEL4_ADVANCE_ACTIVITY_USER_DATA') . " AS usertask")
                ->join(config::get('databaseconstants.TBL_PROFESSIONS') . " AS profession", 'usertask.l4aaua_profession_id', '=', 'profession.id')
                ->join(config::get('databaseconstants.TBL_TEENAGERS') . " AS teenager", 'usertask.l4aaua_teenager', '=', 'teenager.id')
                ->selectRaw('usertask.*,profession.pf_name,teenager.t_name')
                ->where('usertask.l4aaua_teenager','=',$teenager)
                ->where('usertask.l4aaua_profession_id','=',$profession)
                ->where('usertask.deleted','!=',3)
                ->where('l4aaua_is_verified','=',2)
                ->get();
        return $result;
    }

    /*Get level4 advance user tasks*/
    public function getUserAdvanceTaskById($id) {

        $result = DB::table(config::get('databaseconstants.TBL_LEVEL4_ADVANCE_ACTIVITY_USER_DATA') . " AS usertask")
                ->join(config::get('databaseconstants.TBL_PROFESSIONS') . " AS profession", 'usertask.l4aaua_profession_id', '=', 'profession.id')
                ->selectRaw('usertask.*,profession.pf_name')
                ->where('usertask.id','=',$id)
                ->where('usertask.deleted','!=',3)
                ->get();
        return $result;
    }

    /*Copy concept functionality*/
    public function copyConcept($conceptId,$toProfession,$conceptImage) {
        //$query = DB::select(DB::raw("INSERT INTO ".config::get('databaseconstants.TBL_GAMIFICATION_TEMPLATE')." (gt_profession_id, gt_template_id, gt_template_title, gt_template_image, gt_template_descritpion, gt_template_descritpion_popup_imge, gt_temlpate_answer_type, deleted) SELECT {$toProfession}, gt_template_id,gt_template_title,'{$conceptImage}',gt_template_descritpion,gt_template_descritpion_popup_imge,gt_temlpate_answer_type,deleted FROM ".config::get('databaseconstants.TBL_GAMIFICATION_TEMPLATE')." WHERE id = ".$conceptId." "));
        //$id = DB::getPdo()->lastInsertId();
        //return $id;

        $getData = DB::table(config::get('databaseconstants.TBL_GAMIFICATION_TEMPLATE'))->where('id',$conceptId)->first();
        
        if($getData)
        {
            $array = [];
            $array['gt_profession_id'] = $toProfession;
            $array['gt_template_id'] = $getData->gt_template_id;
            $array['gt_template_title'] = $getData->gt_template_title;
            $array['gt_template_image'] = $conceptImage;
            $array['gt_template_descritpion'] = $getData->gt_template_descritpion;
            $array['gt_template_descritpion_popup_imge'] = $getData->gt_template_descritpion_popup_imge;
            $array['gt_temlpate_answer_type'] = $getData->gt_temlpate_answer_type;
            $array['gt_coins'] = $getData->gt_coins;
            $array['gt_valid_upto'] = $getData->gt_valid_upto ;
            $array['deleted'] = $getData->deleted;

            $newData = DB::table(config::get('databaseconstants.TBL_GAMIFICATION_TEMPLATE'))->insertGetId($array);

            return $newData;
        }
        else
        {
            return false;
        }
    }

    /*get level4 activity detail */
    public function getLevel4ActivityDataById($id) {
        $return = DB::table(config::get('databaseconstants.TBL_LEVEL4_INTERMEDIATE_ACTIVITY'))
                        ->select('l4ia_question_popup_image', 'l4ia_question_audio')
                        ->where('id', '=' ,$id)
                        ->get();
        return $return;
    }

    /*copy level4 activity data*/
    public function copyLevel4ActivityData($id,$toProfession,$templateIds,$newImage, $newAudioFileName) {
        // $query = DB::select(DB::raw("INSERT INTO ".config::get('databaseconstants.TBL_LEVEL4_INTERMEDIATE_ACTIVITY')." (l4ia_profession_id, l4ia_question_text, l4ia_question_time, l4ia_question_point, l4ia_question_description, l4ia_question_answer_description, l4ia_question_popup_image, l4ia_question_popup_description,l4ia_question_audio,l4ia_question_template,l4ia_question_right_message,l4ia_question_wrong_message,l4ia_shuffle_options,l4ia_options_metrix,deleted) SELECT {$toProfession}, l4ia_question_text,l4ia_question_time,l4ia_question_point,l4ia_question_description,l4ia_question_answer_description,'{$newImage}',l4ia_question_popup_description,l4ia_question_audio,{$templateIds},l4ia_question_right_message,l4ia_question_wrong_message,l4ia_shuffle_options,l4ia_options_metrix,deleted FROM ".config::get('databaseconstants.TBL_LEVEL4_INTERMEDIATE_ACTIVITY')." WHERE id = ".$id." "));
        // $id = DB::getPdo()->lastInsertId();
        // return $id;

        $getData = DB::table(config::get('databaseconstants.TBL_LEVEL4_INTERMEDIATE_ACTIVITY'))->where('id',$id)->first();
        if($getData)
        {
            $array = [];
            $array['l4ia_profession_id'] = $toProfession;
            $array['l4ia_question_text'] = $getData->l4ia_question_text;
            $array['l4ia_question_time'] = $getData->l4ia_question_time;
            $array['l4ia_question_point'] = $getData->l4ia_question_point;
            $array['l4ia_question_description'] = $getData->l4ia_question_description;
            $array['l4ia_question_answer_description'] = $getData->l4ia_question_answer_description;
            $array['l4ia_question_popup_image'] = $newImage;
            $array['l4ia_question_audio'] = $newAudioFileName;
            $array['l4ia_question_popup_description'] = $getData->l4ia_question_popup_description;
            $array['l4ia_question_template'] = $templateIds;
            $array['l4ia_question_right_message'] = $getData->l4ia_question_right_message;
            $array['l4ia_question_wrong_message'] = $getData->l4ia_question_wrong_message;
            $array['l4ia_shuffle_options'] = $getData->l4ia_shuffle_options;
            $array['l4ia_options_metrix'] = $getData->l4ia_options_metrix;
            $array['deleted'] = $getData->deleted;

            $newData = DB::table(config::get('databaseconstants.TBL_LEVEL4_INTERMEDIATE_ACTIVITY'))->insertGetId($array);
            
            return $newData;
        }
        else
        {
            return false;
        }

    }

    /* get level4 activity data */
    public function getLevel4ActivityData($conceptId){
        
        $oldConceptId = DB::table(config::get('databaseconstants.TBL_LEVEL4_INTERMEDIATE_ACTIVITY'))
                        ->selectRaw(DB::raw('GROUP_CONCAT(id order by id) as oldId'))
                        ->where('l4ia_question_template', '=' ,$conceptId)
                        ->groupBy('l4ia_question_template')                                                   
                        ->get();
        return $oldConceptId;
    }
    /*copy level4 activity options data*/
    public function copyLevel4ActivityOptionsData($conceptId,$templateIds,$newanswerImage,$responseImage) {
        // $query = DB::select(DB::raw("INSERT INTO ".config::get('databaseconstants.TBL_LEVEL4_INTERMEDIATE_ACTIVITY_OPTIONS')." (l4iao_question_id, l4iao_answer_text, l4iao_answer_image, l4iao_answer_image_description, l4iao_correct_answer, l4iao_answer_order, l4iao_answer_group, l4iao_answer_response_text,l4iao_answer_response_image,l4iao_answer_points,deleted) SELECT {$templateIds}, l4iao_answer_text, '{$newanswerImage}', l4iao_answer_image_description, l4iao_correct_answer, l4iao_answer_order, l4iao_answer_group, l4iao_answer_response_text,'{$responseImage}',l4iao_answer_points,deleted FROM ".config::get('databaseconstants.TBL_LEVEL4_INTERMEDIATE_ACTIVITY_OPTIONS')." WHERE l4iao_question_id = ".$conceptId." "));
        // return $conceptId;

        $getDatas = DB::table(config::get('databaseconstants.TBL_LEVEL4_INTERMEDIATE_ACTIVITY_OPTIONS'))->where('l4iao_question_id',$conceptId)->get();
                       
        if($getDatas)
        {
            foreach($getDatas as $key => $getData){
                
                $array = [];
                $array['l4iao_question_id'] = $templateIds;
                $array['l4iao_answer_text'] = $getData->l4iao_answer_text;
                $array['l4iao_answer_image'] = (isset($newanswerImage[$getData->id]) ) ? $newanswerImage[$getData->id] : "";
                $array['l4iao_answer_image_description'] = $getData->l4iao_answer_image_description;
                $array['l4iao_correct_answer'] = $getData->l4iao_correct_answer;
                $array['l4iao_answer_order'] = $getData->l4iao_answer_order;
                $array['l4iao_answer_group'] = $getData->l4iao_answer_group;
                $array['l4iao_answer_response_text'] = $getData->l4iao_answer_response_text;
                $array['l4iao_answer_response_image'] = (isset($responseImage[$getData->id]) ) ? $responseImage[$getData->id] : "";
                $array['l4iao_answer_points'] = $getData->l4iao_answer_points;
                $array['deleted'] = $getData->deleted;
                $newData = DB::table(config::get('databaseconstants.TBL_LEVEL4_INTERMEDIATE_ACTIVITY_OPTIONS'))->insertGetId($array);
            }
            
            return $conceptId;
        }
        else
        {
            return $conceptId;
        }    
    }

    /*get level4 activity answer detail */
    public function getLevel4ActivityOptionsData($id) {
        $return = DB::table(config::get('databaseconstants.TBL_LEVEL4_INTERMEDIATE_ACTIVITY_OPTIONS'))
                        ->select('l4iao_answer_image', 'l4iao_answer_response_image', 'id as optionsId')
                        ->where('l4iao_question_id', '=' ,$id)
                        ->get();
        return $return;
    }

    /*get level4 activity media detail */
    public function getQuestionMediaById($id) {
        $return = DB::table(config::get('databaseconstants.TBL_LEVEL4_INTERMEDIATE_ACTIVITY_MEDIA'))
                        ->select('l4iam_media_name','l4iam_media_type', 'id as mediaId')
                        ->where('l4iam_question_id', '=' ,$id)
                        ->get();
        return $return;
    }

    /*copy level 4 activity media */
    public function copyLevel4ActivityMediaData($conceptId,$questionId,$conceptImage) {
        // $query = DB::select(DB::raw("INSERT INTO ".config::get('databaseconstants.TBL_LEVEL4_INTERMEDIATE_ACTIVITY_MEDIA')." (l4iam_question_id, l4iam_media_name, l4iam_media_type, l4iam_media_desc,deleted) SELECT {$questionId}, '{$conceptImage}',l4iam_media_type,l4iam_media_desc,deleted FROM ".config::get('databaseconstants.TBL_LEVEL4_INTERMEDIATE_ACTIVITY_MEDIA')." WHERE l4iam_question_id = ".$conceptId." "));
        // $id = DB::getPdo()->lastInsertId();
        // return $id;
        $getDatas = DB::table(config::get('databaseconstants.TBL_LEVEL4_INTERMEDIATE_ACTIVITY_MEDIA'))->where('l4iam_question_id',$conceptId)->get();
        if($getDatas)
        {
            foreach($getDatas as $getData){
                $array = [];
                $array['l4iam_question_id'] = $questionId;
                $array['l4iam_media_name'] = (isset($conceptImage[$getData->id]))? $conceptImage[$getData->id] : "";
                $array['l4iam_media_type'] = $getData->l4iam_media_type;
                $array['l4iam_media_desc'] = $getData->l4iam_media_desc;
                $array['deleted'] = $getData->deleted;

                $newData = DB::table(config::get('databaseconstants.TBL_LEVEL4_INTERMEDIATE_ACTIVITY_MEDIA'))->insertGetId($array);    
            }
            return $newData;
        }
        else
        {
            return false;
        }
    }

    public function getImageNameById($id) {
        $return = DB::table(config::get('databaseconstants.TBL_LEVEL4_ADVANCE_ACTIVITY_USER_DATA'))
                        ->select('l4aaua_media_name')
                        ->where('id', '=' ,$id)
                        ->get();
        return $return;
    }

    public function getLevel4AdvanceDetailById($id, $proId, $type) {
        $result = DB::table(config::get('databaseconstants.TBL_LEVEL4_ADVANCE_ACTIVITY_USER_DATA'))
                        ->select(DB::raw('SUM(l4aaua_earned_points) AS earned_points'))
                        ->where('deleted', '=', 1)
                        ->where('l4aaua_teenager', '=', $id)
                        ->where('l4aaua_profession_id', '=', $proId)
                        ->where('l4aaua_media_type', '=', $type)
                        ->get();
        return $result;
    }

    public function getLevel4IntermediateDetailById($id, $proId) {
        $result = DB::table(config::get('databaseconstants.TBL_LEVEL4_INTERMEDIATE_ACTIVITY_ANSWER'))
                        ->select('l4iaua_earned_point','l4iaua_template_id')
                        ->where('l4iaua_teenager', '=', $id)
                        ->where('l4iaua_profession_id', '=', $proId)
                        ->groupBy('l4iaua_activity_id')
                        ->get();
        return $result;
    }

    public function getTemplateDataForCoinsDetail($id) {
        $result = DB::table(config::get('databaseconstants.TBL_GAMIFICATION_TEMPLATE'))->where('deleted', 1)->where('id', '=', $id)->first();
        $data = [];
        $data['gt_coins'] = '';
        $data['gt_valid_upto'] = '';
        if (isset($result)) {
            $data['gt_coins'] = $result->gt_coins;
            $data['gt_valid_upto'] = $result->gt_valid_upto;
        }
        return $data;
    }

    public function updateTemplateCoinsDetail($id, $Coins) {
        $userDetail = DB::table(config::get('databaseconstants.TBL_GAMIFICATION_TEMPLATE'))->where('id', $id)->update(['gt_coins' => $Coins, 'gt_valid_upto' => 30]);
        return $userDetail;
    }

    public function getConceptDataForCoinsDetail($proId, $id) {
        $result = DB::table(config::get('databaseconstants.TBL_GAMIFICATION_TEMPLATE'))
                        ->select('*')
                        ->where('gt_profession_id', '=', $proId)
                        ->where('id', '=', $id)
                        ->get();
        return $result;
    }

    public function getNotAttemptedActivitiesForParent($parentId, $professionId) {
        $activities = DB::select(DB::raw("SELECT tmp.*
                                            FROM (SELECT L4AC.id AS activityID, question_text, points, type,
                                                timer, profession_id, GROUP_CONCAT(L4OP.id) AS optionIds,
                                                GROUP_CONCAT(L4OP.correct_option) AS correctOption,
                                              GROUP_CONCAT(options_text SEPARATOR '#') AS options,
                                                L4AC.deleted, count(*) as 'NoOfQ' FROM
                                              " . config::get('databaseconstants.TBL_LEVEL4_ACTIVITY') . " AS L4AC
                                                INNER JOIN " . config::get('databaseconstants.TBL_LEVEL4_OPTIONS') . " AS L4OP ON L4OP.activity_id = L4AC.id
                                                GROUP BY
                                              L4AC.id) AS tmp
                                                LEFT JOIN " . config::get('databaseconstants.TBL_LEVEL4_ANSWERS_PARENT') . " AS L4ANS ON L4ANS.lbac_activity_id = tmp.activityID AND L4ANS.lbac_parent_id = $parentId
                                                WHERE tmp.profession_id = $professionId and  tmp.deleted=1 and L4ANS.id IS NULL AND L4ANS.lbac_parent_id IS NULL AND L4ANS.lbac_activity_id IS NULL AND L4ANS.lbac_answer_id IS NULL"), array());

        if (isset($activities) && !empty($activities)) {
            foreach ($activities as $key => $activity) {
                $optionIds = explode(",", $activity->optionIds);
                $correctOption = explode(",", $activity->correctOption);
                $multiOption = array_count_values($correctOption);
                $multiOptionCount = (isset($multiOption['1']))?$multiOption['1'] : 0;
                $activities[$key]->totalCorrectOptions = $multiOptionCount;
                $options = explode("#", $activity->options);
                unset($activity->optionIds);
                unset($activity->options);

                $optionsWithId = [];

                foreach ($options as $key1 => $option) {
                    $temp = [];
                    $temp['optionId'] = $optionIds[$key1];
                    $temp['optionText'] = $option;
                    $temp['correctOption'] = $correctOption[$key1];
                    $optionsWithId[] = $temp;
                }
                $activities[$key]->options = $optionsWithId;
            }
        } else {
            $activities = '';
        }

        return $activities;
    }

     public function getNoOfTotalQuestionsAttemptedQuestionForParent($parentId, $professionId) {
        $result = DB::select(DB::raw("select (SELECT count(DISTINCT(l4_aa.id)) FROM ".config::get('databaseconstants.TBL_LEVEL4_ACTIVITY')." AS l4_aa join ".config::get('databaseconstants.TBL_LEVEL4_OPTIONS')." AS l4_an on l4_aa.id = l4_an.activity_id where l4_aa.deleted=1 and l4_aa.profession_id = $professionId ) as 'NoOfTotalQuestions', (select count(DISTINCT(L4_ANS.lbac_activity_id)) from " . config::get('databaseconstants.TBL_LEVEL4_ACTIVITY') . " AS L4_AC join " . config::get('databaseconstants.TBL_LEVEL4_ANSWERS_PARENT') . " AS L4_ANS on L4_AC.id = L4_ANS.lbac_activity_id  where L4_ANS.lbac_parent_id=" . $parentId . " AND L4_AC.profession_id = " . $professionId . " ) as 'NoOfAttemptedQuestions' "), array());
        return $result;
    }

    public function saveParentActivityResponse($parentId, $responses) {
        $points = 0;
        $questionsID = [];

        foreach ($responses as $response) {

            $row = [];
            $row['lbac_parent_id'] = $parentId;
            $row['lbac_activity_id'] = $response['questionID'];
            $answerArray = explode(',', $response['answerID']);
            $row['lbac_earned_points'] = $response['earned_points'];
            $objLevel4Answers = new Level4ParentAnswers();

            foreach ($answerArray as $ansId) {
                $row['lbac_answer_id'] = $ansId;
                $answered = $objLevel4Answers->where("lbac_parent_id", $parentId)->where("lbac_activity_id", $response['questionID'])->where("lbac_answer_id", $row['lbac_answer_id'])->first();
                if ($answered) {
                    $answered = $answered->toArray();
                    $objLevel4Answers->where('id', $answered['id'])->update($row);
                } else {
                    $res = $objLevel4Answers->create($row);
                    if ($res) {

                    }
                }
            }

            $points += $response['earned_points'];

        }

        $parentLevel4PointsRow = [];
        $parentLevel4PointsRow['plb_parent_id'] = $parentId;
        $parentLevel4PointsRow['plb_level'] = config::get('constant.LEVEL4_ID');
        $parentLevel4PointsRow['plb_profession'] = $response['profession_id'];

        $parentLevelPoints = DB::table(config::get('databaseconstants.TBL_PARENT_LEVEL_BOOSTERS'))->where(["plb_parent_id" => $parentId, "plb_level" => config::get('constant.LEVEL4_ID'), "plb_profession" => $response['profession_id']])->first();
        if ($parentLevelPoints) {
            $parentLevelPoints = (array) $parentLevelPoints;
            $parentLevel4PointsRow['plb_points'] = $parentLevelPoints['plb_points'] + $points;
            DB::table(config::get('databaseconstants.TBL_PARENT_LEVEL_BOOSTERS'))->where('id', $parentLevelPoints['id'])->update($parentLevel4PointsRow);
        } else {
            $parentLevel4PointsRow['plb_points'] = $points;
            DB::table(config::get('databaseconstants.TBL_PARENT_LEVEL_BOOSTERS'))->insert($parentLevel4PointsRow);
        }

        $returnArray = [];
        $returnArray['questionsID'] = $questionsID;
        $returnArray['total_Points'] = $points;

        return $returnArray;
    }


    public function getNotAttemptedIntermediateActivitiesForParent($parentId, $professionId, $templateId) {
        $activities = DB::select(DB::raw("SELECT tmp.*
                                            FROM (SELECT L4_I_AC.id AS activityID, l4ia_shuffle_options, l4ia_options_metrix, l4ia_question_description, l4ia_question_answer_description, l4ia_question_text, l4ia_question_point, l4ia_question_template,
                                                l4ia_question_time, l4ia_profession_id,l4ia_question_popup_image,l4ia_question_audio,l4ia_question_popup_description, GROUP_CONCAT(L4_I_OP.id) AS optionIds,
                                                GROUP_CONCAT(L4_I_OP.l4iao_correct_answer SEPARATOR '###') AS correctOption,
                                                GROUP_CONCAT(L4_I_OP.l4iao_answer_order) AS correctOrder,
                                                GROUP_CONCAT(L4_I_OP.l4iao_answer_image SEPARATOR '###') AS optionAsImage,
                                                GROUP_CONCAT(L4_I_OP.l4iao_answer_image_description SEPARATOR '###') AS optionImageText,
                                                GROUP_CONCAT(L4_I_OP.l4iao_answer_response_text SEPARATOR '###') AS optionResponseText,
                                                GROUP_CONCAT(L4_I_OP.l4iao_answer_response_image SEPARATOR '###') AS optionResponseImage,
                                              GROUP_CONCAT(l4iao_answer_text SEPARATOR '###') AS options,
                                                L4_I_AC.deleted,
                                                count(*) as 'NoOfQ' FROM
                                              " . config::get('databaseconstants.TBL_LEVEL4_INTERMEDIATE_ACTIVITY') . " AS L4_I_AC
                                                INNER JOIN " . config::get('databaseconstants.TBL_LEVEL4_INTERMEDIATE_ACTIVITY_OPTIONS') . " AS L4_I_OP ON L4_I_OP.l4iao_question_id = L4_I_AC.id
                                                GROUP BY L4_I_AC.id) AS tmp
                                                  LEFT JOIN " . config::get('databaseconstants.TBL_LEVEL4_INTERMEDIATE_ACTIVITY_PARENT') . " AS L4_I_ANS ON L4_I_ANS.l4iapa_activity_id = tmp.activityID AND L4_I_ANS.l4iapa_parent_id = $parentId
                                                WHERE tmp.l4ia_question_template = $templateId and tmp.l4ia_profession_id = $professionId and  tmp.deleted=1 and L4_I_ANS.id IS NULL AND L4_I_ANS.l4iapa_parent_id IS NULL AND L4_I_ANS.l4iapa_activity_id IS NULL AND L4_I_ANS.l4iapa_answer IS NULL"), array());

        if (isset($activities) && !empty($activities)) {
            //shuffle($activities);
            foreach ($activities as $key => $activity) {
                $optionIds = (isset($activity->optionIds) && $activity->optionIds != '') ? explode(",", $activity->optionIds) : '';
                $correctOption = explode("###", $activity->correctOption);
                $optionAsImage = explode("###", $activity->optionAsImage);
                $correctOrder = explode(",", $activity->correctOrder);

                $optionImageText = explode("###", $activity->optionImageText);
                $optionResponseText = explode("###", $activity->optionResponseText);
                $optionResponseImage = explode("###", $activity->optionResponseImage);


                $multiOption = array_count_values($correctOption);
                $multiOptionCount = (isset($multiOption['1'])) ? $multiOption['1'] : '';
                $activities[$key]->totalCorrectOptions = ($multiOptionCount != '')? $multiOptionCount : 0;
                $options = (isset($activity->options) && $activity->options != '') ? explode("###", $activity->options) : '';
                unset($activity->optionIds);
                unset($activity->options);
                unset($activity->optionResponseImage);
                unset($activity->optionResponseText);
                unset($activity->optionImageText);
                unset($activity->optionAsImage);

                $optionsWithId = [];
                if (isset($options) && !empty($options)) {
                    foreach ($options as $key1 => $option) {
                        $temp = [];
                        $temp['optionId'] = $optionIds[$key1];
                        $temp['optionText'] = (isset($option)) ? $option : '';
                        $temp['correctOption'] = (isset($correctOption[$key1])) ? $correctOption[$key1] : 0;
                        $temp['correctOrder'] = (isset($correctOrder[$key1])) ? $correctOrder[$key1] : 0;
                        $temp['optionImageText'] = (isset($optionImageText[$key1])) ? $optionImageText[$key1] : '';
                        $temp['optionResponseText'] = (isset($optionResponseText[$key1])) ? $optionResponseText[$key1] : '';

                        if (isset($optionAsImage[$key1]) && $optionAsImage[$key1] != '') {
                            if ($optionAsImage[$key1] != '' && isset($optionAsImage[$key1])) {
                                $temp['optionAsImage'] = Storage::url(Config::get('constant.LEVEL4_INTERMEDIATE_ANSWER_ORIGINAL_IMAGE_UPLOAD_PATH') . $optionAsImage[$key1]);
                            } else {
                                $temp['optionAsImage'] = Storage::url(Config::get('constant.LEVEL4_INTERMEDIATE_ANSWER_ORIGINAL_IMAGE_UPLOAD_PATH') . "proteen-logo.png");
                            }
                        } else {
                            $temp['optionAsImage'] = '';
                        }
                        if (isset($optionResponseImage[$key1]) && $optionResponseImage[$key1] != '') {
                            if ($optionResponseImage[$key1] != '' && isset($optionResponseImage[$key1])) {
                                $temp['optionResponseImage'] = Config::get('constant.LEVEL4_INTERMEDIATE_RESPONSE_ORIGINAL_IMAGE_UPLOAD_PATH') . $optionResponseImage[$key1];
                            } else {
                                $temp['optionResponseImage'] = Config::get('constant.LEVEL4_INTERMEDIATE_RESPONSE_ORIGINAL_IMAGE_UPLOAD_PATH') . "proteen-logo.png";
                            }
                        } else {
                            $temp['optionResponseImage'] = '';
                        }
                        $optionsWithId[] = $temp;
                    }
                }
                $activities[$key]->options = $optionsWithId;
            }
        } else {
            $activities = '';
        }
        return $activities;
    }

    public function getNoOfTotalIntermediateQuestionsAttemptedQuestionForParent($parentId, $professionId, $templateId) {
        $result = DB::select(DB::raw("select (SELECT count(DISTINCT(l4_ic.id)) FROM ".config::get('databaseconstants.TBL_LEVEL4_INTERMEDIATE_ACTIVITY')." AS l4_ic join ".config::get('databaseconstants.TBL_LEVEL4_INTERMEDIATE_ACTIVITY_OPTIONS')." AS l4_ao on l4_ic.id = l4_ao.l4iao_question_id where l4_ic.deleted=1 AND l4_ic.l4ia_profession_id = ".$professionId." AND l4_ic.l4ia_question_template = ".$templateId.") as 'NoOfTotalQuestions', (select count(DISTINCT(L4_I_ANS.l4iapa_activity_id)) from " . config::get('databaseconstants.TBL_LEVEL4_INTERMEDIATE_ACTIVITY') . " AS L4_I_AC join " . config::get('databaseconstants.TBL_LEVEL4_INTERMEDIATE_ACTIVITY_PARENT') . " AS L4_I_ANS on L4_I_AC.id = L4_I_ANS.l4iapa_activity_id  where L4_I_AC.deleted=1 AND L4_I_ANS.l4iapa_parent_id=" . $parentId . " AND L4_I_AC.l4ia_profession_id = " . $professionId . " AND L4_I_AC.l4ia_question_template=" . $templateId . ") as 'NoOfAttemptedQuestions' "), array());
        return $result;
    }

    public function saveParentIntermediateActivitySingleLineAnswer($parentId, $data) {

        $parentIntermediateAnswer = DB::table(config::get('databaseconstants.TBL_LEVEL4_INTERMEDIATE_ACTIVITY_PARENT'))->where(["l4iapa_parent_id" => $data['l4iapa_parent_id'], "l4iapa_activity_id" => $data['l4iapa_activity_id'], "l4iapa_profession_id" => $data['l4iapa_profession_id'], "l4iapa_template_id" => $data['l4iapa_template_id']])->first();
        if (isset($parentIntermediateAnswer) && !empty($parentIntermediateAnswer)) {
            DB::table(config::get('databaseconstants.TBL_LEVEL4_INTERMEDIATE_ACTIVITY_PARENT'))->where('id', $parentIntermediateAnswer->id)->update($data);
        } else {
            DB::table(config::get('databaseconstants.TBL_LEVEL4_INTERMEDIATE_ACTIVITY_PARENT'))->insert($data);
        }

        $parentLevel4PointsRow = [];
        $parentLevel4PointsRow['plb_parent_id'] = $parentId;
        $parentLevel4PointsRow['plb_level'] = config::get('constant.LEVEL4_ID');
        $parentLevel4PointsRow['plb_profession'] = $data['l4iapa_profession_id'];

        $teenagerLevelPoints = DB::table(config::get('databaseconstants.TBL_PARENT_LEVEL_BOOSTERS'))->where(["plb_parent_id" => $parentId, "plb_level" => config::get('constant.LEVEL4_ID'), "plb_profession" => $data['l4iapa_profession_id']])->first();

        if ($teenagerLevelPoints) {
            $teenagerLevelPoints = (array) $teenagerLevelPoints;
            $parentLevel4PointsRow['plb_points'] = $teenagerLevelPoints['plb_points'] + $data['l4iapa_earned_point'];
            DB::table(config::get('databaseconstants.TBL_PARENT_LEVEL_BOOSTERS'))->where('id', $teenagerLevelPoints['id'])->update($parentLevel4PointsRow);
        } else {
            $parentLevel4PointsRow['plb_points'] = $data['l4iapa_earned_point'];
            DB::table(config::get('databaseconstants.TBL_PARENT_LEVEL_BOOSTERS'))->insert($parentLevel4PointsRow);
        }
        $returnArray = [];
        $returnArray['questionsID'] = $data['l4iapa_activity_id'];
        $returnArray['total_Points'] = $data['l4iapa_earned_point'];
        return $returnArray;
    }

    public function saveParentIntermediateActivityDropDownAnswer($parentId, $data) {

        $parentIntermediateAnswer = DB::table(config::get('databaseconstants.TBL_LEVEL4_INTERMEDIATE_ACTIVITY_PARENT'))->where(["l4iapa_parent_id" => $data['l4iapa_parent_id'], "l4iapa_activity_id" => $data['l4iapa_activity_id'], "l4iapa_profession_id" => $data['l4iapa_profession_id'], "l4iapa_template_id" => $data['l4iapa_template_id']])->first();
        if (isset($parentIntermediateAnswer) && !empty($parentIntermediateAnswer)) {
            DB::table(config::get('databaseconstants.TBL_LEVEL4_INTERMEDIATE_ACTIVITY_PARENT'))->where('id', $parentIntermediateAnswer->id)->update($data);
        } else {
            DB::table(config::get('databaseconstants.TBL_LEVEL4_INTERMEDIATE_ACTIVITY_PARENT'))->insert($data);
        }

        $parentLevel4PointsRow = [];
        $parentLevel4PointsRow['plb_parent_id'] = $parentId;
        $parentLevel4PointsRow['plb_level'] = config::get('constant.LEVEL4_ID');
        $parentLevel4PointsRow['plb_profession'] = $data['l4iapa_profession_id'];

        $teenagerLevelPoints = DB::table(config::get('databaseconstants.TBL_PARENT_LEVEL_BOOSTERS'))->where(["plb_parent_id" => $parentId, "plb_level" => config::get('constant.LEVEL4_ID'), "plb_profession" => $data['l4iapa_profession_id']])->first();

        if ($teenagerLevelPoints) {
            $teenagerLevelPoints = (array) $teenagerLevelPoints;
            $parentLevel4PointsRow['plb_points'] = $teenagerLevelPoints['plb_points'] + $data['l4iapa_earned_point'];
            DB::table(config::get('databaseconstants.TBL_PARENT_LEVEL_BOOSTERS'))->where('id', $teenagerLevelPoints['id'])->update($parentLevel4PointsRow);
        } else {
            $parentLevel4PointsRow['plb_points'] = $data['l4iapa_earned_point'];
            DB::table(config::get('databaseconstants.TBL_PARENT_LEVEL_BOOSTERS'))->insert($parentLevel4PointsRow);
        }
        $returnArray = [];
        $returnArray['questionsID'] = $data['l4iapa_parent_id'];
        $returnArray['total_Points'] = $data['l4iapa_earned_point'];
        return $returnArray;
    }


    public function saveParentIntermediateActivityImageReorderAnswer($parentId, $data, $answer) {
        foreach ($answer as $key => $detail) {
            $data['l4iapa_answer'] = $detail;
            $data['l4iapa_order'] = $key;
            $parentIntermediateAnswer = DB::table(config::get('databaseconstants.TBL_LEVEL4_INTERMEDIATE_ACTIVITY_PARENT'))->where(["l4iapa_parent_id" => $data['l4iapa_parent_id'], "l4iapa_activity_id" => $data['l4iapa_activity_id'], "l4iapa_profession_id" => $data['l4iapa_profession_id'], "l4iapa_template_id" => $data['l4iapa_template_id']])->first();
            if (isset($parentIntermediateAnswer) && !empty($parentIntermediateAnswer)) {
                DB::table(config::get('databaseconstants.TBL_LEVEL4_INTERMEDIATE_ACTIVITY_PARENT'))->where('id', $parentIntermediateAnswer->id)->update($data);
            } else {
                DB::table(config::get('databaseconstants.TBL_LEVEL4_INTERMEDIATE_ACTIVITY_PARENT'))->insert($data);
            }
        }

        $parentLevel4PointsRow = [];
        $parentLevel4PointsRow['plb_parent_id'] = $parentId;
        $parentLevel4PointsRow['plb_level'] = config::get('constant.LEVEL4_ID');
        $parentLevel4PointsRow['plb_profession'] = $data['l4iapa_profession_id'];

        $teenagerLevelPoints = DB::table(config::get('databaseconstants.TBL_PARENT_LEVEL_BOOSTERS'))->where(["plb_parent_id" => $parentId, "plb_level" => config::get('constant.LEVEL4_ID'), "plb_profession" => $data['l4iapa_profession_id']])->first();

        if ($teenagerLevelPoints) {
            $teenagerLevelPoints = (array) $teenagerLevelPoints;
            $parentLevel4PointsRow['plb_points'] = $teenagerLevelPoints['plb_points'] + $data['l4iapa_earned_point'];
            DB::table(config::get('databaseconstants.TBL_PARENT_LEVEL_BOOSTERS'))->where('id', $teenagerLevelPoints['id'])->update($parentLevel4PointsRow);
        } else {
            $parentLevel4PointsRow['plb_points'] = $data['l4iapa_earned_point'];
            DB::table(config::get('databaseconstants.TBL_PARENT_LEVEL_BOOSTERS'))->insert($parentLevel4PointsRow);
        }
        $returnArray = [];
        $returnArray['questionsID'] = $data['l4iapa_parent_id'];
        $returnArray['total_Points'] = $data['l4iapa_earned_point'];
        return $returnArray;
    }


    public function saveParentIntermediateActivityFillInBlanksAnswer($parentId, $data, $answer) {
        foreach ($answer as $detail) {
            $data['l4iapa_answer'] = $detail;
            $parentIntermediateAnswer = DB::table(config::get('databaseconstants.TBL_LEVEL4_INTERMEDIATE_ACTIVITY_PARENT'))->where(["l4iapa_parent_id" => $data['l4iapa_parent_id'], "l4iapa_activity_id" => $data['l4iapa_activity_id'], "l4iapa_profession_id" => $data['l4iapa_profession_id'], "l4iapa_template_id" => $data['l4iapa_template_id']])->first();
            if (isset($parentIntermediateAnswer) && !empty($parentIntermediateAnswer)) {
                DB::table(config::get('databaseconstants.TBL_LEVEL4_INTERMEDIATE_ACTIVITY_PARENT'))->where('id', $parentIntermediateAnswer->id)->update($data);
            } else {
                DB::table(config::get('databaseconstants.TBL_LEVEL4_INTERMEDIATE_ACTIVITY_PARENT'))->insert($data);
            }
        }

        $parentLevel4PointsRow = [];
        $parentLevel4PointsRow['plb_parent_id'] = $parentId;
        $parentLevel4PointsRow['plb_level'] = config::get('constant.LEVEL4_ID');
        $parentLevel4PointsRow['plb_profession'] = $data['l4iapa_profession_id'];

        $teenagerLevelPoints = DB::table(config::get('databaseconstants.TBL_PARENT_LEVEL_BOOSTERS'))->where(["plb_parent_id" => $parentId, "plb_level" => config::get('constant.LEVEL4_ID'), "plb_profession" => $data['l4iapa_profession_id']])->first();

        if ($teenagerLevelPoints) {
            $teenagerLevelPoints = (array) $teenagerLevelPoints;
            $parentLevel4PointsRow['plb_points'] = $teenagerLevelPoints['plb_points'] + $data['l4iapa_earned_point'];
            DB::table(config::get('databaseconstants.TBL_PARENT_LEVEL_BOOSTERS'))->where('id', $teenagerLevelPoints['id'])->update($parentLevel4PointsRow);
        } else {
            $parentLevel4PointsRow['plb_points'] = $data['l4iapa_earned_point'];
            DB::table(config::get('databaseconstants.TBL_PARENT_LEVEL_BOOSTERS'))->insert($parentLevel4PointsRow);
        }
        $returnArray = [];
        $returnArray['questionsID'] = $data['l4iapa_parent_id'];
        $returnArray['total_Points'] = $data['l4iapa_earned_point'];
        return $returnArray;
    }

    public function getParentAllVerifiedTasks($parent,$profession)
    {
        $result = DB::table(config::get('databaseconstants.TBL_LEVEL4_ADVANCE_ACTIVITY_PARENT_DATA') . " AS usertask")
                ->join(config::get('databaseconstants.TBL_PROFESSIONS') . " AS profession", 'usertask.l4aapa_profession_id', '=', 'profession.id')
                ->join(config::get('databaseconstants.TBL_PARENTS') . " AS parent", 'usertask.l4aapa_parent_id', '=', 'parent.id')
                ->selectRaw('usertask.*,profession.pf_name,parent.p_first_name')
                ->where('usertask.l4aapa_parent_id','=',$parent)
                ->where('usertask.l4aapa_profession_id','=',$profession)
                ->where('usertask.deleted','!=',3)
                ->where('usertask.l4aapa_is_verified','=',2)
                ->get();
        return $result;
    }


     public function getLevel4AdvanceActivityTaskByParent($parent,$professionId,$mediaType) {

        $result = DB::table(config::get('databaseconstants.TBL_LEVEL4_ADVANCE_ACTIVITY_PARENT_DATA') . " AS activity")
                ->leftjoin(config::get('databaseconstants.TBL_ADMIN_USERS') . " AS admin", 'activity.l4aapa_verified_by', '=', 'admin.id')
                ->selectRaw('activity.* , admin.id as adminid, admin.name as adminname')
                ->where('activity.deleted', 1)
                ->where('activity.l4aapa_parent_id', $parent)
                ->where('activity.l4aapa_profession_id', $professionId)
                ->where('activity.l4aapa_media_type', $mediaType)
    ->get();
        return $result;
    }

    public function saveLevel4AdvanceActivityParent($level4AdvanceData) {
        if ($level4AdvanceData['id'] != '' && $level4AdvanceData['id'] > 0) {
            $response = DB::table(config::get('databaseconstants.TBL_LEVEL4_ADVANCE_ACTIVITY_PARENT_DATA'))->where('id', $level4AdvanceData['id'])->update($level4AdvanceData);
        } else {
            $response = DB::table(config::get('databaseconstants.TBL_LEVEL4_ADVANCE_ACTIVITY_PARENT_DATA'))->insert($level4AdvanceData);
        }
        return $response;
    }

     public function updateStatusAdvanceTaskParent($dataid,$updateData)
    {
        if ($dataid != '' && $dataid > 0) {
            $response = DB::table(config::get('databaseconstants.TBL_LEVEL4_ADVANCE_ACTIVITY_PARENT_DATA'))->where('id', $dataid)->update($updateData);
        }
    }

    public function getUserAdvanceTaskByIdForParent($id) {

        $result = DB::table(config::get('databaseconstants.TBL_LEVEL4_ADVANCE_ACTIVITY_PARENT_DATA') . " AS usertask")
                ->join(config::get('databaseconstants.TBL_PROFESSIONS') . " AS profession", 'usertask.l4aapa_profession_id', '=', 'profession.id')
                ->selectRaw('usertask.*,profession.pf_name')
                ->where('usertask.id','=',$id)
                ->where('usertask.deleted','!=',3)
                ->get();
        return $result;
    }


    public function deleteParentAdvanceTask($id)
    {
        if ($id != '' && $id > 0) {
            $response = DB::table(config::get('databaseconstants.TBL_LEVEL4_ADVANCE_ACTIVITY_PARENT_DATA'))->where('id', $id)->delete();
        }
        return $response;
    }


    /*Get Parents all tasks for AdvanceLevel which are submitted for review*/
    public function getParentTaskForAdmin() {
        $result = DB::table(config::get('databaseconstants.TBL_LEVEL4_ADVANCE_ACTIVITY_PARENT_DATA') . " AS usertask")
                ->join(config::get('databaseconstants.TBL_PROFESSIONS') . " AS profession", 'usertask.l4aapa_profession_id', '=', 'profession.id')
                ->join(config::get('databaseconstants.TBL_PARENTS') . " AS parent", 'usertask.l4aapa_parent_id', '=', 'parent.id')
                ->selectRaw('usertask.*,profession.pf_name,parent.p_first_name')
                ->groupBy('usertask.l4aapa_profession_id')
                ->groupBy('usertask.l4aapa_parent_id')
                ->where('usertask.deleted', '<>', Config::get('constant.DELETED_FLAG'))
                ->where('l4aapa_is_verified', '<>', 0)
                ->get();
        return $result;
    }


    /*Get Parent all tasks for AdvanceLevel by profession*/

    public function getParentAllTasksForAdvanceLevel($parent,$profession,$type) {

        $result = DB::table(config::get('databaseconstants.TBL_LEVEL4_ADVANCE_ACTIVITY_PARENT_DATA') . " AS usertask")
                ->join(config::get('databaseconstants.TBL_PROFESSIONS') . " AS profession", 'usertask.l4aapa_profession_id', '=', 'profession.id')
                ->join(config::get('databaseconstants.TBL_PARENTS') . " AS parent", 'usertask.l4aapa_parent_id', '=', 'parent.id')
                ->leftjoin(config::get('databaseconstants.TBL_ADMIN_USERS') . " AS admin", 'usertask.l4aapa_verified_by', '=', 'admin.id')
                ->selectRaw('usertask.*,profession.pf_name,parent.p_first_name,admin.id as adminid, admin.name as adminname')
                ->where('usertask.l4aapa_parent_id','=',$parent)
                ->where('usertask.l4aapa_profession_id','=',$profession)
                ->where('usertask.deleted','!=',3)
                ->where('l4aapa_is_verified','!=',0)
                ->where('l4aapa_media_type',$type)
                ->get();
        return $result;
    }


     public function getImageNameByIdForParent($id) {
        $return = DB::table(config::get('databaseconstants.TBL_LEVEL4_ADVANCE_ACTIVITY_PARENT_DATA'))
                        ->select('l4aapa_media_name')
                        ->where('id', '=' ,$id)
                        ->get();
        return $return;
    }


    /*Update parent task status
     */
    public function updateParentTaskStatusByAdmin($id,$updateData)
    {
        if ($id != '' && $id > 0) {
            $response = DB::table(config::get('databaseconstants.TBL_LEVEL4_ADVANCE_ACTIVITY_PARENT_DATA'))->where('id', $id)->update($updateData);
        }
        return $response;
    }
}
