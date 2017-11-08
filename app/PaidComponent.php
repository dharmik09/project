<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use Config;

class PaidComponent extends Model
{
    protected $table = 'pro_pc_paid_components';
//    protected $fillable = ['id', 'pc_element_name', 'pc_required_coins', 'pc_is_paid', 'pc_valid_upto','created_at','updated_at','deleted'];
    protected $guarded = [];
    
    public function getAllPaidComponents($searchParamArray) {
        $whereStr = '';
        $orderStr = '';

        $whereArray = [];
        $whereArray[] = 'deleted IN (1,2)';
        if (isset($searchParamArray) && !empty($searchParamArray)) {
            if (isset($searchParamArray['searchBy']) && isset($searchParamArray['searchText']) && $searchParamArray['searchBy'] != '' && $searchParamArray['searchText'] != '') {
                $whereArray[] = $searchParamArray['searchBy'] . " LIKE '%" . $searchParamArray['searchText'] . "%'";
            }

            if (isset($searchParamArray['orderBy']) && isset($searchParamArray['sortOrder']) && $searchParamArray['orderBy'] != '' && $searchParamArray['sortOrder'] != '') {
                $orderStr = " ORDER BY " . $searchParamArray['orderBy'] . " " . $searchParamArray['sortOrder'];
            }
        }

        if (!empty($whereArray)) {
            $whereStr = implode(" AND ", $whereArray);
        }

        $painComponents = DB::table(config::get('databaseconstants.TBL_PAID_COMPONENTS'))
                        ->selectRaw('*')
                        ->whereRaw($whereStr . $orderStr)
                        ->paginate(Config::get('constant.ADMIN_RECORD_PER_PAGE'));

        return $painComponents;
    }


    public function savePaidComponentsDetail($componentsDetail) {
       if ($componentsDetail['id'] != '0'){
          $this->where('id', $componentsDetail['id'])->update($componentsDetail);
       } else {
          $this->create($componentsDetail);
       }
         return '1';
    }

    public function deletePaidComponents($id) {
        $flag = true;
        $components = $this->find($id);
        $components->deleted = config::get('constant.DELETED_FLAG');
        $response = $components->save();
        if ($response) {
            return true;
        } else {
            return false;
        }
    }

    public function getRequredPointToViewData($name) {
        $result = DB::table(config::get('databaseconstants.TBL_PAID_COMPONENTS'))
                    ->selectRaw('pc_required_coins')
                    ->where('deleted',1)
                    ->where('pc_element_name', 'like', '%' . $name . '%')
                    ->get();
         if (count($result) > 0) {
           return $result[0]->pc_required_coins;
        } else {
            $result = '';
            return $result;
        }
    }

    public function getPaidComponentsData($name) {
        $result = DB::table(config::get('databaseconstants.TBL_PAID_COMPONENTS'))
                    ->selectRaw('*')
                    ->where('deleted',1)
                    ->where('pc_element_name', 'like', '%' . $name . '%')
                    ->get();
        return $result;
    }
}
