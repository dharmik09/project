@extends('layouts.common-master')

@push('script-header')
    <title>{{ trans('labels.appname') }} : Forgot Password</title>
@endpush

@section('content')

<div class="centerlize">
    <div class="container">
        <div class="clearfix col-md-offset-2 col-sm-offset-1 col-md-8 col-sm-10 detail_container">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <form class="registration_form" method="POST" id="forgot-password-OTP" action="{{url('school/forgot-password-OTP-verify')}}">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="userid" value="{{$response['data']['userid']}}" />
                    <h1><span class="title_border">New Password</span></h1>
                    <p class="header_text">OTP(One Time Password) is sent successfully to your email.</p>
                    <div class="col-md-offset-1 col-sm-offset-1 col-md-10 col-sm-10">
                        <div class="clearfix">
                            <div class="col-md-12 col-sm-12 col-xs-12 input_icon email">
                                <input type="password" name="OTP" class="cst_input_primary" placeholder="Enter OTP">
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
</script>

@stop
