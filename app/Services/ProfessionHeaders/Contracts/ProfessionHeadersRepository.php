<?php

namespace App\Services\ProfessionHeaders\Contracts;
use App\Services\Repositories\BaseRepository;
use App\ProfessionHeaders;

interface ProfessionHeadersRepository extends BaseRepository
{    
    /**     
     * @return array of all active Profession Headers in the application
     */
    public function getAllProfessionHeaders();

    /**
     * Save Profession Header detail passed in $headerDetail array
     */
    public function saveProfessionHeaderDetail($headerDetail,$headerTitle,$headerContent);


    /**
     * Delete Profession Header by $id
     */
    public function deleteProfessionHeader($id);

}
