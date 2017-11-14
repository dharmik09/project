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
use App\Services\FileStorage\Contracts\FileStorageRepository;

class Level4IntermediateActivityManagementController extends Controller {

    public function __construct(FileStorageRepository $fileStorageRepository, ProfessionsRepository $professionsRepository, Level4ActivitiesRepository $level4ActivitiesRepository) {
        $this->professionsRepository = $professionsRepository;
        $this->objLevel4Activities = new Level4Activity();
        $this->level4ActivitiesRepository = $level4ActivitiesRepository;
        $this->level4PointsForQuestions = Config::get('constant.LEVEL4_POINTS_FOR_QUESTION');
        $this->level4TimerForQuestions = Config::get('constant.LEVEL4_TIMER_FOR_QUESTION');
        $this->intermediateQuestionOriginalImageUploadPath = Config::get('constant.LEVEL4_INTERMEDIATE_QUESTION_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->intermediateQuestionThumbImageUploadPath = Config::get('constant.LEVEL4_INTERMEDIATE_QUESTION_THUMB_IMAGE_UPLOAD_PATH');
        $this->intermediateQuestionOriginalImageHeight = Config::get('constant.LEVEL4_INTERMEDIATE_QUESTION_THUMB_IMAGE_HEIGHT');
        $this->intermediateQuestionOriginalImageWidth = Config::get('constant.LEVEL4_INTERMEDIATE_QUESTION_THUMB_IMAGE_WIDTH');
        $this->intermediateAnswerOriginalImageUploadPath = Config::get('constant.LEVEL4_INTERMEDIATE_ANSWER_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->intermediateAnswerThumbImageUploadPath = Config::get('constant.LEVEL4_INTERMEDIATE_ANSWER_THUMB_IMAGE_UPLOAD_PATH');
        $this->intermediateAnswerOriginalImageHeight = Config::get('constant.LEVEL4_INTERMEDIATE_ANSWER_THUMB_IMAGE_HEIGHT');
        $this->intermediateAnswerOriginalImageWidth = Config::get('constant.LEVEL4_INTERMEDIATE_ANSWER_THUMB_IMAGE_WIDTH');
        $this->intermediateResponseOriginalImageUploadPath = Config::get('constant.LEVEL4_INTERMEDIATE_RESPONSE_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->intermediateResponseThumbImageUploadPath = Config::get('constant.LEVEL4_INTERMEDIATE_RESPONSE_THUMB_IMAGE_UPLOAD_PATH');
        $this->intermediateResponseOriginalImageHeight = Config::get('constant.LEVEL4_INTERMEDIATE_RESPONSE_THUMB_IMAGE_HEIGHT');
        $this->intermediateResponseOriginalImageWidth = Config::get('constant.LEVEL4_INTERMEDIATE_RESPONSE_THUMB_IMAGE_WIDTH');
        $this->fileStorageRepository = $fileStorageRepository;
    }

    public function index()
    {
        $searchParamArray = Input::all();
        if (isset($searchParamArray['clearSearch'])) {
            unset($searchParamArray);
            Cache::forget('searchArrayLevel4');
            Cache::forget('l4intermediateActivites');
            $searchParamArray = array();
        }
        if (!empty($searchParamArray)) {
            Cache::forget('l4intermediateActivites');
            if (isset($searchParamArray['searchText'])) {
                Cache::forever('searchArrayLevel4', $searchParamArray);
            } else {
                $searchParamArray = Cache::get('searchArrayLevel4');
            }
            $leve4intermediateActivites = $this->level4ActivitiesRepository->getLevel4IntermediateActivities($searchParamArray);
        } else {
            if (Cache::has('searchArrayLevel4')) {
                $searchParamArray = Cache::get('searchArrayLevel4');
            } else {
                Cache::forget('searchArrayLevel4');
            }
            if (Cache::has('l4intermediateActivites')) {
                $leve4intermediateActivites = Cache::get('l4intermediateActivites');
            } else {
                $leve4intermediateActivites = $this->level4ActivitiesRepository->getLevel4IntermediateActivities($searchParamArray);
                Cache::forever('l4intermediateActivites', $leve4intermediateActivites);
            }
        }
        return view('admin.ListLevel4IntermediateActivity',compact('leve4intermediateActivites','searchParamArray'));
    }

    public function add()
    {
        //Get active gamification templates
        $gamificationTemplate = $this->level4ActivitiesRepository->getActiveGamificationTemplate();
        $allActiveProfessions = $this->professionsRepository->getAllActiveProfession();
        //Get last added activity to predefine data
        $lastAddedL4IActivity = $this->level4ActivitiesRepository->getLastAddedIntermediateActivity();
        return view('admin.EditLevel4IntermediateActivity',compact('gamificationTemplate','allActiveProfessions','lastAddedL4IActivity'));
    }

    public function edit($id)
    {
        $intermediateQuestionOriginalImageUploadPath = $this->intermediateQuestionOriginalImageUploadPath;
        $intermediateQuestionThumbImageUploadPath = $this->intermediateQuestionThumbImageUploadPath;

        $gamificationTemplate = $this->level4ActivitiesRepository->getActiveGamificationTemplate();
        $level4IntermediateActivityDetail = $this->level4ActivitiesRepository->getLevel4IntermediateActivityById($id);
        $allActiveProfessions = $this->professionsRepository->getAllActiveProfession();

        return view('admin.EditLevel4IntermediateActivity', compact('level4IntermediateActivityDetail','allActiveProfessions','gamificationTemplate','intermediateQuestionOriginalImageUploadPath','intermediateQuestionThumbImageUploadPath'));
    }

    public function save()
    {
        $allPostdata = Input::All();
        $questionData = array();
        if(isset($allPostdata))
        {
            // Prepare question Data
            $questionData['id'] = (isset($allPostdata['id']) && $allPostdata['id'] != '') ? e($allPostdata['id']) : '';
            $questionData['l4ia_profession_id'] = (isset($allPostdata['question_profession']) && $allPostdata['question_profession'] != '') ? e($allPostdata['question_profession']) : '';
            $questionData['l4ia_question_text'] = (isset($allPostdata['question_text']) && $allPostdata['question_text'] != '') ? ($allPostdata['question_text']) : '';
            $questionData['l4ia_question_time'] = (isset($allPostdata['quetion_time']) && $allPostdata['quetion_time'] != '') ? e($allPostdata['quetion_time']) : 30;
            //$questionData['l4ia_question_point'] = (isset($allPostdata['quetion_point']) && $allPostdata['quetion_point'] != '') ? e($allPostdata['quetion_point']) : 25;
            $questionData['l4ia_question_description'] = (isset($allPostdata['question_description']) && $allPostdata['question_description'] != '') ? $allPostdata['question_description'] : '';
            $questionData['l4ia_question_answer_description'] = (isset($allPostdata['question_answer_description']) && $allPostdata['question_answer_description'] != '') ? $allPostdata['question_answer_description'] : '';
            $questionData['l4ia_question_template'] = (isset($allPostdata['gamification_template']) && $allPostdata['gamification_template'] != '') ? $allPostdata['gamification_template'] : $allPostdata['edit_template_id'];
            $questionData['l4ia_question_right_message'] = (isset($allPostdata['right_question_message']) && $allPostdata['right_question_message'] != '') ? $allPostdata['right_question_message'] : '';
            $questionData['l4ia_question_wrong_message'] = (isset($allPostdata['wrong_question_message']) && $allPostdata['wrong_question_message'] != '') ? $allPostdata['wrong_question_message'] : '';
            $questionData['l4ia_shuffle_options'] = (isset($allPostdata['shuffle_options']) && $allPostdata['shuffle_options'] != '') ? $allPostdata['shuffle_options'] : 0;
            $questionData['l4ia_question_popup_description'] = (isset($allPostdata['l4ia_question_popup_description']) && $allPostdata['l4ia_question_popup_description'] != '') ? $allPostdata['l4ia_question_popup_description'] : '';
            $questionData['deleted'] = $allPostdata['deleted'];
            $matrixDataArr = array();
            $answerMeta = '';
            if(isset($allPostdata['grid_column']) && $allPostdata['grid_column'] != '' && isset($allPostdata['grid_row']) && $allPostdata['grid_row'] != ''){
                $matrixDataArr = array('column'=>isset($allPostdata['grid_column'])?$allPostdata['grid_column']:'','row'=>isset($allPostdata['grid_row'])?$allPostdata['grid_row']:'');
                $answerMeta = serialize($matrixDataArr);
                $questionData['l4ia_options_metrix'] = $answerMeta;
            }

            $point = e(input::get('hidden_points'));
            if(isset($allPostdata['quetion_point']) && $allPostdata['quetion_point'] != '')
            {
                $questionData['l4ia_question_point'] = $allPostdata['quetion_point'];
            }
            else
            {
                $questionData['l4ia_question_point'] = $point;
            }
            //Add question popup image
            $popUpfile = Input::file('question_popup_image');
            if(!empty($popUpfile))
            {
                //Check image valid extension
                $validationPass = Helpers::checkValidImageExtension($popUpfile);
                if($validationPass)
                {
                    $fileName = 'questionPop_' . time() . '.' . $popUpfile->getClientOriginalExtension();
                    if ($popUpfile->getClientOriginalExtension() == 'gif') {
                        copy($popUpfile->getRealPath(), $this->intermediateQuestionOriginalImageUploadPath.$fileName);
                        //Upload on AWS
                        $gifPathOriginal = public_path($this->intermediateQuestionOriginalImageUploadPath . $fileName);
                        $uploadGifImage = $this->fileStorageRepository->addFileToStorage($fileName, $this->intermediateQuestionOriginalImageUploadPath, $gifPathOriginal, "s3");
                        //Deleting Local Files
                        \File::delete($this->intermediateQuestionOriginalImageUploadPath . $fileName);
                    } else {
                        $pathOriginal = public_path($this->intermediateQuestionOriginalImageUploadPath . $fileName);
                        $pathThumb = public_path($this->intermediateQuestionThumbImageUploadPath . $fileName);
                        Image::make($popUpfile->getRealPath())->save($pathOriginal);
                        Image::make($popUpfile->getRealPath())->resize($this->intermediateQuestionOriginalImageWidth, $this->intermediateQuestionOriginalImageHeight)->save($pathThumb);

                        //Uploading on AWS
                        $originalImage = $this->fileStorageRepository->addFileToStorage($fileName, $this->intermediateQuestionOriginalImageUploadPath, $pathOriginal, "s3");
                        $thumbImage = $this->fileStorageRepository->addFileToStorage($fileName, $this->intermediateQuestionThumbImageUploadPath, $pathThumb, "s3");
                        //Deleting Local Files
                        \File::delete($this->intermediateQuestionOriginalImageUploadPath . $fileName);
                        \File::delete($this->intermediateQuestionThumbImageUploadPath . $fileName);
                    }
                    $questionData['l4ia_question_popup_image'] = $fileName;
                }
            }
            //Add question audio file
            $audiofile = Input::file('question_audio');
            if(!empty($audiofile))
            {
                $validImageExtArr = array('mov', 'mp3', 'wav');
                $ext = $audiofile->getClientOriginalExtension();
                if (in_array($ext, $validImageExtArr)) {
                    $fileName = 'audio_' . time() . '.' . $audiofile->getClientOriginalExtension();
                    Input::file('question_audio')->move($this->intermediateQuestionOriginalImageUploadPath, $fileName); // uploading file to given path
                    $audioPath = public_path($this->intermediateQuestionOriginalImageUploadPath . $fileName);
                    //Uploading on AWS
                    $originalFile = $this->fileStorageRepository->addFileToStorage($fileName, $this->intermediateQuestionOriginalImageUploadPath, $audioPath, "s3");
                    //Deleting Local Files
                    \File::delete($this->intermediateQuestionOriginalImageUploadPath . $fileName);
                    $questionData['l4ia_question_audio'] = $fileName;
                }
            }
            $lastInsertId = $this->level4ActivitiesRepository->saveLevel4IntermediateActivity($questionData);
            //Handle Question Media save...Image or Video
            $files = Input::file('question_image');
            if (isset($files) && !empty($files))
            {
                $questionMedia = array();
                foreach($files as $key=>$file)
                {
                    if(!empty($file))
                    {
                        $originalName =  $file->getClientOriginalName();
                        $new_filename = substr($originalName, 0, strrpos($originalName, "."));
                        $fileName = time().'_'.str_random(10). '.' .$file->getClientOriginalExtension();
                        if ($file->getClientOriginalExtension() == 'gif') {
                            copy($file->getRealPath(), $this->intermediateQuestionOriginalImageUploadPath.$fileName);
                            $imagePath = public_path($this->intermediateQuestionOriginalImageUploadPath . $fileName);
                            //Uploading on AWS
                            $originalFile = $this->fileStorageRepository->addFileToStorage($fileName, $this->intermediateQuestionOriginalImageUploadPath, $imagePath, "s3");
                            //Deleting Local Files
                            \File::delete($this->intermediateQuestionOriginalImageUploadPath . $fileName);
                        } else {
                            $pathOriginal = public_path($this->intermediateQuestionOriginalImageUploadPath . $fileName);
                            $pathThumb = public_path($this->intermediateQuestionThumbImageUploadPath . $fileName);
                            Image::make($file->getRealPath())->save($pathOriginal);
                            Image::make($file->getRealPath())->resize($this->intermediateQuestionOriginalImageWidth, $this->intermediateQuestionOriginalImageHeight)->save($pathThumb);

                            //Uploading on AWS
                            $originalImage = $this->fileStorageRepository->addFileToStorage($fileName, $this->intermediateQuestionOriginalImageUploadPath, $pathOriginal, "s3");
                            $thumbImage = $this->fileStorageRepository->addFileToStorage($fileName, $this->intermediateQuestionThumbImageUploadPath, $pathThumb, "s3");
                            //Deleting Local Files
                            \File::delete($this->intermediateQuestionOriginalImageUploadPath . $fileName);
                            \File::delete($this->intermediateQuestionThumbImageUploadPath . $fileName);
                        }
                        $questionMedia['id'] = 0;
                        $questionMedia['l4iam_question_id'] = $lastInsertId;
                        $questionMedia['l4iam_media_name'] = $fileName;
                        $questionMedia['l4iam_media_type'] = 'I';
                        $questionMedia['l4iam_media_desc'] = isset($allPostdata['question_image_description'][$key])?$allPostdata['question_image_description'][$key]:'';
                        $this->level4ActivitiesRepository->saveLevel4IntermediateActivityMedia($questionMedia);
                    }
                }
            }
            if(isset($allPostdata['question_video']) && $allPostdata['question_video'] != '')
            {
                $questionMedia['id'] = 0;
                $questionMedia['l4iam_question_id'] = $lastInsertId;
                $questionMedia['l4iam_media_name'] = $allPostdata['question_video'];
                $questionMedia['l4iam_media_type'] = 'V';
                $this->level4ActivitiesRepository->saveLevel4IntermediateActivityMedia($questionMedia);
            }
            //Now its time to handle question options
            $questionOptionData = array();
            if(isset($allPostdata['edit_template_id']) && $allPostdata['edit_template_id'] > 0){
                $questionTemplate = $allPostdata['edit_template_id'];
            }else{
                $questionTemplate = $allPostdata['gamification_template'];
            }
            $questionTemplateAnsType = $allPostdata['questionTemplateAnsType'];
            $questionOptionData['id'] = 0;
            $questionOptionData['l4iao_question_id'] = $lastInsertId;
            if(isset($allPostdata['edit_template_id']) && $allPostdata['edit_template_id'] == 0){
                switch ($questionTemplateAnsType) {
                    case "filling_blank":
                        if(isset($allPostdata['answer_option_text']) && !empty($allPostdata['answer_option_text'])){
                            foreach($allPostdata['answer_option_text'] as $key => $data){
                                if($data != ''){
                                    $questionOptionData['answer_option_text'] = $data;
                                    $questionOptionData['correct_answer'] = (isset($allPostdata['correct_answer'][$key]))? 1 : 0 ;
                                    $finalQuestionData = array('id'=>$questionOptionData['id'],'l4iao_question_id'=>$lastInsertId,'l4iao_correct_answer'=>$questionOptionData['correct_answer'],'l4iao_answer_text'=>$questionOptionData['answer_option_text'] );
                                    $this->level4ActivitiesRepository->saveLevel4IntermediateActivityOptions($finalQuestionData);
                                }
                            }
                        }
                        break;
                    case "option_choice":
                    case "true_false":
                    case "image_reorder":
                    case "group_selection":
                    case "option_reorder":
                        $files = Input::file('answer_option_image');
                        if (isset($files) && !empty($files))
                        {
                            $key=0;
                            foreach($files as $file)
                            {
                                if((isset($allPostdata['answer_option_text'][$key]) && $allPostdata['answer_option_text'][$key] != '') || !empty($file))
                                {
                                    $fileName = '';
                                    if(!empty($file))
                                    {
                                        $originalName =  $file->getClientOriginalName();
                                        $new_filename = substr($originalName, 0, strrpos($originalName, "."));
                                        $fileName = time().'_'.str_random(10). '.' .$file->getClientOriginalExtension();
                                        if ($file->getClientOriginalExtension() == 'gif') {
                                            copy($file->getRealPath(), $this->intermediateAnswerOriginalImageUploadPath.$fileName);
                                            $gifImagePath = public_path($this->intermediateAnswerOriginalImageUploadPath.$fileName);
                                            //Uploading on AWS
                                            $originalFile = $this->fileStorageRepository->addFileToStorage($fileName, $this->intermediateAnswerOriginalImageUploadPath, $gifImagePath, "s3");
                                            //Deleting Local Files
                                            \File::delete($this->intermediateAnswerOriginalImageUploadPath . $fileName);
                                        }else{
                                            $pathOriginal = public_path($this->intermediateAnswerOriginalImageUploadPath . $fileName);
                                            $pathThumb = public_path($this->intermediateAnswerThumbImageUploadPath . $fileName);
                                            Image::make($file->getRealPath())->save($pathOriginal);
                                            Image::make($file->getRealPath())->resize($this->intermediateAnswerOriginalImageWidth, $this->intermediateAnswerOriginalImageHeight)->save($pathThumb);

                                            //Uploading on AWS
                                            $originalImage = $this->fileStorageRepository->addFileToStorage($fileName, $this->intermediateAnswerOriginalImageUploadPath, $pathOriginal, "s3");
                                            $thumbImage = $this->fileStorageRepository->addFileToStorage($fileName, $this->intermediateAnswerThumbImageUploadPath, $pathThumb, "s3");
                                            //Deleting Local Files
                                            \File::delete($this->intermediateAnswerOriginalImageUploadPath . $fileName);
                                            \File::delete($this->intermediateAnswerThumbImageUploadPath . $fileName);
                                        }
                                    }
                                    $questionOptionData['l4iao_answer_image'] = $fileName;
                                    $questionOptionData['l4iao_answer_text'] = isset($allPostdata['answer_option_text'][$key])?$allPostdata['answer_option_text'][$key]:'';
                                    $questionOptionData['l4iao_correct_answer'] = isset($allPostdata['correct_answer'][$key])?$allPostdata['correct_answer'][$key]:0;
                                    $questionOptionData['l4iao_answer_order'] = isset($allPostdata['answer_order'][$key])?$allPostdata['answer_order'][$key]:0;
                                    $questionOptionData['l4iao_answer_group'] = isset($allPostdata['answer_group'][$key])?$allPostdata['answer_group'][$key]:0;
                                    $questionOptionData['l4iao_answer_image_description'] = isset($allPostdata['answer_image_description'][$key])?$allPostdata['answer_image_description'][$key]:'';
                                    $finalQuestionData = array('id'=>$questionOptionData['id'],'l4iao_question_id'=>$lastInsertId,'l4iao_answer_text'=>$questionOptionData['l4iao_answer_text'],'l4iao_answer_image'=>$questionOptionData['l4iao_answer_image'],'l4iao_answer_image_description'=>$questionOptionData['l4iao_answer_image_description'],'l4iao_correct_answer'=>$questionOptionData['l4iao_correct_answer'],'l4iao_answer_order'=>$questionOptionData['l4iao_answer_order'],'l4iao_answer_group'=>$questionOptionData['l4iao_answer_group']);
                                    $this->level4ActivitiesRepository->saveLevel4IntermediateActivityOptions($finalQuestionData);
                                    $key++;
                                }
                            }
                        }
                        break;
                    case "option_reorder":
                        if(isset($allPostdata['answer_option_text']) && !empty($allPostdata['answer_option_text'])){
                            foreach($allPostdata['answer_option_text'] as $key => $data){
                                if($data != ''){
                                    $questionOptionData['answer_option_text'] = $data;
                                    $questionOptionData['answer_order'] = (isset($allPostdata['answer_order'][$key]))? $allPostdata['answer_order'][$key] : 0 ;
                                    $finalQuestionData = array('id'=>$questionOptionData['id'],'l4iao_question_id'=>$lastInsertId,'l4iao_answer_order'=>$questionOptionData['answer_order'],'l4iao_answer_text'=>$questionOptionData['answer_option_text'] );
                                    $this->level4ActivitiesRepository->saveLevel4IntermediateActivityOptions($finalQuestionData);
                                }
                            }
                        }
                        break;
                    case "single_line_answer":
                        if(isset($allPostdata['correct_answer']) && !empty($allPostdata['correct_answer'])){
                            foreach($allPostdata['correct_answer'] as $key=>$data){
                                if($data != ''){
                                    $questionOptionData['correct_answer'] = $data;
                                    $finalQuestionData = array('id'=>$questionOptionData['id'],'l4iao_question_id'=>$lastInsertId,'l4iao_correct_answer'=>$questionOptionData['correct_answer']);
                                    $this->level4ActivitiesRepository->saveLevel4IntermediateActivityOptions($finalQuestionData);
                                }
                            }
                        }
                        break;
                    case "option_choice_with_response":
                        $answerImages = Input::file('answer_option_image');

                        $answerResponseImages = Input::file('answer_response_image');
                        if (isset($answerImages) && !empty($answerImages))
                        {
                            //$key=0;
                            foreach($answerImages as $key=>$file)
                            {
                                if((isset($allPostdata['answer_option_text'][$key]) && $allPostdata['answer_option_text'][$key] != '') || !empty($file))
                                {
                                    $fileName = '';
                                    $responsefileName = '';
                                    if(!empty($file))
                                    {
                                        $originalName =  $file->getClientOriginalName();
                                        $new_filename = substr($originalName, 0, strrpos($originalName, "."));
                                        $fileName = time().'_'.str_random(10). '.' .$file->getClientOriginalExtension();
                                        $pathOriginal = public_path($this->intermediateAnswerOriginalImageUploadPath . $fileName);
                                        $pathThumb = public_path($this->intermediateAnswerThumbImageUploadPath . $fileName);
                                        
                                        Image::make($file->getRealPath())->save($pathOriginal);
                                        Image::make($file->getRealPath())->resize($this->intermediateAnswerOriginalImageWidth, $this->intermediateAnswerOriginalImageHeight)->save($pathThumb);

                                        //Uploading on AWS
                                        $originalImageUpload = $this->fileStorageRepository->addFileToStorage($fileName, $this->intermediateAnswerOriginalImageUploadPath, $pathOriginal, "s3");
                                        $thumbImageUpload = $this->fileStorageRepository->addFileToStorage($fileName, $this->intermediateAnswerThumbImageUploadPath, $pathThumb, "s3");
                                        //Deleting Local Files
                                        \File::delete($this->intermediateAnswerOriginalImageUploadPath . $fileName);
                                        \File::delete($this->intermediateAnswerThumbImageUploadPath . $fileName);

                                        //Response Image
                                        if(!empty($answerResponseImages[$key])){
                                            $responseOriginalName = $answerResponseImages[$key]->getClientOriginalName();
                                            $new_response_filename = substr($responseOriginalName, 0, strrpos($responseOriginalName, "."));
                                            $responsefileName = time().'_'.str_random(10). '.' .$answerResponseImages[$key]->getClientOriginalExtension();
                                            $pathOriginal = public_path($this->intermediateResponseOriginalImageUploadPath . $responsefileName);
                                            $pathThumb = public_path($this->intermediateResponseThumbImageUploadPath . $responsefileName);
                                            Image::make($answerResponseImages[$key]->getRealPath())->save($pathOriginal);
                                            Image::make($answerResponseImages[$key]->getRealPath())->resize($this->intermediateResponseOriginalImageWidth, $this->intermediateResponseOriginalImageHeight)->save($pathThumb);

                                            //Uploading on AWS
                                            $originalImageData = $this->fileStorageRepository->addFileToStorage($responsefileName, $this->intermediateResponseOriginalImageUploadPath, $pathOriginal, "s3");
                                            $thumbImageData = $this->fileStorageRepository->addFileToStorage($responsefileName, $this->intermediateResponseThumbImageUploadPath, $pathThumb, "s3");
                                            //Deleting Local Files
                                            \File::delete($this->intermediateResponseOriginalImageUploadPath . $responsefileName);
                                            \File::delete($this->intermediateResponseThumbImageUploadPath . $responsefileName);
                                        }
                                    }
                                    $questionOptionData['l4iao_answer_image'] = $fileName;
                                    $questionOptionData['l4iao_answer_response_image'] = $responsefileName;
                                    $questionOptionData['l4iao_answer_response_text'] = isset($allPostdata['answer_response_image_description'][$key])?$allPostdata['answer_response_image_description'][$key]:'';
                                    $questionOptionData['l4iao_answer_text'] = isset($allPostdata['answer_option_text'][$key])?$allPostdata['answer_option_text'][$key]:'';
                                    $questionOptionData['l4iao_correct_answer'] = isset($allPostdata['correct_answer'][$key])?$allPostdata['correct_answer'][$key]:0;
                                    $questionOptionData['l4iao_answer_order'] = isset($allPostdata['answer_order'][$key])?$allPostdata['answer_order'][$key]:0;
                                    $questionOptionData['l4iao_answer_group'] = isset($allPostdata['answer_group'][$key])?$allPostdata['answer_group'][$key]:0;
                                    $questionOptionData['l4iao_answer_image_description'] = isset($allPostdata['answer_image_description'][$key])?$allPostdata['answer_image_description'][$key]:'';
                                    $finalQuestionData = array('id'=>$questionOptionData['id'],'l4iao_question_id'=>$lastInsertId,'l4iao_answer_text'=>$questionOptionData['l4iao_answer_text'],'l4iao_answer_image'=>$questionOptionData['l4iao_answer_image'],'l4iao_answer_image_description'=>$questionOptionData['l4iao_answer_image_description'],'l4iao_correct_answer'=>$questionOptionData['l4iao_correct_answer'],'l4iao_answer_order'=>$questionOptionData['l4iao_answer_order'],'l4iao_answer_group'=>$questionOptionData['l4iao_answer_group'],'l4iao_answer_response_text'=>$questionOptionData['l4iao_answer_response_text'],'l4iao_answer_response_image'=>$questionOptionData['l4iao_answer_response_image']);
                                    $this->level4ActivitiesRepository->saveLevel4IntermediateActivityOptions($finalQuestionData);
                                }
                                //$key++;
                            }
                        }
                        break;
                    case "select_from_dropdown_option":
                        if(isset($allPostdata['answer_option_text']) && !empty($allPostdata['answer_option_text'])){
                            foreach($allPostdata['answer_option_text'] as $key=>$data){
                                if($data != '')
                                {
                                    $questionOptionData['l4iao_answer_text'] = isset($allPostdata['answer_option_text'][$key])?$allPostdata['answer_option_text'][$key]:'';
                                    $questionOptionData['l4iao_correct_answer'] = isset($allPostdata['correct_answer'][$key])?$allPostdata['correct_answer'][$key]:'0';
                                    $questionOptionData['l4iao_answer_order'] = isset($allPostdata['answer_order'][$key])?$allPostdata['answer_order'][$key]:'';
                                    $finalQuestionData = array('id'=>$questionOptionData['id'],'l4iao_question_id'=>$lastInsertId,'l4iao_answer_text'=>$questionOptionData['l4iao_answer_text'],'l4iao_correct_answer'=>$questionOptionData['l4iao_correct_answer'],'l4iao_answer_order'=>$questionOptionData['l4iao_answer_order']);
                                    $this->level4ActivitiesRepository->saveLevel4IntermediateActivityOptions($finalQuestionData);
                                }
                            }
                        }
                        break;
                    default:
                        echo "Something went wrong...";
                }
            }
            //if($lastInsertId)
            //{
              $pageRank = (isset($allPostdata['pageRank']) && $allPostdata['pageRank'] != '')? $allPostdata['pageRank'] : '';
              Cache::forget('l4intermediateActivites');
              return Redirect::to("admin/listLevel4IntermediateActivity".$pageRank)->with('success', trans('labels.level4activityupdatesuccess'));
            //}
        }
    }

    public function delete($id)
    {
        $return = $this->level4ActivitiesRepository->deleteLevel4IntermediateActivity($id);
        if($return)
        {
            return Redirect::to("admin/listLevel4IntermediateActivity")->with('success', trans('labels.level4activitydeletesuccess'));
        }
        else
        {
            return Redirect::to("admin/listLevel4IntermediateActivity")->with('error', trans('labels.commonerrormessage'));
        }
    }
    /*
     * Get Media by question id
     */
    public function manageActivityMedia($activityid)
    {        
        //get media of questions        
        $questionOriginalImagePath =  $this->intermediateQuestionOriginalImageUploadPath;
        $questionThumbImagePath = $this->intermediateQuestionThumbImageUploadPath;
        $level4IntermediateActivityDetail = $this->level4ActivitiesRepository->getLevel4IntermediateActivityById($activityid);                
        $questionMedia =  $this->level4ActivitiesRepository->getIntermediateActivityMediaByQuestionId($activityid);
        $lastMedia = $this->level4ActivitiesRepository->getIntermediateActivityMediaLastId();
        if(!empty($lastMedia)){
            $lastMediaId = $lastMedia->id+1;
        }else{
            $lastMediaId = 1;
        }        
        return view('admin.Level4IntermediateActivityMedia',compact('lastMediaId','questionMedia','questionOriginalImagePath','questionThumbImagePath','level4IntermediateActivityDetail'));                  
    }
    
    /*
     * Delete media by media id
     */
    public function deleteLevel4IntermediateMediaById()
    {
        $postData = Input::all();
        if(isset($postData) && !empty($postData))
        {
            $id = $postData['media_id'];
            $result = $this->level4ActivitiesRepository->deleteLeve4IntermediateMedia($id);
            if($result){
                if($postData['media_type'] == 'I'){
                    unlink($this->intermediateQuestionOriginalImageUploadPath.$postData['media_name']);
                    unlink($this->intermediateQuestionThumbImageUploadPath.$postData['media_name']);
                }
            }
        }
    }

    /*
     * Save level4 intermediate activity media
     */
    public function savelevel4IntermediateMedia()
    {
        //Handle Question Media save...Image or Video            
        $files = Input::file('question_image');
        $allPostdata = Input::All();        
        if (isset($files) && !empty($files)) 
        { 
            $questionMedia = array();
            foreach($files as $key=>$file)
            {                   
                $fileName = '';
                if(!empty($file))
                {
                    $originalName =  $file->getClientOriginalName();

                    $new_filename = substr($originalName, 0, strrpos($originalName, "."));                                
                    $fileName = time().'_'.str_random(10). '.' . $file->getClientOriginalExtension();
                    if ($file->getClientOriginalExtension() == 'gif') {
                        copy($file->getRealPath(), $this->intermediateQuestionOriginalImageUploadPath.$fileName);
                    }else{
                    $pathOriginal = public_path($this->intermediateQuestionOriginalImageUploadPath . $fileName);
                    $pathThumb = public_path($this->intermediateQuestionThumbImageUploadPath . $fileName);

                    Image::make($file->getRealPath())->save($pathOriginal);
                    Image::make($file->getRealPath())->resize($this->intermediateQuestionOriginalImageWidth, $this->intermediateQuestionOriginalImageHeight)->save($pathThumb);
                    }
                    $questionMedia['l4iam_media_name'] = $fileName;
                    $questionMedia['l4iam_media_type'] = 'I';
                    $questionMedia['l4iam_media_desc'] = isset($allPostdata['question_image_description'][$key])?$allPostdata['question_image_description'][$key]:'';                    
                }   
                else
                {
                    $questionMedia['l4iam_media_desc'] = isset($allPostdata['question_image_description'][$key])?$allPostdata['question_image_description'][$key]:'';                    
                }
                $questionMedia['l4iam_question_id'] = $allPostdata['question_id'];
                
                //Check if record exist for key
                $recordExist = $this->level4ActivitiesRepository->getIntermediateActivityMediaByMediaId($key);
                if(isset($recordExist) && !empty($recordExist)){
                    $this->level4ActivitiesRepository->updateLevel4IntermediateActivityMedia($questionMedia,$key);
                }
                else{     
                    if($fileName != ''){
                        //$questionMedia['id'] = 0;
                        $this->level4ActivitiesRepository->saveLevel4IntermediateActivityMedia($questionMedia);   
                    }
                }
            }    
                
        }
        
        //update youtube video
        if(isset($allPostdata['question_video']) && !empty($allPostdata['question_video']))
        {
            foreach($allPostdata['question_video'] as $key=>$val)
            {
                $questionMedia['l4iam_question_id'] = $allPostdata['question_id'];
                $questionMedia['l4iam_media_name'] = $val;
                $questionMedia['l4iam_media_type'] = 'V';
                $this->level4ActivitiesRepository->updateLevel4IntermediateActivityMedia($questionMedia,$key);
            }
        }
        
        return Redirect::to("admin/editlevel4IntermediateActivity/".$allPostdata['question_id'])->with('success', 'Media has been updated successfully');
    }
    
    public function manageIntermediateActivityAnswer($id)
    {
        $level4IntermediateActivityDetail = $this->level4ActivitiesRepository->getLevel4IntermediateActivityById($id);    
        
        $intermediateAnswerOriginalImageUploadPath = $this->intermediateAnswerOriginalImageUploadPath;
        $intermediateAnswerThumbImageUploadPath = $this->intermediateAnswerThumbImageUploadPath;
        
        $intermediateResponseOriginalImageUploadPath = $this->intermediateResponseOriginalImageUploadPath;
        $intermediateResponseThumbImageUploadPath = $this->intermediateResponseThumbImageUploadPath;
        
        $level4IntermediateActivityAnswerDetail = $this->level4ActivitiesRepository->getIntermediateActivityAnswerByQuestionId($id);
        
        return view('admin.Level4IntermediateActivityAnswer',compact('level4IntermediateActivityAnswerDetail','level4IntermediateActivityDetail','intermediateAnswerOriginalImageUploadPath','level4IntermediateActivityAnswerDetail','intermediateResponseOriginalImageUploadPath'));                  
    }
    
    //Handle the update answer for each template
    public function updatelevel4IntermediateOption()
    {
        $allPostdata = Input::All();
        $questionTemplateAnsType = $allPostdata['questionTemplateAnsType'];
        
        //Update shuffle option
        $questionData = array();
        $questionData['id'] = (isset($allPostdata['question_id']) && $allPostdata['question_id'] != '') ? e($allPostdata['question_id']) : 0;
        $questionData['l4ia_shuffle_options'] = (isset($allPostdata['shuffle_options']) && $allPostdata['shuffle_options'] != '') ? $allPostdata['shuffle_options'] : 0;
        $lastInsertId = $this->level4ActivitiesRepository->saveLevel4IntermediateActivity($questionData);        
        switch ($questionTemplateAnsType) {
            case "option_choice":
            case "true_false":    
            case "image_reorder":  
            case "group_selection":
            case "filling_blank":   
            case "option_reorder":                   
            $files = Input::file('answer_option_image');
            
            if (isset($files) && !empty($files)) 
            {                      
                foreach($files as $key=>$file)
                {                       
                    if(!empty($allPostdata['answer_option_text'][$key]) || !empty($file) || $allPostdata['edit_answer_image'][$key] != '')
                    {
                        $fileName = isset($allPostdata['edit_answer_image'][$key])?$allPostdata['edit_answer_image'][$key]:'';
                        if(!empty($file))
                        {                
                            $originalName =  $file->getClientOriginalName();
                            $new_filename = substr($originalName, 0, strrpos($originalName, "."));                                
                            $fileName = time().'_'.str_random(10). '.' .$file->getClientOriginalExtension();
                            if ($file->getClientOriginalExtension() == 'gif') {
                                copy($file->getRealPath(), $this->intermediateAnswerOriginalImageUploadPath.$fileName);
                            }else{
                                $pathOriginal = public_path($this->intermediateAnswerOriginalImageUploadPath . $fileName);
                                $pathThumb = public_path($this->intermediateAnswerThumbImageUploadPath . $fileName);
                                Image::make($file->getRealPath())->save($pathOriginal);
                                Image::make($file->getRealPath())->resize($this->intermediateAnswerOriginalImageWidth, $this->intermediateAnswerOriginalImageHeight)->save($pathThumb);                                 
                            }
                        }
                        $questionOptionData['l4iao_answer_image'] = $fileName;                            
                        $questionOptionData['l4iao_answer_text'] = isset($allPostdata['answer_option_text'][$key])?$allPostdata['answer_option_text'][$key]:'';    
                        $questionOptionData['l4iao_correct_answer'] = isset($allPostdata['correct_answer'][$key])?$allPostdata['correct_answer'][$key]:0; 
                        $questionOptionData['l4iao_answer_order'] = isset($allPostdata['answer_order'][$key])?$allPostdata['answer_order'][$key]:0;   
                        $questionOptionData['l4iao_answer_group'] = isset($allPostdata['answer_group'][$key])?$allPostdata['answer_group'][$key]:0;   
                        $questionOptionData['l4iao_answer_image_description'] = isset($allPostdata['answer_image_description'][$key])?$allPostdata['answer_image_description'][$key]:'';   
                        $finalQuestionData = array('id'=>$key,'l4iao_question_id'=>$allPostdata['question_id'],'l4iao_answer_text'=>$questionOptionData['l4iao_answer_text'],'l4iao_answer_image'=>$questionOptionData['l4iao_answer_image'],'l4iao_answer_image_description'=>$questionOptionData['l4iao_answer_image_description'],'l4iao_correct_answer'=>$questionOptionData['l4iao_correct_answer'],'l4iao_answer_order'=>$questionOptionData['l4iao_answer_order'],'l4iao_answer_group'=>$questionOptionData['l4iao_answer_group']);
                        $this->level4ActivitiesRepository->updateLevel4IntermediateActivityOptions($finalQuestionData,$key);                                                                    
                    }
                }
            }
            break;
            case "option_reorder":
            if(isset($allPostdata['answer_option_text']) && !empty($allPostdata['answer_option_text'])){
                foreach($allPostdata['answer_option_text'] as $key => $data){
                    if($data != ''){
                        $questionOptionData['answer_option_text'] = $data;
                        $questionOptionData['answer_order'] = (isset($allPostdata['answer_order'][$key]))? $allPostdata['answer_order'][$key] : 0 ;
                        $finalQuestionData = array('id'=>$key,'l4iao_question_id'=>$allPostdata['question_id'],'l4iao_answer_order'=>$questionOptionData['answer_order'],'l4iao_answer_text'=>$questionOptionData['answer_option_text'] );
                        $this->level4ActivitiesRepository->updateLevel4IntermediateActivityOptions($finalQuestionData,$key);
                    }
                }
            }
            break;
            case "single_line_answer":                    
            if(isset($allPostdata['correct_answer']) && !empty($allPostdata['correct_answer'])){
                foreach($allPostdata['correct_answer'] as $key=>$data){
                    if($data != ''){
                        $questionOptionData['correct_answer'] = $data;                            
                        $finalQuestionData = array('id'=>$key,'l4iao_question_id'=>$allPostdata['question_id'],'l4iao_correct_answer'=>$questionOptionData['correct_answer']);                            
                        $this->level4ActivitiesRepository->updateLevel4IntermediateActivityOptions($finalQuestionData,$key); 
                    }
                }
            }                    
            break;
            case "select_from_dropdown_option":    
            if(isset($allPostdata['answer_option_text']) && !empty($allPostdata['answer_option_text'])){
                foreach($allPostdata['answer_option_text'] as $key=>$data){
                    if($data != '')
                    {
                        $questionOptionData['l4iao_answer_text'] = isset($allPostdata['answer_option_text'][$key])?$allPostdata['answer_option_text'][$key]:'';    
                        $questionOptionData['l4iao_correct_answer'] = isset($allPostdata['correct_answer'][$key])?$allPostdata['correct_answer'][$key]:'0'; 
                        $questionOptionData['l4iao_answer_order'] = isset($allPostdata['answer_order'][$key])?$allPostdata['answer_order'][$key]:'';   
                        $finalQuestionData = array('id'=>$key,'l4iao_question_id'=>$allPostdata['question_id'],'l4iao_answer_text'=>$questionOptionData['l4iao_answer_text'],'l4iao_correct_answer'=>$questionOptionData['l4iao_correct_answer'],'l4iao_answer_order'=>$questionOptionData['l4iao_answer_order']);
                        $this->level4ActivitiesRepository->updateLevel4IntermediateActivityOptions($finalQuestionData,$key);                                                                    
                    }
                }
            }
            break;
            case "option_choice_with_response":
            $answerImages = Input::file('answer_option_image');
            $answerResponseImages = Input::file('answer_response_image');            
            if(isset($answerImages) && !empty($answerImages)) 
            {                             
                //$key=0;                
                foreach($answerImages as $key=>$file)
                {       
                    $fileName = isset($allPostdata['edit_answer_image'][$key])?$allPostdata['edit_answer_image'][$key]:'';
                    //$responsefileName = isset($allPostdata['edit_response_image'][$key])?$allPostdata['edit_response_image'][$key]:'';
                    if(!empty($file))
                    {                              
                        $originalName =  $file->getClientOriginalName();
                        $new_filename = substr($originalName, 0, strrpos($originalName, "."));                                
                        $fileName = time().'_'.str_random(10). '.' .$file->getClientOriginalExtension();
                        $pathOriginal = public_path($this->intermediateAnswerOriginalImageUploadPath . $fileName);
                        $pathThumb = public_path($this->intermediateAnswerThumbImageUploadPath . $fileName);
                        Image::make($file->getRealPath())->save($pathOriginal);
                        Image::make($file->getRealPath())->resize($this->intermediateAnswerOriginalImageWidth, $this->intermediateAnswerOriginalImageHeight)->save($pathThumb);                                 

                    } 
                    $questionOptionData['l4iao_answer_image'] = $fileName;  
                    //$questionOptionData['l4iao_answer_response_image'] = $responsefileName; 
                    $questionOptionData['l4iao_answer_response_text'] = isset($allPostdata['answer_response_image_description'][$key])?$allPostdata['answer_response_image_description'][$key]:'';    
                    $questionOptionData['l4iao_answer_text'] = isset($allPostdata['answer_option_text'][$key])?$allPostdata['answer_option_text'][$key]:'';    
                    $questionOptionData['l4iao_correct_answer'] = isset($allPostdata['correct_answer'][$key])?$allPostdata['correct_answer'][$key]:0; 
                    $questionOptionData['l4iao_answer_order'] = isset($allPostdata['answer_order'][$key])?$allPostdata['answer_order'][$key]:0;   
                    $questionOptionData['l4iao_answer_group'] = isset($allPostdata['answer_group'][$key])?$allPostdata['answer_group'][$key]:0;   
                    $questionOptionData['l4iao_answer_image_description'] = isset($allPostdata['answer_image_description'][$key])?$allPostdata['answer_image_description'][$key]:'';   
                    $finalQuestionData = array('id'=>$key,'l4iao_question_id'=>$allPostdata['question_id'],'l4iao_answer_text'=>$questionOptionData['l4iao_answer_text'],'l4iao_answer_image'=>$questionOptionData['l4iao_answer_image'],'l4iao_answer_image_description'=>$questionOptionData['l4iao_answer_image_description'],'l4iao_correct_answer'=>$questionOptionData['l4iao_correct_answer'],'l4iao_answer_order'=>$questionOptionData['l4iao_answer_order'],'l4iao_answer_group'=>$questionOptionData['l4iao_answer_group'],'l4iao_answer_response_text'=>$questionOptionData['l4iao_answer_response_text']);
                    $this->level4ActivitiesRepository->updateLevel4IntermediateActivityOptions($finalQuestionData,$key);   
                    
                    //$key++;
                }
            }
            if(isset($answerResponseImages) && !empty($answerResponseImages)){
                foreach($answerResponseImages as $key2 => $file2)
                {       
                    //$fileName = isset($allPostdata['edit_answer_image'][$key])?$allPostdata['edit_answer_image'][$key]:'';
                    $responsefileName = isset($allPostdata['edit_response_image'][$key2])?$allPostdata['edit_response_image'][$key2]:'';
                    if(!empty($file2))
                    {                              
                        $responseOriginalName = $file2->getClientOriginalName();
                        $new_response_filename = substr($responseOriginalName, 0, strrpos($responseOriginalName, ".")); 
                        $responsefileName = time().'_'.str_random(10). '.' .$file2->getClientOriginalExtension();
                        $pathOriginal = public_path($this->intermediateResponseOriginalImageUploadPath . $responsefileName);
                        $pathThumb = public_path($this->intermediateResponseThumbImageUploadPath . $responsefileName);
                        Image::make($file2->getRealPath())->save($pathOriginal);
                        Image::make($file2->getRealPath())->resize($this->intermediateResponseOriginalImageWidth, $this->intermediateResponseOriginalImageHeight)->save($pathThumb);                                        
                    } 
                    //$questionOptionData['l4iao_answer_image'] = $fileName;  
                    $questionOptionData['l4iao_answer_response_image'] = $responsefileName; 
                    $questionOptionData['l4iao_answer_response_text'] = isset($allPostdata['answer_response_image_description'][$key2])?$allPostdata['answer_response_image_description'][$key2]:'';    
                    $questionOptionData['l4iao_answer_text'] = isset($allPostdata['answer_option_text'][$key2])?$allPostdata['answer_option_text'][$key2]:'';    
                    $questionOptionData['l4iao_correct_answer'] = isset($allPostdata['correct_answer'][$key2])?$allPostdata['correct_answer'][$key2]:0; 
                    $questionOptionData['l4iao_answer_order'] = isset($allPostdata['answer_order'][$key2])?$allPostdata['answer_order'][$key2]:0;   
                    $questionOptionData['l4iao_answer_group'] = isset($allPostdata['answer_group'][$key2])?$allPostdata['answer_group'][$key2]:0;   
                    $questionOptionData['l4iao_answer_image_description'] = isset($allPostdata['answer_image_description'][$key2])?$allPostdata['answer_image_description'][$key2]:'';   
                    $finalQuestionData = array('id'=>$key2,'l4iao_question_id'=>$allPostdata['question_id'],'l4iao_answer_text'=>$questionOptionData['l4iao_answer_text'],'l4iao_answer_image_description'=>$questionOptionData['l4iao_answer_image_description'],'l4iao_correct_answer'=>$questionOptionData['l4iao_correct_answer'],'l4iao_answer_order'=>$questionOptionData['l4iao_answer_order'],'l4iao_answer_group'=>$questionOptionData['l4iao_answer_group'],'l4iao_answer_response_text'=>$questionOptionData['l4iao_answer_response_text'],'l4iao_answer_response_image'=>$questionOptionData['l4iao_answer_response_image']);
                    $this->level4ActivitiesRepository->updateLevel4IntermediateActivityOptions($finalQuestionData,$key2);   
                }
            }
            break;                
        }        
        return Redirect::to("admin/editlevel4IntermediateActivity/".$allPostdata['question_id'])->with('success', 'Answer has been updated successfully');
    }
    
    /*
     * Delete media by media id
     */
    public function deleteAudioPopupImage($id, $file, $type)
    {
        if(isset($id) && $id>0)
        {           
            if($type == 2) {
                $deleteData=array('l4ia_question_audio'=>'');
            } else {
                $deleteData=array('l4ia_question_popup_image'=>'');
            }
            $result = $this->level4ActivitiesRepository->deleteAudioPopupImage($deleteData, $id);
            if($result && $file != ""){                
                @unlink($this->intermediateQuestionOriginalImageUploadPath.$file);  
                $deleteFile = $this->fileStorageRepository->deleteFileToStorage($file, $this->intermediateQuestionOriginalImageUploadPath, "s3");
                return Redirect::to("admin/editlevel4IntermediateActivity/".$id)->with('success', 'File has been deleted successfully');
            }
        }
    }
}
