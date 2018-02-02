<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use Config;

class ProfessionSubject extends Model
{
    protected $table = 'pro_ps_profession_subjects';

    protected $guarded = [];
    
    public function getAllProfessionSubjects() {
        $subjects = ProfessionSubject::selectRaw('*')->where('deleted', '<>', Config::get('constant.DELETED_FLAG'))->get();
        return $subjects;
    }


    public function saveProfessionSubjectDetail($subjectDetail) {
       if ($subjectDetail['id'] != '0') {
          $this->where('id', $subjectDetail['id'])->update($subjectDetail);
       } else {
          $this->create($subjectDetail);
       }
         return '1';
    }

    public function deleteSubject($id) {
        $flag = true;
        $subject = $this->find($id);
        $subject->deleted = config::get('constant.DELETED_FLAG');
        $response = $subject->save();
        if ($response) {
            return true;
        } else {
            return false;
        }
    }

    public function getProfessionSubjectByName($name) {
        $certifications = $this->where('ps_name', $name)->where('deleted', '<>', Config::get('constant.DELETED_FLAG'))->first();
        return $certifications;
    }

    /**
     * Returns subject details array
     */
    public function getSubjectDetailsBySlug($slug)
    {
        $subject = $this->where('ps_slug', $slug)->where('deleted', '<>', Config::get('constant.DELETED_FLAG'))->first();
        return $subject;
    }
}
