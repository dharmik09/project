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
        $this->sponsorOriginalImageUploadPath = Config::get('constant.SPONSOR_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->sponsorThumbImageUploadPath = Config::get('constant.SPONSOR_THUMB_IMAGE_UPLOAD_PATH');
        $this->teenThumbImageHeight = Config::get('constant.TEEN_THUMB_IMAGE_HEIGHT');
        $this->teenThumbImageWidth = Config::get('constant.TEEN_THUMB_IMAGE_WIDTH');
        
    }

    /* Request Params : signup
    *  email, password, device_id, device_type
    *  No loginToken required because it's call without loggedin user
    */
    public function signup(Request $request)
    {
        $response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg') ] ;
    	if($request->email != "") {
            $teenagerDetail['t_uniqueid'] = Helpers::getTeenagerUniqueId();
            $teenagerDetail['t_name'] = $request->name;
            $teenagerDetail['t_lastname'] = $request->lastname;
            $teenagerDetail['t_nickname'] = $request->proteen_code;
            $teenagerDetail['t_gender'] = (in_array($request->gender, ['1','2'])) ? $request->gender : '1';
            $teenagerDetail['password'] = ($request->password != "") ? bcrypt($request->password) : "";
            $teenagerDetail['t_phone_new'] = $request->phone;
            $teenagerDetail['t_country'] = $request->country;
            $teenagerDetail['t_pincode'] = $request->pincode;
            $teenagerDetail['t_device_type'] = $request->t_device_type;
            $teenagerDetail['t_photo'] = '';
            $teenagerDetail['deleted'] = '1';

            //Social Providers
            $teenagerDetail['t_social_provider'] = ($request->social_provider != "") ? $request->social_provider : '';
            $teenagerDetail['t_social_identifier'] = ($request->social_id && $request->social_id != '') ? $request->social_id : '';
            $teenagerDetail['t_social_accesstoken'] = ($request->social_accesstoken && $request->social_accesstoken != '') ? $request->social_accesstoken : '';
            $teenagerDetail['t_sponsor_choice'] = ($request->sponsor_choice != '') ? $request->sponsor_choice : '2';

            //Birthdate
            $day = $request->day;
            $month = $request->month;
            $year = $request->year;
            $teenagerDetail['t_birthdate'] = '';
            if($day != "" && $month != "" && $year != "") {
                $stringVariable = $year."-".$month."-".$day;
                $birthDate = Carbon::createFromFormat("Y-m-d", $stringVariable);
                $todayDate = Carbon::now();
                if (Helpers::validateDate($stringVariable, "Y-m-d") && $todayDate->gt($birthDate) ) {
                    $teenagerDetail['t_birthdate'] = $stringVariable;
                }
            }

            //On-Off Buttons
            $teenagerDetail['is_search_on'] = ( $request->public_profile != "") ? $request->public_profile : "0";
            $teenagerDetail['is_share_with_teachers'] = ( $request->share_with_teachers != "") ? $request->share_with_teachers : "0";
            $teenagerDetail['is_share_with_other_members'] = ( $request->share_with_members != "") ? $request->share_with_members : "0";
            $teenagerDetail['is_share_with_parents'] = ( $request->share_with_parents != "") ? $request->share_with_parents : "0";
            $teenagerDetail['is_notify'] = ( $request->notifications != "") ? $request->notifications : "0";
            $teenagerDetail['t_view_information'] = ( $request->t_view_information != "") ? $request->t_view_information : "0";
            $teenagerDetail['is_sound_on'] = ( $request->is_sound_on != "") ? $request->is_sound_on : "0";

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
                                $file = Input::file('profile_pic');
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
                        $teenagerDetailSaved->t_birthdate = ($teenagerDetailSaved->t_birthdate != '0000-00-00' ) ? $teenagerDetailSaved->t_birthdate : '';
                        $teenagerDetailSaved->year = ($teenagerDetailSaved->t_birthdate != "") ? Carbon::createFromFormat('Y-m-d', $teenagerDetailSaved->t_birthdate)->year : "";
                        $teenagerDetailSaved->day = ($teenagerDetailSaved->t_birthdate != "") ? Carbon::createFromFormat('Y-m-d', $teenagerDetailSaved->t_birthdate)->day : "";
                        $teenagerDetailSaved->month = ($teenagerDetailSaved->t_birthdate != "") ? Carbon::createFromFormat('Y-m-d', $teenagerDetailSaved->t_birthdate)->month : "";
                        $teenagerDetailSaved->t_sponsors = $this->teenagersRepository->getSelfSponserListData($teenagerDetailSaved->id);
                        $response['status'] = 1;
                        //$response['login'] = 1;
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
                                $file = Input::file('profile_pic');
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
                            $sponserDetailSave = $this->teenagersRepository->saveTeenagerSponserId($teenagerDetailSaved->id, $request->sponsor_id);
                            //Get Collection of teena and sponsors
                            $teenagerDetailbyId = $this->teenagersRepository->getTeenagerById($teenagerDetailSaved->id);
                            $teenagerDetailbyId->t_birthdate = (isset($teenagerDetailbyId->t_birthdate) && $teenagerDetailbyId->t_birthdate != '0000-00-00') ? $teenagerDetailbyId->t_birthdate : '';
                            $teenagerDetailbyId->year = ($teenagerDetailbyId->t_birthdate != "") ? Carbon::createFromFormat('Y-m-d', $teenagerDetailbyId->t_birthdate)->year : "";
                            $teenagerDetailbyId->day = ($teenagerDetailbyId->t_birthdate != "") ? Carbon::createFromFormat('Y-m-d', $teenagerDetailbyId->t_birthdate)->day : "";
                            $teenagerDetailbyId->month = ($teenagerDetailbyId->t_birthdate != "") ? Carbon::createFromFormat('Y-m-d', $teenagerDetailbyId->t_birthdate)->month : "";
                            
                            if ($teenagerDetailbyId->t_photo != '') {
                                $teenPhoto = $teenagerDetailbyId->t_photo;
                                $teenagerDetailbyId->t_photo = Helpers::getTeenagerOriginalImageUrl($teenPhoto);
                                $teenagerDetailbyId->t_photo_thumb = Helpers::getTeenagerThumbImageUrl($teenPhoto);
                            }
                            if (isset($teenagerDetailbyId->t_sponsors)) {
                                foreach ($teenagerDetailbyId->t_sponsors as $sponsor) {
                                    $sponsorPhoto = $sponsor->sp_logo;
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
    		$response['message'] = trans('appmessages.missing_data_msg');
    	}
    	return response()->json($response, 200);
    	exit;
    }
}