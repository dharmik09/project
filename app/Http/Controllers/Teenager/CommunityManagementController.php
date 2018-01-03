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
use App\Services\Teenagers\Contracts\TeenagersRepository;

class CommunityManagementController extends Controller {

    public function __construct(TemplatesRepository $templateRepository, CommunityRepository $communityRepository, TeenagersRepository $teenagersRepository) 
    {
        $this->templateRepository = $templateRepository;
        $this->communityRepository = $communityRepository;
        $this->teenagersRepository = $teenagersRepository;
    }

    public function index()
    {
        $loggedInTeen = Auth::guard('teenager')->user()->id;
        $searchConnections = Input::get('searchConnections');
        if (isset($searchConnections) && !empty($searchConnections)) {
            $newConnections = $this->communityRepository->getNewConnections($loggedInTeen, $searchConnections);
            $myConnections = $this->communityRepository->getMyConnections($loggedInTeen, $searchConnections);
            return view('teenager.searchedConnections', compact('newConnections', 'myConnections'));

        } else {
            $newConnections = $this->communityRepository->getNewConnections($loggedInTeen, array());
            $myConnections = $this->communityRepository->getMyConnections($loggedInTeen, array());
            return view('teenager.community', compact('newConnections', 'myConnections'));
        }
    }

    public function getMemberDetails($teenId)
    {
        $teenDetails = $this->teenagersRepository->getTeenagerDetailById($teenId);
        $myConnections = $this->communityRepository->getMyConnections($teenId);
        $teenagerAPIData = Helpers::getTeenInterestAndStregnthDetails($teenId);
        $teenagerInterest = isset($teenagerAPIData['APIscore']['interest']) ? $teenagerAPIData['APIscore']['interest'] : [];
        $teenagerMI = isset($teenagerAPIData['APIscale']['MI']) ? $teenagerAPIData['APIscale']['MI'] : [];
        foreach($teenagerMI as $miKey => $miVal) {
            $mitName = Helpers::getMIBySlug($miKey);
            $teenagerMI[$miKey] = (array('score' => $miVal, 'name' => $mitName, 'type' => Config::get('constant.MULTI_INTELLIGENCE_TYPE')));
        }

        $teenagerAptitude = isset($teenagerAPIData['APIscale']['aptitude']) ? $teenagerAPIData['APIscale']['aptitude'] : [];
        foreach($teenagerAptitude as $apptitudeKey => $apptitudeVal) {
            $aptName = Helpers::getApptitudeBySlug($apptitudeKey);
            $teenagerAptitude[$apptitudeKey] = (array('score' => $apptitudeVal, 'name' => $aptName, 'type' => Config::get('constant.APPTITUDE_TYPE')));
        }
        $teenagerPersonality = isset($teenagerAPIData['APIscale']['personality']) ? $teenagerAPIData['APIscale']['personality'] : [];
        foreach($teenagerPersonality as $personalityKey => $personalityVal) {
            $ptName = Helpers::getPersonalityBySlug($personalityKey);
            $teenagerPersonality[$personalityKey] = (array('score' => $personalityVal, 'name' => $ptName, 'type' => Config::get('constant.PERSONALITY_TYPE')));
        }
        $teenagerStrength = array_merge($teenagerAptitude, $teenagerPersonality, $teenagerMI);
        return view('teenager.networkMember', compact('teenDetails', 'myConnections', 'teenagerStrength', 'teenagerInterest'));
    }

    public function searchConnections()
    {

    }

}
