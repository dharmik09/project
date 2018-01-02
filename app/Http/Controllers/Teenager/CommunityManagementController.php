<?php

namespace App\Http\Controllers\Teenager;

use App\Http\Controllers\Controller;
use Auth;
use Config;
use Storage;
use Input;
use App\Services\Community\Contracts\CommunityRepository;
use App\Services\Template\Contracts\TemplatesRepository;
use Mail;
use Helpers;

class CommunityManagementController extends Controller {

    public function __construct(TemplatesRepository $templateRepository, CommunityRepository $communityRepository) 
    {
        $this->templateRepository = $templateRepository;
        $this->communityRepository = $communityRepository;
    }

    public function index()
    {
        $loggedInTeen = Auth::guard('teenager')->user()->id;
        $newConnections = $this->communityRepository->getNewConnections($loggedInTeen);
        $myConnections = $this->communityRepository->getMyConnections($loggedInTeen);
        return view('teenager.community', compact('newConnections', 'myConnections'));
    }

}
