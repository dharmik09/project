<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Reports\Contracts\ReportsRepository;
use App\Services\Level1Activity\Contracts\Level1ActivitiesRepository;
use App\Services\Level2Activity\Contracts\Level2ActivitiesRepository;
use App\Services\Teenagers\Contracts\TeenagersRepository;
use App\Services\Baskets\Contracts\BasketsRepository;
use App\Services\Schools\Contracts\SchoolsRepository;
use App\Services\Professions\Contracts\ProfessionsRepository;
use App\Services\Level4Activity\Contracts\Level4ActivitiesRepository;
use Illuminate\Http\Request;
use Auth;
use Helpers;
use Input;
use Redirect;
use Config;

class ReportController extends Controller {

    public function __construct(SchoolsRepository $schoolsRepository, TeenagersRepository $teenagersRepository, Level1ActivitiesRepository $level1ActivitiesRepository, Level2ActivitiesRepository $level2ActivitiesRepository, BasketsRepository $basketsRepository, ProfessionsRepository $professionsRepository, Level4ActivitiesRepository $level4ActivitiesRepository) {
        $this->level1ActivitiesRepository = $level1ActivitiesRepository;
        $this->level2ActivitiesRepository = $level2ActivitiesRepository;
        $this->teenagersRepository = $teenagersRepository;
        $this->basketsRepository = $basketsRepository;
        $this->professionsRepository = $professionsRepository;
        $this->schoolsRepository         = $schoolsRepository;
        $this->controller = 'ReportController';
        $this->loggedInUser = Auth::guard('admin');
        $this->level4ActivitiesRepository = $level4ActivitiesRepository;
        $this->cartoonThumbImageUploadPath = Config::get('constant.CARTOON_THUMB_IMAGE_UPLOAD_PATH');
        $this->humanThumbImageUploadPath = Config::get('constant.HUMAN_THUMB_IMAGE_UPLOAD_PATH');
    }

    public function level1() {
        $level1Questions = $this->level1ActivitiesRepository->getLevel1AllActiveQuestion();  // Get level1 Activity(question)
        $teenDetails = $this->teenagersRepository->getAllTeenagers();
        $id = Input::get('questionId');
        $gender = Input::get('gen');
        $age = Input::get('age');
        $questionText = '';
        if (isset($id) && $id != '') {
            foreach ($level1Questions as $singleData) {
                if ($singleData->id == $id) {
                    $questionText = $singleData->l1ac_text;
                }
            }
            $chart = Input::get('chart');
        } else {
            $id = $level1Questions[0]->id;
            $questionText = $level1Questions[0]->l1ac_text;
            $chart = 'column';
        }
        $finallevel1 = [];
        $total = 0;
        $anstotal = 0;
        $alltotal = 0;
        $allQuestion = [];
        $suggestion = '';

        if(isset($id) && $id == 0){
            foreach ($level1Questions as $singleData) {
                $level1final = Helpers::calculateTrendForLevel1Admin($singleData->id,$gender,$age);
                foreach ($level1final['trend'] as $key => $value) {
                    $allQuestion[$singleData->id]['trenddata'][$key] = $value;
                }
                $allQuestion[$singleData->id]['text'] = $singleData->l1ac_text;
                $allQuestion[$singleData->id]['total'] = $level1final['alltotal'];
                $allQuestion[$singleData->id]['anstotal'] = $level1final['anstotal'];
            }

        }else{
            $level1final = Helpers::calculateTrendForLevel1Admin($id,$gender,$age); // Get options name and  parecentage for level1(General)
            if (isset($level1final['trend']) && !empty($level1final['trend']))
            {
                foreach ($level1final['trend'] as $key => $value) {
                    $surveyicon1[] = array('y' => $value, 'name' => $key);
                }
                $finallevel1 = json_encode($surveyicon1);
                $total = $level1final['alltotal'];
                $anstotal = $level1final['anstotal'];
            }
        }

        return view('admin.ListLevel1Report', compact('id','level1Questions','questionText','finallevel1', 'final', 'teenDetails','total','chart','allQuestion','gender','anstotal','age'));
    }

