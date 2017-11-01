<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Level1CartoonIconCategory extends Model
{
    protected $table = 'pro_cic_cartoon_icons_category';
    protected $guarded = [];

    public function getActiveCartoonCategory()
    {
        $result = Level1CartoonIconCategory::select('*')
                        ->where('deleted' ,'1')
                        ->get();
        return $result;
    }
}
