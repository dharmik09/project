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
                'name' => 'required | min:3',
                'lastname' => 'required | min:3',
                'email' => 'required | email | max : 100',
                'country' => 'required',
                'pincode' => 'required | min : 5 | max : 6',
                'gender' => 'required',
                'year' => 'required',
                'month' => 'required',
                'day' => 'required',
                'mobile' => 'required | numeric',
            ];
    }
    
}
