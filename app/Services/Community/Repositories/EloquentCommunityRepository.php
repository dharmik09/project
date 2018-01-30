<?php

namespace App\Services\Community\Repositories;

use DB;
use Config;
use App\Community;
use App\Services\Community\Contracts\CommunityRepository;
use App\Services\Repositories\Eloquent\EloquentBaseRepository;
use Carbon\Carbon;

class EloquentCommunityRepository extends EloquentBaseRepository implements CommunityRepository {

    /**
     * @return array of all the new connections
       Parameters
       @$searchParamArray : Array of Searching and Sorting parameters
     */
    public function getNewConnections($loggedInTeen, $searchedConnections = array(), $lastTeenId = '', $filterBy = '', $filterOption = '')
    {
        $activeFlag = Config::get('constant.ACTIVE_FLAG');
        $connectionRequests = $this->getAcceptedAndPendingConnectionsBySenderId($loggedInTeen);
        $newConnections = DB::table(Config::get('databaseconstants.TBL_TEENAGERS'))
                                ->whereNotIn('id', $connectionRequests)
                                ->where('id', '<>', $loggedInTeen)
                                ->where('deleted', $activeFlag)
                                ->where('is_search_on', Config('constant.TEENAGER_PUBLIC_PROFILE_ON'))
                                ->where(function($query) use ($searchedConnections)  {
                                    if(isset($searchedConnections) && !empty($searchedConnections)) {
                                        $query->where('t_name', 'like', '%'.$searchedConnections.'%');
                                        $query->orWhere('t_email', 'like', '%'.$searchedConnections.'%');
                                    }
                                 })
                                ->where(function($qry) use ($lastTeenId)  {
                                    if(isset($lastTeenId) && !empty($lastTeenId)) {
                                        $qry->where('id', '<', $lastTeenId);
                                    }
                                 })
                                ->where(function($qryFilter) use ($filterBy, $filterOption)  {
                                    if(isset($filterBy) && !empty($filterBy) && isset($filterOption) && !empty($filterOption)) {
                                        if ($filterBy != 't_birthdate' && $filterBy != 't_pincode') {
                                            $qryFilter->where($filterBy, $filterOption);
                                        } else if ($filterBy == 't_pincode') {
                                            $qryFilter->where('t_pincode', 'like', '%'.$filterOption.'%');
                                        } else {
                                            if (is_array($filterOption)) {
                                                $qryFilter->whereBetween($filterBy, [$filterOption['fromDate'], $filterOption['toDate']]);
                                            } else if($filterOption == 13) {
                                                $filterDate = Carbon::now()->subYears($filterOption);
                                                $qryFilter->where($filterBy, '>=', $filterDate->format('Y-m-d'));
                                            } else {
                                                $filterDate = Carbon::now()->subYears($filterOption);
                                                $qryFilter->where($filterBy, '<=', $filterDate->format('Y-m-d'));
                                            }
                                        }
                                    }
                                 })
                                ->orderBy('id', 'desc')
                                ->limit(10)
                                ->get();
        return $newConnections;
    }

    public function getAcceptedAndPendingConnectionsBySenderId($senderId)
    {
        $connectedIds = [];
        $receiverIdArr = $this->model->select('tc_receiver_id')->where('tc_sender_id', $senderId)->where('tc_status', '<>', Config::get('constant.CONNECTION_REJECT_STATUS'))->get();
        $accptedIdArr = $this->model->select('tc_sender_id')->where('tc_receiver_id', $senderId)->where('tc_status', '<>', Config::get('constant.CONNECTION_REJECT_STATUS'))->get();
        foreach ($receiverIdArr as $receiverId) {
            $connectedIds[] =  $receiverId->tc_receiver_id;
        }
        foreach ($accptedIdArr as $accptedId) {
            $connectedIds[] = $accptedId->tc_sender_id;
        }
        return $connectedIds;
    }

