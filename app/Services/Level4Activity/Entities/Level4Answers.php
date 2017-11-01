<?php

namespace App\Services\Level4Activity\Entities;
use Illuminate\Database\Eloquent\Model;

class Level4Answers extends Model
{
    protected $table = 'level4_basic_activity_answer';
    protected $fillable = ['id', 'teenager_id', 'activity_id', 'answer_id', 'earned_points', 'deleted'];
}
