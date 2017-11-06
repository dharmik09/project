<?php

namespace App\Services\Level1HumanIcon\Contracts;
use App\Services\Repositories\BaseRepository;
use App\Level1HumanIcon;
use File;

interface Level1HumanIconRepository extends BaseRepository
{    
    /**
     * @return array of all active human icons in the application
     */
    public function getLeve1HumanIcon();

    /**
     * Save Human Icon detail passed in $humanIconDetail array
     */
    public function saveLevel1HumanIconDetail($humanIconDetail, $profession = array());

    /**
     * Delete Human Icon by $id
     */
    public function deleteLevel1HumanIcon($id);

     /**
     * @return array of all active human icons Category in the application
     */
    public function getLeve1HumanIconCategory();

    /**
     * Save Human Icon detail passed in $humanIconCategoryDetail array
     */
    public function saveLevel1HumanIconCategoryDetail($humanIconCategoryDetail);

    /**
     * Delete Human Icon Category by $id
     */
    public function deleteLevel1HumanIconCategory($id);
    
    /**
     * Save For Front
     */
    public function saveLevel1HumanIconCategoryDetailForFront($detailArray);
    
    public function getLeve1HumanIconfromUsers($searchParamArray = array());
    
    public function deleteLevel1HumanIconuploadedbyUser($id);
   
}
