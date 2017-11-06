<?php

namespace App;
use Illuminate\Database\Eloquent\Model;
use Config;
use DB;

class TeenParentRequest extends Model {

    protected $table = 'pro_tpr_teen_parent_request';
//    protected $fillable = ['id', 'tpr_teen_id', 'tpr_parent_id', 'tpr_status', 'created_at','updated_at','deleted'];
    protected $guarded = [];
    
    public function saveTeenParentRequestDetail($saveData) {
        $return = DB::table(config::get('databaseconstants.TBL_TEEN_PARENT_REQUEST'))->insert($saveData);
        return $return;
    }
    public function getTeenParentRequestDetail($id) {
        $coinsDetail = DB::table(config::get('databaseconstants.TBL_TEEN_PARENT_REQUEST') . " AS teen_parent ")
                ->leftjoin(config::get('databaseconstants.TBL_TEENAGERS') . " AS teen ", 'teen_parent.tpr_teen_id', '=', 'teen.id')
                ->selectRaw('teen_parent.* , teen.t_name, teen.t_email')
                ->where('teen_parent.tpr_parent_id',$id)
                ->get();

        return $coinsDetail;
    }

    public function updateTeenParentRequestDetail($parentid,$teenagerId) {
        $return = DB::table(config::get('databaseconstants.TBL_TEEN_PARENT_REQUEST'))->where('tpr_parent_id', $parentid)->where('tpr_teen_id', $teenagerId)->update(['tpr_status'=> 2]);
        return $return;
    }
}
