@extends('layouts.home-master')

@push('script-header')
    <title>{{ trans('labels.appname') }} : {{$type}}</title>
@endpush

@section('content')

    <div class="banner-landing {{ ($type == 'Parent') ? 'banner-parent' : 'banner-mentor'}}">
        <div class="container">
            <div class="play-icon">
                <a href="javascript:void(0);" class="play-btn" id="iframe-video">
                    <img src="{{ asset('img/play-icon.png') }}" alt="play icon">
                </a>
            </div>
        </div>
        <iframe width="100%" height="100%" src="https://www.youtube.com/embed/NpEaa2P7qZI?rel=0&amp;showinfo=0&autoplay=1" frameborder="0" allowfullscreen id="iframe-video"></iframe>
    </div>
    <!-- teen bio-->
    <section class="teen-bio">
        <div class="container-small">
            <div class="row">
                <div class="col-sm-6">
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
                        <p>ProTeen is a self-discovery game for teens and is based on their individual aptitude, personality, multiple intelligences and interests. It guides high school and college students through the maze of real world career options and helps them to achieve their future goals by making intelligent academic choices today. ProTeen is a gamified app and web platform.</p>
                        <p>ProTeen supplements traditional school or counselor driven approaches currently in use globally. It encompasses all aspects of the educational ecosystem – students, parents, schools and career mentors such as teachers, counselors and professionals.</p>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-sec {{ ($type == 'Parent') ? 'bg-parent' : 'bg-mentor'}}">
                        <h2>welcome back</h2>
                        <form id="login_form" method="POST" action="{{ url('/parent/login-check') }}" autocomplete="off">
                            {{csrf_field()}}
                            <input type="hidden" name="user_type" id="user_type" value="{{ ($type == 'Parent') ? 1 : 2 }}">
                            <div class="form-group">
                                <input type="text" class="form-control" id="email" maxlength="50" name="email" placeholder="Email or Mobile" value="{{old('email')}}" autocomplete="off" tabindex="1">
                            </div>
                            <div class="form-group">
                                <input type="password" class="form-control" id="password" maxlength="20" minlength="6" name="password" placeholder="password" tabindex="2">
                            </div>
                            <div class="checkbox">
                                <label><input type="checkbox" name="remember_me" value="1" tabindex="3"><span class="checker"></span> Remember me</label>
                            </div>
                            <button type="button" id="loginSubmit" value="SIGN IN" class="btn btn-default" title="SIGN IN" tabindex="4">sign in</button>
                        </form>
                        <?php 
                            if($type == 'Parent')
                            {
                                $signUpRoute = url('parent/signup');
                                $passwordRoute = url('parent/forgot-password');
                            } else {
                                $signUpRoute = url('counselor/signup');
                                $passwordRoute = url('counselor/forgot-password');
                            }
                        ?>
                        <p><a href="{{ $passwordRoute }}" title="Forgot username/password?">Forgot username/password?</a></p>
                        <p>Not enrolled? <a href="{{ $signUpRoute }}" title="Sign up now.">Sign up now.</a></p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- teen bio end-->
    <!-- testimonial section-->
    <section class="sec-testimonial">
        <div class="container-small clearfix">
            <ul class="testimonial-slider owl-carousel clearfix">
                <li class="clearfix">
                    <div class="testimonial-img">
                        <img src="{{Storage::url('img/user.jpg')}}" alt="user">
                    </div>
                    <div class="testimonial-content">
                        <span>
                            @if($type == 'Parent')
                                <img src="{{Storage::url('img/quote-blue.png')}}" alt="quote">
                            @else
                                <img src="{{Storage::url('img/quote-mentor.png')}}" alt="quote">
                            @endif
                        </span>
                        <p> Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam a tincidunt justo, sit amet tincidunt tortor. Nullam quis augue sem. Aliquam id turpis luctus.</p>
                        <h5>
                            <a href="#" title="Lorem ipsum" class="{{ ($type == 'Parent') ? 'font-blue' : 'cl-mentor' }}">Lorem ipsum</a>
                        </h5>
                    </div>
                </li>
                <li class="clearfix">
                    <div class="testimonial-img">
                        <img src="{{Storage::url('img/user.jpg')}}" alt="user">
                    </div>
                    <div class="testimonial-content">
                        <span>
                            @if($type == 'Parent')
                                <img src="{{Storage::url('img/quote-blue.png')}}" alt="quote">
                            @else
                                <img src="{{Storage::url('img/quote-mentor.png')}}" alt="quote">
                            @endif
                        </span>
                        <p> Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam a tincidunt justo, sit amet tincidunt tortor. Nullam quis augue sem. Aliquam id turpis luctus.</p>
                        <h5><a href="#" title="Lorem ipsum" class="{{ ($type == 'Parent') ? 'font-blue' : 'cl-mentor' }}">Lorem ipsum </a></h5>
                    </div>
                </li>
            </ul>
        </div>
    </section>
    <!-- testimonial section end-->
    <!-- content area-->
    <section class="sec-content">
        <div class="container-small">
            <div class="row">
                <div class="col-md-6">
                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam a tincidunt justo, sit amet tincidunt tortor. Nullam quis augue sem. Aliquam id turpis luctus, pellentesque diam sed, vehicula lacus. Fusce sollicitudin arcu sit amet elit accumsan tristique. Curabitur malesuada tortor vel egestas consequat. Nam et rutrum dolor. In consectetur ante in odio viverra, et posuere sapien mattis. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Quisque blandit ornare eros nec facilisis. Fusce varius odio sit amet ornare dictum. Nunc sed magna et quam suscipit porta. Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Sed eros est, rutrum vitae augue id, placerat consequat velit. Sed magna leo, aliquam quis suscipit at, egestas vel nisl. Cras nec orci ac risus sagittis bibendum sit amet sit amet </p>
                </div>
                <div class="col-md-6">
                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam a tincidunt justo, sit amet tincidunt tortor. Nullam quis augue sem. Aliquam id turpis luctus, pellentesque diam sed, vehicula lacus. Fusce sollicitudin arcu sit amet elit accumsan tristique. Curabitur malesuada tortor vel egestas consequat. Nam et rutrum dolor. In consectetur ante in odio viverra, et posuere sapien mattis. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Quisque blandit ornare eros nec facilisis. Fusce varius odio sit amet ornare dictum. Nunc sed magna et quam suscipit porta. Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Sed eros est, rutrum vitae augue id, placerat consequat velit. Sed magna leo, aliquam quis suscipit at, egestas vel nisl. Cras nec orci ac risus sagittis bibendum sit amet sit amet </p>
                </div>
            </div>
        </div>
    </section>
    <!-- content area end-->
    <!-- masonary section-->
    <section class="sec-masonary">
        <div class="container-large">
            <h2 class="{{ ($type == 'Parent') ? 'font-blue' : 'cl-mentor' }}">lorem ipsum dolor sit amet</h2>
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
                                        <figcaption>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam a tincidunt justo, sit amet tincidunt </figcaption>
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
        });
        $('.play-icon').click(function () {
            $(this).hide();
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