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
    public function getAllLearningStyle() {
        $learningStyle = DB::table(config::get('databaseconstants.TBL_LEARNING_STYLE'))
                    ->selectRaw('*')
                    ->where('deleted', '<>', Config::get('constant.DELETED_FLAG'))
                    ->get();

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