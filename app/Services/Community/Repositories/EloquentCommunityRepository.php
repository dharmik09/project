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
        $connectionExist = $this->checkForTeenHasAnyConnection($loggedInTeen);
        $connectionArr = $connectionExist->toArray();
        $activeFlag = Config::get('constant.ACTIVE_FLAG');
        if (isset($connectionArr) && !empty($connectionArr)) {
            $acceptedStatus = $this->getAcceptedConnectionsBySenderId($loggedInTeen);
            $newConnections = $this->model
                                ->join(config::get('databaseconstants.TBL_TEENAGERS') . " AS teenager", 'pro_tc_teen_connections.tc_sender_id', '=', 'teenager.id')
                                ->select('pro_tc_teen_connections.*', 'teenager.t_name', 'teenager.t_photo', 'teenager.t_coins')
                                ->where('pro_tc_teen_connections.tc_sender_id', $loggedInTeen)
                                ->whereNotIn('teenager.id', $acceptedStatus->toArray())
                                ->where('pro_tc_teen_connections.deleted', $activeFlag)
                                ->where('teenager.id', '<>', $loggedInTeen)
                                ->where('teenager.deleted', $activeFlag)
                                ->orderBy('teenager.created_at', 'desc')
                                ->limit(10)
                                ->get();
        } else {
            $newConnections = DB::table(Config::get('databaseconstants.TBL_TEENAGERS'))
                                ->where('id', '<>', $loggedInTeen)
                                ->where('deleted', $activeFlag)
                                ->orderBy('created_at', 'desc')
                                ->limit(10)
                                ->get();
        }
        return $newConnections;
    }

    public function getAcceptedConnectionsBySenderId($senderId)
    {
        $receiverId = $this->model->select('tc_receiver_id')->where('tc_sender_id', $senderId)->where('deleted', Config::get('constant.ACTIVE_FLAG'))->where('tc_status', '<>', Config::get('constant.CONNECTION_REJECT_STATUS'))->get();
        return $receiverId;
    }

    public function checkForTeenHasAnyConnection($senderId)
    {
        $connectionFlag = $this->model->where('tc_sender_id', $senderId)->where('deleted', Config::get('constant.ACTIVE_FLAG'))->get();
        return $connectionFlag;
    }
}
