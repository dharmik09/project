<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use Config;

class Personality extends Model
{
    protected $table = 'pro_pt_personality_types';
    protected $fillable = ['id', 'pt_name','pt_logo' , 'pt_video', 'pt_information', 'deleted'];

    public function getActivepersonality()
    {
        $result = $this->select('*')
                        ->where('deleted' ,'1')
                        ->get();
        return $result;
    }

    /**
     * @return array of all the active Personality Types
       Parameters
       @$searchParamArray : Array of Searching and Sorting parameters
     */
    public function getAllPersonalityTypes($searchParamArray = array())
    {
        $personalitytypes = $this->where('deleted', '<>', Config::get('constant.DELETED_FLAG'))->get();
        return $personalitytypes;
    }

    /**
     * @return Personality Types details object
       Parameters
       @$personalityDetail : Array of Personality Type detail from front
    */
    public function savePersonalityDetail($personalityDetail)
    {
        if($personalityDetail['id'] != '' && $personalityDetail['id'] > 0)
        {
            $return = $this->where('id', $personalityDetail['id'])->update($personalityDetail);
        }
        else
        {
            $return = $this->create($personalityDetail);
        }
        return $return;
    }

    /**
     * @return Boolean True/False
       Parameters
       @$id : Personality Type ID
     */
    public function deletePersonalityType($id)
    {
        $personalitytype         = $this->find($id);
        $personalitytype->deleted = config::get('constant.DELETED_FLAG');
        $response          = $personalitytype->save();
        if($response)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public function personalityScale() {
        return $this->hasOne('App\PersonalityScale');
    }

}
