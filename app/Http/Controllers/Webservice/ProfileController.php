<?php

namespace App\Http\Controllers\Webservice;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Helpers;
use Config;
use App\Services\Teenagers\Contracts\TeenagersRepository;
use App\Services\Level1Activity\Contracts\Level1ActivitiesRepository;
use App\Services\Community\Contracts\CommunityRepository;
use App\Services\FileStorage\Contracts\FileStorageRepository;
use App\Http\Requests\TeenagerProfileUpdateRequest;
use App\Teenagers;
use App\TeenagerLoginToken;
use App\DeviceToken;
use Storage;
use App\Country;
use Carbon\Carbon;
use Input;
use Image;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use App\PaidComponent;
use App\DeductedCoins;
use App\Level4ProfessionProgress;

class ProfileController extends Controller
{
    public function __construct(CommunityRepository $communityRepository, FileStorageRepository $fileStorageRepository, Level1ActivitiesRepository $level1ActivitiesRepository, TeenagersRepository $teenagersRepository)
    {
        $this->teenagersRepository = $teenagersRepository;
        $this->fileStorageRepository = $fileStorageRepository;
        $this->communityRepository = $communityRepository;
        $this->objTeenagerLoginToken = new TeenagerLoginToken();
        $this->objDeviceToken = new DeviceToken();
        $this->objCountry = new Country();
        $this->level1ActivitiesRepository = $level1ActivitiesRepository;
        $this->teenOriginalImageUploadPath = Config::get('constant.TEEN_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->teenThumbImageUploadPath = Config::get('constant.TEEN_THUMB_IMAGE_UPLOAD_PATH');
        $this->sponsorOriginalImageUploadPath = Config::get('constant.SPONSOR_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->sponsorThumbImageUploadPath = Config::get('constant.SPONSOR_THUMB_IMAGE_UPLOAD_PATH');
        $this->teenProfileImageUploadPath = Config::get('constant.TEEN_PROFILE_IMAGE_UPLOAD_PATH');
        $this->teenThumbImageHeight = Config::get('constant.TEEN_THUMB_IMAGE_HEIGHT');
        $this->teenThumbImageWidth = Config::get('constant.TEEN_THUMB_IMAGE_WIDTH');
        $this->log = new Logger('api-restless-controller');
        $this->log->pushHandler(new StreamHandler(storage_path().'/logs/monolog-'.date('m-d-Y').'.log'));
        $this->cartoonThumbImageUploadPath = Config::get('constant.CARTOON_THUMB_IMAGE_UPLOAD_PATH');
        $this->humanThumbImageUploadPath = Config::get('constant.HUMAN_THUMB_IMAGE_UPLOAD_PATH');
        $this->relationIconThumbImageUploadPath = Config::get('constant.RELATION_ICON_THUMB_IMAGE_UPLOAD_PATH');
        $this->objPaidComponent = new PaidComponent;
        $this->objDeductedCoins = new DeductedCoins;
        $this->objLevel4ProfessionProgress = new Level4ProfessionProgress;
    }

    /* Request Params : getTeenagerProfileData
     *  loginToken, userId
     *  Service after loggedIn user
     */
    public function getTeenagerProfileData(Request $request)
    {
		$response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg') ] ;
    	$teenager = $this->teenagersRepository->getTeenagerById($request->userId);
        if($request->userId != "" && $teenager) {
    		$totalQuestion = $this->level1ActivitiesRepository->getNoOfTotalQuestionsAttemptedQuestion($request->userId);
            $response['NoOfTotalQuestions'] = (isset($totalQuestion[0]->NoOfTotalQuestions)) ? $totalQuestion[0]->NoOfTotalQuestions : 0;
            $response['NoOfAttemptedQuestions'] = (isset($totalQuestion[0]->NoOfAttemptedQuestions)) ? $totalQuestion[0]->NoOfAttemptedQuestions : 0;
            $response['l1_question_attempted'] = 0;
            $response['attemptLevel1At'] = 0;
            if($response['NoOfTotalQuestions'] > 0 && (int)$response['NoOfAttemptedQuestions'] >= (int)$response['NoOfTotalQuestions']) {
                $response['l1_question_attempted'] = 1;
                $getLevel1AttemptedQuality = $this->level1ActivitiesRepository->getTeenAttemptedQualityType($request->userId);
                if(isset($getLevel1AttemptedQuality[0]) && count($getLevel1AttemptedQuality[0]) > 0) {
                    $array = ['1', '2', '3', '4'];
                    $array2 = $getLevel1AttemptedQuality->toArray();
                    $arrayDiff = array_diff($array, $array2);
                    $response['attemptLevel1At'] = (count($arrayDiff) > 0) ? min($arrayDiff) : 5;
                    if($response['attemptLevel1At'] == 5) {
                        $response['attemptedCompletionMessage'] = "Your profile survey completed 100%, But if you want to vote more Icon please click on below";
                    }
                } else {
                    $response['attemptLevel1At'] = 1;
                }
            }
            $teenager->t_birthdate = (isset($teenager->t_birthdate) && $teenager->t_birthdate != '0000-00-00') ? Carbon::parse($teenager->t_birthdate)->format('d/m/Y') : '';
            
            $teenager->t_sponsors = $this->teenagersRepository->getSelfSponserListData($teenager->id);
            if (isset($teenager->t_sponsors)) {
                foreach ($teenager->t_sponsors as $sponsor) {
                    $sponsorPhoto = ($sponsor->sp_logo != "") ? $sponsor->sp_logo : "proteen-logo.png";
                    $sponsor->sp_logo = Storage::url($this->sponsorOriginalImageUploadPath . $sponsorPhoto);
                    $sponsor->sp_logo_thumb = Storage::url($this->sponsorThumbImageUploadPath . $sponsorPhoto);
                }
            }

            //Teenager Image
            $teenager->t_photo_thumb = "";
            if ($teenager->t_photo != '') {
                $teenager->t_photo_thumb = Storage::url($this->teenThumbImageUploadPath . $teenager->t_photo);
                $teenager->t_photo = Storage::url($this->teenOriginalImageUploadPath . $teenager->t_photo);
            }
            //Country info
            $teenager->c_code = ( isset(Country::getCountryDetail($teenager->t_country)->c_code) ) ? Country::getCountryDetail($teenager->t_country)->c_code : "";
            $teenager->c_name = ( isset(Country::getCountryDetail($teenager->t_country)->c_name) ) ? Country::getCountryDetail($teenager->t_country)->c_name : "";
            $teenager->country_id = $teenager->t_country;
            
            //Get Location Area
            if ($teenager->t_location != "") {
                $getCityArea = $teenager->t_location;
            } else if($teenager->t_pincode != "") {
                $getLocation = file_get_contents('http://maps.googleapis.com/maps/api/geocode/json?address='.$teenager->t_pincode.'&sensor=true');
                $getCityArea = ( isset(json_decode($getLocation)->results[0]->address_components[1]->long_name) && json_decode($getLocation)->results[0]->address_components[1]->long_name != "" ) ? json_decode($getLocation)->results[0]->address_components[1]->long_name : "Default";
            } else {
                $getCityArea = ( $teenager->c_name != "" ) ? $teenager->c_name : "Default";
            }
            $teenager->t_about_info = (isset($teenager->t_about_info) && !empty($teenager->t_about_info)) ? $teenager->t_about_info : "";
            $response['teenagerLocationArea'] = $getCityArea. " Area";
            $profileComplete = Helpers::calculateProfileComplete($request->userId);
            $response['profileComplete'] = "Profile ". $profileComplete ."% complete";
            $response['facebookUrl'] = "https://facebook.com";
            $response['googleUrl'] = "https://google.com";
            $response['connectionsCount'] = $this->communityRepository->getMyConnectionsCount($request->userId);
            $response['loginToken'] = base64_encode($teenager->t_email.':'.$teenager->t_uniqueid);

            $learningGuidance = Helpers::getCmsBySlug('learning-guidance-info');
            $response['learningGuidenceDescription'] = (isset($learningGuidance->cms_body) && !empty($learningGuidance->cms_body)) ? $learningGuidance->cms_body : "";
            $componentsData = $this->objPaidComponent->getPaidComponentsData(Config::get('constant.LEARNING_STYLE'));
            $deductedCoinsDetail = (isset($componentsData->id)) ? $this->objDeductedCoins->getDeductedCoinsDetailByIdForLS($request->userId, $componentsData->id, 1) : [];
            $remainingDaysForActivity = 0;
            if (!empty($deductedCoinsDetail[0])) {
                $remainingDaysForActivity = Helpers::calculateRemainingDays($deductedCoinsDetail[0]->dc_end_date);
            }
            $coinConsumptionDetails = [];
            $coinConsumptionDetails['componentId'] = $componentsData->id;
            $coinConsumptionDetails['componentName'] = Config::get('constant.LEARNING_STYLE');
            $coinConsumptionDetails['componentCoins'] = $componentsData->pc_required_coins;
            $coinConsumptionDetails['remainingDays'] = $remainingDaysForActivity;

            $response['learningGuidanceCoinsDetails'] = $coinConsumptionDetails;
            $response['attemptedCompletionMessage'] = "Your profile survey completed 100%, But if you want to vote more Icon please click on below";
            $response['status'] = 1;
            $response['login'] = 1;
            $response['message'] = trans('appmessages.default_success_msg');
            $response['data'] = $teenager;
        } else {
    		$response['message'] = trans('appmessages.missing_data_msg');
    	}
    	
    	return response()->json($response, 200);
    	exit;
    }

    /* Request Params : deleteTeenagerData
    *  loginToken, userId
    *  Service after loggedIn user
    */
    public function deleteTeenagerData(Request $request)
    {
        $response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg') ] ;
        $teenager = $this->teenagersRepository->getTeenagerDetailById($request->userId);
        if($request->userId != "" && $teenager) {
            $this->teenagersRepository->deleteTeenagerData($request->userId);
            $response['status'] = 1;
            $response['message'] = 'Successfully deleted!';
            $response['data'] = [];
        } else {
            $response['message'] = trans('appmessages.missing_data_msg');
        }
        return response()->json($response, 200);
        exit;
    }

    /* Request Params : saveTeenagerAboutInfo
    *  loginToken, userId, aboutInfo
    *  Service after loggedIn user
    */
    public function saveTeenagerAboutInfo(Request $request)
    {
        $response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg') ] ;
        $teenager = $this->teenagersRepository->getTeenagerDetailById($request->userId);
        if($request->userId != "" && $teenager) {
            $teenager->t_about_info = $request->aboutInfo;
            $teenager->save();
            
            $response['status'] = 1;
            $response['login'] = 1;
            $response['message'] = 'Successfully updated!';
            $response['data'] = $teenager;
        } else {
            $response['message'] = trans('appmessages.missing_data_msg');
        }
        return response()->json($response, 200);
        exit;
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
                
                $teenEmail = $request->email;
                if ($request->email != "") {
                    $teenagerEmailExist = $this->teenagersRepository->checkActiveEmailExist($teenEmail, $teenagerDetail['id']);
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
                if($teenagerDetail['t_pincode'] != "") {
                    $getLocation = json_decode(file_get_contents('http://maps.googleapis.com/maps/api/geocode/json?address='.$teenagerDetail['t_pincode'].'&sensor=true'));
                    $getCityArea = ( isset($getLocation->results[0]->address_components[1]->long_name) && $getLocation->results[0]->address_components[1]->long_name != "" ) ? $getLocation->results[0]->address_components[1]->long_name : "Default";
                } else if ($teenagerDetail['t_country'] != "") {
                    $country = $this->objCountry->find($teenagerDetail['t_country']);
                    $getCityArea = $country->c_name;
                } else {
                    $getCityArea = "Default";
                }
                $teenagerDetail['t_location'] = $getCityArea;

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
                    
                    //Get Location Area
                    if($teenager->t_pincode != "") {
                        $getLocation = file_get_contents('http://maps.googleapis.com/maps/api/geocode/json?address='.$teenager->t_pincode.'&sensor=true');
                        $getCityArea = ( isset(json_decode($getLocation)->results[0]->address_components[1]->long_name) && json_decode($getLocation)->results[0]->address_components[1]->long_name != "" ) ? json_decode($getLocation)->results[0]->address_components[1]->long_name : "Default";
                    } else {
                        $getCityArea = ( $teenager->c_name != "" ) ? $teenager->c_name : "Default";
                    }
                    
                    $response['teenagerLocationArea'] = $getCityArea. " Area";
                    $profileComplete = Helpers::calculateProfileComplete($request->userId);
                    $response['profileComplete'] = "Profile ". $profileComplete ."% complete";
                    $response['facebookUrl'] = "https://facebook.com";
                    $response['googleUrl'] = "https://google.com";
                    $response['connectionsCount'] = $this->communityRepository->getMyConnectionsCount($request->userId);

                    $learningGuidance = Helpers::getCmsBySlug('learning-guidance-info');
                    $response['learningGuidenceDescription'] = (isset($learningGuidance->cms_body) && !empty($learningGuidance->cms_body)) ? strip_tags($learningGuidance->cms_body) : "";
                    
                    //$ads = Helpers::getAds($teenager->id);
                    //$response['ads'] = $ads;
                    $response['status'] = 1;
                    $response['login'] = 1;
                    $response['message'] = trans('appmessages.default_success_msg');
                    $response['loginToken'] = base64_encode($teenager->t_email.':'.$teenager->t_uniqueid);
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
            $data = ($teenagerMeta) ? array($teenagerMeta) : [];
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
            $data = ($teenagerMeta) ? array($teenagerMeta) : [];
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
    *  loginToken, userId, metaValue, metaId = 2
    */
    public function saveTeenagerAcademicInfo(Request $request) {
        $response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg') ] ;
        $checkUserExist = $this->teenagersRepository->getTeenagerByTeenagerId($request->userId);
        
        if($checkUserExist) {
            $dataPoint = Helpers::getTeenagerEducationData($request->userId);
            $metaValueId = ( isset($dataPoint->id) ) ? $dataPoint->id : 0;
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
            $data['id'] = $metaValueId;
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
     *  loginToken, userId, metaValue, metaId = 1
     */
    public function saveTeenagerAchievementInfo(Request $request) {
        $response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg') ] ;
        $checkUserExist = $this->teenagersRepository->getTeenagerByTeenagerId($request->userId);
        
        if($checkUserExist) {
            $request->metaValue = trim($request->metaValue);
            $dataPoint = Helpers::getTeenagerAchievementData($request->userId);
            $metaValueId = ( isset($dataPoint->id) ) ? $dataPoint->id : 0;
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
            $data['id'] = $metaValueId;
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

    /* Request Params : getTeenagerProfileIcons
     *  loginToken, userId
     *  Service after loggedIn user
     */
    public function getTeenagerProfileIcons(Request $request) {
        $response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg') ] ;
        $teenager = $this->teenagersRepository->getTeenagerById($request->userId);
        if($request->userId != "" && $teenager) {
            $data = array();
            $iconsArray = array();
            $teenagerMyIcons = array();
            //Get teenager choosen Icon
            $teenagerIcons = $this->teenagersRepository->getTeenagerSelectedIcon($request->userId);
            $relationIcon = array();
            $fictionIcon = array();
            $nonFiction = array();
            if (isset($teenagerIcons) && !empty($teenagerIcons)) {
                foreach ($teenagerIcons as $key => $icon) {
                    if ($icon->ti_icon_type == 1) {
                        $fictionIconArr = [];
                        if ($icon->fiction_image != '' && Storage::size($this->cartoonThumbImageUploadPath . $icon->fiction_image) > 0)  {
                            $fictionIconArr['iconImage'] = Storage::url($this->cartoonThumbImageUploadPath . $icon->fiction_image);
                        } else {
                            $fictionIconArr['iconImage'] = Storage::url($this->cartoonThumbImageUploadPath . 'proteen-logo.png');
                        }
                        $fictionIconArr['iconDescription'] = (isset($icon->ci_description) && !empty($icon->ci_description)) ? $icon->ci_description : '';
                        $fictionIcon[] = $fictionIconArr;
                    } else if ($icon->ti_icon_type == 2) {
                        $nonFictionArr = [];
                        if ($icon->nonfiction_image != '' && Storage::size($this->humanThumbImageUploadPath . $icon->nonfiction_image) > 0) {
                            $nonFictionArr['iconImage'] = Storage::url($this->humanThumbImageUploadPath . $icon->nonfiction_image);
                        } else {
                            $nonFictionArr['iconImage'] = Storage::url($this->humanThumbImageUploadPath . 'proteen-logo.png');
                        }
                        $nonFictionArr['iconDescription'] = (isset($icon->hi_description) && !empty($icon->hi_description)) ? $icon->hi_description : '';
                        $nonFiction[] = $nonFictionArr;
                    } else {
                        $relationIconArr = [];
                        if ($icon->ti_icon_image != '' && Storage::size($this->relationIconThumbImageUploadPath . $icon->ti_icon_image) > 0) {
                            $relationIconArr['iconImage'] = Storage::url($this->relationIconThumbImageUploadPath . $icon->ti_icon_image);
                        } else {
                            $relationIconArr['iconImage'] = Storage::url($this->relationIconThumbImageUploadPath . 'proteen-logo.png');
                        }
                        $relationIconArr['iconDescription'] = "";
                        $relationIcon[] = $relationIconArr;
                    }
                }
                $teenagerMyIcons = array_merge($fictionIcon, $nonFiction, $relationIcon);
            }
            $data['desc'] = "Lorem ipsum dolor sit amet, consectetur adipisicing elit. Nemo pariatur id, explicabo vitae delectus eveniet rem doloremque perspiciatis, soluta, officiis mollitia reprehenderit assumenda libero molestias quae et. Tenetur, a, atque.";
            $data['icons'] = $teenagerMyIcons;
            $response['login'] = 1;
            $response['status'] = 1;
            $response['message'] = trans('appmessages.default_success_msg');
            $response['data'] = $data; 
        } else {
            $response['message'] = trans('appmessages.invalid_userid_msg') . ' or ' . trans('appmessages.notvarified_user_msg');
        }
        return response()->json($response, 200);
        exit;
    }

    /* Request Params : getTeenagerEarnAchievement
     *  loginToken, userId
     *  Service after loggedIn user
     */
    public function getTeenagerEarnAchievement(Request $request) {
        $response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg') ] ;
        $teenager = $this->teenagersRepository->getTeenagerById($request->userId);
        if($request->userId != "" && $teenager) {
            //Achievements Section achieved points details
            $basicBoosterPoint = Helpers::getTeenagerBasicBooster($request->userId);
            $teenagerAchievedPoints = (isset($basicBoosterPoint['total']) && $basicBoosterPoint['total'] > 0) ? $basicBoosterPoint['total'] : 0;
            $achievementBadgeCount = 0;
            if (isset($teenagerAchievedPoints) && $teenagerAchievedPoints > 0) {
                $achievementBadgeCount = Helpers::calculateBadgeCount(Config::get('constant.ACHIEVEMENT_BADGE_ARRAY'), $teenagerAchievedPoints);
            }
            //Achievements Section career completed details
            $careerCompletedCount = $this->objLevel4ProfessionProgress->getCompletedProfessionCountByTeenId($request->userId);
            $careerBadgeCount = 0;
            if (isset($careerCompletedCount) && $careerCompletedCount > 0) {
                $careerBadgeCount = Helpers::calculateBadgeCount(Config::get('constant.CAREER_COMPLETED_BADGE_ARRAY'), $careerCompletedCount);
            }
            //Achievements Section connection details
            $myConnectionsCount = $this->communityRepository->getMyConnectionsCount($request->userId);
            $connectionBadgeCount = 0;
            if (isset($myConnectionsCount) && $myConnectionsCount > 0) {
                $connectionBadgeCount = Helpers::calculateBadgeCount(Config::get('constant.CONNECTION_BADGE_ARRAY'), $myConnectionsCount);
            }

            //Achievement array
            $acheivementArr = [];
            $acheivementArr['type'] = "points_achieved";
            $acheivementArr['name'] = "Points Achieved";
            $acheivementArr['color'] = "#ff5f44";
            $acheivementArr['achievementsCount'] = $achievementBadgeCount + $careerBadgeCount + $connectionBadgeCount;
            $acheivementArr['child_data'] = [];
            for ($achievementBadges = 1; $achievementBadges <= 5; $achievementBadges++) { 
                $achievementChildData = [];
                $achievementChildData['badge_name'] = 'POINTS ACHIEVED';
                $achievementChildData['badge_point'] = Config::get('constant.ACHIEVEMENT_DISPLAY_BADGE_ARRAY')[$achievementBadges];
                if ($achievementBadgeCount > 0 && $achievementBadges <= $achievementBadgeCount) { 
                    $achievementChildData['badge_active'] = 1;
                    $achievementChildData['badge_image'] = Storage::url('img/badge-orange.png');
                    $achievementChildData['badge_color'] = "#ff5f44";
                } else {
                    $achievementChildData['badge_active'] = 0;
                    $achievementChildData['badge_image'] = Storage::url('img/badge-grey.png');
                    $achievementChildData['badge_color'] = "#c8cbce";
                }
                $acheivementArr['child_data'][] = $achievementChildData;
            }

            //Career completed array
            $careerArr = [];
            $careerArr['type'] = "careers_completed";
            $careerArr['name'] = "Careers Completed";
            $careerArr['color'] = "#27a6b5";
            $careerArr['child_data'] = [];
            for ($careerBadges = 1; $careerBadges <= 5; $careerBadges++) { 
                $careerChildData = [];
                $careerChildData['badge_name'] = 'Careers Completed';
                $careerChildData['badge_point'] = Config::get('constant.CAREER_COMPLETED_BADGE_ARRAY')[$careerBadges];
                if ($careerBadgeCount > 0 && $careerBadges <= $careerBadgeCount) { 
                    $careerChildData['badge_active'] = 1;
                    $careerChildData['badge_image'] = Storage::url('img/badge-blue.png');
                    $careerChildData['badge_color'] = "#27a6b5";
                } else {
                    $careerChildData['badge_active'] = 0;
                    $careerChildData['badge_image'] = Storage::url('img/badge-grey.png');
                    $careerChildData['badge_color'] = "#c8cbce";
                }
                $careerArr['child_data'][] = $careerChildData;
            }

            //Connection badges array
            $connectionArr = [];
            $connectionArr['type'] = "connections_made";
            $connectionArr['name'] = "Connections Made";
            $connectionArr['color'] = "#73376d";
            $connectionArr['child_data'] = [];
            for ($connectionBadges = 1; $connectionBadges <= 5; $connectionBadges++) { 
                $connChildData = [];
                $connChildData['badge_name'] = 'CONNECTIONS MADE';
                $connChildData['badge_point'] = Config::get('constant.CONNECTION_BADGE_ARRAY')[$connectionBadges];
                if ($connectionBadgeCount > 0 && $connectionBadges <= $connectionBadgeCount) { 
                    $connChildData['badge_active'] = 1;
                    $connChildData['badge_image'] = Storage::url('img/badge-purple.png');
                    $connChildData['badge_color'] = "#27a6b5";
                } else {
                    $connChildData['badge_active'] = 0;
                    $connChildData['badge_image'] = Storage::url('img/badge-grey.png');
                    $connChildData['badge_color'] = "#c8cbce";
                }
                $connectionArr['child_data'][] = $connChildData;
            }
            $data = [];
            $data[] = $acheivementArr;
            $data[] = $careerArr;
            $data[] = $connectionArr;
            $response['login'] = 1;
            $response['status'] = 1;
            $response['message'] = trans('appmessages.default_success_msg');
            $response['data'] = $data;
        } else {
            $response['message'] = trans('appmessages.invalid_userid_msg') . ' or ' . trans('appmessages.notvarified_user_msg');
        }
        return response()->json($response, 200);
        exit;
    }
}