@extends('layouts.home-master')

@push('script-header')
    <title>{{ trans('labels.appname') }} : Parent Login</title>
@endpush

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
<!-- <div class="centerlize">
    <div class="container">
        <div class="clearfix col-md-offset-2 col-sm-offset-1 col-md-8 col-sm-10 detail_container">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <form id="login_form" role="form" method="POST" class="login_form" action="{{ url('/parent/login-check') }}" autocomplete="on" autosuggesion="off">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="user_type" id="user_type" value="{{ ($type == 'Parent')?1:2 }}">
                    <h1><span class="title_border">{{$type}} Login <i class="fa fa-play" aria-hidden="true" style="cursor: pointer;"></i></span></h1>
                    <div class="col-md-offset-1 col-sm-offset-1 col-md-10 col-sm-10">
                        <?php
                            if(old('email')){
                                $email = old('email');
                            }else{
                                $email = '';
                            }
                        ?>
                        <div class="clearfix">
                            <div class="col-md-12 col-sm-12 col-xs-12 input_icon email">
                                <input type="text" class="cst_input_primary" id="email" maxlength="50" minlength="5" name="email" placeholder="{{trans('labels.emaillbl')}}" value="{{$email}}" autocomplete="off">
                            </div>
                        </div>
                        <div class="clearfix">
                            <div class="col-md-12 col-sm-12 col-xs-12 input_icon password">
                                <input type="password" class="cst_input_primary" id="password" maxlength="20" minlength="6" name="password" placeholder="{{trans('labels.passwordlbl')}}" autocomplete="off">
                            </div>
                        </div>
                        <div class="clearfix">
                            <div class="col-md-12 col-sm-12 col-xs-12">
                                <a href="{{url('parent/forgot-password')}}" class="link">Forgot Password?</a>
                            </div>
                        </div>
                        <div class="button_container social_btn">
                            <div class="submit_register">
                                <input type="submit" id="loginSubmit" value="{{trans('labels.login')}}" class="btn primary_btn">
                                <span class="or">OR</span>
                                <?php 
                                    if($type == 'Parent')
                                    {
                                        $signUpRoute = url('parent/signup');
                                    }else{
                                        $signUpRoute = url('counselor/signup');
                                    }
                                ?>
                                <a href="{{ $signUpRoute }}" class="btn primary_btn"><em>Sign Up</em></a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div> -->
<!-- mid section starts-->
    <section class="sec-login">
        <div class="container-small">
            <div class="login-form">
                <h1>Parent login</h1>
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam a tincidunt justo, sit amet tincidunt tortor. </p>
                <span class="icon"><i class="icon-hand" data-aos="fade-down"><!-- --></i></span>

                <div class="form-sec">
                    <form>
                        <div class="form-group">
                            <input type="text" class="form-control" id="name" placeholder="username" tabindex="1">
                        </div>
                        <div class="form-group">
                            <input type="password" class="form-control pass-visi" id="password" placeholder="password" tabindex="2">
                            <span class="visibility-pwd"><img src="{{ Storage::url('img/view.png') }}" alt="view" class="view img">
                                <img src="{{ Storage::url('img/hide.png') }}" alt="view" class="img-hide hide img"></span>
                        </div>
                        <div class="checkbox">
                            <label><input type="checkbox"><span class="checker"></span> Remember me</label>
                        </div>
                        <button type="submit" class="btn btn-default" title="Login" tabindex="4">Login</button>
                    </form>
                    <p><a href="#" title="Forgot username/password?">Forgot username/password?</a></p>
                    <p>Not enrolled? <a href="#" title="Sign up now.">Sign up now.</a></p>
                </div>
            </div>
        </div>
    </section>
    <!-- mid section end-->
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
        
        $('.fa-play').click(function(event) {
            $('#login_info_popup').modal('show');
        });
        //$('#login_info_popup .modal-body .para_holder').mCustomScrollbar();
    });
    $("#loginSubmit").click(function() {
        var form = $("#login_form");
        form.validate();
        if (form.valid()) {
            form.submit();
            $('.ajax-loader').show();
            $("#loginSubmit").attr("disabled", 'disabled');
        }else{
            $('.ajax-loader').hide();
            $("#loginSubmit").removeAttr("disabled", 'disabled');
        }
        ;
    });
</script>

@stop