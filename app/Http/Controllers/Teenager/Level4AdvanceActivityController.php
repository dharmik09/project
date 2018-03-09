<?php

namespace App\Http\Controllers\Teenager;

use App\Http\Controllers\Controller;
use Auth;
use Config;
use Storage;
use Input;
use Mail;
use Helpers;
use App\Services\Teenagers\Contracts\TeenagersRepository;
use Redirect;
use Request;
use App\Teenagers;
use App\ProfessionLearningStyle;
use App\UserLearningStyle;
use Carbon\Carbon;  
use App\TeenagerBoosterPoint;
use App\Services\Professions\Contracts\ProfessionsRepository;
use App\Services\Level4Activity\Contracts\Level4ActivitiesRepository;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Image;
use App\Services\FileStorage\Contracts\FileStorageRepository;
use App\Services\Template\Contracts\TemplatesRepository;

class Level4AdvanceActivityController extends Controller {

    public function __construct(Level4ActivitiesRepository $level4ActivitiesRepository, TeenagersRepository $teenagersRepository, ProfessionsRepository $professionsRepository, FileStorageRepository $fileStorageRepository, TemplatesRepository $templatesRepository) 
    {        
        $this->professionsRepository = $professionsRepository;
        $this->teenagersRepository = $teenagersRepository;   
        $this->level4ActivitiesRepository = $level4ActivitiesRepository;   
        $this->teenagerBoosterPoint = new TeenagerBoosterPoint();
        $this->extraQuestionDescriptionTime = Config::get('constant.EXTRA_QUESTION_DESCRIPTION');
        $this->templateRepository = $templatesRepository;
        $this->questionDescriptionORIGINALImage = Config::get('constant.LEVEL4_INTERMEDIATE_QUESTION_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->questionDescriptionTHUMBImage = Config::get('constant.LEVEL4_INTERMEDIATE_QUESTION_THUMB_IMAGE_UPLOAD_PATH');
        $this->optionORIGINALImage = Config::get('constant.LEVEL4_INTERMEDIATE_ANSWER_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->optionTHUMBImage = Config::get('constant.LEVEL4_INTERMEDIATE_ANSWER_THUMB_IMAGE_UPLOAD_PATH');
        $this->answerResponseImageOriginal = Config::get('constant.LEVEL4_INTERMEDIATE_RESPONSE_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->level4MinimumPointsRequirements = Config::get('constant.LEVEL4_MINIMUM_POINTS_REQUIREMENTS');
        $this->level4AdvanceThumbImageUploadPath = Config::get('constant.LEVEL4_ADVANCE_THUMB_IMAGE_UPLOAD_PATH');
        $this->level4AdvanceOriginalImageUploadPath = Config::get('constant.LEVEL4_ADVANCE_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->level4AdvanceThumbImageWidth = Config::get('constant.LEVEL4_ADVANCE_THUMB_IMAGE_WIDTH');
        $this->level4AdvanceThumbImageHeight = Config::get('constant.LEVEL4_ADVANCE_THUMB_IMAGE_HEIGHT');
        $this->fileStorageRepository = $fileStorageRepository;
        $this->log = new Logger('teenager-level4-advance-activity-controller');
        $this->log->pushHandler(new StreamHandler(storage_path().'/logs/monolog-'.date('m-d-Y').'.log'));
    }

    /*
     * Get L4 advance level question data
     */
    public function getQuestionDataAdvanceLevel()
    {
        $type = Input::get('activityType');
        $professionId = Input::get('professionId');
        $professionDetail = $this->professionsRepository->getProfessionsDataFromId($professionId);
        $activityData = $this->level4ActivitiesRepository->getLevel4AdvanceActivityByType($type);
        //Store log in System
        $this->log->info('Retrieve L4 advance activity data', array('userId' => Auth::guard('teenager')->user()->id));
        return view('teenager.basic.careerAdvanceQuizData', compact('activityData', 'professionDetail', 'type'));
    }

    /*
     * Returns L4 upload media section view
     */
    public function getMediaUploadSection()
    {
        return view('teenager.basic.careerAdvanceQuizSection');
    }

    /*
     * Returns L4 advance activity uploaded media details 
     */
    public function getLevel4AdvanceStep2Details() {
        $userId = Auth::guard('teenager')->user()->id;
        $professionId = Input::get('professionId');
        $typeId = Input::get('type');
        $total = $this->teenagersRepository->getTeenagerTotalBoosterPoints($userId);
        $professionId = intval($professionId);
        $totalBasicQuestion = $this->level4ActivitiesRepository->getNoOfTotalQuestionsAttemptedQuestion($userId, $professionId);

        // remove this after development
        $totalBasicQuestion[0]->NoOfAttemptedQuestions = $totalBasicQuestion[0]->NoOfTotalQuestions + 1;
        //

        if ($totalBasicQuestion[0]->NoOfTotalQuestions == 0) {
            $response['status'] = 0;
            $response['message'] = "Profession Doesn't have any basic questions"; 
            return response()->json($response, 200);
            exit;
        } else if ($totalBasicQuestion[0]->NoOfTotalQuestions > $totalBasicQuestion[0]->NoOfAttemptedQuestions) {
            $response['status'] = 0;
            $response['message'] = "Play Basic to play advance activtiy."; 
            return response()->json($response, 200);
            exit;
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
            $level4AdvanceThumbImageUploadPath = $this->level4AdvanceThumbImageUploadPath;
            $level4AdvanceOriginalImageUploadPath = $this->level4AdvanceOriginalImageUploadPath;
            $userLevel4AdvanceImageTask = $this->level4ActivitiesRepository->getLevel4AdvanceActivityTaskByUser(Auth::guard('teenager')->user()->id, $professionId, Config::get('constant.ADVANCE_IMAGE_TYPE'));
            $userLevel4AdvanceDocumentTask = $this->level4ActivitiesRepository->getLevel4AdvanceActivityTaskByUser(Auth::guard('teenager')->user()->id, $professionId, Config::get('constant.ADVANCE_DOCUMENT_TYPE'));
            $userLevel4AdvanceVideoTask = $this->level4ActivitiesRepository->getLevel4AdvanceActivityTaskByUser(Auth::guard('teenager')->user()->id, $professionId, Config::get('constant.ADVANCE_VIDEO_TYPE'));
            //$level4Booster = Helpers::level4Booster($professionId, $userid);
            $level4Booster['total'] = $total;
            $response['level4Booster'] = $level4Booster;
            $response['boosterPoints'] = '';
            $response['status'] = 1;
            $response['message'] = trans('appmessages.default_success_msg');
            return view('teenager.basic.careerAdvanceQuizStep2Activity', compact('response', 'typeId', 'professionId', 'professionDetail', 'userLevel4AdvanceImageTask', 'level4AdvanceThumbImageUploadPath', 'level4AdvanceOriginalImageUploadPath', 'userLevel4AdvanceDocumentTask', 'userLevel4AdvanceVideoTask'));
            exit;
        }
    }

    public function submitLevel4AdvanceActivity()
    {
        $level4AdvanceData = array();
        if (Input::file()) {
            $profession_id = Input::get('profession_id');
            $media_type = Input::get('media_type');
            $file = Input::file('media');
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
                        $level4AdvanceData['l4aaua_media_type'] = 3;
                    } else {
                        $response['status'] = 0;
                        $response['message'] = "Invalid media uploaded";
                        return response()->json($response, 200);
                        exit;
                    }
                } elseif ($media_type == 2) {
                    $validImageExtArr = array('pdf', 'docx', 'doc', 'ppt', 'pptx', 'xls', 'xlsx');
                    if (in_array($ext, $validImageExtArr)) {
                        $save = true;
                        $fileName = 'advance_' . time() . '.' . $file->getClientOriginalExtension();
                        Input::file('media')->move($this->level4AdvanceOriginalImageUploadPath, $fileName); // uploading file to given path
                        $docOriginalPath = public_path($this->level4AdvanceOriginalImageUploadPath . $fileName);
                        //Uploading on AWS
                        $originalDoc = $this->fileStorageRepository->addFileToStorage($fileName, $this->level4AdvanceOriginalImageUploadPath, $docOriginalPath, "s3");
                        \File::delete($this->level4AdvanceOriginalImageUploadPath . $fileName);
                        $level4AdvanceData['l4aaua_media_type'] = 2;
                    } else {
                        $response['status'] = 0;
                        $response['message'] = "Invalid media uploaded";
                        return response()->json($response, 200);
                        exit;
                    }
                } elseif ($media_type == 1) {
                    $validImageExtArr = array('mov', 'avi', 'mp4', 'mkv', 'wmv','flv');
                    if (in_array($ext, $validImageExtArr)) {
                        $save = true;
                        $fileName = 'advance_' . time() . '.' . $file->getClientOriginalExtension();
                        Input::file('media')->move($this->level4AdvanceOriginalImageUploadPath, $fileName); // uploading file to given path
                        $videoOriginalPath = public_path($this->level4AdvanceOriginalImageUploadPath . $fileName);
                        //Uploading on AWS
                        $originalDoc = $this->fileStorageRepository->addFileToStorage($fileName, $this->level4AdvanceOriginalImageUploadPath, $videoOriginalPath, "s3");
                        \File::delete($this->level4AdvanceOriginalImageUploadPath . $fileName);
                        $level4AdvanceData['l4aaua_media_type'] = 1;
                    } else {
                        $response['status'] = 0;
                        $response['message'] = "Invalid media uploaded";
                        return response()->json($response, 200);
                        exit;
                    }
                } else {
                    $response['status'] = 0;
                    $response['message'] = "Invalid media uploaded";
                    return response()->json($response, 200);
                    exit;
                }
                if ($save) {
                    //Prepare Data for save
                    $level4AdvanceData['id'] = 0;
                    $level4AdvanceData['l4aaua_teenager'] = Auth::guard('teenager')->user()->id;
                    $level4AdvanceData['l4aaua_profession_id'] = $profession_id;
                    $level4AdvanceData['l4aaua_media_name'] = $fileName;
                    $this->level4ActivitiesRepository->saveLevel4AdvanceActivityUser($level4AdvanceData);
                    $response['status'] = 1;
                    $response['message'] = "Media file uploaded successfully";
                    return response()->json($response, 200);
                    exit;
                } else {
                    $response['status'] = 0;
                    $response['message'] = "Something went wrong";
                    return response()->json($response, 200);
                    exit;
                }
            } else {
                $response['status'] = 0;
                $response['message'] = "Upload appropriate media file, Can not find any uploaded media file";
                return response()->json($response, 200);
                exit;
            }
        } else {
            $response['status'] = 0;
            $response['message'] = "Upload appropriate media file, Can not find any uploaded media file";
            return response()->json($response, 200);
            exit;
        }
    }

    //Store records of Level4 advance activity for admin review
    public function submitLevel4AdvanceActivityForReview() {
        $postData = Input::all();
        if (isset($postData['data_id']) && !empty($postData['data_id'])) {
            //Update the status       
            $sendMail = false;
            $advanceActivityData = [];
            foreach ($postData['data_id'] as $key => $dataid) {
                if ($postData['data_status'][$key] == 0) {
                    $sendMail = true;
                    $updateData['l4aaua_is_verified'] = 1;
                    $this->level4ActivitiesRepository->updateStatusAdvanceTaskUser($dataid, $updateData);
                    $advanceActivityData = $this->level4ActivitiesRepository->getUserAdvanceTaskById($dataid);
                }
            }
            if (isset($advanceActivityData) && count($advanceActivityData) > 0) {
                if ($advanceActivityData[0]->l4aaua_media_type == 1) {
                    $activityType = 'Video';  
                } else if($advanceActivityData[0]->l4aaua_media_type == 2) {
                    $activityType = 'Document';  
                } else {
                    $activityType = 'Image';  
                }
                $profession = $advanceActivityData[0]->pf_name;
            } else {
                $profession = '';
                $activityType = '';
            } 
            
            if ($sendMail) {
                //Send notification mail to admin 
                $teenagerDetailbyId = $this->teenagersRepository->getTeenagerById(Auth::guard('teenager')->user()->id);
                $replaceArray = array();
                $replaceArray['TEEN_NAME'] = $teenagerDetailbyId->t_name;
                $replaceArray['TEEN_EMAIL'] = $teenagerDetailbyId->t_email;
                $replaceArray['PROFESSION'] = $profession;
                $replaceArray['ACTIVITY_TYPE'] = $activityType;
                $emailTemplateContent = $this->templateRepository->getEmailTemplateDataByName(Config::get('constant.USER_SUBMITTED_ADVANCE_TASK'));
                $content = $this->templateRepository->getEmailContent($emailTemplateContent->et_body, $replaceArray);
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
                $response['mediaType'] = $advanceActivityData[0]->l4aaua_media_type;
                return response()->json($response, 200);
                exit;
            } else {
                $response['status'] = 0;
                $response['message'] = "Something went wrong";
                $response['mediaType'] = $advanceActivityData[0]->l4aaua_media_type;
                return response()->json($response, 200);
                exit;
            }
        } else {
            $response['status'] = 0;
            $response['message'] = "Something went wrong";
            $response['mediaType'] = 1;
            return response()->json($response, 200);
            exit;
        }
    }

    /*
     * Delete user task
     */
    public function deleteUserAdvanceTask() {
        $postData = Input::all();
        if (isset($postData) && !empty($postData)) {
            $id = $postData['taskId'];
            $result = $this->level4ActivitiesRepository->deleteUserAdvanceTask($id);
            if ($result) {
                $pathOriginal = public_path($this->level4AdvanceOriginalImageUploadPath . $postData['mediaName']);
                $pathThumb = public_path($this->level4AdvanceThumbImageUploadPath . $postData['mediaName']);
                if ($postData['mediaType'] == 3) {
                    //delete from AWS
                    $thumbMedia = $this->fileStorageRepository->deleteFileToStorage($postData['mediaName'], $this->level4AdvanceThumbImageUploadPath, "s3");
                } 
                $originalMedia = $this->fileStorageRepository->deleteFileToStorage($postData['mediaName'], $this->level4AdvanceOriginalImageUploadPath, "s3");
                $response['status'] = 1;
                $response['message'] = "Media deleted successfully";
                return response()->json($response, 200);
                exit;
            } else {
                $response['status'] = 0;
                $response['message'] = "Something went wrong";
                return response()->json($response, 200);
                exit;
            }
        } else {
            $response['status'] = 0;
            $response['message'] = "Something went wrong";
            return response()->json($response, 200);
            exit;
        }
    }


}