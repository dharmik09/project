<?php

namespace App\Services\FeedbackQuestions\Contracts;
use App\Services\Repositories\BaseRepository;
use App\FeedbackQuestions;

interface FeedbackQuestionsRepository extends BaseRepository
{    
    /**
     * @return array of all Feedback Questions in the application
     */
    public function getAllFeedbackQuestions($searchParamArray = array());

    /**
     * Save Feedback Question detail passed in $feedbackQuestionsDetail array
     */
    public function saveFeedbackQuestionsDetail($feedbackQuestionsDetail);

    /**
     * Delete Feedback Question by $id
     */
    public function deleteFeedbackQuestion($id);

}
