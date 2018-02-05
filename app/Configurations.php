<?php

namespace App;
use Illuminate\Database\Eloquent\Model;

class Configurations extends Model
{
    protected $table = 'pro_cfg_configurations';

    protected $fillable = ['id', 'cfg_key', 'cfg_value', 'created_at', 'created_by', 'updated_at', 'updated_by', 'deleted'];
    
    public function getCreditValue($config)
    {
        $creditValue = Configurations::Select('pro_cfg_configurations.cfg_value')->where('cfg_key', $config)->first();
        if($creditValue) {
          return $creditValue->cfg_value;
        }
        return false;    
    }
    
    public function getCreditKey($type)
    {       
       $creditKey = Configurations::Select('pro_cfg_configurations.*')->where('id', $type)->get();
       foreach($creditKey as $value)
       {
           return $value->cfg_key;
       }
    }
    
}
