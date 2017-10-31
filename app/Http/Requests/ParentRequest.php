<?php

namespace App\Http\Requests;

use Config;
use App\Http\Requests\Request;

class ParentRequest extends Request
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
                'p_first_name'         => 'required | min :3 | max : 30',
                'p_last_name'         => 'required | min :3 | max : 30',
                'p_address1'         => 'required | min :3 | max : 30',
                'p_address2'         => 'required | min :3 | max : 30',
                'p_city'         => 'required',
                'p_state'         => 'required',
                'p_country'         => 'required',
                'p_pincode'         => 'required | min :6 ',
                'p_gender'         => 'required',
                'p_email'         => "required | email | unique:".Config::get('databaseconstants.TBL_PARENTS').",p_email," . $this->get('id'),
                'password'         => 'required | min :6 | max : 20',
                'confirm_password'    => 'required | min :6 | max : 20 | same:password',               
                'p_teenager_id'     => 'required',
                'deleted' => 'required',
            ];
        }
        else
        {
            return [
                'id'=> 'required',
                'p_first_name'         => 'required | min :3 | max : 30',
                'p_last_name'         => 'required | min :3 | max : 30',
                'p_address1'         => 'required | min :3 | max : 30',
                'p_address2'         => 'required | min :3 | max : 30',
                'p_city'         => 'required',
                'p_state'         => 'required',
                'p_country'         => 'required',
                'p_pincode'         => 'required | min :6',
                'p_gender'         => 'required',
                //'p_email'         => 'required | email | unique:'.Config::get('databaseconstants.TBL_PARENTS').',p_email,' . $this->get('id'),
                //'p_teenager_id'     => 'required',
                'deleted' => 'required',
            ];
        }
    }

    public function messages() {
        return [
            'p_first_name.required' => trans('validation.firstnamerequiredfield'),
            'p_last_name.required' => trans('validation.lastnamerequiredfield'),
            'p_address1.required' => trans('validation.address1requiredfield'),
            'p_address2.required' => trans('validation.address2requiredfield'),
            'p_city.required' => trans('validation.cityrequiredfield'),
            'p_state.required' => trans('validation.staterequiredfield'),
            'p_country.required' => trans('validation.countryrequiredfield'),
            'p_pincode.required' => trans('validation.pincoderequiredfield'),
            'p_gender.required' => trans('validation.genderrequiredfield'),
            'p_email.required' => trans('validation.emailrequiredfield'),
            'p_email.email' => trans('validation.validemail'),
            'p_email.unique' => trans('validation.emailrepeat'),
            'password.required' => trans('validation.passwordrequiredfield'),
            'confirm_password.required' => trans('validation.confirmpasswordrequiredfield'),
            'confirm_password.same' => trans('validation.passwordnotmatch'),
           // 'p_phone.required' => trans('validation.phonerequiredfield'),
            //'p_phone.numeric' => trans('validation.digitsonly'),
            //'p_phone.unique' => trans('validation.phonerepeat'),
            'p_teenager_id.required' => trans('validation.teenageridrequiredfield'),
            'deleted.required' => trans('validation.statusrequired'),
        ];
    }
}
