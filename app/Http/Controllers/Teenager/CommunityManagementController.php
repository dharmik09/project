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
use Redirect;
use Request;    

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
            $newConnections = $this->communityRepository->getNewConnections($loggedInTeen, $searchConnections, '');
            $newConnectionsCount = $this->communityRepository->getNewConnectionsCount($loggedInTeen, $searchConnections, '');
            $myConnections = $this->communityRepository->getMyConnections($loggedInTeen, $searchConnections, '');
            $myConnectionsCount = $this->communityRepository->getMyConnectionsCount($loggedInTeen, $searchConnections, '');
            return view('teenager.searchedConnections', compact('newConnections', 'myConnections', 'newConnectionsCount', 'myConnectionsCount'));

        } else {
            $newConnections = $this->communityRepository->getNewConnections($loggedInTeen, array(), '');
            $newConnectionsCount = $this->communityRepository->getNewConnectionsCount($loggedInTeen, array(), '');
            $myConnections = $this->communityRepository->getMyConnections($loggedInTeen, array(), '');
            $myConnectionsCount = $this->communityRepository->getMyConnectionsCount($loggedInTeen, array(), '');
            return view('teenager.community', compact('newConnections', 'myConnections', 'newConnectionsCount', 'myConnectionsCount'));
        }
    }

    public function getMemberDetails($uniqueId)
    {
        $teenDetails = $this->teenagersRepository->getTeenagerByUniqueId($uniqueId);
        if (isset($teenDetails) && !empty($teenDetails)) {
            $myConnections = $this->communityRepository->getMyConnections($teenDetails->id, array(), '');
            $teenagerAPIData = Helpers::getTeenInterestAndStregnthDetails($teenDetails->id);
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
        if (isset($searchConnections) && !empty($searchConnections)) {
            $newConnections = $this->communityRepository->getNewConnections($loggedInTeen, $searchConnections, $lastTeenId);
            $newConnectionsCount = $this->communityRepository->getNewConnectionsCount($loggedInTeen, $searchConnections, $lastTeenId);
        } else {
            $newConnections = $this->communityRepository->getNewConnections($loggedInTeen, array(), $lastTeenId);
            $newConnectionsCount = $this->communityRepository->getNewConnectionsCount($loggedInTeen, array(), $lastTeenId);
        }
        return view('teenager.loadMoreNewConnections', compact('newConnections', 'newConnectionsCount'));
    }

    public function loadMoreMyConnections()
    {
        $lastTeenId = Input::get('lastTeenId');
        $loggedInTeen = Auth::guard('teenager')->user()->id;
        $searchConnections = Input::get('searchConnections');
        if (isset($searchConnections) && !empty($searchConnections)) {
            $myConnections = $this->communityRepository->getMyConnections($loggedInTeen, $searchConnections, $lastTeenId);
            $myConnectionsCount = $this->communityRepository->getMyConnectionsCount($loggedInTeen, $searchConnections, $lastTeenId);
        } else {
            $myConnections = $this->communityRepository->getMyConnections($loggedInTeen, array(), $lastTeenId);
            $myConnectionsCount = $this->communityRepository->getMyConnectionsCount($loggedInTeen, array(), $lastTeenId);
        }
        return view('teenager.loadMoreMyConnections', compact('myConnections', 'myConnectionsCount'));
    }

    public function sendRequest($uniqueId)
    {
        $receiverTeenDetails = $this->teenagersRepository->getTeenagerByUniqueId($uniqueId);
        $senderTeenDetails = $this->teenagersRepository->getTeenagerDetailById(Auth::guard('teenager')->user()->id);
        $data = array();
        $emailTemplateContent = $this->templateRepository->getEmailTemplateDataByName('send-connection-request-to-teen');
        $replaceArray = array();
        $connectionUniqueId = uniqid("", TRUE);
        $replaceArray['RECEIVER_TEEN_NAME'] = $receiverTeenDetails->t_name;
        $replaceArray['SENDER_TEEN_NAME'] = $senderTeenDetails->t_name;
        $replaceArray['ACCEPT_URL'] = url('teenager/accept-connection-request?token='.$connectionUniqueId);
        $replaceArray['REJECT_URL'] = url("teenager/reject-connection-request?token=".$connectionUniqueId);
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
                        // Save parent-teen id in verification table
                        $connectionRequestData['tc_unique_id'] = $data['tc_unique_id'];
                        $connectionRequestData['tc_sender_id'] = $data['tc_sender_id'];
                        $connectionRequestData['tc_receiver_id'] = $data['tc_receiver_id'];
                        $connectionRequestData['tc_status'] = Config::get('constant.CONNECTION_PENDING_STATUS');

                        $this->communityRepository->saveConnectionRequest($connectionRequestData);
                            });
                    // ------------------------end sending mail ----------------------------//
            return Redirect::back()->with('success', 'Connection request sent successfully');
        } else {
            return Redirect::back()->with('error', trans('validation.somethingwrong'));
        }
    }
}
