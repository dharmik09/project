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
                <form class="registration_form" method="POST" id="forgot-password-set-new" action="{{url('teenager/save-forgot-password')}}">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="userid" value="{{$response['data']['userid']}}" />
                    <input type="hidden" name="OTP" value="{{$response['data']['otp']}}" />
                    <h1><span class="title_border">New Password</span></h1>
                    <p class="header_text">Please enter a new password</p>
                    <div class="col-md-offset-1 col-sm-offset-1 col-md-10 col-sm-10">
                        <div class="clearfix">
                            <div class="col-md-12 col-sm-12 col-xs-12 input_icon password">
                                <div class="pass_cont">
                                    <input type="password" name="newPassword" class="cst_input_primary pass_visi" placeholder="Enter new password" id="password">
                                    <span class="visibility_password"><i class="fa fa-eye" aria-hidden="true"></i></span>
                                    <em style="color:red" id="pass_validation">  </em>
                                </div>
                            </div>
                        </div>
                        <div class="button_container">
                            <div class="submit_register"><input type="submit" class="btn primary_btn" value="Save"></div>
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