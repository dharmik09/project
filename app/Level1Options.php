<?php

namespace App;
use Illuminate\Database\Eloquent\Model;

class Level1Options extends Model
{
    protected $table = 'pro_l1op_level1_options';
    protected $guarded = [];

    public function getQuestionDetail() {
    	return $this->belongsTo(Level1Activity::class, 'id');
    }

    public function questionOptions($questionId) {
    	$return = $this->where('l1op_activity', $questionId)->with('getQuestionDetail')->get();
    	return $return;
    }
}
