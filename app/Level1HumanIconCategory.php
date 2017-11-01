<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Level1HumanIconCategory extends Model
{
    protected $table = 'pro_hi_human_icons_category';
    protected $guarded = [];
    
    public function getActiveCategory()
    {
        $result = Level1HumanIconCategory::select('*')
                        ->where('deleted' ,'1')
                        ->get();
        return $result;
    }
}
