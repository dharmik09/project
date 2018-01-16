<?php

namespace App\Http\Controllers\Teenager;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Services\Level1Activity\Contracts\Level1ActivitiesRepository;
use Auth;
use Illuminate\Http\Request;
use Config;
use Input;
use Redirect;
use Image;
use Helpers;
use Storage;
use App\Level1Activity;

class Level1ActivityController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Level1ActivitiesRepository $level1ActivitiesRepository)
    {
        $this->level1ActivitiesRepository = $level1ActivitiesRepository;
        $this->objLevel1Activity = new Level1Activity;
        $this->cartoonThumbImageUploadPath = Config::get('constant.CARTOON_THUMB_IMAGE_UPLOAD_PATH');
        $this->cartoonOriginalImageUploadPath = config::get('constant.CARTOON_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->cartoonThumbImageWidth = Config::get('constant.CARTOON_THUMB_IMAGE_WIDTH');
        $this->cartoonThumbImageHeight = Config::get('constant.CARTOON_THUMB_IMAGE_HEIGHT');
        $this->humanThumbImageWidth = Config::get('constant.HUMAN_THUMB_IMAGE_WIDTH');
        $this->humanThumbImageHeight = Config::get('constant.HUMAN_THUMB_IMAGE_HEIGHT');
        $this->humanOriginalImageUploadPath = config::get('constant.HUMAN_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->humanThumbImageUploadPath = Config::get('constant.HUMAN_THUMB_IMAGE_UPLOAD_PATH');
    }

    /*
    * Method : playLevel1Activity
    * Response : Not attempted questions collections
    */
    public function playLevel1Activity(Request $request) {
        $userId = Auth::guard('teenager')->user()->id;
        $level1Activities = $this->level1ActivitiesRepository->getNotAttemptedActivities($userId);
        $totalQuestion = $this->level1ActivitiesRepository->getNoOfTotalQuestionsAttemptedQuestion($userId);
        if($level1Activities && isset($totalQuestion[0]->NoOfTotalQuestions) && $totalQuestion[0]->NoOfTotalQuestions > 0 && $totalQuestion[0]->NoOfAttemptedQuestions < $totalQuestion[0]->NoOfTotalQuestions) {
            return view('teenager.basic.level1Question', compact('level1Activities'));
        } else {
            $isQuestionCompleted = 1;
            return view('teenager.basic.level1ActivityWorldType', compact('qualityDetail', 'isQuestionCompleted'));
        }
    }

    /*
    * Method : playLevel1WorldActivity
    * Response : Not attempted questions collections
    */
    public function playLevel1WorldActivity(Request $request) {
        $userId = Auth::guard('teenager')->user()->id;
        $isQuestionCompleted = 1;
        $type = ($request->type != "") ? $request->type : '0';
        //Get top trending images
        $topTrendingImages = $this->level1ActivitiesRepository->getAllTopTrendingImages($type);
        $topImages['image'] = $toptrending = [];
        if (isset($topTrendingImages[0]) && !empty($topTrendingImages)) {
            foreach ($topTrendingImages as $key => $val) {
                if ($type == 2) {
                    $topImages['image'] = ($val->ci_image != "") ? Storage::url($this->humanThumbImageUploadPath . $val->ci_image) : Storage::url($this->humanThumbImageUploadPath . 'proteen-logo.png');
                } else if($type == 1) {
                    $topImages['image'] = ($val->ci_image != "") ? Storage::url($this->cartoonThumbImageUploadPath . $val->ci_image) : Storage::url($this->cartoonThumbImageUploadPath . 'proteen-logo.png');
                } else {
                    $topImages['image'] = [];
                }
                $topImages['name'] = $val->ci_name;
                $topImages['category'] = $val->cic_name;
                $topImages['votes'] = $val->timesused;
                $topImages['rank'] = $key+1;
                $toptrending[] = $topImages;
            }
        }
        $mainArray['topTrendingImages'] = $toptrending;
        //print_r($mainArray); die();
        if($type == 1) {
            $cartoonIconCategory = $this->level1ActivitiesRepository->getLevel1FictionCartoonCategory();
            //print_r($cartoonIconCategory); die();    
            $maincartoonIconCategoryArray = [];
            if($cartoonIconCategory) {
                foreach ($cartoonIconCategory as $cartooncategory) {
                    $cartooniconCategoryList = [];
                    $cartooniconCategoryList['id'] = $cartooncategory->id;
                    $cartooniconCategoryList['name'] = $cartooncategory->cic_name;
                    $maincartoonIconCategoryArray[] = $cartooniconCategoryList;
                }
            }
            return view('teenager.basic.level1ActivityWorldFiction', compact('isQuestionCompleted', 'mainArray', 'maincartoonIconCategoryArray'));
        } else if($type == 2) {
            return view('teenager.basic.level1ActivityWorldNonFiction', compact('isQuestionCompleted', 'mainArray'));
        } else if($type == 3) {
            return view('teenager.basic.level1ActivityWorldRelation', compact('isQuestionCompleted', 'mainArray'));
        } else {
            return view('teenager.basic.level1ActivityWorldType', compact('isQuestionCompleted'));
        }
        return view('teenager.basic.level1ActivityWorldType', compact('isQuestionCompleted'));
    }

    public function saveFirstLevelActivity(Request $request) {
        $userId = Auth::guard('teenager')->user()->id;
        $questionOption = $this->objLevel1Activity->questionOptions($request->questionId);
        
        if($questionOption->toArray() && isset($questionOption[0]->options) && in_array($request->answerId, array_column($questionOption[0]->options->toArray(), 'id')) ) {
            $answers = [];
            $answers['answerID'] = $request->answerId;
            $answers['questionID'] = $questionOption[0]->id;
            $answers['points'] = $questionOption[0]->l1ac_points;
            $questionsArray = $this->level1ActivitiesRepository->saveTeenagerActivityResponseOneByOne($userId, $answers);
            $response['status'] = 1;
            $response['message'] = trans('appmessages.default_success_msg');
            $response['dataOfLastAttempt'] = $questionsArray;
        } else {
            $response['status'] == 0;
            $response['message'] = trans('appmessages.invalid_userid_msg');
        }
        return response()->json($response, 200);
        exit;
    }

    public function getIconNameNew() {
        $categoryId = Input::get('categoryId');
        $categoryType = Input::get('categoryType');

        $textName = '';
        $iconCategoryName = $data_cat_type = $data_name = $data_car_image = $image_path_location = $imagePath = '';
        $iconCategoryNameArray = array();
        if ($categoryType == "1" && $categoryId != '') {
            $data_cat_type = 1;
            $data_name = "ci_name";
            $data_car_image = "ci_image";
            $image_path_location = $this->cartoonThumbImageUploadPath;
            $iconCategoryName = $this->level1ActivitiesRepository->getIconNameWithPagination($textName, $categoryId, "pro_ci_cartoon_icons");
        } elseif ($categoryType == "2" && $categoryId != '') {
            $iconCategoryName = $this->Level1ActivitiesRepository->getIconNameWithPagination($textName, $categoryId, "pro_hi_human_icons");
            $data_cat_type = 2;
            $data_name = "hi_name";
            $image_path_location = $this->humanThumbImageUploadPath;
            $data_car_image = "hi_image";
        } else {
            $iconCategoryName = '';
        }

        $html = '';
        if (isset($iconCategoryName) && !empty($iconCategoryName) && count($iconCategoryName) > 0) {
            foreach ($iconCategoryName as $value) {
                $value->image = ($value->$data_car_image != '') ? Storage::url($image_path_location . $value->$data_car_image) : Storage::url($image_path_location . "proteen-logo.png");
                $value->name = $value->$data_name;
            }
        }

        return view('teenager.basic.level1ActivityIcon', compact('iconCategoryName','data_cat_type'));
    }

}