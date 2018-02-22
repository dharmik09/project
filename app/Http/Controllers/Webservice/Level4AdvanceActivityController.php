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
use Image;
use App\Services\FileStorage\Contracts\FileStorageRepository;

class Level4AdvanceActivityController extends Controller {

    public function __construct(TeenagersRepository $teenagersRepository, ProfessionsRepository $professionsRepository, TemplatesRepository $templatesRepository, Level4ActivitiesRepository $level4ActivitiesRepository, FileStorageRepository $fileStorageRepository) 
    {
        $this->teenagersRepository = $teenagersRepository;
        $this->professionsRepository = $professionsRepository;
        $this->templatesRepository = $templatesRepository;
        $this->level4ActivitiesRepository = $level4ActivitiesRepository;
        $this->level4AdvanceThumbImageUploadPath = Config::get('constant.LEVEL4_ADVANCE_THUMB_IMAGE_UPLOAD_PATH');
        $this->level4AdvanceOriginalImageUploadPath = Config::get('constant.LEVEL4_ADVANCE_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->level4AdvanceThumbImageWidth = Config::get('constant.LEVEL4_ADVANCE_THUMB_IMAGE_WIDTH');
        $this->level4AdvanceThumbImageHeight = Config::get('constant.LEVEL4_ADVANCE_THUMB_IMAGE_HEIGHT');
        $this->fileStorageRepository = $fileStorageRepository;
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
                //$totalBasicQuestion[0]->NoOfAttemptedQuestions = 6;
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

    /* Request Params : saveLevel4AdvanceUserTask
     *  loginToken, userId, careerId, mediaType, mediaFile
     */
    public function saveLevel4AdvanceUserTask(Request $request) 
    {
        $response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg') ] ;
        $teenager = $this->teenagersRepository->getTeenagerById($request->userId);
        if ($teenager) {
            if ($request->careerId != "" && $request->mediaType != "") {
                $userId = $request->userId;
                $media_type = $request->mediaType;
                $profession_id = $request->careerId;
                $validTypeArr = array(Config::get('constant.ADVANCE_IMAGE_TYPE'), Config::get('constant.ADVANCE_DOCUMENT_TYPE'), Config::get('constant.ADVANCE_VIDEO_TYPE'));

                $professionDetail = $this->professionsRepository->getProfessionsDataFromId($profession_id);
                if (isset($professionDetail) && !empty($professionDetail)) {
                    if (isset($media_type) && $media_type != '' && in_array($media_type, $validTypeArr)) {
                        $file = Input::file('mediaFile');
                        if (!empty($file)) {
                            $ext = $file->getClientOriginalExtension();
                            $save = false;
                            //check for image extension
                            if ($media_type == 3) {
                                $validImageExtArr = array('jpg', 'jpeg', 'png', 'bmp', 'PNG');
                                if (in_array($ext, $validImageExtArr)) {
                                    $save = true;
                                    $fileName = 'advance_' . time() . '.' . $file->getClientOriginalExtension();
                                    $pathOriginal = public_path($this->level4AdvanceOriginalImageUploadPath . $fileName);
                                    $pathThumb = public_path($this->level4AdvanceThumbImageUploadPath . $fileName);
                                    Image::make($file->getRealPath())->save($pathOriginal);
                                    Image::make($file->getRealPath())->resize($this->level4AdvanceThumbImageWidth, $this->level4AdvanceThumbImageHeight)->save($pathThumb);
                                    //Uploading on AWS
                                    $originalImage = $this->fileStorageRepository->addFileToStorage($fileName, $this->level4AdvanceOriginalImageUploadPath, $pathOriginal, "s3");
                                    $thumbImage = $this->fileStorageRepository->addFileToStorage($fileName, $this->level4AdvanceThumbImageUploadPath, $pathThumb, "s3");
                                    
                                    \File::delete($this->level4AdvanceOriginalImageUploadPath . $fileName);
                                    \File::delete($this->level4AdvanceThumbImageUploadPath . $fileName);
                                    $level4AdvanceData['l4aaua_media_type'] = $media_type;
                                } else {
                                    $response['status'] = 0;
                                    $response['message'] = 'Invalid image file';
                                }
                            } elseif ($media_type == 2) {
                                $validImageExtArr = array('pdf', 'docx', 'doc', 'ppt', 'xls', 'xlsx');
                                if (in_array($ext, $validImageExtArr)) {
                                    $save = true;
                                    $fileName = 'advance_' . time() . '.' . $file->getClientOriginalExtension();
                                    Input::file('mediaFile')->move($this->level4AdvanceOriginalImageUploadPath, $fileName); // uploading file to given path

                                    $docOriginalPath = public_path($this->level4AdvanceOriginalImageUploadPath . $fileName);
                                    //Uploading on AWS
                                    $originalDoc = $this->fileStorageRepository->addFileToStorage($fileName, $this->level4AdvanceOriginalImageUploadPath, $docOriginalPath, "s3");
                                    \File::delete($this->level4AdvanceOriginalImageUploadPath . $fileName);
                                    $level4AdvanceData['l4aaua_media_type'] = $media_type;
                                } else {
                                    $response['status'] = 0;
                                    $response['message'] = 'Invalid document file';
                                }
                            } elseif ($media_type == 1) {
                                $validImageExtArr = array('mov', 'avi', 'mp4', 'mkv', 'wmv');
                                if (in_array($ext, $validImageExtArr)) {
                                    $save = true;
                                    $fileName = 'advance_' . time() . '.' . $file->getClientOriginalExtension();
                                    Input::file('mediaFile')->move($this->level4AdvanceOriginalImageUploadPath, $fileName); // uploading file to given path

                                     $videoOriginalPath = public_path($this->level4AdvanceOriginalImageUploadPath . $fileName);
                                    //Uploading on AWS
                                    $originalDoc = $this->fileStorageRepository->addFileToStorage($fileName, $this->level4AdvanceOriginalImageUploadPath, $videoOriginalPath, "s3");
                                    \File::delete($this->level4AdvanceOriginalImageUploadPath . $fileName);
                                    $level4AdvanceData['l4aaua_media_type'] = $media_type;
                                } else {
                                    $response['status'] = 0;
                                    $response['message'] = 'Invalid video file';
                                }
                            } else {
                                $response['status'] = 0;
                                $response['message'] = 'Invalid activity type';
                            }
                            if ($save) {
                                //Prepare Data for save
                                $getTeenagerBoosterPoints = $this->teenagersRepository->getTeenagerBoosterPoints($userId);
                                $level4Booster = Helpers::level4Booster($profession_id, $userId);
                                $level4Booster['total'] = $getTeenagerBoosterPoints['total'];
                                $data['level4Booster'] = $level4Booster;
                                //$response['booster_points'] = '';
                                $level4AdvanceData['id'] = 0;
                                $level4AdvanceData['l4aaua_teenager'] = $userId;
                                $level4AdvanceData['l4aaua_profession_id'] = $profession_id;
                                $level4AdvanceData['l4aaua_media_name'] = $fileName;
                                $this->level4ActivitiesRepository->saveLevel4AdvanceActivityUser($level4AdvanceData);
                                $response['status'] = 1;
                                $response['message'] = "Media file uploaded successfully";
                            }
                        } else {
                            $response['status'] = 0;
                            $response['message'] = 'Please upload appropriate media file';
                        }
                    } else {
                        $response['status'] = 0;
                        $response['message'] = 'Invalid activity type';
                    }
                } else {
                    $response['status'] = 0;
                    $response['message'] = 'Invalid profession';
                }
                //Store log in System
                $this->log->info('User upload level 4 advance activity task', array('userId' => $request->userId));
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

    /* Request Params : submitLevel4AdvanceTaskForReview
     *  loginToken, userId, careerId, mediaType, taskId
     */
    public function submitLevel4AdvanceTaskForReview(Request $request) 
    {
        $response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg') ] ;
        $teenager = $this->teenagersRepository->getTeenagerById($request->userId);
        if ($teenager) {
            if ($request->taskId != "" && $request->mediaType != "" && $request->careerId != "") {
                $type = $request->mediaType;
                $taskId = $request->taskId;
                $professionId = $request->careerId;
                $validTypeArr = array(Config::get('constant.ADVANCE_IMAGE_TYPE'), Config::get('constant.ADVANCE_DOCUMENT_TYPE'), Config::get('constant.ADVANCE_VIDEO_TYPE'));
                $professionDetail = $this->professionsRepository->getProfessionsDataFromId($professionId);
                $dataIdArr = [];
                if (isset($professionDetail) && !empty($professionDetail)) {
                    if (isset($type) && in_array($type, $validTypeArr)) {
                        if (isset($taskId) && !empty($taskId)) {
                            $dataIdArr = explode(', ', $taskId);
                        } 
                        $sendMail = false;
                        if (isset($dataIdArr) && !empty($dataIdArr)) {
                            foreach ($dataIdArr as $key => $dataid) {
                                $sendMail = true;
                                $updateData['l4aaua_is_verified'] = 1;
                                $this->level4ActivitiesRepository->updateStatusAdvanceTaskUser($dataid, $updateData);
                            }
                            if ($sendMail) {
                                //Send notification mail to admin
                                $teenagerDetailbyId = $this->teenagersRepository->getTeenagerById($request->userId);
                                $replaceArray = array();
                                $replaceArray['TEEN_NAME'] = $teenagerDetailbyId->t_name;
                                $replaceArray['TEEN_EMAIL'] = $teenagerDetailbyId->t_email;
                                $emailTemplateContent = $this->templatesRepository->getEmailTemplateDataByName(Config::get('constant.USER_SUBMITTED_ADVANCE_TASK'));
                                $content = $this->templatesRepository->getEmailContent($emailTemplateContent->et_body, $replaceArray);
                                $adminEmail = Helpers::getConfigValueByKey('ADMIN_EMAIL');
                                $adminName = Helpers::getConfigValueByKey('ADMIN_NAME');
                                $data = array();
                                $data['subject'] = $emailTemplateContent->et_subject;
                                $data['toEmail'] = $adminEmail;
                                $data['toName'] = $adminName;
                                $data['content'] = $content;
                                $data['teen_id'] = $teenagerDetailbyId->id;
                                Mail::send(['html' => 'emails.Template'], $data, function($message) use ($data) {
                                            $message->subject($data['subject']);
                                            $message->to($data['toEmail'], $data['toName']);
                                        });
                                $response['status'] = 1;
                                $response['message'] = "Your tasks has been submitted for review.";
                            } else {
                                $response['status'] = 0;
                                $response['message'] = "Something went wrong";
                            }
                        } else {
                            $response['status'] = 0;
                            $response['message'] = "Something went wrong";
                        }
                    } else {
                        $response['status'] = 0;
                        $response['message'] = "Invalid task submitted";
                    }
                } else {
                    $response['status'] = 0;
                    $response['message'] = "Invalid profession";
                }
                //Store log in System
                $this->log->info('User upload level 4 advance activity task', array('userId' => $request->userId));
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
