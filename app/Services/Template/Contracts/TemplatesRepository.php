<?php

namespace App\Services\Template\Contracts;
use App\Services\Repositories\BaseRepository;
use App\Templates;

interface TemplatesRepository extends BaseRepository
{    
    /**     
     * @return array of all active Templates in the application
     */
    public function getAllTemplates($searchParamArray = array());

    /**
     * Save Parent detail passed in $templateDetail array
    */
    public function saveTemplateDetail($templateDetail);


    /**
     * Delete Template by $id
    */
    public function deleteTemplate($id);
    
     /*
     return : array of placeholder of email template
     */
    public  function getEmailTemplateDataByName($pseudoName);
            
    /*
     *change place holder with dynamic value
     */
    public function  getEmailContent($str , $arr);

}
