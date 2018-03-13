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
use App\ProfessionLearningStyle;
use App\UserLearningStyle;
use Carbon\Carbon;  
use App\TeenagerBoosterPoint;
use App\Services\Professions\Contracts\ProfessionsRepository;
use App\Services\Level4Activity\Contracts\Level4ActivitiesRepository;
use App\PromisePlus;
use App\Jobs\CalculateProfessionCompletePercentage;
use App\Level4Answers;
use App\LearningStyle;
use App\Professions;
use App\Level4ProfessionProgress;

class Level4ActivityController extends Controller {

    public function __construct(Level4ActivitiesRepository $level4ActivitiesRepository, TeenagersRepository $teenagersRepository, ProfessionsRepository $professionsRepository) 
    {        
        $this->professionsRepository = $professionsRepository;
        $this->teenagersRepository = $teenagersRepository;   
        $this->level4ActivitiesRepository = $level4ActivitiesRepository;   
        $this->teenagerBoosterPoint = new TeenagerBoosterPoint();
        $this->extraQuestionDescriptionTime = Config::get('constant.EXTRA_QUESTION_DESCRIPTION');
        $this->questionDescriptionORIGINALImage = Config::get('constant.LEVEL4_INTERMEDIATE_QUESTION_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->questionDescriptionTHUMBImage = Config::get('constant.LEVEL4_INTERMEDIATE_QUESTION_THUMB_IMAGE_UPLOAD_PATH');
        $this->optionORIGINALImage = Config::get('constant.LEVEL4_INTERMEDIATE_ANSWER_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->optionTHUMBImage = Config::get('constant.LEVEL4_INTERMEDIATE_ANSWER_THUMB_IMAGE_UPLOAD_PATH');
        $this->answerResponseImageOriginal = Config::get('constant.LEVEL4_INTERMEDIATE_RESPONSE_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->objPromisePlus = new PromisePlus();
        $this->learningStyleThumbImageUploadPath = Config::get('constant.LEARNING_STYLE_THUMB_IMAGE_UPLOAD_PATH');
        $this->objLevel4ProfessionProgress = new Level4ProfessionProgress();
    }

    /*
     * Get L4 Basic career questions
    */
    public function professionBasicQuestion() {
        $professionId = Input::get('professionId');
        $userId = Auth::guard('teenager')->user()->id;
        if($userId > 0 && $professionId != '') {
            $totalQuestion = $this->level4ActivitiesRepository->getNoOfTotalQuestionsAttemptedQuestion($userId, $professionId);
            $professionName = $this->professionsRepository->getProfessionNameById($professionId);
            $basicCompleted = 0; 
            if(isset($totalQuestion[0]->NoOfTotalQuestions) && $totalQuestion[0]->NoOfTotalQuestions > 0 && ($totalQuestion[0]->NoOfTotalQuestions == $totalQuestion[0]->NoOfAttemptedQuestions) ) {
                $basicCompleted = 1;
                dispatch( new CalculateProfessionCompletePercentage($userId, $professionId) );
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
            $response['professionName'] = $professionName;
            $response['teenagerName'] = Auth::guard('teenager')->user()->t_name . ' '.Auth::guard('teenager')->user()->t_lastname;
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
                    $answerID = 0;
                    $timer = 0;
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

                $templateId = "L4B";
                $objProfessionLearningStyle = new ProfessionLearningStyle();
                $learningId = $objProfessionLearningStyle->getIdByProfessionIdForAdvance($professionId, $templateId);
                if ($learningId != '') {
                    $objUserLearningStyle = new UserLearningStyle();
                    $learningData = $objUserLearningStyle->getUserLearningStyle($learningId);
                    if (!empty($learningData)) {
                        $array['points'] += $learningData->uls_earned_points;
                    }
                    $userData = [];
                    $userData['uls_learning_style_id'] = $learningId;
                    $userData['uls_profession_id'] = $professionId;
                    $userData['uls_teenager_id'] = $userId;
                    $userData['uls_earned_points'] = $array['points'];
                    $result = $objUserLearningStyle->saveUserLearningStyle($userData);
                }

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

    /*
     * Get L4 Intermediate career questions
    */
    public function professionIntermediateQuestion() {
        $professionId = Input::get('professionId');
        $templateId = Input::get('templateId');
        $userId = Auth::guard('teenager')->user()->id;
        if($userId > 0 && $professionId != '' && $templateId != '') {
            $intermediateActivities = $this->level4ActivitiesRepository->getNotAttemptedIntermediateActivities($userId, $professionId, $templateId);
            $totalIntermediateQuestion = $this->level4ActivitiesRepository->getNoOfTotalIntermediateQuestionsAttemptedQuestion($userId, $professionId, $templateId);
            $professionName = $this->professionsRepository->getProfessionNameById($professionId);
            
            $intermediateCompleted = 0;
            if(isset($totalIntermediateQuestion[0]->NoOfTotalQuestions) && $totalIntermediateQuestion[0]->NoOfTotalQuestions > 0 && ($totalIntermediateQuestion[0]->NoOfAttemptedQuestions >= $totalIntermediateQuestion[0]->NoOfTotalQuestions) ) {
                $intermediateCompleted = 1;
                dispatch( new CalculateProfessionCompletePercentage($userId, $professionId) );
            }
            
            //$activities = $this->level4ActivitiesRepository->getNotAttemptedActivities($userId, $professionId);
            if (isset($intermediateActivities[0]) && !empty($intermediateActivities[0])) {
                $intermediateActivitiesData = $intermediateActivities[0];
                
                $intermediateActivitiesData->gt_temlpate_answer_type = Helpers::getAnsTypeFromGamificationTemplateId($intermediateActivitiesData->l4ia_question_template);
                $intermediateActivitiesData->l4ia_extra_question_time = $this->extraQuestionDescriptionTime;
                $timer = $intermediateActivitiesData->l4ia_question_time;
                $response['timer'] = $intermediateActivitiesData->l4ia_question_time;
                //Question Popup Image
                $intermediateActivitiesData->l4ia_question_popup_image = ($intermediateActivitiesData->l4ia_question_popup_image != "" && Storage::size($this->questionDescriptionTHUMBImage . $intermediateActivitiesData->l4ia_question_popup_image) > 0) ? Storage::url($this->questionDescriptionTHUMBImage . $intermediateActivitiesData->l4ia_question_popup_image) : '';
                $intermediateActivitiesData->l4ia_question_popup_description = ($intermediateActivitiesData->l4ia_question_popup_description != "") ? $intermediateActivitiesData->l4ia_question_popup_description : '';
                
                //Set Question audio
                if (isset($intermediateActivitiesData->l4ia_question_audio) && $intermediateActivitiesData->l4ia_question_audio != '') {
                        $intermediateActivitiesData->l4ia_question_audio = Storage::url($this->questionDescriptionORIGINALImage . $intermediateActivitiesData->l4ia_question_audio);
                } else {
                    $intermediateActivitiesData->l4ia_question_audio = '';
                }

                //Set question youtube video
                $getQuestionVideo = $this->level4ActivitiesRepository->getQuestionVideo($intermediateActivitiesData->activityID);
                if (isset($getQuestionVideo['video']) && !empty($getQuestionVideo['video'])) {
                    $intermediateActivitiesData->l4ia_question_video = $getQuestionVideo['video'];
                } else {
                    $intermediateActivitiesData->l4ia_question_video = '';
                }

                //Set question images
                $getQuestionImage = $this->level4ActivitiesRepository->getQuestionMultipleImages($intermediateActivitiesData->activityID);
                if (isset($getQuestionImage) && !empty($getQuestionImage)) {
                    foreach ($getQuestionImage as $key => $image) {
                        $intermediateActivitiesData->question_images[$key]['l4ia_question_image'] = ( $image['image'] != "" && Storage::size($this->questionDescriptionTHUMBImage . $image['image']) > 0 ) ? Storage::url($this->questionDescriptionTHUMBImage . $image['image']) : Storage::url($this->questionDescriptionTHUMBImage . 'proteen-logo.png');
                        $intermediateActivitiesData->question_images[$key]['l4ia_question_imageDescription'] = $image['imageDescription'];
                    }
                } else {
                    $intermediateActivitiesData->l4ia_question_image = $intermediateActivitiesData->l4ia_question_imageDescription = '';
                }

            } else {
                $intermediateActivitiesData = [];
                $timer = 0;
            }
            //print_r($intermediateActivitiesData); die();
            $response = [];
            $response['message'] = trans('appmessages.default_success_msg');
            $response['NoOfTotalQuestions'] = $totalIntermediateQuestion[0]->NoOfTotalQuestions;
            $response['NoOfAttemptedQuestions'] = $totalIntermediateQuestion[0]->NoOfAttemptedQuestions;
            $response['intermediateCompleted'] = $intermediateCompleted;
            $response['data'] = $intermediateActivitiesData;
            $response['timer'] = $timer;
            $response['professionId'] = $professionId;
            $response['professionName'] = $professionName;
            $response['teenagerName'] = Auth::guard('teenager')->user()->t_name . ' '.Auth::guard('teenager')->user()->t_lastname;
            $response['status'] = 1;
            //print_r($intermediateActivitiesData); die();
            if(isset($intermediateActivitiesData->gt_temlpate_answer_type) && in_array($intermediateActivitiesData->gt_temlpate_answer_type, ["option_choice", "option_choice_with_response", "true_false", "filling_blank"]) ) { 
                return view('teenager.basic.careerIntermediateOptionChoiceQuestion', compact('response'));
            } else if(isset($intermediateActivitiesData->gt_temlpate_answer_type) && $intermediateActivitiesData->gt_temlpate_answer_type == "single_line_answer") {
                return view('teenager.basic.careerIntermediateSingleLineQuestion', compact('response'));
            } else if(isset($intermediateActivitiesData->gt_temlpate_answer_type) && $intermediateActivitiesData->gt_temlpate_answer_type == "select_from_dropdown_option") {
                return view('teenager.basic.careerIntermediateDropDownSelectQuestion', compact('response'));
            } else if(isset($intermediateActivitiesData->gt_temlpate_answer_type) && $intermediateActivitiesData->gt_temlpate_answer_type == "option_reorder") {
                return view('teenager.basic.careerIntermediateOptionReorderQuestion', compact('response'));
            } else if(isset($intermediateActivitiesData->gt_temlpate_answer_type) && $intermediateActivitiesData->gt_temlpate_answer_type == "image_reorder") {
                return view('teenager.basic.careerIntermediateImageReorderQuestion', compact('response'));
                //return view('teenager.basic.careerIntermediateQuizQuestion', compact('response'));
            } else {
                
            }
            return view('teenager.basic.careerIntermediateQuizQuestion', compact('response'));
        }
        $response['status'] = 0;
        $response['message'] = "Something went wrong!";

        return response()->json($response, 200);
        exit;
    }

    /*
     * Save L4 Intermediate career questions
    */
    public function saveIntermediateLevelActivity() {
        $response = [];
        $userId = Auth::guard('teenager')->user()->id;
        
        if ($userId && $userId > 0) {
            $body = Input::all();

            $body['userid'] = Auth::guard('teenager')->user()->id;
            $body['timer'] = (isset($body['timer'])) ? $body['timer'] : 0;
            $questionID = (isset($body['questionID']) && $body['questionID'] > 0) ? $body['questionID'] : '';
            $body['questionID'] = $questionID;
            $body['answer'][0] = (isset($body['answer'][0])) ? $body['answer'][0] : 0;
            $checkOptionIsTrulyQuestionOption = false;

            $getAllQuestionRelatedDataFromQuestionId = $this->level4ActivitiesRepository->getAllIntermediateQuestionRelatedDataFromQuestionId($questionID);
            
            if (isset($getAllQuestionRelatedDataFromQuestionId->options_id) && $getAllQuestionRelatedDataFromQuestionId->gt_temlpate_answer_type != "image_reorder" && $getAllQuestionRelatedDataFromQuestionId->gt_temlpate_answer_type != "option_reorder") {
                $optionsIdArray = explode(',', $getAllQuestionRelatedDataFromQuestionId->options_id);

                if ($body['answer'][0] != 0 && $body['ajax_answer_type'] != 'single_line_answer') {
                    foreach ($body['answer'] as $ID) {
                        if (in_array($ID, $optionsIdArray)) {
                            $checkOptionIsTrulyQuestionOption = true;
                        } else {
                            $checkOptionIsTrulyQuestionOption = false;
                        }
                    }
                } else {
                    $checkOptionIsTrulyQuestionOption = true;
                }
            } else {
                $checkOptionIsTrulyQuestionOption = true;
            }

            if (isset($getAllQuestionRelatedDataFromQuestionId->l4ia_question_time)) {
                if ($body['timer'] != 0 && $body['timer'] > 0 && ($getAllQuestionRelatedDataFromQuestionId->gt_temlpate_answer_type != "filling_blank" || $getAllQuestionRelatedDataFromQuestionId->gt_temlpate_answer_type != "image_reorder" || $getAllQuestionRelatedDataFromQuestionId->gt_temlpate_answer_type != "option_choice_with_response") && ($body['timer'] < $getAllQuestionRelatedDataFromQuestionId->l4ia_question_time)) {
                    $body['timer'] = $getAllQuestionRelatedDataFromQuestionId->l4ia_question_time - $body['timer'];
                } else if (($getAllQuestionRelatedDataFromQuestionId->gt_temlpate_answer_type == "filling_blank" || $getAllQuestionRelatedDataFromQuestionId->gt_temlpate_answer_type == "image_reorder" ) && $body['timer'] != 0 && $body['timer'] > 0) {
                    $body['timer'] = ($getAllQuestionRelatedDataFromQuestionId->l4ia_question_time + $this->extraQuestionDescriptionTime) - $body['timer'];
                } else if ($getAllQuestionRelatedDataFromQuestionId->gt_temlpate_answer_type == "option_choice_with_response" && $body['timer'] != 0 && $body['timer'] > 0) {
                    if ($getAllQuestionRelatedDataFromQuestionId->l4ia_question_description != '') {
                        $body['timer'] = ($getAllQuestionRelatedDataFromQuestionId->l4ia_question_time + $this->extraQuestionDescriptionTime) - $body['timer'];
                    } else {
                        $body['timer'] = $getAllQuestionRelatedDataFromQuestionId->l4ia_question_time - $body['timer'];
                    }
                } else {
                    $body['timer'] = 0;
                    $body['answer'][0] = 0;
                }
            }
            if (isset($getAllQuestionRelatedDataFromQuestionId->correct_option)) {
                $multiOption = array_count_values(explode(",", $getAllQuestionRelatedDataFromQuestionId->correct_option));
                $multiOptionCount = (isset($multiOption['1'])) ? $multiOption['1'] : 0;
            }
            if ($checkOptionIsTrulyQuestionOption) {
                $professionId = isset($getAllQuestionRelatedDataFromQuestionId->l4ia_profession_id) ? $getAllQuestionRelatedDataFromQuestionId->l4ia_profession_id : '';
                $response['profession_id'] = $professionId;
                $total = $this->teenagersRepository->getTeenagerTotalBoosterPoints($userId);
                
                if (isset($getAllQuestionRelatedDataFromQuestionId) && !empty($getAllQuestionRelatedDataFromQuestionId)) {
                    
                    if ($getAllQuestionRelatedDataFromQuestionId->gt_temlpate_answer_type == "single_line_answer" && isset($body['answer'][0])) {
                        $userAnswer = strtolower(str_replace(' ', '', trim($body['answer'][0])));
                        $systemCorrectAnswer = strtolower(str_replace(' ', '', trim($getAllQuestionRelatedDataFromQuestionId->correct_option)));
                        if ($userAnswer === $systemCorrectAnswer) {
                            $response['answerRightWrongMsg'] = $getAllQuestionRelatedDataFromQuestionId->l4ia_question_right_message;
                            $response['systemCorrectAnswer'] = 1;
                            $earnedPoints = $getAllQuestionRelatedDataFromQuestionId->l4ia_question_point;
                        } else {
                            $response['answerRightWrongMsg'] = $getAllQuestionRelatedDataFromQuestionId->l4ia_question_wrong_message;
                            $response['systemCorrectAnswer'] = 0;
                            $earnedPoints = 0;
                        }
                        $response['systemCorrectAnswerText'] = $getAllQuestionRelatedDataFromQuestionId->correct_option;
                        $data = [];
                        $data['l4iaua_teenager'] = $body['userid'];
                        $data['l4iaua_activity_id'] = $body['questionID'];
                        $data['l4iaua_profession_id'] = $professionId;
                        $data['l4iaua_template_id'] = $getAllQuestionRelatedDataFromQuestionId->l4ia_question_template;
                        $data['l4iaua_answer'] = (isset($body['timer']) && $body['timer'] != 0) ? $body['answer'][0] : 0;
                        $data['l4iaua_order'] = 0;
                        $data['l4iaua_earned_point'] = $earnedPoints;
                        $data['l4iaua_time'] = $body['timer'];
                        $templateId = $getAllQuestionRelatedDataFromQuestionId->l4ia_question_template;
                        $objProfessionLearningStyle = new ProfessionLearningStyle();
                        $learningId = $objProfessionLearningStyle->getIdByProfessionId($professionId,$templateId);
                        if ($learningId != '') {
                            $objUserLearningStyle = new UserLearningStyle();
                            $learningData = $objUserLearningStyle->getUserLearningStyle($learningId);
                            if (!empty($learningData)) {
                                $earnedPoints += $learningData->uls_earned_points;
                            }
                            $userData = [];
                            $userData['uls_learning_style_id'] = $learningId;
                            $userData['uls_profession_id'] = $professionId;
                            $userData['uls_teenager_id'] = $body['userid'];
                            $userData['uls_earned_points'] = $earnedPoints;
                            $result = $objUserLearningStyle->saveUserLearningStyle($userData);
                        }

                        $saveLevel4IntemediateActivityAnswer = $this->level4ActivitiesRepository->saveTeenagerIntermediateActivitySingleLineAnswer($body['userid'], $data);
                        $response['message'] = trans('appmessages.default_success_msg');
                        $response['status'] = 1;
                    } else if ($getAllQuestionRelatedDataFromQuestionId->gt_temlpate_answer_type == "select_from_dropdown_option" && isset($body['answer'][0])) {
                        $userAnswer = $body['answer'][0];
                        $userAnswerOrder = $body['answer_order'][0];
                        $systemCorrectOptionArray = explode(',', $getAllQuestionRelatedDataFromQuestionId->correct_option);
                        $systemCorrectOptionOrderArray = explode(',', $getAllQuestionRelatedDataFromQuestionId->option_order);
                        $systemCorrectOptionIdArray = explode(',', $getAllQuestionRelatedDataFromQuestionId->options_id);
                        $searchSystemCorrectOption = array_search(1, $systemCorrectOptionArray);
                        $searchSystemCorrectOptionOrder = (isset($systemCorrectOptionOrderArray[$searchSystemCorrectOption])) ? $systemCorrectOptionOrderArray[$searchSystemCorrectOption] : 0;
                        $searchSystemCorrectOptionId = (isset($systemCorrectOptionIdArray[$searchSystemCorrectOption])) ? $systemCorrectOptionIdArray[$searchSystemCorrectOption] : 0;
                        if (($userAnswer == $searchSystemCorrectOptionId) && ($userAnswerOrder == $searchSystemCorrectOptionOrder)) {
                            $response['answerRightWrongMsg'] = $getAllQuestionRelatedDataFromQuestionId->l4ia_question_right_message;
                            $response['systemCorrectAnswer'] = 1;
                            $earnedPoints = $getAllQuestionRelatedDataFromQuestionId->l4ia_question_point;
                        } else {
                            $response['answerRightWrongMsg'] = $getAllQuestionRelatedDataFromQuestionId->l4ia_question_wrong_message;
                            $response['systemCorrectAnswer'] = 0;
                            $earnedPoints = 0;
                        }
                        $response['systemCorrectOptionOrder'] = $searchSystemCorrectOptionOrder;
                        $response['systemCorrectOptionId'] = $searchSystemCorrectOptionId;
                        $data = [];
                        $data['l4iaua_teenager'] = $body['userid'];
                        $data['l4iaua_activity_id'] = $body['questionID'];
                        $data['l4iaua_profession_id'] = $professionId;
                        $data['l4iaua_template_id'] = $getAllQuestionRelatedDataFromQuestionId->l4ia_question_template;
                        $data['l4iaua_answer'] = (isset($body['timer']) && $body['timer'] != 0) ? $body['answer'][0] : 0;
                        $data['l4iaua_order'] = (isset($body['answer_order'][0])) ? $body['answer_order'][0] : 0;
                        $data['l4iaua_earned_point'] = $earnedPoints;
                        $data['l4iaua_time'] = $body['timer'];
                        $templateId = $getAllQuestionRelatedDataFromQuestionId->l4ia_question_template;
                        $objProfessionLearningStyle = new ProfessionLearningStyle();
                        $learningId = $objProfessionLearningStyle->getIdByProfessionId($professionId,$templateId);
                        if ($learningId != '') {
                            $objUserLearningStyle = new UserLearningStyle();
                            $learningData = $objUserLearningStyle->getUserLearningStyle($learningId);
                            if (!empty($learningData)) {
                                $earnedPoints += $learningData->uls_earned_points;
                            }
                            $userData = [];
                            $userData['uls_learning_style_id'] = $learningId;
                            $userData['uls_profession_id'] = $professionId;
                            $userData['uls_teenager_id'] = $body['userid'];
                            $userData['uls_earned_points'] = $earnedPoints;
                            $result = $objUserLearningStyle->saveUserLearningStyle($userData);
                        }

                        $saveLevel4IntemediateActivityAnswer = $this->level4ActivitiesRepository->saveTeenagerIntermediateActivityDropDownAnswer($body['userid'], $data);
                        $response['message'] = trans('appmessages.default_success_msg');
                        $response['status'] = 1;
                    } else if ( in_array($getAllQuestionRelatedDataFromQuestionId->gt_temlpate_answer_type, [ "image_reorder", "option_reorder" ] ) ) {
                        $orderArray = explode(',', $getAllQuestionRelatedDataFromQuestionId->option_order);
                        $optionsIdArray = explode(',', $getAllQuestionRelatedDataFromQuestionId->options_id);
                        $userAnswerIdArray = explode(',', $body['answer'][0]);

                        foreach ($optionsIdArray as $k => $opId) {
                            $newAssoArray[$orderArray[$k]] = $opId;
                            $userAnswerIdArray2[$k + 1] = isset($userAnswerIdArray[$k]) ? $userAnswerIdArray[$k] : 0;
                        }
                        ksort($newAssoArray);
                        if ($getAllQuestionRelatedDataFromQuestionId->gt_temlpate_answer_type == "option_reorder") {
                            foreach ($newAssoArray as $optionkey => $optionId) {
                                $response['systemCorrectOptionOrder'][] = $this->level4ActivitiesRepository->getOptionTextFromOptionId($optionId);
                            }
                        }

                        $orderArray = implode('', $newAssoArray);
                        $answerString = trim(str_replace(',', '', $body['answer'][0]));
                        if ($orderArray === $answerString) {
                            $response['answerRightWrongMsg'] = $getAllQuestionRelatedDataFromQuestionId->l4ia_question_right_message;
                            $response['systemCorrectAnswer'] = 1;
                            $earnedPoints = $getAllQuestionRelatedDataFromQuestionId->l4ia_question_point;
                        } else {
                            $response['answerRightWrongMsg'] = $getAllQuestionRelatedDataFromQuestionId->l4ia_question_wrong_message;
                            $response['systemCorrectAnswer'] = 0;
                            $earnedPoints = 0;
                        }

                        $data = [];
                        $data['l4iaua_teenager'] = $body['userid'];
                        $data['l4iaua_activity_id'] = $body['questionID'];
                        $data['l4iaua_profession_id'] = $professionId;
                        $data['l4iaua_template_id'] = $getAllQuestionRelatedDataFromQuestionId->l4ia_question_template;
                        $data['l4iaua_order'] = 0;
                        $data['l4iaua_earned_point'] = $earnedPoints;
                        $data['l4iaua_time'] = $body['timer'];
                        $templateId = $getAllQuestionRelatedDataFromQuestionId->l4ia_question_template;
                        $objProfessionLearningStyle = new ProfessionLearningStyle();
                        $learningId = $objProfessionLearningStyle->getIdByProfessionId($professionId,$templateId);
                        if ($learningId != '') {
                            $objUserLearningStyle = new UserLearningStyle();
                            $learningData = $objUserLearningStyle->getUserLearningStyle($learningId);
                            if (!empty($learningData)) {
                                $earnedPoints += $learningData->uls_earned_points;
                            }
                            $userData = [];
                            $userData['uls_learning_style_id'] = $learningId;
                            $userData['uls_profession_id'] = $professionId;
                            $userData['uls_teenager_id'] = $body['userid'];
                            $userData['uls_earned_points'] = $earnedPoints;
                            $result = $objUserLearningStyle->saveUserLearningStyle($userData);
                        }

                        $saveLevel4IntemediateActivityAnswer = $this->level4ActivitiesRepository->saveTeenagerIntermediateActivityImageReorderAnswer($body['userid'], $data, $userAnswerIdArray2);
                        $response['message'] = trans('appmessages.default_success_msg');
                        $response['status'] = 1;
                    } else if ( in_array( $getAllQuestionRelatedDataFromQuestionId->gt_temlpate_answer_type, ["filling_blank", "true_false", "option_choice", "option_choice_with_response"] ) ) {
                        $checkAnswerFromOption = '';
                        $correctOptionsArray = explode(',', $getAllQuestionRelatedDataFromQuestionId->correct_option);
                        
                        if ($getAllQuestionRelatedDataFromQuestionId->gt_temlpate_answer_type == "option_choice") {
                            $response['questionAnswerText'] = ($getAllQuestionRelatedDataFromQuestionId->l4ia_question_answer_description != '') ? $getAllQuestionRelatedDataFromQuestionId->l4ia_question_answer_description : '';
                        }
                        
                        if ($getAllQuestionRelatedDataFromQuestionId->gt_temlpate_answer_type == "option_choice_with_response") {
                            if (isset($body['answer'][0]) && $body['answer'][0] != 0) {
                                $getAnswerResponseTextAndImage = $this->level4ActivitiesRepository->getAnswerResponseTextAndImage($body['answer'][0]);
                                if (isset($getAnswerResponseTextAndImage) && !empty($getAnswerResponseTextAndImage)) {
                                    $response['questionAnswerText'] = ( isset($getAnswerResponseTextAndImage['answerResponseText']) && $getAnswerResponseTextAndImage['answerResponseText'] != '') ? $getAnswerResponseTextAndImage['answerResponseText'] : '';
                                    if (isset($getAnswerResponseTextAndImage['answerResponseImage']) && $getAnswerResponseTextAndImage['answerResponseImage'] != '') {
                                        if (Storage::size($this->answerResponseImageOriginal . $getAnswerResponseTextAndImage['answerResponseImage']) > 0) {
                                            $response['questionAnswerImage'] = Storage::url($this->answerResponseImageOriginal . $getAnswerResponseTextAndImage['answerResponseImage']);
                                        } else {
                                            $response['questionAnswerImage'] = '';
                                        }
                                    } else {
                                        $response['questionAnswerImage'] = '';
                                    }
                                }
                            }
                        }
                        
                        $yourResult = $this->level4ActivitiesRepository->checkIntermediateQuestionRightOrWrong($body['questionID'], implode(',', $body['answer']));
                        if ($yourResult == 1) {
                            $response['answerRightWrongMsg'] = $getAllQuestionRelatedDataFromQuestionId->l4ia_question_right_message;
                            $response['systemCorrectAnswer'] = 1;
                            $earnedPoints = $getAllQuestionRelatedDataFromQuestionId->l4ia_question_point;
                        } else {
                            $response['answerRightWrongMsg'] = $getAllQuestionRelatedDataFromQuestionId->l4ia_question_wrong_message;
                            $response['systemCorrectAnswer'] = 0;
                            $earnedPoints = 0;
                        }

                        $makeAnswerArray = [];
                        if (isset($getAllQuestionRelatedDataFromQuestionId->options_id) && !empty($optionsIdArray) && !empty($correctOptionsArray)) {
                            foreach ($optionsIdArray as $keyId => $dataId) {
                                $makeAnswerArray[$dataId] = $correctOptionsArray[$keyId];
                            }
                        }
                        
                        $response['systemCorrectAnswer2'] = $makeAnswerArray;
                        
                        $data = [];
                        $data['l4iaua_teenager'] = $userId;
                        $data['l4iaua_activity_id'] = $body['questionID'];
                        $data['l4iaua_profession_id'] = $professionId;
                        $data['l4iaua_template_id'] = $getAllQuestionRelatedDataFromQuestionId->l4ia_question_template;
                        $body['answer'] = (isset($body['timer']) && $body['timer'] != 0) ? $body['answer'] : $body['answer'];
                        $data['l4iaua_order'] = 0;
                        $data['l4iaua_earned_point'] = $earnedPoints;
                        $data['l4iaua_time'] = $body['timer'];
                        
                        $templateId = $getAllQuestionRelatedDataFromQuestionId->l4ia_question_template;
                        $objProfessionLearningStyle = new ProfessionLearningStyle();
                        $learningId = $objProfessionLearningStyle->getIdByProfessionId($professionId,$templateId);
                        if ($learningId != '') {
                            $objUserLearningStyle = new UserLearningStyle();
                            $learningData = $objUserLearningStyle->getUserLearningStyle($learningId);
                            if (!empty($learningData)) {
                                $earnedPoints += $learningData->uls_earned_points;
                            }
                            $userData = [];
                            $userData['uls_learning_style_id'] = $learningId;
                            $userData['uls_profession_id'] = $professionId;
                            $userData['uls_teenager_id'] = $userId;
                            $userData['uls_earned_points'] = $earnedPoints;
                            $result = $objUserLearningStyle->saveUserLearningStyle($userData);
                        }

                        $saveLevel4IntemediateActivityAnswer = $this->level4ActivitiesRepository->saveTeenagerIntermediateActivityFillInBlanksAnswer($userId, $data, $body['answer']);
                        $response['message'] = trans('appmessages.default_success_msg');
                        $response['status'] = 1;
                    } else {
                        $response['status'] = 0;
                        $response['message'] = "Invalid Answer Type";
                        $response['reload'] = 1;
                    }
                    $response['templateId'] = $getAllQuestionRelatedDataFromQuestionId->l4ia_question_template;
                    $response['answerType'] = $getAllQuestionRelatedDataFromQuestionId->gt_temlpate_answer_type;
                    $response['reload'] = 1;
                    $total = $this->teenagersRepository->getTeenagerTotalBoosterPoints($userId);
                    /*if (!empty($getTeenagerBoosterPoints)) {
                        $return = Helpers::sendMilestoneNotification($userid,$getTeenagerBoosterPoints['total']);
                    }*/
                    //$level4Booster = Helpers::level4Booster($professionId, $userid);
                    $level4Booster['total'] = $total;
                    $response['level4Booster'] = $level4Booster;
                    $response['boosterPoints'] = '';
                    $response['boosterScale'] = 50;
                } else {
                    $response['status'] = 0;
                    $response['message'] = "Invalid Question Id";
                    $response['reload'] = 1;
                }
                
            } else {
                $response['status'] = 0;
                $response['reload'] = 1;
                $response['message'] = "Your option choice is not belong from this question's option";
                $response['redirect'] = '/teenager/level4Inclination/';
            }
        } else {
            $response['status'] = 0;
            $response['message'] = "Invalid User";
            $response['reload'] = 1;
            $response['redirect'] = '/teenager';
        }

        return response()->json($response, 200);
        exit;
    }

    public function getPromisePlusData() 
    {
        $professionId = Input::get('professionId');
        $userId = Auth::guard('teenager')->user()->id;
        if($userId > 0 && $professionId != '') {
            $level4Booster = Helpers::level4Booster($professionId, $userId);
            
            $getTeenagerAllTypeBadges = $this->teenagersRepository->getTeenagerAllTypeBadges($userId, $professionId);

            $totalPoints = 0;
            if (!empty($getTeenagerAllTypeBadges)) {
                if ($getTeenagerAllTypeBadges['level4Basic']['noOfAttemptedQuestion'] != 0) {
                    $totalPoints += $getTeenagerAllTypeBadges['level4Basic']['basicAttemptedTotalPoints'];
                }
                if ($getTeenagerAllTypeBadges['level4Intermediate']['noOfAttemptedQuestion'] != 0) {
                    foreach ($getTeenagerAllTypeBadges['level4Intermediate']['templateWiseEarnedPoint'] AS $k => $val) {
                       // if ($getTeenagerAllTypeBadges['level4Intermediate']['templateWiseEarnedPoint'][$k] != 0) {
                            $totalPoints += $getTeenagerAllTypeBadges['level4Intermediate']['templateWiseTotalAttemptedPoint'][$k];
                      //  }
                    }
                }
                if ($getTeenagerAllTypeBadges['level4Advance']['earnedPoints'] != 0) {
                    $totalPoints += $getTeenagerAllTypeBadges['level4Advance']['advanceTotalPoints'];
                }
            }

            $level2Data = '';
            $level4PromisePlus = '';
            $flag = false;
            if ($totalPoints != 0) {
                $level4PromisePlus = Helpers::calculateLevel4PromisePlus($level4Booster['yourScore'], $totalPoints);
                $flag = true;
            }

            $PromisePlus = 0;
            if ($flag) {
                if ($level4PromisePlus >= Config::get('constant.NOMATCH_MIN_RANGE') && $level4PromisePlus <= Config::get('constant.NOMATCH_MAX_RANGE') ) {
                    $PromisePlus = "nomatch";
                } else if ($level4PromisePlus >= Config::get('constant.MODERATE_MIN_RANGE') && $level4PromisePlus <= Config::get('constant.MODERATE_MAX_RANGE') ) {
                $PromisePlus = "moderate";
                } else if ($level4PromisePlus >= Config::get('constant.MATCH_MIN_RANGE') && $level4PromisePlus <= Config::get('constant.MATCH_MAX_RANGE') ) {
                $PromisePlus = "match";
                } else {
                    $PromisePlus = "";
                }
            } else {
                 $PromisePlus = "";
            }

            //get L2 HML 
            $level2Promise = '';
            $getTeenagerHML = Helpers::getTeenagerMatchScale($userId);
            $level2Promise = isset($getTeenagerHML[$professionId])?$getTeenagerHML[$professionId]:'nomatch';

            if ($level2Promise == 'nomatch') {
                $level2Data = 'TOUGH & CHALLENGING';
            } else if ($level2Promise == 'moderate') {
                $level2Data = 'MODERATELY SUITED';
            } else if ($level2Promise == 'match') {
                $level2Data = 'LIKELY FIT FOR YOU';
            } else {
                $level2Promise = "";
                $level2Data = '';
            }

            $promisePlusData = $this->objPromisePlus->getAllPromisePlus();

            $L4promisePlus = [];
            $colorCode = '';
            $L4PP = '';
            $professionFeedback = '';
            if ($level2Promise == 'nomatch' && $PromisePlus == 'nomatch' ) {
                $professionFeedback = 0;
                $colorCode = 1;
                $L4PP = 1;
            } else if ($level2Promise == 'nomatch' && $PromisePlus == 'moderate' ) {
                $professionFeedback = 3;
            } else if ($level2Promise == 'nomatch' && $PromisePlus == 'match' ) {
                $professionFeedback = 6;
            } else if ($level2Promise == 'moderate' && $PromisePlus == 'nomatch' ) {
                $professionFeedback = 1;
            } else if ($level2Promise == 'moderate' && $PromisePlus == 'moderate' ) {
                $professionFeedback = 4;
                $colorCode = 2;
                $L4PP = 1;
            } else if ($level2Promise == 'moderate' && $PromisePlus == 'match' ) {
                $professionFeedback = 7;
            } else if ($level2Promise == 'match' && $PromisePlus == 'nomatch' ) {
                $professionFeedback = 2;
            } else if ($level2Promise == 'match' && $PromisePlus == 'moderate' ) {
                $professionFeedback = 5;
            } else if ($level2Promise == 'match' && $PromisePlus == 'match' ) {
                $professionFeedback = 8;
                $colorCode = 3;
                $L4PP = 1;
            }
            if (!empty($promisePlusData)) {
                if ($PromisePlus != '') {
                    $L4promisePlus[] = $promisePlusData[$professionFeedback];
                }
            } else {
                $L4promisePlus[] = '';
            }

            $finalPromisePlusData = [];
            $finalPromisePlusData['level2Promise'] = $level2Promise;
            $finalPromisePlusData['promisePlus'] = $PromisePlus;
            $finalPromisePlusData['colorCode'] = $colorCode;
            $finalPromisePlusData['L4FeedbackCode'] = $L4PP;
            $finalPromisePlusData['level2Data'] = $level2Data;
            $finalPromisePlusData['level4Data'] = $L4promisePlus;
            $finalPromisePlusData['message'] = trans('appmessages.default_success_msg');
            $finalPromisePlusData['status'] = 1;
           
            return view('teenager.basic.getPromisePlus', compact('finalPromisePlusData'));
        }
        $response['status'] = 0;
        $response['message'] = "Something went wrong!";

        return response()->json($response, 200);
        exit;
    }
    
    public function getProfessionCompetitor() {
        $professionId = Input::get('professionId');
        $userId = Auth::guard('teenager')->user()->id;
        
        if($userId > 0 && $professionId != '') {
            $level4Booster = Helpers::level4Booster($professionId, $userId);             
            return view('teenager.basic.careerBoosterScaleSection', compact('level4Booster'));
        }
        $response['status'] = 0;
        $response['message'] = "Something went wrong!";

        return response()->json($response, 200);
        exit;
    }
    
    /**
     * Returns learning Guidance page.
     *
     * @return \Illuminate\Http\Response
     */
    public function learningGuidance()
    {
        //$learningGuidance = Helpers::learningGuidance();
        $userId = Auth::guard('teenager')->user()->id;
        //Insert all user learning style data
        $professionArray = $this->objLevel4ProfessionProgress->getTeenAttemptProfessionWithTotal($userId);
        
        $objLevel4Answers = new Level4Answers();
        $objProfessionLearningStyle = new ProfessionLearningStyle();
        $objUserLearningStyle = new UserLearningStyle();
        if (isset($professionArray) && !empty($professionArray)) {
            foreach ($professionArray as $key => $proValue) {
                $professionId = $proValue->id;
                $level4BasicData = $objLevel4Answers->getLevel4BasicDetailById($userId,$professionId);
                if (isset($level4BasicData) && !empty($level4BasicData)) {
                    $templateId = "L4B";
                    $learningId = $objProfessionLearningStyle->getIdByProfessionIdForAdvance($professionId,$templateId);
                    if ($learningId != '') {
                        $userData = [];
                        $userData['uls_learning_style_id'] = $learningId;
                        $userData['uls_profession_id'] = $professionId;
                        $userData['uls_teenager_id'] = $userId;
                        $userData['uls_earned_points'] = $level4BasicData[0]->earned_points;
                        $result = $objUserLearningStyle->saveUserLearningStyle($userData);
                    }
                }
                $media = array(1,2,3);
                for ($i = 0; $i < count($media); $i++) {
                    $level4AdvanceData = $this->level4ActivitiesRepository->getLevel4AdvanceDetailById($userId,$professionId,$media[$i]);
                    $templateId = '';
                    if ($media[$i] == 3) {
                        $templateId = "L4AP";
                    } else if ($media[$i] == 2) {
                        $templateId = "L4AD";
                    } else if ($media[$i] == 1) {
                        $templateId = "L4AV";
                    }
                    $learningId = $objProfessionLearningStyle->getIdByProfessionIdForAdvance($professionId,$templateId);
                    if (isset($level4AdvanceData) && !empty($level4AdvanceData)) {
                        if ($learningId != '') {
                            $userData = [];
                            $userData['uls_learning_style_id'] = $learningId;
                            $userData['uls_profession_id'] = $professionId;
                            $userData['uls_teenager_id'] = $userId;
                            $userData['uls_earned_points'] = $level4AdvanceData[0]->earned_points;
                            $result = $objUserLearningStyle->saveUserLearningStyle($userData);
                        }
                    }
                }
                $level4IntermediateData = $this->level4ActivitiesRepository->getLevel4IntermediateDetailById($userId,$professionId);
                if (isset($level4IntermediateData) && !empty($level4IntermediateData)) {
                    $dataArr = [];
                    $uniqueArr =[];
                    foreach ($level4IntermediateData AS $key => $value) {
                        if(!in_array($value->l4iaua_template_id, $uniqueArr))
                        {
                            $uniqueArr[] = $value->l4iaua_template_id;
                            $data = [];
                            $data['l4iaua_template_id'] = $value->l4iaua_template_id;
                            $data['l4iaua_earned_point'] = 0;
                            $dataArr[] = $data;
                        }
                    }
                    foreach ($level4IntermediateData AS $key => $value) {
                        foreach ($dataArr As $k => $val) {
                            if ($value->l4iaua_template_id == $val['l4iaua_template_id']){
                                $dataArr[$k]['l4iaua_earned_point'] += $value->l4iaua_earned_point;
                            }
                        }
                    }
                    for ($j = 0; $j < count($dataArr); $j++) {
                        $templateId = $dataArr[$j]['l4iaua_template_id'];
                        $learningId = $objProfessionLearningStyle->getIdByProfessionId($professionId,$templateId);
                        if ($learningId != '') {
                            $userData = [];
                            $userData['uls_learning_style_id'] = $learningId;
                            $userData['uls_profession_id'] = $professionId;
                            $userData['uls_teenager_id'] = $userId;
                            $userData['uls_earned_points'] = $dataArr[$j]['l4iaua_earned_point'];
                            $result = $objUserLearningStyle->saveUserLearningStyle($userData);
                        }
                    }
                }
            }
        }

        $finalProfessionArray = [];
        $objLearningStyle = new LearningStyle();

        $userLearningData = $objLearningStyle->getLearningStyleDetails();
        $objProfession =  new Professions();
        $AllProData = $objProfession->getActiveProfessions();

        $TotalAttemptedP = 0;
        $allp = count($AllProData);
        $attemptedp = count($professionArray);
        $TotalAttemptedP = ($attemptedp * 100) / $allp;
        if (!empty($userLearningData)) {
            foreach ($userLearningData as $k => $value ) {
                $userLearningData[$k]->earned_points = 0;
                $userLearningData[$k]->total_points = 0;
                $userLearningData[$k]->percentage = '';
                $userLearningData[$k]->interpretationrange = '';
                $userLearningData[$k]->totalAttemptedP = round($TotalAttemptedP);
                $photo = $value->ls_image;
                if ($photo != '' && file_exists($this->learningStyleThumbImageUploadPath . $photo)) {
                    $value->ls_image = asset($this->learningStyleThumbImageUploadPath . $photo);
                } else {
                    $value->ls_image = asset("/frontend/images/proteen-logo.png");
                }
            }

            if (isset($professionArray) && !empty($professionArray)) {
                foreach ($professionArray as $key => $val) {
                    $professionId = $val->id;
                    $getTeenagerAllTypeBadges = $this->teenagersRepository->getTeenagerAllTypeBadges($userId, $professionId);
                    $level4Booster = Helpers::level4Booster($professionId, $userId);
                    $l4BTotal = (isset($getTeenagerAllTypeBadges['level4Basic']) && !empty($getTeenagerAllTypeBadges['level4Basic'])) ? $getTeenagerAllTypeBadges['level4Basic']['basicAttemptedTotalPoints'] : '';
                    $l4ATotal = (isset($getTeenagerAllTypeBadges['level4Advance']) && !empty($getTeenagerAllTypeBadges['level4Advance'])) ? $getTeenagerAllTypeBadges['level4Advance']['earnedPoints'] : '';
                    $UserLerningStyle = [];
                    
                    foreach ($userLearningData as $k => $value ) {
                        $userLData = $objLearningStyle->getLearningStyleDetailsByProfessionId($professionId,$value->parameterId,$userId);
                        if (!isset($userLData) && count($userLData) > 0) {
                            $points = '';
                            $LAPoints = '';
                            $points = $userLData[0]->uls_earned_points;
                            $userLearningData[$k]->earned_points += $userLData[0]->uls_earned_points;
                            $activityName = $userLData[0]->activity_name;
                            if (strpos($activityName, ',') !== false) {
                                $Activities = explode(",",$activityName);
                                foreach ($Activities As $Akey => $acty) {
                                    if ($acty == 'L4B') {
                                            $userLearningData[$k]->total_points += $l4BTotal;
                                    } else if ($acty == 'L4AV') {
                                        if ($l4ATotal != 0) {
                                            $userLearningData[$k]->total_points += Config::get('constant.USER_L4_VIDEO_POINTS');
                                        }
                                    }else if ($acty == 'L4AP') {
                                        if ($l4ATotal != 0) {
                                            $userLearningData[$k]->total_points += Config::get('constant.USER_L4_PHOTO_POINTS');
                                        }
                                    }else if ($acty == 'L4AD') {
                                        if ($l4ATotal != 0) {
                                            $userLearningData[$k]->total_points += Config::get('constant.USER_L4_DOCUMENT_POINTS');
                                        }
                                    } else if ($acty == 'N/A') {
                                        if ($points != 0) {
                                            $userLearningData[$k]->total_points += '';
                                        }
                                    } else {
                                        if ($acty != '' && intval($acty) > 0) {
                                            $TotalPoints = $getTeenagerAllTypeBadges['level4Intermediate']['templateWiseTotalAttemptedPoint'][$acty];
                                            $userLearningData[$k]->total_points += $TotalPoints;
                                        }

                                    }
                                }
                          } else {
                              if ($activityName == 'L4B') {
                                    $userLearningData[$k]->total_points += $l4BTotal;
                              } else if ($activityName == 'L4AV') {
                                  if ($l4ATotal != 0) {
                                      $userLearningData[$k]->total_points += Config::get('constant.USER_L4_VIDEO_POINTS');
                                  }
                              }else if ($activityName == 'L4AP') {
                                  if ($l4ATotal != 0) {
                                      $userLearningData[$k]->total_points += Config::get('constant.USER_L4_PHOTO_POINTS');
                                  }
                              }else if ($activityName == 'L4AD') {
                                  if ($l4ATotal != 0) {
                                      $userLearningData[$k]->total_points += Config::get('constant.USER_L4_DOCUMENT_POINTS');
                                  }
                              } else if ($activityName == 'N/A') {
                                  if ($points != 0) {
                                      $userLearningData[$k]->total_points += '';
                                  }
                              } else {
                                  if (intval($activityName) > 0) {
                                      $TotalPoints = $getTeenagerAllTypeBadges['level4Intermediate']['templateWiseTotalAttemptedPoint'][$activityName];
                                      $userLearningData[$k]->total_points += $TotalPoints;
                                  }
                              }
                        }
                        if ($userLearningData[$k]->total_points != 0) {
                            $LAPoints = ($value->earned_points * 100) / $userLearningData[$k]->total_points;
                        }
                        $range = '';
                        $LAPoints = round($LAPoints);
                        if ($LAPoints >= Config::get('constant.LS_LOW_MIN_RANGE') && $LAPoints <= Config::get('constant.LS_LOW_MAX_RANGE') ) {
                            $range = "Low";
                        } else if ($LAPoints >= Config::get('constant.LS_MEDIUM_MIN_RANGE') && $LAPoints <= Config::get('constant.LS_MEDIUM_MAX_RANGE') ) {
                            $range = "Medium";
                        } else if ($LAPoints >= Config::get('constant.LS_HIGH_MIN_RANGE') && $LAPoints <= Config::get('constant.LS_HIGH_MAX_RANGE') ) {
                            $range = "High";
                        }
                        $userLearningData[$k]->interpretationrange = $range;
                        $userLearningData[$k]->percentage = $LAPoints;
                        }
                    }
                }
            }
        }
       
        if(isset($userLearningData) && !empty($userLearningData))
        {            
            foreach($userLearningData as $key=>$lg){                  
                if (strpos($lg->ls_name, 'factual_') !== false)
                {
                    $subPanelDataFactual[] = array('id'=>$lg->parameterId,'title'=>$lg->ls_name,'titleColor'=>'#f1c246','titleType'=>$lg->interpretationrange,'subPanelDescription'=>$lg->ls_description); 
                    $learningGuidance[0] = array('id'=>$lg->parameterId,'name'=>'Factual','slug'=>$lg->ls_name,'panelColor'=>'#ff5f44','image'=>'https://s3proteen.s3.ap-south-1.amazonaws.com/img/brain-img.png','subPanelData'=>$subPanelDataFactual);                                     
                }
                elseif (strpos($lg->ls_name, 'conceptual_') !== false)
                {
                    $subPanelDataConcept[] = array('id'=>$lg->parameterId,'title'=>$lg->ls_name,'titleColor'=>'#f1c246','titleType'=>$lg->interpretationrange,'subPanelDescription'=>$lg->ls_description); 
                    $learningGuidance[1] = array('id'=>$lg->parameterId,'name'=>'Conceptual','slug'=>$lg->ls_name,'panelColor'=>'#27a6b5','image'=>'https://s3proteen.s3.ap-south-1.amazonaws.com/img/bulb-img.png','subPanelData'=>$subPanelDataConcept);                                       
                }
                elseif (strpos($lg->ls_name, 'procedural_') !== false)
                {
                    $subPanelDataProcedural[] = array('id'=>$lg->parameterId,'title'=>$lg->ls_name,'titleColor'=>'#f1c246','titleType'=>$lg->interpretationrange,'subPanelDescription'=>$lg->ls_description); 
                    $learningGuidance[2] = array('id'=>$lg->parameterId,'name'=>'Procedural','slug'=>$lg->ls_name,'panelColor'=>'#65c6e6','image'=>'https://s3proteen.s3.ap-south-1.amazonaws.com/img/puzzle-img.png','subPanelData'=>$subPanelDataProcedural);                                       
                }
                elseif (strpos($lg->ls_name, 'meta_cognitive_') !== false)
                {
                    $subPanelDataMeta[] = array('id'=>$lg->parameterId,'title'=>$lg->ls_name,'titleColor'=>'#f1c246','titleType'=>$lg->interpretationrange,'subPanelDescription'=>$lg->ls_description); 
                    $learningGuidance[3] = array('id'=>$lg->parameterId,'name'=>'Meta-Cognitive','slug'=>$lg->ls_name,'panelColor'=>'#73376d','image'=>'https://s3proteen.s3.ap-south-1.amazonaws.com/img/star-img.png','subPanelData'=>$subPanelDataMeta);                                       
                }                   
            }
        }          
        
        return view('teenager.learningGuidance', compact('learningGuidance'));
    }
}