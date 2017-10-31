<?php

namespace App\Http\Requests;

use Config;
use App\Http\Requests\Request;

class CareerMappingRequest extends Request
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
            return [
                'tcm_profession_id'    => 'required',
            ];
    }

    public function messages() {
        return [
            'tcm_profession_id.required' => trans('validation.professionoptionrequired'),
        ];
    }
}
