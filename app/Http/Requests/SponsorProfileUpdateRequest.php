<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use config;

class SponsorProfileUpdateRequest extends Request {

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

                'company_name' => 'required | min : 3 | max : 255',
                'admin_name' => 'required | min : 3 | max : 255',
                'address1' => 'required | min : 3 | max : 255',
                'address2' => 'required | min : 3 | max : 255',
                'pincode' => 'required | min : 5 | max : 6',
                'city' => 'required',
                'state' => 'required',
                'country' => 'required',
                'first_name' => 'required | min : 3 | max : 50',
                'last_name' => 'required | min : 3 | max : 50',
                'title' => 'required | min : 2 | max : 50',
                'phone'=> "required | min : 10 | numeric" ,
                'logo' => 'mimes:jpeg,jpg,bmp,png',
                'photo' => 'mimes:jpeg,jpg,bmp,png',               
                'email'=> "required | email",
            ];
    }
    
}
