@extends('layouts.common-master')

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
                <form class="registration_form" method="POST" id="forgot-password" action="{{url('parent/forgot-password-OTP')}}">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <h1><span class="title_border">Forgot Password</span></h1>
                    <p class="header_text">Not a problem! Just enter your email and we will send you an OTP (One Time Password) to reset your password. </p>
                    <div class="col-md-offset-1 col-sm-offset-1 col-md-10 col-sm-10">
                        <div class="clearfix">
                            <div class="col-md-12 col-sm-12 col-xs-12 input_icon email">
                                <input type="text" name="email" class="cst_input_primary" placeholder="Email">
                            </div>
                        </div>
                        <div class="button_container">
                            <div class="submit_register"><input type="submit" class="btn primary_btn" value="Reset Password"></div>
                        </div>
                        <div class="text-center frgt-pwd-text">
                            <p>Not enrolled? <a href="{{url('parent/signup')}}" title="Sign up now.">Sign up now.</a></p>
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
        var forgotPasswordRules = {
            email: {
                required: true,
                email: true
            }
        };
        $("#forgot-password").validate({
            rules: forgotPasswordRules,
            messages: {
                email: {required: '<?php echo trans('validation.emailrequired') ?>'
                }
            }
        });
    });
</script>



@stop
