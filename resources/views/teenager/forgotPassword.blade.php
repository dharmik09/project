@extends('layouts.home-master')

@push('script-header')
    <title>{{ trans('labels.appname') }} : Forgot Password</title>
@endpush

@section('content')
<div class="col-xs-12">
    @if ($message = Session::get('error'))
    <div class="row">
        <div class="col-md-12">
            <div class="box-body">
                <div class="alert alert-error alert-dismissable">
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
<div class="centerlize">
    <div class="container">
        <div class="clearfix col-md-offset-2 col-sm-offset-1 col-md-8 col-sm-10 detail_container">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="form-sec">
                    <form id="forgot_password" method="POST" action="{{ url('/teenager/forgot-password-OTP') }}" autocomplete="off">
                        <h1><span class="title_border">Forgot Password</span></h1>
                        <p class="header_text">Not a problem! just type your email and we will send OTP(one time password) to reset password</p>
                        {{ csrf_field() }}
                        <div class="form-group">
                            <input type="text" class="form-control {eitherEmailPhone:true}" id="email" maxlength="100" name="email" placeholder="Your Email" value="" autocomplete="off" tabindex="1" value="{{old('email')}}}">
                        </div>
                        <button type="submit" id="loginSubmit" value="Reset Password" class="btn btn-default" title="Reset Password" tabindex="2">Reset Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@stop

@section('script')
<script src="{{ asset('js/jquery.validate.min.js') }}"></script>
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
</script>
@stop