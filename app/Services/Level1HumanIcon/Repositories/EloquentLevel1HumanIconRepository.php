<?php 

namespace App\Services\Level1HumanIcon\Repositories;

use DB;
use Config;

use App\Level1HumanIconCategory;
use App\Level1HumanIconProfessionMapping;
use App\Services\Level1HumanIcon\Contracts\Level1HumanIconRepository;
use App\Services\Repositories\Eloquent\EloquentBaseRepository;


use File;
use Helpers;
use Mail;

class EloquentLevel1HumanIconRepository extends EloquentBaseRepository implements Level1HumanIconRepository {

    public function _construct(TemplatesRepository $TemplatesRepository){
        
    }
    //$a = Auth::admin()->get()->t_email;
    
    /**
     * @return array of all the active human icons
      Parameters
      @$searchParamArray : Array of Searching and Sorting parameters
     */
    public function getLeve1HumanIcon() {
        $humanIcon = DB::table(config::get('databaseconstants.TBL_LEVEL1_HUMAN_ICON') . " AS human")
                ->leftjoin(config::get('databaseconstants.TBL_HUMAN_ICON_CATEGORY') . " AS hcategory", 'human.hi_category', '=', 'hcategory.id')
                ->selectRaw('human.*, hcategory.hic_name')
                ->where('human.deleted', '<>', Config::get('constant.DELETED_FLAG'))
                ->get();
        return $humanIcon;
    }
    
    public function getHumanCategoryNameFromId($categoryId){
        $objCategory = new Level1HumanIconCategory();
        $categoryName = $objCategory->where('id', $categoryId)->first();
        if(isset($categoryName) && !empty($categoryName)){
            $data = $categoryName->hic_name;
        }else{
            $data = '';
        }
        return $data;
    }
    
    
    /**
     * @return Human Icons details object
      Parameters
      @$humanIconDetail : Array of Human Icons detail from front
     */
    public function saveLevel1HumanIconDetail($humanIconDetail, $profession = array()) {

        $objHumanIconProfession = new Level1HumanIconProfessionMapping();
        $professionLength = count($profession);
        if (isset($humanIconDetail['id']) && $humanIconDetail['id'] != '' && $humanIconDetail['id'] > 0) {
            $return = $this->model->where('id', $humanIconDetail['id'])->update($humanIconDetail);
        } else {
            $return = $this->model->create($humanIconDetail);
        }
        if ($professionLength > 0) {
            if ($return) {
                if ($humanIconDetail['id'] != '' && $humanIconDetail['id'] > 0) {
                    $id = $humanIconDetail['id'];
                } else {
                    $id = $return->id;
                }
            }
            $professionData = $objHumanIconProfession->where('hpm_humanicon_id', $humanIconDetail['id'])->get();
            $professionDataLength = count($professionData);
            $noOfRow = 0;

            if ($professionLength >= $professionDataLength) {
                for ($i = 0; $i < $professionLength; ++$i) {
                    $professionDetail = [];
                    $professionDetail['hpm_humanicon_id'] = $id;
                    $professionDetail['hpm_profession_id'] = $profession[$i];
                    if ($noOfRow < $professionDataLength) {
                        if ($humanIconDetail['id'] != '' && $humanIconDetail['id'] > 0) {
                            $result = $objHumanIconProfession->where('id', $professionData[$i]->id)->update($professionDetail);
                        }
                        $noOfRow++;
                    } else {
                        $result = $objHumanIconProfession->create($professionDetail);
                    }
                }
            } else {
                for ($i = 0; $i < $professionDataLength; ++$i) {
                    for ($i = 0; $i < $professionLength; ++$i) {
                        $professionDetail = [];
                        $professionDetail['hpm_humanicon_id'] = $id;
                        $professionDetail['hpm_profession_id'] = $profession[$i];

                        if ($noOfRow < $professionDataLength - 1) {
                            if ($humanIconDetail['id'] != '' && $humanIconDetail['id'] > 0) {
                                $result = $objHumanIconProfession->where('id', $professionData[$i]->id)->update($professionDetail);
                            }
                            $noOfRow++;
                        }
                    }
                    $result = $objHumanIconProfession->where('id', $professionData[$i]->id)->delete($professionDetail);
                }
            }
        }
        return $return;
    }

