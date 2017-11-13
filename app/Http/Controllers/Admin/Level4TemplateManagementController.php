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

class Level4TemplateManagementController extends Controller {

    public function __construct(ProfessionsRepository $ProfessionsRepository, Level4ActivitiesRepository $Level4ActivitiesRepository,TeenagersRepository $TeenagersRepository) {
        $this->ProfessionsRepository = $ProfessionsRepository;
        $this->objLevel4Activities = new Level4Activity();
        $this->Level4ActivitiesRepository = $Level4ActivitiesRepository;
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
        $this->TeenagersRepository       = $TeenagersRepository;
    }

    public function index() {
        $searchParamArray = Input::all();
        if (isset($searchParamArray['clearSearch'])) {
            unset($searchParamArray);
            Cache::forget('L4searchArray');
            Cache::forget('gamificationTemplate');
            $searchParamArray = array();
        }
        if (!empty($searchParamArray)) {
            Cache::forget('gamificationTemplate');
            if (isset($searchParamArray['page'])) {
                if (Cache::has('L4searchArray')) {
                    $searchParamArray = Cache::get('L4searchArray');
                } else {
                    Cache::forget('L4searchArray');
                }
            } else {
                Cache::forget('L4searchArray');
            }
            $gamificationTemplate = $this->Level4ActivitiesRepository->getAllGamificationTemplate($searchParamArray);
        } else {
            if (Cache::has('L4searchArray')) {
                $searchParamArray = Cache::get('L4searchArray');
            }
            if (Cache::has('gamificationTemplate')) {
                $gamificationTemplate = Cache::get('gamificationTemplate');
            } else {
                $gamificationTemplate = $this->Level4ActivitiesRepository->getAllGamificationTemplate($searchParamArray);
                Cache::forever('gamificationTemplate', $gamificationTemplate);
            }
        }
        return view('admin.ListGamificationTemplate',compact('gamificationTemplate', 'searchParamArray'));
    }

    public function add()
    {
        $level4TemplateDetail = array();
        $allActiveProfessions = $this->ProfessionsRepository->getAllActiveProfession();
        $leve4TemplateAnswrTypes = $this->Level4ActivitiesRepository->getLevel4TemplateAnswerTypes();
        return view('admin.EditLevel4Template',compact('leve4TemplateAnswrTypes','level4TemplateDetail','allActiveProfessions'));
    }

    public function edit($id)
    {
        $level4TemplateDetail = $this->Level4ActivitiesRepository->getGamificationTemplateById($id);
        $leve4TemplateAnswrTypes = $this->Level4ActivitiesRepository->getLevel4TemplateAnswerTypes();
        $allActiveProfessions = $this->ProfessionsRepository->getAllActiveProfession();
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
            $professionData = $this->ProfessionsRepository->getProfessionsDataFromId($allPostdata['question_profession']);
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
                            $imageOriginal = public_path($this->conceptOriginalImageUploadPath.$allPostdata['hidden_logo']);
                            File::delete($imageOriginal);
                        }
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

                    $saveData['gt_template_descritpion_popup_imge'] = $popupFileName;
                }
            }
        }
        $response = $this->Level4ActivitiesRepository->saveGamificationTemplate($saveData);
