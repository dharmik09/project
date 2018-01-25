<?php

namespace App;

use DB;
use Config;
use Illuminate\Database\Eloquent\Model;

class Level1TraitsAnswers extends Model
{
    protected $table = 'pro_tqa_traits_quality_answer';
    protected $fillable = ['tqq_id','tqo_id','tqa_from','tqa_to','deleted'];

    function traitOptionsDetail() {
        return $this->belongsTo(Level1TraitsOptions::class, 'tqo_id');
    }
    
}
