<?php

namespace App;
use Illuminate\Database\Eloquent\Model;
use Config;
use DB;

class VersionsList extends Model {

    protected $table = 'pro_av_app_versions';
    protected $guarded = [];

    public function getAllVersionsList() {
        $tags = VersionsList::get();
        return $tags;
    }

    public function saveVersionsListDetail($versionDetail) {
       if ($versionDetail['id'] != '0') {
          $this->where('id', $versionDetail['id'])->update($versionDetail);
       } else {
          $this->create($versionDetail);
       }
         return '1';
    }

}