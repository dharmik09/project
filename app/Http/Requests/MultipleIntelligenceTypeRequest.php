<?php

namespace App\Http\Requests;

use Config;
use App\Http\Requests\Request;

class MultipleIntelligenceTypeRequest extends Request
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
                'mit_name'         => 'required',
                'deleted' => 'required',
            ];
        }
        else
        {
            return [
                'mit_name'         => 'required',
                'deleted' => 'required',
            ];
        }
    }

    public function messages() {
        return [
            'mit_name.required' => trans('validation.namerequiredfield'),
            'deleted.required' => trans('validation.statusrequired'),
        ];
    }
}
