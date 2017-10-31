<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use Config;

class Interest extends Model
{
    protected $table = 'pro_it_interest_types';
    protected $fillable = ['id', 'it_name', 'it_logo' , 'deleted'];

    public function getActiveInterest()
    {
        $result = Interest::select('*')
                        ->where('deleted' ,'1')
                        ->get();
        return $result;
    }

    /**
     * @return array of all the active Interest Types
       Parameters
       @$searchParamArray : Array of Searching and Sorting parameters
     */
    public function getAllInterestTypes($searchParamArray = array())
    {
        $interesttype = Interest::where('deleted', '<>', Config::get('constant.DELETED_FLAG'))->get();
        return $interesttype;
    }

    /**
     * @return Interest Types details object
       Parameters
       @$interestDetail : Array of Interest Types detail from front
    */
    public function saveInterestDetail($interestDetail)
    {
        if($interestDetail['id'] != '' && $interestDetail['id'] > 0)
        {
            $return = $this->where('id', $interestDetail['id'])->update($interestDetail);
        }
        else
        {
            $return = $this->create($interestDetail);
        }
        return $return;
    }

    /**
     * @return Boolean True/False
       Parameters
       @$id : Interest Type ID
     */
    public function deleteInterestType($id)
    {
        $interesttype         = $this->find($id);
        $interesttype->deleted = config::get('constant.DELETED_FLAG');
        $response          = $interesttype->save();
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
