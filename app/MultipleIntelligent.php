<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use Config;
use App\MultipleIntelligentScale;

class MultipleIntelligent extends Model
{
    protected $table = 'pro_mit_multiple_intelligence_types';
    protected $fillable = ['id', 'mit_name','mit_logo','mi_video','mi_information','deleted'];

    public function getActiveMultipleIntelligent()
    {
        $result = MultipleIntelligent::select('*')
                        ->where('deleted' ,'1')
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
        $multipleintelligenttype = MultipleIntelligent::where('deleted', '<>', Config::get('constant.DELETED_FLAG'))->get();
        return $multipleintelligenttype;
    }

    /**
     * @return MultipleIntelligence Types details object
       Parameters
       @$multipleintelligenceDetail : Array of MultipleIntelligence Types detail from front
    */
    public function saveMultipleIntelligenceDetail($multipleintelligenceDetail)
    {
        if($multipleintelligenceDetail['id'] != '' && $multipleintelligenceDetail['id'] > 0)
        {
            $return = $this->where('id', $multipleintelligenceDetail['id'])->update($multipleintelligenceDetail);
        }
        else
        {
            $return = $this->create($multipleintelligenceDetail);
        }
        return $return;
    }

    /**
     * @return Boolean True/False
       Parameters
       @$id : MultipleIntelligence Type ID
     */
    public function deleteMultipleIntelligenceType($id)
    {
        $multipleintelligenceytype         = $this->find($id);
        $multipleintelligenceytype->deleted = config::get('constant.DELETED_FLAG');
        $response          = $multipleintelligenceytype->save();
        if($response)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public function multipleIntelligentScale()
    {
        return $this->hasOne('App\MultipleIntelligentScale');
    }


}
