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

    public function index(){
        $userid = Auth::guard('teenager')->user()->id;
        $basketsData = $this->baskets->with('profession')->get();
        return view('teenager.careersListing', compact('basketsData'));
    }

    public function getIndex(){
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

}
