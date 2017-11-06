<?php

use Illuminate\Database\Eloquent\Model;

class Level1Answers extends Model
{
    protected $table = 'pro_l1ans_level1_answers';
    protected $fillable = ['id', 'l1ans_teenager', 'l1ans_activity', 'l1ans_answer'];

}
