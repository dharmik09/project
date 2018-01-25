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
use App\Services\Level1Activity\Contracts\Level1ActivitiesRepository;
use Redirect;
use Request;
use App\Services\Schools\Contracts\SchoolsRepository;  
use Carbon\Carbon;  

class CommunityManagementController extends Controller {

    public function __construct(Level1ActivitiesRepository $level1ActivitiesRepository, TemplatesRepository $templateRepository, CommunityRepository $communityRepository, TeenagersRepository $teenagersRepository, SchoolsRepository $schoolsRepository) 
    {
        $this->templateRepository = $templateRepository;
        $this->communityRepository = $communityRepository;
        $this->teenagersRepository = $teenagersRepository;
        $this->schoolsRepository = $schoolsRepository;
        $this->level1ActivitiesRepository = $level1ActivitiesRepository;
    }

    public function index()
    {
        $loggedInTeen = Auth::guard('teenager')->user()->id;
        $searchConnections = Input::get('searchConnections');
        $filterBy = Input::get('filter_by');
        $filterOption = Input::get('filter_option');
        if ((isset($searchConnections) && !empty($searchConnections)) || (isset($filterOption) && !empty($filterOption) && isset($filterBy) && !empty($filterBy))) {
            if (isset($filterBy) && !empty($filterBy) && $filterBy == 't_age') {
                $filterBy = 't_birthdate';
                if (strpos($filterOption, '-') !== false) {
                    $ageArr = explode("-", $filterOption);
                    $toDate = Carbon::now()->subYears($ageArr[0]);
                    $fromDate = Carbon::now()->subYears($ageArr[1]);
                    $filterOptionArr['fromDate'] = $fromDate->format('Y-m-d');
                    $filterOptionArr['toDate'] = $toDate->format('Y-m-d');
                    $filterOption = $filterOptionArr;
                } 
            }
            $newConnections = $this->communityRepository->getNewConnections($loggedInTeen, $searchConnections, '', $filterBy, $filterOption);
            $newConnectionsCount = $this->communityRepository->getNewConnectionsCount($loggedInTeen, $searchConnections, '', $filterBy, $filterOption);
            $myConnections = $this->communityRepository->getMyConnections($loggedInTeen, $searchConnections, '', $filterBy, $filterOption);
            $myConnectionsCount = $this->communityRepository->getMyConnectionsCount($loggedInTeen, $searchConnections, '', $filterBy, $filterOption);
            return view('teenager.searchedConnections', compact('newConnections', 'myConnections', 'newConnectionsCount', 'myConnectionsCount'));

        } else {
            $newConnections = $this->communityRepository->getNewConnections($loggedInTeen, array(), '', $filterBy, $filterOption);
            $newConnectionsCount = $this->communityRepository->getNewConnectionsCount($loggedInTeen, array(), '', $filterBy, $filterOption);
            $myConnections = $this->communityRepository->getMyConnections($loggedInTeen, array(), '', $filterBy, $filterOption);
            $myConnectionsCount = $this->communityRepository->getMyConnectionsCount($loggedInTeen, array(), '', $filterBy, $filterOption);
            return view('teenager.community', compact('newConnections', 'myConnections', 'newConnectionsCount', 'myConnectionsCount'));
        }
    }

