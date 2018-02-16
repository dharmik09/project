<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use Config;

class ApptitudeTypeScale extends Model
{
    protected $table = 'pro_ats_apptitude_type_scale';
    protected $guarded = [];

    /**
     * @return array of all the active Apptitude Type Scale
       Parameters
       @$searchParamArray : Array of Searching and Sorting parameters
     */
    public function getAllApptitudeTypesScale()
    {
        $apptitudetypescales = ApptitudeTypeScale::with('apptitude')->get();

        return $apptitudetypescales;
    }

    /**
     * @return Apptitude Type Scale details object
       Parameters
       @$apptitudeScaleDetail : Array of Apptitude Type Scale detail from front
    */
    public function saveApptitudeTypeScaleDetail($apptitudeScaleDetail)
    {
         $apptitudeLength = count($apptitudeScaleDetail['ats_apptitude_type_id']);
         $apptitudeScale =[];
             
         for($i = 0; $i<$apptitudeLength ; $i++)
         {
             $apptitudeScale['ats_apptitude_type_id'] = $apptitudeScaleDetail['ats_apptitude_type_id'][$i];
             $apptitudeScale['ats_high_min_score'] = $apptitudeScaleDetail['ats_high_min_score'][$i];
             $apptitudeScale['ats_high_max_score'] = $apptitudeScaleDetail['ats_high_max_score'][$i];
             $apptitudeScale['ats_moderate_min_score'] = $apptitudeScaleDetail['ats_moderate_min_score'][$i];
             $apptitudeScale['ats_moderate_max_score'] = $apptitudeScaleDetail['ats_moderate_max_score'][$i];
             $apptitudeScale['ats_low_min_score'] = $apptitudeScaleDetail['ats_low_min_score'][$i];
             $apptitudeScale['ats_low_max_score'] = $apptitudeScaleDetail['ats_low_max_score'][$i];

             if($apptitudeScaleDetail['id'][$i] != '0')
             {
                $this->where('id', $apptitudeScaleDetail['id'][$i])->update($apptitudeScale);
             }  
             else
             {
                $this->create($apptitudeScale);
             }

         }
         return '1';
    }

    /**
     * @return Boolean True/False
       Parameters
       @$id : Apptitude Type Scale ID

    public function deleteApptitudeTypeScale($id)
    {
        $result = $this->where('id',$id)->delete();
        if($result)
        {
            return true;
        }
        else
        {
            return false;
        }
    }*/
    
    public function calculateApptitudeHML($apptitudename,$score){
        $scale = '';
        $apptitudeScale = $this->getAllApptitudeTypesScaleForCalculateHML();
        foreach($apptitudeScale as $key=>$val){
            if($apptitudename == $val->apt_name)
            {                            
                if($score >= $val->ats_low_min_score && $score <= $val->ats_low_max_score)
                {
                    $scale = "L";
                }
                elseif($score >= $val->ats_moderate_min_score && $score <= $val->ats_moderate_max_score)
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

    public function apptitude() {
        return $this->belongsTo('App\Apptitude', 'ats_apptitude_type_id');
    }

    public function getAllApptitudeTypesScaleForCalculateHML()
    {
        $apptitudetypescales = DB::select( DB::raw("SELECT
                                              scale.* ,apptitude.apt_name
                                          FROM " . config::get('databaseconstants.TBL_APPTITUDE_TYPE_SCALE'). " AS scale join " .config::get('databaseconstants.TBL_LEVEL2_APPTITUDE')." As apptitude on apptitude.id = scale.ats_apptitude_type_id where apptitude.deleted=1"), array());

        return $apptitudetypescales;
    }
    
    public function getApptitudeScaleById($id)
    {
        $apptitudeScale = ApptitudeTypeScale::where('ats_apptitude_type_id',$id)->first();

        return $apptitudeScale;
    }
}
