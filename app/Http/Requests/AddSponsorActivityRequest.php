<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use config;

class AddSponsorActivityRequest extends Request {

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
                'type' => 'required',
                'sa_name' => 'required | min :3 | max : 30',
                'level' => 'required',
                'status' => 'required',
                'startdate' => 'required',
                'enddate' => 'required',
            ];
    }

}
