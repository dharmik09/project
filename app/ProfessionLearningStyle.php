<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Config;
use DB;

class ProfessionLearningStyle extends Model{
    protected $table = 'pro_pls_profession_learning_style';

//  protected $fillable = ['id','pls_profession_id','pls_parameter_id','pls_activity_name','deleted'];
    protected $guarded = [];

    public function saveProfessionLearningStyle($learningStyleData) {

        $data = DB::table(config::get('databaseconstants.TBL_PROFESSION_LEARNING_STYLE'))->where('pls_profession_id', $learningStyleData['pls_profession_id'])->where('pls_parameter_id', $learningStyleData['pls_parameter_id'])->where('deleted', '1')->first();
        if (count($data) > 0) {
            $return = DB::table(config::get('databaseconstants.TBL_PROFESSION_LEARNING_STYLE'))->where('pls_profession_id', $learningStyleData['pls_profession_id'])->where('pls_parameter_id', $learningStyleData['pls_parameter_id'])->update($learningStyleData);
        } else {
            $return = DB::table(config::get('databaseconstants.TBL_PROFESSION_LEARNING_STYLE'))->insert($learningStyleData);
        }
        return $return;
    }

    public function getAllProfessionLearningStyle($searchParamArray) {
        $whereStr = '';
        $orderStr = '';

        $whereArray = [];
        $whereArray[] = 'LearningStyle.deleted IN (1,2)';
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

        $learningStyle = DB::table(config::get('databaseconstants.TBL_PROFESSION_LEARNING_STYLE'). " AS LearningStyle")
                        ->leftjoin(config::get('databaseconstants.TBL_PROFESSIONS') . " AS profession", 'profession.id', '=', 'LearningStyle.pls_profession_id')
                        ->selectRaw('LearningStyle.*, profession.pf_name, GROUP_CONCAT(LearningStyle.pls_activity_name ORDER BY LearningStyle.id ASC SEPARATOR "##") AS activity_name')
                        ->whereRaw($whereStr . $orderStr)
                        ->groupBy('LearningStyle.pls_profession_id')
                        ->paginate(Config::get('constant.ADMIN_RECORD_PER_PAGE'));

        return $learningStyle;
    }

    public function getLearningStyleDetailsById($id) {
        $learningStyle = DB::table(config::get('databaseconstants.TBL_PROFESSION_LEARNING_STYLE'). " AS LearningStyle")
                        ->leftjoin(config::get('databaseconstants.TBL_PROFESSIONS') . " AS profession", 'profession.id', '=', 'LearningStyle.pls_profession_id')
                        ->leftjoin(config::get('databaseconstants.TBL_LEARNING_STYLE') . " AS LS", 'LS.id', '=', 'LearningStyle.pls_parameter_id')
                        ->selectRaw('LearningStyle.*, profession.pf_name, LS.ls_name')
                        ->where('LearningStyle.pls_profession_id', $id)
                        ->where('LearningStyle.deleted','=',1)
                        ->get();

        return $learningStyle;
    }

    public function getIdByProfessionId($professionId,$templateId) {
        $result = DB::table(config::get('databaseconstants.TBL_PROFESSION_LEARNING_STYLE'))
                    ->selectRaw('id,pls_activity_name')
                    ->where('pls_profession_id',$professionId)
                    ->where('pls_activity_name', 'like', '%' . $templateId . '%')
                    ->get();
        $tempId = '';
        if (count($result) > 0) {
            foreach ($result as $value) {
                $id = explode(",",$value->pls_activity_name);
                foreach ($id As $key => $val) {
                    if ($val == $templateId) {
                        $tempId = $value->id;
                    }
                }
            }
           return $tempId;
        } else {
            $result = '';
            return $result;
        }
    }

    public function getIdByProfessionIdForAdvance($professionId,$templateId) {
       $result = DB::table(config::get('databaseconstants.TBL_PROFESSION_LEARNING_STYLE'))
                    ->selectRaw('id')
                    ->where('pls_profession_id',$professionId)
                    ->where('pls_activity_name', 'like', '%' . $templateId . '%')
                    ->get();
        if (count($result) > 0) {
           return $result[0]->id;
        } else {
            $result = '';
            return $result;
        }
    }

    public function getLearningStyleDetailsByProfessionId($pId) {
        $learningStyle = DB::select( DB::raw("SELECT GROUP_CONCAT(id) AS id ,GROUP_CONCAT(pls_parameter_id) AS pls_parameter_id,GROUP_CONCAT(pls_activity_name SEPARATOR '#') AS pls_activity_name,pls_profession_id
                                          FROM  " . config::get('databaseconstants.TBL_PROFESSION_LEARNING_STYLE') . "
                                           where deleted = 1 and pls_profession_id =". $pId ." group by pls_profession_id"));
        if (count($learningStyle) > 0) {
            return $learningStyle;
        } else {
            $result = '';
            return $result;
        }
    }
}