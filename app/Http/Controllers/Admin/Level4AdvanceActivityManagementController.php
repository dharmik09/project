<?php

namespace App\Http\Controllers\Admin;

use Auth;
use Input;
use Image;
use DB;
use File;
use Config;
use Request;
use Helpers;
use Redirect;
use Mail;
use App\Services\Professions\Contracts\ProfessionsRepository;
use Illuminate\Pagination\Paginator;
use App\Level4Activity;
use App\Http\Requests\Level4ActivityRequest;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Services\Level4Activity\Contracts\Level4ActivitiesRepository;
use App\Services\Teenagers\Contracts\TeenagersRepository;
use App\Services\Template\Contracts\TemplatesRepository;
use App\Services\Parents\Contracts\ParentsRepository;
use App\ProfessionLearningStyle;
use App\UserLearningStyle;
use App\DeviceToken;
use Cache;
use App\Services\FileStorage\Contracts\FileStorageRepository;

class Level4AdvanceActivityManagementController extends Controller {

    public function __construct(FileStorageRepository $fileStorageRepository, ProfessionsRepository $professionsRepository, Level4ActivitiesRepository $level4ActivitiesRepository, TeenagersRepository $teenagersRepository, TemplatesRepository $templatesRepository, ParentsRepository $parentsRepository) {
        $this->professionsRepository = $professionsRepository;
        $this->teenagersRepository = $teenagersRepository;
        $this->parentsRepository = $parentsRepository;
        $this->objLevel4Activities = new Level4Activity();
        $this->templateRepository = $templatesRepository;
        $this->level4ActivitiesRepository = $level4ActivitiesRepository;
        $this->level4PointsForQuestions = Config::get('constant.LEVEL4_POINTS_FOR_QUESTION');
        $this->level4TimerForQuestions = Config::get('constant.LEVEL4_TIMER_FOR_QUESTION');
        $this->level4AdvanceOriginalImageUploadPath = Config::get('constant.LEVEL4_ADVANCE_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->level4AdvanceThumbImageUploadPath = Config::get('constant.LEVEL4_ADVANCE_THUMB_IMAGE_UPLOAD_PATH');
        $this->level4AdvanceThumbImageHeight = Config::get('constant.LEVEL4_ADVANCE_THUMB_IMAGE_HEIGHT');
        $this->level4AdvanceThumbImageWidth = Config::get('constant.LEVEL4_ADVANCE_THUMB_IMAGE_WIDTH');
        $this->userCerfificatePath = Config::get('constant.CERTIFICATE_PATH');
        $this->fileStorageRepository = $fileStorageRepository;
    }

    public function index() {
        $leve4advanceactivities = $this->level4ActivitiesRepository->getAllLevel4AdvanceActivity();
        return view('admin.ListLevel4AdvanceActivity', compact('leve4advanceactivities'));
    }

    public function add() {
        $level4advacneactvityDetail = [];
        return view('admin.EditLevel4AdavanceActivity', compact('level4advacneactvityDetail'));
    }

    public function edit($id) {
        $level4advacneactvityDetail = $this->level4ActivitiesRepository->getLevel4AdvanceActivityById($id);
        return view('admin.EditLevel4AdavanceActivity', compact('level4advacneactvityDetail'));
    }

    public function savelevel4advanceactivity() {
        $saveData = [];
        $allPostdata = Input::All();

        if (isset($allPostdata)) {
            $saveData['id'] = $allPostdata['id'];
            $saveData['l4aa_type'] = $allPostdata['activity_type'];
            $saveData['l4aa_text'] = $allPostdata['question_text'];
            $saveData['l4aa_description'] = $allPostdata['description'];
            $saveData['deleted'] = $allPostdata['deleted'];
        }
        $response = $this->level4ActivitiesRepository->saveLevel4AdvanceActivityDetail($saveData);
        if ($response) {
            return Redirect::to("admin/listlevel4advanceactivity")->with('success', trans('labels.level4activityupdatesuccess'));
        } else {
            return Redirect::to("admin/listlevel4advanceactivity")->with('error', trans('labels.commonerrormessage'));
        }
    }

    public function delete($id) {
        $return = $this->level4ActivitiesRepository->deleteLevel4AdvanceActivity($id);
        if ($return) {
            return Redirect::to("admin/listlevel4advanceactivity")->with('success', trans('labels.level4activitydeletesuccess'));
        } else {
            return Redirect::to("admin/listlevel4advanceactivity")->with('error', trans('labels.commonerrormessage'));
        }
    }

    /*
     * Get User Task
     */

    public function level4AdvanceActivityUserTask() {
        $userTasks = $this->level4ActivitiesRepository->getUserTaskForAdmin();
        return view('admin.ListLevel4AdvanceActivityUser', compact('userTasks','searchParamArray'));
    }

    /*
     * Get user All activites by profession
     */

    public function viewUserAllAdvanceActivities($teenager, $profession, $typeId = 3) {
        $level4AdvanceThumbImageUploadPath = $this->level4AdvanceThumbImageUploadPath;
        $level4AdvanceOriginalImageUploadPath = $this->level4AdvanceOriginalImageUploadPath;
        $professionDetail = $this->professionsRepository->getProfessionsDataFromId($profession);
        $teenagerDetail = $this->teenagersRepository->getTeenagerById($teenager);

        $userAllImageTasks = $this->level4ActivitiesRepository->getUserAllTasksForAdvanceLevel($teenager, $profession, Config::get('constant.ADVANCE_IMAGE_TYPE'));
        $userAllDocumentTasks = $this->level4ActivitiesRepository->getUserAllTasksForAdvanceLevel($teenager, $profession, Config::get('constant.ADVANCE_DOCUMENT_TYPE'));
        $userAllVideoTasks = $this->level4ActivitiesRepository->getUserAllTasksForAdvanceLevel($teenager, $profession, Config::get('constant.ADVANCE_VIDEO_TYPE'));
        $validTypeArr = array(Config::get('constant.ADVANCE_IMAGE_TYPE'), Config::get('constant.ADVANCE_DOCUMENT_TYPE'), Config::get('constant.ADVANCE_VIDEO_TYPE'));
        $typeId = intval($typeId);
        if (in_array($typeId, $validTypeArr)) {
            $typeId = $typeId;
        } else {
            $typeId = Config::get('constant.ADVANCE_IMAGE_TYPE');
        }
        return view('admin.Level4AdvanceUserTasks', compact('teenagerDetail', 'typeId', 'professionDetail', 'userAllImageTasks', 'userAllDocumentTasks', 'userAllVideoTasks', 'level4AdvanceThumbImageUploadPath', 'level4AdvanceOriginalImageUploadPath'));
    }
    /*
     * Verify User task
     */
    public function verifyUserAdvanceTask() {
        $postData = Input::all();
        $image = [];
        $photos = [];
        if ($postData['typeId'] == 3) {
            foreach ($postData['note'] as $key => $value) {
                $data = $this->level4ActivitiesRepository->getImageNameById($key);
                $photo = $data[0]->l4aaua_media_name;
                if ($photo != '' && file_exists($this->level4AdvanceThumbImageUploadPath . $photo)) {
                    //$image[] = asset($this->level4AdvanceThumbImageUploadPath . $photo);
                    $image[] = Config::get('constant.DEFAULT_AWS') . $this->level4AdvanceThumbImageUploadPath . $photo;
                } else {
                    $image[] = asset("/backend/images/logo.png");
                }
                $photos[] = $photo;
            }
        }
        $saveUserData = array();
        $earnedPoints = 0;
        if (isset($postData['boosterPoint']) && !empty($postData)) {
            foreach ($postData['boosterPoint'] as $key => $val) {
                if (isset($postData['status'][$key]) && $postData['status'][$key] != '' && $postData['verified_status'][$key] != 2 && $postData['verified_status'][$key] != 3) {
                    $earnedPoints = $earnedPoints+$val;
                    $saveUserData['l4aaua_earned_points'] = $val;
                    $saveUserData['l4aaua_is_verified'] = isset($postData['status'][$key]) ? $postData['status'][$key] : '1';
                    $saveUserData['l4aaua_note'] = isset($postData['note'][$key]) ? $postData['note'][$key] : '';
                    $saveUserData['l4aaua_verified_by'] = Auth::guard('admin')->user()->id;
                    $saveUserData['l4aaua_verified_date'] = date('Y-m-d');
                    $this->level4ActivitiesRepository->updateUserTaskStatusByAdmin($key, $saveUserData);

                    $teenagerLevel4PointsRow = [];
                    $teenagerLevel4PointsRow['tlb_teenager'] = $postData['teenager'];
                    $teenagerLevel4PointsRow['tlb_level'] = config::get('constant.LEVEL4_ID');
                    $teenagerLevel4PointsRow['tlb_profession'] = $postData['profession_id'];

                    $teenagerLevelPoints = DB::table(config::get('databaseconstants.TBL_TEENAGER_LEVEL_BOOSTERS'))->where(["tlb_teenager" => $postData['teenager'], "tlb_level" => config::get('constant.LEVEL4_ID'), "tlb_profession" => $postData['profession_id']])->first();

                    if ($teenagerLevelPoints) {
                        $teenagerLevelPoints = (array) $teenagerLevelPoints;
                        $teenagerLevel4PointsRow['tlb_points'] = $teenagerLevelPoints['tlb_points'] + $val;
                        DB::table(config::get('databaseconstants.TBL_TEENAGER_LEVEL_BOOSTERS'))->where('id', $teenagerLevelPoints['id'])->update($teenagerLevel4PointsRow);
                    } else {
                        $teenagerLevel4PointsRow['tlb_points'] = $val;
                        DB::table(config::get('databaseconstants.TBL_TEENAGER_LEVEL_BOOSTERS'))->insert($teenagerLevel4PointsRow);
                    }
                }
            }
            $teenId = $postData['teenager'];
            $professionId = $postData['profession_id'];
            $templateId = '';
            $objProfessionLearningStyle = new ProfessionLearningStyle();
            if ($postData['typeId'] == 3) {
                $templateId = "L4AP";
            } else if ($postData['typeId'] == 2) {
                $templateId = "L4AD";
            } else if ($postData['typeId'] == 1) {
                $templateId = "L4AV";
            }


            $learningId = $objProfessionLearningStyle->getIdByProfessionIdForAdvance($professionId,$templateId);

            if ($learningId != '') {
                $objUserLearningStyle = new UserLearningStyle();
                $learningData = $objUserLearningStyle->getUserLearningStyle($learningId);
                if (!empty($learningData)) {
                    $earnedPoints += $learningData->uls_earned_points;
                }
                $userData = [];
                $userData['uls_learning_style_id'] = $learningId;
                $userData['uls_profession_id'] = $professionId;
                $userData['uls_teenager_id'] = $teenId;
                $userData['uls_earned_points'] = $earnedPoints;
                $result = $objUserLearningStyle->saveUserLearningStyle($userData);
            }
            $ProfessionName = $this->professionsRepository->getProfessionNameById($professionId);
            $result = $this->teenagersRepository->getTeenagerByTeenagerId($teenId);

            $objDeviceToken = new DeviceToken();
            $tokenResult = $objDeviceToken->getDeviceTokenDetail($result['id']);
            if (!empty($tokenResult)) {
                foreach ($tokenResult AS $k => $tData) {
                    $token = $tData->tdt_device_token;
                    $data = [];
                    $data['message'] = "Congratulations! Your L4 Advanced Submission for ". $ProfessionName ." just got approved";
                    $certificatePath = $this->userCerfificatePath;
                    if ($tData->tdt_device_type == 1) {
                        $return = Helpers::pushNotificationForiPhone($token,$data,$certificatePath);
                    } else if ($tData->tdt_device_type == 2) {
                        $return = Helpers::pushNotificationForAndroid($token,$data);
                    }
                }
            }
            /*$getTeenagerBoosterPoints = $this->teenagersRepository->getTeenagerBoosterPoints($result['id']);
            if (!empty($getTeenagerBoosterPoints)) {
                $return = Helpers::sendMilestoneNotification($result['id'],$getTeenagerBoosterPoints['total']);
            }*/
            $type = '';
            if ($postData['typeId'] == 3) {
              $type = 'Image';
            } else if ($postData['typeId'] == 2) {
              $type = 'Document';
            } else if ($postData['typeId'] == 1) {
              $type = 'Video';
            }

            $replaceArray = array();
            $replaceArray['TEEN_NAME'] = $result['t_name'];
            $replaceArray['PROFESSION_NAME'] = $ProfessionName;
            $replaceArray['TASK_TYPE'] = $type;
            $emailTemplateContent = $this->templateRepository->getEmailTemplateDataByName(Config::get('constant.USER_TASK_REVIEW_TEMPLATE'));
            $content = $this->templateRepository->getEmailContent($emailTemplateContent->et_body, $replaceArray);

            $data = array();
            $data['subject'] = 'User Advances Task Approved';
            $data['toEmail'] = $result['t_email'];
            $data['toName'] = $result['t_name'];
            $data['content'] = $content;

            Mail::send(['html' => 'emails.Template'], $data , function ($m) use ($data) {
                $m->from(Config::get('constant.FROM_MAIL_ID'), 'Advance Task Approved');
                $m->subject($data['subject']);
                $m->to($data['toEmail'], $data['toName']);
            });
        }
        return Redirect::to("admin/viewUserAllAdvanceActivities/" . $postData['teenager'] . '/' . $postData['profession_id']. '/' .$postData['typeId'])->with('success', 'Task has been updated successfully');
    }

    /*
     * Delte user task id admin find any weird thing in that
     */

    public function deleteUserAdvanceTask() {
        $postData = Input::all();
        if (isset($postData) && !empty($postData)) {
            $id = $postData['task_id'];
            $result = $this->level4ActivitiesRepository->deleteUserAdvanceTask($id);
            if ($result) {
                if ($postData['media_type'] == 3) {
                    @unlink($this->level4AdvanceOriginalImageUploadPath . $postData['media_name']);
                    @unlink($this->level4AdvanceThumbImageUploadPath . $postData['media_name']);
                    $deleteFile = $this->fileStorageRepository->deleteFileToStorage($postData['media_name'], $this->level4AdvanceOriginalImageUploadPath, "s3");
                    $deleteFile = $this->fileStorageRepository->deleteFileToStorage($postData['media_name'], $this->level4AdvanceThumbImageUploadPath, "s3");
                } else {
                    @unlink($this->level4AdvanceOriginalImageUploadPath . $postData['media_name']);
                    $deleteFile = $this->fileStorageRepository->deleteFileToStorage($postData['media_name'], $this->level4AdvanceOriginalImageUploadPath, "s3");
                }
            }
        }
    }
    /*
     * Get Parent Task
     */
    public function level4AdvanceActivityParentTask() {
        $searchParamArray = Input::all();
        if (isset($searchParamArray['clearSearch'])) {
            unset($searchParamArray);
            $searchParamArray = array();
        }
        $userTasks = $this->level4ActivitiesRepository->getParentTaskForAdmin($searchParamArray);
        return view('admin.ListLevel4AdvanceActivityParent', compact('userTasks','searchParamArray'));
    }

    /*
     * Get parent All activites by profession
     */

    public function viewParentAllAdvanceActivities($parent, $profession, $typeId = 3) {
        $level4AdvanceThumbImageUploadPath = $this->level4AdvanceThumbImageUploadPath;
        $level4AdvanceOriginalImageUploadPath = $this->level4AdvanceOriginalImageUploadPath;
        $professionDetail = $this->professionsRepository->getProfessionsDataFromId($profession);
        $parentDetail = $this->parentsRepository->getParentById($parent);

        $userAllImageTasks = $this->level4ActivitiesRepository->getParentAllTasksForAdvanceLevel($parent, $profession, Config::get('constant.ADVANCE_IMAGE_TYPE'));
        $userAllDocumentTasks = $this->level4ActivitiesRepository->getParentAllTasksForAdvanceLevel($parent, $profession, Config::get('constant.ADVANCE_DOCUMENT_TYPE'));
        $userAllVideoTasks = $this->level4ActivitiesRepository->getParentAllTasksForAdvanceLevel($parent, $profession, Config::get('constant.ADVANCE_VIDEO_TYPE'));
        $validTypeArr = array(Config::get('constant.ADVANCE_IMAGE_TYPE'), Config::get('constant.ADVANCE_DOCUMENT_TYPE'), Config::get('constant.ADVANCE_VIDEO_TYPE'));
        $typeId = intval($typeId);
        if (in_array($typeId, $validTypeArr)) {
            $typeId = $typeId;
        } else {
            $typeId = Config::get('constant.ADVANCE_IMAGE_TYPE');
        }
        return view('admin.Level4AdvanceParentTasks', compact('parentDetail', 'typeId', 'professionDetail', 'userAllImageTasks', 'userAllDocumentTasks', 'userAllVideoTasks', 'level4AdvanceThumbImageUploadPath', 'level4AdvanceOriginalImageUploadPath'));
    }

    /*
     * Verify User task
     */

    public function verifyParentAdvanceTask() {
        $postData = Input::all();
        $image = [];
        $photos = [];
        if ($postData['typeId'] == 3) {
            foreach ($postData['note'] as $key => $value) {
                $data = $this->level4ActivitiesRepository->getImageNameByIdForParent($key);
                $photo = $data[0]->l4aapa_media_name;
                if ($photo != '' && file_exists($this->level4AdvanceThumbImageUploadPath . $photo)) {
                    $image[] = asset($this->level4AdvanceThumbImageUploadPath . $photo);
                } else {
                    $image[] = asset("/backend/images/logo.png");
                }
                $photos[] = $photo;
            }
        }
        $saveUserData = array();
        $earnedPoints = '';
        if (isset($postData) && !empty($postData)) {
            foreach ($postData['boosterPoint'] as $key => $val) {
                if (isset($postData['status'][$key]) && $postData['status'][$key] != '' && $postData['verified_status'][$key] != 2 && $postData['verified_status'][$key] != 3) {
                    $earnedPoints = $earnedPoints+$val;
                    $saveUserData['l4aapa_earned_points'] = $val;
                    $saveUserData['l4aapa_is_verified'] = isset($postData['status'][$key]) ? $postData['status'][$key] : '1';
                    $saveUserData['l4aapa_note'] = isset($postData['note'][$key]) ? $postData['note'][$key] : '';
                    $saveUserData['l4aapa_verified_by'] = Auth::guard('admin')->user()->id;
                    $saveUserData['l4aapa_verified_date'] = date('Y-m-d');
                    $this->level4ActivitiesRepository->updateParentTaskStatusByAdmin($key, $saveUserData);

                    $parentLevel4PointsRow = [];
                    $parentLevel4PointsRow['plb_parent_id'] = $postData['parent'];
                    $parentLevel4PointsRow['plb_level'] = config::get('constant.LEVEL4_ID');
                    $parentLevel4PointsRow['plb_profession'] = $postData['profession_id'];

                    $teenagerLevelPoints = DB::table(config::get('databaseconstants.TBL_PARENT_LEVEL_BOOSTERS'))->where(["plb_parent_id" => $postData['parent'], "plb_level" => config::get('constant.LEVEL4_ID'), "plb_profession" => $postData['profession_id']])->first();

                    if ($teenagerLevelPoints) {
                        $teenagerLevelPoints = (array) $teenagerLevelPoints;
                        $parentLevel4PointsRow['plb_points'] = $teenagerLevelPoints['plb_points'] + $val;
                        DB::table(config::get('databaseconstants.TBL_PARENT_LEVEL_BOOSTERS'))->where('id', $teenagerLevelPoints['id'])->update($parentLevel4PointsRow);
                    } else {
                        $parentLevel4PointsRow['plb_points'] = $val;
                        DB::table(config::get('databaseconstants.TBL_PARENT_LEVEL_BOOSTERS'))->insert($parentLevel4PointsRow);
                    }
                }
            }

            $type = '';
            if ($postData['typeId'] == 3) {
              $type = 'Image';
            } else if ($postData['typeId'] == 2) {
              $type = 'Document';
            } else if ($postData['typeId'] == 1) {
              $type = 'Video';
            }

            $parentDetail = $this->parentsRepository->getParentById($postData['parent']);
            $ProfessionName = $this->professionsRepository->getProfessionNameById($postData['profession_id']);

            $replaceArray = array();
            $replaceArray['TEEN_NAME'] = $parentDetail->p_first_name;
            $replaceArray['PROFESSION_NAME'] = $ProfessionName;
            $replaceArray['TASK_TYPE'] = $type;
            $emailTemplateContent = $this->templateRepository->getEmailTemplateDataByName(Config::get('constant.USER_TASK_REVIEW_TEMPLATE'));
            $content = $this->templateRepository->getEmailContent($emailTemplateContent->et_body, $replaceArray);

            $data = array();
            $data['subject'] = 'User Advances Task Approved';
            $data['toEmail'] = $parentDetail->p_email;
            $data['toName'] = $parentDetail->p_first_name;
            $data['content'] = $content;

            Mail::send(['html' => 'emails.Template'], $data , function ($m) use ($data) {
                $m->from(Config::get('constant.FROM_MAIL_ID'), 'Advance Task Approved');
                $m->subject($data['subject']);
                $m->to($data['toEmail'], $data['toName']);
            });
        }
        return Redirect::to("admin/viewParentAllAdvanceActivities/" . $postData['parent'] . '/' . $postData['profession_id']. '/' .$postData['typeId'])->with('success', 'Task has been updated successfully');
    }


    /*
     * Delte parent task id admin find any weird thing in that
     */

    public function deleteParentAdvanceTask() {
        $postData = Input::all();
        if (isset($postData) && !empty($postData)) {
            $id = $postData['task_id'];
            $result = $this->level4ActivitiesRepository->deleteParentAdvanceTask($id);
            if ($result) {
                if ($postData['media_type'] == 3) {
                    unlink($this->level4AdvanceOriginalImageUploadPath . $postData['media_name']);
                    unlink($this->level4AdvanceThumbImageUploadPath . $postData['media_name']);
                } else {
                    unlink($this->level4AdvanceOriginalImageUploadPath . $postData['media_name']);
                }
            }
        }
    }

}
