<?php

namespace App\Http\Controllers\Teenager;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Auth;
use Illuminate\Http\Request;
use App\Video;
use App\CMS;
use App\Testimonial;
use App\FAQ;
use Config;
use Input;
use Helpers;
use App\Services\Teenagers\Contracts\TeenagersRepository;
use Redirect;
use Response;
use Storage;
use App\TeenagerPromiseScore;
use App\PromiseParametersMaxScore;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use App\PaidComponent;
use App\DeductedCoins;
use App\MultipleIntelligentScale;
use App\ApptitudeTypeScale;
use App\PersonalityScale;

class HomeController extends Controller
{
    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    public $redirectTo = '/teenager';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(TeenagersRepository $teenagersRepository)
    {
        //$this->middleware('admin.guest', ['except' => 'logout']);
        $this->teenagersRepository = $teenagersRepository;
        $this->cmsObj = new CMS;
        $this->objTestimonial = new Testimonial;
        $this->objVideo = new Video();
        $this->objTeenagerPromiseScore = new TeenagerPromiseScore();
        $this->objPromiseParametersMaxScore = new PromiseParametersMaxScore();
        $this->objPaidComponent = new PaidComponent;
        $this->objDeductedCoins = new DeductedCoins;
        $this->objMIScale = new MultipleIntelligentScale();
        $this->objApptitudeScale = new ApptitudeTypeScale();
        $this->objPersonalityScale = new PersonalityScale();
        $this->log = new Logger('teenager-home-controller');
        $this->log->pushHandler(new StreamHandler(storage_path().'/logs/monolog-'.date('m-d-Y').'.log'));
        
    }

    /**
     * Show the teenager's home page.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(Auth::guard('teenager')->check()) {
            return redirect()->to(route('teenager.home'));
        }
        $videoCount = $this->objVideo->getAllVideoDetail()->count();
        $videoDetail = $this->objVideo->getVideos();
        $teenText = '';
        $loginInfo = $this->cmsObj->getCmsBySlug('teenagerlogininfotext');
        if(!empty($loginInfo)){
            $loginText = $loginInfo->toArray();
            $teenText = $loginText['cms_body'];
        }
        $testimonials = $this->objTestimonial->getAllTestimonials();
        $quoteImage = 'img/quote.png';
        return view('teenager.index', compact('videoDetail', 'teenText', 'testimonials', 'quoteImage', 'videoCount'));
    }
    
    /**
     * Returns More video on Index page.
     *
     * @return \Illuminate\Http\Response
     */
    public function loadMoreVideo(Request $request)
    {
        $id = $request->id;
        $videoDetail = $this->objVideo->getMoreVideos($id);
        $videoCount = $this->objVideo->loadMoreVideoCount($id);
        return view('teenager.loadMoreVideo', compact('videoDetail', 'videoCount'));
    }

    /**
     * Returns learning Guidance page.
     *
     * @return \Illuminate\Http\Response
     */
    public function learningGuidance()
    {
        $learningGuidance = Helpers::learningGuidance();
        return view('teenager.learningGuidance', compact('learningGuidance'));
    }

