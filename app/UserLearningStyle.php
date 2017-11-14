<?php

namespace App;
use Illuminate\Database\Eloquent\Model;
use Config;
use DB;

class UserLearningStyle extends Model {

    protected $table = 'pro_uls_user_learning_style';

    //protected $fillable = ['id','uls_learning_style_id','uls_profession_id','uls_teenager_id','uls_earned_points', 'created_at','updated_at','deleted'];
    protected $guarded = [];

    public function saveUserLearningStyle($learningStyleData) {

        $data = DB::table(config::get('databaseconstants.TBL_USER_LEARNING_STYLE'))->where('uls_learning_style_id', $learningStyleData['uls_learning_style_id'])->where('uls_teenager_id', $learningStyleData['uls_teenager_id'])->where('deleted', '1')->first();        if (count($data) > 0) {
            $return = DB::table(config::get('databaseconstants.TBL_USER_LEARNING_STYLE'))->where('uls_learning_style_id', $learningStyleData['uls_learning_style_id'])->where('uls_teenager_id', $learningStyleData['uls_teenager_id'])->update($learningStyleData);
        } else {
            $return = DB::table(config::get('databaseconstants.TBL_USER_LEARNING_STYLE'))->insert($learningStyleData);
        }
        return $return;
    }

    public function getUserLearningDetailById($id,$pid,$lid) {
        $learningStyle = DB::table(config::get('databaseconstants.TBL_USER_LEARNING_STYLE'). " AS LearningStyle")
                        ->leftjoin(config::get('databaseconstants.TBL_PROFESSIONS') . " AS profession", 'profession.id', '=', 'LearningStyle.uls_profession_id')
                        ->selectRaw('LearningStyle.*, profession.pf_name')
                        ->where('LearningStyle.uls_teenager_id', $id)
                        ->where('LearningStyle.uls_profession_id', $pid)
                        ->where('LearningStyle.uls_learning_style_id', $lid)
                        ->where('LearningStyle.deleted','=',1)
                        ->get();

        return $learningStyle;
    }

    public function getUserLearningStyle($id) {

        $data = DB::table(config::get('databaseconstants.TBL_USER_LEARNING_STYLE'))->where('uls_learning_style_id', $id)->where('deleted', '1')->first();

        return $data;
    }
}