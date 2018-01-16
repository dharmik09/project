<?php

namespace App\Http\Controllers\Teenager;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Services\Level1Activity\Contracts\Level1ActivitiesRepository;
use App\Services\FileStorage\Contracts\FileStorageRepository;
use App\Services\Level1CartoonIcon\Contracts\Level1CartoonIconRepository;
use App\Services\Level1HumanIcon\Contracts\Level1HumanIconRepository;
use App\Services\Teenagers\Contracts\TeenagersRepository;
use Auth;
use Illuminate\Http\Request;
use Config;
use Input;
use Redirect;
use Image;
use Helpers;
use Storage;
use App\Level1Activity;
use App\Level1Traits;

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
        $this->objLevel1Activity = new Level1Activity;
        $this->fileStorageRepository = $fileStorageRepository;
        $this->objTraits = new Level1Traits;
        $this->teenagersRepository = $teenagersRepository;
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
    public function getLevel1Trait(){
        $userId = Auth::guard('teenager')->user()->id;
        $toUser = Input::get('toUserId');
        if($toUser == ''){
            $toUserId = $userId;
        }
        else{
            $toUserId = $this->teenagersRepository->getTeenagerByUniqueId($toUser)->id;
        }
        $traitQuestion = $this->level1ActivitiesRepository->getLastNotAttemptedTraits($userId,$toUserId);
        if(count($traitQuestion)>0){
            $return = '<div class="survey-list">
                <div class="qualities-sec">
                    <p>'.$traitQuestion[0]->tqq_text.'</p>
                    <input type="hidden" id="traitQue" value="'.$traitQuestion[0]->activityID.'">
                    <div class="row">';
            foreach ($traitQuestion[0]->options as $key => $value) {
                $return .= '<div class="col-md-4 col-sm-6 col-xs-6">
                                <div class="ck-button">
                                    <label><input type="checkbox" name="traitAns" value="'.$value['optionId'].'"><span>'.$value['optionText'].'</span></label>
                                </div>
                            </div>';
            }
            $return .= '</div>
                </div>
                <div class="form-btn">
                    <span class="icon"><i class="icon-arrow-spring"></i></span>
                    <a onclick="saveLevel1TraitQuestion();" title="Next">Next</a>
                </div>
            </div>';
        }
        else{
            $return = '<h3>'.trans('labels.traitscompletionmessage').'</h3>';
        }
        return $return;
    }

    public function saveLevel1Trait(){
        $userId = Auth::guard('teenager')->user()->id;
        $questionID = Input::get('questionID');
        $answerArray = Input::get('answerID');
        $toUser = Input::get('toUserId');
        if($toUser == ''){
            $toUserId = $userId;
        }
        else{
            $toUserId = $this->teenagersRepository->getTeenagerByUniqueId($toUser)->id;
        }

        if (isset($userId) && $userId > 0 && isset($questionID) && $questionID != 0) {
            $answerType = $this->objTraits->find($questionID)->tqq_is_multi_select;
            if($answerType == 0){
                if(count($answerArray)>1){
                    $return = trans('appmessages.onlyoneoptionallowedforthisquestion');
                    return $return;
                }
            }
            $questionsArray = '';
            foreach ($answerArray as $key => $value) {
                $answers = [];
                $answers['tqq_id'] = $questionID;
                $answers['tqo_id'] = $value;
                $answers['tqa_from'] = $userId;
                $answers['tqa_to'] = $toUserId;
                $questionsArray = $this->level1ActivitiesRepository->saveLevel1TraitsAnswer($answers);
            }
            if($questionsArray){
                return $this->getLevel1Trait();
            } else {
                $return = trans('appmessages.default_error_msg');
                return $return;
            }
        } else {
            $return = trans('appmessages.default_error_msg');
            return $return;
        }
    }

    public function getIconNameNew() {
        $categoryId = Input::get('categoryId');
        $categoryType = Input::get('categoryType');

        $textName = ( Input::get('searchText') != "") ? Input::get('searchText') : "";
        $iconCategoryName = $data_cat_type = $data_name = $data_car_image = $image_path_location = $imagePath = '';
        $iconCategoryNameArray = array();
        if ($categoryType == "1" && $categoryId != '') {
            $data_cat_type = 1;
            $data_name = "ci_name";
            $data_car_image = "ci_image";
            $image_path_location = $this->cartoonThumbImageUploadPath;
            $iconCategoryName = $this->level1ActivitiesRepository->searchIconNameWithPagination($categoryId, "pro_ci_cartoon_icons", $textName);
        } elseif ($categoryType == "2" && $categoryId != '') {
            $iconCategoryName = $this->level1ActivitiesRepository->getIconNameWithPagination($textName, $categoryId, "pro_hi_human_icons");
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

        return view('teenager.basic.level1ActivityIcon', compact('iconCategoryName','data_cat_type', 'categoryId', 'textName'));
    }

    public function addIconCategory() {
        $response = [];
        $response['status'] = 0;
        $response['message'] = trans('appmessages.default_error_msg');
        
        $body['ci_category'] = Input::get('categoryId');
        $body['userid'] = Auth::guard('teenager')->user()->id;
        $body['categoryType'] = Input::get('categoryType');
        $body['characterName'] = Input::get('characterName');
        $fileName = $imagePath = '';

        if (isset($body['userid']) && $body['userid'] > 0 && ($body['categoryType'] == 1 || $body['categoryType'] == 2)) {
            $cartoonIconDetail['ci_image'] = '';
            if (Input::file()) {
                $file = Input::file('image');
                if (!empty($file)) {
                    if ($body['categoryType'] == 1) {
                        $fileName = 'cartoon_' . time() . '.' . $file->getClientOriginalExtension();
                        $pathOriginal = public_path($this->cartoonOriginalImageUploadPath . $fileName);
                        $pathThumb = public_path($this->cartoonThumbImageUploadPath . $fileName);
                        Image::make($file->getRealPath())->save($pathOriginal);
                        Image::make($file->getRealPath())->resize($this->cartoonThumbImageWidth, $this->cartoonThumbImageHeight)->save($pathThumb);
                        //Uploading on AWS
                        $originalImage = $this->fileStorageRepository->addFileToStorage($fileName, $this->cartoonOriginalImageUploadPath, $pathOriginal, "s3");
                        $thumbImage = $this->fileStorageRepository->addFileToStorage($fileName, $this->cartoonThumbImageUploadPath, $pathThumb, "s3");
                        //Deleting Local Files
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
                        //Deleting Local Files
                        \File::delete($this->humanOriginalImageUploadPath . $fileName);
                        \File::delete($this->humanThumbImageUploadPath . $fileName);
                        
                        $imagePath = Storage::url($this->humanThumbImageUploadPath . $fileName);
                    }
                }
            } else {
                if ($body['categoryType'] == 1) {
                    $imagePath = Storage::url($this->cartoonThumbImageUploadPath . "proteen-logo.png");
                } else {
                    $imagePath = Storage::url($this->humanThumbImageUploadPath . "proteen-logo.png");
                }
            }
            $iconDetail = $iconNameDetail = [];
            $iconNameDetail['deleted'] = 1;
            $saveCartoonIconCategory = $body['ci_category'];
            if (isset($saveCartoonIconCategory) && $saveCartoonIconCategory != null && $saveCartoonIconCategory != 0) {
                $iconDetail['deleted'] = 1;
                if ($body['categoryType'] == 1) {
                    $iconDetail['ci_name'] = $body['characterName'];
                    $iconDetail['ci_category'] = $saveCartoonIconCategory;
                    $iconDetail['ci_image'] = $fileName;
                    $iconDetail['ci_added_by'] = Auth::guard('teenager')->user()->id;
                    $categoryName = $this->level1CartoonIconRepository->getCartoonCategoryNameFromId($saveCartoonIconCategory);
                    $saveCartoonIcons = $this->level1CartoonIconRepository->saveLevel1CartoonIconDetail($iconDetail, $professions = null);
                } else {
                    $iconDetail['hi_name'] = $body['characterName'];
                    $iconDetail['hi_category'] = $saveCartoonIconCategory;
                    $iconDetail['hi_image'] = $fileName;
                    $iconDetail['hi_added_by'] = Auth::guard('teenager')->user()->id;
                    $categoryName = $this->level1HumanIconRepository->getHumanCategoryNameFromId($saveCartoonIconCategory);
                    $saveCartoonIcons = $this->level1HumanIconRepository->saveLevel1HumanIconDetail($iconDetail, $professions = null);
                }
                if ($saveCartoonIcons) {
                    $response['status'] = 1;
                    $response['message'] = trans('appmessages.default_success_msg');
                    $response['characterid'] = $saveCartoonIcons->id;
                    $response['categoryid'] = $saveCartoonIconCategory;
                    $response['charactername'] = $body['characterName'];
                    $response['Categoryname'] = $categoryName;
                    $response['image'] = $imagePath;
                    $response['categoryType'] = $body['categoryType'];
                } else {
                    $response['status'] = 0;
                    $response['message'] = trans('appmessages.default_error_msg') . ' or ' . trans('appmessages.notvarified_user_msg');
                }
            } else {
                $response['status'] = 0;
                $response['message'] = trans('appmessages.default_error_msg') . ' or ' . trans('appmessages.notvarified_user_msg');
            }
            
        } else {
            $response['status'] = 0;
            $response['message'] = trans('appmessages.default_error_msg');
        }

        echo json_encode($response);
        exit;
    }
}