<?php

namespace App\Services\Configurations\Contracts;
use App\Services\Repositories\BaseRepository;
use App\Configurations;

interface ConfigurationsRepository extends BaseRepository
{
    /**
     * @return array of all active teenagers in the application
     */
    public function getAllConfigurations($searchParamArray = array());

    /**
     * Save Teenager detail passed in $teenagerDetail array
     */
    public function saveConfigurationDetail($configurationDetail);

     /**
     * @return Boolean True/False
       Parameters
       @$email : key
     */
    public function checkActiveKey($cfg_key);


    /**
     * Delete Teenager by $id
     */
    public function deleteConfiguration($id);

      /**
     * @return Boolean True/False
       Parameters
       @$email : Teenager's email
     */
    }
