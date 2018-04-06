<?php

namespace App\Http\Controllers\Admin;

use Auth;
use Input;
use Image;
use File;
use Config;
use Request;
use Helpers;
use Redirect;
use App\Services\Professions\Contracts\ProfessionsRepository;
use Illuminate\Pagination\Paginator;
use App\Level4Activity;
use App\Http\Requests\Level4ActivityRequest;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Services\Level4Activity\Contracts\Level4ActivitiesRepository;
use Cache;
use App\Services\Teenagers\Contracts\TeenagersRepository;
use App\Services\FileStorage\Contracts\FileStorageRepository;
use Storage;
use App\Jobs\SendPushNotificationToAllTeenagers;
use App\Notifications;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class Level4TemplateManagementController extends Controller {

    public function __construct(FileStorageRepository $fileStorageRepository, ProfessionsRepository $professionsRepository, Level4ActivitiesRepository $level4ActivitiesRepository,TeenagersRepository $teenagersRepository) {
        $this->professionsRepository = $professionsRepository;
        $this->objLevel4Activities = new Level4Activity();
        $this->level4ActivitiesRepository = $level4ActivitiesRepository;
        $this->level4PointsForQuestions = Config::get('constant.LEVEL4_POINTS_FOR_QUESTION');
        $this->level4TimerForQuestions = Config::get('constant.LEVEL4_TIMER_FOR_QUESTION');
        $this->conceptOriginalImageUploadPath = Config::get('constant.CONCEPT_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->questionOriginalImageUploadPath = Config::get('constant.LEVEL4_INTERMEDIATE_QUESTION_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->questionThumbImageUploadPath = Config::get('constant.LEVEL4_INTERMEDIATE_QUESTION_THUMB_IMAGE_UPLOAD_PATH');
        $this->answerOriginalImageUploadPath = Config::get('constant.LEVEL4_INTERMEDIATE_ANSWER_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->answerThumbImageUploadPath = Config::get('constant.LEVEL4_INTERMEDIATE_ANSWER_THUMB_IMAGE_UPLOAD_PATH');
        $this->responseOriginalImageUploadPath = Config::get('constant.LEVEL4_INTERMEDIATE_RESPONSE_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->responseThumbImageUploadPath = Config::get('constant.LEVEL4_INTERMEDIATE_RESPONSE_THUMB_IMAGE_UPLOAD_PATH');
        $this->questionDescriptionORIGINALImage = Config::get('constant.LEVEL4_INTERMEDIATE_QUESTION_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->teenagersRepository       = $teenagersRepository;
        $this->fileStorageRepository = $fileStorageRepository;
        $this->objNotifications = new Notifications();
        $this->log = new Logger('admin-copy-concept');
        $this->log->pushHandler(new StreamHandler(storage_path().'/logs/monolog-'.date('m-d-Y').'.log'));
    }

    public function index() {
        return view('admin.ListGamificationTemplate');
    }

    public function getIndex(){
        $templates = $this->level4ActivitiesRepository->getAllGamificationTemplateObj()->get()->count();
        $records = array();
        $columns = array(
            0 => 'id',
            1 => 'pf_name',
            2 => 'gt_template_title',
            3 => 'gt_coins',
            4 => 'tat_type',
            5 => 'deleted'
        );
        
        $order = Input::get('order');
        $search = Input::get('search');
        $records["data"] = array();
        $iTotalRecords = $templates;
        $iTotalFiltered = $iTotalRecords;
        $iDisplayLength = intval(Input::get('length')) <= 0 ? $iTotalRecords : intval(Input::get('length'));
        $iDisplayStart = intval(Input::get('start'));
        $sEcho = intval(Input::get('draw'));

        $records["data"] = $this->level4ActivitiesRepository->getAllGamificationTemplateObj();
        if (!empty($search['value'])) {
            $val = $search['value'];
            $records["data"]->where(function($query) use ($val) {
                $query->where('profession.pf_name', "Like", "%$val%");
                $query->orWhere('concepttemplate.gt_template_title', "Like", "%$val%");
            });

            // No of record after filtering
            $iTotalFiltered = $records["data"]->where(function($query) use ($val) {
                    $query->where('profession.pf_name', "Like", "%$val%");
                    $query->orWhere('concepttemplate.gt_template_title', "Like", "%$val%");
                })->count();
        }
        
        //order by
        foreach ($order as $o) {
            $records["data"] = $records["data"]->orderBy($columns[$o['column']], $o['dir']);
        }

        //limit
        $records["data"] = $records["data"]->take($iDisplayLength)->offset($iDisplayStart)->get();
        
        if (!empty($records["data"])) {
            foreach ($records["data"] as $key => $_records) {
                $records["data"][$key]->deleted = ($_records->deleted == 1) ? '<i class="s_active fa fa-square"></i>' : '<i class="s_inactive fa fa-square"></i>';
                $records["data"][$key]->action = '<a href="'.url('/admin/editGamificationTemplate').'/'.$_records->id.'"><i class="fa fa-edit"></i> &nbsp;&nbsp;</a><a onclick="return confirm(\'Are you sure you want to delete ?\')" href="'.url('/admin/deleteGamificationTemplate').'/'.$_records->id.'"><i class="i_delete fa fa-trash"></i> &nbsp;&nbsp;</a><a href="" onClick="add_coins_details('.$_records->id.');" data-toggle="modal" id="#templateCoinsData" data-target="#templateCoinsData"><i class="fa fa-database" aria-hidden="true"></i></a>'; 
            }
        }

        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalFiltered;

        return \Response::json($records);
        exit;
    }

    public function add()
    {
        $level4TemplateDetail = array();
        $allActiveProfessions = $this->professionsRepository->getAllActiveProfession();
        $leve4TemplateAnswrTypes = $this->level4ActivitiesRepository->getLevel4TemplateAnswerTypes();
        return view('admin.EditLevel4Template',compact('leve4TemplateAnswrTypes','level4TemplateDetail','allActiveProfessions'));
    }

    public function edit($id)
    {
        $level4TemplateDetail = $this->level4ActivitiesRepository->getGamificationTemplateById($id);
        $leve4TemplateAnswrTypes = $this->level4ActivitiesRepository->getLevel4TemplateAnswerTypes();
        $allActiveProfessions = $this->professionsRepository->getAllActiveProfession();
        $conceptOriginalImageUploadPath = $this->conceptOriginalImageUploadPath;
        return view('admin.EditLevel4Template', compact('level4TemplateDetail','leve4TemplateAnswrTypes','allActiveProfessions','conceptOriginalImageUploadPath'));
    }

    public function save()
    {
        $saveData = [];
        $allPostdata = Input::All();

        if(isset($allPostdata))
        {
            $saveData['id'] = $allPostdata['id'];
            $saveData['gt_template_title'] = trim($allPostdata['template_title']);
            $saveData['gt_template_descritpion'] = $allPostdata['template_description'];
            $answerTypeWithId = explode('##', $allPostdata['template_answer_type']);

            $saveData['gt_template_id'] = $answerTypeWithId[1];
            $saveData['gt_temlpate_answer_type'] = $answerTypeWithId[0];
            $saveData['gt_profession_id'] = $allPostdata['question_profession'];
            $saveData['deleted'] = $allPostdata['deleted'];
            $saveData['gt_template_image'] = $allPostdata['hidden_logo'];
            $saveData['gt_coins'] = $allPostdata['gt_coins'];
            if ($allPostdata['gt_valid_upto'] == 0 || $allPostdata['gt_valid_upto'] == '') {
                $saveData['gt_valid_upto'] = 30;
            } else {
                $saveData['gt_valid_upto'] = $allPostdata['gt_valid_upto'];
            }
            $professionName = '';
            //Get Profession name 
            $professionData = $this->professionsRepository->getProfessionsDataFromId($allPostdata['question_profession']);
            if(isset($professionData) && !empty($professionData))
            {
               $professionName = $professionData[0]->pf_name; 
            }

            if (Input::file())
            {
                $file = Input::file('gt_template_image');
                if(!empty($file))
                {
                    //Check image valid extension
                    $validationPass = Helpers::checkValidImageExtension($file);
                    if($validationPass)
                    {
                        $fileName = 'concept_' . time() . '.' . $file->getClientOriginalExtension();
                        $pathOriginal = public_path($this->conceptOriginalImageUploadPath.$fileName);
                        Image::make($file->getRealPath())->save($pathOriginal);

                        if($allPostdata['hidden_logo'] != '')
                        {
                            // $imageOriginal = public_path($this->conceptOriginalImageUploadPath.$allPostdata['hidden_logo']);
                            // File::delete($imageOriginal);
                            $originalImageDelete = $this->fileStorageRepository->deleteFileToStorage($allPostdata['hidden_logo'], $this->conceptOriginalImageUploadPath, "s3");
                        }
                        //Uploading on AWS
                        $originalImage = $this->fileStorageRepository->addFileToStorage($fileName, $this->conceptOriginalImageUploadPath, $pathOriginal, "s3");
                        //Deleting Local Files
                        \File::delete($this->conceptOriginalImageUploadPath . $fileName);
                        $saveData['gt_template_image'] = $fileName;
                    }
                }
            }
            $PopUpfile = Input::file('gt_template_descritpion_popup_imge');
            if(!empty($PopUpfile))
            {
                //Check image valid extension
                $validationPass = Helpers::checkValidImageExtension($PopUpfile);
                if($validationPass)
                {
                    $popupFileName = 'popup_' . str_random(10) . '.' . $PopUpfile->getClientOriginalExtension();
                    $pathOriginal = public_path($this->conceptOriginalImageUploadPath.$popupFileName);
                    Image::make($PopUpfile->getRealPath())->save($pathOriginal);
                    
                    //Uploading on AWS
                    $originalImage = $this->fileStorageRepository->addFileToStorage($popupFileName, $this->conceptOriginalImageUploadPath, $pathOriginal, "s3");
                    //Deleting Local Files
                    \File::delete($this->conceptOriginalImageUploadPath . $popupFileName);
                    
                    $saveData['gt_template_descritpion_popup_imge'] = $popupFileName;
                }
            }

        }
        $response = $this->level4ActivitiesRepository->saveGamificationTemplate($saveData);

        $notificationData['n_sender_id'] = '0';
        $notificationData['n_sender_type'] = Config::get('constant.NOTIFICATION_ADMIN');
        $notificationData['n_receiver_id'] = 0;
        $notificationData['n_receiver_type'] = Config::get('constant.NOTIFICATION_TEENAGER');
        $notificationData['n_notification_type'] = Config::get('constant.NOTIFICATION_TYPE_ADD_PROFESSION');
        $notificationData['n_notification_text'] = 'New Role Play <strong>'.$saveData['gt_template_title'].'</strong> updated in ProTeen!';
        $this->objNotifications->insertUpdate($notificationData);
        
        dispatch( new SendPushNotificationToAllTeenagers($notificationData['n_notification_text']) )->onQueue('processing');

//        $teenagers = $this->teenagersRepository->getAllActiveTeenagersForNotification();
//        foreach ($teenagers AS $key => $value) {
//            $message = 'Role play "' .$saveData['gt_template_title'].'" as a "'.$professionName.'" in ProTeen now!';
//            $return = Helpers::saveAllActiveTeenagerForSendNotifivation($value->id, $message);
//        }
        Cache::forget('gamificationTemplate');
        return Redirect::to("admin/listGamificationTemplate".$allPostdata['pageRank'])->with('success', trans('labels.level4activityupdatesuccess'));
    }

    public function delete($id)
    {
        $return = $this->level4ActivitiesRepository->deleteGamificationTemplate($id);
        if($return)
        {
            return Redirect::to("admin/listGamificationTemplate")->with('success', trans('labels.level4activitydeletesuccess'));
        }
        else
        {
            return Redirect::to("admin/listGamificationTemplate")->with('error', trans('labels.commonerrormessage'));
        }
    }

    public function getGamificationTemplateAnswerBox()
    {
        $postData = Input::all();
        if(isset($postData['template']) && $postData['template'] != '')
        {
            $gamificationTemplate = $postData['template'];
            switch ($gamificationTemplate) {
                case "option_choice":
                    return view('admin.templateAnswerBox.ajaxOptionChoice');
                    exit;
                    break;
                case "single_line_answer":
                    return view('admin.templateAnswerBox.ajaxSingleLine');
                    exit;
                    break;
                case "filling_blank":
                    return view('admin.templateAnswerBox.ajaxFillingBlank');
                    exit;
                    break;
                case "true_false":
                    return view('admin.templateAnswerBox.ajaxTrueFalse');
                    exit;
                    break;
                case "option_reorder":
                    return view('admin.templateAnswerBox.ajaxOptionReOrder');
                    exit;
                    break;
                case "image_reorder":
                    return view('admin.templateAnswerBox.ajaxImageDrag');
                    exit;
                    break;
                case "select_from_dropdown_option":
                    return view('admin.templateAnswerBox.ajaxSelectFromDropDown');
                    exit;
                    break;
                case "group_selection":
                    return view('admin.templateAnswerBox.ajaxGroupSelection');
                    exit;
                    break;
                case "option_choice_with_response":
                    return view('admin.templateAnswerBox.ajaxOptionChoiceWithResponse');
                    exit;
                    break;
                default:
                    echo "Something went wrong...";
            }
        }
        else{
            echo "something went wrong...";
            exit;
        }
    }

    public function copyConcept()
    {
        $professions = $this->professionsRepository->getAllActiveProfession();
        $concept = 0;
        return view('admin.CopyConcept',compact('professions','concept'));
    }

    public function saveCopyConcept()
    {
        $conceptIds = '';
        $postData = Input::all();
        $conceptNames = array();
        $conceptOriginalImageUploadPath = $this->conceptOriginalImageUploadPath;
        if (isset($postData['concept']) && !empty($postData['concept'])) {        
            foreach ($postData['concept'] as $key => $conceptId) {                
                $this->log->info('Basic concept data copy start from profession id ->'.$postData["professionId"].' to profession id ->'.$postData["to_profession_id"]); 
                
                if ($conceptId != 0) {
                    //copy existing image of concept
                    $conceptDetail = $this->level4ActivitiesRepository->getGamificationTemplateById($conceptId);
                    $this->log->info('Copy concept started, from concept Id ->'.$conceptId.' And concept name ->'.$conceptDetail->gt_template_title);     
                    $newconceptImage = '';
                    $file = $conceptOriginalImageUploadPath.$conceptDetail->gt_template_image;
                    if (Storage::size($conceptOriginalImageUploadPath.$conceptDetail->gt_template_image) > 0 && $conceptDetail->gt_template_image != '') {
                        $newconceptImage = $conceptId.'_'.time().'_'.$conceptDetail->gt_template_image;
                        $newfile = $conceptOriginalImageUploadPath.$newconceptImage;
                        if (!Storage::copy($file, $newfile)) {
                            return Redirect::to("admin/copyConcept")->withErrors('')->withInput();
                            exit;
                        }
                    }
                    $return = $this->level4ActivitiesRepository->copyConcept($conceptId,$postData['to_profession_id'],$newconceptImage);
                    $new_templateID[] = $return;
                    $conceptNames[] = $conceptDetail->gt_template_title;
                    $this->log->info('Basic concept data copy end from profession id ->'.$postData["professionId"].' to profession id ->'.$postData["to_profession_id"]); 
                    $this->log->info('From concept id ->'.$conceptId.' to newly created concept id->'.$return);  
                }
            }
            $professionName = '';
            //Get Profession name 
            $professionData = $this->professionsRepository->getProfessionsDataFromId($postData['to_profession_id']);
            if(isset($professionData) && !empty($professionData))
            {
               $professionName = $professionData[0]->pf_name; 
            }
            
            $countConcept = count($postData['concept']);
            $array = [];
            $imageInfo = array();
            for ($i = 0; $i < $countConcept; $i++) {
                $conceptId = $postData['concept'][$i];
                if ($conceptId != 0) {
                    $oldConceptId = $this->level4ActivitiesRepository->getLevel4ActivityData($conceptId);                    
                    $id = $oldConceptId[0]->oldId;
                    $oldId = explode(",",$id);
                   
                    for ($k = 0; $k < count($oldId); $k++) {
                        $this->log->info('Start Copy concept question data question id ->'.$oldId[$k]); 
                        $level4Detail = $this->level4ActivitiesRepository->getLevel4ActivityDataById($oldId[$k]);
                        
                        if (!empty($level4Detail)) {
                            $newImage = $newAudio = $newAudioFileName= '';
                            //Set Question audio
                            if (isset($level4Detail[0]->l4ia_question_audio) && $level4Detail[0]->l4ia_question_audio != '') {
                                if (Storage::size($this->questionDescriptionORIGINALImage . $level4Detail[0]->l4ia_question_audio) > 0) {
                                    $this->log->info('Question Audio file copy start...');
                                    $audioFile = $this->questionDescriptionORIGINALImage.$level4Detail[0]->l4ia_question_audio;
                                    $newAudioFileName = $oldId[$k].'_'.time().'_'.$level4Detail[0]->l4ia_question_audio;
                                    $newAudioFile = $this->questionDescriptionORIGINALImage.$newAudioFileName;
                                    if (!Storage::copy($audioFile, $newAudioFile)) 
                                    {
                                        $this->log->error('Question Audio file copy failed audio file name ->'.$level4Detail[0]->l4ia_question_audio);
                                        return Redirect::to("admin/copyConcept")->withErrors('Audio file not copied perfectly')->withInput();
                                        exit;
                                    }
                                    $this->log->info('Question audio file copied successfully old audio file name ->'.$level4Detail[0]->l4ia_question_audio.' and New audio file name ->'.$newAudioFileName);
                                } 

                            }
                            //$audioFile = public_path($this->questionOriginalImageUploadPath.$level4Detail[0]->l4ia_question_popup_image); 
                            $file = $this->questionOriginalImageUploadPath.$level4Detail[0]->l4ia_question_popup_image;
                            $thumbfile = $this->questionThumbImageUploadPath.$level4Detail[0]->l4ia_question_popup_image;
                            if (Storage::size($this->questionOriginalImageUploadPath.$level4Detail[0]->l4ia_question_popup_image) > 0 && $level4Detail[0]->l4ia_question_popup_image != '') {

                                $this->log->info('Question popup image copy start...');

                                $newImage = $oldId[$k].'_'.time().'_'.$level4Detail[0]->l4ia_question_popup_image;
                                $newfile = $this->questionOriginalImageUploadPath.$newImage;
                                $newthumbfile = $this->questionThumbImageUploadPath.$newImage;
                                if (!Storage::copy($file, $newfile)) {
                                    $this->log->error('Question popup image copy failed audio file name ->'.$level4Detail[0]->l4ia_question_popup_image);
                                    return Redirect::to("admin/copyConcept")->withErrors('')->withInput();
                                    exit;
                                }
                                $imageInfo = pathinfo($thumbfile); 
                                if($imageInfo['extension'] != 'gif')
                                {
                                    if (!Storage::copy($thumbfile, $newthumbfile)) {
                                        $this->log->error('Question popup image copy failed audio file name ->'.$level4Detail[0]->l4ia_question_popup_image);
                                        return Redirect::to("admin/copyConcept")->withErrors('')->withInput();
                                        exit;
                                    }
                                }
                                $this->log->info('Question popup image copied successfully old popup image name ->'.$level4Detail[0]->l4ia_question_popup_image.' and New popupimage name ->'.$newImage);
                            }
                            
                            $activityData = $this->level4ActivitiesRepository->copyLevel4ActivityData($oldId[$k],$postData['to_profession_id'],$new_templateID[$i],$newImage, $newAudioFileName);

                            $this->log->info('Successfully End Copy concept question data new question id ->'.$activityData); 
                        }
                        
                    }
                   
                    $count = count($oldId);
                    
                    $newConceptId = $this->level4ActivitiesRepository->getLevel4ActivityData($new_templateID[$i]);
                    $newId = $newConceptId[0]->oldId;                    
                    $newId = explode(",",$newId);
                    for ($j = 0; $j < $count; $j++) {

                        $this->log->info('Start copy question answer data for question id ->'.$oldId[$j]);

                        $optionDetail = $this->level4ActivitiesRepository->getLevel4ActivityOptionsData($oldId[$j]);
                        if (!empty($optionDetail)) 
                        {
                            $newanswerImageArray = [];
                            $newresponseImageArray = [];
                            $imageInfo = array();
                            foreach($optionDetail as $keyOption => $optionValue)
                            {
                                $this->log->info('Start copy answer image name ->'.$optionValue->l4iao_answer_image);
                                $newanswerImage = '';
                                $file = $this->answerOriginalImageUploadPath.$optionValue->l4iao_answer_image;
                                $thumbfile = $this->answerThumbImageUploadPath.$optionValue->l4iao_answer_image;
                                if (Storage::size($this->answerOriginalImageUploadPath.$optionValue->l4iao_answer_image) > 0 && $optionValue->l4iao_answer_image != '') {
                                    $newanswerImage = $oldId[$j].'_'.time().'_'.$optionValue->l4iao_answer_image;
                                    $newfile = $this->answerOriginalImageUploadPath.$newanswerImage;
                                    $newthumbfile = $this->answerThumbImageUploadPath.$newanswerImage;
                                    if (!Storage::copy($file, $newfile)) {
                                        $this->log->error('Copy answer image failed original name ->'.$optionValue->l4iao_answer_image);
                                        return Redirect::to("admin/copyConcept")->withErrors('')->withInput();
                                        exit;
                                    }
                                    if (!Storage::copy($thumbfile,$newthumbfile)) {
                                        $this->log->error('Copy answer image failed thumb name ->'.$optionValue->l4iao_answer_image);
                                        return Redirect::to("admin/copyConcept")->withErrors('')->withInput();
                                        exit;
                                    }

                                    $this->log->info('End successfully copy answer image name ->'.$newanswerImage);
                                }

                                $newresponseImage = '';
                                $file1 = $this->responseOriginalImageUploadPath.$optionValue->l4iao_answer_response_image;
                                $thumbfile1 = $this->responseThumbImageUploadPath.$optionValue->l4iao_answer_response_image;
                                if (Storage::size($this->responseOriginalImageUploadPath.$optionValue->l4iao_answer_response_image) > 0 && $optionValue->l4iao_answer_response_image != '') {

                                    $this->log->info('Start copy answer response image name ->'.$optionValue->l4iao_answer_response_image);

                                    $newresponseImage = $oldId[$j].'_'.time().'_'.$optionValue->l4iao_answer_response_image;
                                    $newfile1 = $this->responseOriginalImageUploadPath.$newresponseImage;
                                    $newThumbfile1 = $this->responseThumbImageUploadPath.$newresponseImage;
                                    if (!Storage::copy($file1, $newfile1)) {
                                        return Redirect::to("admin/copyConcept")->withErrors('')->withInput();
                                        exit;
                                    }
                                    $imageInfo = pathinfo($thumbfile1); 
                                    if($imageInfo['extension'] != 'gif')
                                    {    
                                        if (!Storage::copy($thumbfile1,$newThumbfile1)) {
                                            return Redirect::to("admin/copyConcept")->withErrors('')->withInput();
                                            exit;
                                        }
                                    }
                                    $this->log->info('End successfully copy answer response image name ->'.$newresponseImage);
                                } 

                                $newanswerImageArray[$optionValue->optionsId] = $newanswerImage;
                                $newresponseImageArray[$optionValue->optionsId] = $newresponseImage;
                            }
                            
                            $activityOptionData = $this->level4ActivitiesRepository->copyLevel4ActivityOptionsData(
                            $oldId[$j],$newId[$j],$newanswerImageArray,$newresponseImageArray);

                            $this->log->info('End successfully copy question answer data new option id ->'.$activityOptionData);
                        }

                        $questionData = $this->level4ActivitiesRepository->getQuestionMediaById($oldId[$j]);
                        if (!empty($questionData)) 
                        {
                            $newquestionImageArray = [];
                            $imageInfo = array();
                            $this->log->info('Now start copy question images and video');
                            foreach($questionData as $keyQuestion => $valueQuestion)
                            {
                                $newquestionImage = '';
                                if ($valueQuestion->l4iam_media_type == "I") 
                                {
                                    $this->log->info('Start copy question images image name ->'.$valueQuestion->l4iam_media_name);
                                    $file2 = $this->questionOriginalImageUploadPath.$valueQuestion->l4iam_media_name;
                                    $thumbfile2 = $this->questionThumbImageUploadPath.$valueQuestion->l4iam_media_name;
                                    if (Storage::size($this->questionOriginalImageUploadPath.$valueQuestion->l4iam_media_name) > 0 && $valueQuestion->l4iam_media_name != '') {
                                        $newquestionImage = $oldId[$i].'_'.time().'_'.$valueQuestion->l4iam_media_name;
                                        $newfile2 = $this->questionOriginalImageUploadPath.$newquestionImage;
                                        $newthumbfile2 = $this->questionThumbImageUploadPath.$newquestionImage;
                                        if (!Storage::copy($file2, $newfile2)) {
                                            $this->log->error('Copy original question failed image name ->'.$newquestionImage);
                                            return Redirect::to("admin/copyConcept")->withErrors('')->withInput();
                                            exit;
                                        }
                                        $imageInfo = pathinfo($thumbfile2); 
                                        if($imageInfo['extension'] != 'gif')
                                        {
                                            if (!Storage::copy($thumbfile2, $newthumbfile2)) {
                                                $this->log->error('Copy thumb question failed image name ->'.$newquestionImage);
                                                return Redirect::to("admin/copyConcept")->withErrors('')->withInput();
                                                exit;
                                            }
                                        }
                                    }
                                    $this->log->info('End successfully copy question images image name ->'.$newquestionImage);
                                } else {
                                    $newquestionImage = $valueQuestion->l4iam_media_name;
                                }
                                $newquestionImageArray[$valueQuestion->mediaId] = $newquestionImage;
                            }    
                             $this->log->info('Copy successfully end question images and video');
                            $activityMediaData = $this->level4ActivitiesRepository->copyLevel4ActivityMediaData($oldId[$j],$newId[$j],$newquestionImageArray);
                        }
                    }                   
                }
            }
        }
        //If valid concepts ids then copy the those concepts
        if ($return) {
//            $teenagers = $this->teenagersRepository->getAllActiveTeenagersForNotification();
//            if(isset($conceptNames) && !empty($conceptNames))
//            {
//                foreach($conceptNames as $key=>$conceptname)
//                {
//                    foreach($teenagers AS $key => $value) 
//                    {
//                        $message = 'Role play "' .$conceptname.'" as a "'.$professionName.'" in ProTeen now!';
//                        $return = Helpers::saveAllActiveTeenagerForSendNotifivation($value->id, $message);
//                    } 
//                }
//            }
            
            return Redirect::to("admin/copyConcept")->with('success', 'Concept copied successfully');
        } else {
            return Redirect::to("admin/copyConcept")->withErrors(trans('appmessages.missing_data_msg'))->withInput();
            exit;
        }
    }

    public function addCoinsDataForTemplate() {
        $data = [];
        $data['template_Id'] = $_REQUEST['templateid'];
        $data['searchBy'] = $_REQUEST['searchBy'];
        $data['searchText'] = $_REQUEST['searchText'];
        $data['orderBy'] = $_REQUEST['orderBy'];
        $data['sortOrder'] = $_REQUEST['sortOrder'];
        $data['page'] = $_REQUEST['page'];

        return view('admin.AddCoinsDataForTemplate',compact('data'));
    }

    public function saveCoinsDataForTemplate() {

        $id = e(Input::get('id'));
        $coins = e(Input::get('gt_coins'));
        $searchParamArray = [];
        $searchParamArray['searchBy'] = e(Input::get('searchBy'));
        $searchParamArray['searchText'] = Input::get('searchText');
        $searchParamArray['orderBy'] = e(Input::get('orderBy'));
        $searchParamArray['sortOrder'] = e(Input::get('sortOrder'));
        $page = e(Input::get('page'));
        $postData['pageRank'] = '?page='.$page;

        if (!empty($searchParamArray)) {
            Cache::forever('L4searchArray', $searchParamArray);
        } else {
            Cache::forget('L4searchArray');
        }
        $templateData = $this->level4ActivitiesRepository->getTemplateDataForCoinsDetail($id);
        if (!empty($templateData)) {
            $coins += $templateData['gt_coins'];
        }
        $result = $this->level4ActivitiesRepository->updateTemplateCoinsDetail($id, $coins);

        return Redirect::to("admin/listGamificationTemplate".$postData['pageRank'])->with('success', trans('labels.coinsaddsuccess'));
    }
}