    public function level2() {

        $id = Input::get('questionId');

        $level2 = $this->level2ActivitiesRepository->getLevel2AllActiveQuestion(); // Get Level2 Questions
        $gender = Input::get('gen');
        $age = Input::get('age');
        if (isset($id) && $id != '') {
            foreach ($level2 as $singleData) {
                if ($singleData->id == $id) {
                    $questionText = $singleData->l2ac_text;
                }
            }
            $chart = Input::get('chart');
        } else {
            $id = $level2[0]->id;
            $questionText = $level2[0]->l2ac_text;
            $chart = 'column';
        }


        $final = [];
        $level2g = [];
        $total = 0;
        $allQuestion = [];
        $finalLevel2 = [];

        if(isset($id) && $id == 0){
            foreach ($level2 as $singleData) {
                $level2graph = Helpers::calculateTrendForLevel2($singleData->id,$gender,$age);
                foreach ($level2graph['result'] as $key => $value) {
                    $allQuestion[$singleData->id]['level2data'][$key] = $value;
                }
                $allQuestion[$singleData->id]['text'] = $singleData->l2ac_text;
                $allQuestion[$singleData->id]['total'] = $level2graph['total'];
            }

        }else{
            $level2graph = Helpers::calculateTrendForLevel2($id,$gender,$age);  //  Get Level2 Options name and percentage
            if (isset($level2graph['result']) && !empty($level2graph['result'])) {
                foreach ($level2graph['result'] as $key => $value) {
                    $level2g[] = array('y' => $value, 'name' => $key);
                }
                $finalLevel2 = json_encode($level2g);
                $total = $level2graph['total'];
            }
        }

        return view('admin.ListLevel2Report', compact('id', 'level2', 'questionText', 'finalLevel2','total','chart','allQuestion','gender','age'));
    }

    public function getuserapiscore() {

        $id = Input::get('teenagerId');

        $teenager = Helpers::getActiveTeenagers(); //Get teenager name

        if (isset($id) && $id != '') {
            foreach ($teenager as $singleData) {
                if ($singleData->id == $id) {
                    $Name = $singleData->t_name;
                }
            }
            $chart = Input::get('chart');
        } else {
            $id = (isset($teenager) && count($teenager)>0)?$teenager[0]->id:'0';
            $Name = (isset($teenager) && count($teenager)>0)?$teenager[0]->t_name:'';
            $chart = 'column';
        }

        $final = Helpers::getTeenAPIScore($id); //Get teenager api score for lavel2

        $finalMI = [];
        $finalAptitude = [];
        $finalPersanality = [];
        $finalInterest = [];
        $suggestion = '';

        //Get name and value for MI
        $maxScoreMI = array('Interpersonal'=>8,'Logical'=>20,'Linguistic'=>10,'Intrapersonal'=>7,'Existential'=>4,'Bodily-Kinesthetic'=>5,'Spatial'=>9,'Musical'=>6,'Naturalist'=>6);
        $maxScoreAptitude = array('Verbal Reasoning'=>10,'Logical Reasoning'=>15,'Scientific Reasoning'=>5,'Spatial Ability'=>3,'Social Ability'=>5,'Numerical Ability'=>4,'Artistic Ability'=>1,'Creativity'=>1,'Clerical Ability'=>1);
        $maxScorePersonality = array('Social'=>2,'Investigative'=>1,'Conventional'=>2,'Mechanical'=>1,'Enterprising'=>1,'Artistic'=>1);
        $maxScoreInterest = array('Computers, Programming, Logic'=>1,'Sports'=>3,'Language, Reading, Writing'=>1,'Art and Fashion'=>1,'Music and Singing'=>1,'People'=>1,'Nature and Travel'=>5,'Technical and Engineering'=>1,'Creative, Fine Arts'=>2,'Numbers, Accounts and Money'=>1,'Research'=>1,'Performing Arts'=>3,'Social'=>1);

        if (!empty($final['APIscore']['MI'])) {
            foreach ($final['APIscore']['MI'] as $key => $value) {
                $test = 10;
                $MIL[] = array('y' => $value, 'name' => $key);
                $MIMax[] = array('y' => $maxScoreMI[$key], 'name' => $key);
            }
            $finalMI = json_encode($MIL);
            $finalMIMax = json_encode($MIMax);
        }
        
        //Get name and value for Aptitude
        if (!empty($final['APIscore']['aptitude'])) {
            foreach ($final['APIscore']['aptitude'] as $key => $value) {

                $Apti[] = array('y' => $value, 'name' => $key);
                $AptiMax[] = array('y' => $maxScoreAptitude[$key], 'name' => $key);
            }
            $finalAptitude = json_encode($Apti);
            $finalAptMax = json_encode($AptiMax);
        }

        //Get name and value for Personality
        if (!empty($final['APIscore']['personality'])) {
            foreach ($final['APIscore']['personality'] as $key => $value) {

                $Personal[] = array('y' => $value, 'name' => $key);
                $PersonalMax[] = array('y' => $maxScorePersonality[$key], 'name' => $key);
            }
            $finalPersonality = json_encode($Personal);
            $finalPersonalityMax = json_encode($PersonalMax);
        }

        //Get name and value for Interest
        if (!empty($final['APIscore']['interest'])) {
            foreach ($final['APIscore']['interest'] as $key => $value) {

                $Inter[] = array('y' => $value, 'name' => $key);
                $interestMax[] = array('y' => $maxScoreInterest[$key], 'name' => $key);
            }

            $finalInterest = json_encode($Inter);
            $finalInterestMax = json_encode($interestMax);
        }
        $finalscore = array('MI' => $finalMI,'MIMax'=>$finalMIMax, 'Aptitude' => $finalAptitude,'AptitudeMax'=>$finalAptMax, 'Personality' => $finalPersonality,'PersonalityMax'=>$finalPersonalityMax, 'Interest' => $finalInterest, 'finalInterestMax'=>$finalInterestMax);

        return view('admin.ListUserApiScore', compact('Name', 'id', 'teenager', 'finalscore','chart'));
    }

