<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use Config;

class Helptext extends Model
{
    protected $table = 'pro_h_helptext';

    protected $guarded = [];
    
    public function getAllHelptexts() {
        $helptexts = Helptext::selectRaw('*')->where('deleted', '<>', Config::get('constant.DELETED_FLAG'))->get();
        return $helptexts;
    }


    public function saveHelptextDetail($helpTextDetail) {
       if ($helpTextDetail['id'] != '0'){
          $this->where('id', $helpTextDetail['id'])->update($helpTextDetail);
       } else {
          $this->create($helpTextDetail);
       }
         return '1';
    }

    public function deleteHelptext($id) {
        $flag = true;
        $helptext = $this->find($id);
        $helptext->deleted = config::get('constant.DELETED_FLAG');
        $response = $helptext->save();
        if ($response) {
            return true;
        } else {
            return false;
        }
    }
}
