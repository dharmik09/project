<?php

namespace App;

use Illuminate\Notifications\Notifiable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Parents extends Authenticatable {

  use Notifiable;

  protected $table = 'pro_p_parent';
  protected $guarded = [];

  public function getActiveParents($parentType = 1)
  {
      $result = Parents::select('*')
                      ->where('deleted', '1')
                      ->where('p_user_type', $parentType)
                      ->get();
      return $result;
  }
  
  public function getUniqueId($id)
  {
      $result = DB::table(config::get('databaseconstants.TBL_PARENT_TEEN_PAIR') . " AS pair ")
              ->join(config::get('databaseconstants.TBL_TEENAGERS') . " AS teenager ", 'pair.ptp_teenager', '=', 'teenager.id')
              ->selectRaw('teenager.t_uniqueid, pair.*')
              ->where('pair.ptp_parent_id', $id)
              ->get();
      
      return $result;
  }

  public function getParentsData($parentId) {
      $result = Parents::select('*')
              ->where('deleted', '1')
              ->where('id',$parentId)
              ->first();
      return $result;
  }

}