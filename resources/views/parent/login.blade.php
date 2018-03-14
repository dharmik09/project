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
<section class="sec-login">
    <div class="container-small">
        <div class="login-form">
            <h1>{{$type}} login</h1>
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam a tincidunt justo, sit amet tincidunt tortor. </p>
            <span class="icon"><i class="icon-hand" data-aos="fade-down"><!-- --></i></span>
            <div class="form-sec">
                <form id="login_form" role="form" method="POST" class="login_form" action="{{ url('/parent/login-check') }}" autocomplete="on" autosuggesion="off">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="user_type" id="user_type" value="{{ ($type == 'Parent')?1:2 }}">
                    <div class="form-group">
                        <?php
                            if (old('email')) {
                                $email = old('email');
                            } else {
                                $email = '';
                            }
                        ?>
                        <input type="text" class="form-control" id="email" maxlength="50" minlength="5" name="email" placeholder="{{trans('labels.emaillbl')}}" value="{{$email}}" autocomplete="off">
                    </div>
                    <div class="form-group">
                        <input type="password" class="form-control pass-visi" id="password" maxlength="20" minlength="6" name="password" placeholder="{{trans('labels.passwordlbl')}}" autocomplete="off" tabindex="2">
                        <span class="visibility-pwd">
                            <img src="{{ Storage::url('img/view.png') }}" alt="view" class="view img">
                            <img src="{{ Storage::url('img/hide.png') }}" alt="view" class="img-hide hide img">
                        </span>
                    </div>
                    <button id="loginSubmit" type="submit" class="btn btn-default" title="Login" tabindex="4">Login</button>
                </form>
                <p><a href="{{url('parent/forgot-password')}}" title="Forgot password?">Forgot password?</a></p>
                <?php 
                    if($type == 'Parent')
                    {
                        $signUpRoute = url('parent/signup');
                    }else{
                        $signUpRoute = url('counselor/signup');
                    }
                ?>
                <p>Not enrolled? <a href="{{ $signUpRoute }}" title="Sign up now.">Sign up now.</a></p>
            </div>
        </div>
    </div>
</section>
                        
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
        
    });
</script>

@stop