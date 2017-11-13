<?php

namespace App\Http\Requests;

use Config;
use App\Http\Requests\Request;

class PaidComponentsRequest extends Request
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
                'pc_element_name'      => 'required',
                'pc_required_coins'      => 'required | numeric',
                'pc_valid_upto'      => 'required | numeric',
                'deleted' => 'required',
            ];
        }
        else
        {
            return [
                'pc_element_name'      => 'required',
                'pc_required_coins'      => 'required | numeric',
                'pc_valid_upto'      => 'required | numeric',
                'deleted' => 'required',
            ];
        }
    }

    public function messages() {
        return [
            'pc_element_name.required' => trans('validation.elemetnamerequired'),
            'pc_required_coins.required' => trans('validation.coinsrequiredfield'),
            'pc_valid_upto.required' => trans('validation.validuptorequired'),
            'pc_required_coins.numeric' => trans('validation.digitsonly'),
            'pc_valid_upto.numeric' => trans('validation.digitsonly'),
            'deleted.required' => trans('validation.statusrequired'),
        ];
    }
}
