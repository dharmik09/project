<?php

namespace App\Http\Requests;

use Config;
use App\Http\Requests\Request;

class ProfessionCertificationRequest extends Request
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
                'pc_name'      => 'required',
                'pc_image'      => 'required',
                'deleted' => 'required',
            ];
        }
        else
        {
            return [
                'pc_name'      => 'required',
                'deleted' => 'required',
            ];
        }
    }

    public function messages() {
        return [
            'pc_name.required' => trans('validation.nameisrequired'),
            'pc_image.required' => trans('validation.imageisrequired')
        ];
    }
}