    public function userReport()
    {
        $postChart = Input::get('chart');
        if(isset($postChart) && $postChart != ''){
           $chart = Input::get('chart');
        }else{
           $chart = 'column';
        }
        $activeUsers = Helpers::getActiveTeenagers();
        $webUser = $androidUser = $iosUser = $totalFemale = $totalMale = $selfSponor = $sponsored = $free = $normal = $fb = $google = $webMaleUser = $webFemaleUser = $androidMaleUser = $iosMaleUser = $androidFemaleUser = $iosFemaleUser = array();
        $total = 0;
        if(isset($activeUsers) && !empty($activeUsers)){
            foreach($activeUsers as $key=>$user){
                //Device type users
                if($user['t_device_type'] == 3){
                    $webUser[] = $user['id'];
                    if($user['t_gender'] == 2){
                        $webFemaleUser[] = $user['id'];
                    }else{
                        $webMaleUser[] = $user['id'];
                    }
                }
                elseif($user['t_device_type'] == 2){
                    $androidUser[] = $user['id'];
                    if($user['t_gender'] == 2){
                        $androidFemaleUser[] = $user['id'];
                    }else{
                        $androidMaleUser[] = $user['id'];
                    }
                }
                else{
                    $iosUser[] = $user['id'];
                    if($user['t_gender'] == 2){
                        $iosFemaleUser[] = $user['id'];
                    }else{
                        $iosMaleUser[] = $user['id'];
                    }
                }

                //Gender type users
                if($user['t_gender'] == 2){
                    $totalFemale[] = $user['id'];
                }else{
                    $totalMale[] = $user['id'];
                }

                //Sponsor type users
                if($user['t_sponsor_choice'] == 1){
                    $selfSponor[] = $user['id'];
                }elseif($user['t_sponsor_choice'] == 2){
                    $sponsored[] = $user['id'];
                }else{
                     $free[] = $user['id'];
                }

                //Based on registration
                if($user['t_social_provider'] == 'Normal'){
                    $normal[] = $user['id'];
                }elseif($user['t_social_provider'] == 'Facebook'){
                    $fb[] = $user['id'];
                }else{
                     $google[] = $user['id'];
                }

                $total = count($activeUsers);
            }
            $deviceWiseUser = array('Web'=>count($webUser),'iOS'=>count($iosUser),'Android'=>count($androidUser));
            $genderWiseUser = array('Female'=>count($totalFemale),'Male'=>count($totalMale));
            $sponsorWiseUser = array('Self Sponsor'=>count($selfSponor),'Sponsored'=>count($sponsored),'Free'=>count($free));
            $accountWiseUser = array('Normal'=>count($normal),'Facebook'=>count($fb),'Google'=>count($google));
            $webGenderWiseUser = array('Web Male'=>count($androidMaleUser),'Web Female'=>count($webFemaleUser),'Android Male'=>count($webMaleUser),'Android Female'=>count($androidFemaleUser),'IOS Male'=>count($iosMaleUser),'IOS Female'=>count($iosFemaleUser));

            foreach($deviceWiseUser as $platform=>$number){
                $deviceWiseUserJson[] = array('y' => $number, 'name' => $platform);
            }
            foreach($genderWiseUser as $gender=>$count){
                $genderWiseUserJson[] = array('y' => $count, 'name' => $gender);
            }
            foreach($sponsorWiseUser as $sponsor=>$count){
                $sponsorWiseUserJson[] = array('y' => $count, 'name' => $sponsor);
            }
            foreach($accountWiseUser as $account=>$count){
                $accountWiseUserJson[] = array('y' => $count, 'name' => $account);
            }
            foreach($webGenderWiseUser as $account=>$count){
                $webGenderWiseUserJson[] = array('y' => $count, 'name' => $account);
            }


            $deviceWiseUserJson = json_encode($deviceWiseUserJson);
            $genderWiseUserJson = json_encode($genderWiseUserJson);
            $sponsorWiseUserJson = json_encode($sponsorWiseUserJson);
            $accountWiseUserJson = json_encode($accountWiseUserJson);
            $webGenderWiseUserJson = json_encode($webGenderWiseUserJson);

            return view('admin.UserReport', compact('deviceWiseUserJson','genderWiseUserJson','sponsorWiseUserJson','accountWiseUserJson','total','chart','webGenderWiseUserJson'));
        }
    }

