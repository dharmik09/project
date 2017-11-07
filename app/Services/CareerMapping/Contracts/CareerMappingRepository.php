<?php

namespace App\Services\CareerMapping\Contracts;
use App\Services\Repositories\BaseRepository;
use App\CareerMapping;

interface CareerMappingRepository extends BaseRepository
{
    /**
     * Save CareerMapping detail passed in $carrerMappingDetail array
     */
    public function saveCareerMapping($carrerMappingDetail);
    
    public function getCareerMappingDetailsById($id);
}
