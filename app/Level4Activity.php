<?php

namespace App;
use DB;
use Config;
use Illuminate\Database\Eloquent\Model;

class Level4Activity extends Model
{
    protected $table = 'level4_basic_activity';
    protected $guarded = [];

    public function getActiveLevel4Activity($id)
    {
        $leve4activities = DB::table(config::get('databaseconstants.TBL_LEVEL4_BASIC_ACTIVITY'). " AS activity")
                            ->leftjoin(config::get('databaseconstants.TBL_LEVEL4_OPTIONS') . " AS options", 'activity.id', '=', 'options.activity_id')
                            ->selectRaw('activity.* , GROUP_CONCAT(options.options_text SEPARATOR "#") AS options_text,GROUP_CONCAT(options.correct_option) AS correct_option')
                            ->groupBy('activity.id')
                            ->where('activity.id',$id)
                            ->get();
        return $leve4activities;
    }

    public function getLevel4BasicActivity()
    {
        return  $this->select('*')
                    ->where('deleted', '1')
                    ->get();
    }
}

