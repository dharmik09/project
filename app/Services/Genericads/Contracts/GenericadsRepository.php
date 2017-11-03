<?php

namespace App\Services\Genericads\Contracts;
use App\Services\Repositories\BaseRepository;
use App\Genericads;

interface GenericadsRepository extends BaseRepository
{
    public function getAllGeneric();
    
    public function saveGenericDetail($genericDetail);
    
    public function deleteGeneric($id);
}