    public function iconReport()
    {
       $humanIcon = array();
       $cartoonIcon = array();
       $humanIconData = array();
       $cartoonIconData = array();
       $finalIconData = array();
       $postChart = Input::get('chart');
       $category_type = Input::get('category_type');
       $humanThumbPath = $this->humanThumbImageUploadPath;
       $cartoonThumbPath = $this->cartoonThumbImageUploadPath;
       $gender = Input::get('gen');

       if(isset($postChart) && $postChart!= ''){
           $chart = $postChart;
       }else{
           $chart = 'column';
       }
       $topAllSelectedIcons = array();
       if(isset($category_type) && $category_type!= ''){
           $category = $category_type;
           $topAllSelectedIcons = $this->level1ActivitiesRepository->getAllSelectedIcons($category,$gender);
       }


       $topSelectedIcons = $this->level1ActivitiesRepository->getTopSelectedIcons($gender);
       if(isset($topSelectedIcons) && !empty($topSelectedIcons))
       {
           foreach($topSelectedIcons['human'] as $icon=>$data){
               $humanIcon[$data->ti_icon_id]['name'] = $data->hi_name;
               $humanIcon[$data->ti_icon_id]['category'] = $data->hic_name;
               $humanIcon[$data->ti_icon_id]['usedtime'] = $data->timesused;
               $humanIcon[$data->ti_icon_id]['hi_image'] = $data->hi_image;
           }
       }

       if(isset($humanIcon) && !empty($humanIcon)){
           foreach($humanIcon as $key=>$val)
           $humanIconData[] = array('y' => intval($val['usedtime']), 'name' => $val['name'],'iconcategory' => $val['category'],'image'=>$val['hi_image']);
       }

       if(isset($topSelectedIcons) && !empty($topSelectedIcons))
       {
           foreach($topSelectedIcons['cartoon'] as $icon=>$data){
               $cartoonIcon[$data->ti_icon_id]['name'] = $data->ci_name;
               $cartoonIcon[$data->ti_icon_id]['category'] = $data->cic_name;
               $cartoonIcon[$data->ti_icon_id]['usedtime'] = $data->timesused;
               $cartoonIcon[$data->ti_icon_id]['ci_image'] = $data->ci_image;
           }
       }
       if(isset($cartoonIcon) && !empty($cartoonIcon)){
           foreach($cartoonIcon as $key=>$val)
           $cartoonIconData[] = array('y' => intval($val['usedtime']), 'name' => $val['name'],'iconcategory' => $val['category'],'image'=>$val['ci_image']);
       }

       $humanIconData = json_encode($humanIconData);
       $cartoonIconData = json_encode($cartoonIconData);
       $finalIconData = array('human'=>$humanIconData,'cartoon'=>$cartoonIconData);

       return view('admin.IconReport', compact('finalIconData','chart','topAllSelectedIcons','category_type','humanThumbPath','cartoonThumbPath','gender'));
    }

