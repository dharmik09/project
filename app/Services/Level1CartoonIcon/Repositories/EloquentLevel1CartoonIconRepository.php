<?php

namespace App\Services\Level1CartoonIcon\Repositories;

use DB;
use Config;
use App\Services\Level1CartoonIcon\Contracts\Level1CartoonIconRepository;
use App\Level1CartoonIconCategory;
use App\Level1CartoonIconProfessions;
use App\Services\Repositories\Eloquent\EloquentBaseRepository;
use File;

class EloquentLevel1CartoonIconRepository extends EloquentBaseRepository implements Level1CartoonIconRepository {

    /**
     * @return array of all the active cartoons
      Parameters
      @$searchParamArray : Array of Searching and Sorting parameters
     */
    public function getLeve1CartoonIcon() {
        $cartoonIcon = DB::table(config::get('databaseconstants.TBL_LEVEL1_CARTOON_ICON'). " AS cartoon")
                ->leftjoin(config::get('databaseconstants.TBL_CARTOON_ICON_CATEGORY') . " AS ccategory", 'cartoon.ci_category', '=', 'ccategory.id')
                ->selectRaw('cartoon.*,ccategory.cic_name')
                ->where('cartoon.deleted', '<>', Config::get('constant.DELETED_FLAG'))
                ->get();
        return $cartoonIcon;
    }

    /**
     * @return Cartoons details object
      Parameters
      @$cartoonIconDetail : Array of cartoons detail from front
     */
    public function saveLevel1CartoonIconDetail($cartoonIconDetail, $professions = array()) {
        if (isset($cartoonIconDetail['id']) && $cartoonIconDetail['id'] != '' && $cartoonIconDetail['id'] > 0) {
            $return = $this->model->where('id', $cartoonIconDetail['id'])->update($cartoonIconDetail);
        } else {
            $return = $this->model->create($cartoonIconDetail);
        }
        $Level1CartoonIconPro = new Level1CartoonIconProfessions();
        $countPro = count($professions);
        /* $id = $return->id;
          for($i = 0; $i < $countPro; $i++)
          {
          $carProMapping = [];
          $carProMapping['cpm_cartoon_id'] = $id;
          $carProMapping['cpm_profession_id'] = $professions[$i];
          $return = $Level1CartoonIconPro->create($carProMapping);
          } */
        if ($countPro > 0) {
            if ($return) {
                if ($cartoonIconDetail['id'] != '' && $cartoonIconDetail['id'] > 0) {
                    $id = $cartoonIconDetail['id'];
                } else {
                    $id = $return->id;
                }
            }
            $professionData = $Level1CartoonIconPro->where('cpm_cartoon_id', $cartoonIconDetail['id'])->get();
            $professionDataLength = count($professionData);
            $noOfRow = 0;

            if ($countPro >= $professionDataLength) {
                for ($i = 0; $i < $countPro; ++$i) {
                    $professionDetail = [];
                    $professionDetail['cpm_cartoon_id'] = $id;
                    $professionDetail['cpm_profession_id'] = $professions[$i];
                    if ($noOfRow < $professionDataLength) {
                        if ($cartoonIconDetail['id'] != '' && $cartoonIconDetail['id'] > 0) {
                            $result = $Level1CartoonIconPro->where('id', $professionData[$i]->id)->update($professionDetail);
                        }
                        $noOfRow++;
                    } else {
                        $result = $Level1CartoonIconPro->create($professionDetail);
                    }
                }
            } else {
                for ($i = 0; $i < $professionDataLength; ++$i) {
                    for ($i = 0; $i < $countPro; ++$i) {
                        $professionDetail = [];
                        $professionDetail['cpm_cartoon_id'] = $id;
                        $professionDetail['cpm_profession_id'] = $professions[$i];

                        if ($noOfRow < $professionDataLength - 1) {
                            if ($cartoonIconDetail['id'] != '' && $cartoonIconDetail['id'] > 0) {
                                $result = $Level1CartoonIconPro->where('id', $professionData[$i]->id)->update($professionDetail);
                            }
                            $noOfRow++;
                        }
                    }
                    $result = $Level1CartoonIconPro->where('id', $professionData[$i]->id)->delete($professionDetail);
                }
            }
        }

        return $return;
    }

    /**
     * @return Boolean True/False
      Parameters
      @$id : Cartoon ID
     */
    public function deleteLevel1Cartoon($id) {

        $flag = true;
        $cartoon = $this->model->find($id);
        $cartoon->deleted = config::get('constant.DELETED_FLAG');
        $response = $cartoon->save();
        if ($response) {
            return true;
        } else {
            return false;
        }
    }

