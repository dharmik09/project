<?php

namespace App\Http\Controllers\Admin;

use Auth;
use Input;
use Config;
use Image;
use File;
use Request;
use Helpers;
use Redirect;
use Illuminate\Pagination\Paginator;
use App\Level1Activity;
use App\Level1Options;
use App\Http\Controllers\Controller;
use App\Http\Requests\Level1ActivityRequest;
use App\Services\Level1Activity\Contracts\Level1ActivitiesRepository;
use Validator;

class Level1ActivityManagementController extends Controller
{

    public function __construct(Level1ActivitiesRepository $Level1ActivitiesRepository)
    {
        $this->objLevel1Activities = new Level1Activity();
        $this->Level1ActivitiesRepository = $Level1ActivitiesRepository;
        $this->Level1ActivityOriginalImageUploadPath = Config::get('constant.LEVEL1_ACTIVITY_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->Level1ActivityThumbImageUploadPath = Config::get('constant.LEVEL1_ACTIVITY_THUMB_IMAGE_UPLOAD_PATH');
        $this->Level1ActivityThumbImageHeight = Config::get('constant.LEVEL1_ACTIVITY_THUMB_IMAGE_HEIGHT');
        $this->Level1ActivityThumbImageWidth = Config::get('constant.LEVEL1_ACTIVITY_THUMB_IMAGE_WIDTH');
        $this->controller = 'Level1ActivityManagementController';
        $this->loggedInUser = Auth::guard('admin');
    }
    public function index()
    {
        $level1activities = $this->Level1ActivitiesRepository->getAllLeve1Activities();
        Helpers::createAudit($this->loggedInUser->user()->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_READ'), $this->controller . "@index", $_SERVER['REQUEST_URI'], Config::get('constant.AUDIT_ORIGIN_WEB'), '', '', $_SERVER['REMOTE_ADDR']);
        return view('admin.ListLevel1Activity',compact('level1activities'));
    }

    public function add()
    {
        $activityDetail = [];
        $uploadLevel1ActivityThumbPath = $this->Level1ActivityThumbImageUploadPath;
        Helpers::createAudit($this->loggedInUser->user()->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_READ'), $this->controller . "@add", $_SERVER['REQUEST_URI'], Config::get('constant.AUDIT_ORIGIN_WEB'), '', '', $_SERVER['REMOTE_ADDR']);

        return view('admin.EditLevel1Activity', compact('activityDetail', 'uploadLevel1ActivityThumbPath'));
    }

    public function edit($id)
    {
        $activityDetail = $this->objLevel1Activities->getActiveLevel1Activity($id);
        $uploadLevel1ActivityThumbPath = $this->Level1ActivityThumbImageUploadPath;
        Helpers::createAudit($this->loggedInUser->user()->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_READ'), $this->controller . "@edit", $_SERVER['REQUEST_URI'], Config::get('constant.AUDIT_ORIGIN_WEB'), '', '', $_SERVER['REMOTE_ADDR']);

        return view('admin.EditLevel1Activity', compact('activityDetail', 'uploadLevel1ActivityThumbPath'));
    }

    public function save(Level1ActivityRequest $Level1ActivityRequest)
    {
        $activityDetail = [];
        $optionDetail = [];

        $optionDetail['l1op_option'] = input::get('l1op_option');
        $optionDetail['l1op_fraction'] = e(input::get('l1op_fraction'));

        $hiddenLogo     = e(input::get('hidden_logo'));
        $activityDetail['l1ac_image']    = $hiddenLogo;
        $activityDetail['id'] = e(input::get('id'));
        $activityDetail['l1ac_text'] = e(input::get('l1ac_text'));
        //$question_active_date = e(input::get('l1ac_active_date'));
        //if (isset($question_active_date) && $question_active_date != '') {
          //  list($month, $day, $year) = explode('/', date("m/d/Y", strtotime($question_active_date)));
            //$activityDetail['l1ac_active_date'] = $year . "-" . $month . "-" . $day;
        //}
        if (Input::get('l1ac_active_date') != '') {
            $sdate = Input::get('l1ac_active_date');
            $startdate = str_replace('/', '-', $sdate);
            $activityDetail['l1ac_active_date'] = date("Y-m-d", strtotime($startdate));            
        }
        $activityDetail['deleted'] = e(input::get('deleted'));
        $l1ac_point = e(input::get('l1ac_points'));
        $point = e(input::get('hidden_points'));
        if(!empty($l1ac_point))
        {
             $activityDetail['l1ac_points'] = $l1ac_point;
        }
        else
        {
             $activityDetail['l1ac_points'] = $point;
        }
        $postData['pageRank'] = Input::get('pageRank');
        if (Input::file())
        {
            $file = Input::file('l1ac_image');
            if(!empty($file))
            {
                //Check image valid extension 
                $validationPass = Helpers::checkValidImageExtension($file);
                if($validationPass)
                {
                    $fileName = 'level1_Activity_' . time() . '.' . $file->getClientOriginalExtension();
                    $pathOriginal = public_path($this->Level1ActivityOriginalImageUploadPath . $fileName);
                    $pathThumb = public_path($this->Level1ActivityThumbImageUploadPath . $fileName);

                    Image::make($file->getRealPath())->save($pathOriginal);
                    Image::make($file->getRealPath())->resize($this->Level1ActivityThumbImageWidth, $this->Level1ActivityThumbImageHeight)->save($pathThumb);

                    if ($hiddenLogo != '')
                    {
                        $imageOriginal = public_path($this->Level1ActivityOriginalImageUploadPath . $hiddenLogo);
                        $imageThumb = public_path($this->Level1ActivityThumbImageUploadPath . $hiddenLogo);
                        File::delete($imageOriginal, $imageThumb);
                    }
                    $activityDetail['l1ac_image'] = $fileName;
                }                
            }
        }
        $response = $this->Level1ActivitiesRepository->saveLevel1ActivityDetail($activityDetail,$optionDetail);
        if($response)
        {
          Helpers::createAudit($this->loggedInUser->user()->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_UPDATE'), Config::get('databaseconstants.TBL_LEVEL1_ACTIVITY'), $response, Config::get('constant.AUDIT_ORIGIN_WEB'),  trans('labels.level1activityupdatesuccess'), serialize($activityDetail), $_SERVER['REMOTE_ADDR']);

          return Redirect::to("admin/level1Activity".$postData['pageRank'])->with('success', trans('labels.level1activityupdatesuccess'));
        }
        else
        {
          Helpers::createAudit($this->loggedInUser->user()->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_UPDATE'), Config::get('databaseconstants.TBL_LEVEL1_ACTIVITY'), $response, Config::get('constant.AUDIT_ORIGIN_WEB'),  trans('labels.somethingwrong'), serialize($activityDetail), $_SERVER['REMOTE_ADDR']);

          return Redirect::to("admin/level1Activity".$postData['pageRank'])->with('error', trans('labels.commonerrormessage'));
        }
    }


    public function delete($id)
    {
        $return = $this->Level1ActivitiesRepository->deleteLevel1Activity($id);
        if ($return)
        {
            Helpers::createAudit($this->loggedInUser->user()->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_DELETE'), Config::get('databaseconstants.TBL_LEVEL1_ACTIVITY'), $id, Config::get('constant.AUDIT_ORIGIN_WEB'), trans('labels.level1activitydeletesuccess'), '', $_SERVER['REMOTE_ADDR']);

            return Redirect::to("admin/level1Activity")->with('success', trans('labels.level1activitydeletesuccess'));
        }
        else
        {
            Helpers::createAudit($this->loggedInUser->user()->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_DELETE'), Config::get('databaseconstants.TBL_LEVEL1_ACTIVITY'), $id, Config::get('constant.AUDIT_ORIGIN_WEB'), trans('labels.somethingwrong'), '', $_SERVER['REMOTE_ADDR']);

            return Redirect::to("admin/level1Activity")->with('error', trans('labels.commonerrormessage'));
        }
    }
}
