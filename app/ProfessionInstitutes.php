<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use Config;

class ProfessionInstitutes extends Model
{
    protected $table = 'pro_pi_profession_institutes';

    protected $fillable = ['school_id','institute_state','college_institution','address_line1','address_line2','city','district','pin_code','website','year_of_establishment','affiliat_university','year_of_affiliation','location','latitude','longitude','institute_type','autonomous','management','speciality','girl_exclusive','hostel_count','minimum_fee','maximum_fee','accreditation_score','accreditation_body','is_institute_signup','deleted'];

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
     * get all Profession Institutes for list Ajax
     */
    public function getAllProfessionInstitutesForAjax() {  
        $tags = ProfessionInstitutes::where('deleted', '<>', Config::get('constant.DELETED_FLAG'));
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

    /**
     * get Profession Institutes page wise
     */
    public function getProfessionInstitutesByPage($record) {
        $return = ProfessionInstitutes::skip($record)->take(5)->get();
        return $return;
    }

    /**
     * get Profession Institutes unique AffiliatUniversity
     */
    public function getProfessionInstitutesUniqueAffiliatUniversity() {
        $return = ProfessionInstitutes::groupBy('affiliat_university')->where('affiliat_university','<>',NULL)->get();
        return $return;
    }
        
    /**
     * get Profession Institutes unique Managaement
     */
    public function getProfessionInstitutesUniqueManagement() {
        $return = ProfessionInstitutes::groupBy('management')->where('management','<>',NULL)->get();
        return $return;
    }

    /**
     * get Profession Institutes unique AccreditationBody
     */
    public function getProfessionInstitutesUniqueAccreditationBody() {
        $return = ProfessionInstitutes::groupBy('accreditation_body')->where('accreditation_body','<>',NULL)->get();
        return $return;
    }

    /**
     * get Profession Institutes unique MinimumFee
     */
    public function getProfessionInstitutesUniqueMinimumFee() {
        $return = ProfessionInstitutes::groupBy('minimum_fee')->orderBy('minimum_fee','asc')->where('minimum_fee','<>',NULL)->get();
        return $return;
    }

    /**
     * get Profession Institutes unique MaximumFee
     */
    public function getProfessionInstitutesUniqueMaximumFee() {
        $return = ProfessionInstitutes::groupBy('maximum_fee')->orderBy('maximum_fee','asc')->where('maximum_fee','<>',NULL)->get();
        return $return;
    }

    /**
     * get Profession Institutes get page wise and filter
     */
    public function getProfessionInstitutesWithFilter($answerName, $questionType, $answer, $record) {
        $return = ProfessionInstitutes::skip($record)->take(5);
        
        if(isset($answerName) && $answerName != ""){
            $return->where('college_institution','like','%'.$answerName.'%');
        }

        if($questionType == "Institute_Affiliation" && $answer != ""){
            $return = $return->where('affiliat_university',$answer)->get();
        }
        elseif($questionType == "Speciality"  && $answer != ""){
            $return = $return->where('speciality','like','%'.$answer.'%')->get();
        }
        elseif($questionType == "State"  && $answer != ""){
            $return = $return->where('institute_state','like', '%'.$answer.'%')->get();
        }
        elseif($questionType == "City"  && $answer != ""){
            $return = $return->where('city','like', '%'.$answer.'%')->get();
        }
        elseif($questionType == "Pincode"  && $answer != ""){
            $return = $return->where('pin_code','like', '%'.$answer.'%')->get();
        }
        elseif($questionType == "Management_Category"  && $answer != ""){
            $return = $return->where('management',$answer)->get();
        }
        elseif($questionType == "Accreditation"  && $answer != ""){
            $return = $return->where('accreditation_body',$answer)->get();
        }
        elseif($questionType == "Hostel"  && $answer != ""){
            if($answer == "0"){
                $return = $return->where('hostel_count',"0")->get();
            }
            else{
                $return = $return->where('hostel_count',"<>","0")->get();
            }
        }
        elseif($questionType == "Gender" && $answer != ""){
            if($answer == "0"){
                $return = $return->where('girl_exclusive',$answer)->get();
            }
            else{
                $return = $return->where('girl_exclusive',$answer)->get();
            }
        }
        elseif($questionType == "Fees" && $answer != ""){
            
            $feesArray = explode("#", $answer);

            $fees['minimumFees'] = (isset($feesArray[0]) && !empty($feesArray[0])) ? $feesArray[0] : '';
            $fees['maximumFees'] = (isset($feesArray[1]) && !empty($feesArray[1])) ? $feesArray[1] : '';

            
            if(isset($fees['minimumFees']) && empty($fees['maximumFees']) || $fees['maximumFees'] == 'null'){
                $return = $return->where('minimum_fee','>=',$fees['minimumFees'])->get();
            }
            elseif(isset($fees['maximumFees']) && empty($fees['minimumFees']) || $fees['minimumFees'] == 'null'){        
                $return = $return->where('maximum_fee','<=',$fees['maximumFees'])->get();
            }
            else{
                $return = $return->where('minimum_fee','>=',$fees['minimumFees'])->where('maximum_fee','<=',$fees['maximumFees'])->get();
            }
        }
        else{
            $return = $return->get();
        }

        return $return;
    }

}
