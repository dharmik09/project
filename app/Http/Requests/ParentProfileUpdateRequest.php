<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use config;

class ParentProfileUpdateRequest extends Request {

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

                //'photo' => 'mimes:jpeg,jpg,bmp,png',
                'first_name' => 'required | min :3 | max : 30',
                'last_name' => 'required | min :3 | max : 30',
                'address1' => 'required | min :3 | max : 30',
                'address2' => 'required | min :3 | max : 30',
                'pincode' => 'required  | min : 6',
                'city' => 'required',
                'state' => 'required',
                'country' => 'required',
                'gender' => 'required',
                'email'  => 'required | email',           
                //'password' => 'required | min : 6',
            ];
    }

}
