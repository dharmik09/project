<?php

namespace App\Http\Controllers\Admin;

use Auth;
use Input;
use File;
use Image;
use Config;
use Helpers;
use Redirect;
use App\Coupons;
use Illuminate\Pagination\Paginator;
use App\Http\Controllers\Controller;
use App\Http\Requests\CouponRequest;
use App\Http\Requests\CouponBulkRequest;
use App\Services\Coupons\Contracts\CouponsRepository;
use App\Services\FileStorage\Contracts\FileStorageRepository;

class CouponManagementController extends Controller {

    public function __construct(FileStorageRepository $fileStorageRepository, CouponsRepository $couponsRepository) {
        //$this->middleware('auth.admin');
        $this->fileStorageRepository = $fileStorageRepository;
        $this->objCoupons = new Coupons();
        $this->couponsRepository = $couponsRepository;
        $this->couponOriginalImageUploadPath = Config::get('constant.COUPON_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->couponThumbImageUploadPath = Config::get('constant.COUPON_THUMB_IMAGE_UPLOAD_PATH');
        $this->couponThumbImageHeight = Config::get('constant.COUPON_THUMB_IMAGE_HEIGHT');
        $this->couponThumbImageWidth = Config::get('constant.COUPON_THUMB_IMAGE_WIDTH');
        $this->controller = 'CouponManagementController';
        $this->loggedInUser = Auth::guard('admin');
    }

    public function index() {
        $uploadCouponThumbPath = $this->couponThumbImageUploadPath;
        $coupons = $this->couponsRepository->getAllCoupons();
        Helpers::createAudit($this->loggedInUser->user()->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_READ'), $this->controller . "@index", $_SERVER['REQUEST_URI'], Config::get('constant.AUDIT_ORIGIN_WEB'), '', '', $_SERVER['REMOTE_ADDR']);

        return view('admin.ListCoupons', compact('coupons', 'uploadCouponThumbPath'));
    }

    public function add() {
        $couponDetail = [];
        Helpers::createAudit($this->loggedInUser->user()->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_READ'), $this->controller . "@add", $_SERVER['REQUEST_URI'], Config::get('constant.AUDIT_ORIGIN_WEB'), '', '', $_SERVER['REMOTE_ADDR']);

        return view('admin.EditCoupon', compact('couponDetail'));
    }

    public function edit($id) {
        $couponDetail = $this->objCoupons->find($id);
        $uploadCouponThumbPath = $this->couponThumbImageUploadPath;
        Helpers::createAudit($this->loggedInUser->user()->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_READ'), $this->controller . "@edit", $_SERVER['REQUEST_URI'], Config::get('constant.AUDIT_ORIGIN_WEB'), '', '', $_SERVER['REMOTE_ADDR']);

        return view('admin.EditCoupon', compact('couponDetail', 'uploadCouponThumbPath'));
    }

    public function save(CouponRequest $couponRequest) {
        $couponDetail = [];

        $couponDetail['id'] = e(input::get('id'));
        $hiddenLogo = e(input::get('hidden_logo'));
        $couponDetail['cp_image'] = $hiddenLogo;
        $couponDetail['cp_code'] = e(input::get('cp_code'));
        $couponDetail['cp_sponsor'] = e(input::get('cp_sponsor'));
        if (Input::get('cp_validfrom') != '') {
            $Validfrom = Input::get('cp_validfrom');
            $ValidfromDate = str_replace('/', '-', $Validfrom);
            $couponDetail['cp_validfrom'] = date("Y-m-d", strtotime($ValidfromDate));            
        }
        if (Input::get('cp_validto') != '') {
            $validTo = Input::get('cp_validto');
            $validToDate = str_replace('/', '-', $validTo);
            $couponDetail['cp_validto'] = date("Y-m-d", strtotime($validToDate));            
        }
        //$couponDetail['cp_validfrom'] = date('Y-d-m', strtotime(e(input::get('cp_validfrom'))));
        //$couponDetail['cp_validto'] = e(input::get('cp_validto'));
        $couponDetail['deleted'] = e(input::get('deleted'));

        if (Input::file()) {
            $file = Input::file('cp_image');
            if (!empty($file)) {
                //Check image valid extension 
                $validationPass = Helpers::checkValidImageExtension($file);
                if($validationPass)
                {
                    $fileName = 'coupon_' . time() . '.' . $file->getClientOriginalExtension();
                    $width = Image::make($file->getRealPath())->width();
                    $height = Image::make($file->getRealPath())->height();
                    if ($width != 255 && $height != 150) {
                        if ($couponDetail['id'] > 0) {
                            return Redirect::to("admin/edit-coupon/" . $couponDetail['id'])->withErrors('Image width must be 255px and Height 150px')->withInput();
                            exit;
                        } else {
                            return Redirect::to("admin/add-coupon")->withErrors('Image width must be 255px and Height 150px')->withInput();
                            exit;
                        }
                    }
                    $pathOriginal = public_path($this->couponOriginalImageUploadPath . $fileName);
                    $pathThumb = public_path($this->couponThumbImageUploadPath . $fileName);

                    Image::make($file->getRealPath())->save($pathOriginal);
                    Image::make($file->getRealPath())->resize($this->couponThumbImageWidth, $this->couponThumbImageHeight)->save($pathThumb);

                    if ($hiddenLogo != '') {
                        $originalImageDelete = $this->fileStorageRepository->deleteFileToStorage($hiddenLogo, $this->couponOriginalImageUploadPath, "s3");
                        $thumbImageDelete = $this->fileStorageRepository->deleteFileToStorage($hiddenLogo, $this->couponThumbImageUploadPath, "s3");
                    }
                    //Uploading on AWS
                    $originalImage = $this->fileStorageRepository->addFileToStorage($fileName, $this->couponOriginalImageUploadPath, $pathOriginal, "s3");
                    $thumbImage = $this->fileStorageRepository->addFileToStorage($fileName, $this->couponThumbImageUploadPath, $pathThumb, "s3");
                    //Deleting Local Files
                    \File::delete($this->couponOriginalImageUploadPath . $fileName);
                    \File::delete($this->couponThumbImageUploadPath . $fileName);
                    $couponDetail['cp_image'] = $fileName;
                }
            }
        }

        $response = $this->couponsRepository->saveCouponDetail($couponDetail);
        if ($response) {
            Helpers::createAudit($this->loggedInUser->user()->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_UPDATE'), Config::get('databaseconstants.TBL_COUPONS'), $response, Config::get('constant.AUDIT_ORIGIN_WEB'), trans('labels.couponupdatesuccess'), serialize($couponDetail), $_SERVER['REMOTE_ADDR']);

            return Redirect::to("admin/coupons")->with('success', trans('labels.couponupdatesuccess'));
        } else {
            Helpers::createAudit($this->loggedInUser->user()->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_UPDATE'), Config::get('databaseconstants.TBL_COUPONS'), $response, Config::get('constant.AUDIT_ORIGIN_WEB'), trans('labels.somethingwrong'), serialize($couponDetail), $_SERVER['REMOTE_ADDR']);

            return Redirect::to("admin/coupons")->with('error', trans('labels.commonerrormessage'));
        }
    }

    public function delete($id) {
        $return = $this->couponsRepository->deleteCoupon($id);
        if ($return) {
            Helpers::createAudit($this->loggedInUser->user()->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_DELETE'), Config::get('databaseconstants.TBL_COUPONS'), $id, Config::get('constant.AUDIT_ORIGIN_WEB'), trans('labels.coupondeletesuccess'), '', $_SERVER['REMOTE_ADDR']);

            return Redirect::to("admin/coupons")->with('success', trans('labels.coupondeletesuccess'));
        } else {
            Helpers::createAudit($this->loggedInUser->user()->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_DELETE'), Config::get('databaseconstants.TBL_COUPONS'), $id, Config::get('constant.AUDIT_ORIGIN_WEB'), trans('labels.somethingwrong'), '', $_SERVER['REMOTE_ADDR']);

            return Redirect::to("admin/coupons")->with('error', trans('labels.commonerrormessage'));
        }
    }

    public function addbulk() {
        return view('admin.AddCouponBulk');
    }

    public function savebulkdata(CouponBulkRequest $CouponBulkRequest) {
        $coupon = Input::file('cp_bulk');
        $couponfile = fopen($coupon, 'r');
        $i = 0;
        while (($data = fgetcsv($couponfile, 1000, ",")) !== FALSE) {
            $num = count($data);
            if ($i > 0) {
                for ($c = 0; $c < $num; $c++) {
                    $result[$c] = $data[$c];
                }
                $couponDetail['cp_name'] = $result[0];
                $couponDetail['cp_code'] = $result[1];
                $couponDetail['cp_description'] = $result[2];
                $couponDetail['cp_profession'] = $result[3];
                $couponDetail['cp_level'] = $result[4];
                $couponDetail['cp_sponsor'] = $result[5];
                $couponDetail['cp_validfrom'] = $result[6];
                $couponDetail['cp_validto'] = $result[7];
                $response = $this->couponsRepository->saveCouponBulkDetail($couponDetail);
            }
            $i++;
        }
        if ($response) {
            return Redirect::to("admin/coupons")->with('success', trans('labels.couponupdatesuccess'));
        } else {
            return Redirect::to("admin/coupons")->with('error', trans('labels.couponerrormessage'));
        }
        fclose($couponfile);
    }
    
    public function couponUsage($couponId)
    {
        $couponUsage =  $this->couponsRepository->checkConsumeCouponByTeen($couponId);
        $couponName = $this->couponsRepository->getCouponsById($couponId);
        return view('admin.ListCouponUsage', compact('couponUsage', 'couponName'));
        
    }
}
