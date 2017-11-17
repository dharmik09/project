@extends('layouts.sponsor-master')

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
    @if($message = Session::get('success'))
    <div class="clearfix">
        <div class="col-md-12">
            <div class="box-body">
                <div class="alert alert-error alert-succ-msg alert-dismissable success">
                    <button aria-hidden="true" data-dismiss="success" class="close" type="button">X</button>
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
<div class="centerlize">
    <div class="container">
        <div class="clearfix col-md-offset-2 col-sm-offset-1 col-md-8 col-sm-10 detail_container">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <form id="login_form" role="form" method="POST" class="login_form" action="{{ url('/sponsor/login-check') }}">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <h1><span class="title_border">Enterprise Login <i class="fa fa-play" aria-hidden="true" style="cursor: pointer;"></i></span></h1>
                    <div class="col-md-offset-1 col-sm-offset-1 col-md-10 col-sm-10">
                        <div class="clearfix">
                            <div class="col-md-12 col-sm-12 col-xs-12 input_icon email">
                                <input type="text" class="cst_input_primary" id="email" maxlength="50" minlength="5" name="email" placeholder="{{trans('labels.emaillbl')}}" value="">
                            </div>
                        </div>
                        <div class="clearfix">
                            <div class="col-md-12 col-sm-12 col-xs-12 input_icon password">
                                <input type="password" class="cst_input_primary" id="password" maxlength="20" minlength="6" name="password" placeholder="{{trans('labels.passwordlbl')}}">
                            </div>
                        </div>
                        <div class="clearfix">
                            <div class="col-md-12 col-sm-12 col-xs-12">
                                <a href="{{url('sponsor/forgotPassword')}}" class="link">Forgot Password?</a>
                            </div>
                        </div>
                        <div class="button_container social_btn">
                            <div class="submit_register">
                                <input type="submit" value="{{trans('labels.login')}}" id="loginSubmit" class="btn primary_btn">
                                <span class="or">OR</span>
                                <a href="{{ url('sponsor/signup') }}" class="btn primary_btn"><em>Sign Up</em></a>
                            </div>

                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
<div class="loader ajax-loader" style="display:none;">
    <div class="cont_loader">
        <div class="img1"></div>
        <div class="img2"></div>
    </div>
</div>
<div id="login_info_popup" class="modal fade login_info_popup_cst" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i></button>
                <h4 class="modal-title">Enterprise</h4>
            </div>
            <div class="modal-body">
                <div class="para_holder">
                    {!!$text!!}                                   
                </div>
            </div>
        </div>
    </div>
</div>

@stop
@section('script')
<script type="text/javascript">
    jQuery(document).ready(function() {
        var loginRules = {
            email: {
                required: true,
                email: true
            },
            password: {
                required: true,
                minlength: 6,
                maxlength: 20
            }
        };
        $("#login_form").validate({
            rules: loginRules,
            messages: {
                email: {required: '<?php echo trans('validation.emailrequired') ?>'
                },
                password: {required: '<?php echo trans('validation.passwordrequired') ?>',
                    maxlength: 'Password maximum range is 20',
                    minlength: 'Password length is minimum 6'
                }
            }
        });
        $("#loginSubmit").click(function() {
            var form = $("#login_form");
            form.validate();
            if (form.valid()) {
                form.submit();
                $('.ajax-loader').show();
                $("#loginSubmit").attr("disabled", 'disabled');
            } else {
                $('.ajax-loader').hide();
                $("#loginSubmit").removeAttr("disabled", 'disabled');
            }
        });
        $('.fa-play').click(function(event) {
            $('#login_info_popup').modal('show');
        });
        $('#login_info_popup .modal-body .para_holder').mCustomScrollbar();
    });
</script>

@stop