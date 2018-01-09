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

    /* Request Params : getDashboardDetail
    *  loginToken, userId
    */
    public function getDashboardDetail(Request $request) {
        $response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg') ] ;
        $teenager = $this->teenagersRepository->getTeenagerById($request->userId);
        if($teenager) {
            $teenager->t_birthdate = (isset($teenager->t_birthdate) && $teenager->t_birthdate != '0000-00-00') ? Carbon::parse($teenager->t_birthdate)->format('d/m/Y') : '';
            //Teenager Image
            $teenager->t_photo_thumb = "";
            if ($teenager->t_photo != '') {
                $teenager->t_photo_thumb = Storage::url($this->teenThumbImageUploadPath . $teenager->t_photo);
                $teenager->t_photo = Storage::url($this->teenOriginalImageUploadPath . $teenager->t_photo);
            }
            
            $teenager->c_code = ( isset(Country::getCountryDetail($teenager->t_country)->c_code) ) ? Country::getCountryDetail($teenager->t_country)->c_code : "";
            $teenager->c_name = ( isset(Country::getCountryDetail($teenager->t_country)->c_name) ) ? Country::getCountryDetail($teenager->t_country)->c_name : "";
            $teenager->country_id = $teenager->t_country;

            $teenager->progress = 23;
            $teenager->total_points = 10000;
            $teenager->recent_progress = "You advanced 7% on your last visit. Well done you!";

            $response['login'] = 1;
            $response['status'] = 1;
            $response['message'] = trans('appmessages.default_success_msg');
            $response['data'] = $teenager;
        } else {
            $response['message'] = trans('appmessages.invalid_userid_msg') . ' or ' . trans('appmessages.notvarified_user_msg');
        }
        return response()->json($response, 200);
        exit;
    }

    /* Request Params : getInterestDetail
    *  loginToken, userId
    */
    public function getInterestDetail(Request $request) {
        $response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg') ] ;
        $teenager = $this->teenagersRepository->getTeenagerById($request->userId);
        if($teenager) {
            //Dummy Records
            $array = array ( 'people' => "People", 'nature' => "Nature", 'technical' => "Technical", 'creative-fine-arts' => "Creative Fine Arts", 'numerical' => "Numerical", 'computers' => "Computers", 'research' => "Research", 'performing-arts' => "Performing Arts", 'social' => "Social", 'sports' => "Sports", 'language' => "Language", 'artistic' => "Artistic", 'musical' => "Musical");
            
            $teenagerAPIData = Helpers::getTeenInterestAndStregnthDetails($request->userId);
            $teenagerInterest = isset($teenagerAPIData['APIscore']['interest']) ? $teenagerAPIData['APIscore']['interest'] : $array;
            foreach($teenagerInterest as $tiNameKey => $tiPoint) {
                $dataArray[] = (array('points' => rand(0,100), 'slug' => $tiNameKey, 'link' => url('teenager/interest/').'/'.$tiNameKey, 'name' => $array[$tiNameKey]) );
            }
            
            $response['login'] = 1;
            $response['status'] = 1;
            $response['message'] = trans('appmessages.default_success_msg');
            $response['data'] = $dataArray;
        } else {
            $response['message'] = trans('appmessages.invalid_userid_msg') . ' or ' . trans('appmessages.notvarified_user_msg');
        }
        return response()->json($response, 200);
        exit;
    }

    /* Request Params : getStrengthDetail
    *  loginToken, userId
    */
    public function getStrengthDetail(Request $request) {
        $response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg') ] ;
        $teenager = $this->teenagersRepository->getTeenagerById($request->userId);
        if($teenager) {
            //Dummy Records
            $array = array ( 'people' => 0, 'nature' => 0, 'technical' => 0, 'creative-fine-arts' => 0, 'numerical' => 0, 'computers' => 0, 'research' => 0, 'performing-arts' => 0, 'social' => 0, 'sports' => 0, 'language' => 0, 'artistic' => 0, 'musical' => 0);
            
            $teenagerAPIData = Helpers::getTeenInterestAndStregnthDetails($request->userId);
            $teenagerMI = isset($teenagerAPIData['APIscale']['MI']) ? $teenagerAPIData['APIscale']['MI'] : [];
            foreach($teenagerMI as $miKey => $miVal) {
                $mitName = Helpers::getMIBySlug($miKey);
                $teenagerMI[$miKey] = (array('slug' => $miKey, 'points' => 0, 'score' => $miVal, 'name' => $mitName, 'type' => Config::get('constant.MULTI_INTELLIGENCE_TYPE'), 'link_url' => url('/teenager/multi-intelligence/').'/'.Config::get('constant.MULTI_INTELLIGENCE_TYPE').'/'.$miKey));
            }

            $teenagerAptitude = isset($teenagerAPIData['APIscale']['aptitude']) ? $teenagerAPIData['APIscale']['aptitude'] : [];
            foreach($teenagerAptitude as $apptitudeKey => $apptitudeVal) {
                $aptName = Helpers::getApptitudeBySlug($apptitudeKey);
                $teenagerAptitude[$apptitudeKey] = (array('points' => 0, 'score' => $apptitudeVal, 'name' => $aptName, 'type' => Config::get('constant.APPTITUDE_TYPE'), 'link_url' => url('/teenager/multi-intelligence/').'/'.Config::get('constant.APPTITUDE_TYPE').'/'.$apptitudeKey));
            }
            $teenagerPersonality = isset($teenagerAPIData['APIscale']['personality']) ? $teenagerAPIData['APIscale']['personality'] : [];
            foreach($teenagerPersonality as $personalityKey => $personalityVal) {
                $ptName = Helpers::getPersonalityBySlug($personalityKey);
                $teenagerPersonality[$personalityKey] = (array('points' => 0, 'score' => $personalityVal, 'name' => $ptName, 'type' => Config::get('constant.PERSONALITY_TYPE'), 'link_url' => url('/teenager/multi-intelligence/').'/'.Config::get('constant.PERSONALITY_TYPE').'/'.$personalityKey));
            }

            $teenagerStrength = array_merge($teenagerAptitude, $teenagerPersonality, $teenagerMI);
            //Dummy array
            $array = array ('scientific-reasoning' => array ('points' => 0,'score' => '','name' => 'Scientific Reasoning','type' => 'apptitude','link_url' => 'http://local.inexture.com/teenager/multi-intelligence/apptitude/scientific-reasoning',
                    ),'verbal-reasoning' => array ('points' => 25,'score' => '','name' => 'Verbal Reasoning','type' => 'apptitude','link_url' => 'http://local.inexture.com/teenager/multi-intelligence/apptitude/verbal-reasoning',
                    ),'numerical-ability' => array ('points' => 25,'score' => '','name' => 'Numerical Ability','type' => 'apptitude','link_url' => 'http://local.inexture.com/teenager/multi-intelligence/apptitude/numerical-ability',
                    ),'logical-reasoning' => array ('points' => 25,'score' => '','name' => 'Logical Reasoning','type' => 'apptitude','link_url' => 'http://local.inexture.com/teenager/multi-intelligence/apptitude/logical-reasoning',
                    ),'social-ability' => array ('points' => 15,'score' => '','name' => 'Social Ability','type' => 'apptitude','link_url' => 'http://local.inexture.com/teenager/multi-intelligence/apptitude/social-ability',
                    ),'artistic-ability' => array ('points' => 15,'score' => '','name' => 'Artistic Ability','type' => 'apptitude','link_url' => 'http://local.inexture.com/teenager/multi-intelligence/apptitude/artistic-ability',
                    ),'spatial-ability' => array ('points' => 35,'score' => '','name' => 'Spatial Ability','type' => 'apptitude','link_url' => 'http://local.inexture.com/teenager/multi-intelligence/apptitude/spatial-ability',
                    ),'creativity' => array ('points' => 35,'score' => '','name' => 'Creativity','type' => 'apptitude','link_url' => 'http://local.inexture.com/teenager/multi-intelligence/apptitude/creativity',
                    ),'clerical-ability' => array ('points' => 35,'score' => '','name' => 'Clerical Ability','type' => 'apptitude','link_url' => 'http://local.inexture.com/teenager/multi-intelligence/apptitude/clerical-ability',
                    ),'conventional' => array ('points' => 85,'score' => 'H','name' => 'Conventional','type' => 'personality','link_url' => 'http://local.inexture.com/teenager/multi-intelligence/personality/conventional',
                    ),'enterprising' => array ('points' => 85,'score' => 'H','name' => 'Enterprising','type' => 'personality','link_url' => 'http://local.inexture.com/teenager/multi-intelligence/personality/enterprising',
                    ),'investigative' => array ('points' => 85,'score' => 'H','name' => 'Investigative','type' => 'personality','link_url' => 'http://local.inexture.com/teenager/multi-intelligence/personality/investigative',
                    ),'social' => array ('points' => 85,'score' => 'H','name' => 'Social','type' => 'personality','link_url' => 'http://local.inexture.com/teenager/multi-intelligence/personality/social',
                    ),'artistic' => array ('points' => 90,'score' => 'H','name' => 'Artistic','type' => 'personality','link_url' => 'http://local.inexture.com/teenager/multi-intelligence/personality/artistic',
                    ),'mechanical' => array ('points' => 90,'score' => 'H','name' => 'Mechanical','type' => 'personality','link_url' => 'http://local.inexture.com/teenager/multi-intelligence/personality/mechanical',
                    ),'interpersonal' => array ('points' => 90,'score' => '','name' => 'Interpersonal','type' => 'mi','link_url' => 'http://local.inexture.com/teenager/multi-intelligence/mi/interpersonal',
                    ),'logical' => array ('points' => 75,'score' => '','name' => 'Logical','type' => 'mi','link_url' => 'http://local.inexture.com/teenager/multi-intelligence/mi/logical',
                    ),'linguistic' => array ('points' => 75,'score' => '','name' => 'Linguistic','type' => 'mi','link_url' => 'http://local.inexture.com/teenager/multi-intelligence/mi/linguistic',
                    ),'intrapersonal' => array ('points' => 75,'score' => '','name' => 'Intrapersonal','type' => 'mi','link_url' => 'http://local.inexture.com/teenager/multi-intelligence/mi/intrapersonal',
                    ),'musical' => array ('points' => 10,'score' => '','name' => 'Musical','type' => 'mi','link_url' => 'http://local.inexture.com/teenager/multi-intelligence/mi/musical',
                    ),'spatial' => array ('points' => 10,'score' => '','name' => 'Spatial','type' => 'mi','link_url' => 'http://local.inexture.com/teenager/multi-intelligence/mi/spatial',
                    ),'bodilykinesthetic' => array ('points' => 10,'score' => '','name' => 'Bodily-Kinesthetic','type' => 'mi','link_url' => 'http://local.inexture.com/teenager/multi-intelligence/mi/bodilykinesthetic',
                    ),'naturalist' => array ('points' => 0,'score' => '','name' => 'Naturalist','type' => 'mi','link_url' => 'http://local.inexture.com/teenager/multi-intelligence/mi/naturalist',
                    ),'existential' => array ('points' => 0,'score' => '','name' => 'Existential','type' => 'mi','link_url' => 'http://local.inexture.com/teenager/multi-intelligence/mi/existential',
                    ));
            
            $response['login'] = 1;
            $response['status'] = 1;
            $response['message'] = trans('appmessages.default_success_msg');
            $response['data'] = $array;
        } else {
            $response['message'] = trans('appmessages.invalid_userid_msg') . ' or ' . trans('appmessages.notvarified_user_msg');
        }
        return response()->json($response, 200);
        exit;
    }
}