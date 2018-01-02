<?php

namespace App;
use DB;
use Config;
use Illuminate\Database\Eloquent\Model;

class Community extends Model
{
    protected $table = 'pro_tc_teen_connections';
    protected $guarded = [];
}
