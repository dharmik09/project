<?php

namespace App;
use Illuminate\Database\Eloquent\Model;
use DB;
use Config;

class Level1TraitsOptions extends Model
{
    protected $table = 'pro_tqo_traits_quality_options';
    protected $guarded = [];

    public function getQuestionDetail() {
    	return $this->belongsTo(Level1Traits::class, 'id');
    }

    public function questionOptions($questionId) {
    	$return = $this->where('tqq_id', $questionId)->with('getQuestionDetail')->get();
    	return $return;
    }

    function traitAnswers() {
        return $this->hasMany(Level1TraitsAnswers::class, 'tqo_id');
    }
}
