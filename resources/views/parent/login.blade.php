@extends('layouts.common-master')

@push('script-header')
    <title>{{ trans('labels.appname') }} : Parent Login</title>
@endpush

@section('content')
<div class="col-xs-12">
    @if ($message = Session::get('error'))
    <div class="row">
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
@include('flash::message')
<section class="sec-login">
    <div class="container-small">
        <div class="login-form">
            <h1>{{$type}} login</h1>
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam a tincidunt justo, sit amet tincidunt tortor. </p>
            <span class="icon"><img src="../img/hand-icon.png" alt="hand icon"></span>
            <div class="form-sec">
                <form id="login_form" role="form" method="POST" class="login_form" action="{{ url('/parent/login-check') }}" autocomplete="on" autosuggesion="off">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="user_type" id="user_type" value="{{ ($type == 'Parent')?1:2 }}">
                    <div class="form-group">
                        <?php
                            if (old('email')) {
                                $email = old('email');
                            } else {
                                $email = '';
                            }
                        ?>
                        <input type="text" class="form-control" id="email" maxlength="50" minlength="5" name="email" placeholder="email" value="{{$email}}" autocomplete="off">
                        <span class="invalid" id="email_mobile_invalid" style="display: none;">Valid email required</span>
                    </div>
                    <div class="form-group">
                        <input type="password" class="form-control pass-visi" id="password" maxlength="20" minlength="6" name="password" placeholder="password" autocomplete="off" tabindex="2">
                        <span class="visibility-pwd">
                            <img src="{{ Storage::url('img/view.png') }}" alt="view" class="view img">
                            <img src="{{ Storage::url('img/hide.png') }}" alt="view" class="img-hide hide img">
                        </span>
                    </div>
                    <button id="loginSubmit" type="submit" class="btn btn-default" title="Login" tabindex="4">Login</button>
                </form>
                <p><a href="{{url('parent/forgot-password')}}" title="Forgot password?">Forgot password?</a></p>
                <?php 
                    if($type == 'Parent')
                    {
                        $signUpRoute = url('parent/signup');
                    }else{
                        $signUpRoute = url('counselor/signup');
                    }
                ?>
                <p>Not enrolled? <a href="{{ $signUpRoute }}" title="Sign up now.">Sign up now.</a></p>
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
            }
        };
        $("#login_form").validate({
            rules: loginRules,
            messages: {
                password: {required: '<?php echo trans('validation.passwordrequired') ?>',
                    maxlength: 'Password maximum range is 20',
                    minlength: 'Password length is minimum 6'
                }
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
    });

    function validateEmail(email) {
        var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return re.test(email);
    }
</script>

@stop