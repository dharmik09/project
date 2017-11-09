<?php

namespace App\Http\Controllers\Admin;

use Auth;
use Input;
use Config;
use File;
use Image;
use Request;
use Helpers;
use Redirect;
use App\Baskets;
use Illuminate\Pagination\Paginator;
use App\Http\Controllers\Controller;
use App\Http\Requests\BasketRequest;
use App\Services\Baskets\Contracts\BasketsRepository;
use App\Services\Professions\Contracts\ProfessionsRepository;
use App\Services\FileStorage\Contracts\FileStorageRepository;

class BasketManagementController extends Controller
{

    public function __construct(FileStorageRepository $fileStorageRepository, BasketsRepository $BasketsRepository, ProfessionsRepository $ProfessionsRepository)
    {
        $this->objBaskets = new Baskets();
        $this->BasketsRepository = $BasketsRepository;
        $this->ProfessionsRepository = $ProfessionsRepository;
        $this->fileStorageRepository = $fileStorageRepository;
        $this->basketOriginalImageUploadPath = Config::get('constant.BASKET_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->basketThumbImageUploadPath = Config::get('constant.BASKET_THUMB_IMAGE_UPLOAD_PATH');
        $this->basketThumbImageHeight = Config::get('constant.BASKET_THUMB_IMAGE_HEIGHT');
        $this->basketThumbImageWidth = Config::get('constant.BASKET_THUMB_IMAGE_WIDTH');
        $this->basketVideoUploadPath = Config::get('constant.BASKET_ORIGINAL_VIDEO_UPLOAD_PATH');
        $this->controller = 'BasketManagementController';
        $this->loggedInUser = Auth::guard('admin');
    }
    public function index()
    {
        $uploadBasketThumbPath = $this->basketThumbImageUploadPath;
        $baskets = $this->BasketsRepository->getAllBaskets();
        Helpers::createAudit($this->loggedInUser->user()->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_READ'), $this->controller . "@index", $_SERVER['REQUEST_URI'], Config::get('constant.AUDIT_ORIGIN_WEB'), '', '', $_SERVER['REMOTE_ADDR']);
        return view('admin.ListBasket', compact('baskets', 'uploadBasketThumbPath'));
    }

    public function add()
    {
        $basketDetail = [];
        Helpers::createAudit($this->loggedInUser->user()->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_READ'), $this->controller . "@add", $_SERVER['REQUEST_URI'], Config::get('constant.AUDIT_ORIGIN_WEB'), '', '', $_SERVER['REMOTE_ADDR']);
        return view('admin.EditBasket',compact('basketDetail'));
    }

    public function edit($id)
    {
        $basketDetail = $this->objBaskets->find($id);
        $uploadBasketThumbPath = $this->basketThumbImageUploadPath;
        $uploadVideoPath = $this->basketVideoUploadPath;
        Helpers::createAudit($this->loggedInUser->user()->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_READ'), $this->controller . "@edit", $_SERVER['REQUEST_URI'], Config::get('constant.AUDIT_ORIGIN_WEB'), '', '', $_SERVER['REMOTE_ADDR']);
        return view('admin.EditBasket',compact('basketDetail' , 'uploadBasketThumbPath', 'uploadVideoPath'));
    }

    public function save(BasketRequest $BasketRequest)
    {
        $basketDetail = [];

        $basketDetail['id']   = e(input::get('id'));
        $hiddenLogo     = e(input::get('hidden_logo'));
        $basketDetail['b_logo']    = $hiddenLogo;
        $basketDetail['b_name']   = e(input::get('b_name'));
        //$basketDetail['b_intro']   = input::get('b_intro');
        $basketDetail['deleted']   = e(input::get('deleted'));
        $hiddenVideo     = e(input::get('hidden_video'));
        $videotype = e(Input::get('b_video_type'));
        $postData['pageRank'] = Input::get('pageRank');
        if ($videotype == '1')
        {
           $basketDetail['b_video'] = $hiddenVideo;
        }
        else if($videotype == '2')
        {
           $basketDetail['b_video'] = trim(e(input::get('youtube')));
           if ($hiddenVideo != '')
           {
                $videoOriginal = public_path($this->basketVideoUploadPath . $hiddenVideo);
                File::delete($videoOriginal);
           }
        }
        else if($videotype == '3')
        {
           $basketDetail['b_video'] = e(input::get('vimeo'));
           if ($hiddenVideo != '')
           {
                $videoOriginal = public_path($this->basketVideoUploadPath . $hiddenVideo);
                File::delete($videoOriginal);
           }
        }
        $basketDetail['b_video_type'] = $videotype;

        if (Input::file())
        {
            $file = Input::file('b_logo');
            $videoFile = Input::file('normal');
            if(!empty($file))
            {
                //Check image valid extension 
                $validationPass = Helpers::checkValidImageExtension($file);
                if($validationPass)
                {
                    $fileName = 'basket_' . time() . '.' . $file->getClientOriginalExtension();
                    $pathOriginal = public_path($this->basketOriginalImageUploadPath . $fileName);
                    $pathThumb = public_path($this->basketThumbImageUploadPath . $fileName);

                    Image::make($file->getRealPath())->save($pathOriginal);
                    Image::make($file->getRealPath())->resize($this->basketThumbImageWidth, $this->basketThumbImageHeight)->save($pathThumb);

                    if ($hiddenLogo != '')
                    {
                        $originalImageDelete = $this->fileStorageRepository->deleteFileToStorage($hiddenLogo, $this->basketOriginalImageUploadPath, "s3");
                        $thumbImageDelete = $this->fileStorageRepository->deleteFileToStorage($hiddenLogo, $this->basketThumbImageUploadPath, "s3");
                    }

                    //Uploading on AWS
                    $originalImage = $this->fileStorageRepository->addFileToStorage($fileName, $this->basketOriginalImageUploadPath, $pathOriginal, "s3");
                    $thumbImage = $this->fileStorageRepository->addFileToStorage($fileName, $this->basketThumbImageUploadPath, $pathThumb, "s3");
                    
                    \File::delete($this->basketOriginalImageUploadPath . $fileName);
                    \File::delete($this->basketThumbImageUploadPath . $fileName);
                    $basketDetail['b_logo'] = $fileName;
                }                
            }
            if(!empty($videoFile))
            {
                $fileName = 'basket_' . time() . '.' . $videoFile->getClientOriginalExtension();
                $pathOriginal = public_path($this->basketVideoUploadPath);

                $videoFile->move($pathOriginal, $fileName);
                if ($hiddenVideo != '')
                {
                    $videoOriginal = public_path($this->basketVideoUploadPath . $hiddenVideo);
                    File::delete($videoOriginal);
                }

                $basketDetail['b_video'] = $fileName;
            }
        }
        $response = $this->BasketsRepository->saveBasketDetail($basketDetail);
        if($response)
        {
            Helpers::createAudit($this->loggedInUser->user()->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_UPDATE'), Config::get('databaseconstants.TBL_BASKETS'), $response, Config::get('constant.AUDIT_ORIGIN_WEB'),  trans('labels.basketupdatesuccess'), serialize($basketDetail), $_SERVER['REMOTE_ADDR']);
            return Redirect::to("admin/baskets".$postData['pageRank'])->with('success',trans('labels.basketupdatesuccess'));
        }
        else
        {
            Helpers::createAudit($this->loggedInUser->user()->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_UPDATE'), Config::get('databaseconstants.TBL_BASKETS'), $response, Config::get('constant.AUDIT_ORIGIN_WEB'),  trans('labels.somethingwrong'), serialize($basketDetail), $_SERVER['REMOTE_ADDR']);
            return Redirect::to("admin/baskets".$postData['pageRank'])->with('error', trans('labels.commonerrormessage'));
        }
    }

    public function delete($id)
    {
        $checkBasketExist = $this->ProfessionsRepository->checkForBasket($id);
        if(isset($checkBasketExist) && !empty($checkBasketExist))
        {
            return Redirect::to("admin/baskets")->with('error', trans('labels.basketassociatedwithprofession'));
        }
        else if(empty($checkBasketExist))
        {
            $return = $this->BasketsRepository->deleteBasket($id);
            if ($return)
            {
                Helpers::createAudit($this->loggedInUser->user()->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_DELETE'), Config::get('databaseconstants.TBL_BASKETS'), $id, Config::get('constant.AUDIT_ORIGIN_WEB'), trans('labels.basketdeletesuccess'), '', $_SERVER['REMOTE_ADDR']);
                return Redirect::to("admin/baskets")->with('success', trans('labels.basketsdeletesuccess'));
            }
            else
            {
                Helpers::createAudit($this->loggedInUser->user()->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_DELETE'), Config::get('databaseconstants.TBL_BASKETS'), $id, Config::get('constant.AUDIT_ORIGIN_WEB'), trans('labels.somethingwrong'), '', $_SERVER['REMOTE_ADDR']);
                return Redirect::to("admin/baskets")->with('error', trans('labels.commonerrormessage'));
            }
        }
        else
        {
            return Redirect::to("admin/baskets")->with('error', trans('labels.commonerrormessage'));
        }
    }

}