    public function level3Report()
    {
       //get basket list
       $baskets = $this->basketsRepository->getBasketsList();
       $postData = Input::all();

       $chart = 'column';
       $basketId = $baskets[0]->id;
       $topList = 'top';
       $finalProfession = array();
       $professionData = array();
       $gender = '';
       if(isset($postData) && !empty($postData)){
           $chart = $postData['chart'];
           $basketId = $postData['basket'];
           $topList = $postData['top'];
           $gender = $postData['gen'];
       }
       $professions = $this->professionsRepository->getProfessionsByBasketId($basketId);
       if(isset($professions) && !empty($professions)){
            foreach($professions as $profession){
                $professionIds[] = $profession->id;
            }
       }
       $notAttemptedProfessions = array();
       $professionCount = $this->professionsRepository->getProfessionAttemptedCount($professionIds,$topList,$gender);

       if(isset($professionCount) && !empty($professionCount)){
            foreach($professionCount as $key=>$val){
                 $finalProfession[$val->id]['professionId'] = $val->id;
                 $finalProfession[$val->id]['attemptecount'] = $val->professionCount;
                 $finalProfession[$val->id]['professioname'] = $val->pf_name;
            }
       }

       foreach($finalProfession as $key=>$val){
           $notAttemptedProfessions[] = $val['professionId'];
       }

       $result=array_diff($professionIds,$notAttemptedProfessions);
       $notattempeted = $this->professionsRepository->getNotAttemptedProfession($result);

       if(isset($topList) && $topList == 'not')
       {
            if(isset($notattempeted) && !empty($notattempeted)){
                foreach($notattempeted as $key=>$val){
                     $professionData[] = array('y' => 0, 'name' => $val->pf_name);
                }
            }
       }
       else
       {
            if(isset($finalProfession) && !empty($finalProfession)){
                foreach($finalProfession as $key=>$val){
                     $professionData[] = array('y' => intval($val['attemptecount']), 'name' => $val['professioname']);
                }
            }
       }

       $professionData = json_encode($professionData);
       return view('admin.Level3Report', compact('professionData','baskets','chart','basketId','topList','gender'));
    }

    public function iconQualityReport()
    {
        $teenDetails = Helpers::getActiveTeenagers();
        $id = Input::get('teenagerId');
        $postType = Input::get('icontype');
        if (isset($postType) && $postType != '') {
            $chart = Input::get('chart');
            $selectedType = Input::get('icontype');
        } else {
            $chart = 'column';
            $selectedType = 'icon';
        }

        //get teen selected icons
        if($selectedType == 'self'){
            $iconType = array(4);
            $displayMsg = "What They Felt About Themselves";
        }else{
            $iconType = array(1,2,3);
            $displayMsg = "Reason\'s They Liked Their ICON's";
        }
        $teenSelectedIcons = $this->teenagersRepository->getTeenSelectedIconWithQualities($id,$iconType);

        $iconQuality = $iconQualityData = array();
        $totalSelectedQualities = 0;
        if(isset($teenSelectedIcons) && !empty($teenSelectedIcons)){
            foreach($teenSelectedIcons as $key=>$icon){
                $totalSelectedQualities += $icon->sum;
            }
        }

        //Get total active qualities
        $totalActiveQualities = $this->level1ActivitiesRepository->getLevel1qualities();
        $totalActiveQualitiesCount = count($totalActiveQualities);

        if(isset($teenSelectedIcons) && !empty($teenSelectedIcons)){
            foreach($teenSelectedIcons as $key=>$val){
                 $iconQualityData[] = array('y' => intval($val->sum), 'name' => $val->l1qa_name);
            }
        }

        $iconQualityData = json_encode($iconQualityData);
        return view('admin.IconQualityReport',compact('teenDetails','chart','id','name','iconQualityData','selectedType','displayMsg'));
    }

