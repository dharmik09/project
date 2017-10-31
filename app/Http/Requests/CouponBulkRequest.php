<?php

namespace App\Http\Requests;

use Config;
use App\Http\Requests\Request;

class CouponBulkRequest extends Request
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
                'cp_bulk'          => 'required|mimes:csv,txt',
            ];
    }

    public function messages() {
        return [
            'cp_bulk.required' => trans('validation.bulkrequired'),
            'cp_bulk.mimes' => trans('validation.validbulkrequired')
        ];
    }
}
