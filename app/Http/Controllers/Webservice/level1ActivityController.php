<?php

namespace App\Http\Controllers\Webservice;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Services\Level1Activity\Contracts\Level1ActivitiesRepository;
use Auth;
use Illuminate\Http\Request;
use Config;
use Input;
use Redirect;
use Image;
use App\Services\Teenagers\Contracts\TeenagersRepository;
use Helpers;
use Storage;
use App\Level1Activity;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class Level1ActivityController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Level1ActivitiesRepository $level1ActivitiesRepository, TeenagersRepository $teenagersRepository)
    {
        $this->level1ActivitiesRepository = $level1ActivitiesRepository;
        $this->objLevel1Activity = new Level1Activity;
        $this->teenagersRepository = $teenagersRepository;
        $this->level1ActivityThumbImageUploadPath = Config::get('constant.LEVEL1_ACTIVITY_THUMB_IMAGE_UPLOAD_PATH');
        $this->level1ActivityOriginalImageUploadPath = Config::get('constant.LEVEL1_ACTIVITY_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->log = new Logger('api-level1-activity-controller');
        $this->log->pushHandler(new StreamHandler(storage_path().'/logs/monolog-'.date('m-d-Y').'.log'));
        $this->cartoonThumbImageWidth = Config::get('constant.CARTOON_THUMB_IMAGE_WIDTH');
        $this->cartoonThumbImageHeight = Config::get('constant.CARTOON_THUMB_IMAGE_HEIGHT');
        $this->cartoonThumbImageUploadPath = Config::get('constant.CARTOON_THUMB_IMAGE_UPLOAD_PATH');
        $this->cartoonOriginalImageUploadPath = config::get('constant.CARTOON_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->humanThumbImageWidth = Config::get('constant.HUMAN_THUMB_IMAGE_WIDTH');
        $this->humanThumbImageHeight = Config::get('constant.HUMAN_THUMB_IMAGE_HEIGHT');
        $this->humanOriginalImageUploadPath = config::get('constant.HUMAN_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->humanThumbImageUploadPath = Config::get('constant.HUMAN_THUMB_IMAGE_UPLOAD_PATH');
        $this->relationIconOriginalImageUploadPath = Config::get('constant.RELATION_ICON_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->relationIconThumbImageUploadPath = Config::get('constant.RELATION_ICON_THUMB_IMAGE_UPLOAD_PATH');
        
    }

    /* Request Params : getFiestLevelActivity
    *  loginToken, userId
    *  Array of not attempted all level 1 part 1 questions
    */
    public function getFirstLevelActivity(Request $request) {
        $response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg')];
        $teenager = $this->teenagersRepository->getTeenagerById($request->userId);
        $this->log->info('Get teenager detail for userId'.$request->userId , array('api-name'=> 'getFirstLevelActivity'));
        if($request->userId != "" && $teenager) {
            $activities = $this->level1ActivitiesRepository->getNotAttemptedActivities($request->userId);
            if($activities) {
                $imageArray = ['icon-4.png', 'icon-3.png', 'icon-5.png'];
                foreach ($activities as $key => $activity) {
                    $activities[$key]->l1ac_image = ($activities[$key]->l1ac_image != "") ? Storage::url($this->level1ActivityOriginalImageUploadPath . $activity->l1ac_image) : $this->level1ActivityOriginalImageUploadPath . "proteen-logo.png";
                    if(isset($activity->options) && $activity->options) {
                        foreach($activity->options as $optionsKey => $optionsValue) {
                            $activity->options[$optionsKey]['optionImage'] =  (isset($imageArray[$optionsKey])) ?  Storage::url('img/Original-image/' . $imageArray[$optionsKey]) : Storage::url('img/Original-image/icon-4.png');
                        }
                    }
                    $hintData = [];
                    $hintText = $totalTrend = "";
                    if ($activity->activityID > 0) {
                        $level1AnswerTrend = Helpers::calculateTrendForLevel1($activity->activityID, 1);
                        if($level1AnswerTrend) {
                            $points = 0;
                            foreach($level1AnswerTrend as $keyTrend => $trendValue) {
                                if($keyTrend + 1 == count($level1AnswerTrend)) {
                                    $level1AnswerTrend[$keyTrend]['percentage'] = 100 - $points;
                                } else {
                                    $points = $points + round($trendValue['percentage']);
                                    $level1AnswerTrend[$keyTrend]['percentage'] = round($trendValue['percentage']);
                                }
                            }
                        }
                        $totalTrend = Helpers::calculateTotalTrendForLevel1($activity->activityID, 1);
                        $activityQuestionText = $activity->l1ac_text;
                        $hintText = $activity->l1ac_text;
                        $hintData = ($level1AnswerTrend) ? $level1AnswerTrend : [];
                    }
                    $activities[$key]->hint_text = $hintText;
                    $activities[$key]->hint_data = $hintData;
                    $activities[$key]->total_trend = $totalTrend;
                }
            }

            $totalQuestion = $this->level1ActivitiesRepository->getNoOfTotalQuestionsAttemptedQuestion($request->userId);
            $response['NoOfTotalQuestions'] = (isset($totalQuestion[0]->NoOfTotalQuestions)) ? $totalQuestion[0]->NoOfTotalQuestions : 0;
            $response['NoOfAttemptedQuestions'] = (isset($totalQuestion[0]->NoOfAttemptedQuestions)) ? $totalQuestion[0]->NoOfAttemptedQuestions : 0;
            $response['status'] = 1;
            $response['login'] = 1;
            $response['message'] = trans('appmessages.default_success_msg');
            $response['data'] = $activities;
            $this->log->info('Response for Level1questions' , array('api-name'=> 'getLevel1Questions'));
        } else {
            $this->log->error('Parameter missing error' , array('api-name'=> 'getFirstLevelActivity'));
            $response['message'] = trans('appmessages.missing_data_msg');
        }
        
        return response()->json($response, 200);
        exit;
    }
    /* Request Params : saveFirstLevelActivity
    *  loginToken, userId, questionId, answerId
    *  Array of not attempted all level 1 part 1 questions
    */
    public function saveFirstLevelActivity(Request $request) {
        $response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg')];
        $teenager = $this->teenagersRepository->getTeenagerById($request->userId);
        $questionOption = $this->objLevel1Activity->questionOptions($request->questionId);
        
        $this->log->info('Get teenager & level 1 question detail for userId'.$request->userId , array('api-name'=> 'saveFirstLevelActivity'));
        if( $questionOption->toArray() && isset($questionOption[0]->options) && in_array($request->answerId, array_column($questionOption[0]->options->toArray(), 'id')) && $request->userId != "" && $teenager) {
            
            $answers = [];
            $answers['answerID'] = $request->answerId;
            $answers['questionID'] = $questionOption[0]->id;
            $answers['points'] = $questionOption[0]->l1ac_points;

            $questionsArray = $this->level1ActivitiesRepository->saveTeenagerActivityResponseOneByOne($request->userId, $answers);
            if(isset($questionsArray['questionsID'][0])) {
                $questionsArray['questionsID'] = $questionsArray['questionsID'][0];
            }

            $response['status'] = 1;
            $response['login'] = 1;
            $response['message'] = trans('appmessages.default_success_msg');
            $response['data'] = $questionsArray;
            $this->log->info('Response for save Level1questions answer' , array('api-name'=> 'saveFirstLevelActivity'));
        } else {
            $this->log->error('Parameter missing error' , array('api-name'=> 'saveFirstLevelActivity'));
            $response['login'] = 1;
            $response['message'] = trans('appmessages.missing_data_msg');
        }
        
        return response()->json($response, 200);
        exit;
    }

    /* Request Params : getLevel1Part2Options
    *  loginToken, userId
    *  Array of not attempted all level 1 part 1 questions
    */
    public function getLevel1Part2Options(Request $request) {
        $response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg')];
        $teenager = $this->teenagersRepository->getTeenagerById($request->userId);
        $this->log->info('Get teenager detail for userId'.$request->userId , array('api-name'=> 'getLevel1Part2Options'));
        if($request->userId != "" && $teenager) {
            $qualityDetail = $this->level1ActivitiesRepository->getLevel1qualities();
            $mainqualityArray = [];
            foreach ($qualityDetail as $detail) {
                $qualityList = [];
                $qualityList['id'] = $detail->id;
                $qualityList['quality'] = $detail->l1qa_name;
                $mainqualityArray[] = $qualityList;
            }
            $mainArray['qualityList'] = $mainqualityArray;

            //Get top trending images
            $topTrendingImages = $this->level1ActivitiesRepository->getTopTrendingImages();
            $topImages['image'] = $toptrending = [];
            if (!empty($topTrendingImages)) {
                foreach ($topTrendingImages as $key => $val) {
                    if ($val->ci_image != '' && file_exists($this->humanThumbImageUploadPath . $val->ci_image)) {
                        $topImages['image'] = asset($this->humanThumbImageUploadPath . $val->ci_image);
                    } else {
                        $topImages['image'] = asset($this->humanThumbImageUploadPath . 'proteen-logo.png');
                    }

                    if ($val->ci_image != '' && file_exists($this->humanOriginalImageUploadPath . $val->ci_image)) {
                        $topImages['imageOriginal'] = asset($this->humanOriginalImageUploadPath . $val->ci_image);
                    } else {
                        $topImages['imageOriginal'] = asset($this->humanOriginalImageUploadPath . 'proteen-logo.png');
                    }

                    $topImages['name'] = $val->ci_name;
                    $topImages['category'] = $val->cic_name;
                    $toptrending[] = $topImages;
                }
            }
            $mainArray['topTrendingImages'] = $toptrending;

            $cartoonIconDetail = $this->level1ActivitiesRepository->getLevel1FictionCartoon();
            $maincartoonIconArray = [];
            foreach ($cartoonIconDetail as $cartoon) {
                $cartooniconList = [];
                $cartooniconList['id'] = $cartoon->id;
                $cartooniconList['name'] = $cartoon->ci_name;
                if ($cartoon->ci_image != '' && file_exists($this->cartoonThumbImageUploadPath . $cartoon->ci_image)) {
                    $cartooniconList['image'] = asset($this->cartoonThumbImageUploadPath . $cartoon->ci_image);
                } else {
                    $cartooniconList['image'] = asset($this->cartoonThumbImageUploadPath . 'proteen-logo.png');
                }

                if ($cartoon->ci_image != '' && file_exists($this->cartoonOriginalImageUploadPath . $cartoon->ci_image)) {
                    $cartooniconList['imageOriginal'] = asset($this->cartoonOriginalImageUploadPath . $cartoon->ci_image);
                } else {
                    $cartooniconList['imageOriginal'] = asset($this->cartoonOriginalImageUploadPath . 'proteen-logo.png');
                }

                $cartooniconList['categoryID'] = $cartoon->ci_category;
                $maincartoonIconArray[] = $cartooniconList;
            }
            $mainArray['fictional']['Characters'] = $maincartoonIconArray;
            
            $cartoonIconCategory = $this->level1ActivitiesRepository->getLevel1FictionCartoonCategory();
                $maincartoonIconCategoryArray = [];
                foreach ($cartoonIconCategory as $cartooncategory) {
                    $cartooniconCategoryList = [];
                    $cartooniconCategoryList['id'] = $cartooncategory->id;
                    $cartooniconCategoryList['name'] = $cartooncategory->cic_name;
                    $maincartoonIconCategoryArray[] = $cartooniconCategoryList;
                }
                $mainArray['fictional']['CategoryList'] = $maincartoonIconCategoryArray;

                $humanIconDetail = $this->level1ActivitiesRepository->getLevel1NonFictionhuman();
                $mainhumanIconArray = [];
                foreach ($humanIconDetail as $human) {
                    $humaniconList = [];
                    $humaniconList['id'] = $human->id;
                    $humaniconList['name'] = $human->hi_name;
                    if ($human->hi_image != '' && file_exists($this->humanThumbImageUploadPath . $human->hi_image)) {
                        $humaniconList['image'] = asset($this->humanThumbImageUploadPath . $human->hi_image);
                    } else {
                        $humaniconList['image'] = asset($this->humanThumbImageUploadPath . 'proteen-logo.png');
                    }

                    if ($human->hi_image != '' && file_exists($this->humanOriginalImageUploadPath . $human->hi_image)) {
                        $humaniconList['imageOriginal'] = asset($this->humanOriginalImageUploadPath . $human->hi_image);
                    } else {
                        $humaniconList['imageOriginal'] = asset($this->humanOriginalImageUploadPath . 'proteen-logo.png');
                    }

                    $humaniconList['categoryID'] = $human->hi_category;
                    $mainhumanIconArray[] = $humaniconList;
                }
                $mainArray['nonfictional']['Characters'] = $mainhumanIconArray;

                $humanIconCategory = $this->level1ActivitiesRepository->getLevel1NonFictionHumanCategory();
                $mainhumanIconCategoryArray = [];
                foreach ($humanIconCategory as $humancategory) {
                    $humaniconCategoryList = [];
                    $humaniconCategoryList['id'] = $humancategory->id;
                    $humaniconCategoryList['name'] = $humancategory->hic_name;
                    $mainhumanIconCategoryArray[] = $humaniconCategoryList;
                }
                $mainArray['nonfictional']['CategoryList'] = $mainhumanIconCategoryArray;

                $relationDetail = $this->level1ActivitiesRepository->getLevel1Relation();
                $mainrelationArray = [];
                foreach ($relationDetail as $detail) {
                    $relationList = [];
                    $relationList['id'] = $detail->id;
                    $relationList['name'] = $detail->rel_name;
                    $mainrelationArray[] = $relationList;
                }
                $mainArray['relations']['CategoryList'] = $mainrelationArray;

            $response['status'] = 1;
            $response['login'] = 1;
            $response['message'] = trans('appmessages.default_success_msg');
            $response['data'] = $mainArray;
        } else {
            $this->log->error('Parameter missing error' , array('api-name'=> 'getLevel1Part2Options'));
            $response['message'] = trans('appmessages.missing_data_msg');
        }
        return response()->json($response, 200);
        exit;
    }
}