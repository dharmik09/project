<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use Config;

class ProfessionInstitutes extends Model
{
    protected $table = 'pro_pi_profession_institutes';

    protected $fillable = ['school_id','institute_state','college_institution','address_line1','address_line2','city','district','pin_code','website','year_of_establishment','affiliat_university','year_of_affiliation','location','latitude','longitude','institute_type','management','speciality','girl_exclusive','hostel_count','minimum_fee','maximum_fee','is_accredited','accreditation_body','is_institute_signup','deleted'];

    /**
     * Insert and Update Profession Institutes
     */
    public function insertUpdate($data)
    {
        if (isset($data['id']) && $data['id'] != '' && $data['id'] > 0) {
            return ProfessionInstitutes::where('id', $data['id'])->update($data);
        } else {
            return ProfessionInstitutes::create($data);
        }
    }

    /**
     * get all Profession Institutes
     */
    public function getAllProfessionInstitutes() {  
        $tags = ProfessionInstitutes::where('deleted', '<>', Config::get('constant.DELETED_FLAG'))->get();
        return $tags;
    }

    /**
     * get Profession Institutes details by Institutes Id
     */
    public function getProfessionInstitutesByInstitutesId($schoolId) {
        $tags = ProfessionInstitutes::where('school_id',$schoolId)->where('deleted', Config::get('constant.ACTIVE_FLAG'))->first();
        return $tags;
    }

    /**
     * Delete Profession Institutes
     */
    public function deleteProfessionInstitutesByProfessionId($id) {
        $return = ProfessionInstitutes::where('profession_id',$id)->delete();
        return $return;
    }
}
