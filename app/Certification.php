<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use Config;

class Certification extends Model
{
    protected $table = 'pro_pc_profession_certifications';

    protected $guarded = [];
    
    public function getAllProfessionCertifications() {
        $certifications = Certification::selectRaw('*')->where('deleted', '<>', Config::get('constant.DELETED_FLAG'))->get();
        return $certifications;
    }

    public function getProfessionCertificationByName($name) {
        $certifications = Certification::where('pc_name', $name)->where('deleted', '<>', Config::get('constant.DELETED_FLAG'))->first();
        return $certifications;
    }


    public function saveProfessionCertificationDetail($certificationDetail) {
       if ($certificationDetail['id'] != '0') {
          $this->where('id', $certificationDetail['id'])->update($certificationDetail);
       } else {
          $this->create($certificationDetail);
       }
         return '1';
    }

    public function deleteCertification($id) {
        $flag = true;
        $certification = $this->find($id);
        $certification->deleted = config::get('constant.DELETED_FLAG');
        $response = $certification->save();
        if ($response) {
            return true;
        } else {
            return false;
        }
    }
}
