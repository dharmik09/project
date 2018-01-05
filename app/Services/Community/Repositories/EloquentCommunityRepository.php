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
    public function getNewConnections($loggedInTeen, $searchedConnections, $lastTeenId, $filterBy = '', $filterOption = '')
    {
        $activeFlag = Config::get('constant.ACTIVE_FLAG');
        $connectionRequests = $this->getAcceptedAndPendingConnectionsBySenderId($loggedInTeen);
        $newConnections = DB::table(Config::get('databaseconstants.TBL_TEENAGERS'))
                                ->whereNotIn('id', $connectionRequests->toArray())
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
                                        if ($filterBy != 't_birthdate') {
                                            $qryFilter->where($filterBy, $filterOption);
                                        } else {
                                            if (is_array($filterOption)) {
                                                $qryFilter->whereBetween($filterBy, [$filterOption['fromDate'], $filterOption['toDate']]);
                                                //$qryFilter->where($filterBy, '>=', $filterOption['fromDate']);
                                                //$qryFilter->where($filterBy, '<=', $filterOption['toDate']);
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
                                ->limit(10)
                                ->get();
        return $newConnections;
    }

    public function getAcceptedAndPendingConnectionsBySenderId($senderId)
    {
        $receiverId = $this->model->select('tc_receiver_id')->where('tc_sender_id', $senderId)->where('tc_status', '<>', Config::get('constant.CONNECTION_REJECT_STATUS'))->get();
        return $receiverId;
    }

    public function getMyConnections($loggedInTeen, $searchedConnections, $lastTeenId, $filterBy = '', $filterOption = '')
    {
        $connectedTeenIds = $this->getAcceptedConnectionsBySenderId($loggedInTeen);
        $myConnections = DB::table(Config::get('databaseconstants.TBL_TEENAGERS'))
                                ->whereIn('id', $connectedTeenIds->toArray())
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
                                        if ($filterOption != 't_age') {
                                            $qryFilter->where($filterBy, $filterOption);
                                        } else {
                                            if (is_array($filterOption)) {
                                                $qryFilter->where($filterBy, '>=', $filterOption['fromDate']);
                                                $qryFilter->where($filterBy, '<=', $filterOption['toDate']);
                                            } else if($filterOption == 13) {
                                                $qryFilter->where($filterBy, '<=', $filterOption);
                                            } else {
                                                $qryFilter->where($filterBy, '>=', $filterOption);
                                            }
                                        }
                                    }
                                 })
                                ->orderBy('created_at', 'desc')
                                ->limit(10)
                                ->get();
        return $myConnections;
    }

    public function getAcceptedConnectionsBySenderId($senderId)
    {
        $receiverId = $this->model->select('tc_receiver_id')->where('tc_sender_id', $senderId)->where('tc_status', Config::get('constant.CONNECTION_ACCEPT_STATUS'))->get();
        return $receiverId;
    }

    public function getNewConnectionsCount($loggedInTeen, $searchedConnections, $lastTeenId, $filterBy = '', $filterOption = '')
    {
        $activeFlag = Config::get('constant.ACTIVE_FLAG');
        $connectionRequests = $this->getAcceptedAndPendingConnectionsBySenderId($loggedInTeen);
        $newConnectionsCount = DB::table(Config::get('databaseconstants.TBL_TEENAGERS'))
                                ->whereNotIn('id', $connectionRequests->toArray())
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
                                        if ($filterOption != 't_age') {
                                            $qryFilter->where($filterBy, $filterOption);
                                        } else {
                                            if (is_array($filterOption)) {
                                                $qryFilter->where($filterBy, '>=', $filterOption['fromDate']);
                                                $qryFilter->where($filterBy, '<=', $filterOption['toDate']);
                                            } else if($filterOption == 13) {
                                                $qryFilter->where($filterBy, '<=', $filterOption);
                                            } else {
                                                $qryFilter->where($filterBy, '>=', $filterOption);
                                            }
                                        }
                                    }
                                 })
                                ->orderBy('created_at', 'desc')
                                ->count();
        return $newConnectionsCount;
    }

    public function getMyConnectionsCount($loggedInTeen, $searchedConnections, $lastTeenId, $filterBy = '', $filterOption = '')
    {
        $connectedTeenIds = $this->getAcceptedConnectionsBySenderId($loggedInTeen);
        $myConnectionsCount = DB::table(Config::get('databaseconstants.TBL_TEENAGERS'))
                                ->whereIn('id', $connectedTeenIds->toArray())
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
                                        if ($filterOption != 't_age') {
                                            $qryFilter->where($filterBy, $filterOption);
                                        } else {
                                            if (is_array($filterOption)) {
                                                $qryFilter->where($filterBy, '>=', $filterOption['fromDate']);
                                                $qryFilter->where($filterBy, '<=', $filterOption['toDate']);
                                            } else if($filterOption == 13) {
                                                $qryFilter->where($filterBy, '<=', $filterOption);
                                            } else {
                                                $qryFilter->where($filterBy, '>=', $filterOption);
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
            $return = $this->model->create($connectionRequestData);
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
}
