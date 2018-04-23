@extends('layouts.common-master')

@push('script-header')
    <title>{{ trans('labels.appname') }} : Profile</title>
@endpush

@section('content')
    <div class="centerlize">
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
                <form class="registration_form" id="parent_registration_form" role="form" enctype="multipart/form-data" method="POST" action="{{ url('/parent/set-profile') }}" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="user_type" id="user_type" value="{{ ($parentData->p_user_type == 'Parent')?1:2 }}">
                    <input type="hidden" name="id" id="parent_id" value="{{ $parentData->id }}">
                    <h1><span class="title_border">Set Profile</span></h1>
                    <div class="clearfix">
                        <div class="col-md-offset-1 col-md-5 col-sm-6 input_icon name">
                            <div class="mandatory">*</div>
                            <div class="firstname">
                                <input type="text" name="first_name" class="cst_input_primary alphaonly" minlength="3" maxlength="50" placeholder="First Name" >
                            </div>
                            <div class="lastname input_icon name">
                                <input type="text" name="last_name" class="cst_input_primary alphaonly" minlength="3" maxlength="50" placeholder="Last Name" >
                            </div><!-- lastname End -->
                        </div>
                    </div>
                    <div class="clearfix">
                        <div class="col-md-offset-1 col-md-5 col-sm-6 input_icon address">
                            <div class="mandatory">*</div>
                            <input type="text" name="address1" class="cst_input_primary addressvalid" placeholder="Address 1" minlength="3" maxlength="200" >
                        </div>
                        <div class="col-md-5 col-sm-6 u_image">
                            <div class="user_detail">
                                <div class="pull-right">
                                    <div class="male_female">
                                        <input type="radio" id="male" name="gender" class="gender male" value="1" checked="checked" />
                                        <label for="male"><span data-toggle="tooltip" title="Male"></span></label>
                                        <input type="radio" id="female" name="gender" class="gender female" value="2" />
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
                        <div class="col-md-offset-1 col-md-5 col-sm-6 input_icon address">
                            <div class="mandatory">*</div>
                            <input type="text" name="address2" class="cst_input_primary addressvalid" minlength="3" maxlength="200" placeholder="Address 2" >
                        </div>
                        <div class="col-md-5 col-sm-6 pair_with_teen input_icon">
                            <div class="mandatory">*</div>
                            <input type="text" id="p_teenager_reference_id" name="p_teenager_reference_id" maxlength="100" class="cst_input_primary" value="{{$teenReferenceId}}" placeholder="Teen Pair : Use Teen Reference Code"> 
                            <!-- <button class="info_popup_open" style="right: 22px;top: 10px;cursor:pointer;" title="Enter unique Teen ID. Teen will receive an email once you submit the form. Once Teen verifies your invitation, you can see their progress through the ProTeen levels. If you are not aware of unique Teen ID, please contact the Teen or you can find it in their Profile section."><i aria-hidden="true" class="fa fa-question-circle"></i></button> -->
                            <span class="sec-popup help_noti"><a id="parent-signup-teen-reference-field" href="javascript:void(0);" data-trigger="hover" data-popover-content="#pop1" class="help-icon custompop" rel="popover" data-placement="bottom" onmouseover="getHelpText('parent-signup-teen-reference-field')"><i class="icon-question"></i></a></span>
                            <div id="pop1" class="hide popoverContent">
                                <span class="parent-signup-teen-reference-field"></span>
                            </div>
                        </div>
                    </div>
                    <div class="clearfix">
                        <div class="col-md-offset-1 col-md-5 col-sm-6 input_icon email">
                            <div class="mandatory">*</div>
                            <input type="text" id="email" name="email" class="cst_input_primary" minlength="4" maxlength="100" placeholder="Email" value="{{ $parentData->p_email }}" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false">
                        </div>
                        <div class=" col-md-5 col-sm-6 input_icon password">
                            <div class="mandatory">*</div>
                            <input type="password" name="password" class="cst_input_primary" placeholder="Password" id="password">
                            <em style="color:red" id="pass_validation">  </em>
                        </div>
                    </div>
                    <div class="clearfix">
                        <div class="col-md-offset-1 col-md-5 col-sm-6 input_icon pincode clearfix">
                            <div class="mandatory">*</div>
                            <div class="pincode_input">
                                <input type="text" name="pincode" class="cst_input_primary onlyNumber" minlength="6" maxlength="6" placeholder="Zip Code"  >
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
                    <div class="button_container">
                        <div class="submit_register">
                            <input type="submit" value="Submit" name="save" class="btn primary_btn">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>


@stop
@section('script')
<script type="text/javascript">

    jQuery(document).ready(function() {
        $("#p_teenager_reference_id").attr('readonly', true);
        $("#email").attr('readonly', true);
        $('.alphaonly').bind('keyup blur', function() {
            var node = $(this);
            node.val(node.val().replace(/[^a-zA-Z ]/g, ''));
        });

        $('.addressvalid').bind('keyup blur', function() {
            var node = $(this);
            node.val(node.val().replace(/[^0-9a-zA-Z-&,. ]/g, ''));
        });

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
                maxlength: 200
            },
            address2: {
                required: true,
                minlength: 3,
                maxlength: 200
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
    });
    // var postCountry = '<?php //echo $country ?>';
    // if(postCountry>0 && postCountry != ''){
    //     getDataOfState(postCountry);
    // }
    // var postState = '<?php //echo $state ?>';
    // if(postState>0 && postState != ''){
    //     getDataOfCity(postState);
    // }
                                        
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

    function getHelpText(helpSlug)
    {
        var CSRF_TOKEN = "{{ csrf_token() }}";
        $.ajax({
            type: 'POST',
            url: "{{url('parent/get-help-text')}}",
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN
            },
            data: {'helpSlug':helpSlug},
            success: function(response) {
                $("."+helpSlug).text(response);    
                showPopover(helpSlug);
            }
        });
    }

    function showPopover(helpSlug) {
        $('#'+helpSlug).popover({
            html:true,
            content : function() { 
                return $( $(this).data("popover-content") ).html();
            }
        });
    }

</script>
@stop