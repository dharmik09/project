<?php

namespace App\Services\CareerMapping\Repositories;

use DB;
use Auth;
use Config;
use App\Services\CareerMapping\Contracts\CareerMappingRepository;
use App\Services\Repositories\Eloquent\EloquentBaseRepository;

class EloquentCareerMappingRepository extends EloquentBaseRepository implements CareerMappingRepository {

    public function saveCareerMapping($carrerMappingDetail) {
        $pf_id = $carrerMappingDetail['tcm_profession'];
        $checkForprofession = DB::table(config::get('databaseconstants.TBL_TEENAGER_CAREER_MAPPING'))->where('tcm_profession', $pf_id)->get();
        if (!empty($checkForprofession) && isset($checkForprofession)) {
            $id = $checkForprofession[0]->id;
            $return = $this->model->where('tcm_profession', $pf_id)->where('id', $id)->update($carrerMappingDetail);
        } else {
            $return = $this->model->create($carrerMappingDetail);
        }
        return $return;
    }

    public function getCareerMappingDetailsById($id) {
        $careerMappingDetails = DB::table(config::get('databaseconstants.TBL_TEENAGER_CAREER_MAPPING') . " AS mapping ")
                ->leftjoin(config::get('databaseconstants.TBL_PROFESSIONS') . " AS profession ", 'mapping.tcm_profession', '=', 'profession.id')
                ->selectRaw('mapping.*,profession.pf_name')
                ->where('mapping.id', $id)
                ->get();

        return $careerMappingDetails[0];
    }

}