    /* Request Params : getInterestDetail
    *  Param : teenagerId
    */
    public function getInterestDetail(Request $request) {
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
                $response['message'] = "Please attemp atleast one section of Profile Builder to view your interest!";
            }
            return view('teenager.basic.myInterest', compact('teenagerInterest'));
            exit;                        
        } else {
            $response['message'] = "Something went wrong!";
        }
        return response()->json($response, 200);
        exit;
    }

    /* Request Params : getStrengthDetail
    *  Param : teenagerId
    */
    public function getStrengthDetail(Request $request) {
        $response = [ 'status' => 0, 'message' => trans('appmessages.default_error_msg') ] ;
        $teenager = $this->teenagersRepository->getTeenagerById($request->teenagerId);        
  
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
                        $teenagerStrength[] = (array('scale'=>$scaleapt,'slug' => $paramkey, 'score' => $teenAptScore, 'points' => $paramvalue, 'name' => $arraypromiseParametersMaxScoreBySlug[$paramkey]['parameter_name'], 'type' => Config::get('constant.APPTITUDE_TYPE'), 'link_url' => url('/teenager/multi-intelligence/').'/'.Config::get('constant.APPTITUDE_TYPE').'/'.$paramkey));
                    }elseif(strpos($paramkey, 'pt_') !== false){
                        $scalept = $this->objPersonalityScale->calculatePersonalityHML($arraypromiseParametersMaxScoreBySlug[$paramkey]['parameter_name'], $paramvalue);
                        $teenAptScore = $this->getTeenScoreInPercentage($arraypromiseParametersMaxScoreBySlug[$paramkey]['parameter_max_score'], $paramvalue);
                        $teenagerStrength[] = (array('scale'=>$scalept,'slug' => $paramkey, 'score' => $teenAptScore, 'points' => $paramvalue, 'name' => $arraypromiseParametersMaxScoreBySlug[$paramkey]['parameter_name'], 'type' => Config::get('constant.PERSONALITY_TYPE'), 'link_url' => url('/teenager/multi-intelligence/').'/'.Config::get('constant.PERSONALITY_TYPE').'/'.$paramkey));
                    }elseif(strpos($paramkey, 'mit_') !== false){
                        $scalemi = $this->objMIScale->calculateMIHML($arraypromiseParametersMaxScoreBySlug[$paramkey]['parameter_name'], $paramvalue);
                        $teenAptScore = $this->getTeenScoreInPercentage($arraypromiseParametersMaxScoreBySlug[$paramkey]['parameter_max_score'], $paramvalue);
                        $teenagerStrength[] = (array('scale'=>$scalemi,'slug' => $paramkey, 'score' => $teenAptScore, 'points' => $paramvalue, 'name' => $arraypromiseParametersMaxScoreBySlug[$paramkey]['parameter_name'], 'type' => Config::get('constant.MULTI_INTELLIGENCE_TYPE'), 'link_url' => url('/teenager/multi-intelligence/').'/'.Config::get('constant.MULTI_INTELLIGENCE_TYPE').'/'.$paramkey));
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
            
            
            return view('teenager.basic.myStrength', compact('teenagerStrength'));
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

    public function saveConsumedCoinsDetails()
    {
        $teenId = Auth::guard('teenager')->user()->id;
        $consumedCoins = Input::get('consumedCoins');
        $componentName = Input::get('componentName');
        $professionId = Input::get('professionId');
        //$remainingDaysForLg = 0;
        $componentsData = $this->objPaidComponent->getPaidComponentsData($componentName);

        if (isset($professionId) && $professionId != "" && $professionId > 0) {
            $deductedCoinsDetail = $this->objDeductedCoins->getDeductedCoinsDetailById($teenId, $componentsData->id, 1, $professionId);
        } else {
            $deductedCoinsDetail = $this->objDeductedCoins->getDeductedCoinsDetailByIdForLS($teenId, $componentsData->id, 1);
        }
        $days = 0;
        if (!empty($deductedCoinsDetail[0])) {
            $days = Helpers::calculateRemainingDays($deductedCoinsDetail[0]->dc_end_date);
        }
        $remainingDays = 0;
        if ($days == 0) {
            $deductCoins = 0;
            //deduct coin from user
            $userDetail = $this->teenagersRepository->getUserDataForCoinsDetail($teenId);
            if (!empty($userDetail)) {
                $deductCoins = $userDetail['t_coins'] - $consumedCoins;
            }
            $returnData = $this->teenagersRepository->updateTeenagerCoinsDetail($teenId, $deductCoins);
            $return = Helpers::saveDeductedCoinsData($teenId, 1, $consumedCoins, $componentName, $professionId);
            if ($return) {
                if (isset($professionId) && $professionId != "" && $professionId > 0) {
                    $updatedDeductedCoinsDetail = $this->objDeductedCoins->getDeductedCoinsDetailById($teenId, $componentsData->id, 1, $professionId);
                } else {
                    $updatedDeductedCoinsDetail = $this->objDeductedCoins->getDeductedCoinsDetailByIdForLS($teenId, $componentsData->id, 1);
                }
                if (!empty($updatedDeductedCoinsDetail)) {
                    $remainingDays = Helpers::calculateRemainingDays($updatedDeductedCoinsDetail[0]->dc_end_date);
                }
            } 
            //Store log in System
            if ($componentName == Config::get('constant.ADVANCE_ACTIVITY')) {
                $coinsConsumedFor = "Advance activity";
            } else if ($componentName == Config::get('constant.LEARNING_STYLE')) {
                $coinsConsumedFor = "Learning guidance";
            } else if ($componentName == Config::get('constant.PROMISE_PLUS')) {
                $coinsConsumedFor = "Promise Plus";
            } else {
                $coinsConsumedFor = "";
            }
            $this->log->info('User coins consumed for' . $coinsConsumedFor, array('userId' => $teenId));
        } 
        return $remainingDays;
    }
    
    public function getUserScoreProgress()
    {
        $user = Auth::guard('teenager')->user();
        $progress = Helpers::calculateProfileComplete($user->id);
        $basicBoosterPoint = $this->teenagersRepository->getTeenagerBasicBooster($user->id);
        $response['progress'] = $progress."%";
        $response['procoins'] = ($user->t_coins > 0) ? number_format($user->t_coins) : 'No Coins';
        $response['totalpoint'] = ( isset($basicBoosterPoint['Total']) && $basicBoosterPoint['Total'] > 0) ? number_format($basicBoosterPoint['Total']) : 0;
        return response()->json($response, 200);
        exit;
    }    
}