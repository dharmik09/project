<?php

namespace App\Http\Controllers\Admin;

use Auth;
use Input;
use File;
use Image;
use Config;
use Helpers;
use Redirect;
use Illuminate\Pagination\Paginator;
use App\GenericAds;
use App\Http\Controllers\Controller;
use App\Services\Genericads\Contracts\GenericadsRepository;
use App\Services\FileStorage\Contracts\FileStorageRepository;

class GenericAdsManagementController extends Controller
{
    public function __construct(FileStorageRepository $fileStorageRepository, GenericadsRepository $GenericadsRepository)
    {
        //$this->middleware('auth.admin');
        $this->objGeneric = new GenericAds();
        $this->GenericadsRepository = $GenericadsRepository;
        $this->fileStorageRepository = $fileStorageRepository;
        $this->genericOrigionalImagePath = Config::get('constant.GENERIC_ADS_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->genericThumbImagePath = Config::get('constant.GENERIC_ADS_THUMB_IMAGE_UPLOAD_PATH');
        $this->genericThumbImageHeight = Config::get('constant.GENERIC_THUMB_IMAGE_HEIGHT');
        $this->genericThumbImageWidth = Config::get('constant.GENERIC_THUMB_IMAGE_WIDTH');
        $this->controller = 'GenericAdsManagementController';
        $this->loggedInUser = Auth::guard('admin');
    }

    public function index()
    {
        $genericThumbImagePath = $this->genericThumbImagePath;
        $generics = $this->GenericadsRepository->getAllGeneric();
        return view('admin.ListGeneric', compact('generics', 'genericThumbImagePath'));
    }

    public function add()
    {
        $genericDetail =[];
        return view('admin.EditGeneric', compact('genericDetail'));
    }

    public function edit($id)
    {
        $genericDetail = $this->objGeneric->find($id);
        $genericThumbImagePath = $this->genericThumbImagePath;
        return view('admin.EditGeneric', compact('genericDetail', 'genericThumbImagePath'));
    }

    public function save()
    {
        $genericDetail = [];

        $genericDetail['id']   = e(input::get('id'));
        $hiddenLogo     = e(input::get('hidden_logo'));
        $genericDetail['ga_image']    = $hiddenLogo;
        $genericDetail['ga_name']   = e(input::get('ga_name'));
        //$genericDetail['ga_apply_level'] =    e(input::get('ga_apply_level'));
        if (Input::get('ga_start_date') != '') {
            $sdate = Input::get('ga_start_date');
            $startdate = str_replace('/', '-', $sdate);
            $genericDetail['ga_start_date'] = date("Y-m-d", strtotime($startdate));            
        }
        if (Input::get('ga_end_date') != '') {
            $edate = Input::get('ga_end_date');
            $enddate = str_replace('/', '-', $edate);
            $genericDetail['ga_end_date'] = date("Y-m-d", strtotime($enddate));            
        }
        $genericDetail['deleted']   = e(input::get('deleted'));
        if (Input::file())
        {
            $file = Input::file('ga_image');
            if(!empty($file))
            {
                //Check image valid extension 
                $validationPass = Helpers::checkValidImageExtension($file);
                if($validationPass)
                {                                
                    $fileName = 'generic_' . time() . '.' . $file->getClientOriginalExtension();
                    $pathOriginal = public_path($this->genericOrigionalImagePath . $fileName);
                    $pathThumb = public_path($this->genericThumbImagePath . $fileName);

                    Image::make($file->getRealPath())->save($pathOriginal);
                    Image::make($file->getRealPath())->resize($this->genericThumbImageWidth, $this->genericThumbImageHeight)->save($pathThumb);

                    if ($hiddenLogo != '')
                    {
                        $originalImageDelete = $this->fileStorageRepository->deleteFileToStorage($hiddenLogo, $this->genericOrigionalImagePath, "s3");
                        $thumbImageDelete = $this->fileStorageRepository->deleteFileToStorage($hiddenLogo, $this->genericThumbImagePath, "s3");
                    }

                    //Uploading on AWS
                    $originalImage = $this->fileStorageRepository->addFileToStorage($fileName, $this->genericOrigionalImagePath, $pathOriginal, "s3");
                    $thumbImage = $this->fileStorageRepository->addFileToStorage($fileName, $this->genericThumbImagePath, $pathThumb, "s3");
                    
                    \File::delete($this->genericOrigionalImagePath . $fileName);
                    \File::delete($this->genericThumbImagePath . $fileName);
                    $genericDetail['ga_image'] = $fileName;
                }
            }
        }
        $response = $this->GenericadsRepository->saveGenericDetail($genericDetail);
        if($response)
        {
            return Redirect::to("admin/genericAds")->with('success',trans('labels.genericupdatesuccess'));
        }
        else
        {
            return Redirect::to("admin/genericAds")->with('error', trans('labels.commonerrormessage'));
        }
    }

    public function delete($id)
    {
        $return = $this->GenericadsRepository->deleteGeneric($id);
        if($return)
        {
            return Redirect::to("admin/genericAds")->with('success', trans('labels.genericdeletesuccess'));
        }
        else
        {
            return Redirect::to("admin/genericAds")->with('error', trans('labels.commonerrormessage'));
        }
    }
}