<?php

namespace App\Services\Community\Repositories;

use DB;
use Config;
use App\Community;
use App\Services\Community\Contracts\CommunityRepository;
use App\Services\Repositories\Eloquent\EloquentBaseRepository;

class EloquentCommunityRepository extends EloquentBaseRepository implements CommunityRepository {

    /**
     * @return array of all the new connections
       Parameters
       @$searchParamArray : Array of Searching and Sorting parameters
     */
    public function getNewConnections($loggedInTeen)
    {
        $activeFlag = Config::get('constant.ACTIVE_FLAG');
        $connectionRequests = $this->getAcceptedAndPendingConnectionsBySenderId($loggedInTeen);
        $newConnections = DB::table(Config::get('databaseconstants.TBL_TEENAGERS'))
                                ->whereNotIn('id', $connectionRequests->toArray())
                                ->where('id', '<>', $loggedInTeen)
                                ->where('deleted', $activeFlag)
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

    public function getMyConnections($loggedInTeen)
    {
        $connectedTeenIds = $this->getAcceptedConnectionsBySenderId($loggedInTeen);
        $myConnections = DB::table(Config::get('databaseconstants.TBL_TEENAGERS'))
                                ->whereIn('id', $connectedTeenIds->toArray())
                                ->where('id', '<>', $loggedInTeen)
                                ->where('deleted', Config::get('constant.ACTIVE_FLAG'))
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
}
