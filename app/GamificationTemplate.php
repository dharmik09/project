<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Config;
use DB;

class GamificationTemplate extends Model {

    protected $table = 'pro_gt_gamification_template';

//    protected $fillable = ['id','gt_profession_id','gt_template_id','gt_template_title','gt_template_image', 'gt_template_descritpion', 'gt_template_descritpion_popup_imge', 'gt_temlpate_answer_type','gt_coins','gt_valid_upto','deleted'];
    protected $guarded = [];
    
    public function getActivityIdByName($title,$professionId) {
       // $result =  DB::table('pro_gt_gamification_template')->where('gt_template_title', $conceptName)->where('deleted', '1')->first();
        
        $result = GamificationTemplate::select('id')
                ->where('gt_template_title', $title)
                ->where('deleted', '1')
                ->where('gt_profession_id', $professionId)
                ->first();
        
        return $result;
    }
    
    public function getConceptNameByids($ids) {
        $result = GamificationTemplate::selectRaw('GROUP_CONCAT(gt_template_title) as concept')
                ->whereIn('id', $ids)
                ->where('deleted', '1')
                ->first();
        return $result;
    }
    
    
}
