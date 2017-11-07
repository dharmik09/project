<?php

namespace App\Services\ProfessionHeaders\Repositories;

use DB;
use Config;
use App\ProfessionHeaders;
use App\Services\ProfessionHeaders\Contracts\ProfessionHeadersRepository;
use App\Services\Repositories\Eloquent\EloquentBaseRepository;

class EloquentProfessionHeadersRepository extends EloquentBaseRepository
implements ProfessionHeadersRepository
{
    /**
    * @return array of all the active Profession Headers
    Parameters
    @$searchParamArray : Array of Searching and Sorting parameters
    */

    public function getAllProfessionHeaders()
    {
        $headers = DB::table(config::get('databaseconstants.TBL_PROFESSION_HEADER'). " AS header ")
                  ->join(config::get('databaseconstants.TBL_PROFESSIONS') . " AS profession ", 'header.pfic_profession', '=', 'profession.id')
                  ->selectRaw('header.* ,profession.pf_name, GROUP_CONCAT(header.pfic_title) AS pfic_title')
                  ->groupBy('header.pfic_profession')
                  ->get();
        return $headers;
    }

    /**
    * @return Profession Header details object
    Parameters
    @$headerDetail : Array of Profession Header detail from front
    */
    public function saveProfessionHeaderDetail($headerDetail,$headerTitle,$headerContent)
    {
        $objHeader = new ProfessionHeaders();
        $return = '';
        $data = $objHeader->where('pfic_profession',$headerDetail['pfic_profession'] )->get();
        $j = 0;
        for ($i = 0 ; $i< count($headerTitle); ++$i)
        {
            $headerDetail['pfic_title'] = $headerTitle[$i];
            $headerDetail['pfic_content'] = $headerContent[$i];
            if($j < count($data))
            {
                if($headerDetail['id'] != '' && $headerDetail['id'] > 0)
                {
                    $headerDetail['id'] = $data[$i]['id'];
                    $return = $this->model->where('id', $data[$i]['id'])->update($headerDetail);
                }
                $j++;
            }
            else
            {
                $headerDetail['id'] = '';
                $return = $this->model->create($headerDetail);
            }
        }
        return $return;
    }

    public function saveProfessionHeaderFromAdmin($headerData){
        if(isset($headerData) && !empty($headerData)) {
            foreach($headerData['pfic_title'] as $key=>$val) {
                $headerDetail['pfic_title'] = $headerData['pfic_title'][$key];
                $headerDetail['pfic_content'] = $headerData['pfic_content'][$key];
                $headerDetail['pfic_profession'] = ($headerData['id'] > 0)?$headerData['pfic_profession_id']:$headerData['pfic_profession'];
                if($headerData['id'] != '' && $headerData['id'] > 0) {
                   $headerDetail['pfic_profession'] = $headerData['pfic_profession_id'];
                   $return = $this->model->where('id', $key)->update($headerDetail); 
                } else{
                   $headerDetail['pfic_profession'] = $headerData['pfic_profession'];
                   $return = $this->model->create($headerDetail);
                }
            } 
        }
        return $return;
    }

    /**
    * @return Boolean True/False
    Parameters
    @$id : Profession Header ID
    */
    public function deleteProfessionHeader($id)
    {
        $objHeader = new ProfessionHeaders();
        $flag              = true;
        $response = $objHeader->where('pfic_profession',$id)->delete();
        if($response != 0)
        {
            return true;
        }
        else
        {
            return false;
        }
    }
}
