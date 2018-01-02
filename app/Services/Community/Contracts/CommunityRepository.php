<?php

namespace App\Services\Community\Contracts;
use App\Services\Repositories\BaseRepository;
use App\Community;

interface CommunityRepository extends BaseRepository
{
    /**
     * @return array of all the new connections
       Parameters
       @$searchParamArray : Array of Searching and Sorting parameters
     */
    public function getNewConnections($loggedInTeen);
}
