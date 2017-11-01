<?php

namespace App;
use DB;
use Illuminate\Database\Eloquent\Model;
use Config;

class Level2Answers extends Model
{
    protected $table = 'pro_l2ans_level2_answers';
    protected $guarded = [];
    
    public function level2GetCorrectAnswerQuestionIds($teenagerId)
    {
        $level2CorrectAnswerQuestionIds  = DB::select(DB::raw("SELECT pro_l2ans_level2_answers.*
                                            FROM 
                                            " . config::get('databaseconstants.TBL_LEVEL2_ANSWERS') . " JOIN pro_l2op_level2_options ON pro_l2ans_level2_answers.l2ans_answer = pro_l2op_level2_options.id WHERE l2ans_teenager = $teenagerId
                                            AND pro_l2ans_level2_answers.deleted=1 AND pro_l2op_level2_options.l2op_fraction= 1 "), array());        
        return $level2CorrectAnswerQuestionIds;   
    }    
}
