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
                    $answerID == 0;
                    $timer == 0;
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
            }
            
            //$activities = $this->level4ActivitiesRepository->getNotAttemptedActivities($userId, $professionId);
            if (isset($intermediateActivities[0]) && !empty($intermediateActivities[0])) {
                $intermediateActivitiesData = $intermediateActivities[0];
                
                $intermediateActivitiesData->gt_temlpate_answer_type = Helpers::getAnsTypeFromGamificationTemplateId($intermediateActivitiesData->l4ia_question_template);
                $intermediateActivitiesData->l4ia_extra_question_time = 120;
                $timer = $intermediateActivitiesData->l4ia_question_time;
                $response['timer'] = $intermediateActivitiesData->l4ia_question_time;
                $intermediateActivitiesData->l4ia_question_popup_image = ($intermediateActivitiesData->l4ia_question_popup_image != "" && Storage::size($this->questionDescriptionORIGINALImage . $intermediateActivitiesData->l4ia_question_popup_image) > 0) ? Storage::url($this->questionDescriptionORIGINALImage . $intermediateActivitiesData->l4ia_question_popup_image) : '';
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
                        $intermediateActivitiesData->question_images[$key]['l4ia_question_image'] = ( $image['image'] != "" && Storage::size($this->questionDescriptionORIGINALImage . $image['image']) > 0 ) ? Storage::url($this->questionDescriptionORIGINALImage . $image['image']) : Storage::url($this->questionDescriptionORIGINALImage . 'proteen-logo.png');
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
            if(isset($intermediateActivitiesData->gt_temlpate_answer_type) && in_array($intermediateActivitiesData->gt_temlpate_answer_type, ["option_choice", "option_choice_with_response", "true_false"]) ) {
                return view('teenager.basic.careerIntermediateOptionChoiceQuestion', compact('response'));
            } else if(isset($intermediateActivitiesData->gt_temlpate_answer_type) && $intermediateActivitiesData->gt_temlpate_answer_type == "single_line_answer") {
                return view('teenager.basic.careerIntermediateSingleLineQuestion', compact('response'));
            } else if(isset($intermediateActivitiesData->gt_temlpate_answer_type) && $intermediateActivitiesData->gt_temlpate_answer_type == "select_from_dropdown_option") {
                return view('teenager.basic.careerIntermediateDropDownSelectQuestion', compact('response'));
            } else if(isset($intermediateActivitiesData->gt_temlpate_answer_type) && $intermediateActivitiesData->gt_temlpate_answer_type == "option_reorder") {
                return view('teenager.basic.careerIntermediateOptionReorderQuestion', compact('response'));
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

    public function getQuestionDataAdvanceLevel()
    {
        $type = Input::get('activityType');
        $professionId = Input::get('professionId');
        $professionDetail = $this->professionsRepository->getProfessionsDataFromId($professionId);
        $activityData = $this->level4ActivitiesRepository->getLevel4AdvanceActivityByType($type);
        return view('teenager.basic.careerAdvanceQuizData', compact('activityData', 'professionDetail', 'type'));
    }

    public function getMediaUploadSection()
    {
        return view('teenager.basic.careerAdvanceQuizSection');
    }
}