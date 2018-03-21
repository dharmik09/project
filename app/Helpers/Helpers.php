<?php

namespace App\Helpers;

use DB;
use Config;
use App\Country;
use App\State;
use App\City;
use App\Teenagers;
use App\Schools;
use App\Professions;
use App\Apptitude;
use App\Interest;
use App\MultipleIntelligent;
use App\Personality;
use App\Baskets;
use App\Sponsors;
use App\Audit;
use App\SystemLevels;
use App\Level1HumanIconCategory;
use App\Level1CartoonIconCategory;
use App\Level2Answers;
use App\Level2Activity;
use App\Level4Activity;
use App\MultipleIntelligentScale;
use App\ApptitudeTypeScale;
use App\PersonalityScale;
use App\GenericAds;
use App\Configurations;
use DateTime;
use App\Parents;
use App\ProfessionMatchScale;
use Redirect;
use Validator;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use FCM;
use App\GamificationTemplate;
use App\DeviceToken;
use App\PaidComponent;
use App\DeductedCoins;
use App\ProfileViewDeductedCoins;
use App\Notifications;
use Illuminate\Support\Facades\Storage;
use App\CMS;
use Carbon\Carbon;
use App\Jobs\SetProfessionMatchScale;
use Illuminate\Support\Facades\Auth;
use App\Level1Activity;
use App\TeenagerPromiseScore;

