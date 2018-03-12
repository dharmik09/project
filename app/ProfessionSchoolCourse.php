<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use Config;

class ProfessionSchoolCourse extends Model
{
    protected $table = 'pro_psc_profession_school_course';

    protected $fillable = ['school_id','state','college_institution','address_line1','address_line2','city','district','pin_code','website','year_of_establishment','affiliat_university','year_of_affiliation','location','latitude','longitude','type','management','speciality','girl_exclusive','hostel_count','minimum_fee','maximum_fee','is_accredited','accreditation_body','deleted'];

    /**
     * Insert and Update Profession School Course
     */
    public function insertUpdate($data)
    {
        if (isset($data['id']) && $data['id'] != '' && $data['id'] > 0) {
            return ProfessionSchoolCourse::where('id', $data['id'])->update($data);
        } else {
            return ProfessionSchoolCourse::create($data);
        }
    }

    /**
     * get all Profession School Course
     */
    public function getAllProfessionSchool() {  
        $tags = ProfessionSchoolCourse::where('deleted', '<>', Config::get('constant.DELETED_FLAG'))->get();
        return $tags;
    }

    /**
     * get Profession School details by school Id
     */
    public function getProfessionSchoolBySchoolId($schoolId) {
        $tags = ProfessionSchoolCourse::where('school_id',$schoolId)->where('deleted', Config::get('constant.ACTIVE_FLAG'))->first();
        return $tags;
    }

    /**
     * Delete Profession School Course
     */
    public function deleteProfessionSchoolCourseByProfessionId($id) {
        $return = ProfessionSchoolCourse::where('profession_id',$id)->delete();
        return $return;
    }
}
