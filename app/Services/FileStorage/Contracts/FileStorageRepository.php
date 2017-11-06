<?php

namespace App\Services\FileStorage\Contracts;

use App\Services\Repositories\BaseRepository;

interface FileStorageRepository extends BaseRepository
{
    /**
     * @return array of all active Blog Post in the application
    */
 	//public function addFileToStorage($fileName, $folderName, $file);
}
