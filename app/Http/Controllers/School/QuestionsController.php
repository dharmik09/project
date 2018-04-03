<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use Auth;
use Input;
use Image;
use File;
use Config;
use Request;
use Helpers;
use Redirect;
use Illuminate\Pagination\Paginator;
use App\Level2Activity;
use App\Http\Requests\Level2ActivityRequest;
use App\Services\Level2Activity\Contracts\Level2ActivitiesRepository;
use App\Services\FileStorage\Contracts\FileStorageRepository;

class QuestionsController extends Controller {

    public function __construct(FileStorageRepository $fileStorageRepository, Level2ActivitiesRepository $level2ActivitiesRepository) {
        $this->objLevel2Activities = new Level2Activity();
        $this->level2ActivitiesRepository = $level2ActivitiesRepository;
        $this->fileStorageRepository = $fileStorageRepository;
        $this->loggedInUser = Auth::guard('admin');
        $this->level2ActivityOriginalImageUploadPath = Config::get('constant.LEVEL2_ACTIVITY_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->level2ActivityThumbImageUploadPath = Config::get('constant.LEVEL2_ACTIVITY_THUMB_IMAGE_UPLOAD_PATH'); 
        $this->level2ActivityThumbImageHeight = Config::get('constant.LEVEL2_ACTIVITY_THUMB_IMAGE_HEIGHT');
        $this->level2ActivityThumbImageWidth = Config::get('constant.LEVEL2_ACTIVITY_THUMB_IMAGE_WIDTH');
        $this->controller = 'Level2ActivityManagementController';
    }

    /*
     * Returns questions listing page
     */
    public function index() 
    {
        $level2activities = $this->level2ActivitiesRepository->getAllLeve2Activities(Auth::guard('school')->user()->id);
        return view('school.listQuestions', compact('level2activities'));
    }

    /*
     * Returns Add questions form
     */
    public function add()
    {
        $activityDetail = [];
        return view('school.editQuestions', compact('activityDetail'));
    }

    /*
     * Store level 2 questions details
     */
    public function save()
    {
        $activityDetail = [];
        $option = input::get('l2op_option');
        $hiddenLogo     = e(input::get('hidden_logo'));
        $activityDetail['l2ac_image']    = $hiddenLogo;

        $activityDetail['id'] = e(input::get('id'));
        $activityDetail['l2ac_text'] = e(input::get('l2ac_text'));
        $activityDetail['l2ac_apptitude_type'] = input::get('l2ac_apptitude');
        $activityDetail['l2ac_interest'] = input::get('l2ac_interest');
        $activityDetail['l2ac_mi_type'] = input::get('l2ac_multipleintelligent');
        $activityDetail['l2ac_personality_type'] = input::get('l2ac_personality');
        $activityDetail['section_type'] = input::get('section_type');
        $activityDetail['l2ac_school_id'] = Auth::guard('school')->user()->id;
        $activityDetail['deleted'] = e(input::get('deleted'));
        $postData['pageRank'] = Input::get('pageRank');
        $l2ac_point = e(input::get('l2ac_points'));
        $point = e(input::get('hidden_points'));
        if(!empty($l2ac_point)) {
            $activityDetail['l2ac_points'] = $l2ac_point;
        } else {
            $activityDetail['l2ac_points'] = $point;
        }
        if (Input::file()) {
            $file = Input::file('l2ac_image');
            if(!empty($file)) {
                //Check image valid extension 
                $validationPass = Helpers::checkValidImageExtension($file);
                if($validationPass) {
                    $fileName = 'level2_Activity_' . time() . '.' . $file->getClientOriginalExtension();
                    $pathOriginal = public_path($this->level2ActivityOriginalImageUploadPath . $fileName);
                    $pathThumb = public_path($this->level2ActivityThumbImageUploadPath . $fileName);
                    Image::make($file->getRealPath())->save($pathOriginal);
                    Image::make($file->getRealPath())->resize($this->level2ActivityThumbImageWidth, $this->level2ActivityThumbImageHeight)->save($pathThumb);
                    if($hiddenLogo != '') {
                        $originalImageDelete = $this->fileStorageRepository->deleteFileToStorage($hiddenLogo, $this->level2ActivityOriginalImageUploadPath, "s3");
                        $thumbImageDelete = $this->fileStorageRepository->deleteFileToStorage($hiddenLogo, $this->level2ActivityThumbImageUploadPath, "s3");
                    }
                    //Uploading on AWS
                    $originalImage = $this->fileStorageRepository->addFileToStorage($fileName, $this->level2ActivityOriginalImageUploadPath, $pathOriginal, "s3");
                    $thumbImage = $this->fileStorageRepository->addFileToStorage($fileName, $this->level2ActivityThumbImageUploadPath, $pathThumb, "s3");

                    \File::delete($this->level2ActivityOriginalImageUploadPath . $fileName);
                    \File::delete($this->level2ActivityThumbImageUploadPath . $fileName);
                    $activityDetail['l2ac_image'] = $fileName;
                }
            }
        }
        $radio_val = input::get('l2rad_option');
        $response = $this->level2ActivitiesRepository->saveLevel2ActivityDetail($activityDetail, $option, $radio_val);
        if($response) {
            return Redirect::to("school/questions".$postData['pageRank'])->with('success', trans('labels.level2activityupdatesuccess'));
        } else {
            return Redirect::to("school/questions".$postData['pageRank'])->with('error', trans('labels.commonerrormessage'));
        }
    }

    /*
     * Returns Edit questions form
     * @param: activityId
     */
    public function edit($activityId)
    {
        $activityDetail = $this->objLevel2Activities->getActiveLevel2Activity($activityId);
        $uploadLevel2ActivityThumbPath = $this->level2ActivityThumbImageUploadPath;
        return view('school.editQuestions', compact('activityDetail', 'uploadLevel2ActivityThumbPath'));
    }

    /*
     * Delete questions
     * @param: activityId
     */
    public function delete($activityId)
    {
        $return = $this->level2ActivitiesRepository->deleteLevel2Activity($activityId);
        if ($return) {
            return Redirect::to("school/questions")->with('success', trans('labels.level2activitydeletesuccess'));
        } else {
            return Redirect::to("school/questions")->with('error', trans('labels.commonerrormessage'));
        }
    }
}

