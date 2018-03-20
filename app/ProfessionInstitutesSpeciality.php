<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use Config;

class ProfessionInstitutesSpeciality extends Model 
{

    protected $table = 'pro_pis_profession_institutes_speciality';

    protected $fillable = ['pis_name'];

    /**
     * Insert and Update Profession Institutes Speciality
     */
    public function insertUpdate($data)
    {
        if (isset($data['id']) && $data['id'] != '' && $data['id'] > 0) {
            return ProfessionInstitutesSpeciality::where('id', $data['id'])->update($data);
        } else {
            return ProfessionInstitutesSpeciality::create($data);
        }
    }

    /**
     * get all Profession Institutes Speciality
     */
    public function getAllProfessionInstitutesSpeciality() {  
        $tags = ProfessionInstitutesSpeciality::get();
        return $tags;
    }
}
