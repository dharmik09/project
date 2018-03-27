<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use Auth;
use Input;
use Redirect;
use App\Services\Parents\Contracts\ParentsRepository;
use App\Services\Teenagers\Contracts\TeenagersRepository;
use App\Services\Level1Activity\Contracts\Level1ActivitiesRepository;
use App\Services\Level4Activity\Contracts\Level4ActivitiesRepository;
use App\Services\Template\Contracts\TemplatesRepository;
use Helpers;
use Config;
use App\Services\Professions\Contracts\ProfessionsRepository;
use Mail;
use App;
use PDF;
use App\PromisePlus;
use App\ProfessionLearningStyle;
use App\UserLearningStyle;
use App\LearningStyle;
use App\Professions;
use App\Level4Answers;
use App\PaidComponent;
use App\DeductedCoins;
use App\MultipleIntelligent;
use App\Apptitude;
use App\Personality;
use App\Level2ParentsActivity;
use App\TeenParentChallenge;
use App\TemplateDeductedCoins;
use Storage;
use App\Level4ProfessionProgress;

class Level4ActivityManagementController extends Controller {

    public function __construct(ParentsRepository $parentsRepository, TeenagersRepository $teenagersRepository, Level1ActivitiesRepository $level1ActivitiesRepository, ProfessionsRepository $professionsRepository, TemplatesRepository $templatesRepository, Level4ActivitiesRepository $level4ActivitiesRepository) {
        $this->parentsRepository = $parentsRepository;
        $this->teenagersRepository = $teenagersRepository;
        $this->level1ActivitiesRepository = $level1ActivitiesRepository;
        $this->professionsRepository = $professionsRepository;
        $this->templateRepository = $templatesRepository;
        $this->level4ActivitiesRepository = $level4ActivitiesRepository;
        $this->interestOriginalImageUploadPath = Config::get('constant.INTEREST_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->apptitudeOriginalImageUploadPath = Config::get('constant.APPTITUDE_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->miOriginalImageUploadPath = Config::get('constant.MI_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->personalityOriginalImageUploadPath = Config::get('constant.PERSONALITY_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->professionThumbImageUploadPath = Config::get('constant.PROFESSION_THUMB_IMAGE_UPLOAD_PATH');
        $this->professionOriginalImageUploadPath = Config::get('constant.PROFESSION_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->cartoonThumbImageUploadPath = Config::get('constant.CARTOON_THUMB_IMAGE_UPLOAD_PATH');
        $this->cartoonOriginalImageUploadPath = Config::get('constant.CARTOON_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->humanThumbImageUploadPath = Config::get('constant.HUMAN_THUMB_IMAGE_UPLOAD_PATH');
        $this->humanOriginalImageUploadPath = Config::get('constant.HUMAN_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->relationIconOriginalImageUploadPath = Config::get('constant.RELATION_ICON_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->relationIconThumbImageUploadPath = Config::get('constant.RELATION_ICON_THUMB_IMAGE_UPLOAD_PATH');
        $this->teenThumbImageUploadPath = Config::get('constant.TEEN_THUMB_IMAGE_UPLOAD_PATH');
        $this->learningStyleThumbImageUploadPath = Config::get('constant.LEARNING_STYLE_THUMB_IMAGE_UPLOAD_PATH');
        $this->extraQuestionDescriptionTime = Config::get('constant.EXTRA_QUESTION_DESCRIPTION');
        $this->questionDescriptionORIGINALImage = Config::get('constant.LEVEL4_INTERMEDIATE_QUESTION_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->questionDescriptionTHUMBImage = Config::get('constant.LEVEL4_INTERMEDIATE_QUESTION_THUMB_IMAGE_UPLOAD_PATH');
        $this->optionORIGINALImage = Config::get('constant.LEVEL4_INTERMEDIATE_ANSWER_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->optionTHUMBImage = Config::get('constant.LEVEL4_INTERMEDIATE_ANSWER_THUMB_IMAGE_UPLOAD_PATH');
        $this->answerResponseImageOriginal = Config::get('constant.LEVEL4_INTERMEDIATE_RESPONSE_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->objLevel4ProfessionProgress = new Level4ProfessionProgress;
        $this->loggedInUser = Auth::guard('parent');
    }

    public function myChallengers() {
        if (Auth::guard('parent')->check()) {
            $parentId = $this->loggedInUser->user()->id;
            $objTeenParentChallenge = new TeenParentChallenge();
            $parentChallengeData = $objTeenParentChallenge->getTeenParentChallengeData($parentId);
            $professionOriginalImageUploadPath = $this->professionOriginalImageUploadPath;
            foreach ($parentChallengeData AS $key => $value) {
                $getParentAllTypeBadges = $this->parentsRepository->getParentAllTypeBadges($parentId, $value->tpc_profession_id);
                $parentChallengeData[$key]->L4Attempted = 0;
                if ($getParentAllTypeBadges['level4Basic']['noOfAttemptedQuestion'] != 0) {
                    $parentChallengeData[$key]->L4Attempted = 1;
                }
                //echo "<pre>"; print_r($parentChallengeData); exit;
                $parentChallengeData[$key]->L4BasicPoint = $getParentAllTypeBadges['level4Basic']['earnedPoints'];
                $parentChallengeData[$key]->L4IntermediatePoint = $getParentAllTypeBadges['level4Intermediate']['earnedPoints'];
                $parentChallengeData[$key]->L4BAdvancePoint = $getParentAllTypeBadges['level4Advance']['earnedPoints'];
            }
            return view('parent.parentChallengers',compact('parentChallengeData','professionOriginalImageUploadPath'));
        } else {
            return view('parent.dashboard');
        }
    }

    public function myChallengersResearch($professionId,$teenId) {
        $professionId = intval($professionId);
        $response = $professionHeaderList = [];
        $response['status'] = 0;
        if (Auth::guard('parent')->check()) {
            $parentid = $this->loggedInUser->user()->id;
            if (isset($parentid) && $parentid > 0) {
                $professionHeaderDetail = $this->professionsRepository->getProfessionsHeaderByProfessionId($professionId);
                $professionOtherDetail = $this->professionsRepository->getProfessionsDataFromId($professionId);

                if (isset($professionHeaderDetail) && !empty($professionHeaderDetail)) {
                    foreach ($professionHeaderDetail as $key => $val) {
                        $professionHeaderList['professionID'] = $val->professionid;
                        $professionHeaderList['profession_name'] = $val->pf_name;
                        $professionHeaderList['video_url'] = $val->pf_video;
                        $professionHeaderList[$val->pfic_title] = $val->pfic_content;
                    }
                    $response['message'] = trans('appmessages.default_success_msg');
                } else {
                    $professionHeaderList['profession_name'] = @$professionOtherDetail[0]->pf_name;
                    $professionHeaderList['video_url'] = @$professionOtherDetail[0]->pf_video;
                    $response['message'] = trans('appmessages.data_empty_msg');
                }
                //Get booster points
                $getTeenagerBoosterPoints = $this->teenagersRepository->getTeenagerBoosterPoints($teenId);
                $level4Booster = Helpers::level4Booster($professionId, $teenId);

                $level4ParentBooster = Helpers::level4ParentBooster($professionId, $parentid);
                $response['profession_id'] = $professionId;
                $response['teen_id'] = $teenId;
                $teenDetail = $this->teenagersRepository->getTeenagerByTeenagerId($teenId);
                $response['teenDetail'] = $teenDetail;
                $level4Booster['total'] = $getTeenagerBoosterPoints['total'];
                $response['level4Booster'] = $level4Booster;
                $response['level4ParentBooster'] = $level4ParentBooster;
                $response['data'] = $professionHeaderList;

                return view('parent.level4ActivityProfessionDetail', compact('response', 'professionId'));
            } else {
                Auth::guard('parent')->logout();
                return Redirect::to('/parent')->with('error', trans('appmessages.invalid_userid_msg'));
                exit;
            }
        } else {
            Auth::guard('parent')->logout();
            return Redirect::to('/parent')->with('error', trans('appmessages.invalid_userid_msg'));
            exit;
        }
    }

     public function myChallengersAccept($professionId,$teenId) {
        $professionId = intval($professionId);
        $response = $professionHeaderList = [];
        $response['status'] = 0;
        if (Auth::guard('parent')->check()) {
            $parentid = $this->loggedInUser->user()->id;
            $response['profession_id'] = $professionId;
            $response['teen_id'] = $teenId;
            $teenDetail = $this->teenagersRepository->getTeenagerByTeenagerId($teenId);
            $response['teenDetail'] = $teenDetail;
            if (isset($parentid) && $parentid > 0) {
                $getProfessionNameFromProfessionId = $this->professionsRepository->getProfessionsByProfessionId($professionId);
                if (isset($getProfessionNameFromProfessionId[0]) && !empty($getProfessionNameFromProfessionId[0])) {
                    $response['profession_name'] = $getProfessionNameFromProfessionId[0]->pf_name;
                    $response['basket_id'] = $getProfessionNameFromProfessionId[0]->pf_basket;
                } else {
                    $response['profession_name'] = "Choose Your Level";
                    $response['basket_id'] = 0;
                }
                $getTeenagerBoosterPoints = $this->teenagersRepository->getTeenagerBoosterPoints($teenId);
                $getParentAllTypeBadges = $this->parentsRepository->getParentAllTypeBadges($parentid, $professionId);
                $response['level4Basic'] = (isset($getParentAllTypeBadges['level4Basic']) && !empty($getParentAllTypeBadges['level4Basic'])) ? $getParentAllTypeBadges['level4Basic'] : '';
                if (isset($getParentAllTypeBadges['level4Basic']['noOfTotalQuestion']) && isset($getParentAllTypeBadges['level4Basic']['noOfAttemptedQuestion']) && $getParentAllTypeBadges['level4Basic']['noOfTotalQuestion'] > 0 && ($getParentAllTypeBadges['level4Basic']['noOfTotalQuestion'] == $getParentAllTypeBadges['level4Basic']['noOfAttemptedQuestion'])) {
                    $response['level4Basic']['basicButton'] = "Play over";
                } else {
                    $response['level4Basic']['basicButton'] = "Play now";
                }
                $response['level4Intermediate'] = (isset($getParentAllTypeBadges['level4Intermediate']) && !empty($getParentAllTypeBadges['level4Intermediate'])) ? $getParentAllTypeBadges['level4Intermediate'] : '';
                if (isset($getParentAllTypeBadges['level4Intermediate']['noOfAttemptedQuestion']) && isset($getParentAllTypeBadges['level4Intermediate']['noOfTotalQuestion']) && $getParentAllTypeBadges['level4Intermediate']['noOfTotalQuestion'] > 0 && ($getParentAllTypeBadges['level4Intermediate']['noOfTotalQuestion'] == $getParentAllTypeBadges['level4Intermediate']['noOfAttemptedQuestion'])) {
                    $response['level4Intermediate']['intermediateButton'] = "Play over";
                } else {
                    $response['level4Intermediate']['intermediateButton'] = "Play now";
                }

                $response['level4Advance'] = (isset($getParentAllTypeBadges['level4Advance']) && !empty($getParentAllTypeBadges['level4Advance'])) ? $getParentAllTypeBadges['level4Advance'] : '';

                $level4Booster = Helpers::level4Booster($professionId, $teenId);

                $level4ParentBooster = Helpers::level4ParentBooster($professionId, $parentid);

                $level4Booster['total'] = $getTeenagerBoosterPoints['total'];
                $response['level4Booster'] = $level4Booster;
                $response['teen_id'] = $teenId;
                $teenDetail = $this->teenagersRepository->getTeenagerByTeenagerId($teenId);
                $response['teenDetail'] = $teenDetail;
                $response['level4ParentBooster'] = $level4ParentBooster;
                $response['boosterPoints'] = '';
                $response['boosterScale'] = 50;
                return view('parent.exploreProfessions', compact('response'));
            } else {
                Auth::guard('parent')->logout();
                return Redirect::to('/parent')->with('error', trans('appmessages.invalid_userid_msg'));
                exit;
            }
        } else {
            Auth::guard('parent')->logout();
            return Redirect::to('/parent')->with('error', trans('appmessages.invalid_userid_msg'));
            exit;
        }
    }

    public function professionQuestion($professionId = null,$userid) {
        $response = [];
        $response['status'] = 0;
        if ($professionId != '' && $professionId != 0) {
            if (Auth::guard('parent')->check()) {
                $parentid = $this->loggedInUser->user()->id;
                if (isset($userid) && $userid > 0) {
                    $response['profession_id'] = $professionId;
                    $response['teen_id'] = $userid;
                    $teenDetail = $this->teenagersRepository->getTeenagerByTeenagerId($userid);
                    $response['teenDetail'] = $teenDetail;
                    $activities = [];
                    $activities = $this->level4ActivitiesRepository->getNotAttemptedActivitiesForParent($parentid, $professionId);
                    $totalQuestion = $this->level4ActivitiesRepository->getNoOfTotalQuestionsAttemptedQuestionForParent($parentid, $professionId);

                    //If total no of question is 0 OR user trying to get invalid id than redirect to l4 Inlination page
                    if ($totalQuestion[0]->NoOfTotalQuestions == 0) {
                        return Redirect::to("parent/my-challengers-accept/$professionId/$userid")->with('error', trans('appmessages.no_any_question_profession'));
                        exit;
                    }
                    if (isset($activities) && !empty($activities)) {
                        $activities = $activities;
                        $response['timer'] = $activities[0]->timer;
                    } else {
                        $activities = [];
                        $response['timer'] = 0;
                    }

                    $getTeenagerBoosterPoints = $this->teenagersRepository->getTeenagerBoosterPoints($userid);
                    $response['message'] = trans('appmessages.default_success_msg');
                    $response['NoOfTotalQuestions'] = $totalQuestion[0]->NoOfTotalQuestions;
                    $response['NoOfAttemptedQuestions'] = $totalQuestion[0]->NoOfAttemptedQuestions;
                    $response['status'] = 1;
                    $level4Booster = Helpers::level4Booster($professionId, $userid);
                    $level4Booster['total'] = $getTeenagerBoosterPoints['total'];
                    $response['level4Booster'] = $level4Booster;
                    $level4ParentBooster = Helpers::level4ParentBooster($professionId, $parentid);
                    $response['level4ParentBooster'] = $level4ParentBooster;
                    $response['teen_id'] = $userid;
                    $teenDetail = $this->teenagersRepository->getTeenagerByTeenagerId($userid);

                    $response['teenDetail'] = $teenDetail;

                    // STOP SENDING ALL DATA. JUST SENDING ONLY ONE DATA //$response['data'] = $activities;
                    if (($response['NoOfTotalQuestions'] == $response['NoOfAttemptedQuestions']) || empty($activities)) {
                        $response['data'] = '';
                        $response['setCanvas'] = "yes";
                        return view('parent.level4ProfessionQuestion', compact('response'));
                        exit;
                    } else {
                        $response['setCanvas'] = "no";
                        $response['data'] = (isset($activities[0])) ? $activities[0] : '';
                        
                        return view('parent.level4ProfessionQuestion', compact('response'));
                        exit;
                    }
                    
                } else {
                    Auth::guard('parent')->logout();
                    return Redirect::to('/parent')->with('error', trans('appmessages.invalid_userid_msg'));
                    exit;
                }
            } else {
                Auth::guard('parent')->logout();
                return Redirect::to('/parent')->with('error', trans('appmessages.invalid_userid_msg'));
                exit;
            }
        }
    }

    public function saveLevel4Ans() {

        $response = [];
        $response['status'] = 0;
        $response['message'] = trans('appmessages.default_error_msg');

        if (Auth::guard('parent')->check()) {
            $userid = $this->loggedInUser->user()->id;
            $body = Input::all();


            $timer = (isset($body['timer']) && $body['timer'] > 0) ? $body['timer'] : 0;
            $answerArray = (isset($body['answerID'])) ? $body['answerID'] : '';
            $answerID = (count($answerArray) > 0) ? implode(',', $answerArray) : $answerArray;

            $questionID = (isset($body['questionID']) && $body['questionID'] > 0) ? $body['questionID'] : '';
            $getAllQuestionRelatedDataFromQuestionId = $this->level4ActivitiesRepository->getAllQuestionRelatedDataFromQuestionId($questionID);

            if (isset($getAllQuestionRelatedDataFromQuestionId) && !empty($getAllQuestionRelatedDataFromQuestionId)) {
                $points = $getAllQuestionRelatedDataFromQuestionId->points;
                $type = $getAllQuestionRelatedDataFromQuestionId->type;
            } else {
                $response['data'] = '';
                echo "Reloading...";
                exit;
            }
            $array = [];

            $ansCorrect = $this->level4ActivitiesRepository->checkQuestionRightOrWrong($questionID, $answerID);
            $getProfessionIdFromQuestionId = $this->level4ActivitiesRepository->getProfessionIdFromQuestionId($questionID);

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
            $array['profession_id'] = $getProfessionIdFromQuestionId;

            $data['answers'][] = $array;

            $professionId = $getAllQuestionRelatedDataFromQuestionId->profession_id;

            $questionsArray = $this->level4ActivitiesRepository->saveParentActivityResponse($userid, $data['answers']);

            $getQuestionOPtionFromQuestionId = $this->level4ActivitiesRepository->getQuestionOPtionFromQuestionId($questionID);
            $answerArrayId = explode(',', $getQuestionOPtionFromQuestionId->options_id);
            $answerArrayOptionCorrect = explode(',', $getQuestionOPtionFromQuestionId->correct_option);

            foreach ($answerArrayId as $keyValueP => $idOption) {
                $activities[$idOption] = $answerArrayOptionCorrect[$keyValueP];
            }
            //If user not attempt that profession and trying to give answer of this profession than redirect to L4 Inclination page

            $response['status'] = 1;
            $response['profession_id'] = $getProfessionIdFromQuestionId;
            $response['message'] = trans('appmessages.default_success_msg');
            $response['data'] = $activities;
            $dataPointArray = json_encode($response);
            echo $dataPointArray;

            exit;
        } else {
            Auth::guard('parent')->logout();
            return Redirect::to('/parent')->with('error', trans('appmessages.invalid_userid_msg'));
            exit;
        }
    }

    public function level4PlayMore($professionId,$teenId) {
        $response = [];
        $response['status'] = 0;
        if ($professionId != '' && $professionId != 0) {
            if (Auth::guard('parent')->check()) {
                $parentid = $this->loggedInUser->user()->id;
                if (isset($parentid) && $parentid > 0) {
                    $response['profession_id'] = $professionId;
                    $response['teen_id'] = $teenId;
                    $teenDetail = $this->teenagersRepository->getTeenagerByTeenagerId($teenId);
                    $response['teenDetail'] = $teenDetail;
                    $parentDetail = $this->parentsRepository->getParentDataForCoinsDetail($parentid);
                    $response['available_coins'] = $parentDetail['p_coins'];
                    $getProfessionNameFromProfessionId = $this->professionsRepository->getProfessionsByProfessionId($professionId);
                    if (isset($getProfessionNameFromProfessionId[0]) && !empty($getProfessionNameFromProfessionId[0])) {
                        $response['profession_name'] = $getProfessionNameFromProfessionId[0]->pf_name;
                    } else {
                        $response['profession_name'] = "Choose Any Concepts";
                    }


                    $totalQuestion = $this->level4ActivitiesRepository->getNoOfTotalQuestionsAttemptedQuestionForParent($parentid, $professionId);
                    if ($totalQuestion[0]->NoOfTotalQuestions != 0 && ($totalQuestion[0]->NoOfTotalQuestions == $totalQuestion[0]->NoOfAttemptedQuestions) || ($totalQuestion[0]->NoOfTotalQuestions < $totalQuestion[0]->NoOfAttemptedQuestions)) {
                        $getQuestionTemplateForProfession = $this->level4ActivitiesRepository->getQuestionTemplateForProfession($professionId);

                        $objTemplateDeductedCoins = new TemplateDeductedCoins();

                        if (!empty($getQuestionTemplateForProfession)) {
                            
                            foreach ($getQuestionTemplateForProfession As $key => $value) {
                                $deductedCoinsDetail = $objTemplateDeductedCoins->getDeductedCoinsDetailById($parentid,$professionId,$value->gt_template_id,2);
                                $days = 0;

                                if (!empty($deductedCoinsDetail->toArray())) {
                                    $days = Helpers::calculateRemainingDays($deductedCoinsDetail[0]->tdc_end_date);
                                }
                                $getQuestionTemplateForProfession[$key]->remaningDays = $days;
                                $intermediateActivities = [];
                                $intermediateActivities = $this->level4ActivitiesRepository->getNotAttemptedIntermediateActivitiesForParent($parentid, $professionId, $value->gt_template_id);
                                $totalIntermediateQuestion = $this->level4ActivitiesRepository->getNoOfTotalIntermediateQuestionsAttemptedQuestionForParent($parentid, $professionId, $value->gt_template_id);
                                $response['NoOfTotalQuestions'] = $totalIntermediateQuestion[0]->NoOfTotalQuestions;
                                $response['NoOfAttemptedQuestions'] = $totalIntermediateQuestion[0]->NoOfAttemptedQuestions;
                                if (empty($intermediateActivities) || ($response['NoOfTotalQuestions'] == $response['NoOfAttemptedQuestions']) || ($response['NoOfTotalQuestions'] < $response['NoOfAttemptedQuestions'])) {
                                   $getQuestionTemplateForProfession[$key]->attempted = 'yes';
                                } else {
                                    $getQuestionTemplateForProfession[$key]->attempted = 'no';
                                }
                            }
                        }

                        if (isset($getQuestionTemplateForProfession)) {
                            $response['questionTemplate'] = $getQuestionTemplateForProfession;
                        } else {
                            $response['questionTemplate'] = [];
                        }
                        $response['message'] = trans('appmessages.default_success_msg');
                        $response['status'] = 1;

                        $getTeenagerBoosterPoints = $this->teenagersRepository->getTeenagerBoosterPoints($teenId);
                        $level4Booster = Helpers::level4Booster($professionId, $teenId);
                        $level4ParentBooster = Helpers::level4ParentBooster($professionId, $parentid);
                        $response['level4ParentBooster'] = $level4ParentBooster;
                        $response['teen_id'] = $teenId;
                        $teenDetail = $this->teenagersRepository->getTeenagerByTeenagerId($teenId);
                        $response['teenDetail'] = $teenDetail;
                        $level4Booster['total'] = $getTeenagerBoosterPoints['total'];
                        $response['level4Booster'] = $level4Booster;
                        $response['boosterPoints'] = '';
                        $response['boosterScale'] = 50;

                        return view('parent.level4PlayMore', compact('response'));
                        exit;
                    } else {
                        return Redirect::to("parent/my-challengers-accept/$professionId/$teenId")->with('error', "Play Basic to get to play Intermediate.");
                        exit;
                    }
                } else {
                    Auth::guard('parent')->logout();
                    return Redirect::to('/parent')->with('error', trans('appmessages.invalid_userid_msg'));
                    exit;
                }
            } else {
                Auth::guard('parent')->logout();
                return Redirect::to('/parent')->with('error', trans('appmessages.invalid_userid_msg'));
                exit;
            }
        } else {
            if (Auth::guard('parent')->check()) {
                $userid = $this->loggedInUser->user()->id;
                if (isset($userid) && $userid > 0) {
                    return Redirect::to('/parent/my-challengers');
                    exit;
                } else {
                    Auth::guard('parent')->logout();
                    return Redirect::to('/parent')->with('error', trans('appmessages.invalid_userid_msg'));
                    exit;
                }
            } else {
                Auth::guard('parent')->logout();
                return Redirect::to('/parent')->with('error', trans('appmessages.invalid_userid_msg'));
                exit;
            }
        }
    }

    public function level4IntermediateActivity($professionId, $templateId, $teenId) {
        $response = [];
        $response['profession_name'] = '';
        $response['profession_id'] = '';
        $response['status'] = 0;

        if ($professionId != '' && $professionId > 0 && $templateId != '' && $templateId > 0) {
            if (Auth::guard('parent')->check()) {
                $parentid = $this->loggedInUser->user()->id;
                if (isset($parentid) && $parentid > 0) {
                    $response['profession_id'] = $professionId;
                    $response['teen_id'] = $teenId;
                    $teenDetail = $this->teenagersRepository->getTeenagerByTeenagerId($teenId);
                    $response['teenDetail'] = $teenDetail;
                    $intermediateActivities = [];
                    $intermediateActivities = $this->level4ActivitiesRepository->getNotAttemptedIntermediateActivitiesForParent($parentid, $professionId, $templateId);
                    $totalBasicQuestion = $this->level4ActivitiesRepository->getNoOfTotalQuestionsAttemptedQuestionForParent($parentid, $professionId);
                    $totalIntermediateQuestion = $this->level4ActivitiesRepository->getNoOfTotalIntermediateQuestionsAttemptedQuestionForParent($parentid, $professionId, $templateId);

                    //If total no of question is 0 OR user trying to get invalid id than redirect to l4 Inlination page
                    if ($totalBasicQuestion[0]->NoOfTotalQuestions == 0) {
                        return Redirect::to('/parent/my-challengers-accept/$professionId/$teenId')->with('error', "Profession Doesn't have any basic questions");
                        exit;
                    } else if ($totalBasicQuestion[0]->NoOfTotalQuestions > $totalBasicQuestion[0]->NoOfAttemptedQuestions) {
                        return Redirect::to("/parent/my-challengers-accept/$professionId/$teenId")->with('error', "Play Basic to get to play Intermediate.");
                        exit;
                    } else {

                    }

                    if (isset($intermediateActivities) && !empty($intermediateActivities)) {
                        $intermediateActivitiesData = $intermediateActivities[0];
                        $intermediateActivitiesData->gt_temlpate_answer_type = Helpers::getAnsTypeFromGamificationTemplateId($intermediateActivitiesData->l4ia_question_template);
                        
                        $intermediateActivitiesData->l4ia_extra_question_time = $this->extraQuestionDescriptionTime;
                        $response['timer'] = $intermediateActivitiesData->l4ia_question_time;
                        //Set popup image
                        if ($intermediateActivitiesData->l4ia_question_popup_image != '') {
                            if ($intermediateActivitiesData->l4ia_question_popup_image != '' && isset($intermediateActivitiesData->l4ia_question_popup_image)) {
                                $intermediateActivitiesData->l4ia_question_popup_image = Storage::url($this->questionDescriptionORIGINALImage . $intermediateActivitiesData->l4ia_question_popup_image);
                            } else {
                                $intermediateActivitiesData->l4ia_question_popup_image = Storage::url($this->questionDescriptionORIGINALImage . 'proteen-logo.png');
                            }
                        } else {
                            $intermediateActivitiesData->l4ia_question_popup_image = '';
                        }

                        //Set popup description
                        if ($intermediateActivitiesData->l4ia_question_popup_description != '') {
                            $intermediateActivitiesData->l4ia_question_popup_description = $intermediateActivitiesData->l4ia_question_popup_description;
                        } else {
                            $intermediateActivitiesData->l4ia_question_popup_description = '';
                        }

                        //Set Question audio
                        if (isset($intermediateActivitiesData->l4ia_question_audio) && $intermediateActivitiesData->l4ia_question_audio != '') {
                            if (file_exists($this->questionDescriptionORIGINALImage . $intermediateActivitiesData->l4ia_question_audio)) {
                                $intermediateActivitiesData->l4ia_question_audio = asset($this->questionDescriptionORIGINALImage . $intermediateActivitiesData->l4ia_question_audio);
                            } else {
                                $intermediateActivitiesData->l4ia_question_audio = '';
                            }
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
                                if (isset($image['image']) && $image['image'] != '') {
                                    $intermediateActivitiesData->question_images[$key]['l4ia_question_image'] = $this->questionDescriptionORIGINALImage . $image['image'];
                                    $intermediateActivitiesData->question_images[$key]['l4ia_question_imageDescription'] = $image['imageDescription'];
                                } else {
                                    $intermediateActivitiesData->question_images[$key]['l4ia_question_image'] = $this->questionDescriptionORIGINALImage . 'proteen-logo.png';
                                    $intermediateActivitiesData->question_images[$key]['l4ia_question_imageDescription'] = $image['imageDescription'];
                                }
                            }
                        } else {
                            $intermediateActivitiesData->l4ia_question_image = $intermediateActivitiesData->l4ia_question_imageDescription = '';
                        }
                    } else {
                        $intermediateActivitiesData = [];
                    }
                    $getTeenagerBoosterPoints = $this->teenagersRepository->getTeenagerBoosterPoints($teenId);
                    $level4Booster = Helpers::level4Booster($professionId, $teenId);
                    $level4Booster['total'] = $getTeenagerBoosterPoints['total'];
                    $level4ParentBooster = Helpers::level4ParentBooster($professionId, $parentid);
                    $response['level4ParentBooster'] = $level4ParentBooster;
                    $response['teen_id'] = $teenId;
                    $teenDetail = $this->teenagersRepository->getTeenagerByTeenagerId($teenId);
                    $response['teenDetail'] = $teenDetail;
                    $response['message'] = trans('appmessages.default_success_msg');
                    $response['NoOfTotalQuestions'] = $totalIntermediateQuestion[0]->NoOfTotalQuestions;
                    $response['NoOfAttemptedQuestions'] = $totalIntermediateQuestion[0]->NoOfAttemptedQuestions;
                    $response['status'] = 1;
                    $response['level4Booster'] = $level4Booster;
                    $response['boosterPoints'] = '';
                    $response['boosterScale'] = 50;
                    if (empty($intermediateActivitiesData) || ($response['NoOfTotalQuestions'] == $response['NoOfAttemptedQuestions']) || ($response['NoOfTotalQuestions'] < $response['NoOfAttemptedQuestions'])) {
                        $response['data'] = '';
                        $response['setCanvas'] = "yes";
                        return view('parent.level4ProfessionIntermediateQuestion', compact('response'));
                        exit;
                    } else {
                        $response['setCanvas'] = "no";
                        $response['data'] = $intermediateActivitiesData;
                        return view('parent.level4ProfessionIntermediateQuestion', compact('response'));
                        exit;
                    }
                } else {
                    Auth::guard('parent')->logout();
                    return Redirect::to('/parent')->with('error', trans('appmessages.invalid_userid_msg'));
                    exit;
                }
            } else {
                Auth::guard('parent')->logout();
                return Redirect::to('/parent')->with('error', trans('appmessages.invalid_userid_msg'));
                exit;
            }
        } else {
            if (Auth::guard('parent')->check()) {
                $userid = $this->loggedInUser->user()->id;
                if (isset($userid) && $userid > 0) {
                    return Redirect::to('/parent/my-challengers');
                    exit;
                } else {
                    Auth::guard('parent')->logout();
                    return Redirect::to('/parent')->with('error', trans('appmessages.invalid_userid_msg'));
                    exit;
                }
            } else {
                Auth::guard('parent')->logout();
                return Redirect::to('/parent')->with('error', trans('appmessages.invalid_userid_msg'));
                exit;
            }
        }
    }

    public function saveLevel4IntermediateAns() {
        $response = [];
        $response['status'] = 0;
        $response['reload'] = 1;
        $response['message'] = trans('appmessages.default_error_msg');
        if (Auth::guard('parent')->check()) {
            $body = Input::all();
            $body['userid'] = $this->loggedInUser->user()->id;
            $userid = $body['userid'];
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
                $professionId = $getAllQuestionRelatedDataFromQuestionId->l4ia_profession_id;
                $response['profession_id'] = $professionId;
                $getTeenagerBoosterPoints = $this->teenagersRepository->getTeenagerBoosterPoints($body['userid']);
                // Check Teenager having sufficient point OR not
                //$getTeenagerAttemptedProfession = $this->professionsRepository->getTeenagerAttemptedProfession($body['userid']);
                //Check Teenager attempted this profession or not
                /*if (isset($getTeenagerAttemptedProfession) && !empty($getTeenagerAttemptedProfession)) {
                    foreach ($getTeenagerAttemptedProfession as $keyId) {
                        $professionList[] = $keyId->id;
                    }
                } else {
                    $professionList = array();
                }*/
                //if (in_array($professionId, $professionList)) {
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
                            $data['l4iapa_parent_id'] = $body['userid'];
                            $data['l4iapa_activity_id'] = $body['questionID'];
                            $data['l4iapa_profession_id'] = $professionId;
                            $data['l4iapa_template_id'] = $getAllQuestionRelatedDataFromQuestionId->l4ia_question_template;
                            $data['l4iapa_answer'] = (isset($body['timer']) && $body['timer'] != 0) ? $body['answer'][0] : 0;
                            $data['l4iapa_order'] = 0;
                            $data['l4iapa_earned_point'] = $earnedPoints;
                            $data['l4iapa_time'] = $body['timer'];
                            $templateId = $getAllQuestionRelatedDataFromQuestionId->l4ia_question_template;

                            $saveLevel4IntemediateActivityAnswer = $this->level4ActivitiesRepository->saveParentIntermediateActivitySingleLineAnswer($body['userid'], $data);
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
                            $data['l4iapa_parent_id'] = $body['userid'];
                            $data['l4iapa_activity_id'] = $body['questionID'];
                            $data['l4iapa_profession_id'] = $professionId;
                            $data['l4iapa_template_id'] = $getAllQuestionRelatedDataFromQuestionId->l4ia_question_template;
                            $data['l4iapa_answer'] = (isset($body['timer']) && $body['timer'] != 0) ? $body['answer'][0] : 0;
                            $data['l4iapa_order'] = (isset($body['answer_order'][0])) ? $body['answer_order'][0] : 0;
                            $data['l4iapa_earned_point'] = $earnedPoints;
                            $data['l4iapa_time'] = $body['timer'];
                            $templateId = $getAllQuestionRelatedDataFromQuestionId->l4ia_question_template;

                            $saveLevel4IntemediateActivityAnswer = $this->level4ActivitiesRepository->saveParentIntermediateActivityDropDownAnswer($body['userid'], $data);
                            $response['message'] = trans('appmessages.default_success_msg');
                            $response['status'] = 1;
                        } else if ($getAllQuestionRelatedDataFromQuestionId->gt_temlpate_answer_type == "image_reorder" || $getAllQuestionRelatedDataFromQuestionId->gt_temlpate_answer_type == "option_reorder") {
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
                            $data['l4iapa_parent_id'] = $body['userid'];
                            $data['l4iapa_activity_id'] = $body['questionID'];
                            $data['l4iapa_profession_id'] = $professionId;
                            $data['l4iapa_template_id'] = $getAllQuestionRelatedDataFromQuestionId->l4ia_question_template;
                            $data['l4iapa_order'] = 0;
                            $data['l4iapa_earned_point'] = $earnedPoints;
                            $data['l4iapa_time'] = $body['timer'];
                            $templateId = $getAllQuestionRelatedDataFromQuestionId->l4ia_question_template;

                            $saveLevel4IntemediateActivityAnswer = $this->level4ActivitiesRepository->saveParentIntermediateActivityImageReorderAnswer($body['userid'], $data, $userAnswerIdArray2);
                            $response['message'] = trans('appmessages.default_success_msg');
                            $response['status'] = 1;
                        } else if ($getAllQuestionRelatedDataFromQuestionId->gt_temlpate_answer_type == "filling_blank" || $getAllQuestionRelatedDataFromQuestionId->gt_temlpate_answer_type == "true_false" || $getAllQuestionRelatedDataFromQuestionId->gt_temlpate_answer_type == "option_choice" || $getAllQuestionRelatedDataFromQuestionId->gt_temlpate_answer_type == "option_choice_with_response") {
                            $checkAnswerFromOption = '';

                            $correctOptionsArray = explode(',', $getAllQuestionRelatedDataFromQuestionId->correct_option);
                            if ($getAllQuestionRelatedDataFromQuestionId->gt_temlpate_answer_type == "option_choice") {
                                $response['questionAnswerText'] = ($getAllQuestionRelatedDataFromQuestionId->l4ia_question_answer_description != '') ? $getAllQuestionRelatedDataFromQuestionId->l4ia_question_answer_description : '';
                            }
                            if ($getAllQuestionRelatedDataFromQuestionId->gt_temlpate_answer_type == "option_choice_with_response") {
                                if (isset($body['answer'][0]) && $body['answer'][0] != 0) {
                                    $getAnswerResponseTextAndImage = $this->level4ActivitiesRepository->getAnswerResponseTextAndImage($body['answer'][0]);
                                    if (isset($getAnswerResponseTextAndImage) && !empty($getAnswerResponseTextAndImage)) {
                                        $response['questionAnswerText'] = (isset($getAnswerResponseTextAndImage['answerResponseText']) && $getAnswerResponseTextAndImage['answerResponseText'] != '') ? $getAnswerResponseTextAndImage['answerResponseText'] : '';
                                        if (isset($getAnswerResponseTextAndImage['answerResponseImage']) && $getAnswerResponseTextAndImage['answerResponseImage'] != '') {
                                            if (isset($getAnswerResponseTextAndImage['answerResponseImage']) && $getAnswerResponseTextAndImage['answerResponseImage'] != '') {
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
                            $data['l4iapa_parent_id'] = $body['userid'];
                            $data['l4iapa_activity_id'] = $body['questionID'];
                            $data['l4iapa_profession_id'] = $professionId;
                            $data['l4iapa_template_id'] = $getAllQuestionRelatedDataFromQuestionId->l4ia_question_template;
                            $body['answer'] = (isset($body['timer']) && $body['timer'] != 0) ? $body['answer'] : $body['answer'];
                            $data['l4iapa_order'] = 0;
                            $data['l4iapa_earned_point'] = $earnedPoints;
                            $data['l4iapa_time'] = $body['timer'];
                            $templateId = $getAllQuestionRelatedDataFromQuestionId->l4ia_question_template;

                            $saveLevel4IntemediateActivityAnswer = $this->level4ActivitiesRepository->saveParentIntermediateActivityFillInBlanksAnswer($body['userid'], $data, $body['answer']);
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
                        //$getTeenagerBoosterPoints = $this->teenagersRepository->getTeenagerBoosterPoints($body['userid']);

                        //$level4Booster = Helpers::level4ParentBooster($professionId, $userid);
                        //$response['level4ParentBooster'] = $level4Booster;
                        $response['boosterPoints'] = '';
                        $response['boosterScale'] = 50;
                    } else {
                        $response['status'] = 0;
                        $response['message'] = "Invalid Question Id";
                        $response['reload'] = 1;
                    }
                /*} else {
                    $response['status'] = 0;
                    $response['message'] = "Profession Not Valid";
                    $response['reload'] = 1;
                    $response['redirect'] = '/parent/myChallengers/';
                }*/
            } else {
                $response['status'] = 0;
                $response['reload'] = 1;
                $response['message'] = "Your option choice is not belong from this question's option";
                $response['redirect'] = '/parent/my-challengers/';
            }
        } else {
            Auth::guard('parent')->logout();
            $response['status'] = 0;
            $response['message'] = "Invalid User";
            $response['reload'] = 1;
            $response['redirect'] = '/parent';
        }
        $dataPointArray = json_encode($response);
        echo $dataPointArray;
        exit;
    }

    public function getL4BasicQuestions() {
        $professionId = Input::get('professionId');
        $userId = Auth::guard('parent')->user()->id;
        if($userId > 0 && $professionId != '') {
            $totalQuestion = $this->level4ActivitiesRepository->getNoOfTotalQuestionsAttemptedQuestionForParent($userId, $professionId);
            $professionName = $this->professionsRepository->getProfessionNameById($professionId);
            $basicCompleted = 0; 
            if(isset($totalQuestion[0]->NoOfTotalQuestions) && $totalQuestion[0]->NoOfTotalQuestions > 0 && ($totalQuestion[0]->NoOfTotalQuestions == $totalQuestion[0]->NoOfAttemptedQuestions) ) {
                $basicCompleted = 1;
                //dispatch( new CalculateProfessionCompletePercentage($userId, $professionId) );
            }
            
            $activities = $this->level4ActivitiesRepository->getNotAttemptedActivitiesForParent($userId, $professionId);
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
            $response['parentName'] = Auth::guard('parent')->user()->p_first_name . ' '.Auth::guard('parent')->user()->p_last_name;
            $response['status'] = 1;
            return view('parent.basic.careerBasicQuizSection', compact('response'));
        }
        $response['status'] = 0;
        $response['message'] = "Something went wrong!";

        return response()->json($response, 200);
        exit;
    }

    /*
     * Save parent level 4 basic activity answer
     */
    public function saveBasicLevelActivity() {
        $response = [];
        $userId = Auth::guard('parent')->user()->id;
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
                $questionsArray = $this->level4ActivitiesRepository->saveParentActivityResponse($userId, $data['answers']);

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

    public function professionIntermediateQuestions() {
        $professionId = Input::get('professionId');
        $templateId = Input::get('templateId');
        $userId = Auth::guard('parent')->user()->id;
        if($userId > 0 && $professionId != '' && $templateId != '') {
            $intermediateActivities = $this->level4ActivitiesRepository->getNotAttemptedIntermediateActivitiesForParent($userId, $professionId, $templateId);
            $totalIntermediateQuestion = $this->level4ActivitiesRepository->getNoOfTotalIntermediateQuestionsAttemptedQuestionForParent($userId, $professionId, $templateId);
            $professionName = $this->professionsRepository->getProfessionNameById($professionId);
            
            $intermediateCompleted = 0;
            if(isset($totalIntermediateQuestion[0]->NoOfTotalQuestions) && $totalIntermediateQuestion[0]->NoOfTotalQuestions > 0 && ($totalIntermediateQuestion[0]->NoOfAttemptedQuestions >= $totalIntermediateQuestion[0]->NoOfTotalQuestions) ) {
                $intermediateCompleted = 1;
                //dispatch( new CalculateProfessionCompletePercentage($userId, $professionId) );
            }
            
            //$activities = $this->level4ActivitiesRepository->getNotAttemptedActivities($userId, $professionId);
            if (isset($intermediateActivities[0]) && !empty($intermediateActivities[0])) {
                $intermediateActivitiesData = $intermediateActivities[0];
                
                $intermediateActivitiesData->gt_temlpate_answer_type = Helpers::getAnsTypeFromGamificationTemplateId($intermediateActivitiesData->l4ia_question_template);
                $intermediateActivitiesData->l4ia_extra_question_time = $this->extraQuestionDescriptionTime;
                $timer = $intermediateActivitiesData->l4ia_question_time;
                $response['timer'] = $intermediateActivitiesData->l4ia_question_time;
                //Question Popup Image
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
            $response = [];
            $response['message'] = trans('appmessages.default_success_msg');
            $response['NoOfTotalQuestions'] = $totalIntermediateQuestion[0]->NoOfTotalQuestions;
            $response['NoOfAttemptedQuestions'] = $totalIntermediateQuestion[0]->NoOfAttemptedQuestions;
            $response['intermediateCompleted'] = $intermediateCompleted;
            $response['data'] = $intermediateActivitiesData;
            $response['timer'] = $timer;
            $response['professionId'] = $professionId;
            $response['professionName'] = $professionName;
            $response['parentName'] = Auth::guard('parent')->user()->p_first_name . ' '.Auth::guard('parent')->user()->p_last_name;
            $response['status'] = 1;
            //print_r($intermediateActivitiesData); die();
            if(isset($intermediateActivitiesData->gt_temlpate_answer_type) && in_array($intermediateActivitiesData->gt_temlpate_answer_type, ["option_choice", "option_choice_with_response", "true_false", "filling_blank"]) ) { 
                return view('parent.basic.careerIntermediateOptionChoiceQuestion', compact('response'));
            } else if(isset($intermediateActivitiesData->gt_temlpate_answer_type) && $intermediateActivitiesData->gt_temlpate_answer_type == "single_line_answer") {
                return view('parent.basic.careerIntermediateSingleLineQuestion', compact('response'));
            } else if(isset($intermediateActivitiesData->gt_temlpate_answer_type) && $intermediateActivitiesData->gt_temlpate_answer_type == "select_from_dropdown_option") {
                return view('parent.basic.careerIntermediateDropDownSelectQuestion', compact('response'));
            } else if(isset($intermediateActivitiesData->gt_temlpate_answer_type) && $intermediateActivitiesData->gt_temlpate_answer_type == "option_reorder") {
                return view('parent.basic.careerIntermediateOptionReorderQuestion', compact('response'));
            } else if(isset($intermediateActivitiesData->gt_temlpate_answer_type) && $intermediateActivitiesData->gt_temlpate_answer_type == "image_reorder") {
                return view('parent.basic.careerIntermediateImageReorderQuestion', compact('response'));
                //return view('teenager.basic.careerIntermediateQuizQuestion', compact('response'));
            } else {
                
            }
            return view('parent.basic.careerIntermediateQuizQuestion', compact('response'));
        }
        $response['status'] = 0;
        $response['message'] = "Something went wrong!";

        return response()->json($response, 200);
        exit;
    }

    //Save Intermediate activity answer
    public function saveIntermediateLevelActivity() {
        $response = [];
        $userId = Auth::guard('parent')->user()->id;
        
        if ($userId && $userId > 0) {
            $body = Input::all();

            $body['userid'] = Auth::guard('parent')->user()->id;
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
                        $data['l4iapa_parent_id'] = $body['userid'];
                        $data['l4iapa_activity_id'] = $body['questionID'];
                        $data['l4iapa_profession_id'] = $professionId;
                        $data['l4iapa_template_id'] = $getAllQuestionRelatedDataFromQuestionId->l4ia_question_template;
                        $data['l4iapa_answer'] = (isset($body['timer']) && $body['timer'] != 0) ? $body['answer'][0] : 0;
                        $data['l4iapa_order'] = 0;
                        $data['l4iapa_earned_point'] = $earnedPoints;
                        $data['l4iapa_time'] = $body['timer'];
                        $templateId = $getAllQuestionRelatedDataFromQuestionId->l4ia_question_template;
                        // $objProfessionLearningStyle = new ProfessionLearningStyle();
                        // $learningId = $objProfessionLearningStyle->getIdByProfessionId($professionId,$templateId);
                        // if ($learningId != '') {
                        //     $objUserLearningStyle = new UserLearningStyle();
                        //     $learningData = $objUserLearningStyle->getUserLearningStyle($learningId);
                        //     if (!empty($learningData)) {
                        //         $earnedPoints += $learningData->uls_earned_points;
                        //     }
                        //     $userData = [];
                        //     $userData['uls_learning_style_id'] = $learningId;
                        //     $userData['uls_profession_id'] = $professionId;
                        //     $userData['uls_teenager_id'] = $body['userid'];
                        //     $userData['uls_earned_points'] = $earnedPoints;
                        //     $result = $objUserLearningStyle->saveUserLearningStyle($userData);
                        // }

                        $saveLevel4IntemediateActivityAnswer = $this->level4ActivitiesRepository->saveParentIntermediateActivitySingleLineAnswer($body['userid'], $data);
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
                        $data['l4iapa_parent_id'] = $body['userid'];
                        $data['l4iapa_activity_id'] = $body['questionID'];
                        $data['l4iapa_profession_id'] = $professionId;
                        $data['l4iapa_template_id'] = $getAllQuestionRelatedDataFromQuestionId->l4ia_question_template;
                        $data['l4iapa_answer'] = (isset($body['timer']) && $body['timer'] != 0) ? $body['answer'][0] : 0;
                        $data['l4iapa_order'] = (isset($body['answer_order'][0])) ? $body['answer_order'][0] : 0;
                        $data['l4iapa_earned_point'] = $earnedPoints;
                        $data['l4iapa_time'] = $body['timer'];
                        $templateId = $getAllQuestionRelatedDataFromQuestionId->l4ia_question_template;
                        // $objProfessionLearningStyle = new ProfessionLearningStyle();
                        // $learningId = $objProfessionLearningStyle->getIdByProfessionId($professionId,$templateId);
                        // if ($learningId != '') {
                        //     $objUserLearningStyle = new UserLearningStyle();
                        //     $learningData = $objUserLearningStyle->getUserLearningStyle($learningId);
                        //     if (!empty($learningData)) {
                        //         $earnedPoints += $learningData->uls_earned_points;
                        //     }
                        //     $userData = [];
                        //     $userData['uls_learning_style_id'] = $learningId;
                        //     $userData['uls_profession_id'] = $professionId;
                        //     $userData['uls_teenager_id'] = $body['userid'];
                        //     $userData['uls_earned_points'] = $earnedPoints;
                        //     $result = $objUserLearningStyle->saveUserLearningStyle($userData);
                        // }

                        $saveLevel4IntemediateActivityAnswer = $this->level4ActivitiesRepository->saveParentIntermediateActivityDropDownAnswer($body['userid'], $data);
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
                        $data['l4iapa_parent_id'] = $body['userid'];
                        $data['l4iapa_activity_id'] = $body['questionID'];
                        $data['l4iapa_profession_id'] = $professionId;
                        $data['l4iapa_template_id'] = $getAllQuestionRelatedDataFromQuestionId->l4ia_question_template;
                        $data['l4iapa_order'] = 0;
                        $data['l4iapa_earned_point'] = $earnedPoints;
                        $data['l4iapa_time'] = $body['timer'];
                        $templateId = $getAllQuestionRelatedDataFromQuestionId->l4ia_question_template;
                        // $objProfessionLearningStyle = new ProfessionLearningStyle();
                        // $learningId = $objProfessionLearningStyle->getIdByProfessionId($professionId,$templateId);
                        // if ($learningId != '') {
                        //     $objUserLearningStyle = new UserLearningStyle();
                        //     $learningData = $objUserLearningStyle->getUserLearningStyle($learningId);
                        //     if (!empty($learningData)) {
                        //         $earnedPoints += $learningData->uls_earned_points;
                        //     }
                        //     $userData = [];
                        //     $userData['uls_learning_style_id'] = $learningId;
                        //     $userData['uls_profession_id'] = $professionId;
                        //     $userData['uls_teenager_id'] = $body['userid'];
                        //     $userData['uls_earned_points'] = $earnedPoints;
                        //     $result = $objUserLearningStyle->saveUserLearningStyle($userData);
                        // }

                        $saveLevel4IntemediateActivityAnswer = $this->level4ActivitiesRepository->saveParentIntermediateActivityImageReorderAnswer($body['userid'], $data, $userAnswerIdArray2);
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
                        $data['l4iapa_parent_id'] = $userId;
                        $data['l4iapa_activity_id'] = $body['questionID'];
                        $data['l4iapa_profession_id'] = $professionId;
                        $data['l4iapa_template_id'] = $getAllQuestionRelatedDataFromQuestionId->l4ia_question_template;
                        $body['answer'] = (isset($body['timer']) && $body['timer'] != 0) ? $body['answer'] : $body['answer'];
                        $data['l4iapa_order'] = 0;
                        $data['l4iapa_earned_point'] = $earnedPoints;
                        $data['l4iapa_time'] = $body['timer'];
                        
                        $templateId = $getAllQuestionRelatedDataFromQuestionId->l4ia_question_template;
                        // $objProfessionLearningStyle = new ProfessionLearningStyle();
                        // $learningId = $objProfessionLearningStyle->getIdByProfessionId($professionId,$templateId);
                        // if ($learningId != '') {
                        //     $objUserLearningStyle = new UserLearningStyle();
                        //     $learningData = $objUserLearningStyle->getUserLearningStyle($learningId);
                        //     if (!empty($learningData)) {
                        //         $earnedPoints += $learningData->uls_earned_points;
                        //     }
                        //     $userData = [];
                        //     $userData['uls_learning_style_id'] = $learningId;
                        //     $userData['uls_profession_id'] = $professionId;
                        //     $userData['uls_teenager_id'] = $userId;
                        //     $userData['uls_earned_points'] = $earnedPoints;
                        //     $result = $objUserLearningStyle->saveUserLearningStyle($userData);
                        // }

                        $saveLevel4IntemediateActivityAnswer = $this->level4ActivitiesRepository->saveParentIntermediateActivityFillInBlanksAnswer($userId, $data, $body['answer']);
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
                    //$level4Booster['total'] = $total;
                    //$response['level4Booster'] = $level4Booster;
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

    /**
     * Returns learning Guidance page.
     *
     * @return \Illuminate\Http\Response
     */
    public function learningGuidance($teenUniqueId)
    {
        //$learningGuidance = Helpers::learningGuidance();
        $teenDetails = $this->teenagersRepository->getTeenagerByUniqueId($teenUniqueId);
        $userId = $teenDetails->id;
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
        
        return view('parent.learningGuidance', compact('learningGuidance'));
    }

    /*
     * Store consumed coins details and returns remaining days
     */
    public function saveConsumedCoinsDetails() {
        //$userId = Input::get('teenId');
        $parentId = Auth::guard('parent')->user()->id;
        $objPaidComponent = new PaidComponent();
        $objDeductedCoins = new DeductedCoins();
        $componentsData = $objPaidComponent->getPaidComponentsData(Config::get('constant.LEARNING_STYLE'));
        $coins = $componentsData->pc_required_coins;
        $deductedCoinsDetail = $objDeductedCoins->getDeductedCoinsDetailByIdForLS($parentId,$componentsData->id,2);
        $days = 0;
        if (!empty($deductedCoinsDetail->toArray())) {
            $days = Helpers::calculateRemainingDays($deductedCoinsDetail[0]->dc_end_date);
        }
        $response['status'] = 0;
        $response['coinsDetails'] = $coins;
        if ($days == 0) {
            $deductCoins = 0;
            //deduct coin from user
            $parentDetail = $this->parentsRepository->getParentDataForCoinsDetail($parentId);
            if (!empty($parentDetail)) {
                $deductCoins = $parentDetail['p_coins']-$coins;
            }
            $returnData = $this->parentsRepository->updateParentCoinsDetail($parentId, $deductCoins);

            $return = Helpers::saveDeductedCoinsData($parentId,2,$coins,Config::get('constant.LEARNING_STYLE'),0);
            if ($return) {
                $remainingDays = 0;
                $updatedDeductedCoinsDetail = $objDeductedCoins->getDeductedCoinsDetailByIdForLS($parentId,$componentsData->id,2);
                if (!empty($updatedDeductedCoinsDetail)) {
                    $remainingDays = Helpers::calculateRemainingDays($updatedDeductedCoinsDetail[0]->dc_end_date);
                }
                $response['status'] = 1;
                $response['coinsDetails'] = $remainingDays;
            } 
        }
        
        return response()->json($response, 200);
        exit;
    }
}
