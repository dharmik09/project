<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use Config;

class Level1Qualities extends Model
{
    protected $table = 'pro_l1qa_level1_qualities';
    protected $guarded = [];

    /**
     * @return array of all the active Level1 Qualities
       Parameters
       @$searchParamArray : Array of Searching and Sorting parameters
     */
    public function getAllLevel1Qualities($searchParamArray = array())
    {
        $level1qualities = $this->where('deleted', '<>', Config::get('constant.DELETED_FLAG'))->get();

        return $level1qualities;
    }

    /**
     * @return Level1 Quality details object
       Parameters
       @$qualityDetail : Array of Level1 Quality detail from front
    */
    public function saveLevel1QualityDetail($qualityDetail)
    {
        if($qualityDetail['id'] != '' && $qualityDetail['id'] > 0)
        {
            $return = $this->where('id', $qualityDetail['id'])->update($qualityDetail);
        }
        else
        {
            $return = $this->create($qualityDetail);
        }
        return $return;
    }

    /**
     * @return Boolean True/False
       Parameters
       @$id : Level1 Qualities ID
    */
    public function deleteLevel1QualityType($id)
    {
        $level1quality         = $this->find($id);
        $level1quality->deleted = config::get('constant.DELETED_FLAG');
        $response          = $level1quality->save();
        if($response)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

}
