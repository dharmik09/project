<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use Config;

class SystemLevels extends Model
{
    protected $table = 'pro_sl_system_levels';
    protected $fillable = ['id', 'sl_name', 'sl_info', 'sl_boosters', 'deleted'];

    public function getActiveLevels()
    {
        $result = SystemLevels::select('*')
                        ->where('deleted' ,'1')
                        ->get();
        return $result;
    }
    
    public function getLevelname()
    {
        $result = SystemLevels::select('id', 'sl_name')
                        ->where('deleted' ,'1')
                        ->get();
        return $result;
    }
    /**
     * @return array of all the active System Levels
       Parameters
       @$searchParamArray : Array of Searching and Sorting parameters
     */
    public function getAllSystemLevels($searchParamArray = array())
    {
        $systemLevels = SystemLevels::where('deleted', '<>', Config::get('constant.DELETED_FLAG'))->get();
        return $systemLevels;
    }

    /**
     * @return System levels details object
       Parameters
       @$systemlevelDetail : Array of System Levels detail from front
    */
    public function saveSystemLevelDetail($systemlevelDetail)
    {
        if($systemlevelDetail['id'] != '' && $systemlevelDetail['id'] > 0)
        {
            $return = $this->where('id', $systemlevelDetail['id'])->update($systemlevelDetail);
        }
        else
        {
            $return = $this->create($systemlevelDetail);
        }
        return $return;
    }

    /**
     * @return Boolean True/False
       Parameters
       @$id : System Level ID
     */
    public function deleteSystemLevel($id)
    {
        $systemLevel         = $this->find($id);
        $systemLevel->deleted = config::get('constant.DELETED_FLAG');
        $response          = $systemLevel->save();
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
