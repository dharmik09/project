<?php

namespace App\Http\Controllers\Webservice;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Services\Level1Activity\Contracts\Level1ActivitiesRepository;
use App\Services\FileStorage\Contracts\FileStorageRepository;
use App\Services\Level1CartoonIcon\Contracts\Level1CartoonIconRepository;
use App\Services\Level1HumanIcon\Contracts\Level1HumanIconRepository;
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
use App\Level1Traits;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class Level1ActivityController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Level1HumanIconRepository $level1HumanIconRepository, Level1CartoonIconRepository $level1CartoonIconRepository, FileStorageRepository $fileStorageRepository, Level1ActivitiesRepository $level1ActivitiesRepository, TeenagersRepository $teenagersRepository)
    {
        $this->level1ActivitiesRepository = $level1ActivitiesRepository;
        $this->level1CartoonIconRepository = $level1CartoonIconRepository;
        $this->level1HumanIconRepository = $level1HumanIconRepository;
        $this->fileStorageRepository = $fileStorageRepository;
        $this->objLevel1Activity = new Level1Activity;
        $this->objTraits = new Level1Traits;
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
        $this->teenOriginalImageUploadPath = Config::get('constant.TEEN_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->teenThumbImageUploadPath = Config::get('constant.TEEN_THUMB_IMAGE_UPLOAD_PATH');
        $this->teenProfileImageUploadPath = Config::get('constant.TEEN_PROFILE_IMAGE_UPLOAD_PATH');
        $this->teenThumbImageHeight = Config::get('constant.TEEN_THUMB_IMAGE_HEIGHT');
        $this->teenThumbImageWidth = Config::get('constant.TEEN_THUMB_IMAGE_WIDTH');
        $this->relationIconThumbWidth = Config::get('constant.RELATION_THUMB_IMAGE_WIDTH');
        $this->relationIconThumbHeight = Config::get('constant.RELATION_THUMB_IMAGE_HEIGHT');
        
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

                $cartooniconList['categoryId'] = $cartoon->ci_category;
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

                    $humaniconList['categoryId'] = $human->hi_category;
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

    /* Request Params : getLevel1Part2Category
    *  loginToken, userId, categoryType
    */
    public function getLevel1Part2Category(Request $request) {
        $response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg')];
        $teenager = $this->teenagersRepository->getTeenagerById($request->userId);
        if($request->userId != "" && $teenager) {
            $mainArray = [];
                $type = ($request->categoryType != "") ? $request->categoryType : '0';
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
                $topTrendingImages = $this->level1ActivitiesRepository->getAllTopTrendingImages($type);
                $topImages['image'] = $toptrending = [];
                if (!empty($topTrendingImages)) {
                    foreach ($topTrendingImages as $key => $val) {
                        if ($type == 2) {
                            $topImages['image'] = ($val->ci_image != "") ? Storage::url($this->humanThumbImageUploadPath . $val->ci_image) : Storage::url($this->humanThumbImageUploadPath . 'proteen-logo.png');
                            $topImages['imageOriginal'] = ($val->ci_image != "") ? Storage::url($this->humanOriginalImageUploadPath . $val->ci_image) : Storage::url($this->humanOriginalImageUploadPath . 'proteen-logo.png'); 
                        } else if($type == 1) {
                            $topImages['image'] = ($val->ci_image != "") ? Storage::url($this->cartoonThumbImageUploadPath . $val->ci_image) : Storage::url($this->cartoonThumbImageUploadPath . 'proteen-logo.png');
                            $topImages['image'] = ($val->ci_image != "") ? Storage::url($this->cartoonOriginalImageUploadPath . $val->ci_image) : Storage::url($this->cartoonOriginalImageUploadPath . 'proteen-logo.png');
                        }
                        $topImages['name'] = $val->ci_name;
                        $topImages['category'] = $val->ci_name;
                        $topImages['volts'] = $val->timesused;
                        $topImages['rank'] = $key+1;
                        $toptrending[] = $topImages;
                    }
                }

                $mainArray['topTrendingImages'] = $toptrending;
                $page = 0;
                if ($type == 1) {
                    $cartoonIconCategory = $this->level1ActivitiesRepository->getLevel1FictionCartoonCategory();
                    $maincartoonIconCategoryArray = [];
                    foreach ($cartoonIconCategory as $cartooncategory) {
                        $cartooniconCategoryList = [];
                        $cartooniconCategoryList['id'] = $cartooncategory->id;
                        $cartooniconCategoryList['name'] = $cartooncategory->cic_name;
                        $maincartoonIconCategoryArray[] = $cartooniconCategoryList;
                    }
                    $mainArray['fictional']['CategoryList'] = $maincartoonIconCategoryArray;
                } else if ($type == 2) {
                    $humanIconCategory = $this->level1ActivitiesRepository->getLevel1NonFictionHumanCategory();
                    $mainhumanIconCategoryArray = [];
                    foreach ($humanIconCategory as $humancategory) {
                        $humaniconCategoryList = [];
                        $humaniconCategoryList['id'] = $humancategory->id;
                        $humaniconCategoryList['name'] = $humancategory->hic_name;
                        $mainhumanIconCategoryArray[] = $humaniconCategoryList;
                    }
                    $mainArray['nonfictional']['CategoryList'] = $mainhumanIconCategoryArray;
                } else if ($type == 3) {
                    $relationDetail = $this->level1ActivitiesRepository->getLevel1Relation();
                    $mainrelationArray = [];
                    foreach ($relationDetail as $detail) {
                        $relationList = [];
                        $relationList['id'] = $detail->id;
                        $relationList['name'] = $detail->rel_name;
                        $mainrelationArray[] = $relationList;
                    }
                    $mainArray['relations']['CategoryList'] = $mainrelationArray;
                }

                $getLevel1AttemptedQuality = $this->level1ActivitiesRepository->getLevel1AttemptedQuality($request->userId);

                if (isset($getLevel1AttemptedQuality) && !empty($getLevel1AttemptedQuality)) {
                    $response['qualityAttempted'] = "yes";
                } else {
                    $response['qualityAttempted'] = "no";
                }
                
                $response['status'] = 1;
                $response['page'] = 0;
                $response['login'] = 1;
                $response['message'] = trans('appmessages.default_success_msg');
                $response['data'] = $mainArray;
        } else {
            $response['message'] = trans('appmessages.invalid_userid_msg') . ' or ' . trans('appmessages.notvarified_user_msg');
        }
        return response()->json($response, 200);
        exit;
    }

    /* Request Params : getLevel1Part2IconData
    *  loginToken, userId, categoryType, page, categoryId
    */
    public function getLevel1Part2IconData(Request $request) {
        $response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg')];
        $teenager = $this->teenagersRepository->getTeenagerById($request->userId);
        if($request->userId != "" && $teenager) {
            $mainArray = [];
                $type = ($request->categoryType != "") ? $request->categoryType : '0';
                $page = ($request->page != "") ? $request->page : '0';
                $category_id = ($request->categoryId != "") ? $request->categoryId : '0';
                if ($type == 1) {
                    $cartoonIconDetail = $this->level1ActivitiesRepository->getLevel1FictionCartoonById($page, $category_id);
                    $maincartoonIconArray = [];
                    foreach ($cartoonIconDetail as $cartoon) {
                        $cartooniconList = [];
                        $cartooniconList['id'] = $cartoon->id;
                        $cartooniconList['name'] = $cartoon->ci_name;
                        $cartooniconList['image'] = ($cartoon->ci_image != '') ? Storage::url($this->cartoonThumbImageUploadPath . $cartoon->ci_image) : Storage::url($this->cartoonThumbImageUploadPath . 'proteen-logo.png');
                        $cartooniconList['imageOriginal'] = ($cartoon->ci_image != '') ? Storage::url($this->cartoonOriginalImageUploadPath . $cartoon->ci_image) : Storage::url($this->cartoonOriginalImageUploadPath . 'proteen-logo.png');
                        $cartooniconList['categoryId'] = $cartoon->ci_category;
                        $maincartoonIconArray[] = $cartooniconList;
                    }
                    $mainArray['fictional']['Characters'] = $maincartoonIconArray;

                } else if ($type == 2) {

                    $humanIconDetail = $this->level1ActivitiesRepository->getLevel1NonFictionhumanById($page,$category_id);
                    $mainhumanIconArray = [];
                    foreach ($humanIconDetail as $human) {
                        $humaniconList = [];
                        $humaniconList['id'] = $human->id;
                        $humaniconList['name'] = $human->hi_name;
                        $humaniconList['image'] = ($human->hi_image != '') ? Storage::url($this->humanThumbImageUploadPath . $human->hi_image) : Storage::url($this->humanThumbImageUploadPath . 'proteen-logo.png');
                        $humaniconList['imageOriginal'] = ($human->hi_image != '') ? Storage::url($this->humanOriginalImageUploadPath . $human->hi_image) : Storage::url($this->humanOriginalImageUploadPath . 'proteen-logo.png');
                        $humaniconList['categoryId'] = $human->hi_category;
                        $mainhumanIconArray[] = $humaniconList;
                    }
                    $mainArray['nonfictional']['Characters'] = $mainhumanIconArray;
                }
                $response['status'] = 1;
                $response['login'] = 1;
                $response['message'] = trans('appmessages.default_success_msg');
                $response['data'] = $mainArray;
        } else {
            $response['message'] = trans('appmessages.invalid_userid_msg') . ' or ' . trans('appmessages.notvarified_user_msg');
        }
        return response()->json($response, 200);
        exit;
    }

    /* Request Params : getSearchLevel1Part2IconData
    *  loginToken, userId, categoryType, page, categoryId, searchIcon
    */
    public function getSearchLevel1Part2IconData(Request $request) {
        $response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg')];
        $teenager = $this->teenagersRepository->getTeenagerById($request->userId);
        if($request->userId != "" && $teenager) {
            $mainArray = [];
                $type = ($request->categoryType != "") ? $request->categoryType : '0';
                $page = ($request->page != "") ? $request->page : '0';
                $category_id = ($request->categoryId != "") ? $request->categoryId : '0';
                $search = ($request->searchIcon != "") ? $request->searchIcon : '0';
                if ($type == 1) {
                    $cartoonIconDetail = $this->level1ActivitiesRepository->getLevel1FictionCartoonByIdForSearch($page,$category_id,$search);
                    $maincartoonIconArray = [];
                    foreach ($cartoonIconDetail as $cartoon) {
                        $cartooniconList = [];
                        $cartooniconList['id'] = $cartoon->id;
                        $cartooniconList['name'] = $cartoon->ci_name;
                        $cartooniconList['image'] = ($cartoon->ci_image != "") ? Storage::url($this->cartoonThumbImageUploadPath . $cartoon->ci_image) : Storage::url($this->cartoonThumbImageUploadPath . 'proteen-logo.png');
                        $cartooniconList['imageOriginal'] = ($cartoon->ci_image != "") ? Storage::url($this->cartoonOriginalImageUploadPath . $cartoon->ci_image) : Storage::url($this->cartoonOriginalImageUploadPath . 'proteen-logo.png');
                        $cartooniconList['categoryId'] = $cartoon->ci_category;
                        $maincartoonIconArray[] = $cartooniconList;
                    }
                    $mainArray['fictional']['Characters'] = $maincartoonIconArray;

                } else if ($type == 2) {

                    $humanIconDetail = $this->level1ActivitiesRepository->getLevel1NonFictionhumanByIdForSearch($page,$category_id,$search);
                    $mainhumanIconArray = [];
                    foreach ($humanIconDetail as $human) {
                        $humaniconList = [];
                        $humaniconList['id'] = $human->id;
                        $humaniconList['name'] = $human->hi_name;
                        $humaniconList['image'] = ($human->hi_image != "") ? Storage::url($this->humanThumbImageUploadPath . $human->hi_image) : Storage::url($this->humanThumbImageUploadPath . 'proteen-logo.png');
                        $humaniconList['imageOriginal'] = ($human->hi_image != "") ? Storage::url($this->humanOriginalImageUploadPath . $human->hi_image) : Storage::url($this->humanOriginalImageUploadPath . 'proteen-logo.png');
                        $humaniconList['categoryId'] = $human->hi_category;
                        $mainhumanIconArray[] = $humaniconList;
                    }
                    $mainArray['nonfictional']['Characters'] = $mainhumanIconArray;
                }
                $response['status'] = 1;
                $response['login'] = 1;
                $response['message'] = trans('appmessages.default_success_msg');
                $response['data'] = $mainArray;
        } else {
            $response['message'] = trans('appmessages.invalid_userid_msg') . ' or ' . trans('appmessages.notvarified_user_msg');
        }
        return response()->json($response, 200);
        exit;
    }

    /* Request Params : submitSelfIcon
    *  loginToken, userId, nickname // lastname, name, selfIconType, selfIconId, profilePic
    */
    public function submitSelfIcon(Request $request) {
        $response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg')];
        $teenager = $this->teenagersRepository->getTeenagerById($request->userId);
        if($request->userId != "" && $teenager) {
            $teenagerID = $request->userId;
            //For self data
            $lastInterId = '';
            $teenagerDetail['t_nickname'] = '';
            $teenagerDetail['t_lastname'] = '';
            $teenagerDetail['t_name'] = '';
            $self_user_image_url = '';
            $teenagerData = $this->teenagersRepository->getTeenagerById($request->userId);
            if ($request->selfIconType != '' && $request->selfIconId != '') {
                $teenagerDetail['t_nickname'] = ($request->nickname != '') ? $request->nickname : $teenagerData->t_nickname;
                $teenagerDetail['t_lastname'] = ($request->lastname != '') ? $request->lastname : $teenagerData->t_lastname;
                $teenagerDetail['t_name'] = ($request->name != '') ? $request->name : $teenagerData->t_name;
                $fileName = '';
                if (Input::file()) {
                    $file = Input::file('profilePic');
                    if (!empty($file)) {
                        $fileName = 'teenager_' . time() . '.' . $file->getClientOriginalExtension();
                        $pathOriginal = public_path($this->teenOriginalImageUploadPath . $fileName);
                        $pathThumb = public_path($this->teenThumbImageUploadPath . $fileName);
                        $pathProfile = public_path($this->teenProfileImageUploadPath . $fileName);
                        Image::make($file->getRealPath())->save($pathOriginal);
                        Image::make($file->getRealPath())->resize($this->teenThumbImageWidth, $this->teenThumbImageHeight)->save($pathThumb);
                        Image::make($file->getRealPath())->resize(200, 200)->save($pathProfile);
                        //Uploading on AWS
                        $originalImage = $this->fileStorageRepository->addFileToStorage($fileName, $this->teenOriginalImageUploadPath, $pathOriginal, "s3");
                        $thumbImage = $this->fileStorageRepository->addFileToStorage($fileName, $this->teenThumbImageUploadPath, $pathThumb, "s3");
                        $profileImage = $this->fileStorageRepository->addFileToStorage($fileName, $this->teenProfileImageUploadPath, $pathProfile, "s3");
                        //Deleting Local Files
                        \File::delete($this->teenOriginalImageUploadPath . $fileName);
                        \File::delete($this->teenThumbImageUploadPath . $fileName);
                        \File::delete($this->teenProfileImageUploadPath . $fileName);
                        if($teenagerData->t_photo != "") {
                            $originalImageDelete = $this->fileStorageRepository->deleteFileToStorage($teenagerData->t_photo, $this->teenOriginalImageUploadPath, "s3");
                            $thumbImageDelete = $this->fileStorageRepository->deleteFileToStorage($teenagerData->t_photo, $this->teenThumbImageUploadPath, "s3");
                            $profileImageDelete = $this->fileStorageRepository->deleteFileToStorage($teenagerData->t_photo, $this->teenProfileImageUploadPath, "s3");
                        }
                        $teenagerDetail['t_photo'] = $fileName;
                        $self_user_image_url = asset($this->teenOriginalImageUploadPath . $fileName);
                    }
                } else {
                    $self_user_image_url = ($teenagerData->t_photo != '') ? Storage::url($this->teenOriginalImageUploadPath . $teenagerData->t_photo) : Storage::url($this->teenOriginalImageUploadPath . 'proteen-logo.png');
                }

                $teenagerDetailSaved = $this->teenagersRepository->updateTeenagerImageAndNickname($teenagerID, $teenagerDetail);
                $teenIconSelection[] = array("ti_teenager" => $teenagerID, "ti_icon_type" => $request->selfIconType, "ti_icon_id" => $request->selfIconId);

                foreach ($teenIconSelection as $key => $val) {
                    $lastInterId = $this->level1ActivitiesRepository->saveTeenagerLevel1Part2($val);
                }
            }

            if ($teenagerDetail['t_name'] == '') {
                $teenagerDetail['t_name'] = $teenagerData->t_name;
                $teenagerDetail['t_lastname'] = $teenagerData->t_lastname;
            }
            $response['status'] = 1;
            $response['login'] = 1;
            $response['message'] = trans('appmessages.default_success_msg');
            $response['data'] = array('iconDataID' => $lastInterId, 'user_self_image_url' => $self_user_image_url, 'teen_name' => $teenagerDetail['t_name'], 'teen_lastname' => $teenagerDetail['t_lastname']);
        } else {
            $response['message'] = trans('appmessages.invalid_userid_msg') . ' or ' . trans('appmessages.notvarified_user_msg');
        }
        return response()->json($response, 200);
        exit;
    }

    /* Request Params : submitRelationIcon
    *  loginToken, userId, relationName, relationIconType, relationIconId, relationId, relativeImage
    */
    public function submitRelationIcon(Request $request) {
        $response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg')];
        $teenager = $this->teenagersRepository->getTeenagerById($request->userId);
        if($request->userId != "" && $teenager) {
            $teenagerID = $request->userId;
            $image_url = '';
            $relation_name  = '';
            //For Relation data
            $lastInterId = '';
            if ($request->relationIconType != '' && $request->relationIconId != '' && $request->relationId != '') {
                $fileName = '';
                $relation_name = $request->relationName;
                if (Input::file('relativeImage')) {
                    $file = Input::file('relativeImage');
                    if (!empty($file)) {
                        $fileName = 'relation_' . time() . '.' . $file->getClientOriginalExtension();
                        $pathOriginal = public_path($this->relationIconOriginalImageUploadPath . $fileName);
                        $pathThumb = public_path($this->relationIconThumbImageUploadPath . $fileName);
                        
                        $image_url = asset($this->relationIconOriginalImageUploadPath . $fileName);;
                        Image::make($file->getRealPath())->save($pathOriginal);
                        Image::make($file->getRealPath())->resize($this->relationIconThumbWidth, $this->relationIconThumbHeight)->save($pathThumb);
                        //Uploading on AWS
                        $originalImage = $this->fileStorageRepository->addFileToStorage($fileName, $this->relationIconOriginalImageUploadPath, $pathOriginal, "s3");
                        $thumbImage = $this->fileStorageRepository->addFileToStorage($fileName, $this->relationIconThumbImageUploadPath, $pathThumb, "s3");
                        \File::delete($this->relationIconOriginalImageUploadPath . $fileName);
                        \File::delete($this->relationIconThumbImageUploadPath . $fileName);
                    }
                    $teenIconSelection[] = array("ti_teenager" => $teenagerID, "ti_icon_type" => $request->relationIconType, "ti_icon_id" => $request->relationIconId, 'ti_icon_name' => $request->relationName, 'ti_icon_image' => $fileName, 'ti_icon_relation' => $request->relationId);
                } elseif ($request->relationName != '' && $request->relationId != '') {
                    $teenIconSelection[] = array("ti_teenager" => $teenagerID, "ti_icon_type" => $request->relationIconType, "ti_icon_id" => $request->relationIconId, 'ti_icon_name' => $request->relationName, 'ti_icon_image' => $fileName, 'ti_icon_relation' => $request->relationId);
                } else {
                    $teenIconSelection[] = array("ti_teenager" => $teenagerID, "ti_icon_type" => $request->relationIconType, "ti_icon_id" => $request->relationIconId, 'ti_icon_name' => '', 'ti_icon_image' => $fileName, 'ti_icon_relation' => '');
                }
                foreach ($teenIconSelection as $key => $val) {
                    $lastInterId = $this->level1ActivitiesRepository->saveTeenagerLevel1Part2($val);
                }
            }
            $response['login'] = 1;
            $response['status'] = 1;
            $response['message'] = trans('appmessages.default_success_msg');
            $response['data'] = array('iconDataID' => $lastInterId, 'image_url' => $image_url, 'relation_name' => $relation_name);
        } else {
            $response['message'] = trans('appmessages.invalid_userid_msg') . ' or ' . trans('appmessages.notvarified_user_msg');
        }
        return response()->json($response, 200);
        exit;
    }

    /* Request Params : submitLevel1Part2QualitiesData
    *  loginToken, userId, qualitiesId, categoryId, categoryType, 
    */
    public function submitLevel1Part2QualitiesData(Request $request) {
        $response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg')];
        $teenager = $this->teenagersRepository->getTeenagerById($request->userId);
        if($request->userId != "" && $teenager) {
            $teenagerID = $request->userId;
            $qualityData = $request->qualitiesId;
            $qualities = explode(",", $qualityData);
            $category_id = ($request->categoryId) ? $request->categoryId : '';
            $category_type = ($request->categoryType) ? $request->categoryType : ''; 
            $icon = [];
            if ($category_type != '' && count($qualities) >= 5) {
                foreach ($qualities AS $key => $value) {
                    $icon[$value] = 1;
                }
                if ($category_type == 1 || $category_type == 2) {
                    $teenIconSelection[] = array("ti_teenager" => $teenagerID, "ti_icon_type" => $category_type, "ti_icon_id" => $category_id);
                    $lastInterId = $this->level1ActivitiesRepository->saveTeenagerLevel1Part2($teenIconSelection[0]);
                }
                $qualityDetail = $this->level1ActivitiesRepository->getLevel1qualities();
                $iconCountArray = array();
                foreach ($qualityDetail as $key => $data) {
                    $iconQualityValue = (isset($icon[$data->id]) && isset($icon[$data->id]) == 1) ? 1 : 0;

                    if ($iconQualityValue == 1) {
                        if ($category_type == 1 || $category_type == 2) {
                            $qualityResponseData = array("tiqa_teenager" => $teenagerID, "tiqa_ti_id" => $lastInterId, "tiqa_quality_id" => $data->id, "tiqa_response" => $iconQualityValue);
                        } else if ($category_type == 3) {
                            $qualityResponseData = array("tiqa_teenager" => $teenagerID, "tiqa_ti_id" => $category_id, "tiqa_quality_id" => $data->id, "tiqa_response" => $iconQualityValue);
                        } else if ($category_type == 4) {
                            $qualityResponseData = array("tiqa_teenager" => $teenagerID, "tiqa_ti_id" => $category_id, "tiqa_quality_id" => $data->id, "tiqa_response" => $iconQualityValue);
                        }
                        $this->level1ActivitiesRepository->saveTeenagerLevel1Part2Qualities($qualityResponseData);
                        $iconCountArray[] = $category_type;
                    }
                }

                if(isset($iconCountArray) && !empty($iconCountArray)){
                    $iconCount = count(array_unique($iconCountArray));
                }

                $category = [1,2,3,4];
                $UserData = $this->level1ActivitiesRepository->getTeenagerLevel1Part2Icon($teenagerID,$category);
                $iconLength = 0;
                if(isset($UserData) && !empty($UserData)){
                    $UserData = json_decode(json_encode($UserData), true);
                    $UserData = array_unique(array_column($UserData, 'ti_icon_type'));
                    $iconLength = count($UserData);
                }
                if ($iconLength >= 4) {
                    $response['qualityAttempted'] = 'yes';
                } else {
                    $response['qualityAttempted'] = 'no';
                }

                $message = Helpers::sendMilestoneNotification(2000);
                $response['displayMsg'] = $message;
                $response['status'] = 1;
                $response['login'] = 1;
                $response['message'] = trans('appmessages.default_success_msg');
            } else {
                $response['login'] = 1;
                $response['message'] = "Please, select at-least 5 qualities!";
            }
        } else {
            $response['message'] = trans('appmessages.invalid_userid_msg') . ' or ' . trans('appmessages.notvarified_user_msg');
        }
        return response()->json($response, 200);
        exit;
    }

    /* Request Params : addIcon
    *  loginToken, userId, categoryType, categoryId, characterName, image, 
    */
    public function addIcon(Request $request) {
        $response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg')];
        $teenager = $this->teenagersRepository->getTeenagerById($request->userId);
        if($request->userId != "" && $teenager) {
            $fileName = $imagePath = '';
            $cartoonIconDetail['ci_image'] = '';
            $categoryId = $request->categoryId;
            if (Input::file()) {
                $file = Input::file('image');
                if (!empty($file)) {
                    if ($request->categoryType == 1) {
                        $fileName = 'cartoon_' . time() . '.' . $file->getClientOriginalExtension();
                        $pathOriginal = public_path($this->cartoonOriginalImageUploadPath . $fileName);
                        $pathThumb = public_path($this->cartoonThumbImageUploadPath . $fileName);
                        Image::make($file->getRealPath())->save($pathOriginal);
                        Image::make($file->getRealPath())->resize($this->cartoonThumbImageWidth, $this->cartoonThumbImageHeight)->save($pathThumb);
                        //Uploading on AWS
                        $originalImage = $this->fileStorageRepository->addFileToStorage($fileName, $this->cartoonOriginalImageUploadPath, $pathOriginal, "s3");
                        $thumbImage = $this->fileStorageRepository->addFileToStorage($fileName, $this->cartoonThumbImageUploadPath, $pathThumb, "s3");
                        \File::delete($this->cartoonOriginalImageUploadPath . $fileName);
                        \File::delete($this->cartoonThumbImageUploadPath . $fileName);
                        $imagePath = Storage::url($this->cartoonThumbImageUploadPath . $fileName);
                    } else {
                        $fileName = 'human_' . time() . '.' . $file->getClientOriginalExtension();
                        $pathOriginal = public_path($this->humanOriginalImageUploadPath . $fileName);
                        $pathThumb = public_path($this->humanThumbImageUploadPath . $fileName);
                        Image::make($file->getRealPath())->save($pathOriginal);
                        Image::make($file->getRealPath())->resize($this->humanThumbImageWidth, $this->humanThumbImageHeight)->save($pathThumb);
                        //Uploading on AWS
                        $originalImage = $this->fileStorageRepository->addFileToStorage($fileName, $this->humanOriginalImageUploadPath, $pathOriginal, "s3");
                        $thumbImage = $this->fileStorageRepository->addFileToStorage($fileName, $this->humanThumbImageUploadPath, $pathThumb, "s3");
                        \File::delete($this->humanOriginalImageUploadPath . $fileName);
                        \File::delete($this->humanThumbImageUploadPath . $fileName);
                        $imagePath = Storage::url($this->humanThumbImageUploadPath . $fileName);
                    }
                }
            }
            $iconDetail = $iconNameDetail = [];
            $iconNameDetail['deleted'] = 1;
            $saveCartoonIconCategory = $categoryId;
            if (isset($saveCartoonIconCategory) && $saveCartoonIconCategory != null && $saveCartoonIconCategory != 0) {
                $iconDetail['deleted'] = 1;
                if ($request->categoryType == 1) {
                    $iconDetail['ci_name'] = $request->characterName;
                    $iconDetail['ci_category'] = $saveCartoonIconCategory;
                    $iconDetail['ci_image'] = $fileName;
                    $iconDetail['ci_added_by'] = $request->userId;
                    $categoryName = $this->level1CartoonIconRepository->getCartoonCategoryNameFromId($saveCartoonIconCategory);
                    $saveCartoonIcons = $this->level1CartoonIconRepository->saveLevel1CartoonIconDetail($iconDetail, $professions = null);
                } else {
                    $iconDetail['hi_name'] = $request->characterName;
                    $iconDetail['hi_category'] = $saveCartoonIconCategory;
                    $iconDetail['hi_image'] = $fileName;
                    $iconDetail['hi_added_by'] = $request->userId;
                    $categoryName = $this->level1HumanIconRepository->getHumanCategoryNameFromId($saveCartoonIconCategory);
                    $saveCartoonIcons = $this->level1HumanIconRepository->saveLevel1HumanIconDetail($iconDetail, $professions = null);
                }

                if ($saveCartoonIcons) {
                    $response['status'] = 1;
                    $response['login'] = 1;
                    $response['message'] = trans('appmessages.default_success_msg');
                    $response['characterid'] = $saveCartoonIcons->id;
                    $response['categoryid'] = $saveCartoonIconCategory;
                    $response['charactername'] = $request->characterName;
                    $response['categoryname'] = $categoryName;
                    $response['image'] = $imagePath;
                } else {
                    $response['status'] = 0;
                    $response['login'] = 1;
                    $response['message'] = "Something went wrong!";
                }
            } else {
                $response['status'] = 0;
                $response['login'] = 1;
                $response['message'] = "Something went wrong!";
            }
        } else {
            $response['message'] = trans('appmessages.invalid_userid_msg') . ' or ' . trans('appmessages.notvarified_user_msg');
        }
        return response()->json($response, 200);
        exit;
    }

    /* Request Params : submitLevel1Part2Icon
    *  loginToken, userId, fictionIconId, fictionIconType, nonfictionIconType, nonfictionIconId, relationIconType, relationIconId, relationName, relationId, selfIconType, selfIconId, nickname, lastname, relativeImage, profilePic
    */
    public function submitLevel1Part2Icon(Request $request) {
        $response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg')];
        $teenager = $this->teenagersRepository->getTeenagerById($request->userId);
        if($request->userId != "" && $teenager) {
            $teenagerID = $request->userId;
            //For Fiction data
            if ($request->fictionIconType != '' && $request->fictionIconId != '') {
                $teenIconSelection[] = array("ti_teenager" => $teenagerID, "ti_icon_type" => $request->fictionIconType, "ti_icon_id" => $request->fictionIconId);
            }

            //For Non-Fiction data
            if ($request->nonfictionIconType != '' && $request->nonfictionIconId != '') {
                $teenIconSelection[] = array("ti_teenager" => $teenagerID, "ti_icon_type" => $request->nonfictionIconType, "ti_icon_id" => $request->nonfictionIconId);
            }

            //For Relation data
            if ($request->relationIconType != '' && $request->relationIconId != '') {
                $fileName = '';
                if (Input::file('relativeImage')) {
                    $file = Input::file('relativeImage');
                    if (!empty($file)) {
                        $fileName = 'relation_' . time() . '.' . $file->getClientOriginalExtension();
                        $pathOriginal = public_path($this->relationIconOriginalImageUploadPath . $fileName);
                        $pathThumb = public_path($this->relationIconThumbImageUploadPath . $fileName);
                        Image::make($file->getRealPath())->save($pathOriginal);
                        Image::make($file->getRealPath())->resize($this->relationIconThumbWidth, $this->relationIconThumbHeight)->save($pathThumb);
                        //Upload on AWS
                        $originalImage = $this->fileStorageRepository->addFileToStorage($fileName, $this->relationIconOriginalImageUploadPath, $pathOriginal, "s3");
                        $thumbImage = $this->fileStorageRepository->addFileToStorage($fileName, $this->relationIconThumbImageUploadPath, $pathThumb, "s3");
                        //Delete local files
                        \File::delete($this->relationIconOriginalImageUploadPath . $fileName);
                        \File::delete($this->relationIconThumbImageUploadPath . $fileName);
                    }
                    $teenIconSelection[] = array("ti_teenager" => $teenagerID, "ti_icon_type" => $request->relationIconType, "ti_icon_id" => $request->relationIconId, 'ti_icon_name' => $request->relationName, 'ti_icon_image' => $fileName, 'ti_icon_relation' => $request->relationId);
                } elseif ($request->relationName != '' && $request->relationId != '') {
                    $teenIconSelection[] = array("ti_teenager" => $teenagerID, "ti_icon_type" => $request->relationIconType, "ti_icon_id" => $request->relationIconId, 'ti_icon_name' => $request->relationName, 'ti_icon_image' => $fileName, 'ti_icon_relation' => $request->relationId);
                } else {
                    $teenIconSelection[] = array("ti_teenager" => $teenagerID, "ti_icon_type" => $request->relationIconType, "ti_icon_id" => $request->relationIconId, 'ti_icon_name' => '', 'ti_icon_image' => $fileName, 'ti_icon_relation' => '');
                }
            }

            //For self data
            $teenagerDetail['t_nickname'] = '';
            if ($request->selfIconType != '' && $request->selfIconId != '') {
                $teenagerDetail['t_nickname'] = ($request->nickname != "") ? $request->nickname : "";
                $teenagerDetail['t_lastname'] = ($request->lastname != "") ? $request->lastname : "";
                $fileName = '';
                $self_user_image_url = '';
                if (Input::file()) {
                    $file = Input::file('profilePic');
                    if (!empty($file)) {
                        $fileName = 'teenager_' . time() . '.' . $file->getClientOriginalExtension();
                        $pathOriginal = public_path($this->teenOriginalImageUploadPath . $fileName);
                        $pathThumb = public_path($this->teenThumbImageUploadPath . $fileName);

                        Image::make($file->getRealPath())->save($pathOriginal);
                        Image::make($file->getRealPath())->resize($this->teenThumbImageWidth, $this->teenThumbImageHeight)->save($pathThumb);
                        //Upload on AWS
                        $originalImage = $this->fileStorageRepository->addFileToStorage($fileName, $this->teenOriginalImageUploadPath, $pathOriginal, "s3");
                        $thumbImage = $this->fileStorageRepository->addFileToStorage($fileName, $this->teenThumbImageUploadPath, $pathThumb, "s3");
                        //Delete local files
                        \File::delete($this->teenOriginalImageUploadPath . $fileName);
                        \File::delete($this->teenThumbImageUploadPath . $fileName);
                        
                        $teenagerDetail['t_photo'] = $fileName;
                        $self_user_image_url = Storage::url($this->teenOriginalImageUploadPath . $fileName);
                    }
                }
                $teenagerDetailSaved = $this->teenagersRepository->updateTeenagerImageAndNickname($teenagerID, $teenagerDetail);
                $teenIconSelection[] = array("ti_teenager" => $teenagerID, "ti_icon_type" => $request->selfIconType, "ti_icon_id" => $request->selfIconId);
            }

            foreach ($teenIconSelection as $key => $val) {
                $lastInterId[] = $this->Level1ActivitiesRepository->saveTeenagerLevel1Part2($val);
            }
            
            $response['status'] = 1;
            $response['login'] = 1;
            $response['message'] = trans('appmessages.default_success_msg');
            $response['data'] = array('iconDataID' => $lastInterId, 'user_self_image_url' => $self_user_image_url);
        } else {
            $response['message'] = trans('appmessages.invalid_userid_msg') . ' or ' . trans('appmessages.notvarified_user_msg');
        }
        return response()->json($response, 200);
        exit;
    }

    /* Request Params : submitLevel1Part2Qualities
    *  loginToken, userId, qualitiesResponseData, ['iconDataId', 'response', 'qualityId']
    */
    public function submitLevel1Part2Qualities(Request $request) {
        $response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg')];
        $teenager = $this->teenagersRepository->getTeenagerById($request->userId);
        if($request->userId != "" && $teenager) {
            $teenagerID = $request->userId;
            if (isset($request->qualitiesResponseData) && !empty($request->qualitiesResponseData)) {
                foreach ($request->qualitiesResponseData as $key => $data) {
                    if ($data['response'] == 1) {
                        $qualityResponseData = array("tiqa_teenager" => $teenagerID, "tiqa_ti_id" => $data['iconDataId'], "tiqa_quality_id" => $data['qualityId'], "tiqa_response" => $data['response']);
                        $this->level1ActivitiesRepository->saveTeenagerLevel1Part2Qualities($qualityResponseData);
                    }
                }
            }
            if (isset($request->iconCount) && $request->iconCount > 0) {
                $noOfIconSelected = $request->iconCount;
            } else {
                $noOfIconSelected = 1;
            }
            // Save booster points which user get for Level1Part2
            $this->teenagersRepository->saveLevel1Part2BoosterPoints($request->userId, ($noOfIconSelected * Helpers::getConfigValueByKey('LEVEL1_ICON_SELECTION_POINTS')));
            $message = Helpers::sendMilestoneNotification(2000);
            $response['displayMsg'] = $message;
            $response['status'] = 1;
            $response['login'] = 1;
            $response['message'] = trans('appmessages.default_success_msg');
        } else {
            $response['message'] = trans('appmessages.invalid_userid_msg') . ' or ' . trans('appmessages.notvarified_user_msg');
        }
        return response()->json($response, 200);
        exit;
    }


    public function getLevel1Traits(Request $request) {
        $response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg')];
        $teenager = $this->teenagersRepository->getTeenagerById($request->userId);
        $teenagerToUserID = $this->teenagersRepository->getTeenagerById($request->toUserID);
        $this->log->info('Get teenager detail for userId'.$request->userId , array('api-name'=> 'getLevel1Traits'));
        if($request->userId != "" && $request->toUserID != "" && $teenager && $teenagerToUserID) {
            $toUserID = $request->toUserID;
            $data = $this->level1ActivitiesRepository->getAllNotAttemptedTraits($request->userId,$toUserID);
            $response['status'] = 1;
            $response['login'] = 1;
            $response['message'] = trans('appmessages.default_success_msg');
            $response['data'] = $data;
            $this->log->info('Response for Level 1 Traits' , array('api-name'=> 'getLevel1Traits'));
        } else {
            $this->log->error('Parameter missing error' , array('api-name'=> 'getLevel1Traits'));
            $response['message'] = trans('appmessages.missing_data_msg');
        }
        
        return response()->json($response, 200);
        
    }

    public function saveLevel1Traits(Request $request) {
        $response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg')];
        $teenager = $this->teenagersRepository->getTeenagerById($request->userId);
        $teenagerToUserID = $this->teenagersRepository->getTeenagerById($request->toUserID);
        $this->log->info('Get teenager detail for userId'.$request->userId , array('api-name'=> 'getLevel2Activity'));
        if($request->userId != "" && $request->activityID != "" && $request->optionId != "" && $request->toUserID != "" && $teenager && $teenagerToUserID) {

            $questionID = $request->activityID;
            $toUserID = $request->toUserID;
            $answerArray = explode("," , $request->optionId);

            if (isset($request->userId) && $request->userId > 0 && isset($questionID) && $questionID != 0) {
                $answerType = $this->objTraits->find($questionID)->tqq_is_multi_select;
                if($answerType == 0){
                    if(count($answerArray)>1){
                        $response['message'] = trans('appmessages.onlyoneoptionallowedforthisquestion');
                        return response()->json($response, 200);
                    }
                }
                $questionsArray = '';
                foreach ($answerArray as $key => $value) {
                    $answers = [];
                    $answers['tqq_id'] = $questionID;
                    $answers['tqo_id'] = $value;
                    $answers['tqa_from'] = $request->userId;
                    $answers['tqa_to'] = $toUserID;
                    $questionsArray = $this->level1ActivitiesRepository->saveLevel1TraitsAnswer($answers);
                }
                if($questionsArray){
                    $response['status'] = 1;
                    $response['login'] = 1;
                    $response['message'] = trans('appmessages.default_success_msg');
                } else {
                    $response['message'] = trans('appmessages.default_error_msg');
                }
            } else {
                $response['message'] = trans('appmessages.default_error_msg');
            }
        } else {
            $this->log->error('Parameter missing error' , array('api-name'=> 'getLevel2Activity'));
            $response['message'] = trans('appmessages.missing_data_msg');
        }
        
        return response()->json($response, 200);
    }

}