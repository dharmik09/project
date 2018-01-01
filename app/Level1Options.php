<?php
namespace App;
use Illuminate\Database\Eloquent\Model;

class Level1Options extends Model
{
    protected $table = 'pro_l1op_level1_options';
    protected $guarded = [];

    public function getQuestionDetail() {
    	return $this->belongsTo(Level1Activity::class, 'l1op_activity');
    }
}
