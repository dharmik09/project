<?php

namespace App\Http\Controllers\Admin;

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
use App\Http\Controllers\Controller;
use App\Http\Requests\Level2ActivityRequest;
use App\Services\Level2Activity\Contracts\Level2ActivitiesRepository;
use App\Services\FileStorage\Contracts\FileStorageRepository;
use App\Services\Schools\Contracts\SchoolsRepository;

class Level2ActivityManagementController extends Controller
{

    public function __construct(FileStorageRepository $fileStorageRepository, Level2ActivitiesRepository $level2ActivitiesRepository, SchoolsRepository $schoolsRepository) {
        $this->objLevel2Activities = new Level2Activity();
        $this->level2ActivitiesRepository = $level2ActivitiesRepository;
        $this->fileStorageRepository = $fileStorageRepository;
        $this->loggedInUser = Auth::guard('admin');
        $this->level2ActivityOriginalImageUploadPath = Config::get('constant.LEVEL2_ACTIVITY_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->level2ActivityThumbImageUploadPath = Config::get('constant.LEVEL2_ACTIVITY_THUMB_IMAGE_UPLOAD_PATH'); 
        $this->level2ActivityThumbImageHeight = Config::get('constant.LEVEL2_ACTIVITY_THUMB_IMAGE_HEIGHT');
        $this->level2ActivityThumbImageWidth = Config::get('constant.LEVEL2_ACTIVITY_THUMB_IMAGE_WIDTH');
        $this->schoolsRepository = $schoolsRepository;
        $this->controller = 'Level2ActivityManagementController';
    }
    public function index()
    {
        $correctAnswerQuestionsIds = Helpers::getTeenAPIScore(8);
        $level2activities = $this->level2ActivitiesRepository->getAllLeve2Activities();
        Helpers::createAudit($this->loggedInUser->user()->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_READ'), $this->controller . "@index", $_SERVER['REQUEST_URI'], Config::get('constant.AUDIT_ORIGIN_WEB'), '', '', $_SERVER['REMOTE_ADDR']);
        return view('admin.ListLevel2Activity',compact('level2activities','searchParamArray'));
    }

    public function add()
    {
        $activityDetail = [];
        //$uploadLevel2ActivityThumbPath = $this->level2ActivityThumbImageUploadPath;
        Helpers::createAudit($this->loggedInUser->user()->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_READ'), $this->controller . "@add", $_SERVER['REQUEST_URI'], Config::get('constant.AUDIT_ORIGIN_WEB'), '', '', $_SERVER['REMOTE_ADDR']);

        return view('admin.EditLevel2Activity', compact('activityDetail'));
    }

    public function edit($id)
    {
        $activityDetail = $this->objLevel2Activities->getActiveLevel2Activity($id);
        
        $uploadLevel2ActivityThumbPath = $this->level2ActivityThumbImageUploadPath;
        Helpers::createAudit($this->loggedInUser->user()->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_READ'), $this->controller . "@edit", $_SERVER['REQUEST_URI'], Config::get('constant.AUDIT_ORIGIN_WEB'), '', '', $_SERVER['REMOTE_ADDR']);
        return view('admin.EditLevel2Activity', compact('activityDetail', 'uploadLevel2ActivityThumbPath'));
    }

    public function save(Level2ActivityRequest $Level2ActivityRequest)
    {
      $activityDetail = [];
      $option = input::get('l2op_option');
      //print_r($option);die;

      $hiddenLogo     = e(input::get('hidden_logo'));
      $activityDetail['l2ac_image']    = $hiddenLogo;

      $activityDetail['id'] = e(input::get('id'));
      $activityDetail['l2ac_text'] = e(input::get('l2ac_text'));
      $activityDetail['l2ac_apptitude_type'] = input::get('l2ac_apptitude');
      $activityDetail['l2ac_interest'] = input::get('l2ac_interest');
      $activityDetail['l2ac_mi_type'] = input::get('l2ac_multipleintelligent');
      $activityDetail['l2ac_personality_type'] = input::get('l2ac_personality');
      $activityDetail['section_type'] = input::get('section_type');

      $activityDetail['deleted'] = e(input::get('deleted'));
      $postData['pageRank'] = Input::get('pageRank');
      $l2ac_point = e(input::get('l2ac_points'));
      $point = e(input::get('hidden_points'));
      if(!empty($l2ac_point))
      {
        $activityDetail['l2ac_points'] = $l2ac_point;
      }
      else
      {
        $activityDetail['l2ac_points'] = $point;
      }
      if (Input::file())
        {
            $file = Input::file('l2ac_image');
            if(!empty($file))
            {
                //Check image valid extension 
                $validationPass = Helpers::checkValidImageExtension($file);
                if($validationPass)
                {
                    $fileName = 'level2_Activity_' . time() . '.' . $file->getClientOriginalExtension();
                    $pathOriginal = public_path($this->level2ActivityOriginalImageUploadPath . $fileName);
                    $pathThumb = public_path($this->level2ActivityThumbImageUploadPath . $fileName);
                    Image::make($file->getRealPath())->save($pathOriginal);
                    Image::make($file->getRealPath())->resize($this->level2ActivityThumbImageWidth, $this->level2ActivityThumbImageHeight)->save($pathThumb);
                    if($hiddenLogo != '')
                    {
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
        //print_r($radio_val);die;
        $response = $this->level2ActivitiesRepository->saveLevel2ActivityDetail($activityDetail,$option,$radio_val);

        if($response)
        {
          Helpers::createAudit($this->loggedInUser->user()->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_UPDATE'), Config::get('databaseconstants.TBL_LEVEL2_ACTIVITY'), $response, Config::get('constant.AUDIT_ORIGIN_WEB'),  trans('labels.level2activityupdatesuccess'), serialize($activityDetail), $_SERVER['REMOTE_ADDR']);
          if($activityDetail['section_type'] == Config::get('constant.LEVEL2_SECTION_4')) {
            return Redirect::to("admin/schoolLevel2Activity".$postData['pageRank'])->with('success', trans('labels.level2activityupdatesuccess'));
          } else {
            return Redirect::to("admin/level2Activity".$postData['pageRank'])->with('success', trans('labels.level2activityupdatesuccess'));
          }
        }
        else
        {
          Helpers::createAudit($this->loggedInUser->user()->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_UPDATE'), Config::get('databaseconstants.TBL_LEVEL2_ACTIVITY'), $response, Config::get('constant.AUDIT_ORIGIN_WEB'),  trans('labels.somethingwrong'), serialize($activityDetail), $_SERVER['REMOTE_ADDR']);
          if ($activityDetail['section_type'] == Config::get('constant.LEVEL2_SECTION_4')) {
            return Redirect::to("admin/schoolLevel2Activity".$postData['pageRank'])->with('success', trans('labels.level2activityupdatesuccess'));
          } else {
            return Redirect::to("admin/level2Activity".$postData['pageRank'])->with('error', trans('labels.commonerrormessage'));
          }
        }
    }


    public function delete($id)
    {
        $return = $this->level2ActivitiesRepository->deleteLevel2Activity($id);
        if ($return)
        {

            return Redirect::to("admin/level2Activity")->with('success', trans('labels.level2activitydeletesuccess'));
        }
        else
        {
            return Redirect::to("admin/level2Activity")->with('error', trans('labels.commonerrormessage'));
        }
    }

    /* Returns List view of school l2 activity */
    public function schoolLevel2Activity()
    {
        $correctAnswerQuestionsIds = Helpers::getTeenAPIScore(8);
        $level2activities = $this->level2ActivitiesRepository->getAllSchoolLeve2Activities();
        $schools = $this->schoolsRepository->getApprovedSchools();
        return view('admin.ListSchoolLevel2Activity',compact('level2activities','searchParamArray', 'schools'));
    }

    /* Returns searched List view of school l2 activity */
    public function searchSchoolLevel2Activity()
    {
        $schoolId = Input::get('school');
        $level2activities = $this->level2ActivitiesRepository->getAllSchoolLeve2Activities($schoolId);
        $schools = $this->schoolsRepository->getApprovedSchools();
        return view('admin.ListSchoolLevel2Activity',compact('level2activities','schoolId', 'schools'));
    }
}
