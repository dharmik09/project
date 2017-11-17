@extends('layouts.home-master')

@push('script-header')
    <title>{{trans('labels.appname')}} : Teenager</title>
@endpush

@section('content')

    <div class="banner-landing">
        <div class="container">
            <div class="play-icon">
                <a href="javascript:void(0);" class="play-btn" id="iframe-video">
                    <img src="{{ asset('img/play-icon.png') }}" alt="play icon">
                </a>
            </div>
        </div>
        <iframe width="100%" height="100%" src="https://www.youtube.com/embed/NpEaa2P7qZI?autoplay=1" frameborder="0" allowfullscreen id="iframe-video"></iframe>
    </div>
    <!-- teen bio-->
    <section class="teen-bio">
        <div class="container-small">
            <div class="row">
                <div class="col-sm-6">
                    <div class="sec-heading">
                        <h1>Teen</h1>
                        <span>Finally there’s a solution! </span>
                        <div class="hand-img">
                            <img src="{{Storage::url('img/hand-img.png')}}" alt="hand image">
                        </div>
                    </div>
                    <div class="content">
                        <p>ProTeen is a self-discovery game for teens and is based on their individual aptitude, personality, multiple intelligences and interests. It guides high school and college students through the maze of real world career options and helps them to achieve their future goals by making intelligent academic choices today. ProTeen is a gamified app and web platform.</p>
                        <p>ProTeen supplements traditional school or counselor driven approaches currently in use globally. It encompasses all aspects of the educational ecosystem – students, parents, schools and career mentors such as teachers, counselors and professionals.</p>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-sec">
                        <h2>welcome back</h2>
                        <form>
                            <div class="form-group">
                                <input type="text" class="form-control" id="name" placeholder="username" tabindex="1">
                            </div>
                            <div class="form-group">
                                <input type="password" class="form-control" id="pwd" placeholder="password" tabindex="2">
                            </div>
                            <div class="checkbox">
                                <label><input type="checkbox"><span class="checker"></span> Remember me</label>
                            </div>
                            <button type="submit" class="btn btn-default" title="SIGN IN" tabindex="4">sign in</button>
                        </form>
                        <p><a href="#" title="Forgot username/password?">Forgot username/password?</a></p>
                        <p>Not enrolled? <a href="#" title="Sign up now.">Sign up now.</a></p>
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
                        <span><img src="{{Storage::url('img/quote.png')}}" alt="quote"></span>
                        <p> Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam a tincidunt justo, sit amet tincidunt tortor. Nullam quis augue sem. Aliquam id turpis luctus.</p>
                        <h5><a href="#" title="Lorem ipsum">Lorem ipsum</a> </h5>
                    </div>
                </li>
                <li class="clearfix">
                    <div class="testimonial-img">
                        <img src="{{Storage::url('img/user.jpg')}}" alt="user">
                    </div>
                    <div class="testimonial-content">
                        <span><img src="{{Storage::url('img/quote.png')}}" alt="quote"></span>
                        <p> Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam a tincidunt justo, sit amet tincidunt tortor. Nullam quis augue sem. Aliquam id turpis luctus.</p>
                        <h5><a href="#" title="Lorem ipsum">Lorem ipsum </a></h5>
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
            <h2>lorem ipsum dolor sit amet</h2>
            <div class="row">
                <div class="masonary-grid">
                    <div class="grid_sizer"></div>
                    <div class="product-list clearfix">
                        <div class="item clearfix">
                            <div class="grid-box">
                                <figure>
                                    <a href="https://www.youtube.com/embed/OCWj5xgu5Ng" title="Play" class="play-video"><img src="{{Storage::url('img/grid-1.png')}}" alt="grid img">
                                   <div class="overlay"><i class="icon-play"></i></div></a>
                                    <figcaption>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam a tincidunt justo, sit amet tincidunt </figcaption>
                                </figure>
                            </div>
                        </div>
                        <div class="item clearfix">
                            <div class="grid-box">
                                <figure>
                                    <a href="https://www.youtube.com/embed/OCWj5xgu5Ng" title="Play" class="play-video"><img src="{{Storage::url('img/grid-2.png')}}" alt="grid img"><div class="overlay"><i class="icon-play"></i></div></a>
                                    <figcaption>Lorem ipsum dolor sit amet, consectetur adipiscing elit. </figcaption>
                                </figure>
                            </div>
                        </div>
                        <div class="item clearfix">
                            <div class="grid-box">
                                <figure>
                                    <a href="https://www.youtube.com/embed/OCWj5xgu5Ng" title="Play" class="play-video"><img src="{{Storage::url('img/grid-3.png')}}" alt="grid img"><div class="overlay"><i class="icon-play"></i></div></a>
                                    <figcaption>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam a tincidunt justo, sit amet tincidunt tortor. Nullam quis augue sem. Aliquam id turpis luctus, pellentesque diam sed, vehicula lacus. Fusce sollicitudin arcu sit amet elit accumsan tristique. Curabitur malesuada tortor vel egestas consequat. Nam et rutrum dolor.</figcaption>
                                </figure>
                            </div>
                        </div>
                        <div class="item clearfix">
                            <div class="grid-box">
                                <figure>
                                    <a href="https://www.youtube.com/embed/OCWj5xgu5Ng" title="Play" class="play-video"><img src="{{Storage::url('img/grid-4.png')}}" alt="grid img"><div class="overlay"><i class="icon-play"></i></div></a>
                                    <figcaption>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam a tincidunt justo, sit amet tincidunt tortor. Nullam quis augue sem. Aliquam id turpis luctus, pellentesque diam sed, vehicula lacus. Fusce sollicitudin arcu sit amet elit accumsan tristique. Curabitur malesuada tortor vel egestas consequat. Nam et rutrum dolor. </figcaption>
                                </figure>
                            </div>
                        </div>
                        <div class="item clearfix">
                            <div class="grid-box">
                                <figure>
                                    <a href="https://www.youtube.com/embed/OCWj5xgu5Ng" title="Play" class="play-video"><img src="{{Storage::url('img/grid-5.png')}}" alt="grid img"><div class="overlay"><i class="icon-play"></i></div></a>
                                    <figcaption>Aliquam id turpis luctus, pellentesque diam sed, vehicula lacus. Fusce sollicitudin arcu sit amet elit accumsan tristique. Lorem ipsum dolor sit amet, consectetur adipiscing elit.</figcaption>
                                </figure>
                            </div>
                        </div>
                        <div class="item clearfix">
                            <div class="grid-box">
                                <figure>
                                    <a href="https://www.youtube.com/embed/OCWj5xgu5Ng" title="Play" class="play-video"><img src="{{Storage::url('img/grid-6.png')}}" alt="grid img"><div class="overlay"><i class="icon-play"></i></div></a>
                                    <figcaption>Etiam a tincidunt justo, sit amet tincidunt tortor. Nullam quis augue sem. Aliquam id turpis luctus, pellentesque diam sed, vehicula lacus. Fusce sollicitudin arcu sit amet elit accumsan tristique. Curabitur malesuada tortor vel egestas consequat.</figcaption>
                                </figure>
                            </div>
                        </div>
                        <div class="item clearfix">
                            <div class="grid-box">
                                <figure>
                                    <a href="https://www.youtube.com/embed/OCWj5xgu5Ng" title="Play" class="play-video"><img src="{{Storage::url('img/grid-7.png')}}" alt="grid img"><div class="overlay"><i class="icon-play"></i></div></a>
                                    <figcaption>Lorem ipsum dolor sit amet, consectetur adipiscing elit. </figcaption>
                                </figure>
                            </div>
                        </div>
                        <div class="item clearfix">
                            <div class="grid-box">
                                <figure>
                                    <a href="https://www.youtube.com/embed/OCWj5xgu5Ng" title="Play" class="play-video"><img src="{{Storage::url('img/grid-8.png')}}" alt="grid img"><div class="overlay"><i class="icon-play"></i></div></a>
                                    <figcaption>Fusce sollicitudin arcu sit amet elit accumsan tristique. Curabitur</figcaption>
                                </figure>
                            </div>
                        </div>
                    </div>
                </div>
                <p class="text-center"><a href="#" title="load more" class="btn btn-primary">load more</a></p>
            </div>
        </div>
    </section>
    <!-- masonary section end-->
@stop

@section('script')
    <script src="{{ asset('js/masonry.pkgd.js') }}"></script>
    <script src="{{ asset('js/jquery.magnific-popup.min.js') }}"></script>
    <script src="{{ asset('js/general.js') }}"></script>
    <script type="text/javascript">
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
    </script>
@stop