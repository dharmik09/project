<?php 
    $metaTitle = $_GET['title'];
    $metaDescription = $_GET['description'];
    $siteUrl = url($_GET['store']);
    $shareImageUrl = $_GET['image'];
?>
@extends('layouts.home-master')

@push('script-header')
    <title>{{ $metaTitle }}</title>
    <meta name="title" content="{{$metaTitle}}" />
    <meta name="description" content="{{$metaDescription}}" />
    <meta name="keywords" content="{{$metaTitle}}" />
    <!-- Twitter Card data -->
    <meta name="twitter:card" content="summary" />
    <meta name="twitter:site" content="@ProTeenLife" />
    <meta name="twitter:title" content="{{$metaTitle}}" />
    <meta name="twitter:description" content="{{$metaDescription}}" />
    <meta name="twitter:creator" content="@ProTeenLife" />
    <meta name="twitter:image"  content="{{$shareImageUrl}}"  />
    <!-- Facebook Card data -->
    <meta property="fb:app_id" content="1899859370300984" />
    <meta property="og:title" content="{{$metaTitle}}" />
    <meta property="og:type" content="deal" />
    <!-- <meta property="og:url" content="{{$siteUrl}}" /> -->
    <meta property="og:image"  content="{{$shareImageUrl}}"  />
    <meta property="og:description" content="{{$metaDescription}}" />
    <meta property="og:site_name" content="ProTeenLife" />
@endpush

@section('content')
    <section class="sec-login">
        <div class="container-small">
            <div class="login-form">
                <h1>Click Here to Login</h1>
                <div class="form-sec">
                    <form  autocomplete="off">
                        <a href="{{$siteUrl}}"> <button value="Login" class="btn btn-default" title="Login" tabindex="4">Login</button> </a>
                    </form>
                </div>
            </div>
        </div>
    </section>
@stop

@section('script')
    <script type="text/javascript">
        jQuery(document).ready(function() {
            var loginRules = {
                password: {
                    required: true,
                    minlength: 6,
                    maxlength: 20
                },
            };
            $("#login_form").validate({
                rules: loginRules,
                messages: {
                    password: {required: '{{trans("validation.passwordrequired")}}',
                        maxlength: 'Password maximum range is 20',
                        minlength: 'Password minimum length is 6'
                    }
                },
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
        });
        //masonary
        $('.masonary-grid').masonry({
            // options
            itemSelector: '.item',
            columnWidth: 1
        });
        //video popup
        $('.play-video').magnificPopup({
            disableOn: 0,
            type: 'iframe',
            mainClass: 'mfp-fade',
            removalDelay: 160,
            preloader: false,
            overflowY: 'auto',
            removalDelay: 300,
            midClick: true,
            fixedBgPos: true,
            fixedContentPos: true
        });
        //testimonial slider
        $('.testimonial-slider').owlCarousel({
            loop: true,
            margin: 10,
            items: 1,
            nav: true,
            dots: false,
        });
        $('.play-icon').click(function () {
            $(this).hide();
            $('iframe').show();
        })
        $("#login_form").submit(function() {
            $("#loginSubmit").toggleClass('sending').blur();
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

        function validateEmail(email) {
            var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
            return re.test(email);
        }
        AOS.init({
            duration: 1200,
        });
    </script>
@stop