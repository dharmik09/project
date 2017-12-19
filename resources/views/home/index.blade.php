@extends('layouts.home-master')

@push('script-header')
    <title>{{trans('labels.appname')}}</title>
@endpush

@section('content')

    <div class="section">
        <div class="banner-slider">
            <ul class="intro-slider owl-carousel" id="intro">
                <div class="banner-0-1 banner item">
                    <div class="container">
                        <div class="left-section">
                            <h1>
                                <span class="tag-intro">Who</span>
                                <span class="tag-image">
                                    <img class="i-arrow-1" alt="" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAPwAAABtAQMAAAC/X57CAAAAA1BMVEX///+nxBvIAAAAAXRSTlMAQObYZgAAABlJREFUeNrtwQEBAAAAgiD/r25IQAEAAHBnDg0AAQ/0vT4AAAAASUVORK5CYII=">
                                </span>
                                <span class="tag-close">am I?</span>
                            </h1>
                            <p>Finally there’s a solution! ProTeen is a web-based game that helps you navigate the world of careers and academics.</p>
                        </div>
                        <a href="#teen" class="bottom-show"><i class="icon-down-arrow"></i></a>
                    </div>
                </div>
                <div class="banner-0-2 banner item">
                    <div class="container">
                        <div class="left-section">
                            <h1>
                                <span class="tag-intro">Who</span>
                                <span class="tag-image"><img class="i-dream-white" alt="" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAQQAAACsAQMAAACn/kB+AAAAA1BMVEX///+nxBvIAAAAAXRSTlMAQObYZgAAAB1JREFUeNrtwQENAAAAwqD3T20ON6AAAAAAAODHABbYAAFfvr7GAAAAAElFTkSuQmCC"></span>
                                <span class="tag-close">can i be?</span>
                            </h1>
                            <p>Finally there’s a solution! ProTeen is a web-based game that helps you navigate the world of careers and academics.</p>
                        </div>
                        <a href="#teen" class="bottom-show"><i class="icon-down-arrow"></i></a>
                    </div>
                </div>
                <div class="banner-0-3 banner item">
                    <div class="container">
                        <div class="left-section">
                            <h1>
                                <span class="tag-intro">Learn your</span>
                                <span class="tag-image"><img class="i-brain-white" alt="" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAANgAAAC8AQMAAAAgi/LiAAAAA1BMVEX///+nxBvIAAAAAXRSTlMAQObYZgAAABxJREFUeNrtwTEBAAAAwqD1T20LL6AAAAAAAD4GFJAAAXP7Mr4AAAAASUVORK5CYII="></span>
                                <span class="tag-close">true potential</span>
                            </h1>
                            <p>Finally there’s a solution! ProTeen is a web-based game that helps you navigate the world of careers and academics.</p>
                        </div>
                        <a href="#teen" class="bottom-show"><i class="icon-down-arrow"></i></a>
                    </div>
                </div>
                <div class="banner-0-4 banner item">
                    <div class="container">
                        <div class="left-section">
                            <h1>
                                <span class="tag-intro">Unlock a</span>
                                <span class="tag-image"><img class="i-rocket-white" alt="" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAKoAAADVAQMAAAD9zPnVAAAAA1BMVEX///+nxBvIAAAAAXRSTlMAQObYZgAAABtJREFUeNrtwQEBAAAAgiD/r25IQAEAAADAtQETIwABIA/h6QAAAABJRU5ErkJggg=="></span>
                                <span class="tag-close">brighter future</span>
                            </h1>
                            <p>Finally there’s a solution! ProTeen is a web-based game that helps you navigate the world of careers and academics.</p>
                        </div>
                        <a href="#teen" class="bottom-show"><i class="icon-down-arrow"></i></a>
                    </div>
                </div>
            </ul>
            <div class="slider-maper">
                <div class="container">
                    <ul class="clearfix">
                        <li class="hidden"><a href="#intro">intro</a></li>
                        <li class="teen"><a href="#teen">Teen</a></li>
                        <li class="parent"><a href="#parent">Parent</a></li>
                        <li class="mentor"><a href="#mentor">Mentor</a></li>
                        <li class="school"><a href="#school">School</a></li>
                        <li class="enterprise"><a href="#enterprise">Enterprise</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="banner-1 banner" id="teen">
            <div class="container">
                <div class="row">
                    <div class="col-md-6">
                        <img class="i-teen-arrow decorator" alt="" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAIEAAAB/AQMAAADy9jN6AAAAA1BMVEX///+nxBvIAAAAAXRSTlMAQObYZgAAABlJREFUeNpjYBgFo2AUjIJRMApGwSgYzgAACO4AASHaSoYAAAAASUVORK5CYII=">
                        <img class="i-hand-teen decorator" alt="" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAKcAAACbAQMAAAA9YBl5AAAAA1BMVEX///+nxBvIAAAAAXRSTlMAQObYZgAAABpJREFUeNrtwTEBAAAAwqD1T20Hb6AAAAD4DA1SAAGMYcmhAAAAAElFTkSuQmCC">
                    </div>
                    <div class="col-md-6">
                        <div class="box">
                            <h1>teen</h1>
                            <p>Are you confused about which subjects to choose in senior school or which major to choose in college? <br /><br />Finally there’s a solution! ProTeen is a web-based game that helps you navigate the world of careers and academics and understand how to match your skills </p>
                            <a href="{{url('/teenager')}}" class="btn-base" title="Explore Teenager">Explore</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="slider-maper">
                <div class="container">
                    <ul class="clearfix">
                        <li class="hidden"><a href="#intro">intro</a></li>
                        <li class="teen teen-active"><a href="#teen">Teen</a></li>
                        <li class="parent"><a href="#parent">Parent</a></li>
                        <li class="mentor"><a href="#mentor">Mentor</a></li>
                        <li class="school"><a href="#school">School</a></li>
                        <li class="enterprise"><a href="#enterprise">Enterprise</a></li>
                    </ul>
                </div>
            </div>
            <a href="#parent" class="bottom-show"><i class="icon-down-arrow"></i></a>
        </div>
        <div class="banner-2 banner" id="parent">
            <div class="container">
                <div class="row">
                    <div class="col-md-6">
                        <div class="box">
                            <h1>parent</h1>
                            <p>Parenting is a challenging job and helping your teens make decisions about their future is a daunting task. There are hundreds of academic and career options available. PROTEEN brings hope and takes a fresh approach to the standard psychometric assessment of intelligence.</p>
                            <a href="{{url('/parent')}}" class="btn-base" title="Explore Parent">Explore</a>
                            <img class="i-parent-arrow decorator" alt="" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAALAAAABPAQMAAABxvd3RAAAAA1BMVEX///+nxBvIAAAAAXRSTlMAQObYZgAAABdJREFUeNpjYBgFo2AUjIJRMApGARwAAAcZAAFZsHVlAAAAAElFTkSuQmCC">
                            <img class="i-parent-hand decorator" alt="" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAK8AAAB+AQMAAABoPtO8AAAAA1BMVEX///+nxBvIAAAAAXRSTlMAQObYZgAAABpJREFUeNrtwTEBAAAAwqD1T20Hb6AAAIDfAAtSAAH+RaQAAAAAAElFTkSuQmCC">
                        </div>
                    </div>
                </div>
            </div>
            <div class="slider-maper">
                <div class="container">
                    <ul class="clearfix">
                        <li class="hidden"><a href="#intro">intro</a></li>
                        <li class="teen"><a href="#teen">Teen</a></li>
                        <li class="parent parent-active"><a href="#parent">Parent</a></li>
                        <li class="mentor"><a href="#mentor">Mentor</a></li>
                        <li class="school"><a href="#school">School</a></li>
                        <li class="enterprise"><a href="#enterprise">Enterprise</a></li>
                    </ul>
                </div>
            </div>
            <a href="#mentor" class="bottom-show"><i class="icon-down-arrow"></i></a>
        </div>
        <div class="banner-3 banner" id="mentor">
            <div class="container">
                <div class="row">
                    <div class="col-md-6"></div>
                    <div class="col-md-6">
                        <div class="box">
                            <img class="i-mentor-arrow decorator" alt="" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAIgAAAAxAQMAAAA7sXOsAAAAA1BMVEX///+nxBvIAAAAAXRSTlMAQObYZgAAABFJREFUeNpjYBgFo2AU0A8AAANyAAGw+IxhAAAAAElFTkSuQmCC">
                            <img class="i-mentor-hand decorator" alt="" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAM8AAACcAQMAAADiVW1FAAAAA1BMVEX///+nxBvIAAAAAXRSTlMAQObYZgAAABtJREFUeNrtwTEBAAAAwqD1T20JT6AAAAAA/gYQdAAB5WpmIgAAAABJRU5ErkJggg==">
                            <h1>mentor</h1>
                            <p>Education and career mentors face a daunting task – guiding students to make good academic and career choices is a challenge. <br />But now there's a new way – ProTeen brings hope and takes a fresh approach to the standard psychometric assessment of intelligence. </p>
                            <a href="{{ url('/counselor') }}" class="btn-base" title="Explore Mentor">Explore</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="slider-maper">
                <div class="container">
                    <ul class="clearfix">
                        <li class="hidden"><a href="#intro">intro</a></li>
                        <li class="teen"><a href="#teen">Teen</a></li>
                        <li class="parent"><a href="#parent">Parent</a></li>
                        <li class="mentor mentor-active"><a href="#mentor">Mentor</a></li>
                        <li class="school"><a href="#school">School</a></li>
                        <li class="enterprise"><a href="#enterprise">Enterprise</a></li>
                    </ul>
                </div>
            </div>
            <a href="#school" class="bottom-show"><i class="icon-down-arrow"></i></a>
        </div>
        <div class="banner-4 banner" id="school">
            <div class="container">
                <div class="row">
                    <div class="col-md-6">
                        <div class="box">
                            <h1>school</h1>
                            <p>Every school aspires to create generations of accomplished students who go to college, gain skills to eventually become productive members of society. ProTeen’s assessment and functionality is built as a unique web-based game that allows students to navigate the world of professions.</p>
                            <a href="{{ url('/school') }}" class="btn-base" title="Explore School">Explore</a>
                            <img class="i-school-arrow decorator" alt="" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAMkAAACIAQMAAAB3DF2PAAAAA1BMVEX///+nxBvIAAAAAXRSTlMAQObYZgAAABpJREFUeNrtwTEBAAAAwqD1T20Hb6AAAADgNw5YAAGqN+r4AAAAAElFTkSuQmCC">
                            <img class="i-school-hand decorator" alt="" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAK8AAAB9AQMAAADuqqESAAAAA1BMVEX///+nxBvIAAAAAXRSTlMAQObYZgAAABpJREFUeNrtwYEAAAAAw6D5U1/hAFUBAACHAQs7AAFxHAdOAAAAAElFTkSuQmCC">
                        </div>
                    </div>
                </div>
            </div>
            <div class="slider-maper">
                <div class="container">
                    <ul class="clearfix">
                        <li class="hidden"><a href="#intro">intro</a></li>
                        <li class="teen"><a href="#teen">Teen</a></li>
                        <li class="parent"><a href="#parent">Parent</a></li>
                        <li class="mentor"><a href="#mentor">Mentor</a></li>
                        <li class="school school-active"><a href="#school">School</a></li>
                        <li class="enterprise"><a href="#enterprise">Enterprise</a></li>
                    </ul>
                </div>
            </div>
            <a href="#enterprise" class="bottom-show"><i class="icon-down-arrow"></i></a>
        </div>
        <div class="banner-5 banner" id="enterprise">
            <div class="container">
                <div class="row">
                    <div class="col-md-6"></div>
                    <div class="col-md-6">
                        <div class="box">
                            <img class="i-enterprise-arrow decorator" alt="" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAKsAAABRAQMAAACXL8QlAAAAA1BMVEX///+nxBvIAAAAAXRSTlMAQObYZgAAABdJREFUeNpjYBgFo2AUjIJRMApGAZkAAAdHAAHlvLOKAAAAAElFTkSuQmCC">
                            <img class="i-enterprise-hand decorator" alt="" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAKAAAACVAQMAAADltmNwAAAAA1BMVEX///+nxBvIAAAAAXRSTlMAQObYZgAAABpJREFUeNrtwYEAAAAAw6D5Ux/hAlUBAAAcAww5AAEHz3YTAAAAAElFTkSuQmCC">
                            <h1>enterprise</h1>
                            <p>ProTeen is a sponsorship platform for corporates and academic institutions that aspire to get connected with very specific young adult communities in a socially responsible way. Promote your services, promote your Educational Program, access powerful analytics and more.</p>
                            <a href="{{ url('/sponsor') }}" class="btn-base" title="Explore Enterprise">Explore</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="slider-maper">
                <div class="container">
                    <ul class="clearfix">
                        <li class="hidden"><a href="#intro">intro</a></li>
                        <li class="teen"><a href="#teen">Teen</a></li>
                        <li class="parent"><a href="#parent">Parent</a></li>
                        <li class="mentor"><a href="#mentor">Mentor</a></li>
                        <li class="school"><a href="#school">School</a></li>
                        <li class="enterprise active"><a href="#enterprise">Enterprise</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@stop

