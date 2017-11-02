<?php

namespace App\Services\Sponsors\Contracts;
use App\Services\Repositories\BaseRepository;
use App\Sponsors;

interface SponsorsRepository extends BaseRepository
{    
    /**
     * @return array of all active sponsors in the application
     */
    public function getAllSponsors($searchParamArray = array());

    /**
     * Save Sponsor detail passed in $sponsorDetail array
     */
    public function saveSponsorDetail($sponsorDetail);



     /**
     * get entire detail related user
     */
    public function getSponsorById($id);

     /*
     return : array of Teenager detail by email id
     */
    public function getSponsorDetailByEmailId($email);

    /**
     * Delete Sponsor by $id
     */

    public function deleteSponsor($id);
    
    /**
     * Get Sponsor 
     */
    public function getApprovedSponsors();
    
    /**
     * Edit sponsor to Approve by $id
     */
    public function editToApprovedSponser($id);

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
       @$email : Sponsor's email
     */
    public function checkActiveEmailExist($email);

    /**
     * @return Boolean True/False
       Parameters
       @$phone : Sponsor's phone
     */
    public function checkActivePhoneExist($phone);

     /*
     * Generate otp
     */
    public function saveSponsorPasswordResetRequest($sponsor_id);

    /*
     return : array of Sponsor detail by email id
     */
   // public function getSponsorDetailByEmailId($email);

    /*
     * Verify the OTP against Sponsor ID and return Boolean TRUE / FALSE accordingly....
     */
    public function verifyOTPAgainstSponsorId($sponsorId, $OTP);

    public function getActiveSponsorActivityDetail($sponsorId);
    
    public function saveSponsorActivityDetail($activityDetail);
    
    public function getActivityById($id);
    
    public function inactiveRecord($id);
    
    public function checkForSponsorToTeen($id);
    
    public function checkForSponsorToCoupon($id);
    
    public function checkForSponsorToSponsorActivity($id);

}
