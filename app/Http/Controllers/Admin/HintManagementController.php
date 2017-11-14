<?php

namespace App\Http\Controllers\Admin;


use App\Item;
use Session;
use Auth;
use File;
use Image;
use Input;
use Config;
use Request;
use Helpers;
use Redirect;
use DB;
use Illuminate\Pagination\Paginator;
use App\Teenagers;
use App\Http\Controllers\Controller;
use App\Http\Requests\TeenagerRequest;
use App\Http\Requests\TeenagerBulkRequest;
use App\Services\Teenagers\Contracts\TeenagersRepository;
use App\Services\Professions\Contracts\ProfessionsRepository;
use App\Services\Level1Activity\Contracts\Level1ActivitiesRepository;
use App\Services\Level2Activity\Contracts\Level2ActivitiesRepository;
use App\Services\FeedbackQuestions\Contracts\FeedbackQuestionsRepository;

class HintManagementController extends Controller {

    public function __construct(TeenagersRepository $teenagersRepository,ProfessionsRepository $professionsRepository,Level1ActivitiesRepository $Level1ActivitiesRepository, Level2ActivitiesRepository $Level2ActivitiesRepository, FeedbackQuestionsRepository $FeedbackQuestionsRepository) { 
        $this->objTeenagers = new Teenagers();
        $this->teenagersRepository = $teenagersRepository;
        $this->professionsRepository = $professionsRepository;
        $this->hintOriginalImageUploadPath = Config::get('constant.HINT_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->hintThumbImageUploadPath = Config::get('constant.HINT_THUMB_IMAGE_UPLOAD_PATH');
        $this->hintThumbImageHeight = Config::get('constant.HINT_THUMB_IMAGE_HEIGHT');
        $this->hintThumbImageWidth = Config::get('constant.HINT_THUMB_IMAGE_WIDTH');
        
        $this->controller = 'TeenagerManagementController';
        $this->loggedInUser = Auth::guard('admin');
        $this->Level1ActivitiesRepository = $Level1ActivitiesRepository;
        $this->Level2ActivitiesRepository = $Level2ActivitiesRepository;
        $this->FeedbackQuestionsRepository = $FeedbackQuestionsRepository;
    }

    public function index() {
       
    }

    public function hintLogic()
    {
        $hintData = array();
        $hintPath = public_path('uploads/hint/original/');
        $files = File::allFiles($hintPath);
        $imageUrl = Config::get('constant.HINT_ORIGINAL_IMAGE_UPLOAD_PATH');//converts to url
        foreach ($files as $file)
        {
            $gifImagesName[] = basename($file);
        }
        $hintOriginalImageUploadPath = Config::get('constant.HINT_ORIGINAL_IMAGE_UPLOAD_PATH');
        
        $systemLevels = DB::table(config::get('databaseconstants.TBL_SYSTEM_LEVELS'))->get();
        return view('admin.hintLogic',compact('systemLevels','hintOriginalImageUploadPath','gifImagesName','hintData'));
    }
    
    
    public function edithintLogic($id)
    {        
        $hintData = $this->FeedbackQuestionsRepository->getHintById($id); 
        
        $hintPath = public_path('uploads/hint/original/');
        $files = File::allFiles($hintPath);
        $imageUrl = Config::get('constant.HINT_ORIGINAL_IMAGE_UPLOAD_PATH');//converts to url
        foreach ($files as $file)
        {
            $gifImagesName[] = basename($file);
        }
        $hintOriginalImageUploadPath = Config::get('constant.HINT_ORIGINAL_IMAGE_UPLOAD_PATH');
        
        $systemLevels = DB::table(config::get('databaseconstants.TBL_SYSTEM_LEVELS'))->get();
        return view('admin.hintLogic',compact('systemLevels','hintData','hintOriginalImageUploadPath','gifImagesName'));
    }
    
    public function listhint()
    {
        $hintOriginalImageUploadPath = Config::get('constant.HINT_ORIGINAL_IMAGE_UPLOAD_PATH');
        
        $hints = $this->FeedbackQuestionsRepository->getAllHints();
        
        return view('admin.ListHint',compact('hints','hintOriginalImageUploadPath','searchParamArray'));        
    }
    
    public function savehint()
    {
        $allPostdata = Input::All();
        if(isset($allPostdata))
        {
            $saveData['id'] = $allPostdata['id'];
            $saveData['applied_level'] = $allPostdata['level'];
            $saveData['hint_type'] = $allPostdata['hint_type'];
            $saveData['data_id'] = isset($allPostdata['data_id'])?$allPostdata['data_id']:'0';
            $saveData['hint_text'] = trim($allPostdata['hint_text']);
            //$saveData['time'] = $allPostdata['hint_time'];
            $saveData['hint_image'] = $allPostdata['hint_image'];
            $saveData['deleted'] = $allPostdata['deleted'];
        }
                
        if(Input::file())
        {
            $file = Input::file('hint_image');
            if(!empty($file))
            {
                $fileName = 'hint_' . time() . '.' . $file->getClientOriginalExtension();
                $pathOriginal = public_path($this->hintOriginalImageUploadPath . $fileName);
                $pathThumb = public_path($this->hintThumbImageUploadPath . $fileName);

                Image::make($file->getRealPath())->save($pathOriginal);
                Image::make($file->getRealPath())->resize($this->hintThumbImageWidth, $this->hintThumbImageHeight)->save($pathThumb);

                $saveData['hint_image'] = $fileName;
            }
        }
        $this->FeedbackQuestionsRepository->saveHint($saveData);   
        return Redirect::to("admin/listHint".$allPostdata['pageRank'])->with('success', 'Hint Added successfully');
        exit;
    }
    
    public function calculateLevel1QuestionTrend()
    {
        $answers= DB::table('pro_l1ans_level1_answers')->where('l1ans_activity',1)->get();
        
        if(!empty($answers))
        {
           foreach($answers as $key=>$data)
           {
              $answerCount[] = $data->l1ans_answer; 
           }
        }
        $totalCount = count($answerCount);
        $count = array_count_values($answerCount);
        $yesPercent = '';
        $noPercent = '';
        $notSurePercent = '';
        if(!empty($count)){
            $yesPercent = ($count[1] * 100) / $totalCount.'%';
            $noPercent = ($count[2] * 100) / $totalCount.'%';
            $notSurePercent = ($count[3] * 100) / $totalCount.'%';
        }
        $returnArr = array('Yes'=>$yesPercent,'No'=>$noPercent,'Sometime'=>$notSurePercent);
    }
    
    public function deletehint($id)
    {
        $return = $this->FeedbackQuestionsRepository->deletehint($id);
        if ($return)
        {
            return Redirect::to("admin/listHint")->with('success','Hint deleted successfully');
        }
        else
        {
            return Redirect::to("admin/listHint")->with('error', trans('labels.commonerrormessage'));
        }
    }

}
