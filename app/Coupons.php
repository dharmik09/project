<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Coupons extends Model
{
    protected $table = 'pro_cp_coupons';
//    protected $fillable = ['id', 'cp_code', 'cp_description', 'cp_image','cp_sponsor', 'cp_validfrom', 'cp_validto','cp_usage', 'deleted'];
    protected $guarded = [];

}
