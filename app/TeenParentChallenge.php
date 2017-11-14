<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Config;
use DB;

class TeenParentChallenge  extends Model {

    protected $table = 'pro_tpc_teenager_parent_challenge';
//    protected $fillable = ['id', 'tpc_teenager_id', 'tpc_parent_id', 'tpc_profession_id', 'created_at','updated_at','deleted'];
    protected $guarded = [];

    public function saveTeenParentRequestDetail($saveData) {
        $result = TeenParentChallenge::select('*')
                        ->where('deleted', '1')
                        ->where('tpc_teenager_id', $saveData['tpc_teenager_id'])
                        ->where('tpc_parent_id', $saveData['tpc_parent_id'])
                        ->where('tpc_profession_id', $saveData['tpc_profession_id'])
                        ->get();
        if (isset($result) && !empty($result) && count($result) > 0) {
            $return = $this->where('id', $result[0]['id'])->update($saveData);
        } else {
            $return = $this->create($saveData);
        }
        return $return;
    }

    public function getTeenParentChallengeData($parentId) {
        $result = DB::table(config::get('databaseconstants.TBL_TEENAGER_PARENT_CHALLENGE'). " AS parent_challenge")
                        ->leftjoin(config::get('databaseconstants.TBL_PROFESSIONS') . " AS profession", 'profession.id', '=', 'parent_challenge.tpc_profession_id')
                        ->leftjoin(config::get('databaseconstants.TBL_TEENAGERS') . " AS teen", 'teen.id', '=', 'parent_challenge.tpc_teenager_id')
                        ->selectRaw('parent_challenge.*, profession.pf_name, teen.t_name, profession.pf_logo')
                        ->where('parent_challenge.tpc_parent_id', $parentId)
                        ->where('parent_challenge.deleted','=',1)
                        ->get();
        return $result;
    }

    public function getTeenParentRequestDetail($saveData) {
        $result = TeenParentChallenge::select('*')
                        ->where('deleted', '1')
                        ->where('tpc_teenager_id', $saveData['tpc_teenager_id'])
                        ->where('tpc_parent_id', $saveData['tpc_parent_id'])
                        ->where('tpc_profession_id', $saveData['tpc_profession_id'])
                        ->get();
        if (isset($result) && !empty($result) && count($result) > 0) {
            return true;
        } else {
            return false;
        }
    }
}