    public function level4BasicReport()
    {
        $teenDetails = Helpers::getActiveTeenagers();
        //Get active profession lists
        $professions = $this->professionsRepository->getAllActiveProfession();

        $postData = Input::All();
        $gender = Input::get('gen');
        if (isset($postData) && !empty($postData)) {
            $chart = $postData['chart'];
            $teenagerid = $postData['teenagerId'];
            $professionid = $postData['professionId'];
        } else {
            $professionid = (isset($professions[0]->id))? $professions[0]->id : 0;
            $teenagerid = (isset($teenDetails) && count($teenDetails)>0)?$teenDetails[0]->id:'0';
            $chart = 'column';
        }
        $professionArray = $this->professionsRepository->getTeenagerAttemptedProfessionForReport($teenagerid,$professionid);

        $teenArray = [];
        if ($gender != '') {
            foreach($professionArray AS $k => $val) {
                if ($val->t_gender == $gender) {
                    $teenArray[] = $val->id;
                }
            }
            $allTeen = count($teenArray);
        } else {
            $allTeen = count($professionArray);
        }

        $basicDataJson = $basicData = array();
        if ($teenagerid == 0) {
            $basicData = $this->teenagersRepository->getTeenagerAllTypeBadgesForReport($teenagerid, $professionid,$gender);
        } else {
            $basicData = $this->teenagersRepository->getTeenagerAllTypeBadges($teenagerid, $professionid);
        }

        if($teenagerid == 0){
            $allTeen =  $allTeen * ($basicData['level4Basic']['totalPoints']);
            $basicData = array('Total Point' => $allTeen, 'Earned Points' => $basicData['level4Basic']['earnedPoints']);
        } else {
            $allTeen =  $basicData['level4Basic']['totalPoints'];
            $basicData = array('Total Point' => $allTeen, 'Earned Points' => $basicData['level4Basic']['earnedPoints'], 'Badge Star'=>$basicData['level4Basic']['badgesStarCount']);
        }


        foreach($basicData as $key=>$data){
                $basicDataJson[] = array('y' => intval($data), 'name' => $key);
            }
        $basicData = json_encode($basicDataJson);
        $totalTeen = count($professionArray);
        $totalTeenByGender = count($teenArray);
        return view('admin.Level4BasicReport',compact('professions','chart','professionid','basicData','teenDetails','teenagerid','gender','totalTeen','totalTeenByGender'));

    }

    public function level4AdvanceReport()
    {
        $teenDetails = Helpers::getActiveTeenagers();
        //Get active profession lists
        $professions = $this->professionsRepository->getAllActiveProfession();

        $postData = Input::All();
        $gender = Input::get('gen');
        if (isset($postData) && !empty($postData)) {
            $chart = $postData['chart'];
            $teenagerid = $postData['teenagerId'];
            $professionid = $postData['professionId'];

        } else {
            $professionid = $professions[0]->id;
            $teenagerid = $teenDetails[0]->id;
            $chart = 'column';
        }
        $professionArray = $this->professionsRepository->getTeenagerAttemptedProfessionForReport($teenagerid,$professionid);
        $teenArray = [];
        if ($gender != '') {
            foreach($professionArray AS $k => $val) {
                if ($val->t_gender == $gender) {
                    $teenArray[] = $val->id;
                }
            }
            $allTeen = count($teenArray);
        } else {
            $allTeen = count($professionArray);
        }
        if($teenagerid == 0){
            $totaladvancePoint =  $allTeen * (config::get('constant.TOTAL_L4_ADVANCE_POINT'));
        } else {
            $totaladvancePoint = config::get('constant.TOTAL_L4_ADVANCE_POINT');
        }

        $advanceDataJson = $advanceData = array();
         if ($teenagerid == 0) {
             $advanceData = $this->teenagersRepository->getTeenagerAllTypeBadgesForReport($teenagerid, $professionid,$gender);
             $advanceData = array('Earned Points' => $advanceData['level4Advance']['earnedPoints'], 'Verified Images'=>$advanceData['level4Advance']['snap'],'Verified Document'=>$advanceData['level4Advance']['report'],'Verified Video'=>$advanceData['level4Advance']['shoot']);
        } else {
            $advanceData = $this->teenagersRepository->getTeenagerAllTypeBadges($teenagerid, $professionid);
             $advanceData = array('Earned Points' => $advanceData['level4Advance']['earnedPoints'], 'Badge Star'=>$advanceData['level4Advance']['advanceBadgeStar'],'Verified Images'=>$advanceData['level4Advance']['snap'],'Verified Document'=>$advanceData['level4Advance']['report'],'Verified Video'=>$advanceData['level4Advance']['shoot']);
        }

        foreach($advanceData as $key=>$data){
                $advanceDataJson[] = array('y' => intval($data), 'name' => $key);
            }
        $advanceData = json_encode($advanceDataJson);
        $totalTeen = count($professionArray);
        $totalTeenByGender = count($teenArray);
        return view('admin.Level4AdvanceReport',compact('professions','chart','professionid','advanceData','teenDetails','teenagerid','totaladvancePoint','gender','totalTeen','totalTeenByGender'));
    }