    public function getMemberDetails($uniqueId)
    {
        $teenDetails = $this->teenagersRepository->getTeenagerByUniqueId($uniqueId);
        if (isset($teenDetails) && !empty($teenDetails)) {
            $teenagerTrait = $traitAllQuestion = $this->level1ActivitiesRepository->getTeenagerTraitAnswerCount($teenDetails->id);
            $connectedTeen = $this->communityRepository->checkTeenAlreadyConnected($teenDetails->id, Auth::guard('teenager')->user()->id);
            $myConnections = $this->communityRepository->getMyConnections($teenDetails->id);
            $teenagerAPIData = Helpers::getTeenInterestAndStregnthDetails($teenDetails->id);
            $teenagerAPIMaxScore = Helpers::getTeenInterestAndStregnthMaxScore();
            $teenagerInterestArr = isset($teenagerAPIData['APIscore']['interest']) ? $teenagerAPIData['APIscore']['interest'] : [];
            $teenagerInterest = [];
            foreach($teenagerInterestArr as $interestKey => $interestVal){
                if ($interestVal < 1) { continue; } else {
                    $itName = Helpers::getInterestBySlug($interestKey);
                    $teenItScore = $this->getTeenScoreInPercentage($teenagerAPIMaxScore['interest'][$interestKey], $interestVal);
                    $teenagerInterest[$interestKey] = (array('score' => $teenItScore, 'name' => $itName));
                }
            }

            $teenagerMI = isset($teenagerAPIData['APIscore']['MI']) ? $teenagerAPIData['APIscore']['MI'] : [];

            foreach($teenagerMI as $miKey => $miVal) {
                $mitName = Helpers::getMIBySlug($miKey);
                $teenMIScore = $this->getTeenScoreInPercentage($teenagerAPIMaxScore['MI'][$miKey], $miVal);
                    $teenagerMI[$miKey] = (array('score' => $teenMIScore, 'name' => $mitName, 'type' => Config::get('constant.MULTI_INTELLIGENCE_TYPE')));
            }

            $teenagerAptitude = isset($teenagerAPIData['APIscore']['aptitude']) ? $teenagerAPIData['APIscore']['aptitude'] : [];
            foreach($teenagerAptitude as $apptitudeKey => $apptitudeVal) {
                $aptName = Helpers::getApptitudeBySlug($apptitudeKey);
                $teenAptScore = $this->getTeenScoreInPercentage($teenagerAPIMaxScore['aptitude'][$apptitudeKey], $apptitudeVal);
                $teenagerAptitude[$apptitudeKey] = (array('score' => $teenAptScore, 'name' => $aptName, 'type' => Config::get('constant.APPTITUDE_TYPE')));
            }
            $teenagerPersonality = isset($teenagerAPIData['APIscore']['personality']) ? $teenagerAPIData['APIscore']['personality'] : [];
            foreach($teenagerPersonality as $personalityKey => $personalityVal) {
                $ptName = Helpers::getPersonalityBySlug($personalityKey);
                $teenPtScore = $this->getTeenScoreInPercentage($teenagerAPIMaxScore['personality'][$personalityKey], $personalityVal);
                $teenagerPersonality[$personalityKey] = (array('score' => $teenPtScore, 'name' => $ptName, 'type' => Config::get('constant.PERSONALITY_TYPE')));
            }
            $teenagerStrength = array_merge($teenagerAptitude, $teenagerPersonality, $teenagerMI);
            $myConnectionCount = $this->communityRepository->getMyConnectionsCount($teenDetails->id);
            
            return view('teenager.networkMember', compact('teenagerTrait', 'teenDetails', 'myConnections', 'teenagerStrength', 'teenagerInterest', 'connectedTeen', 'myConnectionCount'));
        } else {
            return Redirect::back()->with('error', 'Member not found');
        }
    }

    /**
     * Returns More community connections
     *
     * @return \Illuminate\Http\Response
     */
    public function loadMoreNewConnections()
    {
        $lastTeenId = Input::get('lastTeenId');
        $loggedInTeen = Auth::guard('teenager')->user()->id;
        $searchConnections = Input::get('searchConnections');
        $filterBy = Input::get('filter_by');
        $filterOption = Input::get('filter_option');
        if (isset($filterBy) && !empty($filterBy) && $filterBy == 't_age') {
            $filterBy = 't_birthdate';
            if (strpos($filterOption, '-') !== false) {
                $ageArr = explode("-", $filterOption);
                $toDate = Carbon::now()->subYears($ageArr[0]);
                $fromDate = Carbon::now()->subYears($ageArr[1]);
                $filterOptionArr['fromDate'] = $fromDate->format('Y-m-d');
                $filterOptionArr['toDate'] = $toDate->format('Y-m-d');
                $filterOption = $filterOptionArr;
            } 
        }
        $newConnections = $this->communityRepository->getNewConnections($loggedInTeen, $searchConnections, $lastTeenId, $filterBy, $filterOption);
        $newConnectionsCount = $this->communityRepository->getNewConnectionsCount($loggedInTeen, $searchConnections, $lastTeenId, $filterBy, $filterOption);
        return view('teenager.loadMoreNewConnections', compact('newConnections', 'newConnectionsCount'));
    }

