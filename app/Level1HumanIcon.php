<?php

namespace App;

use DB;
use Config;
use Illuminate\Database\Eloquent\Model;

class Level1HumanIcon extends Model
{
    protected $table = 'pro_hi_human_icons';
    protected $guarded = [];
    
    public function getHumaniconCategoryName($afterUnderscore)
    {
        $category = DB::table(config::get('databaseconstants.TBL_HUMAN_ICON_CATEGORY'))->where('hic_name', $afterUnderscore)->first();
        if(empty($category))
        {
           $fillCategory = DB::table(config::get('databaseconstants.TBL_HUMAN_ICON_CATEGORY'))->insert(['hic_name' => $afterUnderscore]);
           $fillCategoryFetch = DB::table(config::get('databaseconstants.TBL_HUMAN_ICON_CATEGORY'))->where('hic_name', $afterUnderscore)->first();
           $fillcategoryId = $fillCategoryFetch->id;
           return $fillcategoryId;
        }
        else {
            $categoryId = $category->id;
            return $categoryId;
            
        }
    }

    public function getActiveLevel1HumanActivity($id)
    {
        $level1activities = DB::select( DB::raw("SELECT
                                              human.* , GROUP_CONCAT(profession.hpm_profession_id) AS hpm_profession_id
                                          FROM " . config::get('databaseconstants.TBL_LEVEL1_HUMAN_ICON') . " AS human left join " . config::get('databaseconstants.TBL_HUMAN_ICON_PROFESSION_MAPPING') ." AS profession on human.id = profession.hpm_humanicon_id
                                           where human.id = ".$id." group by  human.id "));

        return $level1activities;
    }
}
