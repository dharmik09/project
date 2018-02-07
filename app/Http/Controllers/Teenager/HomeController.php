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
        $this->objFAQ = new FAQ;
        $this->faqThumbImageUploadPath = Config::get('constant.FAQ_THUMB_IMAGE_UPLOAD_PATH');
        $this->objVideo = new Video();
        $this->objTeenagerPromiseScore = new TeenagerPromiseScore();
        $this->objPromiseParametersMaxScore = new PromiseParametersMaxScore();
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
     * Show FAQ page.
     *
     * @return \Illuminate\Http\Response
     */
    public function help()
    {
        $searchText = Input::get('search_help');
        $searchedAnsColumnIds = array();
        $ansIds = array();
        if (isset($searchText) && !empty($searchText)) {
            $helps = $this->objFAQ->getSearchedFAQ($searchText);
            $searchedAnsColumnIds = $this->objFAQ->getSearchedFAQFromAnsColumn($searchText);
            foreach ($searchedAnsColumnIds as $searchedAnsColumnId) {
                $ansIds[] = $searchedAnsColumnId->id;
            }
        } else {
            $helps = $this->objFAQ->getAllFAQ();
        }
        $faqThumbImageUploadPath = $this->faqThumbImageUploadPath;
        return view('teenager.help', compact('helps', 'faqThumbImageUploadPath', 'searchText', 'ansIds'));
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
                $response['message'] = "Please attemp atleast one section of Profile Builder to view your strength!";
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
            $teenagerStrength = $arraypromiseParametersMaxScoreBySlug = [];
            
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
                        $teenAptScore = $this->getTeenScoreInPercentage($arraypromiseParametersMaxScoreBySlug[$paramkey]['parameter_max_score'], $paramvalue);
                        $teenagerStrength[] = (array('slug' => $paramkey, 'score' => $teenAptScore, 'points' => $paramvalue, 'name' => $arraypromiseParametersMaxScoreBySlug[$paramkey]['parameter_name'], 'type' => Config::get('constant.APPTITUDE_TYPE'), 'link_url' => url('/teenager/multi-intelligence/').'/'.Config::get('constant.APPTITUDE_TYPE').'/'.$paramkey));
                    }elseif(strpos($paramkey, 'pt_') !== false){
                        $teenAptScore = $this->getTeenScoreInPercentage($arraypromiseParametersMaxScoreBySlug[$paramkey]['parameter_max_score'], $paramvalue);
                        $teenagerStrength[] = (array('slug' => $paramkey, 'score' => $teenAptScore, 'points' => $paramvalue, 'name' => $arraypromiseParametersMaxScoreBySlug[$paramkey]['parameter_name'], 'type' => Config::get('constant.PERSONALITY_TYPE'), 'link_url' => url('/teenager/multi-intelligence/').'/'.Config::get('constant.PERSONALITY_TYPE').'/'.$paramkey));
                    }elseif(strpos($paramkey, 'mit_') !== false){
                        $teenAptScore = $this->getTeenScoreInPercentage($arraypromiseParametersMaxScoreBySlug[$paramkey]['parameter_max_score'], $paramvalue);
                        $teenagerStrength[] = (array('slug' => $paramkey, 'score' => $teenAptScore, 'points' => $paramvalue, 'name' => $arraypromiseParametersMaxScoreBySlug[$paramkey]['parameter_name'], 'type' => Config::get('constant.MULTI_INTELLIGENCE_TYPE'), 'link_url' => url('/teenager/multi-intelligence/').'/'.Config::get('constant.MULTI_INTELLIGENCE_TYPE').'/'.$paramkey));
                    }
                }
            }else{
                $response['message'] = "Please attemp atleast one section of Profile Builder to view your strength!";
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
}