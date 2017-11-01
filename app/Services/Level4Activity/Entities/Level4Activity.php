<?php

namespace App\Services\Level4Activity\Entities;
use Illuminate\Database\Eloquent\Model;

class Level4Activity extends Model
{
    protected $table = 'level4_basic_activity';
    protected $fillable = ['id', 'profession_id', 'question_text', 'points', 'timer','type','deleted'];

}
