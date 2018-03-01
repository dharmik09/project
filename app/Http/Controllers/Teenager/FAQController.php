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
use App\FAQ;

class FAQController extends Controller {

    public function __construct(TeenagersRepository $teenagersRepository) 
    {
        $this->teenagersRepository = $teenagersRepository;
        $this->objFAQ = new FAQ;
        $this->faqThumbImageUploadPath = Config::get('constant.FAQ_ORIGINAL_IMAGE_UPLOAD_PATH');        
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
        
}
