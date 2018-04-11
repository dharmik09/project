@extends('layouts.teenager-master')

@push('script-header')
    <title>Teenager : Profile</title>
@endpush

@section('content')
    <!--mid section-->
    <!-- profile section-->
    <section class="sec-profile sponsor-overflow" id="profile-info">
        <div class="container">
            <div class="col-xs-12">
                @if ($message = Session::get('success'))
                <div class="row">
                    <div class="col-md-12">
                        <div class="box-body">
                            <div class="alert alert-success alert-dismissable">
                                <button aria-hidden="true" data-dismiss="alert" class="close" type="button">X</button>
                                <h4><i class="icon fa fa-check"></i> {{trans('validation.successlbl')}}</h4>
                                {{ $message }}
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                @if ($message = Session::get('error'))
                <div class="row">
                    <div class="col-md-8 col-md-offset-2 invalid_pass_error">
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
                @if (count($errors) > 0)
                <div class="alert alert-danger danger">
                    <strong>{{trans('validation.whoops')}}</strong>
                    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">X</button>
                    {{trans('validation.someproblems')}}<br><br>
                    <ul>
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
            </div>
            <!--profile detail-->
            <div class="profile-detail">
                <form id="teenager_set_profile_form" role="form" enctype="multipart/form-data" method="POST" action="{{ url('/teenager/save-profile') }}" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <div class="row">
                        <div class="col-sm-3">
                            <div class="upload-img profile-img" id="img-preview">
                                <span style="background-image: url({{ $data['user_profile'] }})"></span>
                                <input type="file" name="pic" accept="image/*" onchange="readURL(this);" title="Edit Profile image">
                            </div>                            
                        </div>
                        <?php
                            if($user->t_pincode != "")
                            {
                                $getLocation = file_get_contents('http://maps.googleapis.com/maps/api/geocode/json?address='.$user->t_pincode.'&sensor=true');
                                $getCityArea = ( isset(json_decode($getLocation)->results[0]->address_components[1]->long_name) && json_decode($getLocation)->results[0]->address_components[1]->long_name != "" ) ? json_decode($getLocation)->results[0]->address_components[1]->long_name : "Default";
                            } else {
                                $getCityArea = ( Auth::guard('teenager')->user()->getCountry->c_name != "" ) ? Auth::guard('teenager')->user()->getCountry->c_name : "Default";
                            }
                        ?>
                        <div class="col-sm-9">
                            <h1>{{ $user->t_name }} {{ $user->t_lastname }}</h1>
                            <ul class="area-detail">
                                <li id="defaultArea">
                                    <?php
                                        if ($user->t_location != "") {
                                            $getCityArea = $user->t_location;
                                        } else if ($user->t_pincode != "") {
                                            $getLocation = json_decode(file_get_contents('http://maps.googleapis.com/maps/api/geocode/json?address='.$user->t_pincode.'&sensor=true'));
                                            $getCityArea = ( isset($getLocation->results[0]->address_components[1]->long_name) && $getLocation->results[0]->address_components[1]->long_name != "" ) ? $getLocation->results[0]->address_components[1]->long_name : "Default";
                                        } else {
                                            $getCityArea = ( Auth::guard('teenager')->user()->getCountry->c_name != "" ) ? Auth::guard('teenager')->user()->getCountry->c_name : "Default";
                                        }
                                    ?>
                                    {{ $getCityArea }} Area
                                </li>
                            </ul>                            
                            
                            <div class="about-info-block">
                                <p id="display-about-info" style="">{{ ($user->t_about_info != "") ? $user->t_about_info : 'Describe yourself' }}
                                    <a id="editInfo" href="javascript:void(0);" title="Describe yourself" class="editInfo">
                                        <img src="{{Storage::url('img/edit.png')}}" alt="Describe yourself">
                                    </a>
                                </p>
                                <textarea class="form-control about-info" id="t_about_info" name="t_about_info" placeholder="Describe yourself" value="{{ $user->t_about_info }}" style="display: none;" rows="4">{{ $user->t_about_info }}</textarea>
                                <a id="editInfo" href="javascript:void(0);" title="Edit Info" class="editInfo hide editInfo-outer">
                                    <img src="{{Storage::url('img/edit.png')}}" alt="Describe yourself">
                                </a>
                            </div>
                        </div>
                    </div>
                    <!--profile form-->
                    <div class="profile-form">
                        <div class="clearfix row flex-container">
                            <div class="col-sm-6 col-xs-12 flex-items">
                                <div class="form-group">
                                    <input type="text" class="form-control alphaonly" id="name" name="name" placeholder="first name *" tabindex="1" value="{{ $user->t_name }}" required maxlength="50">
                                </div>
                            </div>
                            <div class="col-sm-6 col-xs-12 flex-items">
                                <div class="form-group">
                                    <input type="text" class="form-control alphaonly" id="lastname" name="lastname" placeholder="last name *" tabindex="2" value="{{ $user->t_lastname }}" required maxlength="50">
                                </div>
                            </div>
                            <div class="col-sm-6 col-xs-12 flex-items">
                                <div class="form-group">
                                    <input type="email" class="form-control" id="email" name="email" placeholder="email *" tabindex="3" value="{{ $user->t_email }}" readonly>
                                </div>
                            </div>
                            <div class="col-sm-6 col-xs-12 flex-items">
                                <div class="form-group">
                                    <input type="text" class="form-control onlyNumber" id="phone" name="phone" placeholder="phone" minlength="7" maxlength="10" tabindex="5" value="{{ $user->t_phone_new }}">
                                </div>
                            </div>
                            <div class="col-sm-6 col-xs-12 flex-items">
                                <div class="form-group input-group">
                                    <div class="clearfix">
                                        <span id="countrycode" class="input-group-addon">+91</span>
                                        <input type="text" class="form-control onlyNumber" id="mobile" name="mobile" maxlength="10" placeholder="mobile phone" tabindex="6" value="{{ $user->t_phone }}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-xs-12 flex-items">
                                <div class="form-group custom-select">
                                    <select tabindex="7" class="form-control" id="country" name="country" onchange="getPhoneCodeByCountry(this.value);" required>
                                        <option value="">country</option>
                                        @forelse($countries as $val)
                                            <option value="{{$val->id}}" <?php echo ($user->t_country == $val->id ) ? "selected='selected'" : ''; ?> > {{$val->c_name}} </option>
                                        @empty
                                        @endforelse
                                    </select>
                                    <input type="hidden" name="country_phone_code" id="country_phone_code" readonly="readonly" id="country_phone_code" class="cst_input_primary" maxlength="10" placeholder="Phone Code" value="{{old('country_phone_code')}}">
                                </div>
                            </div>
                            <div class="col-sm-6 col-xs-12 flex-items">
                                <div class="form-group">
                                    <input type="text" class="form-control" id="pincode" name="pincode" placeholder="zip code *" tabindex="8" value="{{ $user->t_pincode }}">
                                </div>
                            </div>
                            <div class="col-sm-6 col-xs-12 flex-items">
                                <div class="form-group">
                                    <input type="password" class="form-control pass-visi" id="password" name="password" placeholder="password" tabindex="11" value="" maxlength="16">
                                    <span class="visibility-pwd">
                                        <img src="{{ Storage::url('img/view.png') }}" alt="view" class="view img">
                                        <img src="{{ Storage::url('img/hide.png') }}" alt="view" class="img-hide hide img">
                                    </span>
                                    <span class="password-info">Type password to change your current password</span>
                                    <em id="pass_validation">  </em>
                                </div>
                            </div>
                            <div class="col-sm-6 col-xs-12 flex-items">
                                <div class="form-group custom-select">
                                    <select tabindex="9" class="form-control" id="gender" name="gender" required >
                                        <option value="1" <?php echo (old('gender') && old('gender') == 1) ? "selected='selected'" : ''; ?> >Male</option>
                                        <option value="2" <?php echo (old('gender') && old('gender') == 2) ? "selected='selected'" : ''; ?> >Female</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-6 col-xs-12 flex-items">
                                <div class="form-group">
                                    <input type="text" class="form-control nospace" id="proteen_code" name="proteen_code" placeholder="proteen code" tabindex="10" value="{{ $user->t_nickname }}">
                                </div>
                            </div>
                            <?php 
                                $birthYear = $birthMonth = $birthDay = "";
                                if(isset($user->t_birthdate) && $user->t_birthdate != "") {
                                    list($birthYear, $birthMonth, $birthDay) = explode("-", $user->t_birthdate);
                                }
                            ?>
                            <div class="col-sm-9 col-sm-offset-3 text-right">
                                <div class="form-group date-sec">
                                    <label>Date of Birth:</label>
                                    <div class="date-feild">
                                        <select name="month" class="form-control date-block" id="month" tabindex="13">
                                            <option value="">mm</option>
                                            @for($month = 01; $month <= 12; $month++)
                                                <option value="{{date('m', mktime(0,0,0,$month, 1, date('Y')))}}" <?php echo ($month == $birthMonth) ? "selected='selected'" : ''; ?> >{{ date('M', mktime(0,0,0,$month, 1, date('Y'))) }}</option>
                                            @endfor
                                        </select>
                                        <select name="day" class="form-control date-block" id="day" tabindex="14" >
                                            <option value="">dd</option>
                                            @for($day = 1; $day <= 31; $day++)
                                                <option value="{{date('d', mktime(0,0,0,0, $day, date('Y')))}}" <?php echo ($day == $birthDay) ? "selected='selected'" : ''; ?> >{{ date('d', mktime(0,0,0,0, $day, date('Y'))) }}</option>
                                            @endfor
                                        </select>
                                        <select name="year" class="form-control date-block" id="year" tabindex="15">
                                            <option value="">yyyy</option>
                                            @foreach(range(\Carbon\Carbon::now()->year, 1950) as $year)
                                                <option value="{{$year}}" <?php echo ($year == $birthYear) ? "selected='selected'" : ''; ?> >{{$year}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="birth-error" style="text-align: left;padding-left:35%;"></div>
                            </div>
                            <div class="col-sm-12">
                                <div class="sponsor-sec">
                                    <div class="sponsor-content">
                                        <p>Select at least one sponsor.</p>
                                        <p>Benefits can include sponsored events, contests, scholarships and coupons.</p>
                                        <div class="form-register sponsor-list owl-carousel">
                                            @forelse($sponsorDetail as $key => $value)
                                                <div class="checkbox">
                                                    <label>
                                                        <input type="checkbox" name="selected_sponsor[]" id="sponsor_{{$key}}" value="{{$value->sponsor_id}}" <?php echo (!empty($teenSponsorIds) && in_array($value->sponsor_id, $teenSponsorIds) ) ? "checked" : ""; ?> />
                                                        <span class="checker"></span>
                                                        <span class="logo-icon">
                                                            <?php
                                                                $sponsor_logo = ($value->sp_logo != "" && Storage::size(Config::get('constant.SPONSOR_THUMB_IMAGE_UPLOAD_PATH').$value->sp_logo) > 0) ? Storage::url(Config::get('constant.SPONSOR_THUMB_IMAGE_UPLOAD_PATH').$value->sp_logo) : asset(Config::get('constant.SPONSOR_THUMB_IMAGE_UPLOAD_PATH') . 'proteen-logo.png');
                                                            ?>
                                                            <img src="{{ $sponsor_logo }}" alt="{{ $value->sp_company_name }}" style="height:74px; width:127px;">
                                                        </span>
                                                        <span class="sponsor-name">{{ str_limit($value->sp_company_name, $limit = 100, $end = '..') }}</span>
                                                    </label>
                                                </div>
                                            @empty

                                            @endforelse
                                        </div>
                                        <div class="sponsor-error"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-7 text-right">
                                <ul class="toggle-switch">
                                    <li>Public profile
                                        <label class="switch">
                                            <input type="checkbox" id="public_profile" name="public_profile" <?php echo (isset($user->is_search_on) && $user->is_search_on == '1') ? "checked='checked'": '' ?> value="1">
                                            <span class="slider round">
                                                <span class="on">On</span>
                                                <span class="off">Off</span>
                                            </span>
                                        </label>
                                    </li>
                                    <li>Share info with other members
                                        <label class="switch">
                                            <input type="checkbox" id="share_with_members" name="share_with_members" <?php echo (isset($user->is_share_with_other_members) && $user->is_share_with_other_members == '1') ? "checked='checked'": '' ?> value="1">
                                            <span class="slider round">
                                                <span class="on">On</span>
                                                <span class="off">Off</span>
                                            </span>
                                        </label>
                                    </li>
                                    <li>Share info with parents
                                        <label class="switch">
                                            <input type="checkbox" id="share_with_parents" name="share_with_parents" <?php echo (isset($user->is_share_with_parents) && $user->is_share_with_parents == '1') ? "checked='checked'": '' ?> value="1">
                                            <span class="slider round">
                                                <span class="on">On</span>
                                                <span class="off">Off</span>
                                            </span>
                                        </label>
                                    </li>
                                    <li>Share info with teachers
                                        <label class="switch">
                                            <input type="checkbox" id="share_with_teachers" name="share_with_teachers" <?php echo (isset($user->is_share_with_teachers) && $user->is_share_with_teachers == '1') ? "checked='checked'": '' ?> value="1">
                                            <span class="slider round">
                                                <span class="on">On</span>
                                                <span class="off">Off</span>
                                            </span>
                                        </label>
                                    </li>
                                    <li>Notifications
                                        <label class="switch">
                                            <input type="checkbox" id="notifications" name="notifications" <?php echo (isset($user->is_notify) && $user->is_notify == '1') ? "checked='checked'": '' ?> value="1">
                                            <span class="slider round">
                                                <span class="on">On</span>
                                                <span class="off">Off</span>
                                            </span>
                                        </label>
                                    </li>
                                    <li>View information for
                                        <label class="switch">
                                        <input type="checkbox" id="t_view_information" name="t_view_information" <?php echo (isset($user->t_view_information) && $user->t_view_information == '1') ? "checked='checked'": '' ?> value="1">
                                            <span class="slider round">
                                              <span class="on">USA</span>
                                              <span class="off">India</span>
                                            </span>
                                        </label>
                                    </li>
                                </ul>
                            </div>
                            <div class="text-center col-sm-12">
                                <button id="saveProfile" class="btn btn-submit btn-default" type="submit" title="Submit">Submit</button>
                                <span class="hand-icon"><i class="icon-hand-simple"></i></span>
                            </div>
                        </div>
                    </div>
                    <!--profile form end-->
                </form>
            </div>
            <!--profile detail end-->
        </div>
    </section>
    <!-- profile section-->
    
@stop

@section('script')
<script>

    $('.onlyNumber').on('keyup', function() {
        this.value = this.value.replace(/[^0-9]/gi, '');
    });
    $('.alphaonly').bind('keyup blur', function() {
        var node = $(this);
        node.val(node.val().replace(/[^a-zA-Z_' ]/g, ''));
    });
    $('.nospace').bind('keyup blur', function() {
        var node = $(this);
        node.val(node.val().replace(/\s/g, ''));
    });
    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                var a = document.querySelector("#img-preview");
                if (input.files[0].size > 3000000) {
                    alert("File size is too large. Maximum 3MB allowed");
                    $(this).val('');
                } else {
                    a.style.backgroundImage = "url('" + e.target.result + "')";
                    // document.getElementById("#").className = "activated";
                    a.className = "upload-img activated";
                }
            };

            reader.readAsDataURL(input.files[0]);
        }
    }
        
    $(document).ready(function() {
        $("#t_about_info").hide();
        $('.sponsor-list').owlCarousel({
            loop: false,
            margin: 20,
            items: 2,
            autoplay: false,
            autoplayTimeout: 3000,
            smartSpeed: 1000,
            nav: true,
            dots: false,
            responsive: {
                0: {
                    items: 1
                },
                768: {
                    items: 2
                },
            }
        });
        jQuery.validator.addMethod("lettersonly", function(value, element) {
            return this.optional(element) || /^[a-z_'\s]+$/i.test(value);
        }, "Letters only please");
        jQuery.validator.addMethod("mobilelength", function(value, element) {
            return this.optional(element) || /^\d{10}$/i.test(value);
        }, "Please enter valid mobile number");
        var setUpProfileRules = {
            name: {
                required: true,
                minlength: 3,
                maxlength: 50,
                lettersonly: true
            },
            lastname: {
                required: true,
                minlength: 3,
                maxlength: 50,
                lettersonly: true
            },
            email: {
                required: true,
                email: true,
                maxlength : 100
            },
            country: {
                required: true
            },
            pincode: {
                required: true,
                minlength: 5,
                maxlength: 6,
                number: true
            },
            gender: {
                required: true
            },
            year: {
                required: true,
            },
            month: {
                required: true,
            },
            day: {
                required: true,
            },
            'selected_sponsor[]': {
                required: true
            },
            mobile: {
                mobilelength: true,
                number: true
            },
            phone: {
                minlength: 7,
                maxlength: 10,
                number: true
            }
        };
        
        $("#teenager_set_profile_form").validate({
            rules: setUpProfileRules,
            messages: {
                name: {
                    required: "First name is required",
                },
                lastname: {
                    required: "Last name is required",
                },
                email: {
                    required: "Email is required",
                },
                country: {
                    required: "Country is required"
                },
                pincode: {
                    required: "Zipcode is required",
                },
                gender: {
                    required: "Gender is required",
                },
                year: {
                    required: "Year is required",
                },
                month: {
                    required: "Month is required",
                },
                day: {
                    required: "Day is required",
                },
                'selected_sponsor[]': {
                    required: "Please select atleast one sponsor",
                }
            },
            errorPlacement: function(error, element) {
                if(element.attr("name") == "selected_sponsor[]") {
                    error.appendTo(".sponsor-error");
                }else if(element.attr("name") == "day" || element.attr("name") == "year" || element.attr("name") == "month"){
                    error.appendTo(".birth-error");
                } else {
                    error.insertAfter(element)
                }
            },
        });
        
        // Cache the toggle button
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
        var countryCode = $("#country").val();
        if (countryCode) {
            getPhoneCodeByCountry(countryCode);
        }
        $('#email').attr('readonly', true);
    });
    
    $("input[type=password]").keyup(function() {
        var password = $(this).val();
        if (password == '') {
            $("#pass_validation").text('');
            $("#pass_validation").removeClass('invalid');
            $("#saveProfile").removeAttr("disabled");
            return true;
        } else if (password.length < 6) {
            $("#pass_validation").addClass('invalid');
            $("#pass_validation").text('Use at least 6 characters');
            $("#saveProfile").attr("disabled", "disabled");
            return true;
        } else if (password.length > 20) {
            $("#pass_validation").addClass('invalid');
            $("#pass_validation").text('Password maximum range is 20');
            $("#saveProfile").attr("disabled", "disabled");
            return true;
        } else if (password.search(/\d/) == -1) {
            $("#pass_validation").addClass('invalid');
            $("#pass_validation").text('Use at least one number');
            $("#saveProfile").attr("disabled", "disabled");
            return true;
        } else if (password.search(/[a-zA-Z]/) == -1) {
            $("#pass_validation").addClass('invalid');
            $("#pass_validation").text('Use at least one character');
            $("#saveProfile").attr("disabled", "disabled");
            return true;
        } else if (password.search(/[!\@\#\$\%\^\&\*\(\)\_\+]/) == -1) {
            $("#pass_validation").addClass('invalid');
            $("#pass_validation").text('Use at least one special character');
            $("#saveProfile").attr("disabled", "disabled");
            return true;
        } else {
            $("#pass_validation").removeClass('invalid');
            $("#pass_validation").text('');
            $("#saveProfile").removeAttr("disabled");
            return false;
        }
    });
    
    function getPhoneCodeByCountry(country_id) {
        $.ajax({
            url: "{{ url('teenager/get-phone-code-by-country-for-profile') }}",
            type: 'post',
            data: {
                "_token": '{{ csrf_token() }}',
                "country_id": country_id
            },
            success: function(response) {
                $('#countrycode').text(response);
            }
        });
    }
    
    $("#teenager_set_profile_form").submit(function() {
            $("#saveProfile").toggleClass('sending').blur();
            var form = $("#teenager_set_profile_form");
            form.validate();
            if (form.valid()) {
                return true;
                setTimeout(function () {
                    $("#saveProfile").removeClass('sending').blur();
                }, 2500);
            } else {
                $("#saveProfile").removeClass('sending').blur();
            }
    });

    function getHelpText(helpSlug)
    {
        var CSRF_TOKEN = "{{ csrf_token() }}";
        $.ajax({
            type: 'POST',
            url: "{{url('teenager/get-help-text')}}",
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN
            },
            data: {'helpSlug':helpSlug},
            success: function(response) {
                $("."+helpSlug).html(response);                
            }
        });
    }
    
    $(document).on('click', '.editInfo', function() {
        if ($("#t_about_info").is(':visible')) {
            $("#t_about_info").hide(500);
            $("#display-about-info").show(500);
        } else {
            $("#display-about-info").hide();
            $("#t_about_info").show(500);
        }
        if ($("#t_about_info").is(':visible')) {
            $('.editInfo-outer').toggleClass('hide');
        } else {
            $('.editInfo-outer').addClass('hide');
        }
    });
</script>
@stop