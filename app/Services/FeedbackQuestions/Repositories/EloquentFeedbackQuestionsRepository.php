<?php

namespace App\Services\FeedbackQuestions\Repositories;

use DB;
use Config;
use App\Services\FeedbackQuestions\Contracts\FeedbackQuestionsRepository;
use App\Services\Repositories\Eloquent\EloquentBaseRepository;


class EloquentFeedbackQuestionsRepository extends EloquentBaseRepository implements FeedbackQuestionsRepository {

    /**
     * @return array of all the active Feedback Questions
      Parameters
      @$searchParamArray : Array of Searching and Sorting parameters
     */
    public function getAllFeedbackQuestions($searchParamArray = array()) {
        $whereStr = '';
        $orderStr = '';

        $whereArray = [];
        $whereArray[] = 'deleted IN (1,2)';
        if (isset($searchParamArray) && !empty($searchParamArray))
        {
            if (isset($searchParamArray['searchBy']) && isset($searchParamArray['searchText']) && $searchParamArray['searchBy'] != '' && $searchParamArray['searchText'] != '') {
                $whereArray[] = $searchParamArray['searchBy'] . " LIKE '%" . $searchParamArray['searchText'] . "%'";
            }

            if (isset($searchParamArray['orderBy']) && isset($searchParamArray['sortOrder']) && $searchParamArray['orderBy'] != '' && $searchParamArray['sortOrder'] != '') {
                $orderStr = " ORDER BY " . $searchParamArray['orderBy'] . " " . $searchParamArray['sortOrder'];
            }
        }

        if(!empty($whereArray))
        {
            $whereStr = implode(" AND ", $whereArray);
        }

        $feedbackquestions = DB::table(config::get('databaseconstants.TBL_FEEDBACK_QUESTION'))
                          ->selectRaw('*')
                          ->whereRaw($whereStr . $orderStr)
                          ->paginate(Config::get('constant.ADMIN_RECORD_PER_PAGE'));
        return $feedbackquestions;
    }

    /**
     * @return Feedback Questions details object
      Parameters
      @$feedbackQuestionsDetail : Array of Feedback Questions detail from front
     */
    public function saveFeedbackQuestionsDetail($feedbackQuestionsDetail) {
        if (isset($feedbackQuestionsDetail['id']) && $feedbackQuestionsDetail['id'] != '' && $feedbackQuestionsDetail['id'] > 0)
        {
            $return = $this->model->where('id', $feedbackQuestionsDetail['id'])->update($feedbackQuestionsDetail);
        }
        else
        {
            $return = $this->model->create($feedbackQuestionsDetail);
        }

        return $return;
    }

    /**
     * @return Boolean True/False
      Parameters
      @$id : Feedback Question ID
     */
    public function deleteFeedbackQuestion($id) {
        $flag = true;
        $feedbackquestion = $this->model->find($id);
        $feedbackquestion->deleted = config::get('constant.DELETED_FLAG');
        $response = $feedbackquestion->save();
        if ($response)
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    
    public function getAllHints()
    {
        $result = DB::table('hint')
                    ->selectRaw('*')
                    ->where('deleted','<>', Config::get('constant.DELETED_FLAG'))
                    ->get();
        return $result;
         
    }
        
    /**
     * @return Hint details object
      Parameters
      @$hintDetail : Array of hint detail from front
     */
    public function saveHint($saveData) {
        if ($saveData['id'] != '' && $saveData['id'] > 0) {
            $return = DB::table('hint')->where('id', $saveData['id'])->update($saveData);
        } else {
            $return = DB::table('hint')->insert($saveData);
        }

        return $return;
    }
    
    public function getHintById($id)
    {
        $result = DB::table('hint')->where('id',$id)->first();
        return $result; 
    }
    
    /**
     * @return Boolean True/False
      Parameters
      @$id : Hint ID
     */
    public function deletehint($id) {
        $saveData['deleted'] = config::get('constant.DELETED_FLAG');
        $return = DB::table('hint')->where('id', $id)->update($saveData);
        if ($return) {
            return true;
        } else {
            return false;
        }
    }

}
