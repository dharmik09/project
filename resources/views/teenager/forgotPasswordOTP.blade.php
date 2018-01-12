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
            <p>OTP(One Time Password) is sent successfully to your email. </p>
            <span class="icon" ><i class="icon-hand" data-aos="fade-down"><!-- --></i></span>
            <div class="form-sec">
                <form class="registration_form" method="POST" id="forgot-password-OTP" action="{{url('teenager/forgot-password-OTP-verify')}}">
                    {{ csrf_field() }}
                    <input type="hidden" name="userid" value="{{$response['data']['userid']}}" />
                    <input type="hidden" name="u_token" value="{{$response['data']['u_token']}}" />
                    <div class="form-group">
                        <input type="password" name="OTP" class="form-control" placeholder="Enter OTP" tabindex="1">
                        <a id="resend_otp" name="resend_otp" href="javascript:void(0)" onClick="resendOTP()" class="back_me left10">Resend OTP?</a>
                        <span id="resetMSG"></span>
                    </div>
                    <button type="submit" id="resetPasswordSubmit" class="btn btn-default primary_btn" value="Reset My Password" tabindex="2">Reset My Password</button>
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
        var forgotPasswordOTP = {
            OTP: {
                required: true,
                number:true
            }
        };
        $("#forgot-password-OTP").validate({
            rules: forgotPasswordOTP,
            messages: {
                OTP: {required: '<?php echo trans('Please Enter OTP') ?>'
                }
            }
        });
    });

    function resendOTP() {
        $("#resetMSG").html('');
        $("#resend_otp").toggleClass('sending-otp');
        $("#resend_otp").text("Sending....");
        $.ajax({
            url: "{{ url('/teenager/resend-OTP') }}",
            type: 'POST',
            data: {
                "_token": '{{ csrf_token() }}',
                "email" : "{{$response['data']['email'] or ''}}"
            },
            success: function(response) {
                $("#resend_otp").removeClass('sending-otp');
                $("#resend_otp").text("Resend OTP?");
                $("#resetMSG").html(response);
                setTimeout(function(){$('#resetMSG').html(' ');},5000);
            },
            error: function(response) {
                $("#resend_otp").removeClass('sending-otp');
                $("#resend_otp").text("Resend OTP?");
                $("#resetMSG").html(response);
                setTimeout(function(){$('#resetMSG').html(' ');},5000);
            },
        });
    }

    AOS.init({
        duration: 1200,
    });

    $("#forgot-password-OTP").submit(function() {
        $("#resetPasswordSubmit").toggleClass('sending').blur();
        var form = $("#forgot-password-OTP");
        form.validate();
        if (form.valid()) {
            return true;
        }
        setTimeout(function () {
            $("#resetPasswordSubmit").removeClass('sending').blur();
        }, 2500);
        return true;
    });
</script>

@stop