    public function level4IntermediateReport()
    {
        $teenDetails = Helpers::getActiveTeenagers();
        //Get active profession lists
        $professions = $this->professionsRepository->getAllActiveProfession();

        $postData = Input::All();
        $gender = Input::get('gen');
        if (isset($postData) && !empty($postData)) {
            $chart = $postData['chart'];
            $teenagerid = $postData['teenagerId'];
            $professionid = $postData['professionId'];
            $concept = isset($postData['concept'])?$postData['concept']:'0';
        }else {
            $professionid = $professions[0]->id;
            $teenagerid = $teenDetails[0]->id;
            $chart = 'column';
            $concept = 0;
        }
        $professionArray = $this->professionsRepository->getTeenagerAttemptedProfessionForReport($teenagerid,$professionid);
        $teenArray = [];
        if ($gender != '') {
            foreach($professionArray AS $k => $val) {
                if ($val->t_gender == $gender) {
                    $teenArray[] = $val->id;
                }
            }
            $allTeen = count($teenArray);
        } else {
            $allTeen = count($professionArray);
        }

        $intermediateDataJson = $intermediateData = array();
        if ($teenagerid == 0) {
            $intermediateData = $this->teenagersRepository->getTeenagerAllTypeBadgesForReport($teenagerid, $professionid,$gender);
        } else {
            $intermediateData = $this->teenagersRepository->getTeenagerAllTypeBadges($teenagerid, $professionid);
        }

        if(isset($postData['concept']) && $postData['concept'] > 0){
            if($teenagerid == 0){
                $allTeen =  $allTeen * ($intermediateData['level4Intermediate']['templateWiseTotalPoint'][$concept]);
                $intermediateData = array('Total Points'=>$allTeen,'Earned Points' => $intermediateData['level4Intermediate']['templateWiseEarnedPoint'][$concept]/*,'Badge Star'=>$intermediateData['level4Intermediate']['badgesCount']*/);
            } else {
                $allTeen =  $intermediateData['level4Intermediate']['templateWiseTotalPoint'][$concept];
                $intermediateData = array('Total Points'=>$allTeen,'Earned Points' => $intermediateData['level4Intermediate']['templateWiseEarnedPoint'][$concept],'Badge Star'=>$intermediateData['level4Intermediate']['badgesCount']);
            }
        }else{
            if($teenagerid == 0){
                $allTeen =  $allTeen * ($intermediateData['level4Intermediate']['totalPoints']);
                $intermediateData = array('Total Points'=>$allTeen,'Earned Points' => $intermediateData['level4Intermediate']['earnedPoints']/*,'Badge Star'=>$intermediateData['level4Intermediate']['badgesCount']*/);
            } else {
                $allTeen =  $intermediateData['level4Intermediate']['totalPoints'];
                $intermediateData = array('Total Points'=>$allTeen,'Earned Points' => $intermediateData['level4Intermediate']['earnedPoints'],'Badge Star'=>$intermediateData['level4Intermediate']['badgesCount']);
            }

        }
        foreach($intermediateData as $key=>$data){
            $intermediateDataJson[] = array('y' => intval($data), 'name' => $key);
        }

        $intermediateData = json_encode($intermediateDataJson);
        $totalTeen = count($professionArray);
        $totalTeenByGender = count($teenArray);
        return view('admin.Level4IntermediateReport',compact('professions','chart','professionid','intermediateData','teenDetails','teenagerid','getQuestionTemplateForProfession','concept','gender','totalTeen','totalTeenByGender'));
    }

