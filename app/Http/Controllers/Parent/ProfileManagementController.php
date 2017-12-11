<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Http\Requests\ParentLoginRequest;
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
use App\Parents;
use App\Templates;
use App\Services\Parents\Contracts\ParentsRepository;
use App\Services\Sponsors\Contracts\SponsorsRepository;
use App\Http\Requests\ParentProfileUpdateRequest;
use App\Services\FileStorage\Contracts\FileStorageRepository;

class ProfileManagementController extends Controller {

    public function __construct(ParentsRepository $parentsRepository, SponsorsRepository $sponsorsRepository, FileStorageRepository $fileStorageRepository)
    {
        $this->objParents = new Parents();
        $this->parentsRepository = $parentsRepository;
        $this->fileStorageRepository = $fileStorageRepository;
        $this->parentOriginalImageUploadPath = Config::get('constant.PARENT_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->parentThumbImageUploadPath = Config::get('constant.PARENT_THUMB_IMAGE_UPLOAD_PATH');
        $this->parentThumbImageHeight = Config::get('constant.PARENT_THUMB_IMAGE_HEIGHT');
        $this->parentThumbImageWidth = Config::get('constant.PARENT_THUMB_IMAGE_WIDTH');
        $this->loggedInUser = Auth::guard('parent');
    }

    public function updateProfile()
    {
        $id = $this->loggedInUser->user()->id;
        $user = $this->parentsRepository->getParentById($id);
        //$countries = Helpers::getCountries();
        //$cities =  Helpers::getCities();
        //$states =  Helpers::getStates();
        $parentOriginalImagePath = Config::get('constant.PARENT_ORIGINAL_IMAGE_UPLOAD_PATH');
        return view('parent.updateProfile',compact('user','parentOriginalImagePath','countries','cities','states'));
    }
    

    public function saveProfile(ParentProfileUpdateRequest $request)
    {
        $user_id = $this->loggedInUser->user()->id;
        $user = Parents::find($user_id);
        $user->p_first_name = (Input::get('first_name') != '') ? e(Input::get('first_name')) : '';
        $user->p_last_name = (Input::get('last_name') != '') ? e(Input::get('last_name')) : '';
        $user->p_address1 = (Input::get('address1') != '') ? e(Input::get('address1')) : '';
        $user->p_address2 = (Input::get('address2') != '') ? e(Input::get('address2')) : '';
        $user->p_pincode = (Input::get('pincode') != '') ? e(Input::get('pincode')) : '';
        $user->p_city = (Input::get('city') != '') ? e(Input::get('city')) : '';
        $user->p_state = (Input::get('state') != '') ? e(Input::get('state')) : '';
        $user->p_country = (Input::get('country') != '') ? e(Input::get('country')) : '';
        $user->p_gender = (Input::get('gender') != '') ? e(Input::get('gender')) : '';
        $user->p_email = (Input::get('email') != '') ? e(Input::get('email')) : '';

        if(Input::get('email') != '') {
            $parentEmailExist = $this->parentsRepository->checkActiveEmailExist(Input::get('email'),$user_id);
        }

        if(isset($parentEmailExist) && $parentEmailExist)
        {
            return Redirect::to("parent/update-profile")->with('error', trans('appmessages.userwithsameemailaddress'));
            exit;
        }

        else
        {
            //Image upload
            if (Input::file())
            {
                $file = Input::file('photo');
                if (!empty($file)) {
                    $fileName = 'parent_' . time() . '.' . $file->getClientOriginalExtension();
                    $pathOriginal = public_path($this->parentOriginalImageUploadPath . $fileName);
                    $pathThumb = public_path($this->parentThumbImageUploadPath . $fileName);
                    Image::make($file->getRealPath())->save($pathOriginal);
                    Image::make($file->getRealPath())->resize(300,300)->save($pathThumb);

                    //Uploading on AWS
                    $originalImage = $this->fileStorageRepository->addFileToStorage($fileName, $this->parentOriginalImageUploadPath, $pathOriginal, "s3");
                    $thumbImage = $this->fileStorageRepository->addFileToStorage($fileName, $this->parentThumbImageUploadPath, $pathThumb, "s3");
                    
                    \File::delete($this->parentOriginalImageUploadPath . $fileName);
                    \File::delete($this->parentThumbImageUploadPath . $fileName);
                    $user->p_photo = $fileName;
                }
                else {
                    $user->p_photo = 'proteen_logo.png';
                }
            }
            $user->save();
            return Redirect::to("parent/update-profile")->with('success', 'Profile Updated successfully.');
        }
    }
}
