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
use App\Services\Level4Activity\Contracts\Level4ActivitiesRepository;

class Level4ActivityController extends Controller {

    public function __construct(Level4ActivitiesRepository $level4ActivitiesRepository, TeenagersRepository $teenagersRepository, ProfessionsRepository $professionsRepository) 
    {        
        $this->professionsRepository = $professionsRepository;
        $this->teenagersRepository = $teenagersRepository;   
        $this->level4ActivitiesRepository = $level4ActivitiesRepository;   
        $this->teenagerBoosterPoint = new TeenagerBoosterPoint();
    }

    /*
     * Save teen data for L3 career attempt 
     */
    public function professionBasicQuestion() {
        $professionId = Input::get('professionId');
        $userId = Auth::guard('teenager')->user()->id;
        if($userId > 0 && $professionId != '') {
            $totalQuestion = $this->level4ActivitiesRepository->getNoOfTotalQuestionsAttemptedQuestion($userId, $professionId);
            $activities = $this->level4ActivitiesRepository->getNotAttemptedActivities($userId, $professionId);
            
            if (isset($activities[0]) && !empty($activities[0])) {
                $activity = $activities[0];
                $timer = $activities[0]->timer;
            } else {
                $activity = [];
                $timer = 0;
            }

            $response = [];
            $response['data'] = $activity;
            $response['timer'] = $timer;
            $response['professionId'] = $professionId;
            $response['status'] = 1;
                        
            return view('teenager.basic.careerBasicQuizSection', compact('response'));
        }
        $response['status'] = 0;
        $response['message'] = "Something went wrong!";

        return response()->json($response, 200);
        exit;
    }    
}
