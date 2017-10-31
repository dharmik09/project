<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class TeenagerProfileUpdateRequest extends Request {

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
                'mobile' => 'max:11 | min:10',
                'photo' => 'mimes:jpeg,jpg,bmp,png',
                'name' => 'required',
                'birth_date' => 'required',
                'gender' => 'required',
                'country' => 'required',
                'pincode' => 'required',
                'email' => 'required | email',
                //'password' => 'required | min : 6',
            ];
    }
    
}
