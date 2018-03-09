<?php

namespace App;

use Illuminate\Notifications\Notifiable;

use Illuminate\Database\Eloquent\Model;
use Config;
use DB;

class TeenagersMetaDataInfo extends Model {

    protected $table = 'pro_tmd_teenager_meta_data';
    protected $guarded = [];
    
    public function getTeenagerInfo() {
        return $this->belongsTo(Teenagers::class, 'tmd_teenager');
    }
}