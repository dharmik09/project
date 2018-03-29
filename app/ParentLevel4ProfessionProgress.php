<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use Config;

class ParentLevel4ProfessionProgress extends Model
{
    protected $table = 'pro_l4p_level4_parent_profession_progress';
    protected $guarded = [];
    
    //Store parent progress records by parent id
    public function saveParentProfessionProgress($array) 
    {
        $findData = [];
        if( isset($array['parent_id']) && isset($array['profession_id']) ) {
            $findData = ParentLevel4ProfessionProgress::where(['parent_id' => $array['parent_id'], 'profession_id' => $array['profession_id']])->first();
            if($findData) {
                $findData->update($array);
                $findData = ParentLevel4ProfessionProgress::where(['parent_id' => $array['parent_id'], 'profession_id' => $array['profession_id']])->first(); 
            } else {
                $findData = ParentLevel4ProfessionProgress::insert($array);
            }
        }
        return $findData;
    }

    //Returns completed profession count by Parent id
    public function getCompletedProfessionCountByParentId($parentId) 
    {
        $careerCount = $this->where('parent_id', $parentId)->where('level4_total', 100)->distinct('profession_id')->count('profession_id');
        return $careerCount;
    }
    
    //Returns completed profession count by Parent id
    public function getTeenAttemptProfessionWithTotal($parentId) 
    {
       
        $careerData = $this->select('profession_id as id')->where('parent_id', $parentId)->where('level4_total','>', 0)->get();
       
        return $careerData;
    }
}
