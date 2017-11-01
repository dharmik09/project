<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use Config;

class MultipleIntelligentScale extends Model
{
    protected $table = 'pro_mts_mi_type_scale';
    protected $guarded = [];

    public function getActiveMultipleIntelligentScale()
    {
        $result = $this->select('*')
                        ->get();
        return $result;
    }

    /**
     * @return array of all the active MultipleIntelligence Types
       Parameters
       @$searchParamArray : Array of Searching and Sorting parameters
     */
    public function getAllMultipleIntelligenceTypes()
    { 
       $multipleintelligenttype = DB::select( DB::raw("SELECT
                                              scale.* , mi.mit_name
                                          FROM " . config::get('databaseconstants.TBL_MI_TYPE_SCALE'). " AS scale join " .config::get('databaseconstants.TBL_LEVEL2_MI')." As mi on mi.id = scale.mts_mi_type_id where mi.deleted=1"), array());
        return $multipleintelligenttype;
    }

    /**
     * @return MultipleIntelligence Types details object
       Parameters
       @$multipleintelligenceDetail : Array of MultipleIntelligence Types detail from front
    */
    public function saveMultipleIntelligenceScaleDetail($multipleintelligenceDetail)
    {  
        $miLength = count($multipleintelligenceDetail['mts_mi_type_id']);
        $miArray = [];
        for($i=0;$i<$miLength;$i++)
        {
            $miArray['mts_mi_type_id'] = $multipleintelligenceDetail['mts_mi_type_id'][$i];
            $miArray['mts_high_min_score'] = $multipleintelligenceDetail['mts_high_min_score'][$i];
            $miArray['mts_high_max_score'] = $multipleintelligenceDetail['mts_high_max_score'][$i];
            $miArray['mts_moderate_min_score'] = $multipleintelligenceDetail['mts_moderate_min_score'][$i];
            $miArray['mts_moderate_max_score'] = $multipleintelligenceDetail['mts_moderate_max_score'][$i];
            $miArray['mts_low_min_score'] = $multipleintelligenceDetail['mts_low_min_score'][$i];
            $miArray['mts_low_max_score'] = $multipleintelligenceDetail['mts_low_max_score'][$i];
            if($multipleintelligenceDetail['id'][$i] != '0')
            { 
               $this->where('id', $multipleintelligenceDetail['id'][$i])->update($miArray);
            }  
            else 
            {
              $this->create($miArray);
            }
            
        }
        return '1';
    }

    /**
     * @return Boolean True/False
       Parameters
       @$id : MultipleIntelligence Type ID
     */
    public function deleteMultipleIntelligenceTypeScale($id)
    {
        $response  = $this->where('id', $id)->delete();
        if($response)
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    
    public function calculateMIHML($MIname,$score){
        
        $scale = '';
        $MIScale = $this->getAllMultipleIntelligenceTypes();
        foreach($MIScale as $key=>$val){
            if($MIname == $val->mit_name)
            {                            
                if($score >= $val->mts_low_min_score && $score <= $val->mts_low_max_score)
                {
                    $scale = "L";
                }
                elseif($score >= $val->mts_moderate_min_score && $score <= $val->mts_moderate_max_score)
                {
                    $scale = "M";
                }
                else
                {
                    $scale = "H";
                }
            }           
        }
        return $scale;
    }

}
