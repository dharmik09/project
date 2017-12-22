<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TeenagerSponsor extends Model
{
    protected $table = 'pro_ts_teenager_sponsors';
    protected $guarded = [];

    public function teenager() {
        return $this->belongsTo(Teenagers::class, 'ts_teenager');
    }

    public function sponsor() {
        return $this->belongsTo(Sponsors::class, 'ts_sponsor');
    }

}
