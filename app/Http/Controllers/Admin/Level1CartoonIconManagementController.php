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
use App\Level1CartoonIcon;
use App\Http\Controllers\Controller; 
use App\Http\Requests\Level1CartoonIconRequest;
use App\Services\Level1CartoonIcon\Contracts\Level1CartoonIconRepository;
use App\Templates;
use App\Services\Template\Contracts\TemplatesRepository;
use Mail;
use Cache;
use App\Services\FileStorage\Contracts\FileStorageRepository;

class Level1CartoonIconManagementController extends Controller
{

    public function __construct(FileStorageRepository $fileStorageRepository, Level1CartoonIconRepository $Level1CartoonIconRepository, TemplatesRepository $TemplatesRepository)
    {
        //$this->middleware('auth.admin');
        $this->objLevel1CartoonActivity = new Level1CartoonIcon();
        $this->Level1CartoonIconRepository = $Level1CartoonIconRepository;
        $this->fileStorageRepository = $fileStorageRepository;
        $this->objTemplates = new Templates();
        $this->TemplateRepository = $TemplatesRepository;
        $this->controller = 'Level1CartoonIconManagementController';
        $this->loggedInUser = Auth::guard('admin');
        $this->cartoonOriginalImageUploadPath = Config::get('constant.CARTOON_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->cartoonThumbImageUploadPath = Config::get('constant.CARTOON_THUMB_IMAGE_UPLOAD_PATH');
        $this->cartoonThumbImageHeight = Config::get('constant.CARTOON_THUMB_IMAGE_HEIGHT');
        $this->cartoonThumbImageWidth = Config::get('constant.CARTOON_THUMB_IMAGE_WIDTH');
    }
    public function index()
    {
        $level1cartoonicon = $this->Level1CartoonIconRepository->getLeve1CartoonIcon();
        Helpers::createAudit($this->loggedInUser->user()->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_READ'), $this->controller . "@index", $_SERVER['REQUEST_URI'], Config::get('constant.AUDIT_ORIGIN_WEB'), '', '', $_SERVER['REMOTE_ADDR']);
        $cartoonThumbPath = $this->cartoonThumbImageUploadPath;
        return view('admin.ListLevel1CartoonIcon',compact('level1cartoonicon','cartoonThumbPath'));
    }

    public function add()
    {
        $cartoonIconDetail = [];
        Helpers::createAudit($this->loggedInUser->user()->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_READ'), $this->controller . "@add", $_SERVER['REQUEST_URI'], Config::get('constant.AUDIT_ORIGIN_WEB'), '', '', $_SERVER['REMOTE_ADDR']);

        return view('admin.EditLevel1CartoonIcon', compact('cartoonIconDetail'));
    }

    public function edit($id)
    {
        $cartoonIconDetail = $this->objLevel1CartoonActivity->findData($id);
        $cartoonThumbPath = $this->cartoonThumbImageUploadPath;
        Helpers::createAudit($this->loggedInUser->user()->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_READ'), $this->controller . "@edit", $_SERVER['REQUEST_URI'], Config::get('constant.AUDIT_ORIGIN_WEB'), '', '', $_SERVER['REMOTE_ADDR']);
        return view('admin.EditLevel1CartoonIcon', compact('cartoonIconDetail', 'cartoonThumbPath'));
    }

    public function save(Level1CartoonIconRequest $Level1CartoonIconRequest)
    {
        $cartoonIconDetail = [];

        $cartoonIconDetail['id'] = e(input::get('id'));
        $cartoonIconDetail['ci_name'] = e(input::get('l1ci_name'));
        $cartoonIconDetail['ci_category'] = e(input::get('ci_category'));
        $cartoonIconDetail['deleted'] = e(input::get('deleted'));
        $hiddenLogo     = e(input::get('hidden_logo'));
        $cartoonIconDetail['ci_image']    = $hiddenLogo;
        $professions = input::get('ci_professions');
        $postData['pageRank'] = Input::get('pageRank');
       /* start upload image of cartoons */
        if (Input::file())
        {
            $file = Input::file('l1ci_image');
            if(!empty($file))
            {
                //Check image valid extension
                $validationPass = Helpers::checkValidImageExtension($file);
                if($validationPass)
                {
                    echo $fileName = 'cartoon_' . time() . '.' . $file->getClientOriginalExtension();
                    $pathOriginal = public_path($this->cartoonOriginalImageUploadPath . $fileName);
                    $pathThumb = public_path($this->cartoonThumbImageUploadPath . $fileName);

                    Image::make($file->getRealPath())->save($pathOriginal);
                    Image::make($file->getRealPath())->resize($this->cartoonThumbImageWidth, $this->cartoonThumbImageHeight)->save($pathThumb);

                    if ($hiddenLogo != '')
                    {
                        $originalImageDelete = $this->fileStorageRepository->deleteFileToStorage($hiddenLogo, $this->cartoonOriginalImageUploadPath, "s3");
                        $thumbImageDelete = $this->fileStorageRepository->deleteFileToStorage($hiddenLogo, $this->cartoonThumbImageWidth, "s3");
                    }

                    //Uploading on AWS
                    $originalImage = $this->fileStorageRepository->addFileToStorage($fileName, $this->cartoonOriginalImageUploadPath, $pathOriginal, "s3");
                    $thumbImage = $this->fileStorageRepository->addFileToStorage($fileName, $this->cartoonThumbImageUploadPath, $pathThumb, "s3");
                    
                    \File::delete($this->cartoonOriginalImageUploadPath . $fileName);
                    \File::delete($this->cartoonThumbImageUploadPath . $fileName);
                    $cartoonIconDetail['ci_image'] = $fileName;


                }
            }
        }

        /* stop upload image of cartoons */
        $response = $this->Level1CartoonIconRepository->saveLevel1CartoonIconDetail($cartoonIconDetail,$professions);
        Cache::forget('l1cartoonicon');
        if($response)
        {
          Helpers::createAudit($this->loggedInUser->user()->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_UPDATE'), Config::get('databaseconstants.TBL_LEVEL1_CARTOON_ICON'), $response, Config::get('constant.AUDIT_ORIGIN_WEB'),  trans('labels.cartoonupdatesuccess'), serialize($cartoonIconDetail), $_SERVER['REMOTE_ADDR']);

          return Redirect::to("admin/cartoons".$postData['pageRank'])->with('success', trans('labels.level1cartooniconyupdatesuccess'));
        }
        else
        {
          Helpers::createAudit($this->loggedInUser->user()->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_UPDATE'), Config::get('databaseconstants.TBL_LEVEL1_CARTOON_ICON'), $response, Config::get('constant.AUDIT_ORIGIN_WEB'),  trans('labels.somethingwrong'), serialize($cartoonIconDetail), $_SERVER['REMOTE_ADDR']);

          return Redirect::to("admin/cartoons".$postData['pageRank'])->with('error', trans('labels.commonerrormessage'));
        }


    }


    public function delete($id)
    {
        //echo asset($this->cartoonOriginalImageUploadPath.'cartoon_1459421710.jpg'); exit;
        // @unlink(asset($this->$cartoonOriginalImageUploadPath.'cartoon_1459421710.jpg'));
        $return = $this->Level1CartoonIconRepository->deleteLevel1Cartoon($id);
        if ($return)
        {
            Helpers::createAudit($this->loggedInUser->user()->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_DELETE'), Config::get('databaseconstants.TBL_LEVEL1_CARTOON_ICON'), $id, Config::get('constant.AUDIT_ORIGIN_WEB'), trans('labels.cartoondeletesuccess'), '', $_SERVER['REMOTE_ADDR']);

            return Redirect::to("admin/cartoons")->with('success', trans('labels.level1cartoondeletesuccess'));
        }
        else
        {
            Helpers::createAudit($this->loggedInUser->user()->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_DELETE'), Config::get('databaseconstants.TBL_LEVEL1_CARTOON_ICON'), $id, Config::get('constant.AUDIT_ORIGIN_WEB'), trans('labels.somethingwrong'), '', $_SERVER['REMOTE_ADDR']);

            return Redirect::to("admin/cartoons")->with('error', trans('labels.commonerrormessage'));
        }
    }

    public function uploadView()
    {
        return view('admin.CartoonUploadScreen');
    }

    public function uploadCartoons()
    {
        $categoryMapped = array('C1'=>'Movies',
            'C2'=>'Television',
            'C3'=>'Comics Books',
            'C4'=>'Advertisements',
            'C5'=>'Games',
            'C6'=>'Others',
            );
        
        $files = Input::file('cartoons');
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
                    $pathOriginal = public_path($this->cartoonOriginalImageUploadPath . $fileName);
                    $pathThumb = public_path($this->cartoonThumbImageUploadPath . $fileName);

                    Image::make($file->getRealPath())->save($pathOriginal);
                    Image::make($file->getRealPath())->resize($this->cartoonThumbImageWidth, $this->cartoonThumbImageHeight)->save($pathThumb);

                    $cartoonIconDetail = [];
                    
                    //$nameArr = explode(".", $file->getClientOriginalName());
                    //array_pop($nameArr);
                    //$nameStr = implode(".", $nameArr);
                    //$arrUnderscore = explode("_", $nameStr);
                    //$beforeUnderscore = reset($arrUnderscore);
                    //$afterUnderscore = substr($nameStr, strpos($nameStr, "_") + 1);
                    
                    $nameArr = explode(".", $file->getClientOriginalName());
                    array_pop($nameArr);
                    $arrUnderscore = explode("_", $nameArr[0]);
                    $categoryName = end($arrUnderscore);

                    if(array_key_exists(strtoupper($categoryName), $categoryMapped)){
                    $actualcategoryName = $categoryMapped[strtoupper($categoryName)];
                    $new_filename = preg_replace('/_[^_.]*\./', '.', $fileName);
                    $iconName = str_replace('_',' ',substr($new_filename, 0, strrpos($new_filename, ".")));
//                    if (strpos($new_filename, '_') !== false) {
//                        //PERCENT SIGN FOUND
//                        $iconName = str_replace('_',' ', substr($new_filename, 0, strrpos($new_filename, "_")));                    
//                    }
//                    else{
//                        $iconName = substr($new_filename, 0, strrpos($new_filename, "."));
//                    }
                                        
                    $cartooniconCategoryId = $this->objLevel1CartoonActivity->getCartooniconCategoryName($actualcategoryName);
                    $cartoonIconDetail['ci_name'] = trim($iconName);;
                    $cartoonIconDetail['ci_category'] = $cartooniconCategoryId;
                    $cartoonIconDetail['ci_image'] = $fileName;
                    $cartoonIconDetail['deleted'] = 1;
                    $this->Level1CartoonIconRepository->saveLevel1CartoonIconDetail($cartoonIconDetail);

                    $uploadCount = $uploadCount + 1;
                    }
                }
            }
        }
        if($uploadCount == $filesCount)
        {
            return Redirect::to("admin/cartoons")->with('success', trans('labels.level1cartoonbulkuploadsuccess'));
        }
        else
        {
            return Redirect::to('admin/uploadCartoons')->withInput()->withErrors($validator);
        }
    }
    
    public function displayimage()
    {
        //$searchParamArray = Input::all();
        $level1cartoonicon = $this->Level1CartoonIconRepository->getLeve1CartoonIconfromUsers();
        Helpers::createAudit($this->loggedInUser->user()->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_READ'), $this->controller . "@index", $_SERVER['REQUEST_URI'], Config::get('constant.AUDIT_ORIGIN_WEB'), '', '', $_SERVER['REMOTE_ADDR']);
        $cartoonThumbPath = $this->cartoonThumbImageUploadPath;
        //return view('admin.viewLevel1CartoonIcon',compact('level1cartoonicon','cartoonThumbPath'));
        return view('admin.viewUserImage',compact('level1cartoonicon','cartoonThumbPath'));
    }
    
    public function deleteusericon($id)
    {
        $teenid = $_GET['tid'];
        $return = $this->Level1CartoonIconRepository->deleteLevel1CartoonuploadedbyUser($id);
        if ($return)
        {
            Helpers::createAudit($this->loggedInUser->user()->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_DELETE'), Config::get('databaseconstants.TBL_LEVEL1_CARTOON_ICON'), $id, Config::get('constant.AUDIT_ORIGIN_WEB'), trans('labels.cartoondeletesuccess'), '', $_SERVER['REMOTE_ADDR']);
            $teenDetail = Helpers::getEmailaddress($teenid);
            $emailTemplateContent = $this->TemplateRepository->getEmailTemplateDataByName(Config::get('constant.DELETE_IMAGE'));
                        //die($emailTemplateContent);
                        $data = array();
                        $replaceArray = array();
                        $replaceArray['toName'] = $teenDetail[0]->t_name;
                        
                        $content = $this->TemplateRepository->getEmailContent($emailTemplateContent->et_body, $replaceArray);
                        $data['subject'] = $emailTemplateContent->et_subject;
                        $data['toEmail'] = $teenDetail[0]->t_email;
                        $data['toName'] = $teenDetail[0]->t_name;
                        $data['content'] = $content;
                        Mail::send(['html' => 'emails.Template'], $data, function($message) use($data) {
                        $message->subject($data['subject']);
                        $message->to($data['toEmail'],$data['toName']);
                });
            return Redirect::to("admin/viewUserImage")->with('success', trans('labels.level1cartoondeletesuccess'));
        }
        else
        {
            Helpers::createAudit($this->loggedInUser->user()->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_DELETE'), Config::get('databaseconstants.TBL_LEVEL1_CARTOON_ICON'), $id, Config::get('constant.AUDIT_ORIGIN_WEB'), trans('labels.somethingwrong'), '', $_SERVER['REMOTE_ADDR']);

            return Redirect::to("admin/viewuserimage")->with('error', trans('labels.commonerrormessage'));
        }
    }
    
    public function deleteSelectedIcon()
    {
        $selectedIcons = Input::get('deleteIcons');
        if(isset($selectedIcons) && !empty($selectedIcons)){
            foreach($selectedIcons as $key=>$val){
                $return = $this->Level1CartoonIconRepository->deleteLevel1CartoonuploadedbyUser($key);
            }
            return Redirect::to("admin/viewUserImage")->with('success', trans('labels.level1cartoondeletesuccess'));                      
        }else{
            return Redirect::to("admin/viewUserImage")->with('error', 'Please select atleast image to delete');
        }        
    }
}
