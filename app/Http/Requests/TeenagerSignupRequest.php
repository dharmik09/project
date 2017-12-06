<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class TeenagerSignupRequest extends Request {

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
                //'mobile' => 'min : 10 | max : 11',
                //'photo' => 'mimes:jpeg,jpg,bmp,png',
                'name' => 'required | min : 3',
                't_lastname' => 'required | min : 3',
                //'birth_date' => 'required',
                'gender' => 'required',
                'country' => 'required',
                'pincode' => 'required | min : 5 | max : 6',
                'email' => 'required | email | max : 100 | confirmed',
                'password' => 'required | min : 6 | confirmed',
                //'nickname' => 'min : 2',
                'year' => 'required',
                'month' => 'required',
                'day' => 'required'
            ];
    }
}
