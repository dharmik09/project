<?php

namespace App;
use Illuminate\Database\Eloquent\Model;

class Level1HumanIconProfessionMapping extends Model
{
    protected $table = 'pro_hpm_humanicon_profession_mapping';

    protected $fillable = ['id', 'hpm_humanicon_id','hpm_profession_id'];

}
