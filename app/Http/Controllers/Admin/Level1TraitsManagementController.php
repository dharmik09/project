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
use App\Level1Traits;
use App\Level1TraitsOptions;
use App\Http\Controllers\Controller;
use App\Http\Requests\Level1TraitsRequest;
use App\Services\Level1Activity\Contracts\Level1ActivitiesRepository;
use Validator;

class Level1TraitsManagementController extends Controller
{

    public function __construct(Level1ActivitiesRepository $level1ActivitiesRepository)
    {
        $this->objLevel1Traits = new Level1Traits();
        $this->level1ActivitiesRepository = $level1ActivitiesRepository;
        $this->level1TraitsOriginalImageUploadPath = Config::get('constant.LEVEL1_TRAITS_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->level1TraitsThumbImageUploadPath = Config::get('constant.LEVEL1_TRAITS_THUMB_IMAGE_UPLOAD_PATH');
        $this->level1TraitsThumbImageHeight = Config::get('constant.LEVEL1_TRAITS_THUMB_IMAGE_HEIGHT');
        $this->level1TraitsThumbImageWidth = Config::get('constant.LEVEL1_TRAITS_THUMB_IMAGE_WIDTH');
        $this->controller = 'Level1TraitsManagementController';
        $this->loggedInUser = Auth::guard('admin');
    }
    public function index()
    {
        // $level1activities = $this->level1ActivitiesRepository->getAllLeve1Activities();
        $level1traits = $this->level1ActivitiesRepository->getAllLeve1Traits();
        return view('admin.ListLevel1Traits',compact('level1traits'));
    }

    public function add()
    {
        $traitsDetail = [];
        $uploadLevel1TraitsThumbPath = $this->level1TraitsThumbImageUploadPath;
        return view('admin.EditLevel1Traits', compact('traitsDetail', 'uploadLevel1TraitsThumbPath'));
    }

    public function edit($id)
    {
        $traitsDetail = $this->objLevel1Traits->getActiveLevel1Traits($id);
        $uploadLevel1TraitsThumbPath = $this->level1TraitsThumbImageUploadPath;
        return view('admin.EditLevel1Traits', compact('traitsDetail', 'uploadLevel1TraitsThumbPath'));
    }

    public function save(Level1TraitsRequest $level1TraitsRequest)
    {
        $traitsDetail = [];
        $optionDetail = [];

        $optionDetail['tqo_option'] = input::get('tqo_option');
        $hiddenLogo = e(input::get('hidden_logo'));

        $traitsDetail['id'] = e(input::get('id'));
        $traitsDetail['tqq_image']    = $hiddenLogo;
        $traitsDetail['tqq_text'] = e(input::get('tqq_text'));
        $traitsDetail['tqq_is_multi_select'] = e(input::get('tqq_is_multi_select'));

        if (Input::get('tqq_active_date') != '') {
            $sdate = Input::get('tqq_active_date');
            $startdate = str_replace('/', '-', $sdate);
            $traitsDetail['tqq_active_date'] = date("Y-m-d", strtotime($startdate));            
        }
        
        $traitsDetail['deleted'] = e(input::get('deleted'));
        $tqq_points = e(input::get('tqq_points'));
        $point = e(input::get('hidden_points'));

        if(!empty($tqq_points))
        {
             $traitsDetail['tqq_points'] = $tqq_points;
        }
        else
        {
             $traitsDetail['tqq_points'] = $point;
        }

        $postData['pageRank'] = Input::get('pageRank');
        
        if (Input::file())
        {
            $file = Input::file('tqq_image');
            if(!empty($file))
            {
                //Check image valid extension 
                $validationPass = Helpers::checkValidImageExtension($file);
                if($validationPass)
                {
                    $fileName = 'level1_Traits_' . time() . '.' . $file->getClientOriginalExtension();
                    $pathOriginal = public_path($this->level1TraitsOriginalImageUploadPath . $fileName);
                    $pathThumb = public_path($this->level1TraitsThumbImageUploadPath . $fileName);

                    Image::make($file->getRealPath())->save($pathOriginal);
                    Image::make($file->getRealPath())->resize($this->level1TraitsThumbImageWidth, $this->level1TraitsThumbImageHeight)->save($pathThumb);

                    if ($hiddenLogo != '')
                    {
                        $imageOriginal = public_path($this->level1TraitsOriginalImageUploadPath . $hiddenLogo);
                        $imageThumb = public_path($this->level1TraitsThumbImageUploadPath . $hiddenLogo);
                        File::delete($imageOriginal, $imageThumb);
                    }
                    $traitsDetail['tqq_image'] = $fileName;
                }                
            }
        }
        $response = $this->level1ActivitiesRepository->saveLevel1TraitsDetail($traitsDetail,$optionDetail);
        if($response)
        {
          return Redirect::to("admin/level1Traits".$postData['pageRank'])->with('success', trans('labels.level1traitupdatesuccess'));
        }
        else
        {
          return Redirect::to("admin/level1Traits".$postData['pageRank'])->with('error', trans('labels.commonerrormessage'));
        }
    }


    public function delete($id)
    {
        $return = $this->level1ActivitiesRepository->deleteLevel1Trait($id);
        if ($return)
        {
            return Redirect::to("admin/level1Traits")->with('success', trans('labels.level1traitdeletesuccess'));
        }
        else
        {
            return Redirect::to("admin/level1Traits")->with('error', trans('labels.commonerrormessage'));
        }
    }
}
