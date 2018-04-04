<?php

namespace App;
use DB;
use Config;
use Illuminate\Database\Eloquent\Model;

class Community extends Model
{
    protected $table = 'pro_tc_teen_connections';
    protected $guarded = [];

    /* @Name : checkTeenConnectionStatusForNetworkMemberPage
     *  @Params : Teenager Reciver Id, Sender Id
     *  @return : count [0 : 'pending', 1 : 'Accepted', 2 : 'Rejected', 3 : 'Request Available' ]
     *            connectionDetails Array
     *  @default : Default passing 2 use as a response to show not connected
    */
    public function checkTeenConnectionStatus($receiverId, $senderId)
    {
        $flag = [];
        $flag['count'] = 2;
        $availableRequest = $this->where('tc_receiver_id', $receiverId)->where('tc_sender_id', $senderId)->first();
        if ($availableRequest && !empty($availableRequest)) {
            $flag['count'] = $availableRequest->tc_status;
        }
        else{
            $availableRequestReverse = $this->where('tc_receiver_id', $senderId)->where('tc_sender_id', $receiverId)->first();
            if ($availableRequestReverse && !empty($availableRequestReverse)) {
                $flag['count'] = 3;
                $flag['connectionDetails'] = $availableRequestReverse;
            }
        }
        return $flag;
    }
}
