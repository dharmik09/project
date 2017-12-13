<?php

namespace App\Http\Requests;

use Config;
use App\Http\Requests\Request;

class InterestTypeRequest extends Request
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
                'it_name' => 'required',
                'it_slug' => 'required',
                'deleted' => 'required',
            ];
        }
        else
        {
            return [
                'it_name' => 'required',
                'it_slug' => 'required',
                'deleted' => 'required',
            ];
        }
    }

    public function messages() {
        return [
            'it_name.required' => trans('validation.namerequiredfield'),
            'it_slug.required' => trans('validation.slugisrequired'),
            'deleted.required' => trans('validation.statusrequired'),
        ];
    }
}
