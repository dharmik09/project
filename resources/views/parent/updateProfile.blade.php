@extends('layouts.parent-master')

@section('content')

<div>
    @if (count($errors) > 0)
    <div class="alert alert-danger">
        <strong>{{trans('validation.whoops')}}</strong> {{trans('validation.someproblems')}}<br><br>
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    @if($message = Session::get('error'))
    <div class="clearfix">
        <div class="col-md-12">
            <div class="box-body">
                <div class="alert alert-error alert-dismissable danger">
                    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">X</button>
                    <h4><i class="icon fa fa-check"></i> {{trans('validation.errorlbl')}}</h4>
                    {{ $message }}
                </div>
            </div>
        </div>
    </div>
    @endif

    @if($message = Session::get('success'))
    <div class="clearfix">
        <div class="col-md-12">
            <div class="box-body">
                <div class="alert alert-error alert-succ-msg alert-dismissable success">
                    <button aria-hidden="true" data-dismiss="success" class="close" type="button">X</button>
                    {{ $message }}
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
<div class="container">
    <div class="detail_container">
        <form class="registration_form" id="parent_registration_form" role="form" enctype="multipart/form-data" method="POST" action="{{ url('/parent/save-profile') }}" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <h1><span class="title_border">Update Profile</span></h1>
            <div class="clearfix">
                <div class="col-md-offset-1 col-md-5 col-sm-6 input_icon name">
                    <div class="firstname input_icon_inner">
                        <div class="mandatory">*</div>
                        <input type="text" name="first_name" class="cst_input_primary alphaonly" maxlength="100" placeholder="First Name" value="{{ $user->p_first_name or ''}}">
                    </div><!-- firstname End -->
                    <div class="lastname input_icon input_icon_inner name">
                        <div class="mandatory">*</div>
                        <input type="text" name="last_name" class="cst_input_primary alphaonly" maxlength="100" placeholder="Last Name" value="{{ $user->p_last_name or ''}}">
                    </div><!-- lastname End -->
                </div>
            </div>
            <div class="clearfix">
                <div class="col-md-offset-1 col-md-5 col-sm-6 input_icon address">
                    <div class="mandatory">*</div>
                    <input type="text" name="address1" class="cst_input_primary addressvalid" maxlength="100" placeholder="Address1" value="{{ $user->p_address1 or ''}}">
                </div>
                <div class="col-md-5 col-sm-6 u_image">
                    <div class="user_detail">
                        <div class="pull-right">
                            <?php
                            $gender = isset($user->p_gender) ? $user->p_gender : '';
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
                                    <div class="placeholder_image update_profile">
                                        <span>
                                            <?php
                                            if (isset($user->p_photo) && $user->p_photo != '') {
                                                ?>
                                                <img src="{{ Storage::url($parentOriginalImagePath . $user->p_photo) }}"/>
                                            <?php } else { ?>
                                                <span>
                                                    <span><img src="{{ Storage::url('frontend/images/proteen_logo.png') }}"/></span>
                                                    <p><span>Upload Your Photo</span></p>
                                                </span>
                                            <?php } ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="clearfix">
                <div class="col-md-offset-1 col-md-5 col-sm-6 input_icon address">
                    <div class="mandatory">*</div>
                    <input type="text" name="address2" class="cst_input_primary addressvalid" maxlength="100" placeholder="Address 2" value="{{ $user->p_address2 or ''}}">
                </div>
                <div class="col-md-5 col-sm-6 input_icon email">
                    <div class="mandatory">*</div>
                    <input type="hidden" name="email" id="email" class="cst_input_primary" maxlength="100" placeholder="{{trans('labels.emaillbl')}}" value="{{ $user->p_email or '' }}" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" readonly>
                    <input type="text" name="email2" id="email2" class="cst_input_primary" maxlength="100" placeholder="{{trans('labels.emaillbl')}}" value="{{ $user->p_email or '' }}" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" readonly>
                </div>
            </div>
            <div class="clearfix">
                <div class="col-md-offset-1 col-md-5 col-sm-6 input_icon pincode clearfix">
                    <div class="pincode_input input_icon_inner">
                        <div class="mandatory">*</div>
                        <input type="text" name="pincode" class="cst_input_primary onlyNumber" maxlength="12" placeholder="Zip Code" value="{{ $user->p_pincode or ''}}">
                    </div><!-- pincode_input End -->
                    <div class="city_input input_icon_inner">
                        <div class="mandatory">*</div>
                        <div class="select-style input_icon_parent country_icon">
                            <?php $countries = Helpers::getCountries(); ?>
                            <select name="country" onchange="getDataOfState(this.value)">
                                <option value="">Country</option>
                                <?php foreach ($countries as $key => $val) { ?>
                                    <option value="{{$val->id}}" <?php
                                    if (isset($user->p_country) && $user->p_country == $val->id) {
                                        echo "selected='selected'";
                                    }
                                    ?> >{{$val->c_name}}</option>
                                        <?php } ?>
                            </select>
                        </div>
                        <em for="country" class="invalid"></em>
                    </div><!-- city_input End -->
                </div>
                <div class=" col-md-5 col-sm-6">
                    <div class="pincode_input input_icon_inner">
                        <div class="mandatory">*</div>
                        <div class="select-style input_icon_parent state_icon">
                            <?php $states = Helpers::getStates($user->p_country); ?>
                            <select id="state_name" name="state" onchange="getDataOfCity(this.value)" >
                                <option value="">State</option>
                                <?php foreach ($states as $key => $val) { ?>
                                    <option value="{{$val->id}}" <?php
                                    if (isset($user->p_state) && $user->p_state == $val->id) {
                                        echo "selected='selected'";
                                    }
                                    ?> >{{$val->s_name}}</option>
                                        <?php } ?>
                            </select>
                        </div>
                        <em for="state_name" class="invalid"></em>
                    </div><!-- pincode_input End -->
                    <div class="city_input input_icon_inner">
                        <div class="mandatory">*</div>
                        <div class="select-style input_icon_parent city_icon">
                            <?php $cities = Helpers::getCities($user->p_state); ?>
                            <select id="city_name" name="city" >
                                <option value="">City</option>
                                <?php foreach ($cities as $key => $val) { ?>
                                    <option value="{{$val->id}}" <?php
                                    if (isset($user->p_city) && $user->p_city == $val->id) {
                                        echo "selected='selected'";
                                    }
                                    ?> >{{$val->c_name}}</option>
                                        <?php } ?>
                            </select>
                        </div>
                        <em for="city_name" class="invalid"></em>																					
                    </div><!-- city_input End -->
                </div>
            </div>
            <br/>
            <div class="button_container">
                <div class="submit_register">
                    <div class="submit_register"><input type="submit" class="btn primary_btn" id="next1" name="submit" value="Update"></div>
                </div>
            </div>
        </form>
    </div>
</div>

@stop
@section('script')

<script type="text/javascript">

                                        jQuery(document).ready(function() {

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

                                            $("#email").keypress(function() {
                                                return false;
                                            });

                                            var loginRules = {
                                                first_name: {
                                                    required: true,
                                                    minlength: 3,
                                                    maxlength: 100
                                                },
                                                last_name: {
                                                    required: true,
                                                    minlength: 3,
                                                    maxlength: 100

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
                                                    maxlength: 12
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
                                                password: {
                                                    required: true,
                                                    minlength: 6,
                                                    maxlength: 20
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
                                                    pincode: {required: '<?php echo trans('validation.pincoderequired') ?>'
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
                                                    password: {required: '<?php echo trans('validation.passwordrequired') ?>',
                                                        maxlength: 'Password maximum range is 20',
                                                        minlength: 'Password length is minimum 3'
                                                    }
                                                }
                                            });
                                        });

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
                                                        $("#city_name").append($('<option></option>').val('').html('Select'));
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


</script>
@stop