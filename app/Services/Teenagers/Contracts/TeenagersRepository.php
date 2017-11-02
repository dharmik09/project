<?php

namespace App\Services\Teenagers\Contracts;
use App\Services\Repositories\BaseRepository;
use App\Teenagers;

interface TeenagersRepository extends BaseRepository
{
    /**
     * @return array of all active teenagers in the application
     */
    public function getAllTeenagers($searchParamArray = array());

    public function getAllTeenagersData();

    /**
     * Save Teenager detail passed in $teenagerDetail array
     */
    public function saveTeenagerDetail($teenagerDetail);
                                           
    /**
     * Delete Teenager by $id
     */
    public function deleteTeenager($id);

      /**
     * @return Boolean True/False
       Parameters
       @$email : Teenager's email
     */
    public function checkActiveEmailExist($email);
    
    /**
     * @return Boolean True/False
       Parameters
       @$phone : Teenager's phone
     */
    public function checkActivePhoneExist($phone);
    
    /**
     * update device token
    */
    public function updateTeenagerDeviceToken($userid,$deviceDetail);
    
    /**
     * get user detail by mobile
    */
    public function getTeenagerByMobile($mobile);
    
    /**
     * get entire detail related user
     */
    public function getTeenagerById($id);
    
    /**
     * save  detail teenager sponsor
     */
    public function saveTeenagerSponserId($teenagerId,$sponsorId);
    
     /*
     return : array of Teenager detail by email id
     */
    public function getTeenagerDetailByEmailId($email);
   
     /*
     * Generate otp
     */
    public function saveTeenagerPasswordResetRequest($teenager_id);

    /*
     * Get userdetail by Social Identifier
     */
    public function getTeenagerBySocialId($socialId, $socialProvider);

    /*
     * Verify the OTP against Teenager ID and return Boolean TRUE / FALSE accordingly....
     */
    public function verifyOTPAgainstTeenagerId($teenagerId, $OTP);

    /*
     * Check the current password against Teenager ID and return Boolean TRUE / FALSE accordingly....
     */
    public function checkCurrentPasswordAgainstTeenager($teenagerId, $OTP);
    
    /*
    * get country id by country name....
    */
    public function getCountryIdByName($country);
    
    /*
    * get school id by school name....
    */
    public function getSchoolIdByName($school);
    
     /*
     * update teenager varify status by Token
     */
    public function updateTeenagerTokenStatusByToken($token);
    
     /*
     * update teenager varify status by Token
     */
    public function updateTeenagerVerifyStatusById($teenagerid);
    
    /*
     * add token for teenager varify status by teenagerToken detail
     */
    public function addTeenagerEmailVarifyToken($teenagerTokenDetail); 
    
     /**
     * @return Boolean True/False
       Parameters
       @$teenager id : Teenager's id
     */
    public function checkActiveTeenager($id);
    
    public function deleteTeenagerData($teenagerid);
    
    //Enter level record into tclr
    public function addTeenagerLevelCompleteRecord($teenagerId, $level, $timer);
    
    public function getActiveSchoolStudentsDetail($id);
    
    public function checkMailSentOrNot($userid);
    
    public function getEmailDataOfStudent($schoolid);
    
    public function getTeenDetailByParentId($id);

}
