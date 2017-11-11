<?php

namespace App\Services\Reports\Contracts;
use App\Services\Repositories\BaseRepository;
use App\Reports;

interface ReportsRepository extends BaseRepository
{
    /**
     * @return array of all level1 question and answare
     */
    public function getAlllevel1data();

    
 }
