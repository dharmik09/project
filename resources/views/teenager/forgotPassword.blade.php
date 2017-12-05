@extends('layouts.home-master')

@push('script-header')
    <title>{{ trans('labels.appname') }} : Forgot Password</title>
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
            <h1>forgot password</h1>
            <p>Not a problem! just type your email and we will send OTP(one time password) to reset password. </p>
            <span class="icon" ><i class="icon-hand" data-aos="fade-down"><!-- --></i></span>

            <div class="form-sec">
                <form id="forgot_password" method="POST" action="{{ url('/teenager/forgot-password-OTP') }}" autocomplete="off">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <input type="text" class="form-control {eitherEmailPhone:true}" id="email" maxlength="100" name="email" placeholder="Your Email" value="" autocomplete="off" tabindex="1" value="{{old('email')}}}">
                    </div>
                    <button type="submit" id="loginSubmit" value="Reset Password" class="btn btn-default" title="Reset Password" tabindex="2">Reset Password</button>
                </form>
                <p>Not enrolled? <a href="{{ url('teenager/signup') }}" title="Sign up now.">Sign up now.</a></p>
            </div>
        </div>
    </div>
</section>

@stop

@section('script')
<script type="text/javascript">
    jQuery(document).ready(function() {
        var forgotPasswordRules = {
            email: {
                required: true,
                email: true
            }
        };
        $("#forgot_password").validate({
            rules: forgotPasswordRules,
            messages: {
                email: {required: '<?php echo trans('validation.emailrequired') ?>'
                }
            }
        });
    });
    AOS.init({
        duration: 1200,
    });
</script>
@stop