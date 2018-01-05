<?php

namespace App\Http\Controllers\Webservice;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Helpers;
use Config;
use App\Services\Teenagers\Contracts\TeenagersRepository;
use App\Services\FileStorage\Contracts\FileStorageRepository;
use App\Http\Requests\TeenagerProfileUpdateRequest;
use App\Teenagers;
use App\TeenagerLoginToken;
use App\DeviceToken;
use App\Country;
use Storage;
use Carbon\Carbon;
use Image;
use Input;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class DashboardController extends Controller
{
    public function __construct(TeenagersRepository $teenagersRepository, FileStorageRepository $fileStorageRepository)
    {
        $this->teenagersRepository = $teenagersRepository;
        $this->fileStorageRepository = $fileStorageRepository;
        $this->objTeenagerLoginToken = new TeenagerLoginToken();
        $this->objDeviceToken = new DeviceToken();
        $this->objCountry = new Country();
        $this->teenOriginalImageUploadPath = Config::get('constant.TEEN_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->teenThumbImageUploadPath = Config::get('constant.TEEN_THUMB_IMAGE_UPLOAD_PATH');
        $this->sponsorOriginalImageUploadPath = Config::get('constant.SPONSOR_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->sponsorThumbImageUploadPath = Config::get('constant.SPONSOR_THUMB_IMAGE_UPLOAD_PATH');
        $this->teenProfileImageUploadPath = Config::get('constant.TEEN_PROFILE_IMAGE_UPLOAD_PATH');
        $this->teenThumbImageHeight = Config::get('constant.TEEN_THUMB_IMAGE_HEIGHT');
        $this->teenThumbImageWidth = Config::get('constant.TEEN_THUMB_IMAGE_WIDTH');
        $this->log = new Logger('api-restless-controller');
        $this->log->pushHandler(new StreamHandler(storage_path().'/logs/monolog-'.date('m-d-Y').'.log'));
    }

    /* Request Params : updateProfile
    *  loginToken, userId, name, lastname, email, country, pincode, gender, month, day, year, sponsorIds, mobile, phone, proteenCode, photo, password, publicProfile, shareWithMembers, share_with_parents, notifications, shareWithTeachers, t_view_information
    */
    public function updateProfile(Request $request) {
        $response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg') ] ;
        
        if($request->userId != "" && $request->name != "" && $request->lastname != "" && $request->country != "" && $request->pincode != "" && $request->gender != "" && $request->sponsorIds != "" && $request->birthDate != "") {
            $checkuserexist = $this->teenagersRepository->getTeenagerByTeenagerId($request->userId);
            if ($checkuserexist) {
                $teenagerDetail['id'] = $request->userId;
                $teenagerDetail['t_name'] = $request->name;
                $teenagerDetail['t_lastname'] = $request->lastname;
                $teenagerDetail['t_nickname'] = $request->proteenCode;
                $teenagerDetail['t_gender'] = (in_array($request->gender, ['1','2'])) ? $request->gender : '1';
                
                $teenagerDetail['t_email'] = $request->email;
                if ($request->email != "") {
                    $teenagerEmailExist = $this->teenagersRepository->checkActiveEmailExist($teenagerDetail['t_email'], $teenagerDetail['id']);
                    if ($teenagerEmailExist) {
                        $response['message'] = "User with same email already exists";
                        $response['login'] = 1;
                        return response()->json($response, 200);
                        exit;
                    }
                }

                $teenagerDetail['t_phone'] = $request->mobile;
                if ($request->mobile != '') {
                    $teenagerMobileExist = $this->teenagersRepository->checkActivePhoneExist($teenagerDetail['t_phone'], $teenagerDetail['id']);
                    if ($teenagerMobileExist) {
                        $response['message'] = "User with same Phone Number already exists";
                        $response['login'] = 1;
                        return response()->json($response, 200);
                        exit;
                    }
                }

                if($request->password != "" && strlen($request->password) > 5) {
                    $teenagerDetail['password'] = bcrypt($request->password); 
                }
                
                //Birthdate
                $teenagerDetail['t_birthdate'] = '';
                if (isset($request->birthDate) && $request->birthDate != '') {
                    $dob = $request->birthDate;
                    $dobDate = str_replace('/', '-', $dob);
                    $teenagerDetail['t_birthdate'] = date("Y-m-d", strtotime($dobDate));
                }

                $teenagerDetail['t_phone_new'] = $request->phone;
                $teenagerDetail['t_country'] = $request->country;
                $teenagerDetail['t_pincode'] = $request->pincode;
                //On-Off Buttons
                $teenagerDetail['is_search_on'] = ( $request->publicProfile != "") ? $request->publicProfile : "0";
                $teenagerDetail['is_share_with_teachers'] = ( $request->shareWithTeachers != "") ? $request->shareWithTeachers : "0";
                $teenagerDetail['is_share_with_other_members'] = ( $request->shareWithMembers != "") ? $request->shareWithMembers : "0";
                $teenagerDetail['is_share_with_parents'] = ( $request->shareWithParents != "") ? $request->shareWithParents : "0";
                $teenagerDetail['is_notify'] = ( $request->notifications != "") ? $request->notifications : "0";
                $teenagerDetail['t_view_information'] = ( $request->viewInformation != "") ? $request->viewInformation : "0";
                $teenagerDetail['is_sound_on'] = ( $request->isSoundOn != "") ? $request->isSoundOn : "0";

                if($request->sponsorIds == "") {
                    $response['message'] = "Please, select at-least one sponsor";
                    $response['login'] = 1;
                    return response()->json($response, 200);
                    exit;
                }
                if (Input::file()) {
                    $file = Input::file('profilePic');
                    if (!empty($file)) {
                        //Delete old uploaded file
                        if(isset($checkuserexist['t_photo']) && $checkuserexist['t_photo'] != "") {
                            $originalImageDelete = $this->fileStorageRepository->deleteFileToStorage($checkuserexist['t_photo'], $this->teenOriginalImageUploadPath, "s3");
                            $thumbImageDelete = $this->fileStorageRepository->deleteFileToStorage($checkuserexist['t_photo'], $this->teenThumbImageUploadPath, "s3");
                            $profileImageDelete = $this->fileStorageRepository->deleteFileToStorage($checkuserexist['t_photo'], $this->teenProfileImageUploadPath, "s3");
                        }
                        $fileName = 'teenager_' . time() . '.' . $file->getClientOriginalExtension();
                        $pathOriginal = public_path($this->teenOriginalImageUploadPath . $fileName);
                        $pathThumb = public_path($this->teenThumbImageUploadPath . $fileName);
                        $pathProfile = public_path($this->teenProfileImageUploadPath . $fileName);
                        Image::make($file->getRealPath())->save($pathOriginal);
                        Image::make($file->getRealPath())->resize($this->teenThumbImageWidth, $this->teenThumbImageHeight)->save($pathThumb);
                        Image::make($file->getRealPath())->resize(200, 200)->save($pathProfile);
                        //Uploading on AWS
                        $originalImage = $this->fileStorageRepository->addFileToStorage($fileName, $this->teenOriginalImageUploadPath, $pathOriginal, "s3");
                        $thumbImage = $this->fileStorageRepository->addFileToStorage($fileName, $this->teenThumbImageUploadPath, $pathThumb, "s3");
                        $profileImage = $this->fileStorageRepository->addFileToStorage($fileName, $this->teenProfileImageUploadPath, $pathProfile, "s3");
                        //Deleting Local Files
                        \File::delete($this->teenOriginalImageUploadPath . $fileName);
                        \File::delete($this->teenThumbImageUploadPath . $fileName);
                        \File::delete($this->teenProfileImageUploadPath . $fileName);
                        $teenagerDetail['t_photo'] = $fileName;
                    }
                }
                $teenager = $this->teenagersRepository->saveTeenagerDetail($teenagerDetail);
                $saveSponsors = $this->teenagersRepository->saveTeenagerSponserId($teenagerDetail['id'], $request->sponsorIds);
                if($teenager) {
                    $teenager->t_sponsors = $this->teenagersRepository->getSelfSponserListData($teenager->id);
                    if (isset($teenager->t_sponsors)) {
                        foreach ($teenager->t_sponsors as $sponsor) {
                            $sponsorPhoto = ($sponsor->sp_logo != "") ? $sponsor->sp_logo : "proteen-logo.png";
                            $sponsor->sp_logo = Storage::url($this->sponsorOriginalImageUploadPath . $sponsorPhoto);
                            $sponsor->sp_logo_thumb = Storage::url($this->sponsorThumbImageUploadPath . $sponsorPhoto);
                        }
                    }
                    //Country related info
                    $teenager->c_code = ( isset(Country::getCountryDetail($teenager->t_country)->c_code) ) ? Country::getCountryDetail($teenager->t_country)->c_code : "";
                    $teenager->c_name = ( isset(Country::getCountryDetail($teenager->t_country)->c_name) ) ? Country::getCountryDetail($teenager->t_country)->c_name : "";
                    $teenager->country_id = $teenager->t_country;

                    $teenager->t_photo_thumb = "";
                    if ($teenager->t_photo != '') {
                        $teenager->t_photo_thumb = Storage::url($this->teenThumbImageUploadPath . $teenager->t_photo);
                        $teenager->t_photo = Storage::url($this->teenOriginalImageUploadPath . $teenager->t_photo);
                    }
                    $teenager->t_birthdate = (isset($teenager->t_birthdate) && $teenager->t_birthdate != '0000-00-00') ? Carbon::parse($teenager->t_birthdate)->format('d/m/Y') : '';
                    
                    $ads = Helpers::getAds($teenager->id);
                    $response['ads'] = $ads;
                    $response['status'] = 1;
                    $response['login'] = 1;
                    $response['message'] = trans('appmessages.default_success_msg');
                    $response['data'] = $teenager;
                } else {
                    $response['message'] = "Something went wrong";
                    $response['login'] = 1;
                    return response()->json($response, 200);
                    exit;
                }
            } else {
            //    $response['login'] = 1;
                $response['message'] = trans('appmessages.invalid_userid_msg') . ' or ' . trans('appmessages.notvarified_user_msg');
            }
        } else {
            $response['login'] = 1;
            $response['message'] = "Please correctly fillup all mandatory fields.";
        }
        return response()->json($response, 200);
        exit;
    }

    /* Request Params : getTeenagerAcademicInfo
    *  loginToken, userId
    */
    public function getTeenagerAcademicInfo(Request $request) {
        $response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg') ] ;
        $checkUserExist = $this->teenagersRepository->getTeenagerByTeenagerId($request->userId);
        if($checkUserExist) {
            $teenagerMeta = Helpers::getTeenagerEducationData($request->userId);
            $data = ($teenagerMeta) ? $teenagerMeta : [];
            $response['data'] = $data;
            $response['login'] = 1;
            $response['message'] = trans('appmessages.default_success_msg');
            $response['status'] = 1;
        } else {
            $response['message'] = trans('appmessages.invalid_userid_msg') . ' or ' . trans('appmessages.notvarified_user_msg');
        }
        return response()->json($response, 200);
        exit;
    }

    /* Request Params : getTeenagerAchievementInfo
    *  loginToken, userId
    */
    public function getTeenagerAchievementInfo(Request $request) {
        $response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg') ] ;
        $checkUserExist = $this->teenagersRepository->getTeenagerByTeenagerId($request->userId);
        if($checkUserExist) {
            $teenagerMeta = Helpers::getTeenagerAchievementData($request->userId);
            $data = ($teenagerMeta) ? $teenagerMeta : [];
            $response['data'] = $data;
            $response['login'] = 1;
            $response['message'] = trans('appmessages.default_success_msg');
            $response['status'] = 1;
        } else {
            $response['message'] = trans('appmessages.invalid_userid_msg') . ' or ' . trans('appmessages.notvarified_user_msg');
        }
        return response()->json($response, 200);
        exit;
    }

    /* Request Params : saveTeenagerAcademicInfo
    *  loginToken, userId, metaValue, metaValueId, metaId = 2
    */
    public function saveTeenagerAcademicInfo(Request $request) {
        $response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg') ] ;
        $checkUserExist = $this->teenagersRepository->getTeenagerByTeenagerId($request->userId);
        
        if($checkUserExist && $request->metaValueId != "") {
            $request->metaValue = trim($request->metaValue);
            if($request->metaValue == "") {
                $response['login'] = 1;
                $response['status'] = 1;
                $response['message'] = 'Please enter the data';
                return response()->json($response, 200);
                exit;
            }
            $data['tmd_teenager'] = $request->userId;
            $data['tmd_meta_id'] = 2; //Use '2' for education
            $data['tmd_meta_value'] = $request->metaValue;
            $data['id'] = $request->metaValueId;
            $this->teenagersRepository->saveTeenagerMetaData($data);
            
            $teenagerMeta = Helpers::getTeenagerEducationData($request->userId);
            $data = ($teenagerMeta) ? $teenagerMeta : [];
            $response['data'] = $data;
            $response['login'] = 1;
            $response['message'] = trans('appmessages.default_success_msg');
            $response['status'] = 1;
        } else {
            $response['message'] = "Something went wrong or missing data!";
        }
        return response()->json($response, 200);
        exit;
    } 

    /* Request Params : saveTeenagerAchievementInfo
    *  loginToken, userId, metaValue, metaValueId, metaId = 1
    */
    public function saveTeenagerAchievementInfo(Request $request) {
        $response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg') ] ;
        $checkUserExist = $this->teenagersRepository->getTeenagerByTeenagerId($request->userId);
        
        if($checkUserExist && $request->metaValueId != "") {
            $request->metaValue = trim($request->metaValue);
            if($request->metaValue == "") {
                $response['login'] = 1;
                $response['status'] = 1;
                $response['message'] = 'Please enter the data';
                return response()->json($response, 200);
                exit;
            }
            $data['tmd_teenager'] = $request->userId;
            $data['tmd_meta_id'] = 1; //Use '1' for Achievement
            $data['tmd_meta_value'] = $request->metaValue;
            $data['id'] = $request->metaValueId;
            $this->teenagersRepository->saveTeenagerMetaData($data);
            
            $teenagerMeta = Helpers::getTeenagerAchievementData($request->userId);
            $data = ($teenagerMeta) ? $teenagerMeta : [];
            $response['data'] = $data;
            $response['login'] = 1;
            $response['message'] = trans('appmessages.default_success_msg');
            $response['status'] = 1;
        } else {
            $response['message'] = "Something went wrong or missing data!";
        }
        return response()->json($response, 200);
        exit;
    }    
}