<!DOCTYPE html>
<html lang="en">
    <head>
        <META charset="utf-8">
        <META http-equiv="X-UA-Compatible" content="IE=edge">
        <META content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <META Http-Equiv="Cache-Control" Content="no-cache"/>
        <META Http-Equiv="Pragma" Content="no-cache"/>
        <META Http-Equiv="Expires" Content="0"/>
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{trans('labels.appname')}}</title>
        <link href="{{ asset('/frontend/css/bootstrap.min.css')}}" rel="stylesheet" >
        <link href="{{ asset('/frontend/css/bootstrap-material-design.min.css')}}" rel="stylesheet">
        <link href="{{ asset('/frontend/css/font-awesome.min.css')}}" rel="stylesheet">
        <link href="{{ asset('/frontend/css/ripples.min.css')}}" rel="stylesheet">
        <link href="{{ asset('/frontend/css/jquery-ui.structure.min.css')}}" rel="stylesheet">
        <link href="{{ asset('/frontend/css/jquery-ui.theme.min.css')}}" rel="stylesheet">
        <link href="{{ asset('/frontend/css/jquery.mCustomScrollbar.min.css')}}" rel="stylesheet">
        <link href="{{ asset('/frontend/css/owl.carousel.css')}}" rel="stylesheet" >
        <link href="{{ asset('/frontend/css/font.css')}}" rel="stylesheet">
        <link href="{{ asset('/frontend/css/style.css')}}" rel="stylesheet">
        <link href="{{ asset('/frontend/css/custom.css')}}" rel="stylesheet">
        <link rel="icon" type="image/png" href="{{ asset('/frontend/images/favicon-32x32.png')}}" sizes="32x32" />
        <link rel="icon" type="image/png" href="{{ asset('/frontend/images/favicon-16x16.png')}}" sizes="16x16" />
        @yield('header')

    </head>

    <body>
        @if(Auth::guard('sponsor')->check())
        <div class="navbar navbar-cst">
            <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-responsive-collapse">
                    <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                </button>
                <a class="navbar-brand" href="/"><img src="{{ Storage::url('frontend/images/proteen_logo.png') }}" alt=""></a>
            </div>
            <div class="navbar-collapse collapse navbar-responsive-collapse">
                <ul class="nav nav_basic navbar-nav navbar-right home_page_navigation non_teen">
                    <li class="{{ (Request::is('sponsor/update-profile')) ? 'active' : '' }}"><a href="{{url('/sponsor/update-profile')}}">My Profile</a></li>
                    <li class="{{ (Request::is('sponsor/home')) ? 'active' : '' }}"><a href="{{url('/sponsor/home')}}">Progress</a></li>
                    <li class="{{ (Request::is('sponsor/mycoins')) ? 'active' : '' }}"><a href="{{url('/sponsor/mycoins')}}">My ProCoins</a></li>
                    <li class="user_avatar">
                        <a href="#" class="drop_down_menu">
                           <span class="user_detail_name">{{Auth::guard('sponsor')->user()->sp_first_name}}</span>
                            <?php
                                $photo = Auth::guard('sponsor')->user()->sp_photo;
                                $profilePicUrl = ($photo != '') ? Storage::url(Config::get('constant.CONTACT_PHOTO_ORIGINAL_IMAGE_UPLOAD_PATH') . $photo) : Storage::url('frontend/images/proteen_logo.png');
                            ?>
                            <img class="user_detail_image" src="{{ $profilePicUrl }}" alt="">
                        </a>
                        <ul class="navigation_prime menu_dropdown" style="display: none;">
                            <form id="logout-form" action="{{ url('/sponsor/logout') }}" method="POST" style="display: none;">
                                        {{ csrf_field() }}
                            </form>
                            <li><a href="{{url('/sponsor/logout')}}" onclick="event.preventDefault();
                                                         document.getElementById('logout-form').submit();">Log Out</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
        </div>
        <div id="confirm" title="Congratulations!" style="display:none;">
            <div class="confirm_coins"></div><br/>
            <div class="confirm_detail"></div>
        </div>
        @else
        <div class="navbar navbar-cst home_header_navigation">
            <div class="container">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-responsive-collapse">
                        <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                    </button>
                    <a class="navbar-brand" href="/"><img src="{{asset('frontend/images/proteen_logo.png')}}" alt=""></a>
                    <span class="brand_tag sticky_tag">Explore<i class="fa fa-circle" aria-hidden="true"></i>Experience<i class="fa fa-circle" aria-hidden="true"></i>Enjoy</span>
                </div>
                <div class="navbar-collapse collapse navbar-responsive-collapse">
                    <ul class="nav nav_basic navbar-nav navbar-right home_page_navigation">
                        <li><a href="{{url('about')}}">About</a></li>
                        <li><a href="{{url('watchVideo')}}" class="intro_video">Watch</a></li>
                        <li>
                            <a href="#" class="drop_down_menu">
                                <span class="user_detail_name play_menu">Play <i class="fa fa-caret-down" aria-hidden="true"></i></span>
                            </a>
                            <ul class="navigation_prime menu_dropdown product_drop" style="display: none;">
                                <li><a href="{{url('teenager')}}">Teen</a></li>
                                <li><a href="{{url('parent')}}">Parent</a></li>
                                <li><a href="{{url('counselor')}}">Mentor</a></li>
                                <li><a href="{{url('school')}}">School</a></li>
                                <li><a href="{{url('sponsor')}}">Enterprise</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        @endif
        @yield('content')
        @if(Auth::guard('sponsor')->check())
        <footer class="primary_footer tnc">

        </footer>
        @else
        <footer class="primary_footer tnc">
            <div class="container">
                <div class="copyright tandc">
                    <span><a href="{{url('contact')}}"  class="terms">Contact</a></span>
                    <span><a href="{{url('term')}}" class="terms">Terms &amp; Conditions</a></span>
                    <span><a href="{{url('privacy')}}" class="terms">Privacy Policy</a></span>
                    <span>(c) 2017 All Rights Reserved</span>
                </div>
                <div class="social_footer">
                    <a href="https://www.linkedin.com/company/proteen-life" target="_blank" class="linkedin_footer"><i class="fa fa-linkedin-square" aria-hidden="true"></i></a>
                    <a href="https://www.facebook.com/proteenlife/" target="_blank" class="facebook_footer"><i class="fa fa-facebook-square" aria-hidden="true"></i></a>
                    <a href="https://plus.google.com/109414106711493074923" target="_blank" class="google_plus_footer"><i class="fa fa-google-plus-square" aria-hidden="true"></i></a>
                    <a href="https://twitter.com/ProTeenLife" target="_blank" class="twitter_footer"><i class="fa fa-twitter-square" aria-hidden="true"></i></a>
                </div>
            </div>
        </footer>
        @endif
       @yield('footer')
        <script src="{{ asset('backend/plugins/jQuery/jQuery-2.1.4.min.js')}}"></script>
        <script src="{{ asset('frontend/js/jquery-ui.min.js')}}"></script>
        <script src="{{ asset('frontend/js/bootstrap.min.js')}}"></script>
        <script src="{{ asset('frontend/js/chosen.js')}}"></script>
        <script src="{{ asset('frontend/js/comman.js')}}"></script>
        <script src="{{ asset('frontend/js/owl.carousel.min.js')}}"></script>
        <!-- SlimScroll -->
        <script src="{{ asset('frontend/js/jquery.mCustomScrollbar.min.js') }}"></script>
        <script src="{{ asset('frontend/plugins/slimScroll/jquery.slimscroll.min.js')}}"></script>
        <!-- FastClick -->
        <script src="{{ asset('frontend/plugins/fastclick/fastclick.min.js')}}"></script>
        <script src="{{ asset('frontend/js/jquery.validate.min.js') }}"></script>
        <script src="{{ asset('frontend/js/jquery.autocomplete.min.js') }}"></script>
        <script>
            $(document).ready(function() {
                setTimeout(function() {
                    $('.alert-dismissable').fadeOut();
                }, 5000);
                $('[data-toggle="tooltip"]').tooltip();
            });
            $('.onlyNumber').on('keyup', function() {
                this.value = this.value.replace(/[^0-9]/gi, '');
            });

            $('#search_box').on('keyup', function() {
                var search_keyword = $('#search_box').val();
                var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
                var form_data = 'search_keyword=' + search_keyword;

                if(search_keyword.length > 2){
                    $.ajax({
                        type: 'POST',
                        data: form_data,
                        dataType: 'html',
                        url: "{{ url('/teenager/globalSearch')}}",
                        headers: {
                            'X-CSRF-TOKEN': CSRF_TOKEN
                        },
                        cache: false,
                        success: function(data) {
                            $("#searchData").html(data);
                        }
                    });
                }
            });

        </script>

        @yield('script')

    </body>

</html>
