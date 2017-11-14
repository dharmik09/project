<?php
namespace App\Http\Controllers\Admin;

use File;
use Image;
use Auth;
use Input;
use Config;
use Request;
use Helpers;
use Redirect;
use Validator;
use Illuminate\Pagination\Paginator;
use App\Level1HumanIcon;
use App\Http\Controllers\Controller;
use App\Http\Requests\Level1HumanIconRequest;
use App\Services\Level1HumanIcon\Contracts\Level1HumanIconRepository;
use App\Templates;
use App\Services\Template\Contracts\TemplatesRepository;
use Mail;
use Cache;
use App\Services\FileStorage\Contracts\FileStorageRepository;

class Level1HumanIconManagementController extends Controller
{

    public function __construct(FileStorageRepository $fileStorageRepository, Level1HumanIconRepository $level1HumanIconRepository, TemplatesRepository $templatesRepository)
    {
        $this->objLevel1HumanActivity = new Level1HumanIcon();
        $this->level1HumanIconRepository = $level1HumanIconRepository;
        $this->fileStorageRepository = $fileStorageRepository;
        $this->objTemplates = new Templates();
        $this->templateRepository = $templatesRepository;
        $this->humanOriginalImageUploadPath = Config::get('constant.HUMAN_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->humanThumbImageUploadPath = Config::get('constant.HUMAN_THUMB_IMAGE_UPLOAD_PATH');
        $this->humanThumbImageHeight = Config::get('constant.HUMAN_THUMB_IMAGE_HEIGHT');
        $this->humanThumbImageWidth = Config::get('constant.HUMAN_THUMB_IMAGE_WIDTH');
        $this->controller = 'Level1HumanIconManagementController';
        $this->loggedInUser = Auth::guard('admin');
    }
    public function index()
    {
        $humanThumbPath = $this->humanThumbImageUploadPath;
        $level1humanicon = $this->level1HumanIconRepository->getLeve1HumanIcon();
        Helpers::createAudit($this->loggedInUser->user()->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_READ'), $this->controller . "@index", $_SERVER['REQUEST_URI'], Config::get('constant.AUDIT_ORIGIN_WEB'), '', '', $_SERVER['REMOTE_ADDR']);
        return view('admin.ListLevel1HumanIcon', compact('level1humanicon','humanThumbPath'));
    }
    public function add()
    {
        $humanIconDetail = [];
        //$humanThumbPath = $this->humanThumbImageUploadPath;
        Helpers::createAudit($this->loggedInUser->user()->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_READ'), $this->controller . "@add", $_SERVER['REQUEST_URI'], Config::get('constant.AUDIT_ORIGIN_WEB'), '', '', $_SERVER['REMOTE_ADDR']);

        return view('admin.EditLevel1HumanIcon', compact('humanIconDetail'));
    }

    public function edit($id)
    {
        $humanIconDetail = $this->objLevel1HumanActivity->getActiveLevel1HumanActivity($id);
        $humanThumbPath = $this->humanThumbImageUploadPath;
        Helpers::createAudit($this->loggedInUser->user()->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_READ'), $this->controller . "@edit", $_SERVER['REQUEST_URI'], Config::get('constant.AUDIT_ORIGIN_WEB'), '', '', $_SERVER['REMOTE_ADDR']);
        return view('admin.EditLevel1HumanIcon', compact('humanIconDetail','humanThumbPath'));
    }

    public function save(Level1HumanIconRequest $Level1HumanIconRequest)
    {
        $humanIconDetail = [];

        $humanIconDetail['id'] = e(input::get('id'));
        $humanIconDetail['hi_name'] = e(input::get('hi_name'));
        $humanIconDetail['hi_category'] = e(input::get('hi_category'));
        $humanIconDetail['deleted'] = e(input::get('deleted'));
        $hiddenLogo     = e(input::get('hidden_logo'));
        $humanIconDetail['hi_image']    = $hiddenLogo;

        $profession = input::get('hpm_profession_id');
        $postData['pageRank'] = Input::get('pageRank');

       /* start upload image of human icons */
        if (Input::file())
        {
            $file = Input::file('hi_image');
            if(!empty($file))
            {
                //Check image valid extension 
                $validationPass = Helpers::checkValidImageExtension($file);
                if($validationPass)
                {
                    $fileName = 'human_' . time() . '.' . $file->getClientOriginalExtension();
                    $pathOriginal = public_path($this->humanOriginalImageUploadPath . $fileName);
                    $pathThumb = public_path($this->humanThumbImageUploadPath . $fileName);

                    Image::make($file->getRealPath())->save($pathOriginal);
                    Image::make($file->getRealPath())->resize($this->humanThumbImageWidth, $this->humanThumbImageHeight)->save($pathThumb);

                    if ($hiddenLogo != '')
                    {
                        $originalImageDelete = $this->fileStorageRepository->deleteFileToStorage($hiddenLogo, $this->humanOriginalImageUploadPath, "s3");
                        $thumbImageDelete = $this->fileStorageRepository->deleteFileToStorage($hiddenLogo, $this->humanThumbImageUploadPath, "s3");
                    }

                    //Uploading on AWS
                    $originalImage = $this->fileStorageRepository->addFileToStorage($fileName, $this->humanOriginalImageUploadPath, $pathOriginal, "s3");
                    $thumbImage = $this->fileStorageRepository->addFileToStorage($fileName, $this->humanThumbImageUploadPath, $pathThumb, "s3");
                    
                    \File::delete($this->humanOriginalImageUploadPath . $fileName);
                    \File::delete($this->humanThumbImageUploadPath . $fileName);
                    $humanIconDetail['hi_image'] = $fileName;
                }
            }
        }
        /* stop upload image of human icons */
        $response = $this->level1HumanIconRepository->saveLevel1HumanIconDetail($humanIconDetail,$profession);
        Cache::forget('l1humanicon');
        if($response)
        {
          Helpers::createAudit($this->loggedInUser->user()->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_UPDATE'), Config::get('databaseconstants.TBL_LEVEL1_HUMAN_ICON'), $response, Config::get('constant.AUDIT_ORIGIN_WEB'),  trans('labels.level1humaniconupdatesuccess'), serialize($humanIconDetail), $_SERVER['REMOTE_ADDR']);

          return Redirect::to("admin/humanIcons".$postData['pageRank'])->with('success', trans('labels.level1humaniconupdatesuccess'));
        }
        else
        {
          Helpers::createAudit($this->loggedInUser->user()->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_UPDATE'), Config::get('databaseconstants.TBL_LEVEL1_HUMAN_ICON'), $response, Config::get('constant.AUDIT_ORIGIN_WEB'),  trans('labels.somethingwrong'), serialize($humanIconDetail), $_SERVER['REMOTE_ADDR']);

          return Redirect::to("admin/humanIcons".$postData['pageRank'])->with('error', trans('labels.commonerrormessage'));
        }
    }

    public function delete($id)
    {
        $return = $this->level1HumanIconRepository->deleteLevel1HumanIcon($id);
        if ($return)
        {
             Helpers::createAudit($this->loggedInUser->user()->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_DELETE'), Config::get('databaseconstants.TBL_LEVEL1_HUMAN_ICON'), $id, Config::get('constant.AUDIT_ORIGIN_WEB'), trans('labels.level1humandeletesuccess'), '', $_SERVER['REMOTE_ADDR']);

            return Redirect::to("admin/humanIcons")->with('success', trans('labels.level1humandeletesuccess'));
        }
        else
        {
             Helpers::createAudit($this->loggedInUser->user()->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_DELETE'), Config::get('databaseconstants.TBL_LEVEL1_HUMAN_ICON'), $id, Config::get('constant.AUDIT_ORIGIN_WEB'), trans('labels.somethingwrong'), '', $_SERVER['REMOTE_ADDR']);

            return Redirect::to("admin/humanIcons")->with('error', trans('labels.commonerrormessage'));
        }
    }

     public function uploadView()
    {
        return view('admin.HumanIconUploadScreen');
    }

    public function uploadHumanIcons()
    {
        //Map icon category with basket category
        $categoryMapped = array('B1'=>'Agriculture, Food and Natural Resources',
            'B2'=>'Architecture and Construction',
            'B3'=>'Arts, Audio-Video Media, Mass Communication',
            'B4'=>'Business and Administration',
            'B5'=>'Education and Training',
            'B6'=>'Finance',
            'B7'=>'Government and Public Administration',
            'B8'=>'Health Sciences',
            'B9'=>'Hospitality and Tourism',
            'B10'=>'Human Services',
            'B11'=>'Information Technology',
            'B12'=>'Law and Public Safety',
            'B13'=>'Manufacturing',
            'B14'=>'Direct / Retail / Wholesale - Sales and Marketing',
            'B15'=>'Scientific Research, Engineering',
            'B16'=>'Transportation, Distribution, Logistics',
            );
        
        $files = Input::file('humanicons');
        $filesCount = count($files);
        $uploadCount = 0;
        $fileError = 0;

        foreach($files as $file)
        {
            $rules = array('file' => 'required|mimes:png,jpg,jpeg,bmp,gif');
            $validator = Validator::make(array('file'=> $file), $rules);
            if(!$validator->passes())
            {
                $fileError++;
            }
        }

        if($fileError == 0)
        {
            foreach($files as $file)
            {
                $rules = array('file' => 'required|mimes:png,jpg,jpeg,bmp,gif');
                $validator = Validator::make(array('file'=> $file), $rules);
                if($validator->passes())
                {
                    $fileName = $file->getClientOriginalName();
                    
                    $pathOriginal = public_path($this->humanOriginalImageUploadPath . $fileName);
                    $pathThumb = public_path($this->humanThumbImageUploadPath . $fileName);

                    Image::make($file->getRealPath())->save($pathOriginal);
                    Image::make($file->getRealPath())->resize($this->humanThumbImageWidth, $this->humanThumbImageHeight)->save($pathThumb);

                    $humanIconDetail = [];
                    
                    $nameArr = explode(".", $file->getClientOriginalName());
                    array_pop($nameArr);
                    $arrUnderscore = explode("_", $nameArr[0]);
                    $categoryName = end($arrUnderscore);
                    if(array_key_exists(strtoupper($categoryName), $categoryMapped)){
                    $actualcategoryName = $categoryMapped[strtoupper($categoryName)];
                    if(isset($actualcategoryName) && $actualcategoryName != ''){
                    $new_filename = preg_replace('/_[^_.]*\./', '.', $fileName);
                    $iconName = str_replace('_',' ',substr($new_filename, 0, strrpos($new_filename, ".")));
                                                                               
                    $humaniconCategoryId = $this->objLevel1HumanActivity->getHumaniconCategoryName($actualcategoryName);
                    $humanIconDetail['hi_name'] = trim($iconName);
                    $humanIconDetail['hi_category'] = $humaniconCategoryId;
                    $humanIconDetail['hi_image'] = $fileName;
                    $humanIconDetail['hi_added_by'] = 0;
                    $humanIconDetail['deleted'] = 1;
                
                    $this->level1HumanIconRepository->saveLevel1HumanIconDetail($humanIconDetail);

                    $uploadCount ++;
                    
                    }}
                }
            }
        }
        
        if($uploadCount == $filesCount)
        {
            return Redirect::to("admin/humanIcons")->with('success', trans('labels.level1humaniconupdatesuccess'));
        }
        else
        {
            return Redirect::to('admin/uploadHumanIcons')->withInput()->withErrors($validator);
        }
    }
    
    public function displayimage()
    {
        $searchParamArray = Input::all();
        $level1Humanicon = $this->level1HumanIconRepository->getLeve1HumanIconfromUsers($searchParamArray);
        Helpers::createAudit($this->loggedInUser->user()->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_READ'), $this->controller . "@index", $_SERVER['REQUEST_URI'], Config::get('constant.AUDIT_ORIGIN_WEB'), '', '', $_SERVER['REMOTE_ADDR']);
        $humanThumbPath = $this->humanThumbImageUploadPath;
        return view('admin.ViewUserHumanIcon',compact('level1Humanicon','humanThumbPath'));
    }
    
    public function deletehumaniconuploadedbyuser($id)
    {
        $teenid = $_GET['tid'];
        $return = $this->level1HumanIconRepository->deleteLevel1HumanIconuploadedbyUser($id);
        if ($return)
        {
            Helpers::createAudit($this->loggedInUser->user()->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_DELETE'), Config::get('databaseconstants.TBL_LEVEL1_CARTOON_ICON'), $id, Config::get('constant.AUDIT_ORIGIN_WEB'), trans('labels.cartoondeletesuccess'), '', $_SERVER['REMOTE_ADDR']);
            $teenDetail = Helpers::getEmailaddress($teenid);
            $emailTemplateContent = $this->templateRepository->getEmailTemplateDataByName(Config::get('constant.DELETE_IMAGE'));
                        $data = array();
                        $replaceArray = array();
                        $replaceArray['toName'] = $teenDetail[0]->t_name;
                        
                        $content = $this->templateRepository->getEmailContent($emailTemplateContent->et_body, $replaceArray);
                        $data['subject'] = $emailTemplateContent->et_subject;
                        $data['toEmail'] = $teenDetail[0]->t_email;
                        $data['toName'] = $teenDetail[0]->t_name;
                        $data['content'] = $content;
                        Mail::send(['html' => 'emails.Template'], $data, function($message) use($data) {
                        $message->subject($data['subject']);
                        $message->to($data['toEmail'],$data['toName']);
                });
            return Redirect::to("admin/viewHumanUserImage")->with('success', trans('labels.level1cartoondeletesuccess'));
        }
        else
        {
            Helpers::createAudit($this->loggedInUser->user()->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_DELETE'), Config::get('databaseconstants.TBL_LEVEL1_CARTOON_ICON'), $id, Config::get('constant.AUDIT_ORIGIN_WEB'), trans('labels.somethingwrong'), '', $_SERVER['REMOTE_ADDR']);

            return Redirect::to("admin/viewHumanUserImage")->with('error', trans('labels.commonerrormessage'));
        }
    }
    
    public function deleteHumanIcon()
    {
        $selectedIcons = Input::get('deleteIcons');
        if(isset($selectedIcons) && !empty($selectedIcons)){
            foreach($selectedIcons as $key=>$val){
                $return = $this->level1HumanIconRepository->deleteLevel1HumanIconuploadedbyUser($key);
            }
            return Redirect::to("admin/viewHumanUserImage")->with('success', trans('labels.level1cartoondeletesuccess'));                      
        }else{
            return Redirect::to("admin/viewHumanUserImage")->with('error', 'Please select atleast image to delete');
        }
    }
}
