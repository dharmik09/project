<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Auth;
use Helpers;
use Illuminate\Http\Request;
use App\Teenagers;
use App\Sponsors;
use App\Schools;
use App\Parents;
use App\Level4Activity;
use App\Baskets;
use App\Professions;
use App\Services\Level1Activity\Contracts\Level1ActivitiesRepository;
use App\Services\Level2Activity\Contracts\Level2ActivitiesRepository;
use App\Services\Level4Activity\Contracts\Level4ActivitiesRepository;

class DashboardController extends Controller
{
    public function __construct(Level1ActivitiesRepository $level1ActivitiesRepository, Level2ActivitiesRepository $level2ActivitiesRepository, Level4ActivitiesRepository $level4ActivitiesRepository)
    {
        $this->objTeenager = new Teenagers();
        $this->objSponsors = new Sponsors();
        $this->objSchool = new Schools();
        $this->objParent = new Parents();
        $this->objBaskets = new Baskets();
        $this->objProfessions = new Professions();
        $this->objLevel4Activity = new Level4Activity();
        $this->level1ActivitiesRepository = $level1ActivitiesRepository;
        $this->level2ActivitiesRepository = $level2ActivitiesRepository;
        $this->level4ActivitiesRepository = $level4ActivitiesRepository;
    }

    public function index()
    {   
        $parentType = 1;
        $counsellorType = 2;
        
        //Get active teenager count
        $teenagers = $this->objTeenager->getActiveTeenagers();
        $countteen = ( isset($teenagers) && !empty($teenagers) ) ? count($teenagers) : '0';
        
        //Get active sponsor
        $sponsor = $this->objSponsors->getActiveSponsors();
        $countsponsor = (isset($sponsor) && !empty($sponsor)) ? count($sponsor) : '0';    
               
        //Get active school
        $school = $this->objSchool->getActiveSchools();
        $countschool = (isset($school) && !empty($school)) ? count($school) : '0';    
                
        //Get active parent
        $parent = $this->objParent->getActiveParents($parentType);
        $countparent = (isset($parent) && !empty($parent)) ? count($parent) : '0';
        
        //Get active counsellor
        $counsellor = $this->objParent->getActiveParents($counsellorType);
        $countcounsellor = (isset($counsellor) && !empty($counsellor)) ? count($counsellor) : '0';
                            
        //Get active level1 activity
        $level1activity = $this->level1ActivitiesRepository->getLevel1AllActiveQuestion();
        $countlevel1activity = (isset($level1activity) && !empty($level1activity)) ? count($level1activity) : '0';   
                
        //Get active level2 activity
        $level2activity = $this->level2ActivitiesRepository->getLevel2AllActiveQuestion();
        $countlevel2activity = (isset($level2activity) && !empty($level2activity)) ? count($level2activity) : '0';
        
        //Get active level4 basicactivity
        $level4activity = $this->objLevel4Activity->getLevel4BasicActivity();
        $countlevel4activity = (isset($level4activity) && !empty($level4activity)) ? count($level4activity) : '0';
                
        //Get active level4 intermediateactivity
        $level4intermediateactivity = Helpers::getLevel4IntermediateActivity();        
        $countlevel4intermediateactivity = (isset($level4intermediateactivity) && !empty($level4intermediateactivity)) ? count($level4intermediateactivity) : '0';
                
        //Get active level4 advanceactivity
        $level4advanceactivity = Helpers::getLevel4AdvanceActivity();
        $countlevel4advanceactivityCount = 0;
                
        //Get active Basket
        $basket = $this->objBaskets->getActiveBaskets();   
        $countbasket = (isset($basket) && !empty($basket))?count($basket):'0';        
        
        //Get active Profession
        $profession= $this->objProfessions->getActiveProfessions();   
        $countprofession = (isset($profession) && !empty($profession))?count($profession):'0';        
                            
        //Get Level1 HumanCategory
        $humanCategory= Helpers::getActiveCategory();   
        $counthumanCategory = (isset($humanCategory) && !empty($humanCategory))?count($humanCategory):'0';  
                
        //Get Level1 CartoonCategory
        $cartoonCategory= Helpers::getActiveCartoonCategory();   
        $countcartoonCategory = (isset($cartoonCategory) && !empty($cartoonCategory))?count($cartoonCategory):'0';  
               
        //Get Level4 advance activity 
        $l4AdvanceActivities = $this->level4ActivitiesRepository->getAllLevel4AdvanceActivity();
        $countl4AdvanceActivities = (isset($l4AdvanceActivities) && !empty($l4AdvanceActivities))?count($l4AdvanceActivities):'0';  
        
        
        $finalData = array('Total teens'=>$countteen,'Total parents'=>$countparent,'Total mentors'=>$countcounsellor,'Total sponsors'=>$countsponsor,'Total schools'=>$countschool,'Total L1 activities'=>$countlevel1activity,'Total Non-Fiction categories'=>$counthumanCategory,'Total fiction categories'=>$countcartoonCategory,'Total L2 activities'=>$countlevel2activity,'Total baskets'=>$countbasket,'Total professions'=>$countprofession,'Total L4 Basic activities'=>$countlevel4activity,'Total L4 Intermediate activities'=>$countlevel4intermediateactivity,'Total L4 Advance activities'=>$countl4AdvanceActivities);
        $urls = array('Total teens'=>'teenagers','Total parents'=>'parents/1','Total mentors'=>'counselors/2','Total sponsors'=>'sponsors','Total schools'=>'schools',
            'Total L1 activities'=>'level1Activity','Total Non-Fiction categories'=>'humanIconsCategory','Total fiction categories'=>'cartoonIconsCategory',
            'Total L2 activities'=>'level2Activity',
            'Total baskets'=>'baskets','Total professions'=>'professions','Total L4 Basic activities'=>'level4Activity',            
            'Total L4 Intermediate activities'=>'listLevel4IntermediateActivity','Total L4 Advance activities'=>'listlevel4advanceactivity');
        
        foreach($finalData as $name=>$counting){
            $dashboardData[] = array('y' => $counting, 'name' => $name,'url' => $urls[$name]);
        }
        $dashboardData = json_encode($dashboardData);
        return view('admin.Home', compact('dashboardData'));
    }
}