    public function loadMoreMyConnections()
    {
        $lastTeenId = Input::get('lastTeenId');
        $loggedInTeen = Auth::guard('teenager')->user()->id;
        $searchConnections = Input::get('searchConnections');
        $filterBy = Input::get('filter_by');
        $filterOption = Input::get('filter_option');
        if (isset($filterBy) && !empty($filterBy) && $filterBy == 't_age') {
            $filterBy = 't_birthdate';
            if (strpos($filterOption, '-') !== false) {
                $ageArr = explode("-", $filterOption);
                $toDate = Carbon::now()->subYears($ageArr[0]);
                $fromDate = Carbon::now()->subYears($ageArr[1]);
                $filterOptionArr['fromDate'] = $fromDate->format('Y-m-d');
                $filterOptionArr['toDate'] = $toDate->format('Y-m-d');
                $filterOption = $filterOptionArr;
            } 
        }
        $myConnections = $this->communityRepository->getMyConnections($loggedInTeen, $searchConnections, $lastTeenId, $filterBy, $filterOption);
        $myConnectionsCount = $this->communityRepository->getMyConnectionsCount($loggedInTeen, $searchConnections, $lastTeenId, $filterBy, $filterOption);
        return view('teenager.loadMoreMyConnections', compact('myConnections', 'myConnectionsCount'));
    }

    public function sendRequest($uniqueId)
    {
        $receiverTeenDetails = $this->teenagersRepository->getTeenagerByUniqueId($uniqueId);
        $senderTeenDetails = $this->teenagersRepository->getTeenagerDetailById(Auth::guard('teenager')->user()->id);
        $connectedTeen = $this->communityRepository->checkTeenAlreadyConnected($receiverTeenDetails->id, Auth::guard('teenager')->user()->id);
        if (!empty($connectedTeen) && $connectedTeen == true) {
            $data = array();
            $emailTemplateContent = $this->templateRepository->getEmailTemplateDataByName('send-connection-request-to-teen');
            $replaceArray = array();
            $connectionUniqueId = uniqid("", TRUE);
            $replaceArray['RECEIVER_TEEN_NAME'] = $receiverTeenDetails->t_name;
            $replaceArray['SENDER_TEEN_NAME'] = $senderTeenDetails->t_name;
            //$replaceArray['ACCEPT_URL'] = url('teenager/accept-connection-request?token='.$connectionUniqueId);
            //$replaceArray['REJECT_URL'] = url("teenager/reject-connection-request?token=".$connectionUniqueId);
            $content = $this->templateRepository->getEmailContent($emailTemplateContent->et_body, $replaceArray);
            if (isset($emailTemplateContent) && !empty($emailTemplateContent)) {
                $data['subject'] = $emailTemplateContent->et_subject;
                $data['toEmail'] = $receiverTeenDetails->t_email;
                $data['toName'] = (isset($receiverTeenDetails->t_name) && !empty($receiverTeenDetails->t_name)) ? $receiverTeenDetails->t_name : "";
                $data['content'] = $content;
                $data['tc_unique_id'] = $connectionUniqueId;
                $data['tc_sender_id'] = Auth::guard('teenager')->user()->id;
                $data['tc_receiver_id'] = $receiverTeenDetails->id;
                Mail::send(['html' => 'emails.Template'], $data, function($message) use ($data) {
                            $message->subject($data['subject']);
                            $message->to($data['toEmail'], $data['toName']);
                            // Save connection request data
                            $connectionRequestData['tc_unique_id'] = $data['tc_unique_id'];
                            $connectionRequestData['tc_sender_id'] = $data['tc_sender_id'];
                            $connectionRequestData['tc_receiver_id'] = $data['tc_receiver_id'];
                            $connectionRequestData['tc_status'] = Config::get('constant.CONNECTION_PENDING_STATUS');

                            $this->communityRepository->saveConnectionRequest($connectionRequestData, '');
                                });
                        // ------------------------end sending mail ----------------------------//
                return Redirect::back()->with('success', 'Connection request sent successfully');
            } else {
                return Redirect::back()->with('error', trans('validation.somethingwrong'));
            }
        } else {
            return Redirect::back()->with('error', 'Request already sent');
        }
    }

    public function getSubFilter()
    {
        $filterData = array();
        $filterOption = Input::get('filter_option');
        if ($filterOption == 't_school') {
            $filterData = $this->schoolsRepository->getApprovedSchools();
        } else if ($filterOption == 't_gender') {
            $filterData = Helpers::gender();
        } else if ($filterOption == 't_age') {
            $filterData = Helpers::age();
        } else {
            $filterData = array();
        }
        return view('teenager.communitySubFilter', compact('filterData', 'filterOption'));
    }

    //Calculate teenager strength and interest score percentage
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
