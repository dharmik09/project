@extends('layouts.common-master')

@push('script-header')
    <title>{{ trans('labels.appname') }} : Sponsor Login</title>
@endpush

@section('content')


<div class="col-xs-12">
    @if ($message = Session::get('success'))
    <div class="row">
        <div class="col-md-12">
            <div class="box-body">
                <div class="alert alert-success alert-dismissable">
                    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">X</button>
                    <h4><i class="icon fa fa-check"></i> {{trans('validation.successlbl')}}</h4>
                    {{ $message }}
                </div>
            </div>
        </div>
    </div>
    @endif
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
<section class="sec-login">
    <div class="container-small">
        <div class="login-form">
            <h1>Sponsor login</h1>
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam a tincidunt justo, sit amet tincidunt tortor. </p>
            <span class="icon"><img src="{{ Storage::url('img/hand-icon.png') }}" alt="hand icon"></span>
            <div class="form-sec">
                <form id="login_form" role="form" method="POST" action="{{ url('/sponsor/login-check') }}">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <div class="form-group">
                        <input type="text" class="form-control" id="email" name="email" maxlength="50" minlength="5" placeholder="{{trans('labels.emaillbl')}}" tabindex="1">
                    </div>
                    <div class="form-group">
                        <input type="password" class="form-control pass-visi" id="password" maxlength="20" minlength="6" name="password" placeholder="{{trans('labels.passwordlbl')}}" tabindex="2">
                        <span class="visibility-pwd"><img src="{{ Storage::url('img/view.png') }}" alt="view" class="view img">
                            <img src="{{ Storage::url('img/hide.png') }}" alt="view" class="img-hide hide img"></span>
                    </div>
                    <button id="loginSubmit" type="submit" class="btn btn-default" title="Login" tabindex="4">Login</button>
                </form>
                <p><a href="{{url('sponsor/forgot-password')}}" title="Forgot username/password?">Forgot password?</a></p>
                <p>Not enrolled? <a href="{{ url('sponsor/signup') }}" title="Sign up now.">Sign up now.</a></p>
            </div>
        </div>
    </div>
</section>

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
                <h4 class="modal-title">Sponsor</h4>
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
        
        $('.fa-play').click(function(event) {
            $('#login_info_popup').modal('show');
        });
        // Cache the toggle button
        var $toggle = $(".visibility-pwd");
        var $field = $(".pass-visi");
         var i = $(this).find('.img');
        // Toggle the field type
        $toggle.on("click", function(e) {
            e && e.preventDefault();
            if ($field.attr("type") == "password") {
                $field.attr("type", "text");
                i.toggleClass("hide");
            } else {
               i.toggleClass("hide");
                $field.attr("type", "password");
            }

        });
        //$('#login_info_popup .modal-body .para_holder').mCustomScrollbar();
        $("#login_form").submit(function() {
            $("#loginSubmit").addClass('sending').blur();
            var form = $("#login_form");
            form.validate();
            var validEmailOrMobile = false;
            $('#email_mobile_invalid').show();
            var emailOrMobile = $.trim($("#email").val());
            if (emailOrMobile.length > 0 && emailOrMobile.match(/[a-zA-Z]/i)) {
                if (validateEmail(emailOrMobile)) {
                    var validEmailOrMobile = true;
                }
            }
            if ($.isNumeric(emailOrMobile) && emailOrMobile.length > 9) {
                var validEmailOrMobile = true;
            }
            if (validEmailOrMobile) {
                $('#email_mobile_invalid').hide();
                if (form.valid()) {
                    return true;
                }
                setTimeout(function () {
                    $("#loginSubmit").removeClass('sending').blur();
                }, 2500);
                return true;
            } else {
                $('#email_mobile_invalid').show();
                $("#loginSubmit").removeClass('sending').blur();
                return false;
            }
        });
    }); 

    function validateEmail(email) {
        var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return re.test(email);
    }
</script>

@stop