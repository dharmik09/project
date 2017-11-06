<?php
namespace App\Services\Configurations\Repositories;
use DB;
use Auth;
use Config;
use App\Services\Configurations\Contracts\ConfigurationsRepository;
use App\Services\Repositories\Eloquent\EloquentBaseRepository;


class EloquentConfigurationsRepository extends EloquentBaseRepository implements ConfigurationsRepository {

    /**
    * @return array of all the active teenagers
    Parameters
    @$searchParamArray : Array of Searching and Sorting parameters
    */
    public function getAllConfigurations($searchParamArray = array()) {

        $configurations = DB::table(config::get('databaseconstants.TBL_CONFIGURATION'))
                          ->selectRaw('*')
                          ->where('deleted', '<>', Config::get('constant.DELETED_FLAG'))->get();
                      
        return $configurations;
    }

    /**
    * @return Teenager details object
    Parameters
    @$teenagerDetail : Array of teenagers detail from front
    */
    public function saveConfigurationDetail($configurationDetail) {
        if (isset($configurationDetail['id']) && $configurationDetail['id'] != '' && $configurationDetail['id'] > 0) {
            $return = $this->model->where('id', $configurationDetail['id'])->update($configurationDetail);
        } else {
            $return = $this->model->create($configurationDetail);
        }
        return $return;
    }

    /**
    * @return Boolean True/False
    Parameters
    @$id : Teenager ID
    */
    public function deleteConfiguration($id) {
        $flag = true;
        $configuration = $this->model->find($id);
        $configuration->deleted = config::get('constant.DELETED_FLAG');
        $response = $configuration->save();
        if ($response) {
            return true;
        } else {
            return false;
        }
    }

    /**
    * @return Boolean True/False
    Parameters
    @$email : Parent's email
    */
    public function checkActiveKey($cfg_key,$id='')
    {

        if($id != '')
        {
            $user = $this->model->where('cfg_key', $cfg_key)->where('id','!=',$id)->get();
        }
        else
        {
            $user = $this->model->where('cfg_key', $cfg_key)->get();
        }
        if($user->count() > 0)
        {
            return true;
        }
        else
        {
            return false;
        }
    }
}

