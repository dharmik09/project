<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted'             => 'The :attribute must be accepted.',
    'active_url'           => 'The :attribute is not a valid URL.',
    'after'                => 'The :attribute must be a date after :date.',
    'alpha'                => 'The :attribute may only contain letters.',
    'alpha_dash'           => 'The :attribute may only contain letters, numbers, and dashes.',
    'alpha_num'            => 'The :attribute may only contain letters and numbers.',
    'array'                => 'The :attribute must be an array.',
    'before'               => 'The :attribute must be a date before :date.',
    'between'              => [
        'numeric' => 'The :attribute must be between :min and :max.',
        'file'    => 'The :attribute must be between :min and :max kilobytes.',
        'string'  => 'The :attribute must be between :min and :max characters.',
        'array'   => 'The :attribute must have between :min and :max items.',
    ],
    'boolean'              => 'The :attribute field must be true or false.',
    'confirmed'            => 'The :attribute confirmation does not match.',
    'date'                 => 'The :attribute is not a valid date.',
    'date_format'          => 'The :attribute does not match the format :format.',
    'different'            => 'The :attribute and :other must be different.',
    'digits'               => 'The :attribute must be :digits digits.',
    'digits_between'       => 'The :attribute must be between :min and :max digits.',
    'email'                => 'The :attribute must be a valid email address.',
    'exists'               => 'The selected :attribute is invalid.',
    'filled'               => 'The :attribute field is required.',
    'image'                => 'The :attribute must be an image.',
    'in'                   => 'The selected :attribute is invalid.',
    'integer'              => 'The :attribute must be an integer.',
    'ip'                   => 'The :attribute must be a valid IP address.',
    'json'                 => 'The :attribute must be a valid JSON string.',
    'max'                  => [
        'numeric' => 'The :attribute may not be greater than :max.',
        'file'    => 'The :attribute may not be greater than :max kilobytes.',
        'string'  => 'The :attribute may not be greater than :max characters.',
        'array'   => 'The :attribute may not have more than :max items.',
    ],
    'mimes'                => 'The :attribute must be a file of type: :values.',
    'min'                  => [
        'numeric' => 'The :attribute must be at least :min.',
        'file'    => 'The :attribute must be at least :min kilobytes.',
        'string'  => 'The :attribute must be at least :min characters.',
        'array'   => 'The :attribute must have at least :min items.',
    ],
    'not_in'               => 'The selected :attribute is invalid.',
    'numeric'              => 'The :attribute must be a number.',
    'regex'                => 'The :attribute format is invalid.',
    'required'             => 'The :attribute field is required.',
    'required_if'          => 'The :attribute field is required when :other is :value.',
    'required_with'        => 'The :attribute field is required when :values is present.',
    'required_with_all'    => 'The :attribute field is required when :values is present.',
    'required_without'     => 'The :attribute field is required when :values is not present.',
    'required_without_all' => 'The :attribute field is required when none of :values are present.',
    'same'                 => 'The :attribute and :other must match.',
    'size'                 => [
        'numeric' => 'The :attribute must be :size.',
        'file'    => 'The :attribute must be :size kilobytes.',
        'string'  => 'The :attribute must be :size characters.',
        'array'   => 'The :attribute must contain :size items.',
    ],
    'string'               => 'The :attribute must be a string.',
    'timezone'             => 'The :attribute must be a valid zone.',
    'unique'               => 'The :attribute has already been taken.',
    'url'                  => 'The :attribute format is invalid.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of "email". This simply helps us make messages a little cleaner.
    |
    */

    'attributes' => [],
    'emailrequired' => 'Email is required',
    'passwordrequired' => 'Password is required',
    'newpasswordrequired' => 'New Password is required',
    'confirmpasswordrequired' => 'Confirm Password is required',
    'whoops' => 'Whoops!',
    'someproblems' => 'There were some problems with your input.',
    'invalidcombo' => 'This username/password combo does not exist.',
    'somethingwrong' => 'Something went wrong. Please try again.',
    'addsuccessmessage' => ':entity added successfully',
    'updatesuccessmessage' => ':entity updated successfully',
    'deletesuccessmessage' => ':entity deleted successfully',
    'successlbl' => 'Success!',
    'errorlbl' => 'Error!',
    'requiredfield' => 'This field is required',
    'alphabetsonly' => 'Use alphabets only',
    'validemail' => 'Please enter valid email',
    'emailrepeat' => 'The given email has already been taken.',
    'cfgkeyrepeat' => 'The given Key has already been taken.',
    'uniqueidrepeat' => 'The given unique id has already been taken.',
    'phonerepeat' => 'The given phone has already been taken.',
    'digitsonly' => 'Please enter only digits',
    'validdate' => 'Please enter valid date',
    'passwordnotmatch' => 'Password not matching',
    'namerequiredfield' => 'Please enter name. This is required.',
    'nicknamerequiredfield' => 'Please enter nick name. This is required.',
    'emailrequiredfield' => 'Please enter email. This is required.',
    'uniqueidrequiredfield' => 'Please enter unique id. This is required.',
    'passwordrequiredfield' => 'Please enter password. This is required.',
    'confirmpasswordrequiredfield' => 'Please enter confirm password. This is required.',
    'phonerequiredfield' => 'Please enter phone. This is required.',
    'bdaterequiredfield' => 'Please enter birthdate. This is required.',
    'levelrequired' => 'Please select level. This is required.',
    'photorequired' => 'Please upload Image.',
    'validphotorequired' => 'Please upload valid Image.',
    'statusrequired' => 'Status is required',
    'companynamerequiredfield' => 'Please enter Company name.This  is required.',
    'adminnamerequiredfield' => 'Please enter Admin Name.This is required.',
    'banknamerequiredfield' => 'Please enter Bank Name.This is required.',
    'bankaccountnorequiredfield' => 'Please enter Bank Account No.This is required.',
    'teenageridrequiredfield' => 'Please select Teenager ID.This is required.',
    'professionintrorequiredfield' => 'Please enter Profession Intro.This is required.',
    'couponcoderequiredfield' => 'Please enter Coupon Code.This is required.',
    'couponsponsorrequiredfield' => 'Please select Coupon Sponsor.This  is required.',
    'couponvalidfromrequiredfield' => 'Please enter valid From Date.This is required.',
    'couponvalidtorequiredfield' => 'Please enter valid To Date.This is required.',
    'bulkrequired' => 'Please Upload Bulk File.',
    'validbulkrequired' => 'Please upload Only CSV File.',
    'cmssubjectrequiredfield' => 'Please enter Subject.This is required.',
    'cmsslugrequiredfield' => 'Please enter Slug.This is required.',
    'cmsbodyrequiredfield' => 'Please enter Body.This is required.',
    'templatenamerequiredfield' => 'Please enter Template Name.This is required.',
    'templatepseudonamerequiredfield' => 'Please enter Template Pseudo Name.This is required.',
    'templatesubjectrequiredfield' => 'Please enter Template Subject.This is required.',
    'templatebobyrequiredfield' => 'Please enter Template Body.This is required.',
    'templatestatusrequiredfield' => 'Please enter Status.This is required.',
    'activitytextrequired' => 'Please enter Text. This is required.',
    'activitypointsrequired' => 'Please enter Points.This is required.',
    'activityoptionrequired' => 'Please enter Options.This is required.',
    'professionoptionrequired' => 'Please select Profession.This is required.',
    'basketintrorequiredfield' => 'Please enter Basket Intro.This is required.',
    'activityfractionrequired' => 'Please select Fraction.This is required.',
    'pincoderequired'  => 'Please enter zipcode',
    'systemlevelinforequiredfield' => 'Please enter Info.This is required.',
    'validphoneno' => 'Please enter Valid Phone No.',
    'validvideorequired' => 'Please enter Valid Video File.',
    'feedbacklevelrequiredfield' => 'Please enter Feedback level.This is required',
    'feedbackquestionrequiredfield' => 'Please enter Feedback Question.This is required',
    'cartoonnametextrequired' => 'Please enter Cartoon name. This is required.',
    'cartoonimagerequired' => 'Please select Image for Cartoon.This is required.',
    'cartooncategoryrequired' => 'Please select Category for Cartoon.This is required.',
    'humannametextrequired' => 'Please enter Human Icon name. This is required.',
    'humanimagerequired' => 'Please select Image for Human Icon.This is required.',
    'humancategoryrequired' => 'Please select Category for Human.This is required.',
    'professionrequiredfield' => 'Please Select Profession.This is required.',
    'headertitlerequiredfield' => 'Please enter Sun Header.This is required.',
    'headercontentrequired' => 'Please Enter Header Content.This is required.',
    'cartooncategorynametextrequired' => 'Please Enter Cartoon Category.This is required.',
    'humancategorynametextrequired' => 'Please Enter Human Icon Category.This is required.',
    'videotypeequired' => 'Please select Video Type.This is required.',
    'apptitudetyperequiredfield' => 'Please select Apptitude Type.This is required.',
    'birthdaterequiredfield' => 'Please Enter Valid Birthdate.The Age should be Between 13 To 17',
    'address1requiredfield'  => 'Address1 is Required',
    'address2requiredfield'  => 'Address2 is Required',
    'firstnamerequiredfield'  => 'First Name is Required',
    'lastnamerequiredfield'  => 'Last Name is Required',
    'parentteenrequiredfield'  => 'Parent with Teen is Required',
    'companynamerequiredfield'  => 'Company Name is Required',
    'adminnamerequiredfield'  => 'Admin Name is Required',
    'schoolnamerequiredfield'  => 'School Name is Required',
    'cfg_keyrequiredfield'  => 'Key is Required',
    'cfg_valuerequiredfield'  => 'Value is Required',
    'address1required' => 'Please enter Address1. This is required.',
    'address2required' => 'Please enter Address2. This is required.',
    'firstnamerequired' => 'Please enter First Name. This is required.',
    'lastnamerequired' => 'Please enter Last Name. This is required.',
    'titlerequired' => 'Please enter Title. This is required.',
    'phonerequired' => 'Please enter Mobile Number. This is required.',
    'cityrequired' => 'Please enter City. This is required.',
    'staterequired' => 'Please enter State. This is required.',
    'countryrequired' => 'Please enter Country. This is required.',
    'nameisrequired' => 'Name is required',
    'titleisrequired' => 'Title is required',
    'imageisrequired' => 'Image is required',
    'descriptionisrequired' => 'Description is required',
    'tagsrequired' => 'Tags field is required',
    'pageisrequired' => 'Page is required',
    'slugisrequired' =>'Slug is required',
];
