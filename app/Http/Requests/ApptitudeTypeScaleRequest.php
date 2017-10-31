<?php

namespace App\Http\Requests;

use Config;
use App\Http\Requests\Request;

class ApptitudeTypeScaleRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        if($this->get('id') == 0)
        {
            return [
                'ats_apptitude_type_id'         => 'required',
            ];
        }
        else
        {
            return [
                'ats_apptitude_type_id'         => 'required',
            ];
        }
    }

    public function messages() {
        return [
            'ats_apptitude_type_id.required' => trans('validation.apptitudetyperequiredfield'),
        ];
    }
}
