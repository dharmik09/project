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
        <link href="{{asset('css/style.css')}}" rel="stylesheet">
        <link href="{{ asset('/frontend/css/custom.css')}}" rel="stylesheet">
        <link rel="icon" type="image/png" href="{{ asset('/frontend/images/favicon-32x32.png')}}" sizes="32x32" />
        <link rel="icon" type="image/png" href="{{ asset('/frontend/images/favicon-16x16.png')}}" sizes="16x16" />
        @yield('header')

    </head>

    <body>
        @if(Auth::guard('sponsor')->check())
        <nav>
            <div class="container">
                <div class="logo pull-left"><a href="#"><img src="../img/logo.png" alt=""></a></div>
                <div class="menu-toggle pull-right">
                    <ul class="nav-bar clearfix">
                        <li class="n-user submenu-container">
                            <a href="javascript:void(0);"><i class="i-user"><img src="../img/alex.jpg" alt=""></i></a>
                            <div class="submenu">
                                <div class="user-snapshot">
                                    <div class="user-avatar"><img src="../img/alex.jpg" alt=""></div>
                                    <div class="user-name">
                                        <h2>Alex Murphy</h2>
                                        <p>Alexmurphy@gmail.com</p>
                                    </div>
                                </div>
                                <div class="btns"><a href="#" class="btn btn-primary btn-small text-uppercase">Sign out</a></div>
                            </div>
                        </li>
                        <li class="n-menu"><a href="javascript:void(0);" class="menu-toggler"><i class="icon-menu"></i></a></li>
                    </ul>
                </div>
            </div>
            <div class="main-menu">
                <div class="menu-container">
                    <ul>
                        <li><a href="#" title="My Profile">My Profile</a></li>
                        <li><a href="#" class="active" title="My Student">My Student</a></li>
                        <li><a href="#" title="progress">progress</a></li>
                        <li><a href="#" title="My procoins">My procoins</a></li>
                    </ul>
                    <img class="i-menu-rocket menu-rocket" alt="" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAMkAAABZAQMAAACubpIFAAAAA1BMVEX///+nxBvIAAAAAXRSTlMAQObYZgAAABlJREFUeNrtwTEBAAAAwqD1T20JT6AAADgbCWMAAa20KzgAAAAASUVORK5CYII="><a href="#" class="menu-close"><i class="icon-close"></i></a>
                </div>
            </div>
        </nav>
        @else
        <nav class="fixed-navigation">
            <div class="container">
                <div class="logo pull-left">
                    <a href="{{ url('/') }}">
                        <img src="{{ Storage::url('img/logo.png') }}" alt="{{ trans('labels.appname') }}">
                    </a>
                </div>
                <div class="menu-toggle pull-right">
                    <ul class="nav-bar clearfix">
                        <li class="n-menu">
                            <a href="javascript:void(0);" class="menu-toggler">
                                <i class="icon-menu"></i>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="main-menu bg-light">
                <div class="menu-container">
                    <ul>
                        <li><a href="{{ url('/teenager') }}" title="Teen" class="{{ in_array(Route::getFacadeRoot()->current()->uri(), ['teenager', 'teenager/login', 'teenager/signup']) ? 'active' : ''}}">Student</a></li>
                        <li><a href="{{ url('/parent') }}" title="Parent" class="{{ in_array(Route::getFacadeRoot()->current()->uri(), ['parent', 'parent/login', 'parent/signup']) ? 'active' : ''}}">Parent</a></li>
                        <li><a href="{{ url('/counselor') }}"  title="Mentor" class="{{ in_array(Route::getFacadeRoot()->current()->uri(), ['counselor', 'counselor/login', 'counselor/signup']) ? 'active' : ''}}">Mentor</a></li>
                        <li><a href="{{ url('/school') }}" title="School" class="{{ in_array(Route::getFacadeRoot()->current()->uri(), ['school', 'school/login', 'school/signup']) ? 'active' : ''}}">School</a></li>
                        <li><a href="{{ url('/sponsor') }}" title="Enterprise" class="{{ in_array(Route::getFacadeRoot()->current()->uri(), ['sponsor', 'sponsor/login', 'sponsor/signup']) ? 'active' : ''}}">Enterprise</a></li>
                            <!--<li><a href="{{ url('/team') }}" title="Team" class="{{ in_array(Route::getFacadeRoot()->current()->uri(), ['team']) ? 'active' : ''}}">Team</a></li>
                        <li><a href="{{ url('/contact-us') }}" title="Contact" class="{{ in_array(Route::getFacadeRoot()->current()->uri(), ['contact-us']) ? 'active' : ''}}">Contact</a></li>-->
                    </ul>
                    <img class="i-menu-rocket menu-rocket" alt="" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAMkAAABZAQMAAACubpIFAAAAA1BMVEX///+nxBvIAAAAAXRSTlMAQObYZgAAABlJREFUeNrtwTEBAAAAwqD1T20JT6AAADgbCWMAAa20KzgAAAAASUVORK5CYII=">
                    <a href="#" class="menu-close"><i class="icon-close"></i></a>
                </div>
            </div>
        </nav>

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
        <script src="{{ asset('js/general.js') }}"></script>
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
