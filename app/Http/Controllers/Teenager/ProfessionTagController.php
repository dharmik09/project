<?php

namespace App\Http\Controllers\Teenager;

use App\Http\Controllers\Controller;
use App\ProfessionTag;
use Auth;
use Redirect;
use Request;
use Input;

class ProfessionTagController extends Controller {

    public function __construct() 
    {
        $this->professionTag = new ProfessionTag();
    }

    public function index($slug){
        $userid = Auth::guard('teenager')->user()->id;
        $professionsTagData = $this->professionTag->getProfessionTagBySlug($slug);
        return view('teenager.careerTag', compact('professionsTagData','slug'));
    }

    public function getIndex(){
        $userid = Auth::guard('teenager')->user()->id;
        $slug = Input::get('slug');
        $professionsTagData = $this->professionTag->getProfessionTagBySlugWithProfessionAndAttemptedProfession($slug,$userid);
        return view('teenager.basic.level3Tag', compact('professionsTagData'));
    }
}