    public function getProfessionConcepts()
    {
        $professionId = Input::get('professionid');
        $selectedconcept = Input::get('concept');
        $all = Input::get('all');
        //Get concept for the profession
        $getQuestionTemplateForProfession = $this->level4ActivitiesRepository->getQuestionTemplateForProfession($professionId);
        return view('admin.AjaxProfessionConcepts',compact('getQuestionTemplateForProfession','selectedconcept','all'));
        exit;
    }

    public function schoolReport(Request $request)
    {
        $school_id = Input::get("school_id");
        $class_id = Input::get("class_id");
        $schools = $this->schoolsRepository->getAllSchoolsData();
        $schoolClass = ($school_id > 0) ? $this->schoolsRepository->getClassDetail($school_id) : [];
        $studentData = ($school_id > 0 && $class_id != "") ? $this->schoolsRepository->getClassStudentList($school_id, $class_id) : [];
        //echo "<pre/>"; print_r($studentData); die();
        $level1Students = $level2Students = $level3Students = $level4Students = $schoolValidate = $varifiedStudent = 0;
        if(isset($studentData[0]) && !empty($studentData[0]))
        {
            foreach($studentData as $key => $value)
            {
                $getTeenagerBoosterPoints = $this->teenagersRepository->getTeenagerBoosterPoints($value->id);
                $studentData[$key]->level_1 = (isset($getTeenagerBoosterPoints['Level1'])) ? $getTeenagerBoosterPoints['Level1'] : 0;
                $studentData[$key]->level_2 = (isset($getTeenagerBoosterPoints['Level2'])) ? $getTeenagerBoosterPoints['Level2'] : 0;
                $studentData[$key]->level_3 = (isset($getTeenagerBoosterPoints['Level3'])) ? $getTeenagerBoosterPoints['Level3'] : 0;
                $studentData[$key]->level_4 = (isset($getTeenagerBoosterPoints['Level4'])) ? $getTeenagerBoosterPoints['Level4'] : 0;
                $level1Students = (isset($getTeenagerBoosterPoints['Level1Progress']) && $getTeenagerBoosterPoints['Level1Progress'] > 0) ? $level1Students + 1 : $level1Students;
                $level2Students = (isset($getTeenagerBoosterPoints['Level2Progress']) && $getTeenagerBoosterPoints['Level2Progress'] > 0) ? $level2Students + 1 : $level2Students;
                $level3Students = (isset($getTeenagerBoosterPoints['Level3Progress']) && $getTeenagerBoosterPoints['Level3Progress'] > 0) ? $level3Students + 1 : $level3Students;
                $level4Students = (isset($getTeenagerBoosterPoints['Level4Progress']) && $getTeenagerBoosterPoints['Level4Progress'] > 0) ? $level4Students + 1 : $level4Students;
                $schoolValidate = (isset($value->t_school_status) && $value->t_school_status == 1) ? $schoolValidate + 1 : $schoolValidate;
                $varifiedStudent = (isset($value->t_isverified) && $value->t_isverified == 1) ? $varifiedStudent + 1 : $varifiedStudent;
            }
        }
        $reportData = array('School Validate' => $schoolValidate, 'Verified Student' => $varifiedStudent, 'Level 1'=> $level1Students,'Level 2'=> $level2Students ,'Level 3'=> $level3Students, 'Level 4' => $level4Students);         
        foreach($reportData as $platform => $number) {
            $reportDataJson[] = array('y' => $number, 'name' => $platform);
        }
        $reportDataJson = json_encode($reportDataJson);

        return view('admin.SchoolReport', compact('reportDataJson', 'schools', 'studentData', 'schoolClass', 'school_id', 'class_id'));
        exit;
    }

    public function getClass(Request $request)
    {
        $school_id = Input::get("school_id");
        $result = $this->schoolsRepository->getClassDetail($school_id);
        return view('admin.AjaxSchoolClass', compact('result'));
    }    
}
