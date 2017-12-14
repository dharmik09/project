<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use Config;
use App\MultipleIntelligentScale;

class MultipleIntelligent extends Model
{
    protected $table = 'pro_mit_multiple_intelligence_types';
    protected $guarded = [];

    public function getActiveMultipleIntelligent()
    {
        $result = $this->select('*')
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
        $multipleintelligenttype = $this->where('deleted', '<>', Config::get('constant.DELETED_FLAG'))->get();
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

    public function getMultipleIntelligentIdByName($name)
    {
        $result = MultipleIntelligent::select('*')
                        ->where('deleted' ,'1')
                        ->where('mit_name',$name)
                        ->get();
        if (count($result) > 0) {
            return $result[0]['id'];
        } else {
            return false;
        }
    }

    public function getMultipleIntelligenceDetailBySlug($slug)
    {
        $mi = MultipleIntelligent::where('deleted', Config::get('constant.ACTIVE_FLAG'))->where('mi_slug', $slug)->first();
        return $mi;
    }
}
