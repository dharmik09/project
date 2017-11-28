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

class DashboardController extends Controller {

    public function __construct(SchoolsRepository $SchoolsRepository,Level1ActivitiesRepository $Level1ActivitiesRepository, TeenagersRepository $TeenagersRepository, TemplatesRepository $TemplatesRepository,ProfessionsRepository $ProfessionsRepository) {
        $this->middleware('auth.school');
        $this->SchoolsRepository = $SchoolsRepository;
        $this->TeenagersRepository = $TeenagersRepository;
        $this->TemplateRepository = $TemplatesRepository;
        $this->Level1ActivitiesRepository = $Level1ActivitiesRepository;
        $this->professionThumbImageUploadPath = Config::get('constant.PROFESSION_THUMB_IMAGE_UPLOAD_PATH');
        $this->cartoonThumbImageUploadPath = Config::get('constant.CARTOON_THUMB_IMAGE_UPLOAD_PATH');
        $this->schoolOriginalImageUploadPath = Config::get('constant.SCHOOL_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->cartoonOriginalImageUploadPath = Config::get('constant.CARTOON_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->humanThumbImageUploadPath = Config::get('constant.HUMAN_THUMB_IMAGE_UPLOAD_PATH');
        $this->humanOriginalImageUploadPath = Config::get('constant.HUMAN_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->relationIconOriginalImageUploadPath = Config::get('constant.RELATION_ICON_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->relationIconThumbImageUploadPath = Config::get('constant.RELATION_ICON_THUMB_IMAGE_UPLOAD_PATH');
        $this->ProfessionsRepository = $ProfessionsRepository;
    }

    public function index() {

        $teenDetailSchoolWise = array();
        $currentPage = 1;
        if (Auth::school()->check()) {
            if((\Session::has('currentPage'))){
                $currentPage = \Session::get('currentPage'); 
            }
            $school_id = Auth::school()->get()->id;
            $schoolData = $this->SchoolsRepository->getSchoolDataForCoinsDetail($school_id);
            $finalEmailArr = array();
            $teenDetailSchoolWise = $this->TeenagersRepository->getActiveSchoolStudentsDetail($school_id);

            $emailDetails = $this->TeenagersRepository->getEmailDataOfStudent($school_id);
            if(!empty($emailDetails)){
                foreach ($emailDetails as $data) {
                    $userid = $data->id;
                    $email = $data->t_email;
                    $checkIfMailSent = $this->TeenagersRepository->checkMailSentOrNot($userid);
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
            $totalPage = $teenDetailSchoolWise->lastPage();
            \Session::forget('currentPage');
            return view('school.Dashboard', compact('teenDetailSchoolWise', 'finalEmailArr','schoolData','currentPage','totalPage'));
        } else {
            return view('school.login');
        }
    }

    public function bulkImport() {
        $schoolOriginalImagePath = Config::get('constant.SCHOOL_ORIGINAL_IMAGE_UPLOAD_PATH');
        return view('school.AddSchoolBulk',compact('schoolOriginalImagePath'));
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
                            $activeEmail = $this->TeenagersRepository->checkActiveEmailExist($row['student_email']);
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
                                    $schoolDetail['t_school'] = Auth::school()->get()->id;
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
                                    $response = $this->SchoolsRepository->saveSchoolBulkDetail($schoolDetail);
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
               return Redirect::to("school/dashboard")->with('success', 'Users imported successfully...');
               exit;
            }else{
               return Redirect::to("school/bulkimport")->with('error', 'Invalid data in excel file...');
               exit;
            }
        } else {
            return Redirect::to("school/bulkimport")->with('error', 'Invalid file type...');
            exit;
        }
    }

    public function inactive($id,$status) {
        $response = $this->SchoolsRepository->inactiveRecord($id,$status);
        if ($response) {
            return Redirect::to("school/dashboard");
        } else {
            return Redirect::to("school/dashboard");
        }
    }

    public function sendemail() {
        $a = 0;
        
        $emaildata = Input::get('email');
        if(!empty($emaildata) && count($emaildata) > 0)
        {
          foreach ($emaildata as $key => $value) {
              $teenagerDetailbyEmail = $this->TeenagersRepository->getTeenagerDetailByEmailId($value);
              
              if (!empty($teenagerDetailbyEmail)) {
                  // --------------------start sending mail -----------------------------//
                  $password = str_random(10);
                  $replaceArray = array();
                  $replaceArray['VERIFICATION_LINK'] = "<a href=" . url("verifyTeenfromSchool?token=" . $teenagerDetailbyEmail['t_uniqueid']) . ">" . url("verifyTeenfromSchool?token=" . $teenagerDetailbyEmail['t_uniqueid']) . "</a>";
                  $replaceArray['SCHOOL_NAME'] = Auth::school()->get()->sc_name;
                  $replaceArray['EMAIL'] = $value;
                  $replaceArray['PASSWORD'] = $password;
                  $emailTemplateContent = $this->TemplateRepository->getEmailTemplateDataByName(Config::get('constant.STUDENT_BULK_IMPORT'));
                  $content = $this->TemplateRepository->getEmailContent($emailTemplateContent->et_body, $replaceArray);
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
                      $this->TeenagersRepository->addTeenagerEmailVarifyToken($teenagerTokenDetail);
                  });


                  $savePassword = bcrypt($password);
                  $saveTeenPassword = $this->TeenagersRepository->savePassword($savePassword, $teenagerDetailbyEmail['t_uniqueid']);
              } else {
                  return Redirect::to("school/dashboard")->with('error', 'Email Address does not Exist...');
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
          
          return Redirect::to("school/dashboard")->with('success', 'Mail sent successfully...');
        }
        else
        {
            return Redirect::to("school/dashboard")->with('error', 'Email Address does not Exist...');
        }
    }

    public function exportPDF($id = 0) {
        $schoolid = Auth::school()->get()->id;
        $objDeductedCoins = new DeductedCoins();
        $objTeenagerCoinsGift = new TeenagerCoinsGift();

        if (empty($id) && $id == 0 && $id == '') {
            $classid = $this->SchoolsRepository->getFirstClassDetail($schoolid);
            $id = (isset($classid->t_class) && $classid->t_class != '')?$classid->t_class:'';
        } else {
            $id = $id;
        }
        if (isset($id) && !empty($id)) {
            $classDetails = $this->SchoolsRepository->getClassDetail($schoolid);
            $teenDetailsForLevel1 = $this->SchoolsRepository->getStudentForLevel1($schoolid, $id);
            $teenDetailsForLevel2 = $this->SchoolsRepository->getStudentForLevel2($schoolid, $id);
            $teenDetailsForLevel3 = $this->SchoolsRepository->getStudentForLevel3($schoolid, $id);
            $teenDetailsForLevel4 = $this->SchoolsRepository->getStudentForLevel4($schoolid, $id);
            $professionAttempted = $this->SchoolsRepository->getAttemptedProfession($schoolid, $id);
        } else {
            return Redirect::to("school/dashboard")->with('error', 'No data found');
            exit;
        }

        foreach ($professionAttempted['profession'] As $key => $value) {
            $image = $value->pf_logo;
            if ($image != '' && file_exists($this->professionThumbImageUploadPath . $image)) {
                $image = asset($this->professionThumbImageUploadPath . $image);
            } else {
                $image = asset($this->professionThumbImageUploadPath . 'proteen-logo.png');
            }
            $value->pf_logo = $image;
            $professionHeaderDetail = $this->ProfessionsRepository->getProfessionsHeaderByProfessionId($value->id);
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
        $teenDetailSchoolWise = $this->TeenagersRepository->getActiveSchoolStudentsDetail($schoolid);
        $emailDetails = $this->TeenagersRepository->getEmailDataOfStudent($schoolid);
        if(!empty($emailDetails)){
            foreach ($emailDetails as $data) {
                $userid = $data->id;
                $email = $data->t_email;
                $checkIfMailSent = $this->TeenagersRepository->checkMailSentOrNot($userid);
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

        $level1Questions = $this->Level1ActivitiesRepository->getLevel1AllActiveQuestion();  // Get level1 Activity(question)
        $teenDetails = $this->TeenagersRepository->getAllTeenagersByClass($id);
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

        $teenagerIcons = $this->TeenagersRepository->getTeenagerSelectedIconByClass($id);
        $relationIcon = array();
        $fictionIcon = array();
        $nonFiction = array();
        if (isset($teenagerIcons) && !empty($teenagerIcons)) {
            foreach ($teenagerIcons as $key => $icon) {
                if ($icon->ti_icon_type == 1) {

                    if ($icon->fiction_image != '' && file_exists($this->cartoonOriginalImageUploadPath . $icon->fiction_image)) {
                        $fictionIcon[] = $this->cartoonOriginalImageUploadPath . $icon->fiction_image;
                    } else {
                        $fictionIcon[] = $this->cartoonOriginalImageUploadPath . 'proteen-logo.png';
                    }
                } elseif ($icon->ti_icon_type == 2) {
                    if ($icon->nonfiction_image != '' && file_exists($this->humanOriginalImageUploadPath . $icon->nonfiction_image)) {
                        $nonFiction[] = $this->humanOriginalImageUploadPath . $icon->nonfiction_image;
                    } else {
                        $nonFiction[] = $this->humanOriginalImageUploadPath . 'proteen-logo.png';
                    }
                } else {
                    if ($icon->ti_icon_image != '' && file_exists($this->relationIconOriginalImageUploadPath . $icon->ti_icon_image)) {
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
            $basicData = $this->TeenagersRepository->getTeenagerAllTypeBadgesByClass($id, $professionid);
            $badgesData['bacisbadges'] = $basicData['level4Basic']['badgesStarCount'];
            $badgesData['intermediatebadges'] = $basicData['level4Intermediate']['badgesCount'];
            $badgesData['advancebadges'] = $basicData['level4Advance']['advanceBadgeStar'];
            $totalBadges[] = $badgesData;
        }

        $logo = Auth::school()->get()->sc_logo;
        $image = '';
        if (!empty($logo)) {
            if ($logo != '' && file_exists($this->schoolOriginalImageUploadPath . $logo)) {
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

        $pdf=PDF::loadView('school.ExportSchoolDetailPDF', $response);
        return $pdf->stream('School.pdf');
    }

    function purchasedCoinsToViewReport() {
        if (Auth::school()->check()) {
            $schoolId = Input::get('schoolId');
            $objPaidComponent = new PaidComponent();
            $componentsData = $objPaidComponent->getPaidComponentsData('School Report');
            $coins = $componentsData[0]->pc_required_coins;
            $objDeductedCoins = new DeductedCoins();

            $deductedCoinsDetail = $objDeductedCoins->getDeductedCoinsDetailByIdForLS($schoolId,$componentsData[0]->id,3);
            $days = 0;
            if (!empty($deductedCoinsDetail)) {
                $days = Helpers::calculateRemaningDays($deductedCoinsDetail[0]->dc_end_date);
            }
            if ($days == 0) {
                $deductedCoins = $coins;
                $schoolData = $this->SchoolsRepository->getSchoolDataForCoinsDetail($schoolId);
                if (!empty($schoolData)) {
                    $coins = $schoolData['sc_coins']-$coins;
                }
                $result = $this->SchoolsRepository->updateSchoolCoinsDetail($schoolId, $coins);
                $return = Helpers::saveDeductedCoinsData($schoolId,3,$deductedCoins,'School Report', 0);
            }
            return "1";
            exit;
        }
        return view('school.Login'); exit;
    }

    public function getConsumption() {
        if (Auth::school()->check()) {
            $schoolid = Auth::school()->id();
            $objDeductedCoins = new DeductedCoins();

            $deductedCoinsDetail = $objDeductedCoins->getDeductedCoinsDetail($schoolid,3);

            return view('school.ShowConsumptionCoins', compact('deductedCoinsDetail'));
        }
        return view('school.Login'); exit;
    }

     public function getGiftCoins() {
        if (Auth::school()->check()) {
            $schoolid = Auth::school()->id();
            $objTeenagerCoinsGift = new TeenagerCoinsGift();
            $teenCoinsDetail = $objTeenagerCoinsGift->getTeenagerCoinsGiftDetail($schoolid,3);

            return view('school.ShowGiftedCoins', compact('teenCoinsDetail'));
        }
        return view('school.Login'); exit;
    }

    public function giftcoinstoTeenager() {
       if (Auth::school()->check()) {
            $schoolid = Auth::school()->id();
            $teenagerId = Input::get('teen_id');
            $userDetail = $this->TeenagersRepository->getTeenagerByTeenagerId($teenagerId);

            return view('school.GiftCoinsToTeenager', compact('userDetail'));
            exit;
        }
        return view('school.Login'); exit;

    }

    public function saveGiftedCoinsDetail() {
        if (Auth::school()->check()) {
            $id = e(Input::get('id'));
            $giftcoins = e(Input::get('t_coins'));
            $schoolId = Auth::school()->id();
            $objGiftUser = new TeenagerCoinsGift();
            $r_coins = 0;
            $schoolData = $this->SchoolsRepository->getSchoolDataForCoinsDetail($schoolId);
            if (!empty($schoolData)) {
                $r_coins = $schoolData['sc_coins'];
            }
            if ($giftcoins > $r_coins) {
                return Redirect::to("school/dashboard")->with('error', trans('labels.validcoinsparent'));
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
                $userData = $this->TeenagersRepository->getUserDataForCoinsDetail($id);
                if (!empty($userData)) {
                    $coins = $userData['t_coins']+$giftcoins;
                }
                $result = $this->TeenagersRepository->updateTeenagerCoinsDetail($id, $coins);

                //deduct coins from school account
                $schoolData = $this->SchoolsRepository->getSchoolDataForCoinsDetail($schoolId);
                if (!empty($schoolData)) {
                    $giftcoins = $schoolData['sc_coins']-$giftcoins;
                }
                $result = $this->SchoolsRepository->updateSchoolCoinsDetail($schoolId, $giftcoins);

                //Mail to both users
                //mail to teenager
                $schoolData = $this->SchoolsRepository->getSchoolBySchoolId($schoolId);
                $teenagerDetail = $this->TeenagersRepository->getTeenagerByTeenagerId($id);

                $replaceArray = array();
                $replaceArray['TEEN_NAME'] = $teenagerDetail['t_name'];
                $replaceArray['COINS'] = e(Input::get('t_coins'));
                $replaceArray['FROM_USER'] = $schoolData[0]['sc_name'];
                $emailTemplateContent = $this->TemplateRepository->getEmailTemplateDataByName(Config::get('constant.COINS_RECEIBED_TEMPLATE'));
                $content = $this->TemplateRepository->getEmailContent($emailTemplateContent->et_body, $replaceArray);
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
                $emailTemplateContent = $this->TemplateRepository->getEmailTemplateDataByName(Config::get('constant.GIFTED_COINS_TEMPLATE'));
                $content = $this->TemplateRepository->getEmailContent($emailTemplateContent->et_body, $replaceArray);

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
                return Redirect::to("school/dashboard")->with('success', trans('labels.coinsgiftsuccess'));
            }
        }
        return view('school.Login'); exit;
     }

     public function getAvailableCoins() {
        if (Auth::school()->check()) {
            $schoolId = Input::get('schoolId');
            $objPaidComponent = new PaidComponent();
            $componentsData = $objPaidComponent->getPaidComponentsData('School Report');

            return $componentsData[0]->pc_required_coins;
            exit;
        }
        return view('school.Login'); exit;
    }

    public function giftcoinstoAllTeenager() {
       if (Auth::school()->check()) {
            $schoolid = Auth::school()->id();

            return view('school.GiftCoinsToAllTeenager');
            exit;
        }
        return view('school.Login'); exit;

    }

    public function saveCoinsDataForAllTeenager() {
        if (Auth::school()->check()) {
            $id = e(Input::get('id'));
            $giftcoins = e(Input::get('t_coins'));
            $schoolId = Auth::school()->id();
            $objGiftUser = new TeenagerCoinsGift();
            $r_coins = 0;
            $schoolData = $this->SchoolsRepository->getSchoolDataForCoinsDetail($schoolId);
            $teenDetailSchoolWise = $this->TeenagersRepository->getActiveSchoolStudentsDetail($schoolId);
            if (!empty($schoolData)) {
                $r_coins = $schoolData['sc_coins'];
            }
            $totalTeen = count($teenDetailSchoolWise);
            $deductCoins = 0;
            if ($totalTeen > 0) {
                $deductCoins = $totalTeen * $giftcoins;
            }
            if ($deductCoins > $r_coins) {
                return Redirect::to("school/dashboard")->with('error', trans('labels.validcoinsparent'));
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
                    $userData = $this->TeenagersRepository->getUserDataForCoinsDetail($id);
                    if (!empty($userData)) {
                        $coins = $userData['t_coins']+$giftcoins;
                    }
                    $result = $this->TeenagersRepository->updateTeenagerCoinsDetail($id, $coins);

                    //deduct coins from school account
                    $schoolData = $this->SchoolsRepository->getSchoolDataForCoinsDetail($schoolId);
                    $added_coins = 0;
                    if (!empty($schoolData)) {
                        $added_coins = $schoolData['sc_coins']-$giftcoins;
                    }
                    $result = $this->SchoolsRepository->updateSchoolCoinsDetail($schoolId, $added_coins);
                }

                //$teenagers = $this->TeenagersRepository->getAllActiveTeenagersForNotification();
                $schoolData = $this->SchoolsRepository->getSchoolBySchoolId($schoolId);
//                foreach ($teenagers AS $key => $value) {
//                    $message = '"'.$schoolData[0]['sc_name'] . '" just gifted ProCoins to all its students!';
//                    $return = Helpers::saveAllActiveTeenagerForSendNotifivation($value->id, $message);
//                }

                return Redirect::to("school/dashboard")->with('success', trans('labels.coinsgiftsuccess'));
            }
        }
        return view('school.Login'); exit;
     }
     function getCoinsForSchool() {
        if (Auth::school()->check()) {
            $schoolId = Input::get('schoolId');
            $objPaidComponent = new PaidComponent();
            $componentsData = $objPaidComponent->getPaidComponentsData('School Report');
            $coins = $componentsData[0]->pc_required_coins;

            $schoolData = $this->SchoolsRepository->getSchoolDataForCoinsDetail($schoolId);
            if (!empty($schoolData)) {
                if ($schoolData['sc_coins'] < $coins) {
                    return "1";
                    exit;
                }
            }
            return $schoolData['sc_coins'];
            exit;
        }
        return view('school.Login'); exit;
    }

    public function getremainigdaysForSchool() {
        if (Auth::school()->check()) {
            $schoolId = Input::get('schoolId');
            $objPaidComponent = new PaidComponent();
            $objDeductedCoins = new DeductedCoins();

            $componentsData = $objPaidComponent->getPaidComponentsData('School Report');
            $deductedCoinsDetail = $objDeductedCoins->getDeductedCoinsDetailByIdForLS($schoolId,$componentsData[0]->id,3);
            $days = 0;
            if (!empty($deductedCoinsDetail)) {
                $days = Helpers::calculateRemaningDays($deductedCoinsDetail[0]->dc_end_date);
            }
            return view('school.gerRemaningDays',compact('days'));
            /*$data = $days.' Days Left';
            return $data;*/
            exit;
        }
        return view('school.Login'); exit;
    }

     public function display() {
        return view('school.CoinsHistory');
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

        $schoolData = $this->SchoolsRepository->getSchoolDataForCoinsDetail($school_id);

        if ($searchKeyword != '') {
            $finalEmailArr = array();
            $teenDetailSchoolWise = $this->TeenagersRepository->getActiveSchoolStudentsDetailForSearch($school_id,$searchKeyword);
            $emailDetails = $this->TeenagersRepository->getEmailDataOfStudentForSearch($school_id,$searchKeyword);

            if(!empty($emailDetails)){
                foreach ($emailDetails as $data) {
                    $userid = $data->id;
                    $email = $data->t_email;
                    $checkIfMailSent = $this->TeenagersRepository->checkMailSentOrNot($userid);
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
            $teenDetailSchoolWise = $this->TeenagersRepository->getActiveSchoolStudentsDetail($school_id);
            $emailDetails = $this->TeenagersRepository->getEmailDataOfStudent($school_id);
            if(!empty($emailDetails)){
                foreach ($emailDetails as $data) {
                    $userid = $data->id;
                    $email = $data->t_email;
                    $checkIfMailSent = $this->TeenagersRepository->checkMailSentOrNot($userid);
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
        }
    }

     public function editTeenRollnum() {
        if (Auth::school()->check()) {
            $id = Input::get('teenId');
            $rollnumber = Input::get('rollnum');

            $return = $this->TeenagersRepository->updateTeenagerRollNumber($id,$rollnumber);
            return "1";
            exit;
        }
        return view('school.Login'); exit;
     }
}

