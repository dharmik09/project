@extends('layouts.common-master')

@push('script-header')
    <title>{{ trans('labels.appname') }} : School Signup</title>
@endpush

@section('content')
<div class="clearfix">
<div class="col-xs-12">
    @if (count($errors) > 0)
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
    @endif
</div>

@if($message = Session::get('error'))
    <div class="col-md-12">
        <div class="box-body">
            <div class="alert alert-error alert-dismissable">
                <button aria-hidden="true" data-dismiss="alert" class="close" type="button">X</button>
                {{ $message }}
            </div>
        </div>
    </div>
@endif
</div>

<div class="centerlize">
        <div class="container">
            <div class="container_padd detail_container">
                <form class="registration_form" id="school_registration_form" role="form" enctype="multipart/form-data" method="POST" action="{{ url('/school/do-signup') }}" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <h1><span class="title_border">School Registration</span></h1>
                    <div class="clearfix">
                         <?php
                                        $school_name = $admin_name = $address1 = $address2 = $pincode =  $city = $state = $country = $logo = $photo =  $first_name = $last_name = $title = $phone = $email =  $password  = '';
                                        if (old('school_name'))
                                        $school_name = old('school_name');
                                        elseif ($school_name)
                                        $school_name = $newuser->sc_school_name;
                                        else
                                        $school_name = '';
                        ?>
                        <div class="col-md-offset-1 col-md-5 col-sm-6 input_icon company_name">
                        <div class="mandatory">*</div>
                            <input type="text" name="school_name" class="cst_input_primary" maxlength="255" placeholder="School Name" value="{{ $school_name or ''}}">
                        </div>
                        <div class="col-md-5 col-sm-6 u_image">                        
                            <div class="sponsor_detail clearfix">
                                <div class="pull-right">
                                    <div class="user_image">
                                        <div class="upload_image">
                                            <input type='file' onchange="readURL(this);" name="photo"  class="profilePhoto" accept=".png, .jpg, .jpeg, .bmp" />
                                            <div class="placeholder_image">
                                                <span><img src="{{Storage::url('frontend/images/proteen_logo.png')}}"/></span>
                                                <p><span>Upload Your Photo</span></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>                                
                                <div class="pull-left">
                                    <div class="sponso_image">
                                        <div class="upload_image">                                            
                                            <input type='file' onchange="readURL(this);" name="logo"  class="profilePhoto" accept=".png, .jpg, .jpeg, .bmp" />                                            
                                            <div class="placeholder_image sponsor">
                                                <div class="mandatory">*</div>
                                                <span><img src="{{Storage::url('frontend/images/proteen_logo.png')}}"/></span>
                                                <p><span>Upload School Logo</span></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="clearfix">
                        <?php
                            if (old('address1'))
                            $address1 = old('address1');
                            elseif ($address1)
                            $address1 = $newuser->sc_address1;
                            else
                            $address1 = '';
                        ?>
                        <div class="col-md-offset-1 col-md-5 col-sm-6 input_icon address">
                        <div class="mandatory">*</div>
                            <input type="text" name="address1" class="cst_input_primary addressvalid" maxlength="255"  placeholder="Address-1"  value="{{ $address1 or ''}}">
                        </div>
                    </div>
                    <div class="clearfix">
                        <?php
                            if (old('address2'))
                                $address2 = old('address2');
                            elseif ($address2)
                                $address2 = $newuser->sc_address2;
                            else
                                $address2 = '';
                         ?>
                        <div class="col-md-offset-1 col-md-5 col-sm-6 input_icon address">
                                            <div class="mandatory">*</div>
                            <input type="text" name="address2" class="cst_input_primary addressvalid" maxlength="255" placeholder="Address-2" value="{{ $address2 or ''}}">
                        </div>
                    </div>
                    <div class="clearfix">
                        <div class="col-md-offset-1 col-md-10 col-sm-12 padd_none">
                             <?php
                                   if (old('pincode'))
                                    $pincode = old('pincode');
                                   elseif ($pincode)
                                    $pincode = $newuser->sc_pincode;
                                   else
                                    $pincode = '';
                            ?>
                            <div class="col-md-3 col-sm-3 pincode input_icon ">
                            <div class="mandatory">*</div>
                                <input type="text" name="pincode" class="cst_input_primary onlyNumber" maxlength="6" minimum="6"  placeholder="Zip Code"  value="{{ $pincode or ''}}">
                            </div>
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
                            <div class="col-md-3 col-sm-3 input_icon city_icon">                                
                                <div class="mandatory">*</div>
                                <div class="select-style">
                                    <select name="country" onchange="getDataOfState(this.value)" onclick="getDataOfState(this.value)">
                                        <option value="">Country</option>
                                        <?php foreach($countries as $key=>$val){?>
                                        <option value="{{$val->id}}" <?php
                                    if (isset($country) && $country != '' && $country == $val->id) {
                                        echo 'selected="selected"';
                                    }
                                    ?> >{{$val->c_name}}</option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <em for="country" class="invalid"></em>                                                                                         
                            </div>
                            <div class="col-md-3 col-sm-3 input_icon state_icon">
                              <div class="mandatory">*</div>
                                <div class="select-style">
                                    <select id="state_name" name="state" onchange="getDataOfCity(this.value)" onclick="getDataOfCity(this.value)">
                                        <option value="">State</option>
                                    </select>
                                </div>
                                <em for="state_name" class="invalid"></em>
                            </div>
                            <div class="col-md-3 col-sm-3 input_icon country">
                                <div class="mandatory">*</div>
                                <div class="select-style">
                                    <select id="city_name" name="city">
                                        <option value="">City</option>
                                    </select>
                                </div>
                                <em for="city_name" class="invalid"></em>                              
                            </div>
                        </div>
                    </div>
                    <div>
                        <h1><span class="title_border">Contact Person</span></h1>
                        <div class="clearfix">
                            <?php
                                if (old('first_name'))
                                    $first_name = old('first_name');
                                elseif ($first_name)
                                    $first_name = $newuser->sc_first_name;
                                else
                                    $first_name = '';
                            ?>
                            <div class="col-md-offset-1 col-md-5 col-sm-6 input_icon name">
                              <div class="mandatory">*</div>
                                <input type="text" name="first_name" class="cst_input_primary alphaonly" maxlength="50" placeholder="First name" value="{{ $first_name or ''}}">
                            </div>
                                <?php
                                     if (old('last_name'))
                                        $last_name = old('last_name');
                                    elseif ($last_name)
                                        $last_name = $newuser->sc_last_name;
                                    else
                                        $last_name = '';
                                    ?>
                            <div class=" col-md-5 col-sm-6 input_icon name">
                              <div class="mandatory">*</div>
                                <input type="text" name="last_name" class="cst_input_primary addressvalid" maxlength="50" placeholder="Last name"  value="{{ $last_name or ''}}">
                            </div>
                        </div>
                        <div class="clearfix">
                            <?php
                                if (old('title'))
                                    $title = old('title');
                                elseif ($title)
                                    $title = $newuser->sc_title;
                                else
                                    $title = '';
                            ?>
                            <div class="col-md-offset-1 col-md-5 col-sm-6 input_icon title_icon">
                              <div class="mandatory">*</div>
                                <input type="text" name="title" class="cst_input_primary" maxlength="50" placeholder="Title" value="{{ $title or ''}}">
                            </div>
                                <?php
                                    if (old('phone'))
                                        $phone = old('phone');
                                    elseif ($phone)
                                        $phone = $newuser->sc_phone;
                                    else
                                        $phone = '';
                                ?>
                            <div class="col-md-5 col-sm-6 mobile input_icon ">
                              <div class="mandatory">*</div>
                                <input type="text" name="phone" class="cst_input_primary onlyNumber" maxlength="10" placeholder="Mobile Number" value="{{ $phone or ''}}">
                            </div>
                        </div>
                        <div class="clearfix">
                             <?php
                                if (old('email'))
                                    $email = old('email');
                                elseif ($email)
                                    $email = $newuser->sc_email;
                                else
                                    $email = '';
                                ?>
                            <div class="col-md-offset-1 col-md-5 col-sm-6 input_icon email">
                              <div class="mandatory">*</div>
                                <input type="text" name="email" class="cst_input_primary" maxlength="100" placeholder="Email" value="{{ $email or ''}}">
                            </div>
                                <?php
                                    if (old('password'))
                                        $password = old('password');
                                    elseif ($password)
                                        $password = $newuser->sc_password;
                                    else
                                        $password = '';
                                ?>
                            <div class="col-md-5 col-sm-6 input_icon password">
                              <div class="mandatory">*</div>
                                <input type="password" name="password" class="cst_input_primary pass-visi" maxlength="20" placeholder="Password" id="password">
                                <span class="visibility-pwd">
                                    <img src="https://proteenlive-old.s3.ap-south-1.amazonaws.com/img/view.png" alt="view" class="view img">
                                    <img src="https://proteenlive-old.s3.ap-south-1.amazonaws.com/img/hide.png" alt="view" class="img-hide hide img">
                                </span>
                                <em style="color:red" id="pass_validation">  </em>
                            </div>
                        </div>
                        <div class="clearfix">
                        </div>
                    </div>
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
                            <p><a href="{{url('school/forgot-password')}}" title="Forgot password?">Forgot password?</a> Already enrolled? <a href="{{url('school/login')}}" title="Sign in">Sign in now</a>.</p>
                        </div>
                    </div>
                </form>
            </div>
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

    jQuery(document).ready(function () {
            $('#terms_statement .modal-body').mCustomScrollbar();
            $('#privacy_statement .modal-body').mCustomScrollbar();
            $('.alphaonly').bind('keyup blur', function () {
            var node = $(this);
            node.val(node.val().replace(/[^a-zA-Z ]/g, ''));
            }
            );

            $('.addressvalid').bind('keyup blur', function () {
                var node = $(this);
                node.val(node.val().replace(/[^0-9a-zA-Z-&,./ ]/g, ''));
            }
            );

            $('#mychoice').click(function () {
                if ($('#mychoice').is(':checked')) {
                    $('#myModal').modal('show');
                }
            });
            $('.sponsor_img img').click(function () {
                $(this).next().children('input').trigger('click');
            });
        var signupRules = {
            school_name: {
                required: true,
                minlength: 3
            },
            address1: {
                required: true,
                minlength: 3
            },
            address2: {
                required: true,
                minlength: 3
            },
            city: {
                required: true
            },
            state: {
                required: true
            },
            first_name: {
                required: true,
                maxlength: 50,
                minlength: 3
            },
            last_name: {
                required: true,
                maxlength: 50,
                minlength: 3
            },
            title: {
                required: true,
                minlength: 2
            },
            email: {
                required: true,
                email:true
            },
            password: {
                required: function(el) {
                    checkPassword()
                },
            },
            country: {
                required: true
            },
            pincode: {
                required: true,
                minlength: 5,
                maxlength: 6
            },
            phone: {
                required : true,
                minlength: 10,
                maxlength: 11
            }
        };
        $("#school_registration_form").validate({
            rules: signupRules,
            messages: {
                school_name: {
                    required: '<?php echo trans('validation.schoolnamerequiredfield') ?>'
                },

                address1: {
                    required: '<?php echo trans('validation.address1requiredfield') ?>'
                },
                address2: {
                    required: '<?php echo trans('validation.address2requiredfield') ?>'
                },
                city: {
                    required: 'City is required'
                },
                state: {
                    required: 'State is required'
                },
                first_name: {
                    required: '<?php echo trans('validation.firstnamerequiredfield') ?>'
                },
                last_name: {
                    required: '<?php echo trans('validation.lastnamerequiredfield') ?>'
                },
                title: {
                    required: 'Title is required'
                },
                email: {required: '<?php echo trans('validation.emailrequired') ?>'
                },
                password: {required: 'Password required'
                },
                country: {required: 'Country is required'
                },
                pincode: {required: '<?php echo trans('validation.pincoderequired') ?>'
                },
                phone: {required: '<?php echo trans('Mobile number only') ?>'
                }
            }
        });
        $(".profilePhoto").change(function (e) {
            var ext = this.value.match(/\.(.+)$/)[1];
            switch (ext)
            {
                case 'jpg':
                case 'bmp':
                case 'png':
                case 'jpeg':
                    break;
                default:
                    alert('Image type not allowed');
                    this.value = '';
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
    $(".datepicker").datepicker({
        minDate: -6935, maxDate: -4380,
        yearRange: "-18:-13",
        changeMonth: true,
        changeYear: true,
        dateFormat: 'mm/dd/yy',
        defaultDate: null
    }).on('change', function () {
        $(this).valid();
    });

    $(".datepicker").change(function () {
        var dob = $(".datepicker").val();
        var myDate = new Date(dob);
        var currentYear = (new Date).getFullYear();
        var mydate_fullyear = myDate.getFullYear();
        if(dob != ''){
            if (validateDate(dob))
            {
                if (mydate_fullyear < 1900) {
                    $('#next1').attr('disabled', 'disabled');
                    $('.extraBirthValidate').text("You are not Teenager");
                } else if (mydate_fullyear >= currentYear) {
                    $('#next1').attr('disabled', 'disabled');
                    $('.extraBirthValidate').text("You are not Teenager");
                } else if(mydate_fullyear > (currentYear - 13) && mydate_fullyear > (currentYear - 18)){
                    $('#next1').attr('disabled', 'disabled');
                    $('.extraBirthValidate').text("You are not Teenager");
                } else {
                    $('#next1').removeAttr("disabled");
                    $('.extraBirthValidate').text("");
                }
            } else
            {
            $('#next1').attr('disabled', 'disabled');
            $('.extraBirthValidate').text("Date formate is mm/dd/yyyy");
        }
        }
    });
    function validateDate(txtDate) {
        var txtVal = $(".datepicker").val();
        var filter = new RegExp("(0[123456789]|10|11|12)([/])([0-3][0-9])([/])([1-2][0-9][0-9][0-9])");
        if(filter.test(txtDate))
            return true;
        else
            return false;
    }

var postCountry = '<?php echo $country ?>';
if(postCountry>0 && postCountry != ''){
    getDataOfState(postCountry);
}

function getDataOfState(countryId)
{
    $("#state_name").empty();
    $("#city_name").empty();
    $.ajax({
        type: 'GET',
        url: '/get-state/' + countryId,
        dataType: "JSON",
        success: function(JSON){
            $("#state_name").empty()
            for(var i=0;i<JSON.length;i++){
                $("#state_name").append($("<option></option>").val(JSON[i].id).html(JSON[i].s_name))
            }
        }
    });

}
var postState = '<?php echo $state ?>';
if(postState>0 && postState != ''){
    getDataOfCity(postState);
}
function getDataOfCity(stateId)
{
    $.ajax({
        type: 'GET',
        url: '/get-city/' + stateId,
        dataType: "JSON",
        success: function(JSON){
            $("#city_name").empty()
            for(var i=0;i<JSON.length;i++){
                $("#city_name").append($("<option></option>").val(JSON[i].id).html(JSON[i].c_name))
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