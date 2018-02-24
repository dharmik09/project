<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use Config;

class ProfessionWiseCertification extends Model
{
    protected $table = 'pro_pwc_professions_wise_certificates';

    protected $fillable = ['profession_id', 'certificate_id', 'deleted'];

    /**
     * Insert and Update Profession Wise Certification
     */
    public function insertUpdate($data)
    {
        if (isset($data['id']) && $data['id'] != '' && $data['id'] > 0) {
            return ProfessionWiseCertification::where('id', $data['id'])->update($data);
        } else {
            return ProfessionWiseCertification::create($data);
        }
    }

    /**
     * get all Profession Wise Certification
     */
    public function getAllProfessionWiseCertification() {
        // $return = ProfessionWiseCertification::select('profession_id',DB::raw("(GROUP_CONCAT(certificate_id SEPARATOR ',')) as `certificate_id`"))->groupBy('profession_id')->where('deleted',Config::get('constant.ACTIVE_FLAG'))->get();
        $return = DB::table(config::get('databaseconstants.TBL_PROFESSIONS_WISE_CERTIFICATES'). " AS pwc")
                  ->join(config::get('databaseconstants.TBL_PROFESSIONS') . " AS profession", 'pwc.profession_id', '=', 'profession.id')
                  ->join(config::get('databaseconstants.TBL_PROFESSION_CERTIFICATIONS') . " AS certicate", 'pwc.certificate_id', '=', 'certicate.id')
                  ->selectRaw('pwc.id, pwc.profession_id, profession.pf_name as profession_name, GROUP_CONCAT(certicate.pc_name  SEPARATOR \', \') AS certificate_name')
                  ->where('pwc.deleted',Config::get('constant.ACTIVE_FLAG'))
                  ->groupBy('pwc.profession_id')
                  ->get();
        return $return;
    }

    /**
     * get Profession Wise Certification data By Id
     */
    public function getProfessionWiseCertificationByProfessionId($id) {
        $return = ProfessionWiseCertification::select('profession_id',DB::raw("(GROUP_CONCAT(certificate_id SEPARATOR ',')) as `certificate_id`"))->groupBy('profession_id')->where('profession_id',$id)->where('deleted',Config::get('constant.ACTIVE_FLAG'))->first();
                return $return;
    }

    /**
     * Delete Profession Wise Certification
     */
    public function deleteProfessionWiseCertificationByProfessionId($id) {
        $return = ProfessionWiseCertification::where('profession_id',$id)->delete();
        return $return;
    }

    public function certificate(){
        return $this->belongsTo(Certification::class, 'certificate_id');
    }


    /**
     * check Profession Wise Certificate exist
     */
    public function checkProfessionWiseCertificateByCertificateIdAndProfessionId($certificateId,$professionId) {
        $return = ProfessionWiseCertification::where('certificate_id',$certificateId)->where('profession_id',$professionId)->first();
        return $return;
    }
}
