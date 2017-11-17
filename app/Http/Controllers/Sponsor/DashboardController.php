<?php

namespace App\Http\Controllers\Sponsor;

use App\Http\Controllers\Controller;
use Auth;
use Input;
use Redirect;
use Config;
use File;
use Image;
use App\Services\Sponsors\Contracts\SponsorsRepository;
use Helpers;
use App\Http\Requests\AddSponsorActivityRequest;
use App\Services\Coupons\Contracts\CouponsRepository;
use App\Services\Teenagers\Contracts\TeenagersRepository;
use Excel;
use PDF;
use App\PaidComponent;
use App\DeductedCoins;
use App\Transactions;

class DashboardController extends Controller
{
    public function __construct(SponsorsRepository $sponsorsRepository, CouponsRepository $couponsRepository,TeenagersRepository $teenagersRepository)
    {
        $this->sponsorsRepository = $sponsorsRepository;
        $this->teenagersRepository = $teenagersRepository;
        $this->saOrigionalImagePath = Config::get('constant.SA_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->saThumbImagePath = Config::get('constant.SA_THUMB_IMAGE_UPLOAD_PATH');
        $this->saThumbImageHeight = Config::get('constant.SA_THUMB_IMAGE_HEIGHT');
        $this->saThumbImageWidth = Config::get('constant.SA_THUMB_IMAGE_WIDTH');
        $this->sponsorThumbImageUploadPath = Config::get('constant.SPONSOR_THUMB_IMAGE_UPLOAD_PATH');
        $this->couponOriginalImageUploadPath = Config::get('constant.COUPON_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->couponThumbImageUploadPath = Config::get('constant.COUPON_THUMB_IMAGE_UPLOAD_PATH');
        $this->couponThumbImageHeight = Config::get('constant.COUPON_THUMB_IMAGE_HEIGHT');
        $this->couponThumbImageWidth = Config::get('constant.COUPON_THUMB_IMAGE_WIDTH');
        $this->couponsRepository = $couponsRepository;
        $this->loggedInUser = Auth::guard('sponsor');
    }

    public function index()
    {
        if (Auth::guard('sponsor')->check()) {
            $loggedInUser = $this->loggedInUser;
            $sponsorId = $this->loggedInUser->user()->id;
            $saThumbImagePath = $this->saThumbImagePath;
            $activityDetail = $this->sponsorsRepository->getActiveSponsorActivityDetail($sponsorId);
            $coupons = $this->couponsRepository->getCouponsBySponsorId($sponsorId);
            $couponThumbImagePath = $this->couponThumbImageUploadPath;

            //Get total used credit history
            $usedCredits = $this->sponsorsRepository->getSponsorTotalUsedCredit($sponsorId);

            $objPaidComponent = new PaidComponent();
            $componentsData = $objPaidComponent->getPaidComponentsData('Enterprise Report');
            $objDeductedCoins = new DeductedCoins();
            $coins = 0;
            if (!empty($componentsData)) {
                $coins = $componentsData[0]->pc_required_coins;
            }

            $deductedCoinsDetail = $objDeductedCoins->getDeductedCoinsDetailByIdForLS($sponsorId,$componentsData[0]->id,4);
            $days = 0;
            if (!empty($deductedCoinsDetail)) {
                $days = Helpers::calculateRemaningDays($deductedCoinsDetail[0]->dc_end_date);
            }

            return view('sponsor.home', compact('activityDetail', 'saThumbImagePath', 'coupons', 'couponThumbImagePath', 'usedCredits', 'coins', 'days', 'sponsorId', 'loggedInUser'));
        } else {
            return view('sponsor.login');
        }
    }

    public function getCouponCompeting(){
        $couponId = Input::get('couponId');
        $coupons = $this->couponsRepository->checkConsumeCouponByTeen($couponId);
        $couponName = $this->couponsRepository->getCouponsById($couponId);
        $couponNameD = (!empty($couponName))? $couponName->cp_code:"Coupon Competing";
        
        return view('sponsor.couponCompeting', compact('coupons', 'couponNameD'));
    }
    
    public function addform()
    {
        $sponsorData = $this->sponsorsRepository->getSponsorById(Auth::sponsor()->get()->id);                               
        $sponsorAvailableCredit = $sponsorData->sp_credit;
        if($sponsorAvailableCredit == 0){
            return Redirect::to("sponsor/viewdashboard")->with('error', 'You don\'t have sufficient credit to add the activity. Please contact administrator for more detail.');
            exit;
        }
        $activityDetail = [];
        $uploadSAOrigionalPath = $this->saOrigionalImagePath;
        $uploadSAThumbPath = $this->saThumbImagePath;
        return view('sponsor.Addform', compact('activityDetail','uploadSAThumbPath','uploadSAOrigionalPath'));
    }
    
    public function edit($id)
    {
        $uploadSAOrigionalPath = $this->saOrigionalImagePath;
        $uploadSAThumbPath = $this->saThumbImagePath;
        $activityDetail = $this->sponsorsRepository->getActivityById($id);
        return view('sponsor.Addform', compact('activityDetail','uploadSAOrigionalPath','uploadSAThumbPath'));
    }
    
    public function save(AddSponsorActivityRequest $AddSponsorActivityRequest)
    {
        $response = '';
        $activityDetail = [];

        $activityDetail['id']   = e(input::get('id'));
        $hiddenLogo = e(input::get('hidden_logo'));
        $activityDetail['sa_sponsor_id'] = Auth::sponsor()->get()->id;
        $activityDetail['sa_image']    = $hiddenLogo;
        $activityDetail['sa_type']    = e(input::get('type'));
        $activityDetail['sa_name']   = e(input::get('sa_name'));
        $activityDetail['sa_apply_level'] =    e(input::get('level'));
        $activityDetail['sa_location']  = e(input::get('location'));
        $activityDetail['sa_image_href']  = e(input::get('image_href'));

        //$startdate = input::get('startdate');
        if (Input::get('startdate') != '') {
            $sdate = Input::get('startdate');
            $startdate = str_replace('/', '-', $sdate);
            $activityDetail['sa_start_date'] = date("Y-m-d", strtotime($startdate));
        }
        //$enddate = input::get('enddate');
        if (Input::get('enddate') != '') {
            $edate = Input::get('enddate');
            $enddate = str_replace('/', '-', $edate);
            $activityDetail['sa_end_date'] = date("Y-m-d", strtotime($enddate));
        }


        $credit = e(input::get('creditdeducted'));

        $totalCredit = Auth::sponsor()->get()->sp_credit;
        $availableCredit = $totalCredit - $credit;
        $arr[] = '';
        $arr['id'] = Auth::sponsor()->get()->id;
        $arr['sp_credit'] = $availableCredit;
        $activityDetail['sa_credit_used'] = $credit;

        //Check if enough credit available
        if($totalCredit < $credit){
            return Redirect::to("sponsor/viewdashboard")->with('error', 'You don\'t have sufficient credit to add the activity. Please contact administrator for more detail.');
            exit;
        }

        $activityDetail['deleted']   = e(input::get('status'));
        $file = Input::file('sa_image');
        if (!empty($file)) {
            $fileName = 'sponsoractivity_' . time() . '.' . $file->getClientOriginalExtension();
            $width = Image::make($file->getRealPath())->width();
            $height = Image::make($file->getRealPath())->height();
            if($width != 730 && $height != 50 && $activityDetail['sa_type'] == 1)
            {
                if($activityDetail['id'] > 0){
                    return Redirect::to("sponsor/edit/".$activityDetail['id'])->withErrors('Image width must be 730px and Height 50px')->withInput();
                    exit;
                }else{
                    return Redirect::to("sponsor/dataAdd")->withErrors('Image width must be 730px and Height 50px')->withInput();
                    exit;
                }
            }
            else{
                $pathOriginal = public_path($this->saOrigionalImagePath . $fileName);
                $pathThumb = public_path($this->saThumbImagePath . $fileName);
                Image::make($file->getRealPath())->save($pathOriginal);
                Image::make($file->getRealPath())->resize($this->saThumbImageWidth, $this->saThumbImageHeight)->save($pathThumb);

                if ($hiddenLogo != '') {
                    $imageOriginal = public_path($this->saOrigionalImagePath . $hiddenLogo);
                    $imageThumb = public_path($this->saThumbImagePath . $hiddenLogo);
                    File::delete($imageOriginal, $imageThumb);
                }
                $activityDetail['sa_image'] = $fileName;
            }
        }
        $response = $this->sponsorsRepository->saveSponsorActivityDetail($activityDetail);
        //$teenagers = $this->teenagersRepository->getAllActiveTeenagersForNotification();
        $type = '';
        if ($activityDetail['sa_type'] == 1) {
            $type = 'Ads';
        } else if ($activityDetail['sa_type'] == 2) {
            $type = 'Event';
        } else if ($activityDetail['sa_type'] == 3) {
            $type = 'Contest';
        }
//        foreach ($teenagers AS $key => $value) {
//            $message = 'New ' .$type.' "' .$activityDetail['sa_name'].'" has been added/updated in ProTeen!';
//            $return = Helpers::saveAllActiveTeenagerForSendNotifivation($value->id, $message);
//        }
        if($response)
        {
            if($activityDetail['id'] == 0){
                $setCredit = Helpers::setCredit($arr);
            }
        }
        $p_type = '';
        if ($activityDetail['sa_type'] == 1) {
            $p_type = 'Ads ProCoins';
        } else if ($activityDetail['sa_type'] == 2) {
            $p_type = 'Event ProCoins';
        } else if ($activityDetail['sa_type'] == 3) {
            $p_type = 'Contest ProCoins';
        }

        $return = Helpers::saveDeductedCoinsData(Auth::sponsor()->get()->id,4,$credit,$p_type, 0);

        return Redirect::to("sponsor/viewdashboard")->with('success','Activity has been updated successfully.');
        exit;
    }

    public function getCreditKey()
    {
        $configKey = Input::get('configKey');
        $credit = Helpers::getConfigValueByKeyForSponsor($configKey);
        return $credit;
    }

    public function inactive($id)
    {
        $response = $this->sponsorsRepository->inactiveRecord($id);
        if($response)
        {
            return Redirect::to("sponsor/viewdashboard");
        }
        else
        {
            return Redirect::to("sponsor/viewdashboard");
        }
    }
    
    public function addCoupon()
    {
        $couponOriginalImageUploadPath = $this->couponOriginalImageUploadPath;
        $sponsorData = $this->sponsorsRepository->getSponsorById(Auth::sponsor()->get()->id);                               
        $sponsorAvailableCredit = $sponsorData->sp_credit;
        if($sponsorAvailableCredit == 0){
            return Redirect::to("sponsor/viewdashboard")->with('error', 'You don\'t have sufficient credit to add the activity. Please contact administrator for more detail.');
        }
        return view('sponsor.AddCouponBulk',compact('couponOriginalImageUploadPath'));
    }
    
    public function couponBulkSave()
    {
        $response = '';
        $school = Input::file('coupon_bulk');
        $ext = $school->getClientOriginalExtension();
        if($ext == 'xls' or $ext == 'xlsx')
        {
            \Session::put('import', '0');
            Excel::selectSheetsByIndex(0)->load($school, function($reader) {               
                foreach ($reader->toArray() as $row) 
                {                    
                    if (isset($row['code']) && $row['code'] != '') 
                    {
                        \Session::put('import', '1'); 
                        //Get credit which is deduct from sponsor account 
                        $credit = Helpers::getConfigValueByKeyForSponsor('Coupon ProCoins');

                        //Get sponsor available credit
                        $sponsorData = $this->sponsorsRepository->getSponsorById(Auth::sponsor()->get()->id);

                        //Update credit to the sponsor table
                        $totalCredit = $sponsorData->sp_credit;
                        $remainingCredit = $totalCredit - $credit;

                        $arr['id'] = Auth::sponsor()->get()->id;
                        $arr['sp_credit'] = $remainingCredit;

                        //Check if enough credit available
                        if($totalCredit < $credit){
                            return Redirect::to("sponsor/viewdashboard")->with('error', 'You don\'t have sufficient credit to add the activity. Please contact administrator for more detail.');
                            exit;
                        }

                        $couponDetail = [];
                        $couponDetail['id'] = 0;
                        $couponDetail['cp_code'] = $row['code'];

                        $couponDetail['cp_description'] = $row['description'];
                        $couponDetail['cp_image'] = '';
                        $couponDetail['cp_sponsor'] = Auth::sponsor()->get()->id;
                        $couponDetail['cp_validfrom'] = $row['validfrom'];
                        $couponDetail['cp_validto'] = $row['validto'];
                        $couponDetail['cp_credit_used'] = $credit;
                        $couponDetail['cp_limit'] = $row['limit'];
                        $couponDetail['cp_used'] = 0;

                        $return = Helpers::saveDeductedCoinsData(Auth::sponsor()->get()->id,4,$credit,'Coupon ProCoins', 0);

                        $response = $this->couponsRepository->saveCouponDetail($couponDetail);
                        if($response){
//                            $teenagers = $this->teenagersRepository->getAllActiveTeenagersForNotification();
//                            foreach ($teenagers AS $key => $value) {
//                                $message = 'New coupons"' .$couponDetail['cp_code'].'" has been added into ProTeen!';
//                                $return = Helpers::saveAllActiveTeenagerForSendNotifivation($value->id, $message);
//                            }
                            $setCredit = Helpers::setCredit($arr);
                        }
                    }
                }

            });
            
            if(\Session::get('import') == 1){
               return Redirect::to("sponsor/viewdashboard")->with('success', 'Coupons imported successfully...');
               exit;
            }else{
               return Redirect::to("sponsor/addCoupon")->with('error','Invalid data in excel file...');
               exit; 
            }          
        } 
        else{
            return Redirect::to("sponsor/addCoupon")->with('error','Invalid file type...');
            exit;
        }
    }
    
    public function editCoupon($id)
    {
        $couponThumbImagePath = $this->couponThumbImageUploadPath;  
        $couponOriginalImagePath = $this->couponOriginalImageUploadPath;
        $coupon = $this->couponsRepository->getCouponsById($id);        
        return view('sponsor.CouponEdit', compact('coupon','couponThumbImagePath','couponOriginalImagePath'));
    }
    
    public function saveCoupon()
    {        
        $response = '';
        $couponDetail = [];

        $couponDetail['id']   = e(input::get('id'));
        $hiddenLogo     = e(input::get('hidden_logo'));
        $couponDetail['cp_sponsor'] = Auth::sponsor()->get()->id;
        $couponDetail['cp_image']    = $hiddenLogo;
        $couponDetail['cp_code']    = e(input::get('cp_code'));
        $couponDetail['cp_description']   = e(input::get('cp_description'));
        $couponDetail['cp_limit'] =    e(input::get('cp_limit'));
        //$startdate = input::get('cp_validfrom');
        if (Input::get('cp_validfrom') != '') {
            $sdate = Input::get('cp_validfrom');
            $startdate = str_replace('/', '-', $sdate);
            $couponDetail['cp_validfrom'] = date("Y-m-d", strtotime($startdate));
        }
//        if (isset($startdate) && $startdate != '') {
//           list($month, $day, $year) = explode('/', date("m/d/Y", strtotime($startdate)));
//           $couponDetail['cp_validfrom'] = $year . "-" . $month . "-" . $day;
//        }
        //$enddate = input::get('cp_validto');
        if (Input::get('cp_validto') != '') {
            $edate = Input::get('cp_validto');
            $enddate = str_replace('/', '-', $edate);
            $couponDetail['cp_validto'] = date("Y-m-d", strtotime($enddate));
        }
        $couponDetail['deleted']   = e(input::get('status'));
        $file = Input::file('cp_image');
        if (!empty($file)) {
            $fileName = 'coupon_' . time() . '.' . $file->getClientOriginalExtension();
            $width = Image::make($file->getRealPath())->width();
            $height = Image::make($file->getRealPath())->height();
            if($width != 255 && $height != 150)
            {
                return Redirect::to("sponsor/editCoupon/".$couponDetail['id'])->withErrors('Image width must be 255px and Height 150px')->withInput();
                exit;
            }
            else{
                $pathOriginal = public_path($this->couponOriginalImageUploadPath . $fileName);
                $pathThumb = public_path($this->couponThumbImageUploadPath . $fileName);
                Image::make($file->getRealPath())->save($pathOriginal);
                Image::make($file->getRealPath())->resize($this->couponThumbImageWidth, $this->couponThumbImageHeight)->save($pathThumb);

                if ($hiddenLogo != '') {
                    $imageOriginal = public_path($this->couponOriginalImageUploadPath . $hiddenLogo);
                    $imageThumb = public_path($this->couponThumbImageUploadPath . $hiddenLogo);
                    File::delete($imageOriginal, $imageThumb);
                }
                $couponDetail['cp_image'] = $fileName;
            }
        }
        $this->couponsRepository->saveCouponDetail($couponDetail);
        return Redirect::to("sponsor/viewdashboard")->with('success','Coupon has been updated successfully');
        exit;
//        if($response)
//        {
//            return Redirect::to("sponsor/viewdashboard")->with('success',trans('labels.genericupdatesuccess'));
//        }
//        else
//        {
//            return Redirect::to("sponsor/viewdashboard")->with('error', trans('labels.commonerrormessage'));
//        }
    }

    public function purchaseCredit()
    {
        return view('sponsor.purchaseCredit');
    }

    public function exportPDF() {
        if (Auth::sponsor()->check()) {
            $response = [];
            $sponsorId = Auth::sponsor()->get()->id;
            $saThumbImagePath = $this->saThumbImagePath;
            $activityDetail = $this->sponsorsRepository->getActiveSponsorActivityDetail($sponsorId);
            $coupons = $this->couponsRepository->getCouponsBySponsorId($sponsorId);
            $couponThumbImagePath = $this->couponThumbImageUploadPath;
            $objDeductedCoins = new DeductedCoins();
            $objTransactions = new Transactions();

            //Get total used credit history
            $usedCredits = $this->sponsorsRepository->getSponsorTotalUsedCredit($sponsorId);

            $response['saThumbImagePath'] = $saThumbImagePath;
            $response['activityDetail'] = $activityDetail;
            $response['coupons'] = $coupons;
            $response['couponThumbImagePath'] = $couponThumbImagePath;
            $response['usedCredits'] = $usedCredits;

            $couponsData = [];
            foreach ($coupons AS $key => $value) {
                $couponsDetail = [];
                $couponId = $value->id;
                $coupons = $this->couponsRepository->checkConsumeCouponByTeen($couponId);
                $couponName = $this->couponsRepository->getCouponsById($couponId);
                $couponNameD = (!empty($couponName))? $couponName->cp_code:"Coupon Competing";
                $couponsDetail['coupons'] = $coupons;
                $couponsDetail['couponNameD'] = $couponNameD;
                $couponsData[] = $couponsDetail;
            }

            $logo = Auth::sponsor()->get()->sp_logo;
            $image = '';
            if (!empty($logo)) {
                if ($logo != '' && file_exists($this->sponsorThumbImageUploadPath . $logo)) {
                    $image = $this->sponsorThumbImageUploadPath . $logo;
                } else {
                    $image = $this->sponsorThumbImageUploadPath . 'proteen-logo.png';
                }
            }
            $deductedCoinsDetail = $objDeductedCoins->getAllDeductedCoinsDetail($sponsorId,4);
            $transactionDetail = $objTransactions->getTransactionsDetail($sponsorId,4);

            $response['logo'] = $image;
            $response['couponsData'] = $couponsData;
            $response['deductedCoinsDetail'] = $deductedCoinsDetail;
            $response['transactionDetail'] = $transactionDetail;


            $pdf=PDF::loadView('sponsor.ExportSponsorDetailPDF',$response);
            return $pdf->stream('Sponsor.pdf');
        }else{
            return view('sponsor.login');
        }
    }

    function purchasedCoinsToViewReport() {
        if (Auth::sponsor()->check()) {
            $sponsorId = Input::get('sponsorId');
            $objPaidComponent = new PaidComponent();
            $componentsData = $objPaidComponent->getPaidComponentsData('Enterprise Report');
            $coins = $componentsData[0]->pc_required_coins;
            $objDeductedCoins = new DeductedCoins();

            $deductedCoinsDetail = $objDeductedCoins->getDeductedCoinsDetailByIdForLS($sponsorId,$componentsData[0]->id,4);
            $days = 0;
            if (!empty($deductedCoinsDetail)) {
                $days = Helpers::calculateRemaningDays($deductedCoinsDetail[0]->dc_end_date);
            }
            if ($days == 0) {
                $deductedCoins = $coins;
                $sponsorData = $this->sponsorsRepository->getSponsorDataForCoinsDetail($sponsorId);
                if (!empty($sponsorData)) {
                    $coins = $sponsorData['sp_credit']-$coins;
                }
                $result = $this->sponsorsRepository->updateSponsorCoinsDetail($sponsorId, $coins);
                $return = Helpers::saveDeductedCoinsData($sponsorId,4,$deductedCoins,'Enterprise Report',0);
            }
            return "1";
            exit;
        }
        return view('sponsor.Login'); exit;
    }
}