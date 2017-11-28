<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Http\Requests\SchoolLoginRequest;
use Auth;
use Image;
use Config;
use Helpers;
use Input;
use Response;
use Mail;
use Redirect;
use Illuminate\Http\Request;
use App\Transactions;
use App\Schools;
use App\Templates;
use App\Services\Schools\Contracts\SchoolsRepository;
use App\PaidComponent;
use App\DeductedCoins;
use App\Http\Requests\SchoolProfileUpdateRequest;

class UpdateProfileController extends Controller {

    public function __construct(SchoolsRepository $SchoolsRepository) {
        $this->middleware('auth.school');
        $this->objSchools = new Schools();
        $this->SchoolsRepository = $SchoolsRepository;
        $this->contactpersonOriginalImageUploadPath = Config::get('constant.CONTACT_PERSON_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->contactpersonThumbImageUploadPath = Config::get('constant.CONTACT_PERSON_THUMB_IMAGE_UPLOAD_PATH');
        $this->contactpersonThumbImageHeight = Config::get('constant.CONTACT_PERSON_THUMB_IMAGE_HEIGHT');
        $this->contactpersonThumbImageWidth = Config::get('constant.CONTACT_PERSON_THUMB_IMAGE_WIDTH');
        $this->schoolOriginalImageUploadPath = Config::get('constant.SCHOOL_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->schoolThumbImageUploadPath = Config::get('constant.SCHOOL_THUMB_IMAGE_UPLOAD_PATH');
        $this->schoolThumbImageHeight = Config::get('constant.SCHOOL_THUMB_IMAGE_HEIGHT');
        $this->schoolThumbImageWidth = Config::get('constant.SCHOOL_THUMB_IMAGE_WIDTH');
    }

    public function updateProfile() {

        if (Auth::school()->check()) {
            $id = Auth::school()->get()->id;
            $user = $this->SchoolsRepository->getSchoolById($id);
            //$countries = Helpers::getCountries();
            //$cities =  Helpers::getCities();
            //$states =  Helpers::getStates();
            $schoolOriginalImagePath = Config::get('constant.SCHOOL_ORIGINAL_IMAGE_UPLOAD_PATH');
            $contactpersonOriginalImagePath = Config::get('constant.CONTACT_PERSON_ORIGINAL_IMAGE_UPLOAD_PATH');

            return view('school.UpdateProfile', compact('user', 'contactpersonOriginalImagePath', 'schoolOriginalImagePath', 'countries', 'cities', 'states'));
        }
        return view('school.Login');
    }

    public function progress($cid = 0) {
        $schoolid = Auth::school()->get()->id;
        if(empty($cid) && $cid == 0 && $cid == '')
        {   
            $classid = $this->SchoolsRepository->getFirstClassDetail($schoolid);
            $cid = (isset($classid->t_class) && $classid->t_class != '')?$classid->t_class:'';
        }
        else {
            $cid = $cid;
        }
        if(isset($cid) && !empty($cid))
        {
            $classDetails = $this->SchoolsRepository->getClassDetail($schoolid);
            $teenDetailsForLevel1 = $this->SchoolsRepository->getStudentForLevel1($schoolid, $cid);
            $teenDetailsForLevel2 = $this->SchoolsRepository->getStudentForLevel2($schoolid, $cid);
            $teenDetailsForLevel3 = $this->SchoolsRepository->getStudentForLevel3($schoolid, $cid);
            $teenDetailsForLevel4 = $this->SchoolsRepository->getStudentForLevel4($schoolid, $cid);
            $professionAttempted = $this->SchoolsRepository->getAttemptedProfession($schoolid, $cid);
            $objPaidComponent = new PaidComponent();
            $componentsData = $objPaidComponent->getPaidComponentsData('School Report');
            $objDeductedCoins = new DeductedCoins();
            $coins = 0;
            if (!empty($componentsData)) {
                $coins = $componentsData[0]->pc_required_coins;
            }
            $deductedCoinsDetail = $objDeductedCoins->getDeductedCoinsDetailByIdForLS($schoolid,$componentsData[0]->id,3);
            $days = 0;
            if (!empty($deductedCoinsDetail)) {
                $days = Helpers::calculateRemaningDays($deductedCoinsDetail[0]->dc_end_date);
            }
        }
        else
        {
            return Redirect::to("school/dashboard")->with('error', 'No data found');
            exit;
        }

        return view('school.Progress', compact('classDetails', 'schoolid', 'cid', 'teenDetailsForLevel1', 'teenDetailsForLevel2', 'teenDetailsForLevel3', 'teenDetailsForLevel4','professionAttempted','days','coins'));
    }

    public function saveProfile(SchoolProfileUpdateRequest $request) {

        $user_id = Auth::school()->get()->id;
        $user = Schools::find($user_id);

        $user->sc_name = (Input::get('school_name') != '') ? e(Input::get('school_name')) : '';
        $user->sc_address1 = (Input::get('address1') != '') ? e(Input::get('address1')) : '';
        $user->sc_address2 = (Input::get('address2') != '') ? e(Input::get('address2')) : '';
        $user->sc_pincode = (Input::get('pincode') != '') ? e(Input::get('pincode')) : '';
        $user->sc_city = (Input::get('city') != '') ? e(Input::get('city')) : '';
        $user->sc_state = (Input::get('state') != '') ? e(Input::get('state')) : '';
        $user->sc_country = (Input::get('country') != '') ? e(Input::get('country')) : '';
        $user->sc_first_name = (Input::get('first_name') != '') ? e(Input::get('first_name')) : '';
        $user->sc_last_name = (Input::get('last_name') != '') ? e(Input::get('last_name')) : '';
        $user->sc_phone = (Input::get('phone') != '') ? e(Input::get('phone')) : '';
        $user->sc_email = (Input::get('email') != '') ? e(Input::get('email')) : '';

        if (Input::get('email') != '') {
            $schoolEmailExist = $this->SchoolsRepository->checkActiveEmailExist(Input::get('email'), $user_id);
        }

        if (isset($schoolEmailExist) && $schoolEmailExist) {
            return Redirect::to("school/updateprofile")->with('error', trans('appmessages.userwithsameemailaddress'));
            exit;
        } else {
            //Image upload            
            $file = Input::file('logo');
            if (!empty($file)) {
                $fileName = 'school' . time() . '.' . $file->getClientOriginalExtension();
                $pathOriginal = public_path($this->schoolOriginalImageUploadPath . $fileName);
                $pathThumb = public_path($this->schoolThumbImageUploadPath . $fileName);
                Image::make($file->getRealPath())->save($pathOriginal);
                Image::make($file->getRealPath())->save($pathOriginal);
                $user->sc_logo = $fileName;
            } 

            $file = Input::file('photo');
            if (!empty($file)) {
                $fileName = 'contactperson_' . time() . '.' . $file->getClientOriginalExtension();
                $pathOriginal = public_path($this->contactpersonOriginalImageUploadPath . $fileName);
                $pathThumb = public_path($this->contactpersonThumbImageUploadPath . $fileName);
                Image::make($file->getRealPath())->save($pathOriginal);
                Image::make($file->getRealPath())->save($pathThumb);
                $user->sc_photo = $fileName;
            }                      
        }
        $user->save();
        return Redirect::to("school/updateprofile")->with('success', 'Profile Updated successfully.');
    }

}
