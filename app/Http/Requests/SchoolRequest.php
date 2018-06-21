<?php

namespace App\Http\Requests;

use Config;
use App\Http\Requests\Request;

class SchoolRequest extends Request
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
                'sc_name'         => 'required',
                'sc_email'         => "required | email | unique:".Config::get('databaseconstants.TBL_SCHOOLS').",sc_email," . $this->get('id'),
                'password'         => 'required',
                'confirm_password'    => 'required | same:password',
                'sc_address1'      => 'required',
                'sc_address2'      => 'required',
                'sc_city'      => 'required',
                'sc_state'      => 'required',
                'sc_country'      => 'required',
                'sc_pincode'      => 'required',
                'sc_logo'          => 'mimes:jpeg,jpg,bmp,png',
                'sc_first_name'      => 'required',
                'sc_last_name'      => 'required',
                'sc_title'      => 'required',
                'sc_phone'      => 'required | numeric', 'deleted' => 'required',
                'deleted' => 'required',
            ];
        }
        else
        {
            return [
                'sc_name'         => 'required',
                'sc_email'         => "required | email | unique:".Config::get('databaseconstants.TBL_SCHOOLS').",sc_email," . $this->get('id'),                
                'sc_address1'      => 'required',
                'sc_address2'      => 'required',
                'sc_city'      => 'required',
                'sc_state'      => 'required',
                'sc_country'      => 'required',
                'sc_pincode'      => 'required',
                'sc_logo'          => 'mimes:jpeg,jpg,bmp,png',
                'sc_first_name'      => 'required',
                'sc_last_name'      => 'required',
                'sc_title'      => 'required',
                'sc_phone'      => 'required | numeric', 'deleted' => 'required',
                'deleted' => 'required',
            ];
        }
    }

    public function messages() {
        return [
            'sc_name.required' => trans('validation.namerequiredfield'),
            'sc_email.required' => trans('validation.emailrequiredfield'),
            'sc_email.email' => trans('validation.validemail'),
            'sc_email.unique' => trans('validation.emailrepeat'),
            'password.required' => trans('validation.passwordrequiredfield'),
            'confirm_password.required' => trans('validation.confirmpasswordrequiredfield'),
            'confirm_password.same' => trans('validation.passwordnotmatch'),
            'sc_address1.required' => trans('validation.address1required'),
            'sc_address2.required' => trans('validation.address2required'),
            'sc_city.required' => trans('validation.cityrequired'),
            'sc_state.required' => trans('validation.staterequired'),
            'sc_country.required' => trans('validation.countryrequired'),
            'sc_pincode.required' => trans('validation.pincoderequired'),
            'sc_phone.required' => trans('validation.phonerequiredfield'),
            'sc_phone.numeric' => trans('validation.digitsonly'),
            'sc_phone.unique' => trans('validation.phonerepeat'),
            'sc_photo.required' => trans('validation.photorequired'),
            'sc_photo.mimes' => trans('validation.validphotorequired'),
            'sc_first_name.required' => trans('validation.firstnamerequired'),
            'sc_last_name.required' => trans('validation.lastnamerequired'),
            'sc_title.required' => trans('validation.titlerequired'),
            'deleted.required' => trans('validation.statusrequired'),
        ];
    }
}
