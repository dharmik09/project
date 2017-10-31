<?php

namespace App\Http\Requests;

use Config;
use App\Http\Requests\Request;

class ConfigurationRequest extends Request
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

                'cfg_key'         => "required |  unique:".Config::get('databaseconstants.TBL_CONFIGURATION').",cfg_key," . $this->get('id'),
                'cfg_value'         => 'required',
                //'deleted' => 'required',
            ];
        }
        else
        {
            return [
                'id'=> 'required',
                'cfg_key'         => "required |  unique:".Config::get('databaseconstants.TBL_CONFIGURATION').",cfg_key," . $this->get('id'),
                'cfg_value'=> 'required',
                //'deleted' => 'required',

            ];
        }
    }

    public function messages() {
        return [
            'cfg_key.required' => trans('validation.cfg_keyrequiredfield'),
             'cfg_key.unique' => trans('validation.cfgkeyrepeat'),
            'cfg_value.required' => trans('validation.cfg_valuerequiredfield'),
            //'deleted.required' => trans('validation.statusrequired'),
        ];
    }
}
