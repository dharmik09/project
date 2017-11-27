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
                <form class="registration_form" method="POST" id="forgot-password-OTP" action="{{url('teenager/forgot-password-OTP-verify')}}">
                    {{ csrf_field() }}
                    <input type="hidden" name="userid" value="{{$response['data']['userid']}}" />
                    <input type="hidden" name="u_token" value="{{$response['data']['u_token']}}" />
                    <h1><span class="title_border">New Password</span></h1>
                    <p class="header_text">OTP(One Time Password) is sent successfully to your email.</p>
                    <div class="col-md-offset-1 col-sm-offset-1 col-md-10 col-sm-10">
                        <div class="clearfix">
                            <div class="col-md-12 col-sm-12 col-xs-12 input_icon security_pin">
                                <input type="password" name="OTP" class="cst_input_primary" placeholder="Enter OTP">
                            </div>
                            <div class="col-md-12 col-sm-12 col-xs-12">
                                <a href="javascript:void(0)" onClick="resendOTP()" class="back_me left10">Resend OTP?</a>
                                <span id="resetMSG"></span>
                            </div>
                        </div>
                        
                        <div class="button_container">
                            <div class="submit_register"><input type="submit" class="btn primary_btn" value="Reset My Password"></div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@stop

@section('script')

<script src="{{ asset('js/jquery.validate.min.js') }}"></script>
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
        $.ajax({
            url: "{{ url('/teenager/resend-OTP') }}",
            type: 'POST',
            data: {
                "_token": '{{ csrf_token() }}',
                "email" : "{{$response['data']['email'] or ''}}"
            },
            success: function(response) {
                $("#resetMSG").html(response);
                setTimeout(function(){$('#resetMSG').html(' ');},5000);
            }
        });
    }
</script>

@stop