@section('script')
    <script>
        jQuery(document).ready(function($) {
            $('.intro-slider').owlCarousel({
                loop: true,
                margin: 0,
                items: 1,
                mouseDrag: false,
                touchDrag: false,
                autoplay: true,
                autoplayTimeout:7000,
                smartSpeed: 2000,
                dots:true,
                animateOut: 'fadeOut',
                animateIn: 'fadeIn',
            });
            $(".owl-dots").wrap("<div class='container dot-container'></div>");
            $(window).scroll(function () {
                var y = $(this).scrollTop();
                $('.slider-maper a').each(function (event) {
                    if (y >= $($(this).attr('href')).offset().top - ($(window).height()/3)) {
                        $('.slider-maper a').not(this).closest('li').removeClass('active');
                        $(this).closest('li').addClass('active');
                        $($(this).attr('href')).addClass('active');
                    }
                });
            });
            function goToByScroll(id) {
                id = id.replace("link", "");
                $('html,body').animate({
                    scrollTop: $(id).offset().top
                }, 'slow');
            }
            $("a.bottom-show").click(function(e) {
                e.preventDefault();
                goToByScroll($(this).attr("href"));
            });
        });
        // SMOOTH SCROLLING (with negative scroll of 40 for header)
        $(function () {
            $('.slider-maper a').click(function () {
                if (location.pathname.replace(/^\//, '') == this.pathname.replace(/^\//, '') && location.hostname == this.hostname) {
                    var target = $(this.hash);
                    target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');
                    if (target.length) {
                        $('html,body').animate({
                            scrollTop: (target.offset().top )
                        }, 850);
                        return false;
                    }
                }
            });
        });
    </script>
@stop