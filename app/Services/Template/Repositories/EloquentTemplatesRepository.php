<?php

namespace App\Services\Template\Repositories;

use DB;
use Config;
use App\Services\Template\Contracts\TemplatesRepository;
use App\Services\Repositories\Eloquent\EloquentBaseRepository;

class EloquentTemplatesRepository extends EloquentBaseRepository
implements TemplatesRepository
{
    /**
    * @return array of all the active Template
    Parameters
    @$searchParamArray : Array of Searching and Sorting parameters
    */
    public function getAllTemplates($searchParamArray = array())
    {
        $template = DB::table(config::get('databaseconstants.TBL_TEMPLATE'))
                    ->selectRaw('*')
                    ->where('deleted', '<>', Config::get('constant.DELETED_FLAG'))
                    ->get();
        return $template;
    }

    /**
    * @return Template details object
    Parameters
    @$templateDetail : Array of Template detail from front
    */
    public function saveTemplateDetail($templateDetail)
    {
        if($templateDetail['id'] != '' && $templateDetail['id'] > 0) {
            $return = $this->model->where('id', $templateDetail['id'])->update($templateDetail);
        } else {
            $return = $this->model->create($templateDetail);
        }
        return $return;
    }

    /**
    * @return Boolean True/False
    Parameters
    @$id : Tempalte ID
    */
    public function deleteTemplate($id)
    {
        $flag              = true;
        $template          = $this->model->find($id);
        $template->deleted = config::get('constant.DELETED_FLAG');
        $response          = $template->save();
        if($response) {
            return true;
        } else {
            return false;
        }
    }

    /*
    return : array of placeholder of email template
    */
    public  function getEmailTemplateDataByName($pseudoName) 
    {
        $data = $this->model->where('et_templatepseudoname', $pseudoName)->where('deleted','1')->first();
        return $data;
    }

    /*
    *change place holder with dynamic value
    */
    public  function getEmailContent($str , $arr) 
    {
        if (is_array($arr)) {
            reset($arr);
            $keys = array_keys($arr);
            array_walk($keys, create_function('&$val', '$val = "[$val]";'));
            $vals = array_values($arr);
            //return ereg_replace( "[([0-9A-Za-z\_\s\-]+)]", "", str_replace( $keys, $vals, $str));
            return preg_replace('/^[0-9a-zA-Z\/_\/s\/-]+/', '', str_replace($keys, $vals, $str));
        } else {
            return $str;
        }
    }
}
