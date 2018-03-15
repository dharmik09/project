<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use Auth;
use Input;
use Redirect;
use Excel;
use Config;
use Helpers;
use App\Services\Schools\Contracts\SchoolsRepository;
use App\Services\Teenagers\Contracts\TeenagersRepository;
use App\Services\Template\Contracts\TemplatesRepository;
use App\Services\Level1Activity\Contracts\Level1ActivitiesRepository;
use App\Services\Professions\Contracts\ProfessionsRepository;
use Mail;
use PDF;
use App\PaidComponent;
use App\DeductedCoins;
use App\TeenagerCoinsGift;
use Illuminate\Support\Facades\Storage;

class DashboardController extends Controller {

    public function __construct(SchoolsRepository $schoolsRepository,Level1ActivitiesRepository $level1ActivitiesRepository, TeenagersRepository $teenagersRepository, TemplatesRepository $templatesRepository,ProfessionsRepository $professionsRepository) {
        $this->schoolsRepository = $schoolsRepository;
        $this->teenagersRepository = $teenagersRepository;
        $this->templateRepository = $templatesRepository;
        $this->level1ActivitiesRepository = $level1ActivitiesRepository;
        $this->professionThumbImageUploadPath = Config::get('constant.PROFESSION_THUMB_IMAGE_UPLOAD_PATH');
        $this->cartoonThumbImageUploadPath = Config::get('constant.CARTOON_THUMB_IMAGE_UPLOAD_PATH');
        $this->schoolOriginalImageUploadPath = Config::get('constant.SCHOOL_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->cartoonOriginalImageUploadPath = Config::get('constant.CARTOON_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->humanThumbImageUploadPath = Config::get('constant.HUMAN_THUMB_IMAGE_UPLOAD_PATH');
        $this->humanOriginalImageUploadPath = Config::get('constant.HUMAN_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->relationIconOriginalImageUploadPath = Config::get('constant.RELATION_ICON_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->relationIconThumbImageUploadPath = Config::get('constant.RELATION_ICON_THUMB_IMAGE_UPLOAD_PATH');
        $this->professionsRepository = $professionsRepository;
        $this->loggedInUser = Auth::guard('school');
    }

    public function index() {

        $teenDetailSchoolWise = array();
        $currentPage = 1;
        if (Auth::guard('school')->check()) {
            if((\Session::has('currentPage'))){
                $currentPage = \Session::get('currentPage'); 
            }
            $school_id = $this->loggedInUser->user()->id;
            $schoolData = $this->schoolsRepository->getSchoolDataForCoinsDetail($school_id);
            $finalEmailArr = array();
            $teenDetailSchoolWise = $this->teenagersRepository->getActiveSchoolStudentsDetail($school_id);

            $emailDetails = $this->teenagersRepository->getEmailDataOfStudent($school_id);
            if(!empty($emailDetails) && $emailDetails->count() > 0){
                foreach ($emailDetails as $data) {
                    $userid = $data->id;
                    $email = $data->t_email;
                    $checkIfMailSent = $this->teenagersRepository->checkMailSentOrNot($userid);
                    if (empty($checkIfMailSent)) {
                        $finalEmailArr[] = $email;
                    }
                }
            }

            if(!empty($teenDetailSchoolWise) && $teenDetailSchoolWise->count() > 0){
                foreach ($teenDetailSchoolWise as $info) {
                    $info->email_sent = (in_array($info->t_email, $finalEmailArr))? "no":"yes";
                }
            }
            $totalPage = $teenDetailSchoolWise->lastPage();
            \Session::forget('currentPage');
            return view('school.dashboard', compact('teenDetailSchoolWise', 'finalEmailArr','schoolData','currentPage','totalPage'));
        } else {
            return view('school.login');
        }
    }

    public function bulkImport() {
        $schoolOriginalImagePath = Config::get('constant.SCHOOL_ORIGINAL_IMAGE_UPLOAD_PATH');
        return view('school.addSchoolBulk',compact('schoolOriginalImagePath'));
    }

    public function savebulkdata() {
        $response = '';
        $emailList = array();
        $school = Input::file('school_bulk');
        $ext = $school->getClientOriginalExtension();
        if ($ext == 'xls' or $ext == 'xlsx') {
            \Session::put('import', '0');
            Excel::selectSheetsByIndex(0)->load($school, function($reader) {
                foreach($reader->toArray() as $row) {
                    if(filter_var($row['student_email'], FILTER_VALIDATE_EMAIL))
                    {
                        if(isset($row['student_email']) && $row['student_email'] != '')
                        {
                            $row['student_email'] = trim($row['student_email']);
                            \Session::put('import', '1');
                            $activeEmail = $this->teenagersRepository->checkActiveEmailExist($row['student_email']);
    //                        if ($activeEmail == 1) {
    //                            $emailList[] = $row['student_email'];
    //                            \Session::put('email', $emailList);
    //                        } else if ($activeEmail == 0) {
                                if ($row['student_name'] != '') {
                                    $schoolDetail = [];
                                    $schoolDetail['t_name'] = $row['student_name'];
                                    $schoolDetail['t_nickname'] = $row['nick_name'];
                                    $schoolDetail['t_email'] = $row['student_email'];
                                    $schoolDetail['t_birthdate'] = $row['birthdate'];
                                    if ($row['gender'] == "Male") {
                                        $gender = 1;
                                    } else {
                                        $gender = 2;
                                    }
                                    $schoolDetail['t_gender'] = $gender;
                                    $schoolDetail['t_school'] = $this->loggedInUser->user()->id;
                                    $schoolDetail['t_rollnum'] = $row['roll_no'];
                                    $schoolDetail['t_class'] = $row['class'];
                                    $schoolDetail['t_division'] = $row['division'];
                                    $schoolDetail['t_medium'] = $row['medium'];
                                    $schoolDetail['t_academic_year'] = $row['academic_year'];
                                    $schoolDetail['t_school_status'] = 1;
                                    //$schoolDetail['t_uniqueid'] = uniqid("", TRUE);
                                    //$schoolDetail['t_isverified'] = 0;
                                    //$schoolDetail['t_social_provider'] = 'Normal';
                                    //$schoolDetail['t_sponsor_choice'] = 3;
                                    $response = $this->schoolsRepository->saveSchoolBulkDetail($schoolDetail);
                                }
                           // }
                        }
                    }
                    //Store invalid emails that are not imported
                    else{
                         $emailList[] = $row['student_email'];
                         \Session::put('invalidemails', $emailList);
                    }
                }
            });
            if(\Session::get('import') == 1){
               return Redirect::to("school/home")->with('success', 'Users imported successfully...');
               exit;
            }else{
               return Redirect::to("school/bulk-import")->with('error', 'Invalid data in excel file...');
               exit;
            }
        } else {
            return Redirect::to("school/bulk-import")->with('error', 'Invalid file type...');
            exit;
        }
    }

    public function inactive($id,$status) {
        $response = $this->schoolsRepository->inactiveRecord($id,$status);
        if ($response) {
            return Redirect::to("school/home");
        } else {
            return Redirect::to("school/home");
        }
    }

    public function sendemail() {
        $a = 0;
        
        $emaildata = Input::get('email');
        if(!empty($emaildata) && count($emaildata) > 0)
        {
          foreach ($emaildata as $key => $value) {
              $teenagerDetailbyEmail = $this->teenagersRepository->getTeenagerDetailByEmailId($value);
              
              if (!empty($teenagerDetailbyEmail) && $teenagerDetailbyEmail->count() > 0) {
                  // --------------------start sending mail -----------------------------//
                  $password = str_random(10);
                  $replaceArray = array();
                  $replaceArray['VERIFICATION_LINK'] = "<a href=" . url("verifyTeenfromSchool?token=" . $teenagerDetailbyEmail['t_uniqueid']) . ">" . url("verifyTeenfromSchool?token=" . $teenagerDetailbyEmail['t_uniqueid']) . "</a>";
                  $replaceArray['SCHOOL_NAME'] = $this->loggedInUser->user()->sc_name;
                  $replaceArray['EMAIL'] = $value;
                  $replaceArray['PASSWORD'] = $password;
                  $emailTemplateContent = $this->templateRepository->getEmailTemplateDataByName(Config::get('constant.STUDENT_BULK_IMPORT'));
                  $content = $this->templateRepository->getEmailContent($emailTemplateContent->et_body, $replaceArray);
                  $data = array();
                  $data['subject'] = $emailTemplateContent->et_subject;
                  $data['toEmail'] = $value;
                  $data['content'] = $content;
                  $data['tev_token'] = $teenagerDetailbyEmail['t_uniqueid'];
                  $data['teen_id'] = $teenagerDetailbyEmail['id'];

                  Mail::send(['html' => 'emails.Template'], $data, function($message) use ($data) {
                      $message->subject($data['subject']);
                      $message->to($data['toEmail']);
                      $teenagerTokenDetail = [];
                      $teenagerTokenDetail['tev_token'] = $data['tev_token'];
                      $teenagerTokenDetail['tev_teenager'] = $data['teen_id'];
                      $this->teenagersRepository->addTeenagerEmailVarifyToken($teenagerTokenDetail);
                  });


                  $savePassword = bcrypt($password);
                  $saveTeenPassword = $this->teenagersRepository->savePassword($savePassword, $teenagerDetailbyEmail['t_uniqueid']);
              } else {
                  return Redirect::to("school/home")->with('error', 'Email Address does not Exist...');
              }
          }
          //Set current page when user sent an email
          $nextPage = Input::get('page')+1;
          if($nextPage >= Input::get('totalPage')){
              $currentPage = Input::get('totalPage');
          }else{
              $currentPage = $nextPage;
          }
          \Session::put('currentPage', $currentPage);
          
          return Redirect::to("school/home")->with('success', 'Mail sent successfully...');
        }
        else
        {
            return Redirect::to("school/home")->with('error', 'Email Address does not Exist...');
        }
    }

    public function exportPDF($id = 0) {
        $schoolid = $this->loggedInUser->user()->id;
        $objDeductedCoins = new DeductedCoins();
        $objTeenagerCoinsGift = new TeenagerCoinsGift();

        if (empty($id) && $id == 0 && $id == '') {
            $classid = $this->schoolsRepository->getFirstClassDetail($schoolid);
            $id = (isset($classid->t_class) && $classid->t_class != '')?$classid->t_class:'';
        } else {
            $id = $id;
        }
        if (isset($id) && !empty($id)) {
            $classDetails = $this->schoolsRepository->getClassDetail($schoolid);
            $teenDetailsForLevel1 = $this->schoolsRepository->getStudentForLevel1($schoolid, $id);
            $teenDetailsForLevel2 = $this->schoolsRepository->getStudentForLevel2($schoolid, $id);
            $teenDetailsForLevel3 = $this->schoolsRepository->getStudentForLevel3($schoolid, $id);
            $teenDetailsForLevel4 = $this->schoolsRepository->getStudentForLevel4($schoolid, $id);
            $professionAttempted = $this->schoolsRepository->getAttemptedProfession($schoolid, $id);
        } else {
            return Redirect::to("school/home")->with('error', 'No data found');
            exit;
        }

        foreach ($professionAttempted['profession'] As $key => $value) {
            $image = $value->pf_logo;
            if ($image != '' && isset($image)) {
                $image = Storage::url($this->professionThumbImageUploadPath . $image);
            } else {
                $image = Storage::url($this->professionThumbImageUploadPath . 'proteen-logo.png');
            }
            $value->pf_logo = $image;
            $professionHeaderDetail = $this->professionsRepository->getProfessionsHeaderByProfessionId($value->id);
            if (isset($professionHeaderDetail) && !empty($professionHeaderDetail)) {
                if (strpos($professionHeaderDetail[2]->pfic_content, "Salary Range") !== FALSE) {
                    $profession_acadamic_path = substr($professionHeaderDetail[2]->pfic_content, 0, strpos($professionHeaderDetail[2]->pfic_content, 'Salary Range'));
                } else {
                    $profession_acadamic_path = '';
                }
            } else {
                $profession_acadamic_path = '';
            }
            $value->profession_acadamic_path = str_replace('<strong>Education Path</strong><br />', '', $profession_acadamic_path);;
        }

        $finalEmailArr = array();
        $teenDetailSchoolWise = $this->teenagersRepository->getActiveSchoolStudentsDetail($schoolid);
        $emailDetails = $this->teenagersRepository->getEmailDataOfStudent($schoolid);
        if(!empty($emailDetails)){
            foreach ($emailDetails as $data) {
                $userid = $data->id;
                $email = $data->t_email;
                $checkIfMailSent = $this->teenagersRepository->checkMailSentOrNot($userid);
                if (empty($checkIfMailSent)) {
                    $finalEmailArr[] = $email;
                }
            }
        }

        $studentData = [];
        foreach ($teenDetailSchoolWise as $key => $value) {
            if ($value->t_class == $id) {
                $studentData[] = $value;
            }
        }

        $level1Questions = $this->level1ActivitiesRepository->getLevel1AllActiveQuestion();  // Get level1 Activity(question)
        $teenDetails = $this->teenagersRepository->getAllTeenagersByClass($id);
        $questionText = '';
        foreach ($level1Questions as $singleData) {
            if ($singleData->id == $id) {
                $questionText = $singleData->l1ac_text;
            }
        }

        $finallevel1 = [];
        $total = 0;
        $allQuestion = [];
        $suggestion = '';
        foreach ($level1Questions as $singleData) {
            $level1final = Helpers::calculateTrendForLevel1AdminById($singleData->id,$id);
            foreach ($level1final['trend'] as $key => $value) {
                $allQuestion[$singleData->id]['trenddata'][$key] = $value;
            }
            $allQuestion[$singleData->id]['text'] = $singleData->l1ac_text;
            $allQuestion[$singleData->id]['total'] = $level1final['total'];
        }

        $teenagerMyIcons = array();
        //Get teenager choosen Icon

        $teenagerIcons = $this->teenagersRepository->getTeenagerSelectedIconByClass($id);
        $relationIcon = array();
        $fictionIcon = array();
        $nonFiction = array();
        if (isset($teenagerIcons) && !empty($teenagerIcons)) {
            foreach ($teenagerIcons as $key => $icon) {
                if ($icon->ti_icon_type == 1) {

                    if ($icon->fiction_image != '' && isset($icon->ti_icon_image)) {
                        $fictionIcon[] = $this->cartoonOriginalImageUploadPath . $icon->fiction_image;
                    } else {
                        $fictionIcon[] = $this->cartoonOriginalImageUploadPath . 'proteen-logo.png';
                    }
                } elseif ($icon->ti_icon_type == 2) {
                    if ($icon->nonfiction_image != '' && isset($icon->ti_icon_image)) {
                        $nonFiction[] = $this->humanOriginalImageUploadPath . $icon->nonfiction_image;
                    } else {
                        $nonFiction[] = $this->humanOriginalImageUploadPath . 'proteen-logo.png';
                    }
                } else {
                    if ($icon->ti_icon_image != '' && isset($icon->ti_icon_image)) {
                        $relationIcon[] = $this->relationIconOriginalImageUploadPath . $icon->ti_icon_image;
                    }
                }
            }
            $teenagerMyIcons = array_merge($fictionIcon, $nonFiction, $relationIcon);
        }
        $totalBadges = [];
        foreach ($professionAttempted['profession'] as $key => $value) {
            $badgesData = [];
            $professionid = $value->id;
            $badgesData['pf_name'] = $value->pf_name;
            $basicData = $this->teenagersRepository->getTeenagerAllTypeBadgesByClass($id, $professionid);
            $badgesData['bacisbadges'] = $basicData['level4Basic']['badgesStarCount'];
            $badgesData['intermediatebadges'] = $basicData['level4Intermediate']['badgesCount'];
            $badgesData['advancebadges'] = $basicData['level4Advance']['advanceBadgeStar'];
            $totalBadges[] = $badgesData;
        }

        $logo = $this->loggedInUser->user()->sc_logo;
        $image = '';
        if (!empty($logo)) {
            if ($logo != '' && isset($logo)) {
                $image = $this->schoolOriginalImageUploadPath . $logo;
            } else {
                $image = $this->schoolOriginalImageUploadPath . 'proteen-logo.png';
            }
        }

        $deductedCoinsDetail = $objDeductedCoins->getAllDeductedCoinsDetail($schoolid,3);
        $teenCoinsDetail = $objTeenagerCoinsGift->getAllTeenagerCoinsGiftDetail($schoolid,3);

        $response = [];
        $response['classDetails'] = $classDetails;
        $response['schoolid'] = $schoolid;
        $response['cid'] = $id;
        $response['studentData'] = $studentData;
        $response['teenDetailsForLevel1'] = $teenDetailsForLevel1;
        $response['teenDetailsForLevel2'] = $teenDetailsForLevel2;
        $response['teenDetailsForLevel3'] = $teenDetailsForLevel3;
        $response['teenDetailsForLevel4'] = $teenDetailsForLevel4;
        $response['professionAttempted'] = $professionAttempted;
        $response['allQuestion'] = $allQuestion;
        $response['teenagerMyIcons'] = $teenagerMyIcons;
        $response['totalBadges'] = $totalBadges;
        $response['logo'] = $image;
        $response['deductedCoinsDetail'] = $deductedCoinsDetail;
        $response['teenCoinsDetail'] = $teenCoinsDetail;

        $pdf=PDF::loadView('school.exportSchoolDetailPDF', $response);
        return $pdf->stream('School.pdf');
    }

    function purchasedCoinsToViewReport() {
        if (Auth::guard('school')->check()) {
            $schoolId = Input::get('schoolId');
            $objPaidComponent = new PaidComponent();
            $componentsData = $objPaidComponent->getPaidComponentsData('School Report');
            $coins = $componentsData->pc_required_coins;
            $objDeductedCoins = new DeductedCoins();

            $deductedCoinsDetail = $objDeductedCoins->getDeductedCoinsDetailByIdForLS($schoolId, $componentsData->id, 3);
            $days = 0;
            if ($deductedCoinsDetail->count() > 0) {
                $days = Helpers::calculateRemainingDays($deductedCoinsDetail[0]->dc_end_date);
            }
            if ($days == 0) {
                $deductedCoins = $coins;
                $schoolData = $this->schoolsRepository->getSchoolDataForCoinsDetail($schoolId);
                if (!empty($schoolData) && isset($schoolData['sc_coins'])) {
                    $coins = $schoolData['sc_coins'] - $coins;
                }
                $result = $this->schoolsRepository->updateSchoolCoinsDetail($schoolId, $coins);
                $return = Helpers::saveDeductedCoinsData($schoolId,3,$deductedCoins,'School Report', 0);
            }
            return "1";
            exit;
        }
        return view('school.login'); exit;
    }

    public function getConsumption() {
        if (Auth::guard('school')->check()) {
            $schoolid = $this->loggedInUser->user()->id;
            $objDeductedCoins = new DeductedCoins();

            $deductedCoinsDetail = $objDeductedCoins->getDeductedCoinsDetail($schoolid,3);

            return view('school.showConsumptionCoins', compact('deductedCoinsDetail'));
        }
        return view('school.login'); exit;
    }

     public function getGiftCoins() {
        if (Auth::guard('school')->check()) {
            $schoolid = $this->loggedInUser->user()->id;
            $objTeenagerCoinsGift = new TeenagerCoinsGift();
            $teenCoinsDetail = $objTeenagerCoinsGift->getTeenagerCoinsGiftDetail($schoolid,3);

            return view('school.showGiftedCoins', compact('teenCoinsDetail'));
        }
        return view('school.login'); exit;
    }

    public function giftcoinstoTeenager() {
       if (Auth::guard('school')->check()) {
            $schoolid = $this->loggedInUser->user()->id;
            $teenagerId = Input::get('teen_id');
            $userDetail = $this->teenagersRepository->getTeenagerByTeenagerId($teenagerId);

            return view('school.giftCoinsToTeenager', compact('userDetail'));
            exit;
        }
        return view('school.login'); exit;

    }

    public function saveGiftedCoinsDetail() {
        if (Auth::guard('school')->check()) {
            $id = e(Input::get('id'));
            $giftcoins = e(Input::get('t_coins'));
            $schoolId = $this->loggedInUser->user()->id;
            $objGiftUser = new TeenagerCoinsGift();
            $r_coins = 0;
            $schoolData = $this->schoolsRepository->getSchoolDataForCoinsDetail($schoolId);
            if (!empty($schoolData) && isset($schoolData['sc_coins'])) {
                $r_coins = $schoolData['sc_coins'];
            }
            if ($giftcoins > $r_coins) {
                return Redirect::to("school/home")->with('error', trans('labels.validcoinsparent'));
            } else {
                $saveData = [];
                $saveData['tcg_sender_id'] = $schoolId;
                $saveData['tcg_reciver_id'] = $id;
                $saveData['tcg_total_coins'] = $giftcoins;
                $saveData['tcg_gift_date'] = date('Y-m-d');
                $saveData['tcg_user_type'] = 3;
                $return = $objGiftUser->saveTeenagetGiftCoinsDetail($saveData);

                //add coins to teenager
                $coins = 0;
                $userData = $this->teenagersRepository->getUserDataForCoinsDetail($id);
                if (!empty($userData) && isset($userData['t_coins']) ) {
                    $coins = $userData['t_coins'] + $giftcoins;
                }
                $result = $this->teenagersRepository->updateTeenagerCoinsDetail($id, $coins);

                //deduct coins from school account
                $schoolData = $this->schoolsRepository->getSchoolDataForCoinsDetail($schoolId);
                if (!empty($schoolData) && isset($schoolData['sc_coins']) ) {
                    $giftcoins = $schoolData['sc_coins']-$giftcoins;
                }
                $result = $this->schoolsRepository->updateSchoolCoinsDetail($schoolId, $giftcoins);

                //Mail to both users
                //mail to teenager
                $schoolData = $this->schoolsRepository->getSchoolBySchoolId($schoolId);
                $teenagerDetail = $this->teenagersRepository->getTeenagerByTeenagerId($id);

                $replaceArray = array();
                $replaceArray['TEEN_NAME'] = $teenagerDetail['t_name'];
                $replaceArray['COINS'] = e(Input::get('t_coins'));
                $replaceArray['FROM_USER'] = $schoolData[0]['sc_name'];
                $emailTemplateContent = $this->templateRepository->getEmailTemplateDataByName(Config::get('constant.COINS_RECEIBED_TEMPLATE'));
                $content = $this->templateRepository->getEmailContent($emailTemplateContent->et_body, $replaceArray);
                $data = array();
                $data['subject'] = $emailTemplateContent->et_subject;
                $data['toEmail'] = $teenagerDetail['t_email'];
                $data['toName'] = $teenagerDetail['t_name'];
                $data['content'] = $content;

                Mail::send(['html' => 'emails.Template'], $data , function ($m) use ($data) {
                    $m->from(Config::get('constant.FROM_MAIL_ID'), 'Gift ProCoins');
                    $m->subject($data['subject']);
                    $m->to($data['toEmail'], $data['toName']);
                });

                //mail to school

                $replaceArray = array();
                $replaceArray['TEEN_NAME'] = $schoolData[0]['sc_name'];
                $replaceArray['COINS'] = e(Input::get('t_coins'));
                $replaceArray['TO_USER'] = $teenagerDetail['t_name'];
                $emailTemplateContent = $this->templateRepository->getEmailTemplateDataByName(Config::get('constant.GIFTED_COINS_TEMPLATE'));
                $content = $this->templateRepository->getEmailContent($emailTemplateContent->et_body, $replaceArray);

                $data = array();
                $data['subject'] = $emailTemplateContent->et_subject;
                $data['toEmail'] = $schoolData[0]['sc_email'];
                $data['toName'] = $schoolData[0]['sc_name'];
                $data['content'] = $content;

                Mail::send(['html' => 'emails.Template'], $data , function ($m) use ($data) {
                    $m->from(Config::get('constant.FROM_MAIL_ID'), 'Gift Coins');
                    $m->subject($data['subject']);
                    $m->to($data['toEmail'], $data['toName']);
                });
                return Redirect::to("school/home")->with('success', trans('labels.coinsgiftsuccess'));
            }
        }
        return view('school.login'); exit;
     }

     public function getAvailableCoins() {
        if (Auth::guard('school')->check()) {
            $schoolId = Input::get('schoolId');
            $objPaidComponent = new PaidComponent();
            $componentsData = $objPaidComponent->getPaidComponentsData('School Report');

            return $componentsData->pc_required_coins;
            exit;
        }
        return view('school.login'); exit;
    }

    public function giftcoinstoAllTeenager() {
       if (Auth::guard('school')->check()) {
            $schoolid = $this->loggedInUser->user()->id;

            return view('school.giftCoinsToAllTeenager');
            exit;
        }
        return view('school.login'); exit;

    }

    public function saveCoinsDataForAllTeenager() {
        if (Auth::guard('school')->check()) {
            $id = e(Input::get('id'));
            $giftcoins = e(Input::get('t_coins'));
            $schoolId = $this->loggedInUser->user()->id;
            $objGiftUser = new TeenagerCoinsGift();
            $r_coins = 0;
            $schoolData = $this->schoolsRepository->getSchoolDataForCoinsDetail($schoolId);
            $teenDetailSchoolWise = $this->teenagersRepository->getActiveSchoolStudentsDetail($schoolId);
            if (!empty($schoolData) && isset($schoolData['sc_coins'])) {
                $r_coins = $schoolData['sc_coins'];
            }
            $totalTeen = count($teenDetailSchoolWise);
            $deductCoins = 0;
            if ($totalTeen > 0) {
                $deductCoins = $totalTeen * $giftcoins;
            }
            if ($deductCoins > $r_coins) {
                return Redirect::to("school/home")->with('error', trans('labels.validcoinsparent'));
            } else {
                foreach ($teenDetailSchoolWise AS $key => $value) {
                    $id = $value->id;
                    $saveData = [];
                    $saveData['tcg_sender_id'] = $schoolId;
                    $saveData['tcg_reciver_id'] = $id;
                    $saveData['tcg_total_coins'] = $giftcoins;
                    $saveData['tcg_gift_date'] = date('Y-m-d');
                    $saveData['tcg_user_type'] = 3;
                    $return = $objGiftUser->saveTeenagetGiftCoinsDetail($saveData);

                    //add coins to teenager
                    $coins = 0;
                    $userData = $this->teenagersRepository->getUserDataForCoinsDetail($id);
                    if (!empty($userData) && isset($userData['t_coins']) ) {
                        $coins = $userData['t_coins']+$giftcoins;
                    }
                    $result = $this->teenagersRepository->updateTeenagerCoinsDetail($id, $coins);

                    //deduct coins from school account
                    $schoolData = $this->schoolsRepository->getSchoolDataForCoinsDetail($schoolId);
                    $added_coins = 0;
                    if (!empty($schoolData) && isset($schoolData['sc_coins'])) {
                        $added_coins = $schoolData['sc_coins']-$giftcoins;
                    }
                    $result = $this->schoolsRepository->updateSchoolCoinsDetail($schoolId, $added_coins);
                }

                //$teenagers = $this->teenagersRepository->getAllActiveTeenagersForNotification();
                $schoolData = $this->schoolsRepository->getSchoolBySchoolId($schoolId);
//                foreach ($teenagers AS $key => $value) {
//                    $message = '"'.$schoolData[0]['sc_name'] . '" just gifted ProCoins to all its students!';
//                    $return = Helpers::saveAllActiveTeenagerForSendNotifivation($value->id, $message);
//                }

                return Redirect::to("school/home")->with('success', trans('labels.coinsgiftsuccess'));
            }
        }
        return view('school.login'); exit;
     }
     function getCoinsForSchool() {
        if (Auth::guard('school')->check()) {
            $schoolId = Input::get('schoolId');
            $objPaidComponent = new PaidComponent();
            $componentsData = $objPaidComponent->getPaidComponentsData('School Report');
            $coins = $componentsData->pc_required_coins;

            $schoolData = $this->schoolsRepository->getSchoolDataForCoinsDetail($schoolId);
            if (!empty($schoolData) && isset($schoolData['sc_coins'])) {
                if ($schoolData['sc_coins'] < $coins) {
                    return "1";
                    exit;
                }
            }
            return $schoolData['sc_coins'];
            exit;
        }
        return view('school.login'); exit;
    }

    public function getremainigdaysForSchool() {
        if (Auth::guard('school')->check()) {
            $schoolId = Input::get('schoolId');
            $objPaidComponent = new PaidComponent();
            $objDeductedCoins = new DeductedCoins();

            $componentsData = $objPaidComponent->getPaidComponentsData('School Report');
            $deductedCoinsDetail = $objDeductedCoins->getDeductedCoinsDetailByIdForLS($schoolId,$componentsData->id,3);
            $days = 0;
            if (!empty($deductedCoinsDetail) && $deductedCoinsDetail->count() > 0) {
                $days = Helpers::calculateRemainingDays($deductedCoinsDetail[0]->dc_end_date);
            }
            return view('school.getRemainingDays',compact('days'));
            /*$data = $days.' Days Left';
            return $data;*/
            exit;
        }
        return view('school.login'); exit;
    }

     public function display() {
        return view('school.coinsHistory');
        exit;
    }

    public function userSearchForShowGiftCoins() {
        $searchKeyword = Input::get('search_keyword');
        $schoolId = Input::get('schoolId');
        $searchArray = explode(",",$searchKeyword);

        $objTeenagerCoinsGift = new TeenagerCoinsGift();
        if ($searchKeyword != '') {
            $teenCoinsDetail = $objTeenagerCoinsGift->getTeenagerCoinsGiftDetailName($schoolId,3,$searchArray);

            return view('school.searchGiftedCoins', compact('teenCoinsDetail'));
            exit;
        } else {
            $teenCoinsDetail = $objTeenagerCoinsGift->getTeenagerCoinsGiftDetail($schoolId,3);

            return view('school.searchGiftedCoins', compact('teenCoinsDetail'));
            exit;
        }
    }


    public function userSearchForSchoolData() {
        $searchKeyword = Input::get('search_keyword');
        $school_id = Input::get('schoolId');

        $schoolData = $this->schoolsRepository->getSchoolDataForCoinsDetail($school_id);

        if ($searchKeyword != '') {
            $finalEmailArr = array();
            $teenDetailSchoolWise = $this->teenagersRepository->getActiveSchoolStudentsDetailForSearch($school_id,$searchKeyword);
            $emailDetails = $this->teenagersRepository->getEmailDataOfStudentForSearch($school_id,$searchKeyword);

            if(!empty($emailDetails) && $emailDetails->count() > 0){
                foreach ($emailDetails as $data) {
                    $userid = $data->id;
                    $email = $data->t_email;
                    $checkIfMailSent = $this->teenagersRepository->checkMailSentOrNot($userid);
                    if (empty($checkIfMailSent)) {
                        $finalEmailArr[] = $email;
                    }
                }
            }
            if(!empty($teenDetailSchoolWise)){
                foreach ($teenDetailSchoolWise as $info) {
                    $info->email_sent = (in_array($info->t_email, $finalEmailArr))? "no":"yes";
                }
            }
            return view('school.searchDashboard', compact('teenDetailSchoolWise', 'finalEmailArr','schoolData'));
        } else {
            $finalEmailArr = array();
            $teenDetailSchoolWise = $this->teenagersRepository->getActiveSchoolStudentsDetail($school_id);
            $emailDetails = $this->teenagersRepository->getEmailDataOfStudent($school_id);
            if(!empty($emailDetails) && $emailDetails->count() > 0){
                foreach ($emailDetails as $data) {
                    $userid = $data->id;
                    $email = $data->t_email;
                    $checkIfMailSent = $this->teenagersRepository->checkMailSentOrNot($userid);
                    if (empty($checkIfMailSent)) {
                        $finalEmailArr[] = $email;
                    }
                }
            }

            if(!empty($teenDetailSchoolWise) && $teenDetailSchoolWise->count() > 0){
                foreach ($teenDetailSchoolWise as $info) {
                    $info->email_sent = (in_array($info->t_email, $finalEmailArr))? "no":"yes";
                }
            }
            
            return view('school.searchDashboard', compact('teenDetailSchoolWise', 'finalEmailArr','schoolData'));
        }
    }

     public function editTeenRollnum() {
        if (Auth::guard('school')->check()) {
            $id = Input::get('teenId');
            $rollnumber = Input::get('rollnum');

            $return = $this->teenagersRepository->updateTeenagerRollNumber($id,$rollnumber);
            return "1";
            exit;
        }
        return view('school.login'); exit;
     }
}

