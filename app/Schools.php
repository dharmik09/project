<?php

namespace App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Schools extends Authenticatable {
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
        $result = $this->select('*')
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
        $result = $this->select('*')
                    ->where('id', $id)
                    ->get();
        return $result;
    }

}