    public function getLeve1CartoonIconCategory($searchParamArray = array()) {
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
        
        $cartoonIconCategory = DB::table(config::get('databaseconstants.TBL_CARTOON_ICON_CATEGORY'))
                ->selectRaw('*')
                ->whereRaw($whereStr . $orderStr)
                ->paginate(Config::get('constant.ADMIN_RECORD_PER_PAGE'));
        return $cartoonIconCategory;
    }

    /**
     * @return Human Icons details object
      Parameters
      @$humanIconCategoryDetail : Array of Human Icons detail category from front
     */
    public function saveLevel1CartoonIconCategoryDetail($cartoonIconCategoryDetail) {

        $objCategory = new Level1CartoonIconCategory();
        if (isset($cartoonIconCategoryDetail['id']) && $cartoonIconCategoryDetail['id'] != '' && $cartoonIconCategoryDetail['id'] > 0) {
            $return = $objCategory->where('id', $cartoonIconCategoryDetail['id'])->update($cartoonIconCategoryDetail);
        } else {
            $return = $objCategory->create($cartoonIconCategoryDetail);
        }

        return $return;
    }

    /**
     * @return Boolean True/False
      Parameters
      @$id : Human Icon Category ID
     */
    public function deleteLevel1CartoonIconCategory($id) {

        $flag = true;
        $objCategory = new Level1CartoonIconCategory();
        $category = $objCategory->find($id);
        $category->deleted = config::get('constant.DELETED_FLAG');
        $response = $category->save();
        if ($response) {
            return true;
        } else {
            return false;
        }
    }
    public function getCartoonCategoryNameFromId($categoryId){
        $objCategory = new Level1CartoonIconCategory();
        $categoryName = $objCategory->where('id', $categoryId)->first();
        if(isset($categoryName) && !empty($categoryName)){
            $data = $categoryName->cic_name;
        }else{
            $data = '';
        }
        return $data;
    }
    
    
    public function saveLevel1CartoonIconCategoryDetailForFront($cartoonIconCategoryDetail) {

        $objCategory = new Level1CartoonIconCategory();
        $checkCartoonIconCategory = $objCategory->where('cic_name', $cartoonIconCategoryDetail['cic_name'])->first();
        
        if (isset($checkCartoonIconCategory) && $checkCartoonIconCategory->id != '' && $checkCartoonIconCategory->id > 0) {
            $return = $objCategory->where('id', $checkCartoonIconCategory->id)->update($cartoonIconCategoryDetail);
            $id = $checkCartoonIconCategory->id; 
        } else {
            //$return = $objCategory->create($cartoonIconCategoryDetail);
            $iconSelection = array("cic_name"=>$cartoonIconCategoryDetail['cic_name'], "cic_from"=>$cartoonIconCategoryDetail['cic_from'], "deleted"=>1);
            $id = DB::table('pro_cic_cartoon_icons_category')->insertGetId($iconSelection);
        }
        return $id;
    }
    
    public function getLeve1CartoonIconfromUsers($searchParamArray = array())
    {
        $cartoonIconUploadbyUser = DB::table(config::get('databaseconstants.TBL_LEVEL1_CARTOON_ICON'). " AS cartoon")
                ->leftjoin(config::get('databaseconstants.TBL_CARTOON_ICON_CATEGORY') . " AS ccategory", 'cartoon.ci_category', '=', 'ccategory.id')
                ->leftjoin(config::get('databaseconstants.TBL_TEENAGERS') . " AS teenager", 'cartoon.ci_added_by', '=', 'teenager.id')
                ->selectRaw('cartoon.*,teenager.t_name,teenager.id as teenagerid,ccategory.cic_from')
                ->where('cartoon.ci_added_by','!=',0)
                ->where('cartoon.deleted','!=',3)
                ->paginate(Config::get('constant.ADMIN_RECORD_PER_PAGE'));
        return $cartoonIconUploadbyUser;
    }
    
    public function deleteLevel1CartoonuploadedbyUser($id)
    {
        $cartoonOriginalImageUploadPath = Config::get('constant.CARTOON_ORIGINAL_IMAGE_UPLOAD_PATH');
        $cartoonThumbImageUploadPath = Config::get('constant.CARTOON_THUMB_IMAGE_UPLOAD_PATH');
        $cartoon = $this->model->find($id);
        $imageOriginal = public_path($cartoonOriginalImageUploadPath . $cartoon->hi_image);
        $imageThumb = public_path($cartoonThumbImageUploadPath . $cartoon->hi_image);
        File::delete($imageOriginal, $imageThumb);
        $cartoonIcon = $this->model->find($id);
        $cartoonIcon->deleted = config::get('constant.DELETED_FLAG');
        $response = $cartoonIcon->save();
        if ($response) {
            return true;
        } else {
            return false;
        }
    }

}