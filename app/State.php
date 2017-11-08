<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Config;

class State extends Model {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'pro_s_state';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['s_name', 'c_code'];

    public function getAllStates($country_id) {
        if ($country_id > 0) {
            $states = State::select('*')->where('c_code', $country_id)->orderBy('s_name', 'ASC')->get();
        } else {
            $states = State::select('*')->orderBy('s_name', 'ASC')->get();
        }
        return $states;
    }

    public function getStateById($id) {

            $states = State::select('*')->where('c_code', $id)->orderBy('s_name', 'ASC')->get();
            return $states;
    }
}
