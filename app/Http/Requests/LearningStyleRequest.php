<?php

namespace App\Http\Requests;

use Config;
use App\Http\Requests\Request;

class LearningStyleRequest extends Request
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
                'ls_name'      => 'required',
                'deleted' => 'required',
            ];
        }
        else
        {
            return [
                'ls_name'      => 'required',
                'deleted' => 'required',
            ];
        }
    }

    public function messages() {
        return [
            'ls_name.required' => trans('validation.learningstylenamerequired'),
            'deleted.required' => trans('validation.statusrequired'),
        ];
    }
}
