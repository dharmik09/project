<?php

namespace App;
use Illuminate\Database\Eloquent\Model;
use Config;
use DB;

class LearningStyle extends Model {

    protected $table = 'pro_ls_learning_styles';

    protected $guarded = [];

    public function getLearningStyleId($name) {
        $result = $this->select('id')
                ->where('ls_name', $name)
                ->where('deleted', '1')
                ->first();
        return $result;
    }

    public function getLearningStyleDetailsById($id) {
        $result = DB::table(config::get('databaseconstants.TBL_LEARNING_STYLE'))
                ->selectRaw('id AS parameterId, ls_name, ls_description')
                ->where('deleted', '=', 1)
                ->where('id', '=', $id)
                ->get();
        return $result;
    }

    public function getLearningStyleDetails() {
        $result = DB::table(config::get('databaseconstants.TBL_LEARNING_STYLE'))
                ->selectRaw('id AS parameterId, ls_name, ls_description, ls_image')
                ->where('deleted', '=', 1)
                ->get();
        return $result;
    }

    public function getLearningStyleDetailsByProfessionId($id,$perameterId,$teenId) {
        $learningStyle = DB::table(config::get('databaseconstants.TBL_PROFESSION_LEARNING_STYLE'). " AS Prols")
                        ->selectRaw('Prols.id,Prols.pls_activity_name')
                        ->where('Prols.pls_profession_id', $id)
                        ->where('Prols.pls_parameter_id', $perameterId)
                        ->where('Prols.deleted','=',1)
                        ->get();
        if (count($learningStyle) > 0) {
            $lId = $learningStyle[0]->id;
            $result = DB::table(config::get('databaseconstants.TBL_USER_LEARNING_STYLE'))
                    ->selectRaw('uls_learning_style_id,uls_earned_points')
                    ->where('uls_learning_style_id',$lId)
                    ->where('uls_profession_id',$id)
                    ->where('uls_teenager_id',$teenId)
                    ->get();
            if (!empty($result)) {
                $result[0]->activity_name = $learningStyle[0]->pls_activity_name;
            }
            return $result;
        } else {
            $result = '';
            return $result;
        }
    }
}
