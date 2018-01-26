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
use App\Helptext;

class HelpController extends Controller {

    public function __construct(TeenagersRepository $teenagersRepository) 
    {
        $this->teenagersRepository = $teenagersRepository;
        $this->objHelptext = new Helptext;
    }
    
    /*
     * Get chat users and pass json data
     */
    public function getHelpTextBySlug()
    { 
        $helpSlug = Input::get('helpSlug');
        
        $helptext = $this->objHelptext->getHelptextBySlug($helpSlug);
        if(isset($helptext) && count($helptext) > 0){
            $help = $helptext->h_description;
        }else{
            $help = 'Invalid slug passed';
        }
        return $help;            
    }        
}
