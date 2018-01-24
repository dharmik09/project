<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use Config;

class ProfessionAttempted extends Model
{
    protected $table = 'pro_tpa_teenager_profession_attempted';

    protected $fillable = ['tpa_teenager', 'tpa_peofession_id', 'tpa_type'];
}