//        $teenagers = $this->TeenagersRepository->getAllActiveTeenagersForNotification();
//        foreach ($teenagers AS $key => $value) {
//            $message = 'Role play "' .$saveData['gt_template_title'].'" as a "'.$professionName.'" in ProTeen now!';
//            $return = Helpers::saveAllActiveTeenagerForSendNotifivation($value->id, $message);
//        }
        Cache::forget('gamificationTemplate');
        return Redirect::to("admin/listGamificationTemplate".$allPostdata['pageRank'])->with('success', trans('labels.level4activityupdatesuccess'));
    }

    public function delete($id)
    {
        $return = $this->Level4ActivitiesRepository->deleteGamificationTemplate($id);
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
        $professions = $this->ProfessionsRepository->getAllActiveProfession();
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
                if ($conceptId != 0) {
                    //copy existing image of concept
                    $conceptDetail = $this->Level4ActivitiesRepository->getGamificationTemplateById($conceptId);
                        
                    $newconceptImage = '';
                    $file = public_path($conceptOriginalImageUploadPath.$conceptDetail->gt_template_image);
                    if (File::exists(public_path($conceptOriginalImageUploadPath.$conceptDetail->gt_template_image)) && $conceptDetail->gt_template_image != '') {
                        $newconceptImage = $conceptId.'_'.$conceptDetail->gt_template_image;
                        $newfile = public_path($conceptOriginalImageUploadPath.$newconceptImage);
                        if (!copy($file, $newfile)) {
                            return Redirect::to("admin/copyConcept")->withErrors('')->withInput();
                            exit;
                        }
                    }
                    $return = $this->Level4ActivitiesRepository->copyConcept($conceptId,$postData['to_profession_id'],$newconceptImage);
                    $new_templateID[] = $return;
                    $conceptNames[] = $conceptDetail->gt_template_title;
                }
            }
           
            $professionName = '';
            //Get Profession name 
            $professionData = $this->ProfessionsRepository->getProfessionsDataFromId($postData['to_profession_id']);
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
                    $oldConceptId = $this->Level4ActivitiesRepository->getLevel4ActivityData($conceptId);                    
                    $id = $oldConceptId[0]->oldId;
                    $oldId = explode(",",$id);
                   
                    for ($k = 0; $k < count($oldId); $k++) {
                        $level4Detail = $this->Level4ActivitiesRepository->getLevel4ActivityDataById($oldId[$k]);
                        
                        if (!empty($level4Detail)) {
                            $newImage = $newAudio = $newAudioFileName= '';
                            //Set Question audio
                            if (isset($level4Detail[0]->l4ia_question_audio) && $level4Detail[0]->l4ia_question_audio != '') {
                                if (file_exists($this->questionDescriptionORIGINALImage . $level4Detail[0]->l4ia_question_audio)) {
                                    $audioFile = public_path($this->questionDescriptionORIGINALImage.$level4Detail[0]->l4ia_question_audio);
                                    $newAudioFileName = $oldId[$k].'_'.$level4Detail[0]->l4ia_question_audio;
                                    $newAudioFile = public_path($this->questionDescriptionORIGINALImage.$newAudioFileName);
                                    if (!copy($audioFile, $newAudioFile)) 
                                    {
                                        return Redirect::to("admin/copyConcept")->withErrors('Audio file not copied perfectly')->withInput();
                                        exit;
                                    }
                                } 
                            }
                            //$audioFile = public_path($this->questionOriginalImageUploadPath.$level4Detail[0]->l4ia_question_popup_image); 
                            $file = public_path($this->questionOriginalImageUploadPath.$level4Detail[0]->l4ia_question_popup_image);
                            $thumbfile = public_path($this->questionThumbImageUploadPath.$level4Detail[0]->l4ia_question_popup_image);
                            if (File::exists(public_path($this->questionOriginalImageUploadPath.$level4Detail[0]->l4ia_question_popup_image)) && $level4Detail[0]->l4ia_question_popup_image != '') {
                                $newImage = $oldId[$k].'_'.$level4Detail[0]->l4ia_question_popup_image;
                                $newfile = public_path($this->questionOriginalImageUploadPath.$newImage);
                                $newthumbfile = public_path($this->questionThumbImageUploadPath.$newImage);
                                if (!copy($file, $newfile)) {
                                    return Redirect::to("admin/copyConcept")->withErrors('')->withInput();
                                    exit;
                                }
                                $imageInfo = pathinfo($thumbfile); 
                                if($imageInfo['extension'] != 'gif')
                                {
                                    if (!copy($thumbfile, $newthumbfile)) {
                                        return Redirect::to("admin/copyConcept")->withErrors('')->withInput();
                                        exit;
                                    }
                                }
                            }
                            
                            $activityData = $this->Level4ActivitiesRepository->copyLevel4ActivityData($oldId[$k],$postData['to_profession_id'],$new_templateID[$i],$newImage, $newAudioFileName);
                        }
                    }
                   
                    $count = count($oldId);
                    
                    $newConceptId = $this->Level4ActivitiesRepository->getLevel4ActivityData($new_templateID[$i]);
                    $newId = $newConceptId[0]->oldId;                    
                    $newId = explode(",",$newId);
                    for ($j = 0; $j < $count; $j++) {
                        $optionDetail = $this->Level4ActivitiesRepository->getLevel4ActivityOptionsData($oldId[$j]);
                        if (!empty($optionDetail)) 
                        {
                            $newanswerImageArray = [];
                            $newresponseImageArray = [];
                            $imageInfo = array();
                            foreach($optionDetail as $keyOption => $optionValue)
                            {
                                $newanswerImage = '';
                                $file = public_path($this->answerOriginalImageUploadPath.$optionValue->l4iao_answer_image);
                                $thumbfile = public_path($this->answerThumbImageUploadPath.$optionValue->l4iao_answer_image);
                                if (File::exists(public_path($this->answerOriginalImageUploadPath.$optionValue->l4iao_answer_image)) && $optionValue->l4iao_answer_image != '') {
                                    $newanswerImage = $oldId[$j].'_'.$optionValue->l4iao_answer_image;
                                    $newfile = public_path($this->answerOriginalImageUploadPath.$newanswerImage);
                                    $newthumbfile = public_path($this->answerThumbImageUploadPath.$newanswerImage);
                                    if (!copy($file, $newfile)) {
                                        return Redirect::to("admin/copyConcept")->withErrors('')->withInput();
                                        exit;
                                    }
                                    if (!copy($thumbfile,$newthumbfile)) {
                                        return Redirect::to("admin/copyConcept")->withErrors('')->withInput();
                                        exit;
                                    }
                                }

                                $newresponseImage = '';
                                $file1 = public_path($this->responseOriginalImageUploadPath.$optionValue->l4iao_answer_response_image);
                                $thumbfile1 = public_path($this->responseThumbImageUploadPath.$optionValue->l4iao_answer_response_image);
                                if (File::exists(public_path($this->responseOriginalImageUploadPath.$optionValue->l4iao_answer_response_image)) && $optionValue->l4iao_answer_response_image != '') {
                                    $newresponseImage = $oldId[$j].'_'.$optionValue->l4iao_answer_response_image;
                                    $newfile1 = public_path($this->responseOriginalImageUploadPath.$newresponseImage);
                                    $newThumbfile1 = public_path($this->responseThumbImageUploadPath.$newresponseImage);
                                    if (!copy($file1, $newfile1)) {
                                        return Redirect::to("admin/copyConcept")->withErrors('')->withInput();
                                        exit;
                                    }
                                    $imageInfo = pathinfo($thumbfile1); 
                                    if($imageInfo['extension'] != 'gif')
                                    {    
                                        if (!copy($thumbfile1,$newThumbfile1)) {
                                            return Redirect::to("admin/copyConcept")->withErrors('')->withInput();
                                            exit;
                                        }
                                    }
                                } 

                                $newanswerImageArray[$optionValue->optionsId] = $newanswerImage;
                                $newresponseImageArray[$optionValue->optionsId] = $newresponseImage;
                            }
                            
                            $activityOptionData = $this->Level4ActivitiesRepository->copyLevel4ActivityOptionsData(
                            $oldId[$j],$newId[$j],$newanswerImageArray,$newresponseImageArray);
                        }

                        $questionData = $this->Level4ActivitiesRepository->getQuestionMediaById($oldId[$j]);
                        if (!empty($questionData)) 
                        {
                            $newquestionImageArray = [];
                            $imageInfo = array();
                            foreach($questionData as $keyQuestion => $valueQuestion)
                            {
                                if ($valueQuestion->l4iam_media_type == "I") 
                                {
                                    $file2 = public_path($this->questionOriginalImageUploadPath.$valueQuestion->l4iam_media_name);
                                    $thumbfile2 = public_path($this->questionThumbImageUploadPath.$valueQuestion->l4iam_media_name);
                                    if (File::exists(public_path($this->questionOriginalImageUploadPath.$valueQuestion->l4iam_media_name)) && $valueQuestion->l4iam_media_name != '') {
                                        $newquestionImage = $oldId[$i].'_'.$valueQuestion->l4iam_media_name;
                                        $newfile2 = public_path($this->questionOriginalImageUploadPath.$newquestionImage);
                                        $newthumbfile2 = public_path($this->questionThumbImageUploadPath.$newquestionImage);
                                        if (!copy($file2, $newfile2)) {
                                            return Redirect::to("admin/copyConcept")->withErrors('')->withInput();
                                            exit;
                                        }
                                        $imageInfo = pathinfo($thumbfile2); 
                                        if($imageInfo['extension'] != 'gif')
                                        {
                                            if (!copy($thumbfile2, $newthumbfile2)) {
                                                return Redirect::to("admin/copyConcept")->withErrors('')->withInput();
                                                exit;
                                            }
                                        }
                                    }
                                } else {
                                    $newquestionImage = $valueQuestion->l4iam_media_name;
                                }
                                $newquestionImageArray[$valueQuestion->mediaId] = $newquestionImage;
                            }    
                            
                            $activityMediaData = $this->Level4ActivitiesRepository->copyLevel4ActivityMediaData($oldId[$j],$newId[$j],$newquestionImageArray);
                        }
                    }                   
                }
            }
        }
        //If valid concepts ids then copy the those concepts
        if ($return) {
//            $teenagers = $this->TeenagersRepository->getAllActiveTeenagersForNotification();
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
        $templateData = $this->Level4ActivitiesRepository->getTemplateDataForCoinsDetail($id);
        if (!empty($templateData)) {
            $coins += $templateData['gt_coins'];
        }
        $result = $this->Level4ActivitiesRepository->updateTemplateCoinsDetail($id, $coins);

        return Redirect::to("admin/listGamificationTemplate".$postData['pageRank'])->with('success', trans('labels.coinsaddsuccess'));
    }
}