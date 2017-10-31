<?php

namespace App\Http\Requests;

use Config;
use App\Http\Requests\Request;

class Level1HumanIconCategoryRequest extends Request
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
                'hic_name'         => 'required',
            ];
        }
        else
        {
            return [
                'hic_name'         => 'required',
            ];
        }
    }

    public function messages() {
        return [
            'hic_name.required' => trans('validation.humancategorynametextrequired'),
        ];
    }
}
