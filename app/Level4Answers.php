<?php

namespace App;
use DB;
use Config;
use Illuminate\Database\Eloquent\Model;

class Level4Answers extends Model
{
    protected $table = 'level4_basic_activity_answer';
    protected $guarded = [];

    public function getLevel4BasicDetailById($id, $proId)
    {
         $result = DB::table(config::get('databaseconstants.TBL_LEVEL4_ANSWERS') ." AS answer")
                        ->join(config::get('databaseconstants.TBL_LEVEL4_BASIC_ACTIVITY') . " AS activity", 'activity.id', '=', 'answer.activity_id')
                        ->select(DB::raw('SUM(answer.earned_points) AS earned_points'))
                        ->where('answer.deleted', '=', 1)
                        ->where('answer.teenager_id', '=', $id)
                        ->where('activity.profession_id', '=', $proId)
                        ->get();
        return $result;
    }
}
