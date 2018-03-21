@extends('layouts.common-master')

@push('script-header')
    <title>{{ trans('labels.appname') }} : Signup</title>
@endpush

@section('content')

<div class="clearfix">
<div class="col-xs-12">
    @if (count($errors) > 0)
    <div class="clearfix">
    <div class="alert alert-danger danger">
        <strong>{{trans('validation.whoops')}}</strong>
        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">X</button>
        {{trans('validation.someproblems')}}<br><br>
        <ul style="padding-left:15px;">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    </div>
    @endif

    @if($message = Session::get('error'))
    <div class="clearfix">
        <div class="col-md-12">
            <div class="box-body">
                <div class="alert alert-error alert-dismissable">
                    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">X</button>
                    {{ $message }}
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
</div>

<div class="container">
    <div class="detail_container">
        <form class="registration_form" id="parent_registration_form" role="form" enctype="multipart/form-data" method="POST" action="{{ url('/parent/do-signup') }}" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="user_type" id="user_type" value="{{ ($type == 'Parent')?1:2 }}">
            <h1><span class="title_border">{{$type}} Registration</span></h1>
            <div class="clearfix">
                <div class="col-md-offset-1 col-md-5 col-sm-6 input_icon name">
                    <?php
                    $first_name = $last_name = $address1 = $address2 = $pincode = $city = $state = $gender = $photo = $email = $password = $p_teenager_reference_id = '';
                    if (old('first_name'))
                        $first_name = old('first_name');
                    elseif ($first_name)
                        $first_name = $newuser->p_first_name;
                    else
                        $first_name = '';
                    ?>
                    <div class="mandatory">*</div>
                    <div class="firstname">
                        <input type="text" name="first_name" class="cst_input_primary alphaonly" minlength="3" maxlength="50" placeholder="First Name" value="{{ $first_name  or ''}}" >
                    </div>
                    <?php
                    if (old('last_name'))
                        $last_name = old('last_name');
                    elseif ($last_name)
                        $last_name = $newuser->p_last_name;
                    else
                        $last_name = '';
                    ?>
                    <div class="lastname input_icon name">
                        <input type="text" name="last_name" class="cst_input_primary alphaonly" minlength="3" maxlength="50" placeholder="Last Name"   value="{{ $last_name  or ''}}">
                    </div><!-- lastname End -->
                </div>
            </div>
            <div class="clearfix">
                <?php
                if (old('address1'))
                    $address1 = old('address1');
                elseif ($address1)
                    $address1 = $newuser->p_address1;
                else
                    $address1 = '';
                ?>
                <div class="col-md-offset-1 col-md-5 col-sm-6 input_icon address">
                    <div class="mandatory">*</div>
                    <input type="text" name="address1" class="cst_input_primary addressvalid" placeholder="Address 1" minlength="3" maxlength="100"  value="{{ $address1 or ''}}">
                </div>
                <div class="col-md-5 col-sm-6 u_image">
                    <div class="user_detail">
                        <div class="pull-right">
                            <?php
                            if (old('gender'))
                                $gender = old('gender');
                            elseif ($gender)
                                $gender = $newuser->p_gender;
                            else
                                $gender = 1;
                            ?>

                            <div class="male_female">
                                <input type="radio" id="male" name="gender" class="gender male" <?php
                                if ($gender == 1) {
                                    echo 'checked="checked"';
                                }
                                ?> value="1"/>
                                <label for="male"><span data-toggle="tooltip" title="Male"></span></label>
                                <input type="radio" id="female" name="gender" class="gender female" <?php
                                if ($gender == 2) {
                                    echo 'checked="checked"';
                                }
                                ?> value="2"/>
                                <label for="female"><span data-toggle="tooltip" title="Female"></span></label>
                            </div>
                            <div class="user_image">
                                <div class="upload_image">
                                    <input type="file" onchange="readURL(this);" name="photo"  class="profilePhoto" accept=".png, .jpg, .jpeg, .bmp" />
                                    <div class="placeholder_image">
                                        <span><img src="{{Storage::url('frontend/images/proteen_logo.png')}}"/></span>
                                        <p><span>Upload Your Photo</span></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="clearfix">
                <?php
                if (old('address2'))
                    $address2 = old('address2');
                elseif ($address2)
                    $address2 = $newuser->p_address2;
                else
                    $address2 = '';
                ?>
                <div class="col-md-offset-1 col-md-5 col-sm-6 input_icon address">
                    <div class="mandatory">*</div>
                    <input type="text" name="address2" class="cst_input_primary addressvalid" minlength="3" maxlength="100" placeholder="Address 2" value="{{ $address2  or ''}}">
                </div>
                <?php
                if (old('p_teenager_reference_id'))
                    $p_teenager_reference_id = old('p_teenager_reference_id');
                elseif ($p_teenager_reference_id)
                    $p_teenager_reference_id = $newuser->p_teenager_reference_id;
                else
                    $p_teenager_reference_id = '';
                ?>                                   
                <div class="col-md-5 col-sm-6 pair_with_teen input_icon">
                    <div class="mandatory">*</div>
                    <input type="text" name="p_teenager_reference_id" maxlength="100" class="cst_input_primary" value="{{$p_teenager_reference_id}}" placeholder="Teen Pair : Use Teen Reference Code"> 
                    <span class="sec-popup help_noti"><a href="javascript:void(0);" data-trigger="hover" data-popover-content="#pop1" class="help-icon custompop" rel="popover" data-placement="bottom"><i class="icon-question"></i></a></span>
                    <div id="pop1" class="hide popoverContent">
                        Lorem ipsum dolor sit amet, consectetur adipisicing elit. Nisi eos, earum ipsum illum libero, beatae vitae, quos sit cum voluptate iste placeat distinctio porro nobis incidunt rem nesciunt. Cupiditate, animi.
                    </div>
                </div>
            </div>
            <div class="clearfix">
                <?php
                if (old('email'))
                    $email = old('email');
                elseif ($email)
                    $email = $newuser->p_email;
                else
                    $email = '';
                ?>
                <div class="col-md-offset-1 col-md-5 col-sm-6 input_icon email">
                    <div class="mandatory">*</div>
                    <input type="text" name="email" class="cst_input_primary" minlength="4" maxlength="100" placeholder="Email" value="{{ $email  or '' }}" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false">
                </div>
                <?php
                if (old('password'))
                    $password = old('password');
                elseif ($password)
                    $password = $newuser->password;
                else
                    $password = '';
                ?>
                <div class=" col-md-5 col-sm-6 input_icon password">
                    <div class="mandatory">*</div>
                    <input type="password" name="password" class="cst_input_primary pass-visi" placeholder="Password"  value="{{ $password  or ''}}" id="password">
                    <span class="visibility-pwd">
                        <img src="https://proteenlive-old.s3.ap-south-1.amazonaws.com/img/view.png" alt="view" class="view img">
                        <img src="https://proteenlive-old.s3.ap-south-1.amazonaws.com/img/hide.png" alt="view" class="img-hide hide img">
                    </span>
                    <em style="color:red" id="pass_validation">  </em>
                </div>
            </div>
            <div class="clearfix">
                <?php
                if (old('pincode'))
                    $pincode = old('pincode');
                elseif ($pincode)
                    $pincode = $newuser->p_pincode;
                else
                    $pincode = '';
                ?>
                
                <?php                
                    if (old('country')) {
                        $country = old('country');
                    } else {
                        $country = '';
                    }
                    
                    if (old('state')) {
                        $state = old('state');
                    } else {
                        $state = '';
                    }
                ?>
                <div class="col-md-offset-1 col-md-5 col-sm-6 input_icon pincode clearfix">
                    <div class="mandatory">*</div>
                    <div class="pincode_input">
                        <input type="text" name="pincode" class="cst_input_primary onlyNumber" minlength="6" maxlength="6" placeholder="Zip Code"  value="{{ $pincode  or ''}}">
                    </div><!-- pincode_input End -->                   
                    <div class="city_input">
                        <div class="mandatory">*</div>
                        <div class="select-style input_icon_parent country_icon">
                            <select name="country" onchange="getDataOfState(this.value);" onclick="getDataOfState(this.value);">
                                <option value="">Country</option>
                                <?php foreach ($countries as $key => $val) { ?>
                                    <option value="{{$val->id}}">{{$val->c_name}}</option>
                                <?php } ?>
                            </select>
                        </div>
                        <em for="country" class="invalid"></em>
                    </div><!-- city_input End -->                                                         
                </div>
                <div class=" col-md-5 col-sm-6 input_icon clearfix">
                    <div class="pincode_input">
                        <div class="mandatory">*</div>
                        <div class="select-style input_icon_parent state_icon">
                            <select id="state_name" name="state" onchange="getDataOfCity(this.value)" onclick="getDataOfCity(this.value)">
                                <option value="">State</option>
                            </select>
                        </div>
                        <em for="state_name" class="invalid"></em>
                    </div>                    
                    <div class="city_input">
                        <div class="mandatory">*</div>
                        <div class="select-style input_icon_parent city_icon">
                            <select id="city_name" name="city" >
                                <option value="">City</option>
                            </select>
                        </div>
                        <em for="city_name" class="invalid"></em>
                    </div><!-- city_input End --> 
                </div> 

            </div>
            <br/>
            <div class="col-md-8 col-md-offset-2 col-sm-12 clearfix">
                <div class="terms_condition_statement">
                    <label for="terms_condition" id="mychoice_lable"><span></span></label>
                    <span class="terms_condition_statement">By clicking submit button, you agree to ProTeen's <a href="{{ url('terms-condition') }}" title="Terms & Conditions">Terms & Conditions</a> and <a href="{{ url('privacy-policy') }}" title="Privacy Policy">Privacy Policy</a></span>
                </div>
            </div>
            <div class="button_container">
                <div class="submit_register">
                    <input type="submit" value="Submit" name="save" class="btn primary_btn">
                </div>
                <div class="text-center frgt-pwd-signup">
                    <p><a href="{{url('parent/forgot-password')}}" title="Forgot password?">Forgot password?</a> Already enrolled? <a href="{{url('parent/login')}}" title="Sign in">Sign in now</a>.</p>
                </div>
            </div>
        </form>
    </div>
