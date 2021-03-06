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
        <nav>
            <div class="container">
                <div class="logo pull-left">
                    <a href="@if(Auth::guard('school')->check()) {{ url('school/home') }} @else {{ url('/') }} @endif"><img src=" {{ Storage::url('img/logo.png') }}" alt=""></a>
                </div>
                <div class="menu-toggle pull-right">
                    <ul class="nav-bar clearfix">
                        <?php
                            $photo = (isset(Auth::guard('school')->user()->sc_logo)) ? Auth::guard('school')->user()->sc_logo : '';
                            if (isset($photo) && $photo != '' && Storage::size(Config::get('constant.SCHOOL_THUMB_IMAGE_UPLOAD_PATH') . $photo) > 0) {
                                $profilePicUrl = Storage::url(Config::get('constant.SCHOOL_THUMB_IMAGE_UPLOAD_PATH') . $photo);
                            } else {
                                $profilePicUrl = Storage::url(Config::get('constant.SCHOOL_THUMB_IMAGE_UPLOAD_PATH') . "proteen-logo.png");
                            }                                                                                       
                        ?>
                        <li class="n-user submenu-container">
                            <a href="javascript:void(0);"><i class="i-user"><img src="{{ $profilePicUrl }}" alt=""></i></a>
                            <div class="submenu">
                                <div class="user-snapshot">
                                    <div class="user-avatar"><img src="{{ $profilePicUrl }}" alt=""></div>
                                    <div class="user-name">
                                        <h2>{{ Auth::guard('school')->user()->sc_name }}</h2>
                                        <p>{{ Auth::guard('school')->user()->sc_email }}</p>
                                    </div>
                                </div>
                                <div class="btns">
                                    <a href="javascript:void(0);" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="btn btn-primary btn-small text-uppercase">Sign out</a>
                                    <form id="logout-form" action="{{ url('/school/logout') }}" method="POST" style="display: none;">
                                        {{ csrf_field() }}
                                    </form>
                                </div>
                            </div>
                        </li>
                        <li class="n-coin submenu-container">
                            <a href="javascript:void(0);">
                                <span class="coins"></span>
                                <span id="user_procoins">{{ (Auth::guard('school')->user()->sc_coins > 0) ? number_format(Auth::guard('school')->user()->sc_coins) : 'No Coins' }}</span>
                            </a>
                            <div class="submenu">
                                <h2>My ProCoins</h2>
                                <div class="btns">
                                    <a href="{{ url('/school/get-gift-coins') }}" class="btn btn-success btn-small text-uppercase">Gift</a>
                                    <a href="{{ url('/school/get-consumption') }}" class="btn btn-success btn-small text-uppercase">History</a>
                                </div>
                            </div>
                        </li>
                        <li class="n-menu"><a href="javascript:void(0);" class="menu-toggler"><i class="icon-menu"></i></a></li>
                    </ul>
                </div>
            </div>
            <div class="main-menu">
                <div class="menu-container">
                    <ul>
                        <li><a href="{{ url('school/update-profile') }}" class="{{ (Route::getFacadeRoot()->current()->uri() == 'school/update-profile') ? 'active' : '' }}" title="Profile">Profile</a></li>
                        <li><a href="{{ url('school/home') }}" class="{{ (Route::getFacadeRoot()->current()->uri() == 'school/home') ? 'active' : '' }}" title="Student">Dashboard</a></li>
                        <li><a href="{{ url('school/progress') }}" class="{{ (Route::getFacadeRoot()->current()->uri() == 'school/progress') ? 'active' : '' }}" title="Progress">Progress</a></li>
                        <li><a href="{{ url('school/get-gift-coins') }}" class="{{ (Route::getFacadeRoot()->current()->uri() == 'school/get-gift-coins') ? 'active' : '' }}" title="Procoins">ProCoins</a></li>
                        <li><a href="{{ url('school/questions') }}" class="{{ (Route::getFacadeRoot()->current()->uri() == 'school/questions') ? 'active' : '' }}" title="Question Management">Ask Students</a></li>
                        <li><a href="{{ url('school/bulk-import') }}" class="{{ (Route::getFacadeRoot()->current()->uri() == 'school/bulk-import') ? 'active' : '' }}" title="Register Your Student">Enroll Students</a></li>
                    </ul>
                    <a href="#" class="menu-close"><i class="icon-close"></i></a>
                </div>
            </div>
        </nav>
        @yield('content')
        <a class="back-to-top bounce" href="#" title="Back to top"><img src="{{ Storage::url('img/arrow-up.png') }}" alt="back to top"></a>
        <footer>
            <div class="container">
                <div class="left">
                    <ul class="links">
                        <li><a href="{{ url('about-us') }}" title="About Us">About</a></li>
                        <li><a href="{{ url('contact-us') }}" title="Contact Us">Contact</a></li>
                        <li><a href="{{ url('privacy-policy') }}" title="Privacy Policy">Privacy</a></li>
                        <li><a href="{{ url('terms-condition') }}" title="Terms & Conditions">Terms & Conditions</a></li>
                    </ul>
                    <ul class="links">
                        <li><span>&copy; 2018 ProTeen</span></li>
                        <!-- <li><span>A <strong>UNIDEL</strong> COMPANY</span></li> -->
                        <li><span>A <strong><a href="https://www.unidel-group.com/" target="_blank" title="Unidel Company">UNIDEL</a></strong> Company</span></li>
                    </ul>                      
                </div>
                <div class="right">
                    <ul class="social">
                        <li><a href="https://www.facebook.com/proteenlife/" target="_blank"><i class="icon-facebook"></i></a></li>
                        <li><a href="https://twitter.com/ProTeenLife" target="_blank"><i class="icon-twitter"></i></a></li>
                        <li><a href="https://plus.google.com/109414106711493074923" target="_blank"><i class="icon-google"></i></a></li>
                        <li><a href="https://www.linkedin.com/company/proteen-life" target="_blank"><i class="icon-linkdin"></i></a></li>
                        <li><a href="https://www.youtube.com/channel/UCJ7Bw7Jrxgs2QfCI-DzUNSw" target="_blank"><i class="icon-search icon-youtube"><img src="{{ Storage::url('img/youtube.png')}}" alt="youtube" class="icon-img"><img src="{{ Storage::url('img/youtube-hover.png')}}" alt="youtube" class="icon-hover"></i></a></li>
                        <li><a href="https://www.instagram.com/proteenlife/" target="_blank"><i class="icon-search icon-instagram icon-youtube"><img src="{{ Storage::url('img/instagram.png')}}" alt="instagram" class="icon-img"><img src="{{ Storage::url('img/instagram-hover.png')}}" alt="instagram" class="icon-hover"></i></a></li>
                    </ul>
                </div>
            </div>
        </footer>
        @yield('footer')
        <div id="loading-wrapper">
            <!-- <div id="loading-text"><span>Loading...</span></div> -->
            <div id="loading-content"><img src="{{ Storage::url('img/Bars.gif') }}"></div>
        </div> 
        <script src="{{ asset('backend/plugins/jQuery/jQuery-2.1.4.min.js')}}"></script>
        <script src="{{ asset('frontend/js/jquery-ui.min.js')}}"></script>
        <script src="{{ asset('frontend/js/bootstrap.min.js')}}"></script>
        <script src="{{ asset('frontend/js/chosen.js')}}"></script>
        <script src="{{ asset('frontend/js/comman.js')}}"></script>
        <script src="{{ asset('js/general.js') }}"></script>
        <script src="{{ asset('frontend/js/owl.carousel.min.js')}}"></script>
        <script src="{{ asset('frontend/js/jquery.mCustomScrollbar.min.js') }}"></script>
        <script src="{{ asset('frontend/plugins/slimScroll/jquery.slimscroll.min.js')}}"></script>
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
                        url: "{{ url('/teenager/global-search')}}",
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
