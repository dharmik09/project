@extends('layouts.school-master')

@section('content')

<div class="col-xs-12">
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
<div class="centerlize">
        <div class="container">
            <div class="container_padd detail_container">
                <form class="registration_form" id="school_registration_form" role="form" enctype="multipart/form-data" method="POST" action="{{ url('/school/save-profile') }}" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <div class="col-md-offset-1 col-md-10 col-sm-12 teen_relative">
                        <span class="teen_id"><span>School Reference : </span>{{ $user->sc_uniqueid or ''}}</span>
                    </div>
                     <h1><span class="title_border">Update Profile</span></h1>
                    <div class="clearfix">
                        <div class="col-md-offset-1 col-md-5 col-sm-6 input_icon company_name">
                            <div class="mandatory">*</div>
                            <input type="text" name="school_name" class="cst_input_primary" minlength="3" maxlength="255" placeholder="School Name" value="{{ $user->sc_name or ''}}">
                        </div>
                        <div class="col-md-5 col-sm-6 u_image">
                            <div class="sponsor_detail clearfix">
                                <div class="pull-left">              
                                    <div class="sponso_image">
                                        <div class="upload_image">
                                            <input type="file" onchange="readURL(this);" name="logo"  class="profilePhoto" accept=".png, .jpg, .jpeg, .bmp" />
                                                <div class="placeholder_image sponsor update_profile">
                                                    <div class="mandatory">*</div>
                                                    <span>
                                                    <?php $school = Helpers::getSchoolOriginalImageUrl($user->sc_logo);
                                                        if (isset($user->sc_logo) && $user->sc_logo != '') {
                                                    ?>
                                                        <img src="{{Storage::url($school)}}"/>
                                                    <?php } else { ?>
                                                        <span><img src="{{Storage::url('frontend/images/proteen_logo.png')}}"/></span>
                                                        <p><span>Upload Your Logo</span></p>
                                                    <?php } ?>
                                                    </span>
                                                 </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="pull-right">
                                    <div class="user_image">
                                        <div class="upload_image">
                                                <input type="file" onchange="readURL(this);" name="photo"  class="profilePhoto" accept=".png, .jpg, .jpeg, .bmp" />
                                                    <div class="placeholder_image update_profile">
                                                        <span>
                                                        <?php $contactperson = Helpers::getContactpersonOriginalImageUrl($user->sc_photo);
                                                            if (isset($user->sc_photo) && $user->sc_photo != '') {
                                                        ?>
                                                            <img src="{{Storage::url($contactperson)}}"/>
                                                        <?php } else { ?>
                                                            <span><img src="{{Storage::url('frontend/images/proteen_logo.png')}}"/></span>
                                                            <p><span>Upload sponsor photo</span></p>
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
                            <input type="text" name="address1" class="cst_input_primary addressvalid" minlength="3" maxlength="255" placeholder="Address-1" value="{{ $user->sc_address1 or ''}}">
                        </div>
                    </div>
                    <div class="clearfix">
                        <div class="col-md-offset-1 col-md-5 col-sm-6 input_icon address">
                            <div class="mandatory">*</div>
                            <input type="text" name="address2" class="cst_input_primary addressvalid" minlength="3" maxlength="255" placeholder="Address-2" value="{{ $user->sc_address2 or ''}}">
                        </div>
                    </div>
                    <div class="clearfix">
                        <div class="col-md-offset-1 col-md-10 col-sm-12 padd_none">
                            <div class="col-md-3 col-sm-3 pincode input_icon ">
                                <div class="mandatory">*</div>
                                <input type="text" name="pincode" class="cst_input_primary onlyNumber" minlength="6" maxlength="6" placeholder="Zip Code" value="{{ $user->sc_pincode or ''}}">
                            </div>
                            <!-- country start -->
                            <div class="col-md-3 col-sm-3 input_icon country">
                                <?php $countries = Helpers::getCountries(); ?>
                                <div class="mandatory">*</div>
                                <div class="select-style">
                                    <select name="country" onchange="getDataOfState(this.value)">
                                        <option value="">Select</option>
                                        <?php foreach ($countries as $key => $val) { ?>
                                        <option value="{{$val->id}}" <?php if (isset($user->sc_country) && $user->sc_country == $val->id) {
                                        echo "selected='selected'";
                                        } ?> >{{$val->c_name}}</option>
                                        <?php } ?>
                                    </select>
                                </div>
                                    <em for="country" class="invalid"></em>
                            </div>                                                                                                              
                            <div class="col-md-3 col-sm-3 input_icon state_icon">
                                <div class="mandatory">*</div>
                                <div class="select-style">
                                    <?php $states =  Helpers::getStates($user->sc_country); ?>
                                    <select id="state_name" name="state" onchange="getDataOfCity(this.value)">
                                        <option value="">Select</option>
                                        <?php foreach ($states as $key => $val) { ?>
                                        <option value="{{$val->id}}" <?php if (isset($user->sc_state) && $user->sc_state == $val->id) {
                                        echo "selected='selected'";
                                        } ?> >{{$val->s_name}}</option>
                                        <?php } ?>
                                    </select>
                                </div>
                                    <em for="state_name" class="invalid"></em>
                            </div>                                                                                                                
                            <div class="col-md-3 col-sm-3 input_icon city_icon">
                                <?php $cities =  Helpers::getCities($user->sc_state); ?>
                                <div class="mandatory">*</div>
                                <div class="select-style">
                                    <select id="city_name" name="city">
                                        <option value="">Select</option>
                                        <?php foreach ($cities as $key => $val) { ?>
                                        <option value="{{$val->id}}" <?php if (isset($user->sc_city) && $user->sc_city == $val->id) {
                                        echo "selected='selected'";
                                        } ?> >{{$val->c_name}}</option>
                                        <?php } ?>
                                    </select>
                                </div>
                                    <em for="city_name" class="invalid"></em>
                            </div>                            
                        </div>
                    </div>
                    <div>
                        <h1><span class="title_border">Contact Person</span></h1>
                        <div class="clearfix">
                            <div class="col-md-offset-1 col-md-5 col-sm-6 input_icon name">
                                <div class="mandatory">*</div>
                                <input type="text" name="first_name" class="cst_input_primary alphaonly" minlength="3" maxlength="50" placeholder="First Name" value="{{ $user->sc_first_name or ''}}">
                            </div>
                            <div class=" col-md-5 col-sm-6 input_icon name">
                                <div class="mandatory">*</div>
                                <input type="text" name="last_name" class="cst_input_primary alphaonly" minlength="3" maxlength="50" placeholder="Last Name" value="{{ $user->sc_last_name or ''}}">
                            </div>
                        </div>
                        <div class="clearfix">
                            <div class="col-md-offset-1 col-md-5 col-sm-6 input_icon title_icon">
                                <div class="mandatory">*</div>
                                <input type="text" name="title" class="cst_input_primary alphaonly" minlength="3" maxlength="50" placeholder="Title" value="{{ $user->sc_title or ''}}">
                            </div>
                            <div class="col-md-5 col-sm-6 mobile input_icon ">
                                <div class="mandatory">*</div>
                                <input type="text" name="phone" class="cst_input_primary onlyNumber" minlength="10" maxlength="11" placeholder="Mobile" value="{{ $user->sc_phone or ''}}">
                            </div>
                        </div>
                        <div class="clearfix">
                            <div class="col-md-offset-1 col-md-5 col-sm-6 input_icon email">
                                <div class="mandatory">*</div>
                                <input type="text" name="email" id="email" readonly="readonly" class="cst_input_primary" placeholder="{{trans('labels.emaillbl')}}" value="{{ $user->sc_email or '' }}" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false">
                            </div>                         
                        </div>
                    </div>
                    <div class="button_container">
                        <div class="submit_register">
                            <input type="submit" class="btn primary_btn" id="next1" name="submit" value="Update">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>


@stop
@section('script')

<script type="text/javascript">

    jQuery(document).ready(function () {
            $('.alphaonly').bind('keyup blur', function () {
            var node = $(this);
            node.val(node.val().replace(/[^a-zA-Z ]/g, ''));
            }
            );

            $('.addressvalid').bind('keyup blur', function () {
                var node = $(this);
                node.val(node.val().replace(/[^0-9a-zA-Z-&,. ]/g, ''));
            }
            );
        
            $("#email").keypress(function(){
                return false; 
            });
        
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
                required: true,
                minlength: 6,
                maxlength: 20
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
                password: {required: 'Password required',
                    maxlength: 'Password maximum range is 20',
                    minlength: 'Password length is minimum 6'
                },
                country: {required: 'Country is required'
                },
                pincode: {required: '<?php echo trans('validation.pincoderequired') ?>',
                    minlength: 'Required 6 character'
                },
                phone: {required: '<?php echo trans('Mobile number only') ?>',
                        minlength: 'Required 10 character'
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
</script>

@stop