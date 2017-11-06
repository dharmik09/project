<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Config;
use DB;

class SponsorsActivity extends Model 
{

    protected $table = 'pro_sp_sponsor';
//    protected $fillable = ['id','sa_sponsor_id', 'sa_type','sa_name','sa_apply_level','sa_location','sa_image','sa_image_href','sa_credit_used','sa_start_date','sa_end_date','deleted','created_at','updated_at'];
    protected $guarded = [];
}


