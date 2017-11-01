<?php

namespace App;
use Illuminate\Database\Eloquent\Model;

class Sponsors extends Model {
  /**
   * The database table used by the model.
   *
   * @var string
   */
  protected $table = 'pro_sp_sponsor';
  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $guarded = [];
  /**
   * The get active sponsors.
   *
   * @var array
   */
  public function getActiveSponsors()
  {
      $result = $this->select('*')
                      ->where('deleted', '1')
                      ->get();
      return $result;
  }

}