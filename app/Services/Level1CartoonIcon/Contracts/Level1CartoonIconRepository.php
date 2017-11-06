<?php

namespace App\Services\Level1CartoonIcon\Contracts;
use App\Services\Repositories\BaseRepository;
use App\Level1CartoonIcon;
use File;

interface Level1CartoonIconRepository extends BaseRepository
{    
    /**     
     * @return array of all active human icons in the application
     */
    public function getLeve1CartoonIcon();

    /**
     * Save Cartoons detail passed in $cartoonIconDetail array
     */
    public function saveLevel1CartoonIconDetail($cartoonIconDetail,$professions);
                                           
    /**
     * Delete Cartoon Icon by $id
     */
    public function deleteLevel1Cartoon($id);
    
    /**
     * Save For Front
     */
    public function saveLevel1CartoonIconCategoryDetailForFront($detailArray);
    
    public function getLeve1CartoonIconfromUsers($searchParamArray = array());
   
    public function deleteLevel1CartoonuploadedbyUser($id);
}
