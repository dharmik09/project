<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use Auth;
use Input;
use Redirect;
use App\Services\Parents\Contracts\ParentsRepository;
use App\Services\Teenagers\Contracts\TeenagersRepository;
use App\Services\Level1Activity\Contracts\Level1ActivitiesRepository;
use App\Services\Level4Activity\Contracts\Level4ActivitiesRepository;
use App\Services\Template\Contracts\TemplatesRepository;
use Helpers;
use Config;
use App\Services\Professions\Contracts\ProfessionsRepository;
use Mail;
use App;
use PDF;
use App\PromisePlus;
use App\ProfessionLearningStyle;
use App\UserLearningStyle;
use App\LearningStyle;
use App\Professions;
use App\Level4Answers;
use App\PaidComponent;
use App\DeductedCoins;
use App\MultipleIntelligent;
use App\Apptitude;
use App\Personality;
use App\Level2ParentsActivity;
use App\TeenParentChallenge;
use Illuminate\Support\Facades\Storage;
use App\PromiseParametersMaxScore;
use App\TeenagerPromiseScore;
use Illuminate\Http\Request;
use App\MultipleIntelligentScale;
use App\ApptitudeTypeScale;
use App\PersonalityScale;
use App\Level4ProfessionProgress;

class DashboardManagementController extends Controller {