    public function getMyConnections($loggedInTeen, $searchedConnections = array(), $lastTeenId = '', $filterBy = '', $filterOption = '', $listConnections = '')
    {
        $connectedTeenIds = $this->getAcceptedConnectionsBySenderId($loggedInTeen);
        $query = DB::table(Config::get('databaseconstants.TBL_TEENAGERS'))
                                ->whereIn('id', $connectedTeenIds)
                                ->where('id', '<>', $loggedInTeen)
                                ->where('deleted', Config::get('constant.ACTIVE_FLAG'))
                                ->where(function($query) use ($searchedConnections)  {
                                    if(isset($searchedConnections) && !empty($searchedConnections)) {
                                        $query->where('t_name', 'like', '%'.$searchedConnections.'%');
                                        $query->orWhere('t_email', 'like', '%'.$searchedConnections.'%');
                                    }
                                })
                                ->where(function($qry) use ($lastTeenId)  {
                                    if(isset($lastTeenId) && !empty($lastTeenId)) {
                                        $qry->where('id', '<', $lastTeenId);
                                    }
                                 })
                                ->where(function($qryFilter) use ($filterBy, $filterOption)  {
                                    if(isset($filterBy) && !empty($filterBy) && isset($filterOption) && !empty($filterOption)) {
                                        if ($filterBy != 't_birthdate' && $filterBy != 't_pincode') {
                                            $qryFilter->where($filterBy, $filterOption);
                                        } else if ($filterBy == 't_pincode') {
                                            $qryFilter->where($filterBy, 'like', '%'.$filterOption.'%');
                                        } else {
                                            if (is_array($filterOption)) {
                                                $qryFilter->whereBetween($filterBy, [$filterOption['fromDate'], $filterOption['toDate']]);
                                            } else if($filterOption == 13) {
                                                $filterDate = Carbon::now()->subYears($filterOption);
                                                $qryFilter->where($filterBy, '>=', $filterDate->format('Y-m-d'));
                                            } else {
                                                $filterDate = Carbon::now()->subYears($filterOption);
                                                $qryFilter->where($filterBy, '<=', $filterDate->format('Y-m-d'));
                                            }
                                        }
                                    }
                                })
                                ->orderBy('id', 'desc');
            if ($listConnections == 1 && isset($listConnections)) {
                $myConnections = $query->get();
            } else {
                $myConnections = $query->limit(10)->get();
            }
        return $myConnections;
    }

    public function getAcceptedConnectionsBySenderId($senderId)
    {
        $acceptedConnections = [];
        $receiverIdArr = $this->model->select('tc_receiver_id')->where('tc_sender_id', $senderId)->where('tc_status', Config::get('constant.CONNECTION_ACCEPT_STATUS'))->get();
        $accptedIdArr = $this->model->select('tc_sender_id')->where('tc_receiver_id', $senderId)->where('tc_status', Config::get('constant.CONNECTION_ACCEPT_STATUS'))->get();
        foreach ($receiverIdArr as $receiverId) {
            $acceptedConnections[] =  $receiverId->tc_receiver_id;
        }
        foreach ($accptedIdArr as $accptedId) {
            $acceptedConnections[] = $accptedId->tc_sender_id;
        }
        return $acceptedConnections;
    }

    public function getNewConnectionsCount($loggedInTeen, $searchedConnections = array(), $lastTeenId = '', $filterBy = '', $filterOption = '')
    {
        $activeFlag = Config::get('constant.ACTIVE_FLAG');
        $connectionRequests = $this->getAcceptedAndPendingConnectionsBySenderId($loggedInTeen);
        $newConnectionsCount = DB::table(Config::get('databaseconstants.TBL_TEENAGERS'))
                                ->whereNotIn('id', $connectionRequests)
                                ->where('id', '<>', $loggedInTeen)
                                ->where('deleted', $activeFlag)
                                ->where(function($query) use ($searchedConnections)  {
                                    if(isset($searchedConnections) && !empty($searchedConnections)) {
                                        $query->where('t_name', 'like', '%'.$searchedConnections.'%');
                                        $query->orWhere('t_email', 'like', '%'.$searchedConnections.'%');
                                    }
                                 })
                                ->where(function($qry) use ($lastTeenId)  {
                                    if(isset($lastTeenId) && !empty($lastTeenId)) {
                                        $qry->where('id', '<', $lastTeenId);
                                    }
                                 })
                                ->where(function($qryFilter) use ($filterBy, $filterOption)  {
                                    if(isset($filterBy) && !empty($filterBy) && isset($filterOption) && !empty($filterOption)) {
                                        if ($filterBy != 't_birthdate' && $filterBy != 't_pincode') {
                                            $qryFilter->where($filterBy, $filterOption);
                                        } else if ($filterBy == 't_pincode') {
                                            $qryFilter->where($filterBy, 'like', '%'.$filterOption.'%');
                                        } else {
                                            if (is_array($filterOption)) {
                                                $qryFilter->whereBetween($filterBy, [$filterOption['fromDate'], $filterOption['toDate']]);
                                            } else if($filterOption == 13) {
                                                $filterDate = Carbon::now()->subYears($filterOption);
                                                $qryFilter->where($filterBy, '>=', $filterDate->format('Y-m-d'));
                                            } else {
                                                $filterDate = Carbon::now()->subYears($filterOption);
                                                $qryFilter->where($filterBy, '<=', $filterDate->format('Y-m-d'));
                                            }
                                        }
                                    }
                                 })
                                ->orderBy('created_at', 'desc')
                                ->count();
        return $newConnectionsCount;
    }

