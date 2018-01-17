<?php

namespace App\Http\Controllers\Teenager;

use App\Http\Controllers\Controller;
use App\Services\Professions\Contracts\ProfessionsRepository;
use App\Services\Baskets\Contracts\BasketsRepository;
use App\Baskets;
use Auth;
use Config;
use Storage;
use Input;
use Mail;
use Helpers;
use Redirect;
use Request;    

class ProfessionController extends Controller {

    public function __construct(ProfessionsRepository $professionsRepository, BasketsRepository $basketsRepository) 
    {
        $this->professionsRepository = $professionsRepository;
        $this->baskets = new Baskets();
    }

    public function listIndex(){
        $userid = Auth::guard('teenager')->user()->id;
        $totalProfessionCount = $this->professionsRepository->getAllProfessionsCount($userid);
        $teenagerTotalProfessionAttemptedCount = $this->professionsRepository->getTeenagerTotalProfessionAttempted($userid);
        $basketsData = $this->baskets->with('profession')->where('deleted',config::get('constant.ACTIVE_FLAG'))->get();
        return view('teenager.careersListing', compact('basketsData','totalProfessionCount','teenagerTotalProfessionAttemptedCount'));
    }

    public function gridIndex(){
        $userid = Auth::guard('teenager')->user()->id;
        $totalProfessionCount = $this->professionsRepository->getAllProfessionsCount($userid);
        $teenagerTotalProfessionAttemptedCount = $this->professionsRepository->getTeenagerTotalProfessionAttempted($userid);
        $basketsData = $this->baskets->with('profession')->where('deleted',config::get('constant.ACTIVE_FLAG'))->get();
        return view('teenager.careerGrid', compact('basketsData','totalProfessionCount','teenagerTotalProfessionAttemptedCount'));
    }

    public function listGetIndex(){
        $userid = Auth::guard('teenager')->user()->id;
        $basketsData = $this->baskets->with('profession')->find(Input::get('basket_id'));

        $professionAttemptedCount = 0;
        foreach ($basketsData->profession as $k => $v) {
            $professionAttempted = $this->professionsRepository->getTeenagerProfessionAttempted($userid, $v->id,null);
            if(count($professionAttempted)>0){
                $basketsData['profession'][$k]['attempted'] = 'yes';
                $professionAttemptedCount++;
            }
        }

        $return = '<div class="panel-body">
                        <div class="related-careers careers-tag">
                            <div class="career-heading clearfix">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p>You have completed <strong>'.$professionAttemptedCount.' of '.count($basketsData->profession).'</strong> careers</p>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="pull-right">
                                            <ul class="match-list">
                                                <li><span class="number match-strong">4</span> Strong match</li>
                                                <li><span class="number match-potential">5</span> Potential match</li>
                                                <li><span class="number match-unlikely">4</span> Unlikely match</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <ul class="career-list">';

        foreach($basketsData->profession as $k => $v){

            $return .= '<li class="match-strong complete-feild"><a href="#" title="'.$v->pf_name.'">'.$v->pf_name.'</a>';
                if(isset($v->attempted)){
                    $return .= '<a class="complete"><span>Complete <i class="icon-thumb"></i></span></a>';
                }
            $return .= '</li>';
        }

        $return .= '</ul></div></div>';
        
        return $return;
    }

    public function gridGetIndex(){
        $userid = Auth::guard('teenager')->user()->id;
        $basketsData = $this->baskets->with('profession')->find(Input::get('basket_id'));
        $professionAttemptedCount = 0;
        foreach ($basketsData->profession as $k => $v) {
            $professionAttempted = $this->professionsRepository->getTeenagerProfessionAttempted($userid, $v->id,null);
            if(count($professionAttempted)>0){
                $basketsData['profession'][$k]['attempted'] = 'yes';
                $professionAttemptedCount++;
            }
        }

        $video = Helpers::youtube_id_from_url($basketsData->b_video);

        $return = '<div class="banner-landing banner-career" style="background-image:url('.Storage::url(Config::get('constant.BASKET_ORIGINAL_IMAGE_UPLOAD_PATH').$basketsData->b_logo).');">
                        <div class="">
                            <div class="play-icon"><a id="link'.$basketsData->id.'" onclick="playVideo(this.id,\''.$video.'\');" class="play-btn" id="iframe-video"><img src="'.Storage::url('img/play-icon.png').'" alt="play icon"></a></div>
                        </div><iframe width="100%" height="100%" frameborder="0" allowfullscreen class="iframe" id="iframe-video-link'.$basketsData->id.'"></iframe>
                    </div>
                    <section class="sec-category"><div class="row">
                        <div class="col-md-6">
                            <p>You have completed <strong>'.$professionAttemptedCount.' of '.count($basketsData->profession).'</strong> careers</p>
                        </div>
                        <div class="col-md-6">
                            <div class="pull-right">
                                <ul class="match-list">
                                    <li><span class="number match-strong">4</span> Strong match</li>
                                    <li><span class="number match-potential">5</span> Potential match</li>
                                    <li><span class="number match-unlikely">4</span> Unlikely match</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="career-map">
                        <div class="row">';

        foreach($basketsData->profession as $k => $v){

            $return .= '<div class="col-md-4 col-sm-6">
                            <div class="category match-strong"><a href="#" title="'.$v->pf_name.'">'.$v->pf_name.'</a>
                                                            ';
                if(isset($v->attempted)){
                    $return .= ' <span class="complete"><a href="#" title="Completed"><i class="icon-thumb"></i></a></span>';
                }
            $return .= '<div class="overlay">
                            <span class="salary">Salary: $32,500</span>
                            <span class="assessment">Assessment: High Growth</span>
                        </div>
                    </div>
                </div>';
        }

        $return .= '</div></div></section>';
        
        return $return;
    }

