<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use Config;

class ProfessionWiseTag extends Model
{
    protected $table = 'pro_pwt_professions_wise_tags';

    protected $fillable = ['profession_id', 'tag_id', 'deleted'];

    /**
     * Insert and Update Profession Wise Tag
     */
    public function insertUpdate($data)
    {
        if (isset($data['id']) && $data['id'] != '' && $data['id'] > 0) {
            return ProfessionWiseTag::where('id', $data['id'])->update($data);
        } else {
            return ProfessionWiseTag::create($data);
        }
    }

    /**
     * get all Profession Wise Tag
     */
    public function getAllProfessionWiseTag() {
        $return = DB::table(config::get('databaseconstants.TBL_PROFESSIONS_WISE_TAG'). " AS pwt")
                  ->join(config::get('databaseconstants.TBL_PROFESSIONS') . " AS profession", 'pwt.profession_id', '=', 'profession.id')
                  ->join(config::get('databaseconstants.TBL_PROFESSIONS_TAG') . " AS tag", 'pwt.tag_id', '=', 'tag.id')
                  ->selectRaw('pwt.id, pwt.profession_id, profession.pf_name as profession_name, GROUP_CONCAT(tag.pt_name  SEPARATOR \', \') AS certificate_name')
                  ->where('pwt.deleted',Config::get('constant.ACTIVE_FLAG'))
                  ->groupBy('pwt.profession_id')
                  ->get();
        return $return;
    }

    /**
     * get Profession Wise Tag data By Id
     */
    public function getProfessionWiseTagByProfessionId($id) {
        $return = ProfessionWiseTag::select('profession_id',DB::raw("(GROUP_CONCAT(tag_id SEPARATOR ',')) as `tag_id`"))->groupBy('profession_id')->where('profession_id',$id)->where('deleted',Config::get('constant.ACTIVE_FLAG'))->first();
                return $return;
    }

    /**
     * Delete Profession Wise Tag
     */
    public function deleteProfessionWiseTagByProfessionId($id) {
        $return = ProfessionWiseTag::where('profession_id',$id)->delete();
        return $return;
    }

    public function tag(){
        return $this->belongsTo(ProfessionTag::class, 'tag_id');
    }
}
