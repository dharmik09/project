@extends('layouts.home-master')

@push('script-header')
    <title>{{ trans('labels.appname') }} : {{$type}}</title>
@endpush

@section('content')

    <div class="banner-landing {{ ($type == 'Parent') ? 'banner-parent' : 'banner-mentor'}}">
        <div class="container">
            <div class="play-icon"><a href="javascript:void(0);" class="play-btn" id="iframe-video-click"><img src="img/play-icon.png" alt="play icon"></a></div>
        </div>
        <?php $video = ($type == 'Parent') ? 'S6FgCxlf9Tw' : 'jFzLKuxhv3I'; ?>
        <iframe width="100%" height="100%" src="https://www.youtube.com/embed/{{Config::get('constant.PROTEEN_INTRO_VIDEO')}}?autohide=1&amp;showinfo=0&amp;modestBranding=1&amp;start=0&amp;rel=0&amp;enablejsapi=1" frameborder="0" allow="autoplay" allowfullscreen id="iframe-video"></iframe>
    </div>
    <!-- teen bio-->
    <section class="teen-bio">
        <div class="container-small">
            <div class="row flex-container">
                <div class="col-sm-6 flex-items order-1">
                    <div class="full-width ">
                        <div class="sec-heading {{ ($type == 'Parent') ? 'parent-heading' : 'mentor-heading'}}">
                            <h1>{{ ucfirst($type) }}</h1>
                            <span>Finally there’s a solution! </span>
                            <div class="hand-img" data-aos="zoom-in">
                                @if($type == 'Parent')
                                    <img src="{{Storage::url('img/hand-blue.png')}}" alt="{{ ucfirst($type) }}">
                                @else
                                    <img src="{{Storage::url('img/hand-mentor.png')}}" alt="{{ ucfirst($type) }}">
                                @endif
                            </div>
                        </div>
                        <div class="content">
                            <?php $arrangedText = explode("###", preg_replace("/&nbsp;/", '', $text)); ?>
                            @if(isset($arrangedText[0]) && !empty($arrangedText[0]))
                                {!! $arrangedText[0] !!}
                            @else
                                {!! $text !!}
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 flex-items order-3">
                    <div class="form-sec {{ ($type == 'Parent') ? 'bg-parent' : 'bg-mentor'}}">
                        <h2>welcome back</h2>
                        <form id="login_form" method="POST" action="{{ url('/parent/login-check') }}" autocomplete="off">
                            {{csrf_field()}}
                            <input type="hidden" name="user_type" id="user_type" value="{{ ($type == 'Parent') ? 1 : 2 }}">
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
                            <?php 
                            if($type == 'Parent')
                            {
                                $signUpRoute = url('parent/signup');
                                //$passwordRoute = url('parent/forgot-password');
                            } else {
                                $signUpRoute = url('counselor/signup');
                                //$passwordRoute = url('counselor/forgot-password');
                            }
                            ?>
                     
                            <div class="checkbox">
                                <label><input type="checkbox" name="remember_me" value="1" tabindex="3"><span class="checker"></span> Remember me</label>
                                <span class="pull-right"><a href="{{ url('parent/forgot-password') }}" title="Forgot username/password?">Forgot password?</a></span>
                            </div>
                            <button type="submit" id="loginSubmit" value="SIGN IN" class="btn btn-default" title="SIGN IN" tabindex="4">sign in</button>
                        </form>
                        <p>Not enrolled? <a href="{{ $signUpRoute }}" title="Sign up now.">Sign up now.</a></p>
                    </div>
                </div>
                <div class="col-sm-12 flex-items order-2">
                   <div class="full-width">
                        @if(isset($arrangedText[1]) && !empty($arrangedText[1]))
                            <p>{!! $arrangedText[1] !!}</p>
                        @endif
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
                <div class="col-md-6">
                    <?php
                        $cmsDetails = Helpers::getCmsBySlug('landing-page-info');
                    ?>
                    @if (isset($cmsDetails->cms_body) && !empty($cmsDetails->cms_body))
                        <?php $landingPageText = explode("###", preg_replace("/&nbsp;/", '', $cmsDetails->cms_body)); ?>
                        @if(isset($landingPageText[0]) && !empty($landingPageText[0]))
                            {!! $landingPageText[0] !!}
                        @else
                            {!! $$cmsDetails->cms_body !!}
                        @endif
                    @endif
                </div>
                <div class="col-md-6">
                    @if(isset($landingPageText[1]) && !empty($landingPageText[1]))
                        {!! $landingPageText[1] !!}
                    @endif
                </div>
            </div>
        </div>
<!--        <div class="finalist-img">
            <span><img src="{{ Storage::url('img/award-finalist-transparent.png')}}" alt="award img"></span>
        </div>-->
    </section>
    <!-- content area end-->    
    <!-- masonary section-->
    <section class="sec-masonary">
        <div class="container-large">
            <h2 class="{{ ($type == 'Parent') ? 'font-blue' : 'cl-mentor' }}">Listen to our Community</h2>
            <h3 class="{{ ($type == 'Parent') ? 'font-blue' : 'cl-mentor' }}">Real Stories by Real People</h3>
            <div class="row">
                <div class="masonary-grid">
                    <div class="grid_sizer"></div>
                    <div class="product-list clearfix">
                        @include('teenager/loadMoreVideo')
                    </div>
                </div>
                <div id="load-video"></div>
                @if(isset($nextSlotExist) && count($nextSlotExist) > 0)
                    <div id="video-loader" class="loader_con">
                        <img src="{{Storage::url('img/loading.gif')}}">
                    </div>
                    <p id="remove-row" class="text-center"><a id="load-more" href="javascript:void(0)" title="see more" class="btn btn-primary">see more</a></p>
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
                $('iframe').show();
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
            var videoSlot = 0;
            $(document).on('click','#load-more',function(){
                videoSlot = videoSlot + 1;
                $("#video-loader").show();
                $.ajax({
                    url : '{{ url("parent/load-more-video") }}',
                    method : "POST",
                    data : {slot:videoSlot, _token:"{{csrf_token()}}"},
                    dataType : "json",
                    success : function (data) {
                        if(data.view != '') {
                            $("#video-loader").hide();
                            if (data.nextSlotExist <= 0) {
                                $('#remove-row').remove();
                            } 
                            $('.masonary-grid').append(data.view);
                            $('.masonary-grid').masonry('reloadItems');
                            $('.masonary-grid').masonry();
                        } 
                    }
                });
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
            autoplay:30000
        });
        $('.play-icon').click(function () {
            $(this).hide();
            $('iframe').show();
        });
        $("#login_form").submit(function() {
            $("#loginSubmit").toggleClass('sending').blur();
            var form = $("#login_form");
            form.validate();
            if (form.valid()) {
                //form.submit();
                return true;
                setTimeout(function () {
                    $("#loginSubmit").removeClass('sending').blur();
                }, 2500);
            } else {
                $("#loginSubmit").removeClass('sending').blur();
            }
        });
    </script>
@stop