<?php

namespace App\Services\Parents\Contracts;
use App\Services\Repositories\BaseRepository;
use App\Parents;

interface ParentsRepository extends BaseRepository
{
    /**
     * @return array of all active parents in the application
     */
    public function getAllParents($searchParamArray = array(),$type);

    /**
     * Save Parent detail passed in $parentDetail array
     */
    public function saveParentDetail($parentDetail);



     /**
     * get entire detail related user
     */
    public function getParentById($id);


    /**
     * Save parent password
     */

    public function saveParentPasswordResetRequest($resetRequest);




    /*
     return : array of Parent detail by email id
     */

    public function getParentDetailByEmailId($id);

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
     * Parameter $parentId : Parent ID from provider
     * Parameter $OTP : One Time Password
     * return : Boolean TRUE / FALSE
    */
    public function verifyOTPAgainstParentId($parentId, $OTP);

    /*
    * check current parent password
    */
    public function checkCurrentPasswordAgainstParent($parentId, $currentPassword);

    /**
     * @return Boolean True/False
       Parameters
       @$email : Parent's email
     */
    public function checkActiveEmailExist($email);

      /*
     * update parent varify status by Token
     */
    public function updateParentTokenStatusByToken($token);

     /**
     * Parameter $parentId: Parent ID and token
     * add parent token for varify parent
     */
    public function addParentEmailVarifyToken($parentTokenDetail);

     /*
     * update parent varify status by uniqueid
     */
    public function updateParentVerifyStatusById($parentid);

    /*
    * check parent is active nor not
    */
    public function checkActiveParent($id);

   /**
    * delate parent by id
    */
    public function deleteParent($id,$type);
}