    public function gridGetSearch(){
        $userid = Auth::guard('teenager')->user()->id;
        $this->value = Input::get('search_text');
        $basketsData = $this->baskets
                        ->with(['profession' => function ($query) {
                            $query->where('pf_name', 'like', '%'.$this->value.'%');
                        }])
                        ->whereHas('profession', function ($query) {
                            $query->where('pf_name', 'like', '%'.$this->value.'%');
                        })
                        ->get();
        $return = '<p>Sorry!!! No result Found</p>';
        if($basketsData)
        {
            $return = '';
        
            foreach ($basketsData as $key => $value) {
                $professionAttemptedCount = 0;
                foreach($value->profession as $k => $v){
                    $professionAttempted = $this->professionsRepository->getTeenagerProfessionAttempted($userid, $v->id,null);
                    if(count($professionAttempted)>0){
                        $basketsData[$key]['profession'][$k]['attempted'] = 'yes';
                        $professionAttemptedCount++;
                    }
                }
                $return .= '<div class="panel panel-default">
                                <div class="panel-heading">
                                    <h4 class="panel-title"><a data-parent="#accordion" data-toggle="collapse" href="#accordion'.$value->id.'" id="'.$value->id.'" class="collapsed">'.$value->b_name.'</a> <a href="#" title="Grid view" class="grid"><i class="icon-list"></i></a></h4>
                                </div>
                                <div class="panel-collapse collapse in" id="accordion'.$value->id.'">
                                    <div class="panel-body">
                                        <section class="career-content">
                                            <div class="bg-white">
                                                <div id="profession'.$value->id.'">';


                $video = Helpers::youtube_id_from_url($value->b_video);

                $return .= '<div class="banner-landing banner-career" style="background-image:url('.Storage::url(Config::get('constant.BASKET_ORIGINAL_IMAGE_UPLOAD_PATH').$value->b_logo).');">
                                <div class="">
                                    <div class="play-icon"><a id="link'.$value->id.'" onclick="playVideo(this.id,\''.$video.'\');" class="play-btn" id="iframe-video"><img src="'.Storage::url('img/play-icon.png').'" alt="play icon"></a></div>
                                </div><iframe width="100%" height="100%" frameborder="0" allowfullscreen class="iframe" id="iframe-video-link'.$value->id.'"></iframe>
                            </div>
                            <section class="sec-category"><div class="row">
                                <div class="col-md-6">
                                    <p>You have completed <strong>'.$professionAttemptedCount.' of '.count($value->profession).'</strong> careers</p>
                                </div>
                                <div class="col-md-6">
                                    <div class="pull-right">
                                        <ul class="match-list">
                                            <li><span class="number match-strong">4</span> Strong match</li>
                                            <li><span class="number match-potential">5</span> Potential match</li>
                                            <li><span class="number match-unlikely">4</span> Unlikely match</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="career-map">
                                <div class="row">';

                foreach($value->profession as $k => $v){

                    $return .= '<div class="col-md-4 col-sm-6">
                                    <div class="category match-strong"><a href="#" title="'.$v->pf_name.'">'.$v->pf_name.'</a>
                                                                    ';
                        if(isset($v->attempted)){
                            $return .= ' <span class="complete"><a href="#" title="Completed"><i class="icon-thumb"></i></a></span>';
                        }
                    $return .= '<div class="overlay">
                                    <span class="salary">Salary: $32,500</span>
                                    <span class="assessment">Assessment: High Growth</span>
                                </div>
                            </div>
                        </div>';
                }

                $return .= '</div>
                                    </div>
                                    </section>
                                    </div>
                                </div>
                            </section>
                        </div>
                    </div>
                </div>';
            }
        }
        return $return;
    }
}

