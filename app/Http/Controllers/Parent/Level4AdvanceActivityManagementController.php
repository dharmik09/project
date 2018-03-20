<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use Auth;
use Image;
use Config;
use Helpers;
use Input;
use Redirect;
use Response;
use Mail;
use App\Transactions;
use App\Teenagers;
use App\Templates;
use App\Sponsors;
use App\Country;
use App\Services\Professions\Contracts\ProfessionsRepository;
use App\Services\Template\Contracts\TemplatesRepository;
use App\Services\Teenagers\Contracts\TeenagersRepository;
use App\Services\Sponsors\Contracts\SponsorsRepository;
use App\Services\Level2Activity\Contracts\Level2ActivitiesRepository;
use App\Services\Level4Activity\Contracts\Level4ActivitiesRepository;
use App\Services\Parents\Contracts\ParentsRepository;
use App\Services\FileStorage\Contracts\FileStorageRepository;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class Level4AdvanceActivityManagementController extends Controller {

    public function __construct(Level4ActivitiesRepository $level4ActivitiesRepository, Level2ActivitiesRepository $level2ActivitiesRepository, TeenagersRepository $teenagersRepository, ProfessionsRepository $professionsRepository, SponsorsRepository $sponsorsRepository, TemplatesRepository $templatesRepository, ParentsRepository $parentsRepository, FileStorageRepository $fileStorageRepository) {
        $this->objTeenagers = new Teenagers();
        $this->level4ActivitiesRepository = $level4ActivitiesRepository;
        $this->teenagersRepository = $teenagersRepository;
        $this->level2ActivitiesRepository = $level2ActivitiesRepository;
        $this->parentsRepository = $parentsRepository;
        $this->objSponsors = new Sponsors();
        $this->templateRepository = $templatesRepository;
        $this->sponsorsRepository = $sponsorsRepository;
        $this->objTemplates = new Templates();
        $this->professionsRepository = $professionsRepository;
        $this->teenOriginalImageUploadPath = Config::get('constant.TEEN_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->teenThumbImageUploadPath = Config::get('constant.TEEN_THUMB_IMAGE_UPLOAD_PATH');
        $this->teenThumbImageHeight = Config::get('constant.TEEN_THUMB_IMAGE_HEIGHT');
        $this->teenThumbImageWidth = Config::get('constant.TEEN_THUMB_IMAGE_WIDTH');
        $this->level4QuestionPoints = Config::get('constant.LEVEL4_POINTS_FOR_QUESTION');
        $this->level4MinimumPointsRequirements = Config::get('constant.LEVEL4_MINIMUM_POINTS_REQUIREMENTS');
        $this->extraQuestionDescriptionTime = Config::get('constant.EXTRA_QUESTION_DESCRIPTION');
        $this->questionDescriptionORIGINALImage = Config::get('constant.LEVEL4_INTERMEDIATE_QUESTION_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->questionDescriptionTHUMBImage = Config::get('constant.LEVEL4_INTERMEDIATE_QUESTION_THUMB_IMAGE_UPLOAD_PATH');
        $this->optionORIGINALImage = Config::get('constant.LEVEL4_INTERMEDIATE_ANSWER_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->optionTHUMBImage = Config::get('constant.LEVEL4_INTERMEDIATE_ANSWER_THUMB_IMAGE_UPLOAD_PATH');
        $this->level4AdvanceOriginalImageUploadPath = Config::get('constant.LEVEL4_ADVANCE_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->level4AdvanceThumbImageUploadPath = Config::get('constant.LEVEL4_ADVANCE_THUMB_IMAGE_UPLOAD_PATH');
        $this->level4AdvanceThumbImageHeight = Config::get('constant.LEVEL4_ADVANCE_THUMB_IMAGE_HEIGHT');
        $this->level4AdvanceThumbImageWidth = Config::get('constant.LEVEL4_ADVANCE_THUMB_IMAGE_WIDTH');
        $this->fileStorageRepository = $fileStorageRepository;
        $this->loggedInUser = Auth::guard('parent');
        $this->log = new Logger('parent-level4-advance-activity-controller');
        $this->log->pushHandler(new StreamHandler(storage_path().'/logs/monolog-'.date('m-d-Y').'.log'));
    }

    public function level4Advance($professionId,$teenId) {
        $response = [];
        $response['status'] = 0;
        $response['message'] = trans('appmessages.default_error_msg');
        if (Auth::guard('parent')->check()) {
            $parentid = $this->loggedInUser->user()->id;
            $response['profession_id'] = $professionId;
            $response['teen_id'] = $teenId;
            $teenDetail = $this->teenagersRepository->getTeenagerByTeenagerId($teenId);
            $response['teenDetail'] = $teenDetail;

            $professionId = intval($professionId);
            $totalBasicQuestion = $this->level4ActivitiesRepository->getNoOfTotalQuestionsAttemptedQuestionForParent($parentid, $professionId);
            if ($totalBasicQuestion[0]->NoOfTotalQuestions == 0) {
                return Redirect::to('/parent/my-challengers-accept/$professionId/$teenId')->with('error', "Profession Doesn't have any basic questions");
                exit;
            } else if ($totalBasicQuestion[0]->NoOfTotalQuestions > $totalBasicQuestion[0]->NoOfAttemptedQuestions) {
                return Redirect::to("/parent/my-challengers-accept/$professionId/$teenId")->with('error', "Play Basic to get to play Advanced.");
                exit;
            } else {
                //Check weather user has completed all activities and they are verified
                $userVerifiedActivities = $this->level4ActivitiesRepository->getParentAllVerifiedTasks($parentid,$professionId);
                if(isset($userVerifiedActivities) && count($userVerifiedActivities) == 7)
                {
                    $response['showCongrats'] = 'yes';
                }else{
                    $response['showCongrats'] = 'no';
                }
                $getTeenagerBoosterPoints = $this->teenagersRepository->getTeenagerBoosterPoints($teenId);

                $professionDetail = $this->professionsRepository->getProfessionsDataFromId($professionId);
                $level4Booster = Helpers::level4Booster($professionId, $teenId);
                $level4Booster['total'] = $getTeenagerBoosterPoints['total'];
                $response['teen_id'] = $teenId;
                $teenDetail = $this->teenagersRepository->getTeenagerByTeenagerId($teenId);
                $response['teenDetail'] = $teenDetail;
                $level4ParentBooster = Helpers::level4ParentBooster($professionId, $parentid);
                $response['level4ParentBooster'] = $level4ParentBooster;
                $response['level4Booster'] = $level4Booster;
                $response['boosterPoints'] = '';
                $response['status'] = 1;
                $response['message'] = trans('appmessages.default_success_msg');
                return view('parent.level4AdvanceActivity', compact('professionDetail', 'response'));
                exit;
            }
        } else {
            Auth::guard('parent')->logout();
            return Redirect::to('/parent')->with('error', trans('appmessages.invalid_userid_msg'));
            exit;
        }
    }

    // public function level4AdvanceStep2($professionId, $typeId = 3,$teenId) {
    //     $response = [];
    //     $response['status'] = 0;
    //     $response['message'] = trans('appmessages.default_error_msg');
    //     if (Auth::guard('parent')->check()) {
    //         $parentid = $this->loggedInUser->user()->id;
    //         $response['profession_id'] = $professionId;
    //         $response['teen_id'] = $teenId;
    //         $teenDetail = $this->teenagersRepository->getTeenagerByTeenagerId($teenId);
    //         $response['teenDetail'] = $teenDetail;
    //         $professionId = intval($professionId);
    //         $totalBasicQuestion = $this->level4ActivitiesRepository->getNoOfTotalQuestionsAttemptedQuestionForParent($parentid, $professionId);
    //         if ($totalBasicQuestion[0]->NoOfTotalQuestions == 0) {
    //             return Redirect::to('/parent/my-challengers-accept/$$professionId/$teenId')->with('error', "Profession Doesn't have any basic questions");
    //             exit;
    //         } else if ($totalBasicQuestion[0]->NoOfTotalQuestions > $totalBasicQuestion[0]->NoOfAttemptedQuestions) {
    //             return Redirect::to("/parent/my-challengers-accept/$$professionId/$teenId")->with('error', "Play Basic to get to play Intermediate.");
    //             exit;
    //         } else {
    //             $validTypeArr = array(Config::get('constant.ADVANCE_IMAGE_TYPE'), Config::get('constant.ADVANCE_DOCUMENT_TYPE'), Config::get('constant.ADVANCE_VIDEO_TYPE'));
    //             $typeId = intval($typeId);
    //             if (in_array($typeId, $validTypeArr)) {
    //                 $typeId = $typeId;
    //             } else {
    //                 $typeId = Config::get('constant.ADVANCE_IMAGE_TYPE');
    //             }
    //             //Get User activity
    //             $professionDetail = $this->professionsRepository->getProfessionsDataFromId($professionId);
    //             $level4AdvanceThumbImageUploadPath = $this->level4AdvanceThumbImageUploadPath;
    //             $level4AdvanceOriginalImageUploadPath = $this->level4AdvanceOriginalImageUploadPath;
    //             $userLevel4AdvanceImageTask = $this->level4ActivitiesRepository->getLevel4AdvanceActivityTaskByParent($this->loggedInUser->user()->id, $professionId, Config::get('constant.ADVANCE_IMAGE_TYPE'));
    //             $userLevel4AdvanceDocumentTask = $this->level4ActivitiesRepository->getLevel4AdvanceActivityTaskByParent($this->loggedInUser->user()->id, $professionId, Config::get('constant.ADVANCE_DOCUMENT_TYPE'));
    //             $userLevel4AdvanceVideoTask = $this->level4ActivitiesRepository->getLevel4AdvanceActivityTaskByParent($this->loggedInUser->user()->id, $professionId, Config::get('constant.ADVANCE_VIDEO_TYPE'));

    //             $getTeenagerBoosterPoints = $this->teenagersRepository->getTeenagerBoosterPoints($teenId);

    //             $level4Booster = Helpers::level4Booster($professionId, $teenId);
    //             $level4ParentBooster = Helpers::level4ParentBooster($professionId, $parentid);
    //             $response['level4ParentBooster'] = $level4ParentBooster;
    //             $response['teen_id'] = $teenId;
    //             $teenDetail = $this->teenagersRepository->getTeenagerByTeenagerId($teenId);
    //             $response['teenDetail'] = $teenDetail;

    //             $level4Booster['total'] = $getTeenagerBoosterPoints['total'];
    //             $response['level4Booster'] = $level4Booster;
    //             $response['boosterPoints'] = '';
    //             $response['status'] = 1;
    //             $response['message'] = trans('appmessages.default_success_msg');
    //             return view('parent.level4AdvanceStep2Activtiy', compact('response', 'typeId', 'professionId', 'professionDetail', 'userLevel4AdvanceImageTask', 'level4AdvanceThumbImageUploadPath', 'level4AdvanceOriginalImageUploadPath', 'userLevel4AdvanceDocumentTask', 'userLevel4AdvanceVideoTask'));
    //             exit;
    //         }
    //     } else {
    //         Auth::guard('parent')->logout();
    //         return Redirect::to('/parent')->with('error', trans('appmessages.invalid_userid_msg'));
    //         exit;
    //     }
    // }

    /* Get Question Data */

    // public function getQuestionDataAdvanceLevel() {
    //     $type = Input::get('activity_type');
    //     $professionId = Input::get('professionId');
    //     $teenId = Input::get('teenId');
    //     if (isset($type) && $type != '') {
    //         $professionDetail = $this->professionsRepository->getProfessionsDataFromId($professionId);
    //         $activityData = $this->level4ActivitiesRepository->getLevel4AdvanceActivityByType($type);
    //         return view('parent.level4AdvanceActivityData', compact('activityData', 'professionDetail', 'type','teenId'));
    //     }
    //     echo "Invalid parameter";
    //     exit;
    // }

    /*
     * Submit Level4 Advance Activity
     */

    // public function submitLevel4AdvanceActivity() {
    //     $level4AdvanceData = array();
    //     if (Input::file()) {
    //         $profession_id = Input::get('profession_id');
    //         $media_type = Input::get('media_type');
    //         $file = Input::file('advance_task');

    //         if (!empty($file)) {
    //             $ext = $file->getClientOriginalExtension();
    //             $save = false;
    //             //check for image extension
    //             if ($media_type == 3) {
    //                 $validImageExtArr = array('jpg', 'jpeg', 'png', 'bmp', 'PNG');
    //                 if (in_array($ext, $validImageExtArr)) {
    //                     $save = true;
    //                     $fileName = 'advance_' . time() . '.' . $file->getClientOriginalExtension();
    //                     $pathOriginal = public_path($this->level4AdvanceOriginalImageUploadPath . $fileName);
    //                     $pathThumb = public_path($this->level4AdvanceThumbImageUploadPath . $fileName);
    //                     Image::make($file->getRealPath())->save($pathOriginal);
    //                     Image::make($file->getRealPath())->resize($this->level4AdvanceThumbImageWidth, $this->level4AdvanceThumbImageHeight)->save($pathThumb);

    //                     //Uploading on AWS
    //                     $originalImage = $this->fileStorageRepository->addFileToStorage($fileName, $this->level4AdvanceOriginalImageUploadPath, $pathOriginal, "s3");
    //                     $thumbImage = $this->fileStorageRepository->addFileToStorage($fileName, $this->level4AdvanceThumbImageUploadPath, $pathThumb, "s3");
                    
    //                     \File::delete($this->level4AdvanceOriginalImageUploadPath . $fileName);
    //                     \File::delete($this->level4AdvanceThumbImageUploadPath . $fileName);
    //                     $level4AdvanceData['l4aapa_media_type'] = 3;
    //                 } else {
    //                     echo "invalid";
    //                     exit;
    //                 }
    //             } elseif ($media_type == 2) {
    //                 $validImageExtArr = array('pdf', 'docx', 'doc', 'ppt', 'pptx', 'xls', 'xlsx');
    //                 if (in_array($ext, $validImageExtArr)) {
    //                     $save = true;
    //                     $fileName = 'advance_' . time() . '.' . $file->getClientOriginalExtension();
    //                     $pathOriginal = public_path($this->level4AdvanceOriginalImageUploadPath . $fileName);
                        
    //                     Input::file('advance_task')->move($this->level4AdvanceOriginalImageUploadPath, $fileName); // uploading file to given path

    //                     //Uploading on AWS
    //                     $originalImage = $this->fileStorageRepository->addFileToStorage($fileName, $this->level4AdvanceOriginalImageUploadPath, $pathOriginal, "s3");
    //                     \File::delete($this->level4AdvanceOriginalImageUploadPath . $fileName);
    //                     $level4AdvanceData['l4aapa_media_type'] = 2;
    //                 } else {
    //                     echo "invalid";
    //                     exit;
    //                 }
    //             } elseif ($media_type == 1) {
    //                 $validImageExtArr = array('mov', 'avi', 'mp4', 'mkv', 'wmv','flv');
    //                 if (in_array($ext, $validImageExtArr)) {
    //                     $save = true;
    //                     $fileName = 'advance_' . time() . '.' . $file->getClientOriginalExtension();
    //                     Input::file('advance_task')->move($this->level4AdvanceOriginalImageUploadPath, $fileName); // uploading file to given path
    //                     $level4AdvanceData['l4aapa_media_type'] = 1;
    //                 } else {
    //                     echo "invalid";
    //                     exit;
    //                 }
    //             } else {
    //                 echo "invalidmedia";
    //                 exit;
    //             }
    //             if ($save) {
    //                 //Prepare Data for save
    //                 $level4AdvanceData['id'] = 0;
    //                 $level4AdvanceData['l4aapa_parent_id'] = $this->loggedInUser->user()->id;
    //                 $level4AdvanceData['l4aapa_profession_id'] = $profession_id;
    //                 $level4AdvanceData['l4aapa_media_name'] = $fileName;
    //                 $this->level4ActivitiesRepository->saveLevel4AdvanceActivityParent($level4AdvanceData);
    //             }
    //         }
    //     } else {
    //         echo "required";
    //         exit;
    //     }
    // }

    // public function submitLevel4AdvanceActivityForReview() {
    //     $postData = Input::all();
    //     if (isset($postData['data_id']) && !empty($postData['data_id'])) {
    //         //Update the status
    //         $sendMail = false;
    //         foreach ($postData['data_id'] as $key => $dataid) {
    //             if ($postData['data_status'][$key] == 0) {
    //                 $sendMail = true;
    //                 $updateData['l4aapa_is_verified'] = 1;
    //                 $this->level4ActivitiesRepository->updateStatusAdvanceTaskParent($dataid, $updateData);
    //                 $advanceActivityData = $this->level4ActivitiesRepository->getUserAdvanceTaskByIdForParent($dataid);
    //             }
    //         }
    //         if(isset($advanceActivityData) && !empty($advanceActivityData)){
    //             if($advanceActivityData[0]->l4aapa_media_type == 1){
    //               $activityType = 'Video';
    //             }
    //             elseif($advanceActivityData[0]->l4aapa_media_type == 2){
    //               $activityType = 'Document';
    //             }else{
    //               $activityType = 'Image';
    //             }
    //             $profession = $advanceActivityData[0]->pf_name;

    //         }else{
    //             $profession = '';
    //             $activityType = '';
    //         }

    //         if ($sendMail) {
    //             //Send notification mail to admin
    //             $teenagerDetailbyId = $this->parentsRepository->getParentById($this->loggedInUser->user()->id);
    //             $replaceArray = array();
    //             $replaceArray['TEEN_NAME'] = $teenagerDetailbyId->p_first_name;
    //             $replaceArray['TEEN_EMAIL'] = $teenagerDetailbyId->p_email;
    //             $replaceArray['PROFESSION'] = $profession;
    //             $replaceArray['ACTIVITY_TYPE'] = $activityType;
    //             $emailTemplateContent = $this->templateRepository->getEmailTemplateDataByName(Config::get('constant.USER_SUBMITTED_ADVANCE_TASK'));
    //             $content = $this->templateRepository->getEmailContent($emailTemplateContent->et_body, $replaceArray);
    //             $adminEmail = Helpers::getConfigValueByKey('ADMIN_EMAIL');
    //             $adminName = Helpers::getConfigValueByKey('ADMIN_NAME');
    //             $data = array();
    //             $data['subject'] = $emailTemplateContent->et_subject;
    //             $data['toEmail'] = $adminEmail;
    //             $data['toName'] = $adminName;
    //             $data['content'] = $content;
    //             $data['teen_id'] = $teenagerDetailbyId->id;
    //             Mail::send(['html' => 'emails.Template'], $data, function($message) use ($data) {
    //                         $message->subject($data['subject']);
    //                         $message->to($data['toEmail'], $data['toName']);
    //                     });
    //         }
    //     }
    //     return Redirect::to("parent/level4-advance-step2/" . $postData['profession_id_review'] . "/" . $postData['data_type'] . "/" . $postData['teen_id'])->with('success', 'Your tasks has been submitted for review.');
    //     exit;
    // }

    /*
     * Delte user task
     */

    // public function deleteUserAdvanceTask() {
    //     $postData = Input::all();
    //     if (isset($postData) && !empty($postData)) {
    //         $id = $postData['task_id'];
    //         $result = $this->level4ActivitiesRepository->deleteParentAdvanceTask($id);
    //         if ($result) {
    //             if ($postData['media_type'] == 3) {
    //                 // unlink($this->level4AdvanceOriginalImageUploadPath . $postData['media_name']);
    //                 // unlink($this->level4AdvanceThumbImageUploadPath . $postData['media_name']);

    //                 //Delete file from AWS
    //                 $originalImage = $this->fileStorageRepository->deleteFileToStorage($postData['media_name'], $this->level4AdvanceOriginalImageUploadPath, "s3");
    //                 $thumbImage = $this->fileStorageRepository->deleteFileToStorage($postData['media_name'], $this->level4AdvanceThumbImageUploadPath, "s3");
    //             } else if ($postData['media_type'] == 1) {
    //                 unlink($this->level4AdvanceOriginalImageUploadPath . $postData['media_name']);
    //             } else {

    //                 //Delete document from AWS
    //                 $originalDocument = $this->fileStorageRepository->deleteFileToStorage($postData['media_name'], $this->level4AdvanceOriginalImageUploadPath, "s3");
    //             }
    //         }
    //     }
    // }

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
        $this->log->info('Retrieve L4 advance activity data', array('userId' => Auth::guard('parent')->user()->id));
        return view('parent.basic.careerAdvanceQuizData', compact('activityData', 'professionDetail', 'type'));
    }

    /*
     * Returns L4 upload media section view
     */
    public function getMediaUploadSection()
    {
        return view('parent.basic.careerAdvanceQuizSection');
    }

    /*
     * Returns L4 advance activity uploaded media details 
     */
    public function getLevel4AdvanceStep2Details() {
        $userId = Auth::guard('parent')->user()->id;
        $professionId = Input::get('professionId');
        $typeId = Input::get('type');
        $teenId = Input::get('teenId');
        $professionId = intval($professionId);
        $totalBasicQuestion = $this->level4ActivitiesRepository->getNoOfTotalQuestionsAttemptedQuestionForParent($userId, $professionId);

        if ($totalBasicQuestion[0]->NoOfTotalQuestions > 0 && ( $totalBasicQuestion[0]->NoOfTotalQuestions > $totalBasicQuestion[0]->NoOfAttemptedQuestions ) ) {
            $response['status'] = 0;
            $response['message'] = "First, play basic to play advance activtiy."; 
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
            $userLevel4AdvanceImageTask = $this->level4ActivitiesRepository->getLevel4AdvanceActivityTaskByParent(Auth::guard('parent')->user()->id, $professionId, Config::get('constant.ADVANCE_IMAGE_TYPE'));
            $userLevel4AdvanceDocumentTask = $this->level4ActivitiesRepository->getLevel4AdvanceActivityTaskByParent(Auth::guard('parent')->user()->id, $professionId, Config::get('constant.ADVANCE_DOCUMENT_TYPE'));
            $userLevel4AdvanceVideoTask = $this->level4ActivitiesRepository->getLevel4AdvanceActivityTaskByParent(Auth::guard('parent')->user()->id, $professionId, Config::get('constant.ADVANCE_VIDEO_TYPE'));
            $response['status'] = 1;
            $response['message'] = trans('appmessages.default_success_msg');
            return view('parent.basic.careerAdvanceQuizStep2Activity', compact('response', 'typeId', 'professionId', 'professionDetail', 'userLevel4AdvanceImageTask', 'level4AdvanceThumbImageUploadPath', 'level4AdvanceOriginalImageUploadPath', 'userLevel4AdvanceDocumentTask', 'userLevel4AdvanceVideoTask'));
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
                $totalMedia = $this->level4ActivitiesRepository->getParentUploadedMediaByType(Auth::guard('parent')->user()->id, $profession_id, $media_type);
                $ext = $file->getClientOriginalExtension();
                $save = false;
                //check for image extension
                if ($media_type == 3) {
                    if($totalMedia->count() > 0 && $totalMedia->count() >= Config::get('constant.DEFAULT_TOTAL_ADVANCE_IMAGE_COUNT')) {
                        $response['status'] = 0;
                        $response['message'] = "You reached maximum image upload limit. Total uploaded images are ". count($totalMedia->count()) ."!";
                        return response()->json($response, 200);
                        exit;    
                    }
                    $validImageExtArr = array('jpg', 'jpeg', 'png', 'bmp', 'PNG');
                    if (in_array(strtolower($ext), $validImageExtArr)) {
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
                        $level4AdvanceData['l4aapa_media_type'] = 3;
                    } else {
                        $response['status'] = 0;
                        $response['message'] = "Invalid media uploaded";
                        return response()->json($response, 200);
                        exit;
                    }
                } elseif ($media_type == 2) {
                    if($totalMedia->count() > 0 && $totalMedia->count() >= Config::get('constant.DEFAULT_TOTAL_ADVANCE_DOCUMENT_COUNT')) {
                        $response['status'] = 0;
                        $response['message'] = "You reached maximum document upload limit. Total uploaded document is ". count($totalMedia->count()) ."!";
                        return response()->json($response, 200);
                        exit;    
                    }
                    $validImageExtArr = array('pdf', 'docx', 'doc', 'ppt', 'pptx', 'xls', 'xlsx');
                    if (in_array(strtolower($ext), $validImageExtArr)) {
                        $save = true;
                        $fileName = 'advance_' . time() . '.' . $file->getClientOriginalExtension();
                        Input::file('media')->move($this->level4AdvanceOriginalImageUploadPath, $fileName); // uploading file to given path
                        $docOriginalPath = public_path($this->level4AdvanceOriginalImageUploadPath . $fileName);
                        //Uploading on AWS
                        $originalDoc = $this->fileStorageRepository->addFileToStorage($fileName, $this->level4AdvanceOriginalImageUploadPath, $docOriginalPath, "s3");
                        \File::delete($this->level4AdvanceOriginalImageUploadPath . $fileName);
                        $level4AdvanceData['l4aapa_media_type'] = 2;
                    } else {
                        $response['status'] = 0;
                        $response['message'] = "Invalid media uploaded";
                        return response()->json($response, 200);
                        exit;
                    }
                } elseif ($media_type == 1) {
                    if($totalMedia->count() > 0 && $totalMedia->count() >= Config::get('constant.DEFAULT_TOTAL_ADVANCE_VIDEO_COUNT')) {
                        $response['status'] = 0;
                        $response['message'] = "You reached maximum video upload limit. Total uploaded video is". count($totalMedia->count()) ."!";
                        return response()->json($response, 200);
                        exit;    
                    }
                    $validImageExtArr = array('mov', 'avi', 'mp4', 'mkv', 'wmv','flv');
                    if (in_array(strtolower($ext), $validImageExtArr)) {
                        $save = true;
                        $fileName = 'advance_' . time() . '.' . $file->getClientOriginalExtension();
                        Input::file('media')->move($this->level4AdvanceOriginalImageUploadPath, $fileName); // uploading file to given path
                        $videoOriginalPath = public_path($this->level4AdvanceOriginalImageUploadPath . $fileName);
                        //Uploading on AWS
                        $originalDoc = $this->fileStorageRepository->addFileToStorage($fileName, $this->level4AdvanceOriginalImageUploadPath, $videoOriginalPath, "s3");
                        \File::delete($this->level4AdvanceOriginalImageUploadPath . $fileName);
                        $level4AdvanceData['l4aapa_media_type'] = 1;
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
                    $level4AdvanceData['l4aapa_parent_id'] = Auth::guard('parent')->user()->id;
                    $level4AdvanceData['l4aapa_profession_id'] = $profession_id;
                    $level4AdvanceData['l4aapa_media_name'] = $fileName;
                    $this->level4ActivitiesRepository->saveLevel4AdvanceActivityParent($level4AdvanceData);
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
                    $updateData['l4aapa_is_verified'] = 1;
                    $this->level4ActivitiesRepository->updateStatusAdvanceTaskParent($dataid, $updateData);
                    $advanceActivityData = $this->level4ActivitiesRepository->getUserAdvanceTaskByIdForParent($dataid);
                }
            }
            if (isset($advanceActivityData) && count($advanceActivityData) > 0) {
                if ($advanceActivityData[0]->l4aapa_media_type == 1) {
                    $activityType = 'Video';  
                } else if($advanceActivityData[0]->l4aapa_media_type == 2) {
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
                $teenagerDetailbyId = $this->parentsRepository->getParentById(Auth::guard('parent')->user()->id);
                $replaceArray = array();
                $replaceArray['TEEN_NAME'] = $teenagerDetailbyId->p_first_name;
                $replaceArray['TEEN_EMAIL'] = $teenagerDetailbyId->p_email;
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
                $response['mediaType'] = $advanceActivityData[0]->l4aapa_media_type;
                return response()->json($response, 200);
                exit;
            } else {
                $response['status'] = 0;
                $response['message'] = "Something went wrong";
                $response['mediaType'] = $advanceActivityData[0]->l4aapa_media_type;
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
            $result = $this->level4ActivitiesRepository->deleteParentAdvanceTask($id);
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

