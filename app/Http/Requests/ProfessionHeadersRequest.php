<?php

namespace App\Http\Requests;

use Config;
use App\Http\Requests\Request;

class ProfessionHeadersRequest extends Request
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
                'pfic_profession'         => 'required',
                'pfic_title'         => 'required',
                'pfic_content'          => 'required',
            ];
        }
        else
        {
            return [
                'pfic_profession'         => 'required',
                'pfic_title'         => 'required',
                'pfic_content'          => 'required',
            ];
        }
    }

    public function messages() {
        return [
            'pfic_profession.required' => trans('validation.professionrequiredfield'),
            'pfic_title.required' => trans('validation.headertitlerequiredfield'),
            'pfic_content.required' => trans('validation.headercontentrequired'),
        ];
    }
}
