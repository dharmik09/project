@extends('layouts.home-master')

@push('script-header')
    <title>{{ trans('labels.appname') }} : School</title>
@endpush

@section('content')

    <div class="banner-landing banner-school">
        <div class="container">
            <div class="play-icon">
                <a href="javascript:void(0);" class="play-btn" id="iframe-video-click">
                    <img src="{{ Storage::url('img/play-icon.png') }}" alt="play icon">
                </a>
            </div>
        </div>
        <iframe width="100%" height="100%" src="https://www.youtube.com/embed/4d7ZXKozbtw?autohide=1&amp;showinfo=0&amp;modestBranding=1&amp;start=0&amp;rel=0&amp;enablejsapi=1" frameborder="0" allowfullscreen id="iframe-video"></iframe>
    </div>
    <!-- teen bio-->
    <section class="teen-bio">
        <div class="container-small">
            <div class="row">
                <div class="col-sm-6">
                    <div class="sec-heading school-heading">
                        <h1>School</h1>
                        <span>Finally there’s a solution! </span>
                        <div class="hand-img" data-aos="zoom-in">
                            <img src="{{Storage::url('img/hand-school.png')}}" alt="School">
                        </div>
                    </div>
                    <div class="content">
                        {!! $schoolText !!}
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-sec bg-school">
                        <h2>welcome back</h2>
                        <form id="login_form" method="POST" action="{{ url('school/login-check') }}" autocomplete="off">
                            {{csrf_field()}}
                            <div class="form-group">
                                <input type="text" class="form-control" id="email" maxlength="50" name="email" placeholder="Email or Mobile" value="{{old('email')}}" autocomplete="off" tabindex="1">
                            </div>
                            <div class="form-group">
                                <input type="password" class="form-control pass-visi" id="password" maxlength="20" minlength="6" name="password" placeholder="password" tabindex="2">
                                <span class="visibility-pwd">
                                    <img src="{{ Storage::url('img/view-white.png') }}" alt="view" class="view img">
                                    <img src="{{ Storage::url('img/hide-white.png') }}" alt="view" class="img-hide hide img">
                                </span>
                            </div>
                            <div class="checkbox">
                                <label><input type="checkbox" name="remember_me" value="1" tabindex="3"><span class="checker"></span> Remember me</label>
                            </div>
                            <button type="button" id="loginSubmit" value="SIGN IN" class="btn btn-default" title="SIGN IN" tabindex="4">sign in</button>
                        </form>
                        <p><a href="{{ url('parent/forgot-password') }}" title="Forgot username/password?">Forgot username/password?</a></p>
                        <p>Not enrolled? <a href="{{ url('parent/signup') }}" title="Sign up now.">Sign up now.</a></p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- teen bio end-->
    <!-- testimonial section-->
    <section class="sec-testimonial">
        @include('layouts/testimonials')
    </section>
    <!-- testimonial section end-->
    <!-- content area-->
    <section class="sec-content">
        <div class="container-small">
            <div class="row">
                <div class="col-md-12">
                    <?php $cmsDetails = Helpers::getCmsBySlug('landing-page-info'); ?>
                    @if (isset($cmsDetails->cms_body) && !empty($cmsDetails->cms_body))
                        {!! $cmsDetails->cms_body !!}
                    @endif
                </div>
            </div>
        </div>
    </section>
    <!-- content area end-->
    <!-- masonary section-->
    <section class="sec-masonary">
        <div class="container-large">
            <h2 class="cl-school">lorem ipsum dolor sit amet</h2>
            <div class="row">
                <div class="masonary-grid">
                    <div class="grid_sizer"></div>
                    <div class="product-list clearfix">
                        @forelse($videoDetail as $video)
                            <div class="item clearfix">
                                <div class="grid-box">
                                    <?php
                                        $videoId = '';
                                        $videoCode = Helpers::youtube_id_from_url($video->v_link);
                                        if ($videoCode != '') {
                                            if(strlen($video->v_link) > 50) {
                                                preg_match('/=(.*?)\&/s', $video->v_link, $output);
                                                $videoId = $output[1];
                                            } else {
                                                if (strpos($video->v_link, '=') !== false) {
                                                    $output = explode('=',$video->v_link);
                                                    $videoId = $output[1];
                                                } else {
                                                    $videoId = substr($video->v_link, strrpos($video->v_link, '/') + 1);
                                                }
                                            }
                                        }
                                    ?>
                                    <figure>
                                        <a title="Play : {{ $video->v_title }}" @if($videoId != '') href="https://www.youtube.com/watch?v={{$videoId}}?rel=0&amp;showinfo=0&autoplay=1" @else href="{{$video->v_link}}" @endif class="play-video">
                                            <img src="{{ Storage::url(Config::get('constant.VIDEO_ORIGINAL_IMAGE_UPLOAD_PATH').$video->v_photo) }}" alt="{{ $video->v_title }}">
                                            <div class="overlay">
                                                <i class="icon-play"></i>
                                            </div>
                                        </a>
                                        <h4 class="text-center">{{ $video->v_title }}</h4>
                                        <figcaption>{{ substr($video->v_description, 0, 100) }} </figcaption>
                                    </figure>
                                </div>
                            </div>
                        @empty
                            <div class="col-sm-12 text-center">
                                <h3>Video will coming soon! </h3>
                            </div>
                        @endforelse
                    </div>
                </div>
                @if(count($videoDetail) > 12)
                    <p class="text-center"><a href="#" title="load more" class="btn btn-primary">load more</a></p>
                @endif
            </div>
        </div>
    </section>
    <div class="intro_video_popup" style="display: none;">
        <div class="video_cont">
            <div class="outer_intro">
                <div class="inner_intro">
                    <div class="video_droper">
                        <div class="video_wrapper">

                        </div>
                        <div class="intro_close"><i class="fa fa-times" aria-hidden="true"></i></div>
                    </div>
                    <div class="intro_overlay"></div>
                </div>
            </div>
        </div>
    </div>
    <!-- masonary section end-->
@stop

@section('script')
    <script type="text/javascript">
        jQuery(document).ready(function($) {
            var loginRules = {
                email : {
                    required : true,
                    email : true,
                    maxlength : 50
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
                    email: {required: '<?php echo trans('validation.emailrequired') ?>'},
                    password: {required: '{{trans("validation.passwordrequired")}}',
                        maxlength: 'Password maximum range is 20',
                        minlength: 'Password minimum length is 6'
                    }
                }
            });
            $('#iframe-video-click').on('click', function(ev) {
                $("#iframe-video")[0].src += "&autoplay=1";
                ev.preventDefault();
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
        $(window).bind("load", function() {
            $('.masonary-grid').masonry({
                itemSelector: '.item',
                columnWidth: 1
            });
            AOS.init({
                duration: 1200,
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
        });
        //testimonial slider
        $('.testimonial-slider').owlCarousel({
            loop: true,
            margin: 10,
            items: 1,
            nav: true,
            dots: false,
            smartSpeed: 2000,
        });
        $('.play-icon').click(function () {
            $(this).hide("slow");
            $('iframe').show();
        });
        $("#loginSubmit").click(function() {
            $("#loginSubmit").toggleClass('sending').blur();
            var form = $("#login_form");
            form.validate();
            if (form.valid()) {
                form.submit();
                $("#loginSubmit").removeClass('sending').blur();
            } else {
                $("#loginSubmit").removeClass('sending').blur();
            }
        });
    </script>
@stop