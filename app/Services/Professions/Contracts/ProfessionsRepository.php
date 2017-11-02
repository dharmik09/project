<?php

namespace App\Services\Professions\Contracts;
use App\Services\Repositories\BaseRepository;
use App\Professions;

interface ProfessionsRepository extends BaseRepository
{    
    /**     
     * @return array of all active professions in the application
     */
    public function getAllProfessions($searchParamArray = array());

    /**
     * Save Parent detail passed in $professionDetail array
     */
    public function saveProfessionDetail($professionDetail);

    /**
     * Delete Profession by $id
     */
    public function deleteProfession($id);

    /**
     * Get Profession detail by basket id
     */
    public function getProfessionsByBasketId($basketid);
    
    /**
     * Get Profession detail by profession
     */
    public function getProfessionsByProfessionId($professionid);
    
    /**
     * save Buket, Profession Bulk Detail
     */
    public function saveProfessionBulkDetail($professtionDetail, $basketDetail, $headerDetail);
    
    /**
     * get Profession data from profession name
     */
    public function getProfessionsData($professionName); 
    /**
     * get Profession data from profession id
     */
    public function getProfessionsDataFromId($professionId); 
    
    /*    
     * Get Profession detail by searching text
     */
    public function getsearchByText($serachtext);
    
    /*    
     * Get Profession detail by searching text
     */
    
    public function getLevel3ActivityWithAnswer($id);
    
     public function getExportProfession();
     
    public function checkForBasket($id);
}
