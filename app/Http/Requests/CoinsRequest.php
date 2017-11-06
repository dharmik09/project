<?php

namespace App\Http\Requests;

use Config;
use App\Http\Requests\Request;

class CoinsRequest extends Request
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
                'c_coins'         => 'required',
                'c_price'         => 'required',
                'c_currency'         => 'required',
                'c_package_name'         => 'required',
                'c_description'  => 'required',
                'c_price'         => 'numeric ',
                'c_coins'         => 'numeric ',
                'deleted' => 'required',
            ];
        }
        else
        {
            return [
                'c_coins'         => 'required',
                'c_price'         => 'required',
                'c_currency'         => 'required',
                'c_package_name'         => 'required',
                'c_description'  => 'required',
                'c_price'         => 'numeric ',
                'c_coins'         => 'numeric ',
                'deleted' => 'required',
            ];
        }
    }

    public function messages() {
        return [
            'c_coins.required' => trans('validation.coinsrequiredfield'),
            'c_price.required' => trans('validation.pricerequiredfield'),
            'c_currency.required' => trans('validation.currencyrequiredfield'),
            'c_package_name.required' => trans('validation.packagenamerequiredfield'),
            'c_description.required' => trans('validation.descriptionrequiredfield'),
            'c_coins.numeric' => trans('validation.coinsdigitsonly'),
            'c_price.numeric' => trans('validation.pricedigitsonly'),
            'deleted.required' => trans('validation.statusrequired'),
        ];
    }
}
