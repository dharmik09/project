@extends('layouts.home-master')

@push('script-header')
    <title>{{ trans('labels.appname') }} : Student</title>
@endpush

@section('content')

    <div class="banner-landing">
        <div class="container">
<!--            <div class="play-icon">
                <a href="javascript:void(0);" class="play-btn" id="iframe-video-click">
                    <img src="{{ Storage::url('img/play-icon.png') }}" alt="play icon">
                </a>
            </div>-->
        </div>
        <iframe width="100%" height="100%" src="https://www.youtube.com/embed/WoelVRjFO4A?autohide=1&amp;showinfo=0&amp;modestBranding=1&amp;start=0&amp;rel=0&amp;enablejsapi=1" frameborder="0" allowfullscreen id="iframe-video"></iframe>
    </div>
    <!-- teen bio-->
    <section class="teen-bio">
        <div class="container-small">
            <div class="row flex-container">
                <div class="col-sm-6 flex-items order-1">
                    <div class="full-width ">
                        <div class="sec-heading">
                            <h1>Student</h1>
                            <span>Finally there’s a solution! </span>
                            <div class="hand-img" data-aos="zoom-in">
                                <img src="{{Storage::url('img/hand-img.png')}}" alt="Teenager">
                            </div>
                        </div>
                        <div class="content">
                            <?php $arrangedText = explode("###", preg_replace("/&nbsp;/", '', $teenText)); ?>
                            @if(isset($arrangedText[0]) && !empty($arrangedText[0]))
                                {!! $arrangedText[0] !!}
                            @else
                                {!! $teenText !!}
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 flex-items order-3">
                    <div class="form-sec">
                        <h2>welcome back</h2>
                        <form id="login_form" method="POST" action="{{ url('/teenager/login-check') }}" autocomplete="off">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <div class="form-group">
                                <input type="text" class="form-control {eitherEmailPhone:true}" id="email" maxlength="50" name="email" placeholder="Email or Mobile" value="" autocomplete="off" tabindex="1">
                                <span class="invalid" id="email_mobile_invalid" style="display: none;">Valid email or mobile required</span>
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
                                <span class="pull-right"><a href="{{ url('teenager/forgot-password') }}" title="Forgot password?">Forgot password?</a></span>
                            </div>
                            <button type="submit" id="loginSubmit" value="SIGN IN" class="btn btn-default" title="SIGN IN" tabindex="4">sign in</button>
                            <ul class="btn-list">
                                <li><a href="{{ url('teenager/facebook') }}" title="Facebook" ><i class="icon-facebook"><!-- --></i>Facebook</a></li>
                                <li><a href="{{ url('teenager/google') }}" title="Google" ><i class="icon-google"><!-- --></i>Google</a></li>
                            </ul>
                        </form>
                        <p>Not enrolled? <a href="{{ url('teenager/signup') }}" title="Sign up now.">Sign up now.</a></p>
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
    </section>
    <!-- content area end-->
    <!-- masonary section-->
    <section class="sec-masonary">
        <div class="container-large">
            <h2>Listen to our Community</h2>
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
                                        <figcaption>{{ $video->v_description }} </figcaption>
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
                <div id="load-video"></div>
                @if(isset($videoCount) && $videoCount > 12)
                    <p id="remove-row" class="text-center"><a id="load-more" href="javascript:void(0)" title="load more" data-id="{{ $video->id }}" class="btn btn-primary">load more</a></p>
                @endif
            </div>
        </div>
    </section>
    <!-- masonary section end-->
    <div class="finalist-img">
        <span><img src="{{ Storage::url('img/award-finalist-transparent.png')}}" alt="award img"></span>
    </div>
@stop

@section('script')
    <script type="text/javascript">
        jQuery(document).ready(function($) {
            var loginRules = {
                password: {
                    required: true,
                    minlength: 6,
                    maxlength: 20
                }
            };
            $("#login_form").validate({
                rules: loginRules,
                messages: {
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

            $(document).on('click','#load-more',function(){
                var id = $(this).data('id');
                //$("#btn-more").html("Loading....");
                $.ajax({
                    url : '{{ url("teenager/load-more-video") }}',
                    method : "POST",
                    data : {id:id, _token:"{{csrf_token()}}"},
                    dataType : "text",
                    success : function (data) {
                        if(data != '') {
                            $('#remove-row').remove();
                            $('#load-video').append(data);
                        } else {
                            //$('#btn-more').html("No Data");
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
            $(this).hide("slow");
            $('iframe').show();
        })
        $("#login_form").submit(function() {
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
                    $("#loginSubmit").toggleClass('sending').blur();
                    //form.submit();
                    return true;
                }
                setTimeout(function () {
                    $("#loginSubmit").removeClass('sending').blur();
                }, 2500);
                return true;
            } else {
                $("#loginSubmit").removeClass('sending').blur();
                $('#email_mobile_invalid').show();
                return false;
            }
        });
        
        function validateEmail(email) {
            var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
            return re.test(email);
        }
    </script>
@stop