Class Helpers {
    /*
      @createAudit Parameters
      $userId : LoggedIn User ID
      $userType : LoggedIn User Type
      $action : CREATE, READ, UPDATE, DELETE
      $objectType : Type of object (Table Name) which is added, uodated, deleted & (Controller Function Name) for viewed
      $objectId : ID of object (Table Name) which is added, updated, deleted & (URL) for viewed
      $origin : 1 - Android , 2 - IOS, 3 - Web
      $message : Success or Error message
      $extra : Serialized Data
      $ip : IP of user machine
     */

    public static function createAudit($userId, $userType, $action, $objectType, $objectId, $origin, $message = '', $extra = '', $ip = '') {
        $auditData = [];
        $auditData['au_user_id'] = $userId;
        $auditData['au_user_type'] = $userType;
        $auditData['au_action'] = $action;
        $auditData['au_object_type'] = $objectType;
        $auditData['au_object_id'] = $objectId;
        $auditData['au_origin'] = $origin;
        $auditData['au_message'] = $message;
        $auditData['au_other'] = $extra;
        $auditData['au_ip'] = $ip;

        $objAudit = new Audit();
        $audit = $objAudit->saveAudit($auditData);
        return $audit;
    }

    /*
      return : Array of active schools
     */

    public static function getActiveSchools() {
        $objSchool = new Schools();
        $school = $objSchool->getActiveSchools();

        return $school;
        return array();
    }

    /*
     * deduct points from booster points
      return : true OR false
     */

    public static function deductTeenagerPoints($teenagerId, $deductPoints) {
        $objTeenager = new Teenagers();
        $getTeenagerBoosterPoints = $objTeenager->getTeenagerBoosterPoints($teenagerId);

        if ($getTeenagerBoosterPoints['total'] > $deductPoints) {
            if ($getTeenagerBoosterPoints['Level3'] > 0 && $getTeenagerBoosterPoints['Level3'] > $deductPoints) {
                $deductPoints = DB::table('pro_tlb_teenager_level_boosters')->where('tlb_teenager', $teenagerId)->where('tlb_level', config::get('constant.LEVEL3_ID'))->decrement('tlb_points', $deductPoints);
            } else if ($getTeenagerBoosterPoints['Level2'] > 0 && $getTeenagerBoosterPoints['Level2'] > $deductPoints) {
                $deductPoints = DB::table('pro_tlb_teenager_level_boosters')->where('tlb_teenager', $teenagerId)->where('tlb_level', config::get('constant.LEVEL2_ID'))->decrement('tlb_points', $deductPoints);
            } else if ($getTeenagerBoosterPoints['Level1'] > 0 && $getTeenagerBoosterPoints['Level1'] > $deductPoints) {
                $deductPoints = DB::table('pro_tlb_teenager_level_boosters')->where('tlb_teenager', $teenagerId)->where('tlb_level', config::get('constant.LEVEL1_ID'))->decrement('tlb_points', $deductPoints);
            } else {
                $deductPoints = 0;
            }
        } else {
            $deductPoints = 0;
        }

        return $deductPoints;
    }

    /*
      return : Array of countries
     */

    public static function getCountries() {
        $objCountry = new Country();
        $countries = $objCountry->getAllCounries();

        return $countries;
    }

    /*
      return : Array of cities
     */

    public static function getCities($state_id = 0) {
        $objCity = new City();
        $cities = $objCity->getAllCities($state_id);
        return $cities;
    }

    /*
      return : Array of states
     */

    public static function getStates($country_id = 0) {
        $objState = new State();
        $states = $objState->getAllStates($country_id);

        return $states;
    }

    /*
      return : Array of active teenagers
     */

    public static function getActiveTeenagers() {
        $objTeenager = new Teenagers();
        $teenager = $objTeenager->getActiveTeenagers();
        return $teenager;
    }

    /*
      return : Array of active teenagers
     */

    public static function getActiveTeenagersForDashboard($Id = '') {
        $objTeenager = new Teenagers();
        $teenager = $objTeenager->getActiveTeenagersForDashboard($Id);
        return $teenager;
    }

    public static function getTeenagersData($teenagerId) {
        $objTeenager = new Teenagers();
        $teenager = $objTeenager->getTeenagersData($teenagerId);
        return $teenager;
    }

    /*
      return : Array of active baskets
     */

    public static function getActiveBaskets() {
        $objBasket = new Baskets();
        $basket = $objBasket->getActiveBaskets();

        return $basket;
    }

    public static function getCompetingUserList($professionId) {
        $objProfession = new Professions();
        $objTeenagers = new Teenagers();
        $totalCompeting = $objProfession->getTotalCompetingOfProfession($professionId);
        $getLevel4AllScore = $objProfession->getLevel4AllScore($professionId);

        $level4Competing = [];
        if (isset($totalCompeting) && !empty($totalCompeting)) {
            foreach ($totalCompeting as $teenId) {
                $teendata = [];
                if (isset($teenId->teenager_id)) {
                    $level4Competing[$teenId->teenager_id]['yourScore'] = (isset($getLevel4AllScore['level4TotalPoints'][$teenId->teenager_id]) && $getLevel4AllScore['level4TotalPoints'][$teenId->teenager_id] > 0) ? $getLevel4AllScore['level4TotalPoints'][$teenId->teenager_id] : 0;
                    $level4Competing[$teenId->teenager_id]['highestScore'] = (isset($getLevel4AllScore['level4TotalPoints']) && !empty($getLevel4AllScore['level4TotalPoints'])) ? max($getLevel4AllScore['level4TotalPoints']) : 0;
                    if ($teenId->t_photo != '') {
                        if (file_exists(Config::get('constant.TEEN_THUMB_IMAGE_UPLOAD_PATH') . $teenId->t_photo)) {
                            $teenPhoto = asset(Config::get('constant.TEEN_THUMB_IMAGE_UPLOAD_PATH') . $teenId->t_photo);
                        } else {
                            $teenPhoto = asset(Config::get('constant.TEEN_THUMB_IMAGE_UPLOAD_PATH') . "proteen-logo.png");
                        }
                    } else {
                        $teenPhoto = asset(Config::get('constant.TEEN_THUMB_IMAGE_UPLOAD_PATH') . "proteen-logo.png");
                    }
                    $level4Competing[$teenId->teenager_id]['profile_pic'] = $teenPhoto;
                    $level4Competing[$teenId->teenager_id]['name'] = $teenId->t_name;
                    $level4Competing[$teenId->teenager_id]['teenager_id'] = $teenId->teenager_id;
                    $level4Competing[$teenId->teenager_id]['teenager_unique_id'] = $teenId->t_uniqueid;
                    $level4Competing[$teenId->teenager_id]['t_phone'] = $teenId->t_phone;
                    $level4Competing[$teenId->teenager_id]['t_email'] = $teenId->t_email;
                    $level4Competing[$teenId->teenager_id]['is_search_on'] = $teenId->is_search_on;
                    $level4Competing[$teenId->teenager_id]['competitors'] = (isset($totalCompeting) && !empty($totalCompeting))?count($totalCompeting):0;
                }
            }
        }
        if (!empty($level4Competing)) {
            arsort($level4Competing);
            $point = 1;
            foreach ($level4Competing as $key => $rankvalue) {
                $level4Competing[$key]['rank'] = (isset($level4Competing[$key]['highestScore']) && $level4Competing[$key]['highestScore'] > 0) ? $point : 0;
                $point++;
            }
        }
        return $level4Competing;
    }

    /*
      return : Array of active sponsors
     */

    public static function getActiveSponsors() {
        $objSponsor = new Sponsors();
        $sponsor = $objSponsor->getActiveSponsors();
        return $sponsor;
    }

    public static function gender() {
        $gender = array('1' => 'Male', '2' => 'Female');
        return $gender;
    }

    public static function status() {
        $status = array('1' => 'Active', '2' => 'In active');
        return $status;
    }

    /*
      return : Array of active apptitude
     */

    public static function getActiveApptitude() {
        $objApptitude_type = new Apptitude();
        $apttitude = $objApptitude_type->getActiveApptitude();

        return $apttitude;
    }

    public static function getActiveCategory() {
        $objCategory = new Level1HumanIconCategory();
        $category = $objCategory->getActiveCategory();

        return $category;
    }

    public static function getActiveCartoonCategory() {
        $objCartoonCategory = new Level1CartoonIconCategory();
        $category = $objCartoonCategory->getActiveCartoonCategory();

        return $category;
    }

    public static function getLevel4BasicActivity() {
        $basicActivity = DB::table(config::get('databaseconstants.TBL_LEVEL4_BASIC_ACTIVITY'))
                ->where('deleted', 1)
                ->get();
        return $basicActivity;
    }

    public static function getLevel4IntermediateActivity() {
        $intermediateActivity = DB::table(config::get('databaseconstants.TBL_LEVEL4_INTERMEDIATE_ACTIVITY'))
                ->where('deleted', 1)
                ->get();
        return $intermediateActivity;
    }

    public static function getLevel4AdvanceActivity() {
        $advanceActivity = DB::table(config::get('databaseconstants.TBL_LEVEL4_ADVANCE_ACTIVITY'))
                ->where('deleted', 1)
                ->get();
        return $advanceActivity;
    }

    /*
      return : Array of active interest
     */

    public static function getActiveInterest() {
        $objInterest_type = new Interest();
        $interest = $objInterest_type->getActiveInterest();

        return $interest;
    }

    /*
      return : Array of active multiple interest
     */

    public static function getActiveMultipleIntelligent() {
        $objMit_type = new MultipleIntelligent();
        $mi = $objMit_type->getActiveMultipleIntelligent();

        return $mi;
    }

    /*
      return : Array of active personality
     */

    public static function getActivePersonality() {
        $objPersonality_type = new Personality();
        $personality = $objPersonality_type->getActivePersonality();

        return $personality;
    }

    /*
      return : String of unique ID
     */

    public static function getTeenagerUniqueId() {
        return uniqid("", TRUE);
    }

    /*
      return : String of unique ID
     */

    public static function getParentUniqueId() {
        $uniqueId = uniqid("", TRUE);

        return $uniqueId;
    }

    /*
      return : String of unique ID
     */

    public static function getSponsorUniqueId() {
        $uniqueId = uniqid("", TRUE);

        return $uniqueId;
    }

    /*
      return : Array of active System Levels
     */

    public static function getActiveLevels() {
        $objLevels = new SystemLevels();
        $levels = $objLevels->getActiveLevels();

        return $levels;
    }

    /*
      return $resetOTP: One Time Password
     */

    public static function generateOtp() {
        $resetOTP = mt_rand(1000, 999999999);

        return $resetOTP;
    }

    /*
      return $resetString: One Time Password
     */

    public static function generateUniqueString() {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
        $resetString = '';
        for ($i = 0; $i < 23; $i++) {
            $resetString .= $characters[mt_rand(0, 61)];
        }

        return $resetString;
    }

    /*
      return : Array of active sponsors
     */

    public static function getActiveProfessions() {
        $objProfession = new Professions();
        $profession = $objProfession->getActiveProfessions();

        return $profession;
    }

    public static function getTeenagerCareersIds($teenId) {
        return DB::table('pro_srp_star_rated_professions')->where('srp_teenager_id', $teenId)->pluck('srp_profession_id');
    }

    public static function getAnsTypeFromGamificationTemplateId($templateId) {
        $objProfession = new Professions();
        $gt_temlpate_answer_type = $objProfession->getTemlpateAnswerType($templateId);
        return $gt_temlpate_answer_type;
    }

    /*
      return : Array of active sponsors
     */

    public static function generateRandomPassword() {
        $length = 10;
        $upperalphabets = range('A', 'Z');
        $loweralphabets = range('a', 'z');
        $numbers = range('0', '9');
        $additional_characters = array('_', '.');
        $final_array = array_merge($upperalphabets, $loweralphabets, $numbers, $additional_characters);
        $password = '';

        while ($length--) {
            $key = array_rand($final_array);
            $password .= $final_array[$key];
        }
        return $password;
    }

    public static function getTeenagerOriginalImageUrl($teenagerPhoto) {
        //return asset(Config::get('constant.TEEN_ORIGINAL_IMAGE_UPLOAD_PATH') . $teenagerPhoto);
        return Storage::url(Config::get('constant.TEEN_ORIGINAL_IMAGE_UPLOAD_PATH') . $teenagerPhoto);
    }

    public static function getParentOriginalImageUrl($parentPhoto) {
        return asset(Config::get('constant.PARENT_ORIGINAL_IMAGE_UPLOAD_PATH') . $parentPhoto);
    }

    public static function getSponsorOriginalImageUrl($sponsorLogo) {
        return asset(Config::get('constant.SPONSOR_ORIGINAL_IMAGE_UPLOAD_PATH') . $sponsorLogo);
    }

    public static function getSchoolOriginalImageUrl($schoolLogo) {
        return Config::get('constant.SCHOOL_ORIGINAL_IMAGE_UPLOAD_PATH') . $schoolLogo;
    }

    public static function getContactphotoOriginalImageUrl($sponsorPhoto) {
        return asset(Config::get('constant.CONTACT_PHOTO_ORIGINAL_IMAGE_UPLOAD_PATH') . $sponsorPhoto);
    }

    public static function getContactpersonOriginalImageUrl($schoolPhoto) {
        return Config::get('constant.CONTACT_PERSON_ORIGINAL_IMAGE_UPLOAD_PATH') . $schoolPhoto;
    }

    public static function getTeenagerThumbImageUrl($teenagerPhoto) {
        //return asset(Config::get('constant.TEEN_THUMB_IMAGE_UPLOAD_PATH') . $teenagerPhoto);
        return Storage::url(Config::get('constant.TEEN_THUMB_IMAGE_UPLOAD_PATH') . $teenagerPhoto);
    }

    public static function getParentThumbImageUrl($parentPhoto) {
        return asset(Config::get('constant.PARENT_THUMB_IMAGE_UPLOAD_PATH') . $parentPhoto);
    }

    public static function getSponsorThumbImageUrl($sponsorLogo) {
        return asset(Config::get('constant.SPONSOR_THUMB_IMAGE_UPLOAD_PATH') . $sponsorLogo);
    }

    public static function getSchoolThumbImageUrl($schoolLogo) {
        return asset(Config::get('constant.SCHOOL_THUMB_IMAGE_UPLOAD_PATH') . $schoolLogo);
    }

    public static function getContactphotoThumbImageUrl($sponsorPhoto) {
        return asset(Config::get('constant.CONTACT_PHOTO_THUMB_IMAGE_UPLOAD_PATH') . $sponsorPhoto);
    }

    public static function getContactpersonThumbImageUrl($schoolPhoto) {
        return asset(Config::get('constant.CONTACT_PERSON_THUMB_IMAGE_UPLOAD_PATH') . $schoolPhoto);
    }

    public static function getBasketOriginalImageUrl($basketPhoto) {
        if ($basketPhoto != '' && file_exists(Config::get('constant.BASKET_ORIGINAL_IMAGE_UPLOAD_PATH') . $basketPhoto)) {
            $basketPhotoName = $basketPhoto;
        } else {
            $basketPhotoName = 'proteen-logo.png';
        }
        return asset(Config::get('constant.BASKET_ORIGINAL_IMAGE_UPLOAD_PATH') . $basketPhotoName);
    }

    public static function getProfessionOriginalImageUrl($professionPhoto) {
        if ($professionPhoto != '' && file_exists(Config::get('constant.PROFESSION_ORIGINAL_IMAGE_UPLOAD_PATH') . $professionPhoto)) {
            $professionPhotoName = $professionPhoto;
        } else {
            $professionPhotoName = 'proteen-logo.png';
        }
        return asset(Config::get('constant.PROFESSION_ORIGINAL_IMAGE_UPLOAD_PATH') . $professionPhotoName);
    }

    public static function getProfessionThumbImageUrl($professionPhoto) {
        if ($professionPhoto != '' && file_exists(Config::get('constant.PROFESSION_THUMB_IMAGE_UPLOAD_PATH') . $professionPhoto)) {
            $professionPhotoName = $professionPhoto;
        } else {
            $professionPhotoName = 'proteen-logo.png';
        }
        return asset(Config::get('constant.PROFESSION_THUMB_IMAGE_UPLOAD_PATH') . $professionPhotoName);
    }

    public static function getTeenAPIScore($teengerId) {
        $finalScore = array();
        $APIdataSlug = [];
        $objLevel2Answers = new Level2Answers();
        $objLevel2Activity = new Level2Activity();
        $correctAnswerQuestionsIds = $objLevel2Answers->level2GetCorrectAnswerQuestionIds($teengerId);

        if (isset($correctAnswerQuestionsIds) && !empty($correctAnswerQuestionsIds)) {
            foreach ($correctAnswerQuestionsIds as $key => $questions) {
                $questionData[] = $objLevel2Activity->getActiveLevel2Activity($questions->l2ans_activity);
            }
        }

        // Get default all data of Interest, Aptitude, and personality and MI
        $objPersonality = new Personality();
        $personality = $objPersonality->getActivepersonality();

        $personalityArr = $personality->toArray();
        if (!empty($personalityArr)) {
            foreach ($personalityArr as $key => $val) {
                $allPersonality[$val['pt_name']] = 0;
                $allPersonalitySlug[$val['pt_slug']] = 0;
            }
        }

        $objInterest = new Interest();
        $interest = $objInterest->getActiveInterest();
        $interestArr = $interest->toArray();
        if (!empty($interestArr)) {
            foreach ($interestArr as $key => $val) {
                $allInterest[$val['it_name']] = 0;
                $allInterestSlug[$val['it_slug']] = 0;
            }
        }

        $objApptitude = new Apptitude();
        $apptitude = $objApptitude->getActiveApptitude();
        $apptitudeArr = $apptitude->toArray();
        if (!empty($apptitudeArr)) {
            foreach ($apptitudeArr as $key => $val) {
                $allApptitude[$val['apt_name']] = 0;
                $allApptitudeSlug[$val['apt_slug']] = 0;
            }
        }

        $objMultipleIntelligent = new MultipleIntelligent();
        $MI = $objMultipleIntelligent->getActiveMultipleIntelligent();
        $MIArr = $MI->toArray();
        if (!empty($MIArr)) {
            foreach ($MIArr as $key => $val) {
                $allMI[$val['mit_name']] = 0;
                $allMISlug[$val['mi_slug']] = 0;
            }
        }

        //Get Interest, Aptitude, and personality and MI of Teen based on answer
        if (isset($questionData) && !empty($questionData)) {
            $miName = '';
            $aptitudeName = '';
            $personalityName = '';
            $interest = '';
            foreach ($questionData as $key => $detail) {
                if (isset($detail[0])) {
                    if ($detail[0]->mit_name != '') {
                        $miName = $detail[0]->mit_name;
                        $APIdata['MI'][] = $detail[0]->mit_name;
                        $APIdataSlug[] = $detail[0]->mi_slug;
                    }
                    if ($detail[0]->apt_name != '') {
                        $aptitudeName = $detail[0]->apt_name;
                        $APIdata['aptitude'][] = $detail[0]->apt_name;
                        $APIdataSlug[] = $detail[0]->apt_slug;
                    }
                    if ($detail[0]->pt_name != '') {
                        $personalityName = $detail[0]->pt_name;
                        $APIdata['personality'][] = $detail[0]->pt_name;
                        $APIdataSlug[] = $detail[0]->pt_slug;
                    }
                    if ($detail[0]->it_name != '') {
                        $interest = $detail[0]->it_name;
                        $APIdata['interest'][] = $detail[0]->it_name;
                        $APIdataSlug[] = $detail[0]->it_slug;
                    }
                }
            }
        }

        if (isset($APIdata['MI'])) {
            $score['MI'] = array_count_values($APIdata['MI']);
        } else {
            $score['MI'] = array();
        }

        if (isset($APIdata['aptitude'])) {
            $score['aptitude'] = array_count_values($APIdata['aptitude']);
        } else {
            $score['aptitude'] = array();
        }

        if (isset($APIdata['personality'])) {
            $score['personality'] = array_count_values($APIdata['personality']);
        } else {
            $score['personality'] = array();
        }

        if (isset($APIdata['interest'])) {
            $score['interest'] = array_count_values($APIdata['interest']);
        } else {
            $score['interest'] = array();
        }

        $finalScore['MI'] = $score['MI'] + $allMI;
        $finalScore['aptitude'] = $score['aptitude'] + $allApptitude;
        $finalScore['personality'] = $score['personality'] + $allPersonality;
        $finalScore['interest'] = $score['interest'] + $allInterest;

        //MI Scale
        if (!empty($finalScore['MI'])) {
            $objMIScale = new MultipleIntelligentScale();
            foreach ($finalScore['MI'] as $mi => $score) {
                $scale['MI'][$mi] = $objMIScale->calculateMIHML($mi, $score);
            }
        }
        //Apptitude Scale
        if (!empty($finalScore['aptitude'])) {
            $objApptitudeScale = new ApptitudeTypeScale();
            foreach ($finalScore['aptitude'] as $apptitude => $scoreapptitude) {
                $scale['aptitude'][$apptitude] = $objApptitudeScale->calculateApptitudeHML($apptitude, $scoreapptitude);
            }
        }

        //Personality Scale
        if (!empty($finalScore['personality'])) {
            $objPersonalityScale = new PersonalityScale();
            foreach ($finalScore['personality'] as $personality => $scorepersonality) {
                $scale['personality'][$personality] = $objPersonalityScale->calculatePersonalityHML($personality, $scorepersonality);
            }
        }

        $final = array('APIscore' => $finalScore, 'APIscale' => $scale, 'APIdataSlug' => array_count_values($APIdataSlug));
        return $final;
    }

    public static function getCareerMappingFromSystem() {
        $careerMapping = DB::table('pro_tcm_teenager_career_mapping')->get();
        return $careerMapping;
    }

    public static function getCareerMappingFromSystemByProfession($professionId) {
        return DB::table('pro_tcm_teenager_career_mapping')->where('tcm_profession', $professionId)->first();
    }

    public static function getSpecificCareerMappingFromSystem($professionId) {
        $careerMapping = DB::table('pro_tcm_teenager_career_mapping')->where('tcm_profession', $professionId)->first();
        return $careerMapping;
    }

    public static function getPayment($userid) {
        $paymentstatus = DB::select(DB::raw("SELECT
                                            tn_order_status
                                            FROM " . config::get('databaseconstants.TBL_TEENAGER_TRANSACTION') . " where tn_userid=" . $userid));
        $paymentStauts = 0;
        if (isset($paymentstatus) && !empty($paymentstatus)) {
            if ($paymentstatus[0]->tn_order_status == 1) {
                $paymentStauts = 1;
            }
        }
        return $paymentStauts;
    }

    public static function getAuthUserData($userid) {
        return DB::table('pro_t_teenagers')->where('id', $userid)->first();
    }

    public static function getInterestData($interest) {
        $interestData = DB::table('pro_it_interest_types')->where('it_name', '=', $interest)->first();
        return $interestData;
    }

    public static function getApptitudeData($apptitude) {
        $apptitudeData = DB::table('pro_apt_apptitude_types')->where('apt_name', '=', $apptitude)->first();
        return $apptitudeData;
    }

    public static function getMIData($mi) {
        $MIData = DB::table('pro_mit_multiple_intelligence_types')->where('mit_name', '=', $mi)->first();
        return $MIData;
    }

    public static function getPersonalityData($personality) {
        $personalityData = DB::table('pro_pt_personality_types')->where('pt_name', '=', $personality)->first();
        return $personalityData;
    }

    public static function getTeenagerImageUrl($image, $type = 'thumb') {
        if ($type == 'thumb') {
            $path = Config::get('constant.TEEN_THUMB_IMAGE_UPLOAD_PATH');
        } else {
            $path = Config::get('constant.TEEN_ORIGINAL_IMAGE_UPLOAD_PATH');
        }
        if ($image != '' && isset($image) && Storage::size($path . $image) > 0) {
            $teenagerImage = $image;
        } else {
            $teenagerImage = 'proteen-logo.png';
        }
        return Storage::url($path . $teenagerImage);
    }

    public static function getEmailaddress($email) {
        $tbl = DB::table('pro_t_teenagers')->where('id', $email)->get();
        return $tbl;
    }

    public static function getActiveSchoolById($id) {
        $objSchool = new Schools();
        $school = $objSchool->getActiveSchool($id);
        return $school;
        //return array();
    }

    //Get Badges From level 4 attempted questions.
    public static function earnBadges($teenagerId, $professionId) {
        $objProfession = new Professions();
        $imagePoint = $questionCount = '';
        $getattepmtedQuestionOfProfession = $objProfession->getattepmtedQuestionOfProfession($teenagerId, $professionId);
        if (count($getattepmtedQuestionOfProfession) > 0 && count($getattepmtedQuestionOfProfession) < 6) {
            $questionCount = count($getattepmtedQuestionOfProfession);
            $imagePoint = asset(Config::get('constant.BADGES_ORIGINAL_IMAGE_UPLOAD_PATH') . $questionCount . '.png');
        }
        $totalProfession = $imagePoint;
        return $totalProfession;
    }

    //Get Badges From level 4 attempted questions.
    public static function getTotalScoreOfProfession($teenagerId, $professionId) {
        $objProfession = new Professions();
        $imagePoint = '';
        $points = [];
        $getattepmtedQuestionOfProfession = $objProfession->getattepmtedQuestionOfProfession($teenagerId, $professionId);
        if (isset($getattepmtedQuestionOfProfession) && !empty($getattepmtedQuestionOfProfession)) {
            foreach ($getattepmtedQuestionOfProfession as $earnValue) {
                $points[] = $earnValue->earned_points;
            }
            $totalEarnedPoints = array_sum($points);
        } else {
            $totalEarnedPoints = 0;
        }
        return $totalEarnedPoints;
    }

    //Get all score of profession.
    public static function level4Booster($professionId, $userid = null) {
        $objProfession = new Professions();
        $competing = $highestScore = $yourScore = $yourRank = 0;
        $arrayKeys = [];
        $totalPointCollection = $objProfession->getProfessionLevel4AllTypeTotalPoints($professionId);
        $getLevel4AllScore = $objProfession->getLevel4AllScore($professionId);
        $getLevel4AllScoreUser = $objProfession->getLevel4AllScoreForParent($professionId);


        if (isset($totalPointCollection) && !empty($totalPointCollection)) {
            $totalPobScore = $totalPointCollection['totalBasic'] + $totalPointCollection['totalIntermediate'] + $totalPointCollection['totalAdvance'];
        } else {
            $totalPobScore = 0;
        }

        $allData = [];
        $totalCompetingParent = $objProfession->getCompetingUserListForTeenager($professionId,$userid);
        foreach ($totalCompetingParent AS $key => $value) {
            foreach ($getLevel4AllScoreUser['level4TotalPoints'] AS $k => $val) {
                if ($k == $value->parent_id) {
                    $allData[] = $val;
                }
            }
        }


        //$totalCompeting2 = $objProfession->getTotalCompetingFromLevel3($professionId);
        $totalCompeting = $objProfession->getTotalCompetingOfProfession($professionId);

        $level4Competing = [];
        if (isset($totalCompeting) && !empty($totalCompeting)) {
            foreach ($totalCompeting as $teenId) {
                if (isset($teenId->teenager_id)) {
                    $level4Competing[$teenId->teenager_id]['yourScore'] = (isset($getLevel4AllScore['level4TotalPoints'][$teenId->teenager_id]) && $getLevel4AllScore['level4TotalPoints'][$teenId->teenager_id] > 0) ? $getLevel4AllScore['level4TotalPoints'][$teenId->teenager_id] : 0;
                    $level4Competing[$teenId->teenager_id]['highestScore'] = (isset($getLevel4AllScore['level4TotalPoints']) && !empty($getLevel4AllScore['level4TotalPoints'])) ? max($getLevel4AllScore['level4TotalPoints']) : 0;
                    if ($teenId->t_photo != '') {
                        if (file_exists(Config::get('constant.TEEN_THUMB_IMAGE_UPLOAD_PATH') . $teenId->t_photo)) {
                            $teenPhoto = asset(Config::get('constant.TEEN_THUMB_IMAGE_UPLOAD_PATH') . $teenId->t_photo);
                        } else {
                            $teenPhoto = asset(Config::get('constant.TEEN_THUMB_IMAGE_UPLOAD_PATH') . "proteen-logo.png");
                        }
                    } else {
                        $teenPhoto = asset(Config::get('constant.TEEN_THUMB_IMAGE_UPLOAD_PATH') . "proteen-logo.png");
                    }
                    $level4Competing[$teenId->teenager_id]['profile_pic'] = $teenPhoto;
                    $level4Competing[$teenId->teenager_id]['name'] = $teenId->t_name;
                    $level4Competing[$teenId->teenager_id]['teenager_id'] = $teenId->teenager_id;
                    $level4Competing[$teenId->teenager_id]['teenager_unique_id'] = $teenId->t_uniqueid;
                    $level4Competing[$teenId->teenager_id]['t_phone'] = $teenId->t_phone;
                    $level4Competing[$teenId->teenager_id]['t_email'] = $teenId->t_email;
                    $level4Competing[$teenId->teenager_id]['is_search_on'] = $teenId->is_search_on;
                    $level4Competing[$teenId->teenager_id]['competitors'] = (isset($totalCompeting) && !empty($totalCompeting))?count($totalCompeting):0;
                }
            }
        }

        if (!empty($level4Competing)) {
            arsort($level4Competing);
            $point = 1;
            foreach ($level4Competing as $key => $rankvalue) {
                if ($key == $userid) {
                    $allData[] = (isset($level4Competing[$key]['yourScore']) && $level4Competing[$key]['yourScore'] > 0) ? $level4Competing[$key]['yourScore'] : 0;
                }
                $level4Competing[$key]['rank'] = (isset($level4Competing[$key]['highestScore']) && $level4Competing[$key]['highestScore'] > 0) ? $point : 0;
                $point++;
                unset($level4Competing[$key]['profile_pic']);
                unset($level4Competing[$key]['name']);
                unset($level4Competing[$key]['teenager_id']);
                unset($level4Competing[$key]['teenager_unique_id']);
                unset($level4Competing[$key]['t_phone']);
                unset($level4Competing[$key]['t_email']);
                unset($level4Competing[$key]['is_search_on']);
                unset($level4Competing[$key]['competitors']);
            }
        }

        if (isset($totalCompeting) && !empty($totalCompeting)) {
            $competing = (isset($totalCompeting[0])) ? count($totalCompeting) : 0;
        }
        $getattepmtedQuestionOfProfession = $objProfession->getLevel4AllScore($professionId);

        if (isset($getattepmtedQuestionOfProfession['level4TotalPoints']) && !empty($getattepmtedQuestionOfProfession['level4TotalPoints'])) {
            $highestScore = max($getattepmtedQuestionOfProfession['level4TotalPoints']);
            $yourScore = (isset($getattepmtedQuestionOfProfession['level4TotalPoints'][$userid])) ? $getattepmtedQuestionOfProfession['level4TotalPoints'][$userid] : 0;
            //$arrayKeys = array_keys($getattepmtedQuestionOfProfession['level4TotalPoints']);
            //$yourRank = (isset($arrayKeys) && !empty($arrayKeys) && in_array($userid, $arrayKeys)) ? (1 + array_search($userid, $arrayKeys)) : 0;
            $yourRank = (isset($level4Competing[$userid]['rank']) && !empty($level4Competing[$userid]))? $level4Competing[$userid]['rank'] : 0 ;
        } else {
            $getattepmtedQuestionOfProfession = [];
        }
        rsort($allData);
        $data = [];
        $data['competing'] = $competing;
        $data['yourScore'] = $yourScore;
        $data['highestScore'] = $highestScore;
        $data['yourRank'] = $yourRank;
        $data['totalPobScore'] = $totalPobScore;
        $data['allData'] = $allData;

        return $data;
    }

    public static function getTemplateNo($teenagerId, $professionId) {
        $objProfession = new Professions();
        $objTeenager = new Teenagers();
        $getResult = $objProfession->getTemplateNo($teenagerId, $professionId);
        $professionDetail = $objProfession->getProfessionDetail($professionId);
        $teenagerDataDetail = $objTeenager->getTeenagersData($teenagerId);
        $image2 = 'proteen-logo.png';
        $message = $message3 = '';
        $message2 = 'no';
        if (isset($teenagerDataDetail) && !empty($teenagerDataDetail)) {
            $teenagerName = trim($teenagerDataDetail->t_name) . "! ";
            $image = $teenagerDataDetail->t_photo;
            $path = Config::get('constant.TEEN_ORIGINAL_IMAGE_UPLOAD_PATH');
            if ($image != '' && file_exists($path . $image)) {
                $teenagerImage = $image;
            } else {
                $teenagerImage = 'proteen-logo.png';
            }
            $image2 = asset($path . $teenagerImage);
        }
        if (isset($professionDetail) && !empty($professionDetail)) {
            $professionName = $professionDetail->pf_name;
        }
        if (isset($getResult) && !empty($getResult)) {
            if ($getResult['totalCompletedTemplate'] == 1) {
                $message = "Wow " . ucfirst($teenagerName) . "That was a real world " . ucfirst($professionName) . " experience. Keep playing!";
            } else if ($getResult['totalCompletedTemplate'] == 2) {
                $message = "Wow " . ucfirst($teenagerName) . "That was a real world " . ucfirst($professionName) . " experience. Keep playing!";
                $message .= " Based on your personality, being a " . ucfirst($professionName) . " is going to be quite <Easy / Challenging>. Go play on..";
            } else if ($getResult['totalCompletedTemplate'] == 3) {
                $message = "That's big part of the experience being " . ucfirst($professionName) . ". Keep it up! Your " . ucfirst($professionName) . " badge is one step away!";
            } else if ($getResult['totalCompletedTemplate'] == 4) {
                $desc = "Well done " . ucfirst($teenagerName) . " The " . ucfirst($professionName) . " badge is yours! Let the world know!";
                $url = url("/");
                $message = $desc;
                $message2 = "yes";
                $message3 = '<a href="javascript:void(0);" onclick="shareFacebook(\'' . $url . '\',\'' . $desc . '\',\'' . $desc . '\',\'' . $image2 . '\')" class="fb_congratulation"><i class="fa fa-facebook-official" aria-hidden="true"></i></a>';
                $message3 .= '<a href="https://plus.google.com/share?url=' . $url . '&image=' . $image2 . '" target="_blank"  class="google_congratulation"><i class="fa fa-google-plus-square" aria-hidden="true"></i></a>';
            } else {
                $message = ucfirst($teenagerName) . " You are now a rookie " . ucfirst($professionName) . " in ProTeen.";
            }
        } else {
            $message = ucfirst($teenagerName) . " You are now a rookie ProTeen " . ucfirst($professionName);
        }
        $array = [];
        $array['msg1'] = $message;
        $array['msg2'] = $message2;
        $array['msg3'] = $message3;
        return $array;
    }

    public static function getProfessionAllScore($professionId) {
        $objProfession = new Professions();
        $getattepmtedQuestionOfProfession = $objProfession->getProfessionAllScore($professionId);
        if (isset($getattepmtedQuestionOfProfession['basic']) && !empty($getattepmtedQuestionOfProfession['basic'])) {
            $getattepmtedQuestionOfProfession = $getattepmtedQuestionOfProfession['basic'];
        } else {
            $getattepmtedQuestionOfProfession = [];
        }
        return $getattepmtedQuestionOfProfession;
    }

    public static function getActiveCountryById($id) {
        $objCountry = new Country();
        $country = $objCountry->getActiveCounry($id);
        return $country;
    }

    /**
     * get youtube video ID from URL
     *
     * @param string $url
     * @return string Youtube video id or FALSE if none found. 
     */
    public static function youtube_id_from_url($url) {
        $pattern = '%^# Match any youtube URL
           (?:https?://)?  # Optional scheme. Either http or https
           (?:www\.)?      # Optional www subdomain
           (?:             # Group host alternatives
             youtu\.be/    # Either youtu.be,
           | youtube\.com  # or youtube.com
             (?:           # Group path alternatives
               /embed/     # Either /embed/
             | /v/         # or /v/
             | /watch\?v=  # or /watch\?v=
             )             # End path alternatives.
           )               # End host alternatives.
           ([\w-]{10,12})  # Allow 10-12 for 11 char youtube id.
           $%x'
        ;
        $result = preg_match($pattern, $url, $matches);
        if ($result) {
            return $matches[1];
        }
        return false;
    }

    /**
     * get Strength Type Related Info
     *
     * @param string $strengthType, $strengthSlug
     * @return array of collection
     */
    public static function getStrengthTypeRelatedInfo($strengthType, $strengthSlug) {
        $multipleIntelligence = [];
        if (!empty($strengthType) || !empty($strengthSlug)) {
            switch($strengthType) {
                case Config::get('constant.MULTI_INTELLIGENCE_TYPE'):
                    $objMultipleIntelligent = new MultipleIntelligent();
                    $mi = $objMultipleIntelligent->getMultipleIntelligenceDetailBySlug($strengthSlug);
                    if($mi) {
                        $multipleIntelligence['id'] = $mi->id;
                        $multipleIntelligence['title'] = $mi->mit_name;
                        $multipleIntelligence['slug'] = $mi->mi_slug;
                        $multipleIntelligence['logo'] = ( $mi->mit_logo != "" ) ? Storage::url(Config::get('constant.MI_THUMB_IMAGE_UPLOAD_PATH') . $mi->mit_logo) : Storage::url(Config::get('constant.MI_THUMB_IMAGE_UPLOAD_PATH') . "proteen-logo.png");
                        $multipleIntelligence['video'] = ($mi->mi_video != "") ? self::youtube_id_from_url($mi->mi_video) : "";
                        $multipleIntelligence['description'] = $mi->mi_information;
                    }
                    break;

                case Config::get('constant.APPTITUDE_TYPE'):
                    $objApptitude = new Apptitude();
                    $apptitude = $objApptitude->getApptitudeDetailBySlug($strengthSlug);
                    if($apptitude) {
                        $multipleIntelligence['id'] = $apptitude->id;
                        $multipleIntelligence['title'] = $apptitude->apt_name;
                        $multipleIntelligence['slug'] = $apptitude->apt_slug;
                        $multipleIntelligence['logo'] = ( $apptitude->apt_logo != "") ? Storage::url(Config::get('constant.APPTITUDE_THUMB_IMAGE_UPLOAD_PATH') . $apptitude->apt_logo) : Storage::url(Config::get('constant.APPTITUDE_THUMB_IMAGE_UPLOAD_PATH') . "proteen-logo.png");
                        $multipleIntelligence['video'] = ($apptitude->apt_video != "") ? self::youtube_id_from_url($apptitude->apt_video) : "";
                        $multipleIntelligence['description'] = $apptitude->ap_information;
                    }
                    break;

                case Config::get('constant.PERSONALITY_TYPE'):
                    $objPersonality = new Personality();
                    $personality = $objPersonality->getPersonalityDetailBySlug($strengthSlug);
                    if($personality) {
                        $multipleIntelligence['id'] = $personality->id;
                        $multipleIntelligence['title'] = $personality->pt_name;
                        $multipleIntelligence['slug'] = $personality->pt_slug;
                        $multipleIntelligence['logo'] = ($personality->pt_logo != "") ? Storage::url(Config::get('constant.PERSONALITY_THUMB_IMAGE_UPLOAD_PATH') . $personality->pt_logo) : Storage::url(Config::get('constant.PERSONALITY_THUMB_IMAGE_UPLOAD_PATH') . "proteen-logo.png");
                        $multipleIntelligence['video'] = ($personality->pt_video != "") ? self::youtube_id_from_url($personality->pt_video) : "";
                        $multipleIntelligence['description'] = $personality->pt_information;
                    }
                    break;

                default:
                    $multipleIntelligence = [];
            }
        } else {
            $multipleIntelligence = [];
        }
        return $multipleIntelligence;
    }

    public static function getAds($teenagerId) {
        $teenagerSponsor = DB::table('pro_ts_teenager_sponsors')->where('ts_teenager', $teenagerId)->get();
        $objSponsor = new Sponsors();
        $adsArr = [];
        $sponsorArr = [];
        if (isset($teenagerSponsor) && count($teenagerSponsor) > 0) {
            foreach ($teenagerSponsor as $key => $val) {
                $sponsorArr[] = $val->ts_sponsor;
            }
            // Get sponsor Ads
            if (!empty($sponsorArr)) {
                $sponsorAds = $objSponsor->getSponsorsAds($sponsorArr);
                if (isset($sponsorAds) && !empty($sponsorAds)) {
                    foreach ($sponsorAds as $key => $ads) {
                        $adsData = [];
                        $adsData['id'] = $ads->id;
                        $adsData['sizeType'] = $ads->sa_size_type;
                        $adsData['image'] = $ads->sa_image;
                        // if ($ads->sa_image != '') {
                        //     $adsData['image'] = Storage::url(Config::get('constant.SA_ORIGINAL_IMAGE_UPLOAD_PATH') . $ads->sa_image);
                        // } else {
                        //     $adsData['image'] = Storage::url(Config::get('constant.SA_ORIGINAL_IMAGE_UPLOAD_PATH') . 'proteen-logo.png');
                        // }
                        $adsArr[] = $adsData;
                    }
                }
            } 
        }
        return $adsArr;
    }

    //check Any Level Record from tlcr table.
    public static function checkTeenagerLevelRecord($level, $teenagerId) {
        $checkTeenagerRecord = DB::table(config::get('databaseconstants.TBL_TEENAGER_LEVEL_COMPLETE_RECORD'))->where('tlcr_teenager', $teenagerId)->where('tlcr_level', $level)->first();
        return $checkTeenagerRecord;
    }

    //get hint for different level
    public static function getHint($level, $dataId = 0) {
        if ($dataId > 0) {
            $hint = DB::table('hint')->where('applied_level', $level)->where('data_id', $dataId)->where('deleted', 1)->get();
            if (!empty($hint)) {
                return $hint;
            }
        }

        if (empty($hint)) {
            $hint = DB::table('hint')->where('applied_level', $level)->where('data_id', '=', 0)->where('deleted', 1)->get();
            return $hint;
        }
    }

    public static function type() {
        $type = array('1' => 'Ad', '2' => 'Event', '3' => 'Scholarship Program');
        return $type;
    }

    public static function getLevels() {
        $objLevels = new SystemLevels();
        $levels = $objLevels->getLevelname();

        return $levels;
    }

    public static function getEmailIds($id) {
        $objEmail = new Teenagers();
        $emails = $objEmail->getteenagerEmail($id);
        return $emails;
    }

    //Get teenager Timeline
    public static function getTeenagerTimeLine($teenagerid) {
        $finalData = array();
        $teenager = DB::select(DB::raw("SELECT
                                            id,created_at
                                            FROM " . config::get('databaseconstants.TBL_TEENAGERS') . " where deleted = 1 AND id=" . $teenagerid));
        $teenagerLevel1Answer = DB::select(DB::raw("SELECT
                                            l1ans_teenager,created_at
                                            FROM " . config::get('databaseconstants.TBL_LEVEL1_ANSWERS') . " where l1ans_teenager=" . $teenagerid));

        $teenagerLevel2Answer = DB::select(DB::raw("SELECT
                                            l2ans_teenager,created_at
                                            FROM " . config::get('databaseconstants.TBL_LEVEL2_ANSWERS') . " where l2ans_teenager=" . $teenagerid));

        $teenagerLevel3Answer = DB::select(DB::raw("SELECT
                                            tpa_teenager,created_at
                                            FROM " . config::get('databaseconstants.TBL_TEENAGER_PROFESSION_ATTEMPTED') . " where tpa_teenager=" . $teenagerid . ' order by created_at'));

        $teenagerLevel4Answer = DB::select(DB::raw("SELECT
                                            teenager_id,created_at
                                            FROM " . config::get('databaseconstants.TBL_LEVEL4_ANSWERS') . " where teenager_id=" . $teenagerid . ' order by created_at'));

        if (!empty($teenager)) {
            $finalData['Registered with ProTeen'] = $teenager[0]->created_at;
        }
        if (!empty($teenagerLevel1Answer)) {
            $finalData['Started Playing Level1'] = $teenagerLevel1Answer[0]->created_at;
        }
        if (!empty($teenagerLevel2Answer)) {
            $finalData['Started Playing Level2'] = $teenagerLevel2Answer[0]->created_at;
        }
        if (!empty($teenagerLevel3Answer)) {
            $finalData['Started Playing Level3'] = $teenagerLevel3Answer[0]->created_at;
        }
        if (!empty($teenagerLevel4Answer)) {
            $finalData['Started Playing Level4'] = $teenagerLevel4Answer[0]->created_at;
        }

        return $finalData;
    }

    //Get teenager meta data
    public static function getTeenagerMetaData($teenagerid, $meta_id = 0) {
        $finalData = array();
        if ($meta_id > 0) {
            $result = DB::table(config::get('databaseconstants.TBL_TEENAGER_META_DATA'))->where('tmd_teenager', $teenagerid)->where('tmd_meta_id', $meta_id)->get();
        } else {
            $result = DB::table(config::get('databaseconstants.TBL_TEENAGER_META_DATA'))->where('tmd_teenager', $teenagerid)->get();
        }
        if (isset($result) && !empty($result)) {
            foreach ($result as $key => $data) {
                if ($data->tmd_meta_id == 1) {
                    $finalData['achievement'][] = array('meta_value_id' => $data->id, 'meta_id' => $data->tmd_meta_id, 'meta_value' => $data->tmd_meta_value);
                } elseif ($data->tmd_meta_id == 2) {
                    $finalData['education'] [] = array('meta_value_id' => $data->id, 'meta_id' => $data->tmd_meta_id, 'meta_value' => $data->tmd_meta_value);
                }
            }
        }
        return $finalData;
    }

    public static function getTeenagerEducationData($teenagerId) {
        return DB::table(config::get('databaseconstants.TBL_TEENAGER_META_DATA'))->where(['tmd_teenager' => $teenagerId, 'tmd_meta_id' => 2])->first();
    }

    public static function getTeenagerAchievementData($teenagerId) {
        return DB::table(config::get('databaseconstants.TBL_TEENAGER_META_DATA'))->where(['tmd_teenager' => $teenagerId, 'tmd_meta_id' => 1])->first();
    }

    public static function getPriviouslyAttemptedQuestionId($teenagerId) {

        $answers = DB::table(config::get('databaseconstants.TBL_LEVEL1_ANSWERS') . " AS answer")
                ->join(config::get('databaseconstants.TBL_LEVEL1_ACTIVITY') . " AS acitvity", 'answer.l1ans_activity', '=', 'acitvity.id')
                ->selectRaw('answer.* , acitvity.l1ac_text')
                ->where('l1ans_teenager', $teenagerId)
                ->orderBy('created_at', 'desc')
                ->first();

        //$answers = DB::table(config::get('databaseconstants.TBL_LEVEL1_ANSWERS'))->where('l1ans_teenager', $teenagerId)->orderBy('created_at', 'desc')->first();
        return $answers;
//        if (isset($answers) && !empty($answers)) {
//            $teenagerLastAttemptQuestionId = $answers->l1ans_activity;
//        } else {
//            $teenagerLastAttemptQuestionId = 0;
//        }
        // return $teenagerLastAttemptQuestionId;
    }

    public static function getQuestionData($activityId) {

        $answers = DB::table(config::get('databaseconstants.TBL_LEVEL1_ACTIVITY') . " AS acitvity")
                ->selectRaw('acitvity.l1ac_text')
                ->where('acitvity.id', $activityId)
                ->first();
        return $answers;
    }

    //Calculate Level1 question trend
    public static function calculateTrendForLevel1($activityId, $type) {
        $Percenticon = '';
        $questionArray = ['1' => ['No', 'Not Sure'], '2' => ['As per my expectations', 'Below my expectations'], '3' => ['Yes', 'Sometimes'],
            '4' => ['Sometimes', 'No'], '5' => ['Sometimes', 'Rarely'], '6' => ['Sometimes', 'Rarely'], '7' => ['Maybe', 'No'],
            '8' => ['Many', 'Not Sure'], '9' => ['Yes'], '10' => ['Yes'], '11' => ['Confused', 'None'], '12' => ['Few', 'None'],
            '13' => ['Disagree', 'Sometimes'], '14' => ['No', 'Not Sure'], '15' => ['Yes', 'Sometimes'], '16' => ['Yes', 'Sometimes']];

        $sPointArray = ['1' => ['No' => 278, 'Not Sure' => 362, 'Yes' => 548], '2' => ['As per my expectations' => 559, 'Below my expectations' => 236, 'Above my expectations' => 393], '3' => ['Yes' => 491, 'Sometimes' => 417, 'No' => 280],
            '4' => ['Sometimes' => 380, 'No' => 358, 'Yes' => 450], '5' => ['Sometimes' => 282, 'Rarely' => 299, 'Often' => 607], '6' => ['Sometimes' => 337, 'Rarely' => 382, 'Often' => 469], '7' => ['Maybe' => 515, 'No' => 241, 'Yes' => 432],
            '8' => ['Many' => 442, 'Not Sure' => 253, 'Few' => 493], '9' => ['Yes' => 644, 'Sometimes' => 332, 'No' => 212], '10' => ['Yes' => 769, 'Sometimes' => 188, 'No' => 231], '11' => ['Confused' => 440, 'None' => 163, 'Very Clear' => 585], '12' => ['Few' => 921, 'Many' => 198, 'None' => 69],
            '13' => ['Disagree' => 641, 'Sometimes' => 273, 'Agree' => 274], '14' => ['No' => 430, 'Not Sure' => 402, 'Yes' => 356], '15' => ['Yes' => 581, 'Sometimes' => 299, 'No' => 308], '16' => ['Yes' => 563, 'Sometimes' => 344, 'No' => 281]];

        $answers = DB::table(config::get('databaseconstants.TBL_LEVEL1_ANSWERS') . " AS answer")
                ->join(config::get('databaseconstants.TBL_LEVEL1_OPTIONS') . " AS options", 'answer.l1ans_answer', '=', 'options.id')
                ->selectRaw('answer.* , options.*')
                ->where('l1ans_activity', $activityId)
                ->get();

        //Level1 options name
        $option = DB::table(config::get('databaseconstants.TBL_LEVEL1_OPTIONS') . " AS answer")->where('l1op_activity', $activityId)->get();
        if (!empty($option)) {
            foreach ($option as $key => $data) {
                $optionCount[] = $data->l1op_option;
            }
        }
        if (!empty($answers)) {
            foreach ($answers as $key => $data) {
                $answerCount[] = $data->l1op_option;
            }
        }

        $Percentlevel1 = array();
        $Percentlevel1Array = array();
        $total1 = (isset($answerCount) && !empty($answerCount))? count($answerCount):0;
        $total2 = (isset($sPointArray[$activityId])) ? array_sum($sPointArray[$activityId]) : 0;
        $total = $total1 + $total2;
            $count = (isset($answerCount) && !empty($answerCount))? array_count_values($answerCount):[]; // count individual options for selected activityId
            if (isset($optionCount) && !empty($optionCount)) {
                foreach ($optionCount as $key => $qualityIdName) {
                    $calFromArray = (isset($count[$qualityIdName])) ? $count[$qualityIdName] : 0;
                    $calFromStaticArray = (isset($sPointArray[$activityId][$qualityIdName])) ? $sPointArray[$activityId][$qualityIdName] : 0;
                    $pointScreen = $calFromArray + $calFromStaticArray;
                    $Percentlevel1[] = array('label'=>$qualityIdName,'percentage'=>($total > 0) ? (($pointScreen * 100) / $total) : 0); // convert level1 option in percentage
                    $Percentlevel1Array[$qualityIdName] = ($total > 0) ? (($pointScreen * 100) / $total) : 0; // convert level1 option in percentage
                }
                $firstPoint = (isset($questionArray[$activityId][0]) && isset($Percentlevel1Array[$questionArray[$activityId][0]]) && ($Percentlevel1Array[$questionArray[$activityId][0]] != '' || $Percentlevel1Array[$questionArray[$activityId][0]] > 0 ) ) ? $Percentlevel1Array[$questionArray[$activityId][0]] : 0;
                $secondPoint = (isset($questionArray[$activityId][1]) && isset($Percentlevel1Array[$questionArray[$activityId][1]]) && ($Percentlevel1Array[$questionArray[$activityId][1]] != '' || $Percentlevel1Array[$questionArray[$activityId][1]] > 0 ) ) ? $Percentlevel1Array[$questionArray[$activityId][1]] : 0;

                if (isset($firstPoint) && $firstPoint > 0) {
                    if (isset($secondPoint) && $secondPoint > 0) {
                        $stringName = $questionArray[$activityId][0] . " or " . $questionArray[$activityId][1];
                    } else {
                        $stringName = $questionArray[$activityId][0];
                    }
                    $trendsTotalPoint = round($firstPoint + $secondPoint);
                    $returnString = $trendsTotalPoint . " % Teens voted " . $stringName . " to the question ";
                } else {
                    $returnString = "No any trends for this survey";
                }
            } else {
                $returnString = "No any trends for this survey";
            }
        if ($type == 1) {
            return $Percentlevel1;
        } else if ($type == 2) {
            return $returnString;
        }

    }

    public static function calculateTrendForLevel1Admin($activityId,$genderid,$ageData) {
        $Percenticon = '';
        $questionArray = ['1' => ['No', 'Not Sure'], '2' => ['As per my expectations', 'Below my expectations'], '3' => ['Yes', 'Sometimes'],
            '4' => ['Sometimes', 'No'], '5' => ['Sometimes', 'Rarely'], '6' => ['Sometimes', 'Rarely'], '7' => ['Maybe', 'No'],
            '8' => ['Many', 'Not Sure'], '9' => ['Yes'], '10' => ['Yes'], '11' => ['Confused', 'None'], '12' => ['Few', 'None'],
            '13' => ['Disagree', 'Sometimes'], '14' => ['No', 'Not Sure'], '15' => ['Yes', 'Sometimes'], '16' => ['Yes', 'Sometimes']];

        $sPointArray = ['1' => ['No' => 278, 'Not Sure' => 362, 'Yes' => 548], '2' => ['As per my expectations' => 559, 'Below my expectations' => 236, 'Above my expectations' => 393], '3' => ['Yes' => 491, 'Sometimes' => 417, 'No' => 280],
            '4' => ['Sometimes' => 380, 'No' => 358, 'Yes' => 450], '5' => ['Sometimes' => 282, 'Rarely' => 299, 'Often' => 607], '6' => ['Sometimes' => 337, 'Rarely' => 382, 'Often' => 469], '7' => ['Maybe' => 515, 'No' => 241, 'Yes' => 432],
            '8' => ['Many' => 442, 'Not Sure' => 253, 'Few' => 493], '9' => ['Yes' => 644, 'Sometimes' => 332, 'No' => 212], '10' => ['Yes' => 769, 'Sometimes' => 188, 'No' => 231], '11' => ['Confused' => 440, 'None' => 163, 'Very Clear' => 585], '12' => ['Few' => 921, 'Many' => 198, 'None' => 69],
            '13' => ['Disagree' => 641, 'Sometimes' => 273, 'Agree' => 274], '14' => ['No' => 430, 'Not Sure' => 402, 'Yes' => 356], '15' => ['Yes' => 581, 'Sometimes' => 299, 'No' => 308], '16' => ['Yes' => 563, 'Sometimes' => 344, 'No' => 281]];

        /*$answers = DB::table(config::get('databaseconstants.TBL_LEVEL1_ANSWERS') . " AS answer ")
                ->join(config::get('databaseconstants.TBL_LEVEL1_OPTIONS') . " AS options ", 'answer.l1ans_answer', '=', 'options.id')
                ->selectRaw('answer.* , options.*')
                ->where('l1ans_activity', $activityId)
                ->get();*/

        $whereStr = '';
        $whereArray = [];
        $whereArray[] = 'l1ans_activity = '.$activityId;
        if (isset($genderid) && $genderid != '') {
            $whereArray[] = 'teenager.t_gender ='.$genderid;
        }
        if (!empty($whereArray)) {
            $whereStr = implode(" AND ", $whereArray);
        }
        $allAnswers = DB::table(config::get('databaseconstants.TBL_LEVEL1_ANSWERS') . " AS answer")
                ->join(config::get('databaseconstants.TBL_LEVEL1_OPTIONS') . " AS options", 'answer.l1ans_answer', '=', 'options.id')
                ->join(config::get('databaseconstants.TBL_TEENAGERS') . " AS teenager", 'answer.l1ans_teenager', '=', 'teenager.id')
                ->selectRaw('answer.* , options.*,teenager.t_gender,teenager.t_birthdate')
                ->whereRaw($whereStr)
                ->get();
        $answers = [];
        $ageArr = [];
        if (strpos($ageData, '-') !== false) {
            $ageArr = explode("-",$ageData);
        }
        foreach($allAnswers AS $k => $ans) {
            $age = '';
            $interval = date_diff(date_create(), date_create($ans->t_birthdate));
            $age = $interval->format("%y");
            if (!empty($ageArr)) {
                if($age == $ageArr[0] || $age == $ageArr[1]) {
                    $answers[] = $ans;
                }
            } elseif ($ageData == '13' || $ageData == '20') {
                if($ageData == '13') {
                    if ($age < 13) {
                        $answers[] = $ans;
                    }
                } elseif($ageData == '20') {
                    if ($age > 20) {
                        $answers[] = $ans;
                    }
                }
            } else {
                $answers[] = $ans;
            }
        }

        //Level1 options name
        $option = DB::table(config::get('databaseconstants.TBL_LEVEL1_OPTIONS') . " AS answer")->where('l1op_activity', $activityId)->get();
        if (!empty($option)) {
            foreach ($option as $key => $data) {
                $optionCount[] = $data->l1op_option;
            }
        }
        if (!empty($answers)) {
            foreach ($answers as $key => $data) {
                $answerCount[] = $data->l1op_option;
            }
        }

        $total1 = (isset($answerCount) && !empty($answerCount))? count($answerCount):0;
        $total2 = (isset($sPointArray[$activityId])) ? array_sum($sPointArray[$activityId]) : 0;
        $total = $total1 + $total2;
            $count = (isset($answerCount) && !empty($answerCount))? array_count_values($answerCount):[]; // count individual options for selected activityId
            if (isset($optionCount) && !empty($optionCount)) {
                foreach ($optionCount as $key => $qualityIdName) {
                    $calFromArray = (isset($count[$qualityIdName])) ? $count[$qualityIdName] : 0;
                    $calFromStaticArray = (isset($sPointArray[$activityId][$qualityIdName])) ? $sPointArray[$activityId][$qualityIdName] : 0;
                    $pointScreen = $calFromArray + $calFromStaticArray;
                    $Percentlevel1[$qualityIdName] = ($total > 0) ? (($pointScreen * 100) / $total) : 0; // convert level1 option in percentage
                }

                $firstPoint = (isset($questionArray[$activityId][0]) && isset($Percentlevel1[$questionArray[$activityId][0]]) && ($Percentlevel1[$questionArray[$activityId][0]] != '' || $Percentlevel1[$questionArray[$activityId][0]] > 0 ) ) ? $Percentlevel1[$questionArray[$activityId][0]] : 0;
                $secondPoint = (isset($questionArray[$activityId][1]) && isset($Percentlevel1[$questionArray[$activityId][1]]) && ($Percentlevel1[$questionArray[$activityId][1]] != '' || $Percentlevel1[$questionArray[$activityId][1]] > 0 ) ) ? $Percentlevel1[$questionArray[$activityId][1]] : 0;
            } else {
                $returnString = "No any trends for this survey";
            }
        return array('trend' => $Percentlevel1, 'total' => $total,'anstotal' => $total1,'alltotal' => $total2);
    }

    public static function calculateTrendForLevel1Gender($activityId, $genderid) {
        $Percenticon = '';

        $answers = DB::table(config::get('databaseconstants.TBL_LEVEL1_ANSWERS') . " AS answer")
                ->join(config::get('databaseconstants.TBL_LEVEL1_OPTIONS') . " AS options", 'answer.l1ans_answer', '=', 'options.id')
                ->join(config::get('databaseconstants.TBL_TEENAGERS') . " AS teenager", 'answer.l1ans_teenager', '=', 'teenager.id')
                ->selectRaw('answer.* , options.*,teenager.t_gender')
                ->where('l1ans_activity', $activityId)
                ->where('teenager.t_gender', $genderid)
                ->where('teenager.deleted', 1)
                ->get();

        //Level1 options name


        $option = DB::table(config::get('databaseconstants.TBL_LEVEL1_OPTIONS') . " AS options")->select('id', 'options.l1op_activity', 'options.l1op_option')->get();

        if (!empty($option)) {
            foreach ($option as $key => $data) {
                if ($data->l1op_activity == $activityId)
                    $optionCount[] = $data->l1op_option;
            }
        }

        if (!empty($answers)) {
            foreach ($answers as $key => $data) {
                $answerCount[] = $data->l1op_option;
            }
        }
        if (isset($answerCount) && $answerCount !== '') {
            $total = count($answerCount); // count total number of options
            $count = array_count_values($answerCount); // count individual  options for selected activityId
            if (isset($optionCount) && $optionCount !== '') {
                foreach ($optionCount as $key => $qualityIdName) {
                    $Percentlevel1[$qualityIdName] = (isset($count[$qualityIdName])) ? round(($count[$qualityIdName] * 100) / $total, 2) : 0; // convert level1 option in percentage
                }
            } else {
                $Percentlevel1 = array('None Activty are availble for graph' => 0);
            }
        } else {
            $Percentlevel1 = array('This Question Is Not Attentded By Any One' => 0);
        }

        return $Percentlevel1;
    }

    public static function calculateTrendForLevel1School($activityId, $school) {
        $Percenticon = '';

        $answers = DB::table(config::get('databaseconstants.TBL_LEVEL1_ANSWERS') . " AS answer")
                ->join(config::get('databaseconstants.TBL_LEVEL1_OPTIONS') . " AS options", 'answer.l1ans_answer', '=', 'options.id')
                ->join(config::get('databaseconstants.TBL_TEENAGERS') . " AS teenager", 'answer.l1ans_teenager', '=', 'teenager.id')
                ->selectRaw('answer.* , options.*,teenager.t_gender')
                ->where('l1ans_activity', $activityId)
                ->where('teenager.t_school', $school)
                ->where('teenager.deleted', 1)
                ->get();

        //Level1 options name


        $option = DB::table(config::get('databaseconstants.TBL_LEVEL1_OPTIONS') . " AS options")->select('id', 'options.l1op_activity', 'options.l1op_option')->get();

        if (!empty($option)) {
            foreach ($option as $key => $data) {
                if ($data->l1op_activity == $activityId)
                    $optionCount[] = $data->l1op_option;
            }
        }

        if (!empty($answers)) {
            foreach ($answers as $key => $data) {
                $answerCount[] = $data->l1op_option;
            }
        }
        if (isset($answerCount) && $answerCount !== '') {
            $total = count($answerCount); // count total number of options
            $count = array_count_values($answerCount); // count individual  options for selected activityId
            if (isset($optionCount) && $optionCount !== '') {
                foreach ($optionCount as $key => $qualityIdName) {
                    $Percentlevel1[$qualityIdName] = (isset($count[$qualityIdName])) ? round(($count[$qualityIdName] * 100) / $total, 2) : 0; // convert level1 option in percentage
                }
            } else {
                $Percentlevel1 = array('None Activty are availble for graph' => 0);
            }
        } else {
            $Percentlevel1 = array('This Question Is Not Attentded By Any One' => 0);
        }

        return $Percentlevel1;
    }

    public static function calculateTrendForLevel1Class($activityId, $class_id, $school) {
        $Percenticon = '';

        $answers = DB::table(config::get('databaseconstants.TBL_LEVEL1_ANSWERS') . " AS answer")
                ->join(config::get('databaseconstants.TBL_LEVEL1_OPTIONS') . " AS options", 'answer.l1ans_answer', '=', 'options.id')
                ->join(config::get('databaseconstants.TBL_TEENAGERS') . " AS teenager", 'answer.l1ans_teenager', '=', 'teenager.id')
                ->selectRaw('answer.* , options.*,teenager.t_gender, teenager.deleted, teenager.t_school, teenager.t_class')
                ->where('l1ans_activity', $activityId)
                ->where('teenager.t_school', $school)
                ->where('teenager.t_class', $class_id)
                ->where('teenager.deleted', 1)
                ->get();

        //Level1 options name


        $option = DB::table(config::get('databaseconstants.TBL_LEVEL1_OPTIONS') . " AS options")->select('id', 'options.l1op_activity', 'options.l1op_option')->get();

        if (!empty($option)) {
            foreach ($option as $key => $data) {
                if ($data->l1op_activity == $activityId)
                    $optionCount[] = $data->l1op_option;
            }
        }

        if (!empty($answers)) {
            foreach ($answers as $key => $data) {
                $answerCount[] = $data->l1op_option;
            }
        }
        if (isset($answerCount) && $answerCount !== '') {
            $total = count($answerCount); // count total number of options
            $count = array_count_values($answerCount); // count individual  options for selected activityId
            if (isset($optionCount) && $optionCount !== '') {
                foreach ($optionCount as $key => $qualityIdName) {
                    $Percentlevel1[$qualityIdName] = (isset($count[$qualityIdName])) ? round(($count[$qualityIdName] * 100) / $total, 2) : 0; // convert level1 option in percentage
                }
            } else {
                $Percentlevel1 = array('None Activty are availble for graph' => 0);
            }
        } else {
            $Percentlevel1 = array('This Question Is Not Attentded By Any One' => 0);
        }

        return $Percentlevel1;
    }

    public static function calculateTrendForLevel1Age($activityId, $from, $to) {
        $Percenticon = '';

        $answers = DB::table(config::get('databaseconstants.TBL_LEVEL1_ANSWERS') . " AS answer")
                ->join(config::get('databaseconstants.TBL_LEVEL1_OPTIONS') . " AS options", 'answer.l1ans_answer', '=', 'options.id')
                ->join(config::get('databaseconstants.TBL_TEENAGERS') . " AS teenager", 'answer.l1ans_teenager', '=', 'teenager.id')
                ->selectRaw('answer.* , options.*, teenager.deleted, teenager.t_birthdate')
                ->where('l1ans_activity', $activityId)
                ->where('teenager.deleted', 1)
                ->whereBetween('teenager.t_birthdate', array($from, $to))
                ->get();

        //Level1 options name


        $option = DB::table(config::get('databaseconstants.TBL_LEVEL1_OPTIONS') . " AS options")->select('id', 'options.l1op_activity', 'options.l1op_option')->get();

        if (!empty($option)) {
            foreach ($option as $key => $data) {
                if ($data->l1op_activity == $activityId)
                    $optionCount[] = $data->l1op_option;
            }
        }

        if (!empty($answers)) {
            foreach ($answers as $key => $data) {
                $answerCount[] = $data->l1op_option;
            }
        }
        if (isset($answerCount) && $answerCount !== '') {
            $total = count($answerCount); // count total number of options
            $count = array_count_values($answerCount); // count individual  options for selected activityId
            if (isset($optionCount) && $optionCount !== '') {
                foreach ($optionCount as $key => $qualityIdName) {
                    $Percentlevel1[$qualityIdName] = (isset($count[$qualityIdName])) ? round(($count[$qualityIdName] * 100) / $total, 2) : 0; // convert level1 option in percentage
                }
            } else {
                $Percentlevel1 = array('None Activty are availble for graph' => 0);
            }
        } else {
            $Percentlevel1 = array('This Question Is Not Attentded By Any One' => 0);
        }

        return $Percentlevel1;
    }

    //Calculate Level2 question trend
    public static function calculateTrendForLevel2($activityId,$genderid,$ageData) {


        //Level2 options value by activityId

        $whereStr = '';
        $whereArray = [];
        $whereArray[] = 'l2ans_activity = '.$activityId;
        if (isset($genderid) && $genderid != '') {
            $whereArray[] = 'teenager.t_gender ='.$genderid;
        }
        if (!empty($whereArray)) {
            $whereStr = implode(" AND ", $whereArray);
        }
        $allAnswers = DB::table(config::get('databaseconstants.TBL_LEVEL2_ANSWERS') . " AS answer")
                ->join(config::get('databaseconstants.TBL_LEVEL2_OPTIONS') . " AS options", 'answer.l2ans_answer', '=', 'options.id')
                ->join(config::get('databaseconstants.TBL_TEENAGERS') . " AS teenager", 'answer.l2ans_teenager', '=', 'teenager.id')
                ->selectRaw('answer.* , options.*,teenager.t_birthdate')
                ->whereRaw($whereStr)
                ->get();
        $answers = [];
        $ageArr = [];
        if (strpos($ageData, '-') !== false) {
            $ageArr = explode("-",$ageData);
        }
        foreach($allAnswers AS $k => $ans) {
            $age = '';
            $interval = date_diff(date_create(), date_create($ans->t_birthdate));
            $age = $interval->format("%y");
            if (!empty($ageArr)) {
                if($age == $ageArr[0] || $age == $ageArr[1]) {
                    $answers[] = $ans;
                }
            } elseif ($ageData == '13' || $ageData == '20') {
                if($ageData == '13') {
                    if ($age < 13) {
                        $answers[] = $ans;
                    }
                } elseif($ageData == '20') {
                    if ($age > 20) {
                        $answers[] = $ans;
                    }
                }
            } else {
                $answers[] = $ans;
            }
        }
       /* $answers = DB::table(config::get('databaseconstants.TBL_LEVEL2_ANSWERS') . " AS answer ")
                ->join(config::get('databaseconstants.TBL_LEVEL2_OPTIONS') . " AS options ", 'answer.l2ans_answer', '=', 'options.id')
                ->selectRaw('answer.* , options.*')
                ->where('l2ans_activity', $activityId)
                ->get();*/

        //Level1 options name by activityId
        $option = DB::table(config::get('databaseconstants.TBL_LEVEL2_OPTIONS') . " AS options")->select('id', 'options.l2op_activity', 'options.l2op_option')->where('deleted', 1)->get();

        if (!empty($option)) {
            foreach ($option as $key => $data) {
                if ($data->l2op_activity == $activityId)
                    $optionCount[] = $data->l2op_option;
            }
        }

        if (isset($answers) && !empty($answers)) {
            foreach ($answers as $key => $data) {
                $answerCount[] = $data->l2op_option;
            }
        }
        $totalCount = 0;
        if (isset($answerCount) && !empty($answerCount)) {

            $totalCount = count($answerCount);  // count total number of options
            $count = array_count_values($answerCount); // count individual  options for selected activityId
            $pointArray = array_keys($count);

            if (isset($count) && !empty($count)) {
                foreach ($optionCount as $key => $qualityIdName) {
                    $Percentlevel2[$qualityIdName] = (isset($count[$qualityIdName])) ? round(($count[$qualityIdName] * 100) / $totalCount, 2) : 0;  // convert level2 option in percentage
                }
            }
        } else {
            $Percentlevel2 = array('This Question Is Not Attentded By Any One' => 0);
        }
        return array('result' => $Percentlevel2, 'total' => $totalCount);
    }

    //Calculate Level1 Question Trend By Gender
    public static function getCalculationTrendFormGender($activityId) {


        //Level1 Details by activityId
        $answers = DB::table(config::get('databaseconstants.TBL_LEVEL1_ANSWERS') . " AS answer")
                ->join(config::get('databaseconstants.TBL_LEVEL1_OPTIONS') . " AS options", 'answer.l1ans_answer', '=', 'options.id')
                ->join(config::get('databaseconstants.TBL_TEENAGERS') . " AS teenager", 'answer.l1ans_teenager', '=', 'teenager.id')
                ->selectRaw('answer.* , options.*,teenager.t_gender')
                ->where('l1ans_activity', $activityId)
                ->get();

        //Level2 options name by activityId
        $option = DB::table(config::get('databaseconstants.TBL_LEVEL1_OPTIONS') . " AS options")->select('id', 'options.l1op_activity', 'options.l1op_option')->get();

        if (!empty($option)) {
            foreach ($option as $key => $data) {
                if ($data->l1op_activity == $activityId)
                    $optionCount[] = $data->l1op_option;
            }
        }

        // Saperate options 

        if (!empty($answers)) {
            foreach ($answers as $key => $data) {
                $answerCount['answer'][] = $data->l1op_option;
                if ($data->t_gender == 1) {
                    $answerCount['male'][] = $data->l1op_option;
                } else {
                    $answerCount['female'][] = $data->l1op_option;
                }
            }
        }


        // final male 
        if (isset($answerCount['male']) && $answerCount['male'] !== '') {

            $totalCountmale = count($answerCount['male']);  // total count for male
            $male = array_count_values($answerCount['male']);
            $maleArray = array_keys($male);

            if (isset($optionCount) && $optionCount !== '') {

                foreach ($optionCount as $key => $qualityIdName) {

                    $Percentgender['male'][$qualityIdName] = (isset($male[$qualityIdName])) ? round(($male[$qualityIdName] * 100) / $totalCountmale, 2) : 0;
                }
            }
        }

        // final female
        if (isset($answerCount['female']) && $answerCount['female'] !== '') {

            $totalCountfemale = count($answerCount['female']);
            $female = array_count_values($answerCount['female']);
            $femaleArray = array_keys($female);

            if (isset($optionCount) && $optionCount !== '') {
                foreach ($optionCount as $key => $qualityIdName) {

                    $Percentgender['female'][$qualityIdName] = (isset($female[$qualityIdName])) ? round(($female[$qualityIdName] * 100) / $totalCountfemale, 2) : 0;
                }
            }
        }





        if (isset($Percentgender['male']) && $Percentgender['male'] !== '') {
            $finalmale = $Percentgender['male'];

            foreach ($finalmale AS $key => $value) {
                $finalmale1[][$key] = $value;
            }
        } else {
            $finalmale1 = array('0' => array('--' => 0), '1' => array('This Question Is Not Attentded By Any One' => 0), '2' => array('--' => 0));
        }


        if (isset($Percentgender['female']) && $Percentgender['female'] !== '') {
            $finalfemale = $Percentgender['female'];
            foreach ($finalfemale AS $key => $value) {
                $finalfemale1[][$key] = $value;
            }
        } else {
            $finalfemale1 = array('0' => array('--' => 0), '1' => array('This Question Is Not Attentded By Any Female' => 0), '2' => array('--' => 0));
        }


        return $final = array('male' => $finalmale1, 'female' => $finalfemale1);
    }

    public static function getCredit() {
        $objConfig = new Configurations();
        $config = $objConfig->getCreditValue();

        return $config;
    }

    public static function getCfgKey($type) {
        $objConfig = new Configurations();
        $config = $objConfig->getCreditKey($type);
        $configValue = $objConfig->getCreditValue($config);
        return $configValue;
    }

    public static function setCredit($arr) {
        $objSponsor = new Sponsors();
        $credit = $objSponsor->setSponsorCredit($arr);
        return $credit;
    }

    public static function getQuality() {

        $answers = DB::table(config::get('databaseconstants.TBL_TEENAGER_ICON_QUALITIES') . " AS qualityicon")
                ->join(config::get('databaseconstants.TBL_TEENAGER_ICON') . " AS icon", 'qualityicon.tiqa_ti_id', '=', 'icon.id')
                ->selectRaw('qualityicon.id,qualityicon.tiqa_teenager,qualityicon.tiqa_quality_id,qualityicon.tiqa_response,qualityicon.tiqa_ti_id,icon.ti_icon_type')
                ->where('tiqa_response', 1)
                ->where('ti_icon_type', 4)
                ->orwhere('ti_icon_type', 1)->where('tiqa_response', 1)
                ->orwhere('ti_icon_type', 2)->where('tiqa_response', 1)
                ->get();

        $quality = DB::table(config::get('databaseconstants.TBL_LEVEL1_QUALITY') . " AS quality")->select('id', 'quality.l1qa_name')->where('deleted', 1)->get();

        if (!empty($answers)) {
            foreach ($answers as $key => $data) {
                $answerCount['answer'][] = $data->tiqa_quality_id;
                if ($data->ti_icon_type == 1) {
                    $answerCount['icon1'][] = $data->tiqa_quality_id;
                } else if ($data->ti_icon_type == 2) {
                    $answerCount['icon2'][] = $data->tiqa_quality_id;
                } else {
                    $answerCount['icon4'][] = $data->tiqa_quality_id;
                }
            }

            $combine12 = (object) array_merge_recursive((array) $answerCount['icon1'], (array) $answerCount['icon2']);
            $finalicon4 = json_encode($combine12);
            $answerCount['icon12'] = json_decode(json_encode($combine12), True);
        }



        if (isset($quality) && !empty($quality)) {
            foreach ($quality as $key => $data) {
                $qualityListArray[$data->id] = $data->l1qa_name;
            }
        }

        if (isset($answerCount['icon1']) && $answerCount['icon1'] !== '') {
            $totalCounticon1 = count($answerCount['icon1']);
            $counticon1 = array_count_values($answerCount['icon1']);

            if (isset($qualityListArray) && $qualityListArray !== '') {
                foreach ($qualityListArray as $key => $qualityIdName) {
                    $Percenticon['icon1'][$qualityIdName] = (isset($counticon1[$key])) ? round(($counticon1[$key] * 100) / $totalCounticon1, 2) : 0;
                }
            } else {
                $Percenticon['icon1'] = array('None Activty are availble for graph' => 0);
            }
        }

        if (isset($answerCount['icon2']) && $answerCount['icon2'] !== '') {
            $totalCounticon2 = count($answerCount['icon2']);
            $counticon2 = array_count_values($answerCount['icon2']);
            if (isset($qualityListArray) && $qualityListArray !== '') {
                foreach ($qualityListArray as $key => $qualityIdName) {
                    $Percenticon['icon2'][$qualityIdName] = (isset($counticon2[$key])) ? round(($counticon2[$key] * 100) / $totalCounticon2, 2) : 0;
                }
            } else {
                $Percenticon['icon2'] = array('None Activty are availble for graph' => 0);
            }
        }
        if (isset($answerCount['icon4']) && $answerCount['icon4'] !== '') {
            $totalCounticon4 = count($answerCount['icon4']);
            $counticon4 = array_count_values($answerCount['icon4']);
            if (isset($qualityListArray) && $qualityListArray !== '') {
                foreach ($qualityListArray as $key => $qualityIdName) {
                    $Percenticon['icon4'][$qualityIdName] = (isset($counticon4[$key])) ? round(($counticon4[$key] * 100) / $totalCounticon4, 2) : 0;
                }
            } else {
                $Percenticon['icon4'] = array('None Activty are availble for graph' => 0);
            }
        }

        if (isset($answerCount['icon12']) && $answerCount['icon12'] !== '') {
            $totalCounticon12 = count($answerCount['icon12']);
            $counticon12 = array_count_values($answerCount['icon12']);
            if (isset($qualityListArray) && $qualityListArray !== '') {
                foreach ($qualityListArray as $key => $qualityIdName) {
                    $Percenticon['icon12'][$qualityIdName] = (isset($counticon12[$key])) ? round(($counticon12[$key] * 100) / $totalCounticon4, 2) : 0;
                }
            } else {
                $Percenticon['icon12'] = array('None Activty are availble for graph' => 0);
            }
        }

        return $Percenticon;
    }

    public static function getage($birthdate) {
//        $from = new DateTime($birthdate);
//        $to = new DateTime('today');
//        echo $from->diff($to)->y;
        # procedural
        return date_diff(date_create($birthdate), date_create('today'))->y;
    }

    public static function getConfigValueByKey($key) {
       $configValue = DB::table(config::get('databaseconstants.TBL_CONFIGURATION'))->where('cfg_key', $key)->where('deleted', 1)->first();
        if (isset($configValue) && !empty($configValue)) {
            $configValue = $configValue->cfg_value;
        } else {
            $configValue = '';
        }
        return $configValue;
    }

    public static function getConfigValueByKeyForSponsor($key) {
        $configValue = DB::table(config::get('databaseconstants.TBL_PAID_COMPONENTS'))->where('pc_element_name', $key)->where('deleted', 1)->first();
        if (isset($configValue) && !empty($configValue)) {
            $configValue = $configValue->pc_required_coins;
        } else {
            $configValue = '';
        }
        return $configValue;
    }

    /*
      return : Array of active parents
     */

    public static function getActiveParents($parentType) {
        $objParent = new Parents();
        $parent = $objParent->getActiveParents($parentType);

        return $parent;
        return array();
    }

    public static function getActiveSponsorActivity($type) {
        $activityType = DB::table(config::get('databaseconstants.TBL_CONFIGURATION'))->where('id', $type)->where('deleted', 1)->first();
        return $activityType;
    }

    public static function getCategoryIcon($id, $type) {

        if ($type == 'fiction') {
            $cartoonCategoryIcon = DB::table(config::get('databaseconstants.TBL_LEVEL1_CARTOON_ICON'))->where('ci_category', $id)->where('deleted', 1)->get();
        } else if ($type == 'nonfiction') {
            $cartoonCategoryIcon = DB::table(config::get('databaseconstants.TBL_LEVEL1_HUMAN_ICON'))->where('hi_category', $id)->where('deleted', 1)->get();
        }

        if (!empty($cartoonCategoryIcon)) {
            foreach ($cartoonCategoryIcon as $key => $data) {
                if ($type == 'fiction') {
                    $cartoonCount[] = $data->ci_category;
                } elseif ($type == 'nonfiction') {
                    $cartoonCount[] = $data->hi_category;
                }
            }
        }
        if (isset($cartoonCount) && $cartoonCount != '') {
            $countCartoonIcon = array_count_values($cartoonCount);
        } else {
            $countCartoonIcon = 0;
        }

        $final = array('individual' => $countCartoonIcon);

        return $final;
    }

    public static function getActiveSponsorActivityLevel($level) {
        $levelName = DB::table(config::get('databaseconstants.TBL_SYSTEM_LEVELS'))->where('id', $level)->where('deleted', 1)->first();
        return $levelName;
    }

    public static function getProfessionName($id) {
        $pf_detail = DB::table(config::get('databaseconstants.TBL_PROFESSIONS'))->where('id', $id)->where('deleted', 1)->get();
        return $pf_detail;
    }

    public static function checkPopupForUser($teenager, $popupid) {
        $popupexist = DB::table(config::get('databaseconstants.TBL_TEENAGER_POPUP_SHOW'))->where('ps_teenager', $teenager)->where('ps_popup_id', $popupid)->first();
        return $popupexist;
    }

    public static function getAvailableProfessions() {
        $id = array();
        $pf_id = DB::table(config::get('databaseconstants.TBL_TEENAGER_CAREER_MAPPING'))
                ->select('tcm_profession')
                ->get();
        if (isset($pf_id) && !empty($pf_id)) {
            foreach ($pf_id as $value) {
                $id[] = $value->tcm_profession;
            }
        }
        $pf_detail = DB::table(config::get('databaseconstants.TBL_PROFESSIONS'))
                ->whereNotIn('id', $id)
                ->where('deleted', 1)
                ->select('id', 'pf_name')
                ->get();

        return $pf_detail;
    }

    public static function getCountForAttemptedProfession($pf_id, $sid, $cid) {
        $professionattempt = DB::table(config::get('databaseconstants.TBL_TEENAGER_PROFESSION_ATTEMPTED') . " AS profession")
                ->join(config::get('databaseconstants.TBL_TEENAGERS') . " AS teenager", 'teenager.id', '=', 'profession.tpa_teenager')
                ->selectRaw('teenager.*')
                ->where('profession.tpa_peofession_id', $pf_id)
                ->where('teenager.t_school', $sid)
                ->where('teenager.t_class', $cid)
                ->where('teenager.t_class', '!=', 0)
                ->where('teenager.deleted', 1)
                ->distinct('attempted.tpa_peofession_id')
                ->count('profession.tpa_teenager');
        return $professionattempt;
    }

    public static function checkValidImageExtension($file) {
        $validOk = false;
        $fileArray = array('image' => $file);
        $rules = array(
            'image' => 'mimes:jpeg,jpg,png,gif,bmp|required|max:10000' // max 10000kb
        );
        $validator = Validator::make($fileArray, $rules);
        if ($validator->passes()) {
            $validOk = true;
        }
        return $validOk;
    }

    public static function getMultipleBasketNamesForProfession($professionId) {
        $basketsNames = DB::select(DB::raw("select GROUP_CONCAT(' ',basket.b_name) as b_name from " . config::get('databaseconstants.TBL_PROFESSIONS') . " AS profession
                                                      join " . config::get('databaseconstants.TBL_BASKETS') . " AS basket on FIND_IN_SET(basket.id, profession.pf_related_basket)
                                                      where profession.deleted = 1 AND profession.id = " . $professionId . " "));

        if (isset($basketsNames) && !empty($basketsNames)) {
            $basketsNamesStr = $basketsNames[0]->b_name;
        } else {
            $basketsNamesStr = '';
        }
        return $basketsNamesStr;
    }

    public static function pushNotificationForAndroid($token,$message) {
        $optionBuiler = new OptionsBuilder();
        $optionBuiler->setTimeToLive(60*20);

        $notificationBuilder = new PayloadNotificationBuilder('ProTeen');
        $notificationBuilder->setBody($message['message'])
                            ->setSound('default');

        $dataBuilder = new PayloadDataBuilder();
        $dataBuilder->addData($message);

        $option = $optionBuiler->build();
        $notification = $notificationBuilder->build();
        $data = $dataBuilder->build();

        $downstreamResponse = FCM::sendTo($token, $option, NULL, $data);

        $downstreamResponse->numberSuccess();
        $downstreamResponse->numberFailure();
        $downstreamResponse->numberModification();

        $downstreamResponse->tokensToDelete();

        $downstreamResponse->tokensToModify();

        $downstreamResponse->tokensToRetry();
    }

    public static function pushNotificationForAndroidSetUp($tokens,$title,$message)
    {        
        $url = "https://fcm.googleapis.com/fcm/send";

//        //Title of the Notification.
   //     $title = "Message from PHP";
   //     $message = "Message from PHP";
//
//        //Body of the Notification.
//        $body = "Rupin Luhar";

        //Creating the notification array.
        $notification = array('title' =>$title , 'body' => $message);

        //This array contains, the token and the notification. The 'to' attribute stores the token.
        $arrayToSend = array('registration_ids'  => $tokens, 'notification' => $notification);
    
        $fields = array(
                 'registration_ids' => $tokens,
                 'body' => 'hey'
                );

        $headers = array(
            'Authorization:key = AIzaSyC8mPS-6RvU3d7Z68rLjYbjsUY3t1NQrN4',
            'Content-Type: application/json'
            );

       $ch = curl_init();
       curl_setopt($ch, CURLOPT_URL, $url);
       curl_setopt($ch, CURLOPT_POST, true);
       curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
       curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
       curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);  
       curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
       curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($arrayToSend));
       $result = curl_exec($ch);           
       if ($result === FALSE) {
           die('Curl failed: ' . curl_error($ch));
       }
       curl_close($ch);
       return $result; 
    }
    
    public static function pushNotificationForiPhone($token,$message,$pathForCertificate) {

        $payload['aps'] = array('alert' => $message['message'],'action-loc-key' => 'View', 'data' => $message);

        $payload['aps']['badge'] = 1;
        $payload['aps']['loc-args'] = '123';

        $payload = json_encode($payload);
        $deviceToken = $token;  // iPhone 6+

        $apnsHost = 'gateway.push.apple.com';
        $apnsPort = 2195;
        $apnsCert = $pathForCertificate;

        $streamContext = stream_context_create();
        stream_context_set_option($streamContext, 'ssl', 'local_cert', $apnsCert);

        $apns = stream_socket_client('ssl://' . $apnsHost . ':' . $apnsPort, $error, $errorString, 2, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $streamContext);

        $apnsMessage = chr(0) . chr(0) . chr(32) . pack('H*', str_replace(' ', '', $deviceToken)) . chr(0) . chr(strlen($payload)) . $payload;

        fwrite($apns, $apnsMessage);

        fclose($apns);
    }

     public static function getCalculatedTime() {

        $Current_time = strtotime(date('Y-m-d H:i:s'));

        $time = DB::table(config::get('databaseconstants.TBL_TEENAGERS'))
                ->select('t_last_activity','id')
                ->where('deleted', 1)
                ->get();
        $time = json_decode(json_encode($time), true);
        $teenagerData = [];
        foreach ($time AS $key => $value) {
            $Send_time = $value['t_last_activity'];
            $id = $value['id'];

            $final_date = round(abs($Current_time - $Send_time) / 3600, 2);
            $final_date =  round($final_date * 60);
            if ($final_date < 60 ) {
                $teenagerData[] = array('id' => $id, 'time' => $final_date.' mints');
            }
        }
        return $teenagerData;
    }

    public static function getConceptName($ids)
    {
       $idsArr = array();
       $idsArr = explode(',', $ids);
       $appendValue = $finalValue = '';
       foreach($idsArr as $k=>$val)
       {
            if($val == 'L4AD' || $val == 'L4AP' || $val == 'L4AV' || $val == 'L4B')
            {
               if(($key = array_search($val, $idsArr)) !== false) {
                 unset($idsArr[$key]);
                 $appendValue = $val;
               }
            }
       }

       $conceptNames = '';
       $objGamificatioTemplate = new GamificationTemplate();
       $conceptNames = $objGamificatioTemplate->getConceptNameByids($idsArr);

       if($appendValue != '')
       {
           $finalValue = $conceptNames['concept'].','.$appendValue;
       }else{
           $finalValue = $conceptNames['concept'];
       }

       return $finalValue;
    }

    public static function calculateLevel4PromisePlus($yourScore, $totalScore) {
        $result = ($yourScore * 100) / $totalScore;
        return round($result);
    }

    public static function calculateTrendForLevel1AdminById($activityId,$cid) {
        $Percenticon = '';
        $questionArray = ['1' => ['No', 'Not Sure'], '2' => ['As per my expectations', 'Below my expectations'], '3' => ['Yes', 'Sometimes'],
            '4' => ['Sometimes', 'No'], '5' => ['Sometimes', 'Rarely'], '6' => ['Sometimes', 'Rarely'], '7' => ['Maybe', 'No'],
            '8' => ['Many', 'Not Sure'], '9' => ['Yes'], '10' => ['Yes'], '11' => ['Confused', 'None'], '12' => ['Few', 'None'],
            '13' => ['Disagree', 'Sometimes'], '14' => ['No', 'Not Sure'], '15' => ['Yes', 'Sometimes'], '16' => ['Yes', 'Sometimes']];

        $sPointArray = ['1' => ['No' => 278, 'Not Sure' => 362, 'Yes' => 548], '2' => ['As per my expectations' => 559, 'Below my expectations' => 236, 'Above my expectations' => 393], '3' => ['Yes' => 491, 'Sometimes' => 417, 'No' => 280],
            '4' => ['Sometimes' => 380, 'No' => 358, 'Yes' => 450], '5' => ['Sometimes' => 282, 'Rarely' => 299, 'Often' => 607], '6' => ['Sometimes' => 337, 'Rarely' => 382, 'Often' => 469], '7' => ['Maybe' => 515, 'No' => 241, 'Yes' => 432],
            '8' => ['Many' => 442, 'Not Sure' => 253, 'Few' => 493], '9' => ['Yes' => 644, 'Sometimes' => 332, 'No' => 212], '10' => ['Yes' => 769, 'Sometimes' => 188, 'No' => 231], '11' => ['Confused' => 440, 'None' => 163, 'Very Clear' => 585], '12' => ['Few' => 921, 'Many' => 198, 'None' => 69],
            '13' => ['Disagree' => 641, 'Sometimes' => 273, 'Agree' => 274], '14' => ['No' => 430, 'Not Sure' => 402, 'Yes' => 356], '15' => ['Yes' => 581, 'Sometimes' => 299, 'No' => 308], '16' => ['Yes' => 563, 'Sometimes' => 344, 'No' => 281]];

        $answers = DB::table(config::get('databaseconstants.TBL_LEVEL1_ANSWERS') . " AS answer")
                ->join(config::get('databaseconstants.TBL_LEVEL1_OPTIONS') . " AS options", 'answer.l1ans_answer', '=', 'options.id')
                ->join(config::get('databaseconstants.TBL_TEENAGERS') . " AS teen", 'teen.id', '=', 'answer.l1ans_teenager')
                ->selectRaw('answer.* , options.*')
                ->where('l1ans_activity', $activityId)
                ->where('teen.t_class', $cid)
                ->get();

        //Level1 options name
        $option = DB::table(config::get('databaseconstants.TBL_LEVEL1_OPTIONS') . " AS answer")->where('l1op_activity', $activityId)->get();
        if (!empty($option)) {
            foreach ($option as $key => $data) {
                $optionCount[] = $data->l1op_option;
            }
        }
        if (!empty($answers)) {
            foreach ($answers as $key => $data) {
                $answerCount[] = $data->l1op_option;
            }
        }

        $total1 = (isset($answerCount) && !empty($answerCount))? count($answerCount):0;
        $total2 = (isset($sPointArray[$activityId])) ? array_sum($sPointArray[$activityId]) : 0;
        $total = $total1 + $total2;
            $count = (isset($answerCount) && !empty($answerCount))? array_count_values($answerCount):[]; // count individual options for selected activityId
            if (isset($optionCount) && !empty($optionCount)) {
                foreach ($optionCount as $key => $qualityIdName) {
                    $calFromArray = (isset($count[$qualityIdName])) ? $count[$qualityIdName] : 0;
                    $calFromStaticArray = (isset($sPointArray[$activityId][$qualityIdName])) ? $sPointArray[$activityId][$qualityIdName] : 0;
                    $pointScreen = $calFromArray + $calFromStaticArray;
                    $Percentlevel1[$qualityIdName] = ($total > 0) ? (($pointScreen * 100) / $total) : 0; // convert level1 option in percentage
                }

                $firstPoint = (isset($questionArray[$activityId][0]) && isset($Percentlevel1[$questionArray[$activityId][0]]) && ($Percentlevel1[$questionArray[$activityId][0]] != '' || $Percentlevel1[$questionArray[$activityId][0]] > 0 ) ) ? $Percentlevel1[$questionArray[$activityId][0]] : 0;
                $secondPoint = (isset($questionArray[$activityId][1]) && isset($Percentlevel1[$questionArray[$activityId][1]]) && ($Percentlevel1[$questionArray[$activityId][1]] != '' || $Percentlevel1[$questionArray[$activityId][1]] > 0 ) ) ? $Percentlevel1[$questionArray[$activityId][1]] : 0;
            } else {
                $returnString = "No any trends for this survey";
            }
        return array('trend' => $Percentlevel1, 'total' => $total);
    }

    public static function getAllCurrency() {
        $currency = array('1' => 'INR', '2' => 'USD');
        return $currency;
    }

    public static function getActiveTeenagersForGiftCoins($Id = '', $search = '') {
        $objTeenager = new Teenagers();
        $teenager = $objTeenager->getActiveTeenagersForGiftCoins($Id, $search );
        return $teenager;
    }

    public static function getAllElememtName() {
        $currency = array('1' => 'PROMISE Plus', '2' => 'Learning Guidance', '3' => 'Parent Report', '4' => 'School Report', '5' => 'Enterprise Report', '6' => 'Counsellors Report','7' => 'Ads ProCoins' , '8' => 'Event ProCoins' ,'9' => 'Contest ProCoins', '10' => 'Coupon ProCoins', '11' => 'Advance Activity ProCoins', '13' => 'Careers to Consider ProCoins', '14' => 'Institute Finder ProCoins');
        return $currency;
    }

    public static function sendMilestoneNotification($score) {
        $message = '';
        if ($score == 1000 || $score == 2000 || $score == 3000 || $score == 4000 || $score == 5000 || $score == 6000 || $score == 7000 || $score == 8000 || $score == 9000 || $score == 10000 ) {
            $message = "Congratulations! You scored ". $score ." points in ProTeen! Keep playing ProTeen!";
        }
        return $message;
    }

    public static function saveDeductedCoinsData($userId,$type,$coins,$component,$professionId) {
        $objPaidComponent = new PaidComponent();
        $componentsData = $objPaidComponent->getPaidComponentsData($component);
        $objDeductedCoins = new DeductedCoins();
        $saveDedecutedCoins = [];
        $saveDedecutedCoins['id'] = 0;
        $saveDedecutedCoins['dc_user_id'] = $userId;
        $saveDedecutedCoins['dc_user_type'] = $type;
        $saveDedecutedCoins['dc_profession_id'] = $professionId;
        $saveDedecutedCoins['dc_component_name'] = $componentsData->id;
        $saveDedecutedCoins['dc_total_coins'] = $coins;
        $saveDedecutedCoins['dc_start_date'] = date('y-m-d');
        $saveDedecutedCoins['dc_end_date'] = date('Y-m-d', strtotime("+".$componentsData->pc_valid_upto." days"));
        $saveDedecutedCoins['dc_days'] = $componentsData->pc_valid_upto;

        $returnData = $objDeductedCoins->saveDeductedCoinsDetail($saveDedecutedCoins);
        return true;
    }

    public static function calculateRemainingDays($enddate) {
        $Current_date = strtotime(date('Y-m-d H:i:s'));

        $end_date = strtotime($enddate);
        $final_date = 0;
        $date = $end_date - $Current_date;
        if ($date >= 0) {
           $final_date = round(abs($date) / 86400, 2);
        }
        $days = round($final_date);
        return $days;
    }

    public static function getAllUserTypes() {
        $usertype = array('1' => 'Teen/Parent/Mentor', '2' => 'Sponsor');
        return $usertype;
    }

    public static function saveProfileViewDeductedCoinsData($loginUserId,$userId,$coins,$component,$professionId) {
        $objPaidComponent = new PaidComponent();
        $componentsData = $objPaidComponent->getPaidComponentsData($component);
        $objProfileViewDeductedCoins = new ProfileViewDeductedCoins();
        $saveDedecutedCoins = [];
        $saveDedecutedCoins['id'] = 0;
        $saveDedecutedCoins['pdc_user_id'] = $loginUserId;
        $saveDedecutedCoins['pdc_other_user_id'] = $userId;
        $saveDedecutedCoins['pdc_profession_id'] = $professionId;
        $saveDedecutedCoins['pdc_component_name'] = $componentsData->id;
        $saveDedecutedCoins['pdc_total_coins'] = $coins;
        $saveDedecutedCoins['pdc_deducted_date'] = date('y-m-d');

        $returnData = $objProfileViewDeductedCoins->saveDeductedCoinsDetail($saveDedecutedCoins);
        return true;
    }

    public static function saveAllActiveTeenagerForSendNotifivation($userid,$message){
         $saveData = [];
         $saveData['n_user_id'] = $userid;
         $saveData['n_notification_text'] = $message;
         $objNotifications = new Notifications();
         $return = $objNotifications->saveAllActiveTeenagerForSendNotifivation($saveData);
         return true;
    }

    public static function level4ParentBooster($professionId, $userid = null) {
        $objProfession = new Professions();
        $competing = $highestScore = $yourScore = $yourRank = 0;
        $arrayKeys = [];
        $totalPointCollection = $objProfession->getProfessionLevel4AllTypeTotalPoints($professionId);
        $getLevel4AllScore = $objProfession->getLevel4AllScoreForParent($professionId);
        $getLevel4AllScoreUser = $objProfession->getLevel4AllScore($professionId);
        if (isset($totalPointCollection) && !empty($totalPointCollection)) {
            $totalPobScore = $totalPointCollection['totalBasic'] + $totalPointCollection['totalIntermediate'] + $totalPointCollection['totalAdvance'];
        } else {
            $totalPobScore = 0;
        }
        $allData = [];
        $totalCompeting = $objProfession->getTotalCompetingOfProfessionForParent($professionId,$userid);
        foreach ($totalCompeting AS $key => $value) {
            foreach ($getLevel4AllScoreUser['level4TotalPoints'] AS $k => $val) {
                if ($k == $value->teenager_id) {
                    $allData[] = $val;
                }
            }
        }

        $level4Competing = [];
        if (isset($totalCompeting) && !empty($totalCompeting)) {
            foreach ($totalCompeting as $userId) {
                if (isset($userid)) {
                    $level4Competing[$userid]['yourScore'] = (isset($getLevel4AllScore['level4TotalPoints'][$userid]) && $getLevel4AllScore['level4TotalPoints'][$userid] > 0) ? $getLevel4AllScore['level4TotalPoints'][$userid] : 0;
                    $level4Competing[$userid]['highestScore'] = (isset($getLevel4AllScore['level4TotalPoints']) && !empty($getLevel4AllScore['level4TotalPoints'])) ? max($getLevel4AllScore['level4TotalPoints']) : 0;
                }
            }
        }

        if (!empty($level4Competing)) {
            arsort($level4Competing);
            $point = 1;
            foreach ($level4Competing as $key => $rankvalue) {
                $allData[] = (isset($level4Competing[$key]['yourScore']) && $level4Competing[$key]['yourScore'] > 0) ? $level4Competing[$key]['yourScore'] : 0;
                $level4Competing[$key]['rank'] = (isset($level4Competing[$key]['highestScore']) && $level4Competing[$key]['highestScore'] > 0) ? $point : 0;
                $point++;
            }
        }

        $rank = 0;
        rsort($allData);
        foreach ($level4Competing AS $k => $val) {
            foreach($allData AS $key => $value) {
                if ($val['yourScore'] != 0) {
                  if ($val['yourScore'] == $value) {
                      $rank = $key+1;
                  }
                }
            }
        }

        if (isset($totalCompeting) && !empty($totalCompeting)) {
            $competing = (isset($totalCompeting[0])) ? count($totalCompeting) : 0;
        }
        $getattepmtedQuestionOfProfession = $objProfession->getLevel4AllScoreForParent($professionId);
        if (isset($getattepmtedQuestionOfProfession['level4TotalPoints']) && !empty($getattepmtedQuestionOfProfession['level4TotalPoints'])) {
            $highestScore = max($getattepmtedQuestionOfProfession['level4TotalPoints']);
            $yourScore = (isset($getattepmtedQuestionOfProfession['level4TotalPoints'][$userid])) ? $getattepmtedQuestionOfProfession['level4TotalPoints'][$userid] : 0;
        } else {
            $getattepmtedQuestionOfProfession = [];
        }
        $data = [];
        $data['competing'] = $competing;
        $data['yourScore'] = $yourScore;
        $data['highestScore'] = $highestScore;
        $data['yourRank'] = $rank;
        $data['totalPobScore'] = $totalPobScore;
        $data['allData'] = $allData;

        return $data;
    }


    public static function getTemplateNoForParent($parentId, $professionId) {
        $objProfession = new Professions();
        $objParents = new Parents();
        $getResult = $objProfession->getTemplateNoForParent($parentId, $professionId);
        $professionDetail = $objProfession->getProfessionDetail($professionId);
        $parentDataDetail = $objParents->getParentsData($parentId);
        $image2 = 'proteen-logo.png';
        $message = $message3 = '';
        $message2 = 'no';
        if (isset($parentDataDetail) && !empty($parentDataDetail)) {
            $parentName = trim($parentDataDetail->p_first_name) . "! ";
            $image = $parentDataDetail->p_photo;
            $path = Config::get('constant.PARENT_ORIGINAL_IMAGE_UPLOAD_PATH');
            if ($image != '' && file_exists($path . $image)) {
                $parentImage = $image;
            } else {
                $parentImage = 'proteen-logo.png';
            }
            $image2 = asset($path . $parentImage);
        }
        if (isset($professionDetail) && !empty($professionDetail)) {
            $professionName = $professionDetail->pf_name;
        }
        if (isset($getResult) && !empty($getResult)) {
            if ($getResult['totalCompletedTemplate'] == 1) {
                $message = "Wow " . ucfirst($parentName) . "That was a real world " . ucfirst($professionName) . " experience. Keep playing!";
            } else if ($getResult['totalCompletedTemplate'] == 2) {
                $message = "Wow " . ucfirst($parentName) . "That was a real world " . ucfirst($professionName) . " experience. Keep playing!";
                $message .= " Based on your personality, being a " . ucfirst($professionName) . " is going to be quite <Easy / Challenging>. Go play on..";
            } else if ($getResult['totalCompletedTemplate'] == 3) {
                $message = "That's big part of the experience being " . ucfirst($professionName) . ". Keep it up! Your " . ucfirst($professionName) . " badge is one step away!";
            } else if ($getResult['totalCompletedTemplate'] == 4) {
                $desc = "Well done " . ucfirst($parentName) . " The " . ucfirst($professionName) . " badge is yours! Let the world know!";
                $url = url("/");
                $message = $desc;
                $message2 = "yes";
                $message3 = '<a href="javascript:void(0);" onclick="shareFacebook(\'' . $url . '\',\'' . $desc . '\',\'' . $desc . '\',\'' . $image2 . '\')" class="fb_congratulation"><i class="fa fa-facebook-official" aria-hidden="true"></i></a>';
                $message3 .= '<a href="https://plus.google.com/share?url=' . $url . '&image=' . $image2 . '" target="_blank"  class="google_congratulation"><i class="fa fa-google-plus-square" aria-hidden="true"></i></a>';
            } else {
                $message = ucfirst($teenagerName) . " You are now a rookie " . ucfirst($professionName) . " in ProTeen.";
            }
        } else {
            $message = ucfirst($teenagerName) . " You are now a rookie ProTeen " . ucfirst($professionName);
        }
        $array = [];
        $array['msg1'] = $message;
        $array['msg2'] = $message2;
        $array['msg3'] = $message3;
        return $array;
    }


    public static function getCompetingUserListForParent($professionId,$parentId) {
        $objProfession = new Professions();
        $getLevel4AllScoreUser = $objProfession->getLevel4AllScore($professionId);

        $allData = [];

        $totalCompeting = $objProfession->getTotalCompetingOfProfessionForParent($professionId,$parentId);

        $level4Competing = [];
        if (isset($totalCompeting) && !empty($totalCompeting)) {
            foreach ($totalCompeting as $teenId) {
                $teendata = [];
                if (isset($teenId->teenager_id)) {
                    if ($teenId->t_photo != '') {
                        if (isset($teenId->t_photo) && !empty($teenId->t_photo)) {
                            $teenPhoto = Config::get('constant.TEEN_ORIGINAL_IMAGE_UPLOAD_PATH') . $teenId->t_photo;
                        } else {
                            $teenPhoto = Config::get('constant.TEEN_ORIGINAL_IMAGE_UPLOAD_PATH') . "proteen-logo.png";
                        }
                    } else {
                        $teenPhoto = Config::get('constant.TEEN_ORIGINAL_IMAGE_UPLOAD_PATH') . "proteen-logo.png";
                    }
                    $level4Competing[$teenId->teenager_id]['profile_pic'] = $teenPhoto;
                    $level4Competing[$teenId->teenager_id]['name'] = $teenId->t_name;
                    $level4Competing[$teenId->teenager_id]['teenager_id'] = $teenId->teenager_id;
                    $level4Competing[$teenId->teenager_id]['teenager_unique_id'] = $teenId->t_uniqueid;
                    $level4Competing[$teenId->teenager_id]['competitors'] = (isset($totalCompeting) && !empty($totalCompeting))?count($totalCompeting):0;
                }
            }
        }
        return $level4Competing;
    }

    public static function getCompetingUserListForTeenager($professionId,$teenId) {
        $objProfession = new Professions();
        $getLevel4AllScoreUser = $objProfession->getLevel4AllScore($professionId);

        $allData = [];
        $totalCompeting = $objProfession->getCompetingUserListForTeenager($professionId,$teenId);

        $level4Competing = [];
        if (isset($totalCompeting) && !empty($totalCompeting)) {
            foreach ($totalCompeting as $parentId) {
                $parentdata = [];
                if (isset($parentId->parent_id)) {
                    if ($parentId->p_photo != '') {
                        if (file_exists(Config::get('constant.PARENT_ORIGINAL_IMAGE_UPLOAD_PATH') . $parentId->p_photo)) {
                            $parentPhoto = asset(Config::get('constant.PARENT_ORIGINAL_IMAGE_UPLOAD_PATH') . $parentId->p_photo);
                        } else {
                            $parentPhoto = asset(Config::get('constant.PARENT_ORIGINAL_IMAGE_UPLOAD_PATH') . "proteen-logo.png");
                        }
                    } else {
                        $parentPhoto = asset(Config::get('constant.PARENT_ORIGINAL_IMAGE_UPLOAD_PATH') . "proteen-logo.png");
                    }
                    $parentdata['profile_pic'] = $parentPhoto;
                    $parentdata['name'] = $parentId->p_first_name;
                    $parentdata['parent_id'] = $parentId->parent_id;
                    $level4Competing[] = $parentdata;
                    //$level4Competing[$parentId->parent_id]['competitors'] = (isset($totalCompeting) && !empty($totalCompeting))?count($totalCompeting):0;
                }
            }
        }

        return $level4Competing;
    }

    //Calculate Level1 question trend
    public static function calculateTotalTrendForLevel1($activityId,$type) {

        $sPointArray = ['1' => ['No' => 278, 'Not Sure' => 362, 'Yes' => 548], '2' => ['As per my expectations' => 559, 'Below my expectations' => 236, 'Above my expectations' => 393], '3' => ['Yes' => 491, 'Sometimes' => 417, 'No' => 280],
            '4' => ['Sometimes' => 380, 'No' => 358, 'Yes' => 450], '5' => ['Sometimes' => 282, 'Rarely' => 299, 'Often' => 607], '6' => ['Sometimes' => 337, 'Rarely' => 382, 'Often' => 469], '7' => ['Maybe' => 515, 'No' => 241, 'Yes' => 432],
            '8' => ['Many' => 442, 'Not Sure' => 253, 'Few' => 493], '9' => ['Yes' => 644, 'Sometimes' => 332, 'No' => 212], '10' => ['Yes' => 769, 'Sometimes' => 188, 'No' => 231], '11' => ['Confused' => 440, 'None' => 163, 'Very Clear' => 585], '12' => ['Few' => 921, 'Many' => 198, 'None' => 69],
            '13' => ['Disagree' => 641, 'Sometimes' => 273, 'Agree' => 274], '14' => ['No' => 430, 'Not Sure' => 402, 'Yes' => 356], '15' => ['Yes' => 581, 'Sometimes' => 299, 'No' => 308], '16' => ['Yes' => 563, 'Sometimes' => 344, 'No' => 281]];

        $answers = DB::table(config::get('databaseconstants.TBL_LEVEL1_ANSWERS') . " AS answer")
                ->join(config::get('databaseconstants.TBL_LEVEL1_OPTIONS') . " AS options", 'answer.l1ans_answer', '=', 'options.id')
                ->selectRaw('answer.* , options.*')
                ->where('l1ans_activity', $activityId)
                ->get();

        if (!empty($answers)) {
            foreach ($answers as $key => $data) {
                $answerCount[] = $data->l1op_option;
            }
        }

        $total1 = (isset($answerCount) && !empty($answerCount))? count($answerCount):0;
        $total2 = (isset($sPointArray[$activityId])) ? array_sum($sPointArray[$activityId]) : 0;
        $total = $total1 + $total2;

        return $total;
    }

    public static function getVideoOriginalImageUrl($videoPhoto) {
        if ($videoPhoto != '' && file_exists(Config::get('constant.VIDEO_ORIGINAL_IMAGE_UPLOAD_PATH') . $videoPhoto)) {
            $videoPhotoName = $videoPhoto;
        } else {
            $videoPhotoName = 'proteen-logo.png';
        }
        return asset(Config::get('constant.VIDEO_ORIGINAL_IMAGE_UPLOAD_PATH') . $videoPhotoName);
    }

    public static function getActiveTeenagersForGiftCoupon($Id = '', $searchArray) {
        $objTeenager = new Teenagers();
        $teenager = $objTeenager->getActiveTeenagersForGiftCoupon($Id, $searchArray);
        return $teenager;
    }

    public static function getActiveTeenagersForCoupon($id,$slot) {
        $objTeenager = new Teenagers();
        $teenager = $objTeenager->getActiveTeenagersForCoupon($id, $slot);
        return $teenager;
    }

    //Date validation
    public static function validateDate($date, $format = 'Y-m-d')
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }

    public static function page() {
        $page = array('1' => 'Dashboard Page', '2' => 'Profile Page');
        return $page;
    }
     
    public static function verifyEmailIsReal($toemail, $fromemail, $getdetails = false)
    {
        $result = 'valid';
        // Get the domain of the email recipient
        $email_arr = explode('@', $toemail);
        $domain = array_slice($email_arr, -1);
        $domain = $domain[0];
        $details = '';

        // Trim [ and ] from beginning and end of domain string, respectively
        $domain = ltrim($domain, '[');
        $domain = rtrim($domain, ']');

        if ('IPv6:' == substr($domain, 0, strlen('IPv6:'))) {
            $domain = substr($domain, strlen('IPv6') + 1);
        }

        $mxhosts = array();
            // Check if the domain has an IP address assigned to it
        if (filter_var($domain, FILTER_VALIDATE_IP)) {
            $mx_ip = $domain;
        } else {
            // If no IP assigned, get the MX records for the host name
            getmxrr($domain, $mxhosts, $mxweight);
        }

        if (!empty($mxhosts)) {
            $mx_ip = $mxhosts[array_search(min($mxweight), $mxhosts)];
        } else {
            // If MX records not found, get the A DNS records for the host
            if (filter_var($domain, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
                $record_a = dns_get_record($domain, DNS_A);
                 // else get the AAAA IPv6 address record
            } elseif (filter_var($domain, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
                $record_a = dns_get_record($domain, DNS_AAAA);
            }

            if (!empty($record_a)) {
                $mx_ip = $record_a[0]['ip'];
            } else {
                // Exit the program if no MX records are found for the domain host
                $result = 'invalid';
                $details .= 'No suitable MX records found.';

                return ((true == $getdetails) ? array($result, $details) : $result);
            }
        }

        // Open a socket connection with the hostname, smtp port 25
        $connect = @fsockopen($mx_ip, 25);

        if ($connect) {

                  // Initiate the Mail Sending SMTP transaction
            if (preg_match('/^220/i', $out = fgets($connect, 1024))) {

                          // Send the HELO command to the SMTP server
                fputs($connect, "HELO $mx_ip\r\n");
                $out = fgets($connect, 1024);
                $details .= $out."\n";

                // Send an SMTP Mail command from the sender's email address
                fputs($connect, "MAIL FROM: <$fromemail>\r\n");
                $from = fgets($connect, 1024);
                $details .= $from."\n";

                            // Send the SCPT command with the recepient's email address
                fputs($connect, "RCPT TO: <$toemail>\r\n");
                $to = fgets($connect, 1024);
                $details .= $to."\n";

                // Close the socket connection with QUIT command to the SMTP server
                fputs($connect, 'QUIT');
                fclose($connect);

                // The expected response is 250 if the email is valid
                if (!preg_match('/^250/i', $from) || !preg_match('/^250/i', $to)) {
                    $result = 'invalid';
                } else {
                    $result = 'valid';
                }
            }
        } else {
            $result = 'invalid';
            $details .= 'Could not connect to server';
        }
        if ($getdetails) {
            return array($result, $details);
        } else {
            return $result;
        }
    }

    public static function getCmsBySlug($slug)
    {
        $objCms = new CMS();
        $cmsDetails = $objCms->getCmsBySlug($slug);
        return $cmsDetails;
    }

    // Get interest and strength details by teen id
    public static function getTeenInterestAndStregnthDetails($teengerId) {
        $finalScore = array();
        $objLevel2Answers = new Level2Answers();
        $objLevel2Activity = new Level2Activity();
        $correctAnswerQuestionsIds = $objLevel2Answers->level2GetCorrectAnswerQuestionIds($teengerId);

        if (isset($correctAnswerQuestionsIds) && !empty($correctAnswerQuestionsIds)) {
            foreach ($correctAnswerQuestionsIds as $key => $questions) {
                $questionData[] = $objLevel2Activity->getActiveLevel2Activity($questions->l2ans_activity);
            }
        }

        // Get default all data of Interest, Aptitude, and personality and MI
        $objPersonality = new Personality();
        $personality = $objPersonality->getActivepersonality();
        $personalityArr = $personality->toArray();
        if (!empty($personalityArr)) {
            foreach ($personalityArr as $key => $val) {
                $allPersonality[$val['pt_slug']] = 0;
            }
        }

        $objInterest = new Interest();
        $interest = $objInterest->getActiveInterest();
        $interestArr = $interest->toArray();
        if (!empty($interestArr)) {
            foreach ($interestArr as $key => $val) {
                $allInterest[$val['it_slug']] = 0;
            }
        }

        $objApptitude = new Apptitude();
        $apptitude = $objApptitude->getActiveApptitude();
        $apptitudeArr = $apptitude->toArray();
        if (!empty($apptitudeArr)) {
            foreach ($apptitudeArr as $key => $val) {
                $allApptitude[$val['apt_slug']] = 0;
            }
        }

        $objMultipleIntelligent = new MultipleIntelligent();
        $MI = $objMultipleIntelligent->getActiveMultipleIntelligent();
        $MIArr = $MI->toArray();
        if (!empty($MIArr)) {
            foreach ($MIArr as $key => $val) {
                $allMI[$val['mi_slug']] = 0;
            }
        }

        //Get Interest, Aptitude, and personality and MI of Teen based on answer

        if (isset($questionData) && !empty($questionData)) {
            $miName = '';
            $aptitudeName = '';
            $personalityName = '';
            $interest = '';
            foreach ($questionData as $key => $detail) {
                if (isset($detail[0])) {


                    if ($detail[0]->mi_slug != '') {
                        $miName = $detail[0]->mi_slug;
                        $APIdata['MI'][] = $detail[0]->mi_slug;
                    }
                    if ($detail[0]->apt_slug != '') {
                        $aptitudeName = $detail[0]->apt_slug;
                        $APIdata['aptitude'][] = $detail[0]->apt_slug;
                    }
                    if ($detail[0]->pt_slug != '') {
                        $personalityName = $detail[0]->pt_slug;
                        $APIdata['personality'][] = $detail[0]->pt_slug;
                    }
                    if ($detail[0]->it_slug != '') {
                        $interest = $detail[0]->it_slug;
                        $APIdata['interest'][] = $detail[0]->it_slug;
                    }
                }
            }
        }

        if (isset($APIdata['MI'])) {
            $score['MI'] = array_count_values($APIdata['MI']);
        } else {
            $score['MI'] = array();
        }

        if (isset($APIdata['aptitude'])) {
            $score['aptitude'] = array_count_values($APIdata['aptitude']);
        } else {
            $score['aptitude'] = array();
        }

        if (isset($APIdata['personality'])) {
            $score['personality'] = array_count_values($APIdata['personality']);
        } else {
            $score['personality'] = array();
        }

        if (isset($APIdata['interest'])) {
            $score['interest'] = array_count_values($APIdata['interest']);
        } else {
            $score['interest'] = array();
        }

        $finalScore['MI'] = $score['MI'] + $allMI;
        $finalScore['aptitude'] = $score['aptitude'] + $allApptitude;
        $finalScore['personality'] = $score['personality'] + $allPersonality;
        $finalScore['interest'] = $score['interest'] + $allInterest;

        //MI Scale
        if (!empty($finalScore['MI'])) {
            $objMIScale = new MultipleIntelligentScale();
            foreach ($finalScore['MI'] as $mi => $score) {
                $scale['MI'][$mi] = $objMIScale->calculateMIHML($mi, $score);
            }
        }
        //Apptitude Scale
        if (!empty($finalScore['aptitude'])) {
            $objApptitudeScale = new ApptitudeTypeScale();
            foreach ($finalScore['aptitude'] as $apptitude => $scoreapptitude) {
                $scale['aptitude'][$apptitude] = $objApptitudeScale->calculateApptitudeHML($apptitude, $scoreapptitude);
            }
        }

        //Personality Scale
        if (!empty($finalScore['personality'])) {
            $objPersonalityScale = new PersonalityScale();
            foreach ($finalScore['personality'] as $personality => $scorepersonality) {
                $scale['personality'][$personality] = $objPersonalityScale->calculatePersonalityHML($personality, $scorepersonality);
            }
        }

        $final = array('APIscore' => $finalScore, 'APIscale' => $scale);
        return $final;
    }

    public static function getInterestBySlug($slug)
    {
        $objInterest = new Interest();
        $interest = $objInterest->getInterestDetailBySlug($slug);

        return $interest->it_name;
    }

    public static function getMIBySlug($slug)
    {
        $objMultipleIntelligent = new MultipleIntelligent();
        $multipleIntelligent = $objMultipleIntelligent->getMultipleIntelligenceDetailBySlug($slug);

        return $multipleIntelligent->mit_name;
    }

    public static function getApptitudeBySlug($slug)
    {
        $objApptitude = new Apptitude();
        $apptitude = $objApptitude->getApptitudeDetailBySlug($slug);

        return $apptitude->apt_name;
    }

    public static function getTeenagerMatchScale($teenagerId) {
        $response = [];
        $record = ProfessionMatchScale::where('teenager_id', $teenagerId)->first();
        if($record && isset($record->match_scale) && $record->match_scale != "") {
            $response = json_decode($record->match_scale, true);
        }
        return $response;
    }

    public static function getPersonalityBySlug($slug)
    {
        $objPersonality = new Personality();
        $personality = $objPersonality->getPersonalityDetailBySlug($slug);

        return $personality->pt_name;
    }

    public static function age($ageVal = '') {
        $age = array('1' => '13', '2' => '13-14', '3' => '14-15', '4' => '15-16', '5' => '16-17', '6' => '17-18', '7' => '18-19', '8' => '19-20', '9' => '20');
        if (isset($ageVal) && !empty($ageVal)) {
            $age = $age[$ageVal];
        }
        return $age;
    }

    public static function getCommunitySortByArray()
    {
        $sortArray = array(1 => 'School', 2 => 'Gender', 3 => 'Age', 4 => 'Pincode');
        $sortByArr = [];
        $objSchool = new Schools;
        $schools = $objSchool->select('id', 'sc_name')->where('deleted', '1')->where('sc_isapproved','1')->get();
        foreach($schools as $school) {
            $schoolArr[] = array('id' => $school->id, 'name' => $school->sc_name);
        }
        $age = Self::age();
        foreach($age as $ageKey => $ageValue) {
            $ageArr[] = array('id' => $ageKey, 'name' => $ageValue);
        }
        $gender = Self::gender();
        foreach($gender as $genderKey => $genderValue) {
            $genderArr[] = array('id' => $genderKey, 'name' => $genderValue);
        }
        foreach ($sortArray as $sortKey => $sortValue) {
            if ($sortKey == 1) {
                $sortByArr[] = array('id' => $sortKey, 'name' => $sortValue, 'sortData' => $schoolArr); 
            } else if ($sortKey == 2) {
                $sortByArr[] = array('id' => $sortKey, 'name' => $sortValue, 'sortData' => $genderArr);
            } else if ($sortKey == 3) {
                $sortByArr[] = array('id' => $sortKey, 'name' => $sortValue, 'sortData' => $ageArr);
            } else {
                $sortByArr[] = array('id' => $sortKey, 'name' => $sortValue, 'sortData' => array());
            }
        }
        return $sortByArr;
    }

    public static function getSortByColumn($sortBy) 
    {
        $sortColumn = '';
        if ($sortBy == 1) {
            $sortColumn = 't_school';
        } else if ($sortBy == 2) {
            $sortColumn = 't_gender';
        } else if ($sortBy == 3) {
            $sortColumn = 't_birthdate';
        } else {
            $sortColumn = 't_pincode';
        }
        return $sortColumn;
    }

    public static function getDateRangeByAge($sortOption) 
    {
        $ageArr = explode("-", $sortOption);
        $toDate = Carbon::now()->subYears($ageArr[0]);
        $fromDate = Carbon::now()->subYears($ageArr[1]);
        $filterOptionArr['fromDate'] = $fromDate->format('Y-m-d');
        $filterOptionArr['toDate'] = $toDate->format('Y-m-d');
        return $filterOptionArr;
    }

    public static function learningGuidance()
    {
        $learningGuidance = array();
        $learningGuidance['learningGuidanceInfo'] = "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer lobortis vestibulum ipsum id commodo. Curabitur non turpis eget turpis laoreet mattisac sit amet turpismolestie lacus non, elementum velit.";
        $subPanelData[0] = array(
                                'id' => 1,
                                'title' => 'Remembering',
                                'titleColor' => '#f1c246',
                                'titleType' => Config::get('constant.MEDIUM_FLAG'),
                                'subPanelDescription' => '<p>Recalling terminology, dates, or any information previously learned relevant to a subject.</p><p><strong>Example:</strong> To be able to list primary and secondary colors, list numbers.</p>',
                            );
        $subPanelData[1] = array(
                                'id' => 2,
                                'title' => 'Understanding',
                                'titleColor' => '#07c9a7',
                                'titleType' => Config::get('constant.EASY_FLAG'),
                                'subPanelDescription' => '<p>Interpretting or summarizing facts into something simpler to understand.</p><p><strong>Example:</strong> To be able to summarize features of a new product, para-phrase lines from a poem.</p>',
                            );
        $subPanelData[2] = array(
                                'id' => 3,
                                'title' => 'Applying Analyzing',
                                'titleColor' => '#f58634',
                                'titleType' => Config::get('constant.CHALLENGING_FLAG'),
                                'subPanelDescription' => '<p>Applying facts and terminology in any situation.</p><p><strong>Example 1:</strong> To be able to respond to FAQ’s, classify species of birds and animals. Analyzing facts and terminology in any situation.</p><p><strong>Example 2:</strong> To be able to select the most complete list of activities, outline admission steps.</p>',
                            );
        $subPanelData[3] = array(
                                'id' => 4,
                                'title' => 'Evaluating Creating',
                                'titleColor' => '#f58634',
                                'titleType' => Config::get('constant.CHALLENGING_FLAG'),
                                'subPanelDescription' => '<p>Evaluating given facts or creating new facts as relevant to the subject.</p><p><strong>Example 1:</strong> TTo be able to check for consistency amongst sources, rank students by age. Creating new facts as relevant to a subject.</p><p><strong>Example 2:</strong> To be able to generate a log of daily activities, categorize by age groups.</p>',
                            );
        $factualPanelArray = array(
                                'id' => 1,
                                'name' => 'Factual',
                                'slug' => Config::get('constant.FACTUAL_SLUG'),
                                'panelColor' => '#ff5f44',
                                'image' => Storage::url('img/brain-img.png'),
                                'subPanelData' => $subPanelData
                            );
        $conceptualPanelArray = array(
                                'id' => 2,
                                'name' => 'Conceptual',
                                'slug' => Config::get('constant.CONSEPTUAL_SLUG'),
                                'panelColor' => '#27a6b5',
                                'image' => Storage::url('img/bulb-img.png'),
                                'subPanelData' => $subPanelData
                            );
        $proceduralPanelArray = array(
                                'id' => 3,
                                'name' => 'Procedural',
                                'slug' => Config::get('constant.PROCEDURAL_SLUG'),
                                'panelColor' => '#65c6e6',
                                'image' => Storage::url('img/puzzle-img.png'),
                                'subPanelData' => $subPanelData
                            );
        $metacognitivePanelArray = array(
                                'id' => 4,
                                'name' => 'Meta-Cognitive',
                                'slug' => Config::get('constant.META_SLUG'),
                                'panelColor' => '#73376d',
                                'image' => Storage::url('img/star-img.png'),
                                'subPanelData' => $subPanelData
                            );
        
        $learningGuidance['panelData'][0] = $factualPanelArray;
        $learningGuidance['panelData'][1] = $conceptualPanelArray;
        $learningGuidance['panelData'][2] = $proceduralPanelArray;
        $learningGuidance['panelData'][3] = $metacognitivePanelArray;
        return $learningGuidance;
    }

    public static function getTeenInterestAndStregnthMaxScore()
    {
        $maxScore = [];
        $maxScore['MI'] = array('mit_interpersonal'=>8,'mit_logical'=>20,'mit_linguistic'=>10,'mit_intrapersonal'=>7,'mit_existential'=>4,'mit_bodilykinesthetic'=>5,'mit_spatial'=>9,'mit_musical'=>6,'mit_naturalist'=>6);
        $maxScore['aptitude'] = array('apt_verbal_reasoning'=>10,'apt_logical_reasoning'=>15,'apt_scientific_reasoning'=>5,'apt_spatial_ability'=>3,'apt_social_ability'=>5,'apt_numerical_ability'=>4,'apt_artistic_ability'=>1,'apt_creativity'=>1,'apt_clerical_ability'=>1);
        $maxScore['personality'] = array('pt_social'=>2,'pt_investigative'=>1,'pt_conventional'=>2,'pt_mechanical'=>1,'pt_enterprising'=>1,'pt_artistic'=>1);
        $maxScore['interest'] = array('it_computers'=>1,'it_sports'=>3,'it_language'=>1,'it_artistic'=>1,'it_musical'=>1,'it_people'=>1,'it_nature'=>5,'it_technical'=>1,'it_creative_fine_arts'=>2,'it_numerical'=>1,'it_research'=>1,'it_performing_arts'=>3,'it_social'=>1);
        return $maxScore;
    }

    public static function professionMatchScaleCalculate($array, $userId) {
        if(isset($array[0]->NoOfTotalQuestions) && $array[0]->NoOfTotalQuestions > 0 && isset($array[0]->NoOfAttemptedQuestions)) {            
            if($array[0]->NoOfAttemptedQuestions >= $array[0]->NoOfTotalQuestions) {
                dispatch( new SetProfessionMatchScale($userId) );
            }
        }
        return true;
    }

    public static function getCareerMapColumnName()
    {
        $careerArray = array(
                        'apt_scientific_reasoning' => 'tcm_scientific_reasoning', 
                        'apt_verbal_reasoning' =>'tcm_verbal_reasoning', 
                        'apt_numerical_ability' => 'tcm_numerical_ability', 
                        'apt_logical_reasoning' => 'tcm_logical_reasoning', 
                        'apt_social_ability' => 'tcm_social_ability', 
                        'apt_artistic_ability' => 'tcm_artistic_ability', 
                        'apt_spatial_ability' => 'tcm_spatial_ability', 
                        'apt_creativity' => 'tcm_creativity', 
                        'apt_clerical_ability' => 'tcm_clerical_ability', 
                        'mit_interpersonal' => 'tcm_interpersonal', 
                        'mit_logical' => 'tcm_logical', 
                        'mit_linguistic' => 'tcm_linguistic', 
                        'mit_intrapersonal' => 'tcm_intrapersonal', 
                        'mit_musical' => 'tcm_musical', 
                        'mit_spatial' => 'tcm_spatial', 
                        'mit_bodilykinesthetic' => 'tcm_bodily_kinesthetic', 
                        'mit_naturalist' => 'tcm_naturalist', 
                        'mit_existential' => 'tcm_existential', 
                        'pt_conventional' => 'tcm_organizers_conventional', 
                        'pt_enterprising' => 'tcm_persuaders_enterprising', 
                        'pt_investigative' => 'tcm_thinkers_investigative', 
                        'pt_social' => 'tcm_helpers_social', 
                        'pt_artistic' => 'tcm_creators_artistic', 
                        'pt_mechanical' => 'tcm_doers_realistic');
        return $careerArray;
    }
    
    /*
     * This function is used to calculate the teenager profile completeness 
     */
    public static function calculateProfileComplete($teenagerId)
    {
       $objLevel1Activity = new Level1Activity(); 
       $objLevel2Activity = new Level2Activity(); 
       $user = Teenagers::find($teenagerId);
       $profileComplete = 0;
       //Calculate for basic profile
       if(isset($user) && !empty($user))
       {
           if($user->t_name != '' && $user->t_lastname != '' && $user->t_email != '' && $user->t_pincode != '' && $user->t_country != '' && $user->t_photo != '')
           $profileComplete = $profileComplete + Config::get('constant.TEEN_BASIC_PROFILE_COMPLETE');
       }
       //Calculate L1 question complete
       $level1Activities = $objLevel1Activity->getNoOfTotalQuestionsAttemptedQuestion($user->id);
       if(isset($level1Activities) && !empty($level1Activities)){
           $profileComplete = $profileComplete + (($level1Activities[0]->NoOfAttemptedQuestions*Config::get('constant.TEEN_LEVEL1_PROFILE_COMPLETE'))/$level1Activities[0]->NoOfTotalQuestions);
       }
       
       //Calculate L2 question complete
       $level2Activities = $objLevel2Activity->getNoOfTotalQuestionsAttemptedQuestion($user->id);
       
       if(isset($level2Activities) && !empty($level2Activities)){
           $profileComplete = $profileComplete + (($level2Activities[0]->NoOfAttemptedQuestions*Config::get('constant.TEEN_LEVEL2_PROFILE_COMPLETE'))/$level2Activities[0]->NoOfTotalQuestions);
       }
       
       //Calculate Icons complete
       $level1Icons = $objLevel1Activity->getTeenAttemptedQualityType($user->id);
       if(isset($level1Icons) && !empty($level1Icons)){
           $profileComplete = $profileComplete + (count($level1Icons)*Config::get('constant.TEEN_LEVEL1_ICON_PROFILE_COMPLETE'))/4;
       }       
       return intval($profileComplete);
    }

    //Advertisements image size array
    public static function adsSizeType()
    {
        $sizeType = array('1' => '343 X 400', '2' => '343 X 800', '3' => '850 X 90', '4' => '1200 X 90');
        return $sizeType;
    }

    public static function get_string_between($string, $start, $end){
        $string = ' ' . $string;
        $ini = strpos($string, $start);
        if ($ini == 0) return '';
        $ini += strlen($start);
        $len = strpos($string, $end, $ini) - $ini;
        return strtolower(substr($string, $ini, $len));
    }

    public static function getMyCareerPageFilter()
    {
        $filterData = array('1' => 'Industry', '2' => 'Careers', '3' => 'Interest', '4' => 'Strength', '5' => 'Subject', '6' => 'Tags');
        return $filterData;
    }

    /* @getTeenagerBasicBooster
     *  @params : teenager Id
     *  @response : All level booster points with total points 
     */
    public static function getTeenagerBasicBooster($teenagerId) {
        $boosterPoints = DB::select( DB::raw("select SUM(tlb_points) as points, tlb_level from " . config::get('databaseconstants.TBL_TEENAGER_LEVEL_BOOSTERS') . " where tlb_teenager=" . $teenagerId . " GROUP BY tlb_level"), array());
        $boosterArray = [];
        $totalPoints = 0;
        if($boosterPoints) {
            foreach ($boosterPoints as $points) {
                $boosterArray["Level" . $points->tlb_level] = $points->points;
                $totalPoints = $totalPoints + $points->points;
            }
            $boosterArray["total"] = max((int)$totalPoints, 0);
        }
        return $boosterArray;
    }

    /* @getProfessionCompletePercentage
     *  @params : teenager Id
     *  @response : All level booster points with total points 
     */
    public static function getProfessionCompletePercentage($teenagerId, $professionId) {
        $data = DB::table('pro_l4aapa_level4_profession_progress')->where(['teenager_id' => $teenagerId, 'profession_id' => $professionId])->first();
        return (isset($data->level4_total) && $data->level4_total != "" && $data->level4_total > 0) ? ($data->level4_total > 100) ? 100 : $data->level4_total : 0;
    }

    public static function calculateBadgeCount($badgeArr, $availableBadges) {
        $badgeCount = 0;
        switch ($availableBadges) {
            case $availableBadges >= $badgeArr[5]:
                $badgeCount = 5;
                break;

            case $availableBadges >= $badgeArr[4]:
                $badgeCount = 4;
                break;

            case $availableBadges >= $badgeArr[3]:
                $badgeCount = 3;
                break;

            case $availableBadges >= $badgeArr[2]:
                $badgeCount = 2;
                break;

            case $availableBadges >= $badgeArr[1]:
                $badgeCount = 1;
                break;

            default:
                $badgeCount = 0;
                break;
        }
        return $badgeCount;
    } 

    /* @getProfessionCompleteCount
     *  @params : teenager Id
     *  @response : Total attempted profession count 
     */
    public static function getProfessionCompleteCount($teenagerId, $starRatedProfessionCount = '', $basketId = '') 
    {
        $qry = DB::table('pro_pf_profession As profession')
                    ->join('pro_l4aapa_level4_profession_progress As pofessionProgress', 'pofessionProgress.profession_id', '=', 'profession.id');

        if ($starRatedProfessionCount && $starRatedProfessionCount == 1) {
            $qry->join('pro_srp_star_rated_professions As starRatedProfession', 'starRatedProfession.srp_profession_id', '=', 'profession.id');
        }

        if ($basketId && !empty($basketId)) {
            $qry->where('profession.pf_basket', $basketId);
        }

        $data = $qry->where('pofessionProgress.teenager_id', $teenagerId)->where('pofessionProgress.level4_total', '>=', 100)->where('profession.deleted', Config::get('constant.ACTIVE_FLAG'))->count();
        return $data;
    }

    public static function getTotalBasketProfession($basketId) 
    {
        $data = DB::table('pro_pf_profession As profession')
                ->join('pro_srp_star_rated_professions As starRatedProfession', 'starRatedProfession.srp_profession_id', '=', 'profession.id')
                ->where('profession.pf_basket', $basketId)->where('profession.deleted', Config::get('constant.ACTIVE_FLAG'))->count();

        return $data;
    }

    public static function getProfessionInstituteFilter() 
    {
        $data = [
                    ['label' => "Education Stream", 'value' => "Speciality", 'type' => "1", "type_description" => "dropdown"], 
                    ['label' => "State", 'value' => "State", 'type' => "0", "type_description" => "textbox"], 
                    ['label' => "City", 'value' => "City", 'type' => "0", "type_description" => "textbox"], 
                    ['label' => "Pincode", 'value' => "Pincode", 'type' => "0", "type_description" => "textbox"], 
                    ['label' => "Institute Affiliation", 'value' => "Institute_Affiliation", 'type' => "1", "type_description" => "dropdown"], 
                    ['label' => "Category", 'value' => "Management_Category", 'type' => "1", "type_description" => "dropdown"], 
                    ['label' => "Accreditation By", 'value' => "Accreditation", 'type' => "1", "type_description" => "dropdown"], 
                    ['label' => "Hostel Count", 'value' => "Hostel", 'type' => "1", "type_description" => "dropdown"], 
                    ['label' => "Status", 'value' => "Gender", 'type' => "1", "type_description" => "dropdown"], 
                    ['label' => "Fees Range", 'value' => "Fees", 'type' => "2", "type_description" => "2 dropdowns"], 
                ];

        return $data;
    }

}