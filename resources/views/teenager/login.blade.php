@extends('layouts.home-master')

@push('script-header')
    <title>{{ trans('labels.appname') }} : Student Login</title>
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
                        <?php $t_uniqueid = Session::get('t_uniqueid'); ?>
                        @if($t_uniqueid != "")
                            <div class="resend_verification">Didn't receive verification mail? Click to <a href="{{ url('/teenager/resend-verification') }}/{{$t_uniqueid}}" class="rlink">Resend Verification</a></div>
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
    <section class="sec-login">
        <div class="container-small">
            <div class="login-form">
                <h1>Student login</h1>
                <p>Please enter your registered eMail ID or mobile phone number to login. You may also login using your Facebook or GooglePlus credentials.</p>
                <span class="icon" ><i class="icon-hand" data-aos="fade-down"><!-- --></i></span>

                <div class="form-sec">
                    <form id="login_form" method="POST" action="{{ url('/teenager/login-check') }}" autocomplete="off">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <div class="form-group">
                            <input type="text" class="form-control {eitherEmailPhone:true}" id="email" maxlength="50" name="email" placeholder="Email or Mobile" value="" autocomplete="off" tabindex="1">
                            <span class="invalid" id="email_mobile_invalid" style="display: none;">Valid email or mobile required</span>
                        </div>
                        <div class="form-group">
                            <input type="password" class="form-control pass-visi" id="password" maxlength="20" minlength="6" name="password" placeholder="password" tabindex="2">
                            <span class="visibility-pwd">
                                <img src="{{ Storage::url('img/view.png') }}" alt="view" class="view img">
                                <img src="{{ Storage::url('img/hide.png') }}" alt="view" class="img-hide hide img">
                            </span>
                        </div>
                        <div class="checkbox">
                            <label><input type="checkbox" name="remember_me" value="1" tabindex="3"><span class="checker"></span> Remember me</label>
                        </div>
                        <button type="submit" id="loginSubmit" value="Login" class="btn btn-default" title="Login" tabindex="4">Login</button>
                        <p class="text-center">or</p>
                        <ul class="btn-list clearfix">
                            <li><a href="{{ url('teenager/facebook') }}" title="Facebook" ><i class="icon-facebook"><!-- --></i>Facebook</a></li>
                            <li><a href="{{ url('teenager/google') }}" title="Google" ><i class="icon-google"><!-- --></i>Google</a></li>
                        </ul>
                    </form>
                    <p><a href="{{ url('teenager/forgot-password') }}" title="Forgot password?">Forgot password?</a></p>
                    <p>Not enrolled? <a href="{{ url('teenager/signup') }}" title="Sign up now.">Sign up now.</a></p>
                </div>
            </div>
        </div>
    </section>
@stop

@section('script')
    <script type="text/javascript">
        jQuery(document).ready(function() {
            var loginRules = {
                password: {
                    required: true,
                    minlength: 6,
                    maxlength: 20
                },
            };
            $("#login_form").validate({
                rules: loginRules,
                messages: {
                    password: {required: '{{trans("validation.passwordrequired")}}',
                        maxlength: 'Password maximum range is 20',
                        minlength: 'Password minimum length is 6'
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
        });
        //masonary
        $('.masonary-grid').masonry({
            // options
            itemSelector: '.item',
            columnWidth: 1
        });
        //video popup
        $('.play-video').magnificPopup({
            disableOn: 0,
            type: 'iframe',
            mainClass: 'mfp-fade',
            removalDelay: 160,
            preloader: false,
            overflowY: 'auto',
            removalDelay: 300,
            midClick: true,
            fixedBgPos: true,
            fixedContentPos: true
        });
        //testimonial slider
        $('.testimonial-slider').owlCarousel({
            loop: true,
            margin: 10,
            items: 1,
            nav: true,
            dots: false,
        });
        $('.play-icon').click(function () {
            $(this).hide();
            $('iframe').show();
        })
        $("#login_form").submit(function() {
            $("#loginSubmit").toggleClass('sending').blur();
            var form = $("#login_form");
            form.validate();
            var validEmailOrMobile = false;
            $('#email_mobile_invalid').show();
            var emailOrMobile = $.trim($("#email").val());
            if (emailOrMobile.length > 0 && emailOrMobile.match(/[a-zA-Z]/i)) {
                if (validateEmail(emailOrMobile)) {
                    var validEmailOrMobile = true;
                }
            }
            if ($.isNumeric(emailOrMobile) && emailOrMobile.length > 9) {
                var validEmailOrMobile = true;
            }
            if (validEmailOrMobile) {
                $('#email_mobile_invalid').hide();
                if (form.valid()) {
                    return true;
                }
                setTimeout(function () {
                    $("#loginSubmit").removeClass('sending').blur();
                }, 2500);
                return true;
            } else {
                $('#email_mobile_invalid').show();
                $("#loginSubmit").removeClass('sending').blur();
                return false;
            }
        });

        function validateEmail(email) {
            var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
            return re.test(email);
        }
        AOS.init({
            duration: 1200,
        });
    </script>
@stop