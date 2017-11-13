<?php

namespace App\Services\Schools\Contracts;
use App\Services\Repositories\BaseRepository;
use App\Schools;

interface SchoolsRepository extends BaseRepository
{
    /**
     * @return array of all active schools in the application
     */
    public function getAllSchools($searchParamArray = array(),$isExport=false);

     /**
     * Save School detail passed in $schoolDetail array
     */
    public function saveSchoolDetail($schoolDetail);

    /**
     * Delete School by $id
     */
    public function deleteSchool($id);

    /**
     * Edit school to Approve by $id
     */
    public function editToApprovedSchool($id);

    /*
     return : array of School detail by email id
     */
    public function getSchoolDetailByEmailId($email);

     /**
     * get entire detail related user
     */
    public function getSchoolById($id);

    /*
     * Generate otp
     */
    public function saveSchoolPasswordResetRequest($school_id);

    /**
     * Get School
     */
    public function getApprovedSchools();

     /*
    * get country id by country name....
    */
      public function getCountryIdByName($country);

    /*
    * get country id by state name....
    */

      public function getStateIdByName($state);
    /*
    * get country id by city name....
    */

      public function getCityIdByName($city);

      /**
     * @return Boolean True/False
       Parameters
       @$email : School's email
     */
    public function checkActiveEmailExist($email);

    /**
     * @return Boolean True/False
       Parameters
       @$phone : School's phone
     */
    public function checkActivePhoneExist($phone);


    /*
     * Verify the OTP against School ID and return Boolean TRUE / FALSE accordingly....
     */
    public function verifyOTPAgainstSchoolId($schoolId, $OTP);

    /*
     * Check the current password against Teenager ID and return Boolean TRUE / FALSE accordingly....
     */
    public function checkCurrentPasswordAgainstSchool($schoolId, $OTP);
    
    public function saveSchoolBulkDetail($schoolDetail);
    
    public function inactiveRecord($id,$status);
   
}
