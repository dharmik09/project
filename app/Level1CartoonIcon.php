<?php

namespace App;

use DB;
use Config;
use Illuminate\Database\Eloquent\Model;

class Level1CartoonIcon extends Model
{
    protected $table = 'pro_ci_cartoon_icons';
    protected $guarded = [];
    
    public function getCartooniconCategoryName($afterUnderscore)
    {
        $category = DB::table(config::get('databaseconstants.TBL_CARTOON_ICON_CATEGORY'))->where('cic_name', $afterUnderscore)->first();
        if(empty($category))
        {
           $fillCategory = DB::table(config::get('databaseconstants.TBL_CARTOON_ICON_CATEGORY'))->insert(['cic_name' => $afterUnderscore]);
           $fillCategoryFetch = DB::table(config::get('databaseconstants.TBL_CARTOON_ICON_CATEGORY'))->where('cic_name', $afterUnderscore)->first();
           $fillcategoryId = $fillCategoryFetch->id;
           return $fillcategoryId;
        }
        else {
            $categoryId = $category->id;
            return $categoryId;
            
        }
    }
    public function findData($id)
    {
        $data = DB::select(DB::raw("select cartoon.*".","." GROUP_CONCAT(cprofession.cpm_profession_id) AS cpm_profession_id from ".config::get('databaseconstants.TBL_LEVEL1_CARTOON_ICON')." AS cartoon join ".config::get('databaseconstants.TBL_CARTOON_ICON_PROFESSION_MAPPING')." As cprofession"." on cartoon.id = cprofession.cpm_cartoon_id where cartoon.id = " .$id));
                                                    
        return $data;
    }

}
