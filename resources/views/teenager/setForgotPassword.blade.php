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

<section class="sec-login">
    <div class="container-small">
        <div class="login-form">
            <h1>new password</h1>
            <p>Please enter a new password. </p>
            <span class="icon" ><i class="icon-hand" data-aos="fade-down"><!-- --></i></span>

            <div class="form-sec">
                <form class="registration_form" method="POST" id="forgot-password-set-new" action="{{url('teenager/save-forgot-password')}}">
                    {{ csrf_field() }}
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="userid" value="{{$response['data']['userid']}}" />
                    <input type="hidden" name="OTP" value="{{$response['data']['otp']}}" />
                    <div class="form-group">
                        <input type="password" name="newPassword" class="form-control pass_visi" placeholder="Enter new password" id="password" tabindex="1">
                        <span class="visibility_password"><i class="fa fa-eye" aria-hidden="true"></i></span>
                        <em style="color:red" id="pass_validation">  </em>
                    </div>
                    <input type="submit" class="btn primary_btn" value="Save" tabindex="2">
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
        var setNewPassword = {
            newPassword: {
                required: function(el) {
                    checkPassword()
                },
            }
        };
        $("#forgot-password-set-new").validate({
            rules: setNewPassword,
            messages: {
                newPassword: {required: '<?php echo trans('Please Enter New Password') ?>'
                }
            }
        });
    });
    
    AOS.init({
        duration: 1200,
    });
    
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