<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use Config;

class PromisePlus extends Model
{
    protected $table = 'pro_ps_promise_plus';
    protected $fillable = ['id', 'ps_text', 'ps_description' , 'created_at', 'updated_at'];

    public function getActivePromisePlus() {
        $result = PromisePlus::select('*')
                    ->get()->toArray();
        return $result;
    }

    public function savePromisePlusDetail($promiseplusDetail) {
        $psLength = count($promiseplusDetail['ps_text']);
        $psArray = [];
        for ($i = 0; $i < $psLength; $i++) {
            $psArray['ps_text'] = $promiseplusDetail['ps_text'][$i];
            $psArray['ps_description'] = $promiseplusDetail['ps_description'][$i];
            $result = PromisePlus::select('id')
                    ->where('ps_text', $promiseplusDetail['ps_text'][$i])
                    ->get();
            if (count($result) > 0) {
               $this->where('ps_text', $promiseplusDetail['ps_text'][$i])->update($psArray);
            } else {
              $this->create($psArray);
            }
        }
        return '1';
    }

    public function getDescriptionofPromisePlus($name) {
        $result = PromisePlus::select('ps_description')
                    ->where('ps_text',$name)
                    ->get()->toArray();
        if (count($result) > 0) {
            return $result[0]['ps_description'];
        } else {
            $result = '';
            return $result;
        }
    }

    public function getAllPromisePlus() {
        $result = DB::table(config::get('databaseconstants.TBL_PROMISE_PLUS'))
                    ->selectRaw('ps_text,ps_description')
                    ->get();
        return $result;
    }
}
