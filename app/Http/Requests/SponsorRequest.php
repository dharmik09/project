<?php

namespace App\Http\Requests;

use Config;
use App\Http\Requests\Request;

class SponsorRequest extends Request
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
                'sp_company_name'         => 'required',
                'sp_email'         => "required | email | unique:".Config::get('databaseconstants.TBL_SPONSORS').",sp_email," . $this->get('id'),
                'sp_admin_name'   => 'required',
                'password'         => 'required',
                'confirm_password'    => 'required | same:password',
                'sp_address1'      => 'required',
                'sp_address2'      => 'required',
                'sp_city'      => 'required',
                'sp_state'      => 'required',
                'sp_country'      => 'required',
                'sp_pincode'      => 'required',
                'sp_logo'          => 'mimes:jpeg,jpg,bmp,png',
                'sp_credit'         => 'numeric ',
                'sp_first_name'      => 'required',
                'sp_last_name'      => 'required',
                'sp_title'      => 'required',
                'sp_phone'      => 'required | numeric',
                'deleted' => 'required',
            ];
        }
        else
        {
            return [
                'id'=> 'required',
                'sp_company_name'         => 'required',
                'sp_email'         => 'required | email | unique:'.Config::get('databaseconstants.TBL_SPONSORS').',sp_email,' . $this->get('id'),
                'sp_admin_name'   => 'required',
                'confirm_password'    => 'same:password',
                'sp_address1'      => 'required',
                'sp_address2'      => 'required',
                'sp_city'      => 'required',
                'sp_state'      => 'required',
                'sp_country'      => 'required',
                'sp_pincode'      => 'required',
                'sp_logo'          => 'mimes:jpeg,jpg,bmp,png',
                'sp_credit'         => 'numeric ',
                'sp_first_name'      => 'required',
                'sp_last_name'      => 'required',
                'sp_title'      => 'required',
                'sp_phone'      => 'required | numeric',
                'deleted' => 'required',
            ];
        }
    }

    public function messages() {
        return [
            'sp_company_name.required' => trans('validation.companynamerequiredfield'),
            'sp_email.required' => trans('validation.emailrequiredfield'),
            'sp_email.email' => trans('validation.validemail'),
            'sp_email.unique' => trans('validation.emailrepeat'),
            'sp_admin_name'   => trans('validation.adminnamerequiredfield'),
            'password.required' => trans('validation.passwordrequiredfield'),
            'confirm_password.required' => trans('validation.confirmpasswordrequiredfield'),
            'confirm_password.same' => trans('validation.passwordnotmatch'),
            'sp_address1.required' => trans('validation.address1required'),
            'sp_address2.required' => trans('validation.address2required'),
            'sp_city.required' => trans('validation.cityrequired'),
            'sp_state.required' => trans('validation.staterequired'),
            'sp_country.required' => trans('validation.countryrequired'),
            'sp_pincode.required' => trans('validation.pincoderequired'),
            'sp_logo.required' => trans('validation.photorequired'),
            'sp_logo.mimes' => trans('validation.validphotorequired'),
            'sp_credit.numeric' => trans('validation.digitsonly'),
            'sp_first_name.required' => trans('validation.firstnamerequired'),
            'sp_last_name.required' => trans('validation.lastnamerequired'),
            'sp_title.required' => trans('validation.titlerequired'),
            'sp_phone.required' => trans('validation.phonerequired'),
            'deleted.required' => trans('validation.statusrequired'),
        ];
    }
}
