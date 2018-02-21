<?php

namespace App\Http\Controllers\Webservice;

use App\Http\Controllers\Controller;
use App\Services\Teenagers\Contracts\TeenagersRepository;
use App\Services\Professions\Contracts\ProfessionsRepository;
use Config;
use Storage;
use Helpers;  
use Auth;
use Input;
use Redirect;
use Illuminate\Http\Request;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use App\Services\Template\Contracts\TemplatesRepository;
use App\Services\Level4Activity\Contracts\Level4ActivitiesRepository;
use Mail;

class Level4AdvanceActivityController extends Controller {

    public function __construct(TeenagersRepository $teenagersRepository, ProfessionsRepository $professionsRepository, TemplatesRepository $templatesRepository, Level4ActivitiesRepository $level4ActivitiesRepository) 
    {
        $this->teenagersRepository = $teenagersRepository;
        $this->professionsRepository = $professionsRepository;
        $this->templatesRepository = $templatesRepository;
        $this->level4ActivitiesRepository = $level4ActivitiesRepository;
        $this->level4AdvanceThumbImageUploadPath = Config::get('constant.LEVEL4_ADVANCE_THUMB_IMAGE_UPLOAD_PATH');
        $this->level4AdvanceOriginalImageUploadPath = Config::get('constant.LEVEL4_ADVANCE_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->level4AdvanceThumbImageWidth = Config::get('constant.LEVEL4_ADVANCE_THUMB_IMAGE_WIDTH');
        $this->level4AdvanceThumbImageHeight = Config::get('constant.LEVEL4_ADVANCE_THUMB_IMAGE_HEIGHT');
        $this->log = new Logger('api-level4-advance-activity-controller');
        $this->log->pushHandler(new StreamHandler(storage_path().'/logs/monolog-'.date('m-d-Y').'.log'));    
        
    }

    /* Request Params : getL4AdvanceActivityMediaWiseDescription
     *  loginToken, userId, careerId, mediaType
     */
    public function getL4AdvanceActivityMediaWiseDescription(Request $request) 
    {
        $response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg') ] ;
        $teenager = $this->teenagersRepository->getTeenagerById($request->userId);
        if ($teenager) {
            if ($request->careerId != "" && $request->mediaType != "") {
                $data = [];
                $professionDetail = $this->professionsRepository->getProfessionsDataFromId($request->careerId);
                $activityData = $this->level4ActivitiesRepository->getLevel4AdvanceActivityByType($request->mediaType);
                if (strpos($activityData[0]->l4aa_description, '[PROFESSION_NAME]') !== false) {
                    $professionName = (isset($professionDetail) && !empty($professionDetail)) ? $professionDetail[0]->pf_name : '';
                    $descText = str_replace('[PROFESSION_NAME]', $professionName, $activityData[0]->l4aa_description);
                } else {
                    $descText = $activityData[0]->l4aa_description;
                }
                $data['careerId'] = $request->careerId;
                $data['title'] = $descText;
                $data['mediaType'] = $request->mediaType;
                $questionData = [];
                foreach($activityData as $key => $val) {
                    $questionArr = [];
                    $questionArr['id'] = $val->id;
                    if (strpos($val->l4aa_text, '[PROFESSION_NAME]') !== false) {
                        $professionName = (isset($professionDetail) && !empty($professionDetail)) ? $professionDetail[0]->pf_name : '';
                        $questionArr['activityDescription'] = str_replace('[PROFESSION_NAME]', $professionName, $val->l4aa_text);
                    } else {
                        $questionArr['activityDescription'] = $val->l4aa_text;
                    }
                    $questionArr['type'] = $val->l4aa_type;
                    $questionData[] = $questionArr;
                }
                $data['details'] = $questionData;
                //Store log in System
                $this->log->info('Retrieve L4 advance activity data', array('userId' => $request->userId));
                $response['login'] = 1;
                $response['status'] = 1;
                $response['message'] = trans('appmessages.default_success_msg');
                $response['data'] = $data;
            } else {
                $response['login'] = 1;
                $response['status'] = 0;
                $response['message'] = trans('appmessages.missing_data_msg');
            }
        } else {
            $response['message'] = trans('appmessages.invalid_userid_msg') . ' or ' . trans('appmessages.notvarified_user_msg');
        }
        return response()->json($response, 200);
        exit;
    }

    /* Request Params : getL4AdvanceActivityMediaWiseDescription
     *  loginToken, userId, careerId, mediaType
     */
    public function getL4AdvanceActivityUploadedMediaDescription(Request $request) 
    {
        $response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg') ] ;
        $teenager = $this->teenagersRepository->getTeenagerById($request->userId);
        if ($teenager) {
            if ($request->careerId != "" && $request->mediaType != "") {
                $userId = $request->userId;
                $professionId = $request->careerId;
                $typeId = $request->mediaType;
                $total = $this->teenagersRepository->getTeenagerTotalBoosterPoints($userId);
                $professionId = intval($professionId);
                $totalBasicQuestion = $this->level4ActivitiesRepository->getNoOfTotalQuestionsAttemptedQuestion($userId, $professionId);
                if ($totalBasicQuestion[0]->NoOfTotalQuestions == 0) {
                    $response['status'] = 0;
                    $response['message'] = "Profession Doesn't have any basic questions"; 
                } else if ($totalBasicQuestion[0]->NoOfTotalQuestions > $totalBasicQuestion[0]->NoOfAttemptedQuestions) {
                    $response['status'] = 0;
                    $response['message'] = "Play Basic to get to play Intermediate."; 
                } else {
                    $validTypeArr = array(Config::get('constant.ADVANCE_IMAGE_TYPE'), Config::get('constant.ADVANCE_DOCUMENT_TYPE'), Config::get('constant.ADVANCE_VIDEO_TYPE'));
                    $typeId = intval($typeId);
                    if (in_array($typeId, $validTypeArr)) {
                        $typeId = $typeId;
                    } else {
                        $typeId = Config::get('constant.ADVANCE_IMAGE_TYPE');
                    }
                    //Get User activity
                    $professionDetail = $this->professionsRepository->getProfessionsDataFromId($professionId);
                    $data = [];
                    $data['careerId'] = $request->careerId;
                    $data['title'] = (isset($professionDetail) && !empty($professionDetail)) ? $professionDetail[0]->pf_name : '';
                    if ($typeId == 3) {
                        $userLevel4AdvanceTask = $this->level4ActivitiesRepository->getLevel4AdvanceActivityTaskByUser($userId, $professionId, Config::get('constant.ADVANCE_IMAGE_TYPE'));
                    } else if ($typeId == 2) {
                        $userLevel4AdvanceTask = $this->level4ActivitiesRepository->getLevel4AdvanceActivityTaskByUser($userId, $professionId, Config::get('constant.ADVANCE_DOCUMENT_TYPE'));
                    } else if ($typeId == 1) {
                        $userLevel4AdvanceTask = $this->level4ActivitiesRepository->getLevel4AdvanceActivityTaskByUser($userId, $professionId, Config::get('constant.ADVANCE_VIDEO_TYPE'));
                    } else {
                        $userLevel4AdvanceTask = [];
                    }
                    
                    if (!empty($userLevel4AdvanceTask) && count($userLevel4AdvanceTask) > 0) {
                        foreach($userLevel4AdvanceTask as $key => $task) {
                            $mediaData = [];
                            $mediaData['id'] = $task->id;
                            if (Storage::size($this->level4AdvanceOriginalImageUploadPath . $task->l4aaua_media_name) > 0 && $task->l4aaua_media_name != '') { 
                                if ($typeId == 3) {
                                    $media = $task->l4aaua_media_name; 
                                } else if ($typeId == 2) {
                                    $media = 'document.png';
                                } else if ($typeId == 1) {
                                    $media = 'video.png';
                                } else {
                                    $media = 'proteen-logo.png';
                                }
                                $mediaData['displayImage'] =  Storage::url($this->level4AdvanceOriginalImageUploadPath . $media);
                                $mediaData['mediaPath'] = Storage::url($this->level4AdvanceOriginalImageUploadPath . $media);
                            } else {
                                if ($typeId == 2) {
                                    $media = 'no_document.png';
                                } else if ($typeId == 1) {
                                    $media = 'no-video.png';
                                } else {
                                    $media = 'proteen-logo.png';
                                }
                                $mediaData['displayImage'] =  Storage::url($this->level4AdvanceOriginalImageUploadPath . $media);
                                $mediaData['mediaPath'] = Storage::url($this->level4AdvanceOriginalImageUploadPath . $media);
                            }

                            //Task status
                            if($task->l4aaua_is_verified == 0) {
                                $mediaData['mediaStatus'] = 'Uploaded'; 
                            } else if($task->l4aaua_is_verified == 1) {
                                $mediaData['mediaStatus'] = 'Under Review';
                            } else if($task->l4aaua_is_verified == 2) {
                                $mediaData['mediaStatus'] = 'Approved';
                            } else if($task->l4aaua_is_verified == 3) {
                                $mediaData['mediaStatus'] = 'Rejected';
                            } else {
                                $mediaData['mediaStatus'] = ''; 
                            }

                            //Task delete status
                            $mediaData['mediaDelete'] = (isset($task->l4aaua_is_verified) && $task->l4aaua_is_verified != 2) ? 1 : 0; 

                            //Taks earned points
                            $mediaData['mediaEarnedPoints'] = (isset($task->l4aaua_earned_points) && $task->l4aaua_earned_points > 0) ? $task->l4aaua_earned_points : '';

                            //Task date
                            if($task->l4aaua_is_verified == 0 || $task->l4aaua_is_verified == 1) {
                                $mediaData['mediaDate'] = date('jS M Y', strtotime($task->created_at));
                                $mediaData['adminName'] = '';
                            }
                            else if($task->l4aaua_is_verified == 2) {
                                $mediaData['mediaDate'] = date('jS M Y', strtotime($task->l4aaua_verified_date));
                                $mediaData['adminName'] = $task->adminname;
                            } else if($task->l4aaua_is_verified == 3) {
                                $mediaData['mediaDate'] = date('jS M Y', strtotime($task->l4aaua_verified_date));
                                $mediaData['adminName'] = $task->adminname;
                            } else {
                                $mediaData['mediaDate'] = '';
                                $mediaData['adminName'] = '';
                            }

                            $mediaData['mediaDescription'] = (isset($task->l4aaua_note) && !empty($task->l4aaua_note)) ? $task->l4aaua_note : '';
                            $data['media'][] = $mediaData;
                        }
                    } else {
                        $data['media'] = [];
                    }
        
                    $response['status'] = 1;
                    $response['message'] = trans('appmessages.default_success_msg');
                    $response['data'] = $data;
                    //Store log in System
                    $this->log->info('Retrieve L4 advance activity uploaded media details', array('userId' => $request->userId));
                }
                $response['login'] = 1;
            } else {
                $response['login'] = 1;
                $response['status'] = 0;
                $response['message'] = trans('appmessages.missing_data_msg');
            }
        } else {
            $response['message'] = trans('appmessages.invalid_userid_msg') . ' or ' . trans('appmessages.notvarified_user_msg');
        }
        return response()->json($response, 200);
        exit;
    }

}
