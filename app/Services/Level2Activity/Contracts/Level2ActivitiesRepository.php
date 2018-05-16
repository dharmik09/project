<?php

namespace App\Services\Level2Activity\Contracts;
use App\Services\Repositories\BaseRepository;
use App\Level2Activity;

interface Level2ActivitiesRepository extends BaseRepository
{    
    /**     
     * @return array of all active activities in the application
     */
    public function getAllLeve2Activities();
    

    public function saveLevel2ActivityDetail($activityDetail, $option,$radioval);
    
    /**
     * Delete Level1 Activity by $id
     */
    public function deleteLevel2Activity($id);
    
    public function getLevel2ActivityWithAnswer($id, $sectionType);
    
    public function getTotalTimeForAttemptedQuestion($teenagerId);
}
