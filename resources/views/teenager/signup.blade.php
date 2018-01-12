@extends('layouts.home-master')

@push('script-header')
    <title>{{ trans('labels.appname') }} : Teenager Signup</title>
@endpush

@section('content')
    <div class="col-xs-12">
        @if ($message = Session::get('error'))
        <div class="row">
            <div class="col-md-8 col-md-offset-2 invalid_pass_error">
                <div class="box-body">
                    <div class="alert alert-error alert-dismissable danger">
                        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">X</button>
                        <h4><i class="icon fa fa-check"></i> {{trans('validation.errorlbl')}}</h4>
                        {{ $message }}
                    </div>
                    <div class="resend_verification">
                        <?php $id = Session::get('id'); ?>
                        @if(isset($id) && $id>0)
                            <div class="resend_verification">Didn't receive verification mail? Click to <a href="{{ url('/teenager/varify') }}/{{$id}}" class="rlink">Resend Verification</a></div>
                        @endif
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
    <section class="sec-register">
        <div class="container-small">
            <div class="form-register">
                <form id="teenager_registration_form" role="form" enctype="multipart/form-data" method="POST" action="{{ url('/teenager/do-signup') }}" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="social_provider" value="Normal">
                    <div class="row">
                        <div class="reg-heading clearfix">
                            <div class="col-sm-7 flex-tems pull-right">
                                <div class="full-width">
                                    <span class="icon" data-aos="fade-down"><i class="icon-hand"><!-- --></i></span>
                                    <h1>teen registration</h1>
                                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam a tincidunt justo, sit amet tincidunt tortor. </p>
                                </div>
                            </div>

                            <div class="col-sm-5 flex-items order-2 pull-left">
                                <div class="upload-img" id="img-preview">
                                    <span>photo upload</span>
                                    <input type="file" name="photo"  onchange="readURL(this);" accept=".png, .jpg, .jpeg, .bmp" style="cursor:pointer;">
                                </div>
                                <div class="photo-error-register"></div>
                            </div>
                        </div>
                    </div>
                    <div class="clearfix row flex-container">
                        <div class="col-sm-6 col-xs-12">
                            <div class="form-group">
                                <input type="text" class="form-control alphaonly" name="name" id="name" maxlength="100" placeholder="first name *" tabindex="1" value="{{old('name')}}" required />
                            </div>
                        </div>
                        <div class="col-sm-6 col-xs-12">
                            <div class="form-group">
                                <input type="text" class="form-control alphaonly" name="t_lastname" id="t_lastname" maxlength="100" placeholder="last name *" tabindex="1" value="{{old('t_lastname')}}" required />
                            </div>
                        </div>
                        <div class="col-sm-6 col-xs-12">
                            <div class="form-group">
                                <input type="email" class="form-control" name="email" id="email" autocomplete="off" maxlength="100" placeholder="email address *" tabindex="3" value="{{old('email')}}" required />
                            </div>
                        </div>
                        <div class="col-sm-6 col-xs-12">
                            <div class="form-group">
                                <input type="email" class="form-control" name="email_confirmation" id="email_confirmation" placeholder="confirm email address *" tabindex="4" maxlength="100" value="{{old('email_confirmation')}}" required />
                            </div>
                        </div>
                        <div class="col-sm-6 col-xs-12">
                            <div class="form-group custom-select">
                                <select tabindex="5" class="form-control" id="country" name="country" onchange="getPhoneCodeByCountry(this.value);" required>
                                    <option value="">country*</option>
                                    @forelse($countries as $val)
                                        <option value="{{$val->id}}" <?php echo (old('country') && old('country') == $val->id ) ? "selected='selected'" : ''; ?> > {{$val->c_name}} </option>
                                    @empty
                                    @endforelse
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6 col-xs-12">
                            <div class="form-group">
                                <input type="text" class="form-control" name="pincode" id="pincode" placeholder="zip code *" tabindex="6" required value="{{old('pincode')}}" maxlength="6" />
                            </div>
                        </div>
                        <div class="col-sm-6 col-xs-12">
                            <div class="form-group">
                                <input type="text" class="form-control onlyNumber" id="phone" name="phone" placeholder="phone number" minlength="7" maxlength="10" tabindex="7" value="{{old('phone')}}">
                            </div>
                        </div>
                        <div class="col-sm-6 col-xs-12">
                            <div class="form-group input-group">
                                <div class="clearfix">
                                    <span class="input-group-addon" id="country_phone_code">+91</span>
                                    <input type="text" name="mobile" class="form-control onlyNumber" minlength="10" maxlength="10" placeholder="mobile number" value="{{old('mobile')}}" tabindex="8" />
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-xs-12">
                            <div class="form-group custom-select">
                                <select tabindex="9" class="form-control" name="gender" required >
                                    <option value="">gender *</option>
                                    <option value="1" <?php echo (old('gender') && old('gender') == 1) ? "selected='selected'" : ''; ?> >Male</option>
                                    <option value="2" <?php echo (old('gender') && old('gender') == 2) ? "selected='selected'" : ''; ?> >Female</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6 col-xs-12">
                            <div class="form-group">
                                <input type="text" class="form-control nospace" name="nickname" id="nickname" placeholder="ProTeen code" tabindex="10" value="{{old('nickname')}}">
                            </div>
                        </div>
                        <div class="col-sm-6 col-xs-12">
                            <div class="form-group">
                                <input type="password" class="form-control pass-visi" name="password" id="password" placeholder="password *" tabindex="11" required />
                                <span class="visibility-pwd">
                                    <img src="{{ Storage::url('img/view.png') }}" alt="view" class="view img">
                                    <img src="{{ Storage::url('img/hide.png') }}" alt="view" class="img-hide hide img">
                                </span>
                                <em style="color:red" id="pass_validation">  </em>
                            </div>
                        </div>
                        <div class="col-sm-6 col-xs-12">
                            <div class="form-group">
                                <input type="password" class="form-control" name="password_confirmation" id="password_confirmation" placeholder="confirm password *" tabindex="12" required />
                                
                            </div>
                        </div>
                        <div class="col-sm-6 col-xs-12">
                            <div class="form-group date-sec">
                                <label>birth date *</label>
                                <div class="date-feild">
                                    <select name="month" class="form-control date-block" id="month" tabindex="13">
                                        <option value="">mm</option>
                                        @for($month = 01; $month <= 12; $month++)
                                            <option value="{{date('m', mktime(0,0,0, $month, 1, date('Y')))}}">{{ date('M', mktime(0,0,0,$month, 1, date('Y'))) }}</option>
                                        @endfor
                                    </select>
                                    <select name="day" class="form-control date-block" id="day" tabindex="14">
                                        <option value="">dd</option>
                                        @for($day = 1; $day <= 31; $day++)
                                            <option value="{{date('d', mktime(0,0,0,0, $day, date('Y')))}}">{{ date('d', mktime(0,0,0,0, $day, date('Y'))) }}</option>
                                        @endfor
                                    </select>
                                    <select name="year" class="form-control date-block" id="year" tabindex="15">
                                        <option value="">yyyy</option>
                                        @foreach(range(\Carbon\Carbon::now()->year, 1950) as $year)
                                            <option value="{{$year}}">{{$year}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="birth-error"></div>    
                            </div>
                        </div>
                    </div>
                    <p>Select at least one sponsor. *</p>
                    <p>Benefits include coupon code voucher and event participation.</p>
                    <div class="sponsor-error"></div>
                    <div class="sponsor-sec">
                        <div class="sponsor-content">
                            <div class="form-register sponsor-list owl-carousel">
                                @forelse($sponsorDetail as $key => $value)
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" name="selected_sponsor[]" id="sponsor_{{$key}}" value="{{$value->sponsor_id}}" <?php (old('selected_sponsor') && in_array($value->sponsor_id, old('selected_sponsor')) ) ? "checked" : ""; ?> />
                                            <span class="checker"></span>
                                            <span class="logo-icon">
                                                <?php
                                                    $sponsor_logo = ($value->sp_logo != "") ? Storage::url(Config::get('constant.SPONSOR_THUMB_IMAGE_UPLOAD_PATH').$value->sp_logo) : asset(Config::get('constant.SPONSOR_THUMB_IMAGE_UPLOAD_PATH') . 'proteen-logo.png');
                                                ?>
                                                <img src="{{ $sponsor_logo }}" alt="{{ $value->sp_company_name }}" style="height:74px; width:127px;">
                                            </span>
                                            <span class="sponsor-name">{{ str_limit($value->sp_company_name, $limit = 100, $end = '..') }}</span>
                                        </label>
                                    </div>
                                @empty

                                @endforelse
                            </div>
                        </div>
                    </div>
                    <div class="form-register">
                        <div class="terms-sec">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="terms_condition" id="terms_condition" tabindex="16"><span class="checker"></span> I agree to ProTeen's <a href="{{ url('terms-condition') }}" title="Terms and Conditions" target="_blank">Terms and Conditions</a> and <a href="{{ url('privacy-policy') }}" title="Privacy Policy" target="_blank">Privacy Policy</a>.
                                </label>
                            </div>
                            <div class="terms-error"></div>
                        </div>
                        <p class="text-center">
                            <button type="submit" id="form_submit" class="btn btn-default btn-submit" title="Submit" tabindex="17">Submit</button>
                        </p>
                        <ul class="btn-list login-source">
                            <li><a href="{{ url('teenager/facebook') }}" title="Facebook"><i class="icon-facebook"><!-- --></i>Facebook</a></li>
                            <li><a href="{{ url('teenager/google') }}" title="Google"><i class="icon-google"><!-- --></i>Google</a></li>
                        </ul>
                        <div class="frgtpwd-sec">
                            <p><a href="{{ url('teenager/forgot-password') }}" title="Forgot password?">Forgot password?</a> Already enrolled? <a href="{{ url('teenager/login') }}" title="Sign in">Sign in now</a>.</p>
                            <p>* indicates a mandatory field</p>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
@stop

@section('script')
    <script type="text/javascript">
        
        jQuery.validator.addMethod("mobilelength", function(value, element) {
            return this.optional(element) || /^\d{10}$/i.test(value);
        }, "Please enter valid mobile number");
        var signupRules = {
            name: {
                required: true,
                minlength: 3,
                maxlength: 100
            },
            t_lastname: {
                required: true,
                minlength: 3,
                maxlength: 100
            },
            gender: {
                required: true
            },
            email: {
                required: true,
                email: true,
                maxlength : 100
            },
            email_confirmation: {
                equalTo: "#email"
            },
            password: {
                required: function(el) {
                  checkPassword()
              },
            },
            password_confirmation:{
                equalTo: "#password"
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
            day: {
                required: true,
            },
            year: {
                required: true,
            },
            month: {
                required: true,
            },
            'selected_sponsor[]': {
                required: true
            },
            mobile: {
                required: true,
                mobilelength: true
            },
            terms_condition: {
                required: true
            },
            phone: {
                minlength: 7,
                maxlength: 10
            }
        };
        $("#teenager_registration_form").validate({
            rules: signupRules,
            messages: {
                name: {
                    required: "First name is required"
                },
                t_lastname: {
                    required: "Last name is required"
                },
                gender: {
                    required: "Gender is required"
                },
                email: {
                    required: "Email is required"
                },
                email_confirmation: {
                    required: "Confirm email is required",
                    equalTo: "Confirm email is not matched"
                },
                password: {
                    required: "Password is required"
                },
                password_confirmation: {
                    required: "Confirm password is required",
                    equalTo: "Confirm password is not matched"
                },
                country: {
                    required: "Country is required"
                },
                pincode: {
                    required: "Zipcode is required"
                },
                'selected_sponsor[]': {
                    required: "Please select atleast one sponsor"
                },
                mobile: {
                    required: 'Mobile number is required'
                },                                                   
                terms_condition: { required: 'Please select terms and conditions'
                },
                day: { required: 'Date is required',
                },
                year: { required: 'Year is required',
                },
                month: { required: 'Month is required',
                }
            },
            errorPlacement: function(error, element) {
                if (element.attr("name") == "terms_condition") {
                    error.appendTo(".terms-error");
                } else if(element.attr("name") == "selected_sponsor[]") {
                    error.appendTo(".sponsor-error");
                }else if(element.attr("name") == "day" || element.attr("name") == "year" || element.attr("name") == "month"){
                    error.appendTo(".birth-error");
                } else {
                    error.insertAfter(element)
                }
            },
        });
        AOS.init({
            duration: 1200,
        });

        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    var a = document.querySelector("#img-preview");
                    if (input.files[0].type == 'image/jpeg' || input.files[0].type == 'image/jpg' || input.files[0].type == 'image/png' || input.files[0].type == 'image/bmp') {
                        if (input.files[0].size > 3000000) {
                            $(".photo-error-register").text("File size is too large. Maximum 3MB allowed");
                            $(this).val('');
                        } else {
                            a.style.backgroundImage =  "url('"+ e.target.result +"')";
                            a.className = "upload-img activated";
                        }
                    } else {
                        $(".photo-error-register").text("File type not allowed");
                        $(this).val('');
                    }
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
        function getPhoneCodeByCountry(country_id)
        {
            $.ajax({
                url: "{{ url('teenager/get-phone-code-by-country') }}",
                type: 'post',
                data: {
                    "_token": '{{ csrf_token() }}',
                    "country_id": country_id
                },
                success: function(response) {
                    $('#country_phone_code').text(response);
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
        $('.onlyNumber').on('keyup', function() {
            this.value = this.value.replace(/[^0-9]/gi, '');
        });
        $('.digitalphaonly').bind('keyup blur', function() {
            var node = $(this);
            node.val(node.val().replace(/[^a-zA-Z0-9 ]/g, ''));
        });
        $('.alphaonly').bind('keyup blur', function() {
            var node = $(this);
            node.val(node.val().replace(/[^a-zA-Z_' ]/g, ''));
        });
        $('.nospace').bind('keyup blur', function() {
            var node = $(this);
            node.val(node.val().replace(/\s/g, ''));
        });
        
        $(document).ready(function() {
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

        });
        $("#teenager_registration_form").submit(function() {
            $("#form_submit").toggleClass('sending').blur();
            var form = $("#teenager_registration_form");
            form.validate();
            if (form.valid()) {
                return true;
                setTimeout(function () {
                    $("#form_submit").removeClass('sending').blur();
                }, 2500);
            } else {
                $("#form_submit").removeClass('sending').blur();
            }
        });
    </script>
@stop