    /**
     * @return Boolean True/False
      Parameters
      @$id : Human Icon ID
     */
    public function deleteLevel1HumanIcon($id) {

        $flag = true;
        $humanicon = $this->model->find($id);
        $humanicon->deleted = config::get('constant.DELETED_FLAG');
        $response = $humanicon->save();
        if ($response) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return array of all the active human icons category
      Parameters
      @$searchParamArray : Array of Searching and Sorting parameters
     */
    public function getLeve1HumanIconCategory() {
        $humanIconCategory = DB::table(config::get('databaseconstants.TBL_HUMAN_ICON_CATEGORY'))
                ->select('*')
                ->where('deleted', '<>', Config::get('constant.DELETED_FLAG'))
                ->get();
        return $humanIconCategory;
    }

    /**
     * @return Human Icons details object
      Parameters
      @$humanIconCategoryDetail : Array of Human Icons detail category from front
     */
    public function saveLevel1HumanIconCategoryDetail($humanIconCategoryDetail) {

        $objCategory = new Level1HumanIconCategory();
        if (isset($humanIconCategoryDetail['id']) && $humanIconCategoryDetail['id'] != '' && $humanIconCategoryDetail['id'] > 0) {
            $return = $objCategory->where('id', $humanIconCategoryDetail['id'])->update($humanIconCategoryDetail);
        } else {
            $return = $objCategory->create($humanIconCategoryDetail);
        }

        return $return;
    }

    /**
     * @return Boolean True/False
      Parameters
      @$id : Human Icon Category ID
     */
    public function deleteLevel1HumanIconCategory($id) {

        $flag = true;
        $objCategory = new Level1HumanIconcategory();
        $category = $objCategory->find($id);
        $category->deleted = config::get('constant.DELETED_FLAG');
        $response = $category->save();
        if ($response) {
            return true;
        } else {
            return false;
        }
    }

    public function saveLevel1HumanIconCategoryDetailForFront($humanIconCategoryDetail) {

        $objCategory = new Level1HumanIconCategory();
        $checkHumanIconCategory = $objCategory->where('hic_name', $humanIconCategoryDetail['hic_name'])->first();

        if (isset($checkHumanIconCategory->id) && $checkHumanIconCategory->id != '' && $checkHumanIconCategory->id > 0) {
            $return = $objCategory->where('id', $checkHumanIconCategory->id)->update($humanIconCategoryDetail);
            $id = $checkHumanIconCategory->id;
        } else {
            //$return = $objCategory->create($humanIconCategoryDetail);
            $iconSelection = array("hic_name" => $humanIconCategoryDetail['hic_name'], "hic_from"=>$humanIconCategoryDetail['hic_from'], "deleted" => 1);
            $id = DB::table('pro_hi_human_icons_category')->insertGetId($iconSelection);
        }
        return $id;
    }
    
    public function getLeve1CartoonIconfromUsers($searchParamArray = array())
    {
        $humanIconUploadbyUser = DB::table(config::get('databaseconstants.TBL_LEVEL1_HUMAN_ICON'). " AS human")
                ->leftjoin(config::get('databaseconstants.TBL_HUMAN_ICON_CATEGORY') . " AS hcategory", 'human.hi_category', '=', 'hcategory.id')
                ->leftjoin(config::get('databaseconstants.TBL_TEENAGERS') . " AS teenager", 'human.hi_added_by', '=', 'teenager.id')
                ->selectRaw('human.*,teenager.t_name,teenager.id as teenagerid,hcategory.hic_from')
                ->where('human.hi_added_by','!=',0)
                ->where('human.deleted','!=',3)
                ->get();
        return $humanIconUploadbyUser;
    }

    public function deleteLevel1HumanIconuploadedbyUser($id)
    {
        $humanOriginalImageUploadPath = Config::get('constant.HUMAN_ORIGINAL_IMAGE_UPLOAD_PATH');
        $humanThumbImageUploadPath = Config::get('constant.HUMAN_THUMB_IMAGE_UPLOAD_PATH');
        $human = $this->model->find($id);
        $imageOriginal = public_path($humanOriginalImageUploadPath . $human->hi_image);
        $imageThumb = public_path($humanThumbImageUploadPath . $human->hi_image);
        File::delete($imageOriginal, $imageThumb);
        $humanIcon = $this->model->find($id);
        $humanIcon->deleted = config::get('constant.DELETED_FLAG');
        
        $response = $humanIcon->save();
        if ($response) {
            return true;
        } else {
            return false;
        }
    }


}
