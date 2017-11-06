<?php

namespace App\Services\Level4Activity\Contracts;
use App\Services\Repositories\BaseRepository;
use App\Services\Level4Activity\Entities\Level4Activity;

interface Level4ActivitiesRepository extends BaseRepository
{    
    public function getGamificationTemplateById($id);
    
    public function getAllLevel4AdvanceActivity();
    /*
     * Save Questions only and return inserted id
     */
    public function saveLevel4Question($array);

    /*
     * using inserted id of above question insert and store all options of this questions in answer table
     */
    public function saveLevel4Options($arrayOptions);
    
    public function getNotAttemptedActivities($teenagerId, $professionId);
    
    public function getNoOfTotalQuestionsAttemptedQuestion($teenagerId, $professionId);
    
    public function saveTeenagerActivityResponse($teenagerId, $professionId);
    
    public function checkQuestionRightOrWrong($questionID, $answerID);
    
    public function getProfessionIdFromQuestionId($questionID);

    public function getLevel4Details();

    //public function getActiveLevel4Activity($id);

    public function saveLevel4ActivityDetail($activity4Detail,$options,$radioval);

    public function deleteLevel4Activity($id);
}
