<?php

namespace App\Services\Genericads\Repositories;

use DB;
use Config;
use App\Services\Genericads\Contracts\GenericadsRepository;
use App\Services\Repositories\Eloquent\EloquentBaseRepository;

class EloquentGenericadsRepository extends EloquentBaseRepository
    implements GenericadsRepository
{
    /**
     * @return array of all the active schools
       Parameters
       @$searchParamArray : Array of Searching and Sorting parameters
     */

      public function getAllGeneric()
      {
            $generic = DB::table(config::get('databaseconstants.TBL_GENERIC'))
                              ->selectRaw('*')
                              ->where('deleted', '<>', Config::get('constant.DELETED_FLAG'))
                              ->get();
            return $generic;
      }
      
      public function saveGenericDetail($genericDetail)
      {
          if (isset($genericDetail['id']) && $genericDetail['id'] != '' && $genericDetail['id'] > 0) {
                $return = $this->model->where('id', $genericDetail['id'])->update($genericDetail);
            } else {
                $return = $this->model->create($genericDetail);
            }
            return $return;
      }
      
      public function deleteGeneric($id)
      {
        $flag = true;
        $generic = $this->model->find($id);
        $generic->deleted = config::get('constant.DELETED_FLAG');
        $response = $generic->save();
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
