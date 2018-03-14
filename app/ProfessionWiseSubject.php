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
     * check Profession Wise Subject exist
     */
    public function checkProfessionWiseSubjectBySubjectIdAndProfessionId($subjectId,$professionId) {
        $return = ProfessionWiseSubject::where('subject_id',$subjectId)->where('profession_id',$professionId)->first();
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

    /**
     * Returns professions details array which are matched with subject slug
     */
    public function getProfessionsBySubjectSlug($slug, $lastCareerId = '')
    {
        $professionDetails = $this->join(Config::get('databaseconstants.TBL_PROFESSIONS') . " AS profession", 'pro_pws_professions_wise_subjects.profession_id', '=', 'profession.id')
                    ->join("pro_ps_profession_subjects AS profession_subject", 'pro_pws_professions_wise_subjects.subject_id', '=', 'profession_subject.id')
                    ->selectRaw('profession.*, profession_subject.ps_name, profession_subject.ps_slug, profession_subject.ps_image')
                    ->where('profession_subject.ps_slug', $slug)
                    ->where('profession.deleted', Config::get('constant.ACTIVE_FLAG'))
                    ->where('profession_subject.deleted', Config::get('constant.ACTIVE_FLAG'))
                    ->whereIn('pro_pws_professions_wise_subjects.parameter_grade', ['M','H'])
                    ->where(function($query) use ($lastCareerId)  {
                        if(isset($lastCareerId) && !empty($lastCareerId)) {
                            $query->where('profession.id', '<', $lastCareerId);
                        }
                     })
                    ->orderBy('profession.id', 'DESC')
                    ->limit(Config::get('constant.RECORD_PER_PAGE'))
                    ->get();
        return $professionDetails;
    }

    /**
     * Returns count of professions which are matched with subject slug
     */
    public function getProfessionsCountBySubjectSlug($slug, $lastCareerId = '')
    {
        $professionDetails = $this->join(Config::get('databaseconstants.TBL_PROFESSIONS') . " AS profession", 'pro_pws_professions_wise_subjects.profession_id', '=', 'profession.id')
                    ->join("pro_ps_profession_subjects AS profession_subject", 'pro_pws_professions_wise_subjects.subject_id', '=', 'profession_subject.id')
                    ->selectRaw('profession.*, profession_subject.ps_name, profession_subject.ps_slug, profession_subject.ps_image')
                    ->where('profession_subject.ps_slug', $slug)
                    ->where('profession.deleted', Config::get('constant.ACTIVE_FLAG'))
                    ->where('profession_subject.deleted', Config::get('constant.ACTIVE_FLAG'))
                    ->whereIn('pro_pws_professions_wise_subjects.parameter_grade', ['M','H'])
                    ->where(function($query) use ($lastCareerId)  {
                        if(isset($lastCareerId) && !empty($lastCareerId)) {
                            $query->where('profession.id', '<', $lastCareerId);
                        }
                     })
                    ->orderBy('profession.id', 'DESC')
                    ->count();
        return $professionDetails;
    }

}
