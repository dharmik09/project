<?php

namespace App\Services\Level4Activity\Entities;
use Illuminate\Database\Eloquent\Model;

class Level4Options extends Model
{
    protected $table = 'level4_basic_activity_options';
    protected $fillable = ['id', 'activity_id', 'options_text', 'correct_option', 'deleted'];
}
