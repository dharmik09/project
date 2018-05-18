<?php

namespace App;

use DB;
use Config;
use Illuminate\Database\Eloquent\Model;

class Level1Answers extends Model
{
    protected $table = 'pro_l1ans_level1_answers';
    protected $fillable = ['id', 'l1ans_teenager', 'l1ans_activity', 'l1ans_answer'];

    //get all teenagers attempted level 1
    public function getAllTeenagersAttemptedL1() {
    	$allTeens = $this->join("pro_t_teenagers as teenager", "pro_l1ans_level1_answers.l1ans_teenager", '=', 'teenager.id')->where('teenager.deleted', Config::get('constant.ACTIVE_FLAG'))->distinct('pro_l1ans_level1_answers.l1ans_teenager')->count('pro_l1ans_level1_answers.l1ans_teenager');
    	return $allTeens;
    }

}
