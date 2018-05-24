<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use config;

class SchoolSignupRequest extends Request {

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
            return [
                //'mobile' => 'numeric | max : 11',
                //'photo' => 'mimes:jpeg,jpg,bmp,png',

                'school_name' => 'required | min : 3 | max : 255',
                'address1' => 'required | min : 3 | max : 255',
                'address2' => 'required | min : 3 | max : 255',
                'pincode' => 'required | numeric | min : 5',
                'city' => 'required',
                'state' => 'required',
                'country' => 'required',
                'first_name' => 'required | min : 3 | max : 50',
                'last_name' => 'required | min : 3 | max : 50',
                'title' => 'required | min : 2',
                'phone'=> "required | min : 10 | numeric " ,
                'logo' => 'required | mimes:jpeg,jpg,bmp,png',
                'photo' => ' mimes:jpeg,jpg,bmp,png',
                'password' => 'required | min : 6',
                'email'=> "required | email | unique:".Config::get('databaseconstants.TBL_SCHOOLS').",sc_email," . $this->get('id') . ",id,deleted,1",

            ];
    }

}