</div>
<div id="terms_statement" class="modal fade terms" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i></button>
                <h4 class="modal-title">Disclaimer & Terms of Use</h4>
            </div>
            <div class="modal-body">
                {!!$infotext!!}
            </div>
        </div>
    </div>
</div>
<div id="privacy_statement" class="modal fade privacy" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i></button>
                <h4 class="modal-title">Privacy Policy</h4>
            </div>
            <div class="modal-body">
                {!!$policytext!!}
            </div>
        </div>
    </div>
</div>

@stop
@section('script')
<script type="text/javascript">

                                        jQuery(document).ready(function() {
                                            $('#terms_statement .modal-body').mCustomScrollbar();
                                            $('#privacy_statement .modal-body').mCustomScrollbar();
                                            $('.alphaonly').bind('keyup blur', function() {
                                                var node = $(this);
                                                node.val(node.val().replace(/[^a-zA-Z ]/g, ''));
                                            }
                                            );

                                            $('.addressvalid').bind('keyup blur', function() {
                                                var node = $(this);
                                                node.val(node.val().replace(/[^0-9a-zA-Z-&,. ]/g, ''));
                                            }
                                            );

                                            $('#male').click(function() {
                                                if ($('#male').is(':checked')) {
                                                }
                                            });

                                            var loginRules = {
                                                first_name: {
                                                    required: true,
                                                    minlength: 3,
                                                    maxlength: 50
                                                },
                                                last_name: {
                                                    required: true,
                                                    minlength: 3,
                                                    maxlength: 50
                                                },
                                                address1: {
                                                    required: true,
                                                    minlength: 3,
                                                    maxlength: 100
                                                },
                                                address2: {
                                                    required: true,
                                                    minlength: 3,
                                                    maxlength: 100
                                                },
                                                pincode: {
                                                    required: true,
                                                    minlength: 6,
                                                    maxlength: 6
                                                },
                                                city: {
                                                    required: true
                                                },
                                                state: {
                                                    required: true
                                                },
                                                country: {
                                                    required: true
                                                },
                                                email: {
                                                    required: true,
                                                    email: true
                                                },
                                                p_teenager_reference_id: {
                                                    required: true
                                                },
                                                password : {
                                                  required: function(el) {
                                                      checkPassword()
                                                  },
                                                }
                                            };
                                            $("#parent_registration_form").validate({
                                                rules: loginRules,
                                                messages: {
                                                    first_name: {required: '<?php echo trans('validation.firstnamerequiredfield') ?>'
                                                    },
                                                    last_name: {required: '<?php echo trans('validation.lastnamerequiredfield') ?>'
                                                    },
                                                    address1: {required: '<?php echo trans('validation.address1requiredfield') ?>'
                                                    },
                                                    address2: {required: '<?php echo trans('validation.address2requiredfield') ?>'
                                                    },
                                                    pincode: {required: '<?php echo trans('validation.pincoderequired') ?>',
                                                        minlength: 'It required 6 character',
                                                    },
                                                    city: {required: 'City is required'
                                                    },
                                                    state: {required: 'State is required'
                                                    },
                                                    country: {required: 'Country is required'
                                                    },
                                                    email: {required: '<?php echo trans('validation.emailrequired') ?>'
                                                    },
                                                    p_teenager_reference_id: {required: '<?php echo trans('validation.parentteenrequiredfield') ?>'
                                                    },
                                                    password: {required: '<?php echo trans('validation.passwordrequired') ?>'
                                                    }

                                                }
                                            });
                                            var $toggle = $(".visibility-pwd");
                                            var $field = $(".pass-visi");
                                            var i = $(this).find('.img');
                                            // Toggle the field type
                                            $toggle.on("click", function(e) {
                                                e && e.preventDefault();
                                                if ($field.attr("type") == "password") {
                                                    $field.attr("type", "text");
                                                    i.toggleClass("hide");
                                                } else {
                                                    i.toggleClass("hide");
                                                    $field.attr("type", "password");
                                                }

                                            });
                                        });
                                        var postCountry = '<?php echo $country ?>';
                                        if(postCountry>0 && postCountry != ''){
                                            getDataOfState(postCountry);
                                        }
                                        var postState = '<?php echo $state ?>';
                                        if(postState>0 && postState != ''){
                                            getDataOfCity(postState);
                                        }
                                        
                                        function getDataOfState(countryId)
                                        {
                                            $.ajax({
                                                type: 'GET',
                                                url: '/get-state/' + countryId,
                                                dataType: "JSON",
                                                success: function(JSON) {
                                                    if (JSON.length > 0) {
                                                        $("#state_name").empty();
                                                        $("#city_name").empty();
                                                        for (var i = 0; i < JSON.length; i++) {
                                                            $("#state_name").append($("<option></option>").val(JSON[i].id).html(JSON[i].s_name));
                                                        }
                                                    } else {
                                                        $("#state_name").empty();
                                                        $("#city_name").empty();
                                                        $("#city_name").append($('<option></option>').val('').html('City'));
                                                        $("#state_name").append($('<option></option>').val('').html('State'));

                                                    }

                                                }
                                            });

                                        }

                                        function getDataOfCity(stateId)
                                        {
                                            $.ajax({
                                                type: 'GET',
                                                url: '/get-city/' + stateId,
                                                dataType: "JSON",
                                                success: function(JSON) {
                                                    if (JSON.length > 0) {
                                                        $("#city_name").empty();
                                                        for (var i = 0; i < JSON.length; i++) {
                                                            $("#city_name").append($("<option></option>").val(JSON[i].id).html(JSON[i].c_name))
                                                        }
                                                    } else {
                                                        $("#city_name").empty();
                                                        $("#city_name").append($('<option></option>').val('').html('City'));
                                                    }

                                                }
                                            });

                                        }

function checkPassword(){
var password = $('#password').val();
    if (password == '') {
        $("#pass_validation").text('');
        return true;
    } else if (password.length < 6) {
        $("#pass_validation").text('Use at least 6 characters');
        return true;
    } else if (password.length > 20) {
        $("#pass_validation").text('Password maximum range is 20');
        return true;
    } else if (password.search(/\d/) == -1) {
        $("#pass_validation").text('Use at least one number');
        return true;
    } else if (password.search(/[a-zA-Z]/) == -1) {
        $("#pass_validation").text('Use at least one character');
        return true;
    } else if (password.search(/[!\@\#\$\%\^\&\*\(\)\_\+]/) == -1) {
        $("#pass_validation").text('Use at least one special character');
        return true;
    } else {
        $("#pass_validation").text('');
        return false;
    }
}

</script>
@stop