    public function getMyConnectionsCount($loggedInTeen, $searchedConnections = array(), $lastTeenId = '', $filterBy = '', $filterOption = '')
    {
        $connectedTeenIds = $this->getAcceptedConnectionsBySenderId($loggedInTeen);
        $myConnectionsCount = DB::table(Config::get('databaseconstants.TBL_TEENAGERS'))
                                ->whereIn('id', $connectedTeenIds)
                                ->where('id', '<>', $loggedInTeen)
                                ->where('deleted', Config::get('constant.ACTIVE_FLAG'))
                                ->where(function($query) use ($searchedConnections)  {
                                    if(isset($searchedConnections) && !empty($searchedConnections)) {
                                        $query->where('t_name', 'like', '%'.$searchedConnections.'%');
                                        $query->orWhere('t_email', 'like', '%'.$searchedConnections.'%');
                                    }
                                 })
                                ->where(function($qry) use ($lastTeenId)  {
                                    if(isset($lastTeenId) && !empty($lastTeenId)) {
                                        $qry->where('id', '<', $lastTeenId);
                                    }
                                 })
                                ->where(function($qryFilter) use ($filterBy, $filterOption)  {
                                    if(isset($filterBy) && !empty($filterBy) && isset($filterOption) && !empty($filterOption)) {
                                        if ($filterBy != 't_birthdate' && $filterBy != 't_pincode') {
                                            $qryFilter->where($filterBy, $filterOption);
                                        } else if ($filterBy == 't_pincode') {
                                            $qryFilter->where($filterBy, 'like', '%'.$filterOption.'%');
                                        } else {
                                            if (is_array($filterOption)) {
                                                $qryFilter->whereBetween($filterBy, [$filterOption['fromDate'], $filterOption['toDate']]);
                                            } else if($filterOption == 13) {
                                                $filterDate = Carbon::now()->subYears($filterOption);
                                                $qryFilter->where($filterBy, '>=', $filterDate->format('Y-m-d'));
                                            } else {
                                                $filterDate = Carbon::now()->subYears($filterOption);
                                                $qryFilter->where($filterBy, '<=', $filterDate->format('Y-m-d'));
                                            }
                                        }
                                    }
                                 })
                                ->orderBy('created_at', 'desc')
                                ->count();
        return $myConnectionsCount;
    }

    /**
     * Save connection request data
     */    
    public function saveConnectionRequest($connectionRequestData, $token)
    {
        if(isset($token) && $token != '' && !empty($token)) {
            $return = $this->model->where('tc_unique_id', $token)->update($connectionRequestData);
        } else {
            $availableRequest = $this->model->where('tc_receiver_id', $connectionRequestData['tc_receiver_id'])->where('tc_sender_id', $connectionRequestData['tc_sender_id'])->first();
            if (isset($availableRequest) && !empty($availableRequest)) {
                $connectionRequestDetails['tc_status'] = Config::get('constant.CONNECTION_PENDING_STATUS');
                $return = $this->model->where('id', $availableRequest->id)->update($connectionRequestDetails);
            } else {
                $return = $this->model->create($connectionRequestData);
            }
        }
        return $return;
    }

    public function checkTeenAlreadyConnected($receiverId, $senderId)
    {
        $flag = '';
        $availableRequest = $this->model->where('tc_receiver_id', $receiverId)->where('tc_sender_id', $senderId)->first();
        if (isset($availableRequest) && !empty($availableRequest)) {
            $checkRequestStatus = $this->model->select('tc_status')->where('tc_receiver_id', $receiverId)->where('tc_sender_id', $senderId)->first();
            if(in_array($checkRequestStatus->tc_status, array(Config::get('constant.CONNECTION_PENDING_STATUS'), Config::get('constant.CONNECTION_ACCEPT_STATUS')))) {
                $flag = false;
            } else {
                $flag = true;
            }
        } else {
            $flag = true;
        }
        return $flag;
    }

    /* @Name : checkTeenConnectionStatus
    *  @Params : Teenager Reciver Id, Sender Id
    *  @return : requestStatus [0 : 'pending', 1 : 'Accepted', 2 : 'Rejected' ]
    *  @default : Default passing 2 use as a response to show not connected
    */
    public function checkTeenConnectionStatus($receiverId, $senderId)
    {
        $flag = 2;
        $availableRequest = $this->model->where('tc_receiver_id', $receiverId)->where('tc_sender_id', $senderId)->first();
        if ($availableRequest && !empty($availableRequest)) {
            $flag = $availableRequest->tc_status;
        }
        return $flag;
    }

    /* @Name : checkTeenConnectionStatus
    *  @Params : Teenager uniqueId
    *  @return : requestStatus [0 : 'pending', 1 : 'Accepted', 2 : 'Rejected' ]
    *  @default : Default passing 2 use as a response to show not connected
    */
    public function checkTeenConnectionStatusById($id)
    {
        $flag = 2;
        $availableRequest = $this->model->where('id', $id)->first();
        if ($availableRequest && !empty($availableRequest)) {
            $flag = $availableRequest->tc_status;
        }
        return $flag;
    }

    /* @Name : checkTeenConnectionStatus
    *  @Params : Teenager uniqueId
    *  @default : Default passing 2 use as a response to show not connected
    */
    public function changeTeenConnectionStatusById($id,$status)
    {
        $response = $this->model->where('id',$id)->update(['tc_status' => $status]);
        return $response;
    }
}
