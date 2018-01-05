<?php

namespace App\Http\Controllers\Webservice;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Helpers;
use Config;
use App\Services\Teenagers\Contracts\TeenagersRepository;
use Storage;
use Input;
use App\TeenagerLoginToken;
use App\Http\Requests\TeenagerPairRequest;
use Event;
use App\Events\SendMail;
use App\Services\Parents\Contracts\ParentsRepository;
use App\Services\Template\Contracts\TemplatesRepository;
use Mail;
use Redirect;
use Response;

class ParentController extends Controller
{
    public function __construct(TemplatesRepository $templatesRepository, TeenagersRepository $teenagersRepository, ParentsRepository $parentsRepository)
    {
        $this->teenagersRepository = $teenagersRepository;
        $this->objTeenagerLoginToken = new TeenagerLoginToken();
        $this->parentsRepository = $parentsRepository;
        $this->templateRepository = $templatesRepository;
        $this->teenOriginalImageUploadPath = Config::get('constant.TEEN_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->teenThumbImageUploadPath = Config::get('constant.TEEN_THUMB_IMAGE_UPLOAD_PATH');
        $this->parentOriginalImageUploadPath = Config::get('constant.PARENT_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->parentThumbImageUploadPath = Config::get('constant.PARENT_THUMB_IMAGE_UPLOAD_PATH');
    }

    /* Request Params : getParentList
    *  userId, loginToken, userType
    *  
    */
    public function getParentList(Request $request)
    {
		$response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg') ] ;
    	if($request->userId != "") {
            $teenager = $this->teenagersRepository->getTeenagerDetailById($request->userId);
            if($request->userId != "" && $teenager) {
                $type = ($request->userType != "") ? $request->userType : '0';
                $parentDetail = $this->teenagersRepository->getParentListByTeenagerId($request->userId, $type);
                $data = [];
                foreach ($parentDetail AS $key => $value) {
                    $parentData = [];
                    $parentData['parent_id'] = $value->ptp_parent_id;
                    $parentData['teenager_id'] = $value->ptp_teenager;
                    $parentData['parent_name'] = $value->p_first_name." ".$value->p_last_name;
                    $parentPhoto = $value->p_photo;
                    if ($parentPhoto != '') {
                        $parentData['p_photo'] = Storage::url($this->parentOriginalImageUploadPath . $parentPhoto);
                    } else {
                        $parentData['p_photo'] = Storage::url($this->parentOriginalImageUploadPath . 'proteen-logo.png');
                    }
                    $data[] = $parentData;
                }
                $response['status'] = 1;
                $response['login'] = 1;
                $response['message'] = trans('appmessages.default_success_msg');
                $response['data'] = $data;
            } else {
                $response['message'] = trans('appmessages.missing_data_msg');
            } 
    	} else {
    		$response['message'] = trans('appmessages.missing_data_msg');
    	}
    	
    	return response()->json($response, 200);
    	exit;
    }
    /* Request Params : parentTeenPair
    *  userId, loginToken, userType, parentEmail //userType "1" / "2" as parent/counselor
    *  
    */
    public function parentTeenPair(Request $request)
    {
        $response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg') ] ;
        if($request->userId != "" && $request->parentEmail != "" && $request->userType != "") {
            $teenager = $this->teenagersRepository->getTeenagerDetailById($request->userId);
            if($request->userId != "" && $teenager) {
                $parentDetail = [];
                $parentDetail['p_email'] = $request->parentEmail;
                $parentDetail['p_user_type'] = $request->userType;
                $parentDetail['deleted'] = '1';

                $parentTeenagerEmailExist = $this->teenagersRepository->checkActiveEmailExist($request->parentEmail);
                if (isset($parentteenagerEmailExist) && $parentteenagerEmailExist) {
                    $response['login'] = 1;
                    $response['status'] = 1;
                    $response['message'] = 'Same email already exist for teenager, Please use different one.';
                } else {
                    $parentEmailExist = $this->parentsRepository->checkActiveEmailExist($request->parentEmail);
                    $checkPairAvailability = $getParentDetailByEmailId = [];
                    if ($parentEmailExist) {
                        $getParentDetailByEmailId = $this->parentsRepository->getParentDetailByEmailId($request->parentEmail);
                        $checkPairAvailability = $this->parentsRepository->checkPairAvailability($teenager->id, $getParentDetailByEmailId->id);
                    }

                    if ($checkPairAvailability && count($checkPairAvailability) > 0) {
                        if ($checkPairAvailability->ptp_is_verified == 0) {
                            if ($checkPairAvailability->ptp_sent_by == "parent") {
                                $response['message'] = trans('Invitation already sent by them. Verification link emailed to you. Please, complete verification process.');
                            } else {
                                $response['message'] = trans('Invitation already sent by you. Verification link emailed to them. Please, complete verification process.');
                            }
                            $response['status'] = 1;
                            $response['login'] = 1;
                        } else {
                            $response['status'] = 1;
                            $response['message'] = trans('You already paired with this user');
                            $response['login'] = 1;
                        }
                    } else {
                        $parentData = [];
                        $parentId = 0;
                        if (!$parentEmailExist) {
                            $parentData = $this->parentsRepository->saveParentDetail($parentDetail);
                            if (!empty($parentData)) {
                                $parentData = $parentData->toArray();
                                $parentId = $parentData['id'];
                            }
                        } else {
                            $parentData = $this->parentsRepository->getParentDetailByEmailId($parentDetail['p_email']);
                            $parentData = $parentData->toArray();
                            $parentId = $parentData['id'];
                        }
                        // --------------------start sending mail -----------------------------//
                        $replaceArray = array();
                        $replaceArray['PARENT_NAME'] = (isset($parentData['p_first_name']) && !empty($parentData['p_first_name'])) ? $parentData['p_first_name'] : "";
                        
                        if($parentDetail['p_user_type'] == 1) {
                            $replaceArray['PARENT_SET_PROFILE_URL'] = url("parent/set-profile");
                            $replaceArray['PARENT_LOGIN_URL'] = url("parent/login");
                        } else {
                            $replaceArray['PARENT_SET_PROFILE_URL'] = url("counselor/set-profile");
                            $replaceArray['PARENT_LOGIN_URL'] = url("counselor/login");
                        }
                        $replaceArray['PARENT_EMAIL'] = $parentData['p_email'];
                        $replaceArray['PARENT_PASSWORD'] = "********"; //bcrypt(str_random(10));
                        $replaceArray['PARENT_UNIQUEID'] = Helpers::getParentUniqueId();
                        $replaceArray['VERIFICATION_URL'] = url("parent/verify-parent-teen-pair-registration?token=" . $replaceArray['PARENT_UNIQUEID']);
                        $replaceArray['USERNAME'] = ucwords($teenager->t_name." ".$teenager->t_lastname);
                        
                        if (isset($parentEmailExist) && $parentEmailExist) {
                            $emailTemplateContent = $this->templateRepository->getEmailTemplateDataByName(Config::get('constant.PARENT_TEEN_SECOND_TIME'));
                            $content = $this->templateRepository->getEmailContent($emailTemplateContent->et_body, $replaceArray);
                        } else {
                            $emailTemplateContent = $this->templateRepository->getEmailTemplateDataByName(Config::get('constant.PARENT_TEENAGER_VAIRIFIED_EMAIL_TEMPLATE_NAME'));
                            $content = $this->templateRepository->getEmailContent($emailTemplateContent->et_body, $replaceArray);
                        }
                        
                        $data = array();
                        $data['subject'] = $emailTemplateContent->et_subject;
                        $data['toEmail'] = $parentData['p_email'];
                        $data['toName'] = (isset($parentData['p_first_name']) && !empty($parentData['p_first_name'])) ? $parentData['p_first_name'] : "";
                        $data['content'] = $content;
                        $data['ptp_token'] = $replaceArray['PARENT_UNIQUEID'];
                        $data['parent_id'] = $parentData['id'];
                        $data['parent_token'] = $replaceArray['PARENT_UNIQUEID'];
                        $data['teen_id'] = $teenager->id;
                        
                        Event::fire(new SendMail("emails.Template", $data));
                        
                        $parentTeenVerificationData['ptp_parent_id'] = $data['parent_id'];
                        $parentTeenVerificationData['ptp_teenager'] = $data['teen_id'];
                        $parentTeenVerificationData['ptp_is_verified'] = 0;
                        $parentTeenVerificationData['ptp_sent_by'] = 'teen';
                        $parentTeenVerificationData['ptp_token'] = $data['parent_token'];

                        $this->teenagersRepository->saveParentTeenVerification($parentTeenVerificationData);

                        // ------------------------end sending mail ----------------------------//
                        $response['message'] = "Your invitation has been sent successfully.";
                        $response['login'] = 1;
                        $response['status'] = 1;
                    }
                }
            } else {
                $response['message'] = trans('appmessages.invalid_userid_msg');
            } 
        } else {
            $response['message'] = trans('appmessages.missing_data_msg');
        }
        return response()->json($response, 200);
        exit;
    }
}