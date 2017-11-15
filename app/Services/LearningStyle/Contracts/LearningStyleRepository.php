<?php

namespace App\Services\LearningStyle\Contracts;
use App\Services\Repositories\BaseRepository;
use App\LearningStyle;

interface LearningStyleRepository extends BaseRepository
{
    /**
     * @return array of all active learing style in the application
     */
    public function getAllLearningStyle();

    /**
     * Save Learning Style detail passed in $learningStyleDetail array
     */
    public function saveLearningStyleDetail($learningStyleDetail);
}
