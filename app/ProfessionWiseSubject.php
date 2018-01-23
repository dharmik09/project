<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use Config;

class ProfessionWiseSubject extends Model
{
    protected $table = 'pro_pws_professions_wise_subjects';

    protected $fillable = ['profession_id', 'subject_id', 'parameter_grade', 'deleted'];

    /**
     * Insert and Update Profession Wise Subject
     */
    public function insertUpdate($data)
    {
        if (isset($data['id']) && $data['id'] != '' && $data['id'] > 0) {
            return ProfessionWiseSubject::where('id', $data['id'])->update($data);
        } else {
            return ProfessionWiseSubject::create($data);
        }
    }

    /**
     * get all Profession Wise Subject
     */
    public function getAllProfessionWiseSubject() {
        $return = DB::table(config::get('databaseconstants.TBL_PROFESSIONS_WISE_SUBJECT'). " AS pws")
                  ->join(config::get('databaseconstants.TBL_PROFESSIONS') . " AS profession", 'pws.profession_id', '=', 'profession.id')
                  ->join(config::get('databaseconstants.TBL_PROFESSION_SUBJECT') . " AS subject", 'pws.subject_id', '=', 'subject.id')
                  ->selectRaw('pws.id,  GROUP_CONCAT(pws.parameter_grade  SEPARATOR \', \') AS parameter_grade, pws.profession_id, profession.pf_name as profession_name, GROUP_CONCAT(subject.ps_name  SEPARATOR \', \') AS subject_name')
                  ->where('pws.deleted',Config::get('constant.ACTIVE_FLAG'))
                  ->groupBy('pws.profession_id')
                  ->get();
        return $return;
    }

    /**
     * get Profession Wise Subject data By Id
     */
    public function getProfessionWiseSubjectByProfessionId($id) {
        $return = ProfessionWiseSubject::select('profession_id',DB::raw("(GROUP_CONCAT( DISTINCT CONCAT(subject_id,'_',parameter_grade) SEPARATOR ',')) as `subject_id`"))->groupBy('profession_id')->where('profession_id',$id)->where('deleted',Config::get('constant.ACTIVE_FLAG'))->first();
                return $return;
    }

    /**
     * Delete Profession Wise Subject
     */
    public function deleteProfessionWiseSubjectByProfessionId($id) {
        $return = ProfessionWiseSubject::where('profession_id',$id)->delete();
        return $return;
    }

    public function subject(){
        return $this->belongsTo(ProfessionSubject::class, 'subject_id');
    }
}
