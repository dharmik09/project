<?php

namespace App\Http\Controllers\Teenager;

use App\Http\Controllers\Controller;
use Auth;
use Config;
use Storage;
use Input;
use Mail;
use Helpers;
use App\Services\Teenagers\Contracts\TeenagersRepository;
use Redirect;
use Request;
use App\Teenagers;
use Carbon\Carbon;  
use App\TeenagerBoosterPoint;
use App\Services\Professions\Contracts\ProfessionsRepository;

class Level3ActivityController extends Controller {

    public function __construct(TeenagersRepository $teenagersRepository,ProfessionsRepository $professionsRepository) 
    {        
        $this->professionsRepository = $professionsRepository;
        $this->teenagersRepository = $teenagersRepository;   
        $this->teenagerBoosterPoint = new TeenagerBoosterPoint();
    }

    /*
     * Save teen data for L3 career attempt 
     */
    public function level3CareerResearch()
    {
        $teenagerId = Auth::guard('teenager')->user()->id;
        $professionId = Input::get('professionId');
        $type = Input::get('type');
        $isYouTube = Input::get('isYouTube');
        $points = ($isYouTube == 1)?config::get('constant.LEVEL3_PROFESSION_POINTS'):(2*config::get('constant.LEVEL3_PROFESSION_POINTS'));
        
        $teenagerLevelPoints = $this->teenagerBoosterPoint->getTeenagerBoosterPoint($teenagerId,config::get('constant.LEVEL3_ID'));
        
        $addPoint = "no";
        $teenagerProfessionAttempted = $this->professionsRepository->getTeenagerProfessionAttempted($teenagerId, $professionId, $type);
        
        if (count($teenagerProfessionAttempted) > 0) {
           $addedtype = $teenagerProfessionAttempted[0]->tpa_type;
            $addedtypeArr = explode(',', $addedtype);
            if (!in_array($type, $addedtypeArr)) {
                if ($addedtypeArr[0] == '') {
                    $addedtype = $type;
                } else {
                    $addedtype = $addedtypeArr[0] . ',' . $type;
                }
                $addData = $this->professionsRepository->addTeenagerProfessionAttempted($teenagerId, $professionId, $addedtype, $operation = 'update');
                $addPoint = "yes";
            } else {
                $addPoint = "no";
            } 
        } else {
            $addData = $this->professionsRepository->addTeenagerProfessionAttempted($teenagerId, $professionId, $type, $operation = 'add');
            $addPoint = "yes";            
        }
        
        $teenagerLevel3PointsRow['tlb_teenager'] = $teenagerId;
        $teenagerLevel3PointsRow['tlb_level'] = config::get('constant.LEVEL3_ID');
        
        if ($addPoint == "yes") {            
            if (isset($teenagerLevelPoints) && !empty($teenagerLevelPoints)) {                               
                $teenagerLevel3PointsRow['tlb_points'] = $teenagerLevelPoints->tlb_points + $points;   
                unset($teenagerLevel3PointsRow['updated_at']);
                $teenagerLevelPoints = $this->teenagerBoosterPoint->updateTeenagerBoosterPoint($teenagerLevelPoints->id,$teenagerLevel3PointsRow);
            } else {
                $teenagerLevel3PointsRow['tlb_points'] = $points;
                $teenagerLevelPoints = $this->teenagerBoosterPoint->addTeenagerBoosterPoint($teenagerLevel3PointsRow);                
            }
            $proCoins = Teenagers::find($teenagerId);
            $configValue = Helpers::getConfigValueByKey('PROCOINS_FACTOR_L1');
            if($proCoins) {
                $proCoins->t_coins = (int)$proCoins->t_coins + ( $points * $configValue );
                $proCoins->save();
            }
        }        
        echo "success";
        exit;
    }    
}
