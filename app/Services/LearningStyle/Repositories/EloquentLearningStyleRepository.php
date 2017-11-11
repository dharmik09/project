<?php

namespace App\Services\LearningStyle\Repositories;

use DB;
use Auth;
use Config;
use App\Services\LearningStyle\Contracts\LearningStyleRepository;
use App\Services\Repositories\Eloquent\EloquentBaseRepository;

class EloquentLearningStyleRepository extends EloquentBaseRepository implements LearningStyleRepository {

    /**
     * @return array of all the active leraning style
      Parameters
      @$searchParamArray : Array of Searching and Sorting parameters
     */
    public function getAllLearningStyle($searchParamArray = array()) {
        $whereStr = '';
        $orderStr = '';

        $whereArray = [];
        $whereArray[] = 'deleted IN (1,2)';
        $whereArray[] = 'ls_name != "" ';
        if (isset($searchParamArray) && !empty($searchParamArray)) {
            if (isset($searchParamArray['searchBy']) && isset($searchParamArray['searchText']) && $searchParamArray['searchBy'] != '' && $searchParamArray['searchText'] != '') {
                $whereArray[] = $searchParamArray['searchBy'] . " LIKE '%" . $searchParamArray['searchText'] . "%'";
            }

            if (isset($searchParamArray['orderBy']) && isset($searchParamArray['sortOrder']) && $searchParamArray['orderBy'] != '' && $searchParamArray['sortOrder'] != '') {
                $orderStr = " ORDER BY " . $searchParamArray['orderBy'] . " " . $searchParamArray['sortOrder'];
            }
        }

        if (!empty($whereArray)) {
            $whereStr = implode(" AND ", $whereArray);
        }

        $learningStyle = DB::table(config::get('databaseconstants.TBL_LEARNING_STYLE'))
                    ->selectRaw('*')
                    ->whereRaw($whereStr . $orderStr)
                    ->paginate(Config::get('constant.ADMIN_RECORD_PER_PAGE'));

        return $learningStyle;
    }

    public function saveLearningStyleDetail($learningStyleDetail) {
        if (isset($learningStyleDetail['id']) && $learningStyleDetail['id'] != '' && $learningStyleDetail['id'] > 0) {
            $returnUpdate = $this->model->where('id', $learningStyleDetail['id'])->update($learningStyleDetail);
            $return = $this->model->where('id', $learningStyleDetail['id'])->first();
        } else {
            $return = $this->model->create($learningStyleDetail);
        }
        return $return;
    }
}