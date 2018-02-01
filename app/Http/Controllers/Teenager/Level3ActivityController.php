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
use Carbon\Carbon;  

class Level3ActivityController extends Controller {

    public function __construct(TeenagersRepository $teenagersRepository) 
    {        
        $this->teenagersRepository = $teenagersRepository;        
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
        
    }
    
}
