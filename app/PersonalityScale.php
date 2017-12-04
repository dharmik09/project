<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use Config;

class PersonalityScale extends Model
{
    protected $table = 'pro_pts_personality_type_scale';
    protected $fillable = ['id', 'pts_personality_type_id', 'pts_high_min_score','pts_high_max_score' , 'pts_moderate_min_score' ,'pts_moderate_max_score' ,'pts_low_min_score' , 'pts_low_max_score'];
    
    public function getActivePersonalityScale()
    {
        $result = $this->select('*')
                        ->get();
        return $result;
    }

    /**
     * @return array of all the active Personality Types
       Parameters
       @$searchParamArray : Array of Searching and Sorting parameters
     */
    public function getAllPersonalityTypes()
    {
        $personalitytypescales = PersonalityScale::with('personality')->get();
        return $personalitytypescales;
    }

    /**
     * @return Personality Types details object
       Parameters
       @$personalityDetail : Array of Personality Types detail from front
    */
    public function savePersonalityScaleDetail($personalityDetail)
    {
        $personalityLength = count($personalityDetail['pts_personality_type_id']);
        $personalityArray = [];
        for($i=0;$i<$personalityLength;$i++)
        {
            $personalityArray['pts_personality_type_id'] = $personalityDetail['pts_personality_type_id'][$i];
            $personalityArray['pts_high_min_score'] = $personalityDetail['pts_high_min_score'][$i];
            $personalityArray['pts_high_max_score'] = $personalityDetail['pts_high_max_score'][$i];
            $personalityArray['pts_moderate_min_score'] = $personalityDetail['pts_moderate_min_score'][$i];
            $personalityArray['pts_moderate_max_score'] = $personalityDetail['pts_moderate_max_score'][$i];
            $personalityArray['pts_low_min_score'] = $personalityDetail['pts_low_min_score'][$i];
            $personalityArray['pts_low_max_score'] = $personalityDetail['pts_low_max_score'][$i];
            if($personalityDetail['id'][$i] != '0')
            {
                $this->where('id', $personalityDetail['id'][$i])->update($personalityArray);
            }  
            else 
            {
                $this->create($personalityArray);
            }
            
        }
        return '1';
    }

    /**
     * @return Boolean True/False
       Parameters
       @$id : Personality Type ID
     */
    public function deletePersonalityTypeScale($id)
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
    
    public function calculatePersonalityHML($personality, $score){
        $scale = "H";
        $personalityScale = $this->getAllPersonalityTypesForCalculateHML();
        foreach($personalityScale as $key=>$val){
            if($personality == $val->pt_name)
            {                            
                if($score >= $val->pts_low_min_score && $score <= $val->pts_low_max_score)
                {
                    $scale = "L";
                }
                elseif($score >= $val->pts_moderate_min_score && $score <= $val->pts_moderate_max_score)
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

    public function personality() {
        return $this->belongsTo('App\Personality', 'pts_personality_type_id');
    }

    public function getAllPersonalityTypesForCalculateHML($searchParamArray = array())
    {
        $personalitytypescales = DB::select( DB::raw("SELECT
                                              scale.* , personality.pt_name
                                          FROM " . config::get('databaseconstants.TBL_PERSONALITY_TYPE_SCALE'). " AS scale join " .config::get('databaseconstants.TBL_LEVEL2_PERSONALITY')." As personality on personality.id = scale.pts_personality_type_id where personality.deleted=1"), array());
        return $personalitytypescales;
    }

}
