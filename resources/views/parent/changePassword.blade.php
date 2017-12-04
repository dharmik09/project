@extends('layouts.parent-master')

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
                <form id="change_password_form" role="form" method="POST" class="login_form" action="{{ url('/parent/update-password') }}" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <h1><span class="title_border">Change Password</span></h1>
                    <div class="col-md-offset-1 col-sm-offset-1 col-md-10 col-sm-10">
                        <div class="clearfix">
                            <div class="col-md-12 col-sm-12 col-xs-12 input_icon password">
                                <input type="password" class="cst_input_primary" id="old_password" maxlength="20" minlength="6" name="old_password" placeholder="{{trans('labels.oldpassword')}}">
                            </div>
                        </div>
                        <div class="clearfix">
                            <div class="col-md-12 col-sm-12 col-xs-12 input_icon password">
                                <input type="password" class="cst_input_primary" id="new_password" maxlength="20" minlength="6" name="new_password" placeholder="{{trans('labels.newpassword')}}">
                            </div>
                        </div>
                        <div class="clearfix">
                            <div class="col-md-12 col-sm-12 col-xs-12 input_icon password">
                                <input type="password" class="cst_input_primary" id="confirm_password" maxlength="20" minlength="6" name="confirm_password" placeholder="{{trans('labels.confirmpassword')}}">
                            </div>
                        </div>


                        <div class="button_container social_btn">
                            <div class="submit_register">
                                <input type="submit" value="Save" class="btn primary_btn">
                                <a href="{{ url('parent/home') }}" class="btn primary_btn"><em>Cancel</em><span></span></a>
                            </div>

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
        var loginRules = {
            old_password: {
                required: true
            },
            new_password: {
                required: true,
                minlength: 6,
                maxlength: 20
            },
            confirm_password: {
                required: true,
                minlength: 6,
                maxlength: 20,
                equalTo : '#new_password'
            }
        };
        $("#change_password_form").validate({
            rules: loginRules,
            messages: {
                old_password: {required: '<?php echo trans('validation.passwordrequired') ?>'
                },
                new_password: {required: '<?php echo trans('validation.newpasswordrequired') ?>',
                    maxlength: 'Password maximum range is 20',
                    minlength: 'Password length is minimum 6'
                },
                confirm_password: {required: '<?php echo trans('validation.confirmpasswordrequired') ?>',
                    maxlength: 'Password maximum range is 20',
                    minlength: 'Password length is minimum 6',
                    equalTo : 'Confirm Password and New password must be same'
                }
            }
        });
    });
</script>

@stop