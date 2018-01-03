<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use Config;

class ProfessionTag extends Model
{
    protected $table = 'pro_pt_profession_tags';

    protected $guarded = [];
    
    public function getAllProfessionTags() {
        $tags = ProfessionTag::selectRaw('*')->where('deleted', '<>', Config::get('constant.DELETED_FLAG'))->get();
        return $tags;
    }


    public function saveProfessionTagDetail($tagDetail) {
       if ($tagDetail['id'] != '0') {
          $this->where('id', $tagDetail['id'])->update($tagDetail);
       } else {
          $this->create($tagDetail);
       }
         return '1';
    }

    public function deleteTag($id) {
        $flag = true;
        $tag = $this->find($id);
        $tag->deleted = config::get('constant.DELETED_FLAG');
        $response = $tag->save();
        if ($response) {
            return true;
        } else {
            return false;
        }
    }


    public function getProfessionTagByName($name) {
        $certifications = $this->where('pt_name', $name)->where('deleted', '<>', Config::get('constant.DELETED_FLAG'))->first();
        return $certifications;
    }
}
