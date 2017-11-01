<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use Config;

class Apptitude extends Model
{
    protected $table = 'pro_apt_apptitude_types';
    protected $fillable = ['id', 'apt_name', 'apt_logo' ,'apt_video', 'ap_information', 'deleted'];

    public function getActiveApptitude()
    {
        $result = Apptitude::select('*')
                        ->where('deleted' ,'1')
                        ->get();
        return $result;
    }

    /**
     * @return array of all the active Apptitude Types
       Parameters
       @$searchParamArray : Array of Searching and Sorting parameters
     */
    public function getAllApptitudeTypes()
    {
        $apptitudetypes = Apptitude::where('deleted', '<>', Config::get('constant.DELETED_FLAG'))->get();

        return $apptitudetypes;
    }

    /**
     * @return Apptitude Types details object
       Parameters
       @$apptitudeDetail : Array of Apptitude Type detail from front
    */
    public function saveApptitudeDetail($apptitudeDetail)
    {
        if($apptitudeDetail['id'] != '' && $apptitudeDetail['id'] > 0)
        {
            $return = $this->where('id', $apptitudeDetail['id'])->update($apptitudeDetail);
        }
        else
        {
            $return = $this->create($apptitudeDetail);
        }
        return $return;
    }

    /**
     * @return Boolean True/False
       Parameters
       @$id : Apptitude Type ID
     */
    public function deleteApptitudeType($id)
    {
        $apptitudetype         = $this->find($id);
        $apptitudetype->deleted = config::get('constant.DELETED_FLAG');
        $response          = $apptitudetype->save();
        if($response)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public function apptitudeTypeScale() {
        return $this->hasOne('App\ApptitudeTypeScale');
    }

}
