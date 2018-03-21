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
        $result = $this->where('pt_name', $name)->where('deleted', Config::get('constant.ACTIVE_FLAG'))->first();
        return $result;
    }
    public function getProfessionTagBySlug($slug) {
        $result = $this->where('pt_slug', $slug)->where('deleted', Config::get('constant.ACTIVE_FLAG'))->first();
        return $result;
    }

    public function getProfessionTagBySlugWithProfessionAndAttemptedProfession($slug, $userid) {
        $this->userid = $userid;
        $result = $this->select('*')
                    ->with(['professionTags' => function ($query) {
                        $query->with(['profession' => function ($query) {
                            $query->where('deleted', Config::get('constant.ACTIVE_FLAG'));
                        }]);
                    }])
                    ->where('pt_slug', $slug)
                    ->where('deleted', Config::get('constant.ACTIVE_FLAG'))
                    ->first();

        return $result;
    }

    public function professionTags(){
        return $this->hasMany(ProfessionWiseTag::class, 'tag_id');
    }

    public function getProfessionTagBySlugWithProfessionAndAttemptedProfessionByPage($slug, $userid, $records) {
        $this->userid = $userid;
        $this->records = $records;
        $result = $this->select('*')
                    ->with(['professionTags' => function ($query) {
                        $query->with(['profession' => function ($query) {
                            $query->where('deleted', Config::get('constant.ACTIVE_FLAG'));
                        }])
                        ->skip($this->records)
                        ->take(10)
                        ->where('deleted', Config::get('constant.ACTIVE_FLAG'));
                    }])
                    ->where('pt_slug', $slug)
                    ->where('deleted', Config::get('constant.ACTIVE_FLAG'))
                    ->first();

        return $result;
    }
}