    public function __construct(ParentsRepository $parentsRepository, TeenagersRepository $teenagersRepository, Level1ActivitiesRepository $level1ActivitiesRepository, ProfessionsRepository $professionsRepository, TemplatesRepository $templatesRepository, Level4ActivitiesRepository $level4ActivitiesRepository) {
        $this->parentsRepository = $parentsRepository;
        $this->teenagersRepository = $teenagersRepository;
        $this->level1ActivitiesRepository = $level1ActivitiesRepository;
        $this->professionsRepository = $professionsRepository;
        $this->templateRepository = $templatesRepository;
        $this->level4ActivitiesRepository = $level4ActivitiesRepository;
        $this->interestOriginalImageUploadPath = Config::get('constant.INTEREST_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->apptitudeOriginalImageUploadPath = Config::get('constant.APPTITUDE_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->miOriginalImageUploadPath = Config::get('constant.MI_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->personalityOriginalImageUploadPath = Config::get('constant.PERSONALITY_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->professionThumbImageUploadPath = Config::get('constant.PROFESSION_THUMB_IMAGE_UPLOAD_PATH');
        $this->professionOriginalImageUploadPath = Config::get('constant.PROFESSION_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->cartoonThumbImageUploadPath = Config::get('constant.CARTOON_THUMB_IMAGE_UPLOAD_PATH');
        $this->cartoonOriginalImageUploadPath = Config::get('constant.CARTOON_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->humanThumbImageUploadPath = Config::get('constant.HUMAN_THUMB_IMAGE_UPLOAD_PATH');
        $this->humanOriginalImageUploadPath = Config::get('constant.HUMAN_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->relationIconOriginalImageUploadPath = Config::get('constant.RELATION_ICON_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->relationIconThumbImageUploadPath = Config::get('constant.RELATION_ICON_THUMB_IMAGE_UPLOAD_PATH');
        $this->teenThumbImageUploadPath = Config::get('constant.TEEN_THUMB_IMAGE_UPLOAD_PATH');
        $this->learningStyleThumbImageUploadPath = Config::get('constant.LEARNING_STYLE_THUMB_IMAGE_UPLOAD_PATH');
        $this->objPromiseParametersMaxScore = new PromiseParametersMaxScore;
        $this->objTeenagerPromiseScore = new TeenagerPromiseScore;
        $this->objMIScale = new MultipleIntelligentScale;
        $this->objApptitudeScale = new ApptitudeTypeScale;
        $this->objPersonalityScale = new PersonalityScale;
        $this->objLevel4ProfessionProgress = new Level4ProfessionProgress;
    }

    public function index() {
            $parentId = Auth::guard('parent')->user()->id;
           
            // Get All Verified Teenagers of parent
            $teenThumbImageUploadPath = $this->teenThumbImageUploadPath;
            $teenagersIds = $this->parentsRepository->getAllVerifiedTeenagers($parentId);
           
            $final = array();
            if (isset($teenagersIds) && !empty($teenagersIds)) {
                foreach ($teenagersIds as $key => $data) {
                    $checkuserexist = $this->teenagersRepository->checkActiveTeenager($data->ptp_teenager);
                    
                    if (isset($checkuserexist) && $checkuserexist) {
                        $teengersDetail = $this->teenagersRepository->getTeenagerById($data->ptp_teenager);
                       
                        //$teengersBooster = $this->teenagersRepository->getTeenagerBoosterPoints($data->ptp_teenager);
                        $teengersBooster = $this->teenagersRepository->getTeenagerBasicBooster($data->ptp_teenager);                        
                        $final[] = array('detail' => $teengersDetail, 'booster' => $teengersBooster,'pairdata'=>$data);
                    }
                }
            }
             
            $parentData = $this->parentsRepository->getParentDataForCoinsDetail($parentId);
            $parents = $this->teenagersRepository->getParentTeens($parentId);
            return view('parent.dashboard', compact('teenagersIds', 'final', 'parents', 'teenThumbImageUploadPath','parentData'));
    }

    public function progress($id = 0) {
        //get first teen
        if (empty($id) && $id == 0 && $id == '') {
            $parent_Id = Auth::guard('parent')->user()->id;
            $teenDetails = $this->teenagersRepository->getTeenDetailByParentId($parent_Id);
            if (isset($teenDetails) && !empty($teenDetails)) {
                $teenDetailById = $this->teenagersRepository->getTeenagerById($teenDetails[0]->ptp_teenager);
                $teenUniqueId = $teenDetailById->t_uniqueid;
            }
        } else {
            $teenUniqueId = $id;
        }
        if (isset($teenUniqueId) && $teenUniqueId != '') {
            $teenDetail = $this->teenagersRepository->getTeenagerByUniqueId($teenUniqueId);
        } else {
            return Redirect::to("parent/home")->with('error', 'No data found');
            exit;
        }
        if (isset($teenDetail) && !empty($teenDetail)) {
            $parentType = Auth::guard('parent')->user()->p_user_type;
            if ($parentType == Config::get('constant.PARENT_USER_FLAG')) {
                if ($teenDetail->is_share_with_parents == Config::get('constant.SHARE_INFO_WITH_PARENT_OFF')) {
                    return Redirect::to("parent/home")->with('error', 'Sorry. User has preferred to keep this information private');
                    exit;
                }
            }
            if ($parentType == Config::get('constant.MENTOR_USER_FLAG')) {
                if ($teenDetail->is_share_with_teachers == Config::get('constant.SHARE_INFO_WITH_MENTOR_OFF')) {
                    return Redirect::to("parent/home")->with('error', 'Sorry. User has preferred to keep this information private');
                    exit;
                }
            }
            $checkuserexist = $this->teenagersRepository->checkActiveTeenager($teenDetail->id);
            if (isset($checkuserexist) && $checkuserexist) {
                //Get all teenager detail
                $parentId = Auth::guard('parent')->user()->id;
                // Get All Verified Teenagers of parent
                $teenagersIds = $this->parentsRepository->getAllVerifiedTeenagers($parentId);
                $finalTeens = array();
                if (isset($teenagersIds) && !empty($teenagersIds)) {
                    foreach ($teenagersIds as $key => $data) {
                        $checkuserexist = $this->teenagersRepository->checkActiveTeenager($data->ptp_teenager);
                        if (isset($checkuserexist) && $checkuserexist) {
                            $teengersDetail = $this->teenagersRepository->getTeenagerById($data->ptp_teenager);
                            $finalTeens[] = array('id' => $teengersDetail->id, 'name' => $teengersDetail->t_name, 'nickname' => $teengersDetail->t_nickname, 'unique_id' => $teengersDetail->t_uniqueid);
                        }
                    }
                }

                //get user attempted level 1 question
                $level1Activity = $this->level1ActivitiesRepository->getLevel1ActivityWithAnswer($teenDetail->id);

                if (isset($level1Activity) && !empty($level1Activity)) {
                    $level1Detail = array();
                    $finalData = array();
                    foreach ($level1Activity as $key => $data) {
                        //Get Trend of Level 1 activity
                        $levelTrend = Helpers::calculateTrendForLevel1($data->activityid,2);

                        $level1Detail['activity_id'] = $data->id;
                        $level1Detail['question_text'] = $data->l1ac_text;
                        $level1Detail['teen_anwer'] = $data->l1op_option;
                        $level1Detail['trend'] = $levelTrend;
                        $finalData[] = $level1Detail;
                    }
                    $response['level1result'] = $finalData;
                }

                //get teenager API data
                $teenagerInterest = array();
                $teenagerApptitude = array();
                $teenagerPersonality = array();
                $teenagerMI = array();
                $finalMIParameters = array();
                $finalSortedData = array();
                $sortedMIHData = array();
                $sortedMIMData = array();
                $sortedMILData = array();
                $teenagerAPIData = Helpers::getTeenAPIScore($teenDetail->id);

                if (isset($teenagerAPIData) && !empty($teenagerAPIData)) {
                    $i = 1;
                    // Teenager interest data
                    foreach ($teenagerAPIData['APIscore']['interest'] as $interest => $val) {
                        if ($val == 1) {
                            $interestImage = Helpers::getInterestData($interest);
                            if (!empty($interestImage)) {
                                if (isset($interestImage->it_logo) && $interestImage->it_logo != '') {
                                    $image = Storage::url($this->interestOriginalImageUploadPath . $interestImage->it_logo);
                                } else {
                                    $image = Storage::url($this->interestOriginalImageUploadPath . 'proteen-logo.png');
                                }
                            }
                            $teenagerInterest[] = array('image' => $image, 'interest' => $interest);
                        }
                        $i++;
                    }
                    // Teenager Apptitude data
                    $k = 1;
                    foreach ($teenagerAPIData['APIscore']['aptitude'] as $aptitude => $val) {
                        //if ($val != 0) {
                        $aptitudemage = Helpers::getApptitudeData($aptitude);
                        $video = '';
                        $image = '';
                        $info = '';
                        if (!empty($aptitudemage)) {
                            if ($aptitudemage->apt_logo != '' && isset($aptitudemage->apt_logo)) {
                                $image = Storage::url($this->apptitudeOriginalImageUploadPath . $aptitudemage->apt_logo);
                            } else {
                                $image = Storage::url($this->apptitudeOriginalImageUploadPath . 'proteen-logo.png');
                            }
                            $video = isset($aptitudemage->apt_video) && ($aptitudemage->apt_video != '') ? Helpers::youtube_id_from_url($aptitudemage->apt_video) : '';
                            $info = $aptitudemage->ap_information;
                        }
                        $aptitudescale = $teenagerAPIData['APIscale']['aptitude'][$aptitude];
                        $objApptitude = new Apptitude();

                        $rateId = $objApptitude->getApptitudeDataIdByName($aptitude);

                        $objLevel2ParentsActivity = new Level2ParentsActivity();

                        $level2PActivityData = $objLevel2ParentsActivity->getLevel2ParentsActivity($rateId,$parentId,$teenDetail->id,'apptitude');
                        $parentScale = '';
                        if (!empty($level2PActivityData) && count($level2PActivityData) > 0) {
                            $parentScale = $level2PActivityData[0]['l2pac_value'];
                        }

                        $teenagerApptitude[] = array('image' => $image, 'aptitude' => $aptitude, 'scale' => $aptitudescale, 'video' => $video, 'info' => $info, 'type' => 'apptitude' ,'parentScale' => $parentScale);
                        //}
                        $k++;
                    }

                    // Teenager MI Data
                    foreach ($teenagerAPIData['APIscore']['MI'] as $mi => $val) {
                        //if ($val != 0) {
                        $video = '';
                        $image = '';
                        $info = '';
                        $miimage = Helpers::getMIData($mi);
                        if (!empty($miimage)) {
                            if ($miimage->mit_logo != '' && isset($miimage->mit_logo)) {
                                $image = Storage::url($this->miOriginalImageUploadPath . $miimage->mit_logo);
                            } else {
                                $image = Storage::url($this->miOriginalImageUploadPath . 'proteen-logo.png');
                            }
                            $video = isset($miimage->mi_video) && ($miimage->mi_video != '') ? Helpers::youtube_id_from_url($miimage->mi_video) : '';
                            $info = $miimage->mi_information;
                        }
                        $miscale = $teenagerAPIData['APIscale']['MI'][$mi];
                        $objMultipleIntelligent = new MultipleIntelligent();

                        $rateId = $objMultipleIntelligent->getMultipleIntelligentIdByName($mi);

                        $objLevel2ParentsActivity = new Level2ParentsActivity();

                        $level2PActivityData = $objLevel2ParentsActivity->getLevel2ParentsActivity($rateId,$parentId,$teenDetail->id,'mi');
                        $parentScale = '';
                        if (!empty($level2PActivityData) && count($level2PActivityData) > 0) {
                            $parentScale = $level2PActivityData[0]['l2pac_value'];
                        }
                        $teenagerMI[] = array('image' => $image, 'aptitude' => $mi, 'scale' => $miscale, 'video' => $video, 'info' => $info, 'type' => 'mi','parentScale' => $parentScale);
                        //}
                    }
                    // Teenager personality Data
                    foreach ($teenagerAPIData['APIscore']['personality'] as $personality => $val) {
                        //if ($val != 0) {
                        $video = '';
                        $image = '';
                        $info = '';
                        $personalityimage = Helpers::getPersonalityData($personality);
                        
                        if (!empty($personalityimage)) {
                            if ($personalityimage->pt_logo != '' && isset($personalityimage->pt_logo)) {
                                $image = Storage::url($this->personalityOriginalImageUploadPath . $personalityimage->pt_logo);
                            } else {
                                $image = Storage::url($this->personalityOriginalImageUploadPath . 'proteen-logo.png');
                            }
                            $video = isset($personalityimage->pt_video) && ($personalityimage->pt_video != '') ? Helpers::youtube_id_from_url($personalityimage->pt_video) : '';
                            $info = $personalityimage->pt_information;
                        }
                        $personalityscale = $teenagerAPIData['APIscale']['personality'][$personality];
                        $objPersonality = new Personality();

                        $rateId = $objPersonality->getPersonalityDataIdByName($personality);

                        $objLevel2ParentsActivity = new Level2ParentsActivity();

                        $level2PActivityData = $objLevel2ParentsActivity->getLevel2ParentsActivity($rateId,$parentId,$teenDetail->id,'personality');
                        $parentScale = '';
                        if (!empty($level2PActivityData) && count($level2PActivityData) > 0) {
                            $parentScale = $level2PActivityData[0]['l2pac_value'];
                        }

                        $teenagerPersonality[] = array('image' => $image, 'aptitude' => $personality, 'scale' => $personalityscale, 'video' => $video, 'info' => $info, 'type' => 'personality','parentScale' => $parentScale);
                        //}
                    }
                    
                    $finalMIParameters = array_merge($teenagerApptitude, $teenagerMI, $teenagerPersonality);

                    if (isset($finalMIParameters) && !empty($finalMIParameters)) {
                        foreach ($finalMIParameters as $key => $data) {
                            if ($data['scale'] == 'H') {
                                $sortedMIHData[] = $data;
                            }
                            if ($data['scale'] == 'M') {
                                $sortedMIMData[] = $data;
                            }
                            if ($data['scale'] == 'L') {
                                $sortedMILData[] = $data;
                            }
                        }
                    }
                    $finalSortedData = array_merge($sortedMIHData, $sortedMIMData, $sortedMILData);
                }
               
                $teenagerApptitudeData = array();
                $teenagerMIData = array();
                $teenagerPersonalityData = array();
                $count = 1;
                $teenAssessment = 0;
                shuffle($finalSortedData);
                foreach($finalSortedData AS $key => $Value) {
                    if ($Value['parentScale'] != '') {
                       $teenAssessment++;
                    }
                    if ($count <= 8) {
                        $teenagerMIData[] = $Value;
                    } else if($count <= 16) {
                        $teenagerPersonalityData[] = $Value;
                    } else {
                        $teenagerApptitudeData[] = $Value;
                    }
                    $count++;
                }
                if ($teenAssessment == 24) {
                    $response['teenAssessment'] = 'yes';
                } else {
                    $response['teenAssessment'] = 'no';
                }
                $teenagerMyIcons = array();
                //Get teenager choosen Icon
                $teenagerIcons = $this->teenagersRepository->getTeenagerSelectedIcon($teenDetail->id);
                $relationIcon = array();
                $fictionIcon = array();
                $nonFiction = array();
                if (isset($teenagerIcons) && !empty($teenagerIcons)) {
                    foreach ($teenagerIcons as $key => $icon) {
                        if ($icon->ti_icon_type == 1) {
                            $fictionIconArr = [];
                            if ($icon->fiction_image != '' && Storage::size($this->cartoonThumbImageUploadPath . $icon->fiction_image) > 0)  {
                                $fictionIconArr['iconImage'] = Storage::url($this->cartoonThumbImageUploadPath . $icon->fiction_image);
                            } else {
                                $fictionIconArr['iconImage'] = Storage::url($this->cartoonThumbImageUploadPath . 'proteen-logo.png');
                            }
                            $iconDesription = (isset($icon->ci_description) && !empty($icon->ci_description)) ? ' - ' . $icon->ci_description : '';
                            $fictionIconArr['iconDescription'] = $icon->ci_name . $iconDesription;
                            $fictionIcon[] = $fictionIconArr;
                        } else if ($icon->ti_icon_type == 2) {
                            $nonFictionArr = [];
                            if ($icon->nonfiction_image != '' && Storage::size($this->humanThumbImageUploadPath . $icon->nonfiction_image) > 0) {
                                $nonFictionArr['iconImage'] = Storage::url($this->humanThumbImageUploadPath . $icon->nonfiction_image);
                            } else {
                                $nonFictionArr['iconImage'] = Storage::url($this->humanThumbImageUploadPath . 'proteen-logo.png');
                            }
                            $iconDesription = (isset($icon->hi_description) && !empty($icon->hi_description)) ? ' - ' . $icon->hi_description : '';
                            $nonFictionArr['iconDescription'] = $icon->hi_name .  $iconDesription;
                            $nonFiction[] = $nonFictionArr;
                        } else {
                            $relationIconArr = [];
                            if ($icon->ti_icon_image != '' && Storage::size($this->relationIconThumbImageUploadPath . $icon->ti_icon_image) > 0) {
                                $relationIconArr['iconImage'] = Storage::url($this->relationIconThumbImageUploadPath . $icon->ti_icon_image);
                            } else {
                                $relationIconArr['iconImage'] = Storage::url($this->relationIconThumbImageUploadPath . 'proteen-logo.png');
                            }
                            $relationIconArr['iconDescription'] = $icon->rel_name;
                            $relationIcon[] = $relationIconArr;
                        }
                    }
                    $teenagerMyIcons = array_merge($fictionIcon, $nonFiction, $relationIcon);
                } 

                //Get teenager attempted profession
                $professionArray = $this->objLevel4ProfessionProgress->getTeenAttemptProfessions($teenDetail->id);
                $professionAttempted = array();
                $setAttemptedProfessionIds = [];
                if (isset($professionArray) && count($professionArray) > 0) {
                    foreach($professionArray as $key=>$val) {
                        $setAttemptedProfessionIds[] = $val->profession_id;
                    }
                    foreach ($professionArray as $key => $val) {
                        if($key < 4) {
                            $yourScore = $idAndRank = 0;
                            $professionAttempted[$key]['professionId'] = $val->profession_id;
                            $professionAttempted[$key]['profession_name'] = $val->pf_name;
                        } else {
                            break;
                        }
                    }
                }

                //$professionArray = $this->professionsRepository->getTeenagerAttemptedProfessionForDashboard($teenDetail->id);

                // $professionAttempted = array();
                // $setAttemptedProfessionIds = [];
                // $objDeductedCoins = new DeductedCoins();
                // $objPaidComponent = new PaidComponent();
                // $componentsData = $objPaidComponent->getPaidComponentsData(Config::get('constant.PROMISE_PLUS'));

                // if (isset($professionArray) && !empty($professionArray))
                // {
                //     foreach($professionArray as $key=>$val)
                //     {
                //         $setAttemptedProfessionIds[] = $val->id;
                //     }
                //     foreach ($professionArray as $key => $val)
                //     {
                //         if($key < 4)
                //         {
                //             $yourScore = $idAndRank = 0;
                //             $professionAttempted[$key]['professionId'] = $val->id;
                //             $professionAttempted[$key]['profession_name'] = $val->pf_name;
                //             if(isset($componentsData) && !empty($componentsData)){
                //                 $deductedCoinsDetail = $objDeductedCoins->getDeductedCoinsDetailById($parentId,$val->id,2,$componentsData->id);
                //             }
                //             $days = 0;
                //             if (isset($deductedCoinsDetail[0]) && !empty($deductedCoinsDetail)) {
                //                 $days = Helpers::calculateRemainingDays($deductedCoinsDetail[0]->dc_end_date);
                //             }
                //             $professionAttempted[$key]['remainingDays'] = $days;
                //             $professionAttempted[$key]['required_coins'] = $componentsData->pc_required_coins;
                //         }
                //         else
                //         {
                //             break;
                //         }
                //     }

                // }
                $objDeductedCoins = new DeductedCoins();
                $objPaidComponent = new PaidComponent();
                $componentsData = $objPaidComponent->getPaidComponentsData('Parent Report');
                $coins = 0;
                if (isset($componentsData) && !empty($componentsData)) {
                    $coins = $componentsData->pc_required_coins;
                }
                $deductedCoinsDetail = $objDeductedCoins->getDeductedCoinsDetailByIdForLS($parentId,$componentsData->id,2);
                $days = 0;
                if (isset($deductedCoinsDetail[0]) && !empty($deductedCoinsDetail)) {
                    $days = Helpers::calculateRemainingDays($deductedCoinsDetail[0]->dc_end_date);
                }
                $response['remainingDays'] = $days;
                $days = 0;
                $componentsData = $objPaidComponent->getPaidComponentsData(Config::get('constant.LEARNING_STYLE'));
                $deductedCoinsDetail = $objDeductedCoins->getDeductedCoinsDetailByIdForLS($parentId,$componentsData->id,2);
                $days = 0;
                if (isset($deductedCoinsDetail[0]) && !empty($deductedCoinsDetail)) {
                    $days = Helpers::calculateRemainingDays($deductedCoinsDetail[0]->dc_end_date);
                }
                $response['remainingDaysForLS'] = $days;
                $response['required_coins'] = $componentsData->pc_required_coins;
                $response['attempted_profession'] = $professionAttempted;
                $response['teenagerInterest'] = $teenagerInterest;
                //$response['teenagerMI'] = $finalSortedData;
                $response['teenagerMIData'] = $teenagerMIData;
                $response['teenagerApptitudeData'] = $teenagerApptitudeData;
                $response['teenagerPersonalityData'] = $teenagerPersonalityData;
                $response['teenagerMI'] = $teenagerMI;
                $response['teenagerApptitude'] = $teenagerApptitude;
                $response['teenagerPersonality'] = $teenagerPersonality;
                $response['finalTeens'] = $finalTeens;
                $response['teenagerMyIcons'] = $teenagerMyIcons;
                $response['setAttemptedProfessionIds'] = $setAttemptedProfessionIds;
                $response['finalTeens'] = $finalTeens;
                $response['coins'] = $coins;
                $response['finalSortedData'] = $finalSortedData;
                $learningGuidance = Helpers::getCmsBySlug('parent-progress-learning-guidance-text');
                return view('parent.parentProgress', compact('response', 'teenDetail', 'learningGuidance'));

            } else {
                Auth::guard('parent')->logout();
                return Redirect::to('/parent');
                exit;
            }
        } else {
            return Redirect::to("parent/home")->with('error', 'No data found');
            exit;
        }
    }

    //Parent progress page, LIL Card
    public function getProfessionBadgesAndRank()
    {
        $professionId = Input::get('professionId');
        $teenagerId = Input::get('teenagerId');
        $response = [];

        //get profession name and logo
        $professionData = $this->professionsRepository->getProfessionsById($professionId);

        $professionName = isset($professionData[0]->pf_name)?$professionData[0]->pf_name:'';
        if (isset($professionData[0]->pf_logo) && $professionData[0]->pf_logo != '' && Storage::size($this->professionOriginalImageUploadPath . $professionData[0]->pf_logo) > 0) {
            $profession_logo = Storage::url($this->professionOriginalImageUploadPath . $professionData[0]->pf_logo);
        } else {
            $profession_logo = Storage::url($this->professionOriginalImageUploadPath . 'proteen-logo.png');
        }

        //Get badges
        $getTeenagerAllTypeBadges = $this->teenagersRepository->getTeenagerAllTypeBadges($teenagerId, $professionId);
        $badgesCollection['newbie'] = (isset($getTeenagerAllTypeBadges['level4Basic']['badges'])) ? $getTeenagerAllTypeBadges['level4Basic']['badges'] : '';
        $badgesCollection['apprentice'] = (isset($getTeenagerAllTypeBadges['level4Intermediate']['badges'])) ? $getTeenagerAllTypeBadges['level4Intermediate']['badges'] : '';
        $badgesCollection['wizard'] = (isset($getTeenagerAllTypeBadges['level4Advance']['badges'])) ? $getTeenagerAllTypeBadges['level4Advance']['badges'] : '';
        $response['badges'][] = $badgesCollection;

        //Get rank and points
        $pData = Helpers::getCompetingUserList($professionId);
        $professionAllScore = $pData[$teenagerId];
        $response['professionName'] = $professionName;
        $response['profession_logo'] = $profession_logo;

        if (isset($professionAllScore) && !empty($professionAllScore)) {
            $response['highestScore'] = (isset($professionAllScore['highestScore'])) ? $professionAllScore['highestScore'] : 0;
            $response['yourscore'] = (isset($professionAllScore['yourScore'])) ? $professionAllScore['yourScore'] : 0;
            //$professionAttempted[$key]['rank'] = (isset($professionAllScore['yourRank'])) ? $professionAllScore['yourRank'] : 0;
            $response['rank'] = (isset($professionAllScore['rank'])) ? $professionAllScore['rank'] : 0;
        } else {
            $response['highestScore'] = 0;
            $response['yourscore'] = 0;
            $response['rank'] = 0;
        }

        return view('parent.badgesRank',compact('response','teenagerId'));
        exit;
    }

    public function getProfessionBadgesAndRankOnClick()
    {
        $professionId = (Input::get('professionId') != "") ? Input::get('professionId') : "";
        $teenagerId = (Input::get('teenagerId') != "") ? Input::get('teenagerId') : "";
        $parentId = Auth::guard('parent')->user()->id;
        $response = [];
        $response['professionId'] = $professionId;
        $response['teenagerId'] = $teenagerId;
        $response['message'] = "";
        $response['status'] = 0;
        if($teenagerId != '')
        {
            if($professionId != "")
            {
                //Get teenager attempted profession
                // $professionArray = $this->objLevel4ProfessionProgress->getTeenAttemptProfessions($teenDetail->id);
                // $professionAttempted = array();
                // $setAttemptedProfessionIds = [];
                // if (isset($professionArray) && count($professionArray) > 0) {
                //     foreach($professionArray as $key=>$val) {
                //         $setAttemptedProfessionIds[] = $val->id;
                //     }
                //     foreach ($professionArray as $key => $val) {
                //         if($key < 4) {
                //             $yourScore = $idAndRank = 0;
                //             $professionAttempted[$key]['professionId'] = $val->profession_id;
                //             $professionAttempted[$key]['profession_name'] = $val->pf_name;
                //         } else {
                //             break;
                //         }
                //     }
                // }
                $setAttemptedProfessionIds = [];
                $professionArray = $this->objLevel4ProfessionProgress->getTeenAttemptProfessions($teenagerId);
                if(isset($professionArray) && !empty($professionArray))
                {
                    foreach($professionArray as $key=>$val)
                    {
                        if ($key < 4) {
                            continue;
                        } else {
                            $setAttemptedProfessionIds[] = $val->profession_id;
                        }
                    }
                }
                if(in_array($professionId, $setAttemptedProfessionIds))
                {
                    // $objPaidComponent = new PaidComponent();
                    // $objDeductedCoins = new DeductedCoins();
                    // $componentsData = $objPaidComponent->getPaidComponentsData(Config::get('constant.PROMISE_PLUS'));
                   
                    // if(isset($componentsData) && !empty($componentsData)){
                    //     $deductedCoinsDetail = $objDeductedCoins->getDeductedCoinsDetailById($parentId,$professionId,2,$componentsData->id);
                    // }
                    
                    // $days = 0;
                    // if (count($deductedCoinsDetail) > 0) {                       
                    //     $days = Helpers::calculateRemaningDays($deductedCoinsDetail[0]->dc_end_date);
                    // }
                    // $response['remainingDays'] = $days;
                    // $response['required_coins'] = $componentsData->pc_required_coins;

                    //get profession name and logo
                    $professionData = $this->professionsRepository->getProfessionsById($professionId);
                   
                    $professionName = isset($professionData[0]->pf_name)?$professionData[0]->pf_name:'';
                    if (isset($professionData[0]->pf_logo) && $professionData[0]->pf_logo != '') {
                        $profession_logo = Storage::url($this->professionOriginalImageUploadPath . $professionData[0]->pf_logo);
                    } else {
                        $profession_logo = Storage::url($this->professionOriginalImageUploadPath . 'proteen-logo.png');
                    }
                    //Get badges
                    $getTeenagerAllTypeBadges = $this->teenagersRepository->getTeenagerAllTypeBadges($teenagerId, $professionId);
                    $badgesCollection['newbie'] = (isset($getTeenagerAllTypeBadges['level4Basic']['badges'])) ? $getTeenagerAllTypeBadges['level4Basic']['badges'] : '';
                    $badgesCollection['apprentice'] = (isset($getTeenagerAllTypeBadges['level4Intermediate']['badges'])) ? $getTeenagerAllTypeBadges['level4Intermediate']['badges'] : '';
                    $badgesCollection['wizard'] = (isset($getTeenagerAllTypeBadges['level4Advance']['badges'])) ? $getTeenagerAllTypeBadges['level4Advance']['badges'] : '';
                    $response['badges'][] = $badgesCollection;
                    //Get rank and points
                    $pData = Helpers::getCompetingUserList($professionId);
                    $professionAllScore = $pData[$teenagerId];
                    $response['professionName'] = $professionName;
                    $response['profession_logo'] = $profession_logo;

                    if (isset($professionAllScore) && !empty($professionAllScore)) {
                        $response['highestScore'] = (isset($professionAllScore['highestScore'])) ? $professionAllScore['highestScore'] : 0;
                        $response['yourscore'] = (isset($professionAllScore['yourScore'])) ? $professionAllScore['yourScore'] : 0;
                        $response['rank'] = (isset($professionAllScore['rank'])) ? $professionAllScore['rank'] : 0;
                    } else {
                        $response['highestScore'] = 0;
                        $response['yourscore'] = 0;
                        $response['rank'] = 0;
                    }

                    return view('parent.badgesRankOnClick',compact('response','teenagerId'));
                    exit;
                }
                else
                {
                    // $response['message'] = trans('appmessages.invalid_professionId_msg');
                    // return json_encode($response);
                    return "Error";
                    exit;
                }
            }
            else
            {
                // $response['message'] = trans('appmessages.invalid_professionId_msg');
                // return json_encode($response);
                return "Error";
                exit;
            }
        }
        else
        {
            // $response['message'] = trans('appmessages.invalid_userid_msg') . ' or ' . trans('appmessages.notvarified_user_msg');
            return "Error";
            exit;
        }
    }

    public function getProfessionEducationPath()
    {
        $professionId = Input::get('professionId');
        $professionHeaderDetail = $this->professionsRepository->getProfessionsHeaderByProfessionId($professionId);
       
        $professionName = '';
        if (isset($professionHeaderDetail) && !empty($professionHeaderDetail)) {
            $professionName = $professionHeaderDetail[0]->pf_name;
            if(!empty($professionHeaderDetail[15]->pfic_content)) {
                $profession_acadamic_path = $professionHeaderDetail[15]->pfic_content;
            } else {
                $profession_acadamic_path = '';
            }
        } else {
            $profession_acadamic_path = '';
        }
         
        return view('parent.educationPath',compact('profession_acadamic_path','professionName'));
        exit;
    }

    public function pairWithTeen() {
        return view('parent.inviteTeen');
    }

    public function savePair() {
        $parentFname = Auth::guard('parent')->user()->p_first_name;
        // $parentEmail = Auth::parent()->get()->p_email;
        $parentId = Auth::guard('parent')->user()->id;
        $teenUniqueId = Input::get('p_teenager_reference_id');

        // --------------------start sending mail to teen for pair-----------------------------//
        $teenagerDetail = $this->teenagersRepository->getTeenagerByUniqueId($teenUniqueId);

        if (isset($teenagerDetail) && !empty($teenagerDetail)) {
            $checkPairAvailability = $this->parentsRepository->checkPairAvailability($teenagerDetail->id, $parentId);
            if (isset($checkPairAvailability) && !empty($checkPairAvailability) && count($checkPairAvailability) > 0) {
                if ($checkPairAvailability->ptp_is_verified == 0) {
                    if ($checkPairAvailability->ptp_sent_by == "parent") {
                        $response['message'] = trans('Invitation already sent by you for this teen. Verification link emailed to respected Teen.');
                    } else {
                        $response['message'] = trans('Invitation already sent to you. Verification link emailed by respected Teen. Please, complete verification process.');
                    }
                    return Redirect::to("/parent/pair-with-teen")->with('error', $response['message']);
                    exit;
                } else {
                    $response['message'] = trans('You already paired with this user');
                    return Redirect::to("/parent/pair-with-teen")->with('error', $response['message']);
                    exit;
                }
            } else {
                $replaceArray = array();
                $replaceArray['TEEN_NAME'] = $teenagerDetail->t_name;
                $replaceArray['PARENT_NAME'] = Auth::guard('parent')->user()->p_first_name . ' ' . Auth::guard('parent')->user()->p_last_name;
                $replaceArray['USERTYPE'] = (Auth::guard('parent')->user()->p_user_type == 1)?'Parent':'Counsellor';
                $replaceArray['PARENT_EMAIL'] = Auth::guard('parent')->user()->p_email;
                $replaceArray['PARENT_UNIQUEID'] = Helpers::getParentUniqueId();
                $replaceArray['VERIFICATION_URL'] = url("verify-parent-teen-pair?token=" . $replaceArray['PARENT_UNIQUEID']);

                $emailTemplateContent = $this->templateRepository->getEmailTemplateDataByName(Config::get('constant.PARENT_TEEN_PAIR_FROM_PARENT_SECTION'));

                $content = $this->templateRepository->getEmailContent($emailTemplateContent->et_body, $replaceArray);

                $data = array();
                $data['subject'] = $emailTemplateContent->et_subject;
                $data['toEmail'] = $teenagerDetail->t_email;
                $data['toName'] = $teenagerDetail->t_name;
                $data['content'] = $content;
                $data['ptp_token'] = $replaceArray['PARENT_UNIQUEID'];
                $data['parent_id'] = $parentId;
                $data['parent_token'] = $replaceArray['PARENT_UNIQUEID'];
                $data['teen_id'] = $teenagerDetail->id;

                Mail::send(['html' => 'emails.Template'], $data, function($message) use ($data) {
                            $message->subject($data['subject']);
                            $message->to($data['toEmail'], $data['toName']);

                            // Save parent-teen id in verification table
                            $parentTeenVerificationData['ptp_parent_id'] = $data['parent_id'];
                            $parentTeenVerificationData['ptp_teenager'] = $data['teen_id'];
                            $parentTeenVerificationData['ptp_is_verified'] = 0;
                            $parentTeenVerificationData['ptp_sent_by'] = 'parent';
                            $parentTeenVerificationData['ptp_token'] = $data['parent_token'];

                            $this->teenagersRepository->saveParentTeenVerification($parentTeenVerificationData);
                        });
            }
        } else {
            $response['message'] = trans('appmessages.missing_data_msg');
            return Redirect::to("/parent/pair-with-teen")->with('error', 'Pair with teen doesn\'t happen as invalid teen reference id');
            exit;
        }
        return Redirect::to("/parent/home")->with('success', 'Your invitation has been sent successfully');
        exit;
    }

    public function exportPDF($id = 0) {

        if (empty($id) && $id == 0 && $id == '') {
            $parent_Id = Auth::guard('parent')->user()->id;
            $teenDetails = $this->teenagersRepository->getTeenDetailByParentId($parent_Id);
            if (isset($teenDetails) && !empty($teenDetails)) {
                $teenDetailById = $this->teenagersRepository->getTeenagerById($teenDetails[0]->ptp_teenager);
                $teenUniqueId = $teenDetailById->t_uniqueid;
            }
        } else {
            $teenUniqueId = $id;
        }
        if (isset($teenUniqueId) && $teenUniqueId != '') {
            $teenDetail = $this->teenagersRepository->getTeenagerByUniqueId($teenUniqueId);
        } else {
            return Redirect::to("parent/home")->with('error', 'No data found');
            exit;
        }

        if (isset($teenDetail) && !empty($teenDetail)) {
            $checkuserexist = $this->teenagersRepository->checkActiveTeenager($teenDetail->id);
            if (isset($checkuserexist) && $checkuserexist) {
                //Get all teenager detail
                $parentId = Auth::guard('parent')->user()->id;
                // Get All Verified Teenagers of parent
                $teenagersIds = $this->parentsRepository->getAllVerifiedTeenagers($parentId);
                $finalTeens = array();
                if (isset($teenagersIds) && !empty($teenagersIds)) {
                    foreach ($teenagersIds as $key => $data) {
                        $checkuserexist = $this->teenagersRepository->checkActiveTeenager($data->ptp_teenager);
                        if (isset($checkuserexist) && $checkuserexist) {
                            $teengersDetail = $this->teenagersRepository->getTeenagerById($data->ptp_teenager);
                            $finalTeens[] = array('id' => $teengersDetail->id, 'name' => $teengersDetail->t_name, 'nickname' => $teengersDetail->t_nickname, 'unique_id' => $teengersDetail->t_uniqueid);
                        }
                    }
                }

                //get basic detail

                $teengerDetail = [];
                $url = '';
                $teengerDetail['id'] = $teenDetail->id;
                $teengerDetail['name'] = $teenDetail->t_name;
                $teengerDetail['nickname'] = $teenDetail->t_nickname;
                $teengerDetail['email'] = $teenDetail->t_email;
                $teengerDetail['unique_id'] = $teenDetail->t_uniqueid;
                $photo = $teenDetail->t_photo;
                if (isset($photo) && $photo != '') {
                    $url = Storage::url($this->teenThumbImageUploadPath . $photo);
                } else {
                    $url = Storage::url("frontend/images/proteen_logo.png");
                }
                $teengerDetail['photo'] = $url;

                $response['basicDetail'] = $teengerDetail;

                $boosterPoints = $this->teenagersRepository->getTeenagerBoosterPoints($teenDetail->id);

                $response['booster'] = $boosterPoints;

                //get user attempted level 1 question
                $level1Activity = $this->level1ActivitiesRepository->getLevel1ActivityWithAnswer($teenDetail->id);

                if (isset($level1Activity) && !empty($level1Activity)) {
                    $level1Detail = array();
                    $finalData = array();
                    foreach ($level1Activity as $key => $data) {
                        //Get Trend of Level 1 activity
                        $levelTrend = Helpers::calculateTrendForLevel1($data->activityid,2);

                        $level1Detail['activity_id'] = $data->id;
                        $level1Detail['question_text'] = $data->l1ac_text;
                        $level1Detail['teen_anwer'] = $data->l1op_option;
                        $level1Detail['trend'] = $levelTrend;
                        $finalData[] = $level1Detail;
                    }
                    $response['level1result'] = $finalData;
                }

                //get teenager API data
                $teenagerInterest = array();
                $teenagerApptitude = array();
                $teenagerPersonality = array();
                $teenagerMI = array();
                $finalMIParameters = array();
                $finalSortedData = array();
                $sortedMIHData = array();
                $sortedMIMData = array();
                $sortedMILData = array();
                $teenagerAPIData = Helpers::getTeenAPIScore($teenDetail->id);

                if (isset($teenagerAPIData) && !empty($teenagerAPIData)) {
                    $i = 1;
                    // Teenager interest data
                    foreach ($teenagerAPIData['APIscore']['interest'] as $interest => $val) {
                        if ($val == 1) {
                            $interestImage = Helpers::getInterestData($interest);
                            if (!empty($interestImage)) {
                                if ($interestImage->it_logo != '' && isset($interestImage->it_logo)) {
                                    $image = Storage::url($this->interestOriginalImageUploadPath . $interestImage->it_logo);
                                } else {
                                    $image = Storage::url($this->interestOriginalImageUploadPath . 'proteen-logo.png');
                                }
                            }
                            $teenagerInterest[] = array('image' => $image, 'interest' => $interest);
                        }
                        $i++;
                    }

                    // Teenager Apptitude data
                    $k = 1;

                    foreach ($teenagerAPIData['APIscore']['aptitude'] as $aptitude => $val) {
                        //if ($val != 0) {
                        $aptitudemage = Helpers::getApptitudeData($aptitude);
                        $video = '';
                        $image = '';
                        $info = '';
                        if (!empty($aptitudemage)) {
                            if ($aptitudemage->apt_logo && $aptitudemage->apt_logo != '') {
                                $image = Storage::url($this->apptitudeOriginalImageUploadPath . $aptitudemage->apt_logo);
                            } else {
                                $image = Storage::url($this->apptitudeOriginalImageUploadPath . 'proteen-logo.png');
                            }
                            $video = isset($aptitudemage->apt_video) && ($aptitudemage->apt_video != '') ? Helpers::youtube_id_from_url($aptitudemage->apt_video) : '';
                            $info = $aptitudemage->ap_information;
                        }
                        $aptitudescale = $teenagerAPIData['APIscale']['aptitude'][$aptitude];
                        $teenagerApptitude[] = array('image' => $image, 'aptitude' => $aptitude, 'scale' => $aptitudescale, 'video' => $video, 'info' => $info);
                        //}
                        $k++;
                    }
                    // Teenager MI Data
                    foreach ($teenagerAPIData['APIscore']['MI'] as $mi => $val) {
                        //if ($val != 0) {
                        $video = '';
                        $image = '';
                        $info = '';
                        $miimage = Helpers::getMIData($mi);
                        if (!empty($miimage)) {
                            if ($miimage->mit_logo && $miimage->mit_logo != '') {
                                $image = Storage::url($this->miOriginalImageUploadPath . $miimage->mit_logo);
                            } else {
                                $image = Storage::url($this->miOriginalImageUploadPath . 'proteen-logo.png');
                            }
                            $video = isset($miimage->mi_video) && ($miimage->mi_video != '') ? Helpers::youtube_id_from_url($miimage->mi_video) : '';
                            $info = $miimage->mi_information;
                        }
                        $miscale = $teenagerAPIData['APIscale']['MI'][$mi];
                        $teenagerMI[] = array('image' => $image, 'aptitude' => $mi, 'scale' => $miscale, 'video' => $video, 'info' => $info);
                        //}
                    }
                    // Teenager personality Data
                    foreach ($teenagerAPIData['APIscore']['personality'] as $personality => $val) {
                        //if ($val != 0) {
                        $video = '';
                        $image = '';
                        $info = '';
                        $personalityimage = Helpers::getPersonalityData($personality);
                        if (!empty($personalityimage)) {
                            if ($personalityimage->pt_logo && $personalityimage->pt_logo != '') {
                                $image = Storage::url($this->personalityOriginalImageUploadPath . $personalityimage->pt_logo);
                            } else {
                                $image = Storage::url($this->personalityOriginalImageUploadPath . 'proteen-logo.png');
                            }
                            $video = isset($personalityimage->pt_video) && ($personalityimage->pt_video != '') ? Helpers::youtube_id_from_url($personalityimage->pt_video) : '';
                            $info = $personalityimage->pt_information;
                        }
                        $personalityscale = $teenagerAPIData['APIscale']['personality'][$personality];
                        $teenagerPersonality[] = array('image' => $image, 'aptitude' => $personality, 'scale' => $personalityscale, 'video' => $video, 'info' => $info);
                        //}
                    }
                    $finalMIParameters = array_merge($teenagerApptitude, $teenagerMI, $teenagerPersonality);
                    if (isset($finalMIParameters) && !empty($finalMIParameters)) {
                        foreach ($finalMIParameters as $key => $data) {
                            if ($data['scale'] == 'H') {
                                $sortedMIHData[] = $data;
                            }
                            if ($data['scale'] == 'M') {
                                $sortedMIMData[] = $data;
                            }
                            if ($data['scale'] == 'L') {
                                $sortedMILData[] = $data;
                            }
                        }
                    }
                    $finalSortedData = array_merge($sortedMIHData, $sortedMIMData, $sortedMILData);
                }

                $teenagerMyIcons = array();
                //Get teenager choosen Icon
                $teenagerIcons = $this->teenagersRepository->getTeenagerSelectedIcon($teenDetail->id);
                $relationIcon = array();
                $fictionIcon = array();
                $nonFiction = array();
                if (isset($teenagerIcons) && !empty($teenagerIcons)) {
                    foreach ($teenagerIcons as $key => $icon) {
                        if ($icon->ti_icon_type == 1) {

                            if ($icon->fiction_image != '' && isset($icon->fiction_image)) {
                                $fictionIcon[] = Storage::url($this->cartoonOriginalImageUploadPath . $icon->fiction_image);
                            } else {
                                $fictionIcon[] = Storage::url($this->cartoonOriginalImageUploadPath . 'proteen-logo.png');
                            }
                        } elseif ($icon->ti_icon_type == 2) {
                            if ($icon->nonfiction_image != '' && isset($icon->nonfiction_image)) {
                                $nonFiction[] = Storage::url($this->humanOriginalImageUploadPath . $icon->nonfiction_image);
                            } else {
                                $nonFiction[] = Storage::url($this->humanOriginalImageUploadPath . 'proteen-logo.png');
                            }
                        } else {
                            if ($icon->ti_icon_image != '' && isset($icon->ti_icon_image)) {
                                $relationIcon[] = Storage::url($this->relationIconOriginalImageUploadPath . $icon->ti_icon_image);
                            }
                        }
                    }
                    $teenagerMyIcons = array_merge($fictionIcon, $nonFiction, $relationIcon);
                }
                //Get teenager attempted profession
                $professionArray = $this->objLevel4ProfessionProgress->getTeenAttemptProfessions($teenDetail->id);
                //$professionArray = $this->professionsRepository->getTeenagerAttemptedProfession($teenDetail->id);
                $professionAttempted = array();
                if (isset($professionArray) && !empty($professionArray)) {
                    foreach ($professionArray as $key => $val) {
                        $professionHeaderDetail = $this->professionsRepository->getProfessionsHeaderByProfessionId($val->profession_id);
                        if (isset($professionHeaderDetail) && !empty($professionHeaderDetail)) {
                            if (strpos($professionHeaderDetail[2]->pfic_content, "Salary Range") !== FALSE) {
                                $profession_acadamic_path = substr($professionHeaderDetail[2]->pfic_content, 0, strpos($professionHeaderDetail[2]->pfic_content, 'Salary Range'));
                            } else {
                                $profession_acadamic_path = '';
                            }
                        } else {
                            $profession_acadamic_path = '';
                        }
                        //$yourScore = $idAndRank = 0;
                        $professionAttempted[$key]['name'] = $val->pf_name;
                        $professionAttempted[$key]['profession_id'] = $val->id;
                        $professionAttempted[$key]['profession_acadamic_path'] = str_replace('<strong>Education Path</strong><br />', '', $profession_acadamic_path);
                        //$getTeenagerAllTypeBadges = $this->teenagersRepository->getTeenagerAllTypeBadges($teenDetail->id, $val->id);

                        // $badgesCollection['newbie'] = (isset($getTeenagerAllTypeBadges['level4Basic']['badges'])) ? $getTeenagerAllTypeBadges['level4Basic']['badges'] : '';
                        // $badgesCollection['apprentice'] = (isset($getTeenagerAllTypeBadges['level4Intermediate']['badges'])) ? $getTeenagerAllTypeBadges['level4Intermediate']['badges'] : '';
                        // $badgesCollection['wizard'] = (isset($getTeenagerAllTypeBadges['level4Advance']['badges'])) ? $getTeenagerAllTypeBadges['level4Advance']['badges'] : '';
                        //$professionAttempted[$key]['badges'][] = $badgesCollection;

                        //$totalBadges[$key] = count(array_filter($badgesCollection));
                        // $pData = Helpers::getCompetingUserList($val->profession_id);
                        // $professionAllScore = $pData[$teenDetail->id];
                        // $level4Booster = Helpers::level4Booster($val->id, $teenDetail->id);
                        // if (isset($professionAllScore) && !empty($professionAllScore)) {
                        //     $professionAttempted[$key]['highestScore'] = (isset($professionAllScore['highestScore'])) ? $professionAllScore['highestScore'] : 0;
                        //     $professionAttempted[$key]['yourscore'] = (isset($professionAllScore['yourScore'])) ? $professionAllScore['yourScore'] : 0;
                        //     $professionAttempted[$key]['competitors'] = (isset($professionAllScore['competitors'])) ? $professionAllScore['competitors'] : 0;
                        //     $professionAttempted[$key]['yourRank'] = (isset($professionAllScore['rank'])) ? $professionAllScore['rank'] : 0;
                        //     $professionAttempted[$key]['total'] = (isset($level4Booster) && !empty($level4Booster)) ? $level4Booster['totalPobScore'] : 0;
                        // } else {
                        //     $professionAttempted[$key]['highestScore'] = 0;
                        //     $professionAttempted[$key]['yourscore'] = 0;
                        //     $professionAttempted[$key]['rank'] = 0;
                        // }
                        if (isset($val->pf_logo) && $val->pf_logo != '') {
                            $professionAttempted[$key]['thumb_logo'] = Storage::url($this->professionThumbImageUploadPath . $val->pf_logo);
                        } else {
                            $professionAttempted[$key]['thumb_logo'] = Storage::url($this->professionThumbImageUploadPath . 'proteen-logo.png');
                        }
                        if (isset($val->pf_logo) && $val->pf_logo != '') {
                            $professionAttempted[$key]['orinigal_logo'] = Storage::url($this->professionOriginalImageUploadPath . $val->pf_logo);
                        } else {
                            $professionAttempted[$key]['orinigal_logo'] = Storage::url($this->professionOriginalImageUploadPath . 'proteen-logo.png');
                        }
                    }
                }
                 $professionAttempted2 = $professionAttempted;
                // if (isset($totalBadges) && !empty($totalBadges)) {
                //     arsort($totalBadges);
                //     foreach ($totalBadges as $keyId => $keyValue) {
                //         if (isset($professionAttempted[$keyId])) {
                //             $professionAttempted2[] = $professionAttempted[$keyId];
                //         }
                //     }
                // }

                //Get Promise plus
                $allProPromisePlus = [];
                if (isset($professionArray) && !empty($professionArray)) {

                    $userId = $teenDetail->id;
                    foreach ($professionArray as $key => $pro_val) {
                        $professionId = $pro_val->profession_id;
                        $parentId = Auth::guard('parent')->user()->id;

                        $level4Booster = Helpers::level4Booster($professionId, $userId);
                        $getTeenagerAllTypeBadges = $this->teenagersRepository->getTeenagerAllTypeBadges($userId, $professionId);
                        $totalPoints = 0;
                        if (!empty($getTeenagerAllTypeBadges)) {
                            if ($getTeenagerAllTypeBadges['level4Basic']['noOfAttemptedQuestion'] != 0) {
                                $totalPoints += $getTeenagerAllTypeBadges['level4Basic']['basicAttemptedTotalPoints'];
                            }
                            if ($getTeenagerAllTypeBadges['level4Intermediate']['noOfAttemptedQuestion'] != 0) {
                                foreach ($getTeenagerAllTypeBadges['level4Intermediate']['templateWiseEarnedPoint'] AS $k => $val) {
                                    //if ($getTeenagerAllTypeBadges['level4Intermediate']['templateWiseEarnedPoint'][$k] != 0) {
                                        $totalPoints += $getTeenagerAllTypeBadges['level4Intermediate']['templateWiseTotalAttemptedPoint'][$k];
                                    //}
                                }
                            }
                            if ($getTeenagerAllTypeBadges['level4Advance']['earnedPoints'] != 0) {
                                $totalPoints += $getTeenagerAllTypeBadges['level4Advance']['advanceTotalPoints'];
                            }
                        }
                        $level2Data = '';
                        $level4PromisePlus = 0;
                        $flag = false;
                        if ($totalPoints != 0) {
                            $level4PromisePlus = Helpers::calculateLevel4PromisePlus($level4Booster['yourScore'], $totalPoints);
                            $flag = true;
                        }
                        $PromisePlus = 0;
                        if ($flag) {
                            if ($level4PromisePlus >= Config::get('constant.NOMATCH_MIN_RANGE') && $level4PromisePlus <= Config::get('constant.NOMATCH_MAX_RANGE') ) {
                                $PromisePlus = "nomatch";
                            } else if ($level4PromisePlus >= Config::get('constant.MODERATE_MIN_RANGE') && $level4PromisePlus <= Config::get('constant.MODERATE_MAX_RANGE') ) {
                                $PromisePlus = "moderate";
                            } else if ($level4PromisePlus >= Config::get('constant.MATCH_MIN_RANGE') && $level4PromisePlus <= Config::get('constant.MATCH_MAX_RANGE') ) {
                                $PromisePlus = "match";
                            } else {
                                $PromisePlus = "";
                            }
                        } else {
                             $PromisePlus = "";
                        }
                        $getTeenagerBoosterPoints = $this->teenagersRepository->getTeenagerBoosterPoints($userId);
                        $getAllProfession = $this->professionsRepository->getProfessionsById($professionId);

                        $getLevel2AssessmentResult = Helpers::getTeenAPIScore($userId);
                        $getCareerMappingFromSystem = Helpers::getCareerMappingFromSystem();

                        if (isset($getAllProfession) && !empty($getAllProfession)) {
                            foreach ($getAllProfession as $keyProfession => $professionName) {
                                $pf_name = $professionName->pf_name;
                                $getProfessionIdFromProfessionName = $this->professionsRepository->getProfessionIdByName($professionName->pf_name);
                                if (isset($getProfessionIdFromProfessionName) && $getProfessionIdFromProfessionName > 0) {
                                    $compareLogic = array('HL', 'HM', 'HH', 'ML', 'MM', 'MH', 'LL', 'LM', 'LH');
                                    //FOR COMPARE LOGIC RESULT, L ='nomatch', M = 'moderate', H ='match'
                                    $compareLogicResult = array('L', 'M', 'H', 'L', 'H', 'H', 'H', 'H', 'H');
                                    $value = Helpers::getSpecificCareerMappingFromSystem($getProfessionIdFromProfessionName);

                                    if (!empty($value)) {
                                        $value->tcm_scientific_reasoning = (isset($value->tcm_scientific_reasoning) && $value->tcm_scientific_reasoning != '') ? $value->tcm_scientific_reasoning : 'L';
                                        $value->tcm_verbal_reasoning = (isset($value->tcm_verbal_reasoning) && $value->tcm_verbal_reasoning != '') ? $value->tcm_verbal_reasoning : 'L';
                                        $value->tcm_numerical_ability = (isset($value->tcm_numerical_ability) && $value->tcm_numerical_ability != '') ? $value->tcm_numerical_ability : 'L';
                                        $value->tcm_logical_reasoning = (isset($value->tcm_logical_reasoning) && $value->tcm_logical_reasoning != '') ? $value->tcm_logical_reasoning : 'L';
                                        $value->tcm_social_ability = (isset($value->tcm_social_ability) && $value->tcm_social_ability != '') ? $value->tcm_social_ability : 'L';
                                        $value->tcm_artistic_ability = (isset($value->tcm_artistic_ability) && $value->tcm_artistic_ability != '') ? $value->tcm_artistic_ability : 'L';
                                        $value->tcm_spatial_ability = (isset($value->tcm_spatial_ability) && $value->tcm_spatial_ability != '') ? $value->tcm_spatial_ability : 'L';
                                        $value->tcm_creativity = (isset($value->tcm_creativity) && $value->tcm_creativity != '') ? $value->tcm_creativity : 'L';
                                        $value->tcm_clerical_ability = (isset($value->tcm_clerical_ability) && $value->tcm_clerical_ability != '') ? $value->tcm_clerical_ability : 'L';
                                        $value->tcm_doers_realistic = (isset($value->tcm_doers_realistic) && $value->tcm_doers_realistic != '') ? $value->tcm_doers_realistic : 'L';
                                        $value->tcm_thinkers_investigative = (isset($value->tcm_thinkers_investigative) && $value->tcm_thinkers_investigative != '') ? $value->tcm_thinkers_investigative : 'L';
                                        $value->tcm_creators_artistic = (isset($value->tcm_creators_artistic) && $value->tcm_creators_artistic != '') ? $value->tcm_creators_artistic : 'L';
                                        $value->tcm_helpers_social = (isset($value->tcm_helpers_social) && $value->tcm_helpers_social != '') ? $value->tcm_helpers_social : 'L';
                                        $value->tcm_persuaders_enterprising = (isset($value->tcm_persuaders_enterprising) && $value->tcm_persuaders_enterprising != '') ? $value->tcm_persuaders_enterprising : 'L';
                                        $value->tcm_organizers_conventional = (isset($value->tcm_organizers_conventional) && $value->tcm_organizers_conventional != '') ? $value->tcm_organizers_conventional : 'L';
                                        $value->tcm_linguistic = (isset($value->tcm_linguistic) && $value->tcm_linguistic != '') ? $value->tcm_linguistic : 'L';
                                        $value->tcm_logical = (isset($value->tcm_logical) && $value->tcm_logical != '') ? $value->tcm_logical : 'L';
                                        $value->tcm_musical = (isset($value->tcm_musical) && $value->tcm_musical != '') ? $value->tcm_musical : 'L';
                                        $value->tcm_spatial = (isset($value->tcm_spatial) && $value->tcm_spatial != '') ? $value->tcm_spatial : 'L';
                                        $value->tcm_bodily_kinesthetic = (isset($value->tcm_bodily_kinesthetic) && $value->tcm_bodily_kinesthetic != '') ? $value->tcm_bodily_kinesthetic : 'L';
                                        $value->tcm_naturalist = (isset($value->tcm_naturalist) && $value->tcm_naturalist != '') ? $value->tcm_naturalist : 'L';
                                        $value->tcm_interpersonal = (isset($value->tcm_interpersonal) && $value->tcm_interpersonal != '') ? $value->tcm_interpersonal : 'L';
                                        $value->tcm_intrapersonal = (isset($value->tcm_intrapersonal) && $value->tcm_intrapersonal != '') ? $value->tcm_intrapersonal : 'L';
                                        $value->tcm_existential = (isset($value->tcm_existential) && $value->tcm_existential != '') ? $value->tcm_existential : 'L';

                                        $variable0 = array_keys($compareLogic, $value->tcm_scientific_reasoning . $getLevel2AssessmentResult['APIscale']['aptitude']['Scientific Reasoning']);
                                        $variable1 = array_keys($compareLogic, $value->tcm_verbal_reasoning . $getLevel2AssessmentResult['APIscale']['aptitude']['Verbal Reasoning']);
                                        $variable2 = array_keys($compareLogic, $value->tcm_numerical_ability . $getLevel2AssessmentResult['APIscale']['aptitude']['Numerical Ability']);
                                        $variable3 = array_keys($compareLogic, $value->tcm_logical_reasoning . $getLevel2AssessmentResult['APIscale']['aptitude']['Logical Reasoning']);
                                        $variable4 = array_keys($compareLogic, $value->tcm_social_ability . $getLevel2AssessmentResult['APIscale']['aptitude']['Social Ability']);
                                        $variable5 = array_keys($compareLogic, $value->tcm_artistic_ability . $getLevel2AssessmentResult['APIscale']['aptitude']['Artistic Ability']);
                                        $variable6 = array_keys($compareLogic, $value->tcm_spatial_ability . $getLevel2AssessmentResult['APIscale']['aptitude']['Spatial Ability']);
                                        $variable7 = array_keys($compareLogic, $value->tcm_creativity . $getLevel2AssessmentResult['APIscale']['aptitude']['Creativity']);
                                        $variable8 = array_keys($compareLogic, $value->tcm_clerical_ability . $getLevel2AssessmentResult['APIscale']['aptitude']['Clerical Ability']);

                                        $variable9 = array_keys($compareLogic, $value->tcm_doers_realistic . $getLevel2AssessmentResult['APIscale']['personality']['Mechanical']);
                                        $variable10 = array_keys($compareLogic, $value->tcm_thinkers_investigative . $getLevel2AssessmentResult['APIscale']['personality']['Investigative']);
                                        $variable11 = array_keys($compareLogic, $value->tcm_creators_artistic . $getLevel2AssessmentResult['APIscale']['personality']['Artistic']);
                                        $variable12 = array_keys($compareLogic, $value->tcm_helpers_social . $getLevel2AssessmentResult['APIscale']['personality']['Social']);
                                        $variable13 = array_keys($compareLogic, $value->tcm_persuaders_enterprising . $getLevel2AssessmentResult['APIscale']['personality']['Enterprising']);
                                        $variable14 = array_keys($compareLogic, $value->tcm_organizers_conventional . $getLevel2AssessmentResult['APIscale']['personality']['Conventional']);

                                        $variable15 = array_keys($compareLogic, $value->tcm_linguistic . $getLevel2AssessmentResult['APIscale']['MI']['Linguistic']);
                                        $variable16 = array_keys($compareLogic, $value->tcm_logical . $getLevel2AssessmentResult['APIscale']['MI']['Logical']);
                                        $variable17 = array_keys($compareLogic, $value->tcm_musical . $getLevel2AssessmentResult['APIscale']['MI']['Musical']);
                                        $variable18 = array_keys($compareLogic, $value->tcm_spatial . $getLevel2AssessmentResult['APIscale']['MI']['Spatial']);
                                        $variable19 = array_keys($compareLogic, $value->tcm_bodily_kinesthetic . $getLevel2AssessmentResult['APIscale']['MI']['Bodily-Kinesthetic']);
                                        $variable20 = array_keys($compareLogic, $value->tcm_naturalist . $getLevel2AssessmentResult['APIscale']['MI']['Naturalist']);
                                        $variable21 = array_keys($compareLogic, $value->tcm_interpersonal . $getLevel2AssessmentResult['APIscale']['MI']['Interpersonal']);
                                        $variable22 = array_keys($compareLogic, $value->tcm_intrapersonal . $getLevel2AssessmentResult['APIscale']['MI']['Intrapersonal']);
                                        $variable23 = array_keys($compareLogic, $value->tcm_existential . $getLevel2AssessmentResult['APIscale']['MI']['Existential']);

                                        $arrayCombinePoint[$getProfessionIdFromProfessionName][] = $compareLogicResult[$variable0[0]];
                                        $arrayCombinePoint[$getProfessionIdFromProfessionName][] = $compareLogicResult[$variable1[0]];
                                        $arrayCombinePoint[$getProfessionIdFromProfessionName][] = $compareLogicResult[$variable2[0]];
                                        $arrayCombinePoint[$getProfessionIdFromProfessionName][] = $compareLogicResult[$variable3[0]];
                                        $arrayCombinePoint[$getProfessionIdFromProfessionName][] = $compareLogicResult[$variable4[0]];
                                        $arrayCombinePoint[$getProfessionIdFromProfessionName][] = $compareLogicResult[$variable5[0]];
                                        $arrayCombinePoint[$getProfessionIdFromProfessionName][] = $compareLogicResult[$variable6[0]];
                                        $arrayCombinePoint[$getProfessionIdFromProfessionName][] = $compareLogicResult[$variable7[0]];
                                        $arrayCombinePoint[$getProfessionIdFromProfessionName][] = $compareLogicResult[$variable8[0]];
                                        $arrayCombinePoint[$getProfessionIdFromProfessionName][] = $compareLogicResult[$variable9[0]];
                                        $arrayCombinePoint[$getProfessionIdFromProfessionName][] = $compareLogicResult[$variable10[0]];
                                        $arrayCombinePoint[$getProfessionIdFromProfessionName][] = $compareLogicResult[$variable11[0]];
                                        $arrayCombinePoint[$getProfessionIdFromProfessionName][] = $compareLogicResult[$variable12[0]];
                                        $arrayCombinePoint[$getProfessionIdFromProfessionName][] = $compareLogicResult[$variable13[0]];
                                        $arrayCombinePoint[$getProfessionIdFromProfessionName][] = $compareLogicResult[$variable14[0]];
                                        $arrayCombinePoint[$getProfessionIdFromProfessionName][] = $compareLogicResult[$variable15[0]];
                                        $arrayCombinePoint[$getProfessionIdFromProfessionName][] = $compareLogicResult[$variable16[0]];
                                        $arrayCombinePoint[$getProfessionIdFromProfessionName][] = $compareLogicResult[$variable17[0]];
                                        $arrayCombinePoint[$getProfessionIdFromProfessionName][] = $compareLogicResult[$variable18[0]];
                                        $arrayCombinePoint[$getProfessionIdFromProfessionName][] = $compareLogicResult[$variable19[0]];
                                        $arrayCombinePoint[$getProfessionIdFromProfessionName][] = $compareLogicResult[$variable20[0]];
                                        $arrayCombinePoint[$getProfessionIdFromProfessionName][] = $compareLogicResult[$variable21[0]];
                                        $arrayCombinePoint[$getProfessionIdFromProfessionName][] = $compareLogicResult[$variable22[0]];
                                        $arrayCombinePoint[$getProfessionIdFromProfessionName][] = $compareLogicResult[$variable23[0]];
                                    }
                                }
                            }
                        }

                        $level2Promise = '';
                        if (isset($arrayCombinePoint) && !empty($arrayCombinePoint)) {
                            foreach ($arrayCombinePoint as $key2 => $value) {
                                $point = array_count_values($value);
                                $pingo = $this->professionsRepository->getProfessionsByProfessionId($key2);

                                $L = (isset($point['L'])) ? $point['L'] : 0;
                                $H = (isset($point['H'])) ? $point['H'] : 0;
                                $M = (isset($point['M'])) ? $point['M'] : 0;
                                if ($L > 0) {
                                    $level2Promise = "nomatch";
                                    $level2Data = 'TOUGH & CHALLENGING';
                                } else if ($M > 0 && $L < 1) {
                                    $level2Promise = "moderate";
                                    $level2Data = 'MODERATELY SUITED';
                                } else if ($L == 0 && $M == 0) {
                                    $level2Promise = "match";
                                    $level2Data = 'LIKELY FIT FOR YOU';
                                } else {
                                    $level2Promise = "";
                                }
                            }
                        }

                        $objPromisePlus = new PromisePlus();
                        $promisePlusData = $objPromisePlus->getAllPromisePlus();

                        $L4promisePlus = [];
                        $L4PP = '';
                        $professionFeedback = '';
                        if ($level2Promise == 'nomatch' && $PromisePlus == 'nomatch' ) {
                            $professionFeedback = 0;
                            $L4PP = 1;
                        } else if ($level2Promise == 'nomatch' && $PromisePlus == 'moderate' ) {
                            $professionFeedback = 3;
                        } else if ($level2Promise == 'nomatch' && $PromisePlus == 'match' ) {
                            $professionFeedback = 6;
                        } else if ($level2Promise == 'moderate' && $PromisePlus == 'nomatch' ) {
                            $professionFeedback = 1;
                        } else if ($level2Promise == 'moderate' && $PromisePlus == 'moderate' ) {
                            $professionFeedback = 4;
                            $L4PP = 1;
                        } else if ($level2Promise == 'moderate' && $PromisePlus == 'match' ) {
                            $professionFeedback = 7;
                        } else if ($level2Promise == 'match' && $PromisePlus == 'nomatch' ) {
                            $professionFeedback = 2;
                        } else if ($level2Promise == 'match' && $PromisePlus == 'moderate' ) {
                            $professionFeedback = 5;
                        } else if ($level2Promise == 'match' && $PromisePlus == 'match' ) {
                            $professionFeedback = 8;
                            $L4PP = 1;
                        }
                        if (!empty($promisePlusData)) {
                            if ($PromisePlus != '') {
                                $L4promisePlus[] = $promisePlusData[$professionFeedback];
                            }
                        } else {
                            $L4promisePlus[] = '';
                        }

                        $professionAttempted = [];
                        $objPromisePlus = new PromisePlus();
                        $promiseDetail = $objPromisePlus->getDescriptionofPromisePlus($professionFeedback);
                        $professionAttempted['level2Promise'] = $level2Promise;
                        $professionAttempted['promisePlus'] = $PromisePlus;
                        $professionAttempted['profession_name'] = $pf_name;
                        //$professionAttempted['colorCode'] = $colorCode;
                        //$professionAttempted['L4FeedbackCode'] = $L4PP;
                        $professionAttempted['level2Data'] = $level2Data;
                        $professionAttempted['level4Data'] = $L4promisePlus;
                        $professionAttempted['professionId'] = $professionId;
                        $allProPromisePlus[] = $professionAttempted;
                    }
                }

                //get Learning Style

                $userId = $teenDetail->id;
                $parentId = Auth::guard('parent')->user()->id;

                $professionArray = $this->professionsRepository->getTeenagerAttemptedProfession($userId);

                $finalProfessionArray = [];
                $objLearningStyle = new LearningStyle();

                $userLearningData = $objLearningStyle->getLearningStyleDetails();
                $objProfession =  new Professions();
                $AllProData = $objProfession->getActiveProfessions();

                $TotalAttemptedP = 0;
                $allp = count($AllProData);
                $attemptedp = count($professionArray);
                $TotalAttemptedP = ($attemptedp * 100) / $allp;


                foreach ($userLearningData as $k => $value ) {
                    $userLearningData[$k]->earned_points = 0;
                    $userLearningData[$k]->total_points = 0;
                    $userLearningData[$k]->percentage = '';
                    $userLearningData[$k]->interpretationrange = '';
                    $userLearningData[$k]->totalAttemptedP = round($TotalAttemptedP);
                    $photo = $value->ls_image;
                    if ($photo != '' && isset($photo)) {
                        $value->ls_image = Storage::url($this->learningStyleThumbImageUploadPath . $photo);
                    } else {
                        $value->ls_image = Storage::url("frontend/images/proteen-logo.png");
                    }
                }
                if (isset($professionArray) && !empty($professionArray)) {
                    foreach ($professionArray as $key => $val) {
                        $professionId = $val->id;
                        $getTeenagerAllTypeBadges = $this->teenagersRepository->getTeenagerAllTypeBadges($userId, $professionId);
                        $level4Booster = Helpers::level4Booster($professionId, $userId);
                        $l4BTotal = (isset($getTeenagerAllTypeBadges['level4Basic']) && !empty($getTeenagerAllTypeBadges['level4Basic'])) ? $getTeenagerAllTypeBadges['level4Basic']['basicAttemptedTotalPoints'] : '';
                        $l4ATotal = (isset($getTeenagerAllTypeBadges['level4Advance']) && !empty($getTeenagerAllTypeBadges['level4Advance'])) ? $getTeenagerAllTypeBadges['level4Advance']['earnedPoints'] : '';
                        $UserLerningStyle = [];
                        foreach ($userLearningData as $k => $value ) {
                            $userLData = $objLearningStyle->getLearningStyleDetailsByProfessionId($professionId,$value->parameterId,$userId);
                            if (isset($userLData[0]) && !empty($userLData)) {
                                $points = '';
                                $LAPoints = '';
                                $points = $userLData[0]->uls_earned_points;
                                $userLearningData[$k]->earned_points += $userLData[0]->uls_earned_points;
                                $activityName = $userLData[0]->activity_name;
                                if (strpos($activityName, ',') !== false) {
                                    $Activities = explode(",",$activityName);
                                    foreach ($Activities As $Akey => $acty) {
                                        if ($acty == 'L4B') {
                                            $userLearningData[$k]->total_points += $l4BTotal;
                                        } else if ($acty == 'L4AV') {
                                            if ($l4ATotal != 0) {
                                                $userLearningData[$k]->total_points += Config::get('constant.USER_L4_VIDEO_POINTS');
                                            }
                                        }else if ($acty == 'L4AP') {
                                            if ($l4ATotal != 0) {
                                                $userLearningData[$k]->total_points += Config::get('constant.USER_L4_PHOTO_POINTS');
                                            }
                                        }else if ($acty == 'L4AD') {
                                            if ($l4ATotal != 0) {
                                                $userLearningData[$k]->total_points += Config::get('constant.USER_L4_DOCUMENT_POINTS');
                                            }
                                        } else if ($acty == 'N/A') {
                                            if ($points != 0) {
                                                $userLearningData[$k]->total_points += '';
                                            }
                                        } else {
                                              if ($acty != '' && intval($acty) > 0) {
                                                  $TotalPoints = $getTeenagerAllTypeBadges['level4Intermediate']['templateWiseTotalAttemptedPoint'][$acty];
                                                  $userLearningData[$k]->total_points += $TotalPoints;
                                              }
                                        }
                                    }
                              } else {
                                  if ($activityName == 'L4B') {
                                    $userLearningData[$k]->total_points += $l4BTotal;
                                  } else if ($activityName == 'L4AV') {
                                      if ($l4ATotal != 0) {
                                          $userLearningData[$k]->total_points += Config::get('constant.USER_L4_VIDEO_POINTS');
                                      }
                                  }else if ($activityName == 'L4AP') {
                                      if ($l4ATotal != 0) {
                                          $userLearningData[$k]->total_points += Config::get('constant.USER_L4_PHOTO_POINTS');
                                      }
                                  }else if ($activityName == 'L4AD') {
                                      if ($l4ATotal != 0) {
                                          $userLearningData[$k]->total_points += Config::get('constant.USER_L4_DOCUMENT_POINTS');
                                      }
                                  } else if ($activityName == 'N/A') {
                                      if ($points != 0) {
                                          $userLearningData[$k]->total_points += '';
                                      }
                                  } else {
                                      if (intval($activityName) > 0) {
                                          $TotalPoints = $getTeenagerAllTypeBadges['level4Intermediate']['templateWiseTotalAttemptedPoint'][$activityName];
                                          $userLearningData[$k]->total_points += $TotalPoints;
                                      }
                                  }
                            }
                            if ($userLearningData[$k]->total_points != 0) {
                                $LAPoints = ($value->earned_points * 100) / $userLearningData[$k]->total_points;
                            }
                            $range = '';
                            $LAPoints = round($LAPoints);
                            if ($LAPoints >= 0 && $LAPoints <= 40) {
                                $range = 'Low';
                            } else if ($LAPoints >= 41 && $LAPoints <= 70) {
                                $range = 'Medium';
                            } else if ($LAPoints >= 71 && $LAPoints <= 100) {
                                $range = 'High';
                            }
                            $userLearningData[$k]->interpretationrange = $range;
                            $userLearningData[$k]->percentage = $LAPoints;
                            }
                        }
                    }
                }

                //End Learning Style
                $teenagerAcademic['education'] = array();
                $teenagerAcademic['achievement'] = array();
                $teenager = $teenDetail->id;
                $teenagerMeta = Helpers::getTeenagerMetaData($teenager, 1);
                $teenagerAcademic = Helpers::getTeenagerMetaData($teenager, 2);

                $response['education'] = (isset($teenagerAcademic) && !empty($teenagerAcademic))?$teenagerAcademic['education']:'';
                $response['achievement'] = (isset($teenagerMeta) && !empty($teenagerMeta))?$teenagerMeta['achievement']:'';

                $response['allProPromisePlus'] = $allProPromisePlus;
                $response['userLearningData'] = $userLearningData;
                $response['attempted_profession'] = $professionAttempted2;
                $response['teenagerInterest'] = $teenagerInterest;
                $response['teenagerMI'] = $finalSortedData;
                $response['finalTeens'] = $finalTeens;
                $response['teenagerMyIcons'] = $teenagerMyIcons;
                //$pdf = App::make('dompdf.wrapper');
                $pdf=PDF::loadView('parent.exportParentProgressPDF',$response);
                return $pdf->stream('TeenagerReport.pdf');

            } else {
                Auth::guard('parent')->logout();
                return Redirect::to('/parent');
                exit;
            }
        } else {
            return Redirect::to("parent/home")->with('error', 'No data found');
            exit;
        }
    }

    public function getPromisePlus() {
        if (Auth::guard('parent')->check()) {
            $userId = Input::get('teenId');
            $professionId = Input::get('profession');
            $parentId = Auth::guard('parent')->user()->id;
            $objPaidComponent = new PaidComponent();
            $objDeductedCoins = new DeductedCoins();
            $componentsData = $objPaidComponent->getPaidComponentsData(Config::get('constant.PROMISE_PLUS'));
            $coins = $componentsData->pc_required_coins;
            $deductedCoinsDetail = $objDeductedCoins->getDeductedCoinsDetailById($parentId,$professionId,2,$componentsData->id);
            $days = 0;
            if (!empty($deductedCoinsDetail)) {
                $days = Helpers::calculateRemainingDays($deductedCoinsDetail[0]->dc_end_date);
            }
            if ($days == 0) {
                $deductCoins = 0;
                //deduct coin from user
                $parentDetail = $this->parentsRepository->getParentDataForCoinsDetail($parentId);
                if (!empty($parentDetail)) {
                    $deductCoins = $parentDetail['p_coins']-$coins;
                }
                $returnData = $this->parentsRepository->updateParentCoinsDetail($parentId, $deductCoins);
                $return = Helpers::saveDeductedCoinsData($parentId,2,$coins,Config::get('constant.PROMISE_PLUS'),$professionId);
            }
            

            return view('parent.getPromisePlus',compact('professionAttempted'));
            exit;
        }
        return view('parent.login'); exit;
    }

    public function getLearningStyle() {
        if (Auth::guard('parent')->check()) {
            $userId = Input::get('teenId');
            $parentId = Auth::guard('parent')->user()->id;
            $objPaidComponent = new PaidComponent();
            $objDeductedCoins = new DeductedCoins();
            $componentsData = $objPaidComponent->getPaidComponentsData(Config::get('constant.LEARNING_STYLE'));
            $coins = $componentsData->pc_required_coins;
            $deductedCoinsDetail = $objDeductedCoins->getDeductedCoinsDetailByIdForLS($parentId,$componentsData->id,2);
            $days = 0;
            if (!empty($deductedCoinsDetail->toArray())) {
                $days = Helpers::calculateRemainingDays($deductedCoinsDetail[0]->dc_end_date);
            }
            if ($days == 0) {
                $deductCoins = 0;
                //deduct coin from user
                $parentDetail = $this->parentsRepository->getParentDataForCoinsDetail($parentId);
                if (!empty($parentDetail)) {
                    $deductCoins = $parentDetail['p_coins']-$coins;
                }
                $returnData = $this->parentsRepository->updateParentCoinsDetail($parentId, $deductCoins);

                $return = Helpers::saveDeductedCoinsData($parentId,2,$coins,Config::get('constant.LEARNING_STYLE'),0);
            }
            //Insert all user learning style data
            $professionArray = $this->professionsRepository->getTeenagerAttemptedProfession($userId);

            $objLevel4Answers = new Level4Answers();
            $objProfessionLearningStyle = new ProfessionLearningStyle();
            $objUserLearningStyle = new UserLearningStyle();
            if (isset($professionArray) && !empty($professionArray)) {
                foreach ($professionArray as $key => $proValue) {
                    $professionId = $proValue->id;
                    $level4BasicData = $objLevel4Answers->getLevel4BasicDetailById($userId,$professionId);
                    if (isset($level4BasicData) && !empty($level4BasicData->toArray())) {
                        $templateId = "L4B";
                        $learningId = $objProfessionLearningStyle->getIdByProfessionIdForAdvance($professionId,$templateId);
                        if ($learningId != '') {
                            $userData = [];
                            $userData['uls_learning_style_id'] = $learningId;
                            $userData['uls_profession_id'] = $professionId;
                            $userData['uls_teenager_id'] = $userId;
                            $userData['uls_earned_points'] = $level4BasicData[0]->earned_points;
                            $result = $objUserLearningStyle->saveUserLearningStyle($userData);
                        }
                    }
                    $media = array(1,2,3);
                    for ($i = 0; $i < count($media); $i++) {
                        $level4AdvanceData = $this->level4ActivitiesRepository->getLevel4AdvanceDetailById($userId,$professionId,$media[$i]);
                        $templateId = '';
                        if ($media[$i] == 3) {
                            $templateId = "L4AP";
                        } else if ($media[$i] == 2) {
                            $templateId = "L4AD";
                        } else if ($media[$i] == 1) {
                            $templateId = "L4AV";
                        }
                        $learningId = $objProfessionLearningStyle->getIdByProfessionIdForAdvance($professionId,$templateId);
                        if (isset($level4AdvanceData) && !empty($level4AdvanceData->toArray())) {
                            if ($learningId != '') {
                                $userData = [];
                                $userData['uls_learning_style_id'] = $learningId;
                                $userData['uls_profession_id'] = $professionId;
                                $userData['uls_teenager_id'] = $userId;
                                $userData['uls_earned_points'] = $level4AdvanceData[0]->earned_points;
                                $result = $objUserLearningStyle->saveUserLearningStyle($userData);
                            }
                        }
                    }
                    $level4IntermediateData = $this->level4ActivitiesRepository->getLevel4IntermediateDetailById($userId,$professionId);
                    if (isset($level4IntermediateData) && !empty($level4IntermediateData)) {
                        $dataArr = [];
                        $uniqueArr =[];
                        foreach ($level4IntermediateData AS $key => $value) {
                            if(!in_array($value->l4iaua_template_id, $uniqueArr))
                            {
                                $uniqueArr[] = $value->l4iaua_template_id;
                                $data = [];
                                $data['l4iaua_template_id'] = $value->l4iaua_template_id;
                                $data['l4iaua_earned_point'] = 0;
                                $dataArr[] = $data;
                            }
                        }
                        foreach ($level4IntermediateData AS $key => $value) {
                            foreach ($dataArr As $k => $val) {
                                if ($value->l4iaua_template_id == $val['l4iaua_template_id']){
                                    $dataArr[$k]['l4iaua_earned_point'] += $value->l4iaua_earned_point;
                                }
                            }
                        }
                        for ($j = 0; $j < count($dataArr); $j++) {
                            $templateId = $dataArr[$j]['l4iaua_template_id'];
                            $learningId = $objProfessionLearningStyle->getIdByProfessionId($professionId,$templateId);
                            if ($learningId != '') {
                                $userData = [];
                                $userData['uls_learning_style_id'] = $learningId;
                                $userData['uls_profession_id'] = $professionId;
                                $userData['uls_teenager_id'] = $userId;
                                $userData['uls_earned_points'] = $dataArr[$j]['l4iaua_earned_point'];
                                $result = $objUserLearningStyle->saveUserLearningStyle($userData);
                            }
                        }
                    }
                }
            }

            //get user learning data
            $finalProfessionArray = [];
            $objLearningStyle = new LearningStyle();

            $userLearningData = $objLearningStyle->getLearningStyleDetails();
            $objProfession =  new Professions();
            $AllProData = $objProfession->getActiveProfessions();
            $TotalAttemptedP = 0;
            $allp = count($AllProData);
            $attemptedp = count($professionArray);
            $TotalAttemptedP = ($attemptedp * 100) / $allp;

            if (!empty($userLearningData)) {
                foreach ($userLearningData as $k => $value ) {
                    $userLearningData[$k]->earned_points = 0;
                    $userLearningData[$k]->total_points = 0;
                    $userLearningData[$k]->percentage = '';
                    $userLearningData[$k]->interpretationrange = '';
                    $userLearningData[$k]->totalAttemptedP = round($TotalAttemptedP);
                    $photo = $value->ls_image;
                    if ($photo != '' && isset($photo)) {
                        $value->ls_image = Storage::url($this->learningStyleThumbImageUploadPath . $photo);
                    } else {
                        $value->ls_image = Storage::url("frontend/images/proteen-logo.png");
                    }
                }
                if (isset($professionArray) && !empty($professionArray)) {
                    foreach ($professionArray as $key => $val) {
                        $professionId = $val->id;
                        $getTeenagerAllTypeBadges = $this->teenagersRepository->getTeenagerAllTypeBadges($userId, $professionId);
                        $level4Booster = Helpers::level4Booster($professionId, $userId);
                        $l4BTotal = (isset($getTeenagerAllTypeBadges['level4Basic']) && !empty($getTeenagerAllTypeBadges['level4Basic'])) ? $getTeenagerAllTypeBadges['level4Basic']['basicAttemptedTotalPoints'] : '';
                        $l4ATotal = (isset($getTeenagerAllTypeBadges['level4Advance']) && !empty($getTeenagerAllTypeBadges['level4Advance'])) ? $getTeenagerAllTypeBadges['level4Advance']['earnedPoints'] : '';
                        $UserLerningStyle = [];
                        foreach ($userLearningData as $k => $value ) {
                            $userLData = $objLearningStyle->getLearningStyleDetailsByProfessionId($professionId,$value->parameterId,$userId);
                            if (isset($userLData[0]) && !empty($userLData)) {
                                $points = '';
                                $LAPoints = '';
                                $points = $userLData[0]->uls_earned_points;
                                $userLearningData[$k]->earned_points += $userLData[0]->uls_earned_points;
                                $activityName = $userLData[0]->activity_name;
                                if (strpos($activityName, ',') !== false) {
                                    $Activities = explode(",",$activityName);
                                    foreach ($Activities As $Akey => $acty) {
                                        if ($acty == 'L4B') {
                                            $userLearningData[$k]->total_points += $l4BTotal;
                                        } else if ($acty == 'L4AV') {
                                            if ($l4ATotal != 0) {
                                                $userLearningData[$k]->total_points += Config::get('constant.USER_L4_VIDEO_POINTS');
                                            }
                                        }else if ($acty == 'L4AP') {
                                            if ($l4ATotal != 0) {
                                                $userLearningData[$k]->total_points += Config::get('constant.USER_L4_PHOTO_POINTS');
                                            }
                                        }else if ($acty == 'L4AD') {
                                            if ($l4ATotal != 0) {
                                                $userLearningData[$k]->total_points += Config::get('constant.USER_L4_DOCUMENT_POINTS');
                                            }
                                        } else if ($acty == 'N/A') {
                                            if ($points != 0) {
                                                $userLearningData[$k]->total_points += '';
                                            }
                                        } else {
                                              if ($acty != '' && intval($acty) > 0) {
                                                  $TotalPoints = $getTeenagerAllTypeBadges['level4Intermediate']['templateWiseTotalAttemptedPoint'][$acty];
                                                  $userLearningData[$k]->total_points += $TotalPoints;
                                              }
                                        }
                                    }
                              } else {
                                  if ($activityName == 'L4B') {
                                    $userLearningData[$k]->total_points += $l4BTotal;
                                  } else if ($activityName == 'L4AV') {
                                      if ($l4ATotal != 0) {
                                          $userLearningData[$k]->total_points += Config::get('constant.USER_L4_VIDEO_POINTS');
                                      }
                                  }else if ($activityName == 'L4AP') {
                                      if ($l4ATotal != 0) {
                                          $userLearningData[$k]->total_points += Config::get('constant.USER_L4_PHOTO_POINTS');
                                      }
                                  }else if ($activityName == 'L4AD') {
                                      if ($l4ATotal != 0) {
                                          $userLearningData[$k]->total_points += Config::get('constant.USER_L4_DOCUMENT_POINTS');
                                      }
                                  } else if ($activityName == 'N/A') {
                                      if ($points != 0) {
                                          $userLearningData[$k]->total_points += '';
                                      }
                                  } else {
                                      if (intval($activityName) > 0) {
                                          $TotalPoints = $getTeenagerAllTypeBadges['level4Intermediate']['templateWiseTotalAttemptedPoint'][$activityName];
                                          $userLearningData[$k]->total_points += $TotalPoints;
                                      }
                                  }
                            }

                            if ($userLearningData[$k]->total_points != 0) {
                                $LAPoints = ($value->earned_points * 100) / $userLearningData[$k]->total_points;
                            }
                            $range = '';
                            $LAPoints = round($LAPoints);
                            if ($LAPoints >= 0 && $LAPoints <= 40) {
                                $range = 'Low';
                            } else if ($LAPoints >= 41 && $LAPoints <= 70) {
                                $range = 'Medium';
                            } else if ($LAPoints >= 71 && $LAPoints <= 100) {
                                $range = 'High';
                            }
                            $userLearningData[$k]->interpretationrange = $range;
                            $userLearningData[$k]->percentage = $LAPoints;
                            }
                        }
                    }
                }
            }
            return view('parent.getLearningStyle',compact('userLearningData'));
            exit;
        }
        return view('parent.login'); exit;
    }

    function purchasedCoinsToViewReport() {
        if (Auth::guard('parent')->check()) {
            $parentId = Input::get('parentId');
            $objPaidComponent = new PaidComponent();
            $componentsData = $objPaidComponent->getPaidComponentsData('Parent Report');
            $coins = $componentsData->pc_required_coins;
            $objDeductedCoins = new DeductedCoins();
            $deductedCoinsDetail = $objDeductedCoins->getDeductedCoinsDetailByIdForLS($parentId,$componentsData->id,2);
            $days = 0;
            if (isset($deductedCoinsDetail[0]) && !empty($deductedCoinsDetail)) {
                $days = Helpers::calculateRemainingDays($deductedCoinsDetail[0]->dc_end_date);
            }
            if ($days == 0) {
                $deductedCoins = $coins;
                $parentDetail = $this->parentsRepository->getParentDataForCoinsDetail($parentId);
                if (!empty($parentDetail)) {
                    if ($parentDetail['p_coins'] < $coins) {
                        return $parentDetail['p_coins'];
                        exit;
                    }
                    $coins = $parentDetail['p_coins']-$coins;
                }
                $result = $this->parentsRepository->updateParentCoinsDetail($parentId, $coins);
                $return = Helpers::saveDeductedCoinsData($parentId,2,$deductedCoins,'Parent Report',0);
            }
            return "1";
            exit;
        }
        return view('parent.login'); exit;
    }

    public function saveTeenPromiseRate() {
        $teenId = Input::get('teenId');
        $parentId = Input::get('parentId');
        $data = Input::get('form_data');
        $key = Input::get('key');

        $assesmentData = array();
        parse_str($data,$assesmentData);

        $objMultipleIntelligent = new MultipleIntelligent();
        $objApptitude = new Apptitude();
        $objPersonality = new Personality();
        $objLevel2ParentsActivity = new Level2ParentsActivity();

        if(isset($assesmentData) && !empty($assesmentData)) {
            if (isset($assesmentData['mi_data']['apptitude']) && !empty($assesmentData['mi_data']['apptitude'])) {
                foreach ($assesmentData['mi_data']['apptitude'] AS $key => $value) {
                    $saveParentLevel2Data = [];
                    $saveParentLevel2Data['l2pac_teenager_id'] = $teenId;
                    $saveParentLevel2Data['l2pac_parent_id']  = $parentId;
                    $saveParentLevel2Data['l2pac_value'] = $value;
                    $saveParentLevel2Data['l2pac_type'] = 'apptitude';
                    $rateId = $objApptitude->getApptitudeDataIdByName($key);
                    $saveParentLevel2Data['l2pac_rate_id'] = $rateId;
                    $return = $objLevel2ParentsActivity->saveLevel2ParentsActivity($saveParentLevel2Data);
                }
            }
            if (isset($assesmentData['mi_data']['mi']) && !empty($assesmentData['mi_data']['mi'])) {
                foreach ($assesmentData['mi_data']['mi'] AS $key => $value) {
                    $saveParentLevel2Data = [];
                    $saveParentLevel2Data['l2pac_teenager_id'] = $teenId;
                    $saveParentLevel2Data['l2pac_parent_id']  = $parentId;
                    $saveParentLevel2Data['l2pac_value'] = $value;
                    $saveParentLevel2Data['l2pac_type'] = 'mi';
                    $rateId = $objMultipleIntelligent->getMultipleIntelligentIdByName($key);
                    $saveParentLevel2Data['l2pac_rate_id'] = $rateId;
                    $return = $objLevel2ParentsActivity->saveLevel2ParentsActivity($saveParentLevel2Data);
                }
            }
            if (isset($assesmentData['mi_data']['personality']) && !empty($assesmentData['mi_data']['personality'])) {
                foreach ($assesmentData['mi_data']['personality'] AS $key => $value) {
                    $saveParentLevel2Data = [];
                    $saveParentLevel2Data['l2pac_teenager_id'] = $teenId;
                    $saveParentLevel2Data['l2pac_parent_id']  = $parentId;
                    $saveParentLevel2Data['l2pac_value'] = $value;
                    $saveParentLevel2Data['l2pac_type'] = 'personality';
                    $rateId = $objPersonality->getPersonalityDataIdByName($key);
                    $saveParentLevel2Data['l2pac_rate_id'] = $rateId;
                    $return = $objLevel2ParentsActivity->saveLevel2ParentsActivity($saveParentLevel2Data);
                }
            }
        }

        //return view('parent.getPromiseAssessmentData',compact('MIData','key'));
        return "1";
       //return view('parent.getPromiseAssessment',compact('rate','teenId','parentId','name','type','key'));
    }

    public function getTeenPromiseRateCount() {
        $teenId = Input::get('teenId');
        $parentId = Input::get('parentId');

        $objLevel2ParentsActivity = new Level2ParentsActivity();
        $return = $objLevel2ParentsActivity->getTeenPromiseRateCount($teenId,$parentId);

        if (isset($return) && (count($return) == Config::get('constant.PROMISE_ASSESSMENT_COUNT'))) {
            return "0";
        } else {
            return "1";
        }

    }

    public function getTeenLevel2Activity() {
        $teenId = Input::get('teenId');
        $parentId = Input::get('parentId');
        $name = Input::get('name');
        $type = Input::get('type');

        $teenagerAPIData = Helpers::getTeenAPIScore($teenId);

        $scale = '';
        if ($type == 'apptitude') {
            foreach ($teenagerAPIData['APIscore']['aptitude'] as $aptitude => $val) {
                if ($name == $aptitude) {
                    $scale = $teenagerAPIData['APIscale']['aptitude'][$aptitude];
                    return $scale;
                }
            }
        } else if ($type == 'mi') {
            foreach ($teenagerAPIData['APIscore']['MI'] as $mi => $val) {
                if ($name == $mi) {
                    $scale = $teenagerAPIData['APIscale']['MI'][$mi];
                    return $scale;
                }
            }
        } else if ($type == 'personality') {
            foreach ($teenagerAPIData['APIscore']['personality'] as $personality => $val) {
                if ($name == $personality) {
                    $scale = $teenagerAPIData['APIscale']['personality'][$personality];
                    return $scale;
                }
            }
        }
    }

    public function showCompetitorData() {
        $parentid = Input::get('parentId');
        $profession_id = Input::get('profession_id');
        $teenId = Input::get('teenId');
        $profession_name = '';
        $getProfessionNameFromProfessionId = $this->professionsRepository->getProfessionsByProfessionId($profession_id);
        if (isset($getProfessionNameFromProfessionId[0]) && !empty($getProfessionNameFromProfessionId[0])) {
            $profession_name = $getProfessionNameFromProfessionId[0]->pf_name;
       }

        $level4Booster = Helpers::level4Booster($profession_id, $teenId);

        $level4ParentBooster = Helpers::level4ParentBooster($profession_id, $parentid);

        $teenDetail = $this->teenagersRepository->getTeenagerByTeenagerId($teenId);

        $parentDetail = $this->parentsRepository->getParentDetailByParentId($parentid);

        $rank = 0;
        foreach($level4ParentBooster['allData'] AS $key => $value) {
            if ($level4Booster['yourScore'] != 0) {
              if ($level4Booster['yourScore'] == $value) {
                $rank = $key+1;
              }
            } else{
                $rank = 0;
            }
        }

        return view('parent.showCompetitorData', compact('level4Booster','level4ParentBooster','profession_name','teenDetail','parentDetail','rank'));
    }

    /* Request Params : getInterestDetail
     *  Param : teenagerId
     */
    public function getTeenagerInterestDetails(Request $request) {
        $response = [ 'status' => 0, 'message' => trans('appmessages.default_error_msg') ] ;
        $teenager = $this->teenagersRepository->getTeenagerById($request->teenagerId);
        if($teenager) 
        {            
            $teenagerInterest = $arraypromiseParametersMaxScoreBySlug = [];                        
            //Get Max score for MI parameters
            $promiseParametersMaxScore = $this->objPromiseParametersMaxScore->getPromiseParametersMaxScore();
            $arraypromiseParametersMaxScore = $promiseParametersMaxScore->toArray();
            foreach($arraypromiseParametersMaxScore as $maxkey=>$maxVal){
                $arraypromiseParametersMaxScoreBySlug[$maxVal['parameter_slug']] = $maxVal;
            }            
            //Get teenager promise score 
            $teenPromiseScore = $this->objTeenagerPromiseScore->getTeenagerPromiseScore($request->teenagerId);
            if(isset($teenPromiseScore) && count($teenPromiseScore) > 0)
            {
                $teenPromiseScore = $teenPromiseScore->toArray();                                
                foreach($teenPromiseScore as $paramkey=>$paramvalue)
                {                 
                    $arr = explode("_", $paramkey);
                    $first = $arr[0];
                    if ($first == 'it')
                    {
                        if($paramvalue < 1)
                        {
                            continue;
                        }
                        $teenAptScore = $this->getTeenScoreInPercentage($arraypromiseParametersMaxScoreBySlug[$paramkey]['parameter_max_score'], $paramvalue);
                        $teenagerInterest[$paramkey] = (array('type' => 'interest', 'score' => $teenAptScore, 'slug' => $paramkey, 'link' => url('teenager/interest/').'/'.$paramkey, 'name' => $arraypromiseParametersMaxScoreBySlug[$paramkey]['parameter_name']));
                    }
                }
            }else{
                $response['message'] = "Please attempt atleast one section of Profile Builder to view your interest!";
            }
            return view('parent.teenagerInterest', compact('teenagerInterest'));
            exit;                        
        } else {
            $response['message'] = "Something went wrong!";
        }
        return response()->json($response, 200);
        exit;
    }

    public function getTeenScoreInPercentage($maxScore, $teenScore) 
    {
        if ($teenScore > $maxScore) {
            $teenScore = $maxScore;
        }
        $mul = 100*$teenScore;
        $percentage = $mul/$maxScore;
        return round($percentage);
    }

    public function getTeenagerStrengthDetails(Request $request){
         $response = [ 'status' => 0, 'message' => trans('appmessages.default_error_msg') ] ;
        $teenager = $this->teenagersRepository->getTeenagerById($request->teenagerId);        
        $parentId = Auth::guard('parent')->user()->id;
        if($teenager) {
            $teenagerStrength = $arraypromiseParametersMaxScoreBySlug = $sortedMIHData = $sortedMIMData = $sortedMILData = [];
            
            //Get Max score for MI parameters
            $promiseParametersMaxScore = $this->objPromiseParametersMaxScore->getPromiseParametersMaxScore();
            $arraypromiseParametersMaxScore = $promiseParametersMaxScore->toArray();
            foreach($arraypromiseParametersMaxScore as $maxkey=>$maxVal){
                $arraypromiseParametersMaxScoreBySlug[$maxVal['parameter_slug']] = $maxVal;
            }
            
            //Get teenager promise score 
            $teenPromiseScore = $this->objTeenagerPromiseScore->getTeenagerPromiseScore($request->teenagerId);
            if(isset($teenPromiseScore) && count($teenPromiseScore) > 0)
            {
                $teenPromiseScore = $teenPromiseScore->toArray();  
                
                foreach($teenPromiseScore as $paramkey=>$paramvalue)
                {                    
                    if (strpos($paramkey, 'apt_') !== false) { 
                        $scaleapt = $this->objApptitudeScale->calculateApptitudeHML($arraypromiseParametersMaxScoreBySlug[$paramkey]['parameter_name'], $paramvalue);
                        $teenAptScore = $this->getTeenScoreInPercentage($arraypromiseParametersMaxScoreBySlug[$paramkey]['parameter_max_score'], $paramvalue);

                        $objApptitude = new Apptitude();
                        $aptDetails = $objApptitude->getApptitudeDetailBySlug($paramkey);
                        $objLevel2ParentsActivity = new Level2ParentsActivity();
                        $level2AptActivityData = $objLevel2ParentsActivity->getLevel2ParentsActivity($aptDetails->id, $parentId,$teenager->id, 'apptitude');
                        $parentScale = '';
                        if (!empty($level2AptActivityData) && count($level2AptActivityData) > 0) {
                            $parentScale = $level2AptActivityData[0]['l2pac_value'];
                        }
                        $teenagerStrength[] = (array('scale'=>$scaleapt,'slug' => $paramkey, 'score' => $teenAptScore, 'points' => $paramvalue, 'name' => $arraypromiseParametersMaxScoreBySlug[$paramkey]['parameter_name'], 'type' => Config::get('constant.APPTITUDE_TYPE'), 'link_url' => url('/teenager/multi-intelligence/').'/'.Config::get('constant.APPTITUDE_TYPE').'/'.$paramkey, 'parentScale' => $parentScale));
                    }elseif(strpos($paramkey, 'pt_') !== false){
                        $scalept = $this->objPersonalityScale->calculatePersonalityHML($arraypromiseParametersMaxScoreBySlug[$paramkey]['parameter_name'], $paramvalue);
                        $teenAptScore = $this->getTeenScoreInPercentage($arraypromiseParametersMaxScoreBySlug[$paramkey]['parameter_max_score'], $paramvalue);
                        $objPersonality = new Personality();
                        $ptDetails = $objPersonality->getPersonalityDetailBySlug($paramkey);
                        $objLevel2ParentsActivity = new Level2ParentsActivity();
                        $level2PActivityData = $objLevel2ParentsActivity->getLevel2ParentsActivity($ptDetails->id, $parentId,$teenager->id, 'personality');
                        $ptParentScale = '';
                        if (!empty($level2PActivityData) && count($level2PActivityData) > 0) {
                            $ptParentScale = $level2PActivityData[0]['l2pac_value'];
                        }
                        $teenagerStrength[] = (array('scale'=>$scalept,'slug' => $paramkey, 'score' => $teenAptScore, 'points' => $paramvalue, 'name' => $arraypromiseParametersMaxScoreBySlug[$paramkey]['parameter_name'], 'type' => Config::get('constant.PERSONALITY_TYPE'), 'link_url' => url('/teenager/multi-intelligence/').'/'.Config::get('constant.PERSONALITY_TYPE').'/'.$paramkey, 'parentScale' => $ptParentScale));
                    }elseif(strpos($paramkey, 'mit_') !== false){
                        $scalemi = $this->objMIScale->calculateMIHML($arraypromiseParametersMaxScoreBySlug[$paramkey]['parameter_name'], $paramvalue);
                        $teenAptScore = $this->getTeenScoreInPercentage($arraypromiseParametersMaxScoreBySlug[$paramkey]['parameter_max_score'], $paramvalue);
                        $objMultipleIntelligent = new MultipleIntelligent();
                        $miDetails = $objMultipleIntelligent->getMultipleIntelligenceDetailBySlug($paramkey);
                        $objLevel2ParentsActivity = new Level2ParentsActivity();
                        $level2MIActivityData = $objLevel2ParentsActivity->getLevel2ParentsActivity($miDetails->id, $parentId,$teenager->id, 'mi');
                        $miParentScale = '';
                        if (!empty($level2MIActivityData) && count($level2MIActivityData) > 0) {
                            $miParentScale = $level2MIActivityData[0]['l2pac_value'];
                        }
                        $teenagerStrength[] = (array('scale'=>$scalemi,'slug' => $paramkey, 'score' => $teenAptScore, 'points' => $paramvalue, 'name' => $arraypromiseParametersMaxScoreBySlug[$paramkey]['parameter_name'], 'type' => Config::get('constant.MULTI_INTELLIGENCE_TYPE'), 'link_url' => url('/teenager/multi-intelligence/').'/'.Config::get('constant.MULTI_INTELLIGENCE_TYPE').'/'.$paramkey, 'parentScale' => $miParentScale));
                    }
                }
            }else{
                $response['message'] = "Please attemp atleast one section of Profile Builder to view your strength!";
            }
            $finalSortedData = [];
            if (isset($teenagerStrength) && !empty($teenagerStrength)) {
                foreach ($teenagerStrength as $key => $data) {
                    if ($data['scale'] == 'H') {
                        $sortedMIHData[] = $data;
                    }
                    if ($data['scale'] == 'M') {
                        $sortedMIMData[] = $data;
                    }
                    if ($data['scale'] == 'L') {
                        $sortedMILData[] = $data;
                    }
                }
                $teenagerStrength = array_merge($sortedMIHData, $sortedMIMData, $sortedMILData);
            }
            return view('parent.teenagerStrength', compact('teenagerStrength'));
            exit;
        } else {
            $response['message'] = "Something went wrong!";
        }
        return response()->json($response, 200);
        exit;
    }

    /* @getActivityTimelineDetails
     * params: teenagerId
     * Returns an array of teenager activity details
     */
    public function getActivityTimelineDetails()
    {
        $teenId = Input::get('teenagerId');
        $parentId = Auth::guard('parent')->user()->id;
        $timeLine = Helpers::getTeenagerTimeLine($teenId, $parentId);
        return view("parent.basic.progressActivityTimeLineData", compact('teenId', 'timeLine'));
    }

}
