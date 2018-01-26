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
use App\Services\Template\Contracts\TemplatesRepository;
use App\Services\Sponsors\Contracts\SponsorsRepository;
use App\Teenagers;
use App\TeenagerLoginToken;
use App\DeviceToken;
use Storage;
use Carbon\Carbon;
use Input;
use Mail;
use Image;
use App\Country;

class SignupController extends Controller
{
    public function __construct(SponsorsRepository $sponsorsRepository, TemplatesRepository $templatesRepository, TeenagersRepository $teenagersRepository, FileStorageRepository $fileStorageRepository)
    {
        $this->teenagersRepository = $teenagersRepository;
        $this->fileStorageRepository = $fileStorageRepository;
        $this->templateRepository = $templatesRepository;
        $this->sponsorsRepository = $sponsorsRepository;
        $this->objTeenagerLoginToken = new TeenagerLoginToken();
        $this->objDeviceToken = new DeviceToken();
        $this->teenOriginalImageUploadPath = Config::get('constant.TEEN_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->teenThumbImageUploadPath = Config::get('constant.TEEN_THUMB_IMAGE_UPLOAD_PATH');
        $this->teenProfileImageUploadPath = Config::get('constant.TEEN_PROFILE_IMAGE_UPLOAD_PATH');
        
        $this->sponsorOriginalImageUploadPath = Config::get('constant.SPONSOR_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->sponsorThumbImageUploadPath = Config::get('constant.SPONSOR_THUMB_IMAGE_UPLOAD_PATH');
        $this->teenThumbImageHeight = Config::get('constant.TEEN_THUMB_IMAGE_HEIGHT');
        $this->teenThumbImageWidth = Config::get('constant.TEEN_THUMB_IMAGE_WIDTH');
        $this->objCountry = new Country;
        
    }

    /* Request Params : signup
    *  email, password, 
    *  No loginToken required because it's call without loggedin user
    */
    public function signup(Request $request)
    {
        $response = [ 'status' => 0, 'message' => trans('appmessages.default_error_msg') ] ;
    	if($request->deviceId == "" || $request->deviceType == "") {
            $response['message'] = "DeviceId and Device Type can not be null.";
            return response()->json($response, 200);
            exit;
        }
        if($request->email != "") {
            $teenagerDetail['t_uniqueid'] = Helpers::getTeenagerUniqueId();
            $teenagerDetail['t_name'] = $request->name;
            $teenagerDetail['t_lastname'] = $request->lastname;
            $teenagerDetail['t_nickname'] = $request->proteenCode;
            $teenagerDetail['t_gender'] = (in_array($request->gender, ['1','2'])) ? $request->gender : '1';
            $teenagerDetail['password'] = ($request->password != "") ? bcrypt($request->password) : "";
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
            $teenagerDetail['t_device_type'] = $request->deviceType;
            $teenagerDetail['t_photo'] = '';
            $teenagerDetail['deleted'] = '1';

            //Social Providers
            $teenagerDetail['t_social_provider'] = ($request->socialProvider != "") ? $request->socialProvider : 'Normal';
            $teenagerDetail['t_social_identifier'] = ($request->socialId && $request->socialId != '') ? $request->socialId : '';
            $teenagerDetail['t_social_accesstoken'] = ($request->socialAccessToken && $request->socialAccessToken != '') ? $request->socialAccessToken : '';
            $teenagerDetail['t_sponsor_choice'] = '2';

            //Birthdate
            $teenagerDetail['t_birthdate'] = '';
            if (isset($request->birthDate) && $request->birthDate != '') {
                $dob = $request->birthDate;
                $dobDate = str_replace('/', '-', $dob);
                $teenagerDetail['t_birthdate'] = date("Y-m-d", strtotime($dobDate));
            }

            //On-Off Buttons
            $teenagerDetail['is_search_on'] = ( $request->publicProfile != "") ? $request->publicProfile : "0";
            $teenagerDetail['is_share_with_teachers'] = ( $request->shareWithTeachers != "") ? $request->shareWithTeachers : "0";
            $teenagerDetail['is_share_with_other_members'] = ( $request->shareWithMembers != "") ? $request->shareWithMembers : "0";
            $teenagerDetail['is_share_with_parents'] = ( $request->shareWithParents != "") ? $request->shareWithParents : "0";
            $teenagerDetail['is_notify'] = ( $request->notifications != "") ? $request->notifications : "0";
            $teenagerDetail['t_view_information'] = ( $request->viewInformation != "") ? $request->viewInformation : "0";
            $teenagerDetail['is_sound_on'] = ( $request->isSoundOn != "") ? $request->isSoundOn : "0";

            $teenagerDetail['t_email'] = $request->email;
            $teenagerDetail['t_phone'] = $request->mobile;
            
            if ($teenagerDetail['t_email'] != '' && $teenagerDetail['t_social_provider'] == "Normal") {
                $teenagerEmailExist = $this->teenagersRepository->checkActiveEmailExist($teenagerDetail['t_email']);
            }
            if ($teenagerDetail['t_phone'] != '' && $teenagerDetail['t_social_provider'] == 'Normal') {
                $teenagerMobileExist = $this->teenagersRepository->checkActivePhoneExist($teenagerDetail['t_phone']);
            }
            //If Email, Mobile exist within Normal user Return back with message
            if (isset($teenagerEmailExist) && $teenagerEmailExist) {
                $response['message'] = trans('appmessages.userwithsameemailaddress');
            } else if (isset($teenagerMobileExist) && $teenagerMobileExist) {
                $response['message'] = trans('appmessages.userwithsamenumber');
            } else {
                if (isset($teenagerDetail['t_social_provider']) && $teenagerDetail['t_social_provider'] != '' && ($teenagerDetail['t_social_provider'] == 'Facebook' || $teenagerDetail['t_social_provider'] == 'Google')) {
                    if (filter_var($teenagerDetail['t_email'], FILTER_VALIDATE_EMAIL)) {
                        
                        $teenagerDataBySocialId = $this->teenagersRepository->getTeenagerBySocialId($teenagerDetail['t_social_identifier'], $teenagerDetail['t_social_provider']);
                        $teenagerDataByEmailId = $this->teenagersRepository->getTeenagerDetailByEmailId($teenagerDetail['t_email']);
                        $recordData = [];

                        if($teenagerDataBySocialId && $teenagerDataByEmailId) {
                            if($teenagerDataBySocialId->id != $teenagerDataByEmailId->id)
                            {
                                $recordData['t_email'] = "";
                            }
                            $recordData['id'] = $teenagerDataBySocialId->id;
                            $recordData['t_photo'] = $teenagerDataBySocialId->t_photo;
                        
                        } else if($teenagerDataBySocialId && !$teenagerDataByEmailId) {
                            
                            $recordData['id'] = $teenagerDataBySocialId->id;
                            $recordData['t_email'] = $teenagerDataBySocialId->t_email;
                            $recordData['t_photo'] = $teenagerDataBySocialId->t_photo;

                        } else if(!$teenagerDataBySocialId && $teenagerDataByEmailId) {
                            $recordData['id'] = $teenagerDataByEmailId->id;
                            $recordData['t_email'] = $teenagerDataByEmailId->t_email;
                            $recordData['t_photo'] = $teenagerDataByEmailId->t_photo;

                        } else {
                            $recordData['t_email'] = $teenagerDetail['t_email'];
                            $recordData['t_name'] = $teenagerDetail['t_name'];
                            $recordData['t_lastname'] = $teenagerDetail['t_lastname'];
                            $recordData['t_birthdate'] = $teenagerDetail['t_birthdate'];
                            $recordData['t_photo'] = "";
                            $recordData['t_uniqueid'] = $teenagerDetail['t_uniqueid'];
                        }

                        if ($teenagerDetail['t_social_provider'] == 'Google') {
                            $recordData['t_social_identifier'] = $teenagerDetail['t_social_identifier'];
                            $recordData['t_social_accesstoken'] = $teenagerDetail['t_social_accesstoken'];
                        } else {
                            $recordData['t_fb_social_identifier'] = $teenagerDetail['t_social_identifier'];
                            $recordData['t_fb_social_accesstoken'] = $teenagerDetail['t_social_accesstoken'];
                        }

                        $recordData['t_social_provider'] = $teenagerDetail['t_social_provider'];
                        $recordData['t_isverified'] = 1;
                        
                        if($recordData['t_photo'] == "" && Input::file())
                        {
                            if (Input::file()) {
                                $file = Input::file('profilePic');
                                if (!empty($file)) {
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
                                    $recordData['t_photo'] = $fileName;
                                }
                            }
                        }

                        $teenagerDetailSaved = $this->teenagersRepository->saveTeenagerDetail($recordData);
                        $teenagerDetailSaved->t_photo = ($teenagerDetailSaved->t_photo != "") ? Helpers::getTeenagerOriginalImageUrl($teenagerDetailSaved->t_photo) : "";
                        $teenagerDetailSaved->t_photo_thumb = ($teenagerDetailSaved->t_photo != "") ? Helpers::getTeenagerThumbImageUrl($teenagerDetailSaved->t_photo) : "";
                        //IF require then birthdate will be seprate in day,month,year in response
                        $teenagerDetailSaved->t_birthdate = ($teenagerDetailSaved->t_birthdate != '0000-00-00' ) ? Carbon::parse($teenagerDetailSaved->t_birthdate)->format('d/m/Y') : '';
                        $teenagerDetailSaved->t_sponsors = $this->teenagersRepository->getSelfSponserListData($teenagerDetailSaved->id);
                        if (isset($teenagerDetailSaved->t_sponsors)) {
                            foreach ($teenagerDetailSaved->t_sponsors as $sponsor) {
                                $sponsorPhoto = ($sponsor->sp_logo != "") ? $sponsor->sp_logo : "proteen-logo.png";
                                $sponsor->sp_logo = Storage::url($this->sponsorOriginalImageUploadPath . $sponsorPhoto);
                                $sponsor->sp_logo_thumb = Storage::url($this->sponsorThumbImageUploadPath . $sponsorPhoto);
                            }
                        }
                        //Save Login Token Data
                        $loginDetail['tlt_teenager_id'] = $teenagerDetailSaved->id;
                        $loginDetail['tlt_login_token'] = base64_encode($teenagerDetailSaved->t_email.':'.$teenagerDetailSaved->t_uniqueid);
                        $loginDetail['tlt_device_id'] = $request->deviceId;
                        $userTokenDetails = $this->objTeenagerLoginToken->saveTeenagerLoginDetail($loginDetail);
                        //Save Device Token Data
                        $saveData['tdt_user_id'] = $teenagerDetailSaved->id;
                        $saveData['tdt_device_token'] = ($request->pushToken != "") ? $request->pushToken : base64_encode($teenagerDetailSaved->t_email.':'.$teenagerDetailSaved->t_uniqueid);
                        $saveData['tdt_device_type'] = $request->deviceType;
                        $saveData['tdt_device_id'] = $request->deviceId;
                        $userDeviceDetails = $this->objDeviceToken->saveDeviceToken($saveData);

                        $response['loginToken'] = base64_encode($teenagerDetailSaved->t_email.':'.$teenagerDetailSaved->t_uniqueid);
                        
                        $response['status'] = 1;
                        $response['login'] = 1;
                        $response['message'] = trans('appmessages.default_success_msg');
                        $response['data'] = $teenagerDetailSaved->toArray();
                    } else {
                        $response['message'] = trans('appmessages.invalid_email_msg');
                    }
                } else if (isset($teenagerDetail['t_social_provider']) && $teenagerDetail['t_social_provider'] != '' && $teenagerDetail['t_social_provider'] == 'Normal') {
                    if ($teenagerDetail['t_email'] != '' && $teenagerDetail['password'] != '' && $teenagerDetail['t_name'] != '' && $teenagerDetail['t_lastname'] != '' && $teenagerDetail['t_birthdate'] != '' && $teenagerDetail['t_gender'] != '' && $teenagerDetail['t_country'] != '' && $teenagerDetail['t_pincode'] != '' && $teenagerDetail['t_sponsor_choice'] != '') {
                        if (!filter_var($teenagerDetail['t_email'], FILTER_VALIDATE_EMAIL)) {
                            $response['message'] = trans('appmessages.invalid_email_msg');
                        } else {
                            if (Input::file()) {
                                $file = Input::file('profilePic');
                                if (!empty($file)) {
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
                            $teenagerDetailSaved = $this->teenagersRepository->saveTeenagerDetail($teenagerDetail);
                            /* save sponser by teenager id if sponsor id is not blank */
                            $sponserDetailSave = $this->teenagersRepository->saveTeenagerSponserId($teenagerDetailSaved->id, $request->sponsorIds);
                            //Get Collection of teen and sponsors
                            $teenagerDetailbyId = $this->teenagersRepository->getTeenagerById($teenagerDetailSaved->id);
                            $teenagerDetailbyId->t_birthdate = (isset($teenagerDetailbyId->t_birthdate) && $teenagerDetailbyId->t_birthdate != '0000-00-00') ? Carbon::parse($teenagerDetailbyId->t_birthdate)->format('d/m/Y') : '';
                            
                            if ($teenagerDetailbyId->t_photo != '') {
                                $teenPhoto = $teenagerDetailbyId->t_photo;
                                $teenagerDetailbyId->t_photo = Helpers::getTeenagerOriginalImageUrl($teenPhoto);
                                $teenagerDetailbyId->t_photo_thumb = Helpers::getTeenagerThumbImageUrl($teenPhoto);
                            }
                            if (isset($teenagerDetailbyId->t_sponsors)) {
                                foreach ($teenagerDetailbyId->t_sponsors as $sponsor) {
                                    $sponsorPhoto = ($sponsor->sp_logo != "") ? $sponsor->sp_logo : "proteen-logo.png";
                                    $sponsor->sp_logo = Storage::url($this->sponsorOriginalImageUploadPath . $sponsorPhoto);
                                    $sponsor->sp_logo_thumb = Storage::url($this->sponsorThumbImageUploadPath . $sponsorPhoto);
                                }
                            }
                            //Send verification email to user
                            $replaceArray = array();
                            $replaceArray['TEEN_NAME'] = $teenagerDetailbyId->t_name;
                            $replaceArray['TEEN_UNIQUEID'] = Helpers::getTeenagerUniqueId();
                            $replaceArray['TEEN_URL'] = url("teenager/verify-teenager?token=" . $replaceArray['TEEN_UNIQUEID']);

                            $emailTemplateContent = $this->templateRepository->getEmailTemplateDataByName(Config::get('constant.TEENAGER_VAIRIFIED_EMAIL_TEMPLATE_NAME'));
                            $content = $this->templateRepository->getEmailContent($emailTemplateContent->et_body, $replaceArray);
                            $data = array();
                            $data['subject'] = $emailTemplateContent->et_subject;
                            $data['toEmail'] = $teenagerDetailbyId->t_email;
                            $data['toName'] = $teenagerDetailbyId->t_name;
                            $data['content'] = $content;
                            $data['teen_token'] = $replaceArray['TEEN_UNIQUEID'];
                            $data['teen_id'] = $teenagerDetailbyId->id;

                            Mail::send(['html' => 'emails.Template'], $data, function($message) use ($data) {
                                $message->subject($data['subject']);
                                $message->to($data['toEmail'], $data['toName']);

                                $teenagerTokenDetail = [];
                                $teenagerTokenDetail['tev_token'] = $data['teen_token'];
                                $teenagerTokenDetail['tev_teenager'] = $data['teen_id'];
                                $this->teenagersRepository->addTeenagerEmailVarifyToken($teenagerTokenDetail);
                            });

                            //Save Login Token Data
                            $loginDetail['tlt_teenager_id'] = $teenagerDetailbyId->id;
                            $loginDetail['tlt_login_token'] = base64_encode($teenagerDetailbyId->t_email.':'.$teenagerDetailbyId->t_uniqueid);
                            $loginDetail['tlt_device_id'] = $request->deviceId;
                            $userTokenDetails = $this->objTeenagerLoginToken->saveTeenagerLoginDetail($loginDetail);
                            //Save Device Token Data
                            $saveData['tdt_user_id'] = $teenagerDetailbyId->id;
                            $saveData['tdt_device_token'] = ($request->pushToken != "") ? $request->pushToken : base64_encode($teenagerDetailbyId->t_email.':'.$teenagerDetailbyId->t_uniqueid);
                            $saveData['tdt_device_type'] = $request->deviceType;
                            $saveData['tdt_device_id'] = $request->deviceId;
                            $userDeviceDetails = $this->objDeviceToken->saveDeviceToken($saveData);

                            $response['loginToken'] = base64_encode($teenagerDetailbyId->t_email.':'.$teenagerDetailbyId->t_uniqueid);
                            
                            $response['status'] = 1;
                            $response['message'] = trans('appmessages.signupmail_success_msg');
                            $response['data'] = $teenagerDetailbyId;
                        }
                    } else {
                        $response['message'] = trans('appmessages.missing_data_msg');
                    }
                } else {
                    $response['message'] = trans('appmessages.missing_data_msg');
                }
            }
    	} else {
    		$response['message'] = "Email Id is must be required!";
    	}
    	return response()->json($response, 200);
    	exit;
    }
}