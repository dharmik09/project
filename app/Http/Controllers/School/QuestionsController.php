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
}

