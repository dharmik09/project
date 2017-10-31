<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class TeenagerPairRequest extends Request {

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
                'p_first_name' => 'required | min : 3',
                'p_last_name' => 'required | min : 3',
                'p_user_type' => 'required',
                'gender' => 'required',
                'country' => 'required',
                'pincode' => 'required | min : 5 | max : 6',
                'email' => 'required | email'                
            ];
    }
    
}
