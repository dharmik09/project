<?php

namespace App\Services\CMS\Contracts;
use App\Services\Repositories\BaseRepository;
use App\CMS;

interface CMSRepository extends BaseRepository
{    
    /**     
     * @return array of all active cmss in the application
     */
    public function getAllCMS();

    /**
     * Save CMS detail passed in $cmsDetail array
     */
    public function saveCMSDetail($cmsDetail);


    /**
     * Delete CMS by $id
     */
    public function deleteCMS($id);

}
