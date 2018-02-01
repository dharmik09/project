<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Config;
use DB;

class TeenagerBoosterPoint extends Model {

    protected $table = 'pro_tlb_teenager_level_boosters';
    protected $fillable = [];

    public function getTeenagerBoosterPoint($userId,$level)
    {
       $data = $this->where('tlb_teenager', $userId)->where('tlb_level', $level)->first();
       return $data;        
    }
    
    public function updateTeenagerBoosterPoint($id,$teenagerLevel3PointsRow)
    {
       $data = $this->where('id', $id)->update($teenagerLevel3PointsRow); 
       return $data;        
    }
    
    public function addTeenagerBoosterPoint($teenagerLevel3PointsRow)
    {
       $data = $this->insert($teenagerLevel3PointsRow); 
       return $data;        
    }    
}