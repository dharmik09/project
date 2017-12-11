<?php

namespace App\Http\Controllers\Sponsor;

use App\Http\Controllers\Controller;
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
use App\Sponsors;
use App\Templates;
use App\Services\Parents\Contracts\ParentsRepository;
use App\Services\Sponsors\Contracts\SponsorsRepository;
use App\Http\Requests\SponsorProfileUpdateRequest;
use App\Services\FileStorage\Contracts\FileStorageRepository;

class ProfileManagementController extends Controller {

    public function __construct(FileStorageRepository $fileStorageRepository, SponsorsRepository $sponsorsRepository) {
        $this->objSponsors = new Sponsors();
        $this->sponsorsRepository = $sponsorsRepository;
        $this->fileStorageRepository = $fileStorageRepository;
        $this->contactphotoOriginalImageUploadPath = Config::get('constant.CONTACT_PHOTO_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->contactphotoThumbImageUploadPath = Config::get('constant.CONTACT_PHOTO_THUMB_IMAGE_UPLOAD_PATH');
        $this->contactphotoThumbImageHeight = Config::get('constant.CONTACT_PHOTO_THUMB_IMAGE_HEIGHT');
        $this->contactphotoThumbImageWidth = Config::get('constant.CONTACT_PHOTO_THUMB_IMAGE_WIDTH');

        $this->sponsorOriginalImageUploadPath = Config::get('constant.SPONSOR_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->sponsorThumbImageUploadPath = Config::get('constant.SPONSOR_THUMB_IMAGE_UPLOAD_PATH');
        $this->sponsorThumbImageHeight = Config::get('constant.SPONSOR_THUMB_IMAGE_HEIGHT');
        $this->sponsorThumbImageWidth = Config::get('constant.SPONSOR_THUMB_IMAGE_WIDTH');
        $this->loggedInUser = Auth::guard('sponsor');
    }

    public function updateProfile() {
        $id = $this->loggedInUser->user()->id;
        $user = $this->sponsorsRepository->getSponsorById($id);
        $sponsorOriginalImagePath = Config::get('constant.SPONSOR_ORIGINAL_IMAGE_UPLOAD_PATH');
        $contactphotoOriginalImagePath = Config::get('constant.CONTACT_PHOTO_ORIGINAL_IMAGE_UPLOAD_PATH');

        return view('sponsor.updateProfile', compact('user', 'contactphotoOriginalImagePath', 'sponsorOriginalImagePath', 'countries', 'cities', 'states'));
    }

    public function saveProfile(SponsorProfileUpdateRequest $request) {

        $user_id = $this->loggedInUser->user()->id;
        $user = Sponsors::find($user_id);
        $user->sp_company_name = (Input::get('company_name') != '') ? e(Input::get('company_name')) : '';
        $user->sp_admin_name = (Input::get('admin_name') != '') ? e(Input::get('admin_name')) : '';
        $user->sp_address1 = (Input::get('address1') != '') ? e(Input::get('address1')) : '';
        $user->sp_address2 = (Input::get('address2') != '') ? e(Input::get('address2')) : '';
        $user->sp_pincode = (Input::get('pincode') != '') ? e(Input::get('pincode')) : '';
        $user->sp_city = (Input::get('city') != '') ? e(Input::get('city')) : '';
        $user->sp_state = (Input::get('state') != '') ? e(Input::get('state')) : '';
        $user->sp_country = (Input::get('country') != '') ? e(Input::get('country')) : '';
        $user->sp_first_name = (Input::get('first_name') != '') ? e(Input::get('first_name')) : '';
        $user->sp_last_name = (Input::get('last_name') != '') ? e(Input::get('last_name')) : '';
        $user->sp_title = (Input::get('title') != '') ? e(Input::get('title')) : '';
        $user->sp_phone = (Input::get('phone') != '') ? e(Input::get('phone')) : '';
        $user->sp_email = (Input::get('email') != '') ? e(Input::get('email')) : '';

        if (Input::get('email') != '') {
            $sponsorEmailExist = $this->sponsorsRepository->checkActiveEmailExist(Input::get('email'), $user_id);
        }
        if (isset($sponsorEmailExist) && $sponsorEmailExist) {
            return Redirect::to("sponsor/update-profile")->with('error', trans('appmessages.userwithsameemailaddress'));
            exit;
        } else {
            //Image upload
            if (Input::file()) {
                $file = Input::file('logo');
                if (!empty($file)) {
                    $fileName = 'sponsor_' . time() . '.' . $file->getClientOriginalExtension();
                    $pathOriginal = public_path($this->sponsorOriginalImageUploadPath . $fileName);
                    $pathThumb = public_path($this->sponsorThumbImageUploadPath . $fileName);
                    Image::make($file->getRealPath())->save($pathOriginal);
                    Image::make($file->getRealPath())->resize($this->sponsorThumbImageWidth, $this->sponsorThumbImageHeight)->save($pathThumb);

                    //Uploading on AWS
                    $originalImage = $this->fileStorageRepository->addFileToStorage($fileName, $this->sponsorOriginalImageUploadPath, $pathOriginal, "s3");
                    $thumbImage = $this->fileStorageRepository->addFileToStorage($fileName, $this->sponsorThumbImageUploadPath, $pathThumb, "s3");
                    
                    \File::delete($this->sponsorOriginalImageUploadPath . $fileName);
                    \File::delete($this->sponsorThumbImageUploadPath . $fileName);
                    $user->sp_logo = $fileName;
                }

                if (Input::file()) {
                    $file = Input::file('photo');
                    if (!empty($file)) {
                        $fileName = 'contactsponsor_' . time() . '.' . $file->getClientOriginalExtension();
                        $pathOriginal = public_path($this->contactphotoOriginalImageUploadPath . $fileName);
                        $pathThumb = public_path($this->contactphotoThumbImageUploadPath . $fileName);
                        Image::make($file->getRealPath())->save($pathOriginal);
                        Image::make($file->getRealPath())->resize($this->contactphotoThumbImageWidth, $this->contactphotoThumbImageHeight)->save($pathThumb);

                        //Uploading on AWS
                        $originalImage = $this->fileStorageRepository->addFileToStorage($fileName, $this->contactphotoOriginalImageUploadPath, $pathOriginal, "s3");
                        $thumbImage = $this->fileStorageRepository->addFileToStorage($fileName, $this->contactphotoThumbImageUploadPath, $pathThumb, "s3");
                        
                        \File::delete($this->contactphotoOriginalImageUploadPath . $fileName);
                        \File::delete($this->contactphotoThumbImageUploadPath . $fileName);
                        $user->sp_photo = $fileName;
                    }
                }
            }
        }
        $user->save();
        return Redirect::to("sponsor/update-profile")->with('success', 'Profile Updated successfully.');
    }

}
