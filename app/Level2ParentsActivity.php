<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Config;
use DB;

class Level2ParentsActivity extends Model {

   protected $table = 'pro_l2pac_level2_parent_activities';
   protected $guarded = [];

   public function saveLevel2ParentsActivity($saveParentLevel2Data)
   {
       $result = Level2ParentsActivity::select('*')
                        ->where('deleted', '1')
                        ->where('l2pac_parent_id', $saveParentLevel2Data['l2pac_parent_id'])
                        ->where('l2pac_teenager_id', $saveParentLevel2Data['l2pac_teenager_id'])
                        ->where('l2pac_type', $saveParentLevel2Data['l2pac_type'])
                        ->where('l2pac_rate_id', $saveParentLevel2Data['l2pac_rate_id'])
                        ->get();

        if (isset($result) && !empty($result) && count($result) > 0) {
            $return = $this->where('id', $result[0]['id'])->update($saveParentLevel2Data);
            $return = $this->where('id', $result[0]['id'])->first();
        } else {
            $return = $this->create($saveParentLevel2Data);
            $lastinsertId =  DB::getPdo()->lastInsertId();
            $return = $this->where('id', $lastinsertId)->first();
        }
        return $return;
   }

   public function getLevel2ParentsActivity($rateId,$parent_Id,$teenid,$type) {
        $result = Level2ParentsActivity::select('*')
                        ->where('deleted', '1')
                        ->where('l2pac_parent_id', $parent_Id)
                        ->where('l2pac_teenager_id', $teenid)
                        ->where('l2pac_type', $type)
                        ->where('l2pac_rate_id', $rateId)
                        ->get();
        return $result;
   }

   public function getTeenPromiseRateCount($teenid,$parent_Id) {
        $result = Level2ParentsActivity::select('*')
                        ->where('deleted', '1')
                        ->where('l2pac_parent_id', $parent_Id)
                        ->where('l2pac_teenager_id', $teenid)
                        ->get();
        return $result;
   }
}
