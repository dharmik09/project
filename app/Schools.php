<?php

namespace App;

use Illuminate\Notifications\Notifiable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Schools extends Authenticatable {

  use Notifiable;

  /**
   * The database table used by the model.
   *
   * @var string
   */
  protected $table = 'pro_sc_school';
  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $guarded = [];
  /**
   * The get active schools.
   *
   * @var array
   */
  public function getActiveSchools()
  {
      $result = Schools::select('*')
                      ->where('deleted', '1')
                      ->get();
      return $result;
  }
  /**
   * The get active school by @id.
   *
   * @var array
   */
  public function getActiveSchool($id)
  {
      $result = Schools::select('*')
                      ->where('id', $id)
                      ->get();
      return $result;
  }

}